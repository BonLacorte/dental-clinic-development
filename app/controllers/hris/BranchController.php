<?php

require_once __DIR__ . '/../../models/hris/BranchModel.php';

class BranchController extends Controller
{
    protected $branchModel;

    public function __construct()
    {
        $this->branchModel = new BranchModel();
    }

    public function index()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $csrf_token = Csrf::generate();
        $data = [
            'title' => 'Employee Branches',
            'name' => $_SESSION['user']['name'],
            'csrf_token' => $csrf_token
        ];

        $this->view('module_hris/branches', $data);
    }

    public function main()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $csrf_token = Csrf::generate();
        $data = [
            'title' => 'Branches',
            'name' => $_SESSION['user']['name'],
            'csrf_token' => $csrf_token
        ];

        $this->view('branches', $data);
    }

    public function getAllBranches()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];

        $rules = [
            'page' => ['type' => 'int', 'required' => false]
        ];

        $result = validate($_GET, $rules);
        $result = $result['data'];
        $page = $result['page'] ?: 1;

        $limit = 10;
        $offset = ($page - 1) * $limit;

        $totalRows = $this->branchModel->getTotalBranches($tenant_id);
        $totalPages = ceil($totalRows / $limit);

        $data = $this->branchModel->getAllBranches($tenant_id, $limit, $offset);

        echo json_encode([
            'success' => true,
            'data' => $data,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
        exit;
    }

    public function getBranchById(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];

        $data = $this->branchModel->getBranchById($id, $tenant_id);

        echo json_encode([
            'success' => (bool) $data,
            'data' => $data,
            'message' => $data ? null : 'Branch not found'
        ]);
    }

    public function create()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];

        $rules = [
            'name' => ['type' => 'string', 'required' => true],
            'code' => ['type' => 'string', 'required' => true],
            'address' => ['type' => 'string', 'required' => true],
            'csrf_token' => ['type' => 'string', 'required' => true]
        ];

        $result = validate($_POST, $rules);

        $data = uppercaseData($result['data'], ['csrf_token']);

        if (!Csrf::validate($data['csrf_token'], 'keep')) {
            je([
                'message' => 'Invalid CSRF token',
                'type' => 'error'
            ], 'json');
            exit;
        }

        $newId = $this->branchModel->createBranch($data, $tenant_id);

        echo json_encode([
            'success' => true,
            'message' => 'Branch created successfully',
            'id' => $newId
        ]);
    }

    public function updateBranch(int $id)
    {
        $tenant_id = $_SESSION['user']['tenant_id'];

        $rules = [
            'name' => ['type' => 'string', 'required' => true],
            'code' => ['type' => 'string', 'required' => true],
            'address' => ['type' => 'string', 'required' => true],
            'csrf_token' => ['type' => 'string', 'required' => true]
        ];

        $result = validate($_POST, $rules);
        $data = uppercaseData($result['data'], ['csrf_token']);

        if (!Csrf::validate($data['csrf_token'], 'keep')) {
            je([
                'message' => 'Invalid CSRF token',
                'type' => 'error'
            ], 'json');
            exit;
        }

        $success = $this->branchModel->updateBranch($id, $data, $tenant_id);

        echo json_encode([
            'success' => $success,
            'message' => $success
                ? 'Branch updated successfully'
                : 'Failed to update branch'
        ]);
    }

    public function deleteBranch(int $id)
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

        $success = $this->branchModel->deleteBranch($id, $tenant_id);

        echo json_encode([
            'success' => $success,
            'message' => $success
                ? 'Branch deleted successfully'
                : 'Failed to delete branch'
        ]);
    }
}