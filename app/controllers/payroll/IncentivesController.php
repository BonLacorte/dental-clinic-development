<?php

require_once __DIR__ . '/../../models/payroll/IncentiveModel.php';

class IncentivesController extends Controller
{
    protected $model;
    protected string $viewName;
    protected string $title;
    protected string $singular;
    protected string $recordLabel;
    protected ?string $typeLabel = null;
    protected ?string $typeField = null;
    protected array $recordRules = [];
    protected array $typeRules = [];


    public function __construct()
    {
        $this->model = new IncentiveModel();
        $this->viewName = 'incentives';
        $this->title = 'Incentives';
        $this->singular = 'Incentive';
        $this->recordLabel = 'Incentive';
        $this->typeLabel = 'Incentive Type';
        $this->typeField = 'incentive_type_id';
        $this->recordRules = [
            'employee_id' => ['label' => 'Employee', 'input' => 'employee', 'required' => true],
            'incentive_type_id' => ['label' => 'Incentive Type', 'input' => 'type', 'required' => true],
            'amount' => ['label' => 'Amount', 'input' => 'number', 'required' => true, 'min' => 0.01],
            'is_recurring' => ['label' => 'Recurring', 'input' => 'bool', 'required' => true],
            'effective_from' => ['label' => 'Effective From', 'input' => 'date', 'required' => true],
            'effective_to' => ['label' => 'Effective To', 'input' => 'date', 'required' => false]
        ];
        $this->typeRules = [
            'code' => ['label' => 'Code', 'input' => 'string', 'required' => true, 'max' => 50],
            'name' => ['label' => 'Name', 'input' => 'string', 'required' => true, 'max' => 100],
            'computation_type' => ['label' => 'Computation Type', 'input' => 'string', 'required' => true, 'options' => ['FIXED', 'PERCENTAGE']],
            'default_value' => ['label' => 'Default Value', 'input' => 'number', 'required' => true, 'min' => 0]
        ];
    }

    public function getAllIncentives()
    {
        $this->allRecords();
    }

    public function getIncentiveById(int $id)
    {
        $this->recordById($id);
    }

    public function index()
    {
        $data = [
            'title' => $this->title,
            'name' => $_SESSION['user']['name'],
            'csrf_token' => Csrf::generate()
        ];

        $this->view("module_payroll/{$this->viewName}", $data);
    }

    protected function listParams(array $input): array
    {
        $page = max(1, (int) ($input['page'] ?? 1));

        return [
            'page' => $page,
            'limit' => 10,
            'offset' => ($page - 1) * 10,
            'search' => trim((string) ($input['search'] ?? '')),
            'sort_by' => trim((string) ($input['sort_by'] ?? 'created_at')) ?: 'created_at',
            'sort_dir' => strtoupper((string) ($input['sort_dir'] ?? 'DESC')) === 'ASC' ? 'ASC' : 'DESC'
        ];
    }

    protected function jsonList(array $params, int $totalRows, array $data): void
    {
        je([
            'success' => true,
            'data' => $data,
            'currentPage' => $params['page'],
            'totalPages' => max(1, (int) ceil($totalRows / $params['limit']))
        ], 'json');
    }

    protected function allRecords(): void
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $params = $this->listParams($_GET);
        $totalRows = $this->model->getTotalRecords($tenant_id, $params['search']);
        $data = $this->model->getRecords(
            $tenant_id,
            $params['limit'],
            $params['offset'],
            $params['search'],
            $params['sort_by'],
            $params['sort_dir']
        );

        $this->jsonList($params, $totalRows, $data);
    }

    protected function recordById(int $id): void
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $data = $this->model->getRecordById($id, $tenant_id);

        je([
            'success' => (bool) $data,
            'data' => $data,
            'message' => $data ? null : "{$this->singular} not found"
        ], 'json');
    }

    public function create()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $data = $this->validatedRecordData();
        $this->validateRecordReferences($tenant_id, $data);
        $newId = $this->model->createRecord($tenant_id, $data);

        je([
            'success' => true,
            'message' => "{$this->recordLabel} created successfully",
            'id' => $newId
        ], 'json');
    }

    public function update(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $data = $this->validatedRecordData();
        $this->validateRecordReferences($tenant_id, $data);
        $success = $this->model->updateRecord($id, $tenant_id, $data);

        je([
            'success' => $success,
            'message' => $success
                ? "{$this->recordLabel} updated successfully"
                : "Failed to update {$this->recordLabel}"
        ], 'json');
    }

    public function delete(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $this->validateCsrfOnly();
        $success = $this->model->deleteRecord($id, $tenant_id);

        je([
            'success' => $success,
            'message' => $success
                ? "{$this->recordLabel} deleted successfully"
                : "Failed to delete {$this->recordLabel}"
        ], 'json');
    }

    public function getEmployees()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];

        je([
            'success' => true,
            'data' => $this->model->getEmployeeOptions($tenant_id)
        ], 'json');
    }

    public function getAllTypes()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $params = $this->listParams($_GET);
        $totalRows = $this->model->getTotalTypes($tenant_id, $params['search']);
        $data = $this->model->getTypes(
            $tenant_id,
            $params['limit'],
            $params['offset'],
            $params['search'],
            $params['sort_by'],
            $params['sort_dir']
        );

        $this->jsonList($params, $totalRows, $data);
    }

    public function getTypeById(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $data = $this->model->getTypeById($id, $tenant_id);

        je([
            'success' => (bool) $data,
            'data' => $data,
            'message' => $data ? null : "{$this->typeLabel} not found"
        ], 'json');
    }

    public function createType()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $data = $this->validatedTypeData();

        if ($this->model->typeCodeExists($data['code'], $tenant_id)) {
            $this->fail('A type already exists with this code.');
        }

        $newId = $this->model->createType($tenant_id, $data);

        je([
            'success' => true,
            'message' => "{$this->typeLabel} created successfully",
            'id' => $newId
        ], 'json');
    }

    public function updateType(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $data = $this->validatedTypeData();

        if ($this->model->typeCodeExists($data['code'], $tenant_id, $id)) {
            $this->fail('A type already exists with this code.');
        }

        $success = $this->model->updateType($id, $tenant_id, $data);

        je([
            'success' => $success,
            'message' => $success
                ? "{$this->typeLabel} updated successfully"
                : "Failed to update {$this->typeLabel}"
        ], 'json');
    }

    public function deleteType(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $this->validateCsrfOnly();
        $success = $this->model->deleteType($id, $tenant_id);

        je([
            'success' => $success,
            'message' => $success
                ? "{$this->typeLabel} deleted successfully"
                : "Failed to delete {$this->typeLabel}"
        ], 'json');
    }

    public function getTypeOptions()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];

        je([
            'success' => true,
            'data' => $this->model->getTypeOptions($tenant_id)
        ], 'json');
    }

    protected function validatedRecordData(): array
    {
        $data = $this->validateFields($this->recordRules, $_POST);
        $this->validateCsrfToken((string) ($_POST['csrf_token'] ?? ''));

        if (!empty($data['effective_from']) && !empty($data['effective_to']) && strtotime($data['effective_to']) < strtotime($data['effective_from'])) {
            $this->fail('Effective to must be on or after effective from.');
        }

        if (array_key_exists('remaining_balance', $data) && $data['remaining_balance'] === null && array_key_exists('amount', $data)) {
            $data['remaining_balance'] = $data['amount'];
        }

        if (($data['deduction_type'] ?? null) === 'SINGLE') {
            $data['installment_amount'] = null;
        }

        return $data;
    }

    protected function validatedTypeData(): array
    {
        $data = $this->validateFields($this->typeRules, $_POST);
        $this->validateCsrfToken((string) ($_POST['csrf_token'] ?? ''));

        return uppercaseData($data, ['default_value', 'taxable']);
    }

    protected function validateRecordReferences(int $tenant_id, array $data): void
    {
        if (!$this->model->employeeBelongsToTenant((int) $data['employee_id'], $tenant_id)) {
            $this->fail('Selected employee is invalid for this tenant.');
        }

        if ($this->typeField && !$this->model->typeAllowedForTenant((int) $data[$this->typeField], $tenant_id)) {
            $this->fail('Selected payroll type is invalid for this tenant.');
        }
    }

    protected function validateFields(array $rules, array $input): array
    {
        $data = [];

        foreach ($rules as $name => $rule) {
            $label = $rule['label'];
            $value = $input[$name] ?? null;

            if (is_string($value)) {
                $value = trim($value);
            }

            if ($value === '' || $value === null) {
                if (!empty($rule['required'])) {
                    $this->fail($label . ' is required.');
                }

                $data[$name] = null;
                continue;
            }

            if (in_array($rule['input'], ['employee', 'type', 'bool'], true)) {
                if (filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $this->fail($label . ' must be a valid selection.');
                }

                $value = (int) $value;

                if ($rule['input'] === 'bool' && !in_array($value, [0, 1], true)) {
                    $this->fail($label . ' has invalid value.');
                }
            } elseif ($rule['input'] === 'number') {
                if (!is_numeric($value)) {
                    $this->fail($label . ' must be numeric.');
                }

                $value = (float) $value;

                if (isset($rule['min']) && $value < (float) $rule['min']) {
                    $this->fail($label . ' must be at least ' . $rule['min'] . '.');
                }
            } elseif ($rule['input'] === 'date') {
                $dt = DateTime::createFromFormat('Y-m-d', (string) $value);
                $errors = DateTime::getLastErrors();
                $warningCount = is_array($errors) ? $errors['warning_count'] : 0;
                $errorCount = is_array($errors) ? $errors['error_count'] : 0;

                if (!$dt || $warningCount > 0 || $errorCount > 0) {
                    $this->fail($label . ' must be a valid date.');
                }

                $value = $dt->format('Y-m-d');
            } else {
                $value = strtoupper((string) $value);

                if (!empty($rule['max']) && strlen($value) > (int) $rule['max']) {
                    $this->fail($label . ' is too long.');
                }
            }

            if (!empty($rule['options']) && !in_array($value, $rule['options'], true)) {
                $this->fail($label . ' has invalid value.');
            }

            $data[$name] = $value;
        }

        return $data;
    }

    protected function validateCsrfOnly(): void
    {
        $this->validateCsrfToken((string) ($_POST['csrf_token'] ?? ''));
    }

    protected function validateCsrfToken(string $token): void
    {
        if (!Csrf::validate($token, 'keep')) {
            $this->fail('Invalid CSRF token.');
        }
    }

    protected function failOnValidationErrors(array $errors): void
    {
        if (!empty($errors)) {
            $this->fail($errors[0]);
        }
    }
    protected function fail(string $message): void
    {
        je([
            'success' => false,
            'message' => $message,
            'type' => 'error'
        ], 'json');
    }
}

