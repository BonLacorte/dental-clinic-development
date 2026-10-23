<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_hris/partials/hris_nav.php'; ?>

<div class="container-fluid py-3 management-page attendance-page hris-page attendance-workspace">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <p class="attendance-eyebrow">HRIS · Attendance</p><h1 class="h4 mb-1" tabindex="-1" id="dtrHeading">Daily Time Record</h1>
            <div class="text-muted">
                Review daily attendance and the existing monthly time summaries.
            </div>
        </div>
    </div>


    <!-- Filters -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">

            <h2 class="h6">Choose employee and reporting month</h2>
            <p class="text-muted small">Select the reporting period, then view the available records.</p>
            <form method="GET" action="#">

                <div class="row g-3 align-items-end">

                    <div class="col-md-3">
                        <label for="dtrFilter1_month" class="form-label">
                            Month
                        </label>

                        <input id="dtrFilter1_month" type="month" name="month" class="form-control" value="<?= htmlspecialchars($month) ?>">
                    </div>


                    <?php if ($canViewAll): ?>

                        <div class="col-md-5">
                            <label for="dtrFilter1_employee_id" class="form-label">
                                Employee
                            </label>

                            <select id="dtrFilter1_employee_id" name="employee_id" class="form-select">
                                <option value="">
                                    Select Employee
                                </option>

                                <?php foreach ($employees as $employee): ?>

                                    <option value="<?= $employee['id'] ?>" <?= (int) $employeeId === (int) $employee['id']
                                          ? 'selected'
                                          : '' ?>>
                                        <?= htmlspecialchars(
                                            $employee['employee_no']
                                            . ' - '
                                            . $employee['lastname']
                                            . ', '
                                            . $employee['firstname']
                                        ) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>
                        </div>

                    <?php endif; ?>


                    <div class="col-md-4 col-lg-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search" aria-hidden="true"></i>
                            View Records
                        </button>
                    </div>

                </div>

            </form>

        </div>
    </div>


    <p id="dtrSelectedContext" class="attendance-context" role="status"></p>
    <!-- Summary -->
    <?php if ($employeeId > 0): ?>

        <div class="row g-3 mb-3">

            <div class="col-6 col-xl-3">
                <div class="card h-100 attendance-metric">
                    <div class="card-body">
                        <div class="text-muted small">
                            Days Present
                        </div>

                        <div class="fs-4 fw-semibold">
                            <?= (int) ($summary['days_present'] ?? 0) ?>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-6 col-xl-3">
                <div class="card h-100 attendance-metric">
                    <div class="card-body">
                        <div class="text-muted small">
                            Late Minutes
                        </div>

                        <div class="fs-4 fw-semibold">
                            <?= (int) ($summary['total_late_minutes'] ?? 0) ?>
                            <small class="fs-6 text-muted">mins</small>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-6 col-xl-3">
                <div class="card h-100 attendance-metric">
                    <div class="card-body">
                        <div class="text-muted small">
                            Undertime Minutes
                        </div>

                        <div class="fs-4 fw-semibold">
                            <?= (int) ($summary['total_undertime_minutes'] ?? 0) ?>
                            <small class="fs-6 text-muted">mins</small>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-6 col-xl-3">
                <div class="card h-100 attendance-metric">
                    <div class="card-body">
                        <div class="text-muted small">
                            Overtime Minutes
                        </div>

                        <div class="fs-4 fw-semibold">
                            <?= (int) ($summary['total_overtime_minutes'] ?? 0) ?>
                            <small class="fs-6 text-muted">mins</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <!-- Attendance -->
        <div class="card shadow-sm attendance-log">

            <div class="card-header bg-white">
                <div class="fw-semibold">
                    Attendance Log
                </div>

                <div class="small text-muted">
                    <?= date('F j', strtotime($dateFrom)) ?>
                    -
                    <?= date('F j, Y', strtotime($dateTo)) ?>
                </div>
            </div>


            <p class="hris-scroll-hint" id="dtrScrollHelp">Use horizontal scrolling when columns extend beyond this view.</p>
            <div class="table-responsive hris-table-region" tabindex="0" role="region" aria-labelledby="dtrHeading" aria-describedby="dtrScrollHelp">

                <table class="table table-hover align-middle mb-0 hris-data-table">
                    <caption class="visually-hidden">Daily attendance for the selected employee and month.</caption>

                    <thead class="table-light">
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Day</th>
                            <th scope="col">Time In</th>
                            <th scope="col">Time Out</th>
                            <th scope="col" class="text-end">Late (mins)</th>
                            <th scope="col" class="text-end">Undertime (mins)</th>
                            <th scope="col" class="text-end">Overtime (mins)</th>
                            <th scope="col" class="text-end">Hours</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if (empty($attendance)): ?>

                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    No attendance records found for this month.
                                </td>
                            </tr>

                        <?php else: ?>

                            <?php foreach ($attendance as $row): ?>

                                <tr>

                                    <td>
                                        <?= date('M d, Y', strtotime($row['attendance_date'])) ?>
                                    </td>

                                    <td>
                                        <?= date('D', strtotime($row['attendance_date'])) ?>
                                    </td>

                                    <td>
                                        <?= !empty($row['time_in'])
                                            ? date('h:i A', strtotime($row['time_in']))
                                            : '—' ?>
                                    </td>

                                    <td>
                                        <?= !empty($row['time_out'])
                                            ? date('h:i A', strtotime($row['time_out']))
                                            : '—' ?>
                                    </td>

                                    <td class="text-end">
                                        <?= (int) $row['late_minutes'] ?? 0 ?>
                                    </td>

                                    <td class="text-end">
                                        <?= (int) $row['undertime_minutes'] ?? 0 ?>
                                    </td>

                                    <td class="text-end">
                                        <?= (int) $row['overtime_minutes'] ?? 0 ?>
                                    </td>

                                    <td class="text-end">
                                        <?= $row['computed_actual_hours'] !== null
                                            ? number_format((float) $row['computed_actual_hours'], 2)
                                            : '—' ?>
                                    </td>

                                    <td>
                                        <?php if ($row['id']): ?>

                                            <span class="badge bg-success hris-status" data-hris-kind="attendance">
                                                <?= htmlspecialchars($row['status'] ?? 'PRESENT') ?>
                                            </span>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                —
                                            </span>

                                        <?php endif; ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    <?php else: ?>
        <div class="card attendance-initial"><div class="card-body text-center py-5">
            <i class="bi bi-calendar3 attendance-empty-icon" aria-hidden="true"></i>
            <h2 class="h5 mt-3">Choose an employee to view records</h2>
            <p class="text-muted mb-0">Monthly attendance and time summaries will appear here.</p>
        </div></div>
    <?php endif; ?>

</div>



<?php include '../app/views/partials/admin-footer.php'; ?>
<script>
// Display the submitted server selection. Changing controls does not relabel the loaded records.
const dtrEmployee = document.getElementById('dtrFilter1_employee_id');
const dtrMonth = document.getElementById('dtrFilter1_month').value;
if (document.querySelector('.attendance-log')) {
    const employee = dtrEmployee?.selectedOptions[0]?.value ? dtrEmployee.selectedOptions[0].textContent.trim().replace(/\s+/g, ' ') : 'Selected employee';
    document.getElementById('dtrSelectedContext').textContent = employee + ' · Reporting month: ' + dtrMonth;
}
</script>


</body>

</html>
