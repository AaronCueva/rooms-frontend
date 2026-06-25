<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Foro {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

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

    public function buscar($filtros = [], $pagina = 1, $por_pagina = 10) {
        $condiciones = [];
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $condiciones[] = "(f.titulo ILIKE :busqueda OR f.descripcion ILIKE :busqueda)";
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }
        if (!empty($filtros['categoria'])) {
            $condiciones[] = "f.categoria_codigo = :categoria";
            $params[':categoria'] = $filtros['categoria'];
        }
        if (isset($filtros['estado'])) {
            if ($filtros['estado'] === '1' || $filtros['estado'] === '0') {
                $condiciones[] = "f.habilitado = :estado";
                $params[':estado'] = $filtros['estado'];
            }
        }

        $where = '';
        if (!empty($condiciones)) {
            $where = 'WHERE ' . implode(' AND ', $condiciones);
        }

        $offset = ($pagina - 1) * $por_pagina;

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
                  $where
                  ORDER BY f.fecha_creacion DESC
                  LIMIT $por_pagina OFFSET $offset";
                  
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }

    public function contar($filtros = []) {
        $condiciones = [];
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $condiciones[] = "(f.titulo ILIKE :busqueda OR f.descripcion ILIKE :busqueda)";
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }
        if (!empty($filtros['categoria'])) {
            $condiciones[] = "f.categoria_codigo = :categoria";
            $params[':categoria'] = $filtros['categoria'];
        }
        if (isset($filtros['estado'])) {
            if ($filtros['estado'] === '1' || $filtros['estado'] === '0') {
                $condiciones[] = "f.habilitado = :estado";
                $params[':estado'] = $filtros['estado'];
            }
        }

        $where = '';
        if (!empty($condiciones)) {
            $where = 'WHERE ' . implode(' AND ', $condiciones);
        }

        $query = "SELECT COUNT(*) FROM foro f $where";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        
        return (int) $stmt->fetchColumn();
    }

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
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch();
    }

    public function toggleEstado($id, $estadoHabilitado) {
        $query = "UPDATE foro SET habilitado = :estado WHERE foro_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':estado' => $estadoHabilitado ? true : false, ':id' => $id]);
        
        return $stmt->rowCount() > 0;
    }

    public function actualizar($id, $datos) {
        $query = "UPDATE foro SET titulo = :titulo, descripcion = :descripcion, categoria_codigo = :categoria_codigo, universidad_id = :universidad_id, modificado = NOW() WHERE foro_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':titulo' => $datos['titulo'],
            ':descripcion' => $datos['descripcion'] ?? '',
            ':categoria_codigo' => $datos['categoria_codigo'],
            ':universidad_id' => $datos['universidad_id'] ?: null,
            ':id' => $id,
        ]);
        return $stmt->rowCount() > 0;
    }
}
