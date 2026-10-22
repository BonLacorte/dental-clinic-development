<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_hris/partials/hris_nav.php'; ?>

<div class="container-fluid py-4 management-page hris-page">
    <div class="card shadow-sm management-card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h1 class="h5 mb-0" id="leavesHeading">Manage Leaves</h1>

            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLeaveTypeModal">
                    <i class="bi bi-plus-lg"></i> Add Leave Type
                </button>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLeaveRequestModal">
                    <i class="bi bi-plus-lg"></i> Add Leave Request
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
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-secondary" type="button" id="clearRequestSearchBtn">Clear</button>
                        </div>
                        <div class="col-auto hris-order-control"><button type="button" class="btn btn-outline-secondary request-sort hris-created-sort" data-sort="created_at" aria-pressed="true" aria-label="Date added: newest first. Sort oldest first" title="Sort by date added">Date added: newest first</button></div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <p class="hris-scroll-hint" id="leaveRequestsTableScrollHelp">Use horizontal scrolling when columns extend beyond this view.</p>
                    <div class="table-responsive hris-table-region" tabindex="0" role="region" aria-labelledby="requests-tab" aria-describedby="leaveRequestsTableScrollHelp">
                        <table class="table table-hover align-middle mb-0 hris-data-table" id="leaveRequestsTable">
                            <caption class="visually-hidden">Leave requests with their dates, status and approval details.</caption>
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="ps-4"><button class="btn btn-link p-0 request-sort" data-sort="employee_name">Employee</button></th>
                                    <th scope="col"><button class="btn btn-link p-0 request-sort" data-sort="leave_type">Leave Type</button></th>
                                    <th scope="col"><button class="btn btn-link p-0 request-sort" data-sort="date_from">Date From</button></th>
                                    <th scope="col"><button class="btn btn-link p-0 request-sort" data-sort="date_to">Date To</button></th>
                                    <th scope="col"><button class="btn btn-link p-0 request-sort" data-sort="status">Status</button></th>
                                    <th scope="col"><button class="btn btn-link p-0 request-sort" data-sort="approved_by_name">Approved By</button></th>
                                    <th scope="col"><button class="btn btn-link p-0 request-sort" data-sort="approved_at">Approved At</button></th>
                                    <th scope="col" class="hris-created-at"><button class="btn btn-link p-0 request-sort" data-sort="created_at">Created At</button></th>
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
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-secondary" type="button" id="clearTypeSearchBtn">Clear</button>
                        </div>
                        <div class="col-auto hris-order-control"><button type="button" class="btn btn-outline-secondary type-sort hris-created-sort" data-sort="created_at" aria-pressed="true" aria-label="Date added: newest first. Sort oldest first" title="Sort by date added">Date added: newest first</button></div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <p class="hris-scroll-hint" id="leaveTypesTableScrollHelp">Use horizontal scrolling when columns extend beyond this view.</p>
                    <div class="table-responsive hris-table-region" tabindex="0" role="region" aria-labelledby="types-tab" aria-describedby="leaveTypesTableScrollHelp">
                        <table class="table table-hover align-middle mb-0 hris-data-table" id="leaveTypesTable">
                            <caption class="visually-hidden">Leave types and their existing paid or unpaid setting.</caption>
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="ps-4"><button class="btn btn-link p-0 type-sort" data-sort="name">Name</button></th>
                                    <th scope="col"><button class="btn btn-link p-0 type-sort" data-sort="is_paid">Is Paid</button></th>
                                    <th scope="col" class="hris-created-at"><button class="btn btn-link p-0 type-sort" data-sort="created_at">Created At</button></th>
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

<div class="modal fade hris-dialog" id="addLeaveRequestModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addLeaveRequestModalTitle">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="addLeaveRequestForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="addLeaveRequestModalTitle">Add Leave Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="addLeaveRequestForm_employee_id" class="form-label">Employee</label>
                            <select id="addLeaveRequestForm_employee_id" class="form-select employeeSelect" name="employee_id"></select>
                        </div>
                        <div class="col-md-6">
                            <label for="addLeaveRequestForm_leave_type_id" class="form-label">Leave Type</label>
                            <select id="addLeaveRequestForm_leave_type_id" class="form-select leaveTypeSelect" name="leave_type_id"></select>
                        </div>
                        <div class="col-md-4">
                            <label for="addLeaveRequestForm_date_from" class="form-label">Date From</label>
                            <input id="addLeaveRequestForm_date_from" type="date" class="form-control" name="date_from">
                        </div>
                        <div class="col-md-4">
                            <label for="addLeaveRequestForm_date_to" class="form-label">Date To</label>
                            <input id="addLeaveRequestForm_date_to" type="date" class="form-control" name="date_to">
                        </div>
                        <div class="col-md-4">
                            <label for="addLeaveRequestForm_status" class="form-label">Status</label>
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

<div class="modal fade hris-dialog" id="editLeaveRequestModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editLeaveRequestModalTitle">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="editLeaveRequestForm">
                <input type="hidden" name="id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="editLeaveRequestModalTitle">Edit Leave Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="editLeaveRequestForm_employee_id" class="form-label">Employee</label>
                            <select id="editLeaveRequestForm_employee_id" class="form-select employeeSelect" name="employee_id"></select>
                        </div>
                        <div class="col-md-6">
                            <label for="editLeaveRequestForm_leave_type_id" class="form-label">Leave Type</label>
                            <select id="editLeaveRequestForm_leave_type_id" class="form-select leaveTypeSelect" name="leave_type_id"></select>
                        </div>
                        <div class="col-md-4">
                            <label for="editLeaveRequestForm_date_from" class="form-label">Date From</label>
                            <input id="editLeaveRequestForm_date_from" type="date" class="form-control" name="date_from">
                        </div>
                        <div class="col-md-4">
                            <label for="editLeaveRequestForm_date_to" class="form-label">Date To</label>
                            <input id="editLeaveRequestForm_date_to" type="date" class="form-control" name="date_to">
                        </div>
                        <div class="col-md-4">
                            <label for="editLeaveRequestForm_status" class="form-label">Status</label>
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

<div class="modal fade hris-dialog" id="addLeaveTypeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addLeaveTypeModalTitle">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="addLeaveTypeForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="addLeaveTypeModalTitle">Add Leave Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="addLeaveTypeForm_name" class="form-label">Name</label>
                        <input id="addLeaveTypeForm_name" type="text" class="form-control" name="name">
                    </div>
                    <div class="mb-3">
                        <label for="addLeaveTypeForm_is_paid" class="form-label">Is Paid</label>
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

<div class="modal fade hris-dialog" id="editLeaveTypeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editLeaveTypeModalTitle">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editLeaveTypeForm">
                <input type="hidden" name="id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="editLeaveTypeModalTitle">Edit Leave Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editLeaveTypeForm_name" class="form-label">Name</label>
                        <input id="editLeaveTypeForm_name" type="text" class="form-control" name="name">
                    </div>
                    <div class="mb-3">
                        <label for="editLeaveTypeForm_is_paid" class="form-label">Is Paid</label>
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

<div class="modal fade hris-dialog" id="deleteLeaveRequestModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteLeaveRequestModalTitle">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLeaveRequestModalTitle">Delete Leave Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this leave request? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                <button class="btn btn-danger" type="button" id="confirmDeleteRequestBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade hris-dialog" id="deleteLeaveTypeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteLeaveTypeModalTitle">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLeaveTypeModalTitle">Delete Leave Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this leave type? This action cannot be undone.
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

$(document).ready(function () {
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
        getLeaveRequest($(this).data('id'));
    });

    $(document).on('click', '.deleteLeaveRequestBtn', function () {
        pendingRequestDeleteId = $(this).data('id');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteLeaveRequestModal')).show();
    });

    $(document).on('click', '.editLeaveTypeBtn', function () {
        getLeaveType($(this).data('id'));
    });

    $(document).on('click', '.deleteLeaveTypeBtn', function () {
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
    $.ajax({
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
            console.log(xhr.responseText);
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
    $.ajax({
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
            console.log(xhr.responseText);
            showToast('Failed to load leave types.', 'error');
        }
    });
}

function getAllLeaveRequests(page) {
    showLoader("Loading leave requests...");

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
            hideLoader();
            requestPage = res.currentPage;
            renderRequestRows(res.data || []);
            renderPagination('#requestsPagination', res.currentPage, res.totalPages, 'request-page-btn');
            applySortIndicators('.request-sort', requestSortBy, requestSortDir);
        },
        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Unable to load leave requests.', 'error');
        }
    });
}

function renderRequestRows(requests) {
    let rows = '';

    if (requests.length === 0) {
        rows = '<tr><td colspan="9" class="text-center py-3">No leave requests found</td></tr>';
    } else {
        requests.forEach(req => {
            rows += `
                <tr>
                    <td class="ps-4">${esc(req.employee_name) || '-'}</td>
                    <td>${esc(req.leave_type) || '-'}</td>
                    <td>${esc(req.date_from)}</td>
                    <td>${esc(req.date_to)}</td>
                    <td><span class="badge text-bg-secondary hris-status ${MonteroHris.statusClass(req.status, 'leave')}">${esc(req.status)}</span></td>
                    <td>${esc(req.approved_by_name) || '-'}</td>
                    <td>${esc(req.approved_at) || '-'}</td>
                    <td class="hris-created-at">${esc(req.created_at) || '-'}</td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm editLeaveRequestBtn hris-action" data-id="${req.id}" aria-label="Edit leave request" title="Edit leave request">
                            <i class="bi bi-pencil-square" aria-hidden="true"></i>
                        </button>
                        <button class="btn btn-sm deleteLeaveRequestBtn hris-action hris-action--danger" data-id="${req.id}" aria-label="Delete leave request" title="Delete leave request">
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
    showLoader("Loading leave types...");

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
            hideLoader();
            typePage = res.currentPage;
            renderTypeRows(res.data || []);
            renderPagination('#typesPagination', res.currentPage, res.totalPages, 'type-page-btn');
            applySortIndicators('.type-sort', typeSortBy, typeSortDir);
        },
        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Unable to load leave types.', 'error');
        }
    });
}

function renderTypeRows(types) {
    let rows = '';

    if (types.length === 0) {
        rows = '<tr><td colspan="4" class="text-center py-3">No leave types found</td></tr>';
    } else {
        types.forEach(type => {
            rows += `
                <tr>
                    <td class="ps-4">${esc(type.name)}</td>
                    <td>${type.is_paid == 1 ? 'YES' : 'NO'}</td>
                    <td class="hris-created-at">${esc(type.created_at) || '-'}</td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm editLeaveTypeBtn hris-action" data-id="${type.id}" aria-label="Edit leave type" title="Edit leave type">
                            <i class="bi bi-pencil-square" aria-hidden="true"></i>
                        </button>
                        <button class="btn btn-sm deleteLeaveTypeBtn hris-action hris-action--danger" data-id="${type.id}" aria-label="Delete leave type" title="Delete leave type">
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
    $.ajax({
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
        },
        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Something went wrong.', 'error');
        }
    });
}

function getLeaveRequest(id) {
    showLoader("Loading leave request...");

    $.ajax({
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
            console.log(xhr.responseText);
            showToast('Unable to load leave request.', 'error');
        }
    });
}

function updateLeaveRequest(form) {
    let id = $(form).find('[name="id"]').val();

    $.ajax({
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
        },
        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Something went wrong.', 'error');
        }
    });
}

function deleteLeaveRequest(id) {
    $.ajax({
        url: '/hris/leaves/requests/delete/' + id,
        type: 'POST',
        dataType: 'json',
        data: {
            csrf_token: $('#addLeaveRequestForm [name="csrf_token"]').val()
        },
        beforeSend: function () {
            bootstrap.Modal.getInstance(document.getElementById('deleteLeaveRequestModal')).hide();
            showLoader("Deleting leave request...");
        },
        success: function (res) {
            hideLoader();
            pendingRequestDeleteId = null;
            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }
            showToast(res.message, 'success');
            getAllLeaveRequests(requestPage);
        },
        error: function (xhr) {
            hideLoader();
            pendingRequestDeleteId = null;
            console.log(xhr.responseText);
            showToast('Something went wrong.', 'error');
        }
    });
}

function addLeaveType(form) {
    $.ajax({
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
        },
        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Something went wrong.', 'error');
        }
    });
}

function getLeaveType(id) {
    showLoader("Loading leave type...");

    $.ajax({
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
            console.log(xhr.responseText);
            showToast('Unable to load leave type.', 'error');
        }
    });
}

function updateLeaveType(form) {
    let id = $(form).find('[name="id"]').val();

    $.ajax({
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
        },
        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Something went wrong.', 'error');
        }
    });
}

function deleteLeaveType(id) {
    $.ajax({
        url: '/hris/leaves/types/delete/' + id,
        type: 'POST',
        dataType: 'json',
        data: {
            csrf_token: $('#addLeaveTypeForm [name="csrf_token"]').val()
        },
        beforeSend: function () {
            bootstrap.Modal.getInstance(document.getElementById('deleteLeaveTypeModal')).hide();
            showLoader("Deleting leave type...");
        },
        success: function (res) {
            hideLoader();
            pendingTypeDeleteId = null;
            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }
            showToast(res.message, 'success');
            loadLeaveTypeOptions();
            getAllLeaveTypes(typePage);
        },
        error: function (xhr) {
            hideLoader();
            pendingTypeDeleteId = null;
            console.log(xhr.responseText);
            showToast('Something went wrong.', 'error');
        }
    });
}
</script>
</body>
</html>
