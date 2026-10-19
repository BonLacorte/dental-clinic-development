<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_hris/partials/hris_nav.php'; ?>

<div class="container-fluid py-4 management-page hris-page">
    <div class="card shadow-sm management-card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Manage Holidays</h5>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addHolidayModal">
                <i class="bi bi-plus-lg"></i> Add Holiday
            </button>
        </div>

        <div class="card-body border-bottom">
            <form id="holidaySearchForm" class="row g-2 align-items-end">
                <div class="col-md-6 col-lg-4">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" id="holidaySearchInput" placeholder="Search holidays">
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-secondary" type="button" id="clearHolidaySearchBtn">
                        Clear
                    </button>
                </div>
                <div class="col-auto hris-order-control"><button type="button" class="btn btn-outline-secondary sortable hris-created-sort" data-sort="created_at" aria-pressed="true" aria-label="Date added: newest first. Sort oldest first" title="Sort by date added">Date added: newest first</button></div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="holidaysTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4"><button class="btn btn-link p-0 sortable" data-sort="holiday_date">Holiday Date</button></th>
                            <th><button class="btn btn-link p-0 sortable" data-sort="name">Name</button></th>
                            <th><button class="btn btn-link p-0 sortable" data-sort="holiday_type">Type</button></th>
                            <th><button class="btn btn-link p-0 sortable" data-sort="pay_multiplier">Pay Multiplier</button></th>
                            <th><button class="btn btn-link p-0 sortable" data-sort="allow_work">Allow Work</button></th>
                            <th class="hris-created-at"><button class="btn btn-link p-0 sortable" data-sort="created_at">Created At</button></th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white py-3">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-end mb-0" id="holidaysPagination"></ul>
            </nav>
        </div>
    </div>
</div>

<div class="modal fade" id="addHolidayModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="addHolidayForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Add Holiday</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Holiday Date</label>
                            <input type="date" class="form-control" name="holiday_date">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="holiday_type">
                                <option value="REGULAR">REGULAR</option>
                                <option value="SPECIAL">SPECIAL</option>
                                <option value="COMPANY">COMPANY</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pay Multiplier</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="pay_multiplier" value="1.00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Allow Work</label>
                            <select class="form-select" name="allow_work">
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

<div class="modal fade" id="editHolidayModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="editHolidayForm">
                <input type="hidden" name="id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Holiday</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Holiday Date</label>
                            <input type="date" class="form-control" name="holiday_date">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="holiday_type">
                                <option value="REGULAR">REGULAR</option>
                                <option value="SPECIAL">SPECIAL</option>
                                <option value="COMPANY">COMPANY</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pay Multiplier</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="pay_multiplier">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Allow Work</label>
                            <select class="form-select" name="allow_work">
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

<div class="modal fade" id="deleteHolidayModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Holiday</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this holiday? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                <button class="btn btn-danger" type="button" id="confirmDeleteHolidayBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<?php include '../app/views/partials/admin-footer.php'; ?>

<script>
let currentPage = 1;
let currentSearch = '';
let currentSortBy = 'holiday_date';
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
    getAllHolidays(1);

    $('#holidaySearchForm').on('submit', function (e) {
        e.preventDefault();
        currentSearch = $('#holidaySearchInput').val();
        getAllHolidays(1);
    });

    $('#clearHolidaySearchBtn').on('click', function () {
        $('#holidaySearchInput').val('');
        currentSearch = '';
        getAllHolidays(1);
    });

    $('#addHolidayForm').on('submit', function (e) {
        e.preventDefault();
        addHoliday(this);
    });

    $('#editHolidayForm').on('submit', function (e) {
        e.preventDefault();
        updateHoliday(this);
    });

    $(document).on('click', '.sortable', function () {
        let sortBy = $(this).data('sort');
        currentSortDir = currentSortBy === sortBy && currentSortDir === 'ASC' ? 'DESC' : 'ASC';
        currentSortBy = sortBy;
        getAllHolidays(1);
    });

    $(document).on('click', '.page-btn', function (e) {
        e.preventDefault();
        getAllHolidays($(this).data('page'));
    });

    $(document).on('click', '.editHolidayBtn', function () {
        getHoliday($(this).data('id'));
    });

    $(document).on('click', '.deleteHolidayBtn', function () {
        pendingDeleteId = $(this).data('id');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteHolidayModal')).show();
    });

    $('#confirmDeleteHolidayBtn').on('click', function () {
        if (pendingDeleteId) {
            deleteHoliday(pendingDeleteId);
        }
    });
});

function getAllHolidays(page) {
    showLoader("Loading holidays...");

    $.ajax({
        url: '/hris/holidays/getallholidays',
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
            renderHolidayRows(res.data || []);
            renderPagination(res.currentPage, res.totalPages);
            applySortIndicators();
        },
        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Unable to load holidays.', 'error');
        }
    });
}

function renderHolidayRows(holidays) {
    let rows = '';

    if (holidays.length === 0) {
        rows = '<tr><td colspan="7" class="text-center py-3">No holidays found</td></tr>';
    } else {
        holidays.forEach(h => {
            rows += `
                <tr>
                    <td class="ps-4">${esc(h.holiday_date)}</td>
                    <td>${esc(h.name)}</td>
                    <td>${esc(h.holiday_type) || '-'}</td>
                    <td>${esc(h.pay_multiplier)}</td>
                    <td>${h.allow_work == 1 ? 'YES' : 'NO'}</td>
                    <td class="hris-created-at">${esc(h.created_at) || '-'}</td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm editHolidayBtn hris-action" data-id="${h.id}" aria-label="Edit holiday" title="Edit holiday">
                            <i class="bi bi-pencil-square" aria-hidden="true"></i>
                        </button>
                        <button class="btn btn-sm deleteHolidayBtn hris-action hris-action--danger" data-id="${h.id}" aria-label="Delete holiday" title="Delete holiday">
                            <i class="bi bi-trash" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }

    $('#holidaysTable tbody').html(rows);
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

    $('#holidaysPagination').html(html);
}

function applySortIndicators() {
    $('.sortable').each(function () {
        if (MonteroHris.createdSort(this, currentSortBy, currentSortDir)) return;
        let label = $(this).text().replace(' ASC', '').replace(' DESC', '');
        if ($(this).data('sort') === currentSortBy) {
            label += ' ' + currentSortDir;
        }
        $(this).text(label);
    });
}

function addHoliday(form) {
    $.ajax({
        url: '/hris/holidays/create',
        type: 'POST',
        data: $(form).serialize(),
        dataType: 'json',
        beforeSend: function () {
            showLoader("Saving holiday...");
        },
        success: function (res) {
            hideLoader();
            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }
            showToast(res.message, 'success');
            $('#addHolidayForm')[0].reset();
            bootstrap.Modal.getInstance(document.getElementById('addHolidayModal')).hide();
            getAllHolidays(1);
        },
        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Something went wrong.', 'error');
        }
    });
}

function getHoliday(id) {
    showLoader("Loading holiday...");

    $.ajax({
        url: '/hris/holidays/getholidaybyid/' + id,
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            hideLoader();
            if (!res.success) {
                showToast(res.message, 'error');
                return;
            }

            let h = res.data;
            let $form = $('#editHolidayForm');
            $form.find('[name="id"]').val(h.id);
            $form.find('[name="holiday_date"]').val(h.holiday_date);
            $form.find('[name="name"]').val(h.name);
            $form.find('[name="holiday_type"]').val(h.holiday_type || 'REGULAR');
            $form.find('[name="pay_multiplier"]').val(h.pay_multiplier);
            $form.find('[name="allow_work"]').val(h.allow_work);

            bootstrap.Modal.getOrCreateInstance(document.getElementById('editHolidayModal')).show();
        },
        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Unable to load holiday.', 'error');
        }
    });
}

function updateHoliday(form) {
    let id = $(form).find('[name="id"]').val();

    $.ajax({
        url: '/hris/holidays/update/' + id,
        type: 'POST',
        data: formDataWithoutId(form),
        dataType: 'json',
        beforeSend: function () {
            showLoader("Updating holiday...");
        },
        success: function (res) {
            hideLoader();
            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }
            showToast(res.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('editHolidayModal')).hide();
            getAllHolidays(currentPage);
        },
        error: function (xhr) {
            hideLoader();
            console.log(xhr.responseText);
            showToast('Something went wrong.', 'error');
        }
    });
}

function deleteHoliday(id) {
    $.ajax({
        url: '/hris/holidays/delete/' + id,
        type: 'POST',
        dataType: 'json',
        data: {
            csrf_token: $('#addHolidayForm [name="csrf_token"]').val()
        },
        beforeSend: function () {
            bootstrap.Modal.getInstance(document.getElementById('deleteHolidayModal')).hide();
            showLoader("Deleting holiday...");
        },
        success: function (res) {
            hideLoader();
            pendingDeleteId = null;
            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }
            showToast(res.message, 'success');
            getAllHolidays(currentPage);
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
