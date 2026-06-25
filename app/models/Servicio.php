<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Servicio
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene todos los servicios disponibles
     */
    public function getAll()
    {
        $query = "SELECT * FROM servicio ORDER BY nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
