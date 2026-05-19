<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $primaryKey = 'dept_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'dept_name',
        'dept_head',
    ];

    public function getRouteKeyName(): string
    {
        // Ensure route-model binding uses dept_id
        return 'dept_id';
    }

    /**
     * Get the department name for display (alias for dept_name)
     */
    public function getNameAttribute(): string
    {
        return $this->dept_name;
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'dept_id');
    }
}

