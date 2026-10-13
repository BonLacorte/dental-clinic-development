<?php
require_once __DIR__ . '/../models/UserModel.php'; // adjust path
require_once __DIR__ . '/../models/UserModelDataTables.php';
require_once __DIR__ . '/../models/BranchModel.php';

class UserController extends Controller
{
    protected $userModel;
    protected $userModelDataTables;
    protected $branchModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->userModelDataTables = new UserModelDataTables();
        $this->branchModel = new BranchModel();
    }

    public function index()
    {
        $tenant_id = $_SESSION['user']['tenant_id']; // important
        $csrf_token = Csrf::generate();
        $roles = $this->userModel->getRoles($tenant_id);
        $branches = $this->branchModel->getAllBranches($tenant_id, 10000, 0);
        $data = [
            'title' => 'Users Page',
            'userid' => $_SESSION['user']['id'],
            'roleid' => $_SESSION['user']['roles'][0] ?? '',
            'name' => $_SESSION['user']['name'],
            'roles' => $roles,
            'branches' => $branches,
            'csrf_token' => $csrf_token
        ];
        $this->view('users', $data);
    }

    public function getAllTenantUsers()
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

        $totalRows = $this->userModel->getTotalTenantUsers($tenant_id);
        $totalPages = ceil($totalRows / $limit);

        $data = $this->userModel->getAllTenantUsers($tenant_id, $limit, $offset);

        echo json_encode([
            'data' => $data,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
        exit;
    }

    // public function getUsersDataTables() 
    // {
    //     header('Content-Type: application/json');

    //     $draw = $_GET['draw'] ?? 1;
    //     $start = $_GET['start'] ?? 0;
    //     $length = $_GET['length'] ?? 10;
    //     $search = $_GET['search']['value'] ?? '';
    //     $orderColumnIndex = $_GET['order'][0]['column'] ?? 0;
    //     $orderDir = $_GET['order'][0]['dir'] ?? 'ASC';

    //     $columns = ['userid', 'roleid', 'email'];
    //     $orderColumn = $columns[$orderColumnIndex] ?? 'userid';

    //     // create custom 'where' condition here (based on scope and/or role heiarchy).

    //     $data = $this->userModelDataTables->getUsersData($start, $length, $search, $orderColumn, $orderDir);
    //     $recordsFiltered = $this->userModelDataTables->getFilteredCount($search);
    //     $recordsTotal = $this->userModelDataTables->getTotalUsers();

    //     echo json_encode([
    //         'draw' => (int)$draw,
    //         'recordsTotal' => $recordsTotal,
    //         'recordsFiltered' => $recordsFiltered,
    //         'data' => $data
    //     ]);
    //     exit;
    // }

    public function addUser()
    {
        $tenant_id = $_SESSION['user']['tenant_id'];
        $rules = [
            'name' => ['type' => 'string', 'required' => true],
            'email' => ['type' => 'email', 'required' => true],
            'contact' => ['type' => 'string', 'required' => true],
            'role' => ['type' => 'int', 'required' => true],
            'csrf_token' => ['type' => 'string', 'required' => true]
        ];

        $branches = $_POST['branch'] ?? [];

        // branch array
        if (!is_array($branches)) {
            $branches = [];
        }
        // Keep only valid integers
        $branches = array_map('intval', $branches);
        $branches = array_filter($branches, fn($id) => $id > 0);

        $result = validate($_POST, $rules);
        $skipUppercase = [
            'email',
            'contact',
            'role',
            'branch',
            'csrf_token'
        ];
        $data = uppercaseData($result['data'], $skipUppercase);

        if (Csrf::validate($data['csrf_token'], 'keep')) {
            $message = "Invalid CSRF token";
            je([
                'message' => $message,
                'type' => 'error'
            ], 'json');
            exit;
        }

        if ($this->userModel->emailExists($tenant_id, $data['email'])) {
            je([
                'error' => 'Email address is already in use.'
            ], 'json');
            exit;
        }

        $password = Crypto::encrypt("changeM3!");
        $data = array_merge($data, [
            'tenant_id' => $tenant_id,
            'password' => $password,
            'branches' => $branches
        ]);

        try {
            $userId = $this->userModel->addUser($data);
            echo json_encode([
                'message' => 'User added successfully.',
                'user_id' => $userId
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                'error' => 'Unable to add user.'
            ]);
        }
        //$addUser = $this->userModel->addUser($data);
        //echo json_encode([
        //    'data' => $data
        //]);
        exit;
    }

    public function editUser()
    {
        $id = (int)($_GET['id'] ?? '');
        $csrf_token = (string)($_GET['csrf_token'] ?? 0);
        if (!Csrf::validate($csrf_token, 'keep')) {
            $message = "Invalid CSRF token";
            je([
                'error' => $message
            ], 'json');
            exit;
        }

        $data = $this->userModel->getUserById($id);
        // $userBranches = $this->userModel->getUserBranches($id);

        // check type of role in db, can/may be moved to the services module
        // count role entries by the user; if only one entry, check if branch_id have value (if null, user access all branches)
        // this unusual logic (inserting one entry with null branch_id on global access) is due to laziness 
        // (should insert all user and all branch to user_branches table on user creation if not feeling lazy)
        $userRoles = $this->userModel->getUserModuleRoles($id);
        $roleCount = count($userRoles ?? []);
        $roleMode = 'Unique';
        if ($roleCount == 1) {
            if ($userRoles['branch_id'] == null) {
                $roleMode = 'Global';
            }
        }
        //

        //$data = array_merge($data, ['roles' => $userRoles]);
        //$data = ['id' => $id, 'csrf_token' => $csrf_token];
        echo json_encode([
            'data' => $data,
            'userRoles' => $userRoles,
            'roleMode' => $roleMode,
            'message' => 'Edit user module.'
        ]);
    }

    public function updateUser() {}

    public function editUserBranch()
    {
        $id = (int)($_GET['id'] ?? '');
        $csrf_token = (string)($_GET['csrf_token'] ?? 0);
        if (!Csrf::validate($csrf_token, 'keep')) {
            $message = "Invalid CSRF token";
            je([
                'error' => $message
            ], 'json');
            exit;
        }
        $data = $this->userModel->getUserBranches($id);
        echo json_encode([
            'data' => $data,
            'message' => 'Edit user module.'
        ]);
    }

    // used to get branches that user have access // null = no access
    // public function countUserBranches()
    // {
    //     $userId = (int)($_GET['userid'] ?? '');
    //     $branches = $this->userModel->getUserBranches($userId);
    //     $branchCount = count($branches ?? []);
    //     echo json_encode([
    //         'userBranchCount' => $branchCount
    //     ]);
    // }

    public function updateUserBranch()
    {
        // always delete (where user_id = ) before insert if any
        // no insert = access to all branches
    }

    public function getUserBranchById()
    {
        $branchId = (int)($_GET['branchId'] ?? '');
        $userId = (int)($_GET['userId'] ?? '');

        $data = $this->userModel->getUserBranchById($userId, $branchId); // check access first

        echo json_encode([
            'data' => $data
        ]);
    }

    public function getTotalUsers()
    {
        $totalUsers = $this->userModel->getTotalUsers();
        echo json_encode(['total' => (int) $totalUsers]);
    }
}
