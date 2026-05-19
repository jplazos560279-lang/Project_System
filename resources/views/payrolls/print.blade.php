<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip - {{ $payroll->employee?->first_name }} {{ $payroll->employee?->last_name }}</title>

</head>
<body>
    <div class="payslip">
        <!-- Header -->
        <div class="header">
            <h1>PAYSLIP</h1>
            <p>{{ $payroll->payroll_month ?? $payroll->pay_date?->format('F Y') }}</p>
        </div>
        
        <!-- Employee Information -->
        <div class="employee-info">
            <div>
                <div class="info-row">
                    <span class="info-label">Employee Name:</span>
                    <span class="info-value">{{ $payroll->employee?->first_name }} {{ $payroll->employee?->last_name }}</span>
                </div>
            </div>
            <div>
                <div class="info-row">
                    <span class="info-label">Employee ID:</span>
                    <span class="info-value">{{ $payroll->employee?->emp_id }}</span>
                </div>
            </div>
            <div>
                <div class="info-row">
                    <span class="info-label">Position:</span>
                    <span class="info-value">{{ $payroll->employee?->position?->position_name ?? 'N/A' }}</span>
                </div>
            </div>
            <div>
                <div class="info-row">
                    <span class="info-label">Department:</span>
                    <span class="info-value">{{ $payroll->employee?->department?->dept_name ?? 'N/A' }}</span>
                </div>
            </div>
            <div>
                <div class="info-row">
                    <span class="info-label">Pay Date:</span>
                    <span class="info-value">{{ $payroll->pay_date?->format('M d, Y') }}</span>
                </div>
            </div>
            <div>
                <div class="info-row">
                    <span class="info-label">Pay Period:</span>
                    <span class="info-value">{{ $payroll->payroll_month ?? $payroll->pay_date?->format('M Y') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Earnings Section -->
        <div class="section">
            <div class="section-title">EARNINGS</div>
            <table>
                <tr>
                    <td class="label">Basic Salary</td>
                    <td class="amount">${{ number_format($payroll->basic_salary, 2) }}</td>
                </tr>
                @if($payroll->overtime_pay > 0)
                <tr>
                    <td class="label">Overtime Pay</td>
                    <td class="amount positive">+${{ number_format($payroll->overtime_pay, 2) }}</td>
                </tr>
                @endif
                @if($payroll->bonus > 0)
                <tr>
                    <td class="label">Bonus</td>
                    <td class="amount positive">+${{ number_format($payroll->bonus, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td class="label">Gross Salary</td>
                    <td class="amount">${{ number_format($payroll->gross_salary, 2) }}</td>
                </tr>
            </table>
        </div>
        
        <!-- Deductions Section -->
        <div class="section">
            <div class="section-title">DEDUCTIONS</div>
            <table>
                <!-- Automatic Deductions -->
                @if($payroll->late_deduction > 0)
                <tr>
                    <td class="label">Late Deduction</td>
                    <td class="amount negative">-${{ number_format($payroll->late_deduction, 2) }}</td>
                </tr>
                @endif
                
                @if($payroll->absent_deduction > 0)
                <tr>
                    <td class="label">Absent Deduction</td>
                    <td class="amount negative">-${{ number_format($payroll->absent_deduction, 2) }}</td>
                </tr>
                @endif
                
                @if($payroll->tax_deduction > 0)
                <tr>
                    <td class="label">Automatic Tax</td>
                    <td class="amount negative">-${{ number_format($payroll->tax_deduction, 2) }}</td>
                </tr>
                @endif
                
                <!-- Government Deductions -->
                @if($payroll->sss_manual > 0)
                <tr>
                    <td class="label">SSS (Social Security System)</td>
                    <td class="amount negative">-${{ number_format($payroll->sss_manual, 2) }}</td>
                </tr>
                @endif
                
                @if($payroll->philhealth_manual > 0)
                <tr>
                    <td class="label">PhilHealth</td>
                    <td class="amount negative">-${{ number_format($payroll->philhealth_manual, 2) }}</td>
                </tr>
                @endif
                
                @if($payroll->pag_ibig_manual > 0)
                <tr>
                    <td class="label">Pag-IBIG</td>
                    <td class="amount negative">-${{ number_format($payroll->pag_ibig_manual, 2) }}</td>
                </tr>
                @endif
                
                @if($payroll->tax_manual > 0)
                <tr>
                    <td class="label">Income Tax (BIR)</td>
                    <td class="amount negative">-${{ number_format($payroll->tax_manual, 2) }}</td>
                </tr>
                @endif
                
                <!-- Additional Deductions -->
                @foreach((is_object($payroll->deductions) || is_array($payroll->deductions)) ? $payroll->deductions : [] as $deduction)
                <tr>
                    <td class="label">{{ $deduction->deduction_name }}</td>
                    <td class="amount negative">-${{ number_format($deduction->amount, 2) }}</td>
                </tr>
                @endforeach
                
                <tr class="total-row">
                    <td class="label">Total Deductions</td>
                    <td class="amount negative">-${{ number_format($payroll->total_deductions, 2) }}</td>
                </tr>
            </table>
        </div>
        
        <!-- Summary -->
        <div class="summary">
            <div class="summary-box">
                <div class="summary-label">GROSS SALARY</div>
                <div class="summary-value">${{ number_format($payroll->gross_salary, 2) }}</div>
            </div>
            <div class="summary-box">
                <div class="summary-label">TOTAL DEDUCTIONS</div>
                <div class="summary-value negative">-${{ number_format($payroll->total_deductions, 2) }}</div>
            </div>
            <div class="summary-box net-salary-box">
                <div class="summary-label">NET SALARY (TAKE-HOME PAY)</div>
                <div class="summary-value">${{ number_format($payroll->net_salary, 2) }}</div>
            </div>
        </div>
        
        <!-- Notes -->
        <div class="notes">
            <strong>Important Notice:</strong> All government deductions (SSS, PhilHealth, Pag-IBIG, Income Tax) are manually entered by the HR/Admin. 
            Please verify the accuracy of these deductions. If there are any discrepancies, please contact the HR department immediately.
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>This is an officially generated payslip. For inquiries, contact Human Resources Department.</p>
            <p>Generated on <span id="print-generated-on">Loading...</span></p>
        </div>
    </div>
    
    <script>
        function getManilaTimeString() {
            // Force Asia/Manila regardless of server/client timezone
            return new Date().toLocaleString('en-US', {
                timeZone: 'Asia/Manila',
                year: 'numeric',
                month: 'short',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
        }

        window.addEventListener('load', function() {
            var el = document.getElementById('print-generated-on');
            if (el) el.textContent = getManilaTimeString();
            var el2 = document.getElementById('print-generated-on-alt');
            if (el2) el2.textContent = getManilaTimeString();
            window.print();
        });
    </script>
</body>
</html>

            <h2>Employee Information</h2>
            <div class="info-grid">
                <div class="info-item">
                    <span>Employee ID:</span>
                    <span>{{ $payroll->employee->emp_id ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span>Name:</span>
                    <span>{{ $payroll->employee->full_name ?? 'N/A' }}</span>
                </div>
<div class="info-item">
                    <span>Department:</span>
                    <span>{{ $payroll->employee->department->dept_name ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span>Position:</span>
                    <span>{{ $payroll->employee->position->position_name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
        
        <div class="earnings-deductions">
            <div class="section earnings">
                <h2>Earnings</h2>
                <div class="line-item">
                    <span class="label">Basic Salary</span>
                    <span class="amount">${{ number_format($payroll->basic_salary, 2) }}</span>
                </div>
                <div class="line-item">
                    <span class="label">Overtime Pay</span>
                    <span class="amount">${{ number_format($payroll->overtime_pay, 2) }}</span>
                </div>
                <div class="line-item">
                    <span class="label">Gross Salary</span>
                    <span class="amount">${{ number_format($payroll->gross_salary, 2) }}</span>
                </div>
            </div>
            
            <div class="section deductions">
                <h2>Deductions</h2>
                <div class="line-item">
                    <span class="label">Late Deduction</span>
                    <span class="amount">${{ number_format($payroll->late_deduction, 2) }}</span>
                </div>
                <div class="line-item">
                    <span class="label">Absent Deduction</span>
                    <span class="amount">${{ number_format($payroll->absent_deduction, 2) }}</span>
                </div>
                <div class="line-item">
                    <span class="label">Tax</span>
                    <span class="amount">${{ number_format($payroll->tax_deduction, 2) }}</span>
                </div>
                <div class="line-item" style="font-weight: 600; border-top: 1px solid #eee; margin-top: 10px; padding-top: 10px;">
                    <span class="label">Total Deductions</span>
                    <span class="amount">${{ number_format($payroll->total_deductions, 2) }}</span>
                </div>
            </div>
        </div>
        
        <div class="total-net">
            <div class="label">Net Pay</div>
            <div class="amount">${{ number_format($payroll->net_salary, 2) }}</div>
        </div>
        
        @if($payroll->computation_details)
        <div style="margin-top: 20px; padding: 15px; background: #f9f9f9; border-radius: 6px; font-size: 12px;">
            <strong>Computation Details:</strong><br>
            <pre style="white-space: pre-wrap; font-family: inherit; margin-top: 5px;">{{ $payroll->computation_details }}</pre>
        </div>
        @endif
        
        <div class="footer">
            <p>This is a computer-generated document. No signature required.</p>
            <p>Generated on <span id="print-generated-on-alt">Loading...</span></p>
        </div>
    </div>
</body>
</html>
