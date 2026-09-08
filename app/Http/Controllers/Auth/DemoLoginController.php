<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemoLoginController extends Controller
{
    public function login(Request $request, $role)
    {
        // First logout any existing session
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        switch ($role) {
            case 'admin':
                $user = User::where('email', 'admin@smpn1demo.sch.id')->firstOrFail();
                Auth::login($user);
                $request->session()->regenerate();
                return redirect()->route('admin.reports.dashboard');

            case 'kesiswaan':
                $user = User::where('email', 'kesiswaan@smpn1demo.sch.id')->firstOrFail();
                Auth::login($user);
                $request->session()->regenerate();
                return redirect()->route('admin.reports.dashboard');

            case 'teacher':
                $user = User::where('email', 'siti@smpn1demo.sch.id')->firstOrFail();
                Auth::login($user);
                $request->session()->regenerate();
                return redirect()->route('teacher.points.index');

            case 'homeroom':
                $user = User::where('email', 'budi@smpn1demo.sch.id')->firstOrFail();
                Auth::login($user);
                $request->session()->regenerate();
                return redirect()->route('teacher.my-students');

            case 'counselor':
                $user = User::where('email', 'dewi@smpn1demo.sch.id')->firstOrFail();
                Auth::login($user);
                $request->session()->regenerate();
                return redirect()->route('teacher.points.index');

            case 'student':
                // Student demo is student 1 from class 7A
                $student = Student::withoutGlobalScopes()->where('nisn', '0012340001')->firstOrFail();
                $request->session()->put('parent_student_id', $student->id);
                $request->session()->put('parent_school_id', $student->school_id);
                return redirect()->route('student.dashboard');

            case 'parent':
                // Parent demo is for student 1 from class 7A
                $student = Student::withoutGlobalScopes()->where('nisn', '0012340001')->firstOrFail();
                $request->session()->put('parent_student_id', $student->id);
                $request->session()->put('parent_school_id', $student->school_id);
                return redirect()->route('parent.dashboard');

            default:
                return redirect()->route('login');
        }
    }
}
