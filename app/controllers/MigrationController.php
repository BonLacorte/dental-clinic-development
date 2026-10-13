<?php
require_once __DIR__ . '/../models/MigrationModel.php'; 

class MigrationController extends Controller
{
    protected $migrationModel;

    public function __construct()
    {
        $this->migrationModel = new MigrationModel();
    }

    /// create initial/default user (for devs/sadmins)
    public function sadmin()
    {
        $bootstrap_username = 'sadmindev';
        $secretsPath = __DIR__ . '/../../config/secrets.php';
        $secrets = is_file($secretsPath) ? require $secretsPath : [];
        $bootstrap_password = getenv('MONTERO_BOOTSTRAP_PASSWORD') ?: ($secrets['bootstrap_password'] ?? '');
        if ($bootstrap_password === '') {
            http_response_code(503);
            exit('Configure a bootstrap password before initializing the system.');
        }

        /// Request Method Check
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo '
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <br><br>

                <input type="password" name="password" placeholder="Password" required>
                <br><br>

                <button type="submit">Initialize System</button>
            </form>
            ';
            exit;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if ( $username !== $bootstrap_username || $password !== $bootstrap_password ) {
            http_response_code(403);
            exit('Invalid credentials.');
        }
        
        /// Check Existing Default User
        $existing = $this->migrationModel->findDefaultUser();
        if ($existing) {
            exit('System already initialized.');
        }

        $tenantData = [
            "name"              => "System",
            "code"              => "SYSTEM",
            "contact_person"    => "System Administrator",
            "email"             => "admin@example.test",
            "contact_no"        => "",
            "address"           => ""
        ];

        $userPassword = password_hash($bootstrap_password, PASSWORD_DEFAULT);
        $userData = [
            "name"          => "sadmindev",
            "email"         => "sadmindev@example.test",
            "contact_no"    => "",
            'password'      => $userPassword
        ];

        /// Begin Transaction
        $result = $this->migrationModel->createTenantWithUser($tenantData, $userData);

        if (!$result) {
            exit("Setup failed");
        }
        //var_dump($userData);
        echo "Success";
    }
}
