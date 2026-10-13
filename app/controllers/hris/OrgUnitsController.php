<?php

require_once __DIR__ . '/../../models/hris/OrgUnit.php';

class OrgUnitsController extends Controller
{
    protected $orgUnit;

    public function __construct()
    {
        $this->orgUnit = new OrgUnit();
    }

    public function index()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $csrf_token = Csrf::generate();
        $data = [
            'title'      => 'Organizational Units',
            'name'       => $_SESSION['user']['name'],
            'csrf_token' => $csrf_token
        ];

        $this->view('module_hris/OrgUnits', $data);
    }

    public function getAllUnits()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];

        echo json_encode([
            'success' => true,
            'data'    => $this->orgUnit->getAllOrgUnits($tenant_id)
        ]);
    }

    public function getUnitById(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $unit = $this->orgUnit->getOrgUnitById($id, $tenant_id);

        echo json_encode([
            'success' => (bool) $unit,
            'data'    => $unit,
            'message' => $unit ? null : 'Organizational unit not found'
        ]);
    }

    public function getUnitEmployees(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $unit = $this->orgUnit->getOrgUnitById($id, $tenant_id);

        if (!$unit) {
            echo json_encode([
                'success' => false,
                'message' => 'Organizational unit not found'
            ]);
            return;
        }

        $employees = $this->orgUnit->getEmployeesInUnit($id, $tenant_id);

        echo json_encode([
            'success'  => true,
            'unit'     => $unit,
            'data'     => $employees
        ]);
    }

    public function create()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];

        $rules = [
            'name'       => ['type' => 'string', 'required' => true],
            'code'       => ['type' => 'string', 'required' => true],
            'type'       => ['type' => 'string', 'required' => true],
            'parent_id'  => ['type' => 'int',    'required' => false],
            'csrf_token' => ['type' => 'string', 'required' => true]
        ];

        $result = validate($_POST, $rules);
        $data = uppercaseData($result['data'], ['csrf_token', 'name']);

        if (!Csrf::validate($data['csrf_token'], 'keep')) {
            je([
                'message' => 'Invalid CSRF token',
                'type'    => 'error'
            ], 'json');
            exit;
        }

        $data['code'] = $this->buildCode($data['parent_id'] ?? null, $data['code'], $tenant_id);
        $newId = $this->orgUnit->createOrgUnit($data, $tenant_id);

        echo json_encode([
            'success' => true,
            'message' => 'Organizational unit created successfully',
            'id'      => $newId
        ]);
    }

    public function updateOrgUnit(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];

        $rules = [
            'name'       => ['type' => 'string', 'required' => true],
            'code'       => ['type' => 'string', 'required' => true],
            'type'       => ['type' => 'string', 'required' => true],
            'csrf_token' => ['type' => 'string', 'required' => true]
            // 'parent_id' => ['type' => 'int', 'required' => false]
        ];

        $result = validate($_POST, $rules);
        $data = uppercaseData($result['data'], ['csrf_token', 'name']);

        if (!Csrf::validate($data['csrf_token'], 'keep')) {
            je([
                'message' => 'Invalid CSRF token',
                'type'    => 'error'
            ], 'json');
            exit;
        }

        $data['code'] = $this->buildCode($data['parent_id'] ?? null, $data['code'], $tenant_id);

        $success = $this->orgUnit->updateOrgUnit($id, $data, $tenant_id);

        echo json_encode([
            'success' => $success,
            'message' => $success
                ? 'Organizational unit updated successfully'
                : 'Failed to update organizational unit'
        ]);
    }

    public function deleteOrgUnit(int $id)
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
                'type'    => 'error'
            ], 'json');
            exit;
        }

        $success = $this->orgUnit->deleteOrgUnit($id, $tenant_id);

        echo json_encode([
            'success' => $success,
            'message' => $success
                ? 'Organizational unit deleted successfully'
                : 'Failed to delete organizational unit'
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */
    private function buildCode(?int $parent_id, string $input_code, int $tenant_id): string
    {
        $input_code = strtoupper(trim($input_code));

        if (!$parent_id) {
            return $input_code;
        }

        $parent = $this->orgUnit->getOrgUnitById($parent_id, $tenant_id);

        return $parent ? $parent['code'] . '-' . $input_code : $input_code;
    }
}