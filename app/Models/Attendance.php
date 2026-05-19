<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $primaryKey = 'attendance_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'emp_id',
        'date',
        'time_in',
        'time_out',
        'status',
        'late_minutes',
        'overtime_hours',
        'overtime_type',
        'has_excuse',
    ];

    public function getRouteKeyName(): string
    {
        // Ensure route-model binding uses attendance_id
        return 'attendance_id';
    }

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Overtime multipliers
     */
    const OT_REGULAR = 1.25;
    const OT_REST_DAY = 1.30;
    const OT_HOLIDAY = 2.00;

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }
}

