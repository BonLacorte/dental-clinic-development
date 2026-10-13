<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_hris/partials/hris_nav.php'; ?>

<div class="container-fluid py-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Daily Time Record</h4>
            <div class="text-muted">
                Employee attendance and time records
            </div>
        </div>
    </div>


    <!-- Filters -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">

            <form method="GET" action="#">

                <div class="row g-3 align-items-end">

                    <div class="col-md-3">
                        <label class="form-label">
                            Month
                        </label>

                        <input type="month" name="month" class="form-control" value="<?= htmlspecialchars($month) ?>">
                    </div>


                    <?php if ($canViewAll): ?>

                        <div class="col-md-5">
                            <label class="form-label">
                                Employee
                            </label>

                            <select name="employee_id" class="form-select">
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


                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i>
                            View
                        </button>
                    </div>

                </div>

            </form>

        </div>
    </div>


    <!-- Summary -->
    <?php if ($employeeId > 0): ?>

        <div class="row g-3 mb-3">

            <div class="col-md-3">
                <div class="card h-100">
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


            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small">
                            Late
                        </div>

                        <div class="fs-4 fw-semibold">
                            <?= (int) ($summary['total_late_minutes'] ?? 0) ?>
                            <small class="fs-6 text-muted">mins</small>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small">
                            Undertime
                        </div>

                        <div class="fs-4 fw-semibold">
                            <?= (int) ($summary['total_undertime_minutes'] ?? 0) ?>
                            <small class="fs-6 text-muted">mins</small>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small">
                            Overtime
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
        <div class="card shadow-sm">

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


            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th class="text-end">Late</th>
                            <th class="text-end">Undertime</th>
                            <th class="text-end">Overtime</th>
                            <th class="text-end">Hours</th>
                            <th>Status</th>
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

                                            <span class="badge bg-success">
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

    <?php endif; ?>

</div>



<?php include '../app/views/partials/admin-footer.php'; ?>


</body>

</html>
