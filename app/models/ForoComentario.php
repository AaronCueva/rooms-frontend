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
     * Obtiene los comentarios de un foro específico
     */
    public function getByForoId($foro_id) {
        // Obtenemos los comentarios junto con los datos del usuario que lo creó
        $query = "SELECT fc.*, 
                         u.nombres, u.apellido_paterno, u.correo
                  FROM foro_comentario fc
                  LEFT JOIN usuario u ON fc.usuario_id = u.usuario_id
                  WHERE fc.foro_id = :foro_id
                  ORDER BY fc.fecha_envio ASC";
                  
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':foro_id', $foro_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Elimina un comentario (Soft delete si agregamos habilitado, 
     * o Hard delete como se muestra a continuación ya que el ER original no lo tiene)
     */
    public function eliminar($comentario_id) {
        $query = "DELETE FROM foro_comentario WHERE foro_comentario_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $comentario_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
