<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class PuntoMovimiento {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Buscar movimientos globales con filtros y paginación (Ledger universal)
     */
    public function buscar($filtros = [], $pagina = 1, $por_pagina = 15) {
        $condiciones = ["pm.habilitado = true"];
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $condiciones[] = "(u.nombres ILIKE :bus OR u.correo ILIKE :bus OR pm.descripcion ILIKE :bus)";
            $params[':bus'] = '%' . trim($filtros['busqueda']) . '%';
        }

        if (!empty($filtros['tipo_movimiento'])) {
            $condiciones[] = "pm.tipo_movimiento_codigo = :tipo";
            $params[':tipo'] = $filtros['tipo_movimiento'];
        }

        if (!empty($filtros['usuario_id'])) {
            $condiciones[] = "pm.usuario_id = :uid";
            $params[':uid'] = $filtros['usuario_id'];
        }

        $where = !empty($condiciones) ? "WHERE " . implode(" AND ", $condiciones) : "";
        $offset = ($pagina - 1) * $por_pagina;

        $query = "SELECT pm.*, 
                         u.nombres as usuario_nombres, u.correo as usuario_correo,
                         c.nombre as tipo_nombre, c.codigo as tipo_cod
                  FROM punto_movimiento pm
                  LEFT JOIN usuario u ON pm.usuario_id = u.usuario_id
                  LEFT JOIN catalogo c ON pm.tipo_movimiento_codigo = c.codigo
                  $where
                  ORDER BY pm.fecha_creacion DESC
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
     * Contar movimientos para paginación
     */
    public function contar($filtros = []) {
        $condiciones = ["pm.habilitado = true"];
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $condiciones[] = "(u.nombres ILIKE :bus OR u.correo ILIKE :bus OR pm.descripcion ILIKE :bus)";
            $params[':bus'] = '%' . trim($filtros['busqueda']) . '%';
        }

        if (!empty($filtros['tipo_movimiento'])) {
            $condiciones[] = "pm.tipo_movimiento_codigo = :tipo";
            $params[':tipo'] = $filtros['tipo_movimiento'];
        }

        if (!empty($filtros['usuario_id'])) {
            $condiciones[] = "pm.usuario_id = :uid";
            $params[':uid'] = $filtros['usuario_id'];
        }

        $where = !empty($condiciones) ? "WHERE " . implode(" AND ", $condiciones) : "";

        $query = "SELECT count(*) 
                  FROM punto_movimiento pm
                  LEFT JOIN usuario u ON pm.usuario_id = u.usuario_id
                  $where";

        $stmt = $this->db->prepare($query);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Obtener el historial completo de un usuario por su ID (para modal Ledger)
     */
    public function getLedgerByUsuario($usuario_id) {
        $query = "SELECT pm.*, c.nombre as tipo_nombre 
                  FROM punto_movimiento pm
                  LEFT JOIN catalogo c ON pm.tipo_movimiento_codigo = c.codigo
                  WHERE pm.usuario_id = :uid AND pm.habilitado = true
                  ORDER BY pm.fecha_creacion DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':uid' => $usuario_id]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener Leaderboard (Ranking de estudiantes con más puntos NIDO)
     */
    public function obtenerLeaderboard($filtros = [], $pagina = 1, $por_pagina = 10) {
        $condiciones = ["u.habilitado = true"];
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $condiciones[] = "(u.nombres ILIKE :bus OR u.correo ILIKE :bus)";
            $params[':bus'] = '%' . trim($filtros['busqueda']) . '%';
        }

        if (!empty($filtros['nivel'])) {
            if ($filtros['nivel'] === 'GOLD') {
                $condiciones[] = "COALESCE(u.puntos_acumulados, 0) >= 500";
            } elseif ($filtros['nivel'] === 'CONFIABLE') {
                $condiciones[] = "COALESCE(u.puntos_acumulados, 0) >= 200 AND COALESCE(u.puntos_acumulados, 0) < 500";
            } elseif ($filtros['nivel'] === 'NOVATO') {
                $condiciones[] = "COALESCE(u.puntos_acumulados, 0) < 200";
            }
        }

        $where = !empty($condiciones) ? "WHERE " . implode(" AND ", $condiciones) : "";
        $offset = ($pagina - 1) * $por_pagina;

        $query = "SELECT u.usuario_id, u.nombres, u.correo, COALESCE(u.puntos_acumulados, 0) as puntos_acumulados,
                         CASE 
                             WHEN COALESCE(u.puntos_acumulados, 0) >= 500 THEN 'Nido Gold'
                             WHEN COALESCE(u.puntos_acumulados, 0) >= 200 THEN 'Inquilino Confiable'
                             ELSE 'Novato'
                         END as nivel_nido,
                         CASE 
                             WHEN COALESCE(u.puntos_acumulados, 0) >= 500 THEN 'gold'
                             WHEN COALESCE(u.puntos_acumulados, 0) >= 200 THEN 'info'
                             ELSE 'secondary'
                         END as badge_class
                  FROM usuario u
                  $where
                  ORDER BY COALESCE(u.puntos_acumulados, 0) DESC, u.nombres ASC
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
     * Contar usuarios para paginación de Leaderboard
     */
    public function contarLeaderboard($filtros = []) {
        $condiciones = ["u.habilitado = true"];
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $condiciones[] = "(u.nombres ILIKE :bus OR u.correo ILIKE :bus)";
            $params[':bus'] = '%' . trim($filtros['busqueda']) . '%';
        }

        if (!empty($filtros['nivel'])) {
            if ($filtros['nivel'] === 'GOLD') {
                $condiciones[] = "COALESCE(u.puntos_acumulados, 0) >= 500";
            } elseif ($filtros['nivel'] === 'CONFIABLE') {
                $condiciones[] = "COALESCE(u.puntos_acumulados, 0) >= 200 AND COALESCE(u.puntos_acumulados, 0) < 500";
            } elseif ($filtros['nivel'] === 'NOVATO') {
                $condiciones[] = "COALESCE(u.puntos_acumulados, 0) < 200";
            }
        }

        $where = !empty($condiciones) ? "WHERE " . implode(" AND ", $condiciones) : "";

        $query = "SELECT count(*) FROM usuario u $where";
        $stmt = $this->db->prepare($query);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Registrar un nuevo movimiento de puntos (con transacción ACID para actualizar saldo de usuario)
     */
    public function registrarMovimiento($usuario_id, $tipo_codigo, $puntos, $descripcion, $creado_por = null, $referido_id = null) {
        try {
            $this->db->beginTransaction();

            // 1. Insertar en punto_movimiento
            $queryIns = "INSERT INTO punto_movimiento 
                            (punto_movimiento_id, usuario_id, tipo_movimiento_codigo, puntos, descripcion, fecha_creacion, habilitado, creado, creado_por) 
                         VALUES 
                            (gen_random_uuid(), :uid, :tipo, :pts, :desc, NOW(), true, NOW(), :cpor)";
            $stmt1 = $this->db->prepare($queryIns);
            $stmt1->execute([
                ':uid' => $usuario_id,
                ':tipo' => $tipo_codigo,
                ':pts' => (int)$puntos,
                ':desc' => $descripcion,
                ':cpor' => $creado_por ?? 'Admin'
            ]);

            // 2. Actualizar saldo acumulado en usuario
            $queryUpd = "UPDATE usuario 
                         SET puntos_acumulados = COALESCE(puntos_acumulados, 0) + (:pts) 
                         WHERE usuario_id = :uid";
            $stmt2 = $this->db->prepare($queryUpd);
            $stmt2->execute([
                ':pts' => (int)$puntos,
                ':uid' => $usuario_id
            ]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    /**
     * Obtener lista simple de estudiantes para selectores (Dropdown de Ajuste Manual)
     */
    public function obtenerEstudiantesLista() {
        $query = "SELECT usuario_id, nombres, correo, COALESCE(puntos_acumulados, 0) as puntos_acumulados 
                  FROM usuario 
                  WHERE habilitado = true 
                  ORDER BY nombres ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
