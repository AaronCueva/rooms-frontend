<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class PoliticaCasa
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene todas las políticas de casa disponibles (para combo)
     */
    public function getAll()
    {
        $query = "SELECT * FROM politica_casa ORDER BY nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Busca una política por su ID
     */
    public function findById($id)
    {
        $query = "SELECT * FROM politica_casa WHERE politica_casa_id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
}
