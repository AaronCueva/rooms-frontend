<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Foro {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene todos los foros con la información del usuario y universidad
     */
    public function getAllForos() {
        $query = "SELECT f.*, 
                         u.nombres, u.apellido_paterno, u.correo,
                         un.nombre as universidad_nombre,
                         c.nombre as categoria_nombre,
                         (SELECT COUNT(*) FROM foro_comentario fc WHERE fc.foro_id = f.foro_id) as total_comentarios,
                         (SELECT COUNT(*) FROM foro_reaccion fr WHERE fr.foro_id = f.foro_id) as total_reacciones
                  FROM foro f
                  LEFT JOIN usuario u ON f.usuario_id = u.usuario_id
                  LEFT JOIN universidad un ON f.universidad_id = un.universidad_id
                  LEFT JOIN catalogo c ON f.categoria_codigo = c.codigo
                  ORDER BY f.fecha_creacion DESC";
                  
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Obtiene un foro específico por su ID
     */
    public function findById($id) {
        $query = "SELECT f.*, 
                         u.nombres, u.apellido_paterno, u.correo,
                         un.nombre as universidad_nombre,
                         c.nombre as categoria_nombre,
                         (SELECT COUNT(*) FROM foro_reaccion fr WHERE fr.foro_id = f.foro_id) as total_reacciones
                  FROM foro f
                  LEFT JOIN usuario u ON f.usuario_id = u.usuario_id
                  LEFT JOIN universidad un ON f.universidad_id = un.universidad_id
                  LEFT JOIN catalogo c ON f.categoria_codigo = c.codigo
                  WHERE f.foro_id = :id
                  LIMIT 1";
                  
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    /**
     * Cambia el estado (habilitado/deshabilitado) de un foro
     */
    public function toggleEstado($id, $estadoHabilitado) {
        $query = "UPDATE foro SET habilitado = :estado WHERE foro_id = :id";
        $stmt = $this->db->prepare($query);
        
        $estadoVal = $estadoHabilitado ? true : false;
        
        $stmt->bindParam(':estado', $estadoVal, PDO::PARAM_BOOL);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
