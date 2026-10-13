<?php
include '../app/views/partials/admin-header.php';
include '../app/views/module_hris/partials/hris_nav.php';
?>

<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Appointments</h5>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                <i class="bi bi-plus-lg"></i> Add Appointment
            </button>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="appointmentsTable">
                    <thead class="table-light">
                        <tr>
                            <th width="120" class="ps-4">Code</th>
                            <th>Appointment Name</th>
                            <th>Description</th>
                            <th width="100">Status</th>
                            <th width="140" class="text-end pe-4">Actions</th>
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
<div class="modal fade" id="addAppointmentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="addAppointmentForm">

                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Add Appointment</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Code <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            class="form-control"
                            name="code"
                            placeholder="e.g. PERMANENT, JO, CONTRACTUAL"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Appointment Name</label>
                        <input
                            type="text"
                            class="form-control"
                            name="name"
                            placeholder="e.g. Permanent, Job Order, Contractual">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea
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
<div class="modal fade" id="editAppointmentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="editAppointmentForm">

                <input type="hidden" name="id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Appointment</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Code <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            class="form-control"
                            name="code"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Appointment Name</label>
                        <input
                            type="text"
                            class="form-control"
                            name="name">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea
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
<div class="modal fade" id="deleteAppointmentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Delete Appointment</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal"></button>
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

$(document).ready(function () {

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
    showLoader("Loading appointments...");

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
                            No appointments found
                        </td>
                    </tr>
                `;

            } else {

                res.data.forEach((appointment) => {

                    let statusBadge = parseInt(appointment.is_active) === 1
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-secondary">Inactive</span>';

                    let actions = `
                        <button class="btn btn-link btn-sm text-primary editAppointmentBtn"
                            data-id="${appointment.id}">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <button class="btn btn-link btn-sm text-danger deleteAppointmentBtn"
                            data-id="${appointment.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;

                    rows += `
                        <tr>
                            <td class="ps-4">${appointment.code ?? '-'}</td>
                            <td class="appointment-name">${appointment.name}</td>
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
            showToast('Unable to load appointments.', 'error');
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
            showLoader("Saving appointment...");
        },

        success: function (res) {

            hideLoader();

            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }

            showToast(res.message, 'success');

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
    showLoader("Loading appointment...");

    $.ajax({
        url: '/hris/appointments/getappointmentbyid/' + id,
        type: 'GET',
        dataType: 'json',

        success: function (res) {

            hideLoader();

            if (!res.success) {
                showToast(res.message, 'error');
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

            showToast('Unable to load appointment.', 'error');

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
            showLoader("Updating appointment...");
        },

        success: function (res) {

            hideLoader();

            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }

            showToast(res.message, 'success');

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
            showLoader("Deleting appointment...");
        },

        success: function (res) {

            hideLoader();

            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }

            showToast(res.message, 'success');
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