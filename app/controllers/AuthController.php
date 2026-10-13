<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/TenantModel.php';

class AuthController extends Controller
{
    protected $userModel;
    protected $tenantModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->tenantModel = new TenantModel();
    }

    public function login()
    {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user'])) {
            header("Location: /home");
            exit;
        }

        $data = [
            'title' => 'Login Page'
        ];
        $this->view('login', $data);
    }

    public function loginPost()
    {
        $email = (string) ($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        $csrfToken = $_POST['csrf_token'] ?? '';
        
        ///// CSRF VALIDATION
        if (!Csrf::validate($csrfToken)) {
            echo "<script>
                    alert('Invalid CSRF token');
                    window.location.href = '/';
                </script>";
            exit;
        }
        Csrf::destroy();

        ///// FIND USER
        $user = $this->userModel->findByEmail($email);

        ///// LOGIN FAILED
        if ( !$user || !Crypto::passwordDecrypt($password, $user['password']) ) {
            // login attempt counter
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
            $_SESSION['last_attempt_time'] = time();
            $message = "Login failed: Invalid credentials";
            echo "<script>
                    alert('{$message}');
                    window.location.href = '/';
                </script>";
            exit;
        }

        ///// LOAD TENANT MODULES, USER ROLES, PERMISSIONS and BRANCHES
        //$modules = $this->tenantModel->getModules( (int) $user['tenant_id'] );
        $moduleList = $this->tenantModel->getModules((int)$user['tenant_id']);
        $roles = $this->userModel->getUserRoles( (int) $user['id']);
        $permissions = $this->userModel->getUserPermissions( (int) $user['id']);
        $userBranches = $this->userModel->getUserBranches((int) $user['id']);

        ///// FAST LOOKUP MAP (O(1))
        $permissionMap = array_flip($permissions); // important for optimized PermissionMiddleware
        $moduleMap = array_flip($moduleList); // optimized module middleware

        ///// check superadmin
        $isSuperAdmin = in_array('Super Admin', $roles, true);

        ///// DEFAULT ACTIVE BRANCH
        $defaultBranchId = !empty($userBranches)
            ? $userBranches[0]   // first assigned branch as default
            : null;

        ///// SESSION Security
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => $user['id'],
            'tenant_id' => $user['tenant_id'] ?? null,
            'name' => $user['name'],
            'email' => $user['email'],
            'contact_no' => $user['contact_no'],

            ///// Role Based Account Control (RBAC)
            'roles' => $roles,
            'permissions' => $permissions,
            //'modules' => $modules,
            'modules' => $moduleMap,

            ///// BRANCH ACCESS CONTROL
            'branches' => $userBranches,
            'default_branch_id' => $defaultBranchId,

            ///// Optimized Lookup
            'permission_map' => $permissionMap,
            'is_super_admin' => $isSuperAdmin
        ];

        ///// OPTIONAL: INITIALIZE ACTIVE CONTEXT
        $_SESSION['tenant'] = [
            'id' => $user['tenant_id'],
            'status' => 'ACTIVE'
        ];

        $_SESSION['branch'] = [
            'id' => $defaultBranchId,
            'scope' => $isSuperAdmin ? 'ALL' : (count($userBranches) > 1 ? 'MULTIPLE' : 'SINGLE'),
            'allowed' => $userBranches
        ];

        //// Reset Login Attempts
        unset(
            $_SESSION['login_attempts'],
            $_SESSION['last_attempt_time']
        );

        header("Location: /home");
        exit;
    }

    public function logout()
    {
        unset($_SESSION['user']);
        Csrf::destroy();
        session_destroy();
        header("Location: /");
        exit;
    }

    public function keepAlive()
    {
        session_start(); // This refreshes the session
        // Return nothing or a simple 200 status
    }
}