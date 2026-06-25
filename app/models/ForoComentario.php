<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class ForoComentario {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene los comentarios de un foro específico.
     * Incluye los deshabilitados (eliminados lógicamente) para que el
     * administrador pueda verlos y restaurarlos. Los activos aparecen primero.
     */
    public function getByForoId($foro_id) {
        $query = "SELECT fc.*, 
                         u.nombres, u.apellido_paterno, u.correo,
                         u.usuario_id as comentarista_id,
                         u.habilitado as comentarista_habilitado
                  FROM foro_comentario fc
                  LEFT JOIN usuario u ON fc.usuario_id = u.usuario_id
                  WHERE fc.foro_id = :foro_id
                  ORDER BY fc.habilitado DESC, fc.fecha_envio ASC";
                  
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':foro_id', $foro_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Elimina lógicamente un comentario (soft delete).
     * Marca habilitado = false en lugar de borrar el registro.
     */
    public function eliminar($comentario_id) {
        $query = "UPDATE foro_comentario SET habilitado = false WHERE foro_comentario_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $comentario_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Restaura un comentario eliminado lógicamente (habilitado = true).
     */
    public function restaurar($comentario_id) {
        $query = "UPDATE foro_comentario SET habilitado = true WHERE foro_comentario_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $comentario_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
