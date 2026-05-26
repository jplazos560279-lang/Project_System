<?php

namespace App\Services;

class PayrollCalculator
{
    /**
     * Calculate the final net salary.
     *
     * @param float $baseSalary
     * @param float $deductions
     * @return float
     */
    public function calculateNetPay(float $baseSalary, float $deductions): float
    {
        return $baseSalary - $deductions;
    }
}

