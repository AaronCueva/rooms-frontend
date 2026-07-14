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
     * Obtiene las reseñas de un alojamiento
     */
    public function getByAlojamientoId($alojamiento_id) {
        $query = "
            SELECT r.resenia_alojamiento_id AS resena_id, r.calificacion, r.comentario, r.respuesta_propietario, r.habilitado, r.estado_codigo, r.creado AS fecha_creado,
                   u.nombres, u.apellido_paterno, u.correo, u.url_foto AS usuario_foto
            FROM resenia_alojamiento r
            LEFT JOIN usuario u ON r.estudiante_id = u.usuario_id
            WHERE r.alojamiento_id = :alojamiento_id
            ORDER BY r.creado DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':alojamiento_id' => $alojamiento_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByContratoId($contrato_id) {
        // En esta nueva estructura, las reseñas son al alojamiento y no al contrato.
        // Podríamos obtener la reserva del contrato y con eso el alojamiento y el estudiante
        // para encontrar la reseña correspondiente.
        $query = "
            SELECT r.resenia_alojamiento_id AS resena_id, r.*, r.creado AS fecha_creado
            FROM resenia_alojamiento r
            INNER JOIN reserva res ON r.alojamiento_id = res.alojamiento_id AND r.estudiante_id = res.usuario_id
            INNER JOIN contrato c ON res.reserva_id = c.reserva_id
            WHERE c.contrato_id = :contrato_id
            ORDER BY r.creado DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':contrato_id' => $contrato_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $query = "
            SELECT r.*, r.resenia_alojamiento_id AS resena_id, r.creado AS fecha_creado
            FROM resenia_alojamiento r 
            WHERE r.resenia_alojamiento_id = :id
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findDetalleById($id) {
        $query = "
            SELECT r.resenia_alojamiento_id AS resena_id, r.calificacion, r.comentario, r.respuesta_propietario, r.habilitado, r.estado_codigo, r.creado AS fecha_creado,
                   NULL AS contrato_id, NULL AS contrato_fecha_inicio, NULL AS contrato_fecha_fin, NULL AS monto_renta,
                   NULL AS reserva_id, r.alojamiento_id,
                   a.titulo AS alojamiento_titulo, a.direccion AS alojamiento_direccion, a.precio_mensual,
                   u.usuario_id AS estudiante_id, u.nombres, u.apellido_paterno, u.correo, u.url_foto AS usuario_foto, u.celular,
                   prop.nombres AS prop_nombres, prop.apellido_paterno AS prop_ap, prop.correo AS prop_correo, prop.celular AS prop_celular,
                   cat.nombre AS estado_nombre
            FROM resenia_alojamiento r
            LEFT JOIN alojamiento a ON r.alojamiento_id = a.alojamiento_id
            LEFT JOIN usuario u ON r.estudiante_id = u.usuario_id
            LEFT JOIN usuario prop ON r.propietario_id = prop.usuario_id
            LEFT JOIN catalogo cat ON r.estado_codigo = cat.codigo AND cat.referencia_codigo = 'ESTADO_RESENIA_ALOJAMIENTO'
            WHERE r.resenia_alojamiento_id = :id
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
                // Mapear filtros si vienen con códigos ESRS a códigos ESRA si es necesario
                $estadoFiltro = $filtros['estado'];
                if ($estadoFiltro === 'ESRS001') $estadoFiltro = 'ESRA001';
                if ($estadoFiltro === 'ESRS002') $estadoFiltro = 'ESRA003';
                if ($estadoFiltro === 'ESRS003') $estadoFiltro = 'ESRA004';

                $condiciones[] = "r.estado_codigo = :estado";
                $params[':estado'] = $estadoFiltro;
            }
        }

        $where = '';
        if (!empty($condiciones)) {
            $where = 'WHERE ' . implode(' AND ', $condiciones);
        }

        $offset = ($pagina - 1) * $por_pagina;

        $query = "
            SELECT r.resenia_alojamiento_id AS resena_id, r.calificacion, r.comentario, r.respuesta_propietario, r.habilitado, r.estado_codigo, r.creado AS fecha_creado,
                   NULL AS contrato_id, NULL AS contrato_fecha_inicio, NULL AS contrato_fecha_fin,
                   NULL AS reserva_id, r.alojamiento_id,
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
                $estadoFiltro = $filtros['estado'];
                if ($estadoFiltro === 'ESRS001') $estadoFiltro = 'ESRA001';
                if ($estadoFiltro === 'ESRS002') $estadoFiltro = 'ESRA003';
                if ($estadoFiltro === 'ESRS003') $estadoFiltro = 'ESRA004';

                $condiciones[] = "r.estado_codigo = :estado";
                $params[':estado'] = $estadoFiltro;
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
        $resena = $this->findById($id);
        if (!$resena) return;

        $nuevoHab = !$resena['habilitado'];
        $nuevoEstado = $nuevoHab ? 'ESRA001' : 'ESRA004'; // ACTIVO vs OCULTO

        $query = "UPDATE resenia_alojamiento SET habilitado = :hab, estado_codigo = :est, modificado = CURRENT_TIMESTAMP WHERE resenia_alojamiento_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':hab' => $nuevoHab ? 'true' : 'false', ':est' => $nuevoEstado, ':id' => $id]);
    }

    public function cambiarEstado($id, $estado_codigo) {
        // Adaptar el estado_codigo que viene desde la vista si envian codigo ESRS
        if ($estado_codigo === 'ESRS001') $estado_codigo = 'ESRA001';
        if ($estado_codigo === 'ESRS002') $estado_codigo = 'ESRA003';
        if ($estado_codigo === 'ESRS003') $estado_codigo = 'ESRA004';

        $hab = ($estado_codigo !== 'ESRA004'); // Si es OCULTO, habilitado = false, de lo contrario true
        $query = "UPDATE resenia_alojamiento SET estado_codigo = :estado, habilitado = :hab, modificado = CURRENT_TIMESTAMP WHERE resenia_alojamiento_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':estado' => $estado_codigo, ':hab' => $hab ? 'true' : 'false', ':id' => $id]);
    }
}
