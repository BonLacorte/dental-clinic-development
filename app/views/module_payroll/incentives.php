<?php
$pageConfig = [
    'title' => 'Incentives',
    'singular' => 'Incentive',
    'hasTypes' => true,
    'typeLabel' => 'Incentive Type',
    'recordDefaultSort' => 'created_at',
    'typeDefaultSort' => 'created_at',
    'recordColumns' => [
        ['label' => 'Employee', 'field' => 'employee_name', 'sort' => 'employee_name', 'type' => 'employee'],
        ['label' => 'Type', 'field' => 'type_name', 'sort' => 'type_name', 'type' => 'type'],
        ['label' => 'Amount', 'field' => 'amount', 'sort' => 'amount', 'type' => 'money'],
        ['label' => 'Recurring', 'field' => 'is_recurring', 'sort' => 'is_recurring', 'type' => 'bool'],
        ['label' => 'Effective From', 'field' => 'effective_from', 'sort' => 'effective_from', 'type' => 'date'],
        ['label' => 'Effective To', 'field' => 'effective_to', 'sort' => 'effective_to', 'type' => 'date'],
        ['label' => 'Created At', 'field' => 'created_at', 'sort' => 'created_at', 'type' => 'datetime']
    ],
    'typeColumns' => [
        ['label' => 'Code', 'field' => 'code', 'sort' => 'code'],
        ['label' => 'Name', 'field' => 'name', 'sort' => 'name'],
        ['label' => 'Computation', 'field' => 'computation_type', 'sort' => 'computation_type'],
        ['label' => 'Default Value', 'field' => 'default_value', 'sort' => 'default_value', 'type' => 'money'],
        ['label' => 'Created At', 'field' => 'created_at', 'sort' => 'created_at', 'type' => 'datetime']
    ],
    'recordFields' => [
        ['name' => 'employee_id', 'label' => 'Employee', 'input' => 'employee', 'required' => true, 'col' => 'col-md-6'],
        ['name' => 'incentive_type_id', 'label' => 'Incentive Type', 'input' => 'type', 'required' => true, 'col' => 'col-md-6'],
        ['name' => 'amount', 'label' => 'Amount', 'input' => 'number', 'required' => true, 'step' => '0.01', 'min' => '0.01', 'col' => 'col-md-4'],
        ['name' => 'is_recurring', 'label' => 'Recurring', 'input' => 'bool', 'required' => true, 'default' => '0', 'col' => 'col-md-4'],
        ['name' => 'effective_from', 'label' => 'Effective From', 'input' => 'date', 'required' => true, 'col' => 'col-md-4'],
        ['name' => 'effective_to', 'label' => 'Effective To', 'input' => 'date', 'required' => false, 'col' => 'col-md-4']
    ],
    'typeFields' => [
        ['name' => 'code', 'label' => 'Code', 'input' => 'text', 'required' => true, 'max' => 50, 'col' => 'col-md-6'],
        ['name' => 'name', 'label' => 'Name', 'input' => 'text', 'required' => true, 'max' => 100, 'col' => 'col-md-6'],
        ['name' => 'computation_type', 'label' => 'Computation Type', 'input' => 'select', 'required' => true, 'options' => ['FIXED', 'PERCENTAGE'], 'default' => 'FIXED', 'col' => 'col-md-6'],
        ['name' => 'default_value', 'label' => 'Default Value', 'input' => 'number', 'required' => true, 'step' => '0.01', 'min' => '0', 'default' => '0.00', 'col' => 'col-md-6']
    ],
    'urls' => [
        'employees' => '/payroll/incentives/employees/options',
        'recordGetAll' => '/payroll/incentives/getallincentives',
        'recordGetById' => '/payroll/incentives/getincentivebyid/',
        'recordCreate' => '/payroll/incentives/create',
        'recordUpdate' => '/payroll/incentives/update/',
        'recordDelete' => '/payroll/incentives/delete/',
        'typeOptions' => '/payroll/incentives/types/options',
        'typeGetAll' => '/payroll/incentives/types/getall',
        'typeGetById' => '/payroll/incentives/types/getbyid/',
        'typeCreate' => '/payroll/incentives/types/create',
        'typeUpdate' => '/payroll/incentives/types/update/',
        'typeDelete' => '/payroll/incentives/types/delete/'
    ]
];

?>
<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_payroll/partials/payroll_nav.php'; ?>
<?php
if (!function_exists('renderPayrollItemFields')) {
    function renderPayrollItemFields(array $fields): void
    {
        foreach ($fields as $field):
            $name = e($field['name']);
            $label = e($field['label']);
            $col = e($field['col'] ?? 'col-md-6');
            $required = !empty($field['required']) ? 'required' : '';
            $default = e($field['default'] ?? '');
            ?>
            <div class="<?= $col; ?>">
                <label class="form-label"><?= $label; ?></label>
                <?php if ($field['input'] === 'employee'): ?>
                    <select class="form-select employee-select" name="<?= $name; ?>" <?= $required; ?>>
                        <option value="">Select Employee</option>
                    </select>
                <?php elseif ($field['input'] === 'type'): ?>
                    <select class="form-select payroll-type-select" name="<?= $name; ?>" <?= $required; ?>>
                        <option value="">Select Type</option>
                    </select>
                <?php elseif ($field['input'] === 'select'): ?>
                    <select class="form-select" name="<?= $name; ?>" <?= $required; ?> data-default="<?= $default; ?>">
                        <?php foreach (($field['options'] ?? []) as $option): ?>
                            <option value="<?= e($option); ?>" <?= ($field['default'] ?? '') === $option ? 'selected' : ''; ?>><?= e($option); ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif ($field['input'] === 'bool'): ?>
                    <select class="form-select" name="<?= $name; ?>" <?= $required; ?> data-default="<?= $default; ?>">
                        <option value="1" <?= ($field['default'] ?? '') === '1' ? 'selected' : ''; ?>>YES</option>
                        <option value="0" <?= ($field['default'] ?? '') === '0' ? 'selected' : ''; ?>>NO</option>
                    </select>
                <?php elseif ($field['input'] === 'date'): ?>
                    <input type="date" class="form-control" name="<?= $name; ?>" <?= $required; ?>>
                <?php elseif ($field['input'] === 'number'): ?>
                    <input type="number" class="form-control" name="<?= $name; ?>" step="<?= e($field['step'] ?? '1'); ?>" min="<?= e($field['min'] ?? '0'); ?>" value="<?= $default; ?>" <?= $required; ?> data-default="<?= $default; ?>">
                <?php else: ?>
                    <input type="text" class="form-control" name="<?= $name; ?>" maxlength="<?= e($field['max'] ?? '255'); ?>" <?= $required; ?>>
                <?php endif; ?>
            </div>
        <?php endforeach;
    }
}

$hasTypes = !empty($pageConfig['hasTypes']);
?>

<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Manage <?= e($pageConfig['title']); ?></h5>
            <div class="d-flex gap-2">
                <?php if ($hasTypes): ?>
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTypeModal">
                        <i class="bi bi-plus-lg"></i> Add <?= e($pageConfig['typeLabel']); ?>
                    </button>
                <?php endif; ?>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRecordModal">
                    <i class="bi bi-plus-lg"></i> Add <?= e($pageConfig['singular']); ?>
                </button>
            </div>
        </div>

        <?php if ($hasTypes): ?>
            <div class="card-body border-bottom pb-0">
                <ul class="nav nav-tabs" id="payrollItemTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="records-tab" data-bs-toggle="tab" data-bs-target="#recordsPane" type="button" role="tab">Records</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="types-tab" data-bs-toggle="tab" data-bs-target="#typesPane" type="button" role="tab"><?= e($pageConfig['typeLabel']); ?>s</button>
                    </li>
                </ul>
            </div>
        <?php endif; ?>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="recordsPane" role="tabpanel">
                <div class="card-body border-bottom">
                    <form id="recordSearchForm" class="row g-2 align-items-end">
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label">Search</label>
                            <input type="text" class="form-control" id="recordSearchInput" placeholder="Search <?= e(strtolower($pageConfig['title'])); ?>">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-secondary" type="button" id="clearRecordSearchBtn">Clear</button>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="recordsTable">
                            <thead class="table-light">
                                <tr>
                                    <?php foreach ($pageConfig['recordColumns'] as $index => $column): ?>
                                        <th class="<?= $index === 0 ? 'ps-4' : ''; ?>">
                                            <button class="btn btn-link p-0 record-sort" data-sort="<?= e($column['sort']); ?>"><?= e($column['label']); ?></button>
                                        </th>
                                    <?php endforeach; ?>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-white py-3">
                    <nav aria-label="Record pages">
                        <ul class="pagination justify-content-end mb-0" id="recordsPagination"></ul>
                    </nav>
                </div>
            </div>

            <?php if ($hasTypes): ?>
                <div class="tab-pane fade" id="typesPane" role="tabpanel">
                    <div class="card-body border-bottom">
                        <form id="typeSearchForm" class="row g-2 align-items-end">
                            <div class="col-md-6 col-lg-4">
                                <label class="form-label">Search</label>
                                <input type="text" class="form-control" id="typeSearchInput" placeholder="Search <?= e(strtolower($pageConfig['typeLabel'])); ?>s">
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
                            <table class="table table-hover align-middle mb-0" id="typesTable">
                                <thead class="table-light">
                                    <tr>
                                        <?php foreach ($pageConfig['typeColumns'] as $index => $column): ?>
                                            <th class="<?= $index === 0 ? 'ps-4' : ''; ?>">
                                                <button class="btn btn-link p-0 type-sort" data-sort="<?= e($column['sort']); ?>"><?= e($column['label']); ?></button>
                                            </th>
                                        <?php endforeach; ?>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-white py-3">
                        <nav aria-label="Type pages">
                            <ul class="pagination justify-content-end mb-0" id="typesPagination"></ul>
                        </nav>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="addRecordModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="addRecordForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Add <?= e($pageConfig['singular']); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <?php renderPayrollItemFields($pageConfig['recordFields']); ?>
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

<div class="modal fade" id="editRecordModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="editRecordForm">
                <input type="hidden" name="id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Edit <?= e($pageConfig['singular']); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <?php renderPayrollItemFields($pageConfig['recordFields']); ?>
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

<div class="modal fade" id="deleteRecordModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete <?= e($pageConfig['singular']); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">Are you sure you want to delete this record? This action cannot be undone.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                <button class="btn btn-danger" type="button" id="confirmDeleteRecordBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<?php if ($hasTypes): ?>
    <div class="modal fade" id="addTypeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="addTypeForm">
                    <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Add <?= e($pageConfig['typeLabel']); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <?php renderPayrollItemFields($pageConfig['typeFields']); ?>
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

    <div class="modal fade" id="editTypeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="editTypeForm">
                    <input type="hidden" name="id">
                    <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit <?= e($pageConfig['typeLabel']); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <?php renderPayrollItemFields($pageConfig['typeFields']); ?>
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

    <div class="modal fade" id="deleteTypeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete <?= e($pageConfig['typeLabel']); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">Are you sure you want to delete this type? This action cannot be undone.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-danger" type="button" id="confirmDeleteTypeBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include '../app/views/partials/admin-footer.php'; ?>

<script>
const payrollConfig = <?= json_encode($pageConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

let employees = [];
let payrollTypes = [];
let pendingRecordDeleteId = null;
let pendingTypeDeleteId = null;
let recordState = {
    page: 1,
    search: '',
    sortBy: payrollConfig.recordDefaultSort,
    sortDir: 'DESC'
};
let typeState = {
    page: 1,
    search: '',
    sortBy: payrollConfig.typeDefaultSort || 'created_at',
    sortDir: 'DESC'
};

function esc(str) {
    return String(str ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    }[c]));
}

function money(value) {
    if (value === null || value === undefined || value === '') return '-';
    return Number(value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function displayValue(row, column) {
    let value = row[column.field];

    if (column.type === 'employee') {
        let employeeNo = row.employee_no ? `<small class="text-muted d-block">${esc(row.employee_no)}</small>` : '';
        return `${esc(row.employee_name || '-')}${employeeNo}`;
    }

    if (column.type === 'type') {
        let typeCode = row.type_code ? `<small class="text-muted d-block">${esc(row.type_code)}</small>` : '';
        return `${esc(row.type_name || '-')}${typeCode}`;
    }

    if (column.type === 'money') return money(value);
    if (column.type === 'bool') return String(value) === '1' ? 'YES' : 'NO';
    if (column.type === 'date' || column.type === 'datetime') return esc(value || '-');

    return esc(value || '-');
}

function csrfToken() {
    return $('#addRecordForm [name="csrf_token"]').val();
}

function formDataWithoutId(form) {
    return $.param($(form).serializeArray().filter(item => item.name !== 'id'));
}

function resetForm(formSelector, fields) {
    let $form = $(formSelector);
    $form[0].reset();
    fields.forEach(field => {
        if (field.default !== undefined) {
            $form.find(`[name="${field.name}"]`).val(field.default);
        }
    });
}

$(document).ready(function () {
    loadEmployees();

    if (payrollConfig.hasTypes) {
        loadPayrollTypes();
        getAllTypes(1);
    }

    getAllRecords(1);

    $('#recordSearchForm').on('submit', function (e) {
        e.preventDefault();
        recordState.search = $('#recordSearchInput').val();
        getAllRecords(1);
    });

    $('#clearRecordSearchBtn').on('click', function () {
        $('#recordSearchInput').val('');
        recordState.search = '';
        getAllRecords(1);
    });

    $('#typeSearchForm').on('submit', function (e) {
        e.preventDefault();
        typeState.search = $('#typeSearchInput').val();
        getAllTypes(1);
    });

    $('#clearTypeSearchBtn').on('click', function () {
        $('#typeSearchInput').val('');
        typeState.search = '';
        getAllTypes(1);
    });

    $('#addRecordModal').on('show.bs.modal', function () {
        resetForm('#addRecordForm', payrollConfig.recordFields);
    });

    $('#addTypeModal').on('show.bs.modal', function () {
        resetForm('#addTypeForm', payrollConfig.typeFields || []);
    });

    $('#addRecordForm').on('submit', function (e) {
        e.preventDefault();
        addRecord(this);
    });

    $('#editRecordForm').on('submit', function (e) {
        e.preventDefault();
        updateRecord(this);
    });

    $('#addTypeForm').on('submit', function (e) {
        e.preventDefault();
        addType(this);
    });

    $('#editTypeForm').on('submit', function (e) {
        e.preventDefault();
        updateType(this);
    });

    $(document).on('click', '.record-sort', function () {
        let sortBy = $(this).data('sort');
        recordState.sortDir = recordState.sortBy === sortBy && recordState.sortDir === 'ASC' ? 'DESC' : 'ASC';
        recordState.sortBy = sortBy;
        getAllRecords(1);
    });

    $(document).on('click', '.type-sort', function () {
        let sortBy = $(this).data('sort');
        typeState.sortDir = typeState.sortBy === sortBy && typeState.sortDir === 'ASC' ? 'DESC' : 'ASC';
        typeState.sortBy = sortBy;
        getAllTypes(1);
    });

    $(document).on('click', '.record-page-btn', function (e) {
        e.preventDefault();
        getAllRecords($(this).data('page'));
    });

    $(document).on('click', '.type-page-btn', function (e) {
        e.preventDefault();
        getAllTypes($(this).data('page'));
    });

    $(document).on('click', '.editRecordBtn', function () {
        getRecord($(this).data('id'));
    });

    $(document).on('click', '.deleteRecordBtn', function () {
        pendingRecordDeleteId = $(this).data('id');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteRecordModal')).show();
    });

    $('#confirmDeleteRecordBtn').on('click', function () {
        if (pendingRecordDeleteId) deleteRecord(pendingRecordDeleteId);
    });

    $(document).on('click', '.editTypeBtn', function () {
        getType($(this).data('id'));
    });

    $(document).on('click', '.deleteTypeBtn', function () {
        pendingTypeDeleteId = $(this).data('id');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteTypeModal')).show();
    });

    $('#confirmDeleteTypeBtn').on('click', function () {
        if (pendingTypeDeleteId) deleteType(pendingTypeDeleteId);
    });
});

function loadEmployees() {
    $.ajax({
        url: payrollConfig.urls.employees,
        method: 'GET',
        dataType: 'json',
        success: function (res) {
            employees = res.data || [];
            renderEmployeeOptions();
        }
    });
}

function renderEmployeeOptions() {
    let options = '<option value="">Select Employee</option>';
    employees.forEach(employee => {
        let middle = employee.middlename ? ' ' + employee.middlename : '';
        let label = `${employee.employee_no || 'NO ID'} - ${employee.lastname}, ${employee.firstname}${middle}`;
        options += `<option value="${esc(employee.id)}">${esc(label)}</option>`;
    });
    $('.employee-select').html(options);
}

function loadPayrollTypes() {
    if (!payrollConfig.hasTypes) return;

    $.ajax({
        url: payrollConfig.urls.typeOptions,
        method: 'GET',
        dataType: 'json',
        success: function (res) {
            payrollTypes = res.data || [];
            renderTypeOptions();
        }
    });
}

function renderTypeOptions() {
    let options = '<option value="">Select Type</option>';
    payrollTypes.forEach(type => {
        let label = `${type.code || ''} - ${type.name || ''}`;
        options += `<option value="${esc(type.id)}">${esc(label)}</option>`;
    });
    $('.payroll-type-select').html(options);
}

function getAllRecords(page) {
    recordState.page = page;
    showLoader('Loading ' + payrollConfig.title.toLowerCase() + '...');

    $.ajax({
        url: payrollConfig.urls.recordGetAll,
        method: 'GET',
        dataType: 'json',
        data: {
            page: page,
            search: recordState.search,
            sort_by: recordState.sortBy,
            sort_dir: recordState.sortDir
        },
        success: function (res) {
            renderRows('#recordsTable tbody', res.data || [], payrollConfig.recordColumns, 'record');
            renderPagination('#recordsPagination', res.currentPage || 1, res.totalPages || 1, 'record-page-btn');
        },
        error: function () {
            showToast('Failed to load records', 'error');
        },
        complete: hideLoader
    });
}

function getAllTypes(page) {
    if (!payrollConfig.hasTypes) return;
    typeState.page = page;
    showLoader('Loading types...');

    $.ajax({
        url: payrollConfig.urls.typeGetAll,
        method: 'GET',
        dataType: 'json',
        data: {
            page: page,
            search: typeState.search,
            sort_by: typeState.sortBy,
            sort_dir: typeState.sortDir
        },
        success: function (res) {
            renderRows('#typesTable tbody', res.data || [], payrollConfig.typeColumns, 'type');
            renderPagination('#typesPagination', res.currentPage || 1, res.totalPages || 1, 'type-page-btn');
        },
        error: function () {
            showToast('Failed to load types', 'error');
        },
        complete: hideLoader
    });
}

function renderRows(selector, rows, columns, mode) {
    let html = '';

    if (!rows.length) {
        html = `<tr><td class="text-center py-4" colspan="${columns.length + 1}">No records found.</td></tr>`;
        $(selector).html(html);
        return;
    }

    rows.forEach(row => {
        html += '<tr>';
        columns.forEach((column, index) => {
            html += `<td class="${index === 0 ? 'ps-4' : ''}">${displayValue(row, column)}</td>`;
        });
        html += '<td class="text-end pe-4">';
        html += `<button class="btn btn-link btn-sm text-primary ${mode === 'record' ? 'editRecordBtn' : 'editTypeBtn'}" data-id="${esc(row.id)}"><i class="bi bi-pencil-square"></i></button>`;
        html += `<button class="btn btn-link btn-sm text-danger ${mode === 'record' ? 'deleteRecordBtn' : 'deleteTypeBtn'}" data-id="${esc(row.id)}"><i class="bi bi-trash"></i></button>`;
        html += '</td></tr>';
    });

    $(selector).html(html);
}

function renderPagination(selector, currentPage, totalPages, buttonClass) {
    let html = '';

    for (let i = 1; i <= totalPages; i++) {
        html += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link ${buttonClass}" href="#" data-page="${i}">${i}</a>
            </li>
        `;
    }

    $(selector).html(html);
}

function addRecord(form) {
    showLoader('Saving record...');

    $.ajax({
        url: payrollConfig.urls.recordCreate,
        method: 'POST',
        dataType: 'json',
        data: $(form).serialize(),
        success: function (res) {
            showToast(res.message, res.success ? 'success' : 'error');
            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('addRecordModal')).hide();
                getAllRecords(1);
            }
        },
        error: function () {
            showToast('Failed to save record', 'error');
        },
        complete: hideLoader
    });
}

function getRecord(id) {
    showLoader('Loading record...');

    $.ajax({
        url: payrollConfig.urls.recordGetById + id,
        method: 'GET',
        dataType: 'json',
        success: function (res) {
            if (!res.success) {
                showToast(res.message || 'Record not found', 'error');
                return;
            }

            fillForm('#editRecordForm', res.data || {});
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editRecordModal')).show();
        },
        error: function () {
            showToast('Failed to load record', 'error');
        },
        complete: hideLoader
    });
}

function updateRecord(form) {
    let id = $(form).find('[name="id"]').val();
    showLoader('Updating record...');

    $.ajax({
        url: payrollConfig.urls.recordUpdate + id,
        method: 'POST',
        dataType: 'json',
        data: formDataWithoutId(form),
        success: function (res) {
            showToast(res.message, res.success ? 'success' : 'error');
            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('editRecordModal')).hide();
                getAllRecords(recordState.page);
            }
        },
        error: function () {
            showToast('Failed to update record', 'error');
        },
        complete: hideLoader
    });
}

function deleteRecord(id) {
    showLoader('Deleting record...');

    $.ajax({
        url: payrollConfig.urls.recordDelete + id,
        method: 'POST',
        dataType: 'json',
        data: { csrf_token: csrfToken() },
        success: function (res) {
            showToast(res.message, res.success ? 'success' : 'error');
            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('deleteRecordModal')).hide();
                pendingRecordDeleteId = null;
                getAllRecords(recordState.page);
            }
        },
        error: function () {
            showToast('Failed to delete record', 'error');
        },
        complete: hideLoader
    });
}

function addType(form) {
    showLoader('Saving type...');

    $.ajax({
        url: payrollConfig.urls.typeCreate,
        method: 'POST',
        dataType: 'json',
        data: $(form).serialize(),
        success: function (res) {
            showToast(res.message, res.success ? 'success' : 'error');
            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('addTypeModal')).hide();
                loadPayrollTypes();
                getAllTypes(1);
            }
        },
        error: function () {
            showToast('Failed to save type', 'error');
        },
        complete: hideLoader
    });
}

function getType(id) {
    showLoader('Loading type...');

    $.ajax({
        url: payrollConfig.urls.typeGetById + id,
        method: 'GET',
        dataType: 'json',
        success: function (res) {
            if (!res.success) {
                showToast(res.message || 'Type not found', 'error');
                return;
            }

            fillForm('#editTypeForm', res.data || {});
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editTypeModal')).show();
        },
        error: function () {
            showToast('Failed to load type', 'error');
        },
        complete: hideLoader
    });
}

function updateType(form) {
    let id = $(form).find('[name="id"]').val();
    showLoader('Updating type...');

    $.ajax({
        url: payrollConfig.urls.typeUpdate + id,
        method: 'POST',
        dataType: 'json',
        data: formDataWithoutId(form),
        success: function (res) {
            showToast(res.message, res.success ? 'success' : 'error');
            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('editTypeModal')).hide();
                loadPayrollTypes();
                getAllTypes(typeState.page);
            }
        },
        error: function () {
            showToast('Failed to update type', 'error');
        },
        complete: hideLoader
    });
}

function deleteType(id) {
    showLoader('Deleting type...');

    $.ajax({
        url: payrollConfig.urls.typeDelete + id,
        method: 'POST',
        dataType: 'json',
        data: { csrf_token: csrfToken() },
        success: function (res) {
            showToast(res.message, res.success ? 'success' : 'error');
            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('deleteTypeModal')).hide();
                pendingTypeDeleteId = null;
                loadPayrollTypes();
                getAllTypes(typeState.page);
            }
        },
        error: function () {
            showToast('Failed to delete type', 'error');
        },
        complete: hideLoader
    });
}

function fillForm(formSelector, data) {
    let $form = $(formSelector);
    $form.find('[name="id"]').val(data.id || '');

    Object.keys(data).forEach(key => {
        let $field = $form.find(`[name="${key}"]`);
        if ($field.length) {
            $field.val(data[key]);
        }
    });
}
</script>
</body>
</html>

