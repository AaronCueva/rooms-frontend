<?php
namespace App\Models;

use App\Core\Database;

class Multimedia {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getByForoId($foro_id) {
        $query = "SELECT * FROM multimedia WHERE foro_id = :foro_id AND habilitado = true ORDER BY orden ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':foro_id' => $foro_id]);
        return $stmt->fetchAll();
    }
}
