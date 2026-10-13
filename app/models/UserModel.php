<?php
class UserModel extends Model
{
    public function __construct()
    {
        parent::__construct('default');
    }

    // Get all users
    public function getAllUsers()
    {
        //$stmt = $this->db->query("SELECT userid, roleid, email FROM user ORDER BY seqid ASC");
        $stmt = $this->db->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllTenantUsers($tenant_id, $limit, $offset)
    {
        /// join to `user_sessions` table later ///
        $stmt = $this->db->prepare("SELECT * FROM users WHERE tenant_id = :tenant_id ORDER BY `name` LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':tenant_id', (int) $tenant_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalTenantUsers($tenant_id)
    {
        $stmt = $this->db->prepare("SELECT count(*) as total FROM users WHERE tenant_id = :tenant_id");
        $stmt->execute(['tenant_id' => $tenant_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getTotalUsersByStatus($status)
    {
        $stmt = $this->db->prepare("SELECT count(*) as total FROM users where status = :status");
        $stmt->execute(['status' => $status]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getTotalUsers()
    {
        $stmt = $this->db->prepare("SELECT count(*) as total FROM users");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Get a single user by ID
    public function getUserById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get username (may not be used)
    public function findByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :userid and status = :status LIMIT 1");
        $stmt->execute(['userid' => $username, 'status' => 'ACTIVE']);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get user (Login)
    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email and status = :status LIMIT 1");
        $stmt->execute(['email' => $email, 'status' => 'ACTIVE']);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Add a new user
    // public function addUser($data)
    // {
    //     $stmt = $this->db->prepare("INSERT INTO users (tenant_id, name, email, password) 
    //         VALUES (:tenant_id, :name, :email, :password)");
    //     return $stmt->execute([
    //             'tenant_id'     => $data['tenant_id'], 
    //             'name'          => $data['name'], 
    //             'email'         => $data['email'],
    //             'password'      => $data['password']
    //         ]);
    // }
    public function addUser($data)
    {
        try {
            $this->db->beginTransaction();

            // Insert user
            $stmt = $this->db->prepare("
                INSERT INTO users (tenant_id, name, email, password, contact_no)
                VALUES (:tenant_id, :name, :email, :password, :contact_no)
            ");

            $stmt->execute([
                'tenant_id' => $data['tenant_id'],
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'contact_no' => $data['contact']
            ]);
            $userId = $this->db->lastInsertId();

            // Insert role
            $stmt = $this->db->prepare("
                INSERT INTO user_roles (user_id, role_id)
                VALUES (:user_id, :role_id)
            ");

            $stmt->execute([
                'user_id' => $userId,
                'role_id' => $data['role']
            ]);

            // Insert branches
            if (!empty($data['branches'])) {
                $stmt = $this->db->prepare("
                    INSERT INTO user_branches (user_id, branch_id)
                    VALUES (:user_id, :branch_id)
                ");

                foreach ($data['branches'] as $branchId) {
                    $stmt->execute([
                        'user_id' => $userId,
                        'branch_id' => $branchId
                    ]);
                }
            }

            $this->db->commit();
            return $userId;

        } catch (PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function emailExists($tenantId, $email)
    {
        $stmt = $this->db->prepare("
        SELECT 1
        FROM users
        WHERE tenant_id = :tenant_id
          AND email = :email
        LIMIT 1
    ");

        $stmt->execute([
            'tenant_id' => $tenantId,
            'email' => $email
        ]);

        return (bool) $stmt->fetchColumn();
    }

    /*
    | ROLES
    */
    public function getRoles($tenant_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM roles WHERE tenant_id = :tenant_id AND id > 1");
        $stmt->execute(['tenant_id' => (int) $tenant_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    /*
    |--------------------------------------------------------------------------
    | GET USER ROLES
    |--------------------------------------------------------------------------
    */
    public function getUserRoles(int $userId): array
    {
        $sql = "
            SELECT DISTINCT r.name, r.id, ur.branch_id
            FROM user_roles ur
            INNER JOIN roles r
                ON r.id = ur.role_id
            WHERE ur.user_id = :user_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN); // column value only
        //return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserModuleRoles(int $userId): array
    {
        $sql = "
            SELECT DISTINCT r.name, r.id, ur.branch_id
            FROM user_roles ur
            INNER JOIN roles r
                ON r.id = ur.role_id
            WHERE ur.user_id = :user_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        //return $stmt->fetchAll(PDO::FETCH_COLUMN); // column value only
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | GET USER PERMISSIONS
    |--------------------------------------------------------------------------
    */
    public function getUserPermissions(int $userId): array
    {
        $sql = "
            SELECT DISTINCT p.code
            FROM user_roles ur

            INNER JOIN role_permissions rp
                ON rp.role_id = ur.role_id

            INNER JOIN permissions p
                ON p.id = rp.permission_id

            WHERE ur.user_id = :user_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /*
    |--------------------------------------------------------------------------
    | GET USER BRANCHES
    |--------------------------------------------------------------------------
    */
    public function getUserBranchById(int $userId, int $branchId): array
    {
        $sql = "
            SELECT *
            FROM user_branches
            WHERE user_id = ? AND branch_id = ?
            AND deleted_at IS NULL
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $branchId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getUserBranches(int $userId): array
    {
        $sql = "
            SELECT b.id, b.code, b.name
            FROM user_branches ub
            INNER JOIN tenant_branches b ON b.id = ub.branch_id
            WHERE ub.user_id = ?
            AND b.deleted_at IS NULL
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK SUPER ADMIN (not being used, for reference only. role saved in SESSIONS)
    |--------------------------------------------------------------------------
    */
    public function isSuperAdmin(int $userId): bool
    {
        $sql = "
            SELECT 1
            FROM user_roles ur
            INNER JOIN roles r
                ON r.id = ur.role_id
            WHERE ur.user_id = :user_id
            AND r.name = 'Super Admin'
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return (bool) $stmt->fetchColumn();
    }
}