<?php

class Position extends Model
{
    public function __construct()
    {
        parent::__construct('default');
    }

    public function getAllPositions(int $tenant_id): array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM hris_positions
            WHERE tenant_id = :tenant_id
            ORDER BY name ASC
        ");

        $stmt->execute([
            'tenant_id' => $tenant_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPositionById(int $id, int $tenant_id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM hris_positions
            WHERE id = :id
                AND tenant_id = :tenant_id
            LIMIT 1
        ");

        $stmt->execute([
            'id'        => $id,
            'tenant_id' => $tenant_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createPosition(int $tenant_id, array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO hris_positions (tenant_id, name, created_at)
            VALUES (:tenant_id, :name, NOW())
        ");

        $stmt->execute([
            'tenant_id' => $tenant_id,
            'name'      => $data['name']
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updatePosition(int $id, int $tenant_id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE hris_positions
            SET
                name = :name
            WHERE id = :id
                AND tenant_id = :tenant_id
        ");

        return $stmt->execute([
            'id'        => $id,
            'tenant_id' => $tenant_id,
            'name'      => $data['name']
        ]);
    }

    public function deletePosition(int $id, int $tenant_id): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM hris_positions
            WHERE id = :id
                AND tenant_id = :tenant_id
        ");

        return $stmt->execute([
            'id'        => $id,
            'tenant_id' => $tenant_id
        ]);
    }
}