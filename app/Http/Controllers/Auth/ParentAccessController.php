<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
            'nisn'       => ['required', 'string'],
            'birth_date' => ['required', 'date'],
        ]);

        $student = Student::withoutGlobalScopes()
            ->where('nisn', $request->nisn)
            ->whereNotNull('birth_date')
            ->first();

        $inputDate = \Carbon\Carbon::parse($request->birth_date)->format('Y-m-d');

        if (! $student || $student->birth_date->format('Y-m-d') !== $inputDate) {
            throw ValidationException::withMessages([
                'birth_date' => ['NISN atau tanggal lahir tidak sesuai.'],
            ]);
        }

        $request->session()->regenerate();
        $request->session()->put('parent_student_id', $student->id);
        $request->session()->put('parent_school_id', $student->school_id);

        return redirect()->route('parent.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['parent_student_id', 'parent_school_id']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('parent.login.form');
    }
}
