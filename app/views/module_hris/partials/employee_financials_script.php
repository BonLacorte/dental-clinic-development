<script>
$(function () {
    const definitions = <?= json_encode($financialDefinitions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const canManage = <?= $canManageFinancials ? 'true' : 'false' ?>;
    const baseUrl = '/hris/employees/<?= (int) $employee['id'] ?>/financials/';
    const csrfToken = <?= json_encode($csrf_token, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const employeeNumber = <?= json_encode($employee['employee_no'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const $category = $('#financialCategory'), $table = $('#financialRecords');
    let category = $category.val(), page = 1, totalPages = 1, version = 0, rows = new Map(), types = [];
    let listReady = false, editVersion = 0, pending = false, uncertain = false, initial = '', dialog = null;
    const form = document.getElementById('financialRecordForm');
    const $editor = $('#financialRecordModal'), $delete = $('#financialDeleteModal');
    const esc = value => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;'}[char]));
    const labels = {MONTHLY:'Monthly', DAILY:'Daily', HOURLY:'Hourly', WEEKLY:'Weekly', SEMI_MONTHLY:'Semi-monthly', SINGLE:'Single', INSTALLMENT:'Installment', ACTIVE:'Active', PAID:'Paid', CANCELLED:'Cancelled'};
    function money(value) {
        const parts = String(value ?? '').match(/^([+-]?)(\d+)(?:\.(\d+))?$/);
        return parts ? `${parts[1]}₱${parts[2].replace(/\B(?=(\d{3})+(?!\d))/g, ',')}.${(parts[3] || '').padEnd(2, '0')}` : String(value ?? 'Not recorded');
    }
    function valueFor(row, field) {
        const value = row[field.name];
        if (field.input === 'type') return row.type_name ? `${row.type_name}${row.type_code ? ' (' + row.type_code + ')' : ''}` : (value == null ? 'Not recorded' : 'Type #' + value);
        if (value === null || value === '') return 'Not recorded';
        if (field.input === 'number') return money(value);
        if (field.input === 'bool') return String(value) === '1' ? 'Recurring' : 'One-time';
        return labels[value] || String(value);
    }
    function message(selector, text, error = false) {
        $(selector).text(text).toggleClass('text-danger', error).attr('role', error ? 'alert' : 'status');
    }
    function listControls() {
        $('#financialAdd').prop('disabled', !listReady || pending);
        $('#financialPrevious').prop('disabled', !listReady || pending || page <= 1);
        $('#financialNext').prop('disabled', !listReady || pending || page >= totalPages);
        $category.prop('disabled', pending);
    }
    function render(data) {
        const definition = definitions[category];
        rows = new Map(data.map(row => [String(row.id), row]));
        const fields = [...definition.fields, {name:'created_at', label:'Created At', input:'text'}];
        $table.find('thead').html('<tr>' + fields.map(field => `<th scope="col"${field.input === 'number' ? ' class="text-end"' : ''}>${esc(field.label)}</th>`).join('') + (canManage ? '<th scope="col">Actions</th>' : '') + '</tr>');
        $('#financialCaption').text(definition.title + ' for ' + employeeNumber);
        $table.find('tbody').html(data.length ? data.map(row => '<tr>' + fields.map((field, index) => {
            const tag = index === 0 ? 'th' : 'td';
            return `<${tag}${index === 0 ? ' scope="row"' : ''}${field.input === 'number' ? ' class="text-end text-nowrap"' : ''}>${esc(valueFor(row, field))}</${tag}>`;
        }).join('') + (canManage ? `<td><div class="d-flex gap-2"><button type="button" class="btn btn-outline-primary financial-edit" data-id="${esc(row.id)}" aria-label="Edit ${esc(definition.singular)} record ${esc(row.id)}">Edit</button><button type="button" class="btn btn-outline-danger financial-delete" data-id="${esc(row.id)}" aria-label="Delete ${esc(definition.singular)} record ${esc(row.id)}">Delete</button></div></td>` : '') + '</tr>').join('') : `<tr><td colspan="${fields.length + (canManage ? 1 : 0)}">No ${esc(definition.title.toLowerCase())} recorded for this employee.</td></tr>`);
    }
    function load(targetPage = 1) {
        const currentVersion = ++version;
        listReady = false; rows.clear(); types = [];
        listControls();
        $('#financialRetry').prop('hidden', true);
        $('#financialPageInfo').empty();
        $('#financialAdd').text('Add ' + definitions[category].singular);
        $table.attr('aria-busy', 'true').find('tbody').empty();
        message('#financialListFeedback', 'Loading financial records…');
        $.ajax({
            url: baseUrl + category + '/records', method:'GET', dataType:'json', data:{page:targetPage},
            success(res) {
                if (currentVersion !== version) return;
                if (res?.success !== true || !Array.isArray(res.data) || !Array.isArray(res.types)) { failed(res?.message); return; }
                page = res.currentPage; totalPages = res.totalPages; types = res.types;
                render(res.data); listReady = true;
                message('#financialListFeedback', `${res.totalRecords} ${Number(res.totalRecords) === 1 ? 'record' : 'records'}.`);
                $('#financialPageInfo').text(`Page ${page} of ${totalPages}`);
            },
            error(xhr) { if (currentVersion === version) failed(xhr.responseJSON?.message || xhr.responseJSON?.error); },
            complete() { if (currentVersion === version) { $table.attr('aria-busy', 'false'); listControls(); } }
        });
    }
    function failed(text) {
        message('#financialListFeedback', text || 'Financial records could not be loaded. Please try again.', true);
        $('#financialRetry').prop('hidden', false);
    }
    $category.on('change', function () { category = this.value; load(1); });
    $('#financialRetry').on('click', () => load(page));
    $('#financialPrevious').on('click', () => load(page - 1));
    $('#financialNext').on('click', () => load(page + 1));

    function buildFields(data = {}) {
        const fieldset = document.getElementById('financialFields');
        fieldset.replaceChildren();
        const legend = document.createElement('legend'); legend.className = 'visually-hidden'; legend.textContent = 'Payroll item details'; fieldset.append(legend);
        definitions[category].fields.forEach(field => {
            const cell = document.createElement('div'); cell.className = 'col-12 col-md-6';
            const label = document.createElement('label'); label.className = 'form-label'; label.htmlFor = 'financial_' + field.name; label.textContent = field.label + (field.required ? ' *' : '');
            const select = ['select','type','bool'].includes(field.input);
            const input = document.createElement(select ? 'select' : 'input');
            input.name = field.name; input.id = label.htmlFor; input.required = field.required; input.className = select ? 'form-select' : 'form-control';
            if (select) {
                const choices = field.input === 'type' ? [['','Select Type'], ...types.map(type => [String(type.id), `${type.code ? type.code + ' - ' : ''}${type.name}`])]
                    : field.input === 'bool' ? [['1','Recurring'], ['0','One-time']] : field.options.map(value => [value, labels[value] || value]);
                choices.forEach(([value,text]) => { const option = document.createElement('option'); option.value = value; option.textContent = text; input.append(option); });
            } else {
                input.type = field.input;
                if (field.min !== undefined) input.min = field.min;
                if (field.step !== undefined) input.step = field.step;
            }
            input.value = Object.hasOwn(data,field.name) ? (data[field.name] ?? '') : (field.default ?? '');
            cell.append(label,input);
            if (field.name === 'effective_to' || field.name === 'remaining_balance') {
                const help = document.createElement('p'); help.className = 'form-text mb-0'; help.id = input.id + '_help';
                help.textContent = field.name === 'effective_to' ? 'Optional. Leave blank if there is no end date.' : 'Optional. Leave blank to use the amount. Review the existing balance when editing.';
                input.setAttribute('aria-describedby',help.id); cell.append(help);
            }
            fieldset.append(cell);
        });
    }
    function editorReady(data) {
        buildFields(data);
        $('#financialFields').prop('disabled', false);
        const unavailable = definitions[category].fields.some(field => field.input === 'type') && !types.length;
        $('#financialSave').prop('disabled', unavailable);
        message('#financialRecordFeedback', unavailable ? 'No payroll types are available. Add a type on the Payroll Items page first.' : '', unavailable);
        initial = $(form).serialize();
        form.querySelector('fieldset input, fieldset select')?.focus();
    }
    function openEditor(id, trigger) {
        if (!canManage || !listReady) return;
        const currentVersion = ++editVersion;
        dialog = {category,id,trigger}; pending = false; uncertain = false; initial = '';
        $('#financialRecordTitle').text((id === null ? 'Add ' : 'Edit ') + definitions[category].singular);
        $('#financialFields').empty().prop('disabled', true);
        $('#financialSave').text(id === null ? 'Save' : 'Update').prop('disabled', true);
        message('#financialRecordFeedback', id === null ? '' : 'Loading selected record…');
        bootstrap.Modal.getOrCreateInstance($editor[0]).show(trigger);
        if (id === null) { editorReady({}); return; }
        $.ajax({url:baseUrl + category + '/records/' + encodeURIComponent(id), method:'GET', dataType:'json',
            success(res) {
                if (currentVersion !== editVersion) return;
                if (res?.success === true && res.data?.id) editorReady(res.data);
                else message('#financialRecordFeedback', res?.message || 'Record unavailable. Close and refresh the list.', true);
            },
            error(xhr) { if (currentVersion === editVersion) message('#financialRecordFeedback', xhr.responseJSON?.message || 'Could not load the record. Close and try again.', true); }
        });
    }
    $('#financialAdd').on('click', function () { openEditor(null,this); });
    $table.on('click', '.financial-edit', function () { openEditor($(this).attr('data-id'),this); });
    $table.on('click', '.financial-delete', function () {
        const row = rows.get($(this).attr('data-id'));
        if (!canManage || !row) return;
        dialog = {category,id:row.id,trigger:this}; pending = false; uncertain = false;
        $('#financialDeleteTitle').text('Delete ' + definitions[category].singular);
        $('#financialDeleteContext').text(`Delete this ${definitions[category].singular.toLowerCase()}${row.type_name ? ' (' + row.type_name + ')' : ''} of ${money(row.amount ?? row.salary_rate)}${row.effective_from ? ', effective from ' + row.effective_from : ''}?`);
        message('#financialDeleteFeedback','');
        $('#financialDeleteConfirm').text('Delete').prop('disabled',false);
        bootstrap.Modal.getOrCreateInstance($delete[0]).show(this);
    });
    $editor.on('hide.bs.modal', function(event) {
        if (pending || (initial && !uncertain && $(form).serialize() !== initial && !window.confirm('Discard your unsaved changes?'))) event.preventDefault();
    });
    $delete.on('hide.bs.modal', function(event) { if (pending) event.preventDefault(); });
    $editor.on('shown.bs.modal', function () { form.querySelector('fieldset:not(:disabled) input, fieldset:not(:disabled) select')?.focus(); });
    $delete.on('shown.bs.modal', () => $('#financialDeleteCancel').trigger('focus'));
    $editor.add($delete).on('hidden.bs.modal', function() {
        editVersion++; const trigger = dialog?.trigger; dialog = null; initial = ''; pending = false;
        if (uncertain) load(page);
        uncertain = false;
        if (trigger?.isConnected) trigger.focus(); else $category.trigger('focus');
    });
    function mutate(action, data, $modal, feedback) {
        if (pending || uncertain || !dialog) return;
        pending = true; listControls();
        const context = {...dialog};
        $modal.find('[type="submit"], #financialDeleteConfirm').prop('disabled',true).text(action === 'delete' ? 'Deleting…' : 'Saving…');
        $('#financialFields').prop('disabled',true);
        message(feedback,action === 'delete' ? 'Deleting…' : 'Saving changes…');
        $.ajax({url:baseUrl + context.category + '/' + action + (context.id === null ? '' : '/' + encodeURIComponent(context.id)), method:'POST', dataType:'json', data,
            success(res) {
                if (res?.success === true) {
                    pending = false; initial = '';
                    bootstrap.Modal.getInstance($modal[0]).hide();
                    load(action === 'create' ? 1 : page);
                } else if (res?.success === false) message(feedback,res.message || 'The request was rejected.',true);
                else unknown();
            },
            error(xhr) {
                const res = xhr.responseJSON;
                if (res?.success === false) message(feedback,res.message || 'The request was rejected.',true);
                else if (xhr.status === 403 && res?.error) message(feedback,res.error,true);
                else unknown();
            },
            complete() {
                pending = false; listControls();
                $('#financialFields').prop('disabled',uncertain);
                $modal.find('[type="submit"], #financialDeleteConfirm').prop('disabled',uncertain).text(action === 'delete' ? 'Delete' : context.id === null ? 'Save' : 'Update');
            }
        });
        function unknown() {
            uncertain = true;
            message(feedback,'The result could not be confirmed. Close this dialog to refresh the records before trying again.',true);
        }
    }
    $('#financialRecordForm').on('submit', function(event) {
        event.preventDefault();
        if (!canManage || !form.reportValidity()) return;
        mutate(dialog?.id === null ? 'create' : 'update', $(form).serialize(), $editor, '#financialRecordFeedback');
    });
    $('#financialDeleteConfirm').on('click', () => mutate('delete',{csrf_token:csrfToken},$delete,'#financialDeleteFeedback'));
    load(1);
});
</script>
