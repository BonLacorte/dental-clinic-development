<?php

require_once __DIR__ . '/../../models/payroll/PayrollRunModel.php';

class PayrollRunController extends Controller
{
    protected PayrollRunModel $payrollRunModel;

    public function __construct()
    {
        $this->payrollRunModel = new PayrollRunModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Payroll Runs',
            'name' => $_SESSION['user']['name'] ?? '',
            'csrf_token' => Csrf::generate()
        ];

        $this->view('module_payroll/payroll_run_index', $data);
    }

    public function viewRun(int $id)
    {
        $tenantId = (int) $_SESSION['user']['tenant_id'];

        $run = $this->payrollRunModel->findPayrollRunById($tenantId, $id);

        if (!$run) {
            http_response_code(404);
            echo 'Payroll run not found.';
            return;
        }

        $employees = $this->payrollRunModel->getPayrollRunEmployees($id);

        $data = [
            'title' => 'Payroll Run',
            'name' => $_SESSION['user']['name'] ?? '',
            'csrf_token' => Csrf::generate(),
            'run' => $run,
            'employees' => $employees
        ];

        $this->view('module_payroll/payroll_run_view', $data);

    }

    public function create()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {

            $tenantId = $_SESSION['user']['tenant_id'];
            $userId = $_SESSION['user']['id'];

            if (!Csrf::validate($_POST['csrf_token'] ?? '', 'keep')) {
                throw new RuntimeException('Invalid CSRF token.');
            }

            $payrollMonth = $_POST['payroll_month'] ?? '';

            $periodNo = (int) ($_POST['period_no'] ?? 0);
            $periodId = $periodNo;


            [$year, $month] = array_map('intval', explode('-', $payrollMonth));


            if ($periodNo === 1) {

                $currentMonth = new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month));
                $cutoffFrom = $currentMonth->modify('-1 day')->format('Y-m-d');
                $cutoffTo = $currentMonth->setDate($year, $month, 14)->format('Y-m-d');
                $payDate = $currentMonth->setDate($year, $month, 15)->format('Y-m-d');

            } else {

                $currentMonth = new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month));
                $cutoffFrom = $currentMonth->setDate($year, $month, 15)->format('Y-m-d');
                $lastDay = $currentMonth->modify('last day of this month');
                $requestedPayDate = $currentMonth->setDate($year, $month, 30);

                if ((int) $requestedPayDate->format('m') !== $month) {
                    $requestedPayDate = $lastDay;
                }

                $payDate = $requestedPayDate->format('Y-m-d');
                $cutoffTo = $requestedPayDate->modify('-1 day')->format('Y-m-d');
            }

            if ($cutoffFrom === '' || $cutoffTo === '' || $payDate === '') {
                throw new RuntimeException('Payroll dates are required.');
            }

            if ($periodNo <= 0) {
                throw new RuntimeException('Payroll period is required.');
            }

            if ($periodId <= 0) {
                throw new RuntimeException('Payroll period ID is required.');
            }

            if ($this->payrollRunModel->payrollRunExists($tenantId, $cutoffFrom, $cutoffTo)) {
                throw new RuntimeException('A payroll run already exists for this cutoff period.');
            }

            $year = (int) date('Y', strtotime($payDate));
            $month = (int) date('m', strtotime($payDate));
            $sequence = $this->payrollRunModel->generateNextRunSequence($tenantId, $year, $month);
            $runCode = sprintf('PAY-%04d%02d-%03d', $year, $month, $sequence);
            $payrollName = trim((string) ($_POST['payroll_name'] ?? ''));

            if ($payrollName === '') {
                $payrollName = sprintf('Payroll %s to %s', date('M d', strtotime($cutoffFrom)), date('M d, Y', strtotime($cutoffTo)));
            }

            $data = [
                'tenant_id' => $tenantId,
                'period_no' => $periodNo,
                'period_id' => $periodId,
                'run_code' => $runCode,
                'payroll_name' => $payrollName,
                'cutoff_date_from' => $cutoffFrom,
                'cutoff_date_to' => $cutoffTo,
                'pay_date' => $payDate,
                'created_by' => $userId
            ];


            $result = $this->payrollRunModel->createPayrollRun($data);

            echo json_encode([
                'success' => true,
                'message' => 'Payroll run created successfully.',
                'data' => $result
            ]);

        } catch (Throwable $e) {

            http_response_code(422);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function loadEmployees($runId)
    {

        $tenantId = $_SESSION['user']['tenant_id'];

        header('Content-Type: application/json');

        try {

            if (!Csrf::validate($_POST['csrf_token'] ?? '', 'keep')) {
                throw new RuntimeException('Invalid CSRF token.');
            }

            $payrollRun = $this->payrollRunModel->findPayrollRunById($tenantId, $runId);


            $cutoffFrom = $payrollRun['cutoff_date_from'];
            $cutoffTo = $payrollRun['cutoff_date_to'];


            $loaded = $this->payrollRunModel->loadEmployees($runId, $tenantId, $cutoffFrom, $cutoffTo);


            echo json_encode([
                'success' => true,
                'message' => sprintf(
                    '%d employee%s loaded successfully.',
                    $loaded,
                    $loaded === 1 ? '' : 's'
                ),
                'data' => ['loaded' => $loaded]
            ]);

        } catch (Throwable $e) {

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

    }

    public function process(int $id)
    {
        header('Content-Type: application/json; charset=utf-8');

        try {

            $tenantId = (int) $_SESSION['user']['tenant_id'];
            $userId = (int) $_SESSION['user']['id'];

            if (!Csrf::validate($_POST['csrf_token'] ?? '', 'keep')) {
                throw new RuntimeException('Invalid CSRF token.');
            }

            $result = $this->payrollRunModel->processPayrollRun($id, $tenantId, $userId);

            echo json_encode([
                'success' => true,
                'message' => 'Payroll run processed successfully.',
                'data' => $result
            ]);

        } catch (Throwable $e) {

            http_response_code(422);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function employees(int $runId)
    {
        header('Content-Type: application/json; charset=utf-8');

        try {

            $tenantId = (int) $_SESSION['user']['tenant_id'];

            $run = $this->payrollRunModel->findPayrollRunById($tenantId, $runId);

            if (!$run) {
                throw new RuntimeException(
                    'Payroll run not found.'
                );
            }

            $employees = $this->payrollRunModel->getPayrollRunEmployees($runId);

            echo json_encode([
                'success' => true,
                'data' => $employees
            ]);

        } catch (Throwable $e) {

            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getAllRuns()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $tenantId = (int) $_SESSION['user']['tenant_id'];

            $page = max(
                1,
                (int) ($_GET['page'] ?? 1)
            );

            $limit = 10;
            $offset = ($page - 1) * $limit;

            $status = strtoupper(
                trim((string) ($_GET['status'] ?? ''))
            );

            $payrollMonth = trim(
                (string) ($_GET['payroll_month'] ?? '')
            );

            $totalRecords = $this->payrollRunModel
                ->countPayrollRuns(
                    $tenantId,
                    $status,
                    $payrollMonth
                );

            $runs = $this->payrollRunModel
                ->getPayrollRuns(
                    $tenantId,
                    $limit,
                    $offset,
                    $status,
                    $payrollMonth
                );

            echo json_encode([
                'success' => true,
                'data' => $runs,
                'currentPage' => $page,
                'totalPages' => max(
                    1,
                    (int) ceil($totalRecords / $limit)
                ),
                'totalRecords' => $totalRecords
            ]);

        } catch (Throwable $e) {

            error_log($e->__toString());

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function cancel(int $id)
    {
        header('Content-Type: application/json; charset=utf-8');

        try {

            $tenantId = (int) $_SESSION['user']['tenant_id'];
            $userId = (int) $_SESSION['user']['id'];

            if (!Csrf::validate($_POST['csrf_token'] ?? '', 'keep')) {
                throw new RuntimeException(
                    'Invalid CSRF token.'
                );
            }

            $reason = trim($_POST['cancellation_reason'] ?? '');

            if ($reason === '') {
                throw new RuntimeException(
                    'Cancellation reason is required.'
                );
            }

            $success = $this->payrollRunModel->cancelPayrollRun(
                $id,
                $tenantId,
                $userId,
                $reason
            );

            if (!$success) {
                throw new RuntimeException(
                    'Unable to cancel payroll run.'
                );
            }

            echo json_encode([
                'success' => true,
                'message' => 'Payroll run cancelled successfully.'
            ]);

        } catch (Throwable $e) {

            http_response_code(422);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete(int $id)
    {
        header('Content-Type: application/json; charset=utf-8');

        try {

            $tenantId = (int) $_SESSION['user']['tenant_id'];

            if (!Csrf::validate($_POST['csrf_token'] ?? '', 'keep')) {
                throw new RuntimeException('Invalid CSRF token.');
            }

            $this->payrollRunModel->deletePayrollRun($id, $tenantId);

            echo json_encode([
                'success' => true,
                'message' => 'Payroll run deleted successfully.'
            ]);

        } catch (Throwable $e) {

            http_response_code(422);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function reset(int $id)
    {
        header('Content-Type: application/json; charset=utf-8');

        try {

            $tenantId = (int) $_SESSION['user']['tenant_id'];

            if (!Csrf::validate($_POST['csrf_token'] ?? '', 'keep')) {
                throw new RuntimeException('Invalid CSRF token.');
            }

            $this->payrollRunModel->resetPayrollRun($id, $tenantId);

            echo json_encode([
                'success' => true,
                'message' => 'Payroll run reset successfully.'
            ]);

        } catch (Throwable $e) {

            http_response_code(422);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    //for payroll view

    public function employeeDetails($runId, $runEmployeeId)
    {

        $tenantId = (int) $_SESSION['user']['tenant_id'];
        $runId = (int) $runId;
        $runEmployeeId = (int) $runEmployeeId;




        $run = $this->payrollRunModel->findPayrollRunById($tenantId, $runId);



        if (!$run) {
            throw new RuntimeException('Payroll run not found.');
        }

        $employee = $this->payrollRunModel->getRunEmployeeDetails($tenantId, $runId, $runEmployeeId);

        if (!$employee) {
            throw new RuntimeException('Employee payroll record not found.');
        }

        $days = $this->payrollRunModel->getRunEmployeeDays($runEmployeeId);

        $items = $this->payrollRunModel->getRunEmployeeItems($runEmployeeId);

        $earnings = [];
        $deductions = [];

        foreach ($items as $item) {
            if ($item['item_type'] === 'EARNING') {
                $earnings[] = $item;
            } elseif ($item['item_type'] === 'DEDUCTION') {
                $deductions[] = $item;
            }
        }

        $data = [
            'title' => 'Payroll Employee View',
            'name' => $_SESSION['user']['name'] ?? '',
            'csrf_token' => Csrf::generate(),

            'run' => $run,
            'employee' => $employee,
            'days' => $days,
            'items' => $items,
            'earnings' => $earnings,
            'deductions' => $deductions,
        ];

        $this->view('module_payroll/payroll_employee_view', $data);

    }


    public function importView(){
        $data = [
            'title' => 'Import',
            'name' => $_SESSION['user']['name'] ?? '',
            'csrf_token' => Csrf::generate()
        ];

        $this->view('module_payroll/importView', $data);

    }
}