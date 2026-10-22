<?php

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EmployeeExportException extends RuntimeException
{
    public function __construct(string $message, public readonly int $httpStatus = 422)
    {
        parent::__construct($message);
    }
}

/** Export-only policy. It intentionally grants no empty-branch/global fallback. */
class EmployeeExportService
{
    public const DEFAULT_MAX_ROWS = 1000;
    public const HARD_MAX_ROWS = 5000;
    private const MAX_TEXT_BYTES = 2000000;
    public const SORT_FIELDS = ['employee_no', 'name', 'branch_name', 'date_hired', 'employment_status', 'created_at'];

    public static function authorizedContext(array $session): array
    {
        $user = $session['user'] ?? [];
        if (!is_array($user) || !self::positiveId($user['id'] ?? null)) {
            throw new EmployeeExportException('Please sign in to export employees.', 401);
        }
        $tenantId = self::positiveId($user['tenant_id'] ?? null);
        $tenant = $session['tenant'] ?? [];
        if (!$tenantId || !is_array($tenant) || self::positiveId($tenant['id'] ?? null) !== $tenantId || ($tenant['status'] ?? '') !== 'ACTIVE') {
            throw new EmployeeExportException('An active tenant context is required for export.', 403);
        }
        // This new endpoint requires both explicit permissions, including for Super Admin.
        // Existing middleware and employee/compensation permission behavior are untouched.
        $permissions = $user['permission_map'] ?? [];
        $modules = $user['modules'] ?? [];
        if (!is_array($permissions) || !isset($permissions['hris.employee.view'], $permissions['hris.view'])
            || !is_array($modules) || !isset($modules['HRIS'])) {
            throw new EmployeeExportException('You do not have permission to export employees.', 403);
        }
        $assigned = $user['branches'] ?? [];
        if (!is_array($assigned) || !$assigned || count($assigned) > 200) {
            throw new EmployeeExportException('Export requires explicit assigned branches. Contact your administrator.', 403);
        }
        $branches = [];
        foreach ($assigned as $branch) {
            $id = self::positiveId($branch);
            if (!$id) {
                throw new EmployeeExportException('Branch access could not be verified. Sign in again.', 403);
            }
            $branches[$id] = $id;
        }
        return ['user_id' => self::positiveId($user['id']), 'tenant_id' => $tenantId, 'branch_ids' => array_values($branches)];
    }

    public static function canExport(array $session): bool
    {
        try {
            self::authorizedContext($session);
            return true;
        } catch (EmployeeExportException $error) {
            return false;
        }
    }

    private static function positiveId(mixed $value): int|false
    {
        if (!is_int($value) && !is_string($value)) {
            return false;
        }
        return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    }

    public static function parameters(array $input): array
    {
        if (array_diff(array_keys($input), ['search', 'sort_by', 'sort_dir', 'csrf_token'])) {
            throw new EmployeeExportException('Unexpected export parameters. Refresh the employee list and try again.');
        }
        $search = $input['search'] ?? '';
        $sortBy = $input['sort_by'] ?? 'created_at';
        $sortDir = $input['sort_dir'] ?? 'DESC';
        if (!is_string($search) || !is_string($sortBy) || !is_string($sortDir)
            || strlen($search) > 512 || !mb_check_encoding($search, 'UTF-8')
            || !in_array($sortBy, self::SORT_FIELDS, true) || !in_array($sortDir, ['ASC', 'DESC'], true)) {
            throw new EmployeeExportException('Invalid export search or ordering. Use a search of at most 512 bytes and an available sort.');
        }
        // Same trim and LIKE wildcard semantics as the existing list validator/model.
        return ['search' => trim($search), 'sort_by' => $sortBy, 'sort_dir' => $sortDir];
    }

    public static function rowLimit(): int
    {
        $configured = getenv('MONTERO_EMPLOYEE_EXPORT_MAX_ROWS');
        if ($configured === false || $configured === '') {
            return self::DEFAULT_MAX_ROWS;
        }
        $limit = filter_var($configured, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => self::HARD_MAX_ROWS]]);
        if ($limit === false) {
            throw new RuntimeException('Invalid employee export row-limit configuration.');
        }
        return $limit;
    }

    /** Generates a genuine XLSX in a private, unpredictable directory outside public/. */
    public static function createFile(array $employees, int $limit): string
    {
        if ($limit < 1 || $limit > self::HARD_MAX_ROWS || count($employees) > $limit) {
            throw new EmployeeExportException('Too many employees to export. Narrow your search and try again.');
        }
        $directory = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'montero-employee-export-' . bin2hex(random_bytes(16));
        if (!@mkdir($directory, 0700)) {
            throw new RuntimeException('Unable to create private export storage.');
        }
        $path = $directory . DIRECTORY_SEPARATOR . 'employees.xlsx';
        $workbook = null;
        try {
            $handle = @fopen($path, 'x');
            if ($handle === false) {
                throw new RuntimeException('Unable to create export file.');
            }
            fclose($handle);
            if (!@chmod($path, 0600)) {
                throw new RuntimeException('Unable to protect export file.');
            }
            $workbook = new Spreadsheet();
            $sheet = $workbook->getActiveSheet();
            $sheet->setTitle('Employees');
            $headers = ['Employee Number', 'Employee Name', 'Branch', 'Date Hired', 'Employment Status'];
            foreach ($headers as $index => $label) {
                $sheet->setCellValueExplicit([1 + $index, 1], $label, DataType::TYPE_STRING);
            }
            $sheet->getStyle('A1:E1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle('A1:E1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF11685D');
            $sheet->getRowDimension(1)->setRowHeight(26);
            foreach (['A' => 24, 'B' => 38, 'C' => 32, 'D' => 18, 'E' => 24] as $column => $width) {
                $sheet->getColumnDimension($column)->setWidth($width);
            }
            $sheet->freezePane('A2');
            $sheet->setAutoFilter('A1:E' . max(1, count($employees) + 1));
            $textBytes = 0;
            foreach ($employees as $index => $employee) {
                $row = $index + 2;
                $rawName = (string) ($employee['employee_name'] ?? '');
                if (!mb_check_encoding($rawName, 'UTF-8')) {
                    throw new EmployeeExportException('An employee name has invalid text encoding. Review the source records before exporting.');
                }
                $name = preg_replace('/\s+/u', ' ', trim($rawName));
                $displayName = $name !== '' && $name !== null ? $name : (string) ($employee['employee_no'] ?? '');
                $values = [
                    'A' => $employee['employee_no'] ?? '',
                    'B' => $displayName !== '' ? $displayName : 'employee',
                    'C' => $employee['branch_name'] ?? '',
                    'E' => $employee['employment_status'] ?? '',
                ];
                foreach ($values as $column => $value) {
                    $value = (string) $value;
                    $textBytes += strlen($value);
                    if ($textBytes > self::MAX_TEXT_BYTES || !mb_check_encoding($value, 'UTF-8') || mb_strlen($value, 'UTF-8') > 32767) {
                        throw new EmployeeExportException('Export text exceeds safe workbook limits. Narrow your search or review the source records.');
                    }
                    // Explicit strings preserve leading zeros/Unicode and prevent formula injection.
                    $sheet->setCellValueExplicit($column . $row, $value, DataType::TYPE_STRING);
                }
                $dateValue = $employee['date_hired'] ?? '';
                if ($dateValue !== null && $dateValue !== '') {
                    $date = is_string($dateValue) ? DateTimeImmutable::createFromFormat('!Y-m-d', $dateValue, new DateTimeZone('Asia/Manila')) : false;
                    if (!$date || $date->format('Y-m-d') !== $dateValue || $dateValue < '1900-01-01') {
                        throw new EmployeeExportException('An employee hire date is invalid. Review the source records before exporting.');
                    }
                    $sheet->setCellValueExplicit('D' . $row, ExcelDate::dateTimeToExcel($date), DataType::TYPE_NUMERIC);
                    $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('yyyy-mm-dd');
                }
            }
            $sheet->getStyle('A1:E' . max(1, count($employees) + 1))->getAlignment()->setWrapText(true);
            $writer = new Xlsx($workbook);
            $writer->setPreCalculateFormulas(false);
            $writer->save($path);
            return $path;
        } catch (Throwable $error) {
            self::cleanup($path);
            throw $error;
        } finally {
            if ($workbook !== null) {
                $workbook->disconnectWorksheets();
            }
        }
    }

    public static function filename(): string
    {
        return 'Montero_Dental_Employees_' . (new DateTimeImmutable('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d') . '.xlsx';
    }

    public static function cleanup(?string $path): void
    {
        if ($path === null) {
            return;
        }
        if (is_file($path) && !@unlink($path)) {
            error_log('Employee export private temporary-file cleanup failed.');
            return;
        }
        if (is_dir(dirname($path)) && !@rmdir(dirname($path))) {
            error_log('Employee export private temporary-directory cleanup failed.');
        }
    }
}
