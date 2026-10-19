<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_hris/partials/hris_nav.php'; ?>

<div class="container-fluid py-4 management-page hris-page">
    <div class="card shadow-sm management-card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h1 class="h5 mb-0" id="shiftsHeading">Manage Shifts</h1>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addShiftModal">
                <i class="bi bi-plus-lg"></i> Add Shift
            </button>
        </div>

        <div class="card-body border-bottom">
            <form id="shiftSearchForm" class="row g-2 align-items-end">
                <div class="col-md-6 col-lg-4">
                    <label for="shiftSearchInput" class="form-label">Search</label>
                    <input type="text" class="form-control" id="shiftSearchInput" placeholder="Search shifts">
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-secondary" type="button" id="clearShiftSearchBtn">
                        Clear
                    </button>
                </div>
                <div class="col-auto hris-order-control"><button type="button" class="btn btn-outline-secondary sortable hris-created-sort" data-sort="created_at" aria-pressed="true" aria-label="Date added: newest first. Sort oldest first" title="Sort by date added">Date added: newest first</button></div>
            </form>
        </div>

        <div class="card-body p-0">
            <p class="hris-scroll-hint" id="shiftsTableScrollHelp">Use horizontal scrolling when columns extend beyond this view.</p>
            <div class="table-responsive hris-table-region" tabindex="0" role="region" aria-labelledby="shiftsHeading" aria-describedby="shiftsTableScrollHelp">
                <table class="table table-hover align-middle mb-0 hris-data-table" id="shiftsTable">
                    <caption class="visually-hidden">Shift times, required hours, breaks and overtime settings.</caption>
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4"><button class="btn btn-link p-0 sortable" data-sort="name">Name</button></th>
                            <th scope="col"><button class="btn btn-link p-0 sortable" data-sort="shift_type">Type</button></th>
                            <th scope="col"><button class="btn btn-link p-0 sortable" data-sort="start_time">Start</button></th>
                            <th scope="col"><button class="btn btn-link p-0 sortable" data-sort="end_time">End</button></th>
                            <th scope="col"><button class="btn btn-link p-0 sortable" data-sort="flex_start_time">Flex Start</button></th>
                            <th scope="col"><button class="btn btn-link p-0 sortable" data-sort="flex_end_time">Flex End</button></th>
                            <th scope="col"><button class="btn btn-link p-0 sortable" data-sort="required_hours">Hours</button></th>
                            <th scope="col"><button class="btn btn-link p-0 sortable" data-sort="break_minutes">Break</button></th>
                            <th scope="col"><button class="btn btn-link p-0 sortable" data-sort="allow_overtime">OT</button></th>
                            <th scope="col" class="hris-created-at"><button class="btn btn-link p-0 sortable" data-sort="created_at">Created At</button></th>
                            <th scope="col" class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white py-3">
            <nav aria-label="Shift pages">
                <ul class="pagination justify-content-end mb-0" id="shiftsPagination"></ul>
            </nav>
        </div>
    </div>
</div>

<div class="modal fade hris-dialog" id="addShiftModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addShiftModalTitle">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="addShiftForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="addShiftModalTitle">Add Shift</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="addShiftForm_name" class="form-label">Name</label>
                            <input id="addShiftForm_name" type="text" class="form-control" name="name">
                        </div>
                        <div class="col-md-6">
                            <label for="addShiftForm_shift_type" class="form-label">Shift Type</label>
                            <select id="addShiftForm_shift_type" class="form-select shiftTypeSelect" name="shift_type">
                                <option value="FIXED">FIXED</option>
                                <option value="FLEX_WINDOW">FLEX_WINDOW</option>
                                <option value="FLEX_HOURS">FLEX_HOURS</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="addShiftForm_start_time" class="form-label">Start Time</label>
                            <input id="addShiftForm_start_time" type="time" class="form-control" name="start_time">
                        </div>
                        <div class="col-md-3">
                            <label for="addShiftForm_end_time" class="form-label">End Time</label>
                            <input id="addShiftForm_end_time" type="time" class="form-control" name="end_time">
                        </div>
                        <div class="col-md-3">
                            <label for="addShiftForm_flex_start_time" class="form-label">Flex Start</label>
                            <input id="addShiftForm_flex_start_time" type="time" class="form-control" name="flex_start_time">
                        </div>
                        <div class="col-md-3">
                            <label for="addShiftForm_flex_end_time" class="form-label">Flex End</label>
                            <input id="addShiftForm_flex_end_time" type="time" class="form-control" name="flex_end_time">
                        </div>
                        <div class="col-md-4">
                            <label for="addShiftForm_required_hours" class="form-label">Required Hours</label>
                            <input id="addShiftForm_required_hours" type="number" step="0.01" min="0" class="form-control" name="required_hours" value="8.00">
                        </div>
                        <div class="col-md-4">
                            <label for="addShiftForm_break_minutes" class="form-label">Break Minutes</label>
                            <input id="addShiftForm_break_minutes" type="number" min="0" class="form-control" name="break_minutes" value="60">
                        </div>
                        <div class="col-md-4">
                            <label for="addShiftForm_allow_overtime" class="form-label">Allow Overtime</label>
                            <select id="addShiftForm_allow_overtime" class="form-select" name="allow_overtime">
                                <option value="1">YES</option>
                                <option value="0">NO</option>
                            </select>
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

<div class="modal fade hris-dialog" id="editShiftModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editShiftModalTitle">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="editShiftForm">
                <input type="hidden" name="id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="editShiftModalTitle">Edit Shift</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="editShiftForm_name" class="form-label">Name</label>
                            <input id="editShiftForm_name" type="text" class="form-control" name="name">
                        </div>
                        <div class="col-md-6">
                            <label for="editShiftForm_shift_type" class="form-label">Shift Type</label>
                            <select id="editShiftForm_shift_type" class="form-select shiftTypeSelect" name="shift_type">
                                <option value="FIXED">FIXED</option>
                                <option value="FLEX_WINDOW">FLEX_WINDOW</option>
                                <option value="FLEX_HOURS">FLEX_HOURS</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="editShiftForm_start_time" class="form-label">Start Time</label>
                            <input id="editShiftForm_start_time" type="time" class="form-control" name="start_time">
                        </div>
                        <div class="col-md-3">
                            <label for="editShiftForm_end_time" class="form-label">End Time</label>
                            <input id="editShiftForm_end_time" type="time" class="form-control" name="end_time">
                        </div>
                        <div class="col-md-3">
                            <label for="editShiftForm_flex_start_time" class="form-label">Flex Start</label>
                            <input id="editShiftForm_flex_start_time" type="time" class="form-control" name="flex_start_time">
                        </div>
                        <div class="col-md-3">
                            <label for="editShiftForm_flex_end_time" class="form-label">Flex End</label>
                            <input id="editShiftForm_flex_end_time" type="time" class="form-control" name="flex_end_time">
                        </div>
                        <div class="col-md-4">
                            <label for="editShiftForm_required_hours" class="form-label">Required Hours</label>
                            <input id="editShiftForm_required_hours" type="number" step="0.01" min="0" class="form-control" name="required_hours">
                        </div>
                        <div class="col-md-4">
                            <label for="editShiftForm_break_minutes" class="form-label">Break Minutes</label>
                            <input id="editShiftForm_break_minutes" type="number" min="0" class="form-control" name="break_minutes">
                        </div>
                        <div class="col-md-4">
                            <label for="editShiftForm_allow_overtime" class="form-label">Allow Overtime</label>
                            <select id="editShiftForm_allow_overtime" class="form-select" name="allow_overtime">
                                <option value="1">YES</option>
                                <option value="0">NO</option>
                            </select>
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

<div class="modal fade hris-dialog" id="deleteShiftModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteShiftModalTitle">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteShiftModalTitle">Delete Shift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this shift? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                <button class="btn btn-danger" type="button" id="confirmDeleteShiftBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<?php include '../app/views/partials/admin-footer.php'; ?>

<script>
let currentPage = 1;
let currentSearch = '';
let currentSortBy = 'created_at';
let currentSortDir = 'DESC';
let pendingDeleteId = null;

function esc(str) {
    return String(str ?? '').replace(/[&<>"']/g, c => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    }[c]));
}

function formDataWithoutId(form) {
    return $.param($(form).serializeArray().filter(item => item.name !== 'id'));
}

$(document).ready(function () {
    getAllShifts(1);
    updateShiftFields('#addShiftForm');
    updateShiftFields('#editShiftForm');

    $('.shiftTypeSelect').on('change', function () {
        updateShiftFields('#' + $(this).closest('form').attr('id'));
    });

    $('#addShiftModal').on('show.bs.modal', function () {
        updateShiftFields('#addShiftForm');
    });

    $('#shiftSearchForm').on('submit', function (e) {
        e.preventDefault();
        currentSearch = $('#shiftSearchInput').val();
        getAllShifts(1);
    });

    $('#clearShiftSearchBtn').on('click', function () {
        $('#shiftSearchInput').val('');
        currentSearch = '';
        getAllShifts(1);
    });

    $('#addShiftForm').on('submit', function (e) {
        e.preventDefault();
        addShift(this);
    });

    $('#editShiftForm').on('submit', function (e) {
        e.preventDefault();
        updateShift(this);
    });

    $(document).on('click', '.sortable', function () {
        let sortBy = $(this).data('sort');
        currentSortDir = currentSortBy === sortBy && currentSortDir === 'ASC' ? 'DESC' : 'ASC';
        currentSortBy = sortBy;
        getAllShifts(1);
    });

    $(document).on('click', '.page-btn', function (e) {
        e.preventDefault();
        getAllShifts($(this).data('page'));
    });

    $(document).on('click', '.editShiftBtn', function () {
        getShift($(this).data('id'));
    });

    $(document).on('click', '.deleteShiftBtn', function () {
        pendingDeleteId = $(this).data('id');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteShiftModal')).show();
    });

    $('#confirmDeleteShiftBtn').on('click', function () {
        if (pendingDeleteId) {
            deleteShift(pendingDeleteId);
        }
    });
});

function getAllShifts(page) {
    showLoader("Loading shifts...");

    $.ajax({
        url: '/hris/shifts/getallshifts',
        type: 'GET',
        dataType: 'json',
        data: {
            page: page,
            search: currentSearch,
            sort_by: currentSortBy,
            sort_dir: currentSortDir
        },
        success: function (res) {
            hideLoader();
            currentPage = res.currentPage;
            renderShiftRows(res.data || []);
            renderPagination(res.currentPage, res.totalPages);
            applySortIndicators();
        },
        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Unable to load shifts.', 'error');
        }
    });
}

function renderShiftRows(shifts) {
    let rows = '';

    if (shifts.length === 0) {
        rows = '<tr><td colspan="11" class="text-center py-3">No shifts found</td></tr>';
    } else {
        shifts.forEach(s => {
            rows += `
                <tr>
                    <td class="ps-4">${esc(s.name)}</td>
                    <td>${esc(s.shift_type)}</td>
                    <td>${esc(s.start_time) || '-'}</td>
                    <td>${esc(s.end_time) || '-'}</td>
                    <td>${esc(s.flex_start_time) || '-'}</td>
                    <td>${esc(s.flex_end_time) || '-'}</td>
                    <td>${esc(s.required_hours)}</td>
                    <td>${esc(s.break_minutes)}</td>
                    <td>${s.allow_overtime == 1 ? 'YES' : 'NO'}</td>
                    <td class="hris-created-at">${esc(s.created_at) || '-'}</td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm editShiftBtn hris-action" data-id="${s.id}" aria-label="Edit shift" title="Edit shift">
                            <i class="bi bi-pencil-square" aria-hidden="true"></i>
                        </button>
                        <button class="btn btn-sm deleteShiftBtn hris-action hris-action--danger" data-id="${s.id}" aria-label="Delete shift" title="Delete shift">
                            <i class="bi bi-trash" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }

    $('#shiftsTable tbody').html(rows);
}

function updateShiftFields(formSelector) {
    let $form = $(formSelector);
    let shiftType = $form.find('[name="shift_type"]').val();
    let $start = $form.find('[name="start_time"]');
    let $end = $form.find('[name="end_time"]');
    let $flexStart = $form.find('[name="flex_start_time"]');
    let $flexEnd = $form.find('[name="flex_end_time"]');
    let $requiredHours = $form.find('[name="required_hours"]');

    $start.prop('required', false).prop('disabled', false);
    $end.prop('required', false).prop('disabled', false);
    $flexStart.prop('required', false).prop('disabled', false);
    $flexEnd.prop('required', false).prop('disabled', false);
    $requiredHours.prop('required', true);

    if (shiftType === 'FIXED') {
        $start.prop('required', true);
        $end.prop('required', true);
        $flexStart.val('').prop('disabled', true);
        $flexEnd.val('').prop('disabled', true);
    }

    if (shiftType === 'FLEX_WINDOW') {
        $start.prop('required', true);
        $end.prop('required', true);
        $flexStart.prop('required', true);
        $flexEnd.prop('required', true);
    }

    if (shiftType === 'FLEX_HOURS') {
        $start.val('').prop('disabled', true);
        $end.val('').prop('disabled', true);
        $flexStart.val('').prop('disabled', true);
        $flexEnd.val('').prop('disabled', true);
    }
}

function normalizeShiftType(shift) {
    let shiftType = shift.shift_type;
    let allowed = ['FIXED', 'FLEX_WINDOW', 'FLEX_HOURS'];

    if (allowed.includes(shiftType)) {
        return shiftType;
    }

    if (shift.flex_start_time || shift.flex_end_time) {
        return 'FLEX_WINDOW';
    }

    if (shift.start_time || shift.end_time) {
        return 'FIXED';
    }

    return 'FLEX_HOURS';
}

function renderPagination(page, totalPages) {
    let start = Math.max(1, page - 2);
    let end = Math.min(totalPages, page + 2);
    let html = '';

    if (page > 1) {
        html += `<li class="page-item"><a href="#" class="page-link page-btn" data-page="${page - 1}">&laquo; Prev</a></li>`;
    }

    for (let p = start; p <= end; p++) {
        html += `<li class="page-item ${p === page ? 'active' : ''}"><a href="#" class="page-link page-btn" data-page="${p}"${p === page ? ' aria-current="page"' : ''}>${p}</a></li>`;
    }

    if (page < totalPages) {
        html += `<li class="page-item"><a href="#" class="page-link page-btn" data-page="${page + 1}">Next &raquo;</a></li>`;
    }

    $('#shiftsPagination').html(html);
}

function applySortIndicators() {
    $('.sortable').each(function () {
        const $button = $(this);
        const $header = $button.closest('th');
        $header.removeAttr('aria-sort');
        if (MonteroHris.createdSort(this, currentSortBy, currentSortDir)) return;
        const label = $button.data('label') || $button.text().replace(/\s+(ASC|DESC)$/, '').trim();
        const active = $button.data('sort') === currentSortBy;
        const ascending = currentSortDir === 'ASC';
        $button.data('label', label);
        if (active) $header.attr('aria-sort', ascending ? 'ascending' : 'descending');
        $button.empty().append(document.createTextNode(label));
        $button.append($('<i>', {
            class: 'bi hris-sort-mark ' + (active ? (ascending ? 'bi-arrow-up' : 'bi-arrow-down') : 'bi-arrow-down-up'),
            'aria-hidden': 'true'
        }));
        // Retain the original direction text for print; screen indicators are decorative.
        if (active) $button.append($('<span>', {class: 'hris-sort-print', 'aria-hidden': 'true', text: ' ' + currentSortDir}));
        $button.attr('aria-label', label + (active ? ': sorted ' + (ascending ? 'ascending' : 'descending') + '. ' : ': ') + 'Sort ' + (active && ascending ? 'descending' : 'ascending'));
    });
}

function addShift(form) {
    $.ajax({
        url: '/hris/shifts/create',
        type: 'POST',
        data: $(form).serialize(),
        dataType: 'json',
        beforeSend: function () {
            showLoader("Saving shift...");
        },
        success: function (res) {
            hideLoader();
            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }
            showToast(res.message, 'success');
            $('#addShiftForm')[0].reset();
            updateShiftFields('#addShiftForm');
            bootstrap.Modal.getInstance(document.getElementById('addShiftModal')).hide();
            getAllShifts(1);
        },
        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Something went wrong.', 'error');
        }
    });
}

function getShift(id) {
    showLoader("Loading shift...");

    $.ajax({
        url: '/hris/shifts/getshiftbyid/' + id,
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            hideLoader();
            if (!res.success) {
                showToast(res.message, 'error');
                return;
            }

            let s = res.data;
            let $form = $('#editShiftForm');
            let shiftType = normalizeShiftType(s);

            $form.find('[name="id"]').val(s.id);
            $form.find('[name="name"]').val(s.name);
            $form.find('[name="shift_type"]').val(shiftType);
            $form.find('[name="start_time"]').val(s.start_time);
            $form.find('[name="end_time"]').val(s.end_time);
            $form.find('[name="flex_start_time"]').val(s.flex_start_time);
            $form.find('[name="flex_end_time"]').val(s.flex_end_time);
            $form.find('[name="required_hours"]').val(s.required_hours);
            $form.find('[name="break_minutes"]').val(s.break_minutes);
            $form.find('[name="allow_overtime"]').val(s.allow_overtime);
            updateShiftFields('#editShiftForm');

            bootstrap.Modal.getOrCreateInstance(document.getElementById('editShiftModal')).show();
        },
        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Unable to load shift.', 'error');
        }
    });
}

function updateShift(form) {
    let id = $(form).find('[name="id"]').val();

    $.ajax({
        url: '/hris/shifts/update/' + id,
        type: 'POST',
        data: formDataWithoutId(form),
        dataType: 'json',
        beforeSend: function () {
            showLoader("Updating shift...");
        },
        success: function (res) {
            hideLoader();
            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }
            showToast(res.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('editShiftModal')).hide();
            getAllShifts(currentPage);
        },
        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Something went wrong.', 'error');
        }
    });
}

function deleteShift(id) {
    $.ajax({
        url: '/hris/shifts/delete/' + id,
        type: 'POST',
        dataType: 'json',
        data: {
            csrf_token: $('#addShiftForm [name="csrf_token"]').val()
        },
        beforeSend: function () {
            bootstrap.Modal.getInstance(document.getElementById('deleteShiftModal')).hide();
            showLoader("Deleting shift...");
        },
        success: function (res) {
            hideLoader();
            pendingDeleteId = null;
            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }
            showToast(res.message, 'success');
            getAllShifts(currentPage);
        },
        error: function (xhr) {
            hideLoader();
            pendingDeleteId = null;
            console.log(xhr.responseText);
            showToast('Something went wrong.', 'error');
        }
    });
}
</script>
</body>
</html>
