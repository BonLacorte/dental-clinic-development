<?php
include '../app/views/partials/admin-header.php';
include '../app/views/module_hris/partials/hris_nav.php';
?>

<div class="container-fluid py-4 management-page hris-page">
    <div class="card shadow-sm management-card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Positions</h5>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPositionModal">
                <i class="bi bi-plus-lg"></i> Add Position
            </button>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="positionsTable">
                    <thead class="table-light">
                        <tr>
                            <th width="80" class="ps-4">#</th>
                            <th>Position Name</th>
                            <th width="180">Created At</th>
                            <th width="120" class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="positionsTableBody">
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

<!-- Add Position -->
<div class="modal fade" id="addPositionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="addPositionForm">

                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Add Position</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Position Name</label>

                        <input
                            type="text"
                            class="form-control"
                            name="name"
                            placeholder="Software Engineer">
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

<!-- Edit Position -->
<div class="modal fade" id="editPositionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="editPositionForm">

                <input type="hidden" name="id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Position</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Position Name</label>

                        <input
                            type="text"
                            class="form-control"
                            name="name">
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

<!-- Delete Position Confirmation -->
<div class="modal fade" id="deletePositionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Delete Position</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p class="mb-0">
                    Are you sure you want to delete
                    <strong id="deletePositionName"></strong>?
                    This action cannot be undone.
                </p>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
                    Cancel
                </button>

                <button class="btn btn-danger" type="button" id="confirmDeletePositionBtn">
                    Delete
                </button>
            </div>

        </div>
    </div>
</div>

<?php include '../app/views/partials/admin-footer.php'; ?>

<script>

let currentPage = 1;
let positionIdToDelete = null;

$(document).ready(function () {

    getAllPositions();

    $('#addPositionForm').on('submit', function (e) {
        e.preventDefault();
        addPosition(this);
    });

    $('#editPositionForm').on('submit', function (e) {
        e.preventDefault();
        updatePosition(this);
    });

    $(document).on('click', '.editPositionBtn', function () {
        getPosition($(this).data('id'));
    });

    $(document).on('click', '.deletePositionBtn', function () {
        positionIdToDelete = $(this).data('id');
        let name = $(this).closest('tr').find('td:nth-child(2)').text();

        $('#deletePositionName').text(name);

        bootstrap.Modal.getOrCreateInstance(
            document.getElementById('deletePositionModal')
        ).show();
    });

    $('#confirmDeletePositionBtn').on('click', function () {
        if (positionIdToDelete) {
            deletePosition(positionIdToDelete);
        }
    });

});

function getAllPositions() {
    showLoader("Loading positions...");

    $.ajax({
        url: '/hris/positions/getallpositions',
        type: 'GET',
        dataType: 'json',

        success: function (res) {

            hideLoader();

            let rows = '';

            if (!res.data || res.data.length === 0) {
                rows = `
                    <tr>
                        <td colspan="4" class="text-center">
                            No positions found
                        </td>
                    </tr>
                `;

            } else {

                res.data.forEach((position, index) => {
                    rows += `
                        <tr>
                            <td class="ps-4">${index + 1}</td>
                            <td>${position.name}</td>
                            <td>${position.created_at ?? '-'}</td>
                            <td class="text-end pe-4">

                                <button class="btn btn-sm editPositionBtn hris-action"
                                    data-id="${position.id}" aria-label="Edit position" title="Edit position">
                                    <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                </button>

                                <button class="btn btn-sm deletePositionBtn hris-action hris-action--danger"
                                    data-id="${position.id}" aria-label="Delete position" title="Delete position">
                                    <i class="bi bi-trash" aria-hidden="true"></i>
                                </button>

                            </td>
                        </tr>
                    `;

                });

            }

            $('#positionsTableBody').html(rows);

        },

        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Unable to load positions.', 'error');
        }

    });
}

function addPosition(form) {
    let $form = $(form);

    let formData = {
        name: $form.find('[name="name"]').val(),
        csrf_token: $form.find('[name="csrf_token"]').val()
    };

    $.ajax({
        url: '/hris/positions/create',
        type: 'POST',
        data: formData,
        dataType: 'json',

        beforeSend: function () {
            showLoader("Saving position...");
        },

        success: function (res) {

            hideLoader();

            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }

            showToast(res.message, 'success');

            $('#addPositionForm')[0].reset();

            bootstrap.Modal.getInstance(document.getElementById('addPositionModal')).hide();

            getAllPositions();

        },

        error: function (xhr) {

            hideLoader();

            console.log(xhr.responseText);

            showToast('Something went wrong.', 'error');

        }

    });
}

function getPosition(id) {
    showLoader("Loading position...");

    $.ajax({
        url: '/hris/positions/getpositionbyid/' + id,
        type: 'GET',
        dataType: 'json',

        success: function (res) {

            hideLoader();

            if (!res.success) {
                showToast(res.message, 'error');
                return;
            }

            let position = res.data;
            let $form = $('#editPositionForm');

            $form.find('[name="id"]').val(position.id);
            $form.find('[name="name"]').val(position.name);

            bootstrap.Modal.getOrCreateInstance(
                document.getElementById('editPositionModal')
            ).show();

        },

        error: function (xhr) {

            hideLoader();

            console.log(xhr.responseText);

            showToast('Unable to load position.', 'error');

        }

    });
}

function updatePosition(form) {
    let $form = $(form);

    let id = $form.find('[name="id"]').val();

    let formData = {
        name: $form.find('[name="name"]').val(),
        csrf_token: $form.find('[name="csrf_token"]').val()
    };

    $.ajax({
        url: '/hris/positions/update/' + id,
        type: 'POST',
        data: formData,
        dataType: 'json',

        beforeSend: function () {
            showLoader("Updating position...");
        },

        success: function (res) {

            hideLoader();

            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }

            showToast(res.message, 'success');

            bootstrap.Modal.getInstance(
                document.getElementById('editPositionModal')
            ).hide();

            getAllPositions();

        },

        error: function (xhr) {

            hideLoader();

            console.log(xhr.responseText);

            showToast('Something went wrong.', 'error');

        }

    });
}

function deletePosition(id) {

    $.ajax({
        url: '/hris/positions/delete/' + id,
        type: 'POST',
        data: {
            csrf_token: $('input[name="csrf_token"]').first().val()
        },
        dataType: 'json',

        beforeSend: function () {
            bootstrap.Modal.getInstance(document.getElementById('deletePositionModal')).hide();
            showLoader("Deleting position...");
        },

        success: function (res) {

            hideLoader();

            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }

            showToast(res.message, 'success');
            positionIdToDelete = null;
            getAllPositions();

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