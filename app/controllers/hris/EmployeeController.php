<?php

require_once __DIR__ . '/../../models/hris/EmployeeModel.php';
require_once __DIR__ . '/../../models/hris/BranchModel.php';
require_once __DIR__ . '/../../models/hris/ShiftModel.php';
require_once __DIR__ . '/../../services/hris/EmployeeInput.php';

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
            'csrf_token' => $csrf_token
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

        $this->json([
            'success' => true,
            'data' => $data,
            'currentPage' => $params['page'],
            'totalPages' => $totalPages
        ]);
        exit;
    }

    public function getEmployeeById(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $employee = $this->employeeModel->getEmployeeById($id, $tenant_id);

        if (!$employee) {
            $this->json([
                'success' => false,
                'message' => 'Employee not found.'
            ]);
            return;
        }

        $this->json([
            'success' => true,
            'data' => $employee
        ]);
    }

    public function getShifts()
    {

        $tenant_id = $_SESSION['user']['tenant_id'];

        $result = $this->shiftModel->getAllShifts($tenant_id, 500, 0);

        if (!$result) {
            $this->json([
                'success' => false,
                'message' => 'Data not found.'
            ]);
            return;
        }

        $this->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function getEmployeeCompensation(int $id)
    {

        $tenant_id = $_SESSION['user']['tenant_id'];

        if (!$this->employeeModel->getEmployeeById($id, $tenant_id)) {
            $this->inputFailure('Employee not found.');
        }

        $result = $this->employeeModel->getEmployeeCompensation($id, $tenant_id);



        if (!$result) {
            $this->json([
                'success' => false,
                'message' => 'Data not found.'
            ]);
            return;
        }

        $this->json([
            'success' => true,
            'data' => $result
        ]);
    }



    public function createEmployeeCompensation()
    {

        try {
            $tenant_id = $_SESSION['user']['tenant_id'];

            $data = $this->validatedInput(EmployeeInput::compensation($_POST));

            $data['effective_to'] = !empty($data['effective_to']) ? $data['effective_to'] : null;

            $newId = $this->employeeModel->createEmployeeCompensation($tenant_id, $data);

            $this->json([
                'success' => $newId > 0,
                'message' => $newId > 0 ? 'Employee compensation created successfully.' : 'Employee compensation was not created.',
                'id' => $newId
            ]);

        } catch (Throwable $e) {
            http_response_code(500);

            $this->json([
                'success' => false,
                'message' => 'The request could not be completed. Check the record before trying again.'
            ]);
        }
    }

    public function updateEmployeeCompensation(int $id)
    {

        try {
            $tenant_id = $_SESSION['user']['tenant_id'];

            $data = $this->validatedInput(EmployeeInput::compensation($_POST), $id);

            $data['effective_to'] = !empty($data['effective_to']) ? $data['effective_to'] : null;

            $success = $this->employeeModel->updateEmployeeCompensation($id, $tenant_id, $data);

            $this->json([
                'success' => $success,
                'message' => $success
                    ? 'Employee compensation updated successfully.'
                    : 'Failed to update employee compensation.'
            ]);
        } catch (Throwable $e) {
            http_response_code(500);

            $this->json([
                'success' => false,
                'message' => 'The request could not be completed. Check the record before trying again.'
            ]);
        }
    }
    public function create()
    {
        try {
            $tenant_id = $_SESSION['user']['tenant_id'];

            $data = $this->validatedInput(EmployeeInput::employee($_POST));

            $data['employee_no'] = $this->buildEmployeeNo((int) $data['branch_id'], $tenant_id);
            $data['status'] = 'ACTIVE';

            if (empty($data['employment_status_id'])) {
                $data['employment_status_id'] = $this->employeeModel->getDefaultEmploymentStatusId($tenant_id);
            }

            $newId = $this->employeeModel->createEmployee($tenant_id, $data);

            $this->json([
                'success' => $newId > 0,
                'message' => $newId > 0 ? 'Employee created successfully.' : 'Employee was not created.',
                'id' => $newId
            ]);
        } catch (Throwable $e) {
            http_response_code(500);
            $this->json(['success' => false, 'message' => 'The request could not be completed. Check the record before trying again.']);
        }
    }

    public function updateEmployee(int $id)
    {
        try {
            $tenant_id = $_SESSION['user']['tenant_id'];

            $data = $this->validatedInput(EmployeeInput::employee($_POST, true), $id);

            $success = $this->employeeModel->updateEmployee($id, $tenant_id, $data);

            $this->json([
                'success' => $success,
                'message' => $success
                    ? 'Employee updated successfully.'
                    : 'Failed to update employee.'
            ]);
        } catch (Throwable $e) {
            http_response_code(500);
            $this->json(['success' => false, 'message' => 'The request could not be completed. Check the record before trying again.']);
        }
    }

    public function deleteEmployee(int $id)
    {
        try {
            $tenant_id = $_SESSION['user']['tenant_id'];
            $this->validatedInput(EmployeeInput::csrf($_POST));

            $success = $this->employeeModel->deleteEmployee($id, $tenant_id);

            $this->json([
                'success' => $success,
                'message' => $success
                    ? 'Employee deleted successfully.'
                    : 'Failed to delete employee.'
            ]);
        } catch (Throwable $e) {
            http_response_code(500);
            $this->json(['success' => false, 'message' => 'The request could not be completed. Check the record before trying again.']);
        }
    }

    public function getEmploymentTypes()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];

        $data = $this->employeeModel->getEmploymentTypes($tenant_id);

        $this->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getEmploymentStatuses()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $data = $this->employeeModel->getEmploymentStatuses($tenant_id);

        $this->json([
            'success' => true,
            'data' => $data
        ]);
    }



    private function json(array $payload): void
    {
        header('Content-Type: application/json');
        echo json_encode($payload);
    }

    private function validatedInput(array $result, ?int $pathId = null): array
    {
        if (!empty($result['errors'])) {
            $this->inputFailure('Please correct the submitted fields.', $result['errors']);
        }
        $data = $result['data'];
        if ($pathId !== null && ($pathId < 1 || (isset($data['id']) && $data['id'] !== $pathId))) {
            $this->inputFailure('The record identifier does not match the request.');
        }
        if (!Csrf::validate($data['csrf_token'], 'keep')) {
            $this->inputFailure('Invalid CSRF token');
        }
        unset($data['csrf_token'], $data['id']);
        return $data;
    }

    private function inputFailure(string $message, array $errors = []): never
    {
        header('Content-Type: application/json');
        $this->json(['success' => false, 'message' => $message, 'type' => 'error', 'errors' => $errors]);
        exit;
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