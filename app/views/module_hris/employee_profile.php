<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_hris/partials/hris_nav.php'; ?>
<main class="employee-profile container-fluid py-4 hris-page" aria-labelledby="profileHeading">
<?php if (!$employee): ?>
    <div class="card profile-section"><h1 class="h4" id="profileHeading">Employee not found</h1><p>This record is unavailable or outside your access.</p><a href="/hris/employees" class="btn btn-outline-primary align-self-start">Back to employees</a></div>
<?php else:
    $fullName = trim(preg_replace('/\s+/', ' ', implode(' ', [$employee['firstname'], $employee['middlename'] ?? '', $employee['lastname']])));
    $display = static fn($value): string => e($value !== null && $value !== '' ? (string) $value : 'Not recorded');
    $frequencyLabels = ['DAILY' => 'Daily', 'WEEKLY' => 'Weekly', 'BI_WEEKLY' => 'Bi-weekly', 'SEMI_MONTHLY' => 'Semi-monthly', 'MONTHLY' => 'Monthly'];
    $salaryLabels = ['DAILY' => 'Daily rate', 'MONTHLY' => 'Monthly rate', 'HOURLY' => 'Hourly rate'];
    $rateLabel = $salaryLabels[$compensation['salary_type'] ?? ''] ?? 'Salary rate';
    $frequency = $compensation ? ($frequencyLabels[$compensation['payroll_frequency']] ?? $compensation['payroll_frequency']) : null;
    $rate = $compensation ? '₱' . number_format((float) $compensation['salary_rate'], 2) : null;
    $profileUrl = '/hris/employees/profile/' . (int) $employee['id'];
    $requestedTab = $_GET['tab'] ?? 'overview';
    $activeTab = in_array($requestedTab, ['overview', 'assignments', 'details'], true) ? $requestedTab : 'overview';
    $initials = mb_substr($employee['firstname'], 0, 1) . mb_substr($employee['lastname'], 0, 1);
?>
    <header class="profile-header">
        <nav aria-label="Breadcrumb"><ol class="profile-breadcrumb"><li><?php if ($navHrisHome): ?><a href="/hris">HRIS</a><?php else: ?>HRIS<?php endif; ?></li><li><a href="/hris/employees">Employees</a></li><li aria-current="page"><?= e($fullName) ?></li></ol></nav>
        <div class="profile-heading-row">
            <div class="profile-person"><span class="profile-avatar" aria-hidden="true"><?= e(mb_strtoupper($initials)) ?></span><div><h1 id="profileHeading"><?= e($fullName) ?></h1><p><?= $display($employee['position']) ?> <span aria-hidden="true">·</span> <?= $display($employee['org_unit']) ?> <span aria-hidden="true">·</span> <?= $display($employee['employee_no']) ?></p><span class="profile-status">Employment status: <?= $display($employee['employment_status']) ?></span></div></div>
            <?php if ($canEdit): ?><a class="btn btn-primary profile-edit-link" href="<?= e($profileUrl) ?>?tab=details">Edit employee <i class="bi bi-pencil-square" aria-hidden="true"></i></a><?php endif; ?>
        </div>
        <nav class="profile-tabs" aria-label="Employee profile sections">
            <?php foreach (['overview' => 'Overview', 'assignments' => 'Assignments', 'attendance' => 'Attendance', 'leave' => 'Leave', 'payslips' => 'Payslips', 'financial' => 'Financial', 'details' => 'Details & Edit'] as $key => $label): ?>
                <?php if (in_array($key, ['overview', 'assignments', 'details'], true)): ?>
                <a href="<?= e($profileUrl) ?>?tab=<?= e($key) ?>"<?= $activeTab === $key ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
                <?php else: ?><span class="profile-tab-disabled" aria-disabled="true"><?= e($label) ?><span class="visually-hidden"> - not available yet</span></span><?php endif; ?>
            <?php endforeach; ?>
        </nav>
    </header>
    <?php if ($activeTab === 'overview'): ?>
    <div class="profile-grid profile-overview">
        <section class="card profile-section" aria-labelledby="employmentHeading">
            <div class="profile-section-title"><span class="profile-section-icon bi bi-person-badge" aria-hidden="true"></span><h2 id="employmentHeading">Employment</h2></div>
            <dl class="profile-facts profile-facts-columns">
                <?php foreach (['Employee ID' => $employee['employee_no'], 'Department / organization unit' => $employee['org_unit'], 'Position' => $employee['position'], 'Branch' => $employee['branch_name'], 'Employment type' => $employee['employment_type'], 'Date hired' => $employee['date_hired'], 'Shift' => $shift['name'] ?? null, 'Employment status' => $employee['employment_status'], 'Date resigned' => $employee['date_resigned']] as $label => $value): ?>
                <div><dt><?= e($label) ?></dt><dd><?= $display($value) ?></dd></div>
                <?php endforeach; ?>
            </dl>
        </section>
        <section class="card profile-section" aria-labelledby="compensationHeading">
            <div class="profile-section-title"><span class="profile-section-icon bi bi-wallet2" aria-hidden="true"></span><h2 id="compensationHeading">Compensation</h2></div>
            <p class="profile-caption">Current effective compensation</p>
            <?php if (!$compensation): ?><p class="profile-empty">No current compensation record.</p><?php else: ?>
            <dl class="profile-facts profile-facts-inline">
                <?php foreach ([$rateLabel => $rate, 'Pay schedule' => $frequency, 'Effective from' => $compensation['effective_from'], 'Effective to' => $compensation['effective_to']] as $label => $value): ?><div><dt><?= e($label) ?></dt><dd><?= $display($value) ?></dd></div><?php endforeach; ?>
            </dl>
            <?php endif; ?>
        </section>
    </div>
    <?php elseif ($activeTab === 'assignments'): ?>
    <div class="profile-content-heading"><h2>Assignments</h2><p>Current organization, shift and payroll settings for this employee.</p></div>
    <div class="profile-grid">
        <section class="card profile-section"><div class="profile-section-title"><span class="profile-section-icon bi bi-diagram-3" aria-hidden="true"></span><h3>Department</h3></div><p class="profile-assignment-value"><?= $display($employee['org_unit']) ?></p><dl class="profile-facts profile-facts-inline"><div><dt>Position</dt><dd><?= $display($employee['position']) ?></dd></div><div><dt>Employment type</dt><dd><?= $display($employee['employment_type']) ?></dd></div></dl><?php if ($canEdit): ?><a class="profile-section-link" href="<?= e($profileUrl) ?>?tab=details#profile-employment">Edit employment details <i class="bi bi-arrow-right" aria-hidden="true"></i></a><?php endif; ?></section>
        <section class="card profile-section"><div class="profile-section-title"><span class="profile-section-icon bi bi-clock" aria-hidden="true"></span><h3>Shift</h3></div><p class="profile-assignment-value"><?= $display($shift['name'] ?? null) ?></p><?php if ($shift): ?><dl class="profile-facts profile-facts-inline"><?php foreach (['Type' => $shift['shift_type'], 'Start time' => $shift['start_time'], 'End time' => $shift['end_time'], 'Required hours' => $shift['required_hours'], 'Break (minutes)' => $shift['break_minutes'], 'Source' => $shift['source']] as $label => $value): ?><div><dt><?= e($label) ?></dt><dd><?= $display($value) ?></dd></div><?php endforeach; ?></dl><?php else: ?><p class="profile-empty">No current employee shift or active branch default.</p><?php endif; ?></section>
        <section class="card profile-section"><div class="profile-section-title"><span class="profile-section-icon bi bi-calendar2-check" aria-hidden="true"></span><h3>Leave package</h3></div><p class="profile-empty">Leave package assignments are not available in the current employee records.</p></section>
        <section class="card profile-section"><div class="profile-section-title"><span class="profile-section-icon bi bi-calendar3" aria-hidden="true"></span><h3>Payroll schedule</h3></div><p class="profile-assignment-value"><?= $display($frequency) ?></p><dl class="profile-facts profile-facts-inline"><div><dt>Pay type</dt><dd><?= $display($compensation ? ($salaryLabels[$compensation['salary_type']] ?? $compensation['salary_type']) : null) ?></dd></div><div><dt>Effective from</dt><dd><?= $display($compensation['effective_from'] ?? null) ?></dd></div></dl></section>
        <section class="card profile-section"><div class="profile-section-title"><span class="profile-section-icon bi bi-shield-check" aria-hidden="true"></span><h3>Permission group</h3></div><p class="profile-empty">Permission group assignments are not available in the current employee records.</p></section>
    </div>
    <?php else: ?>
    <section class="profile-details" aria-labelledby="detailsHeading">
        <div class="profile-content-heading"><h2 id="detailsHeading">Details &amp; Edit</h2><p>Keep identity, contact and employment information up to date.</p></div>
        <?php if (!$canEdit): ?><div class="alert alert-info">You can view this employee profile. Editing requires employee edit permission.</div><?php endif; ?>
        <form id="profileEmployeeForm" class="employee-form" action="/hris/employees/updateemployee/<?= (int) $employee['id'] ?>" method="post" data-can-edit="<?= $canEdit ? 'true' : 'false' ?>">
            <input type="hidden" name="id" value="<?= (int) $employee['id'] ?>">
            <?php if ($canEdit): ?><input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>"><?php endif; ?>
            <div id="profileFeedback" class="alert" role="status" tabindex="-1" hidden></div>
            <div id="profileLookupFeedback" class="alert alert-warning" role="status" hidden><p class="mb-2">Some choices could not be loaded. Your entries are kept.</p><button type="button" class="btn btn-outline-primary" id="profileRetryChoices">Retry choices</button></div>
            <p class="profile-caption">Fields marked * are required. Employee number is assigned by the system.</p>
            <?php
                $groups = [
                    'Identity' => [['employee_no', 'Employee No', 'text', false], ['firstname', 'First Name', 'text', true], ['middlename', 'Middle Name', 'text', false], ['lastname', 'Last Name', 'text', true]],
                    'Contact' => [['email', 'Email', 'email', false], ['contact_no', 'Contact No', 'text', false]],
                    'Employment' => [['branch_id', 'Branch', 'select', true, 'branch_name'], ['position_id', 'Position', 'select', false, 'position'], ['org_unit_id', 'Organization Unit', 'select', false, 'org_unit'], ['employment_type_id', 'Employment Type', 'select', true, 'employment_type'], ['date_hired', 'Date Hired', 'date', true], ['employment_status_id', 'Employment Status', 'select', false, 'employment_status'], ['date_resigned', 'Date Resigned', 'date', false]],
                ];
                foreach ($groups as $group => $fields):
            ?>
            <fieldset class="card profile-section" id="profile-<?= e(strtolower($group)) ?>"<?= !$canEdit ? ' disabled' : '' ?>>
                <legend><?= e($group) ?></legend><div class="row g-3">
                <?php foreach ($fields as $field): [$fieldName, $label, $type, $required] = $field; $value = $employee[$fieldName] ?? ''; ?>
                    <div class="col-12 col-md-6"><label class="form-label" for="profile_<?= e($fieldName) ?>"><?= e($label) ?><?= $required ? ' *' : '' ?></label>
                    <?php if ($type === 'select'): ?>
                        <select class="form-select" id="profile_<?= e($fieldName) ?>" name="<?= e($fieldName) ?>"<?= $required ? ' required' : '' ?>><option value="">Select <?= e($label) ?></option><?php if ($value !== ''): ?><option value="<?= e($value) ?>" selected><?= $display($employee[$field[4]] ?? null) ?></option><?php endif; ?></select>
                    <?php else: ?>
                        <input class="form-control" id="profile_<?= e($fieldName) ?>" name="<?= e($fieldName) ?>" type="<?= e($type) ?>" value="<?= e($value) ?>"<?= $required ? ' required' : '' ?><?= $fieldName === 'employee_no' ? ' readonly' : '' ?>>
                    <?php endif; ?></div>
                <?php endforeach; ?>
                </div>
            </fieldset>
            <?php endforeach; ?>
            <?php if ($canEdit): ?><div class="profile-save-bar"><p id="profileSaveStatus" class="mb-0" role="status">No unsaved changes.</p><div class="d-flex gap-2 flex-wrap"><button type="button" class="btn btn-outline-secondary" id="profileReset">Discard changes</button><button type="submit" class="btn btn-primary" id="profileSave" disabled>Save changes</button></div></div><?php endif; ?>
        </form>
    </section>
    <?php endif; ?>
<?php endif; ?>
</main>
<?php include '../app/views/partials/admin-footer.php'; ?>
<?php if ($employee && $activeTab === 'details' && $canEdit): ?>
<script>
/* Employee profile editing uses the existing employee update and lookup contracts. */
$(function () {
    const form = document.getElementById('profileEmployeeForm');
    if (!form || form.dataset.canEdit !== 'true') return;
    const $form = $(form), $save = $('#profileSave'), $reset = $('#profileReset');
    const lookups = {
        branch_id: '/hris/branches/getallbranches',
        position_id: '/hris/positions/getallpositions',
        org_unit_id: '/hris/offices/getallunits',
        employment_type_id: '/hris/employees/employment-types',
        employment_status_id: '/hris/employees/employment-statuses'
    };
    const fields = ['employee_no', 'firstname', 'middlename', 'lastname', 'email', 'contact_no', 'branch_id', 'position_id', 'org_unit_id', 'employment_type_id', 'date_hired', 'employment_status_id', 'date_resigned', 'csrf_token'];
    const values = () => Object.fromEntries(fields.map(name => [name, $form.find(`[name="${name}"]`).val() ?? '']));
    let baseline = values(), pending = false, loading = false;
    const ready = new Set();
    const dirty = () => JSON.stringify(values()) !== JSON.stringify(baseline);
    function sync() {
        $save.prop('disabled', pending || loading || ready.size !== Object.keys(lookups).length || !dirty());
        $reset.prop('disabled', pending || !dirty());
        $('#profileSaveStatus').text(pending ? 'Saving changes...' : loading ? 'Loading choices...' : dirty() ? 'You have unsaved changes.' : 'No unsaved changes.');
    }
    function feedback(message, success = false) {
        $('#profileFeedback').removeClass('alert-success alert-danger').addClass(success ? 'alert-success' : 'alert-danger').prop('hidden', false).text(message).trigger('focus');
    }
    function loadChoices() {
        if (loading || pending) return;
        loading = true;
        $('#profileLookupFeedback').prop('hidden', true);
        sync();
        const requests = Object.entries(lookups).filter(([name]) => !ready.has(name)).map(async ([name, url]) => {
            try {
                const rows = [];
                let page = 1, totalPages = 1;
                do {
                    const res = await $.ajax({url, data: {page}, dataType: 'json'});
                    if (!res || res.error || res.success === false || !Array.isArray(res.data)) return;
                    rows.push(...res.data);
                    // Branch choices use the existing paginated list contract.
                    if (name === 'branch_id') {
                        const count = Number(res.totalPages);
                        if (!Number.isSafeInteger(count) || count < 0) return;
                        totalPages = count;
                    }
                    page += 1;
                } while (page <= totalPages);
                const select = form.elements.namedItem(name), current = select.value;
                const currentLabel = select.selectedOptions[0]?.textContent;
                const placeholder = select.options[0].textContent;
                select.replaceChildren(new Option(placeholder, ''));
                rows.forEach(row => select.add(new Option(String(row.name ?? ''), String(row.id))));
                if (current && !Array.from(select.options).some(option => option.value === current)) {
                    // Keep a historical assignment visible; do not silently clear it.
                    select.add(new Option(currentLabel || `Current selection (${current})`, current));
                }
                select.value = current;
                ready.add(name);
            } catch {
                // Leave the current selection and entries intact for Retry.
            }
        });
        Promise.all(requests).finally(() => {
            loading = false;
            $('#profileLookupFeedback').prop('hidden', ready.size === Object.keys(lookups).length);
            sync();
        });
    }
    $form.on('input change', sync);
    $('#profileRetryChoices').on('click', loadChoices);
    $reset.on('click', function () {
        if (pending || !window.confirm('Discard your unsaved employee changes?')) return;
        fields.forEach(name => { form.elements.namedItem(name).value = baseline[name]; });
        $('#profileFeedback').prop('hidden', true);
        sync();
    });
    window.addEventListener('beforeunload', event => {
        if (pending || dirty()) { event.preventDefault(); event.returnValue = ''; }
    });
    $form.on('submit', function (event) {
        event.preventDefault();
        if (pending || $save.prop('disabled') || !form.reportValidity()) return;
        if (form.elements.date_resigned.value && form.elements.date_resigned.value < form.elements.date_hired.value) {
            feedback('Date resigned cannot be earlier than date hired.');
            form.elements.date_resigned.focus();
            return;
        }
        const submitted = values();
        pending = true;
        $('#profileFeedback').prop('hidden', true);
        // Prevent edits while the submitted snapshot is being saved.
        $form.find('fieldset').prop('disabled', true);
        sync();
        $.ajax({url: form.action, method: 'POST', dataType: 'json', data: submitted}).done(res => {
            if (!res || res.success !== true) {
                feedback(res?.message || res?.error || 'The save was not confirmed. Your entries are kept.');
                return;
            }
            baseline = submitted;
            feedback(res.message || 'Employee updated successfully.', true);
            // Refresh the server-rendered identity, overview and assignment values.
            pending = false;
            window.location.reload();
        }).fail(() => {
            feedback('The save result could not be confirmed. Your entries are kept. Check the record before retrying.');
        }).always(() => {
            pending = false;
            $form.find('fieldset').prop('disabled', false);
            sync();
        });
    });
    loadChoices();
});
</script>
<?php endif; ?>
</body>
</html>
