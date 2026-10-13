<?php
class PayrollRunModel extends Model
{
    public function __construct()
    {
        parent::__construct('default');
    }

    private function getRunEmployees(int $runId): array
    {
        $sql = "
        SELECT
            id,
            employee_id
        FROM payroll_run_employees
        WHERE run_id = :run_id
        ORDER BY id ASC
    ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'run_id' => $runId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createPayrollRunWithEmployees(array $runData): array
    {
        try {
            $this->db->beginTransaction();

            $runId = $this->createPayrollRun($runData);

            if ($runId <= 0) {
                throw new RuntimeException('Failed to create the payroll run.');
            }

            $loadedCount = $this->loadEligibleEmployees($runId, $runData['tenant_id'], $runData['cutoff_date_from'], $runData['cutoff_date_to']);

            if ($loadedCount <= 0) {
                throw new RuntimeException('No eligible employees were found for this payroll run.');
            }

            $runEmployees = $this->getRunEmployees($runId);

            $loadedDayCount = 0;

            foreach ($runEmployees as $runEmployee) {
                $loadedDayCount += $this->insertEmployeeDays(
                    $runEmployee['id'],
                    $runData['tenant_id'],
                    $runData['cutoff_date_from'],
                    $runData['cutoff_date_to']
                );
            }

            $this->db->commit();

            return [
                'run_id' => $runId,
                'loaded_count' => $loadedCount,
                'loaded_day_count' => $loadedDayCount,
            ];

        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    public function createPayrollRun(array $data)
    {
        $stmt = $this->db->prepare("
                INSERT INTO payroll_runs (
                    tenant_id, 
                    period_no, 
                    period_id,
                    run_code, 
                    payroll_name,
                    cutoff_date_from, 
                    cutoff_date_to, 
                    pay_date,
                    status, 
                    created_by
                    )
                    VALUES 
                    (
                    :tenant_id, 
                    :period_no, 
                    :period_id,
                    :run_code, 
                    :payroll_name,
                    :cutoff_date_from, 
                    :cutoff_date_to, 
                    :pay_date,
                    'DRAFT', 
                    :created_by
                    )");

        $stmt->execute([
            'tenant_id' => $data['tenant_id'],
            'period_no' => $data['period_no'],
            'period_id' => $data['period_id'],
            'run_code' => $data['run_code'],
            'payroll_name' => $data['payroll_name'],
            'cutoff_date_from' => $data['cutoff_date_from'],
            'cutoff_date_to' => $data['cutoff_date_to'],
            'pay_date' => $data['pay_date'],
            'created_by' => $data['created_by'],
        ]);

        return $this->db->lastInsertId();
    }

    public function payrollRunExists(int $tenantId, string $from, string $to): bool
    {
        $stmt = $this->db->prepare("
            SELECT id FROM payroll_runs WHERE 
                tenant_id = :tenant_id AND 
                cutoff_date_from = :cutoff_date_from AND 
                cutoff_date_to = :cutoff_date_to AND 
                status <> 'CANCELLED'
                 LIMIT 1");

        $stmt->execute([
            'tenant_id' => $tenantId,
            'cutoff_date_from' => $from,
            'cutoff_date_to' => $to,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function generateNextRunSequence(int $tenantId, int $year, int $month): int
    {
        $prefix = sprintf('PAY-%04d%02d-', $year, $month);

        $stmt = $this->db->prepare("
                SELECT MAX(CAST(SUBSTRING(run_code, LENGTH(:prefix) + 1) AS UNSIGNED))
                FROM payroll_runs WHERE 
                tenant_id = :tenant_id AND 
                run_code LIKE :pattern");

        $stmt->execute([
            'prefix' => $prefix,
            'tenant_id' => $tenantId,
            'pattern' => $prefix . '%',
        ]);

        return ((int) $stmt->fetchColumn()) + 1;
    }

    public function findPayrollRunById(int $tenantId, int $runId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM payroll_runs WHERE 
                id = :id AND 
                tenant_id = :tenant_id
                LIMIT 1");

        $stmt->execute([
            'id' => $runId,
            'tenant_id' => $tenantId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getPayrollRunEmployees(int $runId): array
    {

        $stmt = $this->db->prepare("
            SELECT
                pre.*,
                e.employee_no,
                e.lastname AS last_name,
                e.firstname AS first_name,
                e.middlename AS middle_name

            FROM payroll_run_employees pre
            INNER JOIN hris_employees e
                ON e.id = pre.employee_id
            WHERE pre.run_id = :run_id
            ORDER BY
                e.lastname,
                e.firstname
            ");

        $stmt->execute([
            'run_id' => $runId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function loadEligibleEmployees(int $runId, int $tenantId, string $cutoffDateFrom, string $cutoffDateTo): int
    {

        // $sql = "

        //     INSERT INTO payroll_run_employees
        //             (
        //                 tenant_id, 
        //                 run_id, 
        //                 employee_id, 
        //                 shift_id, 
        //                 shift_name, 
        //                 scheduled_time_in, 
        //                 scheduled_time_out, 
        //                 scheduled_break_minutes, 
        //                 allow_overtime, 
        //                 scheduled_work_minutes, 
        //                 salary_type, 
        //                 payroll_frequency, 
        //                 salary_rate, 
        //                 created_at 
        //             )

        //                 select 
        //                 :tenant_id,
        //                 :run_id,
        //                 a.id,
        //                 es.shift_id,
        //                 s.name,
        //                 s.start_time,
        //                 s.end_time,
        //                 s.break_minutes,
        //                 s.allow_overtime,
        //                 s.required_hours,

        //                 b.salary_type, 
        //                 b.payroll_frequency, 
        //                 b.salary_rate,
        //                         NOW()

        //                 from hris_employees a 
        //                 left join payroll_employee_compensations b 
        //                 on b.id = (
        //                     SELECT comp.id 
        //                     from payroll_employee_compensations comp 
        //                     WHERE 
        //                         comp.employee_id = a.id 
        //                         and comp.tenant_id = b.tenant_id
        //                         and comp.effective_from <= :cutoff_date_to
        //                         and (
        //                                 comp.effective_to IS NULL
        //                                 OR comp.effective_to >= :cutoff_date_from
        //                         )

        //                     order by 
        //                     comp.id desc limit 1
        //                 )

        //                 LEFT JOIN hris_employee_shift_schedules es
        //                     ON es.id = (
        //                         SELECT es2.id
        //                         FROM hris_employee_shift_schedules es2
        //                         WHERE es2.tenant_id = a.tenant_id
        //                         AND es2.employee_id = a.id

        //                         ORDER BY

        //                             es2.id DESC
        //                         LIMIT 1
        //                     )

        //                     LEFT JOIN hris_shifts s
        //                     ON s.id = es.shift_id
        //                 AND s.tenant_id = a.tenant_id

        //         WHERE a.status = 'ACTIVE' and  a.tenant_id = :tenant_id

        // ";

        $sql = "
                INSERT INTO payroll_run_employees
                (
                    tenant_id,
                    run_id,
                    employee_id,
                    shift_id,
                    shift_name,
                    scheduled_time_in,
                    scheduled_time_out,
                    scheduled_break_minutes,
                    allow_overtime,
                    scheduled_work_minutes,
                    salary_type,
                    payroll_frequency,
                    salary_rate,
                    created_at
                )

                SELECT
                
                    :tenant_id,
                    :run_id,
                    a.id,

                    COALESCE(bs.shift_id, es.shift_id ) AS shift_id,
                    COALESCE(s.name, 'No Shift'),
                    s.start_time,
                    s.end_time,
                    COALESCE(s.break_minutes, 0),
                    COALESCE(s.allow_overtime, 0),
                    COALESCE(s.required_hours, 0),

                    COALESCE(b.salary_type, 'UNSET'),
                    COALESCE(b.payroll_frequency, 'UNSET'),
                    COALESCE(b.salary_rate, 0),

                    NOW()

                FROM hris_employees a

                LEFT JOIN payroll_employee_compensations b
                    ON b.id = (
                        SELECT comp.id
                        FROM payroll_employee_compensations comp
                        WHERE comp.employee_id = a.id
                        AND comp.tenant_id = a.tenant_id
                        AND comp.effective_from <= :cutoff_date_to
                        AND (
                                comp.effective_to IS NULL
                                OR comp.effective_to >= :cutoff_date_from
                            )
                        ORDER BY
                            comp.effective_from DESC,
                            comp.id DESC
                        LIMIT 1
                    )

                LEFT JOIN hris_employee_shift_schedules es
                    ON es.id = (
                        SELECT es2.id
                        FROM hris_employee_shift_schedules es2
                        WHERE es2.tenant_id = a.tenant_id
                        AND es2.employee_id = a.id
                        AND es2.effective_from <= :cutoff_date_to
                        AND (
                                es2.effective_to IS NULL
                                OR es2.effective_to >= :cutoff_date_from
                            )
                        ORDER BY
                            es2.effective_from DESC,
                            es2.id DESC
                        LIMIT 1
                    )

                LEFT JOIN hris_branch_shifts bs
                    ON bs.id = (
                        SELECT bs2.id
                        FROM hris_branch_shifts bs2
                        WHERE bs2.tenant_id = a.tenant_id
                        AND bs2.branch_id = a.branch_id
                        AND bs2.is_active = 1
                        ORDER BY bs2.id ASC
                        LIMIT 1
                    )

                LEFT JOIN hris_shifts s
                    ON s.id = es.shift_id
                AND s.tenant_id = a.tenant_id

                WHERE a.status = 'ACTIVE'
                AND a.tenant_id = :tenant_id
            ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([

            'run_id' => $runId,
            'tenant_id' => $tenantId,
            'cutoff_date_from' => $cutoffDateFrom,
            'cutoff_date_to' => $cutoffDateTo,
        ]);

        return $stmt->rowCount();
    }




    private function findPayrollRunForProcessing(int $runId, int $tenantId): ?array
    {
        $sql = "
        SELECT
          *
        FROM payroll_runs
        WHERE id = :run_id
          AND tenant_id = :tenant_id
        LIMIT 1
        FOR UPDATE
    ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'run_id' => $runId,
            'tenant_id' => $tenantId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    private function getPayrollRunEmployeesForProcessing(int $runId): array
    {
        $sql = "
            SELECT
                *
            FROM payroll_run_employees pre
            WHERE pre.run_id = :run_id
            ORDER BY pre.id ASC
            FOR UPDATE
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'run_id' => $runId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function deleteGeneratedPayrollItems(int $runEmployeeId)
    {
        $sql = "
        DELETE FROM payroll_run_items
        WHERE run_employee_id = :run_employee_id
          AND (
                source_type IS NULL
                OR source_type <> 'MANUAL'
              )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'run_employee_id' => $runEmployeeId,
        ]);
    }


    private function getPayableDays(int $runEmployeeId): float
    {
        $sql = "
            SELECT
                COALESCE(SUM(day_credit), 0) AS payable_days
            FROM payroll_run_employee_days
            WHERE run_employee_id = :run_employee_id
            AND is_paid_day = 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'run_employee_id' => $runEmployeeId,
        ]);

        return (float) $stmt->fetchColumn();
    }
    private function calculateBasicPay($salaryType, $payrollFrequency, $salaryRate, $periodNo, $daysPresent = 0)
    {
        $salaryType = strtoupper(trim($salaryType));
        $payrollFrequency = strtoupper(trim($payrollFrequency));

        if ($salaryRate < 0) {
            throw new RuntimeException('Employee salary rate cannot be negative.');
        }

        if ($salaryType === 'DAILY') {
            return round($salaryRate * $daysPresent, 2);
        }

        if ($salaryType === 'MONTHLY') {

            if ($payrollFrequency === 'SEMI_MONTHLY') {
                return round($salaryRate / 2, 2);
            }

            if ($payrollFrequency === 'MONTHLY') {
                return round($salaryRate, 2);
            }
        }

        throw new RuntimeException(
            "Unsupported salary type or payroll frequency: {$salaryType} / {$payrollFrequency}"
        );
    }

    private function insertPayrollRunItem(array $data): int
    {
        $sql = "
        INSERT INTO payroll_run_items
        (
            run_employee_id,
            item_type,
            item_code,
            description,
            amount,
            source_type,
            created_at
        )
        VALUES
        (
            :run_employee_id,
            :item_type,
            :item_code,
            :description,
            :amount,
            :source_type,
            NOW()
        )
    ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'run_employee_id' =>
                $data['run_employee_id'],
            'item_type' => $data['item_type'],
            'item_code' => $data['item_code'],
            'description' => $data['description'] ?? null,
            'amount' => $data['amount'],
            'source_type' => $data['source_type'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function loadEmployees($runId, $tenantId, $cutoffDateFrom, $cutoffDateTo)
    {


        $sql = "
                INSERT INTO payroll_run_employees
                (
                    tenant_id,
                    run_id,
                    employee_id,
                    salary_type,
                    payroll_frequency,
                    salary_rate,
                    created_at
                )

                SELECT
                
                    :tenant_id,
                    :run_id,
                    a.id,
                    COALESCE(b.salary_type, 'UNSET'),
                    COALESCE(b.payroll_frequency, 'UNSET'),
                    COALESCE(b.salary_rate, 0),
                    NOW()

                FROM hris_employees a

                LEFT JOIN payroll_employee_compensations b
                    ON b.id = (
                        SELECT comp.id
                        FROM payroll_employee_compensations comp
                        WHERE comp.employee_id = a.id
                        AND comp.tenant_id = a.tenant_id
                        AND comp.effective_from <= :cutoff_date_to
                        AND (
                                comp.effective_to IS NULL
                                OR comp.effective_to >= :cutoff_date_from
                            )
                        ORDER BY
                            comp.effective_from DESC,
                            comp.id DESC
                        LIMIT 1
                    )

                WHERE a.status = 'ACTIVE'
                AND a.tenant_id = :tenant_id
                
                AND NOT EXISTS (
                    SELECT 1
                    FROM payroll_run_employees pre
                    WHERE pre.run_id = :run_id
                        AND pre.tenant_id = :tenant_id
                        AND pre.employee_id = a.id
                )
            ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([

            'run_id' => $runId,
            'tenant_id' => $tenantId,
            'cutoff_date_from' => $cutoffDateFrom,
            'cutoff_date_to' => $cutoffDateTo,
        ]);


        $runEmployees = $this->getRunEmployees($runId);

        $loadedDayCount = 0;

        foreach ($runEmployees as $runEmployee) {
            $loadedDayCount += $this->insertEmployeeDays(
                $runEmployee['id'],
                $tenantId,
                $cutoffDateFrom,
                $cutoffDateTo
            );
        }

        return $stmt->rowCount();
    }
    private function insertEmployeeDays(int $runEmployeeId, int $tenantId, string $cutoffDateFrom, string $cutoffDateTo): int
    {
        $startDate = DateTimeImmutable::createFromFormat('!Y-m-d', $cutoffDateFrom);
        $endDate = DateTimeImmutable::createFromFormat('!Y-m-d', $cutoffDateTo);

        if (!$startDate || !$endDate) {
            throw new InvalidArgumentException(
                'Invalid payroll cutoff date.'
            );
        }

        if ($startDate > $endDate) {
            throw new InvalidArgumentException(
                'Cutoff start date cannot be later than cutoff end date.'
            );
        }

        $sql = "
            INSERT INTO payroll_run_employee_days
            (
                run_employee_id,
                attendance_date,
                day_type,
                is_holiday,
                holiday_id,
                holiday_name,
                holiday_type,
                holiday_multiplier,
                allow_work
            )
            SELECT
                :run_employee_id,
                :attendance_date,

                CASE
                    WHEN h.id IS NOT NULL THEN 'HOLIDAY'
                    ELSE 'REGULAR'
                END,

                CASE
                    WHEN h.id IS NOT NULL THEN 1
                    ELSE 0
                END,

                h.id,
                h.name,
                h.holiday_type,
                COALESCE(h.pay_multiplier, 1.00),
                COALESCE(h.allow_work, 1)

            FROM (
                SELECT 1
            ) AS base

            LEFT JOIN hris_holidays h
                ON h.tenant_id = :tenant_id
            AND h.holiday_date = :holiday_date

            ON DUPLICATE KEY UPDATE
                day_type = VALUES(day_type),
                is_holiday = VALUES(is_holiday),
                holiday_id = VALUES(holiday_id),
                holiday_name = VALUES(holiday_name),
                holiday_type = VALUES(holiday_type),
                holiday_multiplier = VALUES(holiday_multiplier),
                allow_work = VALUES(allow_work)
        ";

        $stmt = $this->db->prepare($sql);

        $affectedRows = 0;

        for (
            $date = $startDate;
            $date <= $endDate;
            $date = $date->modify('+1 day')
        ) {
            $workDate = $date->format('Y-m-d');

            $stmt->execute([
                'run_employee_id' => $runEmployeeId,
                'attendance_date' => $workDate,
                'tenant_id' => $tenantId,
                'holiday_date' => $workDate,
            ]);

            $affectedRows += $stmt->rowCount();
        }

        return $affectedRows;
    }
    private function markPayrollEmployeeError(int $runEmployeeId, string $message)
    {
        $sql = "
            UPDATE payroll_run_employees
            SET
                computation_status = 'ERROR',
                error_message = :error_message,
                computed_at = NOW()
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'id' => $runEmployeeId,
            'error_message' => mb_substr($message, 0, 1000),
        ]);
    }

    private function markPayrollRunProcessed(int $runId, int $tenantId, int $processedBy): void
    {
        $sql = "
        UPDATE payroll_runs
        SET
            status = 'PROCESSED',
            processed_by = :processed_by,
            processed_at = NOW(),
            updated_at = NOW()
        WHERE id = :run_id
          AND tenant_id = :tenant_id
          AND status = 'DRAFT'
    ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'processed_by' => $processedBy,
            'run_id' => $runId,
            'tenant_id' => $tenantId,
        ]);

        if ($stmt->rowCount() !== 1) {
            throw new RuntimeException(
                'Failed to mark the payroll run as processed.'
            );
        }
    }
    private function recalculatePayrollEmployeeTotals(int $runEmployeeId): void
    {
        $sql = "
        UPDATE payroll_run_employees pre

        LEFT JOIN (
            SELECT
                run_employee_id,

                SUM(
                    CASE
                        WHEN item_type = 'EARNING'
                         AND item_code = 'BASIC_PAY'
                        THEN amount
                        ELSE 0
                    END
                ) AS basic_pay,

                SUM(
                    CASE
                        WHEN item_type = 'EARNING'
                        THEN amount
                        ELSE 0
                    END
                ) AS gross_pay,

                SUM(
                    CASE
                        WHEN item_type = 'EARNING'
                         AND item_code <> 'BASIC_PAY'
                        THEN amount
                        ELSE 0
                    END
                ) AS additional_earnings,

                SUM(
                    CASE
                        WHEN item_type = 'DEDUCTION'
                        THEN amount
                        ELSE 0
                    END
                ) AS deductions

            FROM payroll_run_items
            WHERE run_employee_id = :item_run_employee_id
            GROUP BY run_employee_id
        ) totals
            ON totals.run_employee_id = pre.id

        SET
            pre.basic_pay =
                COALESCE(totals.basic_pay, 0),

            pre.total_earnings =
                COALESCE(totals.additional_earnings, 0),

            pre.gross_pay =
                COALESCE(totals.gross_pay, 0),

            pre.total_deductions =
                COALESCE(totals.deductions, 0),

            pre.net_pay =
                COALESCE(totals.gross_pay, 0)
                - COALESCE(totals.deductions, 0),

            pre.computation_status = 'COMPUTED',

            pre.error_message = NULL,

            pre.computed_at = NOW()

        WHERE pre.id = :run_employee_id
    ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'item_run_employee_id' => $runEmployeeId,
            'run_employee_id' => $runEmployeeId,
        ]);
    }

    private function countDaysPresent(int $runEmployeeId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM payroll_run_employee_days
            WHERE run_employee_id = :run_employee_id
            AND is_present = 1
        ");

        $stmt->execute([
            'run_employee_id' => $runEmployeeId
        ]);

        return (int) $stmt->fetchColumn();
    }

    private function countRegularDaysPresent($runEmployeeId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM payroll_run_employee_days
            WHERE run_employee_id = :run_employee_id
            AND is_present = 1
            AND is_holiday = 0
        ");

        $stmt->execute([
            'run_employee_id' => $runEmployeeId
        ]);

        return $stmt->fetchColumn();
    }

    private function updateDaysPresent($runEmployeeId, $daysPresent): void
    {
        $stmt = $this->db->prepare("
            UPDATE payroll_run_employees
            SET days_present = :days_present
            WHERE id = :id
        ");

        $stmt->execute([
            'days_present' => $daysPresent,
            'id' => $runEmployeeId
        ]);
    }

    private function processHolidayPay($runEmployeeId, $salaryType, $salaryRate): void
    {

        $salaryType = strtoupper(trim($salaryType));

        $sql = "
            SELECT
                id,
                attendance_date,
                holiday_id,
                holiday_name,
                holiday_type,
                holiday_multiplier,
                allow_work,
                is_present
            FROM payroll_run_employee_days
            WHERE run_employee_id = :run_employee_id
            AND is_holiday = 1
            ORDER BY attendance_date
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'run_employee_id' => $runEmployeeId
        ]);

        $holidays = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($holidays as $holiday) {

            $multiplier = $holiday['holiday_multiplier'];
            $isPresent = $holiday['is_present'] === 1;
            $allowWork = $holiday['allow_work'] === 1;

            /*
             * DAILY-paid employees
             */
            if ($salaryType === 'DAILY') {

                /*
                 * No attendance.
                 *
                 * We'll handle paid non-worked holidays separately,
                 * because entitlement depends on the holiday configuration/rules.
                 */
                if (!$isPresent) {
                    continue;
                }

                /*
                 * Employee worked on the holiday.
                 */
                if ($isPresent && $allowWork) {

                    $holidayPay = round($salaryRate * $multiplier, 2);

                    $this->insertPayrollRunItem([
                        'run_employee_id' => $runEmployeeId,
                        'item_type' => 'EARNING',
                        'item_code' => 'HOLIDAY_PAY',
                        'description' => $holiday['holiday_name'] . ' - Holiday Pay',
                        'amount' => $holidayPay,
                        'source_type' => 'SYSTEM',
                    ]);
                }
            }
        }
    }

    private function processOvertimeItems($runEmployeeId, $employee)
    {

        $salaryRate = (float) $employee['salary_rate'];
        $salaryType = strtoupper(trim($employee['salary_type']));

        if ($salaryRate <= 0) {
            return;
        }

        /*
         * Determine daily rate.
         *
         * DAILY:
         * salary_rate already represents daily rate.
         *
         * MONTHLY:
         * We'll eventually use your configured divisor
         * instead of hard-coding one here.
         */
        if ($salaryType === 'DAILY') {

            $dailyRate = $salaryRate;

        } elseif ($salaryType === 'MONTHLY') {

            // Temporary divisor.
            // Better to move this to payroll settings later.
            $dailyRate = $salaryRate / 26;

        } else {
            return;
        }

        $hoursPerDay = 8;

        $hourlyRate = $dailyRate / $hoursPerDay;

        $sql = "
                SELECT
                    id,
                    attendance_date,
                    day_type,
                    is_holiday,
                    holiday_name,
                    holiday_type,
                    holiday_multiplier,
                    overtime_minutes
                FROM payroll_run_employee_days
                WHERE run_employee_id = :run_employee_id
                AND overtime_minutes > 0
                ORDER BY attendance_date
            ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'run_employee_id' => $runEmployeeId
        ]);

        $days = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($days as $day) {

            $overtimeMinutes = (int) $day['overtime_minutes'];

            if ($overtimeMinutes <= 0) {
                continue;
            }

            $overtimeHours = $overtimeMinutes / 60;

            /*
             * Regular day OT
             * 125% of hourly rate.
             */
            // $otMultiplier = 1.25;

            // $amount = round($hourlyRate * $overtimeHours * $otMultiplier, 2);

            $amount = $overtimeHours * 100;

            if ($amount <= 0) {
                continue;
            }

            $this->insertPayrollRunItem([
                'run_employee_id' => $runEmployeeId,
                'item_type' => 'EARNING',
                'item_code' => 'OVERTIME',
                'description' => sprintf(
                    'Overtime - %s (%.2f hrs)',
                    $day['attendance_date'],
                    $overtimeHours
                ),
                'amount' => $amount,
                'source_type' => 'ATTENDANCE',
            ]);
        }
    }

    private function calculateAllowanceAmount($allowance, $employee, $run)
    {

        $amount = $allowance['amount'] ?? $allowance['default_value'] ?? 0;

        if ($amount <= 0) {
            return 0;
        }

        $frequency = strtoupper(trim($allowance['frequency'] ?? 'DAILY'));

        switch ($frequency) {

            case 'DAILY':

                $daysPresent = $this->countDaysPresent($employee['id']);

                return round($amount * $daysPresent, 2);

            case 'PER_PAYROLL':
                return round($amount, 2);

            case 'MONTHLY':

                if (strtoupper($employee['payroll_frequency']) === 'SEMI_MONTHLY') {
                    return round($amount / 2, 2);
                }

                return round($amount, 2);

            default:

                return round($amount, 2);
        }
    }


    private function processAllowanceItems($runEmployeeId, $employee, $run)
    {

        $employeeId = $employee['employee_id'];
        $tenantId = $employee['tenant_id'];

        $cutoffDateFrom = $run['cutoff_date_from'];
        $cutoffDateTo = $run['cutoff_date_to'];

        $sql = "
            SELECT
                ea.id AS employee_allowance_id,
                ea.allowance_type_id,
                ea.amount,
                ea.is_recurring,

                a.code,
                a.name,
                a.taxable,
                a.computation_type,
                a.default_value,
                'DAILY' as frequency

            FROM payroll_employee_allowances ea

            INNER JOIN payroll_allowance_types a
                ON a.id = ea.allowance_type_id
                AND a.tenant_id = ea.tenant_id

            WHERE ea.tenant_id = :tenant_id
            AND ea.employee_id = :employee_id

            AND ea.effective_from <= :cutoff_date_to

            AND (
                    ea.effective_to IS NULL
                    OR ea.effective_to >= :cutoff_date_from
                )

         
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'tenant_id' => $tenantId,
            'employee_id' => $employeeId,
            'cutoff_date_from' => $cutoffDateFrom,
            'cutoff_date_to' => $cutoffDateTo,
        ]);

        $allowances = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($allowances as $allowance) {

            $amount = $this->calculateAllowanceAmount(
                $allowance,
                $employee,
                $run
            );

            if ($amount <= 0) {
                continue;
            }

            $this->insertPayrollRunItem([
                'run_employee_id' => $runEmployeeId,
                'item_type' => 'EARNING',
                'item_code' => $allowance['code'],
                'description' => $allowance['name'],
                'amount' => $amount,
                'source_type' => 'ALLOWANCE',
            ]);
        }
    }

    private function processLateItems($runEmployeeId, $employee)
    {

        $salaryRate = $employee['salary_rate'];
        $salaryType = strtoupper(trim($employee['salary_type']));

        if ($salaryRate <= 0) {
            return;
        }

        if ($salaryType === 'DAILY') {

            $dailyRate = $salaryRate;

        } elseif ($salaryType === 'MONTHLY') {

            // Better to make this configurable later.
            $dailyRate = $salaryRate / 26;

        } else {
            return;
        }

        $sql = "
            SELECT
                id,
                attendance_date,
                late_minutes,
                scheduled_time_in,
                scheduled_time_out,
                scheduled_break_minutes

            FROM payroll_run_employee_days

            WHERE run_employee_id = :run_employee_id
            AND late_minutes > 0

            ORDER BY attendance_date
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'run_employee_id' => $runEmployeeId
        ]);

        $days = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($days as $day) {

            $lateMinutes = $day['late_minutes'];

            if ($lateMinutes <= 0) {
                continue;
            }

            if (empty($day['scheduled_time_in']) || empty($day['scheduled_time_out'])) {
                continue;
            }

            $scheduledIn = new DateTimeImmutable($day['attendance_date'] . ' ' . $day['scheduled_time_in']);

            $scheduledOut = new DateTimeImmutable($day['attendance_date'] . ' ' . $day['scheduled_time_out']);

            /*
             * Handle overnight shifts.
             * Example: 10:00 PM - 6:00 AM.
             */
            if ($scheduledOut <= $scheduledIn) {
                $scheduledOut = $scheduledOut->modify('+1 day');
            }

            $scheduledMinutes = (int) floor(($scheduledOut->getTimestamp() - $scheduledIn->getTimestamp()) / 60);

            $breakMinutes = (int) ($day['scheduled_break_minutes'] ?? 0);

            $scheduledMinutes -= $breakMinutes;

            if ($scheduledMinutes <= 0) {
                continue;
            }

            $lateMultiplier = 2;

            $minuteRate = $dailyRate / $scheduledMinutes;

            $amount = round($minuteRate * $lateMinutes * $lateMultiplier, 2);

            if ($amount <= 0) {
                continue;
            }

            $this->insertPayrollRunItem([
                'run_employee_id' => $runEmployeeId,
                'item_type' => 'DEDUCTION',
                'item_code' => 'LATE',
                'description' => sprintf('Late - %s (%d min)', $day['attendance_date'], $lateMinutes),
                'amount' => $amount,
                'source_type' => 'ATTENDANCE',
            ]);
        }
    }

    private function processCustomIncentives($runEmployeeId, $employee)
    {

        $salaryRate = $employee['salary_rate'];
        $salaryType = strtoupper(trim($employee['salary_type']));

        if ($salaryRate <= 0) {
            return;
        }

        if ($salaryType === 'DAILY') {

            $dailyRate = $salaryRate;

        } elseif ($salaryType === 'MONTHLY') {

            // Better to make this configurable later.
            $dailyRate = $salaryRate / 26;

        } else {
            return;
        }

        $sql = "
            SELECT
                id,
                attendance_date,
                late_minutes,
                scheduled_time_in,
                scheduled_time_out,
                scheduled_break_minutes

            FROM payroll_run_employee_days

            WHERE run_employee_id = :run_employee_id
            AND late_minutes > 0

            ORDER BY attendance_date
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'run_employee_id' => $runEmployeeId
        ]);

        $days = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($days as $day) {

            $lateMinutes = $day['late_minutes'];

            if ($lateMinutes <= 0) {
                continue;
            }

            if (empty($day['scheduled_time_in']) || empty($day['scheduled_time_out'])) {
                continue;
            }

            $scheduledIn = new DateTimeImmutable($day['attendance_date'] . ' ' . $day['scheduled_time_in']);

            $scheduledOut = new DateTimeImmutable($day['attendance_date'] . ' ' . $day['scheduled_time_out']);

            /*
             * Handle overnight shifts.
             * Example: 10:00 PM - 6:00 AM.
             */
            if ($scheduledOut <= $scheduledIn) {
                $scheduledOut = $scheduledOut->modify('+1 day');
            }

            $scheduledMinutes = (int) floor(($scheduledOut->getTimestamp() - $scheduledIn->getTimestamp()) / 60);

            $breakMinutes = (int) ($day['scheduled_break_minutes'] ?? 0);

            $scheduledMinutes -= $breakMinutes;

            if ($scheduledMinutes <= 0) {
                continue;
            }

            $lateMultiplier = 2;

            $minuteRate = $dailyRate / $scheduledMinutes;

            $amount = round($minuteRate * $lateMinutes * $lateMultiplier, 2);

            if ($amount <= 0) {
                continue;
            }

            $this->insertPayrollRunItem([
                'run_employee_id' => $runEmployeeId,
                'item_type' => 'DEDUCTION',
                'item_code' => 'LATE',
                'description' => sprintf('Late - %s (%d min)', $day['attendance_date'], $lateMinutes),
                'amount' => $amount,
                'source_type' => 'ATTENDANCE',
            ]);
        }
    }
    private function processPayrollRunEmployee($tenantId, $employee, $run, $cutoffDateFrom, $cutoffDateTo)
    {

        $runEmployeeId = $employee['id'];

        $employee_id = $employee['employee_id'];



        $this->processEmployeeAttendance($runEmployeeId, $employee_id, $tenantId, $cutoffDateFrom, $cutoffDateTo);

        // $daysPresent = $this->countDaysPresent($runEmployeeId);
        $daysPresent = $this->countRegularDaysPresent($runEmployeeId);

        $this->updateDaysPresent($runEmployeeId, $daysPresent);

        $this->deleteGeneratedPayrollItems($runEmployeeId);

        $basicPay = $this->calculateBasicPay(
            $employee['salary_type'],
            $employee['payroll_frequency'],
            $employee['salary_rate'],
            $run['period_no'],
            $daysPresent
        );

        $this->insertPayrollRunItem([
            'run_employee_id' => $runEmployeeId,
            'item_type' => 'EARNING',
            'item_code' => 'BASIC_PAY',
            'description' => 'Basic Pay',
            'amount' => $basicPay,
            'source_type' => 'SYSTEM',
        ]);


        $this->processHolidayPay(
            $runEmployeeId,
            $employee['salary_type'],
            $employee['salary_rate']
        );

        $this->processOvertimeItems(
            $runEmployeeId,
            $employee
        );

        $this->processAllowanceItems(
            $runEmployeeId,
            $employee,
            $run
        );

        $this->processLateItems(
            $runEmployeeId,
            $employee
        );

        /*
         * Later, these can be added here:
         *
         * processAttendanceItems()
         * processOvertimeItems()
         * processAllowanceItems()
         * processIncentiveItems()
         * processLoanItems()
         * processGovernmentContributions()
         */

        $this->recalculatePayrollEmployeeTotals($runEmployeeId);
    }

    public function processEmployeeAttendance($runEmployeeId, $employeeId, $tenantId, $cutoffDateFrom, $cutoffDateTo)
    {

        $sql = "
            UPDATE payroll_run_employee_days d

            LEFT JOIN hris_attendance_logs a
                ON a.tenant_id = :tenant_id
                AND a.employee_id = :employee_id
                AND a.attendance_date = d.attendance_date

            LEFT JOIN hris_branch_shifts bs
                ON bs.tenant_id = a.tenant_id
                AND bs.branch_id = a.branch_id

            LEFT JOIN hris_shifts s
                ON s.id = bs.shift_id
                AND s.tenant_id = a.tenant_id

            SET
                d.attendance_log_id = a.id,
                d.time_in = a.time_in,
                d.time_out = a.time_out,

                d.shift_id = s.id,
                d.scheduled_time_in = s.start_time,
                d.scheduled_time_out = s.end_time,
                d.scheduled_break_minutes = COALESCE(s.break_minutes, 0),

                d.is_present = CASE
                    WHEN a.id IS NOT NULL
                        AND a.time_in IS NOT NULL
                    THEN 1
                    ELSE 0
                END,
           
            d.late_minutes = COALESCE(a.late_minutes, 0),
            d.undertime_minutes = COALESCE(a.undertime_minutes, 0),
            d.overtime_minutes = COALESCE(a.overtime_minutes, 0)

            WHERE d.run_employee_id = :run_employee_id
            AND d.attendance_date BETWEEN :cutoff_date_from AND :cutoff_date_to
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'tenant_id' => $tenantId,
            'employee_id' => $employeeId,
            'run_employee_id' => $runEmployeeId,
            'cutoff_date_from' => $cutoffDateFrom,
            'cutoff_date_to' => $cutoffDateTo,
        ]);

        return $stmt->rowCount();
    }

    public function processPayrollRun(int $runId, int $tenantId, int $processedBy): array
    {
        try {




            $this->db->beginTransaction();

            $run = $this->findPayrollRunForProcessing($runId, $tenantId);

            if (!$run) {
                throw new RuntimeException(
                    'Payroll run was not found.'
                );
            }

            if ($run['status'] !== 'DRAFT') {
                throw new RuntimeException(
                    'Only draft payroll runs can be processed.'
                );
            }

            $cutoffDateFrom = $run['cutoff_date_from'];
            $cutoffDateTo = $run['cutoff_date_to'];

            $employees = $this->getPayrollRunEmployeesForProcessing($runId);



            if (empty($employees)) {
                throw new RuntimeException('No employees were found in this payroll run.');
            }

            $processedCount = 0;
            $errorCount = 0;

            foreach ($employees as $employee) {
                try {

                    $this->processPayrollRunEmployee($tenantId, $employee, $run, $cutoffDateFrom, $cutoffDateTo);

                    $processedCount++;

                } catch (Throwable $employeeError) {

                    $errorCount++;

                    $this->markPayrollEmployeeError((int) $employee['id'], $employeeError->getMessage());

                }
            }


            // if ($errorCount > 0) {
            //     throw new RuntimeException(
            //         sprintf(
            //             '%d employee payroll record(s) failed to process.',
            //             $errorCount
            //         )
            //     );
            // }

            // $this->markPayrollRunProcessed($runId, $tenantId, $processedBy);

            $this->db->commit();

            return [
                'processed_count' => $processedCount,
                'error_count' => $errorCount,
            ];
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    public function getPayrollRuns(
        int $tenantId,
        int $limit,
        int $offset,
        string $status = '',
        string $payrollMonth = ''
    ): array {

        $where = [
            'pr.tenant_id = :tenant_id'
        ];

        $params = [
            'tenant_id' => $tenantId
        ];

        if ($status !== '') {
            $where[] = 'pr.status = :status';
            $params['status'] = $status;
        }

        if ($payrollMonth !== '') {

            if (!preg_match('/^\d{4}-\d{2}$/', $payrollMonth)) {
                throw new InvalidArgumentException(
                    'Invalid payroll month.'
                );
            }

            $monthFrom = $payrollMonth . '-01';

            $monthTo = date(
                'Y-m-t',
                strtotime($monthFrom)
            );

            $where[] = '
            pr.pay_date BETWEEN :month_from AND :month_to
        ';

            $params['month_from'] = $monthFrom;
            $params['month_to'] = $monthTo;
        }

        $whereSql = implode(
            ' AND ',
            $where
        );

        $sql = "
        SELECT
            pr.id,
            pr.tenant_id,
            pr.period_no,
            pr.period_id,
            pr.run_code,
            pr.payroll_name,
            pr.cutoff_date_from,
            pr.cutoff_date_to,
            pr.pay_date,
            pr.status,
            pr.created_at,

            COUNT(pre.id) AS employee_count,

            COALESCE(
                SUM(pre.gross_pay),
                0
            ) AS total_gross_pay,

            COALESCE(
                SUM(pre.total_deductions),
                0
            ) AS total_deductions,

            COALESCE(
                SUM(pre.net_pay),
                0
            ) AS total_net_pay

        FROM payroll_runs pr

        LEFT JOIN payroll_run_employees pre
            ON pre.run_id = pr.id
           AND pre.tenant_id = pr.tenant_id

        WHERE {$whereSql}

        GROUP BY
            pr.id,
            pr.tenant_id,
            pr.period_no,
            pr.period_id,
            pr.run_code,
            pr.payroll_name,
            pr.cutoff_date_from,
            pr.cutoff_date_to,
            pr.pay_date,
            pr.status,
            pr.created_at

        ORDER BY
            pr.pay_date DESC,
            pr.id DESC

        LIMIT {$limit}
        OFFSET {$offset}
    ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countPayrollRuns(
        int $tenantId,
        string $status = '',
        string $payrollMonth = ''
    ): int {

        $where = [
            'tenant_id = :tenant_id'
        ];

        $params = [
            'tenant_id' => $tenantId
        ];

        if ($status !== '') {
            $where[] = 'status = :status';
            $params['status'] = $status;
        }

        if ($payrollMonth !== '') {

            if (!preg_match('/^\d{4}-\d{2}$/', $payrollMonth)) {
                throw new InvalidArgumentException(
                    'Invalid payroll month.'
                );
            }

            $monthFrom = $payrollMonth . '-01';

            $monthTo = date(
                'Y-m-t',
                strtotime($monthFrom)
            );

            $where[] = '
            pay_date BETWEEN :month_from AND :month_to
        ';

            $params['month_from'] = $monthFrom;
            $params['month_to'] = $monthTo;
        }

        $whereSql = implode(
            ' AND ',
            $where
        );

        $stmt = $this->db->prepare("
        SELECT COUNT(*)
        FROM payroll_runs
        WHERE {$whereSql}
    ");

        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function cancelPayrollRun(
        int $runId,
        int $tenantId,
        int $cancelledBy,
        string $reason
    ): bool {

        $stmt = $this->db->prepare("
        UPDATE payroll_runs
        SET
            status = 'CANCELLED',

            updated_at = NOW()
        WHERE id = :id
          AND tenant_id = :tenant_id
          AND status = 'DRAFT'
    ");

        //     cancellation_reason = :cancellation_reason,
        // cancelled_by = :cancelled_by,
//             // cancelled_at = NOW(),
//   'cancelled_by' => $cancelledBy,
//             'cancellation_reason' => $reason
        $stmt->execute([
            'id' => $runId,
            'tenant_id' => $tenantId

        ]);

        return $stmt->rowCount() === 1;
    }

    public function deletePayrollRun(int $runId, int $tenantId): void
    {

        try {

            $this->db->beginTransaction();

            $run = $this->findPayrollRunForProcessing($runId, $tenantId);

            if (!$run) {
                throw new RuntimeException(
                    'Payroll run not found.'
                );
            }

            // if ($run['status'] !== 'DRAFT') {
            //     throw new RuntimeException(
            //         'Only draft payroll runs can be deleted.'
            //     );
            // }

            $stmt = $this->db->prepare("
            DELETE pri
                FROM payroll_run_items pri
                INNER JOIN payroll_run_employees pre
                    ON pre.id = pri.run_employee_id
                WHERE pre.run_id = :run_id
            ");

            $stmt->execute([
                'run_id' => $runId
            ]);

            $stmt = $this->db->prepare("
            DELETE pred
            FROM payroll_run_employee_days pred
            INNER JOIN payroll_run_employees pre
                ON pre.id = pred.run_employee_id
            WHERE pre.run_id = :run_id
        ");

            $stmt->execute([
                'run_id' => $runId
            ]);

            $stmt = $this->db->prepare("
            DELETE FROM payroll_run_employees
            WHERE run_id = :run_id
              AND tenant_id = :tenant_id
        ");

            $stmt->execute([
                'run_id' => $runId,
                'tenant_id' => $tenantId
            ]);

            $stmt = $this->db->prepare("
            DELETE FROM payroll_runs
            WHERE id = :run_id
              AND tenant_id = :tenant_id
        ");

            $stmt->execute([
                'run_id' => $runId,
                'tenant_id' => $tenantId
            ]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException(
                    'Unable to delete payroll run.'
                );
            }

            $this->db->commit();

        } catch (Throwable $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    public function resetPayrollRun(
        int $runId,
        int $tenantId
    ): void {

        try {
            $this->db->beginTransaction();

            $run = $this->findPayrollRunForProcessing(
                $runId,
                $tenantId
            );

            if (!$run) {
                throw new RuntimeException(
                    'Payroll run not found.'
                );
            }

            if ($run['status'] !== 'DRAFT') {
                throw new RuntimeException(
                    'Only draft payroll runs can be reset.'
                );
            }

            $stmt = $this->db->prepare("
            DELETE pri
            FROM payroll_run_items pri
            INNER JOIN payroll_run_employees pre
                ON pre.id = pri.run_employee_id
            WHERE pre.run_id = :run_id
        ");

            $stmt->execute([
                'run_id' => $runId
            ]);

            $stmt = $this->db->prepare("
            DELETE pred
            FROM payroll_run_employee_days pred
            INNER JOIN payroll_run_employees pre
                ON pre.id = pred.run_employee_id
            WHERE pre.run_id = :run_id
        ");

            $stmt->execute([
                'run_id' => $runId
            ]);

            $stmt = $this->db->prepare("
            DELETE FROM payroll_run_employees
            WHERE run_id = :run_id
              AND tenant_id = :tenant_id
        ");

            $stmt->execute([
                'run_id' => $runId,
                'tenant_id' => $tenantId
            ]);

            $stmt = $this->db->prepare("
            UPDATE payroll_runs
            SET
                processed_by = NULL,
                processed_at = NULL,
                updated_at = NOW()
            WHERE id = :run_id
              AND tenant_id = :tenant_id
        ");

            $stmt->execute([
                'run_id' => $runId,
                'tenant_id' => $tenantId
            ]);

            $this->db->commit();

        } catch (Throwable $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }


    //for payroll view
    public function getRunEmployeeDetails($tenantId, $runId, $runEmployeeId): ?array
    {

        $sql = "
            SELECT
                pre.*,

                e.employee_no,
                e.firstname,
                e.middlename,
                e.lastname

            FROM payroll_run_employees pre

            INNER JOIN hris_employees e
                ON e.id = pre.employee_id
                AND e.tenant_id = pre.tenant_id

            WHERE pre.id = :run_employee_id
            AND pre.run_id = :run_id
            AND pre.tenant_id = :tenant_id

            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'run_employee_id' => $runEmployeeId,
            'run_id' => $runId,
            'tenant_id' => $tenantId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function getRunEmployeeDays($runEmployeeId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                *
            FROM payroll_run_employee_days
            WHERE run_employee_id = :run_employee_id
            ORDER BY attendance_date
        ");

        $stmt->execute([
            'run_employee_id' => $runEmployeeId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRunEmployeeItems(int $runEmployeeId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                *
            FROM payroll_run_items
            WHERE run_employee_id = :run_employee_id
            ORDER BY
                item_type,
                id
        ");

        $stmt->execute([
            'run_employee_id' => $runEmployeeId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}