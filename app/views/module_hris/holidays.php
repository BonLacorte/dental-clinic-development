<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_hris/partials/hris_nav.php'; ?>

<div class="container-fluid py-4 management-page hris-page attendance-workspace hris-consistency-page">
    <div class="card shadow-sm management-card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div class="attendance-heading">
                <p class="attendance-eyebrow d-print-none">HRIS · Attendance</p>
                <h1 class="h5 mb-0" id="holidaysHeading">Holidays</h1>
                <p class="hris-page-description d-print-none">Review holiday dates, classifications and existing work and pay settings.</p>
            </div>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addHolidayModal">
                <i class="bi bi-plus-lg"></i> Add Holiday
            </button>
        </div>

        <div class="card-body border-bottom">
            <form id="holidaySearchForm" class="row g-2 align-items-end">
                <div class="col-md-6 col-lg-4">
                    <label for="holidaySearchInput" class="form-label">Search</label>
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
                <div class="col-auto hris-order-control"><button type="button" class="btn btn-outline-secondary sortable hris-created-sort" data-sort="created_at" aria-pressed="false" aria-label="Date added. Sort oldest first" title="Sort by date added">Date added</button></div>
            </form>
        </div>

        <div class="card-body p-0">
            <p class="hris-scroll-hint" id="holidaysTableScrollHelp">Use horizontal scrolling when columns extend beyond this view.</p>
            <div class="table-responsive hris-table-region" tabindex="0" role="region" aria-labelledby="holidaysHeading" aria-describedby="holidaysTableScrollHelp">
                <table class="table table-hover align-middle mb-0 hris-data-table" id="holidaysTable">
                    <caption class="visually-hidden">Holiday dates, types, pay multipliers and work settings.</caption>
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4"><button class="btn btn-link p-0 sortable" data-sort="holiday_date">Holiday Date</button></th>
                            <th scope="col"><button class="btn btn-link p-0 sortable" data-sort="name">Name</button></th>
                            <th scope="col"><button class="btn btn-link p-0 sortable" data-sort="holiday_type">Type</button></th>
                            <th scope="col"><button class="btn btn-link p-0 sortable" data-sort="pay_multiplier">Pay Multiplier</button></th>
                            <th scope="col"><button class="btn btn-link p-0 sortable" data-sort="allow_work">Allow Work</button></th>
                            <th scope="col" class="hris-created-at"><button class="btn btn-link p-0 sortable" data-sort="created_at">Created At</button></th>
                            <th scope="col" class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white py-3">
            <nav aria-label="Holiday pages">
                <ul class="pagination justify-content-end mb-0" id="holidaysPagination"></ul>
            </nav>
        </div>
    </div>
</div>

<div class="modal fade hris-dialog payroll-items-dialog hris-payroll-dialog" id="addHolidayModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addHolidayModalTitle">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="addHolidayForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h2 class="modal-title" id="addHolidayModalTitle">Add Holiday</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <p class="payroll-dialog-context">Add a new holiday.</p>
                    <div class="payroll-form-fields">
                        <fieldset class="payroll-form-section"><legend>Holiday details</legend><div class="row g-3">
                            <div class="col-md-6">
                                                        <label for="addHolidayForm_holiday_date" class="form-label payroll-field"><span>Holiday Date</span>
                                                        <input id="addHolidayForm_holiday_date" type="date" class="form-control" name="holiday_date">
                                                    </label>
                            </div>
                            <div class="col-md-6">
                                                        <label for="addHolidayForm_name" class="form-label payroll-field"><span>Name</span>
                                                        <input id="addHolidayForm_name" type="text" class="form-control" name="name">
                                                    </label>
                            </div>
                        </div></fieldset>
                        <fieldset class="payroll-form-section"><legend>Work and pay settings</legend><div class="row g-3">
                            <div class="col-md-4">
                                                        <label for="addHolidayForm_holiday_type" class="form-label payroll-field"><span>Type</span>
                                                        <select id="addHolidayForm_holiday_type" class="form-select" name="holiday_type">
                                                            <option value="REGULAR">REGULAR</option>
                                                            <option value="SPECIAL">SPECIAL</option>
                                                            <option value="COMPANY">COMPANY</option>
                                                        </select>
                                                    </label>
                            </div>
                            <div class="col-md-4">
                                                        <label for="addHolidayForm_pay_multiplier" class="form-label payroll-field"><span>Pay Multiplier</span>
                                                        <input id="addHolidayForm_pay_multiplier" type="number" step="0.01" min="0" class="form-control" name="pay_multiplier" value="1.00">
                                                    </label>
                            </div>
                            <div class="col-md-4">
                                                        <label for="addHolidayForm_allow_work" class="form-label payroll-field"><span>Allow Work</span>
                                                        <select id="addHolidayForm_allow_work" class="form-select" name="allow_work">
                                                            <option value="1">YES</option>
                                                            <option value="0">NO</option>
                                                        </select>
                                                    </label>
                            </div>
                        </div></fieldset>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade hris-dialog payroll-items-dialog hris-payroll-dialog" id="editHolidayModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editHolidayModalTitle">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="editHolidayForm">
                <input type="hidden" name="id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h2 class="modal-title" id="editHolidayModalTitle">Edit Holiday</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <p class="payroll-dialog-context">Review and update this holiday.</p>
                    <div class="payroll-form-fields">
                        <fieldset class="payroll-form-section"><legend>Holiday details</legend><div class="row g-3">
                            <div class="col-md-6">
                                                        <label for="editHolidayForm_holiday_date" class="form-label payroll-field"><span>Holiday Date</span>
                                                        <input id="editHolidayForm_holiday_date" type="date" class="form-control" name="holiday_date">
                                                    </label>
                            </div>
                            <div class="col-md-6">
                                                        <label for="editHolidayForm_name" class="form-label payroll-field"><span>Name</span>
                                                        <input id="editHolidayForm_name" type="text" class="form-control" name="name">
                                                    </label>
                            </div>
                        </div></fieldset>
                        <fieldset class="payroll-form-section"><legend>Work and pay settings</legend><div class="row g-3">
                            <div class="col-md-4">
                                                        <label for="editHolidayForm_holiday_type" class="form-label payroll-field"><span>Type</span>
                                                        <select id="editHolidayForm_holiday_type" class="form-select" name="holiday_type">
                                                            <option value="REGULAR">REGULAR</option>
                                                            <option value="SPECIAL">SPECIAL</option>
                                                            <option value="COMPANY">COMPANY</option>
                                                        </select>
                                                    </label>
                            </div>
                            <div class="col-md-4">
                                                        <label for="editHolidayForm_pay_multiplier" class="form-label payroll-field"><span>Pay Multiplier</span>
                                                        <input id="editHolidayForm_pay_multiplier" type="number" step="0.01" min="0" class="form-control" name="pay_multiplier">
                                                    </label>
                            </div>
                            <div class="col-md-4">
                                                        <label for="editHolidayForm_allow_work" class="form-label payroll-field"><span>Allow Work</span>
                                                        <select id="editHolidayForm_allow_work" class="form-select" name="allow_work">
                                                            <option value="1">YES</option>
                                                            <option value="0">NO</option>
                                                        </select>
                                                    </label>
                            </div>
                        </div></fieldset>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade hris-dialog payroll-items-dialog hris-payroll-dialog" id="deleteHolidayModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteHolidayModalTitle">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="deleteHolidayModalTitle">Delete Holiday</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Are you sure you want to delete this holiday? This action cannot be undone.</p>
                <p class="payroll-delete-context" id="deleteHolidayModalContext"></p>
                <p class="payroll-field-help mb-0" id="deleteHolidayModalDetail"></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
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

function formatAttendanceDialogs() {
    $('.hris-payroll-dialog').on('show.bs.modal', function (event) {
        $(this).data('format-origin', event.relatedTarget || document.activeElement);
    }).on('shown.bs.modal', function () {
        const $modal = $(this);
        const $initial = $modal.find('form').length
            ? $modal.find('input:not([type="hidden"]), select, textarea').filter(':visible:enabled').first()
            : $modal.find('.modal-footer [data-bs-dismiss="modal"]').first();
        $initial.trigger('focus');
    }).on('hidden.bs.modal', function () {
        const origin = $(this).data('format-origin');
        if (origin && origin.isConnected && typeof origin.focus === 'function') origin.focus();
    }).on('keydown', function (event) {
        if (event.key !== 'Tab') return;
        const focusable = $(this).find('button, a[href], input:not([type="hidden"]), select, textarea, [tabindex="0"]').filter(':visible:enabled').toArray();
        if (!focusable.length) return;
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
        else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
    });
}

$(document).ready(function () {
    formatAttendanceDialogs();
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
        const $cells = $(this).closest('tr').children('td');
        $('#deleteHolidayModalContext').text($cells.eq(1).text().trim());
        $('#deleteHolidayModalDetail').text($cells.eq(0).text().trim());
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
        html += `<li class="page-item ${p === page ? 'active' : ''}"><a href="#" class="page-link page-btn" data-page="${p}"${p === page ? ' aria-current="page"' : ''}>${p}</a></li>`;
    }

    if (page < totalPages) {
        html += `<li class="page-item"><a href="#" class="page-link page-btn" data-page="${page + 1}">Next &raquo;</a></li>`;
    }

    $('#holidaysPagination').html(html);
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
