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
                <label class="form-label payroll-field">
                    <span><?= $label; ?><?php if ($required): ?> <span class="payroll-required" aria-hidden="true">*</span><span class="visually-hidden"> (required)</span><?php endif; ?></span>
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
                </label>
            </div>
        <?php endforeach;
    }
}

$hasTypes = !empty($pageConfig['hasTypes']);
?>

<div class="container-fluid py-4 management-page payroll-config-page payroll-items-page">
    <div class="card shadow-sm management-card">
        <div class="card-header bg-white payroll-items-header">
            <div><p class="payroll-eyebrow">Payroll items</p><h1 id="payrollItemsTitle"><?= e($pageConfig['title']); ?></h1><p class="payroll-description">Manage employee incentives and incentive types.</p></div>
            <div class="payroll-header-actions">
                <?php if ($hasTypes): ?>
                    <button id="addTypeAction" class="btn btn-primary d-none" data-bs-toggle="modal" data-bs-target="#addTypeModal">
                        <i class="bi bi-plus-lg" aria-hidden="true"></i> Add <?= e($pageConfig['typeLabel']); ?>
                    </button>
                <?php endif; ?>
                <button id="addRecordAction" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRecordModal">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i> Add <?= e($pageConfig['singular']); ?>
                </button>
            </div>
        </div>

        <?php if ($hasTypes): ?>
            <div class="card-body border-bottom pb-0">
                <ul class="nav nav-tabs" id="payrollItemTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="records-tab" data-bs-toggle="tab" data-bs-target="#recordsPane" type="button" role="tab" aria-controls="recordsPane" aria-selected="true">Employee <?= e($pageConfig['title']); ?></button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="types-tab" data-bs-toggle="tab" data-bs-target="#typesPane" type="button" role="tab" aria-controls="typesPane" aria-selected="false"><?= e($pageConfig['typeLabel']); ?>s</button>
                    </li>
                </ul>
            </div>
        <?php endif; ?>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="recordsPane" role="tabpanel" aria-labelledby="records-tab">
                <div class="card-body border-bottom">
                    <form id="recordSearchForm" class="payroll-search-toolbar">
                        <div class="payroll-search-field">
                            <label class="form-label" for="recordSearchInput">Search <?= e($pageConfig['title']); ?></label>
                            <input type="text" class="form-control" id="recordSearchInput" placeholder="Search <?= e(strtolower($pageConfig['title'])); ?>">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="bi bi-search" aria-hidden="true"></i> Search
                            </button>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-secondary" type="button" id="clearRecordSearchBtn">Clear</button>
                        </div>
                    </form>
                    <div class="payroll-sort-toolbar" aria-label="Records ordering">
                        <label for="recordSortBy">Sort by</label>
                        <select class="form-select" id="recordSortBy"></select>
                        <label class="visually-hidden" for="recordSortDirection">Sort direction</label>
                        <select class="form-select" id="recordSortDirection"><option value="DESC">Descending</option><option value="ASC">Ascending</option></select>
                    </div>
                    <p class="payroll-list-feedback" id="recordListFeedback" role="status" aria-live="polite"></p>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive payroll-table-scroll" role="region" aria-label="<?= e($pageConfig['title']); ?> table, scroll horizontally for more columns" tabindex="0">
                        <table class="table table-hover align-middle mb-0" id="recordsTable"><caption class="visually-hidden"><?= e($pageConfig['title']); ?>. Use Sort by for all available ordering fields.</caption>
                            <thead class="table-light">
                                <tr>
                                    <th scope="col"><button type="button" class="btn btn-link record-sort" data-sort="employee_name">Employee<span class="payroll-sort-indicator" aria-hidden="true">↕</span></button></th>
                                    <th scope="col"><button type="button" class="btn btn-link record-sort" data-sort="type_name">Incentive Type<span class="payroll-sort-indicator" aria-hidden="true">↕</span></button></th>
                                    <th scope="col" class="payroll-numeric"><button type="button" class="btn btn-link record-sort" data-sort="amount">Amount<span class="payroll-sort-indicator" aria-hidden="true">↕</span></button></th>
                                    <th scope="col"><button type="button" class="btn btn-link record-sort" data-sort="is_recurring">Recurrence<span class="payroll-sort-indicator" aria-hidden="true">↕</span></button></th>
                                    <th scope="col"><button type="button" class="btn btn-link record-sort" data-sort="effective_from">Effective Period<span class="payroll-sort-indicator" aria-hidden="true">↕</span></button></th>
                                    <th scope="col" class="payroll-actions-heading">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-white py-3">
                    <p class="payroll-page-info" id="recordPageInfo" role="status"></p><nav aria-label="<?= e($pageConfig['title']); ?> pages">
                        <ul class="pagination justify-content-end mb-0" id="recordsPagination"></ul>
                    </nav>
                </div>
            </div>

            <?php if ($hasTypes): ?>
                <div class="tab-pane fade" id="typesPane" role="tabpanel" aria-labelledby="types-tab">
                    <div class="card-body border-bottom">
                        <form id="typeSearchForm" class="payroll-search-toolbar">
                            <div class="payroll-search-field">
                                <label class="form-label" for="typeSearchInput">Search <?= e($pageConfig['typeLabel']); ?>s</label>
                                <input type="text" class="form-control" id="typeSearchInput" placeholder="Search <?= e(strtolower($pageConfig['typeLabel'])); ?>s">
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="bi bi-search" aria-hidden="true"></i> Search
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-outline-secondary" type="button" id="clearTypeSearchBtn">Clear</button>
                            </div>
                        </form>
                        <div class="payroll-sort-toolbar" aria-label="Types ordering">
                            <label for="typeSortBy">Sort by</label>
                            <select class="form-select" id="typeSortBy"></select>
                            <label class="visually-hidden" for="typeSortDirection">Sort direction</label>
                            <select class="form-select" id="typeSortDirection"><option value="DESC">Descending</option><option value="ASC">Ascending</option></select>
                        </div>
                        <p class="payroll-list-feedback" id="typeListFeedback" role="status" aria-live="polite"></p>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive payroll-table-scroll" role="region" aria-label="<?= e($pageConfig['typeLabel']); ?>s table, scroll horizontally for more columns" tabindex="0">
                        <table class="table table-hover align-middle mb-0" id="typesTable"><caption class="visually-hidden"><?= e($pageConfig['typeLabel']); ?>s. Use Sort by for all available ordering fields.</caption>
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col"><button type="button" class="btn btn-link type-sort" data-sort="name">Incentive Type<span class="payroll-sort-indicator" aria-hidden="true">↕</span></button></th>
                                    <th scope="col"><button type="button" class="btn btn-link type-sort" data-sort="computation_type">Computation<span class="payroll-sort-indicator" aria-hidden="true">↕</span></button></th>
                                    <th scope="col" class="payroll-numeric"><button type="button" class="btn btn-link type-sort" data-sort="default_value">Default Value<span class="payroll-sort-indicator" aria-hidden="true">↕</span></button></th>
                                        <th scope="col" class="payroll-actions-heading">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-white py-3">
                        <p class="payroll-page-info" id="typePageInfo" role="status"></p><nav aria-label="<?= e($pageConfig['typeLabel']); ?>s pages">
                            <ul class="pagination justify-content-end mb-0" id="typesPagination"></ul>
                        </nav>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade payroll-items-dialog" id="addRecordModal" aria-labelledby="addRecordModalTitle" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="addRecordForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">
                <div class="modal-header">
                    <h2 class="modal-title" id="addRecordModalTitle">Add <?= e($pageConfig['singular']); ?></h2>
                    <button type="button" class="btn-close" aria-label="Close dialog" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 payroll-form-fields">
                        <?php renderPayrollItemFields($pageConfig['recordFields']); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save <?= e($pageConfig['singular']); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade payroll-items-dialog" id="editRecordModal" aria-labelledby="editRecordModalTitle" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="editRecordForm">
                <input type="hidden" name="id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">
                <div class="modal-header">
                    <h2 class="modal-title" id="editRecordModalTitle">Edit <?= e($pageConfig['singular']); ?></h2>
                    <button type="button" class="btn-close" aria-label="Close dialog" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 payroll-form-fields">
                        <?php renderPayrollItemFields($pageConfig['recordFields']); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update <?= e($pageConfig['singular']); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade payroll-items-dialog" id="deleteRecordModal" aria-labelledby="deleteRecordModalTitle" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="deleteRecordModalTitle">Delete <?= e($pageConfig['singular']); ?></h2>
                <button type="button" class="btn-close" aria-label="Close dialog" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">Review the selected record before deleting.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                <button class="btn btn-danger" type="button" id="confirmDeleteRecordBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<?php if ($hasTypes): ?>
    <div class="modal fade payroll-items-dialog" id="addTypeModal" aria-labelledby="addTypeModalTitle" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <form id="addTypeForm">
                    <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">
                    <div class="modal-header">
                        <h2 class="modal-title" id="addTypeModalTitle">Add <?= e($pageConfig['typeLabel']); ?></h2>
                        <button type="button" class="btn-close" aria-label="Close dialog" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 payroll-form-fields">
                            <?php renderPayrollItemFields($pageConfig['typeFields']); ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save <?= e($pageConfig['typeLabel']); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade payroll-items-dialog" id="editTypeModal" aria-labelledby="editTypeModalTitle" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <form id="editTypeForm">
                    <input type="hidden" name="id">
                    <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">
                    <div class="modal-header">
                        <h2 class="modal-title" id="editTypeModalTitle">Edit <?= e($pageConfig['typeLabel']); ?></h2>
                        <button type="button" class="btn-close" aria-label="Close dialog" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 payroll-form-fields">
                            <?php renderPayrollItemFields($pageConfig['typeFields']); ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update <?= e($pageConfig['typeLabel']); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade payroll-items-dialog" id="deleteTypeModal" aria-labelledby="deleteTypeModalTitle" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="deleteTypeModalTitle">Delete <?= e($pageConfig['typeLabel']); ?></h2>
                    <button type="button" class="btn-close" aria-label="Close dialog" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">Review the selected type before deleting.</div>
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

// Page-local presentation only. The original pageConfig owns forms and endpoints.
let employees = [];
let payrollTypes = [];
let pendingRecordDeleteId = null;
let pendingTypeDeleteId = null;
let recordState = { page: 1, search: '', sortBy: payrollConfig.recordDefaultSort, sortDir: 'DESC' };
let typeState = { page: 1, search: '', sortBy: payrollConfig.typeDefaultSort || 'created_at', sortDir: 'DESC' };
const listVersions = { record: 0, type: 0 };
const rowCache = { record: new Map(), type: new Map() };
const optionsReady = { employees: false, types: !payrollConfig.hasTypes };
const isCashAdvance = payrollConfig.singular === 'Cash Advance';
const isCompensation = payrollConfig.singular === 'Compensation';
const missing = '<span class="payroll-meta">Not provided</span>';
const recordColumns = isCashAdvance
    ? ['employee_name', 'amount', 'deduction_type', 'remaining_balance', 'status']
    : isCompensation
        ? ['employee_name', 'salary_rate', 'salary_type', 'payroll_frequency', 'effective_from']
        : payrollConfig.recordColumns.filter(c => !['effective_to', 'created_at'].includes(c.field)).map(c => c.field);
const typeColumns = ['name', 'computation_type', 'default_value', ...(payrollConfig.typeFields?.some(f => f.name === 'taxable') ? ['taxable'] : [])];

function esc(str) {
    return String(str ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}
function hasValue(value) { return value !== null && value !== undefined && value !== ''; }
function money(value) {
    if (!hasValue(value)) return missing;
    // Format the decimal string, avoiding Number conversion and two-place rounding.
    const parts = String(value).match(/^([+-]?)(\d+)(?:\.(\d+))?$/);
    if (!parts) return esc(value);
    return `${esc(parts[1])}₱${parts[2].replace(/\B(?=(\d{3})+(?!\d))/g, ',')}.${(parts[3] || '').padEnd(2, '0')}`;
}
function dateLabel(value) {
    if (!hasValue(value)) return '';
    const match = String(value).match(/^(\d{4})-(\d{2})-(\d{2})$/);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    if (!match || !months[Number(match[2]) - 1] || Number(match[3]) < 1 || Number(match[3]) > 31) return esc(value);
    return `${Number(match[3])} ${months[Number(match[2]) - 1]} ${match[1]}`;
}
function readable(value) {
    const labels = { FIXED: 'Fixed', PERCENTAGE: 'Percentage', MONTHLY: 'Monthly', DAILY: 'Daily', HOURLY: 'Hourly', WEEKLY: 'Weekly', SEMI_MONTHLY: 'Semi-monthly', SINGLE: 'Single', INSTALLMENT: 'Installment', ACTIVE: 'Active', PAID: 'Paid', CANCELLED: 'Cancelled' };
    return hasValue(value) ? esc(Object.hasOwn(labels, value) ? labels[value] : value) : missing;
}
function displayValue(row, field, mode) {
    const value = row[field];
    if (field === 'employee_name' || (mode === 'type' && field === 'name')) {
        const secondary = mode === 'record' ? row.employee_no : row.code;
        const added = hasValue(row.created_at) ? `<span class="payroll-meta">Added ${esc(row.created_at)}</span>` : '';
        return `<span class="payroll-identity">${hasValue(value) ? esc(value) : missing}</span>${hasValue(secondary) ? `<span class="payroll-meta">${esc(secondary)}</span>` : ''}${added}`;
    }
    if (field === 'type_name') return `<span class="payroll-identity">${hasValue(value) ? esc(value) : missing}</span>${hasValue(row.type_code) ? `<span class="payroll-meta">${esc(row.type_code)}</span>` : ''}`;
    if (field === 'effective_from') return `<span class="payroll-period">${dateLabel(value) || 'Start date not set'}</span><span class="payroll-meta">${hasValue(row.effective_to) ? 'to ' + dateLabel(row.effective_to) : 'End date not set'}</span>`;
    if (field === 'is_recurring') return `<span class="payroll-status">${String(value) === '1' ? 'Recurring' : String(value) === '0' ? 'One-time' : hasValue(value) ? esc(value) : 'Not provided'}</span>`;
    if (field === 'taxable') return String(value) === '1' ? 'Yes' : String(value) === '0' ? 'No' : hasValue(value) ? esc(value) : missing;
    if (field === 'status') {
        const tones = { ACTIVE: 'active', PAID: 'paid', CANCELLED: 'cancelled' };
        const tone = Object.hasOwn(tones, value) ? tones[value] : 'unknown';
        return `<span class="payroll-status payroll-status-${tone}">${readable(value)}</span>`;
    }
    if (field === 'deduction_type') return `${readable(value)}<span class="payroll-meta">Installment: ${money(row.installment_amount)}</span>`;
    if (field === 'default_value') return row.computation_type === 'FIXED' ? money(value) : `${hasValue(value) ? esc(value) : missing}<span class="payroll-meta">Stored value</span>`;
    if (['amount', 'salary_rate', 'remaining_balance'].includes(field)) {
        const unit = field === 'salary_rate' && hasValue(value) ? { MONTHLY: 'month', DAILY: 'day', HOURLY: 'hour' }[row.salary_type] : null;
        return `<span class="payroll-amount">${money(value)}</span>${unit ? `<span class="payroll-meta">/ ${unit}</span>` : ''}`;
    }
    return readable(value);
}
function csrfToken() { return $('#addRecordForm [name="csrf_token"]').val(); }
function formDataWithoutId(form) { return $.param($(form).serializeArray().filter(item => item.name !== 'id')); }
function resetForm(formSelector, fields) {
    const $form = $(formSelector);
    $form[0].reset();
    $form.find('select').removeData('selected-value');
    fields.forEach(field => { if (field.default !== undefined) $form.find(`[name="${field.name}"]`).val(field.default); });
    $form.find('[name="id"]').val('');
}
function message($modal, text, isError = false) {
    $modal.find('.payroll-dialog-feedback').text(text || '').attr('role', isError ? 'alert' : 'status').toggleClass('payroll-error', isError);
}
function updateSubmit($modal) {
    const form = $modal.find('form')[0];
    const needsOptions = form && $(form).find('.employee-select').length;
    const unavailable = needsOptions && (!optionsReady.employees || !optionsReady.types);
    $modal.find('[type="submit"]').prop('disabled', !!($modal.data('pending') || $modal.data('uncertain') || $modal.data('loading') || $modal.data('load-failed') || unavailable));
}
function setPending($modal, pending) {
    $modal.data('pending', pending).attr('aria-busy', String(pending));
    $modal.find('.payroll-form-fields').prop('inert', pending);
    $modal.find('[type="submit"], .payroll-confirm-delete').each(function () {
        const $button = $(this);
        if (!$button.data('ready-label')) $button.data('ready-label', $button.text());
        $button.text(pending ? ($modal.is('[id^="edit"]') ? 'Updating…' : $modal.is('[id^="delete"]') ? 'Deleting…' : 'Saving…') : $button.data('ready-label'));
        if ($button.hasClass('payroll-confirm-delete')) $button.prop('disabled', pending || !!$modal.data('uncertain'));
    });
    updateSubmit($modal);
}
function prepareModal(id, trigger) {
    const $modal = $('#' + id);
    $modal.data({ origin: trigger || document.activeElement, pending: false, uncertain: false, 'allow-close': false, 'load-failed': false });
    $modal.find('.payroll-discard').addClass('d-none');
    message($modal, '');
    return $modal;
}
function rememberForm($modal) { $modal.data('initial', $modal.find('form').serialize()); }
function fillForm(formSelector, data) {
    const $form = $(formSelector);
    $form.find('[name="id"]').val(data.id || '');
    Object.keys(data).forEach(key => {
        const $field = $form.find(`[name="${key}"]`);
        if ($field.length) {
            $field.val(data[key]);
        }
    });
}
function contextFor(row, mode) {
    if (!row) return mode === 'type' ? payrollConfig.typeLabel : payrollConfig.singular;
    return mode === 'type' ? `${row.name ?? 'Unnamed type'}${hasValue(row.code) ? ' (' + row.code + ')' : ''}` : `${payrollConfig.singular.toLowerCase()} for ${row.employee_name || row.employee_no || 'employee'}`;
}
function showDelete(mode, id, trigger) {
    const row = rowCache[mode].get(String(id));
    if (!row) return;
    const $modal = prepareModal(mode === 'record' ? 'deleteRecordModal' : 'deleteTypeModal', trigger);
    if (mode === 'record') pendingRecordDeleteId = id; else pendingTypeDeleteId = id;
    const question = mode === 'type' ? `Delete ${contextFor(row, mode)}?` : `Delete the ${row.type_name || payrollConfig.singular} record for ${row.employee_name || row.employee_no || 'this employee'}?`;
    $modal.find('.payroll-delete-context').text(question);
    $modal.find('.payroll-delete-detail').html(mode === 'record' ? money(row.amount ?? row.salary_rate) : esc(row.code ?? ''));
    $modal.find('.payroll-confirm-delete').prop('disabled', false);
    bootstrap.Modal.getOrCreateInstance($modal[0]).show(trigger);
}
function setupFormGroups($form) {
    const $grid = $form.find('.payroll-form-fields');
    const isType = $form.is('#addTypeForm, #editTypeForm');
    const groups = isType ? [[payrollConfig.typeLabel + ' Configuration', payrollConfig.typeFields.map(f => f.name)]]
        : isCashAdvance ? [['Employee & Advance', ['employee_id', 'amount']], ['Repayment', ['deduction_type', 'installment_amount']], ['Balance & Status', ['remaining_balance', 'status']]]
        : isCompensation ? [['Employee', ['employee_id']], ['Pay Arrangement', ['salary_type', 'payroll_frequency']], ['Salary Rate', ['salary_rate']], ['Effective Period', ['effective_from', 'effective_to']]]
        : [['Employee & ' + payrollConfig.singular, ['employee_id', payrollConfig.recordFields.find(f => f.input === 'type').name]], [payrollConfig.recordFields.some(f => f.name === 'is_recurring') ? 'Amount & Recurrence' : 'Financial Information', ['amount', 'is_recurring']], ['Effective Period', ['effective_from', 'effective_to']]];
    groups.forEach(([label, fields]) => {
        const $section = $('<fieldset class="payroll-form-section col-12"><legend></legend><div class="row g-3"></div></fieldset>');
        $section.find('legend').text(label);
        fields.forEach(name => {
            const $cell = $grid.find(`[name="${name}"]`).closest('[class^="col-md-"]');
            $cell.removeClass('col-md-4 col-md-6').addClass('col-md-6');
            $section.children('.row').append($cell);
        });
        $grid.append($section);
    });
    $form.find('[name="salary_type"], [name="payroll_frequency"], [name="deduction_type"], [name="status"], [name="computation_type"]').find('option').each(function () {
        if (hasValue(this.value)) $(this).html(readable(this.value));
    });
    $form.find('[name="is_recurring"] option').each(function () {
        if (this.value === '1') this.textContent = 'Recurring';
        if (this.value === '0') this.textContent = 'One-time';
    });
    $form.find('[name="effective_to"]').closest('.payroll-field').append('<span class="payroll-field-help">Optional. Leave blank if no end date is configured.</span>');
    $form.find('[name="default_value"]').closest('.payroll-field').append('<span class="payroll-field-help">Use the configured stored value. Percentage units depend on your payroll configuration.</span>');
    if (isCashAdvance) $form.find('[name="remaining_balance"]').closest('.payroll-field').append('<span class="payroll-field-help">Optional. Review the existing balance before changing it.</span>');
}
function configureSort(mode) {
    const columns = mode === 'record' ? payrollConfig.recordColumns : payrollConfig.typeColumns;
    const options = columns.map(c => ({ key: c.sort, label: c.label === 'Type' ? payrollConfig.typeLabel : c.label }));
    if (mode === 'record') {
        options.splice(1, 0, { key: 'employee_no', label: 'Employee Number' });
        if (payrollConfig.hasTypes) options.push({ key: 'type_code', label: payrollConfig.typeLabel + ' Code' });
    }
    $('#' + mode + 'SortBy').html(options.map(o => `<option value="${esc(o.key)}">${esc(o.label)}</option>`).join(''));
    $('#' + mode + 'SortBy, #' + mode + 'SortDirection').on('change', function () {
        const state = mode === 'record' ? recordState : typeState;
        state.sortBy = $('#' + mode + 'SortBy').val();
        state.sortDir = $('#' + mode + 'SortDirection').val();
        if (mode === 'record') getAllRecords(1); else getAllTypes(1);
    });
    syncSort(mode);
}
function syncSort(mode) {
    const state = mode === 'record' ? recordState : typeState;
    $('#' + mode + 'SortBy').val(state.sortBy);
    $('#' + mode + 'SortDirection').val(state.sortDir);
    const $headers = $(mode === 'record' ? '#recordsTable th' : '#typesTable th');
    $headers.removeAttr('aria-sort');
    $headers.find('.payroll-sort-indicator').text('↕');
    $headers.find('.' + mode + '-sort').each(function () {
        const active = $(this).data('sort') === state.sortBy;
        if (active) $(this).closest('th').attr('aria-sort', state.sortDir === 'ASC' ? 'ascending' : 'descending');
        $(this).find('.payroll-sort-indicator').text(active ? (state.sortDir === 'ASC' ? '↑' : '↓') : '↕');
    });
}
$(document).ready(function () {
    $('.payroll-items-dialog').each(function () {
        const $modal = $(this);
        $modal.find('.modal-body').prepend('<p class="payroll-dialog-context"></p><p class="payroll-dialog-feedback" role="status" aria-live="polite"></p>');
        if ($modal.find('form').length) {
            setupFormGroups($modal.find('form'));
            $modal.find('.modal-body').append('<div class="payroll-discard d-none" role="group" aria-label="Unsaved changes"><p>Discard your unsaved changes?</p><button type="button" class="btn btn-primary payroll-stay">Stay Editing</button> <button type="button" class="btn btn-outline-danger payroll-discard-confirm">Discard Changes</button></div>');
        } else {
            $modal.find('.modal-body').append('<p class="payroll-delete-context"></p><p class="payroll-delete-detail payroll-amount"></p>');
            $modal.find('.btn-danger').addClass('payroll-confirm-delete');
        }
        $modal.on('hide.bs.modal', function (event) {
            if ($modal.data('pending')) { event.preventDefault(); return; }
            if (!$modal.data('allow-close') && $modal.find('form').length && $modal.data('initial') !== $modal.find('form').serialize() && !$modal.data('loading') && !$modal.data('load-failed')) {
                event.preventDefault();
                $modal.data('cancel-origin', document.activeElement);
                $modal.find('.payroll-discard').removeClass('d-none');
                $modal.find('.payroll-stay').trigger('focus');
            }
        });
        $modal.on('hidden.bs.modal', function () {
            $modal.data('detail-version', ($modal.data('detail-version') || 0) + 1);
            $modal.find('.payroll-discard').addClass('d-none');
            const origin = $modal.data('origin');
            const fallback = document.querySelector($('#types-tab').hasClass('active') ? '#addTypeAction' : '#addRecordAction');
            const target = origin && origin.isConnected && $(origin).is(':visible') ? origin : fallback;
            if (target) target.focus();
        });
        $modal.on('click', '.payroll-stay', function () {
            $modal.find('.payroll-discard').addClass('d-none');
            const target = $modal.data('cancel-origin');
            if (target && target.isConnected) target.focus();
        });
        $modal.on('click', '.payroll-discard-confirm', function () {
            $modal.data('allow-close', true);
            bootstrap.Modal.getInstance($modal[0]).hide();
        });
        $modal.on('shown.bs.modal', function () {
            const target = $modal.is('[id^="delete"]') ? $modal.find('[data-bs-dismiss]').last()[0] : $modal.find('input:visible, select:visible').first()[0];
            if (target) target.focus();
        });
        // Keep Tab at the dialog boundaries; Bootstrap continues to own the trap.
        $modal.on('keydown', function (event) {
            if (event.key !== 'Tab') return;
            const controls = $modal.find('a[href], button, input, select, textarea, [tabindex]')
                .filter(':visible:not(:disabled):not([tabindex="-1"])')
                .filter(function () { return !this.closest('[inert]'); }).toArray();
            const first = controls[0], last = controls[controls.length - 1];
            if (!first) { event.preventDefault(); this.focus(); return; }
            if (event.shiftKey && (event.target === first || event.target === this)) {
                event.preventDefault(); last.focus();
            } else if (!event.shiftKey && event.target === last) {
                event.preventDefault(); first.focus();
            }
        });
    });
    ['Record', 'Type'].forEach(kind => {
        $('#add' + kind + 'Modal').on('show.bs.modal', function (event) {
            const $modal = prepareModal(this.id, event.relatedTarget);
            resetForm('#add' + kind + 'Form', kind === 'Record' ? payrollConfig.recordFields : payrollConfig.typeFields || []);
            $modal.data({ loading: false, 'load-failed': false });
            $modal.find('.payroll-form-fields').removeClass('d-none');
            $modal.find('.payroll-dialog-context').text('Fields marked * are required.');
            rememberForm($modal);
            updateSubmit($modal);
            if (kind === 'Record' && (!optionsReady.employees || !optionsReady.types)) message($modal, 'Employee or type choices are unavailable or still loading. Close and reopen to retry if needed.');
        });
    });
    // Retry only read-only option requests; never retry a mutation automatically.
    $('#addRecordModal').on('show.bs.modal', function () {
        if (!optionsReady.employees) loadEmployees();
        if (!optionsReady.types) loadPayrollTypes();
    });
    $('#payrollItemTabs').on('shown.bs.tab', function (event) {
        const types = event.target.id === 'types-tab';
        $('#addTypeAction').toggleClass('d-none', !types);
        $('#addRecordAction').toggleClass('d-none', types);
    });
    configureSort('record');
    if (payrollConfig.hasTypes) configureSort('type');
    $('#recordSearchForm, #typeSearchForm').on('submit', function (event) {
        event.preventDefault();
        if (this.id === 'recordSearchForm') { recordState.search = $('#recordSearchInput').val(); getAllRecords(1); }
        else { typeState.search = $('#typeSearchInput').val(); getAllTypes(1); }
    });
    $('#clearRecordSearchBtn').on('click', function () { $('#recordSearchInput').val(''); recordState.search = ''; getAllRecords(1); });
    $('#clearTypeSearchBtn').on('click', function () { $('#typeSearchInput').val(''); typeState.search = ''; getAllTypes(1); });
    $('#addRecordForm').on('submit', function (event) { event.preventDefault(); addRecord(this); });
    $('#editRecordForm').on('submit', function (event) { event.preventDefault(); updateRecord(this); });
    $('#addTypeForm').on('submit', function (event) { event.preventDefault(); addType(this); });
    $('#editTypeForm').on('submit', function (event) { event.preventDefault(); updateType(this); });
    $(document).on('click', '.record-sort, .type-sort', function () {
        const mode = $(this).hasClass('record-sort') ? 'record' : 'type';
        const state = mode === 'record' ? recordState : typeState;
        const sortBy = $(this).data('sort');
        state.sortDir = state.sortBy === sortBy && state.sortDir === 'ASC' ? 'DESC' : 'ASC';
        state.sortBy = sortBy;
        if (mode === 'record') getAllRecords(1); else getAllTypes(1);
    });
    $(document).on('click', '.record-page-btn, .type-page-btn', function (event) {
        event.preventDefault();
        if ($(this).hasClass('record-page-btn')) getAllRecords($(this).data('page')); else getAllTypes($(this).data('page'));
    });
    $(document).on('click', '.editRecordBtn', function () { getRecord($(this).data('id'), this); });
    $(document).on('click', '.editTypeBtn', function () { getType($(this).data('id'), this); });
    $(document).on('click', '.deleteRecordBtn', function () { showDelete('record', $(this).data('id'), this); });
    $(document).on('click', '.deleteTypeBtn', function () { showDelete('type', $(this).data('id'), this); });
    $('#confirmDeleteRecordBtn').on('click', function () { if (pendingRecordDeleteId) deleteRecord(pendingRecordDeleteId); });
    $('#confirmDeleteTypeBtn').on('click', function () { if (pendingTypeDeleteId) deleteType(pendingTypeDeleteId); });
    $(document).on('click', '.payroll-retry-list', function () { if ($(this).data('mode') === 'record') getAllRecords(recordState.page); else getAllTypes(typeState.page); });
    loadEmployees();
    if (payrollConfig.hasTypes) { loadPayrollTypes(); getAllTypes(1); }
    getAllRecords(1);
});
function optionStatus() {
    $('.payroll-items-dialog').each(function () {
        const $modal = $(this);
        const waiting = $modal.data('waiting-data');
        if (waiting && optionsReady.employees && optionsReady.types) {
            $modal.removeData('waiting-data');
            editLoaded(waiting.context, waiting.res);
        }
        updateSubmit($modal);
        if (optionsReady.employees && optionsReady.types && $modal.find('.payroll-dialog-feedback').text().startsWith('Employee or type choices')) message($modal, '');
    });
}
function loadEmployees() {
    $.ajax({
        url: payrollConfig.urls.employees,
        method: 'GET', dataType: 'json',
        success: function (res) {
            if ((!res || res.success !== true) || !Array.isArray(res.data)) return;
            employees = res.data;
            renderEmployeeOptions();
            optionsReady.employees = true;
            optionStatus();
        },
        error: function () { optionsReady.employees = false; optionStatus(); }
    });
}
function replaceOptions(selector, options) {
    $(selector).each(function () {
        const selection = $(this).val();
        $(this).html(options).val(selection);
    });
}
function renderEmployeeOptions() {
    let options = '<option value="">Select Employee</option>';
    employees.forEach(employee => {
        const middle = employee.middlename ? ' ' + employee.middlename : '';
        const label = `${employee.employee_no || 'NO ID'} - ${employee.lastname}, ${employee.firstname}${middle}`;
        options += `<option value="${esc(employee.id)}">${esc(label)}</option>`;
    });
    replaceOptions('.employee-select', options);
}
function loadPayrollTypes() {
    if (!payrollConfig.hasTypes) return;
    $.ajax({
        url: payrollConfig.urls.typeOptions,
        method: 'GET', dataType: 'json',
        success: function (res) {
            if ((!res || res.success !== true) || !Array.isArray(res.data)) return;
            payrollTypes = res.data;
            renderTypeOptions();
            optionsReady.types = true;
            optionStatus();
        },
        error: function () { optionsReady.types = false; optionStatus(); }
    });
}
function renderTypeOptions() {
    let options = '<option value="">Select Type</option>';
    payrollTypes.forEach(type => { options += `<option value="${esc(type.id)}">${esc(`${type.code || ''} - ${type.name || ''}`)}</option>`; });
    replaceOptions('.payroll-type-select', options);
}
function listLoading(mode) {
    const table = mode === 'record' ? '#recordsTable' : '#typesTable';
    const count = (mode === 'record' ? recordColumns : typeColumns).length + 1;
    $(table).attr('aria-busy', 'true').find('tbody').html(`<tr><td colspan="${count}" class="payroll-table-state"><span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Loading ${esc(mode === 'record' ? payrollConfig.title.toLowerCase() : 'types')}…</td></tr>`);
    $(mode === 'record' ? '#recordsPagination' : '#typesPagination').empty();
    $('#' + mode + 'PageInfo').empty();
    $('#' + mode + 'ListFeedback').text('Loading…').removeClass('payroll-error').attr('role', 'status');
    rowCache[mode].clear();
    syncSort(mode);
}
function listError(mode, text) {
    const table = mode === 'record' ? '#recordsTable' : '#typesTable';
    const count = (mode === 'record' ? recordColumns : typeColumns).length + 1;
    $(table).find('tbody').html(`<tr><td colspan="${count}" class="payroll-table-state">${esc(text)} <button type="button" class="btn btn-outline-primary payroll-retry-list" data-mode="${mode}">Retry loading</button></td></tr>`);
    $('#' + mode + 'ListFeedback').text(text).addClass('payroll-error').attr('role', 'alert');
}
function listSuccess(mode, res) {
    if ((!res || res.success !== true) || !Array.isArray(res.data)) { listError(mode, res?.message || 'Could not load this list.'); return; }
    const state = mode === 'record' ? recordState : typeState;
    const current = res.currentPage || 1, total = res.totalPages || 1;
    renderRows(mode === 'record' ? '#recordsTable tbody' : '#typesTable tbody', res.data, mode === 'record' ? recordColumns : typeColumns, mode);
    renderPagination(mode === 'record' ? '#recordsPagination' : '#typesPagination', current, total, mode + '-page-btn');
    $('#' + mode + 'PageInfo').text(`${res.data.length} ${res.data.length === 1 ? 'record' : 'records'} on this page · Page ${current} of ${total}`);
    $('#' + mode + 'ListFeedback').text(state.search ? `Search applied: “${state.search}”` : '');
}
function getAllRecords(page) {
    recordState.page = page;
    const version = ++listVersions.record;
    listLoading('record');
    $.ajax({
        url: payrollConfig.urls.recordGetAll,
        method: 'GET', dataType: 'json',
        data: { page: page, search: recordState.search, sort_by: recordState.sortBy, sort_dir: recordState.sortDir },
        success: function (res) { if (version === listVersions.record) listSuccess('record', res); },
        error: function () { if (version === listVersions.record) listError('record', 'Failed to load records.'); },
        complete: function () { if (version === listVersions.record) $('#recordsTable').attr('aria-busy', 'false'); }
    });
}
function getAllTypes(page) {
    if (!payrollConfig.hasTypes) return;
    typeState.page = page;
    const version = ++listVersions.type;
    listLoading('type');
    $.ajax({
        url: payrollConfig.urls.typeGetAll,
        method: 'GET', dataType: 'json',
        data: { page: page, search: typeState.search, sort_by: typeState.sortBy, sort_dir: typeState.sortDir },
        success: function (res) { if (version === listVersions.type) listSuccess('type', res); },
        error: function () { if (version === listVersions.type) listError('type', 'Failed to load types.'); },
        complete: function () { if (version === listVersions.type) $('#typesTable').attr('aria-busy', 'false'); }
    });
}
function renderRows(selector, rows, columns, mode) {
    rowCache[mode].clear();
    if (!rows.length) {
        const state = mode === 'record' ? recordState : typeState;
        $(selector).html(`<tr><td class="payroll-table-state" colspan="${columns.length + 1}">${state.search ? 'No matching records. Try another search or Clear.' : 'No records yet.'}</td></tr>`);
        return;
    }
    let html = '';
    rows.forEach(row => {
        rowCache[mode].set(String(row.id), row);
        html += '<tr>';
        columns.forEach((field, index) => {
            const numeric = ['amount', 'salary_rate', 'remaining_balance', 'default_value'].includes(field);
            const tag = index === 0 ? 'th' : 'td';
            html += `<${tag}${index === 0 ? ' scope="row"' : ''} class="${numeric ? 'payroll-numeric' : 'payroll-text-cell'}${field === 'remaining_balance' ? ' payroll-balance' : ''}">${displayValue(row, field, mode)}</${tag}>`;
        });
        const context = contextFor(row, mode);
        html += `<td class="payroll-actions"><div class="payroll-row-actions"><button type="button" class="btn btn-outline-primary ${mode === 'record' ? 'editRecordBtn' : 'editTypeBtn'}" data-id="${esc(row.id)}" aria-label="${esc('Edit ' + context)}" title="${esc('Edit ' + context)}"><i class="bi bi-pencil-square" aria-hidden="true"></i><span>Edit</span></button><button type="button" class="btn btn-outline-danger ${mode === 'record' ? 'deleteRecordBtn' : 'deleteTypeBtn'}" data-id="${esc(row.id)}" aria-label="${esc('Delete ' + context)}" title="${esc('Delete ' + context)}"><i class="bi bi-trash" aria-hidden="true"></i><span>Delete</span></button></div></td></tr>`;
    });
    $(selector).html(html);
}
function renderPagination(selector, currentPage, totalPages, buttonClass) {
    const current = Number(currentPage), total = Number(totalPages);
    if (total <= 1) { $(selector).empty(); return; }
    const button = (page, label, disabled = false) => `<li class="page-item${disabled ? ' disabled' : ''}${page === current && typeof label === 'number' ? ' active' : ''}"><button type="button" class="page-link ${buttonClass}" data-page="${page}"${disabled ? ' disabled' : ''}${page === current && typeof label === 'number' ? ' aria-current="page"' : ''} aria-label="${typeof label === 'number' ? 'Page ' + page : label}">${label}</button></li>`;
    const pages = [...new Set([1, current - 1, current, current + 1, total])].filter(p => p >= 1 && p <= total).sort((a, b) => a - b);
    let html = button(Math.max(1, current - 1), 'Previous', current <= 1), previous = 0;
    pages.forEach(page => { if (previous && page - previous > 1) html += '<li class="page-item disabled"><span class="page-link">…</span></li>'; html += button(page, page); previous = page; });
    html += button(Math.min(total, current + 1), 'Next', current >= total);
    $(selector).html(html);
}
function openEdit(mode, id, trigger) {
    const kind = mode === 'record' ? 'Record' : 'Type';
    const $modal = prepareModal('edit' + kind + 'Modal', trigger);
    $modal.removeData('waiting-data');
    resetForm('#edit' + kind + 'Form', mode === 'record' ? payrollConfig.recordFields : payrollConfig.typeFields);
    $modal.data('loading', true);
    $modal.find('.payroll-form-fields').addClass('d-none');
    $modal.find('.payroll-dialog-context').text(contextFor(rowCache[mode].get(String(id)), mode));
    rememberForm($modal);
    message($modal, 'Loading selected ' + (mode === 'record' ? payrollConfig.singular.toLowerCase() : 'type') + '…');
    updateSubmit($modal);
    const version = ($modal.data('detail-version') || 0) + 1;
    $modal.data('detail-version', version);
    bootstrap.Modal.getOrCreateInstance($modal[0]).show(trigger);
    if (mode === 'record') {
        if (!optionsReady.employees) loadEmployees();
        if (!optionsReady.types) loadPayrollTypes();
    }
    return { $modal, version, kind };
}
function editLoaded(context, res) {
    const { $modal, version, kind } = context;
    if ($modal.data('detail-version') !== version) return;
    if (res && res.success === true && kind === 'Record' && (!optionsReady.employees || !optionsReady.types)) {
        $modal.data('waiting-data', { context, res });
        message($modal, 'Employee or type choices are still loading or unavailable. Close and reopen to retry if needed.');
        return;
    }
    $modal.data('loading', false);
    if ((!res || res.success !== true) || !res.data || typeof res.data !== 'object' || !res.data.id) {
        $modal.data('load-failed', true);
        message($modal, res?.message || 'The selected record could not be loaded. Close and try again.', true);
    } else {
        fillForm('#edit' + kind + 'Form', res.data);
        $modal.find('.payroll-form-fields').removeClass('d-none');
        message($modal, kind === 'Record' && (!optionsReady.employees || !optionsReady.types) ? 'Employee or type choices are still loading or unavailable.' : '');
        rememberForm($modal);
        $modal.find('input:visible, select:visible').first().trigger('focus');
    }
    updateSubmit($modal);
}
function editFailed(context) {
    if (context.$modal.data('detail-version') !== context.version) return;
    context.$modal.data({ loading: false, 'load-failed': true });
    message(context.$modal, 'Failed to load the selected record. Close and try again.', true);
    updateSubmit(context.$modal);
}
function getRecord(id, trigger) {
    const context = openEdit('record', id, trigger);
    $.ajax({ url: payrollConfig.urls.recordGetById + id, method: 'GET', dataType: 'json', success: function (res) { editLoaded(context, res); }, error: function () { editFailed(context); } });
}
function getType(id, trigger) {
    const context = openEdit('type', id, trigger);
    $.ajax({ url: payrollConfig.urls.typeGetById + id, method: 'GET', dataType: 'json', success: function (res) { editLoaded(context, res); }, error: function () { editFailed(context); } });
}
function beginMutation($modal) {
    updateSubmit($modal);
    if ($modal.data('pending') || $modal.data('uncertain') || $modal.data('loading') || $modal.data('load-failed') || $modal.find('[type="submit"]').is(':disabled')) return false;
    setPending($modal, true);
    message($modal, $modal.is('[id^="delete"]') ? 'Deleting…' : 'Saving changes…');
    return true;
}
function mutationUnknown($modal) {
    $modal.data('uncertain', true);
    message($modal, 'The result could not be confirmed. Your request may have been processed. Do not submit again until you close this dialog and check the latest records.', true);
}
function mutationResult($modal, res, refresh) {
    if (res && res.success === true) {
        setPending($modal, false);
        $modal.data('allow-close', true);
        showToast(res.message || 'Changes saved.', 'success');
        $('.payroll-items-header').find('.payroll-workspace-feedback').remove();
        $('<p class="payroll-workspace-feedback" role="status"></p>').text(res.message || 'Changes saved.').appendTo('.payroll-items-header');
        bootstrap.Modal.getInstance($modal[0]).hide();
        refresh();
    } else if (res && res.success === false) {
        message($modal, res.message || 'The server rejected this request. Review your entries.', true);
    } else mutationUnknown($modal);
}
function mutationError($modal, xhr) {
    const res = xhr.responseJSON;
    if (res && res.success === false) message($modal, res.message || 'The server rejected this request.', true);
    else mutationUnknown($modal);
}
function addRecord(form) {
    const $modal = $('#addRecordModal');
    if (!beginMutation($modal)) return;
    $.ajax({
        url: payrollConfig.urls.recordCreate, method: 'POST', dataType: 'json', data: $(form).serialize(),
        success: function (res) { mutationResult($modal, res, () => getAllRecords(1)); },
        error: function (xhr) { mutationError($modal, xhr); }, complete: function () { setPending($modal, false); }
    });
}
function updateRecord(form) {
    const id = $(form).find('[name="id"]').val();
    const $modal = $('#editRecordModal');
    if (!beginMutation($modal)) return;
    $.ajax({
        url: payrollConfig.urls.recordUpdate + id, method: 'POST', dataType: 'json', data: formDataWithoutId(form),
        success: function (res) { mutationResult($modal, res, () => getAllRecords(recordState.page)); },
        error: function (xhr) { mutationError($modal, xhr); }, complete: function () { setPending($modal, false); }
    });
}
function deleteRecord(id) {
    const $modal = $('#deleteRecordModal');
    if (!beginMutation($modal)) return;
    $.ajax({
        url: payrollConfig.urls.recordDelete + id, method: 'POST', dataType: 'json', data: { csrf_token: csrfToken() },
        success: function (res) { mutationResult($modal, res, () => { pendingRecordDeleteId = null; getAllRecords(recordState.page); }); },
        error: function (xhr) { mutationError($modal, xhr); }, complete: function () { setPending($modal, false); }
    });
}
function addType(form) {
    const $modal = $('#addTypeModal');
    if (!beginMutation($modal)) return;
    $.ajax({
        url: payrollConfig.urls.typeCreate, method: 'POST', dataType: 'json', data: $(form).serialize(),
        success: function (res) { mutationResult($modal, res, () => { loadPayrollTypes(); getAllTypes(1); }); },
        error: function (xhr) { mutationError($modal, xhr); }, complete: function () { setPending($modal, false); }
    });
}
function updateType(form) {
    const id = $(form).find('[name="id"]').val();
    const $modal = $('#editTypeModal');
    if (!beginMutation($modal)) return;
    $.ajax({
        url: payrollConfig.urls.typeUpdate + id, method: 'POST', dataType: 'json', data: formDataWithoutId(form),
        success: function (res) { mutationResult($modal, res, () => { loadPayrollTypes(); getAllTypes(typeState.page); }); },
        error: function (xhr) { mutationError($modal, xhr); }, complete: function () { setPending($modal, false); }
    });
}
function deleteType(id) {
    const $modal = $('#deleteTypeModal');
    if (!beginMutation($modal)) return;
    $.ajax({
        url: payrollConfig.urls.typeDelete + id, method: 'POST', dataType: 'json', data: { csrf_token: csrfToken() },
        success: function (res) { mutationResult($modal, res, () => { pendingTypeDeleteId = null; loadPayrollTypes(); getAllTypes(typeState.page); }); },
        error: function (xhr) { mutationError($modal, xhr); }, complete: function () { setPending($modal, false); }
    });
}

</script>
</body>
</html>
