<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Resena {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene las reseñas de un alojamiento (a través del contrato -> reserva -> alojamiento)
     */
    public function getByAlojamientoId($alojamiento_id) {
        $query = "
            SELECT r.*, 
                   c.contrato_id, c.fecha_inicio AS contrato_fecha_inicio, c.fecha_fin AS contrato_fecha_fin,
                   res.reserva_id, 
                   u.nombres, u.apellido_paterno, u.correo, u.url_foto AS usuario_foto
            FROM resena r
            INNER JOIN contrato c ON r.contrato_id = c.contrato_id
            INNER JOIN reserva res ON c.reserva_id = res.reserva_id
            INNER JOIN usuario u ON res.usuario_id = u.usuario_id
            WHERE res.alojamiento_id = :alojamiento_id
            ORDER BY r.fecha_creado DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':alojamiento_id' => $alojamiento_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByContratoId($contrato_id) {
        $query = "SELECT * FROM resena WHERE contrato_id = :contrato_id ORDER BY fecha_creado DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':contrato_id' => $contrato_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $query = "SELECT * FROM resena WHERE resena_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findDetalleById($id) {
        $query = "
            SELECT r.*, 
                   c.contrato_id, c.fecha_inicio AS contrato_fecha_inicio, c.fecha_fin AS contrato_fecha_fin, c.monto_renta,
                   res.reserva_id, res.alojamiento_id,
                   a.titulo AS alojamiento_titulo, a.direccion AS alojamiento_direccion, a.precio_mensual,
                   u.usuario_id AS estudiante_id, u.nombres, u.apellido_paterno, u.correo, u.url_foto AS usuario_foto, u.celular,
                   prop.nombres AS prop_nombres, prop.apellido_paterno AS prop_ap, prop.correo AS prop_correo, prop.celular AS prop_celular,
                   cat.nombre AS estado_nombre
            FROM resena r
            LEFT JOIN contrato c ON r.contrato_id = c.contrato_id
            LEFT JOIN reserva res ON c.reserva_id = res.reserva_id
            LEFT JOIN alojamiento a ON res.alojamiento_id = a.alojamiento_id
            LEFT JOIN usuario u ON res.usuario_id = u.usuario_id
            LEFT JOIN usuario prop ON a.usuario_id = prop.usuario_id
            LEFT JOIN catalogo cat ON r.estado_codigo = cat.codigo AND cat.referencia_codigo = 'ESTADO_RESENA'
            WHERE r.resena_id = :id
        ";
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
                   c.contrato_id, c.fecha_inicio AS contrato_fecha_inicio, c.fecha_fin AS contrato_fecha_fin,
                   res.reserva_id, res.alojamiento_id,
                   a.titulo AS alojamiento_titulo,
                   u.nombres, u.apellido_paterno, u.correo, u.url_foto AS usuario_foto,
                   cat.nombre AS estado_nombre
            FROM resena r
            LEFT JOIN contrato c ON r.contrato_id = c.contrato_id
            LEFT JOIN reserva res ON c.reserva_id = res.reserva_id
            LEFT JOIN alojamiento a ON res.alojamiento_id = a.alojamiento_id
            LEFT JOIN usuario u ON res.usuario_id = u.usuario_id
            LEFT JOIN catalogo cat ON r.estado_codigo = cat.codigo AND cat.referencia_codigo = 'ESTADO_RESENA'
            $where
            ORDER BY r.fecha_creado DESC
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
            FROM resena r
            LEFT JOIN contrato c ON r.contrato_id = c.contrato_id
            LEFT JOIN reserva res ON c.reserva_id = res.reserva_id
            LEFT JOIN alojamiento a ON res.alojamiento_id = a.alojamiento_id
            LEFT JOIN usuario u ON res.usuario_id = u.usuario_id
            $where
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function toggleEstado($id) {
        $resena = $this->findById($id);
        if (!$resena) return;

        $nuevoHab = !$resena['habilitado'];
        $nuevoEstado = $nuevoHab ? 'ESRS001' : 'ESRS003'; // ACTIVO vs OCULTO

        $query = "UPDATE resena SET habilitado = :hab, estado_codigo = :est, modificado = CURRENT_TIMESTAMP WHERE resena_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':hab' => $nuevoHab ? 'true' : 'false', ':est' => $nuevoEstado, ':id' => $id]);

        // Sincronizar con resenia_alojamiento si existe
        try {
            if (!empty($resena['comentario'])) {
                $estadoAloj = $nuevoHab ? 'ESRA001' : 'ESRA004';
                $qSync = "UPDATE resenia_alojamiento SET habilitado = :hab, estado_codigo = :est, modificado = CURRENT_TIMESTAMP WHERE comentario = :coment AND calificacion = :calif";
                $stmtSync = $this->db->prepare($qSync);
                $stmtSync->execute([':hab' => $nuevoHab ? 'true' : 'false', ':est' => $estadoAloj, ':coment' => $resena['comentario'], ':calif' => $resena['calificacion']]);
            }
        } catch (\Exception $e) {}
    }

    public function cambiarEstado($id, $estado_codigo) {
        $hab = ($estado_codigo !== 'ESRS003'); // Si es OCULTO, habilitado = false, de lo contrario true
        $query = "UPDATE resena SET estado_codigo = :estado, habilitado = :hab, modificado = CURRENT_TIMESTAMP WHERE resena_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':estado' => $estado_codigo, ':hab' => $hab ? 'true' : 'false', ':id' => $id]);

        // Sincronizar con resenia_alojamiento si existe
        try {
            $resena = $this->findById($id);
            if ($resena && !empty($resena['comentario'])) {
                $estadoAloj = 'ESRA001';
                if ($estado_codigo === 'ESRS002') $estadoAloj = 'ESRA003';
                if ($estado_codigo === 'ESRS003') $estadoAloj = 'ESRA004';
                
                $qSync = "UPDATE resenia_alojamiento SET estado_codigo = :estado_aloj, habilitado = :hab, modificado = CURRENT_TIMESTAMP WHERE comentario = :coment AND calificacion = :calif";
                $stmtSync = $this->db->prepare($qSync);
                $stmtSync->execute([':estado_aloj' => $estadoAloj, ':hab' => $hab ? 'true' : 'false', ':coment' => $resena['comentario'], ':calif' => $resena['calificacion']]);
            }
        } catch (\Exception $e) {}
    }
}
