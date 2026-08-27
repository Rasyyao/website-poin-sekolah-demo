<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->route('student');

        return [
            'nisn' => ['required', 'string', 'max:20', 'unique:students,nisn,' . $studentId . ',id,school_id,' . $this->user()->school_id],
            'name' => ['required', 'string', 'max:255'],
            'class_id' => ['nullable', 'exists:classes,id'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'parent_contact' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nisn.unique' => 'NISN sudah terdaftar di sekolah ini.',
        ];
    }
}
