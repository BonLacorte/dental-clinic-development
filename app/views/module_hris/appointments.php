<?php
include '../app/views/partials/admin-header.php';
include '../app/views/module_hris/partials/hris_nav.php';
?>

<div class="container-fluid py-4 management-page hris-page organization-page hris-consistency-page employment-types-owner-page">
    <div class="card shadow-sm management-card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div class="org-page-intro">
                <p class="org-eyebrow d-print-none">HRIS · Organization</p>
                <h1 class="h5 mb-0" id="employmentTypesHeading">Employment Types</h1>
                <p class="org-page-description d-print-none">Manage employment types, descriptions and their active status.</p>
            </div>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                <i class="bi bi-plus-lg"></i> Add Employment Type
            </button>
        </div>

        <div class="card-body p-0">
            <p class="hris-scroll-hint" id="appointmentsTableScrollHelp">Use horizontal scrolling when columns extend beyond this view.</p>
            <div class="table-responsive hris-table-region" tabindex="0" role="region" aria-labelledby="employmentTypesHeading" aria-describedby="appointmentsTableScrollHelp">
                <table class="table table-hover align-middle mb-0 hris-data-table" id="appointmentsTable">
                    <caption class="visually-hidden">Employment types, descriptions and activity status with existing record actions.</caption>
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">Employment Type Name</th>
                            <th scope="col" width="120">Code</th>
                            <th scope="col">Description</th>
                            <th scope="col" width="100">Status</th>
                            <th scope="col" width="140" class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="appointmentsTableBody">
                        <tr>
                            <td colspan="5" class="text-center">
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
<div class="modal fade hris-dialog" id="addAppointmentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addAppointmentModalTitle">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="addAppointmentForm">

                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="addAppointmentModalTitle">Add Employment Type</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">

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
                            value="1"
                            checked>
                        <label class="form-check-label" for="addAppointmentIsActive">Active</label>
                        <p class="form-text mb-0">Switch off to mark this employment type Inactive. Inactive records remain in this catalog; this does not delete the record.</p>
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
<div class="modal fade hris-dialog" id="editAppointmentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editAppointmentModalTitle">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="editAppointmentForm">

                <input type="hidden" name="id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="editAppointmentModalTitle">Edit Employment Type</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">

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
                            value="1">
                        <label class="form-check-label" for="editAppointmentIsActive">Active</label>
                        <p class="form-text mb-0">Switch off to mark this employment type Inactive. Inactive records remain in this catalog; this does not delete the record.</p>
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
<div class="modal fade hris-dialog" id="deleteAppointmentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteAppointmentModalTitle">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="deleteAppointmentModalTitle">Delete Employment Type</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close dialog"></button>
            </div>

            <div class="modal-body">
                <p class="mb-0">
                    Are you sure you want to delete
                    <strong id="deleteAppointmentName"></strong>?
                    This action cannot be undone.
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

    document.title = document.title.replace('Appointments', 'Employment Types');
    getAllAppointments();

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
        let name = $(this).closest('tr').find('td.appointment-name').text();

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

function getAllAppointments() {
    showLoader("Loading employment types...");

    $.ajax({
        url: '/hris/appointments/getallappointments',
        type: 'GET',
        dataType: 'json',

        success: function (res) {

            hideLoader();

            let rows = '';

            if (!res.data || res.data.length === 0) {
                rows = `
                    <tr>
                        <td colspan="5" class="text-center">
                            No employment types found
                        </td>
                    </tr>
                `;

            } else {

                res.data.forEach((appointment) => {

                    let statusBadge = parseInt(appointment.is_active) === 1
                        ? '<span class="badge bg-success hris-status hris-status--positive">Active</span>'
                        : '<span class="badge bg-secondary hris-status hris-status--neutral">Inactive</span>';

                    let actions = `
                        <button class="btn btn-sm editAppointmentBtn hris-action"
                            data-id="${appointment.id}" aria-label="Edit employment type" title="Edit employment type">
                            <i class="bi bi-pencil-square" aria-hidden="true"></i>
                        </button>

                        <button class="btn btn-sm deleteAppointmentBtn hris-action hris-action--danger"
                            data-id="${appointment.id}" aria-label="Delete employment type" title="Delete employment type">
                            <i class="bi bi-trash" aria-hidden="true"></i>
                        </button>
                    `;

                    rows += `
                        <tr>
                            <td class="ps-4 appointment-name">${appointment.name}</td>
                            <td class="appointment-code">${appointment.code ?? '-'}</td>
                            <td>${appointment.description ?? '-'}</td>
                            <td>${statusBadge}</td>
                            <td class="text-end pe-4">
                                ${actions}
                            </td>
                        </tr>
                    `;

                });

            }

            $('#appointmentsTableBody').html(rows);

        },

        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Unable to load employment types.', 'error');
        }

    });
}

function addAppointment(form) {
    let $form = $(form);

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

        beforeSend: function () {
            showLoader("Saving employment type...");
        },

        success: function (res) {

            hideLoader();

            if (!res.success) {
                showToast(employmentTypeMessage(res.message) || 'Something went wrong.', 'error');
                return;
            }

            showToast(employmentTypeMessage(res.message), 'success');

            $('#addAppointmentForm')[0].reset();

            bootstrap.Modal.getInstance(document.getElementById('addAppointmentModal')).hide();

            getAllAppointments();

        },

        error: function (xhr) {

            hideLoader();

            console.log(xhr.responseText);

            showToast('Something went wrong.', 'error');

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

        beforeSend: function () {
            showLoader("Updating employment type...");
        },

        success: function (res) {

            hideLoader();

            if (!res.success) {
                showToast(employmentTypeMessage(res.message) || 'Something went wrong.', 'error');
                return;
            }

            showToast(employmentTypeMessage(res.message), 'success');

            bootstrap.Modal.getInstance(
                document.getElementById('editAppointmentModal')
            ).hide();

            getAllAppointments();

        },

        error: function (xhr) {

            hideLoader();

            console.log(xhr.responseText);

            showToast('Something went wrong.', 'error');

        }

    });
}

function deleteAppointment(id) {

    $.ajax({
        url: '/hris/appointments/delete/' + id,
        type: 'POST',
        data: {
            csrf_token: $('input[name="csrf_token"]').first().val()
        },
        dataType: 'json',

        beforeSend: function () {
            bootstrap.Modal.getInstance(document.getElementById('deleteAppointmentModal')).hide();
            showLoader("Deleting employment type...");
        },

        success: function (res) {

            hideLoader();

            if (!res.success) {
                showToast(employmentTypeMessage(res.message) || 'Something went wrong.', 'error');
                return;
            }

            showToast(employmentTypeMessage(res.message), 'success');
            appointmentIdToDelete = null;
            getAllAppointments();

        },

        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Something went wrong.', 'error');
        }
    });
}

</script>

</body>
</html>
