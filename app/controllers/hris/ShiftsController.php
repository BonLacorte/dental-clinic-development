<?php

require_once __DIR__ . '/../../models/hris/ShiftModel.php';

class ShiftsController extends Controller
{
    protected $shiftModel;

    public function __construct()
    {
        $this->shiftModel = new ShiftModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Shifts',
            'name' => $_SESSION['user']['name'],
            'csrf_token' => Csrf::generate()
        ];

        $this->view('module_hris/shifts', $data);
    }

    public function getAllShifts()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $params = $this->listParams($_GET);

        $totalRows = $this->shiftModel->getTotalShifts($tenant_id, $params['search']);
        $totalPages = max(1, (int) ceil($totalRows / $params['limit']));
        $data = $this->shiftModel->getAllShifts(
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

    public function getShiftById(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $data = $this->shiftModel->getShiftById($id, $tenant_id);

        echo json_encode([
            'success' => (bool) $data,
            'data' => $data,
            'message' => $data ? null : 'Shift not found'
        ]);
    }

    public function create()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $data = $this->validatedShiftData();
        $newId = $this->shiftModel->createShift($tenant_id, $data);

        echo json_encode([
            'success' => true,
            'message' => 'Shift created successfully',
            'id' => $newId
        ]);
    }

    public function update(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $data = $this->validatedShiftData();
        $success = $this->shiftModel->updateShift($id, $tenant_id, $data);

        echo json_encode([
            'success' => $success,
            'message' => $success
                ? 'Shift updated successfully'
                : 'Failed to update shift'
        ]);
    }

    public function delete(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $this->validateCsrfOnly();
        $success = $this->shiftModel->deleteShift($id, $tenant_id);

        echo json_encode([
            'success' => $success,
            'message' => $success
                ? 'Shift deleted successfully'
                : 'Failed to delete shift'
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

    private function validatedShiftData(): array
    {
        $rules = [
            'id' => ['type' => 'int', 'required' => false],
            'name' => ['type' => 'string', 'required' => true, 'max_length' => 100],
            'shift_type' => ['type' => 'string', 'required' => true, 'enum' => ['FIXED', 'FLEX_WINDOW', 'FLEX_HOURS']],
            'start_time' => ['type' => 'string', 'required' => false, 'regex' => '/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
            'end_time' => ['type' => 'string', 'required' => false, 'regex' => '/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
            'flex_start_time' => ['type' => 'string', 'required' => false, 'regex' => '/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
            'flex_end_time' => ['type' => 'string', 'required' => false, 'regex' => '/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
            'required_hours' => ['type' => 'float', 'required' => true, 'min' => 0],
            'break_minutes' => ['type' => 'int', 'required' => true, 'min' => 0],
            'allow_overtime' => ['type' => 'int', 'required' => true, 'enum' => [0, 1]],
            'csrf_token' => ['type' => 'string', 'required' => true]
        ];

        $result = validate($_POST, $rules);
        $this->failOnValidationErrors($result['errors']);
        $data = uppercaseData($result['data'], ['csrf_token', 'start_time', 'end_time', 'flex_start_time', 'flex_end_time', 'required_hours', 'break_minutes', 'allow_overtime']);
        $this->validateShiftTypeFields($data);
        $this->validateCsrfToken($data['csrf_token']);

        return $data;
    }

    private function validateShiftTypeFields(array $data): void
    {
        if ($data['shift_type'] === 'FIXED') {
            if (empty($data['start_time']) || empty($data['end_time'])) {
                je([
                    'success' => false,
                    'message' => 'Fixed shifts require start time and end time.'
                ], 'json');
            }
        }

        if ($data['shift_type'] === 'FLEX_WINDOW') {
            if (
                empty($data['start_time']) ||
                empty($data['end_time']) ||
                empty($data['flex_start_time']) ||
                empty($data['flex_end_time'])
            ) {
                je([
                    'success' => false,
                    'message' => 'Flex window shifts require start/end time and flex start/end time.'
                ], 'json');
            }
        }
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
