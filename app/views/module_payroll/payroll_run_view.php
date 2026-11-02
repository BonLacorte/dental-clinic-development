<?php include '../app/views/partials/admin-header.php'; ?>
<div class="d-print-none">
    <?php include '../app/views/module_payroll/partials/payroll_nav.php'; ?>
</div>

<?php
$runId = (int) ($run['id'] ?? 0);
$status = strtoupper((string) ($run['status'] ?? 'DRAFT'));
$periodNo = (int) ($run['period_no'] ?? 0);
$employeeCount = count($employees ?? []);

$totalGrossPay = 0.00;
$totalEarnings = 0.00;
$totalDeductions = 0.00;
$totalNetPay = 0.00;

foreach (($employees ?? []) as $employee) {
    $totalGrossPay += (float) ($employee['gross_pay'] ?? 0);
    $totalEarnings += (float) ($employee['total_earnings'] ?? 0);
    $totalDeductions += (float) ($employee['total_deductions'] ?? 0);
    $totalNetPay += (float) ($employee['net_pay'] ?? 0);
}

function payrollStatusBadge(string $status): string
{
    $classes = [
        'DRAFT' => 'text-bg-secondary',
        'PROCESSING' => 'text-bg-warning',
        'PROCESSED' => 'text-bg-info',
        'FINALIZED' => 'text-bg-success',
        'CANCELLED' => 'text-bg-danger',
    ];

    $normalized = strtoupper($status);
    $class = $classes[$normalized] ?? 'text-bg-secondary';

    return '<span class="badge ' . $class . '">' .
        htmlspecialchars(isset($classes[$normalized]) ? ucwords(strtolower($normalized)) : $status) .
        '</span>';
}

function payrollMoney($value): string
{
    return number_format((float) $value, 2);
}

function payrollDate(?string $value): string
{
    if (!$value) {
        return '-';
    }

    $date = DateTimeImmutable::createFromFormat('Y-m-d', substr($value, 0, 10));

    return $date ? $date->format('M d, Y') : htmlspecialchars($value);
}

function employeeFullName(array $employee): string
{
    $lastName = trim((string) ($employee['last_name'] ?? ''));
    $firstName = trim((string) ($employee['first_name'] ?? ''));
    $middleName = trim((string) ($employee['middle_name'] ?? ''));

    if ($lastName === '' && $firstName === '' && $middleName === '') {
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
?>

<style>
    .payroll-summary-card,
    .payroll-total-card {
        border: 1px solid var(--bs-border-color);
        border-radius: .75rem;
        background: var(--bs-body-bg);
        height: 100%;
    }

    .payroll-summary-card .label,
    .payroll-total-card .label {
        font-size: .78rem;
        color: var(--bs-secondary-color);
        margin-bottom: .3rem;
    }

    .payroll-summary-card .label {
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .payroll-summary-card .value {
        font-size: 1.08rem;
        font-weight: 600;
    }

    .payroll-total-card .amount {
        font-size: 1.35rem;
        font-weight: 700;
    }

    .employee-name {
        min-width: 220px;
    }

    .employee-name .employee-no {
        font-size: .75rem;
        color: var(--bs-secondary-color);
    }
</style>

<div class="py-4 payruns-ui payruns-detail">
    <header class="payruns-header">
        <div>
            <p class="payruns-eyebrow">Payroll Run Details</p>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                <h1 class="h3 mb-0" id="payrollRunHeading">
                    <?= htmlspecialchars($run['run_code'] ?? 'Payroll Run'); ?>
                </h1>
                <?= payrollStatusBadge((string) ($run['status'] ?? $status)); ?>
            </div>

            <div class="text-muted">
                <?= htmlspecialchars(
                    $run['payroll_name']
                    ?? (($periodNo === 1 ? 'First' : 'Second') . ' Semi-Monthly Payroll')
                ); ?>
            </div>
        </div>

        <div class="payruns-operations" aria-label="Payroll run actions">
            <a href="/payroll/runs" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Back to Payroll Runs
            </a>

            <?php if ($status === 'DRAFT'): ?>
                <button type="button" class="btn btn-outline-primary" id="btnLoadEmployees">
                    <i class="bi bi-people" aria-hidden="true"></i>
                    Load Employees
                </button>

                <button type="button" class="btn btn-primary" id="btnProcessPayroll">
                    <i class="bi bi-calculator" aria-hidden="true"></i>
                    Process Payroll
                </button>

                <!-- <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                    data-bs-target="#cancelPayrollRunModal">
                    <i class="bi bi-x-circle" aria-hidden="true"></i>
                    Cancel
                </button> -->


            <?php elseif ($status === 'PROCESSED'): ?>
                <button type="button" class="btn btn-success" id="btnFinalizePayroll">
                    <i class="bi bi-lock" aria-hidden="true"></i>
                    Finalize Payroll
                </button>

                <a href="/payroll/runs/print/<?= $runId; ?>" target="_blank" class="btn btn-outline-dark">
                    <i class="bi bi-printer" aria-hidden="true"></i>
                    Print Register
                </a>
            <?php elseif ($status === 'FINALIZED'): ?>
                <a href="/payroll/runs/print/<?= $runId; ?>" target="_blank" class="btn btn-outline-dark">
                    <i class="bi bi-printer" aria-hidden="true"></i>
                    Print Register
                </a>
            <?php endif; ?>

            <?php if ($status === 'DRAFT' || $status === 'CANCELLED'): ?>
                <div class="payruns-danger-actions" role="group" aria-label="High-consequence actions">
                <?php if ($status === 'DRAFT'): ?>
                <button type="button" class="btn btn-outline-danger payruns-consequence" id="btnResetPayrollRun">
                    <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                    Reset
                </button>
                <?php endif; ?>

                <button type="button" class="btn btn-outline-danger payruns-consequence" id="btnDeletePayrollRun">
                    <i class="bi bi-trash" aria-hidden="true"></i>
                    Delete
                </button>
                </div>
            <?php endif; ?>
        </div>
    </header>
    <div id="payrollRunOperationFeedback" class="payruns-feedback d-none" role="status" aria-live="polite" aria-atomic="true"></div>

    <?php if ($status === 'PROCESSING'): ?>
        <div class="alert alert-warning d-flex align-items-center gap-2">
            <div class="spinner-border spinner-border-sm" role="status"></div>
            Payroll processing is currently in progress.
        </div>
    <?php elseif ($status === 'CANCELLED'): ?>
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">This payroll run was cancelled.</div>
            <?php if (!empty($run['cancellation_reason'])): ?>
                <div><?= nl2br(htmlspecialchars($run['cancellation_reason'])); ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <h2 class="payruns-section-title">Run Overview</h2>
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="payroll-summary-card p-3">
                <div class="label">Payroll Period</div>
                <div class="value">
                    <?= $periodNo === 1 ? 'First Payroll' : 'Second Payroll'; ?>
                </div>
                <div class="small text-muted">
                    <?= $periodNo === 1 ? 'Pay on the 15th' : 'Pay on the 30th'; ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="payroll-summary-card p-3">
                <div class="label">Cutoff Period</div>
                <div class="value"><?= payrollDate($run['cutoff_date_from'] ?? null); ?></div>
                <div class="small text-muted">
                    to <?= payrollDate($run['cutoff_date_to'] ?? null); ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="payroll-summary-card p-3">
                <div class="label">Pay Date</div>
                <div class="value"><?= payrollDate($run['pay_date'] ?? null); ?></div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="payroll-summary-card p-3">
                <div class="label">Employees</div>
                <div class="value"><?= number_format($employeeCount); ?></div>
                <div class="small text-muted">Included in this run</div>
            </div>
        </div>
    </div>

    <h2 class="payruns-section-title">Financial Summary</h2>
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="payroll-total-card p-3">
                <div class="label">Gross Pay</div>
                <div class="amount">₱<?= payrollMoney($totalGrossPay); ?></div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="payroll-total-card p-3">
                <div class="label">Total Earnings</div>
                <div class="amount">₱<?= payrollMoney($totalEarnings); ?></div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="payroll-total-card p-3">
                <div class="label">Total Deductions</div>
                <div class="amount">₱<?= payrollMoney($totalDeductions); ?></div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="payroll-total-card payruns-net-card p-3">
                <div class="label">Net Pay</div>
                <div class="amount">₱<?= payrollMoney($totalNetPay); ?></div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                <div>
                    <h2 class="h5 mb-1" id="payrollResultsHeading">Employee Payroll Results</h2>
                    <small class="text-muted">
                        Search and filter the employees already loaded in this run. Totals below cover all loaded employees.
                    </small>
                </div>

                <div class="payruns-filters">
                    <div><label class="form-label" for="employeePayrollSearch">Employee in this run</label>
                    <input type="search" class="form-control form-control-sm" id="employeePayrollSearch"
                        placeholder="Name or employee number">
                    </div>
                    <div><label class="form-label" for="employeeComputationStatusFilter">Computation Status</label>

                    <select class="form-select form-select-sm" id="employeeComputationStatusFilter">
                        <option value="">All Computation Statuses</option>
                        <option value="PENDING">Pending</option>
                        <option value="COMPUTED">Computed</option>
                        <option value="ERROR">Error</option>
                    </select></div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <p id="payrollResultsScrollHelp" class="payruns-scroll-help">Scroll horizontally to compare all financial columns. Amounts are in Philippine pesos (PHP).</p>
            <p id="payrollEmployeeFilterInfo" class="payruns-feedback" role="status" aria-live="polite" aria-atomic="true">Showing all loaded employees.</p>
            <div class="table-responsive payruns-table-region" tabindex="0" role="region" aria-labelledby="payrollResultsHeading" aria-describedby="payrollResultsScrollHelp">
                <table class="table table-hover align-middle mb-0" id="payrollRunEmployeesTable">
                    <caption class="visually-hidden">Employee payroll results in Philippine pesos. Totals cover the complete run, including filtered-out rows.</caption>
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">Employee</th>
                            <th scope="col">Salary Type</th>
                            <th scope="col" class="text-end">Basic Pay</th>
                            <th scope="col" class="text-end">Gross Pay</th>
                            <th scope="col" class="text-end">Earnings</th>
                            <th scope="col" class="text-end">Deductions</th>
                            <th scope="col" class="text-end">Net Pay</th>
                            <th scope="col" class="text-center">Computation Status</th>
                            <th scope="col" class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($employees)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-3 d-block mb-2" aria-hidden="true"></i>
                                    No employees have been loaded into this payroll run.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($employees as $employee): ?>
                                <?php
                                $computationStatus = strtoupper(
                                    (string) ($employee['computation_status'] ?? 'PENDING')
                                );

                                $statusClass = [
                                    'PENDING' => 'text-bg-secondary',
                                    'COMPUTED' => 'text-bg-success',
                                    'ERROR' => 'text-bg-danger',
                                ][$computationStatus] ?? 'text-bg-secondary';

                                $searchValue = strtolower(
                                    employeeFullName($employee) . ' ' .
                                    ($employee['employee_no'] ?? '')
                                );
                                ?>

                                <tr data-search="<?= htmlspecialchars($searchValue); ?>"
                                    data-computation-status="<?= htmlspecialchars($computationStatus); ?>">
                                    <td class="ps-4 employee-name">
                                        <div class="fw-semibold">
                                            <?= htmlspecialchars(employeeFullName($employee)); ?>
                                        </div>
                                        <div class="employee-no">
                                            <?= htmlspecialchars(
                                                $employee['employee_no']
                                                ?? 'Employee #' . (int) ($employee['employee_id'] ?? 0)
                                            ); ?>
                                        </div>
                                    </td>

                                    <td><?= htmlspecialchars($employee['salary_type'] ?? '-'); ?></td>
                                    <td class="text-end"><?= payrollMoney($employee['basic_pay'] ?? 0); ?></td>
                                    <td class="text-end"><?= payrollMoney($employee['gross_pay'] ?? 0); ?></td>
                                    <td class="text-end"><?= payrollMoney($employee['total_earnings'] ?? 0); ?></td>
                                    <td class="text-end"><?= payrollMoney($employee['total_deductions'] ?? 0); ?></td>
                                    <td class="text-end fw-semibold"><?= payrollMoney($employee['net_pay'] ?? 0); ?></td>

                                    <td class="text-center">
                                        <span class="badge <?= $statusClass; ?>">
                                            <?= htmlspecialchars(ucwords(strtolower($computationStatus))); ?>
                                        </span>
                                    </td>

                                    <td class="text-end pe-4">
                                        <a href="/payroll/runs/<?= $runId; ?>/employee/<?= (int) ($employee['id'] ?? 0); ?>"
                                            class="btn btn-outline-primary btn-sm" aria-label="View payroll details for <?= htmlspecialchars(employeeFullName($employee)); ?>" title="View payroll details">
                                            <i class="bi bi-eye" aria-hidden="true"></i> View Payroll Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <tr id="noFilteredEmployeesRow" class="d-none">
                                <td colspan="9" class="text-center py-5 text-muted">
                                    No employees match the current filters.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                    <?php if (!empty($employees)): ?>
                        <tfoot class="table-light">
                            <tr>
                                <th scope="col" colspan="3" class="text-end">All loaded employees</th>
                                <th scope="col" class="text-end"><?= payrollMoney($totalGrossPay); ?></th>
                                <th scope="col" class="text-end"><?= payrollMoney($totalEarnings); ?></th>
                                <th scope="col" class="text-end"><?= payrollMoney($totalDeductions); ?></th>
                                <th scope="col" class="text-end"><?= payrollMoney($totalNetPay); ?></th>
                                <th scope="col" colspan="2"></th>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade payruns-ui payruns-dialog payroll-items-dialog" id="cancelPayrollRunModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-hidden="true" aria-labelledby="cancelPayrollRunTitle">

    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="cancelPayrollRunForm">
                <input type="hidden" name="run_id" value="<?= $runId; ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? ''); ?>">

                <div class="modal-header">
                    <h2 class="modal-title h5" id="cancelPayrollRunTitle">Cancel Payroll Run</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-warning">
                        This will cancel
                        <strong><?= htmlspecialchars($run['run_code'] ?? ''); ?></strong>.
                    </div>

                    <label class="form-label" for="cancellationReason">
                        Cancellation Reason
                    </label>

                    <textarea class="form-control" id="cancellationReason" name="cancellation_reason" rows="4"
                        maxlength="255" required></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-danger">
                        Cancel Payroll Run
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../app/views/partials/admin-footer.php'; ?>

<script>
    let payrollRunOperationInFlight = false;

    function payrollRunPending(message) {
        payrollRunOperationInFlight = true;
        $('.payruns-operations').attr('aria-busy', 'true');
        $('.payruns-operations button').prop('disabled', true);
        $('#payrollRunOperationFeedback').removeClass('d-none').data('pending', true).text(message);
    }

    function payrollRunPendingComplete() {
        payrollRunOperationInFlight = false;
        $('.payruns-operations button').prop('disabled', false);
        $('.payruns-operations').attr('aria-busy', 'false');
        const $feedback = $('#payrollRunOperationFeedback');
        if ($feedback.data('pending')) {
            $feedback.data('pending', false).text('Request finished. Review the stored run status and individual employee results.');
        }
    }
    $(document).ready(function () {
        $('#employeePayrollSearch, #employeeComputationStatusFilter').on(
            'input change',
            filterPayrollEmployees
        );

        $('#btnLoadEmployees').on('click', loadPayrollEmployees);
        $('#btnProcessPayroll').on('click', processPayrollRun);
        $('#btnFinalizePayroll').on('click', finalizePayrollRun);

        $('#btnResetPayrollRun').on('click', resetPayrollRun);

        $('#btnDeletePayrollRun').on('click', deletePayrollRun);

        $('#cancelPayrollRunForm').on('submit', function (event) {
            event.preventDefault();
            cancelPayrollRun(this);
        });
    });

    function filterPayrollEmployees() {
        const search = String($('#employeePayrollSearch').val() || '')
            .trim()
            .toLowerCase();

        const status = String(
            $('#employeeComputationStatusFilter').val() || ''
        ).toUpperCase();

        let visibleRows = 0;

        $('#payrollRunEmployeesTable tbody tr[data-search]').each(function () {
            const $row = $(this);
            const rowSearch = String($row.data('search') || '').toLowerCase();
            const rowStatus = String(
                $row.data('computation-status') || ''
            ).toUpperCase();

            const isVisible =
                (search === '' || rowSearch.includes(search)) &&
                (status === '' || rowStatus === status);

            $row.toggle(isVisible);

            if (isVisible) {
                visibleRows++;
            }
        });

        $('#noFilteredEmployeesRow').toggleClass('d-none', visibleRows !== 0);
        $('#payrollEmployeeFilterInfo').text(visibleRows + ' of ' +
            $('#payrollRunEmployeesTable tbody tr[data-search]').length +
            ' loaded employees shown. Financial totals cover all loaded employees.');
    }

    function loadPayrollEmployees() {
        if (payrollRunOperationInFlight) return;

        if (!confirm(
            'Load eligible employees into ' + document.getElementById('payrollRunHeading').textContent.trim() + '? Review the Run Overview before continuing.'
        )) {
            return;
        }

        $.ajax({
            url: '/payroll/runs/load-employees/<?= $runId; ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                csrf_token: '<?= htmlspecialchars($csrf_token ?? ''); ?>'
            },

            beforeSend: function () {
                payrollRunPending('Loading employees…');
                showLoader('Loading employees...');
                $('#btnLoadEmployees').prop('disabled', true);
            },

            success: function (res) {
                hideLoader();
                $('#btnLoadEmployees').prop('disabled', false);

                if (!res.success) {
                    showToast(res.message || 'Unable to load employees.', 'error');
                    return;
                }

                showToast(res.message || 'Employees loaded successfully.', 'success');

                window.location.reload();
            },

            complete: payrollRunPendingComplete,
            error: function (xhr) {
                hideLoader();
                $('#payrollRunOperationFeedback').removeClass('d-none').data('pending', false).text('The outcome could not be confirmed. Refresh and review this run before trying again.');
                $('#btnLoadEmployees').prop('disabled', false);


                showToast('The outcome could not be confirmed. Refresh and review this run before trying again.', 'error');
            }
        });
    }

    
    function payrollProcessFeedback(res) {
        if (res && (res.success === false || res.error)) {
            return { confirmed: false, rejected: true, type: 'error', message: res.message || res.error || 'Unable to process payroll.' };
        }
        const processed = res?.data?.processed_count;
        const errors = res?.data?.error_count;
        if (!res || res.success !== true || !Number.isInteger(processed) || processed < 0 || !Number.isInteger(errors) || errors < 0) {
            return { confirmed: false, rejected: false, type: 'error', message: 'The processing result could not be confirmed. Refresh the run and review it before trying again.' };
        }
        return {
            confirmed: true, rejected: false, type: errors > 0 ? 'error' : 'success',
            message: `${processed} employee record(s) processed; ${errors} failed.` + (errors > 0 ? ' Review the employee errors before continuing.' : ' Review the stored run status and totals.')
        };
    }

    function processPayrollRun() {
        const $button = $('#btnProcessPayroll');
        if (payrollRunOperationInFlight || $button.prop('disabled')) return;
        if (!confirm('Process payroll? Payroll will be computed for all employees in this run.')) return;
        $.ajax({
            url: '/payroll/runs/process/<?= $runId; ?>',
            type: 'POST',
            dataType: 'json',
            data: { csrf_token: '<?= htmlspecialchars($csrf_token ?? ''); ?>' },
            beforeSend: function () {
                payrollRunPending('Processing payroll. Review individual employee results and stored run status…');
                $button.prop('disabled', true);
                showLoader('Processing payroll...');
            },
            success: function (res) {
                hideLoader();
                const feedback = payrollProcessFeedback(res);
                if (!feedback.confirmed) {
                    if (feedback.rejected) payrollRunPendingComplete();
                    $button.prop('disabled', !feedback.rejected);
                    $('#payrollRunOperationFeedback').removeClass('d-none').data('pending', false).text(feedback.message);
                    showToast(feedback.message, feedback.type, 8000);
                    return;
                }
                // A deliberate GET refreshes stored data and carries truthful per-employee feedback.
                window.location.href = '/payroll/runs/view/<?= $runId; ?>?alert=' + encodeURIComponent(feedback.message) + '&type=' + feedback.type;
            },
            error: function (xhr) {
                hideLoader();
                const rejected = xhr.status >= 400 && xhr.status < 500;
                if (rejected) payrollRunPendingComplete();
                $button.prop('disabled', !rejected);
                $('#payrollRunOperationFeedback').removeClass('d-none').data('pending', false).text(rejected ? 'Processing was rejected. Review the reported issue.' : 'The outcome could not be confirmed. Refresh and review the run before trying again.');
                showToast(rejected ? (xhr.responseJSON?.message || 'Payroll processing was rejected.') : 'The processing result could not be confirmed. Refresh the run and review it before trying again.', 'error', 8000);
            }
        });
    }

    function finalizePayrollRun() {
        Swal.fire({
            title: 'Finalize payroll?',
            text: 'Finalized payroll runs become read-only.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Finalize Payroll'
        }).then(function (result) {
            if (!result.isConfirmed) return;

            $.ajax({
                url: '/payroll/runs/finalize/<?= $runId; ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    csrf_token: '<?= htmlspecialchars($csrf_token ?? ''); ?>'
                },
                beforeSend: function () {
                    showLoader('Finalizing payroll...');
                },
                success: function (res) {
                    hideLoader();

                    if (res.error || res.success === false) {
                        showToast(res.error || res.message || 'Unable to finalize payroll.', 'error');
                        return;
                    }

                    showToast(res.message || 'Payroll finalized successfully.', 'success');
                    window.location.reload();
                },
                error: function (xhr) {
                    hideLoader();
                    console.log(xhr.responseText);
                    showToast('Something went wrong while finalizing payroll.', 'error');
                }
            });
        });
    }

    function cancelPayrollRun(form) {
        const $form = $(form);

        $.ajax({
            url: '/payroll/runs/cancel/<?= $runId; ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                cancellation_reason: $.trim(
                    $form.find('[name="cancellation_reason"]').val()
                ),
                csrf_token: $form.find('[name="csrf_token"]').val()
            },
            beforeSend: function () {
                showLoader('Cancelling payroll run...');
            },
            success: function (res) {
                hideLoader();

                if (res?.error || res?.success === false) {
                    showToast(res.error || res.message || 'Unable to cancel payroll run.', 'error');
                    return;
                }
                if (res?.success !== true) {
                    $('#payrollRunOperationFeedback').removeClass('d-none').data('pending', false).text('The outcome could not be confirmed. Refresh and review this run before trying again.');
                    showToast('The outcome could not be confirmed. Refresh and review this run before trying again.', 'error');
                    return;
                }


                bootstrap.Modal.getInstance(
                    document.getElementById('cancelPayrollRunModal')
                ).hide();

                showToast(res.message || 'Payroll run cancelled successfully.', 'success');
                window.location.reload();
            },
            error: function (xhr) {
                hideLoader();
                $('#payrollRunOperationFeedback').removeClass('d-none').data('pending', false).text('The outcome could not be confirmed. Refresh and review this run before trying again.');
                showToast('The outcome could not be confirmed. Refresh and review this run before trying again.', 'error');
            }
        });
    }


    function resetPayrollRun() {
        if (payrollRunOperationInFlight) return;

        if (!confirm(
            'Reset this payroll run? Loaded employees and computed payroll data will be removed.'
        )) {
            return;
        }

        $.ajax({
            url: '/payroll/runs/reset/<?= $runId ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                csrf_token: '<?= htmlspecialchars($csrf_token ?? '') ?>'
            },

            beforeSend: function () {
                payrollRunPending('Resetting payroll run…');
                showLoader('Resetting payroll run...');
                $('#btnResetPayrollRun').prop('disabled', true);
            },

            success: function (res) {
                hideLoader();
                $('#btnResetPayrollRun').prop('disabled', false);

                if (res?.error || res?.success === false) {
                    showToast(
                        res.error || res.message || 'Unable to reset payroll run.',
                        'error'
                    );
                    return;
                }
                if (res?.success !== true) {
                    $('#payrollRunOperationFeedback').removeClass('d-none').data('pending', false).text('The outcome could not be confirmed. Refresh and review this run before trying again.');
                    showToast('The outcome could not be confirmed. Refresh and review this run before trying again.', 'error');
                    return;
                }


                showToast(
                    res.message || 'Payroll run reset successfully.',
                    'success'
                );

                window.location.reload();
            },

            complete: payrollRunPendingComplete,
            error: function (xhr) {
                hideLoader();
                $('#payrollRunOperationFeedback').removeClass('d-none').data('pending', false).text('The outcome could not be confirmed. Refresh and review this run before trying again.');
                $('#btnResetPayrollRun').prop('disabled', false);


                showToast(
                    xhr.responseJSON?.message
                    || 'The outcome could not be confirmed. Refresh and review this run before trying again.',
                    'error'
                );
            }
        });
    }

    function deletePayrollRun() {
        if (payrollRunOperationInFlight) return;

        if (!confirm(
            'Delete payroll run ' + document.getElementById('payrollRunHeading').textContent.trim() + ' permanently? This cannot be undone.'
        )) {
            return;
        }

        $.ajax({
            url: '/payroll/runs/delete/<?= $runId ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                csrf_token: '<?= htmlspecialchars($csrf_token ?? '') ?>'
            },

            beforeSend: function () {
                payrollRunPending('Deleting payroll run…');
                showLoader('Deleting payroll run...');
                $('#btnDeletePayrollRun').prop('disabled', true);
            },

            success: function (res) {
                hideLoader();
                $('#btnDeletePayrollRun').prop('disabled', false);

                if (res?.error || res?.success === false) {
                    showToast(
                        res.error || res.message || 'Unable to delete payroll run.',
                        'error'
                    );
                    return;
                }
                if (res?.success !== true) {
                    $('#payrollRunOperationFeedback').removeClass('d-none').data('pending', false).text('The outcome could not be confirmed. Refresh and review this run before trying again.');
                    showToast('The outcome could not be confirmed. Refresh and review this run before trying again.', 'error');
                    return;
                }


                showToast(
                    res.message || 'Payroll run deleted successfully.',
                    'success'
                );

                window.location.href = '/payroll/runs';
            },

            complete: payrollRunPendingComplete,
            error: function (xhr) {
                hideLoader();
                $('#payrollRunOperationFeedback').removeClass('d-none').data('pending', false).text('The outcome could not be confirmed. Refresh and review this run before trying again.');
                $('#btnDeletePayrollRun').prop('disabled', false);


                showToast(
                    xhr.responseJSON?.message
                    || 'The outcome could not be confirmed. Refresh and review this run before trying again.',
                    'error'
                );
            }
        });
    }

</script>

</body>

</html>