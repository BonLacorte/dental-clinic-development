<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_hris/partials/hris_nav.php'; ?>
<?php
// Display only: mirror PermissionMiddleware's session map and Super Admin bypass.
// Compensation keeps its legacy HRIS visibility; its policy review is C-EMP-03.
$employeeActions = [
    'view' => $navCanPermission('hris.employee.view'),
    'edit' => $navCanPermission('hris.employee.edit'),
    'delete' => $navCanPermission('hris.employee.delete'),
];
?>
<main class="employee-page container-fluid py-4 management-page hris-page" aria-labelledby="employeesHeading">
    <div class="card shadow-sm management-card">
        <div class="card-header bg-white employee-header">
            <div>
                <h1 class="h4 mb-1" id="employeesHeading" tabindex="-1">Employees</h1>
                <p class="mb-0 text-body-secondary">Employee records and employment details</p>
            </div>
            <div class="employee-header-actions">
                <?php if (!empty($can_export)): ?>
                <button type="button" class="btn btn-outline-primary" id="exportEmployeesBtn" data-csrf="<?= e($csrf_token) ?>" aria-describedby="employeeExportStatus" title="Export assigned branches using the applied search and order" disabled>
                    <i class="bi bi-file-earmark-excel" aria-hidden="true"></i> Export Excel
                </button>
                <?php endif; ?>
                <?php if ($navCanPermission('hris.employee.create')): ?>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i> Add Employee
                </button>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body border-bottom">
            <form id="employeeSearchForm" class="employee-toolbar" role="search" aria-label="Employees">
                <div class="employee-search-field">
                    <label class="form-label" for="employeeSearchInput">Search employees</label>
                    <input type="search" class="form-control" id="employeeSearchInput" placeholder="Name, employee no or branch" aria-describedby="employeeSearchHelp">
                </div>
                <div class="employee-search-actions">
                    <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search" aria-hidden="true"></i> Search</button>
                    <button class="btn btn-outline-secondary" type="button" id="clearEmployeeSearchBtn">Clear</button>
                </div>
            </form>
            <p class="employee-search-help mb-0" id="employeeSearchHelp">Search by name, employee number or branch. Select Search to apply.</p>
        </div>
        <div class="employee-list-feedback">
        <p id="employeeListStatus" role="status" class="employee-result-context mb-0">Loading employees…</p>
        <p id="employeeSortSummary" class="employee-order-summary mb-0">Sorted by newest added.</p>
        <p id="employeeExportStatus" role="status" class="employee-export-status mb-0"></p>
        <p id="employeeScrollHelp" class="visually-hidden">Scroll the table horizontally to reach every column and action. Keyboard: focus the table region and use the arrow keys.</p>
            <div id="employeeListError" class="alert alert-danger m-3" hidden>
                <p role="alert" class="mb-2"></p>
                <button type="button" class="btn btn-outline-danger" id="retryEmployeeList">Retry employee list</button>
            </div>
        </div>
        <form id="employeeMobileSortForm" class="employee-mobile-sort" aria-label="Sort employees">
            <div>
                <label class="form-label" for="employeeMobileSortBy">Sort employees by</label>
                <select class="form-select" id="employeeMobileSortBy">
                    <option value="created_at">Date added (default)</option>
                    <option value="employee_no">Employee No</option>
                    <option value="name">Name</option>
                    <option value="branch_name">Branch</option>
                    <option value="date_hired">Date Hired</option>
                    <option value="employment_status">Employment Status</option>
                </select>
            </div>
            <div>
                <label class="form-label" for="employeeMobileSortDir">Direction</label>
                <select class="form-select" id="employeeMobileSortDir">
                    <option value="ASC">Ascending</option>
                    <option value="DESC" selected>Descending</option>
                </select>
            </div>
            <button class="btn btn-outline-primary" type="submit">Apply order</button>
        </form>
        <div class="table-responsive employee-table-region" tabindex="0" role="region" aria-label="Employee records" aria-describedby="employeeScrollHelp" aria-busy="true">
            <table class="table table-hover align-middle mb-0 hris-data-table" id="employeesTable">
                <caption class="visually-hidden">Employee records. Sort using the column buttons; Actions apply to the named employee.</caption>
                <thead class="table-light"><tr>
                    <?php foreach (['employee_no' => 'Employee No', 'name' => 'Name', 'branch_name' => 'Branch', 'date_hired' => 'Date Hired', 'employment_status' => 'Status'] as $key => $label): ?>
                    <th scope="col"><button type="button" class="btn btn-link employee-sort" data-sort="<?= e($key) ?>" data-label="<?= e($label) ?>"><?= e($label) ?><span aria-hidden="true" class="employee-sort-mark"></span></button></th>
                    <?php endforeach; ?>
                    <th scope="col" class="text-end">Actions</th>
                </tr></thead>
                <tbody></tbody>
            </table>
        </div>
        <ul class="employee-cards" id="employeeCards" aria-label="Employee records" aria-busy="true"></ul>
        <div class="card-footer bg-white employee-pagination">
            <p id="employeePageSummary" class="mb-0">Waiting for employee records.</p>
            <nav aria-label="Employee pages"><ul class="pagination mb-0" id="employeesPagination"></ul></nav>
        </div>
    </div>

    <?php
    // Existing field names, select IDs and request contracts are retained.
    $employeeGroups = [
        'Identity' => [
            ['employee_no', 'Employee No', 'text', false],
            ['firstname', 'First Name', 'text', true],
            ['middlename', 'Middle Name', 'text', false],
            ['lastname', 'Last Name', 'text', true],
        ],
        'Contact' => [
            ['email', 'Email', 'email', false],
            ['contact_no', 'Contact No', 'text', false],
        ],
        'Employment' => [
            ['branch_id', 'Branch', 'select', true, 'branchSelect', 'editBranchSelect'],
            ['position_id', 'Position', 'select', false, 'positionSelect', 'editPositionSelect'],
            ['org_unit_id', 'Organization Unit', 'select', false, 'organizationUnitSelect', 'editOrganizationUnitSelect'],
            ['employment_type_id', 'Employment Type', 'select', true, 'employmentTypeSelect', 'editEmploymentTypeSelect'],
            ['date_hired', 'Date Hired', 'date', true],
        ],
    ];
    foreach (['add' => 'Add', 'edit' => 'Edit'] as $mode => $verb):
        $groups = $employeeGroups;
        if ($mode === 'edit') {
            $groups['Employment'][] = ['employment_status_id', 'Employment Status', 'select', false, '', 'editEmploymentStatusSelect'];
            $groups['Employment'][] = ['date_resigned', 'Date Resigned', 'date', false];
        }
    ?>
    <div class="modal fade employee-dialog" id="<?= $mode ?>EmployeeModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="<?= $mode ?>EmployeeTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <form id="<?= $mode ?>EmployeeForm" class="employee-form">
                    <?php if ($mode === 'edit'): ?><input type="hidden" name="id"><?php endif; ?>
                    <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
                    <div class="modal-header">
                        <h2 class="modal-title fs-5" id="<?= $mode ?>EmployeeTitle" tabindex="-1"><?= $verb ?> Employee</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close <?= $verb ?> Employee"></button>
                    </div>
                    <div class="modal-body">
                        <div class="employee-read-feedback" hidden><p role="status" class="mb-2"></p><button type="button" class="btn btn-outline-danger employee-retry" hidden>Retry employee details and choices</button></div>
                        <div class="employee-mutation-feedback alert alert-warning" hidden><p role="alert" class="mb-0"></p></div>
                        <div class="employee-discard alert alert-warning" role="group" aria-labelledby="<?= $mode ?>DiscardTitle" hidden>
                            <h3 class="h6" id="<?= $mode ?>DiscardTitle">Discard your changes?</h3>
                            <p>Your unsaved entries will be lost.</p>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-primary employee-stay">Stay editing</button>
                                <button type="button" class="btn btn-outline-danger employee-discard-confirm">Discard changes</button>
                            </div>
                        </div>
                        <p class="text-body-secondary">Fields marked <span aria-hidden="true">*</span><span class="visually-hidden">with an asterisk</span> are required. Other fields are optional.</p>
                        <fieldset class="employee-fields">
                            <legend class="visually-hidden"><?= $verb ?> employee fields</legend>
                            <?php foreach ($groups as $group => $fields): ?>
                            <fieldset class="employee-field-group">
                                <legend class="fs-6 fw-semibold"><?= e($group) ?></legend>
                                <div class="row g-3">
                                    <?php foreach ($fields as $field):
                                        [$name, $label, $type, $required] = $field;
                                        $id = $type === 'select' ? $field[$mode === 'add' ? 4 : 5] : $mode . '_' . $name;
                                    ?>
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <label class="form-label" for="<?= e($id) ?>"><?= e($label) ?><?php if ($required): ?> <span aria-hidden="true">*</span><?php endif; ?></label>
                                        <?php if ($type === 'select'): ?>
                                        <select class="form-select" name="<?= e($name) ?>" id="<?= e($id) ?>"<?= $required ? ' required' : '' ?>><option value="">Select <?= e($label) ?></option></select>
                                        <?php else: ?>
                                        <input type="<?= e($type) ?>" class="form-control" name="<?= e($name) ?>" id="<?= e($id) ?>"<?= $required ? ' required' : '' ?><?= $name === 'employee_no' ? ' readonly' : '' ?><?= $name === 'employee_no' && $mode === 'add' ? ' placeholder="Auto-generated on save"' : '' ?>>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </fieldset>
                            <?php endforeach; ?>
                        </fieldset>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
                        <button class="btn btn-primary" type="submit"><?= $mode === 'add' ? 'Save' : 'Update' ?> employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <div class="modal fade employee-dialog" id="viewEmployeeModal" tabindex="-1" aria-labelledby="viewEmployeeTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header"><h2 class="modal-title fs-5" id="viewEmployeeTitle" tabindex="-1">Employee Details</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close Employee Details"></button></div>
                <div class="modal-body">
                    <div class="employee-read-feedback" hidden><p role="status" class="mb-2"></p><button type="button" class="btn btn-outline-danger employee-retry" hidden>Retry employee details</button></div>
                    <dl class="row g-3 employee-details mb-0">
                        <?php foreach (['employeeNo' => 'Employee No', 'fullName' => 'Full Name', 'branch' => 'Branch', 'position' => 'Position', 'orgUnit' => 'Organization Unit', 'email' => 'Email', 'contactNo' => 'Contact No', 'dateHired' => 'Date Hired', 'employmentType' => 'Employment Type', 'employmentStatus' => 'Employment Status', 'status' => 'Status'] as $key => $label): ?>
                        <div class="col-12 col-md-6"><dt class="text-body-secondary fw-normal"><?= e($label) ?></dt><dd class="fw-semibold mb-0" id="view_<?= e($key) ?>">—</dd></div>
                        <?php endforeach; ?>
                    </dl>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
            </div>
        </div>
    </div>

    <div class="modal fade employee-dialog" id="deleteEmployeeModal" tabindex="-1" aria-labelledby="deleteEmployeeTitle" aria-describedby="deleteEmployeeDescription" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header"><h2 class="modal-title fs-5" id="deleteEmployeeTitle">Delete Employee</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close Delete Employee"></button></div>
                <div class="modal-body">
                    <p id="deleteEmployeeDescription">Delete <strong id="deleteEmployeeName"></strong> from the employee list?</p>
                    <div class="employee-mutation-feedback alert alert-warning" hidden><p role="alert" class="mb-0"></p></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary employee-cancel" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-danger" id="confirmDeleteEmployeeBtn">Delete employee</button></div>
            </div>
        </div>
    </div>

    <div class="modal fade employee-dialog" id="compensationModal" tabindex="-1" aria-labelledby="compensationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <form id="compensationForm" class="employee-form">
                    <div class="modal-header"><h2 class="modal-title fs-5" id="compensationModalLabel" tabindex="-1">Employee Compensation</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close Employee Compensation"></button></div>
                    <div class="modal-body">
                        <p id="compensationEmployeeName" class="fw-semibold"></p>
                        <div class="employee-read-feedback" hidden><p role="status" class="mb-2"></p><button type="button" class="btn btn-outline-danger employee-retry" hidden>Retry employee compensation</button></div>
                        <div class="employee-mutation-feedback alert alert-warning" hidden><p role="alert" class="mb-0"></p></div>
                        <input type="hidden" name="id" id="compensation_id">
                        <input type="hidden" name="employee_id" id="compensation_employee_id">
                        <fieldset class="employee-fields"><legend class="visually-hidden">Compensation fields</legend><div class="row g-3">
                            <div class="col-12 col-md-6"><label class="form-label" for="salary_type">Salary Type</label><select class="form-select" name="salary_type" id="salary_type" required><option value="">Select</option><option value="MONTHLY">Monthly</option><option value="DAILY">Daily</option><option value="HOURLY">Hourly</option></select></div>
                            <div class="col-12 col-md-6"><label class="form-label" for="payroll_frequency">Payroll Frequency</label><select class="form-select" name="payroll_frequency" id="payroll_frequency" required><option value="">Select</option><option value="DAILY">Daily</option><option value="WEEKLY">Weekly</option><option value="BI_WEEKLY">Bi-Weekly</option><option value="SEMI_MONTHLY">Semi-Monthly</option><option value="MONTHLY">Monthly</option></select></div>
                            <div class="col-12 col-md-6"><label class="form-label" for="salary_rate">Salary Rate</label><div class="input-group"><span class="input-group-text">₱</span><input type="number" step="0.01" min="0" class="form-control text-end" id="salary_rate" name="salary_rate" required></div></div>
                            <div class="col-12 col-md-6"><label class="form-label" for="effective_from">Effective From</label><input type="date" class="form-control" id="effective_from" name="effective_from" required></div>
                            <div class="col-12 col-md-6"><label class="form-label" for="effective_to">Effective To</label><input type="date" class="form-control" id="effective_to" name="effective_to"></div>
                        </div></fieldset>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary"><span id="compensationSpinner" class="spinner-border spinner-border-sm me-1 d-none" aria-hidden="true"></span>Save compensation</button></div>
                </form>
            </div>
        </div>
    </div>
</main>
<?php include '../app/views/partials/admin-footer.php'; ?>
<script>
    const employeeActions = <?= json_encode($employeeActions) ?>;
    let currentPage = 1;
    let currentSearch = '';
    let currentSortBy = 'created_at';
    let currentSortDir = 'DESC';
    let listRequest = null;
    let listSequence = 0;
    let requestedPage = 1;
    let loadedList = null;
    let employeeExportPending = false;
    let pendingDeleteId = null;
    const employeeDialogs = {};
    const lookupDefinitions = {
        branch_id: {url: '/hris/branches/getallbranches', label: 'Branch'},
        position_id: {url: '/hris/positions/getallpositions', label: 'Position'},
        org_unit_id: {url: '/hris/offices/getallunits', label: 'Organization Unit'},
        employment_type_id: {url: '/hris/employees/employment-types', label: 'Employment Type'},
        employment_status_id: {url: '/hris/employees/employment-statuses', label: 'Employment Status'}
    };

    function esc(value) {
        return String(value ?? '').replace(/[&<>"']/g, c => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c]));
    }

    function employeeName(employee) {
        return `${employee.firstname ?? ''} ${employee.middlename ?? ''} ${employee.lastname ?? ''}`.replace(/\s+/g, ' ').trim() || employee.employee_no || 'employee';
    }

    function listDescription(context) {
        const labels = {employee_no: 'Employee No', name: 'Name', branch_name: 'Branch', date_hired: 'Date Hired', employment_status: 'Status'};
        return context.sortBy === 'created_at' ? `Sorted by ${context.sortDir === 'ASC' ? 'oldest' : 'newest'} added.` : `Sorted by ${labels[context.sortBy]}, ${context.sortDir === 'ASC' ? 'ascending' : 'descending'}.`;
    }

    // Both representations use these same escaped values and permission decisions.
    function employeePresentation(emp) {
        const name = employeeName(emp);
        const action = (allowed, className, label, icon, color) => allowed ? `<button type="button" class="btn btn-outline-${color} hris-action ${className}" data-employee-action="${className}" data-id="${esc(emp.id)}" data-name="${esc(name)}" aria-label="${label}: ${esc(name)}" title="${label}: ${esc(name)}"><i class="bi bi-${icon}" aria-hidden="true"></i><span class="employee-action-label">${label}</span></button>` : '';
        // Legacy compensation visibility is unchanged; policy review stays separate.
        const actions = action(employeeActions.view, 'viewEmployeeBtn', 'View', 'eye', 'primary')
            + action(employeeActions.edit, 'editEmployeeBtn', 'Edit', 'pencil-square', 'primary')
            + action(true, 'editCompensationBtn', 'Compensation', 'cash-stack', 'primary')
            + action(employeeActions.delete, 'deleteEmployeeBtn', 'Delete', 'trash', 'danger');
        return {
            name: esc(name), number: esc(emp.employee_no) || '—', branch: esc(emp.branch_name) || '—',
            dateHired: esc(emp.date_hired) || '—', actions,
            status: `<span class="badge hris-status ${MonteroHris.statusClass(emp.employment_status, 'employment')} ${emp.employment_status ? 'text-bg-info' : 'text-bg-secondary'}" title="Employment status" aria-label="Employment status: ${esc(emp.employment_status) || '—'}">${esc(emp.employment_status) || '—'}</span>`
        };
    }

    function employeeTableRow(record) {
        return `<tr><td>${record.number}</td><td>${record.name}</td><td>${record.branch}</td><td>${record.dateHired}</td><td>${record.status}</td><td class="text-end"><div class="employee-row-actions">${record.actions}</div></td></tr>`;
    }

    function employeeCard(record) {
        return `<li><article class="employee-card" aria-label="Employee: ${record.name}"><header class="employee-card-header"><div><h2 class="h6 mb-1">${record.name}</h2><p class="employee-card-number mb-0"><span class="visually-hidden">Employee No: </span>${record.number}</p></div>${record.status}</header><dl class="employee-card-details"><div><dt>Branch</dt><dd>${record.branch}</dd></div><div><dt>Date Hired</dt><dd>${record.dateHired}</dd></div></dl><div class="employee-row-actions" role="group" aria-label="Actions for ${record.name}">${record.actions}</div></article></li>`;
    }

    function focusEmployeeAction(trigger, employeeId) {
        if (trigger && document.contains(trigger) && $(trigger).is(':visible')) {
            trigger.focus();
            return;
        }
        const replacement = employeeId && $('#employeesTable button, #employeeCards button').filter(':visible').toArray().find(button =>
            button.dataset.id === String(employeeId) && button.dataset.employeeAction === trigger?.dataset.employeeAction);
        (replacement || document.getElementById('employeesHeading')).focus();
    }

    async function exportEmployees() {
        if (employeeExportPending || !loadedList) return;
        // Only successfully loaded criteria apply; unsubmitted text and failed requests do not.
        const applied = {...loadedList};
        const button = document.getElementById('exportEmployeesBtn');
        if (!button) return;
        employeeExportPending = true;
        $(button).prop('disabled', true).attr('aria-busy', 'true');
        $('#employeeExportStatus').removeClass('text-danger').text(`Preparing Excel for your permitted branches${applied.search ? ` matching “${applied.search}”` : ''}…`);
        try {
            const response = await fetch('/hris/employees/export', {
                method: 'POST', credentials: 'same-origin',
                headers: {'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8', 'X-Requested-With': 'XMLHttpRequest'},
                body: new URLSearchParams({csrf_token: button.dataset.csrf, search: applied.search, sort_by: applied.sortBy, sort_dir: applied.sortDir})
            });
            const type = response.headers.get('Content-Type') || '';
            if (!response.ok || !type.includes('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')) {
                const error = type.includes('application/json') ? await response.json().catch(() => null) : null;
                throw new Error(typeof error?.message === 'string' ? error.message : 'Export is unavailable. Check your session and try again.');
            }
            const disposition = response.headers.get('Content-Disposition') || '';
            const filename = disposition.match(/filename="(Montero_Dental_Employees_\d{4}-\d{2}-\d{2}\.xlsx)"/);
            const blob = await response.blob();
            const signature = new Uint8Array(await blob.slice(0, 4).arrayBuffer());
            if (!filename || signature.join(',') !== '80,75,3,4') {
                throw new Error('The export response was not a valid Excel download. Please try again.');
            }
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            try {
                link.href = url;
                link.download = filename[1];
                link.hidden = true;
                document.body.appendChild(link);
                link.click();
            } finally {
                link.remove();
                // Allow the browser to begin the download before releasing the blob.
                window.setTimeout(() => URL.revokeObjectURL(url), 60000);
            }
            $('#employeeExportStatus').text('Excel download started. The file includes permitted branches for the applied search and order.');
        } catch (error) {
            $('#employeeExportStatus').addClass('text-danger').text(error instanceof Error ? error.message : 'Unable to prepare the export. Please try again.');
        } finally {
            employeeExportPending = false;
            $(button).prop('disabled', !loadedList).attr('aria-busy', 'false');
        }
    }

    function getAllEmployees(page) {
        const sequence = ++listSequence;
        if (listRequest) listRequest.abort();
        requestedPage = page;
        const context = {page, search: currentSearch, sortBy: currentSortBy, sortDir: currentSortDir};
        $('.employee-table-region, #employeeCards').attr('aria-busy', 'true');
        if ($('#employeeListError').has(document.activeElement).length) $('#employeesHeading').trigger('focus');
        $('#employeeListError').prop('hidden', true);
        $('#employeeListStatus').text(loadedList ? 'Refreshing employees. Previously loaded rows remain below.' : 'Loading employees…');
        listRequest = $.ajax({
            url: '/hris/employees/getallemployees', type: 'GET', dataType: 'json',
            data: {page, search: context.search, sort_by: context.sortBy, sort_dir: context.sortDir}
        }).done(function (res) {
            if (sequence !== listSequence) return;
            if (!res || res.success !== true || !Array.isArray(res.data) || !Number.isInteger(res.currentPage) || !Number.isInteger(res.totalPages)) {
                listFailed();
                return;
            }
            currentPage = res.currentPage;
            loadedList = {...context, page: currentPage, pages: res.totalPages, count: res.data.length};
            $('#exportEmployeesBtn').prop('disabled', employeeExportPending);
            const records = res.data.map(employeePresentation);
            const emptyText = context.search ? `No employees match “${esc(context.search)}”. Clear or change your search.` : 'No employees found.';
            $('#employeesTable tbody').html(records.map(employeeTableRow).join('') || `<tr><td colspan="6" class="text-center py-4">${emptyText}</td></tr>`);
            $('#employeeCards').html(records.map(employeeCard).join('') || `<li class="employee-card-empty">${emptyText}</li>`);
            const count = `${res.data.length} employee${res.data.length === 1 ? '' : 's'} loaded on this page`;
            $('#employeeListStatus').text(`${count}${context.search ? ` for “${context.search}”` : ''}.`);
            $('#employeePageSummary').text(`Page ${currentPage} of ${res.totalPages}`);
            $('#employeeSortSummary').text(listDescription(context));
            $('#employeeMobileSortBy').val(context.sortBy);
            $('#employeeMobileSortDir').val(context.sortDir);
            $('.employee-sort').each(function () {
                const active = $(this).data('sort') === context.sortBy;
                $(this).closest('th').removeAttr('aria-sort');
                if (active) $(this).closest('th').attr('aria-sort', context.sortDir === 'ASC' ? 'ascending' : 'descending');
                $(this).find('.employee-sort-mark').text(active ? (context.sortDir === 'ASC' ? ' ↑' : ' ↓') : '');
                $(this).attr('aria-label', `${$(this).data('label')}: sort ${active && context.sortDir === 'ASC' ? 'descending' : 'ascending'}`);
            });
            renderPagination(currentPage, res.totalPages);
        }).fail(function (xhr, status) {
            if (sequence === listSequence && status !== 'abort') listFailed();
        }).always(function () {
            if (sequence === listSequence) {
                listRequest = null;
                $('.employee-table-region, #employeeCards').attr('aria-busy', 'false');
            }
        });
    }

    function listFailed() {
        $('#employeeListStatus').text(loadedList
            ? `Previously loaded page ${loadedList.page}: ${loadedList.count} employees${loadedList.search ? ` for “${loadedList.search}”` : ''}. ${listDescription(loadedList)}`
            : 'Employee records have not loaded.');
        $('#employeeListError').prop('hidden', false).find('[role="alert"]').text('Unable to load the requested employee list. Retry when ready.');
    }

    function renderPagination(page, totalPages) {
        const restoreFocus = $('#employeesPagination').has(document.activeElement).length > 0;
        const item = (p, label, name) => `<li class="page-item ${p === page ? 'active' : ''}"><button type="button" class="page-link page-btn" data-page="${p}" aria-label="${name}"${p === page ? ' aria-current="page"' : ''}>${label}</button></li>`;
        const start = Math.max(1, page - 2), end = Math.min(totalPages, page + 2);
        let html = page > 1 ? item(page - 1, '‹ Prev', 'Previous employee page') : '';
        if (start > 1) html += item(1, '1', 'Employee page 1') + (start > 2 ? '<li class="page-item"><span class="page-link">…</span></li>' : '');
        for (let p = start; p <= end; p++) html += item(p, p, `Employee page ${p}`);
        if (end < totalPages) html += (end < totalPages - 1 ? '<li class="page-item"><span class="page-link">…</span></li>' : '') + item(totalPages, totalPages, `Employee page ${totalPages}`);
        if (page < totalPages) html += item(page + 1, 'Next ›', 'Next employee page');
        $('#employeesPagination').html(html);
        if (restoreFocus) $('#employeesPagination [aria-current="page"]').trigger('focus');
    }

    function dialogValues($dialog) {
        const values = {};
        $dialog.find('input[name]:not([type="hidden"]):not([readonly]), select[name]').each(function () { values[this.name] = String($(this).val() ?? ''); });
        return values;
    }

    function dialogIsDirty($dialog) {
        const state = employeeDialogs[$dialog.attr('id')];
        if (!state?.baseline || !state.ready) return false;
        const values = dialogValues($dialog);
        return Object.keys(values).some(name => values[name] !== (state.baseline[name] ?? ''));
    }

    function dialogReadStatus($dialog) {
        const state = employeeDialogs[$dialog.attr('id')];
        if (!state) return;
        const failures = [], loading = [];
        if (state.detailError) failures.push(state.detailError);
        else if (!state.ready) loading.push('employee details');
        Object.entries(state.lookups).forEach(([name, entry]) => {
            if (entry.error) failures.push(`${lookupDefinitions[name].label} choices could not load.`);
            else if (!entry.ready) loading.push(`${lookupDefinitions[name].label} choices`);
        });
        const unresolved = $dialog.find('select option:selected[data-unresolved]').length > 0;
        if (unresolved) failures.push('A current selection is unavailable in the loaded choices. It has been kept. Retry the choices or select an available value.');
        const blocked = !state.ready || loading.length > 0 || failures.length > 0;
        const $feedback = $dialog.find('.employee-read-feedback');
        const retryHadFocus = $feedback.has(document.activeElement).length > 0;
        $feedback.prop('hidden', !blocked).toggleClass('alert alert-danger', failures.length > 0);
        $feedback.find('[role="status"]').text([...failures, ...(loading.length ? [`Loading ${loading.join(', ')}…`] : [])].join(' '));
        $feedback.find('.employee-retry').prop('hidden', failures.length === 0);
        $dialog.find('.employee-fields').prop('disabled', !state.ready || state.discard || state.pending);
        $dialog.find('select[name]').each(function () {
            const entry = state.lookups[this.name];
            $(this).prop('disabled', !!entry && !entry.ready);
        });
        $dialog.find('[type="submit"]').prop('disabled', blocked || state.discard || state.pending);
        $dialog.attr('aria-busy', loading.length ? 'true' : 'false');
        if (retryHadFocus && failures.length === 0 && $dialog.hasClass('show')) focusDialog($dialog);
    }

    function populateChoices($dialog, name) {
        const state = employeeDialogs[$dialog.attr('id')], entry = state.lookups[name];
        const $select = $dialog.find(`[name="${name}"]`);
        const desired = String($select.val() ?? state.baseline[name] ?? '');
        const priorLabel = $select.find(':selected').text();
        $select.empty().append(new Option(`Select ${lookupDefinitions[name].label}`, ''));
        entry.rows.forEach(row => $select.append(new Option(row.name, String(row.id))));
        if (desired && !$select.find('option').toArray().some(option => option.value === desired)) {
            $select.append($(new Option(priorLabel || `Current selection (${desired})`, desired)).attr('data-unresolved', 'true'));
        }
        $select.val(desired);
    }

    function loadDialogLookup($dialog, name) {
        const state = employeeDialogs[$dialog.attr('id')], entry = state.lookups[name];
        if (entry.xhr) entry.xhr.abort();
        const sequence = entry.sequence = (entry.sequence || 0) + 1;
        entry.error = false;
        entry.ready = false;
        dialogReadStatus($dialog);
        entry.xhr = $.ajax({url: lookupDefinitions[name].url, type: 'GET', dataType: 'json'}).done(function (res) {
            if (employeeDialogs[$dialog.attr('id')] !== state || entry.sequence !== sequence) return;
            if (!res || res.error || res.success === false || !Array.isArray(res.data)) { entry.error = true; return; }
            entry.rows = res.data;
            entry.ready = true;
            populateChoices($dialog, name);
        }).fail(function (xhr, status) {
            if (employeeDialogs[$dialog.attr('id')] === state && entry.sequence === sequence && status !== 'abort') entry.error = true;
        }).always(function () {
            if (employeeDialogs[$dialog.attr('id')] === state && entry.sequence === sequence) { entry.xhr = null; dialogReadStatus($dialog); }
        });
    }

    function loadDialogRecord($dialog) {
        const state = employeeDialogs[$dialog.attr('id')];
        if (state.xhr) state.xhr.abort();
        const sequence = state.sequence = (state.sequence || 0) + 1;
        state.detailError = '';
        dialogReadStatus($dialog);
        const compensation = $dialog.is('#compensationModal');
        state.xhr = $.ajax({url: `/hris/employees/${compensation ? 'getcompensation' : 'getemployee'}/${encodeURIComponent(state.employeeId)}`, type: 'GET', dataType: 'json'}).done(function (res) {
            if (employeeDialogs[$dialog.attr('id')] !== state || state.sequence !== sequence) return;
            // Legacy compensation explicitly uses this response for an absent rate.
            const noCompensation = compensation && res?.success === false && res.message === 'Data not found.';
            if (!noCompensation && (!res || res.success !== true || !res.data || typeof res.data !== 'object')) {
                state.detailError = 'Unable to load employee details. Your entries are kept. Retry when ready.';
                return;
            }
            const emp = res.data;
            if ($dialog.is('#editEmployeeModal')) {
                $dialog.find('input[name], select[name]').each(function () {
                    if (this.name === 'csrf_token') return;
                    const value = String(emp[this.name] ?? '');
                    if (this.tagName === 'SELECT' && value && !$(this).find('option').toArray().some(option => option.value === value)) $(this).append($(new Option(`Current selection (${value})`, value)).attr('data-unresolved', 'true'));
                    $(this).val(value);
                });
                state.baseline = dialogValues($dialog);
                Object.keys(state.lookups).forEach(name => { if (state.lookups[name].ready) populateChoices($dialog, name); });
            } else if ($dialog.is('#viewEmployeeModal')) {
                const fields = {employeeNo: emp.employee_no, fullName: employeeName(emp), branch: emp.branch_name, position: emp.position, orgUnit: emp.org_unit, email: emp.email, contactNo: emp.contact_no, dateHired: emp.date_hired, employmentType: emp.employment_type, employmentStatus: emp.employment_status, status: emp.status};
                Object.entries(fields).forEach(([name, value]) => $(`#view_${name}`).text(value ?? '—'));
            } else if (compensation) {
                $('#compensation_employee_id').val(state.employeeId);
                if (emp) {
                    const fields = {compensation_id: emp.id, compensation_employee_id: emp.employee_id, salary_type: emp.salary_type, payroll_frequency: emp.payroll_frequency, salary_rate: emp.salary_rate, effective_from: emp.effective_from, effective_to: emp.effective_to || ''};
                    Object.entries(fields).forEach(([id, value]) => $(`#${id}`).val(value));
                }
                $('#compensationModalLabel').text(emp ? 'Edit Employee Compensation' : 'Add Employee Compensation');
            }
            state.ready = true;
            dialogReadStatus($dialog);
            if ($dialog.hasClass('show') && document.activeElement === $dialog.find('.modal-title')[0]) focusDialog($dialog);
        }).fail(function (xhr, status) {
            if (employeeDialogs[$dialog.attr('id')] === state && state.sequence === sequence && status !== 'abort') state.detailError = 'Unable to load employee details. Your entries are kept. Retry when ready.';
        }).always(function () {
            if (employeeDialogs[$dialog.attr('id')] === state && state.sequence === sequence) { state.xhr = null; dialogReadStatus($dialog); }
        });
    }

    function focusDialog($dialog) {
        const state = employeeDialogs[$dialog.attr('id')];
        const $target = $dialog.is('#deleteEmployeeModal') ? $dialog.find('.employee-cancel')
            : state.ready ? $dialog.find('input:not([type="hidden"]):not([readonly]):enabled, select:enabled').first() : $();
        ($target.length ? $target : $dialog.find('.modal-title')).trigger('focus');
    }

    function openEmployeeDialog(id, trigger) {
        const $dialog = $(id);
        $dialog.data('employee-trigger', trigger);
        bootstrap.Modal.getOrCreateInstance($dialog[0]).show(trigger);
    }

    function mutationFeedback($dialog, text) {
        $dialog.find('.employee-mutation-feedback').prop('hidden', false).find('[role="alert"]').text(text);
    }

    // Presentation guard only: explicit success is required before clearing a dialog.
    // Endpoint validation/response/access correctness and A15 remain separately gated.
    function employeeMutation($dialog, url, data, onSuccess) {
        const state = employeeDialogs[$dialog.attr('id')];
        if (state.pending) return;
        state.pending = true;
        $dialog.find('.employee-mutation-feedback').prop('hidden', true);
        $dialog.find('[type="submit"], #confirmDeleteEmployeeBtn').prop('disabled', true);
        $('#compensationSpinner', $dialog).removeClass('d-none');
        $.ajax({url, type: 'POST', data, dataType: 'json'}).done(function (res) {
            if (!res || res.success !== true) {
                mutationFeedback($dialog, res?.success === false || res?.error ? (res.message || res.error || 'The request was not completed. Your entries are kept.') : 'The result could not be confirmed. Your entries are kept. Check the record before trying again.');
                return;
            }
            state.pending = false;
            state.allowHide = true;
            showToast(res.message || 'Request completed.', 'success');
            bootstrap.Modal.getInstance($dialog[0]).hide();
            onSuccess();
        }).fail(function () {
            mutationFeedback($dialog, 'The result could not be confirmed. Your entries are kept. Check the record before trying again.');
        }).always(function () {
            state.pending = false;
            dialogReadStatus($dialog);
            $dialog.find('#confirmDeleteEmployeeBtn').prop('disabled', false);
            $('#compensationSpinner', $dialog).addClass('d-none');
        });
    }

    function submitEmployee(form) {
        const $form = $(form), $dialog = $form.closest('.modal'), state = employeeDialogs[$dialog.attr('id')];
        if (!state.ready || $form.find('[type="submit"]').prop('disabled')) return;
        // Keep the original payload, including read-only employee_no (C-EMP-01).
        const fields = ['employee_no', 'firstname', 'middlename', 'lastname', 'email', 'contact_no', 'branch_id', 'position_id', 'org_unit_id', 'employment_type_id', 'date_hired', 'csrf_token'];
        const edit = $dialog.is('#editEmployeeModal');
        if (edit) fields.push('employment_status_id', 'date_resigned');
        const data = {};
        fields.forEach(name => { data[name] = $form.find(`[name="${name}"]`).val(); });
        employeeMutation($dialog, edit ? '/hris/employees/updateemployee/' + $form.find('[name="id"]').val() : '/hris/employees/create', data, function () { getAllEmployees(edit ? currentPage : 1); });
    }

    $(function () {
        getAllEmployees(1);
        $('#exportEmployeesBtn').on('click', exportEmployees);
        $('#employeeSearchForm').on('submit', function (event) { event.preventDefault(); currentSearch = $('#employeeSearchInput').val(); getAllEmployees(1); });
        $('#clearEmployeeSearchBtn').on('click', function () { $('#employeeSearchInput').val(''); currentSearch = ''; getAllEmployees(1); });
        $('#retryEmployeeList').on('click', function () { getAllEmployees(requestedPage); });
        $('.employee-sort').on('click', function () { const key = $(this).data('sort'); currentSortDir = currentSortBy === key && currentSortDir === 'ASC' ? 'DESC' : 'ASC'; currentSortBy = key; getAllEmployees(1); });
        $('#employeeMobileSortForm').on('submit', function (event) {
            event.preventDefault();
            currentSortBy = $('#employeeMobileSortBy').val();
            currentSortDir = $('#employeeMobileSortDir').val();
            getAllEmployees(1);
        });
        $('#employeesPagination').on('click', '.page-btn', function () { getAllEmployees($(this).data('page')); });
        $('.employee-page').on('click', '.viewEmployeeBtn, .editEmployeeBtn, .deleteEmployeeBtn, .editCompensationBtn', function () {
            const id = $(this).hasClass('viewEmployeeBtn') ? '#viewEmployeeModal' : $(this).hasClass('editEmployeeBtn') ? '#editEmployeeModal' : $(this).hasClass('deleteEmployeeBtn') ? '#deleteEmployeeModal' : '#compensationModal';
            openEmployeeDialog(id, this);
        });
        const mobileCards = window.matchMedia('(max-width: 767.98px)');
        let employeeListFocus = null;
        $(document).on('focusin.employeeResponsive', function (event) {
            employeeListFocus = $(event.target).closest('.employee-table-region, .employee-mobile-sort, #employeeCards').length ? event.target : null;
        });
        mobileCards.addEventListener('change', function () {
            // A display:none transition may move native focus to body before this event.
            const active = document.activeElement === document.body && employeeListFocus && !$(employeeListFocus).is(':visible') ? employeeListFocus : document.activeElement;
            if ($(active).is('[data-employee-action]')) focusEmployeeAction(active, active.dataset.id);
            else if ($(active).closest('.employee-table-region, .employee-mobile-sort').length && !$(active).is(':visible')) {
                (mobileCards.matches ? document.getElementById('employeeMobileSortBy') : document.querySelector('.employee-sort')).focus();
            }
        });

        $('.employee-dialog').on('keydown', function (event) {
            if (event.key !== 'Tab') return;
            // Bootstrap contains focusin; also prevent Tab from visiting browser chrome.
            const $controls = $(this).find('a[href], button, input:not([type="hidden"]), select, textarea, [tabindex]:not([tabindex="-1"])')
                .filter(':visible:not(:disabled)').filter(function () { return this.tabIndex >= 0; });
            const first = $controls[0], last = $controls[$controls.length - 1];
            if (first && ((event.shiftKey && document.activeElement === first) || (!event.shiftKey && document.activeElement === last))) {
                event.preventDefault();
                (event.shiftKey ? last : first).focus();
            }
        }).on('show.bs.modal', function (event) {
            const $dialog = $(this), trigger = event.relatedTarget || $dialog.data('employee-trigger');
            const form = $dialog.find('form')[0];
            if (form) form.reset();
            const state = employeeDialogs[this.id] = {trigger, employeeId: trigger?.dataset.id, baseline: {}, ready: $dialog.is('#addEmployeeModal, #deleteEmployeeModal'), lookups: {}, pending: false, discard: false};
            $dialog.find('.employee-discard, .employee-mutation-feedback').prop('hidden', true);
            $dialog.find('.employee-details dd').text('—');
            if ($dialog.is('#compensationModal')) { $('#compensation_id, #compensation_employee_id').val(''); $('#compensationModalLabel').text('Employee Compensation'); $('#compensationEmployeeName').text(trigger?.dataset.name || ''); }
            if ($dialog.is('#deleteEmployeeModal')) { pendingDeleteId = state.employeeId; $('#deleteEmployeeName').text(trigger?.dataset.name || 'this employee'); }
            if ($dialog.is('#addEmployeeModal, #editEmployeeModal')) {
                // Reset choices for a new dialog session; retries in this session keep values.
                $dialog.find('select[name]').each(function () {
                    $(this).empty().append(new Option(`Select ${lookupDefinitions[this.name].label}`, ''));
                    state.lookups[this.name] = {ready: false, error: false};
                });
                state.baseline = dialogValues($dialog);
                Object.keys(state.lookups).forEach(name => loadDialogLookup($dialog, name));
            }
            if (!state.ready) loadDialogRecord($dialog);
            dialogReadStatus($dialog);
        }).on('shown.bs.modal', function () { focusDialog($(this)); }).on('hide.bs.modal', function (event) {
            const $dialog = $(this), state = employeeDialogs[this.id];
            if (state.pending) { event.preventDefault(); return; }
            if (!state.allowHide && $dialog.is('#addEmployeeModal, #editEmployeeModal') && dialogIsDirty($dialog)) {
                event.preventDefault();
                if (!state.discard) state.lastFocus = document.activeElement;
                state.discard = true;
                $dialog.find('.employee-discard').prop('hidden', false);
                dialogReadStatus($dialog);
                $dialog.find('.employee-stay').trigger('focus');
            }
        }).on('hidden.bs.modal', function () {
            const $dialog = $(this), state = employeeDialogs[this.id];
            ++state.sequence;
            if (state.xhr) state.xhr.abort();
            Object.values(state.lookups).forEach(entry => { ++entry.sequence; if (entry.xhr) entry.xhr.abort(); });
            if ($dialog.is('#deleteEmployeeModal')) pendingDeleteId = null;
            focusEmployeeAction(state.trigger, state.employeeId);
        });
        $('.employee-stay').on('click', function () {
            const $dialog = $(this).closest('.modal'), state = employeeDialogs[$dialog.attr('id')];
            state.discard = false; $dialog.find('.employee-discard').prop('hidden', true); dialogReadStatus($dialog);
            if (state.lastFocus && !state.lastFocus.disabled) state.lastFocus.focus(); else focusDialog($dialog);
        });
        $('.employee-discard-confirm').on('click', function () { const $dialog = $(this).closest('.modal'); employeeDialogs[$dialog.attr('id')].allowHide = true; bootstrap.Modal.getInstance($dialog[0]).hide(); });
        $('.employee-retry').on('click', function () {
            const $dialog = $(this).closest('.modal'), state = employeeDialogs[$dialog.attr('id')];
            if (!state.ready && !state.xhr) loadDialogRecord($dialog);
            Object.keys(state.lookups).forEach(name => { if (!state.lookups[name].xhr) loadDialogLookup($dialog, name); });
        });
        $('.employee-dialog select').on('change', function () { dialogReadStatus($(this).closest('.modal')); });
        $('#addEmployeeForm, #editEmployeeForm').on('submit', function (event) { event.preventDefault(); submitEmployee(this); });
        $('#confirmDeleteEmployeeBtn').on('click', function () {
            if (pendingDeleteId) employeeMutation($('#deleteEmployeeModal'), '/hris/employees/deleteemployee/' + pendingDeleteId, {csrf_token: $('#addEmployeeForm [name="csrf_token"]').val()}, function () { getAllEmployees(currentPage); });
        });
        $('#compensationForm').on('submit', function (event) {
            event.preventDefault();
            if ($(this).find('[type="submit"]').prop('disabled')) return;
            const from = $('#effective_from').val(), to = $('#effective_to').val(), rate = parseFloat($('#salary_rate').val());
            if (from && to && to < from) { mutationFeedback($('#compensationModal'), 'Effective To cannot be earlier than Effective From.'); $('#effective_to').trigger('focus'); return; }
            if (isNaN(rate) || rate <= 0) { mutationFeedback($('#compensationModal'), 'Please enter a valid salary rate.'); $('#salary_rate').trigger('focus'); return; }
            const id = $('#compensation_id').val();
            employeeMutation($('#compensationModal'), id ? '/hris/employees/updatecompensation/' + id : '/hris/employees/createcompensation', $(this).serialize(), function () {});
        });
    });
</script>
</body>
</html>
