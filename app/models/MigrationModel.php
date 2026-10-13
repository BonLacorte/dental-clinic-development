<?php
class MigrationModel extends Model
{
    public function __construct()
    {
        parent::__construct('default');
    }

    // Add a new user
    public function addUser($data)
    {
        $stmt = $this->db->prepare("INSERT INTO users (tenant_id, name, email, password) 
            VALUES (:tenant_id, :name, :email, :password)");
        return $stmt->execute([
                'tenant_id'     => $data['tenant_id'], 
                'name'          => $data['name'], 
                'email'         => $data['email'],
                'password'      => $data['password']
            ]);
    }

    public function findDefaultUser()
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id AND name = :name");
        $stmt->execute(['id' => 1, 'name' => 'sadmindev']);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createTenantWithUser($tenantData, $userData)
    {
        try {
            $this->db->beginTransaction();

            /// INSERT TENANT
            $stmt = $this->db->prepare("
                INSERT INTO tenants (name, code, contact_person, email, contact_no, address, status, created_at, is_default)
                VALUES (:name, :code, :contact_person, :email, :contact_no, :address, :status, :created_at, :is_default)
            ");

            $stmt->execute([
                'name'              => $tenantData['name'],
                'code'              => $tenantData['code'],
                'contact_person'    => $tenantData['contact_person'],
                'email'             => $tenantData['email'],
                'contact_no'        => $tenantData['contact_no'],
                'address'           => $tenantData['address'],
                'status'            => 'ACTIVE',
                'created_at'        => date('Y-m-d H:i:s'),
                'is_default'        => 1
            ]);

            $tenant_id = $this->db->lastInsertId();

            /// INSERT USER (ADMIN)
            $stmt = $this->db->prepare("
                INSERT INTO users (tenant_id, name, email, contact_no, password, created_at, is_default)
                VALUES (:tenant_id, :name, :email, :contact_no, :password, :created_at, :is_default)
            ");

            $stmt->execute([
                'tenant_id'     => $tenant_id,
                'name'          => $userData['name'],
                'email'         => $userData['email'],
                'contact_no'    => $userData['contact_no'],
                'password'      => $userData['password'],
                'created_at'    => date('Y-m-d H:i:s'),
                'is_default'    => 1
            ]);

            /// COMMIT
            $this->db->commit();
            return $tenant_id;

        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
}