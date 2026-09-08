<?php

namespace App\Http\Requests;

use App\Enums\RuleType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('category') === '__custom__' && $this->filled('custom_category')) {
            $this->merge([
                'category' => trim($this->input('custom_category')),
            ]);
        } elseif ($this->input('category') === '__custom__') {
            $this->merge([
                'category' => null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(RuleType::class)],
            'category' => ['nullable', 'string', 'max:100'],
            'points' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'points.min' => 'Poin harus lebih dari 0.',
        ];
    }
}
