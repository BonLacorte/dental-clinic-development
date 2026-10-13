<?php

require_once __DIR__ . '/../../models/hris/Position.php';

class PositionsController extends Controller
{
    protected $positionModel;

    public function __construct()
    {
        $this->positionModel = new Position();
    }

    public function index()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $csrf_token = Csrf::generate();
        $data = [
            'title'      => 'Positions',
            'name'       => $_SESSION['user']['name'],
            'csrf_token' => $csrf_token
        ];

        $this->view('module_hris/Position', $data);
    }

    public function getAllPositions()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];

        echo json_encode([
            'success' => true,
            'data'    => $this->positionModel->getAllPositions($tenant_id)
        ]);
    }

    public function getPositionById(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $position = $this->positionModel->getPositionById($id, $tenant_id);

        echo json_encode([
            'success' => (bool) $position,
            'data'    => $position,
            'message' => $position ? null : 'Position not found'
        ]);
    }

    public function create()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];

        $rules = [
            'name'       => ['type' => 'string', 'required' => true],
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

        $newId = $this->positionModel->createPosition($tenant_id, $data);

        echo json_encode([
            'success' => true,
            'message' => 'Position created successfully',
            'id'      => $newId
        ]);
    }

    public function update(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];

        $rules = [
            'name'       => ['type' => 'string', 'required' => true],
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

        $success = $this->positionModel->updatePosition($id, $tenant_id, $data);

        echo json_encode([
            'success' => $success,
            'message' => $success
                ? 'Position updated successfully'
                : 'Failed to update position'
        ]);
    }

    public function delete(int $id)
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

        $success = $this->positionModel->deletePosition($id, $tenant_id);

        echo json_encode([
            'success' => $success,
            'message' => $success
                ? 'Position deleted successfully'
                : 'Failed to delete position'
        ]);
    }
}