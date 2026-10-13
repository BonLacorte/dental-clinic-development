<?php

class AttendanceLogModel extends Model
{
    public function __construct()
    {
        parent::__construct('default');
    }

    public function employeeExists(int $tenantId, int $employeeId)
    {
        $stmt = $this->db->prepare("
            SELECT 1
            FROM hris_employees
            WHERE id = :employee_id
            AND tenant_id = :tenant_id
     
            LIMIT 1
        ");

        $stmt->execute([
            'employee_id' => $employeeId,
            'tenant_id' => $tenantId

        ]);

        return (bool) $stmt->fetchColumn();
    }
    public function userCanAccessBranch($userId, $tenantId, $branchId)
    {
        $stmt = $this->db->prepare("
            SELECT 1
            FROM user_branches ub
            INNER JOIN branches b ON b.branch_id = ub.branch_id
            WHERE ub.userid = :userid
              AND ub.branch_id = :branch_id
              AND b.tenant_id = :tenant_id
            LIMIT 1
        ");
        $stmt->execute([
            'userid' => $userId,
            'branch_id' => $branchId,
            'tenant_id' => $tenantId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function employeeBelongsToBranch($employeeId, $tenantId, $branchId)
    {
        $stmt = $this->db->prepare("
            SELECT 1
            FROM hris_employees
            WHERE id = :employee_id
              AND tenant_id = :tenant_id
              AND branch_id = :branch_id
            LIMIT 1
        ");
        $stmt->execute([
            'employee_id' => $employeeId,
            'tenant_id' => $tenantId,
            'branch_id' => $branchId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    private function getEffectiveShift($tenantId, $branchId, $employeeId, $attendanceDate): ?array
    {
        $sql = "
            SELECT
              
                s.id as shift_id,
                s.name as shift_name, 
                s.shift_type, 
                s.start_time, 
                s.end_time, 
                s.flex_start_time, 
                s.flex_end_time, 
                s.required_hours, 
                COALESCE(s.break_minutes, 0) AS break_minutes,
                s.allow_overtime,
                'EMPLOYEE' AS shift_source
            
            FROM hris_employee_shift_schedules es

            INNER JOIN hris_shifts s
                ON s.id = es.shift_id
                AND s.tenant_id = es.tenant_id

            WHERE es.tenant_id = :tenant_id
              AND es.employee_id = :employee_id

              AND es.effective_from <= :attendance_date

              AND (
                    es.effective_to IS NULL
                    OR es.effective_to >= :attendance_date
                  )

            ORDER BY
                es.effective_from DESC,
                es.id DESC

            LIMIT 1
        ";


        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'tenant_id' => $tenantId,
            'employee_id' => $employeeId,
            'attendance_date' => $attendanceDate,

        ]);

        $shift = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($shift) {
            return $shift;
        }



        $sql = "
            SELECT 
                a.branch_id,
                b.name, 
                b.shift_type, 
                b.start_time, 
                b.end_time, 
                b.flex_start_time, 
                b.flex_end_time, 
                b.required_hours, 
                COALESCE(b.break_minutes, 0) AS break_minutes,
                b.allow_overtime
            
            FROM

            hris_branch_shifts a
            
            INNER JOIN hris_shifts b
                ON a.shift_id = b.id
                AND a.tenant_id = b.tenant_id

            WHERE 
                a.tenant_id = :tenant_id
                AND a.branch_id = :branch_id
            
            ORDER BY
                a.id DESC

            LIMIT 1

        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'tenant_id' => $tenantId,
            'branch_id' => $branchId,
        ]);

        $shift = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($shift) {
            return $shift;
        }

        return null;
    }

    private function computeAttendance($attendanceLogId, $tenantId, $branchId, $employeeId, $attendanceDate, $timeIn, $timeOut)
    {

        /*
         * Resolve shift here:
         *
         * employee shift
         *      ↓ fallback
         * branch shift
         */

        $shift = $this->getEffectiveShift($tenantId, $branchId, $employeeId, $attendanceDate);

        if (!$shift) {
            return;
        }

        $scheduledIn = new DateTimeImmutable($attendanceDate . ' ' . $shift['start_time']);

        $scheduledOut = new DateTimeImmutable($attendanceDate . ' ' . $shift['end_time']);

        $actualIn = new DateTimeImmutable($timeIn);
        $actualOut = new DateTimeImmutable($timeOut);

        $breakMinutes = (int) ($shift['break_minutes'] ?? 0);


        $expectedMinutes = (int) floor(
            ($scheduledOut->getTimestamp() - $scheduledIn->getTimestamp()) / 60
        );

        $expectedMinutes -= $breakMinutes;
        $expectedMinutes = max(0, $expectedMinutes);

        $actualMinutes = (int) floor(
            ($actualOut->getTimestamp() - $actualIn->getTimestamp()) / 60
        );

        $actualMinutes -= $breakMinutes;
        $actualMinutes = max(0, $actualMinutes);

        $lateMinutes = 0;

        if ($actualIn > $scheduledIn) {
            $lateMinutes = (int) floor(
                ($actualIn->getTimestamp() - $scheduledIn->getTimestamp()) / 60
            );
        }


        $undertimeMinutes = 0;

        if ($actualOut < $scheduledOut) {
            $undertimeMinutes = (int) floor(
                ($scheduledOut->getTimestamp() - $actualOut->getTimestamp()) / 60
            );
        }


        $overtimeMinutes = 0;

        if ($actualOut > $scheduledOut) {
            $overtimeMinutes = (int) floor(
                ($actualOut->getTimestamp() - $scheduledOut->getTimestamp()) / 60
            );
        }

        $stmt = $this->db->prepare("
            UPDATE hris_attendance_logs
                SET
                    break_minutes = :break_minutes,
                    late_minutes = :late_minutes,
                    undertime_minutes = :undertime_minutes,
                    overtime_minutes = :overtime_minutes,
                    computed_expected_hours = :expected_hours,
                    computed_actual_hours = :actual_hours
                WHERE id = :id
                AND tenant_id = :tenant_id
            ");

        $stmt->execute([
            'break_minutes' => $breakMinutes,
            'late_minutes' => $lateMinutes,
            'undertime_minutes' => $undertimeMinutes,
            'overtime_minutes' => $overtimeMinutes,
            'expected_hours' => round($expectedMinutes / 60, 2),
            'actual_hours' => round($actualMinutes / 60, 2),
            'id' => $attendanceLogId,
            'tenant_id' => $tenantId,
        ]);
    }

    public function recordScan($tenantId, $branchId, $employeeId, $deviceId, DateTimeImmutable $scanTime)
    {
        $attendanceDate = $scanTime->format('Y-m-d');
        $timestamp = $scanTime->format('Y-m-d H:i:s');

        try {

            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                SELECT id, time_in, time_out
                FROM hris_attendance_logs
                WHERE tenant_id = :tenant_id
                AND branch_id = :branch_id
                AND employee_id = :employee_id
                AND attendance_date = :attendance_date
                LIMIT 1
                FOR UPDATE
            ");

            $stmt->execute([
                'tenant_id' => $tenantId,
                'branch_id' => $branchId,
                'employee_id' => $employeeId,
                'attendance_date' => $attendanceDate,
            ]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {

                $insert = $this->db->prepare("
                    INSERT INTO hris_attendance_logs
                    (
                        tenant_id,
                        branch_id,
                        employee_id,
                        attendance_date,
                        time_in,
                        status
                    )
                    VALUES
                    (
                        :tenant_id,
                        :branch_id,
                        :employee_id,
                        :attendance_date,
                        :time_in,
                        'PRESENT'
                    )
                ");

                $insert->execute([
                    'tenant_id' => $tenantId,
                    'branch_id' => $branchId,
                    'employee_id' => $employeeId,
                    'attendance_date' => $attendanceDate,
                    'time_in' => $timestamp,
                ]);

                $result = [
                    'id' => $this->db->lastInsertId(),
                    'action' => 'TIME_IN',
                    'attendance_date' => $attendanceDate,
                    'time_in' => $timestamp,
                    'time_out' => null,
                ];

            } else {

                $update = $this->db->prepare("
                    UPDATE hris_attendance_logs
                    SET time_out = :time_out
                    WHERE id = :id
                    AND tenant_id = :tenant_id
                ");

                $update->execute([
                    'time_out' => $timestamp,
                    'id' => $row['id'],
                    'tenant_id' => $tenantId,
                ]);

                $this->computeAttendance($row['id'], $tenantId, $branchId, $employeeId, $attendanceDate, $row['time_in'], $timestamp);

                $result = [
                    'id' => $row['id'],
                    'action' => 'TIME_OUT',
                    'attendance_date' => $attendanceDate,
                    'time_in' => $row['time_in'],
                    'time_out' => $timestamp,
                ];
            }

            $this->db->commit();

            return $result;

        } catch (\Throwable $th) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $th;
        }
    }

    // public function getEmployeeDtr(int $tenantId, int $employeeId, string $dateFrom, string $dateTo): array
    // {

    //     $stmt = $this->db->prepare("
    //         SELECT
    //             id,
    //             attendance_date,
    //             time_in,
    //             time_out,
    //             break_minutes,
    //             late_minutes,
    //             undertime_minutes,
    //             overtime_minutes,
    //             computed_expected_hours,
    //             computed_actual_hours,
    //             status
    //         FROM hris_attendance_logs
    //         WHERE tenant_id = :tenant_id
    //         AND employee_id = :employee_id
    //         AND attendance_date BETWEEN :date_from AND :date_to
    //         ORDER BY attendance_date ASC
    //     ");

    //     $stmt->execute([
    //         'tenant_id' => $tenantId,
    //         'employee_id' => $employeeId,
    //         'date_from' => $dateFrom,
    //         'date_to' => $dateTo
    //     ]);

    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }

    public function getEmployeeDtr(int $tenantId, int $employeeId, string $dateFrom, string $dateTo): array
    {

        $stmt = $this->db->prepare("
            SELECT
                id,
                attendance_date,
                time_in,
                time_out,
                break_minutes,
                late_minutes,
                undertime_minutes,
                overtime_minutes,
                computed_expected_hours,
                computed_actual_hours,
                status
            FROM hris_attendance_logs
            WHERE tenant_id = :tenant_id
            AND employee_id = :employee_id
            AND attendance_date BETWEEN :date_from AND :date_to
            ORDER BY attendance_date ASC
        ");

        $stmt->execute([
            'tenant_id' => $tenantId,
            'employee_id' => $employeeId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ]);

        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $logsByDate = [];

        foreach ($logs as $log) {
            $logsByDate[$log['attendance_date']] = $log;
        }

        $result = [];

        $current = new DateTimeImmutable($dateFrom);
        $end = new DateTimeImmutable($dateTo);

        while ($current <= $end) {

            $date = $current->format('Y-m-d');

            if (isset($logsByDate[$date])) {

                $result[] = $logsByDate[$date];

            } else {

                $result[] = [
                    'id' => null,
                    'attendance_date' => $date,
                    'time_in' => null,
                    'time_out' => null,
                    'break_minutes' => 0,
                    'late_minutes' => 0,
                    'undertime_minutes' => 0,
                    'overtime_minutes' => 0,
                    'computed_expected_hours' => null,
                    'computed_actual_hours' => null,
                    'status' => null
                ];
            }

            $current = $current->modify('+1 day');
        }

        return $result;
    }

    public function getEmployeeDtrSummary(int $tenantId, int $employeeId, string $dateFrom, string $dateTo): array
    {

        if ($employeeId <= 0) {
            return [];
        }

        $stmt = $this->db->prepare("
                SELECT
                    COUNT(*) AS attendance_days,

                    SUM(
                        CASE
                            WHEN time_in IS NOT NULL
                            THEN 1
                            ELSE 0
                        END
                    ) AS days_present,

                    COALESCE(SUM(late_minutes), 0) AS total_late_minutes,
                    COALESCE(SUM(undertime_minutes), 0) AS total_undertime_minutes,
                    COALESCE(SUM(overtime_minutes), 0) AS total_overtime_minutes,
                    COALESCE(SUM(computed_actual_hours), 0) AS total_actual_hours

                FROM hris_attendance_logs

                WHERE tenant_id = :tenant_id
                AND employee_id = :employee_id
                AND attendance_date BETWEEN :date_from AND :date_to
            ");

        $stmt->execute([
            'tenant_id' => $tenantId,
            'employee_id' => $employeeId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function getEmployeesForAttendance(int $tenantId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                id,
                employee_no,
                firstname,
                lastname
            FROM hris_employees
            WHERE tenant_id = :tenant_id
            AND status = 'ACTIVE'
            ORDER BY lastname, firstname
        ");

        $stmt->execute([
            'tenant_id' => $tenantId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    private function findEmployeeByNumber(int $tenantId, int $branchId, string $employeeNo): ?array
    {
        $stmt = $this->db->prepare("
        SELECT
            id,
            employee_no,
            branch_id
        FROM hris_employees
        WHERE tenant_id = :tenant_id
          AND employee_no = :employee_no
          AND status = 'ACTIVE'
        LIMIT 1
    ");

        $stmt->execute([
            'tenant_id' => $tenantId,
            'employee_no' => $employeeNo
        ]);

        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

        return $employee ?: null;
    }
    public function importBiometricLogs(
        int $tenantId,
        int $branchId,
        array $rows,
        string $sourceFile = ''
    ): array {
        $inserted = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            if (!empty($row['errors'])) {
                $skipped++;
                continue;
            }

            $employeeNo = trim((string) ($row['employee_no'] ?? ''));
            $logDateTime = trim((string) ($row['log_datetime'] ?? ''));
            $eventType = strtoupper(trim((string) ($row['event_type'] ?? '')));

            if (
                $employeeNo === '' ||
                $logDateTime === '' ||
                !in_array($eventType, ['IN', 'OUT'], true)
            ) {
                $skipped++;
                continue;
            }

            $employee = $this->findEmployeeByNumber(
                $tenantId,
                $branchId,
                $employeeNo
            );

            if (!$employee) {
                $skipped++;
                continue;
            }

            $employeeId = (int) $employee['id'];
            $attendanceDate = substr($logDateTime, 0, 10);
            $logTime = substr($logDateTime, 11, 8);

            $result = $this->saveBiometricAttendance(
                $tenantId,
                $branchId,
                $employeeId,
                $attendanceDate,
                $logTime,
                $eventType
            );

            if ($result === 'inserted') {
                $inserted++;
            } elseif ($result === 'updated') {
                $updated++;
            } else {
                $skipped++;
            }
        }

        return [
            'inserted' => $inserted,
            'updated' => $updated,
            'skipped' => $skipped,
            'source_file' => $sourceFile
        ];
    }

    private function saveBiometricAttendance(
        int $tenantId,
        int $branchId,
        int $employeeId,
        string $attendanceDate,
        string $logTime,
        string $eventType
    ): string {
        $this->db->beginTransaction();

        try {
            $selectSql = "
            SELECT id, time_in, time_out
            FROM hris_attendance_logs
            WHERE tenant_id = :tenant_id
              AND branch_id = :branch_id
              AND employee_id = :employee_id
              AND attendance_date = :attendance_date
            LIMIT 1
            FOR UPDATE
        ";

            $selectStmt = $this->db->prepare($selectSql);

            $selectStmt->execute([
                ':tenant_id' => $tenantId,
                ':branch_id' => $branchId,
                ':employee_id' => $employeeId,
                ':attendance_date' => $attendanceDate
            ]);

            $attendance = $selectStmt->fetch(PDO::FETCH_ASSOC);

            if (!$attendance) {
                $insertSql = "
                INSERT INTO hris_attendance_logs (
                    tenant_id,
                    branch_id,
                    employee_id,
                    attendance_date,
                    time_in,
                    time_out,
                    break_minutes,
                    late_minutes,
                    undertime_minutes,
                    overtime_minutes,
                    computed_expected_hours,
                    computed_actual_hours,
                    status,
                    created_at
                ) VALUES (
                    :tenant_id,
                    :branch_id,
                    :employee_id,
                    :attendance_date,
                    :time_in,
                    :time_out,
                    0,
                    0,
                    0,
                    0,
                    0,
                    0,
                    'PRESENT',
                    NOW()
                )
            ";

                $insertStmt = $this->db->prepare($insertSql);

                $insertStmt->execute([
                    ':tenant_id' => $tenantId,
                    ':branch_id' => $branchId,
                    ':employee_id' => $employeeId,
                    ':attendance_date' => $attendanceDate,
                    ':time_in' => $eventType === 'IN' ? $logTime : null,
                    ':time_out' => $eventType === 'OUT' ? $logTime : null
                ]);

                $this->db->commit();

                return 'inserted';
            }

            if ($eventType === 'IN') {
                if (
                    !empty($attendance['time_in']) &&
                    $logTime >= $attendance['time_in']
                ) {
                    $this->db->commit();
                    return 'skipped';
                }

                $updateSql = "
                UPDATE hris_attendance_logs
                SET time_in = :log_time
                WHERE id = :id
            ";
            } else {
                if (
                    !empty($attendance['time_out']) &&
                    $logTime <= $attendance['time_out']
                ) {
                    $this->db->commit();
                    return 'skipped';
                }

                $updateSql = "
                UPDATE hris_attendance_logs
                SET time_out = :log_time
                WHERE id = :id
            ";
            }

            $updateStmt = $this->db->prepare($updateSql);

            $updateStmt->execute([
                ':log_time' => $logTime,
                ':id' => (int) $attendance['id']
            ]);

            $this->db->commit();

            return 'updated';
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

}