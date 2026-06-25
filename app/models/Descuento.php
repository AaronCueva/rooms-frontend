<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Descuento
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Descuentos de un alojamiento
     */
    public function getByAlojamientoId($alojamiento_id)
    {
        $query = "SELECT * FROM descuento WHERE alojamiento_id = :alojamiento_id ORDER BY fin DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':alojamiento_id', $alojamiento_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Crea un descuento
     */
    public function create($datos)
    {
        $query = "INSERT INTO descuento (nombre, monto, motivo, inicio, fin, alojamiento_id)
                  VALUES (:nombre, :monto, :motivo, :inicio, :fin, :alojamiento_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':monto', $datos['monto']);
        $stmt->bindParam(':motivo', $datos['motivo']);
        $stmt->bindParam(':inicio', $datos['inicio']);
        $stmt->bindParam(':fin', $datos['fin']);
        $stmt->bindParam(':alojamiento_id', $datos['alojamiento_id']);
        return $stmt->execute();
    }

    /**
     * Elimina un descuento
     */
    public function delete($id)
    {
        $query = "DELETE FROM descuento WHERE descuento_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
