<?php

namespace App\Http\Requests;

use App\Enums\RuleType;
use App\Enums\ViolationCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(RuleType::class)],
            'category' => ['nullable', 'required_if:type,violation', Rule::enum(ViolationCategory::class)],
            'points' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'points.min' => 'Poin harus lebih dari 0.',
            'category.required_if' => 'Kategori wajib diisi untuk pelanggaran.',
        ];
    }
}
