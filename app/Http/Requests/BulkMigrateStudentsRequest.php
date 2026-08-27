<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkMigrateStudentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['required', 'exists:students,id'],
            'target_class_id' => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'student_ids.required' => 'Pilih minimal satu siswa.',
            'student_ids.min' => 'Pilih minimal satu siswa.',
            'target_class_id.required' => 'Kelas tujuan wajib dipilih.',
            'academic_year_id.required' => 'Tahun ajaran wajib dipilih.',
        ];
    }
}
