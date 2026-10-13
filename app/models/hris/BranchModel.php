<?php

class BranchModel extends Model
{
    public function __construct()
    {
        parent::__construct('default');
    }

    public function getAllBranches(int $tenant_id, int $limit, int $offset): array
    {
        $stmt = $this->db->prepare("
            SELECT id, name, code, address, created_at
            FROM tenant_branches
            WHERE tenant_id = :tenant_id
                AND deleted_at IS NULL
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ");

        $stmt->bindValue(':tenant_id', $tenant_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalBranches(int $tenant_id): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM tenant_branches
            WHERE tenant_id = :tenant_id
                AND deleted_at IS NULL
        ");

        $stmt->execute(['tenant_id' => $tenant_id]);
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getBranchById(int $id, int $tenant_id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM tenant_branches
            WHERE id = :id
                AND tenant_id = :tenant_id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $id,
            'tenant_id' => $tenant_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createBranch(array $data, int $tenant_id): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO tenant_branches (tenant_id, name, code, address, created_at)
            VALUES (:tenant_id, :name, :code, :address, NOW())
        ");

        $stmt->execute([
            'tenant_id' => $tenant_id,
            'name'      => $data['name'],
            'code'      => $data['code'],
            'address'   => $data['address']
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateBranch(int $id, array $data, int $tenant_id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE tenant_branches
            SET
                name       = :name,
                code       = :code,
                address    = :address
            WHERE id        = :id
                AND tenant_id = :tenant_id
        ");

        return $stmt->execute([
            'id'        => $id,
            'tenant_id' => $tenant_id,
            'name'      => $data['name'],
            'code'      => $data['code'],
            'address'   => $data['address']
        ]);
    }

    public function deleteBranch(int $id, int $tenant_id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE tenant_branches
            SET deleted_at = NOW()
            WHERE id        = :id
                AND tenant_id = :tenant_id
        ");

        return $stmt->execute([
            'id' => $id,
            'tenant_id' => $tenant_id
        ]);
    }
}