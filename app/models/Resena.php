<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Resena {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene las reseñas de un alojamiento (a través del contrato -> reserva -> alojamiento)
     */
    public function getByAlojamientoId($alojamiento_id) {
        $query = "
            SELECT r.*, 
                   c.contrato_id, c.fecha_inicio AS contrato_fecha_inicio, c.fecha_fin AS contrato_fecha_fin,
                   res.reserva_id, 
                   u.nombres, u.apellido_paterno, u.correo, u.url_foto AS usuario_foto
            FROM resena r
            INNER JOIN contrato c ON r.contrato_id = c.contrato_id
            INNER JOIN reserva res ON c.reserva_id = res.reserva_id
            INNER JOIN usuario u ON res.usuario_id = u.usuario_id
            WHERE res.alojamiento_id = :alojamiento_id
            ORDER BY r.fecha_creado DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':alojamiento_id' => $alojamiento_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByContratoId($contrato_id) {
        $query = "SELECT * FROM resena WHERE contrato_id = :contrato_id ORDER BY fecha_creado DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':contrato_id' => $contrato_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $query = "SELECT * FROM resena WHERE resena_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function toggleEstado($id) {
        $query = "UPDATE resena SET habilitado = NOT habilitado, modificado = CURRENT_TIMESTAMP WHERE resena_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
    }
}
