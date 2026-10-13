<?php
class TenantModel extends Model
{
    public function __construct()
    {
        parent::__construct('default');
    }

    public function getModules(int $tenantId): array
    {
        $stmt = $this->db->prepare("
            SELECT m.code
            FROM tenant_modules tm
            INNER JOIN modules m ON m.id = tm.module_id
            WHERE tm.tenant_id = :tenant_id
            AND tm.deleted_at IS NULL

            /*
            ------------------------------------------------------------------
            FUTURE SUBSCRIPTION LOGIC (NOT ACTIVE YET)
            ------------------------------------------------------------------
            AND (tm.starts_at IS NULL OR tm.starts_at <= NOW())
            AND (tm.ends_at IS NULL OR tm.ends_at >= NOW())
            ------------------------------------------------------------------
            NOTE:
            - starts_at = activation date (future feature)
            - ends_at   = expiration date (future feature)
            - enable only when SaaS subscription engine is active
            ------------------------------------------------------------------
            */
        ");

        $stmt->execute([
            'tenant_id' => $tenantId
        ]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getTenants($limit, $offset)
    {
        $stmt = $this->db->prepare("SELECT * FROM tenants ORDER BY `name` LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalTenants()
    {
        $stmt = $this->db->prepare("SELECT count(*) as total FROM tenants");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}