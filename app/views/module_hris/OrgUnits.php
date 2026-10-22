<?php
$useDataTables = false;
include '../app/views/partials/admin-header.php';
include '../app/views/module_hris/partials/hris_nav.php';
?>

<div class="container-fluid py-4 management-page hris-page organization-page">
    <div class="card shadow-sm management-card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div class="org-page-intro"><p class="org-eyebrow">Organization</p><h1 class="h5 mb-0" id="officesHeading">Offices</h1><p class="org-page-description">Organize departments, teams, sections and units across your clinic.</p></div>

            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-outline-secondary btn-sm" id="expandAllBtn">
                    <i class="bi bi-arrows-expand" aria-hidden="true"></i> Expand All
                </button>
                <button class="btn btn-outline-secondary btn-sm" id="collapseAllBtn">
                    <i class="bi bi-arrows-collapse" aria-hidden="true"></i> Collapse All
                </button>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addOfficeModal" id="addOfficeButton">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i> Add Office
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="org-feedback d-none" id="orgPageFeedback" role="status" aria-live="polite" aria-atomic="true"></div>
            <div class="org-toolbar">
                <div class="org-search"><label class="form-label" for="officeSearch">Search offices</label><input type="search" class="form-control" id="officeSearch" placeholder="Search by name, code or type" aria-controls="orgUnitsTree" disabled></div>
                <button class="btn btn-outline-secondary" type="button" id="clearOfficeSearch" disabled>Clear Search</button>
            </div>
            <p class="org-results" id="officeResults" role="status" aria-live="polite"></p>
            <div id="orgUnitsTree" aria-busy="true"><p class="org-state" role="status">Loading offices…</p></div>
        </div>
    </div>
</div>

<!-- Add Office -->
<div class="modal fade hris-dialog organization-dialog" id="addOfficeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addOfficeModalTitle">
    <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="addOfficeForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="addOfficeModalTitle">Add Organizational Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="org-form-feedback d-none" role="status" aria-live="polite" aria-atomic="true"></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="parentSelect" class="form-label">Parent Unit</label>
                            <select class="form-select" name="parent_id" id="parentSelect">
                                <option value="">-- None (Top Level) --</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="addOfficeForm_type" class="form-label">Type</label>
                            <select id="addOfficeForm_type" class="form-select" name="type">
                                <option value="DEPARTMENT">DEPARTMENT</option>
                                <option value="TEAM">TEAM</option>
                                <option value="SECTION">SECTION</option>
                                <option value="UNIT">UNIT</option>
                            </select>
                        </div>

                        <div class="col-md-8">
                            <label for="addOfficeForm_name" class="form-label">Name</label>
                            <input id="addOfficeForm_name" type="text" class="form-control" name="name" placeholder="e.g. Clinical Operations">
                        </div>

                        <div class="col-md-4">
                            <label for="addOfficeForm_code" class="form-label">Code</label>
                            <input id="addOfficeForm_code" type="text" class="form-control" name="code" placeholder="e.g. CLINICAL">
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

<!-- Edit Office -->
<div class="modal fade hris-dialog organization-dialog" id="editOfficeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editOfficeModalTitle">
    <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="editOfficeForm">
                <input type="hidden" name="id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="editOfficeModalTitle">Edit Organizational Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="org-form-feedback d-none" role="status" aria-live="polite" aria-atomic="true"></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="editParentSelect" class="form-label">Parent Unit</label>
                            <input class="form-control" id="editParentSelect" readonly aria-describedby="officeParentHelp">
                            <p class="form-text" id="officeParentHelp">Current parent. Moving an existing unit is unavailable.</p>
                        </div>

                        <div class="col-md-6">
                            <label for="editOfficeForm_type" class="form-label">Type</label>
                            <select id="editOfficeForm_type" class="form-select" name="type">
                                <option value="DEPARTMENT">DEPARTMENT</option>
                                <option value="TEAM">TEAM</option>
                                <option value="SECTION">SECTION</option>
                                <option value="UNIT">UNIT</option>
                            </select>
                        </div>

                        <div class="col-md-8">
                            <label for="editOfficeForm_name" class="form-label">Name</label>
                            <input id="editOfficeForm_name" type="text" class="form-control" name="name" placeholder="e.g. Clinical Operations">
                        </div>

                        <div class="col-md-4">
                            <label for="editOfficeForm_code" class="form-label">Code</label>
                            <input id="editOfficeForm_code" type="text" class="form-control" name="code" placeholder="e.g. CLINICAL">
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

<!-- Record-specific frontend confirmation; the existing delete request is unchanged. -->
<div class="modal fade hris-dialog organization-dialog" id="deleteOfficeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteOfficeModalTitle" aria-describedby="deleteOfficeDescription">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="deleteOfficeModalTitle">Delete Unit</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button></div>
        <div class="modal-body"><p id="deleteOfficeDescription">Delete <strong id="deleteOfficeName"></strong>? This action cannot be undone.</p></div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-danger" id="confirmDeleteOfficeBtn">Delete Unit</button></div>
    </div></div>
</div>

<?php include '../app/views/partials/admin-footer.php'; ?>

<!-- View Office Employees -->
<div class="modal fade hris-dialog organization-dialog" id="viewUnitEmployeesModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="viewUnitEmployeesHeading">
    <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewUnitEmployeesHeading">
                    Assigned Employees — <span id="viewUnitEmployeesTitle" class="fw-semibold">-</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
            </div>

            <div class="modal-body">
                    <div class="org-form-feedback d-none" role="status" aria-live="polite" aria-atomic="true"></div>
                <p class="form-text">Employees assigned directly to this unit are listed below. Descendant units are not included.</p>
                <p class="hris-scroll-hint" id="unitEmployeesTableScrollHelp">Use horizontal scrolling when columns extend beyond this view.</p>
                <div class="table-responsive hris-table-region" tabindex="0" role="region" aria-labelledby="viewUnitEmployeesHeading" aria-describedby="unitEmployeesTableScrollHelp">
                    <table class="table table-hover align-middle mb-0 hris-data-table" id="unitEmployeesTable">
                        <caption class="visually-hidden">Employees assigned directly to the selected organizational unit.</caption>
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Employee No</th>
                                <th scope="col">Name</th>
                                <th scope="col">Position</th>
                                <th scope="col">Branch</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let orgUnitsFlat = [];
let expandedOffices = new Set();
let searchExpandedOffices = new Set();
let officeSearchQuery = '';
let officeIdToDelete = null;
let employeeRead = null;

$(document).ready(function () {
    initOrganizationUX();
    getAllOrgUnits();

    $('#addOfficeForm').on('submit', function (e) {
        e.preventDefault();
        addOffice(this);
    });

    $('#editOfficeForm').on('submit', function (e) {
        e.preventDefault();
        updateOffice(this);
    });

    $('#addOfficeButton').on('click', function () { $('#addOfficeModal').data('parent', ''); });
    $('#addOfficeModal').on('show.bs.modal', function () {
        loadParentOptions('#parentSelect', $(this).data('parent') || '');
    });
    $(document).on('click', '.addChildOfficeBtn', function () {
        $('#addOfficeModal').data('parent', String($(this).data('id')));
        bootstrap.Modal.getOrCreateInstance(document.getElementById('addOfficeModal')).show();
    });
    $(document).on('click', '.editOfficeBtn', function () { getOffice($(this).data('id')); });
    $(document).on('click', '.viewUnitEmployeesBtn', function () {
        viewUnitEmployees($(this).data('id'), $(this).attr('data-name'));
    });
    $(document).on('click', '.deleteOfficeBtn', function () {
        officeIdToDelete = $(this).data('id');
        $('#deleteOfficeName').text($(this).attr('data-name'));
        bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteOfficeModal')).show();
    });
    $('#confirmDeleteOfficeBtn').on('click', function () {
        if (officeIdToDelete !== null) deleteOffice(officeIdToDelete);
    });
    $(document).on('click', '.org-toggle', function () {
        const id = String($(this).data('id'));
        const expanded = officeSearchQuery ? searchExpandedOffices : expandedOffices;
        if (expanded.has(id)) expanded.delete(id); else expanded.add(id);
        const open = expanded.has(id);
        $(document.getElementById('children-' + id)).toggleClass('d-none', !open);
        $(this).attr('aria-expanded', String(open)).find('i')
            .toggleClass('bi-chevron-right', !open).toggleClass('bi-chevron-down', open);
    });
    $('#expandAllBtn, #collapseAllBtn').on('click', function () {
        const expanded = officeSearchQuery ? searchExpandedOffices : expandedOffices;
        expanded.clear();
        if (this.id === 'expandAllBtn') orgUnitsFlat.forEach(unit => expanded.add(String(unit.id)));
        renderTree(orgUnitsFlat);
    });
    $('#officeSearch').on('input', function () {
        officeSearchQuery = this.value.trim().toLocaleLowerCase();
        searchExpandedOffices = officeSearchAncestors(orgUnitsFlat, officeSearchQuery);
        renderTree(orgUnitsFlat);
    });
    $('#clearOfficeSearch').on('click', function () {
        $('#officeSearch').val('');
        officeSearchQuery = '';
        renderTree(orgUnitsFlat);
        $('#officeSearch').trigger('focus');
    });
});

function esc(str) {
    return String(str ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    }[c]));
}

function viewUnitEmployees(id, name) {
    if (employeeRead) employeeRead.abort();
    $('#viewUnitEmployeesTitle').text(name || '-');
    $('#unitEmployeesTable tbody').html('<tr><td colspan="5" class="org-state" role="status">Loading assigned employees…</td></tr>');
    $('#unitEmployeesTable').attr('aria-busy', 'true');
    bootstrap.Modal.getOrCreateInstance(document.getElementById('viewUnitEmployeesModal')).show();

    employeeRead = $.ajax({
        url: '/hris/offices/getunitemployees/' + id,
        type: 'GET',
        dataType: 'json',

        success: function (res) {
            $('#unitEmployeesTable').removeAttr('aria-busy');
            if (!res.success) {
                $('#unitEmployeesTable tbody').html('<tr><td colspan="5" class="org-state">Unable to load assigned employees. Close and try again.</td></tr>');
                return;
            }

            $('#viewUnitEmployeesTitle').text(res.unit?.name || name || '-');

            let employees = res.data || [];
            let rows = '';

            if (employees.length === 0) {
                rows = `
                    <tr>
                        <td colspan="5" class="text-center py-3">No employees assigned to this unit.</td>
                    </tr>
                `;
            } else {
                employees.forEach(emp => {
                    let fullName = `${emp.firstname ?? ''} ${emp.middlename ?? ''} ${emp.lastname ?? ''}`
                        .replace(/\s+/g, ' ')
                        .trim();

                    rows += `
                        <tr>
                            <td>${esc(emp.employee_no) || '-'}</td>
                            <td>${esc(fullName) || '-'}</td>
                            <td>${esc(emp.position) || '-'}</td>
                            <td>${esc(emp.branch_name) || '-'}</td>
                            <td>${esc(emp.employment_status) || '-'}</td>
                        </tr>
                    `;
                });
            }

            $('#unitEmployeesTable tbody').html(rows);
        },
        error: function (xhr, status) {
            if (status === 'abort') return;
            $('#unitEmployeesTable').removeAttr('aria-busy');
            $('#unitEmployeesTable tbody').html('<tr><td colspan="5" class="org-state">Unable to load assigned employees. Close and try again.</td></tr>');
        }
    });
}

function buildParentOptions(units, selected = '') {
    let html = '<option value="">-- None (Top Level) --</option>';

    units.forEach(unit => {
        html += `
            <option value="${esc(unit.id)}" ${unit.id == selected ? 'selected' : ''}>
                ${esc(unit.name)}
            </option>
        `;
    });

    return html;
}

function loadParentOptions(selector, selected = '') {
    $(selector).prop('disabled', true).html('<option value="">Loading parent units…</option>');
    $.ajax({
        url: '/hris/offices/getallunits',
        type: 'GET',
        dataType: 'json',

        success: function (res) {
            if (!res.success || !Array.isArray(res.data)) {
                orgFeedback('Unable to load parent units. Close and try again.', 'error', '#addOfficeForm');
                return;
            }
            $(selector).prop('disabled', false).html(buildParentOptions(res.data, selected));
        },

        error: function (xhr) {
            console.log(xhr.responseText);
            orgFeedback('Unable to load parent units. Close and try again.', 'error', '#addOfficeForm');
        }
    });
}

function getAllOrgUnits() {
    $('#orgUnitsTree').attr('aria-busy', 'true').html('<p class="org-state" role="status">Loading offices…</p>');
    $('#officeSearch, #clearOfficeSearch, #expandAllBtn, #collapseAllBtn').prop('disabled', true);
    $.ajax({
        url: '/hris/offices/getallunits',
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            $('#orgUnitsTree').removeAttr('aria-busy');
            if (!res.success || !Array.isArray(res.data)) { officeReadError(); return; }
            orgUnitsFlat = res.data;
            searchExpandedOffices = officeSearchAncestors(orgUnitsFlat, officeSearchQuery);
            renderTree(orgUnitsFlat);
            $('#officeSearch, #clearOfficeSearch, #expandAllBtn, #collapseAllBtn').prop('disabled', false);
        },
        error: officeReadError
    });
}
function officeReadError() {
    $('#orgUnitsTree').removeAttr('aria-busy').html('<p class="org-state" role="status">Unable to load offices. <button type="button" class="btn btn-outline-secondary" id="retryOffices">Try Again</button></p>');
    $('#officeResults').text('Offices could not be loaded.');
}
$(document).on('click', '#retryOffices', getAllOrgUnits);
function officeMatches(unit, query) {
    return [unit.name, unit.code, unit.type].some(value => String(value ?? '').toLocaleLowerCase().includes(query));
}
function officeSearchAncestors(units, query) {
    const byId = new Map(units.map(unit => [String(unit.id), unit]));
    const ancestors = new Set();
    if (!query) return ancestors;
    units.filter(unit => officeMatches(unit, query)).forEach(unit => {
        const seen = new Set([String(unit.id)]);
        let parent = byId.get(String(unit.parent_id));
        while (parent && !seen.has(String(parent.id))) {
            const id = String(parent.id);
            ancestors.add(id); seen.add(id);
            parent = byId.get(String(parent.parent_id));
        }
    });
    return ancestors;
}
function officeHighlight(value) {
    const text = String(value ?? '');
    const query = officeSearchQuery;
    if (!query) return esc(text);
    // Escape every segment separately; search input never becomes HTML or a RegExp.
    let output = '', start = 0, index;
    const lower = text.toLocaleLowerCase();
    while ((index = lower.indexOf(query, start)) !== -1) {
        output += esc(text.slice(start, index)) + '<mark>' + esc(text.slice(index, index + query.length)) + '</mark>';
        start = index + query.length;
    }
    return output + esc(text.slice(start));
}
function renderTree(units) {
    const matches = units.filter(unit => officeMatches(unit, officeSearchQuery));
    const visible = new Set(matches.map(unit => String(unit.id)));
    if (officeSearchQuery) officeSearchAncestors(units, officeSearchQuery).forEach(id => visible.add(id));
    const byId = new Map(units.map(unit => [String(unit.id), unit]));
    const byParent = new Map();
    units.forEach(unit => {
        const key = byId.has(String(unit.parent_id)) ? String(unit.parent_id) : '';
        if (!byParent.has(key)) byParent.set(key, []);
        byParent.get(key).push(unit);
    });
    const visited = new Set();
    let html = buildTreeHTML(byParent, '', visible, visited);
    // Keep legacy orphaned/cyclic records discoverable without changing the stored hierarchy.
    units.forEach(unit => {
        if (!visited.has(String(unit.id)) && visible.has(String(unit.id))) {
            html += buildOfficeNode(unit, byParent, visible, visited);
        }
    });
    $('#orgUnitsTree').html(html || '<p class="org-state">' + (units.length ? 'No offices match your search.' : 'No organizational units found. Add an office to get started.') + '</p>');
    $('#officeResults').text(officeSearchQuery ? matches.length + ' matching offices; ancestor context is included.' : units.length + ' organizational units');
}
function buildTreeHTML(byParent, parentKey, visible, visited) {
    return (byParent.get(parentKey) || []).map(unit => buildOfficeNode(unit, byParent, visible, visited)).join('');
}
function buildOfficeNode(unit, byParent, visible, visited) {
    const id = String(unit.id);
    if (!visible.has(id) || visited.has(id)) return '';
    visited.add(id);
    const hasChildren = (byParent.get(id) || []).some(child => visible.has(String(child.id)) && !visited.has(String(child.id)));
    const expanded = (officeSearchQuery ? searchExpandedOffices : expandedOffices).has(id);
    const typeLabels = {DEPARTMENT: 'Department', TEAM: 'Team', SECTION: 'Section', UNIT: 'Unit'};
    const type = typeLabels[unit.type] || unit.type;
    return `<div class="org-node">
        <div class="org-node-row">
            ${hasChildren ? `<button type="button" class="org-toggle btn btn-outline-secondary" data-id="${esc(id)}" aria-controls="children-${esc(id)}" aria-expanded="${expanded}" aria-label="Expand or collapse ${esc(unit.name)}"><i class="bi ${expanded ? 'bi-chevron-down' : 'bi-chevron-right'}" aria-hidden="true"></i></button>` : '<span class="org-toggle-placeholder invisible" aria-hidden="true"></span>'}
            <div class="org-node-identity"><span class="org-record-name">${officeHighlight(unit.name)}</span><span class="hris-unit-chip badge">${officeHighlight(type)}</span> <span class="org-record-meta">Code: ${officeHighlight(unit.code ?? '-')}</span></div>
            <div class="org-node-actions">
                <button type="button" class="btn viewUnitEmployeesBtn hris-action org-employees" data-id="${esc(id)}" data-name="${esc(unit.name)}" aria-label="View employees assigned directly to ${esc(unit.name)}"><i class="bi bi-people" aria-hidden="true"></i> View Assigned Employees</button>
                <div class="dropdown"><button type="button" class="btn hris-action" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false" aria-label="Actions for ${esc(unit.name)}"><i class="bi bi-three-dots-vertical" aria-hidden="true"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><button type="button" class="dropdown-item addChildOfficeBtn" data-id="${esc(id)}" aria-label="Add child unit to ${esc(unit.name)}"><i class="bi bi-plus-lg" aria-hidden="true"></i> Add Child Unit</button></li>
                        <li><button type="button" class="dropdown-item editOfficeBtn" data-id="${esc(id)}" aria-label="Edit unit ${esc(unit.name)}"><i class="bi bi-pencil-square" aria-hidden="true"></i> Edit Unit</button></li>
                        <li><button type="button" class="dropdown-item text-danger deleteOfficeBtn" data-id="${esc(id)}" data-name="${esc(unit.name)}" aria-label="Delete unit ${esc(unit.name)}"><i class="bi bi-trash" aria-hidden="true"></i> Delete Unit</button></li>
                    </ul>
                </div>
            </div>
        </div>
        ${hasChildren ? `<div class="org-children ${expanded ? '' : 'd-none'}" id="children-${esc(id)}">${buildTreeHTML(byParent, id, visible, visited)}</div>` : ''}
    </div>`;
}

function addOffice(form) {
    let $form = $(form);
    if (fnParentUnavailable($form) || !orgBegin($form)) return;

    let formData = {
        parent_id: $form.find('[name="parent_id"]').val(),
        name: $form.find('[name="name"]').val(),
        code: $form.find('[name="code"]').val(),
        type: $form.find('[name="type"]').val(),
        csrf_token: $form.find('[name="csrf_token"]').val()
    };

    $.ajax({
        url: '/hris/offices/create',
        type: 'POST',
        data: formData,
        dataType: 'json',
        complete: function () { orgEnd($form); },

        beforeSend: function () {
            showLoader("Saving office...");
        },

        success: function (res) {
            hideLoader();

            if (!res.success) {
                orgFeedback(res.message || 'Something went wrong.', 'error', $form);
                return;
            }

            orgFeedback(res.message, 'success');
            $('#addOfficeForm')[0].reset();
            bootstrap.Modal.getInstance(document.getElementById('addOfficeModal')).hide();
            getAllOrgUnits();
        },

        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            orgFeedback('Unable to save this unit. Your entries have been kept; try again.', 'error', $form);
        }
    });
}

function getOffice(id) {
    showLoader("Loading office...");

    $.ajax({
        url: '/hris/offices/getunitbyid/' + id,
        type: 'GET',
        dataType: 'json',

        success: function (res) {
            hideLoader();

            if (!res.success) {
                showToast(res.message, 'error');
                return;
            }

            let unit = res.data;
            let $form = $('#editOfficeForm');

            $form.find('[name="id"]').val(unit.id);
            $form.find('[name="name"]').val(unit.name);
            $form.find('[name="code"]').val(unit.code);
            $form.find('[name="type"]').val(unit.type);

            const parent = orgUnitsFlat.find(item => String(item.id) === String(unit.parent_id));
            $('#editParentSelect').val(parent ? parent.name : (unit.parent_id ? 'Parent unit ' + unit.parent_id : 'None (Top Level)'));
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editOfficeModal')).show();
        },

        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Unable to load office.', 'error');
        }
    });
}

function updateOffice(form) {
    let $form = $(form);
    if (fnParentUnavailable($form) || !orgBegin($form)) return;
    let id = $form.find('[name="id"]').val();

    let formData = {
        // parent_id: $form.find('[name="parent_id"]').val(),
        name: $form.find('[name="name"]').val(),
        code: $form.find('[name="code"]').val(),
        type: $form.find('[name="type"]').val(),
        csrf_token: $form.find('[name="csrf_token"]').val()
    };

    $.ajax({
        url: '/hris/offices/updateunit/' + id,
        type: 'POST',
        data: formData,
        dataType: 'json',
        complete: function () { orgEnd($form); },

        beforeSend: function () {
            showLoader("Updating office...");
        },

        success: function (res) {
            hideLoader();

            if (!res.success) {
                orgFeedback(res.message || 'Something went wrong.', 'error', $form);
                return;
            }

            orgFeedback(res.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('editOfficeModal')).hide();
            getAllOrgUnits();
        },

        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            orgFeedback('Unable to save this unit. Your entries have been kept; try again.', 'error', $form);
        }
    });
}

function deleteOffice(id) {
    const $scope = $('#confirmDeleteOfficeBtn');
    if (!orgBegin($scope)) return;

    $.ajax({
        url: '/hris/offices/deleteunit/' + id,
        type: 'POST',
        data: {
            csrf_token: $('input[name="csrf_token"]').first().val()
        },
        dataType: 'json',
        complete: function () { orgEnd($scope); },

        beforeSend: function () {
            bootstrap.Modal.getInstance(document.getElementById('deleteOfficeModal')).hide();
            showLoader("Deleting office...");
        },

        success: function (res) {
            hideLoader();

            if (!res.success) {
                orgFeedback(res.message || 'Unable to delete this unit.', 'error');
                return;
            }

            officeIdToDelete = null;
            orgFeedback(res.message, 'success');
            getAllOrgUnits();
        },

        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            orgFeedback('Unable to delete this unit. Try again.', 'error');
        }
    });
}
function fnParentUnavailable($form) {
    if ($form.is('#addOfficeForm') && $('#parentSelect').prop('disabled')) {
        orgFeedback('Parent units are unavailable. Close and reopen the form to retry.', 'error', $form);
        return true;
    }
    return false;
}

// Organization-only feedback and pending state. Messages are always rendered as text.
function orgFeedback(message, type = 'info', form = null) {
    const $target = form ? $(form).find('.org-form-feedback').first() : $('#orgPageFeedback');
    $target.removeClass('d-none org-feedback--error org-feedback--success')
        .toggleClass('org-feedback--error', type === 'error')
        .toggleClass('org-feedback--success', type === 'success').text(message || '');
}
function orgBegin($scope) {
    if ($scope.data('orgPending')) return false;
    $scope.data('orgPending', true).attr('aria-busy', 'true');
    $scope.find(':submit').add($scope.filter('button')).prop('disabled', true);
    return true;
}
function orgEnd($scope) {
    $scope.data('orgPending', false).removeAttr('aria-busy');
    $scope.find(':submit').add($scope.filter('button')).prop('disabled', false);
}
function initOrganizationUX() {
    let returnFocus = null;
    $(document).on('click', '.organization-page button, .organization-page a', function () {
        returnFocus = this;
    });
    $('.organization-dialog').on('shown.bs.modal', function () {
        $(this).find('.org-form-feedback').addClass('d-none').empty();
        const target = $(this).find('[id^="confirmDelete"], input:not([type="hidden"]), select, textarea, .btn-close').filter(':visible').first()[0];
        // Destructive dialogs initially focus Cancel, never the destructive action.
        const cancel = $(this).find('.modal-footer [data-bs-dismiss="modal"]')[0];
        (this.id.startsWith('delete') ? cancel : target)?.focus();
    }).on('hidden.bs.modal', function () {
        if (returnFocus?.isConnected) returnFocus.focus();
        else $('.organization-page h1').attr('tabindex', '-1').trigger('focus');
    });
}

</script>
</body>
</html>