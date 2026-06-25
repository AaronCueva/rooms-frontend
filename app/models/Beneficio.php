<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Beneficio
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Beneficios de un alojamiento
     */
    public function getByAlojamientoId($alojamiento_id)
    {
        $query = "SELECT * FROM beneficio WHERE alojamiento_id = :alojamiento_id ORDER BY nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':alojamiento_id', $alojamiento_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Crea un beneficio
     */
    public function create($datos)
    {
        $query = "INSERT INTO beneficio (nombre, descripcion, habilitado, alojamiento_id)
                  VALUES (:nombre, :descripcion, :habilitado, :alojamiento_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $habilitado = $datos['habilitado'] ?? true;
        $stmt->bindParam(':habilitado', $habilitado, PDO::PARAM_BOOL);
        $stmt->bindParam(':alojamiento_id', $datos['alojamiento_id']);
        return $stmt->execute();
    }

    /**
     * Elimina un beneficio
     */
    public function delete($id)
    {
        $query = "DELETE FROM beneficio WHERE beneficio_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
