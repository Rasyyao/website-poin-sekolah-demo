<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePointsLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canInputPoints() || $this->user()->isAdmin() || $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'rule_id' => ['required', 'exists:rules,id'],
            'note' => ['nullable', 'string', 'max:1000'],
            'evidence' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,heic', 'max:5120'],
            'evidence_url' => ['nullable', 'string', 'max:500'],
            'remove_evidence' => ['nullable', 'boolean'],
            'occurred_at' => ['nullable', 'date', 'before_or_equal:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'rule_id.required' => 'Peraturan wajib dipilih.',
            'rule_id.exists' => 'Peraturan tidak ditemukan.',
            'evidence.image' => 'File bukti harus berupa gambar.',
            'evidence.mimes' => 'Format foto yang didukung: JPEG, PNG, JPG, WEBP, HEIC.',
            'evidence.max' => 'Ukuran foto bukti maksimal 5MB.',
            'occurred_at.before_or_equal' => 'Tanggal kejadian tidak boleh di masa depan.',
        ];
    }
}
