<?php
include '../app/views/partials/admin-header.php';
include '../app/views/module_hris/partials/hris_nav.php';
?>

<div class="container-fluid py-4 management-page hris-page organization-page">
    <div class="card shadow-sm management-card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div class="org-page-intro"><p class="org-eyebrow">Organization</p><h1 class="h5 mb-0" id="positionsHeading">Positions</h1><p class="org-page-description">Maintain the position names used for your clinic team.</p></div>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPositionModal">
                <i class="bi bi-plus-lg" aria-hidden="true"></i> Add Position
            </button>
        </div>

        <div class="card-body p-0">
            <div class="org-feedback d-none" id="orgPageFeedback" role="status" aria-live="polite" aria-atomic="true"></div>
            <div class="org-toolbar"><div class="org-search"><label for="positionSearch" class="form-label">Search positions</label><input id="positionSearch" type="search" class="form-control" placeholder="Search position names" aria-controls="positionsTable" disabled></div><button id="clearPositionSearch" type="button" class="btn btn-outline-secondary" disabled>Clear Search</button></div>
            <p id="positionResults" class="org-results" role="status" aria-live="polite"></p>
            <p class="hris-scroll-hint" id="positionsTableScrollHelp">Use horizontal scrolling when columns extend beyond this view.</p>
            <div class="table-responsive hris-table-region" tabindex="0" role="region" aria-labelledby="positionsHeading" aria-describedby="positionsTableScrollHelp">
                <table class="table table-hover align-middle mb-0 hris-data-table" id="positionsTable">
                    <caption class="visually-hidden">Positions and their existing record actions.</caption>
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">Position Name</th>
                            <th scope="col" class="text-end pe-4" style="width: 13rem">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="positionsTableBody">
                        <tr>
                            <td colspan="2" class="text-center">
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
<div class="modal fade hris-dialog organization-dialog" id="addPositionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addPositionModalTitle">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">

            <form id="addPositionForm">

                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="addPositionModalTitle">Add Position</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="org-form-feedback d-none" role="status" aria-live="polite" aria-atomic="true"></div>

                    <div class="mb-3">
                        <label for="addPositionForm_name" class="form-label">Position Name</label>

                        <input id="addPositionForm_name"
                            type="text"
                            class="form-control"
                            name="name"
                            placeholder="e.g. Dental Assistant" aria-describedby="addPositionNameHelp">
                        <p class="form-text" id="addPositionNameHelp">Enter the position name used by your clinic, such as Dentist or Receptionist.</p>
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
<div class="modal fade hris-dialog organization-dialog" id="editPositionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editPositionModalTitle">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">

            <form id="editPositionForm">

                <input type="hidden" name="id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="editPositionModalTitle">Edit Position</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="org-form-feedback d-none" role="status" aria-live="polite" aria-atomic="true"></div>

                    <div class="mb-3">
                        <label for="editPositionForm_name" class="form-label">Position Name</label>

                        <input id="editPositionForm_name"
                            type="text"
                            class="form-control"
                            name="name" placeholder="e.g. Dentist">
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
<div class="modal fade hris-dialog organization-dialog" id="deletePositionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deletePositionModalTitle">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="deletePositionModalTitle">Delete Position</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close dialog"></button>
            </div>

            <div class="modal-body">
                    <div class="org-form-feedback d-none" role="status" aria-live="polite" aria-atomic="true"></div>
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

let positions = [];
let positionIdToDelete = null;

$(document).ready(function () {
    initOrganizationUX();

    getAllPositions();
    $('#positionSearch').on('input', renderPositions);
    $('#clearPositionSearch').on('click', function () {
        $('#positionSearch').val(''); renderPositions(); $('#positionSearch').trigger('focus');
    });

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
        let name = $(this).attr('data-name');

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

function esc(str) {
    return String(str ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;'}[c]));
}
function getAllPositions() {
    $('#positionsTable').attr('aria-busy', 'true');
    $('#positionsTableBody').html('<tr><td colspan="2" class="org-state"><div role="status">Loading positions…</div></td></tr>');
    $('#positionSearch, #clearPositionSearch').prop('disabled', true);
    $.ajax({
        url: '/hris/positions/getallpositions',
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            $('#positionsTable').removeAttr('aria-busy');
            if (!res.success || !Array.isArray(res.data)) { positionReadError(); return; }
            positions = res.data;
            renderPositions();
            $('#positionSearch, #clearPositionSearch').prop('disabled', false);
        },
        error: positionReadError
    });
}
function positionReadError() {
    $('#positionsTable').removeAttr('aria-busy');
    $('#positionsTableBody').html('<tr><td colspan="2" class="org-state"><div role="status">Unable to load positions. <button class="btn btn-outline-secondary" type="button" id="retryPositions">Try Again</button></div></td></tr>');
    $('#positionResults').text('Positions could not be loaded.');
}
$(document).on('click', '#retryPositions', getAllPositions);
function renderPositions() {
    const query = $('#positionSearch').val().trim().toLocaleLowerCase();
    const filtered = positions.filter(position => String(position.name ?? '').toLocaleLowerCase().includes(query));
    $('#positionsTableBody').html(filtered.map(position => `<tr>
        <th scope="row" class="ps-4"><span class="org-record-name">${esc(position.name)}</span><span class="org-record-meta">Created: ${esc(position.created_at ?? '-')}</span></th>
        <td class="text-end pe-4"><div class="org-row-actions">
            <button type="button" class="btn btn-sm editPositionBtn hris-action" data-id="${esc(position.id)}" aria-label="Edit position ${esc(position.name)}"><i class="bi bi-pencil-square" aria-hidden="true"></i> Edit</button>
            <button type="button" class="btn btn-sm deletePositionBtn hris-action hris-action--danger" data-id="${esc(position.id)}" data-name="${esc(position.name)}" aria-label="Delete position ${esc(position.name)}"><i class="bi bi-trash" aria-hidden="true"></i> Delete</button>
        </div></td></tr>`).join('') || '<tr><td colspan="2" class="org-state">' + (positions.length ? 'No positions match your search.' : 'No positions found. Add a position to get started.') + '</td></tr>');
    $('#positionResults').text(filtered.length + ' of ' + positions.length + ' positions');
}

function addPosition(form) {
    let $form = $(form);
    if (!orgBegin($form)) return;

    let formData = {
        name: $form.find('[name="name"]').val(),
        csrf_token: $form.find('[name="csrf_token"]').val()
    };

    $.ajax({
        url: '/hris/positions/create',
        type: 'POST',
        data: formData,
        dataType: 'json',
        complete: function () { orgEnd($form); },

        beforeSend: function () {
            orgFeedback("Saving position...", 'info', $form);
        },

        success: function (res) {

            if (!res.success) {
                orgFeedback(res.message || 'Please check the position name and try again.', 'error', $form);
                return;
            }

            orgEnd($form);
            orgFeedback(res.message, 'success');

            $('#addPositionForm')[0].reset();

            bootstrap.Modal.getInstance(document.getElementById('addPositionModal')).hide();

            getAllPositions();

        },

        error: function (xhr) {

            orgFeedback('Unable to save this position. Your entry has been kept; try again.', 'error', $form);

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

            showToast('Unable to load position.', 'error');

        }

    });
}

function updatePosition(form) {
    let $form = $(form);
    if (!orgBegin($form)) return;

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
        complete: function () { orgEnd($form); },

        beforeSend: function () {
            orgFeedback("Updating position...", 'info', $form);
        },

        success: function (res) {

            if (!res.success) {
                orgFeedback(res.message || 'Please check the position name and try again.', 'error', $form);
                return;
            }

            orgEnd($form);
            orgFeedback(res.message, 'success');

            bootstrap.Modal.getInstance(
                document.getElementById('editPositionModal')
            ).hide();

            getAllPositions();

        },

        error: function (xhr) {

            orgFeedback('Unable to save this position. Your entry has been kept; try again.', 'error', $form);

        }

    });
}

function deletePosition(id) {
    const $scope = $('#confirmDeletePositionBtn');
    if (!orgBegin($scope)) return;

    $.ajax({
        url: '/hris/positions/delete/' + id,
        type: 'POST',
        data: {
            csrf_token: $('input[name="csrf_token"]').first().val()
        },
        dataType: 'json',
        complete: function () { orgEnd($scope); },

        beforeSend: function () {
            bootstrap.Modal.getInstance(document.getElementById('deletePositionModal')).hide();
            orgFeedback("Deleting position...", 'info', null);
        },

        success: function (res) {

            if (!res.success) {
                orgFeedback(res.message || 'Unable to delete this position.', 'error');
                return;
            }

            orgFeedback(res.message, 'success');
            positionIdToDelete = null;
            getAllPositions();

        },

        error: function (xhr) {
            orgFeedback('Unable to delete this position. Try again.', 'error');
        }
    });
}

// Organization-only feedback and pending state. Messages are always rendered as text.
function orgFeedback(message, type = 'info', form = null) {
    const $target = form ? $(form).find('.org-form-feedback').first() : $('#orgPageFeedback');
    $target.removeClass('d-none org-feedback--error org-feedback--success')
        .toggleClass('org-feedback--error', type === 'error')
        .toggleClass('org-feedback--success', type === 'success')
        .text(message && typeof message === 'object' ? Object.values(message).join(' ') : (message || ''));
    if (form && type === 'error') $target.attr('tabindex', '-1').trigger('focus');
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
        // Menu items become hidden on dismissal; return to their visible trigger.
        returnFocus = this.closest('.dropdown')?.querySelector('[data-bs-toggle="dropdown"]') || this;
    });
    $('.organization-dialog').on('show.bs.modal', function () {
        $(this).find('.org-form-feedback').addClass('d-none').empty();
    }).on('shown.bs.modal', function () {
        const target = $(this).find('input:not([type="hidden"]):not([readonly]), select, textarea').filter(':visible:enabled').first()[0]
            || $(this).find('.btn-close')[0];
        // Destructive dialogs initially focus Cancel, never the destructive action.
        const cancel = $(this).find('.modal-footer [data-bs-dismiss="modal"]')[0];
        (this.id.startsWith('delete') ? cancel : target)?.focus();
    }).on('hide.bs.modal', function (event) {
        const $form = $(this).find('form');
        if ($form.data('orgPending')) {
            event.preventDefault();
            orgFeedback('Please wait while your changes are being saved.', 'info', $form);
        }
    }).on('hidden.bs.modal', function () {
        if (returnFocus?.isConnected) returnFocus.focus();
        else $('.organization-page h1').attr('tabindex', '-1').trigger('focus');
    });
}

</script>

</body>
</html>