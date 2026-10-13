<?php

require_once __DIR__ . '/../../models/hris/LeaveModel.php';

class LeavesController extends Controller
{
    protected $leaveModel;

    public function __construct()
    {
        $this->leaveModel = new LeaveModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Leaves',
            'name' => $_SESSION['user']['name'],
            'csrf_token' => Csrf::generate()
        ];

        $this->view('module_hris/leaves', $data);
    }

    public function getAllRequests()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $params = $this->listParams($_GET);

        $totalRows = $this->leaveModel->getTotalLeaveRequests($tenant_id, $params['search']);
        $totalPages = max(1, (int) ceil($totalRows / $params['limit']));
        $data = $this->leaveModel->getLeaveRequests(
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

    public function getRequestById(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $data = $this->leaveModel->getLeaveRequestById($id, $tenant_id);

        echo json_encode([
            'success' => (bool) $data,
            'data' => $data,
            'message' => $data ? null : 'Leave request not found'
        ]);
    }

    public function createRequest()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $data = $this->validatedRequestData();
        $this->validateRequestReferences($data, $tenant_id);

        if ($data['status'] === 'APPROVED') {
            $data['approved_by'] = $_SESSION['user']['id'];
            $data['approved_at'] = date('Y-m-d H:i:s');
        }

        $newId = $this->leaveModel->createLeaveRequest($tenant_id, $data);

        echo json_encode([
            'success' => true,
            'message' => 'Leave request created successfully',
            'id' => $newId
        ]);
    }

    public function updateRequest(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $data = $this->validatedRequestData();
        $this->validateRequestReferences($data, $tenant_id);

        if ($data['status'] === 'APPROVED') {
            $data['approved_by'] = $_SESSION['user']['id'];
            $data['approved_at'] = date('Y-m-d H:i:s');
        } else {
            $data['approved_by'] = null;
            $data['approved_at'] = null;
        }

        $success = $this->leaveModel->updateLeaveRequest($id, $tenant_id, $data);

        echo json_encode([
            'success' => $success,
            'message' => $success
                ? 'Leave request updated successfully'
                : 'Failed to update leave request'
        ]);
    }

    public function deleteRequest(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $this->validateCsrfOnly();

        $success = $this->leaveModel->deleteLeaveRequest($id, $tenant_id);

        echo json_encode([
            'success' => $success,
            'message' => $success
                ? 'Leave request deleted successfully'
                : 'Failed to delete leave request'
        ]);
    }

    public function getAllTypes()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $params = $this->listParams($_GET);

        $totalRows = $this->leaveModel->getTotalLeaveTypes($tenant_id, $params['search']);
        $totalPages = max(1, (int) ceil($totalRows / $params['limit']));
        $data = $this->leaveModel->getLeaveTypes(
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

    public function getTypeById(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $data = $this->leaveModel->getLeaveTypeById($id, $tenant_id);

        echo json_encode([
            'success' => (bool) $data,
            'data' => $data,
            'message' => $data ? null : 'Leave type not found'
        ]);
    }

    public function createType()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $data = $this->validatedTypeData();
        $newId = $this->leaveModel->createLeaveType($tenant_id, $data);

        echo json_encode([
            'success' => true,
            'message' => 'Leave type created successfully',
            'id' => $newId
        ]);
    }

    public function updateType(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $data = $this->validatedTypeData();
        $success = $this->leaveModel->updateLeaveType($id, $tenant_id, $data);

        echo json_encode([
            'success' => $success,
            'message' => $success
                ? 'Leave type updated successfully'
                : 'Failed to update leave type'
        ]);
    }

    public function deleteType(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $this->validateCsrfOnly();
        $success = $this->leaveModel->deleteLeaveType($id, $tenant_id);

        echo json_encode([
            'success' => $success,
            'message' => $success
                ? 'Leave type deleted successfully'
                : 'Failed to delete leave type'
        ]);
    }

    public function getEmployees()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];

        echo json_encode([
            'success' => true,
            'data' => $this->leaveModel->getEmployeeOptions($tenant_id)
        ]);
    }

    public function getTypeOptions()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];

        echo json_encode([
            'success' => true,
            'data' => $this->leaveModel->getLeaveTypeOptions($tenant_id)
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

    private function validatedRequestData(): array
    {
        $rules = [
            'id' => ['type' => 'int', 'required' => false],
            'employee_id' => ['type' => 'int', 'required' => true],
            'leave_type_id' => ['type' => 'int', 'required' => true],
            'date_from' => ['type' => 'date', 'required' => true],
            'date_to' => ['type' => 'date', 'required' => true],
            'reason' => ['type' => 'string', 'required' => false],
            'status' => ['type' => 'string', 'required' => true, 'enum' => ['PENDING', 'APPROVED', 'REJECTED', 'CANCELLED']],
            'csrf_token' => ['type' => 'string', 'required' => true]
        ];

        $result = validate($_POST, $rules);
        $this->failOnValidationErrors($result['errors']);
        $data = $result['data'];

        if (strtotime($data['date_to']) < strtotime($data['date_from'])) {
            je([
                'success' => false,
                'message' => 'Date to must be on or after date from.'
            ], 'json');
        }

        $this->validateCsrfToken($data['csrf_token']);

        return $data;
    }

    private function validatedTypeData(): array
    {
        $rules = [
            'id' => ['type' => 'int', 'required' => false],
            'name' => ['type' => 'string', 'required' => true, 'max_length' => 100],
            'is_paid' => ['type' => 'int', 'required' => true, 'enum' => [0, 1]],
            'csrf_token' => ['type' => 'string', 'required' => true]
        ];

        $result = validate($_POST, $rules);
        $this->failOnValidationErrors($result['errors']);
        $data = uppercaseData($result['data'], ['csrf_token']);
        $this->validateCsrfToken($data['csrf_token']);

        return $data;
    }

    private function validateCsrfOnly(): void
    {
        $rules = [
            'csrf_token' => ['type' => 'string', 'required' => true]
        ];

        $result = validate($_POST, $rules);
        $this->failOnValidationErrors($result['errors']);
        $this->validateCsrfToken($result['data']['csrf_token']);
    }

    private function validateRequestReferences(array $data, int $tenant_id): void
    {
        if (!$this->leaveModel->employeeBelongsToTenant((int) $data['employee_id'], $tenant_id)) {
            je([
                'success' => false,
                'message' => 'Selected employee is invalid.'
            ], 'json');
        }

        if (!$this->leaveModel->leaveTypeAllowedForTenant((int) $data['leave_type_id'], $tenant_id)) {
            je([
                'success' => false,
                'message' => 'Selected leave type is invalid.'
            ], 'json');
        }
    }

    private function validateCsrfToken(string $token): void
    {
        if (!Csrf::validate($token, 'keep')) {
            je([
                'success' => false,
                'message' => 'Invalid CSRF token',
                'type' => 'error'
            ], 'json');
        }
    }

    private function failOnValidationErrors(array $errors): void
    {
        if (!empty($errors)) {
            je([
                'success' => false,
                'message' => implode(' ', $errors),
                'errors' => $errors
            ], 'json');
        }
    }
}
