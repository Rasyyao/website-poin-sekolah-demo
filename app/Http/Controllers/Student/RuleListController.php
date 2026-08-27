<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Rule;
use Illuminate\Http\Request;

class RuleListController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = $request->session()->get('parent_school_id');

        $rules = Rule::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('is_active', true)
            ->orderBy('type')->orderBy('category')->orderBy('name')
            ->get();

        return view('student.rules', compact('rules'));
    }
}
