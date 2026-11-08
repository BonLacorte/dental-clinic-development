<?php

require_once __DIR__ . '/../../models/hris/EmployeeFinancialsModel.php';
require_once __DIR__ . '/../../services/EmployeeFinancials.php';

class EmployeeFinancialsController extends Controller
{
    protected $model;

    public function __construct()
    {
        $this->model = new EmployeeFinancialsModel();
    }

    private function positiveId($value): int
    {
        $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id === false) {
            $this->fail('Record not found or outside your access.', 404);
        }
        return $id;
    }

    private function context($employeeId, string $category): array
    {
        $definitions = EmployeeFinancials::definitions();
        if (!isset($definitions[$category])) {
            $this->fail('Payroll item category not found.', 404);
        }
        $employeeId = $this->positiveId($employeeId);
        $user = $_SESSION['user'];
        $tenantId = (int) $user['tenant_id'];
        $branches = !empty($user['is_super_admin']) ? [] : ($user['branches'] ?? []);
        if (!$this->model->employeeExists($employeeId, $tenantId, $branches)) {
            $this->fail('Employee not found or outside your access.', 404);
        }
        return [$definitions[$category], $employeeId, $tenantId, $branches];
    }

    public function records($employeeId, string $category): void
    {
        [$definition, $employeeId, $tenantId, $branches] = $this->context($employeeId, $category);
        $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;
        try {
            $list = $this->model->records($definition, $employeeId, $tenantId, $branches, $page);
            je(array_merge(['success' => true, 'types' => $this->model->types($definition, $tenantId)], $list), 'json');
        } catch (PDOException $error) {
            $this->fail('Financial records could not be loaded. Please try again.', 500);
        }
    }

    public function record($employeeId, string $category, $id): void
    {
        [$definition, $employeeId, $tenantId, $branches] = $this->context($employeeId, $category);
        $record = $this->model->record($definition, $this->positiveId($id), $employeeId, $tenantId, $branches);
        if (!$record) {
            $this->fail('Record not found or outside your access.', 404);
        }
        je(['success' => true, 'data' => $record], 'json');
    }

    public function create($employeeId, string $category): void
    {
        $this->save($employeeId, $category, null);
    }

    public function update($employeeId, string $category, $id): void
    {
        $this->save($employeeId, $category, $this->positiveId($id));
    }

    private function save($employeeId, string $category, ?int $id): void
    {
        $this->validateCsrf();
        [$definition, $employeeId, $tenantId, $branches] = $this->context($employeeId, $category);
        try {
            $data = EmployeeFinancials::validate($definition, $_POST);
            if (isset($definition['typeField'])
                && !$this->model->typeAllowed($definition, $data[$definition['typeField']], $tenantId)) {
                $this->fail('Selected payroll type is invalid for this tenant.', 422);
            }
            $savedId = $this->model->save($definition, $id, $data, $employeeId, $tenantId, $branches);
            if ($savedId === false) {
                $this->fail('Record not found or outside your access.', 404);
            }
            je(['success' => true, 'id' => $savedId, 'message' => $definition['singular'] . ($id === null ? ' created successfully.' : ' updated successfully.')], 'json');
        } catch (InvalidArgumentException $error) {
            $this->fail($error->getMessage(), 422);
        } catch (PDOException $error) {
            $this->fail('The financial record could not be saved. Refresh the list before trying again.', 500);
        }
    }

    public function delete($employeeId, string $category, $id): void
    {
        $this->validateCsrf();
        [$definition, $employeeId, $tenantId, $branches] = $this->context($employeeId, $category);
        try {
            if (!$this->model->deleteRecord($definition, $this->positiveId($id), $employeeId, $tenantId, $branches)) {
                $this->fail('Record not found or outside your access.', 404);
            }
            je(['success' => true, 'message' => $definition['singular'] . ' deleted successfully.'], 'json');
        } catch (PDOException $error) {
            $this->fail('The financial record could not be deleted. Refresh the list before trying again.', 500);
        }
    }

    private function validateCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!is_string($token) || !Csrf::validate($token, 'keep')) {
            $this->fail('Invalid CSRF token. Reload the profile and try again.', 403);
        }
    }

    private function fail(string $message, int $status): void
    {
        http_response_code($status);
        je(['success' => false, 'message' => $message], 'json');
    }
}
