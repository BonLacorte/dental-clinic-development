<?php include '../app/views/partials/admin-header.php'; ?>
<?php //include '../app/views/module_payroll/partials/payroll_nav.php'; // include on all hris pages 
?>


<div class="py-4 management-page payroll-runs-page payruns-ui">
    <header class="payruns-header">
        <div>
            <p class="payruns-eyebrow">Payroll management</p>
            <h1 id="payrollRunsHeading">Payroll Runs</h1>
            <p class="text-muted mb-0">Create, review and manage payroll processing runs.</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPayrollRunModal">
            <i class="bi bi-plus-lg" aria-hidden="true"></i> New Payroll Run
        </button>
    </header>
    <div class="card management-card">
        <div class="card-header bg-white">
            <div class="payruns-filters">
                <div>
                    <label class="form-label" for="payrollRunStatusFilter">Status</label>
                    <select class="form-select" id="payrollRunStatusFilter">
                        <option value="">All Statuses</option>
                        <option value="DRAFT">Draft</option>
                        <option value="PROCESSING">Processing</option>
                        <option value="PROCESSED">Processed</option>
                        <option value="FINALIZED">Finalized</option>
                        <option value="CANCELLED">Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="payrollRunMonthFilter">Pay Month</label>
                    <input type="month" class="form-control" id="payrollRunMonthFilter" aria-describedby="payMonthHelp">
                </div>
                <button type="button" class="btn btn-outline-secondary" id="btnRefreshPayrollRuns">
                    <i class="bi bi-arrow-clockwise" aria-hidden="true"></i> Refresh
                </button>
            </div>
            <p class="small text-muted mt-2 mb-0" id="payMonthHelp">Pay Month filters by the run’s pay date.</p>
        </div>
        <div class="card-body p-0">
            <p class="payruns-scroll-help" id="payrollRunsScrollHelp">Scroll horizontally to compare all payroll details and actions.</p>
            <div id="payrollRunsFeedback" class="payruns-feedback" role="status" aria-live="polite" aria-atomic="true">Loading payroll runs…</div>
            <div class="table-responsive payruns-table-region" tabindex="0" role="region" aria-labelledby="payrollRunsHeading" aria-describedby="payrollRunsScrollHelp">
                <table class="table table-hover align-middle mb-0" id="payrollRunsTable" aria-busy="true">
                    <caption class="visually-hidden">Payroll runs with stored financial summaries in Philippine pesos.</caption>
                    <thead><tr>
                        <th scope="col">Payroll Run</th>
                        <th scope="col">Schedule</th>
                        <th scope="col" class="text-center">Employees</th>
                        <th scope="col" class="text-end">Financial Summary (PHP)</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                    </tr></thead>
                    <tbody><tr><td colspan="6" class="text-center py-5 text-muted">Loading payroll runs…</td></tr></tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white payruns-results-footer">
            <small class="text-muted" id="payrollRunResultInfo">Loading results…</small>
            <nav aria-label="Payroll run page navigation">
                <ul class="pagination flex-wrap mb-0" id="payrollRunsPagination"></ul>
            </nav>
        </div>
    </div>
</div>

<!-- Create Payroll Run Modal -->
<div class="modal fade payruns-ui payruns-dialog" id="createPayrollRunModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-hidden="true" aria-labelledby="createPayrollRunTitle" aria-describedby="createPayrollRunHelp">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="createPayrollRunForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token ?? ''); ?>">

                <div class="modal-header">
                    <h2 class="modal-title h5" id="createPayrollRunTitle">Create Payroll Run</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>

                <div class="modal-body">
                    <p class="text-muted" id="createPayrollRunHelp">Choose a payroll month and semi-monthly period. Review the cutoff and pay date before creating the run.</p>
                    <div id="createPayrollRunFeedback" class="alert d-none" role="status" aria-live="polite" aria-atomic="true" tabindex="-1"></div>
                    <h3 class="payruns-section-title">Payroll Period</h3>
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

                    <div class="payruns-preview border rounded p-3 d-none" id="payrollPeriodPreview" role="status" aria-live="polite" aria-atomic="true">
                        <h3 class="payruns-section-title">Period Preview</h3>
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
                        Cancel
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
<div class="modal fade payruns-ui payruns-dialog" id="cancelPayrollRunModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-hidden="true" aria-labelledby="cancelPayrollRunTitle">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="cancelPayrollRunForm">
                <input type="hidden" name="run_id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token ?? ''); ?>">

                <div class="modal-header">
                    <h2 class="modal-title h5" id="cancelPayrollRunTitle">Cancel Payroll Run</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
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
    let payrollRunsReadSequence = 0;
    let payrollRunCreating = false;
    let payrollRunCreateUncertain = false;
    const payrollRunManagementPending = new Set();

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

        $('#createPayrollRunModal').on('shown.bs.modal', function () {
            renderPayrollPeriodPreview();
            document.getElementById('payrollRunMonth').focus();
        }).on('hide.bs.modal', function (event) {
            if (payrollRunCreating) event.preventDefault();
        });


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

    function payrollRunsReadFailure(message) {
        $('#payrollRunsTable').attr('aria-busy', 'false');
        $('#payrollRunsTable tbody').html('<tr><td colspan="6" class="text-center py-5">Unable to load payroll runs. Use Refresh to retry.</td></tr>');
        $('#payrollRunsFeedback').text(message).addClass('text-danger');
        $('#payrollRunResultInfo').text('Results unavailable');
        $('#payrollRunsPagination').empty();
    }

    function getAllPayrollRuns(page) {
        const sequence = ++payrollRunsReadSequence;
        const reviewingUncertainCreate = payrollRunCreateUncertain;
        const filters = {
            page: page,
            status: $('#payrollRunStatusFilter').val(),
            payroll_month: $('#payrollRunMonthFilter').val()
        };
        $.ajax({
            url: '/payroll/runs/getallruns',
            type: 'GET',
            dataType: 'json',
            data: filters,
            beforeSend: function () {
                $('#payrollRunsTable').attr('aria-busy', 'true');
                $('#payrollRunsFeedback').text('Loading payroll runs…').removeClass('text-danger');
                $('#payrollRunsTable tbody').html('<tr><td colspan="6" class="text-center py-5 text-muted">Loading payroll runs…</td></tr>');
                $('#payrollRunResultInfo').text('Loading results…');
                $('#payrollRunsPagination').empty();
            },
            success: function (res) {
                if (sequence !== payrollRunsReadSequence) return;
                if (!res || res.success !== true || !Array.isArray(res.data)) {
                    payrollRunsReadFailure(res?.error || res?.message || 'Unable to load payroll runs. Use Refresh to retry.');
                    return;
                }
                currentPayrollRunPage = Number(res.currentPage || page);
                const totalPages = Number(res.totalPages || 1);
                const totalRecords = Number(res.totalRecords ?? res.total ?? 0);
                renderPayrollRunRows(res.data, Boolean(filters.status || filters.payroll_month));
                renderPayrollRunPagination(currentPayrollRunPage, totalPages);
                $('#payrollRunResultInfo').text(
                    res.data.length.toLocaleString() + ' on this page · ' +
                    totalRecords.toLocaleString() + ' payroll run' + (totalRecords === 1 ? '' : 's') +
                    ' total · Page ' + currentPayrollRunPage + ' of ' + totalPages
                );
                $('#payrollRunsTable').attr('aria-busy', 'false');
                $('#payrollRunsFeedback').text(res.data.length ? 'Payroll runs loaded.' :
                    (filters.status || filters.payroll_month ? 'No matching payroll runs. Change the filters to try again.' : 'No payroll runs yet.'));
                // A completed list read lets the administrator inspect an uncertain create outcome.
                if (payrollRunCreateUncertain && reviewingUncertainCreate) {
                    payrollRunCreateUncertain = false;
                    $('#btnCreatePayrollRun').prop('disabled', false);
                    setPayrollRunCreateFeedback('Review the refreshed list for your run before attempting creation again.', 'warning');
                }
            },
            error: function () {
                if (sequence !== payrollRunsReadSequence) return;
                payrollRunsReadFailure('Unable to load payroll runs. Use Refresh to retry.');
            }
        });
    }

    function renderPayrollRunRows(runs, filtered = false) {
        let rows = '';
        if (runs.length === 0) {
            rows = `<tr><td colspan="6" class="text-center py-5 text-muted">
                <i class="bi bi-calendar2-x fs-3 d-block mb-2" aria-hidden="true"></i>
                ${filtered ? 'No payroll runs match these filters.' : 'No payroll runs yet. Create a new payroll run to get started.'}
            </td></tr>`;
        } else {
            runs.forEach(function (run) {
                const runId = Number(run.id);
                const employeeCount = Number(run.employee_count ?? run.total_employees ?? 0);
                rows += `<tr>
                    <td class="payruns-identity">
                        <a href="/payroll/runs/view/${runId}" class="fw-semibold">${esc(run.run_code)}</a>
                        <div class="small text-muted">${esc(run.payroll_name || '')}</div>
                        <div class="small mt-2">${renderPeriodLabel(run.period_no)}</div>
                    </td>
                    <td>
                        <dl class="payruns-schedule mb-0">
                            <div><dt>Cutoff From</dt><dd>${esc(formatDateValue(run.cutoff_date_from))}</dd></div>
                            <div><dt>Cutoff To</dt><dd>${esc(formatDateValue(run.cutoff_date_to))}</dd></div>
                            <div class="payruns-pay-date"><dt>Pay Date</dt><dd>${esc(formatDateValue(run.pay_date))}</dd></div>
                        </dl>
                    </td>
                    <td class="text-center payruns-number">${employeeCount.toLocaleString()}</td>
                    <td class="text-end">
                        <dl class="payruns-money mb-0">
                            <div><dt>Gross Pay</dt><dd>${displayPayrollRunMoney(run.total_gross_pay ?? run.gross_pay)}</dd></div>
                            <div><dt>Total Deductions</dt><dd>${displayPayrollRunMoney(run.total_deductions)}</dd></div>
                            <div class="payruns-net"><dt>Net Pay</dt><dd>${displayPayrollRunMoney(run.total_net_pay ?? run.net_pay)}</dd></div>
                        </dl>
                    </td>
                    <td>${renderPayrollRunStatus(run.status)}</td>
                    <td><div class="payruns-row-actions">${renderPayrollRunActions(run)}</div></td>
                </tr>`;
            });
        }
        $('#payrollRunsTable tbody').html(rows);
    }

    function displayPayrollRunMoney(value) {
        if (value === null || value === undefined || value === '') return '<span class="text-muted">Not available</span>';
        return '₱' + esc(formatMoney(value));
    }

    function renderPayrollRunActions(run) {
        const runId = Number(run.id);
        const status = String(run.status || '').toUpperCase();

        let actions = `
            <a
                href="/payroll/runs/view/${runId}"
                class="btn btn-outline-primary btn-sm"
                aria-label="View run ${esc(run.run_code)}" title="View payroll run">
                <i class="bi bi-eye" aria-hidden="true"></i> View Run
            </a>
        `;

        if (status === 'DRAFT') {
            actions += `
                <a
                    href="/payroll/runs/edit/${runId}"
                    class="btn btn-link btn-sm text-primary"
                    aria-label="Edit payroll run ${esc(run.run_code)}" title="Edit payroll run">
                    <i class="bi bi-pencil" aria-hidden="true"></i>
                </a>


            `;
        }

        actions += `
            <button
                type="button"
                class="btn btn-link btn-sm text-danger deletePayrollRunBtn"
                data-id="${runId}"
                data-code="${esc(run.run_code)}"
                aria-label="Delete payroll run ${esc(run.run_code)}" title="Delete payroll run">
                <i class="bi bi-trash" aria-hidden="true"></i>
            </button>
        `;

        const cancelBtn = `
                <button
                    type="button"
                    class="btn btn-link btn-sm text-danger cancelPayrollRunBtn"
                    data-id="${runId}"
                    data-code="${esc(run.run_code)}"
                    aria-label="Cancel payroll run ${esc(run.run_code)}" title="Cancel payroll run">
                    <i class="bi bi-x-circle" aria-hidden="true"></i>
                </button>
        `



        if (status === 'PROCESSED' || status === 'FINALIZED') {
            actions += `
                <a
                    href="/payroll/runs/print/${runId}"
                    target="_blank"
                    class="btn btn-link btn-sm text-dark"
                    aria-label="Print payroll register ${esc(run.run_code)}" title="Print payroll register">
                    <i class="bi bi-printer" aria-hidden="true"></i>
                </a>
            `;
        }

        return actions;
    }

    function renderPayrollRunStatus(status) {
        const original = String(status ?? '');
        const normalized = original.toUpperCase();

        const badges = {
            DRAFT: 'text-bg-secondary',
            PROCESSING: 'text-bg-warning',
            PROCESSED: 'text-bg-info',
            FINALIZED: 'text-bg-success',
            CANCELLED: 'text-bg-danger'
        };

        return `
            <span class="badge payruns-status ${badges[normalized] || 'text-bg-secondary'}">
                ${esc(badges[normalized] ? toTitleCase(normalized) : (original || 'Unknown'))}
            </span>
        `;
    }

    function renderPeriodLabel(periodNo) {
        if (Number(periodNo) === 1) return 'First Payroll';
        if (Number(periodNo) === 2) return 'Second Payroll';
        return 'Period ' + esc(periodNo ?? 'not available');
    }

    function setPayrollRunCreateFeedback(message, tone) {
        $('#createPayrollRunFeedback').removeClass('d-none alert-danger alert-warning alert-info')
            .addClass('alert-' + tone).text(message);
    }

    function createPayrollRun(form) {
        const $form = $(form);
        if (payrollRunCreating || payrollRunCreateUncertain || !form.reportValidity()) return;
        payrollRunCreating = true;
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
                $('#btnCreatePayrollRun').prop('disabled', true).text('Creating…');
                $form.attr('aria-busy', 'true');
                $form.find('[data-bs-dismiss], #payrollRunMonth, #payrollRunPeriodNo').prop('disabled', true);
                setPayrollRunCreateFeedback('Creating payroll run. Please wait…', 'info');
            },
            success: function (res) {
                if (res?.error || res?.success === false) {
                    setPayrollRunCreateFeedback(res.error || res.message || 'Unable to create payroll run.', 'danger');
                    document.getElementById('createPayrollRunFeedback').focus();
                    return;
                }
                if (res?.success !== true) {
                    uncertainPayrollRunCreate();
                    return;
                }
                showToast(res.message || 'Payroll run created successfully.', 'success');
                payrollRunCreating = false;
                $('#createPayrollRunFeedback').addClass('d-none');
                bootstrap.Modal.getInstance(document.getElementById('createPayrollRunModal')).hide();
                const runId = Number(res.id ?? res.run_id ?? 0);
                if (runId > 0) {
                    window.location.href = '/payroll/runs/view/' + runId;
                    return;
                }
                getAllPayrollRuns(1);
            },
            error: function (xhr) {
                if (xhr.responseJSON?.success === false) {
                    setPayrollRunCreateFeedback(xhr.responseJSON.error || xhr.responseJSON.message || 'Unable to create payroll run.', 'danger');
                    document.getElementById('createPayrollRunFeedback').focus();
                } else {
                    uncertainPayrollRunCreate();
                }
            },
            complete: function () {
                payrollRunCreating = false;
                $form.attr('aria-busy', 'false');
                $form.find('[data-bs-dismiss], #payrollRunMonth, #payrollRunPeriodNo').prop('disabled', false);
                $('#btnCreatePayrollRun').prop('disabled', payrollRunCreateUncertain).text('Create Payroll Run');
            }
        });
    }

    function uncertainPayrollRunCreate() {
        payrollRunCreateUncertain = true;
        setPayrollRunCreateFeedback('Creation could not be confirmed. Close this dialog and Refresh the payroll runs list. Check for your run before trying again.', 'warning');
        document.getElementById('createPayrollRunFeedback').focus();
    }

    function cancelPayrollRun(form) {
        const $form = $(form);
        const runId = Number($form.find('[name="run_id"]').val());
        if (payrollRunManagementPending.has(runId)) return;

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
                payrollRunManagementPending.add(runId);
                $('#payrollRunsTable button[data-id="' + runId + '"]').prop('disabled', true);
                showLoader('Cancelling payroll run...');
            },

            success: function (res) {
                hideLoader();

                if (res?.error || res?.success === false) {
                    showToast(
                        res.error || res.message || 'Unable to cancel payroll run.',
                        'error'
                    );
                    return;
                }
                if (res?.success !== true) {
                    showToast('The outcome could not be confirmed. Refresh and review this run before trying again.', 'error');
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

            complete: function () {
                payrollRunManagementPending.delete(runId);
                $('#payrollRunsTable button[data-id="' + runId + '"]').prop('disabled', false);
            },
            error: function (xhr) {
                hideLoader();
                showToast('The outcome could not be confirmed. Refresh and review this run before trying again.', 'error');
            }
        });
    }

    function renderPayrollRunPagination(page, totalPages) {
        const adjacents = 2;
        const start = Math.max(1, page - adjacents);
        const end = Math.min(totalPages, page + adjacents);

        let html = '';

        if (page === 1) html += '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
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
                        ${pageNumber === page ? 'aria-current="page"' : ''}
                        aria-label="Page ${pageNumber}"
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

        if (page === totalPages) html += '<li class="page-item disabled"><span class="page-link">Next</span></li>';
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
            maximumFractionDigits: 20
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
        if (payrollRunManagementPending.has(runId)) return;

        $.ajax({
            url: '/payroll/runs/delete/' + runId,
            type: 'POST',
            dataType: 'json',

            data: {
                csrf_token: $('#createPayrollRunForm [name="csrf_token"]').val()
            },

            beforeSend: function () {
                payrollRunManagementPending.add(runId);
                $('#payrollRunsTable button[data-id="' + runId + '"]').prop('disabled', true);
                showLoader('Deleting payroll run...');
            },

            success: function (res) {
                hideLoader();

                if (res?.error || res?.success === false) {
                    showToast(
                        res.error ||
                        res.message ||
                        'Unable to delete payroll run.',
                        'error'
                    );
                    return;
                }
                if (res?.success !== true) {
                    showToast('The outcome could not be confirmed. Refresh and review this run before trying again.', 'error');
                    return;
                }


                showToast(
                    res.message || 'Payroll run deleted successfully.',
                    'success'
                );

                getAllPayrollRuns(currentPayrollRunPage);
            },

            complete: function () {
                payrollRunManagementPending.delete(runId);
                $('#payrollRunsTable button[data-id="' + runId + '"]').prop('disabled', false);
            },
            error: function (xhr) {
                hideLoader();


                showToast(
                    xhr.responseJSON?.message ||
                    'The outcome could not be confirmed. Refresh and review this run before trying again.',
                    'error'
                );
            }
        });
    }
</script>

</body>

</html>