<?php include '../app/views/partials/admin-header.php'; ?>
<?php //include '../app/views/module_payroll/partials/payroll_nav.php'; // include on all hris pages 
?>


<div class="py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                <div>
                    <h5 class="mb-1">Payroll Runs</h5>
                    <small class="text-muted">
                        Create and manage semi-monthly payroll processing runs.
                    </small>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <select class="form-select form-select-sm" id="payrollRunStatusFilter" style="min-width: 160px;">
                        <option value="">All Statuses</option>
                        <option value="DRAFT">Draft</option>
                        <option value="PROCESSING">Processing</option>
                        <option value="PROCESSED">Processed</option>
                        <option value="FINALIZED">Finalized</option>
                        <option value="CANCELLED">Cancelled</option>
                    </select>

                    <input type="month" class="form-control form-control-sm" id="payrollRunMonthFilter"
                        style="min-width: 155px;">

                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnRefreshPayrollRuns">
                        <i class="bi bi-arrow-clockwise"></i>
                        Refresh
                    </button>

                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#createPayrollRunModal">
                        <i class="bi bi-plus-lg"></i>
                        New Payroll Run
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="payrollRunsTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Run Code</th>
                            <th>Payroll Period</th>
                            <th>Cutoff</th>
                            <th>Pay Date</th>
                            <th class="text-center">Employees</th>
                            <th class="text-end">Gross Pay</th>
                            <th class="text-end">Deductions</th>
                            <th class="text-end">Net Pay</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                Loading payroll runs...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <small class="text-muted" id="payrollRunResultInfo">
                    Showing 0 payroll runs
                </small>

                <nav aria-label="Payroll run page navigation">
                    <ul class="pagination justify-content-end mb-0" id="payrollRunsPagination">
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>


<!-- Create Payroll Run Modal -->
<div class="modal fade" id="createPayrollRunModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="createPayrollRunForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token ?? ''); ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Create Payroll Run</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-light border">
                        <div class="fw-semibold mb-1">
                            Semi-Monthly Payroll
                        </div>

                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="payrollRunMonth">
                            Payroll Month
                        </label>

                        <input type="month" class="form-control" id="payrollRunMonth" name="payroll_month" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="payrollRunPeriodNo">
                            Payroll Period
                        </label>

                        <select class="form-select" id="payrollRunPeriodNo" name="period_no" required>
                            <option value="">Select payroll period</option>
                            <option value="1">First Payroll — Pay on the 15th</option>
                            <option value="2">Second Payroll — Pay on the 30th</option>
                        </select>
                    </div>

                    <div class="border rounded p-3 bg-light d-none" id="payrollPeriodPreview">
                        <div class="row g-3">
                            <div class="col-12">
                                <small class="text-muted d-block">
                                    Payroll period
                                </small>
                                <span class="fw-semibold" id="previewPeriodName">-</span>
                            </div>

                            <div class="col-6">
                                <small class="text-muted d-block">
                                    Cutoff from
                                </small>
                                <span id="previewCutoffFrom">-</span>
                            </div>

                            <div class="col-6">
                                <small class="text-muted d-block">
                                    Cutoff to
                                </small>
                                <span id="previewCutoffTo">-</span>
                            </div>

                            <div class="col-12">
                                <small class="text-muted d-block">
                                    Pay date
                                </small>
                                <span id="previewPayDate">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>

                    <button type="submit" class="btn btn-primary" id="btnCreatePayrollRun">
                        Create Payroll Run
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Payroll Run Modal -->
<div class="modal fade" id="cancelPayrollRunModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="cancelPayrollRunForm">
                <input type="hidden" name="run_id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token ?? ''); ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Cancel Payroll Run</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">
                    <p>
                        Cancel payroll run
                        <strong id="cancelPayrollRunCode"></strong>?
                    </p>

                    <div class="mb-0">
                        <label class="form-label" for="payrollCancellationReason">
                            Reason
                        </label>

                        <textarea class="form-control" id="payrollCancellationReason" name="cancellation_reason"
                            rows="3" maxlength="255" required></textarea>
                    </div>
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
    let currentPayrollRunPage = 1;

    $(document).ready(function () {
        setDefaultPayrollMonth();
        // setDefaultPayrollMonth();
        getAllPayrollRuns(1);

        $('#btnRefreshPayrollRuns').on('click', function () {
            getAllPayrollRuns(currentPayrollRunPage);
        });

        $('#payrollRunStatusFilter, #payrollRunMonthFilter').on('change', function () {
            getAllPayrollRuns(1);
        }
        );

        $('#payrollRunMonth, #payrollRunPeriodNo').on('change', renderPayrollPeriodPreview);

        $('#createPayrollRunForm').on('submit', function (event) {
            event.preventDefault();
            createPayrollRun(this);
        });

        $('#cancelPayrollRunForm').on('submit', function (event) {
            event.preventDefault();
            cancelPayrollRun(this);
        });

        $(document).on('click', '.payroll-run-page-btn', function (event) {
            event.preventDefault();

            const page = Number($(this).data('page'));

            if (page > 0 && page !== currentPayrollRunPage) {
                getAllPayrollRuns(page);
            }
        });

        $(document).on('click', '.cancelPayrollRunBtn', function () {
            const runId = Number($(this).data('id'));
            const runCode = String($(this).data('code') || '');

            $('#cancelPayrollRunForm [name="run_id"]').val(runId);
            $('#cancelPayrollRunCode').text(runCode);
            $('#payrollCancellationReason').val('');

            bootstrap.Modal.getOrCreateInstance(document.getElementById('cancelPayrollRunModal')).show();
        });

        $(document).on('click', '.deletePayrollRunBtn', function () {

            const runId = Number($(this).data('id'));
            const runCode = String($(this).data('code') || '');

            if (!confirm(
                'Delete payroll run ' + runCode + '? This cannot be undone.'
            )) {
                return;
            }

            deletePayrollRun(runId);
        });



    });

    function setDefaultPayrollMonth() {
        const now = new Date();
        const monthValue =
            now.getFullYear() +
            '-' +
            String(now.getMonth() + 1).padStart(2, '0');

        $('#payrollRunMonth').val(monthValue);
        $('#payrollRunMonthFilter').val(monthValue);
    }

    function renderPayrollPeriodPreview() {
        const payrollMonth = $('#payrollRunMonth').val();
        const periodNo = Number($('#payrollRunPeriodNo').val());

        if (!payrollMonth || ![1, 2].includes(periodNo)) {
            $('#payrollPeriodPreview').addClass('d-none');
            return;
        }

        const dates = getSemiMonthlyDates(payrollMonth, periodNo);

        $('#previewPeriodName').text(
            periodNo === 1
                ? 'First Semi-Monthly Payroll'
                : 'Second Semi-Monthly Payroll'
        );

        $('#previewCutoffFrom').text(formatDate(dates.cutoffFrom));
        $('#previewCutoffTo').text(formatDate(dates.cutoffTo));
        $('#previewPayDate').text(formatDate(dates.payDate));
        $('#payrollPeriodPreview').removeClass('d-none');
    }

    function getSemiMonthlyDates(payrollMonth, periodNo) {
        const parts = payrollMonth.split('-');
        const year = Number(parts[0]);
        const monthIndex = Number(parts[1]) - 1;

        if (periodNo === 1) {
            const previousMonthLastDay = new Date(year, monthIndex, 0);
            const cutoffTo = new Date(year, monthIndex, 14);
            const payDate = new Date(year, monthIndex, 15);

            return {
                cutoffFrom: previousMonthLastDay,
                cutoffTo: cutoffTo,
                payDate: payDate
            };
        }

        const cutoffFrom = new Date(year, monthIndex, 15);
        const requestedPayDate = new Date(year, monthIndex, 30);
        const lastDayOfMonth = new Date(year, monthIndex + 1, 0);

        const payDate =
            requestedPayDate.getMonth() === monthIndex
                ? requestedPayDate
                : lastDayOfMonth;

        const cutoffTo = new Date(payDate);
        cutoffTo.setDate(cutoffTo.getDate() - 1);

        return {
            cutoffFrom: cutoffFrom,
            cutoffTo: cutoffTo,
            payDate: payDate
        };
    }

    function getAllPayrollRuns(page) {
        $.ajax({
            url: '/payroll/runs/getallruns',
            type: 'GET',
            dataType: 'json',
            data: {
                page: page,
                status: $('#payrollRunStatusFilter').val(),
                payroll_month: $('#payrollRunMonthFilter').val()
            },

            beforeSend: function () {
                showLoader('Loading payroll runs...');
            },

            success: function (res) {
                hideLoader();

                if (res.error || res.success === false) {
                    showToast(
                        res.error || res.message || 'Unable to load payroll runs.',
                        'error'
                    );
                    return;
                }

                currentPayrollRunPage = Number(res.currentPage || page);

                const totalPages = Number(res.totalPages || 1);
                const totalRecords = Number(
                    res.totalRecords ??
                    res.total ??
                    0
                );

                renderPayrollRunRows(res.data || []);
                renderPayrollRunPagination(
                    currentPayrollRunPage,
                    totalPages
                );

                $('#payrollRunResultInfo').text(
                    'Showing ' +
                    totalRecords.toLocaleString() +
                    ' payroll run' +
                    (totalRecords === 1 ? '' : 's')
                );
            },

            error: function (xhr) {
                hideLoader();
                console.log(xhr.responseText);

                $('#payrollRunsTable tbody').html(`
                    <tr>
                        <td colspan="10" class="text-center py-5 text-danger">
                            Unable to load payroll runs.
                        </td>
                    </tr>
                `);

                showToast('Unable to load payroll runs.', 'error');
            }
        });
    }

    function renderPayrollRunRows(runs) {
        let rows = '';

        if (runs.length === 0) {
            rows = `
                <tr>
                    <td colspan="10" class="text-center py-5 text-muted">
                        <i class="bi bi-calendar2-x fs-3 d-block mb-2"></i>
                        No payroll runs found.
                    </td>
                </tr>
            `;
        } else {
            runs.forEach(function (run) {
                const runId = Number(run.id);
                const employeeCount = Number(
                    run.employee_count ??
                    run.total_employees ??
                    0
                );

                rows += `
                    <tr>
                        <td class="ps-4">
                            <a
                                href="/payroll/runs/view/${runId}"
                                class="fw-semibold text-decoration-none">
                                ${esc(run.run_code)}
                            </a>
                            <div class="small text-muted">
                                ${esc(run.payroll_name || '')}
                            </div>
                        </td>

                        <td>
                            ${renderPeriodLabel(run.period_no)}
                        </td>

                        <td>
                            <div>
                                ${esc(formatDateValue(run.cutoff_date_from))}
                            </div>
                            <small class="text-muted">
                                to ${esc(formatDateValue(run.cutoff_date_to))}
                            </small>
                        </td>

                        <td>
                            ${esc(formatDateValue(run.pay_date))}
                        </td>

                        <td class="text-center">
                            <span class="badge rounded-pill text-bg-light border">
                                ${employeeCount.toLocaleString()}
                            </span>
                        </td>

                        <td class="text-end">
                            ${formatMoney(run.total_gross_pay ?? run.gross_pay)}
                        </td>

                        <td class="text-end">
                            ${formatMoney(run.total_deductions)}
                        </td>

                        <td class="text-end fw-semibold">
                            ${formatMoney(run.total_net_pay ?? run.net_pay)}
                        </td>

                        <td class="text-center">
                            ${renderPayrollRunStatus(run.status)}
                        </td>

                        <td class="text-end pe-4">
                            ${renderPayrollRunActions(run)}
                        </td>
                    </tr>
                `;
            });
        }

        $('#payrollRunsTable tbody').html(rows);
    }

    function renderPayrollRunActions(run) {
        const runId = Number(run.id);
        const status = String(run.status || '').toUpperCase();

        let actions = `
            <a
                href="/payroll/runs/view/${runId}"
                class="btn btn-link btn-sm text-secondary"
                title="View payroll run">
                <i class="bi bi-eye"></i>
            </a>
        `;

        if (status === 'DRAFT') {
            actions += `
                <a
                    href="/payroll/runs/edit/${runId}"
                    class="btn btn-link btn-sm text-primary"
                    title="Edit payroll run">
                    <i class="bi bi-pencil"></i>
                </a>


            `;
        }

        actions += `
            <button
                type="button"
                class="btn btn-link btn-sm text-danger deletePayrollRunBtn"
                data-id="${runId}"
                data-code="${esc(run.run_code)}"
                title="Delete payroll run">
                <i class="bi bi-trash"></i>
            </button>
        `;

        const cancelBtn = `
                <button
                    type="button"
                    class="btn btn-link btn-sm text-danger cancelPayrollRunBtn"
                    data-id="${runId}"
                    data-code="${esc(run.run_code)}"
                    title="Cancel payroll run">
                    <i class="bi bi-x-circle"></i>
                </button>
        `



        if (status === 'PROCESSED' || status === 'FINALIZED') {
            actions += `
                <a
                    href="/payroll/runs/print/${runId}"
                    target="_blank"
                    class="btn btn-link btn-sm text-dark"
                    title="Print payroll register">
                    <i class="bi bi-printer"></i>
                </a>
            `;
        }

        return actions;
    }

    function renderPayrollRunStatus(status) {
        const normalized = String(status || 'DRAFT').toUpperCase();

        const badges = {
            DRAFT: 'text-bg-secondary',
            PROCESSING: 'text-bg-warning',
            PROCESSED: 'text-bg-info',
            FINALIZED: 'text-bg-success',
            CANCELLED: 'text-bg-danger'
        };

        return `
            <span class="badge ${badges[normalized] || 'text-bg-secondary'}">
                ${esc(toTitleCase(normalized))}
            </span>
        `;
    }

    function renderPeriodLabel(periodNo) {
        return Number(periodNo) === 1
            ? `
                <span class="fw-semibold">First Payroll</span>
                <div class="small text-muted">Pay on the 15th</div>
            `
            : `
                <span class="fw-semibold">Second Payroll</span>
                <div class="small text-muted">Pay on the 30th</div>
            `;
    }

    function createPayrollRun(form) {
        const $form = $(form);

        $.ajax({
            url: '/payroll/runs/create',
            type: 'POST',
            dataType: 'json',

            data: {
                payroll_month: $form.find('[name="payroll_month"]').val(),
                period_no: $form.find('[name="period_no"]').val(),
                csrf_token: $form.find('[name="csrf_token"]').val()
            },

            beforeSend: function () {
                $('#btnCreatePayrollRun').prop('disabled', true);
                showLoader('Creating payroll run...');
            },

            success: function (res) {
                hideLoader();
                $('#btnCreatePayrollRun').prop('disabled', false);

                if (res.error || res.success === false) {
                    showToast(
                        res.error || res.message || 'Unable to create payroll run.',
                        'error'
                    );
                    return;
                }

                showToast(
                    res.message || 'Payroll run created successfully.',
                    'success'
                );

                bootstrap.Modal.getInstance(
                    document.getElementById('createPayrollRunModal')
                ).hide();

                const runId = Number(res.id ?? res.run_id ?? 0);

                if (runId > 0) {
                    window.location.href = '/payroll/runs/view/' + runId;
                    return;
                }

                getAllPayrollRuns(1);
            },

            error: function (xhr) {
                hideLoader();
                $('#btnCreatePayrollRun').prop('disabled', false);
                console.log(xhr.responseText);
                showToast('Something went wrong while creating the payroll run.', 'error');
            }
        });
    }

    function cancelPayrollRun(form) {
        const $form = $(form);
        const runId = Number($form.find('[name="run_id"]').val());

        $.ajax({
            url: '/payroll/runs/cancel/' + runId,
            type: 'POST',
            dataType: 'json',
            data: {
                cancellation_reason:
                    $.trim($form.find('[name="cancellation_reason"]').val()),
                csrf_token: $form.find('[name="csrf_token"]').val()
            },

            beforeSend: function () {
                showLoader('Cancelling payroll run...');
            },

            success: function (res) {
                hideLoader();

                if (res.error || res.success === false) {
                    showToast(
                        res.error || res.message || 'Unable to cancel payroll run.',
                        'error'
                    );
                    return;
                }

                bootstrap.Modal.getInstance(
                    document.getElementById('cancelPayrollRunModal')
                ).hide();

                showToast(
                    res.message || 'Payroll run cancelled successfully.',
                    'success'
                );

                getAllPayrollRuns(currentPayrollRunPage);
            },

            error: function (xhr) {
                hideLoader();
                console.log(xhr.responseText);
                showToast('Something went wrong while cancelling the payroll run.', 'error');
            }
        });
    }

    function renderPayrollRunPagination(page, totalPages) {
        const adjacents = 2;
        const start = Math.max(1, page - adjacents);
        const end = Math.min(totalPages, page + adjacents);

        let html = '';

        if (page > 1) {
            html += paginationItem(page - 1, '&laquo; Prev');
        }

        if (start > 1) {
            html += paginationItem(1, '1');

            if (start > 2) {
                html += `
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                `;
            }
        }

        for (let pageNumber = start; pageNumber <= end; pageNumber++) {
            html += `
                <li class="page-item ${pageNumber === page ? 'active' : ''}">
                    <a
                        href="#"
                        class="page-link payroll-run-page-btn"
                        data-page="${pageNumber}">
                        ${pageNumber}
                    </a>
                </li>
            `;
        }

        if (end < totalPages) {
            if (end < totalPages - 1) {
                html += `
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                `;
            }

            html += paginationItem(totalPages, totalPages);
        }

        if (page < totalPages) {
            html += paginationItem(page + 1, 'Next &raquo;');
        }

        $('#payrollRunsPagination').html(html);
    }

    function paginationItem(page, label) {
        return `
            <li class="page-item">
                <a
                    href="#"
                    class="page-link payroll-run-page-btn"
                    data-page="${page}">
                    ${label}
                </a>
            </li>
        `;
    }

    function formatMoney(value) {
        const amount = Number(value || 0);

        return amount.toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function formatDateValue(value) {
        if (!value) {
            return '-';
        }

        const parts = String(value).substring(0, 10).split('-');

        if (parts.length !== 3) {
            return value;
        }

        return formatDate(
            new Date(
                Number(parts[0]),
                Number(parts[1]) - 1,
                Number(parts[2])
            )
        );
    }

    function formatDate(date) {
        return date.toLocaleDateString('en-PH', {
            month: 'short',
            day: '2-digit',
            year: 'numeric'
        });
    }

    function toTitleCase(value) {
        return String(value || '')
            .toLowerCase()
            .replace(/(^|_|\s)\S/g, function (character) {
                return character.replace('_', ' ').toUpperCase();
            });
    }

    function esc(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (character) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            }[character];
        });
    }

    function deletePayrollRun(runId) {

        $.ajax({
            url: '/payroll/runs/delete/' + runId,
            type: 'POST',
            dataType: 'json',

            data: {
                csrf_token: $('#createPayrollRunForm [name="csrf_token"]').val()
            },

            beforeSend: function () {
                showLoader('Deleting payroll run...');
            },

            success: function (res) {
                hideLoader();

                if (res.error || res.success === false) {
                    showToast(
                        res.error ||
                        res.message ||
                        'Unable to delete payroll run.',
                        'error'
                    );
                    return;
                }

                showToast(
                    res.message || 'Payroll run deleted successfully.',
                    'success'
                );

                getAllPayrollRuns(currentPayrollRunPage);
            },

            error: function (xhr) {
                hideLoader();

                console.log(xhr.responseText);

                showToast(
                    xhr.responseJSON?.message ||
                    'Something went wrong while deleting the payroll run.',
                    'error'
                );
            }
        });
    }
</script>

</body>

</html>