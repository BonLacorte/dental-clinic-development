<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_hris/partials/hris_nav.php'; ?>

<div class="container-fluid py-4 management-page hris-page attendance-workspace">
    <div class="card shadow-sm management-card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div class="attendance-heading"><p class="attendance-eyebrow">HRIS · Attendance</p><h1 class="h4 mb-1" tabindex="-1" id="holidaysHeading">Holidays</h1><p class="text-muted mb-0">Review holiday dates, classifications and existing work and pay settings.</p></div>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addHolidayModal">
                <i class="bi bi-plus-lg" aria-hidden="true"></i> Add Holiday
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
                        <i class="bi bi-search" aria-hidden="true"></i> Search
                    </button>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-secondary" type="button" id="clearHolidaySearchBtn">
                        Clear
                    </button>
                </div>
                <div class="col-auto attendance-sort-control"><label class="form-label" for="holidaySortBy">Sort by</label><div class="d-flex gap-2"><select class="form-select" id="holidaySortBy"><option value="holiday_date" selected>Holiday date</option><option value="name">Holiday name</option><option value="holiday_type">Holiday type</option><option value="pay_multiplier">Pay multiplier</option><option value="allow_work">Work allowed</option><option value="created_at">Date added</option></select><button type="button" id="holidaySortByDirection" class="btn btn-outline-secondary">Descending ↓</button></div></div>
            </form>
        </div>

        <div class="card-body p-0">
            <p class="attendance-list-status px-4 pt-3 mb-0" role="status" aria-live="polite"></p>
                    <p class="hris-scroll-hint" id="holidaysTableScrollHelp">Use horizontal scrolling when columns extend beyond this view.</p>
            <div class="table-responsive hris-table-region" tabindex="0" role="region" aria-labelledby="holidaysHeading" aria-describedby="holidaysTableScrollHelp">
                <table class="table table-hover align-middle mb-0 hris-data-table" id="holidaysTable">
                    <caption class="visually-hidden">Holiday dates, types, pay multipliers and work settings.</caption>
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4"><button class="btn btn-link p-0 sortable" data-sort="name">Holiday Name &amp; Date</button></th>
                            <th scope="col"><button class="btn btn-link p-0 sortable" data-sort="holiday_type">Type</button></th>
                            <th scope="col"><button class="btn btn-link p-0 sortable" data-sort="pay_multiplier">Pay Multiplier</button></th>
                            <th scope="col"><button class="btn btn-link p-0 sortable" data-sort="allow_work">Work Allowed</button></th>
                            <th scope="col" class="hris-created-at attendance-created"><button class="btn btn-link p-0 sortable" data-sort="created_at">Created At</button></th>
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

<div class="modal fade hris-dialog attendance-dialog" id="addHolidayModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addHolidayModalTitle">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="addHolidayForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="addHolidayModalTitle">Add Holiday</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="attendance-feedback alert alert-warning" role="status" aria-live="polite" tabindex="-1" hidden></div>
                    <div class="attendance-discard alert alert-warning" hidden>
                        <p>Leave this dialog and discard your unsaved changes?</p>
                        <button type="button" class="btn btn-secondary attendance-stay">Stay and edit</button>
                        <button type="button" class="btn btn-outline-danger attendance-discard-confirm">Discard changes</button>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="addHolidayForm_holiday_date" class="form-label">Holiday Date <span class="attendance-required">(required)</span></label>
                            <input id="addHolidayForm_holiday_date" type="date" class="form-control" name="holiday_date">
                        </div>
                        <div class="col-md-6">
                            <label for="addHolidayForm_name" class="form-label">Name <span class="attendance-required">(required)</span></label>
                            <input id="addHolidayForm_name" type="text" class="form-control" name="name">
                        </div>
                        <div class="col-md-4">
                            <label for="addHolidayForm_holiday_type" class="form-label">Type <span class="attendance-required">(required)</span></label>
                            <select id="addHolidayForm_holiday_type" class="form-select" name="holiday_type">
                                <option value="REGULAR">REGULAR</option>
                                <option value="SPECIAL">SPECIAL</option>
                                <option value="COMPANY">COMPANY</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="addHolidayForm_pay_multiplier" class="form-label">Pay Multiplier <span class="attendance-required">(required)</span></label>
                            <input id="addHolidayForm_pay_multiplier" aria-describedby="addHolidayForm_pay_multiplierHelp" type="number" step="0.01" min="0" class="form-control" name="pay_multiplier" value="1.00"><p class="form-text" id="addHolidayForm_pay_multiplierHelp">Factor applied by existing pay rules; for example, 2.00×. This is not a percentage.</p>
                        </div>
                        <div class="col-md-4">
                            <label for="addHolidayForm_allow_work" class="form-label">Allow Work <span class="attendance-required">(required)</span></label>
                            <select id="addHolidayForm_allow_work" aria-describedby="addHolidayForm_allow_workHelp" class="form-select" name="allow_work">
                                <option value="1">YES</option>
                                <option value="0">NO</option>
                            </select><p class="form-text" id="addHolidayForm_allow_workHelp">Whether work is allowed on this holiday under the existing configuration.</p>
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

<div class="modal fade hris-dialog attendance-dialog" id="editHolidayModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editHolidayModalTitle">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="editHolidayForm">
                <input type="hidden" name="id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <div><h5 class="modal-title" id="editHolidayModalTitle">Edit Holiday</h5><p class="small text-muted mb-0" data-attendance-record></p></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
                </div>

                <div class="modal-body">
                    <div class="attendance-feedback alert alert-warning" role="status" aria-live="polite" tabindex="-1" hidden></div>
                    <div class="attendance-discard alert alert-warning" hidden>
                        <p>Leave this dialog and discard your unsaved changes?</p>
                        <button type="button" class="btn btn-secondary attendance-stay">Stay and edit</button>
                        <button type="button" class="btn btn-outline-danger attendance-discard-confirm">Discard changes</button>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="editHolidayForm_holiday_date" class="form-label">Holiday Date <span class="attendance-required">(required)</span></label>
                            <input id="editHolidayForm_holiday_date" type="date" class="form-control" name="holiday_date">
                        </div>
                        <div class="col-md-6">
                            <label for="editHolidayForm_name" class="form-label">Name <span class="attendance-required">(required)</span></label>
                            <input id="editHolidayForm_name" type="text" class="form-control" name="name">
                        </div>
                        <div class="col-md-4">
                            <label for="editHolidayForm_holiday_type" class="form-label">Type <span class="attendance-required">(required)</span></label>
                            <select id="editHolidayForm_holiday_type" class="form-select" name="holiday_type">
                                <option value="REGULAR">REGULAR</option>
                                <option value="SPECIAL">SPECIAL</option>
                                <option value="COMPANY">COMPANY</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="editHolidayForm_pay_multiplier" class="form-label">Pay Multiplier <span class="attendance-required">(required)</span></label>
                            <input id="editHolidayForm_pay_multiplier" aria-describedby="editHolidayForm_pay_multiplierHelp" type="number" step="0.01" min="0" class="form-control" name="pay_multiplier"><p class="form-text" id="editHolidayForm_pay_multiplierHelp">Factor applied by existing pay rules; for example, 2.00×. This is not a percentage.</p>
                        </div>
                        <div class="col-md-4">
                            <label for="editHolidayForm_allow_work" class="form-label">Allow Work <span class="attendance-required">(required)</span></label>
                            <select id="editHolidayForm_allow_work" aria-describedby="editHolidayForm_allow_workHelp" class="form-select" name="allow_work">
                                <option value="1">YES</option>
                                <option value="0">NO</option>
                            </select><p class="form-text" id="editHolidayForm_allow_workHelp">Whether work is allowed on this holiday under the existing configuration.</p>
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

<div class="modal fade hris-dialog attendance-dialog" id="deleteHolidayModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteHolidayModalTitle">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteHolidayModalTitle">Delete Holiday</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
            </div>
            <div class="modal-body">
                    <div class="attendance-feedback alert alert-warning" role="status" aria-live="polite" tabindex="-1" hidden></div>
                    <div class="attendance-discard alert alert-warning" hidden>
                        <p>Leave this dialog and discard your unsaved changes?</p>
                        <button type="button" class="btn btn-secondary attendance-stay">Stay and edit</button>
                        <button type="button" class="btn btn-outline-danger attendance-discard-confirm">Discard changes</button>
                    </div>
                Delete this holiday: <strong class="attendance-delete-record" data-attendance-record></strong>?<p class="text-muted mt-2 mb-0">This action cannot be undone.</p>
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
// Page-local Attendance presentation and dialog states. Request options stay owned by each CRUD function.
const attendanceReads = new Map();
let attendanceDetailPending = false;
function attendanceListStart(table, columns, label) {
    const version = (attendanceReads.get(table) || 0) + 1;
    attendanceReads.set(table, version);
    $(table).attr('aria-busy', 'true').find('tbody').html(`<tr><td colspan="${columns}" class="attendance-empty" role="status">Loading ${esc(label)}…</td></tr>`);
    return version;
}
function attendanceListEnd(table, version, failed, retry) {
    if (attendanceReads.get(table) !== version) return false;
    $(table).attr('aria-busy', 'false');
    const $status = $(table).closest('.tab-pane, .management-card').find('.attendance-list-status').first();
    $status.empty();
    if (failed) {
        $(table).find('tbody').empty();
        $status.append(document.createTextNode('Records could not be loaded. '), $('<button>', {
            type: 'button', class: 'btn btn-outline-primary', text: 'Retry loading'
        }).on('click', retry));
    }
    return true;
}
function attendanceDate(value) {
    // Date-only strings are formatted without timezone conversion. Unexpected values remain visible.
    const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(String(value ?? ''));
    if (!match || +match[2] < 1 || +match[2] > 12 || +match[3] < 1 || +match[3] > 31) return esc(value) || '—';
    return `${match[3]} ${['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][+match[2] - 1]} ${match[1]}`;
}
function attendanceLabel(value, labels) {
    return esc(Object.prototype.hasOwnProperty.call(labels, value) ? labels[value] : value) || '—';
}
function attendanceTime(value) {
    // Retain seconds and overnight ordering exactly as supplied; no duration calculation.
    return esc(value) || '—';
}
function attendanceSortControls(id, keys, change) {
    const $select = $('#' + id);
    $select.on('change', function () { if (Object.hasOwn(keys, this.value)) change(this.value); });
    $('#' + id + 'Direction').on('click', function () { change($select.val()); });
    return function (key, direction) {
        $select.val(key);
        $('#' + id + 'Direction').text(direction === 'ASC' ? 'Ascending ↑' : 'Descending ↓')
            .attr('aria-label', `Sorted ${direction === 'ASC' ? 'ascending' : 'descending'}. Reverse sort direction`);
    };
}
function attendanceSnapshot($modal) {
    const values = new URLSearchParams($modal.find('form').serialize());
    values.sort();
    return values.toString();
}
function attendanceUnlock(state) {
    if (!state.disabledFields) return;
    state.disabledFields.forEach(([field, disabled]) => { field.disabled = disabled; });
    state.disabledFields = null;
}
function attendanceFeedback($modal, message) {
    $modal.find('.attendance-feedback').text(message).prop('hidden', !message);
}
function attendanceValidation($modal, res) {
    attendanceFeedback($modal, res.message || 'Changes were not saved. Review the values and try again.');
    if (!res.errors || typeof res.errors !== 'object' || Array.isArray(res.errors)) return;
    $modal.find('input:not([type="hidden"]), select, textarea').each(function () {
        const message = res.errors[this.name];
        if (typeof message !== 'string') return;
        const id = this.id + 'Error';
        $(this).addClass('is-invalid').attr('aria-invalid', 'true').attr('data-attendance-error', id);
        const describedBy = this.getAttribute('aria-describedby') || '';
        this.setAttribute('aria-describedby', (describedBy + ' ' + id).trim());
        $(this).after($('<div>', {id, class: 'invalid-feedback attendance-field-error', text: message}));
    });
}
function attendanceClearValidation($modal) {
    $modal.find('[data-attendance-error]').each(function () {
        const errorId = this.dataset.attendanceError;
        const ids = (this.getAttribute('aria-describedby') || '').split(/\s+/).filter(id => id !== errorId);
        if (ids.length) this.setAttribute('aria-describedby', ids.join(' ')); else this.removeAttribute('aria-describedby');
        $(this).removeClass('is-invalid').removeAttr('aria-invalid data-attendance-error');
    });
    $modal.find('.attendance-field-error').remove();
}
function attendanceRead(selector, options) {
    // Keep row actions disabled while the selected record is being loaded.
    if (attendanceDetailPending) return;
    attendanceDetailPending = true;
    const $modal = $(selector), trigger = $modal.data('attendance-trigger');
    const $actions = $('.attendance-workspace table button');
    $actions.prop('disabled', true);
    const beforeSend = options.beforeSend, success = options.success, error = options.error;
    $.ajax({...options,
        beforeSend: function () { if (beforeSend) beforeSend(); },
        success: function (res) { success(res); },
        error: function (xhr) { if (error) error(xhr); if (trigger && document.contains(trigger)) trigger.focus(); },
        complete: function () { attendanceDetailPending = false; $actions.prop('disabled', false); }
    });
}
function attendanceMutation(selector, options) {
    const $modal = $(selector), state = $modal.data('attendance-state');
    if (!state || state.pending || state.uncertain || $modal.find('[data-attendance-lookup]').toArray().some(select => select.dataset.attendanceLookup !== 'ready')) return;
    state.pending = true;
    state.allowHide = false;
    attendanceClearValidation($modal);
    attendanceFeedback($modal, 'Saving changes. Please wait…');
    // Lock dialog controls after the caller has serialized the original fields.
    $modal.find('button').prop('disabled', true);
    state.disabledFields = $modal.find('input, select, textarea').toArray().map(field => [field, field.disabled]);
    state.disabledFields.forEach(([field]) => { field.disabled = true; });
    $modal.attr('aria-busy', 'true');
    const success = options.success, beforeSend = options.beforeSend;
    $.ajax({...options,
        beforeSend: function () { if (beforeSend) beforeSend(); },
        success: function (res) {
            state.pending = false;
            if (res && res.success === true) {
                state.allowHide = true;
                attendanceUnlock(state);
                attendanceFeedback($modal, 'Changes saved.');
                success(res);
                bootstrap.Modal.getInstance($modal[0])?.hide();
            } else if (res && res.success === false) {
                attendanceValidation($modal, res);
                showToast(res.message || 'Changes were not saved.', 'error');
            } else {
                attendanceUncertain($modal, state);
            }
        },
        error: function (xhr) {
            state.pending = false;
            // Only an explicit, structured rejection establishes that retry is safe.
            if (xhr.status >= 400 && xhr.status < 500 && xhr.responseJSON?.success === false) {
                attendanceValidation($modal, xhr.responseJSON);
                showToast(xhr.responseJSON.message || 'Changes were not saved.', 'error');
            } else attendanceUncertain($modal, state);
        },
        complete: function () {
            hideLoader();
            state.pending = false;
            attendanceUnlock(state);
            $modal.attr('aria-busy', 'false').find('button').prop('disabled', false);
            if (state.uncertain) $modal.find('[type="submit"], .btn-danger').prop('disabled', true);
            if (!state.allowHide) $modal.find('.attendance-feedback').trigger('focus');
        }
    });
}
function attendanceUncertain($modal, state) {
    state.uncertain = true;
    const message = 'The save outcome could not be confirmed. Close this dialog and reload the records to verify the result before trying again. Your entered values are still here.';
    attendanceFeedback($modal, message);
    showToast('Save outcome unknown. Reload records to verify before trying again.', 'error', 8000);
}
$(function () {
    const $dialogs = $('.attendance-dialog');
    $dialogs.each(function () {
        $(this).data('attendance-state', {pending: false, uncertain: false, allowHide: false});
    });
    $dialogs.on('keydown', function (event) {
        if (event.key !== 'Tab') return;
        const $controls = $(this).find('a[href], button, input:not([type="hidden"]), select, textarea, [tabindex]:not([tabindex="-1"])')
            .filter(':visible:not(:disabled)').filter(function () { return this.tabIndex >= 0; });
        const first = $controls[0], last = $controls[$controls.length - 1];
        if (first && ((event.shiftKey && document.activeElement === first) || (!event.shiftKey && document.activeElement === last))) {
            event.preventDefault();
            (event.shiftKey ? last : first).focus();
        }
    });
    $dialogs.on('show.bs.modal', function (event) {
        const $modal = $(this), state = $modal.data('attendance-state');
        state.trigger = event.relatedTarget || $modal.data('attendance-trigger') || document.activeElement;
        state.allowHide = false;
        $modal.find('.attendance-discard').prop('hidden', true);
        if (!state.uncertain) { attendanceFeedback($modal, ''); attendanceClearValidation($modal); }
    }).on('shown.bs.modal', function () {
        const $modal = $(this), state = $modal.data('attendance-state');
        state.baseline = attendanceSnapshot($modal);
        const $focus = $modal.find('form').length ? $modal.find('input:not([type="hidden"]), select, textarea').filter(':visible:not(:disabled)').first() : $modal.find('[data-bs-dismiss="modal"]').last();
        $focus.trigger('focus');
    }).on('hide.bs.modal', function (event) {
        const $modal = $(this), state = $modal.data('attendance-state');
        if (state.pending) { event.preventDefault(); return; }
        if (!state.allowHide && $modal.find('form').length && state.baseline !== attendanceSnapshot($modal)) {
            event.preventDefault();
            state.lastFocus = document.activeElement;
            $modal.find('.attendance-discard').prop('hidden', false).find('.attendance-stay').trigger('focus');
        }
    }).on('hidden.bs.modal', function () {
        const state = $(this).data('attendance-state'), trigger = state.trigger;
        const replacement = trigger?.dataset.id && Array.from(document.querySelectorAll('.attendance-workspace table button')).find(button => button.dataset.id === trigger.dataset.id && button.className === trigger.className);
        const target = trigger && document.contains(trigger) ? trigger : replacement || document.querySelector('.attendance-workspace h1');
        target?.focus();
    });
    $dialogs.find('.attendance-stay').on('click', function () {
        const $modal = $(this).closest('.modal'), state = $modal.data('attendance-state');
        $modal.find('.attendance-discard').prop('hidden', true);
        state.lastFocus?.focus();
    });
    $dialogs.find('.attendance-discard-confirm').on('click', function () {
        const $modal = $(this).closest('.modal');
        // Restore the dialog snapshot on explicit discard; values are never submitted here.
        $modal.data('attendance-state').allowHide = true;
        const form = $modal.find('form')[0];
        if (form) {
            const values = new URLSearchParams($modal.data('attendance-state').baseline);
            for (const field of form.elements) if (field.name && values.has(field.name)) field.value = values.get(field.name);
        }
        bootstrap.Modal.getInstance($modal[0]).hide();
    });
});


function attendanceFactor(value) {
    const raw = String(value ?? '');
    return /^\d+(\.\d+)?$/.test(raw) ? (raw.includes('.') ? raw.padEnd(raw.indexOf('.') + 3, '0') : raw + '.00') + '×' : raw || '—';
}
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

let syncAttendanceSort;
$(document).ready(function () {
    syncAttendanceSort = attendanceSortControls('holidaySortBy', {'holiday_date': 'Holiday date', 'name': 'Holiday name', 'holiday_type': 'Holiday type', 'pay_multiplier': 'Pay multiplier', 'allow_work': 'Work allowed', 'created_at': 'Date added'}, key => {
        currentSortDir = currentSortBy === key && currentSortDir === 'ASC' ? 'DESC' : 'ASC';
        currentSortBy = key; getAllHolidays(1);
    });
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
        $('#editHolidayModal').data('attendance-trigger', this).find('[data-attendance-record]').text($(this).closest('tr').find('.attendance-identity, .attendance-period').find('strong, .attendance-secondary').map(function () { return $(this).text().trim(); }).get().join(' · '));
        getHoliday($(this).data('id'));
    });

    $(document).on('click', '.deleteHolidayBtn', function () {
        $('#deleteHolidayModal').data('attendance-trigger', this).find('[data-attendance-record]').text($(this).closest('tr').find('.attendance-identity, .attendance-period').find('strong, .attendance-secondary').map(function () { return $(this).text().trim(); }).get().join(' · '));
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
    const version = attendanceListStart('#holidaysTable', 6, 'holidays');

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
            if (!attendanceListEnd('#holidaysTable', version, !res || res.success !== true || !Array.isArray(res.data), () => getAllHolidays(page))) return;
            if (!res || res.success !== true || !Array.isArray(res.data)) return;
            currentPage = res.currentPage;
            renderHolidayRows(res.data || []);
            renderPagination(res.currentPage, res.totalPages);
            applySortIndicators();
            syncAttendanceSort(currentSortBy, currentSortDir);
        },
        error: function (xhr) {
            attendanceListEnd('#holidaysTable', version, true, () => getAllHolidays(page));
            showToast('Unable to load holidays.', 'error');
        }
    });
}

function renderHolidayRows(holidays) {
    let rows = '';

    if (holidays.length === 0) {
        rows = '<tr><td colspan="6" class="attendance-empty" role="status">No holidays found</td></tr>';
    } else {
        holidays.forEach(h => {
            rows += `
                <tr>
                    <td class="ps-4 attendance-identity"><strong>${esc(h.name)}</strong><span class="attendance-secondary">${attendanceDate(h.holiday_date)}</span></td>
                    <td>${attendanceLabel(h.holiday_type, {REGULAR: 'Regular', SPECIAL: 'Special', COMPANY: 'Company'})}</td>
                    <td class="attendance-number">${esc(attendanceFactor(h.pay_multiplier))}</td>
                    <td>${h.allow_work == 1 ? 'Yes' : 'No'}</td>
                    <td class="hris-created-at attendance-created">${esc(h.created_at) || '-'}</td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm editHolidayBtn hris-action" data-id="${h.id}" aria-label="Edit holiday: ${esc(h.name)}" title="Edit holiday">
                            <i class="bi bi-pencil-square" aria-hidden="true"></i>
                        </button>
                        <button class="btn btn-sm deleteHolidayBtn hris-action hris-action--danger" data-id="${h.id}" aria-label="Delete holiday: ${esc(h.name)}" title="Delete holiday">
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
    attendanceMutation('#addHolidayModal', {
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
        }
    });
}

function getHoliday(id) {
    showLoader("Loading holiday...");

    attendanceRead('#editHolidayModal', {
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
            showToast('Unable to load holiday.', 'error');
        }
    });
}

function updateHoliday(form) {
    let id = $(form).find('[name="id"]').val();

    attendanceMutation('#editHolidayModal', {
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
        }
    });
}

function deleteHoliday(id) {
    attendanceMutation('#deleteHolidayModal', {
        url: '/hris/holidays/delete/' + id,
        type: 'POST',
        dataType: 'json',
        data: {
            csrf_token: $('#addHolidayForm [name="csrf_token"]').val()
        },
        beforeSend: function () {
            showLoader("Deleting holiday...");
        },
        success: function (res) {
            hideLoader();
            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }
            showToast(res.message, 'success');
            getAllHolidays(currentPage);
        }
    });
}
</script>
</body>
</html>
