<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'homeroom_teacher_id' => ['nullable', 'exists:users,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kelas wajib diisi.',
            'academic_year_id.required' => 'Tahun ajaran wajib dipilih.',
        ];
    }
}
