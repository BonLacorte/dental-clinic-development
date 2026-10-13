<?php

class TenantModuleModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct('default');
    }

    public function getModulesByTenant(int $tenantId): array
    {
        $sql = "
            SELECT m.code
            FROM tenant_modules tm
            INNER JOIN modules m ON m.id = tm.module_id
            WHERE tm.tenant_id = :tenant_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tenant_id' => $tenantId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}