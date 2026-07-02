<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Referido {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Buscar referidos con filtros y paginación
     */
    public function buscar($filtros = [], $pagina = 1, $por_pagina = 10) {
        $condiciones = ["r.habilitado = true"];
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $condiciones[] = "(ur.nombres ILIKE :bus OR ur.correo ILIKE :bus OR uref.nombres ILIKE :bus OR uref.correo ILIKE :bus OR r.codigo ILIKE :bus)";
            $params[':bus'] = '%' . trim($filtros['busqueda']) . '%';
        }

        if (!empty($filtros['estado'])) {
            $condiciones[] = "r.estado_codigo = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        $where = !empty($condiciones) ? "WHERE " . implode(" AND ", $condiciones) : "";
        $offset = ($pagina - 1) * $por_pagina;

        $query = "SELECT r.*, 
                         ur.nombres as referidor_nombres, ur.correo as referidor_correo,
                         uref.nombres as referido_nombres, uref.correo as referido_correo,
                         c.nombre as estado_nombre, c.codigo as estado_cod
                  FROM referido r
                  LEFT JOIN usuario ur ON r.usuario_referidor_id = ur.usuario_id
                  LEFT JOIN usuario uref ON r.usuario_referido_id = uref.usuario_id
                  LEFT JOIN catalogo c ON r.estado_codigo = c.codigo
                  $where
                  ORDER BY r.fecha_creacion DESC
                  LIMIT :limite OFFSET :offset";

        $stmt = $this->db->prepare($query);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limite', (int)$por_pagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Contar referidos para paginación
     */
    public function contar($filtros = []) {
        $condiciones = ["r.habilitado = true"];
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $condiciones[] = "(ur.nombres ILIKE :bus OR ur.correo ILIKE :bus OR uref.nombres ILIKE :bus OR uref.correo ILIKE :bus OR r.codigo ILIKE :bus)";
            $params[':bus'] = '%' . trim($filtros['busqueda']) . '%';
        }

        if (!empty($filtros['estado'])) {
            $condiciones[] = "r.estado_codigo = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        $where = !empty($condiciones) ? "WHERE " . implode(" AND ", $condiciones) : "";

        $query = "SELECT count(*) 
                  FROM referido r
                  LEFT JOIN usuario ur ON r.usuario_referidor_id = ur.usuario_id
                  LEFT JOIN usuario uref ON r.usuario_referido_id = uref.usuario_id
                  $where";

        $stmt = $this->db->prepare($query);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Buscar por ID
     */
    public function findById($id) {
        $query = "SELECT r.*, 
                         ur.nombres as referidor_nombres, ur.correo as referidor_correo,
                         uref.nombres as referido_nombres, uref.correo as referido_correo
                  FROM referido r
                  LEFT JOIN usuario ur ON r.usuario_referidor_id = ur.usuario_id
                  LEFT JOIN usuario uref ON r.usuario_referido_id = uref.usuario_id
                  WHERE r.referido_id = :id AND r.habilitado = true";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Cambiar estado del referido (ej: de ESREF01 Pendiente a ESREF02 Acreditado o ESREF03 Cancelado)
     */
    public function cambiarEstado($id, $estado_codigo, $modificado_por = null) {
        $query = "UPDATE referido SET 
                    estado_codigo = :estado_codigo,
                    modificado = NOW(),
                    modificado_por = :modificado_por
                  WHERE referido_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':estado_codigo' => $estado_codigo,
            ':modificado_por' => $modificado_por,
            ':id' => $id
        ]);
        return $stmt->rowCount() > 0;
    }
}
