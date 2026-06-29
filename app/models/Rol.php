<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Rol {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM rol ORDER BY nombre ASC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $query = "SELECT * FROM rol WHERE rol_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($datos) {
        $query = "INSERT INTO rol (codigo, nombre, descripcion) VALUES (:codigo, :nombre, :descripcion) RETURNING rol_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':codigo' => $datos['codigo'],
            ':nombre' => $datos['nombre'],
            ':descripcion' => $datos['descripcion'] ?? null
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['rol_id'] : null;
    }

    public function update($id, $datos) {
        $query = "UPDATE rol SET codigo = :codigo, nombre = :nombre, descripcion = :descripcion WHERE rol_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':codigo' => $datos['codigo'],
            ':nombre' => $datos['nombre'],
            ':descripcion' => $datos['descripcion'] ?? null,
            ':id' => $id
        ]);
    }

    public function toggleHabilitado($id, $estado) {
        $query = "UPDATE rol SET habilitado = :estado WHERE rol_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':estado' => $estado ? 'true' : 'false', ':id' => $id]);
    }

    /**
     * Obtiene los roles específicos requeridos para el registro
     */
    public function obtenerRolesRegistro() {
        $query = "SELECT rol_id, codigo, nombre 
                  FROM rol 
                  WHERE codigo IN ('ADMIN_SOCIALES', 'ADMIN_RESERVAS', 'OWNER')
                  AND habilitado = true";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
