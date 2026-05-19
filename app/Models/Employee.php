<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $primaryKey = 'emp_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'email_hash',
        'phone',
        'dept_id',
        'position_id',
        'hire_date',
        'basic_salary',
        'daily_rate',
        'hourly_rate',
    ];

    public function getRouteKeyName(): string
    {
        // Ensure route-model binding uses emp_id
        return 'emp_id';
    }

    // Overtime multipliers
    const OT_REGULAR = 1.25;
    const OT_REST_DAY = 1.30;
    const OT_HOLIDAY = 2.00;

    /**
     * Calculate daily rate from basic salary
     */
    public function calculateDailyRate(): float
    {
        // Assuming 22 working days per month
        return $this->basic_salary / 22;
    }

    /**
     * Calculate hourly rate from basic salary
     */
    public function calculateHourlyRate(): float
    {
        // Assuming 8 hours per day
        return $this->calculateDailyRate() / 8;
    }

    /**
     * Auto-calculate and save salary rates
     */
    public function autoCalculateRates(): void
    {
        $this->daily_rate = $this->calculateDailyRate();
        $this->hourly_rate = $this->calculateHourlyRate();
        $this->save();
    }



    /**
     * Calculate withholding tax (simplified)
     */
    public function calculateTax(): float
    {
        $salary = $this->basic_salary ?? 0;
        if ($salary < 20833) return 0;
        if ($salary < 33333) return ($salary - 20833) * 0.15 / 12;
        if ($salary < 66667) return ($salary - 33333) * 0.20 / 12 + 312.50;
        if ($salary < 166667) return ($salary - 66667) * 0.25 / 12 + 975;
        return ($salary - 166667) * 0.30 / 12 + 3491.67;
    }

    protected function casts(): array
    {
        return [
            'email' => 'encrypted',
            'phone' => 'encrypted',
            'hire_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'emp_id');
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class, 'emp_id');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getMaskedEmailAttribute(): string
    {
        $email = $this->email;
        if (str_contains($email, '@')) {
            [$local, $domain] = explode('@', $email);
            $visible = max(1, (int) ceil(strlen($local) * 0.2));
            return substr($local, 0, $visible) . str_repeat('*', strlen($local) - $visible) . '@' . $domain;
        }
        return $email;
    }

    public function getMaskedPhoneAttribute(): string
    {
        $phone = $this->phone;
        $length = strlen($phone);
        if ($length <= 4) {
            return $phone;
        }
        return str_repeat('*', $length - 4) . substr($phone, -4);
    }
}
