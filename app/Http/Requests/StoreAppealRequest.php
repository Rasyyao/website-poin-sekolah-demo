<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'points_log_id' => ['required', 'exists:points_log,id'],
            'reason' => ['required', 'string', 'min:10', 'max:2000'],
            'evidence' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,heic', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'Alasan banding wajib diisi.',
            'reason.min' => 'Alasan banding minimal 10 karakter.',
            'evidence.required' => 'Foto bukti pendukung wajib dilampirkan.',
            'evidence.image' => 'File bukti harus berupa gambar.',
            'evidence.mimes' => 'Format foto yang didukung: JPEG, PNG, JPG, WEBP, HEIC.',
            'evidence.max' => 'Ukuran foto bukti maksimal 5MB.',
        ];
    }
}
