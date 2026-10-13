<?php

require_once __DIR__ . '/../../models/hris/HolidayModel.php';

class HolidaysController extends Controller
{
    protected $holidayModel;

    public function __construct()
    {
        $this->holidayModel = new HolidayModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Holidays',
            'name' => $_SESSION['user']['name'],
            'csrf_token' => Csrf::generate()
        ];

        $this->view('module_hris/holidays', $data);
    }

    public function getAllHolidays()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $params = $this->listParams($_GET);

        $totalRows = $this->holidayModel->getTotalHolidays($tenant_id, $params['search']);
        $totalPages = max(1, (int) ceil($totalRows / $params['limit']));
        $data = $this->holidayModel->getAllHolidays(
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

    public function getHolidayById(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $data = $this->holidayModel->getHolidayById($id, $tenant_id);

        echo json_encode([
            'success' => (bool) $data,
            'data' => $data,
            'message' => $data ? null : 'Holiday not found'
        ]);
    }

    public function create()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $data = $this->validatedHolidayData();

        if ($this->holidayModel->holidayDateExists($data['holiday_date'], $tenant_id)) {
            je([
                'success' => false,
                'message' => 'A holiday already exists for this date.'
            ], 'json');
        }

        $newId = $this->holidayModel->createHoliday($tenant_id, $data);

        echo json_encode([
            'success' => true,
            'message' => 'Holiday created successfully',
            'id' => $newId
        ]);
    }

    public function update(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $data = $this->validatedHolidayData();

        if ($this->holidayModel->holidayDateExists($data['holiday_date'], $tenant_id, $id)) {
            je([
                'success' => false,
                'message' => 'A holiday already exists for this date.'
            ], 'json');
        }

        $success = $this->holidayModel->updateHoliday($id, $tenant_id, $data);

        echo json_encode([
            'success' => $success,
            'message' => $success
                ? 'Holiday updated successfully'
                : 'Failed to update holiday'
        ]);
    }

    public function delete(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $this->validateCsrfOnly();

        $success = $this->holidayModel->deleteHoliday($id, $tenant_id);

        echo json_encode([
            'success' => $success,
            'message' => $success
                ? 'Holiday deleted successfully'
                : 'Failed to delete holiday'
        ]);
    }

    private function listParams(array $input): array
    {
        $rules = [
            'page' => ['type' => 'int', 'required' => false, 'default' => 1],
            'search' => ['type' => 'string', 'required' => false, 'default' => ''],
            'sort_by' => ['type' => 'string', 'required' => false, 'default' => 'holiday_date'],
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
            'sort_by' => $data['sort_by'] ?: 'holiday_date',
            'sort_dir' => strtoupper($data['sort_dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC'
        ];
    }

    private function validatedHolidayData(): array
    {
        $rules = [
            'id' => ['type' => 'int', 'required' => false],
            'holiday_date' => ['type' => 'date', 'required' => true],
            'name' => ['type' => 'string', 'required' => true, 'max_length' => 150],
            'holiday_type' => ['type' => 'string', 'required' => true, 'enum' => ['REGULAR', 'SPECIAL', 'COMPANY']],
            'pay_multiplier' => ['type' => 'float', 'required' => true, 'min' => 0],
            'allow_work' => ['type' => 'int', 'required' => true, 'enum' => [0, 1]],
            'csrf_token' => ['type' => 'string', 'required' => true]
        ];

        $result = validate($_POST, $rules);
        $this->failOnValidationErrors($result['errors']);
        $data = uppercaseData($result['data'], ['csrf_token', 'holiday_date', 'pay_multiplier', 'allow_work']);
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
