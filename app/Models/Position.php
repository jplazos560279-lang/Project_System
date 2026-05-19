<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    protected $primaryKey = 'position_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'position_name',
        'salary',
        'dept_id',
    ];

    public function getRouteKeyName(): string
    {
        // Ensure route-model binding uses position_id
        return 'position_id';
    }

    /**
     * Get the position title for display (alias for position_name)
     */
    public function getTitleAttribute(): string
    {
        return $this->position_name;
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'position_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }
}

