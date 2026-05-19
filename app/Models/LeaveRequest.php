<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    protected $primaryKey = 'leave_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'emp_id',
        'leave_type',
        'reason',
        'start_date',
        'end_date',
        'total_days',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    // Leave type labels
    const LEAVE_TYPES = [
        'sick' => 'Sick Leave',
        'vacation' => 'Vacation Leave',
        'emergency' => 'Emergency Leave',
        'bereavement' => 'Bereavement Leave',
        'maternity' => 'Maternity Leave',
        'paternity' => 'Paternity Leave',
        'other' => 'Other',
    ];

    // Status labels
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Check if leave is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if leave is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if leave is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Get leave type label
     */
    public function getLeaveTypeLabelAttribute(): string
    {
        return self::LEAVE_TYPES[$this->leave_type] ?? $this->leave_type;
    }

    /**
     * Approve the leave request
     */
    public function approve(User $admin, string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'admin_notes' => $notes,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Reject the leave request
     */
    public function reject(User $admin, string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'admin_notes' => $notes,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope for rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Check if date falls within leave period
     */
    public function coversDate($date): bool
    {
        return $date->between($this->start_date, $this->end_date);
    }
}
