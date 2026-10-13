<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_hris/partials/hris_nav.php'; ?>

<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Manage Shifts</h5>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addShiftModal">
                <i class="bi bi-plus-lg"></i> Add Shift
            </button>
        </div>

        <div class="card-body border-bottom">
            <form id="shiftSearchForm" class="row g-2 align-items-end">
                <div class="col-md-6 col-lg-4">
                    <label class="form-label">Search</label>
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
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="shiftsTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4"><button class="btn btn-link p-0 sortable" data-sort="name">Name</button></th>
                            <th><button class="btn btn-link p-0 sortable" data-sort="shift_type">Type</button></th>
                            <th><button class="btn btn-link p-0 sortable" data-sort="start_time">Start</button></th>
                            <th><button class="btn btn-link p-0 sortable" data-sort="end_time">End</button></th>
                            <th><button class="btn btn-link p-0 sortable" data-sort="flex_start_time">Flex Start</button></th>
                            <th><button class="btn btn-link p-0 sortable" data-sort="flex_end_time">Flex End</button></th>
                            <th><button class="btn btn-link p-0 sortable" data-sort="required_hours">Hours</button></th>
                            <th><button class="btn btn-link p-0 sortable" data-sort="break_minutes">Break</button></th>
                            <th><button class="btn btn-link p-0 sortable" data-sort="allow_overtime">OT</button></th>
                            <th><button class="btn btn-link p-0 sortable" data-sort="created_at">Created At</button></th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white py-3">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-end mb-0" id="shiftsPagination"></ul>
            </nav>
        </div>
    </div>
</div>

<div class="modal fade" id="addShiftModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="addShiftForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Add Shift</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Shift Type</label>
                            <select class="form-select shiftTypeSelect" name="shift_type">
                                <option value="FIXED">FIXED</option>
                                <option value="FLEX_WINDOW">FLEX_WINDOW</option>
                                <option value="FLEX_HOURS">FLEX_HOURS</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Start Time</label>
                            <input type="time" class="form-control" name="start_time">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Time</label>
                            <input type="time" class="form-control" name="end_time">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Flex Start</label>
                            <input type="time" class="form-control" name="flex_start_time">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Flex End</label>
                            <input type="time" class="form-control" name="flex_end_time">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Required Hours</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="required_hours" value="8.00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Break Minutes</label>
                            <input type="number" min="0" class="form-control" name="break_minutes" value="60">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Allow Overtime</label>
                            <select class="form-select" name="allow_overtime">
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

<div class="modal fade" id="editShiftModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="editShiftForm">
                <input type="hidden" name="id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Shift</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Shift Type</label>
                            <select class="form-select shiftTypeSelect" name="shift_type">
                                <option value="FIXED">FIXED</option>
                                <option value="FLEX_WINDOW">FLEX_WINDOW</option>
                                <option value="FLEX_HOURS">FLEX_HOURS</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Start Time</label>
                            <input type="time" class="form-control" name="start_time">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Time</label>
                            <input type="time" class="form-control" name="end_time">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Flex Start</label>
                            <input type="time" class="form-control" name="flex_start_time">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Flex End</label>
                            <input type="time" class="form-control" name="flex_end_time">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Required Hours</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="required_hours">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Break Minutes</label>
                            <input type="number" min="0" class="form-control" name="break_minutes">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Allow Overtime</label>
                            <select class="form-select" name="allow_overtime">
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

<div class="modal fade" id="deleteShiftModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Shift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                    <td>${esc(s.created_at) || '-'}</td>
                    <td class="text-end pe-4">
                        <button class="btn btn-link btn-sm text-primary editShiftBtn" data-id="${s.id}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-link btn-sm text-danger deleteShiftBtn" data-id="${s.id}">
                            <i class="bi bi-trash"></i>
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
        html += `<li class="page-item ${p === page ? 'active' : ''}"><a href="#" class="page-link page-btn" data-page="${p}">${p}</a></li>`;
    }

    if (page < totalPages) {
        html += `<li class="page-item"><a href="#" class="page-link page-btn" data-page="${page + 1}">Next &raquo;</a></li>`;
    }

    $('#shiftsPagination').html(html);
}

function applySortIndicators() {
    $('.sortable').each(function () {
        let label = $(this).text().replace(' ASC', '').replace(' DESC', '');
        if ($(this).data('sort') === currentSortBy) {
            label += ' ' + currentSortDir;
        }
        $(this).text(label);
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
