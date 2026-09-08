<?php

namespace App\Casts;

use App\Enums\AchievementCategory;
use App\Enums\ViolationCategory;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class RuleCategoryCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($cat = ViolationCategory::tryFrom($value)) {
            return $cat;
        }

        if ($cat = AchievementCategory::tryFrom($value)) {
            return $cat;
        }

        return new class($value) {
            public string $value;

            public function __construct(string $val)
            {
                $this->value = $val;
            }

            public function label(): string
            {
                return ucwords(str_replace(['_', '-'], ' ', $this->value));
            }

            public function requiresApproval(): bool
            {
                return false;
            }

            public function __toString(): string
            {
                return $this->value;
            }
        };
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        if (is_object($value) && property_exists($value, 'value')) {
            return $value->value;
        }

        return (string) $value;
    }
}
