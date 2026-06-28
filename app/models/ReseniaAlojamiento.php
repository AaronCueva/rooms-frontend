<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class ReseniaAlojamiento {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene las reseñas directas de un alojamiento
     */
    public function getByAlojamientoId($alojamiento_id) {
        $query = "
            SELECT r.*, 
                   u.nombres, u.apellido_paterno, u.correo, u.url_foto AS usuario_foto
            FROM resenia_alojamiento r
            INNER JOIN usuario u ON r.estudiante_id = u.usuario_id
            WHERE r.alojamiento_id = :alojamiento_id
            ORDER BY r.creado DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':alojamiento_id' => $alojamiento_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $query = "SELECT * FROM resenia_alojamiento WHERE resenia_alojamiento_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function toggleEstado($id) {
        $query = "UPDATE resenia_alojamiento SET habilitado = NOT habilitado, modificado = CURRENT_TIMESTAMP WHERE resenia_alojamiento_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
    }
}
