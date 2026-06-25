<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class UniversidadModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Lista todas las universidades
     */
    public function getAll()
    {
        $query = "SELECT uni.*,
                         ub.nombre as distrito_nombre
                  FROM universidad uni
                  LEFT JOIN ubicacion ub ON uni.ubicacion_id = ub.ubicacion_id
                  ORDER BY uni.nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtiene una universidad por ID
     */
    public function findById($id)
    {
        $query = "SELECT uni.*,
                         ub.nombre as distrito_nombre
                  FROM universidad uni
                  LEFT JOIN ubicacion ub ON uni.ubicacion_id = ub.ubicacion_id
                  WHERE uni.universidad_id = :id
                  LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Crea una universidad
     */
    public function create($datos)
    {
        $query = "INSERT INTO universidad (nombre, descripcion, verificado, habilitado, ubicacion_id, direccion)
                  VALUES (:nombre, :descripcion, :verificado, :habilitado, :ubicacion_id, :direccion)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $verificado = $datos['verificado'] ?? false;
        $stmt->bindParam(':verificado', $verificado, PDO::PARAM_BOOL);
        $habilitado = $datos['habilitado'] ?? true;
        $stmt->bindParam(':habilitado', $habilitado, PDO::PARAM_BOOL);
        $stmt->bindParam(':ubicacion_id', $datos['ubicacion_id']);
        $stmt->bindParam(':direccion', $datos['direccion']);
        return $stmt->execute();
    }

    /**
     * Actualiza una universidad
     */
    public function update($id, $datos)
    {
        $query = "UPDATE universidad SET
                    nombre = :nombre, descripcion = :descripcion,
                    verificado = :verificado, habilitado = :habilitado,
                    ubicacion_id = :ubicacion_id, direccion = :direccion
                  WHERE universidad_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $verificado = $datos['verificado'] ?? false;
        $stmt->bindParam(':verificado', $verificado, PDO::PARAM_BOOL);
        $habilitado = $datos['habilitado'] ?? true;
        $stmt->bindParam(':habilitado', $habilitado, PDO::PARAM_BOOL);
        $stmt->bindParam(':ubicacion_id', $datos['ubicacion_id']);
        $stmt->bindParam(':direccion', $datos['direccion']);
        return $stmt->execute();
    }

    /**
     * Cambia el estado habilitado
     */
    public function toggleHabilitado($id, $estado)
    {
        $query = "UPDATE universidad SET habilitado = :estado WHERE universidad_id = :id";
        $stmt = $this->db->prepare($query);
        $estadoVal = $estado ? true : false;
        $stmt->bindParam(':estado', $estadoVal, PDO::PARAM_BOOL);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Obtiene alojamientos relacionados a una universidad (vía alojamiento_universidad)
     */
    public function getAlojamientosRelacionados($universidad_id)
    {
        $query = "SELECT a.alojamiento_id, a.titulo, a.codigo, a.precio_mensual, a.direccion,
                         a.numero_habitaciones, a.habilitado,
                         u.nombres, u.apellido_paterno,
                         au.distancia_km
                  FROM alojamiento_universidad au
                  INNER JOIN alojamiento a ON au.alojamiento_id = a.alojamiento_id
                  LEFT JOIN usuario u ON a.usuario_id = u.usuario_id
                  WHERE au.universidad_id = :universidad_id
                  ORDER BY au.distancia_km ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':universidad_id', $universidad_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
