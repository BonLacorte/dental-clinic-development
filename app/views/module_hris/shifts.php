<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_hris/partials/hris_nav.php'; ?>

<div class="container-fluid py-4 management-page hris-page attendance-workspace">
    <div class="card shadow-sm management-card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div class="attendance-heading"><p class="attendance-eyebrow">HRIS · Attendance</p><h1 class="h4 mb-1" tabindex="-1" id="shiftsHeading">Shifts</h1><p class="text-muted mb-0">Review schedules, required hours, breaks and overtime settings.</p></div>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addShiftModal">
                <i class="bi bi-plus-lg" aria-hidden="true"></i> Add Shift
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
                        <i class="bi bi-search" aria-hidden="true"></i> Search
                    </button>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-secondary" type="button" id="clearShiftSearchBtn">
                        Clear
                    </button>
                </div>
                <div class="col-auto attendance-sort-control"><label class="form-label" for="shiftSortBy">Sort by</label><div class="d-flex gap-2"><select class="form-select" id="shiftSortBy"><option value="name">Shift name</option><option value="shift_type">Shift type</option><option value="start_time">Start time</option><option value="end_time">End time</option><option value="flex_start_time">Flex start</option><option value="flex_end_time">Flex end</option><option value="required_hours">Required hours</option><option value="break_minutes">Break minutes</option><option value="allow_overtime">Overtime</option><option value="created_at" selected>Date added</option></select><button type="button" id="shiftSortByDirection" class="btn btn-outline-secondary">Descending ↓</button></div></div>
            </form>
        </div>

        <div class="card-body p-0">
            <p class="attendance-list-status px-4 pt-3 mb-0" role="status" aria-live="polite"></p>
                    <p class="hris-scroll-hint" id="shiftsTableScrollHelp">Use horizontal scrolling when columns extend beyond this view.</p>
            <div class="table-responsive hris-table-region" tabindex="0" role="region" aria-labelledby="shiftsHeading" aria-describedby="shiftsTableScrollHelp">
                <table class="table table-hover align-middle mb-0 hris-data-table" id="shiftsTable">
                    <caption class="visually-hidden">Shift times, required hours, breaks and overtime settings.</caption>
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4"><button class="btn btn-link p-0 sortable" data-sort="name">Shift Name &amp; Type</button></th>

                            <th scope="col">Schedule</th>
                            <th scope="col"><button class="btn btn-link p-0 sortable" data-sort="required_hours">Required Hours</button></th>
                            <th scope="col"><button class="btn btn-link p-0 sortable" data-sort="break_minutes">Break Minutes</button></th>
                            <th scope="col"><button class="btn btn-link p-0 sortable" data-sort="allow_overtime">Overtime</button></th>
                            <th scope="col" class="hris-created-at attendance-created"><button class="btn btn-link p-0 sortable" data-sort="created_at">Created At</button></th>
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

<div class="modal fade hris-dialog attendance-dialog" id="addShiftModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addShiftModalTitle">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="addShiftForm">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="addShiftModalTitle">Add Shift</h5>
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
                        <div class="col-12 attendance-form-section"><h6>Shift identity</h6></div>
                        <div class="col-md-6">
                            <label for="addShiftForm_name" class="form-label">Name <span class="attendance-required">(required)</span></label>
                            <input id="addShiftForm_name" type="text" class="form-control" name="name">
                        </div>
                        <div class="col-md-6">
                            <label for="addShiftForm_shift_type" class="form-label">Shift Type <span class="attendance-required">(required)</span></label>
                            <select id="addShiftForm_shift_type" class="form-select shiftTypeSelect" name="shift_type">
                                <option value="FIXED">Fixed</option>
                                <option value="FLEX_WINDOW">Flexible Window</option>
                                <option value="FLEX_HOURS">Flexible Hours</option>
                            </select>
                        </div>
                        <div class="col-12 attendance-form-section"><h6>Schedule</h6><p class="form-text attendance-flex-hours-help" hidden>Flexible Hours has no fixed schedule. Review the required hours below.</p></div>
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
                        <div class="col-12 attendance-form-section"><h6>Hours & break configuration</h6></div>
                        <div class="col-md-4">
                            <label for="addShiftForm_required_hours" class="form-label">Required Hours</label>
                            <input id="addShiftForm_required_hours" type="number" step="0.01" min="0" class="form-control" name="required_hours" value="8.00">
                        </div>
                        <div class="col-md-4">
                            <label for="addShiftForm_break_minutes" class="form-label">Break Minutes <span class="attendance-required">(required)</span></label>
                            <input id="addShiftForm_break_minutes" type="number" min="0" class="form-control" name="break_minutes" value="60">
                        </div>
                        <div class="col-12 attendance-form-section"><h6>Overtime</h6></div>
                        <div class="col-md-4">
                            <label for="addShiftForm_allow_overtime" class="form-label">Allow Overtime <span class="attendance-required">(required)</span></label>
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

<div class="modal fade hris-dialog attendance-dialog" id="editShiftModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editShiftModalTitle">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="editShiftForm">
                <input type="hidden" name="id">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="modal-header">
                    <div><h5 class="modal-title" id="editShiftModalTitle">Edit Shift</h5><p class="small text-muted mb-0" data-attendance-record></p></div>
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
                        <div class="col-12 attendance-form-section"><h6>Shift identity</h6></div>
                        <div class="col-md-6">
                            <label for="editShiftForm_name" class="form-label">Name <span class="attendance-required">(required)</span></label>
                            <input id="editShiftForm_name" type="text" class="form-control" name="name">
                        </div>
                        <div class="col-md-6">
                            <label for="editShiftForm_shift_type" class="form-label">Shift Type <span class="attendance-required">(required)</span></label>
                            <select id="editShiftForm_shift_type" class="form-select shiftTypeSelect" name="shift_type">
                                <option value="FIXED">Fixed</option>
                                <option value="FLEX_WINDOW">Flexible Window</option>
                                <option value="FLEX_HOURS">Flexible Hours</option>
                            </select>
                        </div>
                        <div class="col-12 attendance-form-section"><h6>Schedule</h6><p class="form-text attendance-flex-hours-help" hidden>Flexible Hours has no fixed schedule. Review the required hours below.</p></div>
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
                        <div class="col-12 attendance-form-section"><h6>Hours & break configuration</h6></div>
                        <div class="col-md-4">
                            <label for="editShiftForm_required_hours" class="form-label">Required Hours</label>
                            <input id="editShiftForm_required_hours" type="number" step="0.01" min="0" class="form-control" name="required_hours">
                        </div>
                        <div class="col-md-4">
                            <label for="editShiftForm_break_minutes" class="form-label">Break Minutes <span class="attendance-required">(required)</span></label>
                            <input id="editShiftForm_break_minutes" type="number" min="0" class="form-control" name="break_minutes">
                        </div>
                        <div class="col-12 attendance-form-section"><h6>Overtime</h6></div>
                        <div class="col-md-4">
                            <label for="editShiftForm_allow_overtime" class="form-label">Allow Overtime <span class="attendance-required">(required)</span></label>
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

<div class="modal fade hris-dialog attendance-dialog" id="deleteShiftModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteShiftModalTitle">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteShiftModalTitle">Delete Shift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close dialog"></button>
            </div>
            <div class="modal-body">
                    <div class="attendance-feedback alert alert-warning" role="status" aria-live="polite" tabindex="-1" hidden></div>
                    <div class="attendance-discard alert alert-warning" hidden>
                        <p>Leave this dialog and discard your unsaved changes?</p>
                        <button type="button" class="btn btn-secondary attendance-stay">Stay and edit</button>
                        <button type="button" class="btn btn-outline-danger attendance-discard-confirm">Discard changes</button>
                    </div>
                Delete this shift: <strong class="attendance-delete-record" data-attendance-record></strong>?<p class="text-muted mt-2 mb-0">This action cannot be undone.</p>
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

let syncAttendanceSort;
$(document).ready(function () {
    syncAttendanceSort = attendanceSortControls('shiftSortBy', {'name': 'Shift name', 'shift_type': 'Shift type', 'start_time': 'Start time', 'end_time': 'End time', 'flex_start_time': 'Flex start', 'flex_end_time': 'Flex end', 'required_hours': 'Required hours', 'break_minutes': 'Break minutes', 'allow_overtime': 'Overtime', 'created_at': 'Date added'}, key => {
        currentSortDir = currentSortBy === key && currentSortDir === 'ASC' ? 'DESC' : 'ASC';
        currentSortBy = key; getAllShifts(1);
    });
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
        $('#editShiftModal').data('attendance-trigger', this).find('[data-attendance-record]').text($(this).closest('tr').find('.attendance-identity, .attendance-period').find('strong, .attendance-secondary').map(function () { return $(this).text().trim(); }).get().join(' · '));
        getShift($(this).data('id'));
    });

    $(document).on('click', '.deleteShiftBtn', function () {
        $('#deleteShiftModal').data('attendance-trigger', this).find('[data-attendance-record]').text($(this).closest('tr').find('.attendance-identity, .attendance-period').find('strong, .attendance-secondary').map(function () { return $(this).text().trim(); }).get().join(' · '));
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
    const version = attendanceListStart('#shiftsTable', 7, 'shifts');

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
            if (!attendanceListEnd('#shiftsTable', version, !res || res.success !== true || !Array.isArray(res.data), () => getAllShifts(page))) return;
            if (!res || res.success !== true || !Array.isArray(res.data)) return;
            currentPage = res.currentPage;
            renderShiftRows(res.data || []);
            renderPagination(res.currentPage, res.totalPages);
            applySortIndicators();
            syncAttendanceSort(currentSortBy, currentSortDir);
        },
        error: function (xhr) {
            attendanceListEnd('#shiftsTable', version, true, () => getAllShifts(page));
            showToast('Unable to load shifts.', 'error');
        }
    });
}

function renderShiftRows(shifts) {
    let rows = '';

    if (shifts.length === 0) {
        rows = '<tr><td colspan="7" class="attendance-empty" role="status">No shifts found</td></tr>';
    } else {
        shifts.forEach(s => {
            rows += `
                <tr>
                    <td class="ps-4 attendance-identity"><strong>${esc(s.name)}</strong><span class="attendance-secondary">${attendanceLabel(s.shift_type, {FIXED: 'Fixed', FLEX_WINDOW: 'Flexible Window', FLEX_HOURS: 'Flexible Hours'})}</span></td>
                    <td class="attendance-schedule"><span class="attendance-secondary">Start: ${attendanceTime(s.start_time)} · End: ${attendanceTime(s.end_time)}</span><span class="attendance-secondary">Flex start: ${attendanceTime(s.flex_start_time)} · Flex end: ${attendanceTime(s.flex_end_time)}</span></td>
                    <td class="attendance-number">${esc(s.required_hours)}</td>
                    <td class="attendance-number">${esc(s.break_minutes)}</td>
                    <td>${s.allow_overtime == 1 ? 'Allowed' : 'Not allowed'}</td>
                    <td class="hris-created-at attendance-created">${esc(s.created_at) || '-'}</td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm editShiftBtn hris-action" data-id="${s.id}" aria-label="Edit shift: ${esc(s.name)}" title="Edit shift">
                            <i class="bi bi-pencil-square" aria-hidden="true"></i>
                        </button>
                        <button class="btn btn-sm deleteShiftBtn hris-action hris-action--danger" data-id="${s.id}" aria-label="Delete shift: ${esc(s.name)}" title="Delete shift">
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

    [$start, $end, $flexStart, $flexEnd].forEach($field => $field.closest('[class*="col-md-"]').prop('hidden', $field.prop('disabled')));
    $form.find('.attendance-flex-hours-help').prop('hidden', shiftType !== 'FLEX_HOURS');
    $form.find('label .attendance-conditional-required').remove();
    $form.find(':required').each(function () { $form.find('label[for="' + this.id + '"]').append('<span class="attendance-required attendance-conditional-required"> (required)</span>'); });
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
    attendanceMutation('#addShiftModal', {
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
        }
    });
}

function getShift(id) {
    showLoader("Loading shift...");

    attendanceRead('#editShiftModal', {
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
            showToast('Unable to load shift.', 'error');
        }
    });
}

function updateShift(form) {
    let id = $(form).find('[name="id"]').val();

    attendanceMutation('#editShiftModal', {
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
        }
    });
}

function deleteShift(id) {
    attendanceMutation('#deleteShiftModal', {
        url: '/hris/shifts/delete/' + id,
        type: 'POST',
        dataType: 'json',
        data: {
            csrf_token: $('#addShiftForm [name="csrf_token"]').val()
        },
        beforeSend: function () {
            showLoader("Deleting shift...");
        },
        success: function (res) {
            hideLoader();
            if (!res.success) {
                showToast(res.message || 'Something went wrong.', 'error');
                return;
            }
            showToast(res.message, 'success');
            getAllShifts(currentPage);
        }
    });
}
</script>
</body>
</html>
