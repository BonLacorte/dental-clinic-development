<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_payroll/partials/payroll_nav.php'; ?>

<main class="container-fluid py-4 management-page import-page">
    <header class="import-heading">
        <p class="import-eyebrow">Payroll · Attendance Import</p>
        <h1 id="attendanceImportHeading">Import Biometric Logs</h1>
        <p>Upload attendance logs, review validation results, and confirm before importing.</p>
    </header>

    <ol class="import-steps" aria-label="Attendance import progress">
        <li id="importStepUpload" class="<?= $previewRows ? 'is-complete' : 'is-current' ?>" <?= !$previewRows ? 'aria-current="step"' : '' ?>>
            <span class="import-step-number">1</span><span>Upload</span>
        </li>
        <li id="importStepPreview" class="<?= $previewRows ? 'is-current' : '' ?>" <?= $previewRows ? 'aria-current="step"' : '' ?>>
            <span class="import-step-number">2</span><span>Preview &amp; Validate</span>
        </li>
        <li id="importStepConfirm"><span class="import-step-number">3</span><span id="importStepLabel">Import</span></li>
    </ol>

    <?php if ($errors): ?>
        <section class="alert alert-danger import-feedback" role="alert" aria-labelledby="previewErrorTitle">
            <h2 id="previewErrorTitle" class="h6">The file could not be previewed</h2>
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>

    <div class="import-upload-grid">
        <section class="card management-card import-upload-card" aria-labelledby="uploadTitle">
            <div class="card-body">
                <div class="import-section-heading">
                    <span class="import-icon" aria-hidden="true"><i class="bi bi-file-earmark-spreadsheet"></i></span>
                    <div><h2 id="uploadTitle">Select a biometric file</h2><p>Preview first. Import only after you confirm.</p></div>
                </div>
                <form id="previewForm" method="post" action="<?= htmlspecialchars(
                    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="action" value="preview">
                    <div class="import-file-workspace">
                        <label for="biometric_file" class="form-label fw-semibold">Biometric file</label>
                        <input class="form-control" type="file" id="biometric_file" name="biometric_file"
                            accept=".xlsx,.xls,.csv" required aria-describedby="fileGuidance fileSelectionFeedback">
                        <p id="fileGuidance" class="form-text">XLSX, XLS or CSV · Maximum 10 MB · Up to 5,000 data rows.</p>
                        <p id="fileSelectionFeedback" class="import-file-feedback mb-0" role="status" aria-live="polite" aria-atomic="true">No file selected.</p>
                    </div>
                    <p class="import-help">The server checks file contents and format when you preview.</p>
                    <?php if ($previewRows): ?>
                        <p class="import-help">Selecting another file does not replace the current preview until you choose Preview File.</p>
                    <?php endif; ?>
                    <button id="previewButton" class="btn btn-primary" type="submit"><i class="bi bi-eye me-2" aria-hidden="true"></i><span>Preview File</span></button>
                </form>
            </div>
        </section>

        <section class="card import-requirements" aria-labelledby="requirementsTitle">
            <div class="card-body">
                <h2 id="requirementsTitle">File Requirements</h2>
                <p>Use individual biometric attendance events, rather than aggregated DTR summaries.</p>
                <h3 class="h6">Required headers</h3>
                <ul class="import-header-list">
                    <li>Device User ID</li><li>Employee No</li><li>Log Date/Time</li><li>In/Out</li>
                </ul>
                <p class="import-help">Optional: Employee Name, Verify Mode (verification mode), Device ID.</p>
                <details>
                    <summary>Accepted header aliases &amp; event values</summary>
                    <dl class="import-aliases">
                        <dt>Device User ID</dt><dd>User ID, En No, TM No</dd>
                        <dt>Employee No</dt><dd>Employee Number, Employee ID, Badge No</dd>
                        <dt>Log Date/Time</dt><dd>Date Time, Timestamp, Log Time</dd>
                        <dt>In/Out</dt><dd>Event Type, Status, Punch State</dd>
                        <dt>Employee Name</dt><dd>Name</dd>
                        <dt>Verify Mode</dt><dd>Verification</dd>
                        <dt>Device ID</dt><dd>Device Name, Terminal</dd>
                    </dl>
                    <p class="import-help">Headers ignore spaces, punctuation and letter case. Use “Verify Mode” or “Verification”; “Verification Mode” is not a supported header.</p>
                    <p class="import-help mb-0">IN: IN, I, 0, CHECK IN, CHECK-IN.<br>OUT: OUT, O, 1, CHECK OUT, CHECK-OUT.</p>
                </details>
            </div>
        </section>
    </div>

    <?php if ($previewRows): ?>
        <section id="previewWorkspace" class="card import-preview-card mt-4" aria-labelledby="previewTitle">
            <div class="card-body">
                <div class="import-preview-heading">
                    <div><p class="import-eyebrow">Review before import</p><h2 id="previewTitle">File Preview</h2></div>
                    <span class="import-validation-label <?= $invalidCount ? 'text-danger' : '' ?>"><?= $invalidCount ? 'Validation errors' : 'Format validation passed' ?></span>
                </div>
                <p class="import-source"><span class="fw-semibold">Source file:</span> <?= htmlspecialchars($sourceFile, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
                <dl class="import-counts">
                    <div><dt>Normalized records</dt><dd><?= count($previewRows) ?></dd></div>
                    <div><dt>Format-valid records</dt><dd><?= count($previewRows) - $invalidCount ?></dd></div>
                    <div><dt>Format-invalid records</dt><dd><?= $invalidCount ?></dd></div>
                </dl>
                <div class="alert <?= $invalidCount ? 'alert-danger' : 'alert-success' ?> mb-3">
                    <i class="bi <?= $invalidCount ? 'bi-exclamation-triangle' : 'bi-check-circle' ?> me-2" aria-hidden="true"></i>
                    <?= $invalidCount ? 'Resolve validation errors before importing.' : 'File format validation passed. Review the records before importing.' ?>
                    <?php if ($invalidCount): ?>
                        Correct the source file and preview it again. Import is unavailable while any normalized record has an error.
                    <?php endif; ?>
                </div>
                <p class="import-help">Format validation checks structure only. It does not confirm employee matching or guarantee insertion. Existing attendance records may be inserted or updated.</p>
                <p id="previewLimit" class="import-help mb-0">
                    <?php if (count($previewRows) > 300): ?>
                        Showing the first 300 of <?= count($previewRows) ?> normalized records. The import uses the full validated preview, including records not displayed here. Errors may also occur outside these first 300 records.
                    <?php else: ?>
                        Showing all <?= count($previewRows) ?> normalized records.
                    <?php endif; ?>
                    Scroll within the table to review every column.
                </p>
            </div>
            <div class="table-responsive import-table-scroll" tabindex="0" role="region" aria-label="Biometric records preview" aria-describedby="previewLimit">
                <table class="table table-hover mb-0 import-preview-table">
                    <caption class="visually-hidden">Normalized biometric records and structural validation. Up to the first 300 records.</caption>
                    <thead>
                        <tr>
                            <th scope="col" class="text-end">Source Row Number</th><th scope="col">Employee Number</th>
                            <th scope="col">Employee Name</th><th scope="col">Log Date/Time</th>
                            <th scope="col">IN/OUT Event</th><th scope="col">Verification Mode</th>
                            <th scope="col">Device</th><th scope="col">Validation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($previewRows, 0, 300) as $row): ?>
                            <tr class="<?= $row['errors'] ? 'table-danger' : '' ?>">
                                <th scope="row" class="text-end import-row-number"><?= (int) $row['row_no'] ?></th>
                                <td class="fw-semibold"><?= htmlspecialchars($row['employee_no'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['employee_name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                                <td class="import-timestamp"><?= htmlspecialchars($row['log_datetime'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                                <td><span class="import-event"><?= htmlspecialchars($row['event_type'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></span></td>
                                <td><?= htmlspecialchars($row['verify_mode'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['device_id'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                                <td class="import-row-validation"><?= htmlspecialchars(implode('; ', $row['errors']) ?: 'Format valid', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer import-actions">
                <p id="importAvailability" class="import-help mb-0"><?= $invalidCount ? 'Correct validation errors and preview the file again to enable import.' : 'Reviewed the preview? Confirm to import attendance records.' ?></p>
                <form id="importForm" method="post" action="/payroll/attendance/import/process">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <button id="importAttendanceButton" type="submit" class="btn btn-primary" aria-describedby="importAvailability" <?= $invalidCount ? 'disabled' : '' ?>>Import Attendance</button>
                </form>
            </div>
        </section>

        <div class="modal fade payroll-items-dialog" id="importConfirmModal" tabindex="-1" aria-labelledby="importConfirmTitle" aria-describedby="importConfirmDescription" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header"><h2 class="modal-title h5" id="importConfirmTitle">Confirm Attendance Import</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close confirmation"></button></div>
                    <div class="modal-body">
                        <p id="importConfirmDescription">Existing attendance records may be inserted or updated. Format-valid records still depend on employee matching and server checks.</p>
                        <dl class="import-confirm-context">
                            <dt>Source file</dt><dd><?= htmlspecialchars($sourceFile, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></dd>
                            <dt>Normalized records</dt><dd><?= count($previewRows) ?></dd>
                            <dt>Structural validation</dt><dd><?= $invalidCount ? 'Contains validation errors. Import unavailable.' : 'File format validation passed.' ?></dd>
                        </dl>
                        <p class="import-help mb-0">The full preview is used, including records beyond the 300 shown in the table.</p>
                    </div>
                    <div class="modal-footer"><button id="cancelImportButton" type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button id="confirmImportButton" type="button" class="btn btn-primary" <?= $invalidCount ? 'disabled' : '' ?>>Import Attendance Records</button></div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <section id="importFeedback" class="import-feedback mt-4" tabindex="-1" aria-labelledby="importFeedbackTitle" hidden>
        <h2 id="importFeedbackTitle" class="h5"></h2>
        <p id="importFeedbackMessage" role="status" aria-live="polite" aria-atomic="true"></p>
        <dl id="importResultCounts" class="import-counts" hidden>
            <div><dt>Inserted</dt><dd id="importInserted"></dd></div><div><dt>Updated</dt><dd id="importUpdated"></dd></div><div><dt>Skipped</dt><dd id="importSkipped"></dd></div>
        </dl>
        <p id="importRecovery" class="import-help"></p>
        <button id="importReloadButton" type="button" class="btn btn-outline-primary" hidden>Return to upload</button>
    </section>
</main>

<?php include '../app/views/partials/admin-footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        'use strict';
        const previewForm = document.getElementById('previewForm');
        const fileInput = document.getElementById('biometric_file');
        const previewButton = document.getElementById('previewButton');
        const fileFeedback = document.getElementById('fileSelectionFeedback');
        const importForm = document.getElementById('importForm');
        const importButton = document.getElementById('importAttendanceButton');
        const availability = document.getElementById('importAvailability');
        const modalElement = document.getElementById('importConfirmModal');
        const confirmButton = document.getElementById('confirmImportButton');
        const feedback = document.getElementById('importFeedback');
        const reloadButton = document.getElementById('importReloadButton');
        const initiallyUnavailable = !importButton || importButton.disabled;
        const allowedExtensions = ['xlsx', 'xls', 'csv'];
        const maximumFileSize = 10 * 1024 * 1024;
        const confirmation = modalElement && window.bootstrap && window.bootstrap.Modal
            ? bootstrap.Modal.getOrCreateInstance(modalElement) : null;
        let previewPending = false;
        let importPending = false;
        let importFinished = false;
        let confirmationOpen = false;
        let confirmationAccepted = false;

        function setStage(stage, label) {
            ['Upload', 'Preview', 'Confirm'].forEach(function (name, index) {
                const step = document.getElementById('importStep' + name);
                step.classList.toggle('is-current', index === stage);
                step.classList.toggle('is-complete', index < stage);
                step.removeAttribute('aria-current');
                if (index === stage) step.setAttribute('aria-current', 'step');
            });
            document.getElementById('importStepLabel').textContent = label || 'Import';
        }

        function showFeedback(title, message, tone, recovery) {
            feedback.hidden = false;
            feedback.className = 'import-feedback mt-4 alert alert-' + tone;
            document.getElementById('importFeedbackTitle').textContent = title;
            document.getElementById('importFeedbackMessage').textContent = message;
            document.getElementById('importRecovery').textContent = recovery || '';
            feedback.focus();
        }

        function syncFileFeedback() {
            const file = fileInput.files[0];
            let message = 'No file selected.';
            let advisory = false;
            if (file) {
                const extension = file.name.split('.').pop().toLowerCase();
                message = file.name + ' · ' + (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                if (!allowedExtensions.includes(extension)) {
                    message += ' — Unsupported extension. Choose XLSX, XLS or CSV.';
                    advisory = true;
                }
                if (file.size > maximumFileSize) {
                    message += ' — File exceeds the 10 MB limit.';
                    advisory = true;
                }
                if (!advisory) message += ' — Selected. Choose Preview File to validate.';
            }
            fileFeedback.textContent = message;
            fileFeedback.classList.toggle('text-danger', advisory);
            // Advisory checks leave file bytes and native/server validation intact.
        }
        fileInput.addEventListener('change', syncFileFeedback);

        previewForm.addEventListener('submit', function (event) {
            if (previewPending || importPending || importFinished || confirmationOpen) {
                event.preventDefault();
                return;
            }
            if (!previewForm.reportValidity()) {
                event.preventDefault();
                return;
            }
            previewPending = true;
            previewForm.setAttribute('aria-busy', 'true');
            previewButton.disabled = true;
            previewButton.querySelector('span').textContent = 'Preparing preview…';
            fileFeedback.textContent = 'Uploading and validating the file. Wait for the server preview.';
            if (importButton) importButton.disabled = true;
            // Native multipart POST: do not disable the file or hidden fields.
        });

        // A history-restored page must not reuse an old session-backed preview.
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) window.location.assign(previewForm.action);
        });
        reloadButton.addEventListener('click', function () { window.location.assign(previewForm.action); });

        if (!importForm) return;

        function canImport() {
            return !initiallyUnavailable && !previewPending && !importPending && !importFinished;
        }

        function finishFailure(title, message, uncertain) {
            importFinished = true;
            importButton.disabled = true;
            availability.textContent = 'Import is unavailable. Review the result below before continuing.';
            setStage(1);
            showFeedback(title, message, 'danger', uncertain
                ? 'Some records may have been processed. Review attendance before uploading and previewing again. Do not retry blindly.'
                : 'Correct the reported issue, then upload and preview the file again.');
            reloadButton.hidden = false;
        }

        function submitImport() {
            if (!canImport()) return;
            const formData = new FormData(importForm);
            importPending = true;
            importButton.disabled = true;
            confirmButton.disabled = true;
            previewButton.disabled = true;
            fileInput.disabled = true;
            importForm.setAttribute('aria-busy', 'true');
            setStage(2, 'Importing…');
            showFeedback('Import in progress', 'Importing attendance records…', 'info', 'Keep this page open while the server responds.');
            showLoader('Importing biometric logs...');

            $.ajax({
                url: importForm.action,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json'
            })
                .done(function (response) {
                    if (!response || response.success !== true) {
                        const rejected = response && response.success === false;
                        finishFailure(rejected ? 'Import was not completed' : 'Import outcome unconfirmed',
                            response && typeof response.message === 'string' ? response.message : 'The server did not confirm the import result.', true);
                        return;
                    }
                    const counts = response.data && [response.data.inserted, response.data.updated, response.data.skipped];
                    if (!counts || !counts.every(value => Number.isSafeInteger(value) && value >= 0)) {
                        finishFailure('Import outcome unconfirmed', 'The server did not provide complete attendance result counts. Review attendance before starting another import.', true);
                        return;
                    }
                    importFinished = true;
                    importButton.textContent = 'Import completed';
                    availability.textContent = 'This preview has been consumed. Return to upload to start another preview.';
                    setStage(2, 'Completed');
                    document.getElementById('importStepConfirm').classList.add('is-complete');
                    showFeedback('Attendance Import Completed',
                        typeof response.message === 'string' ? response.message : 'The server confirmed the attendance import.',
                        'success', 'Skipped records are not necessarily duplicates. This result remains visible until you return to upload.');
                    const result = response.data || {};
                    ['inserted', 'updated', 'skipped'].forEach(function (field) {
                        const value = result[field];
                        document.getElementById('import' + field[0].toUpperCase() + field.slice(1)).textContent =
                            Number.isSafeInteger(value) && value >= 0 ? String(value) : 'Not provided';
                    });
                    document.getElementById('importResultCounts').hidden = false;
                    reloadButton.hidden = false;
                    // Keep results readable until a deliberate GET of the upload
                    // page. Reloading the preview POST could upload it again.
                })
                .fail(function (xhr) {
                    const rejected = xhr.responseJSON && xhr.responseJSON.success === false;
                    const validation = rejected && [419, 422].includes(xhr.status);
                    finishFailure(validation ? 'Import requires attention' : (rejected ? 'Import failed' : 'Import outcome unconfirmed'),
                        xhr.responseJSON && typeof xhr.responseJSON.message === 'string'
                            ? xhr.responseJSON.message
                            : 'The connection ended without a confirmed import result.', !validation);
                })
                .always(function () {
                    importPending = false;
                    importForm.removeAttribute('aria-busy');
                    hideLoader();
                    feedback.focus();
                });
        }

        importForm.addEventListener('submit', function (event) {
            event.preventDefault();
            if (!canImport() || confirmationOpen) return;
            if (confirmation) {
                confirmationOpen = true;
                confirmationAccepted = false;
                previewButton.disabled = true;
                confirmation.show();
            } else if (window.confirm('Import the previewed biometric logs into attendance? Existing attendance records may be inserted or updated.')) {
                submitImport();
            }
        });

        confirmButton.addEventListener('click', function () {
            if (!confirmationOpen || confirmationAccepted || !canImport()) return;
            confirmationAccepted = true;
            confirmButton.disabled = true;
            // Wait for Bootstrap to release its focus trap before showing results.
            confirmation.hide();
        });
        modalElement.addEventListener('shown.bs.modal', function () {
            document.getElementById('cancelImportButton').focus();
        });
        modalElement.addEventListener('hidden.bs.modal', function () {
            confirmationOpen = false;
            if (confirmationAccepted) {
                submitImport();
            } else {
                previewButton.disabled = previewPending || importFinished;
                importButton.focus();
            }
        });
    });
</script>
</body>
</html>