<?php
$useDataTables = false;
include '../app/views/partials/admin-header.php';
include '../app/views/module_hris/partials/hris_nav.php';
?>

<div class="container-fluid py-4 management-page hris-page organization-page hris-consistency-page">
    <div class="card shadow-sm management-card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div class="org-page-intro">
                <p class="org-eyebrow d-print-none">HRIS · Organization</p>
                <h1 class="h5 mb-0" id="officesHeading">Offices</h1>
                <p class="org-page-description d-print-none">Organize departments, teams, sections and units across your clinic.</p>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-outline-secondary btn-sm" id="expandAllBtn">
                    <i class="bi bi-arrows-expand"></i> Expand All
                </button>
                <button class="btn btn-outline-secondary btn-sm" id="collapseAllBtn">
                    <i class="bi bi-arrows-collapse"></i> Collapse All
                </button>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addOfficeModal">
                    <i class="bi bi-plus-lg"></i> Add Office
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <div id="orgUnitsTree"></div>
        </div>
    </div>
</div>

<!-- Add Office -->
<div class="modal fade hris-dialog organization-page org-owner-dialog" id="addOfficeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addOfficeModalTitle">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="addOfficeForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="addOfficeModalTitle">Add Organizational Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
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
<div class="modal fade hris-dialog organization-page org-owner-dialog" id="editOfficeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editOfficeModalTitle">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="editOfficeForm">
                <input type="hidden" name="id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="editOfficeModalTitle">Edit Organizational Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="editParentSelect" class="form-label">Parent Unit</label>
                            <select class="form-select" name="parent_id" id="editParentSelect">
                                <option value="">-- None (Top Level) --</option>
                            </select>
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
                            <input id="editOfficeForm_name" type="text" class="form-control" name="name">
                        </div>

                        <div class="col-md-4">
                            <label for="editOfficeForm_code" class="form-label">Code</label>
                            <input id="editOfficeForm_code" type="text" class="form-control" name="code">
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

<?php include '../app/views/partials/admin-footer.php'; ?>

<!-- View Office Employees -->
<div class="modal fade hris-dialog" id="viewUnitEmployeesModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="viewUnitEmployeesHeading">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewUnitEmployeesHeading">
                    Employees in <span id="viewUnitEmployeesTitle" class="fw-semibold">-</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
            </div>

            <div class="modal-body">
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

<!-- Selected-unit confirmation; deletion dispatch stays in the existing workflow. -->
<div class="modal fade hris-dialog organization-page org-owner-dialog" id="deleteOfficeModal" tabindex="-1" aria-labelledby="deleteOfficeModalTitle" aria-describedby="deleteOfficeDescription">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteOfficeModalTitle">Delete Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close Delete Unit"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0" id="deleteOfficeDescription">Delete <strong id="deleteOfficeName"></strong>? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteOfficeBtn" disabled>Delete Unit</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let orgUnitsFlat = [];
let pendingOfficeDelete = null;
let officeDeleteTrigger = null;
let officeDeleteInFlight = false;

$(document).ready(function () {
    getAllOrgUnits();

    $('#addOfficeForm').on('submit', function (e) {
        e.preventDefault();
        addOffice(this);
    });

    $('#editOfficeForm').on('submit', function (e) {
        e.preventDefault();
        updateOffice(this);
    });

    $('#addOfficeModal').on('show.bs.modal', function () {
        loadParentOptions('#parentSelect');
    });

    $(document).on('click', '.editOfficeBtn', function (e) {
        e.stopPropagation();
        getOffice($(this).data('id'));
    });

    $(document).on('click', '.viewUnitEmployeesBtn', function (e) {
        e.stopPropagation();
        viewUnitEmployees($(this).data('id'), $(this).data('name'));
    });

    $(document).on('click', '.deleteOfficeBtn', function (e) {
        e.stopPropagation();
        deleteOffice($(this).data('id'), this);
    });

    $('#confirmDeleteOfficeBtn').on('click', function () {
        if (officeDeleteInFlight || !pendingOfficeDelete || !$('#deleteOfficeModal').hasClass('show')) return;
        const selected = resolveOfficeDeleteSelection(pendingOfficeDelete.id);
        if (!selected || selected.name !== pendingOfficeDelete.name) {
            pendingOfficeDelete = null;
            $(this).prop('disabled', true);
            bootstrap.Modal.getInstance(document.getElementById('deleteOfficeModal')).hide();
            showToast('The selected unit is no longer available. Refresh the tree and try again.', 'error');
            return;
        }
        const id = selected.id;
        pendingOfficeDelete = null;
        officeDeleteInFlight = true;
        officeDeleteTrigger = null;
        $(this).prop('disabled', true);
        bootstrap.Modal.getInstance(document.getElementById('deleteOfficeModal')).hide();
        performConfirmedOfficeDelete(id).always(function () { officeDeleteInFlight = false; });
    });

    $('#deleteOfficeModal').on('shown.bs.modal', function () {
        $(this).find('.modal-footer [data-bs-dismiss="modal"]').trigger('focus');
    }).on('keydown', function (event) {
        if (event.key !== 'Tab') return;
        const controls = $(this).find('button:visible:not(:disabled)').get();
        const first = controls[0], last = controls[controls.length - 1];
        if (event.shiftKey && (document.activeElement === first || document.activeElement === this)) {
            event.preventDefault();
            last?.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first?.focus();
        }
    }).on('hide.bs.modal', function () {
        pendingOfficeDelete = null;
        $('#confirmDeleteOfficeBtn').prop('disabled', true);
    }).on('hidden.bs.modal', function () {
        $('#deleteOfficeName').text('');
        const trigger = officeDeleteTrigger;
        officeDeleteTrigger = null;
        const fallback = document.querySelector('[data-bs-target="#addOfficeModal"]');
        (trigger && document.contains(trigger) && $(trigger).is(':visible') ? trigger : fallback)?.focus();
    });

    // Private to this ready callback: only the consuming confirmation handler calls it.
    function performConfirmedOfficeDelete(id) {
        return $.ajax({
            url: '/hris/offices/deleteunit/' + id,
            type: 'POST',
            data: {
                csrf_token: $('input[name="csrf_token"]').first().val()
            },
            dataType: 'json',

            beforeSend: function () {
                showLoader("Deleting office...");
            },

            success: function (res) {
                hideLoader();

                if (!res.success) {
                    showToast(res.message || 'Something went wrong.', 'error');
                    return;
                }

                showToast(res.message, 'success');
                getAllOrgUnits();
            },

            error: function (xhr) {
                hideLoader();
                console.log(xhr.responseText);
                showToast('Something went wrong.', 'error');
            }
        });
    }

    $(document).on('click', '.org-toggle', function () {
        let id = $(this).data('id');
        let $icon = $(this).find('i');
        let $children = $('#children-' + id);

        let expanded = $children.hasClass('d-none');
        $children.toggleClass('d-none', !expanded);
        $(this).attr('aria-expanded', String(expanded));
        $icon.toggleClass('bi-chevron-right', !expanded).toggleClass('bi-chevron-down', expanded);
    });

    $('#expandAllBtn').on('click', function () {
        $('.org-children').removeClass('d-none');
        $('.org-toggle').attr('aria-expanded', 'true');
        $('.org-toggle i').removeClass('bi-chevron-right').addClass('bi-chevron-down');
    });

    $('#collapseAllBtn').on('click', function () {
        $('.org-children').addClass('d-none');
        $('.org-toggle').attr('aria-expanded', 'false');
        $('.org-toggle i').removeClass('bi-chevron-down').addClass('bi-chevron-right');
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
    showLoader("Loading employees...");

    $('#viewUnitEmployeesTitle').text(name || '-');

    $.ajax({
        url: '/hris/offices/getunitemployees/' + id,
        type: 'GET',
        dataType: 'json',

        success: function (res) {
            hideLoader();

            if (!res.success) {
                showToast(res.message || 'Unable to load employees.', 'error');
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
            bootstrap.Modal.getOrCreateInstance(document.getElementById('viewUnitEmployeesModal')).show();
        },

        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Unable to load employees.', 'error');
        }
    });
}

function buildParentOptions(units, selected = '') {
    let html = '<option value="">-- None (Top Level) --</option>';

    units.forEach(unit => {
        html += `
            <option value="${unit.id}" ${unit.id == selected ? 'selected' : ''}>
                ${unit.name}
            </option>
        `;
    });

    return html;
}

function loadParentOptions(selector, selected = '') {
    $.ajax({
        url: '/hris/offices/getallunits',
        type: 'GET',
        dataType: 'json',

        success: function (res) {
            $(selector).html(buildParentOptions(res.data || [], selected));
        },

        error: function (xhr) {
            console.log(xhr.responseText);
            showToast('Unable to load parent units.', 'error');
        }
    });
}

function getAllOrgUnits() {
    showLoader("Loading offices...");

    $.ajax({
        url: '/hris/offices/getallunits',
        type: 'GET',
        dataType: 'json',

        success: function (res) {
            hideLoader();

            orgUnitsFlat = res.data || [];
            renderTree(orgUnitsFlat);
        },

        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Unable to load offices.', 'error');
        }
    });
}

function renderTree(units) {
    let $container = $('#orgUnitsTree');

    if (!units || units.length === 0) {
        $container.html('<div class="text-center text-muted py-4">No organizational units found.</div>');
        return;
    }

    // group children by parent_id ('' bucket = top level)
    let byParent = {};
    units.forEach(unit => {
        let key = unit.parent_id ? unit.parent_id : '';
        if (!byParent[key]) byParent[key] = [];
        byParent[key].push(unit);
    });

    $container.html(buildTreeHTML(byParent, '', 0));
}

function buildTreeHTML(byParent, parentKey, depth) {
    let children = byParent[parentKey];
    if (!children || children.length === 0) return '';

    let html = '';

    children.forEach(unit => {
        let hasChildren = !!byParent[unit.id];
        let indentClass = depth > 0 ? `ps-${Math.min(depth * 4, 5)}` : '';

        html += `
            <div class="border-bottom">
                <div class="d-flex align-items-center px-3 py-2 ${indentClass}">
                    ${hasChildren
                        ? `<button type="button" class="org-toggle btn btn-outline-secondary me-2" data-id="${unit.id}" aria-controls="children-${unit.id}" aria-expanded="false" aria-label="Toggle ${MonteroHris.escape(unit.name)}" title="Toggle ${MonteroHris.escape(unit.name)}">
                                <i class="bi bi-chevron-right" aria-hidden="true"></i>
                           </button>`
                        : `<span class="org-toggle-placeholder me-2 invisible" aria-hidden="true"><i class="bi bi-chevron-right"></i></span>`
                    }

                    <div class="d-flex align-items-center gap-2 flex-grow-1 me-3">
                        <span class="fw-semibold">${unit.name}</span>
                        <span class="badge bg-secondary hris-unit-chip">${unit.type}</span>
                        <span class="small text-muted">${unit.code ?? '-'}</span>
                        <span class="small text-muted ms-auto">${unit.created_at}</span>
                    </div>

                    <div class="text-end">
                        <button class="btn btn-sm viewUnitEmployeesBtn hris-action" data-id="${unit.id}" data-name="${unit.name}" aria-label="View employees in this office" title="View employees in this office">
                            <i class="bi bi-eye" aria-hidden="true"></i>
                        </button>
                        <button class="btn btn-sm editOfficeBtn hris-action" data-id="${unit.id}" aria-label="Edit office" title="Edit office">
                            <i class="bi bi-pencil-square" aria-hidden="true"></i>
                        </button>
                        <button class="btn btn-sm deleteOfficeBtn hris-action hris-action--danger" data-id="${unit.id}" aria-label="Delete office" title="Delete office">
                            <i class="bi bi-trash" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <div class="org-children d-none" id="children-${unit.id}">
                    ${buildTreeHTML(byParent, unit.id, depth + 1)}
                </div>
            </div>
        `;
    });

    return html;
}

function addOffice(form) {
    let $form = $(form);

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

        beforeSend: function () {
            showLoader("Saving office...");
        },

        success: function (res) {
            hideLoader();

            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }

            showToast(res.message, 'success');
            $('#addOfficeForm')[0].reset();
            bootstrap.Modal.getInstance(document.getElementById('addOfficeModal')).hide();
            getAllOrgUnits();
        },

        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Something went wrong.', 'error');
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

            loadParentOptions('#editParentSelect', unit.parent_id);
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

        beforeSend: function () {
            showLoader("Updating office...");
        },

        success: function (res) {
            hideLoader();

            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }

            showToast(res.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('editOfficeModal')).hide();
            getAllOrgUnits();
        },

        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Something went wrong.', 'error');
        }
    });
}

function resolveOfficeDeleteSelection(id) {
    const key = String(id ?? '');
    if (!/^[1-9]\d*$/.test(key) || !Array.isArray(orgUnitsFlat)) return null;
    const matches = orgUnitsFlat.filter(unit => unit && String(unit.id) === key);
    if (matches.length !== 1 || typeof matches[0].name !== 'string' || !matches[0].name.trim()) return null;
    return {id: key, name: matches[0].name.trim()};
}

// Every existing caller enters here and must go through the Bootstrap confirmation.
function deleteOffice(id, trigger = null) {
    if (officeDeleteInFlight) return;
    pendingOfficeDelete = null;
    officeDeleteTrigger = trigger;
    const selected = resolveOfficeDeleteSelection(id);
    if (!selected) {
        $('#confirmDeleteOfficeBtn').prop('disabled', true);
        bootstrap.Modal.getInstance(document.getElementById('deleteOfficeModal'))?.hide();
        showToast('Unable to identify the selected unit. Refresh the tree and try again.', 'error');
        return;
    }
    pendingOfficeDelete = selected;
    $('#deleteOfficeName').text(selected.name);
    $('#confirmDeleteOfficeBtn').prop('disabled', false);
    bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteOfficeModal')).show(trigger);
}
</script>
</body>
</html>