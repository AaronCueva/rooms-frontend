<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Reserva {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $query = "
            SELECT r.*, 
                   u.nombres, u.apellido_paterno, u.correo,
                   a.titulo AS alojamiento_titulo, a.codigo AS alojamiento_codigo,
                   prop.nombres AS propietario_nombres, prop.apellido_paterno AS propietario_apellido
            FROM reserva r
            LEFT JOIN usuario u ON r.usuario_id = u.usuario_id
            LEFT JOIN alojamiento a ON r.alojamiento_id = a.alojamiento_id
            LEFT JOIN usuario prop ON a.usuario_id = prop.usuario_id
            ORDER BY r.fecha_solicitud DESC
        ";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contar($filtros = []) {
        $query = "
            SELECT COUNT(*) as total
            FROM reserva r
            LEFT JOIN usuario u ON r.usuario_id = u.usuario_id
            LEFT JOIN alojamiento a ON r.alojamiento_id = a.alojamiento_id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $query .= " AND (u.nombres ILIKE :busqueda OR u.apellido_paterno ILIKE :busqueda OR a.titulo ILIKE :busqueda OR a.codigo ILIKE :busqueda)";
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }
        if (!empty($filtros['estado'])) {
            $query .= " AND r.estado_codigo = :estado";
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
            SELECT r.*, 
                   u.nombres, u.apellido_paterno, u.correo,
                   a.titulo AS alojamiento_titulo, a.codigo AS alojamiento_codigo,
                   prop.nombres AS propietario_nombres, prop.apellido_paterno AS propietario_apellido
            FROM reserva r
            LEFT JOIN usuario u ON r.usuario_id = u.usuario_id
            LEFT JOIN alojamiento a ON r.alojamiento_id = a.alojamiento_id
            LEFT JOIN usuario prop ON a.usuario_id = prop.usuario_id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $query .= " AND (u.nombres ILIKE :busqueda OR u.apellido_paterno ILIKE :busqueda OR a.titulo ILIKE :busqueda OR a.codigo ILIKE :busqueda)";
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }
        if (!empty($filtros['estado'])) {
            $query .= " AND r.estado_codigo = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        $query .= " ORDER BY r.fecha_solicitud DESC LIMIT :limit OFFSET :offset";
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
            SELECT r.*, 
                   u.nombres, u.apellido_paterno, u.apellido_materno, u.correo, u.celular, u.url_foto AS usuario_foto,
                   a.titulo AS alojamiento_titulo, a.codigo AS alojamiento_codigo, a.direccion AS alojamiento_direccion,
                   a.precio_mensual, a.moneda_codigo,
                   prop.nombres AS propietario_nombres, prop.apellido_paterno AS propietario_apellido, prop.correo AS propietario_correo
            FROM reserva r
            LEFT JOIN usuario u ON r.usuario_id = u.usuario_id
            LEFT JOIN alojamiento a ON r.alojamiento_id = a.alojamiento_id
            LEFT JOIN usuario prop ON a.usuario_id = prop.usuario_id
            WHERE r.reserva_id = :id
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($datos) {
        $query = "INSERT INTO reserva (fecha_solicitud, fecha_ingreso, duracion_meses, monto_total, 
                  mensaje_presentacion, estado_codigo, usuario_id, alojamiento_id, creado_por)
                  VALUES (CURRENT_TIMESTAMP, :fecha_ingreso, :duracion_meses, :monto_total,
                  :mensaje_presentacion, :estado_codigo, :usuario_id, :alojamiento_id, :creado_por)
                  RETURNING reserva_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':fecha_ingreso'         => $datos['fecha_ingreso'] ?? null,
            ':duracion_meses'        => $datos['duracion_meses'] ?? 0,
            ':monto_total'           => $datos['monto_total'] ?? 0,
            ':mensaje_presentacion'  => $datos['mensaje_presentacion'] ?? null,
            ':estado_codigo'         => $datos['estado_codigo'] ?? 'ESRE001',
            ':usuario_id'            => $datos['usuario_id'] ?? null,
            ':alojamiento_id'        => $datos['alojamiento_id'] ?? null,
            ':creado_por'            => $datos['creado_por'] ?? null
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['reserva_id'] : null;
    }

    public function update($id, $datos) {
        $query = "UPDATE reserva SET 
                  fecha_ingreso = :fecha_ingreso,
                  duracion_meses = :duracion_meses,
                  monto_total = :monto_total,
                  mensaje_presentacion = :mensaje_presentacion,
                  estado_codigo = :estado_codigo,
                  usuario_id = :usuario_id,
                  alojamiento_id = :alojamiento_id,
                  fecha_respuesta = :fecha_respuesta,
                  modificado = CURRENT_TIMESTAMP,
                  modificado_por = :modificado_por
                  WHERE reserva_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':fecha_ingreso'         => $datos['fecha_ingreso'] ?? null,
            ':duracion_meses'        => $datos['duracion_meses'] ?? 0,
            ':monto_total'           => $datos['monto_total'] ?? 0,
            ':mensaje_presentacion'  => $datos['mensaje_presentacion'] ?? null,
            ':estado_codigo'         => $datos['estado_codigo'] ?? 'ESRE001',
            ':usuario_id'            => $datos['usuario_id'] ?? null,
            ':alojamiento_id'        => $datos['alojamiento_id'] ?? null,
            ':fecha_respuesta'       => $datos['fecha_respuesta'] ?? null,
            ':modificado_por'        => $datos['modificado_por'] ?? null,
            ':id'                    => $id
        ]);
    }

    public function toggleHabilitado($id, $estado) {
        $query = "UPDATE reserva SET habilitado = :estado, modificado = CURRENT_TIMESTAMP WHERE reserva_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':estado' => $estado ? 'true' : 'false', ':id' => $id]);
    }

    /**
     * Obtiene todas las reservas de un alojamiento
     */
    public function getByAlojamientoId($alojamiento_id) {
        $query = "
            SELECT r.*, u.nombres, u.apellido_paterno, u.correo
            FROM reserva r
            LEFT JOIN usuario u ON r.usuario_id = u.usuario_id
            WHERE r.alojamiento_id = :alojamiento_id
            ORDER BY r.fecha_solicitud DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':alojamiento_id' => $alojamiento_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todos los usuarios y alojamientos para los select del formulario
     */
    public function getUsuarios() {
        $query = "SELECT usuario_id, nombres, apellido_paterno, correo FROM usuario WHERE habilitado = true AND rol_id = 'b42b5b64-ad4a-4ab5-b669-d5bd38ae349e' ORDER BY nombres ASC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAlojamientos() {
        $query = "SELECT alojamiento_id, titulo, codigo FROM alojamiento WHERE habilitado = true AND estado_codigo not in ('EPA002', 'EPA003', 'EPA004') ORDER BY titulo ASC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
