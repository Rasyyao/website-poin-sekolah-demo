<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ParentAccessController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.parent-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nisn' => ['required', 'string'],
            'access_code' => ['required', 'string'],
            'school_slug' => ['required', 'string'],
        ]);

        $student = Student::withoutGlobalScopes()
            ->whereHas('school', fn ($q) => $q->where('slug', $request->school_slug))
            ->where('nisn', $request->nisn)
            ->first();

        $inputCode = strtoupper($request->access_code);

        if (! $student || ! \Illuminate\Support\Facades\Hash::check($inputCode, $student->access_code)) {
            throw ValidationException::withMessages([
                'access_code' => ['NISN atau kode akses salah.'],
            ]);
        }

        $request->session()->put('parent_student_id', $student->id);
        $request->session()->put('parent_school_id', $student->school_id);

        return redirect()->route('parent.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['parent_student_id', 'parent_school_id']);

        return redirect()->route('parent.login.form');
    }
}
