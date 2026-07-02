<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Blog {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Buscar artículos con filtros y paginación
     */
    public function buscar($filtros = [], $pagina = 1, $por_pagina = 10) {
        $condiciones = [];
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $condiciones[] = "(b.titulo ILIKE :busqueda OR b.contenido ILIKE :busqueda)";
            $params[':busqueda'] = '%' . trim($filtros['busqueda']) . '%';
        }

        if (!empty($filtros['estado'])) {
            $condiciones[] = "b.estado_codigo = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        // Siempre mostrar solo los que no están eliminados lógicamente
        $condiciones[] = "b.habilitado = true";

        $where = '';
        if (!empty($condiciones)) {
            $where = 'WHERE ' . implode(' AND ', $condiciones);
        }

        $offset = ($pagina - 1) * $por_pagina;

        $query = "SELECT b.*, 
                         u.nombres, u.apellido_paterno, u.correo,
                         c.nombre as estado_nombre
                  FROM blog b
                  LEFT JOIN usuario u ON b.usuario_id = u.usuario_id
                  LEFT JOIN catalogo c ON b.estado_codigo = c.codigo
                  $where
                  ORDER BY b.creado DESC
                  LIMIT $por_pagina OFFSET $offset";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Contar total de artículos para paginación
     */
    public function contar($filtros = []) {
        $condiciones = [];
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $condiciones[] = "(b.titulo ILIKE :busqueda OR b.contenido ILIKE :busqueda)";
            $params[':busqueda'] = '%' . trim($filtros['busqueda']) . '%';
        }

        if (!empty($filtros['estado'])) {
            $condiciones[] = "b.estado_codigo = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        $condiciones[] = "b.habilitado = true";

        $where = '';
        if (!empty($condiciones)) {
            $where = 'WHERE ' . implode(' AND ', $condiciones);
        }

        $query = "SELECT COUNT(*) FROM blog b $where";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Obtener un artículo por ID
     */
    public function findById($id) {
        $query = "SELECT b.*, 
                         u.nombres, u.apellido_paterno, u.correo,
                         c.nombre as estado_nombre
                  FROM blog b
                  LEFT JOIN usuario u ON b.usuario_id = u.usuario_id
                  LEFT JOIN catalogo c ON b.estado_codigo = c.codigo
                  WHERE b.blog_id = :id
                  LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    /**
     * Crear un nuevo artículo
     */
    public function crear($datos) {
        $fecha_pub = ($datos['estado_codigo'] === 'ESBL002') ? "NOW()" : "NULL";
        $query = "INSERT INTO blog (
                    blog_id, titulo, contenido, fecha_publicacion, estado_codigo, 
                    usuario_id, habilitado, creado, creado_por
                  ) VALUES (
                    gen_random_uuid(), :titulo, :contenido, $fecha_pub, :estado_codigo, 
                    :usuario_id, true, NOW(), :creado_por
                  )";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':titulo' => $datos['titulo'],
            ':contenido' => $datos['contenido'],
            ':estado_codigo' => $datos['estado_codigo'],
            ':usuario_id' => $datos['usuario_id'],
            ':creado_por' => $datos['creado_por'] ?? null
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Actualizar artículo existente
     */
    public function actualizar($id, $datos) {
        $query = "UPDATE blog SET 
                    titulo = :titulo,
                    contenido = :contenido,
                    estado_codigo = :estado_codigo,
                    fecha_publicacion = CASE 
                        WHEN :estado_pub = 'ESBL002' THEN COALESCE(fecha_publicacion, NOW())
                        ELSE fecha_publicacion 
                    END,
                    modificado = NOW(),
                    modificado_por = :modificado_por
                  WHERE blog_id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':titulo' => $datos['titulo'],
            ':contenido' => $datos['contenido'],
            ':estado_codigo' => $datos['estado_codigo'],
            ':estado_pub' => $datos['estado_codigo'],
            ':modificado_por' => $datos['modificado_por'] ?? null,
            ':id' => $id
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Cambiar estado del artículo directamente (ej: publicar u ocultar)
     */
    public function cambiarEstado($id, $estado_codigo, $modificado_por = null) {
        $query = "UPDATE blog SET 
                    estado_codigo = :estado_codigo,
                    fecha_publicacion = CASE 
                        WHEN :estado_pub = 'ESBL002' THEN COALESCE(fecha_publicacion, NOW())
                        ELSE fecha_publicacion 
                    END,
                    modificado = NOW(),
                    modificado_por = :modificado_por
                  WHERE blog_id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':estado_codigo' => $estado_codigo,
            ':estado_pub' => $estado_codigo,
            ':modificado_por' => $modificado_por,
            ':id' => $id
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Eliminación lógica (soft delete)
     */
    public function eliminar($id, $modificado_por = null) {
        $query = "UPDATE blog SET 
                    habilitado = false,
                    modificado = NOW(),
                    modificado_por = :modificado_por
                  WHERE blog_id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':modificado_por' => $modificado_por,
            ':id' => $id
        ]);

        return $stmt->rowCount() > 0;
    }
}
