<?php include '../app/views/partials/admin-header.php'; ?>
<?php //include '../app/views/module_payroll/partials/payroll_nav.php'; ?>

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

    $class = $classes[$status] ?? 'text-bg-secondary';

    return '<span class="badge ' . $class . '">' .
        htmlspecialchars(ucwords(strtolower($status))) .
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

<div class="py-4">
    <div class="d-flex flex-column flex-xl-row justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                <h4 class="mb-0">
                    <?= htmlspecialchars($run['run_code'] ?? 'Payroll Run'); ?>
                </h4>
                <?= payrollStatusBadge($status); ?>
            </div>

            <div class="text-muted">
                <?= htmlspecialchars(
                    $run['payroll_name']
                    ?? (($periodNo === 1 ? 'First' : 'Second') . ' Semi-Monthly Payroll')
                ); ?>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 align-items-start">
            <a href="/payroll/runs" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
                Back to Payroll Runs
            </a>

            <?php if ($status === 'DRAFT'): ?>
                <button type="button" class="btn btn-outline-primary" id="btnLoadEmployees">
                    <i class="bi bi-people"></i>
                    Load Employees
                </button>

                <button type="button" class="btn btn-primary" id="btnProcessPayroll">
                    <i class="bi bi-calculator"></i>
                    Process Payroll
                </button>

                <!-- <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                    data-bs-target="#cancelPayrollRunModal">
                    <i class="bi bi-x-circle"></i>
                    Cancel
                </button> -->


            <?php elseif ($status === 'PROCESSED'): ?>
                <button type="button" class="btn btn-success" id="btnFinalizePayroll">
                    <i class="bi bi-lock"></i>
                    Finalize Payroll
                </button>

                <a href="/payroll/runs/print/<?= $runId; ?>" target="_blank" class="btn btn-outline-dark">
                    <i class="bi bi-printer"></i>
                    Print Register
                </a>
            <?php elseif ($status === 'FINALIZED'): ?>
                <a href="/payroll/runs/print/<?= $runId; ?>" target="_blank" class="btn btn-outline-dark">
                    <i class="bi bi-printer"></i>
                    Print Register
                </a>
            <?php endif; ?>

            <?php if ($status === 'DRAFT' || $status === 'CANCELLED'): ?>

                <button type="button" class="btn btn-outline-warning" id="btnResetPayrollRun">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    Reset
                </button>

                <button type="button" class="btn btn-outline-danger" id="btnDeletePayrollRun">
                    <i class="bi bi-trash"></i>
                    Delete
                </button>

            <?php endif; ?>
        </div>
    </div>

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
            <div class="payroll-total-card p-3">
                <div class="label">Net Pay</div>
                <div class="amount">₱<?= payrollMoney($totalNetPay); ?></div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                <div>
                    <h5 class="mb-1">Employee Payroll Results</h5>
                    <small class="text-muted">
                        Snapshot of employee payroll computations for this run.
                    </small>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <input type="search" class="form-control form-control-sm" id="employeePayrollSearch"
                        placeholder="Search employee..." style="min-width: 220px;">

                    <select class="form-select form-select-sm" id="employeeComputationStatusFilter"
                        style="min-width: 165px;">
                        <option value="">All Computation Status</option>
                        <option value="PENDING">Pending</option>
                        <option value="COMPUTED">Computed</option>
                        <option value="ERROR">Error</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="payrollRunEmployeesTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Employee</th>
                            <th>Salary Type</th>
                            <th class="text-end">Basic Pay</th>
                            <th class="text-end">Gross Pay</th>
                            <th class="text-end">Earnings</th>
                            <th class="text-end">Deductions</th>
                            <th class="text-end">Net Pay</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($employees)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-3 d-block mb-2"></i>
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
                                            class="btn btn-link btn-sm text-secondary" title="View payroll details">
                                            <i class="bi bi-eye"></i>
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
                                <th colspan="3" class="text-end">Totals</th>
                                <th class="text-end"><?= payrollMoney($totalGrossPay); ?></th>
                                <th class="text-end"><?= payrollMoney($totalEarnings); ?></th>
                                <th class="text-end"><?= payrollMoney($totalDeductions); ?></th>
                                <th class="text-end"><?= payrollMoney($totalNetPay); ?></th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cancelPayrollRunModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="cancelPayrollRunForm">
                <input type="hidden" name="run_id" value="<?= $runId; ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? ''); ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Cancel Payroll Run</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
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
    }

    function loadPayrollEmployees() {

        if (!confirm(
            'Load all eligible employees into this payroll run?'
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

            error: function (xhr) {
                hideLoader();
                $('#btnLoadEmployees').prop('disabled', false);

                console.log(xhr.responseText);

                showToast('Something went wrong while loading employees.', 'error');
            }
        });
    }

    
    function processPayrollRun() {



        if (!confirm(
            'Process payroll? Payroll will be computed for all employees in this run.'
        )) {
            return;
        }


        $.ajax({
            url: '/payroll/runs/process/<?= $runId; ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                csrf_token: '<?= htmlspecialchars($csrf_token ?? ''); ?>'
            },
            beforeSend: function () {
                showLoader('Processing payroll...');
            },
            success: function (res) {
                hideLoader();

                if (res.error || res.success === false) {
                    showToast(res.error || res.message || 'Unable to process payroll.', 'error');
                    return;
                }

                showToast(res.message || 'Payroll processed successfully.', 'success');
                window.location.reload();
            },
            error: function (xhr) {
                hideLoader();
                console.log(xhr.responseText);
                showToast(xhr.responseJSON?.message || 'Something went wrong while processing payroll.', 'error');
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

                if (res.error || res.success === false) {
                    showToast(res.error || res.message || 'Unable to cancel payroll run.', 'error');
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
                console.log(xhr.responseText);
                showToast('Something went wrong while cancelling payroll.', 'error');
            }
        });
    }


    function resetPayrollRun() {

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
                showLoader('Resetting payroll run...');
                $('#btnResetPayrollRun').prop('disabled', true);
            },

            success: function (res) {
                hideLoader();
                $('#btnResetPayrollRun').prop('disabled', false);

                if (res.error || res.success === false) {
                    showToast(
                        res.error || res.message || 'Unable to reset payroll run.',
                        'error'
                    );
                    return;
                }

                showToast(
                    res.message || 'Payroll run reset successfully.',
                    'success'
                );

                window.location.reload();
            },

            error: function (xhr) {
                hideLoader();
                $('#btnResetPayrollRun').prop('disabled', false);

                console.log(xhr.responseText);

                showToast(
                    xhr.responseJSON?.message
                    || 'Something went wrong while resetting the payroll run.',
                    'error'
                );
            }
        });
    }

    function deletePayrollRun() {

        if (!confirm(
            'Delete this payroll run permanently? This cannot be undone.'
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
                showLoader('Deleting payroll run...');
                $('#btnDeletePayrollRun').prop('disabled', true);
            },

            success: function (res) {
                hideLoader();
                $('#btnDeletePayrollRun').prop('disabled', false);

                if (res.error || res.success === false) {
                    showToast(
                        res.error || res.message || 'Unable to delete payroll run.',
                        'error'
                    );
                    return;
                }

                showToast(
                    res.message || 'Payroll run deleted successfully.',
                    'success'
                );

                window.location.href = '/payroll/runs';
            },

            error: function (xhr) {
                hideLoader();
                $('#btnDeletePayrollRun').prop('disabled', false);

                console.log(xhr.responseText);

                showToast(
                    xhr.responseJSON?.message
                    || 'Something went wrong while deleting the payroll run.',
                    'error'
                );
            }
        });
    }

</script>

</body>

</html>