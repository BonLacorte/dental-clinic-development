<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_hris/partials/hris_nav.php'; ?>

<div class="container-fluid py-4 management-page hris-page attendance-workspace">
    <div class="card shadow-sm management-card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div class="attendance-heading"><p class="attendance-eyebrow">HRIS · Attendance</p><h1 class="h4 mb-1" tabindex="-1" id="leavesHeading">Leave Management</h1><p class="text-muted mb-0">Review leave periods, request statuses and paid or unpaid leave types.</p></div>

            <div class="d-flex gap-2 attendance-context-actions">
                <button class="btn btn-primary btn-sm" hidden data-leave-add="typesPane" data-bs-toggle="modal" data-bs-target="#addLeaveTypeModal">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i> Add Leave Type
                </button>
                <button class="btn btn-primary btn-sm" data-leave-add="requestsPane" data-bs-toggle="modal" data-bs-target="#addLeaveRequestModal">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i> Add Leave Request
                </button>
            </div>
        </div>

        <div class="card-body pb-0">
            <ul class="nav nav-tabs" id="leaveTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="requests-tab" aria-controls="requestsPane" aria-selected="true" data-bs-toggle="tab" data-bs-target="#requestsPane" type="button" role="tab">
                        Leave Requests
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="types-tab" aria-controls="typesPane" aria-selected="false" data-bs-toggle="tab" data-bs-target="#typesPane" type="button" role="tab">
                        Leave Types
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="requestsPane" role="tabpanel" aria-labelledby="requests-tab">
                <div class="card-body border-bottom">
                    <form id="requestSearchForm" class="row g-2 align-items-end">
                        <div class="col-md-6 col-lg-4">
                            <label for="requestSearchInput" class="form-label">Search</label>
                            <input type="text" class="form-control" id="requestSearchInput" placeholder="Search leave requests">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="bi bi-search" aria-hidden="true"></i> Search
                            </button>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-secondary" type="button" id="clearRequestSearchBtn">Clear</button>
                        </div>
                        <div class="col-auto attendance-sort-control"><label class="form-label" for="requestSortBy">Sort by</label><div class="d-flex gap-2"><select class="form-select" id="requestSortBy"><option value="employee_name">Employee</option><option value="leave_type">Leave type</option><option value="date_from">Period start</option><option value="date_to">Period end</option><option value="status">Status</option><option value="approved_by_name">Approved by</option><option value="approved_at">Approved at</option><option value="created_at" selected>Date added</option></select><button type="button" id="requestSortByDirection" class="btn btn-outline-secondary">Descending ↓</button></div></div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <p class="attendance-list-status px-4 pt-3 mb-0" role="status" aria-live="polite"></p>
                    <p class="hris-scroll-hint" id="leaveRequestsTableScrollHelp">Use horizontal scrolling when columns extend beyond this view.</p>
                    <div class="table-responsive hris-table-region" tabindex="0" role="region" aria-labelledby="requests-tab" aria-describedby="leaveRequestsTableScrollHelp">
                        <table class="table table-hover align-middle mb-0 hris-data-table" id="leaveRequestsTable">
                            <caption class="visually-hidden">Leave requests with their dates, status and approval details.</caption>
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="ps-4"><button class="btn btn-link p-0 request-sort" data-sort="employee_name">Employee</button></th>
                                    <th scope="col"><button class="btn btn-link p-0 request-sort" data-sort="leave_type">Leave Type</button></th>
                                    <th scope="col"><button class="btn btn-link p-0 request-sort" data-sort="date_from">Leave Period</button></th>
                                    <th scope="col"><button class="btn btn-link p-0 request-sort" data-sort="status">Status</button></th>
                                    <th scope="col"><button class="btn btn-link p-0 request-sort" data-sort="approved_by_name">Approved By</button></th>
                                    <th scope="col"><button class="btn btn-link p-0 request-sort" data-sort="approved_at">Approved At</button></th>
                                    <th scope="col" class="hris-created-at attendance-created"><button class="btn btn-link p-0 request-sort" data-sort="created_at">Created At</button></th>
                                    <th scope="col" class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-white py-3">
                    <nav aria-label="Leave request pages">
                        <ul class="pagination justify-content-end mb-0" id="requestsPagination"></ul>
                    </nav>
                </div>
            </div>

            <div class="tab-pane fade" id="typesPane" role="tabpanel" aria-labelledby="types-tab">
                <div class="card-body border-bottom">
                    <form id="typeSearchForm" class="row g-2 align-items-end">
                        <div class="col-md-6 col-lg-4">
                            <label for="typeSearchInput" class="form-label">Search</label>
                            <input type="text" class="form-control" id="typeSearchInput" placeholder="Search leave types">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="bi bi-search" aria-hidden="true"></i> Search
                            </button>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-secondary" type="button" id="clearTypeSearchBtn">Clear</button>
                        </div>
                        <div class="col-auto hris-order-control"><button type="button" class="btn btn-outline-secondary type-sort hris-created-sort" data-sort="created_at" aria-pressed="true" aria-label="Date added: newest first. Sort oldest first" title="Sort by date added">Date added: newest first</button></div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <p class="attendance-list-status px-4 pt-3 mb-0" role="status" aria-live="polite"></p>
                    <p class="hris-scroll-hint" id="leaveTypesTableScrollHelp">Use horizontal scrolling when columns extend beyond this view.</p>
                    <div class="table-responsive hris-table-region" tabindex="0" role="region" aria-labelledby="types-tab" aria-describedby="leaveTypesTableScrollHelp">
                        <table class="table table-hover align-middle mb-0 hris-data-table" id="leaveTypesTable">
                            <caption class="visually-hidden">Leave types and their existing paid or unpaid setting.</caption>
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="ps-4"><button class="btn btn-link p-0 type-sort" data-sort="name">Name</button></th>
                                    <th scope="col"><button class="btn btn-link p-0 type-sort" data-sort="is_paid">Classification</button></th>
                                    <th scope="col" class="hris-created-at attendance-created"><button class="btn btn-link p-0 type-sort" data-sort="created_at">Created At</button></th>
                                    <th scope="col" class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-white py-3">
                    <nav aria-label="Leave type pages">
                        <ul class="pagination justify-content-end mb-0" id="typesPagination"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade hris-dialog attendance-dialog" id="addLeaveRequestModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addLeaveRequestModalTitle">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="addLeaveRequestForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="addLeaveRequestModalTitle">Add Leave Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="attendance-feedback alert alert-warning" role="status" aria-live="polite" tabindex="-1" hidden></div>
                    <div class="attendance-discard alert alert-warning" hidden>
                        <p>Leave this dialog and discard your unsaved changes?</p>
                        <button type="button" class="btn btn-secondary attendance-stay">Stay and edit</button>
                        <button type="button" class="btn btn-outline-danger attendance-discard-confirm">Discard changes</button>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="addLeaveRequestForm_employee_id" class="form-label">Employee <span class="attendance-required">(required)</span></label>
                            <select id="addLeaveRequestForm_employee_id" class="form-select employeeSelect" name="employee_id"></select>
                        </div>
                        <div class="col-md-6">
                            <label for="addLeaveRequestForm_leave_type_id" class="form-label">Leave Type <span class="attendance-required">(required)</span></label>
                            <select id="addLeaveRequestForm_leave_type_id" class="form-select leaveTypeSelect" name="leave_type_id"></select>
                        </div>
                        <div class="col-md-4">
                            <label for="addLeaveRequestForm_date_from" class="form-label">Date From <span class="attendance-required">(required)</span></label>
                            <input id="addLeaveRequestForm_date_from" type="date" class="form-control" name="date_from">
                        </div>
                        <div class="col-md-4">
                            <label for="addLeaveRequestForm_date_to" class="form-label">Date To <span class="attendance-required">(required)</span></label>
                            <input id="addLeaveRequestForm_date_to" type="date" class="form-control" name="date_to">
                        </div>
                        <div class="col-md-4">
                            <label for="addLeaveRequestForm_status" class="form-label">Status <span class="attendance-required">(required)</span></label>
                            <select id="addLeaveRequestForm_status" class="form-select" name="status">
                                <option value="PENDING">PENDING</option>
                                <option value="APPROVED">APPROVED</option>
                                <option value="REJECTED">REJECTED</option>
                                <option value="CANCELLED">CANCELLED</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="addLeaveRequestForm_reason" class="form-label">Reason</label>
                            <textarea id="addLeaveRequestForm_reason" class="form-control" name="reason" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade hris-dialog attendance-dialog" id="editLeaveRequestModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editLeaveRequestModalTitle">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="editLeaveRequestForm">
                <input type="hidden" name="id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <div><h5 class="modal-title" id="editLeaveRequestModalTitle">Edit Leave Request</h5><p class="small text-muted mb-0" data-attendance-record></p></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="attendance-feedback alert alert-warning" role="status" aria-live="polite" tabindex="-1" hidden></div>
                    <div class="attendance-discard alert alert-warning" hidden>
                        <p>Leave this dialog and discard your unsaved changes?</p>
                        <button type="button" class="btn btn-secondary attendance-stay">Stay and edit</button>
                        <button type="button" class="btn btn-outline-danger attendance-discard-confirm">Discard changes</button>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="editLeaveRequestForm_employee_id" class="form-label">Employee <span class="attendance-required">(required)</span></label>
                            <select id="editLeaveRequestForm_employee_id" class="form-select employeeSelect" name="employee_id"></select>
                        </div>
                        <div class="col-md-6">
                            <label for="editLeaveRequestForm_leave_type_id" class="form-label">Leave Type <span class="attendance-required">(required)</span></label>
                            <select id="editLeaveRequestForm_leave_type_id" class="form-select leaveTypeSelect" name="leave_type_id"></select>
                        </div>
                        <div class="col-md-4">
                            <label for="editLeaveRequestForm_date_from" class="form-label">Date From <span class="attendance-required">(required)</span></label>
                            <input id="editLeaveRequestForm_date_from" type="date" class="form-control" name="date_from">
                        </div>
                        <div class="col-md-4">
                            <label for="editLeaveRequestForm_date_to" class="form-label">Date To <span class="attendance-required">(required)</span></label>
                            <input id="editLeaveRequestForm_date_to" type="date" class="form-control" name="date_to">
                        </div>
                        <div class="col-md-4">
                            <label for="editLeaveRequestForm_status" class="form-label">Status <span class="attendance-required">(required)</span></label>
                            <select id="editLeaveRequestForm_status" class="form-select" name="status">
                                <option value="PENDING">PENDING</option>
                                <option value="APPROVED">APPROVED</option>
                                <option value="REJECTED">REJECTED</option>
                                <option value="CANCELLED">CANCELLED</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="editLeaveRequestForm_reason" class="form-label">Reason</label>
                            <textarea id="editLeaveRequestForm_reason" class="form-control" name="reason" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade hris-dialog attendance-dialog" id="addLeaveTypeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addLeaveTypeModalTitle">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="addLeaveTypeForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="addLeaveTypeModalTitle">Add Leave Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="attendance-feedback alert alert-warning" role="status" aria-live="polite" tabindex="-1" hidden></div>
                    <div class="attendance-discard alert alert-warning" hidden>
                        <p>Leave this dialog and discard your unsaved changes?</p>
                        <button type="button" class="btn btn-secondary attendance-stay">Stay and edit</button>
                        <button type="button" class="btn btn-outline-danger attendance-discard-confirm">Discard changes</button>
                    </div>
                    <div class="mb-3">
                        <label for="addLeaveTypeForm_name" class="form-label">Name <span class="attendance-required">(required)</span></label>
                        <input id="addLeaveTypeForm_name" type="text" class="form-control" name="name">
                    </div>
                    <div class="mb-3">
                        <label for="addLeaveTypeForm_is_paid" class="form-label">Is Paid <span class="attendance-required">(required)</span></label>
                        <select id="addLeaveTypeForm_is_paid" class="form-select" name="is_paid">
                            <option value="1">YES</option>
                            <option value="0">NO</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade hris-dialog attendance-dialog" id="editLeaveTypeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editLeaveTypeModalTitle">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="editLeaveTypeForm">
                <input type="hidden" name="id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <div><h5 class="modal-title" id="editLeaveTypeModalTitle">Edit Leave Type</h5><p class="small text-muted mb-0" data-attendance-record></p></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="attendance-feedback alert alert-warning" role="status" aria-live="polite" tabindex="-1" hidden></div>
                    <div class="attendance-discard alert alert-warning" hidden>
                        <p>Leave this dialog and discard your unsaved changes?</p>
                        <button type="button" class="btn btn-secondary attendance-stay">Stay and edit</button>
                        <button type="button" class="btn btn-outline-danger attendance-discard-confirm">Discard changes</button>
                    </div>
                    <div class="mb-3">
                        <label for="editLeaveTypeForm_name" class="form-label">Name <span class="attendance-required">(required)</span></label>
                        <input id="editLeaveTypeForm_name" type="text" class="form-control" name="name">
                    </div>
                    <div class="mb-3">
                        <label for="editLeaveTypeForm_is_paid" class="form-label">Is Paid <span class="attendance-required">(required)</span></label>
                        <select id="editLeaveTypeForm_is_paid" class="form-select" name="is_paid">
                            <option value="1">YES</option>
                            <option value="0">NO</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade hris-dialog attendance-dialog" id="deleteLeaveRequestModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteLeaveRequestModalTitle">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLeaveRequestModalTitle">Delete Leave Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
            </div>
            <div class="modal-body">
                    <div class="attendance-feedback alert alert-warning" role="status" aria-live="polite" tabindex="-1" hidden></div>
                    <div class="attendance-discard alert alert-warning" hidden>
                        <p>Leave this dialog and discard your unsaved changes?</p>
                        <button type="button" class="btn btn-secondary attendance-stay">Stay and edit</button>
                        <button type="button" class="btn btn-outline-danger attendance-discard-confirm">Discard changes</button>
                    </div>
                Delete this leave request: <strong class="attendance-delete-record" data-attendance-record></strong>?<p class="text-muted mt-2 mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                <button class="btn btn-danger" type="button" id="confirmDeleteRequestBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade hris-dialog attendance-dialog" id="deleteLeaveTypeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteLeaveTypeModalTitle">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLeaveTypeModalTitle">Delete Leave Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
            </div>
            <div class="modal-body">
                    <div class="attendance-feedback alert alert-warning" role="status" aria-live="polite" tabindex="-1" hidden></div>
                    <div class="attendance-discard alert alert-warning" hidden>
                        <p>Leave this dialog and discard your unsaved changes?</p>
                        <button type="button" class="btn btn-secondary attendance-stay">Stay and edit</button>
                        <button type="button" class="btn btn-outline-danger attendance-discard-confirm">Discard changes</button>
                    </div>
                Delete this leave type: <strong class="attendance-delete-record" data-attendance-record></strong>?<p class="text-muted mt-2 mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                <button class="btn btn-danger" type="button" id="confirmDeleteTypeBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<?php include '../app/views/partials/admin-footer.php'; ?>

<script>
// Page-local Attendance presentation and dialog states. Request options stay owned by each CRUD function.
const attendanceReads = new Map();
let attendanceDetailPending = false;
function attendanceListStart(table, columns, label) {
    const version = (attendanceReads.get(table) || 0) + 1;
    attendanceReads.set(table, version);
    $(table).attr('aria-busy', 'true').find('tbody').html(`<tr><td colspan="${columns}" class="attendance-empty" role="status">Loading ${esc(label)}…</td></tr>`);
    return version;
}
function attendanceListEnd(table, version, failed, retry) {
    if (attendanceReads.get(table) !== version) return false;
    $(table).attr('aria-busy', 'false');
    const $status = $(table).closest('.tab-pane, .management-card').find('.attendance-list-status').first();
    $status.empty();
    if (failed) {
        $(table).find('tbody').empty();
        $status.append(document.createTextNode('Records could not be loaded. '), $('<button>', {
            type: 'button', class: 'btn btn-outline-primary', text: 'Retry loading'
        }).on('click', retry));
    }
    return true;
}
function attendanceDate(value) {
    // Date-only strings are formatted without timezone conversion. Unexpected values remain visible.
    const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(String(value ?? ''));
    if (!match || +match[2] < 1 || +match[2] > 12 || +match[3] < 1 || +match[3] > 31) return esc(value) || '—';
    return `${match[3]} ${['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][+match[2] - 1]} ${match[1]}`;
}
function attendanceLabel(value, labels) {
    return esc(Object.prototype.hasOwnProperty.call(labels, value) ? labels[value] : value) || '—';
}
function attendanceTime(value) {
    // Retain seconds and overnight ordering exactly as supplied; no duration calculation.
    return esc(value) || '—';
}
function attendanceSortControls(id, keys, change) {
    const $select = $('#' + id);
    $select.on('change', function () { if (Object.hasOwn(keys, this.value)) change(this.value); });
    $('#' + id + 'Direction').on('click', function () { change($select.val()); });
    return function (key, direction) {
        $select.val(key);
        $('#' + id + 'Direction').text(direction === 'ASC' ? 'Ascending ↑' : 'Descending ↓')
            .attr('aria-label', `Sorted ${direction === 'ASC' ? 'ascending' : 'descending'}. Reverse sort direction`);
    };
}
function attendanceSnapshot($modal) {
    const values = new URLSearchParams($modal.find('form').serialize());
    values.sort();
    return values.toString();
}
function attendanceUnlock(state) {
    if (!state.disabledFields) return;
    state.disabledFields.forEach(([field, disabled]) => { field.disabled = disabled; });
    state.disabledFields = null;
}
function attendanceFeedback($modal, message) {
    $modal.find('.attendance-feedback').text(message).prop('hidden', !message);
}
function attendanceValidation($modal, res) {
    attendanceFeedback($modal, res.message || 'Changes were not saved. Review the values and try again.');
    if (!res.errors || typeof res.errors !== 'object' || Array.isArray(res.errors)) return;
    $modal.find('input:not([type="hidden"]), select, textarea').each(function () {
        const message = res.errors[this.name];
        if (typeof message !== 'string') return;
        const id = this.id + 'Error';
        $(this).addClass('is-invalid').attr('aria-invalid', 'true').attr('data-attendance-error', id);
        const describedBy = this.getAttribute('aria-describedby') || '';
        this.setAttribute('aria-describedby', (describedBy + ' ' + id).trim());
        $(this).after($('<div>', {id, class: 'invalid-feedback attendance-field-error', text: message}));
    });
}
function attendanceClearValidation($modal) {
    $modal.find('[data-attendance-error]').each(function () {
        const errorId = this.dataset.attendanceError;
        const ids = (this.getAttribute('aria-describedby') || '').split(/\s+/).filter(id => id !== errorId);
        if (ids.length) this.setAttribute('aria-describedby', ids.join(' ')); else this.removeAttribute('aria-describedby');
        $(this).removeClass('is-invalid').removeAttr('aria-invalid data-attendance-error');
    });
    $modal.find('.attendance-field-error').remove();
}
function attendanceRead(selector, options) {
    // Keep row actions disabled while the selected record is being loaded.
    if (attendanceDetailPending) return;
    attendanceDetailPending = true;
    const $modal = $(selector), trigger = $modal.data('attendance-trigger');
    const $actions = $('.attendance-workspace table button');
    $actions.prop('disabled', true);
    const beforeSend = options.beforeSend, success = options.success, error = options.error;
    $.ajax({...options,
        beforeSend: function () { if (beforeSend) beforeSend(); },
        success: function (res) { success(res); },
        error: function (xhr) { if (error) error(xhr); if (trigger && document.contains(trigger)) trigger.focus(); },
        complete: function () { attendanceDetailPending = false; $actions.prop('disabled', false); }
    });
}
function attendanceMutation(selector, options) {
    const $modal = $(selector), state = $modal.data('attendance-state');
    if (!state || state.pending || state.uncertain || $modal.find('[data-attendance-lookup]').toArray().some(select => select.dataset.attendanceLookup !== 'ready')) return;
    state.pending = true;
    state.allowHide = false;
    attendanceClearValidation($modal);
    attendanceFeedback($modal, 'Saving changes. Please wait…');
    // Lock dialog controls after the caller has serialized the original fields.
    $modal.find('button').prop('disabled', true);
    state.disabledFields = $modal.find('input, select, textarea').toArray().map(field => [field, field.disabled]);
    state.disabledFields.forEach(([field]) => { field.disabled = true; });
    $modal.attr('aria-busy', 'true');
    const success = options.success, beforeSend = options.beforeSend;
    $.ajax({...options,
        beforeSend: function () { if (beforeSend) beforeSend(); },
        success: function (res) {
            state.pending = false;
            if (res && res.success === true) {
                state.allowHide = true;
                attendanceUnlock(state);
                attendanceFeedback($modal, 'Changes saved.');
                success(res);
                bootstrap.Modal.getInstance($modal[0])?.hide();
            } else if (res && res.success === false) {
                attendanceValidation($modal, res);
                showToast(res.message || 'Changes were not saved.', 'error');
            } else {
                attendanceUncertain($modal, state);
            }
        },
        error: function (xhr) {
            state.pending = false;
            // Only an explicit, structured rejection establishes that retry is safe.
            if (xhr.status >= 400 && xhr.status < 500 && xhr.responseJSON?.success === false) {
                attendanceValidation($modal, xhr.responseJSON);
                showToast(xhr.responseJSON.message || 'Changes were not saved.', 'error');
            } else attendanceUncertain($modal, state);
        },
        complete: function () {
            hideLoader();
            state.pending = false;
            attendanceUnlock(state);
            $modal.attr('aria-busy', 'false').find('button').prop('disabled', false);
            if (state.uncertain) $modal.find('[type="submit"], .btn-danger').prop('disabled', true);
            if (!state.allowHide) $modal.find('.attendance-feedback').trigger('focus');
        }
    });
}
function attendanceUncertain($modal, state) {
    state.uncertain = true;
    const message = 'The save outcome could not be confirmed. Close this dialog and reload the records to verify the result before trying again. Your entered values are still here.';
    attendanceFeedback($modal, message);
    showToast('Save outcome unknown. Reload records to verify before trying again.', 'error', 8000);
}
$(function () {
    const $dialogs = $('.attendance-dialog');
    $dialogs.each(function () {
        $(this).data('attendance-state', {pending: false, uncertain: false, allowHide: false});
    });
    $dialogs.on('keydown', function (event) {
        if (event.key !== 'Tab') return;
        const $controls = $(this).find('a[href], button, input:not([type="hidden"]), select, textarea, [tabindex]:not([tabindex="-1"])')
            .filter(':visible:not(:disabled)').filter(function () { return this.tabIndex >= 0; });
        const first = $controls[0], last = $controls[$controls.length - 1];
        if (first && ((event.shiftKey && document.activeElement === first) || (!event.shiftKey && document.activeElement === last))) {
            event.preventDefault();
            (event.shiftKey ? last : first).focus();
        }
    });
    $dialogs.on('show.bs.modal', function (event) {
        const $modal = $(this), state = $modal.data('attendance-state');
        state.trigger = event.relatedTarget || $modal.data('attendance-trigger') || document.activeElement;
        state.allowHide = false;
        $modal.find('.attendance-discard').prop('hidden', true);
        if (!state.uncertain) { attendanceFeedback($modal, ''); attendanceClearValidation($modal); }
    }).on('shown.bs.modal', function () {
        const $modal = $(this), state = $modal.data('attendance-state');
        state.baseline = attendanceSnapshot($modal);
        const $focus = $modal.find('form').length ? $modal.find('input:not([type="hidden"]), select, textarea').filter(':visible:not(:disabled)').first() : $modal.find('[data-bs-dismiss="modal"]').last();
        $focus.trigger('focus');
    }).on('hide.bs.modal', function (event) {
        const $modal = $(this), state = $modal.data('attendance-state');
        if (state.pending) { event.preventDefault(); return; }
        if (!state.allowHide && $modal.find('form').length && state.baseline !== attendanceSnapshot($modal)) {
            event.preventDefault();
            state.lastFocus = document.activeElement;
            $modal.find('.attendance-discard').prop('hidden', false).find('.attendance-stay').trigger('focus');
        }
    }).on('hidden.bs.modal', function () {
        const state = $(this).data('attendance-state'), trigger = state.trigger;
        const replacement = trigger?.dataset.id && Array.from(document.querySelectorAll('.attendance-workspace table button')).find(button => button.dataset.id === trigger.dataset.id && button.className === trigger.className);
        const target = trigger && document.contains(trigger) ? trigger : replacement || document.querySelector('.attendance-workspace h1');
        target?.focus();
    });
    $dialogs.find('.attendance-stay').on('click', function () {
        const $modal = $(this).closest('.modal'), state = $modal.data('attendance-state');
        $modal.find('.attendance-discard').prop('hidden', true);
        state.lastFocus?.focus();
    });
    $dialogs.find('.attendance-discard-confirm').on('click', function () {
        const $modal = $(this).closest('.modal');
        // Restore the dialog snapshot on explicit discard; values are never submitted here.
        $modal.data('attendance-state').allowHide = true;
        const form = $modal.find('form')[0];
        if (form) {
            const values = new URLSearchParams($modal.data('attendance-state').baseline);
            for (const field of form.elements) if (field.name && values.has(field.name)) field.value = values.get(field.name);
        }
        bootstrap.Modal.getInstance($modal[0]).hide();
    });
});


let requestPage = 1;
let requestSearch = '';
let requestSortBy = 'created_at';
let requestSortDir = 'DESC';
let typePage = 1;
let typeSearch = '';
let typeSortBy = 'created_at';
let typeSortDir = 'DESC';
let pendingRequestDeleteId = null;
let pendingTypeDeleteId = null;

function attendanceLookup(selector, retry, options) {
    const $selects = $(selector), success = options.success;
    const previous = $selects.toArray().map(select => select.value);
    $selects.attr('data-attendance-lookup', 'loading').prop('disabled', true);
    $selects.next('.attendance-lookup-feedback').remove();
    $selects.after('<div class="attendance-lookup-feedback form-text" role="status">Loading choices…</div>');
    attendanceLookupActions();
    return $.ajax({...options,
        success: function (res) {
            if (!res.success || !Array.isArray(res.data)) { failed(); return; }
            success(res);
            $selects.each(function (index) {
                if (previous[index] && Array.from(this.options).some(option => option.value === previous[index])) this.value = previous[index];
            }).attr('data-attendance-lookup', 'ready').prop('disabled', false);
            $selects.each(function () {
                const $modal = $(this).closest('.modal'), state = $modal.data('attendance-state');
                if (state?.baseline !== undefined) {
                    const baseline = new URLSearchParams(state.baseline);
                    baseline.set(this.name, this.value); baseline.sort(); state.baseline = baseline.toString();
                }
            });
            $selects.next('.attendance-lookup-feedback').remove();
            attendanceLookupActions();
        },
        error: failed
    });
    function failed() {
        $selects.attr('data-attendance-lookup', 'error');
        $selects.next('.attendance-lookup-feedback').empty().append(document.createTextNode('Choices could not be loaded. '), $('<button>', {
            type: 'button', class: 'btn btn-outline-primary', text: 'Retry choices'
        }).on('click', retry));
        attendanceLookupActions();
    }
}
function attendanceLookupActions() {
    const ready = $('.employeeSelect, .leaveTypeSelect').toArray().every(select => select.dataset.attendanceLookup === 'ready');
    $('#addLeaveRequestForm, #editLeaveRequestForm').each(function () {
        const state = $(this).closest('.modal').data('attendance-state');
        $(this).find('[type="submit"]').prop('disabled', !ready || !!state?.pending || !!state?.uncertain);
    });
}

function esc(str) {
    return String(str ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    }[c]));
}

function formDataWithoutId(form) {
    return $.param($(form).serializeArray().filter(item => item.name !== 'id'));
}

let syncRequestSort;
$(document).ready(function () {
    syncRequestSort = attendanceSortControls('requestSortBy', {'employee_name': 'Employee', 'leave_type': 'Leave type', 'date_from': 'Period start', 'date_to': 'Period end', 'status': 'Status', 'approved_by_name': 'Approved by', 'approved_at': 'Approved at', 'created_at': 'Date added'}, key => {
        requestSortDir = requestSortBy === key && requestSortDir === 'ASC' ? 'DESC' : 'ASC';
        requestSortBy = key; getAllLeaveRequests(1);
    });
    $('#leaveTabs button').on('shown.bs.tab', function () {
        const pane = this.getAttribute('aria-controls');
        $('[data-leave-add]').each(function () { this.hidden = this.dataset.leaveAdd !== pane; });
    });
    loadEmployees();
    loadLeaveTypeOptions();
    getAllLeaveRequests(1);
    getAllLeaveTypes(1);
    bindLeaveDateRules('#addLeaveRequestForm');
    bindLeaveDateRules('#editLeaveRequestForm');

    $('#requestSearchForm').on('submit', function (e) {
        e.preventDefault();
        requestSearch = $('#requestSearchInput').val();
        getAllLeaveRequests(1);
    });

    $('#clearRequestSearchBtn').on('click', function () {
        $('#requestSearchInput').val('');
        requestSearch = '';
        getAllLeaveRequests(1);
    });

    $('#typeSearchForm').on('submit', function (e) {
        e.preventDefault();
        typeSearch = $('#typeSearchInput').val();
        getAllLeaveTypes(1);
    });

    $('#clearTypeSearchBtn').on('click', function () {
        $('#typeSearchInput').val('');
        typeSearch = '';
        getAllLeaveTypes(1);
    });

    $('#addLeaveRequestForm').on('submit', function (e) {
        e.preventDefault();
        addLeaveRequest(this);
    });

    $('#addLeaveRequestModal').on('hidden.bs.modal', function () {
        $('#addLeaveRequestForm [name="date_to"]').removeAttr('min');
    });

    $('#editLeaveRequestForm').on('submit', function (e) {
        e.preventDefault();
        updateLeaveRequest(this);
    });

    $('#addLeaveTypeForm').on('submit', function (e) {
        e.preventDefault();
        addLeaveType(this);
    });

    $('#editLeaveTypeForm').on('submit', function (e) {
        e.preventDefault();
        updateLeaveType(this);
    });

    $(document).on('click', '.request-sort', function () {
        let sortBy = $(this).data('sort');
        requestSortDir = requestSortBy === sortBy && requestSortDir === 'ASC' ? 'DESC' : 'ASC';
        requestSortBy = sortBy;
        getAllLeaveRequests(1);
    });

    $(document).on('click', '.type-sort', function () {
        let sortBy = $(this).data('sort');
        typeSortDir = typeSortBy === sortBy && typeSortDir === 'ASC' ? 'DESC' : 'ASC';
        typeSortBy = sortBy;
        getAllLeaveTypes(1);
    });

    $(document).on('click', '.request-page-btn', function (e) {
        e.preventDefault();
        getAllLeaveRequests($(this).data('page'));
    });

    $(document).on('click', '.type-page-btn', function (e) {
        e.preventDefault();
        getAllLeaveTypes($(this).data('page'));
    });

    $(document).on('click', '.editLeaveRequestBtn', function () {
        $('#editLeaveRequestModal').data('attendance-trigger', this).find('[data-attendance-record]').text($(this).closest('tr').find('.attendance-identity, .attendance-period').find('strong, .attendance-secondary').map(function () { return $(this).text().trim(); }).get().join(' · '));
        getLeaveRequest($(this).data('id'));
    });

    $(document).on('click', '.deleteLeaveRequestBtn', function () {
        $('#deleteLeaveRequestModal').data('attendance-trigger', this).find('[data-attendance-record]').text($(this).closest('tr').find('.attendance-identity, .attendance-period').find('strong, .attendance-secondary').map(function () { return $(this).text().trim(); }).get().join(' · '));
        pendingRequestDeleteId = $(this).data('id');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteLeaveRequestModal')).show();
    });

    $(document).on('click', '.editLeaveTypeBtn', function () {
        $('#editLeaveTypeModal').data('attendance-trigger', this).find('[data-attendance-record]').text($(this).closest('tr').find('.attendance-identity, .attendance-period').find('strong, .attendance-secondary').map(function () { return $(this).text().trim(); }).get().join(' · '));
        getLeaveType($(this).data('id'));
    });

    $(document).on('click', '.deleteLeaveTypeBtn', function () {
        $('#deleteLeaveTypeModal').data('attendance-trigger', this).find('[data-attendance-record]').text($(this).closest('tr').find('.attendance-identity, .attendance-period').find('strong, .attendance-secondary').map(function () { return $(this).text().trim(); }).get().join(' · '));
        pendingTypeDeleteId = $(this).data('id');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteLeaveTypeModal')).show();
    });

    $('#confirmDeleteRequestBtn').on('click', function () {
        if (pendingRequestDeleteId) {
            deleteLeaveRequest(pendingRequestDeleteId);
        }
    });

    $('#confirmDeleteTypeBtn').on('click', function () {
        if (pendingTypeDeleteId) {
            deleteLeaveType(pendingTypeDeleteId);
        }
    });
});

function loadEmployees() {
    attendanceLookup('.employeeSelect', loadEmployees, {
        url: '/hris/leaves/employees/options',
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            let options = '<option value="">Select Employee</option>';
            (res.data || []).forEach(emp => {
                let name = `${emp.employee_no ?? ''} - ${emp.lastname ?? ''}, ${emp.firstname ?? ''} ${emp.middlename ?? ''}`.replace(/\s+/g, ' ').trim();
                options += `<option value="${emp.id}">${esc(name)}</option>`;
            });
            $('.employeeSelect').html(options);
        },
        error: function (xhr) {
            showToast('Failed to load employees.', 'error');
        }
    });
}

function bindLeaveDateRules(formSelector) {
    let $form = $(formSelector);
    let $dateFrom = $form.find('[name="date_from"]');
    let $dateTo = $form.find('[name="date_to"]');

    $dateFrom.on('change', function () {
        let dateFrom = $(this).val();
        $dateTo.attr('min', dateFrom);

        if ($dateTo.val() && $dateTo.val() < dateFrom) {
            $dateTo.val(dateFrom);
        }
    });
}

function loadLeaveTypeOptions() {
    attendanceLookup('.leaveTypeSelect', loadLeaveTypeOptions, {
        url: '/hris/leaves/types/options',
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            let options = '<option value="">Select Leave Type</option>';
            (res.data || []).forEach(type => {
                options += `<option value="${type.id}">${esc(type.name)}</option>`;
            });
            $('.leaveTypeSelect').html(options);
        },
        error: function (xhr) {
            showToast('Failed to load leave types.', 'error');
        }
    });
}

function getAllLeaveRequests(page) {
    const version = attendanceListStart('#leaveRequestsTable', 8, 'leave requests');

    $.ajax({
        url: '/hris/leaves/requests/getall',
        type: 'GET',
        dataType: 'json',
        data: {
            page: page,
            search: requestSearch,
            sort_by: requestSortBy,
            sort_dir: requestSortDir
        },
        success: function (res) {
            if (!attendanceListEnd('#leaveRequestsTable', version, !res || res.success !== true || !Array.isArray(res.data), () => getAllLeaveRequests(page))) return;
            if (!res || res.success !== true || !Array.isArray(res.data)) return;
            requestPage = res.currentPage;
            renderRequestRows(res.data || []);
            renderPagination('#requestsPagination', res.currentPage, res.totalPages, 'request-page-btn');
            applySortIndicators('.request-sort', requestSortBy, requestSortDir);
            syncRequestSort(requestSortBy, requestSortDir);
        },
        error: function (xhr) {
            attendanceListEnd('#leaveRequestsTable', version, true, () => getAllLeaveRequests(page));
            showToast('Unable to load leave requests.', 'error');
        }
    });
}

function renderRequestRows(requests) {
    let rows = '';

    if (requests.length === 0) {
        rows = '<tr><td colspan="8" class="attendance-empty" role="status">No leave requests found</td></tr>';
    } else {
        requests.forEach(req => {
            rows += `
                <tr>
                    <td class="ps-4 attendance-identity"><strong>${esc(req.employee_name) || '—'}</strong><span class="attendance-secondary">${esc(req.employee_no) || '—'}</span></td>
                    <td>${esc(req.leave_type) || '-'}</td>
                    <td class="attendance-period"><span class="attendance-secondary">From ${attendanceDate(req.date_from)}</span><span class="attendance-secondary">To ${attendanceDate(req.date_to)}</span></td>
                    <td><span class="badge text-bg-secondary hris-status ${MonteroHris.statusClass(req.status, 'leave')}">${attendanceLabel(req.status, {PENDING: 'Pending', APPROVED: 'Approved', REJECTED: 'Rejected', CANCELLED: 'Cancelled'})}</span></td>
                    <td>${esc(req.approved_by_name) || '-'}</td>
                    <td>${esc(req.approved_at) || '-'}</td>
                    <td class="hris-created-at attendance-created">${esc(req.created_at) || '-'}</td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm editLeaveRequestBtn hris-action" data-id="${req.id}" aria-label="Edit leave request: ${esc(req.employee_name)}" title="Edit leave request">
                            <i class="bi bi-pencil-square" aria-hidden="true"></i>
                        </button>
                        <button class="btn btn-sm deleteLeaveRequestBtn hris-action hris-action--danger" data-id="${req.id}" aria-label="Delete leave request: ${esc(req.employee_name)}" title="Delete leave request">
                            <i class="bi bi-trash" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }

    $('#leaveRequestsTable tbody').html(rows);
}

function getAllLeaveTypes(page) {
    const version = attendanceListStart('#leaveTypesTable', 4, 'leave types');

    $.ajax({
        url: '/hris/leaves/types/getall',
        type: 'GET',
        dataType: 'json',
        data: {
            page: page,
            search: typeSearch,
            sort_by: typeSortBy,
            sort_dir: typeSortDir
        },
        success: function (res) {
            if (!attendanceListEnd('#leaveTypesTable', version, !res || res.success !== true || !Array.isArray(res.data), () => getAllLeaveTypes(page))) return;
            if (!res || res.success !== true || !Array.isArray(res.data)) return;
            typePage = res.currentPage;
            renderTypeRows(res.data || []);
            renderPagination('#typesPagination', res.currentPage, res.totalPages, 'type-page-btn');
            applySortIndicators('.type-sort', typeSortBy, typeSortDir);
        },
        error: function (xhr) {
            attendanceListEnd('#leaveTypesTable', version, true, () => getAllLeaveTypes(page));
            showToast('Unable to load leave types.', 'error');
        }
    });
}

function renderTypeRows(types) {
    let rows = '';

    if (types.length === 0) {
        rows = '<tr><td colspan="4" class="attendance-empty" role="status">No leave types found</td></tr>';
    } else {
        types.forEach(type => {
            rows += `
                <tr>
                    <td class="ps-4 attendance-identity"><strong>${esc(type.name)}</strong></td>
                    <td><span class="hris-status">${type.is_paid == 1 ? 'Paid' : 'Unpaid'}</span></td>
                    <td class="hris-created-at attendance-created">${esc(type.created_at) || '-'}</td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm editLeaveTypeBtn hris-action" data-id="${type.id}" aria-label="Edit leave type: ${esc(type.name)}" title="Edit leave type">
                            <i class="bi bi-pencil-square" aria-hidden="true"></i>
                        </button>
                        <button class="btn btn-sm deleteLeaveTypeBtn hris-action hris-action--danger" data-id="${type.id}" aria-label="Delete leave type: ${esc(type.name)}" title="Delete leave type">
                            <i class="bi bi-trash" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }

    $('#leaveTypesTable tbody').html(rows);
}

function renderPagination(selector, page, totalPages, buttonClass) {
    let start = Math.max(1, page - 2);
    let end = Math.min(totalPages, page + 2);
    let html = '';

    if (page > 1) {
        html += `<li class="page-item"><a href="#" class="page-link ${buttonClass}" data-page="${page - 1}">&laquo; Prev</a></li>`;
    }

    for (let p = start; p <= end; p++) {
        html += `<li class="page-item ${p === page ? 'active' : ''}"><a href="#" class="page-link ${buttonClass}" data-page="${p}"${p === page ? ' aria-current="page"' : ''}>${p}</a></li>`;
    }

    if (page < totalPages) {
        html += `<li class="page-item"><a href="#" class="page-link ${buttonClass}" data-page="${page + 1}">Next &raquo;</a></li>`;
    }

    $(selector).html(html);
}

function applySortIndicators(selector, sortBy, sortDir) {
    $(selector).each(function () {
        const $button = $(this);
        const $header = $button.closest('th');
        $header.removeAttr('aria-sort');
        if (MonteroHris.createdSort(this, sortBy, sortDir)) return;
        const label = $button.data('label') || $button.text().replace(/\s+(ASC|DESC)$/, '').trim();
        const active = $button.data('sort') === sortBy;
        const ascending = sortDir === 'ASC';
        $button.data('label', label);
        if (active) $header.attr('aria-sort', ascending ? 'ascending' : 'descending');
        $button.empty().append(document.createTextNode(label));
        $button.append($('<i>', {
            class: 'bi hris-sort-mark ' + (active ? (ascending ? 'bi-arrow-up' : 'bi-arrow-down') : 'bi-arrow-down-up'),
            'aria-hidden': 'true'
        }));
        // Retain the original direction text for print; screen indicators are decorative.
        if (active) $button.append($('<span>', {class: 'hris-sort-print', 'aria-hidden': 'true', text: ' ' + sortDir}));
        $button.attr('aria-label', label + (active ? ': sorted ' + (ascending ? 'ascending' : 'descending') + '. ' : ': ') + 'Sort ' + (active && ascending ? 'descending' : 'ascending'));
    });
}

function addLeaveRequest(form) {
    attendanceMutation('#addLeaveRequestModal', {
        url: '/hris/leaves/requests/create',
        type: 'POST',
        data: $(form).serialize(),
        dataType: 'json',
        beforeSend: function () {
            showLoader("Saving leave request...");
        },
        success: function (res) {
            hideLoader();
            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }
            showToast(res.message, 'success');
            $('#addLeaveRequestForm')[0].reset();
            $('#addLeaveRequestForm [name="date_to"]').removeAttr('min');
            bootstrap.Modal.getInstance(document.getElementById('addLeaveRequestModal')).hide();
            getAllLeaveRequests(1);
        }
    });
}

function getLeaveRequest(id) {
    if ($('.employeeSelect, .leaveTypeSelect').toArray().some(select => select.dataset.attendanceLookup !== 'ready')) {
        showToast('Employee and leave type choices must finish loading. Use Retry choices in Add Leave Request if loading failed.', 'error', 8000);
        return;
    }
    showLoader("Loading leave request...");

    attendanceRead('#editLeaveRequestModal', {
        url: '/hris/leaves/requests/getbyid/' + id,
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            hideLoader();
            if (!res.success) {
                showToast(res.message, 'error');
                return;
            }

            let req = res.data;
            let $form = $('#editLeaveRequestForm');
            $form.find('[name="id"]').val(req.id);
            $form.find('[name="employee_id"]').val(req.employee_id);
            $form.find('[name="leave_type_id"]').val(req.leave_type_id);
            $form.find('[name="date_from"]').val(req.date_from);
            $form.find('[name="date_to"]').val(req.date_to);
            $form.find('[name="date_to"]').attr('min', req.date_from);
            $form.find('[name="status"]').val(req.status);
            $form.find('[name="reason"]').val(req.reason);

            bootstrap.Modal.getOrCreateInstance(document.getElementById('editLeaveRequestModal')).show();
        },
        error: function (xhr) {
            hideLoader();
            showToast('Unable to load leave request.', 'error');
        }
    });
}

function updateLeaveRequest(form) {
    let id = $(form).find('[name="id"]').val();

    attendanceMutation('#editLeaveRequestModal', {
        url: '/hris/leaves/requests/update/' + id,
        type: 'POST',
        data: formDataWithoutId(form),
        dataType: 'json',
        beforeSend: function () {
            showLoader("Updating leave request...");
        },
        success: function (res) {
            hideLoader();
            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }
            showToast(res.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('editLeaveRequestModal')).hide();
            getAllLeaveRequests(requestPage);
        }
    });
}

function deleteLeaveRequest(id) {
    attendanceMutation('#deleteLeaveRequestModal', {
        url: '/hris/leaves/requests/delete/' + id,
        type: 'POST',
        dataType: 'json',
        data: {
            csrf_token: $('#addLeaveRequestForm [name="csrf_token"]').val()
        },
        beforeSend: function () {
            showLoader("Deleting leave request...");
        },
        success: function (res) {
            hideLoader();
            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }
            showToast(res.message, 'success');
            getAllLeaveRequests(requestPage);
        }
    });
}

function addLeaveType(form) {
    attendanceMutation('#addLeaveTypeModal', {
        url: '/hris/leaves/types/create',
        type: 'POST',
        data: $(form).serialize(),
        dataType: 'json',
        beforeSend: function () {
            showLoader("Saving leave type...");
        },
        success: function (res) {
            hideLoader();
            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }
            showToast(res.message, 'success');
            $('#addLeaveTypeForm')[0].reset();
            bootstrap.Modal.getInstance(document.getElementById('addLeaveTypeModal')).hide();
            loadLeaveTypeOptions();
            getAllLeaveTypes(1);
        }
    });
}

function getLeaveType(id) {
    showLoader("Loading leave type...");

    attendanceRead('#editLeaveTypeModal', {
        url: '/hris/leaves/types/getbyid/' + id,
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            hideLoader();
            if (!res.success) {
                showToast(res.message, 'error');
                return;
            }

            let type = res.data;
            let $form = $('#editLeaveTypeForm');
            $form.find('[name="id"]').val(type.id);
            $form.find('[name="name"]').val(type.name);
            $form.find('[name="is_paid"]').val(type.is_paid);

            bootstrap.Modal.getOrCreateInstance(document.getElementById('editLeaveTypeModal')).show();
        },
        error: function (xhr) {
            hideLoader();
            showToast('Unable to load leave type.', 'error');
        }
    });
}

function updateLeaveType(form) {
    let id = $(form).find('[name="id"]').val();

    attendanceMutation('#editLeaveTypeModal', {
        url: '/hris/leaves/types/update/' + id,
        type: 'POST',
        data: formDataWithoutId(form),
        dataType: 'json',
        beforeSend: function () {
            showLoader("Updating leave type...");
        },
        success: function (res) {
            hideLoader();
            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }
            showToast(res.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('editLeaveTypeModal')).hide();
            loadLeaveTypeOptions();
            getAllLeaveTypes(typePage);
        }
    });
}

function deleteLeaveType(id) {
    attendanceMutation('#deleteLeaveTypeModal', {
        url: '/hris/leaves/types/delete/' + id,
        type: 'POST',
        dataType: 'json',
        data: {
            csrf_token: $('#addLeaveTypeForm [name="csrf_token"]').val()
        },
        beforeSend: function () {
            showLoader("Deleting leave type...");
        },
        success: function (res) {
            hideLoader();
            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }
            showToast(res.message, 'success');
            loadLeaveTypeOptions();
            getAllLeaveTypes(typePage);
        }
    });
}
</script>
</body>
</html>
