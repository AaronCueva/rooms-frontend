<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Contrato {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function contar($filtros = []) {
        $query = "
            SELECT COUNT(*) as total
            FROM contrato c
            LEFT JOIN reserva res ON c.reserva_id = res.reserva_id
            LEFT JOIN usuario u ON res.usuario_id = u.usuario_id
            LEFT JOIN alojamiento a ON res.alojamiento_id = a.alojamiento_id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $query .= " AND (u.nombres ILIKE :busqueda OR u.apellido_paterno ILIKE :busqueda OR a.titulo ILIKE :busqueda OR a.codigo ILIKE :busqueda)";
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }
        if (!empty($filtros['estado'])) {
            $query .= " AND c.estado_codigo = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    public function buscar($filtros = [], $pagina = 1, $por_pagina = 10) {
        $offset = ($pagina - 1) * $por_pagina;
        $query = "
            SELECT c.*, 
                   res.reserva_id, res.fecha_solicitud, res.duracion_meses AS reserva_duracion,
                   u.nombres, u.apellido_paterno, u.correo,
                   a.titulo AS alojamiento_titulo, a.codigo AS alojamiento_codigo,
                   prop.nombres AS propietario_nombres, prop.apellido_paterno AS propietario_apellido,
                   m.url AS documento_url, m.nombre AS documento_nombre
            FROM contrato c
            LEFT JOIN reserva res ON c.reserva_id = res.reserva_id
            LEFT JOIN usuario u ON res.usuario_id = u.usuario_id
            LEFT JOIN alojamiento a ON res.alojamiento_id = a.alojamiento_id
            LEFT JOIN usuario prop ON a.usuario_id = prop.usuario_id
            LEFT JOIN multimedia m ON c.multimedia_id = m.multimedia_id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $query .= " AND (u.nombres ILIKE :busqueda OR u.apellido_paterno ILIKE :busqueda OR a.titulo ILIKE :busqueda OR a.codigo ILIKE :busqueda)";
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }
        if (!empty($filtros['estado'])) {
            $query .= " AND c.estado_codigo = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        $query .= " ORDER BY c.creado DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', (int)$por_pagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $query = "
            SELECT c.*, 
                   res.reserva_id, res.fecha_solicitud, res.fecha_ingreso AS reserva_fecha_ingreso,
                   res.duracion_meses AS reserva_duracion, res.monto_total AS reserva_monto, res.mensaje_presentacion,
                   u.nombres, u.apellido_paterno, u.apellido_materno, u.correo, u.celular, u.url_foto AS usuario_foto,
                   a.titulo AS alojamiento_titulo, a.codigo AS alojamiento_codigo, a.direccion AS alojamiento_direccion,
                   a.alojamiento_id,
                   prop.nombres AS propietario_nombres, prop.apellido_paterno AS propietario_apellido, prop.correo AS propietario_correo,
                   m.url AS documento_url, m.nombre AS documento_nombre
            FROM contrato c
            LEFT JOIN reserva res ON c.reserva_id = res.reserva_id
            LEFT JOIN usuario u ON res.usuario_id = u.usuario_id
            LEFT JOIN alojamiento a ON res.alojamiento_id = a.alojamiento_id
            LEFT JOIN usuario prop ON a.usuario_id = prop.usuario_id
            LEFT JOIN multimedia m ON c.multimedia_id = m.multimedia_id
            WHERE c.contrato_id = :id
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($datos) {
        $query = "INSERT INTO contrato (reserva_id, fecha_inicio, fecha_fin, monto_renta, monto_garantia, 
                  cargo_plataforma, estado_codigo, fecha_pago_mensual, multimedia_id, creado_por)
                  VALUES (:reserva_id, :fecha_inicio, :fecha_fin, :monto_renta, :monto_garantia,
                  :cargo_plataforma, :estado_codigo, :fecha_pago_mensual, :multimedia_id, :creado_por)
                  RETURNING contrato_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':reserva_id'        => $datos['reserva_id'] ?? null,
            ':fecha_inicio'      => $datos['fecha_inicio'] ?? null,
            ':fecha_fin'         => $datos['fecha_fin'] ?? null,
            ':monto_renta'       => $datos['monto_renta'] ?? 0,
            ':monto_garantia'    => $datos['monto_garantia'] ?? 0,
            ':cargo_plataforma'  => $datos['cargo_plataforma'] ?? 0,
            ':estado_codigo'     => $datos['estado_codigo'] ?? 'ESCO001',
            ':fecha_pago_mensual'=> $datos['fecha_pago_mensual'] ?? 1,
            ':multimedia_id'     => $datos['multimedia_id'] ?? null,
            ':creado_por'        => $datos['creado_por'] ?? null
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['contrato_id'] : null;
    }

    public function update($id, $datos) {
        $query = "UPDATE contrato SET 
                  reserva_id = :reserva_id,
                  fecha_inicio = :fecha_inicio,
                  fecha_fin = :fecha_fin,
                  monto_renta = :monto_renta,
                  monto_garantia = :monto_garantia,
                  cargo_plataforma = :cargo_plataforma,
                  estado_codigo = :estado_codigo,
                  fecha_pago_mensual = :fecha_pago_mensual,
                  multimedia_id = :multimedia_id,
                  modificado = CURRENT_TIMESTAMP,
                  modificado_por = :modificado_por
                  WHERE contrato_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':reserva_id'        => $datos['reserva_id'] ?? null,
            ':fecha_inicio'      => $datos['fecha_inicio'] ?? null,
            ':fecha_fin'         => $datos['fecha_fin'] ?? null,
            ':monto_renta'       => $datos['monto_renta'] ?? 0,
            ':monto_garantia'    => $datos['monto_garantia'] ?? 0,
            ':cargo_plataforma'  => $datos['cargo_plataforma'] ?? 0,
            ':estado_codigo'     => $datos['estado_codigo'] ?? 'ESCO001',
            ':fecha_pago_mensual'=> $datos['fecha_pago_mensual'] ?? 1,
            ':multimedia_id'     => $datos['multimedia_id'] ?? null,
            ':modificado_por'    => $datos['modificado_por'] ?? null,
            ':id'                => $id
        ]);
    }

    public function toggleHabilitado($id, $estado) {
        $query = "UPDATE contrato SET habilitado = :estado, modificado = CURRENT_TIMESTAMP WHERE contrato_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':estado' => $estado ? 'true' : 'false', ':id' => $id]);
    }

    /**
     * Obtiene todas las reservas disponibles para crear contratos
     */
    public function getReservasDisponibles() {
        $query = "
            SELECT res.reserva_id, res.fecha_solicitud, res.duracion_meses, res.monto_total, res.estado_codigo,
                   u.nombres, u.apellido_paterno,
                   a.titulo AS alojamiento_titulo, a.codigo AS alojamiento_codigo
            FROM reserva res
            LEFT JOIN usuario u ON res.usuario_id = u.usuario_id
            LEFT JOIN alojamiento a ON res.alojamiento_id = a.alojamiento_id
            WHERE res.habilitado = true
              AND res.estado_codigo IN ('ESRE002', 'APROBADA')
            ORDER BY res.fecha_solicitud DESC
        ";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
