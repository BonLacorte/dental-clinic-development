# Controllers Guide (MVCLite)

## Purpose
Controllers handle request logic and return responses (HTML or JSON).

---

## Basic Structure

Each controller is a class:

```php
require_once __DIR__ . '/../models/UserModel.php';

class UserController extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $users = $this->userModel->getAllUsers();
        echo json_encode($users);
        exit;
    }

    // With Parameters
    // Route:
    // $app->get('/users/{id}', 'UserController@show');
    public function show($params)
    {
        $id = $params['id'];
    }
}


---

# Model Guide

```md id="doc3"
# Models Guide (MVCLite)

## Purpose
Models handle database operations and business logic.
Configure database in config/config.php (can multi-db)
---

## Basic Structure

```php
class UserModel extends Model
{
    public function __construct()
    {
        parent::__construct('name'); //check config.php
    }

    // Get all users
    public function getAllUsers()
    {
        $stmt = $this->db->query("SELECT id, roleid, email FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalUsers()
    {
        $stmt = $this->db->prepare("SELECT count(*) as total FROM users where sstatus = :sstatus and allowlogon = :allowlogon");
        $stmt->execute(['sstatus' => 'ACTIVE', 'allowlogon' => 'YES']);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Get a single user by ID
    public function getUserById($userid)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE userid = :id");
        $stmt->execute(['id' => $userid]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}