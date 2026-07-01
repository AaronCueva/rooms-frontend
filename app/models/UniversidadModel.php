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
     * Cuenta el total de universidades filtradas
     */
    public function contar($filtros = [])
    {
        $query = "SELECT COUNT(*) FROM universidad uni WHERE 1=1";
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $query .= " AND (uni.nombre ILIKE :busqueda OR uni.codigo ILIKE :busqueda)";
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }
        if (isset($filtros['estado']) && $filtros['estado'] !== '') {
            $estadoVal = $filtros['estado'] == '1' ? 'true' : 'false';
            $query .= " AND uni.habilitado = " . $estadoVal;
        }

        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Busca universidades paginadas
     */
    public function buscar($filtros = [], $pagina = 1, $por_pagina = 10)
    {
        $offset = ($pagina - 1) * $por_pagina;
        $query = "SELECT uni.*,
                         ub.nombre as distrito_nombre
                  FROM universidad uni
                  LEFT JOIN ubicacion ub ON uni.ubicacion_id = ub.ubicacion_id
                  WHERE 1=1";
        
        $params = [];
        if (!empty($filtros['busqueda'])) {
            $query .= " AND (uni.nombre ILIKE :busqueda OR uni.codigo ILIKE :busqueda)";
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }
        if (isset($filtros['estado']) && $filtros['estado'] !== '') {
            $estadoVal = $filtros['estado'] == '1' ? 'true' : 'false';
            $query .= " AND uni.habilitado = " . $estadoVal;
        }

        $query .= " ORDER BY uni.nombre ASC LIMIT :limite OFFSET :offset";

        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limite', $por_pagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
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
        $query = "INSERT INTO universidad (nombre, descripcion, verificado, habilitado, ubicacion_id, direccion, latitud, longitud)
                  VALUES (:nombre, :descripcion, :verificado, :habilitado, :ubicacion_id, :direccion, :latitud, :longitud)
                  RETURNING universidad_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $verificado = $datos['verificado'] ?? false;
        $stmt->bindParam(':verificado', $verificado, PDO::PARAM_BOOL);
        $habilitado = $datos['habilitado'] ?? true;
        $stmt->bindParam(':habilitado', $habilitado, PDO::PARAM_BOOL);
        $stmt->bindParam(':ubicacion_id', $datos['ubicacion_id']);
        $stmt->bindParam(':direccion', $datos['direccion']);
        $latitud = !empty($datos['latitud']) ? $datos['latitud'] : null;
        $longitud = !empty($datos['longitud']) ? $datos['longitud'] : null;
        $stmt->bindParam(':latitud', $latitud);
        $stmt->bindParam(':longitud', $longitud);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['universidad_id'] : null;
    }

    /**
     * Actualiza una universidad
     */
    public function update($id, $datos)
    {
        $query = "UPDATE universidad SET
                    nombre = :nombre, descripcion = :descripcion,
                    verificado = :verificado, habilitado = :habilitado,
                    ubicacion_id = :ubicacion_id, direccion = :direccion,
                    latitud = :latitud, longitud = :longitud
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
        $latitud = !empty($datos['latitud']) ? $datos['latitud'] : null;
        $longitud = !empty($datos['longitud']) ? $datos['longitud'] : null;
        $stmt->bindParam(':latitud', $latitud);
        $stmt->bindParam(':longitud', $longitud);
        return $stmt->execute();
    }

    /**
     * Obtiene todas las universidades que tienen coordenadas definidas
     */
    public function getAllConCoordenadas()
    {
        $query = "SELECT universidad_id, nombre, latitud, longitud
                  FROM universidad
                  WHERE latitud IS NOT NULL AND longitud IS NOT NULL AND habilitado = true";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
