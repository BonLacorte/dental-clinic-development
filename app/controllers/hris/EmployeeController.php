<?php

require_once __DIR__ . '/../../models/hris/EmployeeModel.php';
require_once __DIR__ . '/../../models/hris/BranchModel.php';
require_once __DIR__ . '/../../models/hris/ShiftModel.php';
require_once __DIR__ . '/../../services/hris/EmployeeExportService.php';

class EmployeeController extends Controller
{
    protected $employeeModel;
    protected $branchModel;

    protected $shiftModel;

    public function __construct()
    {
        $this->employeeModel = new EmployeeModel();
        $this->branchModel = new BranchModel();
        $this->shiftModel = new ShiftModel();
    }

    public function index()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $csrf_token = Csrf::generate();
        $data = [
            'title' => 'Employees',
            'name' => $_SESSION['user']['name'],
            'csrf_token' => $csrf_token,
            'can_export' => EmployeeExportService::canExport($_SESSION)
        ];

        $this->view('module_hris/employees', $data);
    }

    public function getAllEmployees()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $params = $this->listParams($_GET);

        $totalRows = $this->employeeModel->getTotalEmployees($tenant_id, $params['search']);
        $totalPages = max(1, (int) ceil($totalRows / $params['limit']));
        $data = $this->employeeModel->getAllEmployees(
            $tenant_id,
            $params['limit'],
            $params['offset'],
            $params['search'],
            $params['sort_by'],
            $params['sort_dir']
        );

        echo json_encode([
            'success' => true,
            'data' => $data,
            'currentPage' => $params['page'],
            'totalPages' => $totalPages
        ]);
        exit;
    }

    public function exportEmployees(): void
    {
        $path = null;
        $stream = null;
        try {
            // Defence in depth after Auth/Tenant/Module and both Permission middleware checks.
            $context = EmployeeExportService::authorizedContext($_SESSION);
            $token = $_POST['csrf_token'] ?? null;
            if (!is_string($token) || !Csrf::validate($token, 'keep')) {
                throw new EmployeeExportException('Your session token is invalid. Refresh the page and try again.', 403);
            }
            $params = EmployeeExportService::parameters($_POST);
            $limit = EmployeeExportService::rowLimit();
            $rows = $this->employeeModel->getEmployeesForExport(
                $context['tenant_id'], $context['user_id'], $context['branch_ids'],
                $params['search'], $params['sort_by'], $params['sort_dir'], $limit
            );
            $path = EmployeeExportService::createFile($rows, $limit);
            // Shutdown cleanup also covers a client disconnect during the binary response.
            register_shutdown_function(static function () use ($path, &$stream): void {
                if (is_resource($stream)) {
                    fclose($stream);
                }
                EmployeeExportService::cleanup($path);
            });
            $stream = @fopen($path, 'rb');
            $size = @filesize($path);
            if ($stream === false || $size === false || $size === 0) {
                throw new RuntimeException('Export file is unavailable.');
            }
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_write_close();
            }
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . EmployeeExportService::filename() . '"');
            header('Content-Length: ' . $size);
            header('Cache-Control: private, no-store, max-age=0');
            header('Pragma: no-cache');
            header('X-Content-Type-Options: nosniff');
            fpassthru($stream);
        } catch (EmployeeExportException $error) {
            $this->exportError($error->httpStatus, $error->getMessage());
        } catch (Throwable $error) {
            // Do not leak SQL, private data, file paths or configuration in a response/log.
            error_log('Employee export failed (' . get_class($error) . ').');
            $this->exportError(500, 'Unable to prepare the employee export. Please try again later.');
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
            EmployeeExportService::cleanup($path);
        }
    }

    private function exportError(int $status, string $message): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: private, no-store');
        header('X-Content-Type-Options: nosniff');
        echo json_encode(['success' => false, 'message' => $message]);
    }

    public function getEmployeeById(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $employee = $this->employeeModel->getEmployeeById($id, $tenant_id);

        if (!$employee) {
            echo json_encode([
                'success' => false,
                'message' => 'Employee not found.'
            ]);
            return;
        }

        echo json_encode([
            'success' => true,
            'data' => $employee
        ]);
    }

    public function getShifts()
    {

        $tenant_id = $_SESSION['user']['tenant_id'];

        $result = $this->shiftModel->getAllShifts($tenant_id, 500, 0);

        if (!$result) {
            echo json_encode([
                'success' => false,
                'message' => 'Data not found.'
            ]);
            return;
        }

        echo json_encode([
            'success' => true,
            'data' => $result
        ]);
    }

    public function getEmployeeCompensation(int $id)
    {

        $tenant_id = $_SESSION['user']['tenant_id'];

        $result = $this->employeeModel->getEmployeeCompensation($id, $tenant_id);



        if (!$result) {
            echo json_encode([
                'success' => false,
                'message' => 'Data not found.'
            ]);
            return;
        }

        echo json_encode([
            'success' => true,
            'data' => $result
        ]);
    }



    public function createEmployeeCompensation()
    {

        try {
            $tenant_id = $_SESSION['user']['tenant_id'];

            $rules = [
                'employee_id' => ['type' => 'integer', 'required' => true],
                'salary_type' => ['type' => 'string', 'required' => true],
                'payroll_frequency' => ['type' => 'string', 'required' => true],
                'salary_rate' => ['type' => 'numeric', 'required' => true],
                'effective_from' => ['type' => 'date', 'required' => true],
                'effective_to' => ['type' => 'date', 'required' => false]
            ];



            $result = validate($_POST, $rules);

            $data = $result['data'];

            $data['effective_to'] = !empty($data['effective_to']) ? $data['effective_to'] : null;

            $newId = $this->employeeModel->createEmployeeCompensation($tenant_id, $data);

            echo json_encode([
                'success' => true,
                'message' => 'Employee compensation created successfully.',
                'id' => $newId
            ]);

        } catch (Throwable $e) {
            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateEmployeeCompensation(int $id)
    {

        try {
            $tenant_id = $_SESSION['user']['tenant_id'];

            $rules = [
                'employee_id' => ['type' => 'integer', 'required' => true],
                'salary_type' => ['type' => 'string', 'required' => true],
                'payroll_frequency' => ['type' => 'string', 'required' => true],
                'salary_rate' => ['type' => 'numeric', 'required' => true],
                'effective_from' => ['type' => 'date', 'required' => true],
                'effective_to' => ['type' => 'date', 'required' => false]
            ];

            $result = validate($_POST, $rules);

            $data = $result['data'];

            $data['effective_to'] = !empty($data['effective_to']) ? $data['effective_to'] : null;

            $success = $this->employeeModel->updateEmployeeCompensation($id, $tenant_id, $data);

            echo json_encode([
                'success' => $success,
                'message' => $success
                    ? 'Employee compensation updated successfully.'
                    : 'Failed to update employee compensation.'
            ]);
        } catch (Throwable $e) {
            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function create()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];

        $rules = [
            'firstname' => ['type' => 'string', 'required' => true],
            'middlename' => ['type' => 'string', 'required' => false],
            'lastname' => ['type' => 'string', 'required' => true],
            'email' => ['type' => 'email', 'required' => false],
            'contact_no' => ['type' => 'string', 'required' => false, 'regex' => '/^[0-9+()\-\s]{7,20}$/'],
            'branch_id' => ['type' => 'int', 'required' => true],
            'position_id' => ['type' => 'int', 'required' => false],
            'org_unit_id' => ['type' => 'int', 'required' => false],
            'employment_type_id' => ['type' => 'int', 'required' => true],
            'employment_status_id' => ['type' => 'int', 'required' => false],
            'date_hired' => ['type' => 'date', 'required' => true],
            'csrf_token' => ['type' => 'string', 'required' => true]
        ];

        $result = validate($_POST, $rules);
        $data = $result['data'];


        if (!Csrf::validate($data['csrf_token'], 'keep')) {
            je([
                'message' => 'Invalid CSRF token',
                'type' => 'error'
            ], 'json');
            exit;
        }

        $data['employee_no'] = $this->buildEmployeeNo((int) $data['branch_id'], $tenant_id);
        $data['status'] = 'ACTIVE';

        if (empty($data['employment_status_id'])) {
            $data['employment_status_id'] = $this->employeeModel->getDefaultEmploymentStatusId($tenant_id);
        }

        $newId = $this->employeeModel->createEmployee($tenant_id, $data);

        echo json_encode([
            'success' => true,
            'message' => 'Employee created successfully.',
            'id' => $newId
        ]);
    }

    public function updateEmployee(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];

        $rules = [
            'firstname' => ['type' => 'string', 'required' => true],
            'middlename' => ['type' => 'string', 'required' => false],
            'lastname' => ['type' => 'string', 'required' => true],
            'email' => ['type' => 'email', 'required' => false],
            'contact_no' => ['type' => 'string', 'required' => false, 'regex' => '/^[0-9+()\-\s]{7,20}$/'],
            'branch_id' => ['type' => 'int', 'required' => true],
            'position_id' => ['type' => 'int', 'required' => false],
            'org_unit_id' => ['type' => 'int', 'required' => false],
            'employment_type_id' => ['type' => 'int', 'required' => true],
            'employment_status_id' => ['type' => 'int', 'required' => false],
            'date_hired' => ['type' => 'date', 'required' => true],
            'date_resigned' => ['type' => 'date', 'required' => false],
            'csrf_token' => ['type' => 'string', 'required' => true]
        ];

        $result = validate($_POST, $rules);

        $data = $result['data'];

        if (!Csrf::validate($data['csrf_token'], 'keep')) {
            je([
                'message' => 'Invalid CSRF token',
                'type' => 'error'
            ], 'json');
            exit;
        }

        if (
            !empty($data['date_resigned']) && !empty($data['date_hired'])
            && $data['date_resigned'] < $data['date_hired']
        ) {
            je([
                'message' => 'Date resigned cannot be earlier than date hired.',
                'type' => 'error'
            ], 'json');
            exit;
        }

        $success = $this->employeeModel->updateEmployee($id, $tenant_id, $data);

        echo json_encode([
            'success' => $success,
            'message' => $success
                ? 'Employee updated successfully.'
                : 'Failed to update employee.'
        ]);
    }

    public function deleteEmployee(int $id)
    {

        $tenant_id = $_SESSION['user']['tenant_id'];
        $rules = [
            'csrf_token' => ['type' => 'string', 'required' => true]
        ];

        $result = validate($_POST, $rules);
        $data = $result['data'];

        if (!Csrf::validate($data['csrf_token'], 'keep')) {
            je([
                'message' => 'Invalid CSRF token',
                'type' => 'error'
            ], 'json');
            exit;
        }

        $success = $this->employeeModel->deleteEmployee($id, $tenant_id);

        echo json_encode([
            'success' => $success,
            'message' => $success
                ? 'Employee deleted successfully.'
                : 'Failed to delete employee.'
        ]);
    }

    public function getEmploymentTypes()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];

        $data = $this->employeeModel->getEmploymentTypes($tenant_id);

        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getEmploymentStatuses()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $data = $this->employeeModel->getEmploymentStatuses($tenant_id);

        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
    }



    private function listParams(array $input): array
    {
        $rules = [
            'page' => ['type' => 'int', 'required' => false, 'default' => 1],
            'search' => ['type' => 'string', 'required' => false, 'default' => ''],
            'sort_by' => ['type' => 'string', 'required' => false, 'default' => 'created_at'],
            'sort_dir' => ['type' => 'string', 'required' => false, 'default' => 'DESC']
        ];

        $result = validate($input, $rules, false);
        $data = $result['data'];
        $page = max(1, (int) ($data['page'] ?: 1));
        $limit = 10;

        return [
            'page' => $page,
            'limit' => $limit,
            'offset' => ($page - 1) * $limit,
            'search' => $data['search'] ?? '',
            'sort_by' => $data['sort_by'] ?: 'created_at',
            'sort_dir' => strtoupper($data['sort_dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC'
        ];
    }

    private function buildEmployeeNo(int $branch_id, int $tenant_id): string
    {
        $branch = $this->branchModel->getBranchById($branch_id, $tenant_id);
        $prefix = $branch ? strtoupper($branch['code']) : 'EMP';

        $last = $this->employeeModel->getLastEmployeeNoByBranch($branch_id, $tenant_id);

        if ($last) {
            $parts = explode('-', $last);
            $number = (int) end($parts) + 1;
        } else {
            $number = 1;
        }

        return $prefix . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}