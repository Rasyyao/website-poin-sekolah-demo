<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePointsLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canInputPoints();
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'rule_id' => ['required', 'exists:rules,id'],
            'note' => ['nullable', 'string', 'max:1000'],
            'evidence_url' => ['nullable', 'string', 'max:500'],
            'occurred_at' => ['nullable', 'date', 'before_or_equal:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'Siswa wajib dipilih.',
            'student_id.exists' => 'Siswa tidak ditemukan.',
            'rule_id.required' => 'Peraturan wajib dipilih.',
            'rule_id.exists' => 'Peraturan tidak ditemukan.',
        ];
    }
}
