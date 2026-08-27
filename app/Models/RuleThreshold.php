<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class RuleThreshold extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'min_points',
        'action',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'min_points' => 'integer',
        ];
    }
}
