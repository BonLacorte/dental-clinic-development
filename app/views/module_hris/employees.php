<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_hris/partials/hris_nav.php'; ?>

<div class="container-fluid py-4">
    <div class="card shadow-sm">

        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Manage Employees</h5>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                <i class="bi bi-plus-lg"></i> Add Employee
            </button>
        </div>

        <div class="card-body border-bottom">
            <form id="employeeSearchForm" class="row g-2 align-items-end">
                <div class="col-md-6 col-lg-4">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" id="employeeSearchInput" placeholder="Search employees">
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-secondary" type="button" id="clearEmployeeSearchBtn">Clear</button>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="employeesTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4"><button class="btn btn-link p-0 employee-sort"
                                    data-sort="employee_no">Employee No</button></th>
                            <th><button class="btn btn-link p-0 employee-sort" data-sort="name">Name</button></th>
                            <th><button class="btn btn-link p-0 employee-sort" data-sort="branch_name">Branch</button>
                            </th>
                            <th><button class="btn btn-link p-0 employee-sort" data-sort="date_hired">Date
                                    Hired</button></th>
                            <th><button class="btn btn-link p-0 employee-sort"
                                    data-sort="employment_status">Status</button></th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white py-3">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-end mb-0" id="employeesPagination"></ul>
            </nav>
        </div>

    </div>
</div>

<div class="modal fade" id="addEmployeeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form id="addEmployeeForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Add Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Employee No</label>
                            <input type="text" class="form-control" name="employee_no" readonly
                                placeholder="Auto-generated on save">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="firstname">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control" name="middlename">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="lastname">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Position</label>
                            <select class="form-select" name="position_id" id="positionSelect"></select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Organization Unit</label>
                            <select class="form-select" name="org_unit_id" id="organizationUnitSelect"></select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Contact No</label>
                            <input type="text" class="form-control" name="contact_no">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Branch</label>
                            <select class="form-select" name="branch_id" id="branchSelect"></select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Employment Type</label>
                            <select class="form-select" name="employment_type_id" id="employmentTypeSelect"></select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Date Hired</label>
                            <input type="date" class="form-control" name="date_hired">
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
                    <button class="btn btn-primary" type="submit">Save</button>
                </div>

            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="editEmployeeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form id="editEmployeeForm">
                <input type="hidden" name="id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- same fields as add -->
                        <div class="col-md-4">
                            <label class="form-label">Employee No</label>
                            <input type="text" class="form-control" name="employee_no" readonly>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="firstname">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control" name="middlename">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="lastname">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Position</label>
                            <select class="form-select" name="position_id" id="editPositionSelect"></select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Organization Unit</label>
                            <select class="form-select" name="org_unit_id" id="editOrganizationUnitSelect"></select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Contact No</label>
                            <input type="text" class="form-control" name="contact_no">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Branch</label>
                            <select class="form-select" name="branch_id" id="editBranchSelect"></select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Employment Type</label>
                            <select class="form-select" name="employment_type_id"
                                id="editEmploymentTypeSelect"></select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Employment Status</label>
                            <select class="form-select" name="employment_status_id"
                                id="editEmploymentStatusSelect"></select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Date Hired</label>
                            <input type="date" class="form-control" name="date_hired">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Date Resigned</label>
                            <input type="date" class="form-control" name="date_resigned">
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
                    <button class="btn btn-primary" type="submit">Update</button>
                </div>

            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="viewEmployeeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Employee Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label text-muted">Employee No</label>
                        <div id="view_employeeNo" class="fw-semibold">-</div>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label text-muted">Full Name</label>
                        <div id="view_fullName" class="fw-semibold">-</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted">Branch</label>
                        <div id="view_branch" class="fw-semibold">-</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted">Position</label>
                        <div id="view_position" class="fw-semibold">-</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted">Organization Unit</label>
                        <div id="view_orgUnit" class="fw-semibold">-</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted">Email</label>
                        <div id="view_email" class="fw-semibold">-</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted">Contact No</label>
                        <div id="view_contactNo" class="fw-semibold">-</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted">Date Hired</label>
                        <div id="view_dateHired" class="fw-semibold">-</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted">Employment Type</label>
                        <div id="view_employmentType" class="fw-semibold">-</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted">Employment Status</label>
                        <div id="view_employmentStatus" class="fw-semibold">-</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted">Status</label>
                        <div id="view_status" class="fw-semibold">-</div>
                    </div>

                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Delete confirmation modal (replaces window.confirm) -->
<div class="modal fade" id="deleteEmployeeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Delete Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                Are you sure you want to delete this employee? This action cannot be undone.
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                <button class="btn btn-danger" type="button" id="confirmDeleteEmployeeBtn">Delete</button>
            </div>

        </div>
    </div>
</div>

<!-- compensation modal -->
<div class="modal fade" id="compensationModal" tabindex="-1" aria-labelledby="compensationModalLabel"  aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="compensationForm">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="compensationModalLabel">
                        Employee Compensation
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="id" id="compensation_id">
                    <input type="hidden" name="employee_id" id="compensation_employee_id">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">
                                Salary Type
                            </label>

                            <select class="form-select" name="salary_type" id="salary_type" required>
                                <option value="">Select</option>
                                <option value="MONTHLY">Monthly</option>
                                <option value="DAILY">Daily</option>
                                <option value="HOURLY">Hourly</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Payroll Frequency
                            </label>

                            <select class="form-select" name="payroll_frequency" id="payroll_frequency" required>

                                <option value="">Select</option>
                                <option value="DAILY">Daily</option>
                                <option value="WEEKLY">Weekly</option>
                                <option value="BI_WEEKLY">Bi-Weekly</option>
                                <option value="SEMI_MONTHLY">Semi-Monthly</option>
                                <option value="MONTHLY">Monthly</option>

                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Salary Rate
                            </label>

                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" step="0.01" min="0" class="form-control text-end" id="salary_rate"
                                    name="salary_rate" required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">
                                Effective From
                            </label>

                            <input type="date" class="form-control" id="effective_from" name="effective_from" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">
                                Effective To
                            </label>

                            <input type="date" class="form-control" id="effective_to" name="effective_to">

                            <small class="text-muted">
                                Leave blank if current.
                            </small>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary">

                        <span class="spinner-border spinner-border-sm d-none" id="compensationSpinner"></span>

                        Save Compensation

                    </button>

                </div>

            </div>
        </form>
    </div>
</div>

<?php include '../app/views/partials/admin-footer.php'; ?>

<script>
    let currentPage = 1;
    let currentSearch = '';
    let currentSortBy = 'created_at';
    let currentSortDir = 'DESC';
    let pendingDeleteId = null;


    function esc(str) {
        return String(str ?? '').replace(/[&<>"']/g, c => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        }[c]));
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

    $(document).ready(function () {
        getAllEmployees(currentPage);
        loadBranches();
        loadOrganizationUnits();
        loadPositions();
        loadEmploymentTypes();
        loadEmploymentStatuses();

        $('#addEmployeeForm').on('submit', function (e) {
            e.preventDefault();
            addEmployee(this);
        });

        $('#editEmployeeForm').on('submit', function (e) {
            e.preventDefault();
            updateEmployee(this);
        });

        $(document).on('click', '.viewEmployeeBtn', function () {
            let id = $(this).data('id'); updateEmployee
            viewEmployee(id);
        });

        $(document).on('click', '.editEmployeeBtn', function () {
            let id = $(this).data('id');
            getEmployee(id);
        });

        $(document).on('click', '.deleteEmployeeBtn', function () {
            pendingDeleteId = $(this).data('id');
            bootstrap.Modal.getOrCreateInstance(
                document.getElementById('deleteEmployeeModal')
            ).show();
        });

        $('#confirmDeleteEmployeeBtn').on('click', function () {
            if (pendingDeleteId) {
                deleteEmployee(pendingDeleteId);
            }
        });

        $(document).on('click', '.page-btn', function (e) {
            e.preventDefault();
            let page = $(this).data('page');
            getAllEmployees(page);
        });

        $('#employeeSearchForm').on('submit', function (e) {
            e.preventDefault();
            currentSearch = $('#employeeSearchInput').val();
            getAllEmployees(1);
        });

        $('#clearEmployeeSearchBtn').on('click', function () {
            $('#employeeSearchInput').val('');
            currentSearch = '';
            getAllEmployees(1);
        });

        $(document).on('click', '.employee-sort', function () {
            let sortBy = $(this).data('sort');
            currentSortDir = currentSortBy === sortBy && currentSortDir === 'ASC' ? 'DESC' : 'ASC';
            currentSortBy = sortBy;
            getAllEmployees(1);
        });

        $(document).on('click', '.editCompensationBtn', function () {
            let id = $(this).data('id');
            getEmployeeCompensation(id);
        });

        $('#compensationForm').on('submit', function (e) {
            e.preventDefault();
            const id = $('#compensation_id').val();

            if (!id) {
                createEmployeeCompensation(this);
            } else {
                updateEmployeeCompensation(this);
            }

        });

    });

    /* getall employees */
    function getAllEmployees(page) {

        showLoader("Loading employees...");

        $.ajax({
            url: '/hris/employees/getallemployees',
            type: 'GET',
            dataType: 'json',
            data: {
                page: page,
                search: currentSearch,
                sort_by: currentSortBy,
                sort_dir: currentSortDir
            },

            success: function (res) {

                hideLoader();

                if (res.error) {
                    showToast(res.error, 'error');
                    return;
                }

                currentPage = res.currentPage;
                let totalPages = res.totalPages;

                let rows = '';
                let employees = res.data || [];

                if (employees.length === 0) {

                    let emptyMessage = currentSearch
                        ? `No employees match "${esc(currentSearch)}"`
                        : 'No employees found';

                    rows = `
                    <tr>
                        <td colspan="6" class="text-center py-3">
                            ${emptyMessage}
                        </td>
                    </tr>
                `;

                } else {

                    employees.forEach(emp => {

                        let fullName = `${emp.firstname ?? ''} ${emp.middlename ?? ''} ${emp.lastname ?? ''}`
                            .replace(/\s+/g, ' ')
                            .trim();

                        let statusClass = emp.employment_status
                            ? 'text-bg-info'
                            : 'text-bg-secondary';

                        rows += `
                        <tr>
                            <td class="ps-4">${esc(emp.employee_no) || '-'}</td>
                            <td>${esc(fullName) || '-'}</td>
                            <td>${esc(emp.branch_name) || '-'}</td>
                            <td>${esc(emp.date_hired) || '-'}</td>
                            <td>
                                <span class="badge ${statusClass}">
                                    ${esc(emp.employment_status) || '-'}
                                </span>
                            </td>

                            <td class="text-end pe-4">

                                <button class="btn btn-link btn-sm text-info viewEmployeeBtn"
                                    data-id="${emp.id}">
                                    <i class="bi bi-eye"></i>
                                </button>

                                <button class="btn btn-link btn-sm text-primary editEmployeeBtn"
                                    data-id="${emp.id}">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <button class="btn btn-link btn-sm btn-primary editCompensationBtn"
                                        data-id="${emp.id}"
                                        id="editCompensationBtn">
                                       <i class="bi bi-cash-stack"></i>
                                </button>

                                <button class="btn btn-link btn-sm text-danger deleteEmployeeBtn"
                                    data-id="${emp.id}">
                                    <i class="bi bi-trash"></i>
                                </button>

                            </td>
                        </tr>
                    `;
                    });
                }

                $('#employeesTable tbody').html(rows);
                renderPagination(currentPage, totalPages);
                applySortIndicators('.employee-sort', currentSortBy, currentSortDir);
            },

            error: function (xhr) {
                hideLoader();
                console.log(xhr.responseText);
                showToast('Unable to load employees.', 'error');
            }
        });
    }

    /* pagination */
    function renderPagination(page, totalPages) {

        let adjacents = 2;
        let start = Math.max(1, page - adjacents);
        let end = Math.min(totalPages, page + adjacents);

        let html = '';

        if (page > 1) {
            html += `
            <li class="page-item">
                <a href="#" class="page-link page-btn" data-page="${page - 1}">&laquo; Prev</a>
            </li>
        `;
        }

        if (start > 1) {
            html += `
            <li class="page-item">
                <a href="#" class="page-link page-btn" data-page="1">1</a>
            </li>
        `;

            if (start > 2) {
                html += `
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            `;
            }
        }

        for (let p = start; p <= end; p++) {
            html += `
            <li class="page-item ${p === page ? 'active' : ''}">
                <a href="#" class="page-link page-btn" data-page="${p}">${p}</a>
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

            html += `
            <li class="page-item">
                <a href="#" class="page-link page-btn" data-page="${totalPages}">
                    ${totalPages}
                </a>
            </li>
        `;
        }

        if (page < totalPages) {
            html += `
            <li class="page-item">
                <a href="#" class="page-link page-btn" data-page="${page + 1}">Next &raquo;</a>
            </li>
        `;
        }

        $('#employeesPagination').html(html);
    }

    /*LOAD BRANCHES (use tables) */
    function loadBranches() {

        $.ajax({
            url: '/hris/branches/getallbranches',
            type: 'GET',
            dataType: 'json',

            success: function (res) {

                let options = `<option value="">Select Branch</option>`;

                (res.data || []).forEach(branch => {
                    options += `<option value="${branch.id}">${esc(branch.name)}</option>`;
                });

                $('#branchSelect, #editBranchSelect').html(options);
            },

            error: function (xhr) {
                console.log(xhr.responseText);
                showToast('Failed to load branches.', 'error');
            }
        });
    }

    /* LOAD EMPLOYMENT TYPES */
    function loadEmploymentTypes() {

        $.ajax({
            url: '/hris/employees/employment-types',
            type: 'GET',
            dataType: 'json',

            success: function (res) {

                let options = `<option value="">Select Employment Type</option>`;

                (res.data || []).forEach(type => {
                    options += `<option value="${type.id}">${esc(type.name)}</option>`;
                });

                $('#employmentTypeSelect, #editEmploymentTypeSelect').html(options);
            },

            error: function (xhr) {
                console.log(xhr.responseText);
                showToast('Failed to load employment types.', 'error');
            }
        });
    }

    /* LOAD EMPLOYMENT STATUSES */
    function loadEmploymentStatuses() {

        $.ajax({
            url: '/hris/employees/employment-statuses',
            type: 'GET',
            dataType: 'json',

            success: function (res) {

                let options = `<option value="">Select Employment Status</option>`;
                (res.data || []).forEach(status => {
                    options += `<option value="${status.id}">${esc(status.name)}</option>`;
                });
                $('#editEmploymentStatusSelect').html(options);
            },

            error: function (xhr) {
                console.log(xhr.responseText);
                showToast('Failed to load employment statuses.', 'error');
            }
        });
    }

    function loadPositions() {

        $.ajax({
            url: '/hris/positions/getallpositions',
            type: 'GET',
            dataType: 'json',

            success: function (res) {

                let options = `<option value="">Select Position</option>`;
                (res.data || []).forEach(position => {
                    options += `<option value="${position.id}">${esc(position.name)}</option>`;
                });
                $('#positionSelect, #editPositionSelect').html(options);
            },

            error: function (xhr) {
                console.log(xhr.responseText);
                showToast('Failed to load positions.', 'error');
            }
        });
    }

    function loadOrganizationUnits() {

        $.ajax({
            url: '/hris/offices/getallunits',
            type: 'GET',
            dataType: 'json',

            success: function (res) {

                let options = `<option value="">Select Organization Unit</option>`;
                (res.data || []).forEach(unit => {
                    options += `<option value="${unit.id}">${esc(unit.name)}</option>`;
                });
                $('#organizationUnitSelect, #editOrganizationUnitSelect').html(options);
            },

            error: function (xhr) {
                console.log(xhr.responseText);
                showToast('Failed to load organization units.', 'error');
            }
        });
    }

    /* ADD EMPLOYEE */
    function addEmployee(form) {

        let $form = $(form);

        let formData = {
            employee_no: $form.find('[name="employee_no"]').val(),
            firstname: $form.find('[name="firstname"]').val(),
            middlename: $form.find('[name="middlename"]').val(),
            lastname: $form.find('[name="lastname"]').val(),
            email: $form.find('[name="email"]').val(),
            contact_no: $form.find('[name="contact_no"]').val(),
            branch_id: $form.find('[name="branch_id"]').val(),
            position_id: $form.find('[name="position_id"]').val(),
            org_unit_id: $form.find('[name="org_unit_id"]').val(),
            employment_type_id: $form.find('[name="employment_type_id"]').val(),
            date_hired: $form.find('[name="date_hired"]').val(),
            csrf_token: $form.find('[name="csrf_token"]').val()
        };

        $.ajax({
            url: '/hris/employees/create',
            type: 'POST',
            data: formData,
            dataType: 'json',

            beforeSend: function () {
                showLoader("Saving employee...");
            },

            success: function (res) {
                hideLoader();

                if (res.error || res.success === false) {
                    showToast(res.message || res.error, 'error');
                    return;
                }

                showToast(res.message || 'Employee created successfully', 'success');
                $('#addEmployeeForm')[0].reset();
                bootstrap.Modal.getInstance(document.getElementById('addEmployeeModal')).hide();
                getAllEmployees(1);
            },

            error: function (xhr) {
                hideLoader();
                console.log(xhr.responseText);
                showToast('Something went wrong.', 'error');
            }
        });
    }

    /* get employee id */
    function getEmployee(id) {

        showLoader("Loading employee...");

        $.ajax({
            url: '/hris/employees/getemployee/' + id,
            type: 'GET',
            dataType: 'json',

            success: function (res) {

                hideLoader();

                if (!res.success) {
                    showToast(res.message, 'error');
                    return;
                }

                let emp = res.data;
                let $form = $('#editEmployeeForm');

                $form.find('[name="id"]').val(emp.id);
                $form.find('[name="employee_no"]').val(emp.employee_no);
                $form.find('[name="firstname"]').val(emp.firstname);
                $form.find('[name="middlename"]').val(emp.middlename);
                $form.find('[name="lastname"]').val(emp.lastname);
                $form.find('[name="email"]').val(emp.email);
                $form.find('[name="contact_no"]').val(emp.contact_no);
                $form.find('[name="branch_id"]').val(emp.branch_id);
                $form.find('[name="position_id"]').val(emp.position_id);
                $form.find('[name="org_unit_id"]').val(emp.org_unit_id);
                $form.find('[name="employment_type_id"]').val(emp.employment_type_id);
                $form.find('[name="employment_status_id"]').val(emp.employment_status_id);
                $form.find('[name="date_hired"]').val(emp.date_hired);
                $form.find('[name="date_resigned"]').val(emp.date_resigned);

                bootstrap.Modal.getOrCreateInstance(
                    document.getElementById('editEmployeeModal')
                ).show();
            },

            error: function (xhr) {
                hideLoader();
                console.log(xhr.responseText);
                showToast('Unable to load employee.', 'error');
            }
        });
    }

    function viewEmployee(id) {

        showLoader("Loading employee...");

        $.ajax({
            url: '/hris/employees/getemployee/' + id,
            type: 'GET',
            dataType: 'json',

            success: function (res) {

                hideLoader();

                if (!res.success) {
                    showToast(res.message, 'error');
                    return;
                }

                let emp = res.data;
                let fullName = `${emp.firstname ?? ''} ${emp.middlename ?? ''} ${emp.lastname ?? ''}`
                    .replace(/\s+/g, ' ')
                    .trim();

                $('#view_employeeNo').text(emp.employee_no ?? '-');
                $('#view_fullName').text(fullName || '-');
                $('#view_branch').text(emp.branch_name ?? '-');
                $('#view_position').text(emp.position ?? '-');
                $('#view_orgUnit').text(emp.org_unit ?? '-');
                $('#view_email').text(emp.email ?? '-');
                $('#view_contactNo').text(emp.contact_no ?? '-');
                $('#view_dateHired').text(emp.date_hired ?? '-');
                $('#view_employmentType').text(emp.employment_type ?? '-');
                $('#view_employmentStatus').text(emp.employment_status ?? '-');
                $('#view_status').text(emp.status ?? '-');
                $('#view_sssNo').text(emp.sss_no ?? '-');
                $('#view_philhealthNo').text(emp.philhealth_no ?? '-');
                $('#view_pagibigNo').text(emp.pagibig_no ?? '-');
                $('#view_schedule').text(emp.schedule ?? '-');

                bootstrap.Modal.getOrCreateInstance(
                    document.getElementById('viewEmployeeModal')
                ).show();
            },

            error: function (xhr) {
                hideLoader();
                console.log(xhr.responseText);
                showToast('Unable to load employee.', 'error');
            }
        });
    }

    /* update employee */
    function updateEmployee(form) {

        let $form = $(form);
        let id = $form.find('[name="id"]').val();

        let formData = {
            employee_no: $form.find('[name="employee_no"]').val(),
            firstname: $form.find('[name="firstname"]').val(),
            middlename: $form.find('[name="middlename"]').val(),
            lastname: $form.find('[name="lastname"]').val(),
            email: $form.find('[name="email"]').val(),
            contact_no: $form.find('[name="contact_no"]').val(),
            branch_id: $form.find('[name="branch_id"]').val(),
            position_id: $form.find('[name="position_id"]').val(),
            org_unit_id: $form.find('[name="org_unit_id"]').val(),
            employment_type_id: $form.find('[name="employment_type_id"]').val(),
            employment_status_id: $form.find('[name="employment_status_id"]').val(),
            date_hired: $form.find('[name="date_hired"]').val(),
            date_resigned: $form.find('[name="date_resigned"]').val(),
            csrf_token: $form.find('[name="csrf_token"]').val()
        };

        $.ajax({
            url: '/hris/employees/updateemployee/' + id,
            type: 'POST',
            data: formData,
            dataType: 'json',

            beforeSend: function () {
                showLoader("Updating employee...");
            },

            success: function (res) {

                hideLoader();

                if (res.error || res.success === false) {
                    showToast(res.message || res.error, 'error');
                    return;
                }

                showToast(res.message || 'Employee updated successfully', 'success');
                bootstrap.Modal.getInstance(document.getElementById('editEmployeeModal')).hide();
                getAllEmployees(currentPage);
            },

            error: function (xhr) {
                hideLoader();
                console.log(xhr.responseText);
                showToast('Something went wrong.', 'error');
            }
        });
    }

    /* delete employee */
    function deleteEmployee(id) {

        let csrfToken = $('#addEmployeeForm [name="csrf_token"]').val();

        $.ajax({
            url: '/hris/employees/deleteemployee/' + id,
            type: 'POST',
            dataType: 'json',
            data: {
                csrf_token: csrfToken
            },

            beforeSend: function () {
                bootstrap.Modal.getInstance(document.getElementById('deleteEmployeeModal')).hide();
                showLoader("Deleting employee...");
            },

            success: function (res) {

                hideLoader();
                pendingDeleteId = null;

                if (res.error || res.success === false) {
                    showToast(res.message || res.error, 'error');
                    return;
                }

                showToast(res.message || 'Employee deleted successfully', 'success');
                getAllEmployees(currentPage);
            },

            error: function (xhr) {
                hideLoader();
                pendingDeleteId = null;
                console.log(xhr.responseText);
                showToast('Something went wrong.', 'error');
            }
        });
    }


    function getEmployeeCompensation(employeeId) {
        showLoader('Loading employee compensation...');

        $.ajax({
            url: '/hris/employees/getcompensation/' + employeeId,
            type: 'GET',
            dataType: 'json',

            success: function (res) {
                hideLoader();

                $('#compensationForm')[0].reset();
                $('#compensation_id').val('');
                $('#compensation_employee_id').val(employeeId);
                $('#effective_to').val('');

                if (res.success && res.data) {
                    const row = res.data;

                    $('#compensation_id').val(row.id);
                    $('#compensation_employee_id').val(row.employee_id);
                    $('#salary_type').val(row.salary_type);
                    $('#payroll_frequency').val(row.payroll_frequency);
                    $('#salary_rate').val(row.salary_rate);
                    $('#effective_from').val(row.effective_from);
                    $('#effective_to').val(row.effective_to || '');

                    $('#compensationModalLabel').text(
                        'Edit Employee Compensation'
                    );
                } else {
                    $('#compensationModalLabel').text(
                        'Add Employee Compensation'
                    );
                }

                bootstrap.Modal.getOrCreateInstance(
                    document.getElementById('compensationModal')
                ).show();
            },

            error: function (xhr) {
                hideLoader();

                console.error(xhr.responseText);

                showToast(
                    'Unable to load employee compensation.',
                    'error'
                );
            }
        });
    }

    function createEmployeeCompensation(form) {
        saveEmployeeCompensation(
            form,
            '/hris/employees/createcompensation',
            'POST'
        );
    }

    function updateEmployeeCompensation(form) {
        const id = $('#compensation_id').val();

        if (!id) {
            showToast('Compensation ID is missing.', 'error');
            return;
        }

        saveEmployeeCompensation(
            form,
            '/hris/employees/updatecompensation/' + id,
            'POST'
        );
    }

    function saveEmployeeCompensation(form, url, method) {
        const $form = $(form);
        const $submitButton = $form.find('button[type="submit"]');
        const $spinner = $('#compensationSpinner');

        const effectiveFrom = $('#effective_from').val();
        const effectiveTo = $('#effective_to').val();
        const salaryRate = parseFloat($('#salary_rate').val());

        if (
            effectiveFrom &&
            effectiveTo &&
            effectiveTo < effectiveFrom
        ) {
            showToast(
                'Effective To cannot be earlier than Effective From.',
                'error'
            );

            $('#effective_to').focus();
            return;
        }

        if (isNaN(salaryRate) || salaryRate <= 0) {
            showToast(
                'Please enter a valid salary rate.',
                'error'
            );

            $('#salary_rate').focus();
            return;
        }

        $submitButton.prop('disabled', true);
        $spinner.removeClass('d-none');

        $.ajax({
            url: url,
            type: method,
            data: $form.serialize(),
            dataType: 'json',

            success: function (res) {
                if (!res.success) {
                    showToast(
                        res.message || 'Unable to save employee compensation.',
                        'error'
                    );
                    return;
                }

                showToast(
                    res.message || 'Employee compensation saved successfully.',
                    'success'
                );

                bootstrap.Modal.getInstance(
                    document.getElementById('compensationModal')
                )?.hide();

         
        
                $form[0].reset();
                $('#compensation_id').val('');
            },

            error: function (xhr) {
                console.error(xhr.responseText);

                let message = 'Unable to save employee compensation.';

                if (
                    xhr.responseJSON &&
                    xhr.responseJSON.message
                ) {
                    message = xhr.responseJSON.message;
                }

                showToast(message, 'error');
            },

            complete: function () {
                $submitButton.prop('disabled', false);
                $spinner.addClass('d-none');
            }
        });
    }
</script>

</body>

</html>