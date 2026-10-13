<?php

require_once __DIR__ . '/../../models/hris/AttendanceLogModel.php';

class AttendanceLogController extends Controller
{
    protected $attendanceLogModel;

    public function __construct()
    {
        $this->attendanceLogModel = new AttendanceLogModel();
    }



    public function test()
    {
        // $tenant_id = $_SESSION['user']['tenant_id'];
        $csrf_token = Csrf::generate();
        $brachId = $_SESSION['user']['branch_id'] ?? 1;

        $data = [
            'title' => 'Attendance Scan Test',
            'branchId' => $brachId,
            'csrf_token' => $csrf_token
        ];

        $this->view('module_hris/attendance_scan_test', $data);
    }

    public function openScanner(int $id)
    {
        // $tenant_id = $_SESSION['user']['tenant_id'];
        $csrf_token = Csrf::generate();


        $data = [
            'title' => 'Attendance Scan Test',
            'branchId' => $id,
            'csrf_token' => $csrf_token
        ];

        $this->view('module_hris/attendance_scan_test', $data);
    }


    public function scan()
    {
        header('Content-Type: application/json; charset=utf-8');

        $tenantId = $_SESSION['user']['tenant_id'] ?? null;

        if (empty($tenantId)) {
            http_response_code(401);

            echo json_encode([
                'success' => false,
                'message' => 'Unauthorized request.'
            ]);
            return;
        }

        $rules = [
            'employee_id' => ['type' => 'int', 'required' => true, 'min' => 1],
            'device_id' => ['type' => 'string', 'required' => true, 'max_length' => 100],
            'branch_id' => ['type' => 'int', 'required' => true, 'min' => 1],
            'csrf_token' => ['type' => 'string', 'required' => true]
        ];

        $result = validate($_POST, $rules);

        if (!empty($result['errors'])) {
            http_response_code(422);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid request data.',
                'errors' => $result['errors']
            ]);
            return;
        }

        $data = $result['data'];

        if (!Csrf::validate($data['csrf_token'], 'keep')) {
            http_response_code(419);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid CSRF token.'
            ]);
            return;
        }

        $employeeId = $data['employee_id'];
        $deviceId = strtoupper($data['device_id']);
        $branchId = $data['branch_id'];

        if (!$this->attendanceLogModel->employeeExists($tenantId, $employeeId)) {
            echo json_encode([
                'success' => false,
                'message' => 'Employee does not exist.'
            ]);

            return;
        }


        try {
            $attendance = $this->attendanceLogModel->recordScan(
                (int) $tenantId,
                (int) $branchId,
                (int) $employeeId,
                $deviceId,
                new DateTimeImmutable(
                    'now',
                    new DateTimeZone('Asia/Manila')
                )
            );

            echo json_encode([
                'success' => true,
                'message' => $attendance['action'] === 'TIME_IN'
                    ? 'Time-in recorded successfully.'
                    : 'Time-out recorded successfully.',
                'data' => $attendance
            ]);
        } catch (DomainException $e) {
            http_response_code(409);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (Throwable $e) {
            error_log($e->__toString());

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => 'Unable to record attendance.'
            ]);
        }
    }

    public function dtr()
    {
        $data = [];
        $tenantId = (int) $_SESSION['user']['tenant_id'];
        $loggedEmployeeId = (int) ($_SESSION['user']['employee_id'] ?? 0);

        $month = $_GET['month'] ?? date('Y-m');

        // Validate YYYY-MM
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = date('Y-m');
        }

        $dateFrom = $month . '-01';
        $dateTo = date('Y-m-t', strtotime($dateFrom));


        $canViewAll = true; // for security

        if ($canViewAll) {
            $employeeId = isset($_GET['employee_id']) ? (int) $_GET['employee_id'] : $loggedEmployeeId;
        } else {
            $employeeId = $loggedEmployeeId;
        }

  

        $attendance = [];

        if ($employeeId > 0) {
            $attendance = $this->attendanceLogModel->getEmployeeDtr(
                $tenantId,
                $employeeId,
                $dateFrom,
                $dateTo
            );
        }

        $summary = $this->attendanceLogModel->getEmployeeDtrSummary(
            $tenantId, 
            $employeeId, 
            $dateFrom, 
            $dateTo);

        $employees = [];

        if ($canViewAll) {
            $employees = $this->attendanceLogModel->getEmployeesForAttendance($tenantId);
        }




 
        $data = [
            'title' => 'DTR',
            'name' => $_SESSION['user']['name'],
            'month' => $month,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'employeeId' => $employeeId,
            'attendance' => $attendance,
            'summary' => $summary,
            'employees' => $employees,
            'canViewAll' => $canViewAll
        ];

        return $this->view('module_hris/dtr', $data);
    }
}