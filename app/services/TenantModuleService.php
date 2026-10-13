<?php

class TenantModuleService
{
    protected $model;

    public function __construct()
    {
        $this->model = new TenantModuleModel();
    }

    public function getTenantModuleMap(int $tenantId): array
    {
        $modules = $this->model->getModulesByTenant($tenantId);

        return array_flip($modules); // O(1) lookup map
    }

    public function isEnabled(int $tenantId, string $moduleCode): bool
    {
        $modules = $this->model->getModulesByTenant($tenantId);

        return in_array($moduleCode, $modules, true);
    }
}