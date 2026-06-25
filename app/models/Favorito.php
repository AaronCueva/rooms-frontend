<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Favorito
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene los usuarios que marcaron un alojamiento como favorito
     */
    public function getByAlojamientoId($alojamiento_id)
    {
        $query = "SELECT f.*, u.nombres, u.apellido_paterno, u.correo, u.celular,
                         un.nombre as universidad_nombre
                  FROM favorito f
                  INNER JOIN usuario u ON f.usuario_id = u.usuario_id
                  LEFT JOIN universidad un ON u.universidad_id = un.universidad_id
                  WHERE f.alojamiento_id = :alojamiento_id
                  ORDER BY f.creado DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':alojamiento_id', $alojamiento_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
