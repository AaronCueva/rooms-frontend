<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class AlojamientoServicio
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Servicios asignados a un alojamiento
     */
    public function getByAlojamientoId($alojamiento_id)
    {
        $query = "SELECT als.*, s.nombre as servicio_nombre, s.descripcion as servicio_descripcion
                  FROM alojamiento_servicio als
                  INNER JOIN servicio s ON als.servicio_id = s.servicio_id
                  WHERE als.alojamiento_id = :alojamiento_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':alojamiento_id', $alojamiento_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Agrega un servicio a un alojamiento
     */
    public function create($datos)
    {
        $query = "INSERT INTO alojamiento_servicio (alojamiento_id, servicio_id, precio, fecha_pago)
                  VALUES (:alojamiento_id, :servicio_id, :precio, :fecha_pago)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':alojamiento_id', $datos['alojamiento_id']);
        $stmt->bindParam(':servicio_id', $datos['servicio_id']);
        $stmt->bindParam(':precio', $datos['precio']);
        $stmt->bindParam(':fecha_pago', $datos['fecha_pago']);
        return $stmt->execute();
    }

    /**
     * Elimina un servicio de un alojamiento
     */
    public function delete($id)
    {
        $query = "DELETE FROM alojamiento_servicio WHERE alojamiento_servicio_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
