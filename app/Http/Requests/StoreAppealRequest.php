<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->session()->has('parent_student_id');
    }

    public function rules(): array
    {
        $studentId = $this->session()->get('parent_student_id');

        return [
            'points_log_id' => [
                'required',
                Rule::exists('points_log', 'id')->where(function ($query) use ($studentId) {
                    $query->where('student_id', $studentId)
                        ->where('status', 'approved');
                }),
            ],
            'reason' => ['required', 'string', 'min:10', 'max:2000'],
            'evidence' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,heic', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'points_log_id.required' => 'Poin pelanggaran wajib dipilih.',
            'points_log_id.exists' => 'Poin tidak valid atau bukan milik siswa ini.',
            'reason.required' => 'Alasan banding wajib diisi.',
            'reason.min' => 'Alasan banding minimal 10 karakter.',
            'evidence.required' => 'Foto bukti pendukung wajib dilampirkan.',
            'evidence.image' => 'File bukti harus berupa gambar.',
            'evidence.mimes' => 'Format foto yang didukung: JPEG, PNG, JPG, WEBP, HEIC.',
            'evidence.max' => 'Ukuran foto bukti maksimal 5MB.',
        ];
    }
}
