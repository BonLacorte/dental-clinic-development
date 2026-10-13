<?php
// require_once __DIR__ . '/../../models/payroll/PayrollSchedulelModel.php'; //// always check for paths when creating controllers
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
class BiometricController extends Controller
{
    // protected $biometricModel;

    public function __construct()
    {
        // $this->biometricModel = new BiometricModel();
    }

    private function cleanHeader(string $value): string
    {
        return strtolower(preg_replace('/[^a-z0-9]+/i', '', trim($value)) ?? '');
    }

    private function parseLogDateTime(mixed $value): ?DateTimeImmutable
    {
        /*
         * Excel dates may be returned as int, float,
         * or a numeric string such as "46287.409722222".
         */
        if (is_numeric($value)) {
            $excelSerial = (float) $value;

            /*
             * Reasonable range for an Excel date.
             * This avoids treating values such as employee IDs as dates.
             */
            if ($excelSerial > 0 && $excelSerial < 2958466) {
                try {
                    $dateTime = ExcelDate::excelToDateTimeObject(
                        $excelSerial,
                        new DateTimeZone('Asia/Manila')
                    );

                    return DateTimeImmutable::createFromMutable(
                        $dateTime
                    );
                } catch (Throwable) {
                    return null;
                }
            }
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'm/d/Y H:i:s',
            'm/d/Y H:i',
            'n/j/Y g:i:s A',
            'n/j/Y g:i A',
            'd/m/Y H:i:s',
            'd/m/Y H:i'
        ];

        foreach ($formats as $format) {
            $date = DateTimeImmutable::createFromFormat('!' . $format, $value, new DateTimeZone('Asia/Manila'));

            $dateErrors = DateTimeImmutable::getLastErrors();

            if ($date !== false && ($dateErrors === false || ($dateErrors['warning_count'] === 0 && $dateErrors['error_count'] === 0))) {
                return $date;
            }
        }

        try {
            return new DateTimeImmutable(
                $value,
                new DateTimeZone('Asia/Manila')
            );
        } catch (Throwable) {
            return null;
        }
    }

    private function normalizeEventType(mixed $value): ?string
    {
        $value = strtoupper(trim((string) $value));
        return match ($value) {
            'IN', 'I', '0', 'CHECK IN', 'CHECK-IN' => 'IN',
            'OUT', 'O', '1', 'CHECK OUT', 'CHECK-OUT' => 'OUT',
            default => null,
        };
    }


    public function index()
    {
        /*
         * Default values used by the view.
         */
        $errors = [];
        $result = null;

        $csrfToken = Csrf::generate();

        /*
         * Restore an existing preview when the page is refreshed.
         */
        $storedPreview = $_SESSION['biometric_preview'] ?? [];

        // $previewRows = $storedPreview['rows'] ?? [];
        // $sourceFile = $storedPreview['source_file'] ?? '';
        $previewRows = [];
        $sourceFile = '';

        $invalidCount = count(array_filter($previewRows, fn($row) => !empty($row['errors'])));


        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'preview') {

            unset($_SESSION['biometric_preview']);

            $previewRows = [];
            $sourceFile = '';
            $invalidCount = 0;

            $file = $_FILES['biometric_file'] ?? null;

            if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                $errors[] = 'Please select a biometric Excel file.';
            } else {
                $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));

                if (!in_array($extension, ['xlsx', 'xls', 'csv'], true)) {
                    $errors[] = 'Only XLSX, XLS, and CSV files are allowed.';
                } elseif (($file['size'] ?? 0) > 10 * 1024 * 1024) {
                    $errors[] = 'The file must not exceed 10 MB.';
                }
            }

            if (!$errors) {
                try {
                    $reader = IOFactory::createReaderForFile(
                        $file['tmp_name']
                    );

                    $reader->setReadDataOnly(true);

                    $spreadsheet = $reader->load($file['tmp_name']);

                    $sheet = $spreadsheet->getActiveSheet();

                    $sheetData = $sheet->toArray(null, true, true, false);

                    if (count($sheetData) < 2) {
                        throw new RuntimeException(
                            'The file has no log records.'
                        );
                    }

                    if (count($sheetData) > 5001) {
                        throw new RuntimeException(
                            'A maximum of 5,000 records is allowed per import.'
                        );
                    }

                    $aliases = [
                        'device_user_id' => [
                            'deviceuserid',
                            'userid',
                            'enno',
                            'tmno'
                        ],

                        'employee_no' => [
                            'employeeno',
                            'employeenumber',
                            'employeeid',
                            'badgeno'
                        ],

                        'employee_name' => [
                            'employeename',
                            'name'
                        ],

                        'log_datetime' => [
                            'logdatetime',
                            'datetime',
                            'timestamp',
                            'logtime'
                        ],

                        'event_type' => [
                            'inout',
                            'eventtype',
                            'status',
                            'punchstate'
                        ],

                        'verify_mode' => [
                            'verifymode',
                            'verification'
                        ],

                        'device_id' => [
                            'deviceid',
                            'devicename',
                            'terminal'
                        ],
                    ];


                    $normalizedHeaders = array_map(fn($header) => $this->cleanHeader((string) $header), $sheetData[0]);

                    $columns = [];

                    foreach ($aliases as $field => $possibleNames) {
                        foreach ($possibleNames as $possibleName) {
                            $columnIndex = array_search(
                                $possibleName,
                                $normalizedHeaders,
                                true
                            );

                            if ($columnIndex !== false) {
                                $columns[$field] = $columnIndex;
                                break;
                            }
                        }
                    }


                    $requiredColumns = [
                        'device_user_id',
                        'employee_no',
                        'log_datetime',
                        'event_type'
                    ];

                    foreach ($requiredColumns as $requiredColumn) {
                        if (!isset($columns[$requiredColumn])) {
                            $errors[] = sprintf(
                                'Missing required column: %s.',
                                $requiredColumn
                            );
                        }
                    }


                    if (!$errors) {
                        foreach (
                            array_slice($sheetData, 1)
                            as $offset => $row
                        ) {
                            $excelRow = $offset + 2;

                            /*
                             * Ignore completely empty rows.
                             */
                            $hasValue = count(array_filter(
                                $row,
                                fn($value) => (
                                    $value !== null
                                    && trim((string) $value) !== ''
                                )
                            )) > 0;

                            if (!$hasValue) {
                                continue;
                            }

                            $date = $this->parseLogDateTime(
                                $row[$columns['log_datetime']] ?? null
                            );

                            $eventType = $this->normalizeEventType(
                                $row[$columns['event_type']] ?? null
                            );

                            $deviceUserId = trim(
                                (string) (
                                    $row[$columns['device_user_id']]
                                    ?? ''
                                )
                            );

                            $employeeNo = trim(
                                (string) (
                                    $row[$columns['employee_no']]
                                    ?? ''
                                )
                            );

                            $employeeName = isset(
                                $columns['employee_name']
                            )
                                ? trim(
                                    (string) (
                                        $row[$columns['employee_name']]
                                        ?? ''
                                    )
                                )
                                : '';

                            $verifyMode = isset(
                                $columns['verify_mode']
                            )
                                ? trim(
                                    (string) (
                                        $row[$columns['verify_mode']]
                                        ?? ''
                                    )
                                )
                                : '';

                            $deviceId = isset(
                                $columns['device_id']
                            )
                                ? trim(
                                    (string) (
                                        $row[$columns['device_id']]
                                        ?? ''
                                    )
                                )
                                : '';

                            /*
                             * Validate the current row.
                             */
                            $rowErrors = [];

                            if ($deviceUserId === '') {
                                $rowErrors[] = 'Device User ID is empty';
                            }

                            if ($employeeNo === '') {
                                $rowErrors[] = 'Employee No is empty';
                            }

                            if (!$date) {
                                $rowErrors[] = 'Invalid Log Date/Time';
                            }

                            if (!$eventType) {
                                $rowErrors[] = 'In/Out must be IN or OUT';
                            }

                            if ($rowErrors) {
                                $invalidCount++;
                            }

                            $previewRows[] = [
                                'row_no' => $excelRow,
                                'device_user_id' => $deviceUserId,
                                'employee_no' => $employeeNo,
                                'employee_name' => $employeeName,
                                'log_datetime' => $date
                                    ? $date->format('Y-m-d H:i:s')
                                    : '',
                                'event_type' => $eventType ?? '',
                                'verify_mode' => $verifyMode,
                                'device_id' => $deviceId,
                                'errors' => $rowErrors,
                            ];
                        }

                        if (!$previewRows) {
                            $errors[] = 'The file contains no usable log records.';
                        } else {
                            /*
                             * Save the normalized preview in the session.
                             */
                            $sourceFile = basename(
                                (string) $file['name']
                            );

                            $_SESSION['biometric_preview'] = [
                                'rows' => $previewRows,
                                'source_file' => $sourceFile
                            ];
                        }
                    }

                    /*
                     * Release the loaded workbook from memory.
                     */
                    $spreadsheet->disconnectWorksheets();
                    unset($spreadsheet);
                } catch (Throwable $e) {
                    $errors[] = 'Unable to read the file: '
                        . $e->getMessage();
                }
            }
        }


        $data = [
            'title' => 'Import Biometric Logs',
            'name' => $_SESSION['user']['name'] ?? '',
            'csrf_token' => $csrfToken,
            'errors' => $errors,
            'result' => $result,
            'previewRows' => $previewRows,
            'sourceFile' => $sourceFile,
            'invalidCount' => $invalidCount
        ];

        $this->view(
            'module_payroll/importView',
            $data
        );
    }

 


}