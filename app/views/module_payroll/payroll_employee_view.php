<?php include '../app/views/partials/admin-header.php'; ?>

<?php

$runId = (int) ($run['id'] ?? 0);
$runEmployeeId = (int) ($employee['id'] ?? 0);

$status = strtoupper((string) ($run['status'] ?? 'DRAFT'));
$computationStatus = strtoupper(
    (string) ($employee['computation_status'] ?? 'PENDING')
);

$earnings = $earnings ?? [];
$deductions = $deductions ?? [];
$days = $days ?? [];


/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function payrollEmployeeMoney($value): string
{
    return number_format((float) $value, 2);
}


function payrollEmployeeDate(?string $value): string
{
    if (!$value) {
        return '-';
    }

    $date = DateTimeImmutable::createFromFormat(
        'Y-m-d',
        substr($value, 0, 10)
    );

    return $date
        ? $date->format('M d, Y')
        : htmlspecialchars($value);
}


function payrollEmployeeTime(?string $value): string
{
    if (!$value) {
        return '-';
    }

    try {
        return (new DateTimeImmutable($value))->format('h:i A');
    } catch (Throwable $e) {
        return htmlspecialchars($value);
    }
}


function payrollEmployeeFullName(array $employee): string
{
    $lastName = trim((string) ($employee['last_name'] ?? ''));
    $firstName = trim((string) ($employee['first_name'] ?? ''));
    $middleName = trim((string) ($employee['middle_name'] ?? ''));

    if (
        $lastName === ''
        && $firstName === ''
        && $middleName === ''
    ) {
        return 'Employee #' . (int) ($employee['employee_id'] ?? 0);
    }

    $name = $lastName;

    if ($firstName !== '') {
        $name .= ($name !== '' ? ', ' : '') . $firstName;
    }

    if ($middleName !== '') {
        $name .= ' ' . mb_substr($middleName, 0, 1) . '.';
    }

    return $name;
}


function payrollEmployeeStatusBadge(string $status): string
{
    $classes = [
        'PENDING' => 'text-bg-secondary',
        'COMPUTED' => 'text-bg-success',
        'ERROR' => 'text-bg-danger',
    ];

    $class = $classes[$status] ?? 'text-bg-secondary';

    return '<span class="badge ' . $class . '">' .
        htmlspecialchars(ucwords(strtolower($status))) .
        '</span>';
}


/*
|--------------------------------------------------------------------------
| Attendance summaries
|--------------------------------------------------------------------------
*/

$daysPresent = 0;
$totalLateMinutes = 0;
$totalUndertimeMinutes = 0;
$totalOvertimeMinutes = 0;
$totalActualHours = 0;
$totalExpectedHours = 0;

foreach ($days as $day) {

    if ((int) ($day['is_present'] ?? 0) === 1) {
        $daysPresent++;
    }

    $totalLateMinutes += (int) ($day['late_minutes'] ?? 0);
    $totalUndertimeMinutes += (int) ($day['undertime_minutes'] ?? 0);
    $totalOvertimeMinutes += (int) ($day['overtime_minutes'] ?? 0);

    $totalActualHours += (float) ($day['actual_hours'] ?? 0);
    $totalExpectedHours += (float) ($day['expected_hours'] ?? 0);
}


/*
|--------------------------------------------------------------------------
| Payroll totals
|--------------------------------------------------------------------------
*/

$basicPay = (float) ($employee['basic_pay'] ?? 0);
$grossPay = (float) ($employee['gross_pay'] ?? 0);
$totalEarnings = (float) ($employee['total_earnings'] ?? 0);
$totalDeductions = (float) ($employee['total_deductions'] ?? 0);
$netPay = (float) ($employee['net_pay'] ?? 0);

?>

<style>
    .payroll-page-card {
        border: 1px solid var(--bs-border-color);
        border-radius: .75rem;
        background: var(--bs-body-bg);
    }


    .payroll-summary-card {
        border: 1px solid var(--bs-border-color);
        border-radius: .75rem;
        background: var(--bs-body-bg);
        height: 100%;
    }


    .payroll-summary-card .label {
        font-size: .76rem;
        color: var(--bs-secondary-color);
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: .25rem;
    }


    .payroll-summary-card .value {
        font-size: 1.05rem;
        font-weight: 600;
    }


    .payroll-total-box {
        border: 1px solid var(--bs-border-color);
        border-radius: .75rem;
        padding: 1rem;
        height: 100%;
    }


    .payroll-total-box .label {
        color: var(--bs-secondary-color);
        font-size: .78rem;
        margin-bottom: .25rem;
    }


    .payroll-total-box .amount {
        font-size: 1.35rem;
        font-weight: 700;
    }


    .payroll-section-title {
        font-size: .82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: var(--bs-secondary-color);
        margin-bottom: .75rem;
    }


    .payroll-item-table td,
    .payroll-item-table th {
        vertical-align: middle;
    }


    .payroll-item-description {
        min-width: 220px;
    }


    .attendance-status {
        min-width: 85px;
    }


    /*
    |--------------------------------------------------------------------------
    | Payslip
    |--------------------------------------------------------------------------
    */

    .payslip-wrapper {
        max-width: 900px;
        margin: 0 auto;
        border: 1px solid var(--bs-border-color);
        border-radius: .75rem;
        background: #fff;
    }


    .payslip-header {
        border-bottom: 1px solid #dee2e6;
    }


    .payslip-company-name {
        font-size: 1.2rem;
        font-weight: 700;
    }


    .payslip-title {
        font-size: 1rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
    }


    .payslip-meta-label {
        color: #6c757d;
        font-size: .75rem;
    }


    .payslip-meta-value {
        font-weight: 600;
    }


    .payslip-section-title {
        font-size: .8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: .4rem;
        margin-bottom: .65rem;
    }


    .payslip-net-pay {
        border-top: 2px solid #212529;
        margin-top: 1rem;
        padding-top: 1rem;
    }


    .payslip-net-pay .amount {
        font-size: 1.6rem;
        font-weight: 700;
    }


    @media print {

        body * {
            visibility: hidden;
        }

        #payslipPrintArea,
        #payslipPrintArea * {
            visibility: visible;
        }

        #payslipPrintArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none;
        }

        .no-print {
            display: none !important;
        }

    }
</style>


<div class="py-4 payruns-ui payruns-employee">

    <!-- ============================================================
         HEADER
    ============================================================= -->

    <header class="d-flex flex-column flex-xl-row justify-content-between gap-3 mb-4 payruns-header">

        <div>

            <p class="payruns-eyebrow no-print">Employee Payroll Details</p>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                <h1 class="h4 mb-0" id="employeePayrollHeading">
                    <?= htmlspecialchars(
                        payrollEmployeeFullName($employee)
                    ); ?>
                </h1>

                <?= payrollEmployeeStatusBadge($computationStatus); ?>

            </div>


            <div class="text-muted">

                <?= htmlspecialchars(
                    $employee['employee_no']
                    ?? 'Employee #' . ($employee['employee_id'] ?? '')
                ); ?>

                <span class="mx-1">•</span>

                <?= htmlspecialchars(
                    $run['run_code'] ?? 'Payroll Run'
                ); ?>

            </div>

        </div>


        <div class="d-flex flex-wrap gap-2 align-items-start">

            <a href="/payroll/runs/view/<?= $runId; ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Back to Payroll Run
            </a>


            <button type="button" class="btn btn-outline-dark" onclick="printPayslip()">
                <i class="bi bi-printer" aria-hidden="true"></i>
                Print Payslip
            </button>

        </div>

    </header>


    <!-- ============================================================
         SUMMARY
    ============================================================= -->

    <div class="row g-3 mb-4">

        <div class="col-12 col-md-6 col-xl-3">

            <div class="payroll-summary-card p-3">

                <div class="label">
                    Salary Type
                </div>

                <div class="value">
                    <?= htmlspecialchars(
                        $employee['salary_type'] ?? '-'
                    ); ?>
                </div>

                <div class="small text-muted">
                    <?= htmlspecialchars(
                        $employee['payroll_frequency'] ?? '-'
                    ); ?>
                </div>

            </div>

        </div>


        <div class="col-12 col-md-6 col-xl-3">

            <div class="payroll-summary-card p-3">

                <div class="label">
                    Salary Rate
                </div>

                <div class="value">
                    ₱<?= payrollEmployeeMoney(
                        $employee['salary_rate'] ?? 0
                    ); ?>
                </div>

            </div>

        </div>


        <div class="col-12 col-md-6 col-xl-3">

            <div class="payroll-summary-card p-3">

                <div class="label">
                    Cutoff Period
                </div>

                <div class="value">
                    <?= payrollEmployeeDate(
                        $run['cutoff_date_from'] ?? null
                    ); ?>
                </div>

                <div class="small text-muted">
                    to
                    <?= payrollEmployeeDate(
                        $run['cutoff_date_to'] ?? null
                    ); ?>
                </div>

            </div>

        </div>


        <div class="col-12 col-md-6 col-xl-3">

            <div class="payroll-summary-card p-3">

                <div class="label">
                    Pay Date
                </div>

                <div class="value">
                    <?= payrollEmployeeDate(
                        $run['pay_date'] ?? null
                    ); ?>
                </div>

            </div>

        </div>

    </div>


    <!-- ============================================================
         TOTALS
    ============================================================= -->

    <div class="row g-3 mb-4">

        <div class="col-12 col-md-6 col-xl-3">

            <div class="payroll-total-box">

                <div class="label">
                    Basic Pay
                </div>

                <div class="amount">
                    ₱<?= payrollEmployeeMoney($basicPay); ?>
                </div>

            </div>

        </div>


        <div class="col-12 col-md-6 col-xl-3">

            <div class="payroll-total-box">

                <div class="label">
                    Earnings
                </div>

                <div class="amount">
                    ₱<?= payrollEmployeeMoney($totalEarnings); ?>
                </div>

            </div>

        </div>


        <div class="col-12 col-md-6 col-xl-3">

            <div class="payroll-total-box">

                <div class="label">
                    Deductions
                </div>

                <div class="amount">
                    ₱<?= payrollEmployeeMoney($totalDeductions); ?>
                </div>

            </div>

        </div>


        <div class="col-12 col-md-6 col-xl-3">

            <div class="payroll-total-box payruns-net-card">

                <div class="label">
                    Net Pay
                </div>

                <div class="amount">
                    ₱<?= payrollEmployeeMoney($netPay); ?>
                </div>

            </div>

        </div>

    </div>


    <div class="payruns-gross-context mb-4 no-print"><span>Gross Pay</span><strong>₱<?= payrollEmployeeMoney($grossPay); ?></strong></div>

    <!-- ============================================================
         TABS
    ============================================================= -->

    <div class="card shadow-sm">

        <div class="card-header bg-white pt-3 px-3 pb-0">

            <ul class="nav nav-tabs" id="employeePayrollTabs" role="tablist">

                <li class="nav-item" role="presentation">

                    <button type="button" class="nav-link active" data-bs-toggle="tab"
                        data-bs-target="#payrollDetailsTab" id="payrollDetailsTabButton" role="tab" aria-controls="payrollDetailsTab" aria-selected="true">
                        <i class="bi bi-receipt me-1" aria-hidden="true"></i>
                        Payroll Details
                    </button>

                </li>


                <li class="nav-item" role="presentation">

                    <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#attendanceTab" id="attendanceTabButton" role="tab" aria-controls="attendanceTab" aria-selected="false">
                        <i class="bi bi-clock-history me-1" aria-hidden="true"></i>
                        Attendance
                    </button>

                </li>


                <li class="nav-item" role="presentation">

                    <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#payslipTab" id="payslipTabButton" role="tab" aria-controls="payslipTab" aria-selected="false">
                        <i class="bi bi-file-earmark-text me-1" aria-hidden="true"></i>
                        Payslip
                    </button>

                </li>

            </ul>

        </div>


        <div class="card-body">

            <div class="tab-content">


                <!-- ====================================================
                     PAYROLL DETAILS
                ===================================================== -->

                <div class="tab-pane fade show active" id="payrollDetailsTab" role="tabpanel" aria-labelledby="payrollDetailsTabButton" tabindex="0">

                    <div class="row g-4">


                        <!-- Earnings -->

                        <div class="col-12 col-xl-6">

                            <div class="payroll-section-title" role="heading" aria-level="2">
                                Earnings
                            </div>


                            <p class="payruns-scroll-help no-print">Scroll horizontally to see every column.</p>
                            <div class="table-responsive payruns-table-region" tabindex="0" role="region" aria-label="Earnings items">

                                <table class="table table-sm payroll-item-table">

                                    <thead class="table-light">

                                        <tr>
                                            <th scope="col">Description</th>
                                            <th scope="col">Source</th>
                                            <th scope="col" class="text-end">
                                                Amount
                                            </th>
                                        </tr>

                                    </thead>


                                    <tbody>

                                        <?php if (empty($earnings)): ?>

                                            <tr>

                                                <td colspan="3" class="text-center py-4 text-muted">
                                                    No earnings found.
                                                </td>

                                            </tr>

                                        <?php else: ?>

                                            <?php foreach ($earnings as $item): ?>

                                                <tr>

                                                    <td class="payroll-item-description">

                                                        <div class="fw-semibold">
                                                            <?= htmlspecialchars(
                                                                $item['description']
                                                                ?? $item['item_code']
                                                                ?? ''
                                                            ); ?>
                                                        </div>

                                                        <div class="small text-muted">
                                                            <?= htmlspecialchars(
                                                                $item['item_code']
                                                                ?? ''
                                                            ); ?>
                                                        </div>

                                                    </td>


                                                    <td>

                                                        <span class="badge text-bg-light border">

                                                            <?= htmlspecialchars(
                                                                $item['source_type']
                                                                ?? 'SYSTEM'
                                                            ); ?>

                                                        </span>

                                                    </td>


                                                    <td class="text-end">

                                                        ₱<?= payrollEmployeeMoney(
                                                            $item['amount']
                                                            ?? 0
                                                        ); ?>

                                                    </td>

                                                </tr>

                                            <?php endforeach; ?>

                                        <?php endif; ?>

                                    </tbody>


                                    <tfoot class="table-light">

                                        <tr>

                                            <th scope="col" colspan="2">
                                                Total Earnings
                                            </th>

                                            <th scope="col" class="text-end">
                                                ₱<?= payrollEmployeeMoney(
                                                    $totalEarnings
                                                ); ?>
                                            </th>

                                        </tr>

                                    </tfoot>

                                </table>

                            </div>

                        </div>



                        <!-- Deductions -->

                        <div class="col-12 col-xl-6">

                            <div class="payroll-section-title" role="heading" aria-level="2">
                                Deductions
                            </div>


                            <p class="payruns-scroll-help no-print">Scroll horizontally to see every column.</p>
                            <div class="table-responsive payruns-table-region" tabindex="0" role="region" aria-label="Deduction items">

                                <table class="table table-sm payroll-item-table">

                                    <thead class="table-light">

                                        <tr>

                                            <th scope="col">Description</th>

                                            <th scope="col">Source</th>

                                            <th scope="col" class="text-end">
                                                Amount
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        <?php if (empty($deductions)): ?>

                                            <tr>

                                                <td colspan="3" class="text-center py-4 text-muted">
                                                    No deductions found.
                                                </td>

                                            </tr>

                                        <?php else: ?>

                                            <?php foreach ($deductions as $item): ?>

                                                <tr>

                                                    <td class="payroll-item-description">

                                                        <div class="fw-semibold">

                                                            <?= htmlspecialchars(
                                                                $item['description']
                                                                ?? $item['item_code']
                                                                ?? ''
                                                            ); ?>

                                                        </div>

                                                        <div class="small text-muted">

                                                            <?= htmlspecialchars(
                                                                $item['item_code']
                                                                ?? ''
                                                            ); ?>

                                                        </div>

                                                    </td>


                                                    <td>

                                                        <span class="badge text-bg-light border">

                                                            <?= htmlspecialchars(
                                                                $item['source_type']
                                                                ?? 'SYSTEM'
                                                            ); ?>

                                                        </span>

                                                    </td>


                                                    <td class="text-end">

                                                        ₱<?= payrollEmployeeMoney(
                                                            $item['amount']
                                                            ?? 0
                                                        ); ?>

                                                    </td>

                                                </tr>

                                            <?php endforeach; ?>

                                        <?php endif; ?>

                                    </tbody>


                                    <tfoot class="table-light">

                                        <tr>

                                            <th scope="col" colspan="2">
                                                Total Deductions
                                            </th>

                                            <th scope="col" class="text-end">

                                                ₱<?= payrollEmployeeMoney(
                                                    $totalDeductions
                                                ); ?>

                                            </th>

                                        </tr>

                                    </tfoot>

                                </table>

                            </div>

                        </div>

                    </div>


                    <!-- NET PAY -->

                    <div class="border-top mt-4 pt-4">

                        <div class="row justify-content-end">

                            <div class="col-12 col-md-5 col-xl-4">

                                <div class="d-flex justify-content-between align-items-center">

                                    <span class="fw-semibold fs-5">
                                        Net Pay
                                    </span>

                                    <span class="fw-bold fs-3">
                                        ₱<?= payrollEmployeeMoney(
                                            $netPay
                                        ); ?>
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- ====================================================
                     ATTENDANCE
                ===================================================== -->

                <div class="tab-pane fade" id="attendanceTab" role="tabpanel" aria-labelledby="attendanceTabButton" tabindex="0">

                    <div class="row g-3 mb-4">

                        <div class="col-6 col-md-4 col-xl-2">

                            <div class="payroll-summary-card p-3">

                                <div class="label">
                                    Present
                                </div>

                                <div class="value">
                                    <?= number_format($daysPresent); ?>
                                </div>

                            </div>

                        </div>


                        <div class="col-6 col-md-4 col-xl-2">

                            <div class="payroll-summary-card p-3">

                                <div class="label">
                                    Late
                                </div>

                                <div class="value">
                                    <?= number_format(
                                        $totalLateMinutes
                                    ); ?> min
                                </div>

                            </div>

                        </div>


                        <div class="col-6 col-md-4 col-xl-2">

                            <div class="payroll-summary-card p-3">

                                <div class="label">
                                    Undertime
                                </div>

                                <div class="value">
                                    <?= number_format(
                                        $totalUndertimeMinutes
                                    ); ?> min
                                </div>

                            </div>

                        </div>


                        <div class="col-6 col-md-4 col-xl-2">

                            <div class="payroll-summary-card p-3">

                                <div class="label">
                                    Overtime
                                </div>

                                <div class="value">
                                    <?= number_format(
                                        $totalOvertimeMinutes
                                    ); ?> min
                                </div>

                            </div>

                        </div>


                        <div class="col-6 col-md-4 col-xl-2">

                            <div class="payroll-summary-card p-3">

                                <div class="label">
                                    Expected
                                </div>

                                <div class="value">
                                    <?= number_format(
                                        $totalExpectedHours,
                                        2
                                    ); ?> hrs
                                </div>

                            </div>

                        </div>


                        <div class="col-6 col-md-4 col-xl-2">

                            <div class="payroll-summary-card p-3">

                                <div class="label">
                                    Actual
                                </div>

                                <div class="value">
                                    <?= number_format(
                                        $totalActualHours,
                                        2
                                    ); ?> hrs
                                </div>

                            </div>

                        </div>

                    </div>


                    <p class="payruns-scroll-help no-print">Scroll horizontally to see every column.</p>
                            <div class="table-responsive payruns-table-region" tabindex="0" role="region" aria-label="Attendance records">

                        <table class="table table-hover table-sm align-middle">

                            <thead class="table-light">

                                <tr>

                                    <th scope="col">Date</th>

                                    <th scope="col">Day Type</th>

                                    <th scope="col">Schedule</th>

                                    <th scope="col">Time In</th>

                                    <th scope="col">Time Out</th>

                                    <th scope="col" class="text-end">
                                        Late (min)
                                    </th>

                                    <th scope="col" class="text-end">
                                        Undertime (min)
                                    </th>

                                    <th scope="col" class="text-end">
                                        Overtime (min)
                                    </th>

                                    <th scope="col" class="text-center">
                                        Status
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php if (empty($days)): ?>

                                    <tr>

                                        <td colspan="9" class="text-center py-5 text-muted">
                                            No attendance records found.
                                        </td>

                                    </tr>

                                <?php else: ?>

                                    <?php foreach ($days as $day): ?>

                                        <?php

                                        $isPresent =
                                            (int) ($day['is_present'] ?? 0)
                                            === 1;

                                        $isHoliday =
                                            (int) ($day['is_holiday'] ?? 0)
                                            === 1;

                                        ?>

                                        <tr>

                                            <td>

                                                <?= payrollEmployeeDate(
                                                    $day['attendance_date']
                                                    ?? null
                                                ); ?>

                                            </td>


                                            <td>

                                                <?php if ($isHoliday): ?>

                                                    <span class="badge text-bg-warning" title="<?= htmlspecialchars(
                                                        $day['holiday_name']
                                                        ?? ''
                                                    ); ?>">
                                                        Holiday
                                                    </span>

                                                <?php else: ?>

                                                    <?= htmlspecialchars(
                                                        $day['day_type']
                                                        ?? 'REGULAR'
                                                    ); ?>

                                                <?php endif; ?>

                                            </td>


                                            <td>

                                                <?php
                                                $scheduledIn =
                                                    $day['scheduled_time_in']
                                                    ?? null;

                                                $scheduledOut =
                                                    $day['scheduled_time_out']
                                                    ?? null;
                                                ?>

                                                <?php if (
                                                    $scheduledIn
                                                    && $scheduledOut
                                                ): ?>

                                                    <?= htmlspecialchars(
                                                        date(
                                                            'h:i A',
                                                            strtotime(
                                                                $scheduledIn
                                                            )
                                                        )
                                                    ); ?>

                                                    -

                                                    <?= htmlspecialchars(
                                                        date(
                                                            'h:i A',
                                                            strtotime(
                                                                $scheduledOut
                                                            )
                                                        )
                                                    ); ?>

                                                <?php else: ?>

                                                    -

                                                <?php endif; ?>

                                            </td>


                                            <td>
                                                <?= payrollEmployeeTime(
                                                    $day['time_in']
                                                    ?? null
                                                ); ?>
                                            </td>


                                            <td>
                                                <?= payrollEmployeeTime(
                                                    $day['time_out']
                                                    ?? null
                                                ); ?>
                                            </td>


                                            <td class="text-end">
                                                <?= number_format(
                                                    (int) (
                                                        $day['late_minutes']
                                                        ?? 0
                                                    )
                                                ); ?>
                                            </td>


                                            <td class="text-end">
                                                <?= number_format(
                                                    (int) (
                                                        $day['undertime_minutes']
                                                        ?? 0
                                                    )
                                                ); ?>
                                            </td>


                                            <td class="text-end">
                                                <?= number_format(
                                                    (int) (
                                                        $day['overtime_minutes']
                                                        ?? 0
                                                    )
                                                ); ?>
                                            </td>


                                            <td class="text-center">

                                                <?php if ($isPresent): ?>

                                                    <span class="badge text-bg-success attendance-status">
                                                        Present
                                                    </span>

                                                <?php elseif ($isHoliday): ?>

                                                    <span class="badge text-bg-warning attendance-status">
                                                        Holiday
                                                    </span>

                                                <?php else: ?>

                                                    <span class="badge text-bg-secondary attendance-status">
                                                        No Log
                                                    </span>

                                                <?php endif; ?>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>



                <!-- ====================================================
                     PAYSLIP
                ===================================================== -->

                <div class="tab-pane fade" id="payslipTab">

                    <div class="d-flex justify-content-end mb-3 no-print">

                        <button type="button" class="btn btn-outline-dark btn-sm" onclick="printPayslip()">
                            <i class="bi bi-printer"></i>
                            Print Payslip
                        </button>

                    </div>


                    <div class="payslip-wrapper p-4" id="payslipPrintArea">


                        <!-- PAYSLIP HEADER -->

                        <div class="payslip-header text-center pb-3 mb-4">

                            <div class="payslip-company-name">

                                <?= htmlspecialchars(
                                    $_SESSION['tenant']['name']
                                    ?? 'Company Name'
                                ); ?>

                            </div>


                            <div class="payslip-title mt-2">
                                Payslip
                            </div>


                            <div class="small text-muted mt-1">

                                <?= payrollEmployeeDate(
                                    $run['cutoff_date_from']
                                    ?? null
                                ); ?>

                                -

                                <?= payrollEmployeeDate(
                                    $run['cutoff_date_to']
                                    ?? null
                                ); ?>

                            </div>

                        </div>



                        <!-- EMPLOYEE INFORMATION -->

                        <div class="row g-3 mb-4">

                            <div class="col-12 col-md-6">

                                <div class="payslip-meta-label">
                                    Employee
                                </div>

                                <div class="payslip-meta-value">

                                    <?= htmlspecialchars(
                                        payrollEmployeeFullName(
                                            $employee
                                        )
                                    ); ?>

                                </div>

                            </div>


                            <div class="col-6 col-md-3">

                                <div class="payslip-meta-label">
                                    Employee No.
                                </div>

                                <div class="payslip-meta-value">

                                    <?= htmlspecialchars(
                                        $employee['employee_no']
                                        ?? '-'
                                    ); ?>

                                </div>

                            </div>


                            <div class="col-6 col-md-3">

                                <div class="payslip-meta-label">
                                    Pay Date
                                </div>

                                <div class="payslip-meta-value">

                                    <?= payrollEmployeeDate(
                                        $run['pay_date']
                                        ?? null
                                    ); ?>

                                </div>

                            </div>


                            <div class="col-6 col-md-3">

                                <div class="payslip-meta-label">
                                    Salary Type
                                </div>

                                <div class="payslip-meta-value">

                                    <?= htmlspecialchars(
                                        $employee['salary_type']
                                        ?? '-'
                                    ); ?>

                                </div>

                            </div>


                            <div class="col-6 col-md-3">

                                <div class="payslip-meta-label">
                                    Salary Rate
                                </div>

                                <div class="payslip-meta-value">

                                    ₱<?= payrollEmployeeMoney(
                                        $employee['salary_rate']
                                        ?? 0
                                    ); ?>

                                </div>

                            </div>


                            <div class="col-6 col-md-3">

                                <div class="payslip-meta-label">
                                    Days Present
                                </div>

                                <div class="payslip-meta-value">
                                    <?= number_format(
                                        $daysPresent
                                    ); ?>
                                </div>

                            </div>


                            <div class="col-6 col-md-3">

                                <div class="payslip-meta-label">
                                    Payroll Run
                                </div>

                                <div class="payslip-meta-value">

                                    <?= htmlspecialchars(
                                        $run['run_code']
                                        ?? '-'
                                    ); ?>

                                </div>

                            </div>

                        </div>



                        <!-- PAYSLIP ITEMS -->

                        <div class="row g-4">

                            <!-- Earnings -->

                            <div class="col-12 col-md-6">

                                <div class="payslip-section-title">
                                    Earnings
                                </div>


                                <?php if (empty($earnings)): ?>

                                    <div class="text-muted small">
                                        No earnings.
                                    </div>

                                <?php else: ?>

                                    <?php foreach ($earnings as $item): ?>

                                        <div class="d-flex justify-content-between gap-3 mb-2">

                                            <div>

                                                <?= htmlspecialchars(
                                                    $item['description']
                                                    ?? $item['item_code']
                                                    ?? ''
                                                ); ?>

                                            </div>

                                            <div class="text-nowrap">

                                                ₱<?= payrollEmployeeMoney(
                                                    $item['amount']
                                                    ?? 0
                                                ); ?>

                                            </div>

                                        </div>

                                    <?php endforeach; ?>

                                <?php endif; ?>


                                <div class="d-flex justify-content-between border-top pt-2 mt-3 fw-semibold">

                                    <span>
                                        Total Earnings
                                    </span>

                                    <span>
                                        ₱<?= payrollEmployeeMoney(
                                            $totalEarnings
                                        ); ?>
                                    </span>

                                </div>

                            </div>



                            <!-- Deductions -->

                            <div class="col-12 col-md-6">

                                <div class="payslip-section-title">
                                    Deductions
                                </div>


                                <?php if (empty($deductions)): ?>

                                    <div class="text-muted small">
                                        No deductions.
                                    </div>

                                <?php else: ?>

                                    <?php foreach ($deductions as $item): ?>

                                        <div class="d-flex justify-content-between gap-3 mb-2">

                                            <div>

                                                <?= htmlspecialchars(
                                                    $item['description']
                                                    ?? $item['item_code']
                                                    ?? ''
                                                ); ?>

                                            </div>

                                            <div class="text-nowrap">

                                                ₱<?= payrollEmployeeMoney(
                                                    $item['amount']
                                                    ?? 0
                                                ); ?>

                                            </div>

                                        </div>

                                    <?php endforeach; ?>

                                <?php endif; ?>


                                <div class="d-flex justify-content-between border-top pt-2 mt-3 fw-semibold">

                                    <span>
                                        Total Deductions
                                    </span>

                                    <span>
                                        ₱<?= payrollEmployeeMoney(
                                            $totalDeductions
                                        ); ?>
                                    </span>

                                </div>

                            </div>

                        </div>



                        <!-- NET PAY -->

                        <div class="payslip-net-pay">

                            <div class="d-flex justify-content-between align-items-end">

                                <div>

                                    <div class="small text-muted">
                                        Net Pay
                                    </div>

                                    <div class="fw-semibold">
                                        Amount Payable
                                    </div>

                                </div>


                                <div class="amount">

                                    ₱<?= payrollEmployeeMoney(
                                        $netPay
                                    ); ?>

                                </div>

                            </div>

                        </div>



                        <!-- SIGNATURE -->

                        <div class="row mt-5 pt-4">

                            <div class="col-6">

                                <div class="border-top text-center pt-2 small">
                                    Prepared By
                                </div>

                            </div>


                            <div class="col-6">

                                <div class="border-top text-center pt-2 small">
                                    Employee Signature
                                </div>

                            </div>

                        </div>


                    </div>

                </div>


            </div>

        </div>

    </div>

</div>


<?php include '../app/views/partials/admin-footer.php'; ?>


<script>

    function printPayslip() {

        const payslipTabButton = document.querySelector(
            '[data-bs-target="#payslipTab"]'
        );

        if (payslipTabButton) {

            const tab = bootstrap.Tab.getOrCreateInstance(
                payslipTabButton
            );

            tab.show();

        }

        setTimeout(function () {
            window.print();
        }, 150);

    }

</script>

</body>

</html>