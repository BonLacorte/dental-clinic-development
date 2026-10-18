<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_payroll/partials/payroll_nav.php'; ?>



<main class="container py-4 management-page import-page">
    <div class="card shadow-sm management-card">
        <div class="card-header">
            <h5 class="mb-0">Import Biometric Logs</h5>
        </div>
        <div class="card-body">
            <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endforeach; ?>
            <?php if ($result): ?>
                <div class="alert alert-success"><?= $result['inserted'] ?> log(s) imported; <?= $result['duplicates'] ?>
                    duplicate(s) skipped.</div><?php endif; ?>
            <form id="previewForm" method="post" action="<?= htmlspecialchars(
                parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
                ENT_QUOTES,
                'UTF-8'
            ) ?>" enctype="multipart/form-data" class="row g-3 align-items-end">

                <input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

                <input type="hidden" name="action" value="preview">

                <div class="col-md-9">
                    <label for="biometric_file" class="form-label">
                        Biometric file
                    </label>

                    <input class="form-control" type="file" id="biometric_file" name="biometric_file"
                        accept=".xlsx,.xls,.csv" required>

                    <div class="form-text">
                        Accepted: XLSX, XLS, CSV. Maximum 5,000 records and 10 MB.
                    </div>
                </div>

                <div class="col-md-3 d-grid">
                    <button class="btn btn-primary" type="submit">
                        Preview File
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($previewRows): ?>
        <div class="card shadow-sm mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Preview: <?= htmlspecialchars($sourceFile) ?></span>
                <span class="badge text-bg-<?= $invalidCount ? 'warning' : 'success' ?>"><?= count($previewRows) ?> rows,
                    <?= $invalidCount ?> invalid</span>
            </div>
            <div class="table-responsive" style="max-height: 520px">
                <table class="table table-sm table-striped table-hover mb-0">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>Row</th>
                            <th>Employee No</th>
                            <th>Name</th>
                            <th>Log Date/Time</th>
                            <th>In/Out</th>
                            <th>Verify Mode</th>
                            <th>Device</th>
                            <th>Validation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($previewRows, 0, 300) as $row): ?>
                            <tr class="<?= $row['errors'] ? 'table-danger' : '' ?>">
                                <td><?= (int) $row['row_no'] ?></td>
                                <td><?= htmlspecialchars($row['employee_no']) ?></td>
                                <td><?= htmlspecialchars($row['employee_name']) ?></td>
                                <td><?= htmlspecialchars($row['log_datetime']) ?></td>
                                <td><?= htmlspecialchars($row['event_type']) ?></td>
                                <td><?= htmlspecialchars($row['verify_mode']) ?></td>
                                <td><?= htmlspecialchars($row['device_id']) ?></td>
                                <td><?= htmlspecialchars(implode('; ', $row['errors']) ?: 'Ready') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <form id="importForm" method="post" action="/payroll/attendance/import/process">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(
                        $csrf_token ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">

                    <button type="submit" class="btn btn-success">
                        Import Attendance
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</main>




<?php include '../app/views/partials/admin-footer.php'; ?>


<script>

    const previewForm = document.getElementById('previewForm');
    const fileInput = document.getElementById('biometric_file');
    const importForm = document.getElementById('importForm');

    const allowedExtensions = ['xlsx', 'xls', 'csv'];
    const maximumFileSize = 10 * 1024 * 1024;

    document.addEventListener('DOMContentLoaded', function () {


        if (importForm) {
            importForm.addEventListener('submit', function (event) {
                event.preventDefault();

                const confirmed = window.confirm(
                    'Import the previewed biometric logs into attendance?'
                );

                if (!confirmed) {
                    return;
                }

                const formData = new FormData(importForm);



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
                        if (!response.success) {
                            showToast(
                                response.message ||
                                'Unable to import biometric logs.',
                                'danger'
                            );

                            return;
                        }

                        const result = response.data || {};

                        const message = [
                            `${result.inserted || 0} inserted`,
                            `${result.updated || 0} updated`,
                            `${result.skipped || 0} skipped`
                        ].join(', ');

                        showToast(
                            `Biometric import completed: ${message}.`,
                            'success'
                        );

                        window.setTimeout(function () {
                            window.location.reload();
                        }, 800);
                    })
                    .fail(function (xhr) {
                        console.error(xhr.responseText);

                        let message =
                            'Unable to import biometric attendance logs.';

                        if (
                            xhr.responseJSON &&
                            xhr.responseJSON.message
                        ) {
                            message = xhr.responseJSON.message;
                        }

                        showToast(message, 'danger');
                    })
                    .always(function () {
                        hideLoader();

                    });
            });
        }

    });

</script>

</body>

</html>