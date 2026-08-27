<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRuleThresholdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'min_points' => ['required', 'integer', 'min:1'],
            'action' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'min_points.required' => 'Ambang batas poin wajib diisi.',
            'min_points.min' => 'Ambang batas poin minimal 1.',
            'action.required' => 'Aksi wajib dipilih.',
        ];
    }
}
