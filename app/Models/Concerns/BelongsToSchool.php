<?php

namespace App\Models\Concerns;

use App\Models\School;
use App\Models\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Trait for models that belong to a school (tenant-scoped).
 *
 * Automatically:
 * - Applies SchoolScope global scope for query filtering
 * - Sets school_id on model creation from authenticated user
 */
trait BelongsToSchool
{
    public static function bootBelongsToSchool(): void
    {
        static::addGlobalScope(new SchoolScope);

        static::creating(function ($model) {
            if (! $model->school_id && Auth::check() && Auth::user()->school_id) {
                $model->school_id = Auth::user()->school_id;
            }
        });
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
