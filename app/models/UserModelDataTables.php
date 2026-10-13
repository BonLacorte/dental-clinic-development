<?php
class UserModelDataTables extends Model
{
    public function __construct()
    {
        parent::__construct('default');
    }

    // Fetch total records count
    public function getTotalUsers() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM users");
        return (int) $stmt->fetchColumn();
    }

    // Fetch filtered data for server-side DataTables
    public function getUsersData($start, $length, $search = '', $orderColumn = 'userid', $orderDir = 'ASC') {
        $sql = "SELECT userid, roleid, email FROM users";
        $params = [];

        if ($search) {
            $sql .= " WHERE userid LIKE :search OR roleid LIKE :search";
            $params[':search'] = "%$search%";
        }

        $sql .= " ORDER BY $orderColumn $orderDir LIMIT :start, :length";
        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Count filtered records
    public function getFilteredCount($search = '') {
        $sql = "SELECT COUNT(*) FROM users";
        $params = [];

        if ($search) {
            $sql .= " WHERE userid LIKE :search OR roleid LIKE :search";
            $params[':search'] = "%$search%";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
}