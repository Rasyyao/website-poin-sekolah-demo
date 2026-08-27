<?php

namespace App\Http\Requests;

use App\Enums\Semester;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAcademicYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'year_label' => ['required', 'string', 'max:20', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['required', Rule::enum(Semester::class)],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'year_label.required' => 'Label tahun ajaran wajib diisi.',
            'year_label.regex' => 'Format tahun ajaran harus YYYY/YYYY (contoh: 2026/2027).',
            'semester.required' => 'Semester wajib dipilih.',
        ];
    }
}
