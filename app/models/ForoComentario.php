<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class ForoComentario {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getByForoId($foro_id) {
        $query = "SELECT fc.*, 
                         u.nombres, u.apellido_paterno, u.correo,
                         u.usuario_id as comentarista_id,
                         u.habilitado as comentarista_habilitado
                  FROM foro_comentario fc
                  LEFT JOIN usuario u ON fc.usuario_id = u.usuario_id
                  WHERE fc.foro_id = :foro_id
                  ORDER BY fc.comentario_padre_id IS NULL DESC, fc.fecha_envio ASC";
                  
        $stmt = $this->db->prepare($query);
        $stmt->execute([':foro_id' => $foro_id]);
        
        return $stmt->fetchAll();
    }

    public function eliminar($comentario_id) {
        $query = "UPDATE foro_comentario SET habilitado = false WHERE foro_comentario_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $comentario_id]);
        return $stmt->rowCount() > 0;
    }

    public function restaurar($comentario_id) {
        $query = "UPDATE foro_comentario SET habilitado = true WHERE foro_comentario_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $comentario_id]);
        return $stmt->rowCount() > 0;
    }

    public function actualizar($comentario_id, $mensaje) {
        $query = "UPDATE foro_comentario SET mensaje = :mensaje, modificado = NOW() WHERE foro_comentario_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':mensaje' => $mensaje, ':id' => $comentario_id]);
        return $stmt->rowCount() > 0;
    }
}
