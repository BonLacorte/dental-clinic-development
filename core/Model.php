<?php
class Model
{
    protected $db;

    // Optional $dbName parameter to select a database config
    public function __construct($dbName = 'default')
    {
        $configs = require __DIR__ . '/../config/config.php';
        $localConfig = __DIR__ . '/../config/local.php';
        if (is_file($localConfig)) {
            $configs = array_replace_recursive($configs, require $localConfig);
        }

        if (!isset($configs[$dbName])) {
            die("DB Error: Database configuration '$dbName' not found.");
        }

        $config = $configs[$dbName];

        try {
            $this->db = new PDO(
                "mysql:host={$config['host']};port=" . ($config['port'] ?? 3306) . ";dbname={$config['dbname']};charset=utf8mb4",
                $config['user'],
                $config['pass']
            );
            // Optional: set error mode
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            die("DB Error: " . $e->getMessage());
        }
    }
}
