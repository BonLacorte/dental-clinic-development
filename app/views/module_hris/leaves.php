<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_hris/partials/hris_nav.php'; ?>

<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Manage Leaves</h5>

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
                    <button class="nav-link active" id="requests-tab" data-bs-toggle="tab" data-bs-target="#requestsPane" type="button" role="tab">
                        Leave Requests
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="types-tab" data-bs-toggle="tab" data-bs-target="#typesPane" type="button" role="tab">
                        Leave Types
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="requestsPane" role="tabpanel">
                <div class="card-body border-bottom">
                    <form id="requestSearchForm" class="row g-2 align-items-end">
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label">Search</label>
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
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="leaveRequestsTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4"><button class="btn btn-link p-0 request-sort" data-sort="employee_name">Employee</button></th>
                                    <th><button class="btn btn-link p-0 request-sort" data-sort="leave_type">Leave Type</button></th>
                                    <th><button class="btn btn-link p-0 request-sort" data-sort="date_from">Date From</button></th>
                                    <th><button class="btn btn-link p-0 request-sort" data-sort="date_to">Date To</button></th>
                                    <th><button class="btn btn-link p-0 request-sort" data-sort="status">Status</button></th>
                                    <th><button class="btn btn-link p-0 request-sort" data-sort="approved_by_name">Approved By</button></th>
                                    <th><button class="btn btn-link p-0 request-sort" data-sort="approved_at">Approved At</button></th>
                                    <th><button class="btn btn-link p-0 request-sort" data-sort="created_at">Created At</button></th>
                                    <th class="text-end pe-4">Actions</th>
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

            <div class="tab-pane fade" id="typesPane" role="tabpanel">
                <div class="card-body border-bottom">
                    <form id="typeSearchForm" class="row g-2 align-items-end">
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label">Search</label>
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
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="leaveTypesTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4"><button class="btn btn-link p-0 type-sort" data-sort="name">Name</button></th>
                                    <th><button class="btn btn-link p-0 type-sort" data-sort="is_paid">Is Paid</button></th>
                                    <th><button class="btn btn-link p-0 type-sort" data-sort="created_at">Created At</button></th>
                                    <th class="text-end pe-4">Actions</th>
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

<div class="modal fade" id="addLeaveRequestModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="addLeaveRequestForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Add Leave Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Employee</label>
                            <select class="form-select employeeSelect" name="employee_id"></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Leave Type</label>
                            <select class="form-select leaveTypeSelect" name="leave_type_id"></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date From</label>
                            <input type="date" class="form-control" name="date_from">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date To</label>
                            <input type="date" class="form-control" name="date_to">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="PENDING">PENDING</option>
                                <option value="APPROVED">APPROVED</option>
                                <option value="REJECTED">REJECTED</option>
                                <option value="CANCELLED">CANCELLED</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Reason</label>
                            <textarea class="form-control" name="reason" rows="3"></textarea>
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

<div class="modal fade" id="editLeaveRequestModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="editLeaveRequestForm">
                <input type="hidden" name="id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Leave Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Employee</label>
                            <select class="form-select employeeSelect" name="employee_id"></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Leave Type</label>
                            <select class="form-select leaveTypeSelect" name="leave_type_id"></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date From</label>
                            <input type="date" class="form-control" name="date_from">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date To</label>
                            <input type="date" class="form-control" name="date_to">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="PENDING">PENDING</option>
                                <option value="APPROVED">APPROVED</option>
                                <option value="REJECTED">REJECTED</option>
                                <option value="CANCELLED">CANCELLED</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Reason</label>
                            <textarea class="form-control" name="reason" rows="3"></textarea>
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

<div class="modal fade" id="addLeaveTypeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="addLeaveTypeForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Add Leave Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Is Paid</label>
                        <select class="form-select" name="is_paid">
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

<div class="modal fade" id="editLeaveTypeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editLeaveTypeForm">
                <input type="hidden" name="id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Leave Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Is Paid</label>
                        <select class="form-select" name="is_paid">
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

<div class="modal fade" id="deleteLeaveRequestModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Leave Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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

<div class="modal fade" id="deleteLeaveTypeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Leave Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                    <td><span class="badge text-bg-secondary">${esc(req.status)}</span></td>
                    <td>${esc(req.approved_by_name) || '-'}</td>
                    <td>${esc(req.approved_at) || '-'}</td>
                    <td>${esc(req.created_at) || '-'}</td>
                    <td class="text-end pe-4">
                        <button class="btn btn-link btn-sm text-primary editLeaveRequestBtn" data-id="${req.id}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-link btn-sm text-danger deleteLeaveRequestBtn" data-id="${req.id}">
                            <i class="bi bi-trash"></i>
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
                    <td>${esc(type.created_at) || '-'}</td>
                    <td class="text-end pe-4">
                        <button class="btn btn-link btn-sm text-primary editLeaveTypeBtn" data-id="${type.id}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-link btn-sm text-danger deleteLeaveTypeBtn" data-id="${type.id}">
                            <i class="bi bi-trash"></i>
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
        html += `<li class="page-item ${p === page ? 'active' : ''}"><a href="#" class="page-link ${buttonClass}" data-page="${p}">${p}</a></li>`;
    }

    if (page < totalPages) {
        html += `<li class="page-item"><a href="#" class="page-link ${buttonClass}" data-page="${page + 1}">Next &raquo;</a></li>`;
    }

    $(selector).html(html);
}

function applySortIndicators(selector, sortBy, sortDir) {
    $(selector).each(function () {
        let label = $(this).text().replace(' ASC', '').replace(' DESC', '');
        if ($(this).data('sort') === sortBy) {
            label += ' ' + sortDir;
        }
        $(this).text(label);
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
