<?php
include '../app/views/partials/admin-header.php';
include '../app/views/module_hris/partials/hris_nav.php';
?>

<div class="container-fluid py-4 management-page hris-page organization-page">
    <div class="card shadow-sm management-card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div class="org-page-intro"><p class="org-eyebrow">Organization</p><h1 class="h5 mb-0" id="employmentTypesHeading">Employment Types</h1><p class="org-page-description">Manage employment types, descriptions and their active status.</p></div>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                <i class="bi bi-plus-lg" aria-hidden="true"></i> Add Employment Type
            </button>
        </div>

        <div class="card-body p-0">
            <div class="org-feedback d-none" id="orgPageFeedback" role="status" aria-live="polite" aria-atomic="true"></div>
            <div class="org-toolbar">
                <div class="org-search"><label class="form-label" for="employmentTypeSearch">Search employment types</label><input id="employmentTypeSearch" type="search" class="form-control" placeholder="Search name, code or description" aria-controls="appointmentsTable" disabled></div>
                <div class="org-filter"><label class="form-label" for="employmentTypeStatus">Status</label><select id="employmentTypeStatus" class="form-select" aria-controls="appointmentsTable" disabled><option value="all">All</option><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                <button type="button" class="btn btn-outline-secondary" id="clearEmploymentTypeFilters" disabled>Clear Filters</button>
            </div>
            <p id="employmentTypeResults" class="org-results" role="status" aria-live="polite"></p>
            <p class="hris-scroll-hint" id="appointmentsTableScrollHelp">Use horizontal scrolling when columns extend beyond this view.</p>
            <div class="table-responsive hris-table-region" tabindex="0" role="region" aria-labelledby="employmentTypesHeading" aria-describedby="appointmentsTableScrollHelp">
                <table class="table table-hover align-middle mb-0 hris-data-table" id="appointmentsTable">
                    <caption class="visually-hidden">Employment types, descriptions and activity status with existing record actions.</caption>
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">Employment Type / Code</th>
                            <th scope="col">Description</th>
                            <th scope="col" style="width: 8rem">Status</th>
                            <th scope="col" style="width: 13rem" class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="appointmentsTableBody">
                        <tr>
                            <td colspan="4" class="text-center">
                                Loading...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Appointment -->
<div class="modal fade hris-dialog organization-dialog" id="addAppointmentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addAppointmentModalTitle">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">

            <form id="addAppointmentForm">

                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="addAppointmentModalTitle">Add Employment Type</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="org-form-feedback d-none" role="status" aria-live="polite" aria-atomic="true"></div>

                    <div class="mb-3">
                        <label for="addAppointmentForm_code" class="form-label">Code <span class="text-danger">*</span></label>
                        <input id="addAppointmentForm_code"
                            type="text"
                            class="form-control"
                            name="code"
                            placeholder="e.g. PERMANENT, JO, CONTRACTUAL"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="addAppointmentForm_name" class="form-label">Employment Type Name</label>
                        <input id="addAppointmentForm_name"
                            type="text"
                            class="form-control"
                            name="name"
                            placeholder="e.g. Permanent, Job Order, Contractual">
                    </div>

                    <div class="mb-3">
                        <label for="addAppointmentForm_description" class="form-label">Description</label>
                        <textarea id="addAppointmentForm_description"
                            class="form-control"
                            name="description"
                            rows="2"
                            placeholder="Optional description"></textarea>
                    </div>

                    <div class="form-check form-switch">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            role="switch"
                            name="is_active"
                            id="addAppointmentIsActive"
                            aria-describedby="addAppointmentActiveHelp"
                            value="1"
                            checked>
                        <label class="form-check-label" for="addAppointmentIsActive">Active</label>
                        <p class="form-text" id="addAppointmentActiveHelp">Switch off to mark this employment type Inactive. Inactive records remain in this catalog; this does not delete the record.</p>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
                        Close
                    </button>

                    <button class="btn btn-primary" type="submit">
                        Save
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- Edit Appointment -->
<div class="modal fade hris-dialog organization-dialog" id="editAppointmentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editAppointmentModalTitle">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">

            <form id="editAppointmentForm">

                <input type="hidden" name="id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="editAppointmentModalTitle">Edit Employment Type</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="org-form-feedback d-none" role="status" aria-live="polite" aria-atomic="true"></div>

                    <div class="mb-3">
                        <label for="editAppointmentForm_code" class="form-label">Code <span class="text-danger">*</span></label>
                        <input id="editAppointmentForm_code"
                            type="text"
                            class="form-control"
                            name="code"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="editAppointmentForm_name" class="form-label">Employment Type Name</label>
                        <input id="editAppointmentForm_name"
                            type="text"
                            class="form-control"
                            name="name">
                    </div>

                    <div class="mb-3">
                        <label for="editAppointmentForm_description" class="form-label">Description</label>
                        <textarea id="editAppointmentForm_description"
                            class="form-control"
                            name="description"
                            rows="2"></textarea>
                    </div>

                    <div class="form-check form-switch">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            role="switch"
                            name="is_active"
                            id="editAppointmentIsActive"
                            aria-describedby="editAppointmentActiveHelp"
                            value="1">
                        <label class="form-check-label" for="editAppointmentIsActive">Active</label>
                        <p class="form-text" id="editAppointmentActiveHelp">Switch off to mark this employment type Inactive. Inactive records remain in this catalog; this does not delete the record.</p>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
                        Close
                    </button>

                    <button class="btn btn-primary" type="submit">
                        Update
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- Delete Appointment Confirmation -->
<div class="modal fade hris-dialog organization-dialog" id="deleteAppointmentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteAppointmentModalTitle">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="deleteAppointmentModalTitle">Delete Employment Type</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close dialog"></button>
            </div>

            <div class="modal-body">
                    <div class="org-form-feedback d-none" role="status" aria-live="polite" aria-atomic="true"></div>
                <p class="mb-0">
                    Are you sure you want to delete
                    <strong id="deleteAppointmentName"></strong>?
                    Deleting removes it from this catalog. Mark it Inactive instead to keep it listed.
                </p>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
                    Cancel
                </button>

                <button class="btn btn-danger" type="button" id="confirmDeleteAppointmentBtn">
                    Delete
                </button>
            </div>

        </div>
    </div>
</div>

<?php include '../app/views/partials/admin-footer.php'; ?>

<script>

let appointmentIdToDelete = null;
let employmentTypes = [];

// Copy only: preserve unknown messages and every server response value unchanged.
function employmentTypeMessage(message) {
    const labels = new Map([
        ['Appointment not found.', 'Employment type not found.'],
        ['Appointment created successfully.', 'Employment type created successfully.'],
        ['Appointment updated successfully.', 'Employment type updated successfully.'],
        ['Failed to update appointment.', 'Failed to update employment type.'],
        ['Appointment deleted successfully.', 'Employment type deleted successfully.'],
        ['Failed to delete appointment.', 'Failed to delete employment type.'],
        ['System default appointment types cannot be edited.', 'System default employment types cannot be edited.'],
        ['System default appointment types cannot be deleted.', 'System default employment types cannot be deleted.']
    ]);
    return labels.has(message) ? labels.get(message) : message;
}

$(document).ready(function () {
    initOrganizationUX();

    document.title = document.title.replace('Appointments', 'Employment Types');
    getAllAppointments();
    $('#employmentTypeSearch').on('input', renderEmploymentTypes);
    $('#employmentTypeStatus').on('change', renderEmploymentTypes);
    $('#clearEmploymentTypeFilters').on('click', function () {
        $('#employmentTypeSearch').val(''); $('#employmentTypeStatus').val('all');
        renderEmploymentTypes(); $('#employmentTypeSearch').trigger('focus');
    });

    $('#addAppointmentForm').on('submit', function (e) {
        e.preventDefault();
        addAppointment(this);
    });

    $('#editAppointmentForm').on('submit', function (e) {
        e.preventDefault();
        updateAppointment(this);
    });

    $(document).on('click', '.editAppointmentBtn', function () {
        getAppointment($(this).data('id'));
    });

    $(document).on('click', '.deleteAppointmentBtn', function () {
        appointmentIdToDelete = $(this).data('id');
        let name = $(this).attr('data-name');

        $('#deleteAppointmentName').text(name);

        bootstrap.Modal.getOrCreateInstance(
            document.getElementById('deleteAppointmentModal')
        ).show();
    });

    $('#confirmDeleteAppointmentBtn').on('click', function () {
        if (appointmentIdToDelete) {
            deleteAppointment(appointmentIdToDelete);
        }
    });

});

function esc(str) {
    return String(str ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;'}[c]));
}
function getAllAppointments() {
    $('#appointmentsTable').attr('aria-busy', 'true');
    $('#appointmentsTableBody').html('<tr><td colspan="4" class="org-state" role="status">Loading employment types…</td></tr>');
    $('#employmentTypeSearch, #employmentTypeStatus, #clearEmploymentTypeFilters').prop('disabled', true);
    $.ajax({
        url: '/hris/appointments/getallappointments',
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            $('#appointmentsTable').removeAttr('aria-busy');
            if (!res.success || !Array.isArray(res.data)) { employmentTypeReadError(); return; }
            employmentTypes = res.data;
            renderEmploymentTypes();
            $('#employmentTypeSearch, #employmentTypeStatus, #clearEmploymentTypeFilters').prop('disabled', false);
        },
        error: employmentTypeReadError
    });
}
function employmentTypeReadError() {
    $('#appointmentsTable').removeAttr('aria-busy');
    $('#appointmentsTableBody').html('<tr><td colspan="4" class="org-state" role="status">Unable to load employment types. <button class="btn btn-outline-secondary" type="button" id="retryEmploymentTypes">Try Again</button></td></tr>');
    $('#employmentTypeResults').text('Employment types could not be loaded.');
}
$(document).on('click', '#retryEmploymentTypes', getAllAppointments);
function renderEmploymentTypes() {
    const query = $('#employmentTypeSearch').val().trim().toLocaleLowerCase();
    const status = $('#employmentTypeStatus').val();
    const filtered = employmentTypes.filter(appointment => {
        const active = parseInt(appointment.is_active) === 1;
        return [appointment.name, appointment.code, appointment.description].some(value => String(value ?? '').toLocaleLowerCase().includes(query))
            && (status === 'all' || (status === 'active' ? active : !active));
    });
    $('#appointmentsTableBody').html(filtered.map(appointment => {
        const active = parseInt(appointment.is_active) === 1;
        const global = parseInt(appointment.is_global) === 1;
        // The list includes current-tenant and global rows only. Backend ownership,
        // not a role name, prohibits both operations on global defaults.
        const editable = parseInt(appointment.is_global) === 0;
        const identity = `${appointment.name ?? ''} (${appointment.code ?? '-'})`;
        const actions = editable ? `<div class="org-row-actions">
            <button type="button" class="btn btn-sm editAppointmentBtn hris-action" data-id="${esc(appointment.id)}" aria-label="Edit employment type ${esc(identity)}"><i class="bi bi-pencil-square" aria-hidden="true"></i> Edit</button>
            <button type="button" class="btn btn-sm deleteAppointmentBtn hris-action hris-action--danger" data-id="${esc(appointment.id)}" data-name="${esc(identity)}" aria-label="Delete employment type ${esc(identity)}"><i class="bi bi-trash" aria-hidden="true"></i> Delete</button>
        </div>` : `<span class="org-record-meta">${global ? 'Protected default' : 'Actions unavailable'}</span>`;
        return `<tr>
            <th scope="row" class="ps-4 appointment-name"><span class="org-record-name">${esc(appointment.name)}</span><span class="org-record-meta">${esc(appointment.code ?? '-')}</span><span class="org-source ${global ? 'org-source--global' : ''}">${global ? 'System Default' : (editable ? 'Client-defined' : 'Source unavailable')}</span></th>
            <td>${esc(appointment.description ?? '-')}</td>
            <td><span class="badge hris-status ${active ? 'hris-status--positive' : 'hris-status--neutral'}">${active ? 'Active' : 'Inactive'}</span></td>
            <td class="text-end pe-4">${actions}</td>
        </tr>`;
    }).join('') || '<tr><td colspan="4" class="org-state">' + (employmentTypes.length ? 'No employment types match your search and status filter.' : 'No employment types found. Add an employment type to get started.') + '</td></tr>');
    $('#employmentTypeResults').text(filtered.length + ' of ' + employmentTypes.length + ' employment types · Status: ' + (status === 'all' ? 'All' : status === 'active' ? 'Active' : 'Inactive'));
}

function addAppointment(form) {
    let $form = $(form);
    if (!orgBegin($form)) return;

    let formData = {
        code: $form.find('[name="code"]').val(),
        name: $form.find('[name="name"]').val(),
        description: $form.find('[name="description"]').val(),
        is_active: $form.find('[name="is_active"]').is(':checked') ? 1 : 0,
        csrf_token: $form.find('[name="csrf_token"]').val()
    };

    $.ajax({
        url: '/hris/appointments/create',
        type: 'POST',
        data: formData,
        dataType: 'json',
        complete: function () { orgEnd($form); },

        beforeSend: function () {
            showLoader("Saving employment type...");
        },

        success: function (res) {

            hideLoader();

            if (!res.success) {
                orgFeedback(employmentTypeMessage(res.message) || 'Please check the employment type details and try again.', 'error', $form);
                return;
            }

            orgFeedback(employmentTypeMessage(res.message), 'success');

            $('#addAppointmentForm')[0].reset();

            bootstrap.Modal.getInstance(document.getElementById('addAppointmentModal')).hide();

            getAllAppointments();

        },

        error: function (xhr) {

            hideLoader();

            console.log(xhr.responseText);

            orgFeedback('Unable to save this employment type. Your entries have been kept; try again.', 'error', $form);

        }

    });
}

function getAppointment(id) {
    showLoader("Loading employment type...");

    $.ajax({
        url: '/hris/appointments/getappointmentbyid/' + id,
        type: 'GET',
        dataType: 'json',

        success: function (res) {

            hideLoader();

            if (!res.success) {
                showToast(employmentTypeMessage(res.message), 'error');
                return;
            }

            let appointment = res.data;
            let $form = $('#editAppointmentForm');

            $form.find('[name="id"]').val(appointment.id);
            $form.find('[name="code"]').val(appointment.code);
            $form.find('[name="name"]').val(appointment.name);
            $form.find('[name="description"]').val(appointment.description);
            $form.find('[name="is_active"]').prop('checked', parseInt(appointment.is_active) === 1);

            bootstrap.Modal.getOrCreateInstance(
                document.getElementById('editAppointmentModal')
            ).show();

        },

        error: function (xhr) {

            hideLoader();

            console.log(xhr.responseText);

            showToast('Unable to load employment type.', 'error');

        }

    });
}

function updateAppointment(form) {
    let $form = $(form);
    if (!orgBegin($form)) return;

    let id = $form.find('[name="id"]').val();

    let formData = {
        code: $form.find('[name="code"]').val(),
        name: $form.find('[name="name"]').val(),
        description: $form.find('[name="description"]').val(),
        is_active: $form.find('[name="is_active"]').is(':checked') ? 1 : 0,
        csrf_token: $form.find('[name="csrf_token"]').val()
    };

    $.ajax({
        url: '/hris/appointments/update/' + id,
        type: 'POST',
        data: formData,
        dataType: 'json',
        complete: function () { orgEnd($form); },

        beforeSend: function () {
            showLoader("Updating employment type...");
        },

        success: function (res) {

            hideLoader();

            if (!res.success) {
                orgFeedback(employmentTypeMessage(res.message) || 'Please check the employment type details and try again.', 'error', $form);
                return;
            }

            orgFeedback(employmentTypeMessage(res.message), 'success');

            bootstrap.Modal.getInstance(
                document.getElementById('editAppointmentModal')
            ).hide();

            getAllAppointments();

        },

        error: function (xhr) {

            hideLoader();

            console.log(xhr.responseText);

            orgFeedback('Unable to save this employment type. Your entries have been kept; try again.', 'error', $form);

        }

    });
}

function deleteAppointment(id) {
    const $scope = $('#confirmDeleteAppointmentBtn');
    if (!orgBegin($scope)) return;

    $.ajax({
        url: '/hris/appointments/delete/' + id,
        type: 'POST',
        data: {
            csrf_token: $('input[name="csrf_token"]').first().val()
        },
        dataType: 'json',
        complete: function () { orgEnd($scope); },

        beforeSend: function () {
            bootstrap.Modal.getInstance(document.getElementById('deleteAppointmentModal')).hide();
            showLoader("Deleting employment type...");
        },

        success: function (res) {

            hideLoader();

            if (!res.success) {
                orgFeedback(employmentTypeMessage(res.message) || 'Unable to delete this employment type.', 'error');
                return;
            }

            orgFeedback(employmentTypeMessage(res.message), 'success');
            appointmentIdToDelete = null;
            getAllAppointments();

        },

        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            orgFeedback('Unable to delete this employment type. Try again.', 'error');
        }
    });
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
