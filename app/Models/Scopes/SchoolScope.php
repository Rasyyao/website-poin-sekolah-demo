<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class SchoolScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * Automatically filters queries by the authenticated user's school_id.
     * Super admins (no school_id) bypass this scope.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::check() && Auth::user()->school_id) {
            $builder->where($model->getTable() . '.school_id', Auth::user()->school_id);
        }
    }
}
