<?php
require_once __DIR__ . '/../models/TenantModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class TenantController extends Controller
{
    protected $userModel;
    protected $tenantModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->tenantModel = new TenantModel();
    }

    public function index()
    {
        $tenant_id = $_SESSION['user']['tenant_id']; // important
        $csrf_token = Csrf::generate();
        $data = [
            'title' => 'Tenants Page', 
            'userid' => $_SESSION['user']['id'], 
            'roleid' => $_SESSION['user']['roles'][0] ?? '',
            'name' => $_SESSION['user']['name'],
            'csrf_token' => $csrf_token
        ];
        $this->view('tenants', $data);
    }

    public function getTenants()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $rules = [
            'page' => ['type' => 'int', 'required' => false]
        ];
        $result = validate($_GET, $rules);
        $result = $result['data'];
        $page = $result['page'];
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $totalRows = $this->tenantModel->getTotalTenants();
        $totalPages = ceil($totalRows / $limit);
        
        $data = $this->tenantModel->getTenants($limit, $offset);

        echo json_encode([
            'data' => $data,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
        exit;
    }

}
