<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class ReseniaAlojamiento {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene las reseñas directas de un alojamiento
     */
    public function getByAlojamientoId($alojamiento_id) {
        $query = "
            SELECT r.*, 
                   u.nombres, u.apellido_paterno, u.correo, u.url_foto AS usuario_foto
            FROM resenia_alojamiento r
            INNER JOIN usuario u ON r.estudiante_id = u.usuario_id
            WHERE r.alojamiento_id = :alojamiento_id
            ORDER BY r.creado DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':alojamiento_id' => $alojamiento_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $query = "SELECT * FROM resenia_alojamiento WHERE resenia_alojamiento_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscar($filtros = [], $pagina = 1, $por_pagina = 10) {
        $condiciones = [];
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $condiciones[] = "(r.comentario ILIKE :busqueda OR a.titulo ILIKE :busqueda OR u.nombres ILIKE :busqueda OR u.apellido_paterno ILIKE :busqueda)";
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }
        if (!empty($filtros['calificacion'])) {
            $condiciones[] = "r.calificacion = :calificacion";
            $params[':calificacion'] = $filtros['calificacion'];
        }
        if (!empty($filtros['estado'])) {
            if ($filtros['estado'] === '1' || $filtros['estado'] === '0') {
                $condiciones[] = "r.habilitado = :hab";
                $params[':hab'] = $filtros['estado'];
            } else {
                $condiciones[] = "r.estado_codigo = :estado";
                $params[':estado'] = $filtros['estado'];
            }
        }

        $where = '';
        if (!empty($condiciones)) {
            $where = 'WHERE ' . implode(' AND ', $condiciones);
        }

        $offset = ($pagina - 1) * $por_pagina;

        $query = "
            SELECT r.*, 
                   a.titulo AS alojamiento_titulo,
                   u.nombres, u.apellido_paterno, u.correo, u.url_foto AS usuario_foto,
                   cat.nombre AS estado_nombre
            FROM resenia_alojamiento r
            LEFT JOIN alojamiento a ON r.alojamiento_id = a.alojamiento_id
            LEFT JOIN usuario u ON r.estudiante_id = u.usuario_id
            LEFT JOIN catalogo cat ON r.estado_codigo = cat.codigo AND cat.referencia_codigo = 'ESTADO_RESENIA_ALOJAMIENTO'
            $where
            ORDER BY r.creado DESC
            LIMIT $por_pagina OFFSET $offset
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contar($filtros = []) {
        $condiciones = [];
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $condiciones[] = "(r.comentario ILIKE :busqueda OR a.titulo ILIKE :busqueda OR u.nombres ILIKE :busqueda OR u.apellido_paterno ILIKE :busqueda)";
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }
        if (!empty($filtros['calificacion'])) {
            $condiciones[] = "r.calificacion = :calificacion";
            $params[':calificacion'] = $filtros['calificacion'];
        }
        if (!empty($filtros['estado'])) {
            if ($filtros['estado'] === '1' || $filtros['estado'] === '0') {
                $condiciones[] = "r.habilitado = :hab";
                $params[':hab'] = $filtros['estado'];
            } else {
                $condiciones[] = "r.estado_codigo = :estado";
                $params[':estado'] = $filtros['estado'];
            }
        }

        $where = '';
        if (!empty($condiciones)) {
            $where = 'WHERE ' . implode(' AND ', $condiciones);
        }

        $query = "
            SELECT COUNT(*) 
            FROM resenia_alojamiento r
            LEFT JOIN alojamiento a ON r.alojamiento_id = a.alojamiento_id
            LEFT JOIN usuario u ON r.estudiante_id = u.usuario_id
            $where
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function toggleEstado($id) {
        $query = "UPDATE resenia_alojamiento SET habilitado = NOT habilitado, modificado = CURRENT_TIMESTAMP WHERE resenia_alojamiento_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
    }
}
