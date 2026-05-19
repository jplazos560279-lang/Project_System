<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollDeduction extends Model
{
    protected $primaryKey = 'deduction_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'payroll_id',
        'deduction_type',
        'deduction_name',
        'amount',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class, 'payroll_id');
    }

    /**
     * Deduction type constants (Government and Other)
     */
    const TYPE_SSS = 'sss';
    const TYPE_PHILHEALTH = 'philhealth';
    const TYPE_PAG_IBIG = 'pag_ibig';
    const TYPE_TAX = 'tax';
    const TYPE_LOAN = 'loan';
    const TYPE_PENALTY = 'penalty';
    const TYPE_OTHER = 'other';

    public static function getDeductionTypes(): array
    {
        return [
            self::TYPE_SSS => 'SSS (Social Security System)',
            self::TYPE_PHILHEALTH => 'PhilHealth',
            self::TYPE_PAG_IBIG => 'Pag-IBIG',
            self::TYPE_TAX => 'Income Tax (BIR)',
            self::TYPE_LOAN => 'Loan Deduction',
            self::TYPE_PENALTY => 'Penalty/Late',
            self::TYPE_OTHER => 'Other Deduction',
        ];
    }
}
