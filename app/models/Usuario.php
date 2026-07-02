<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Usuario
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByEmail($correo)
    {
        $query = "SELECT * FROM usuario WHERE usuario = :usuario LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':usuario', $correo);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($datos)
    {
        $query = "INSERT INTO usuario (usuario,
                    correo, password, nombres, apellido_paterno, apellido_materno, 
                    tipo_documento_codigo, numero_documento, celular, telefono, genero_codigo, rol_id, universidad_id, ubicacion_id, estado_codigo, habilitado
                  ) 
                  VALUES (
                    :usuario, :correo, :password, :nombres, :apellido_paterno, :apellido_materno, 
                    :tipo_documento_codigo, :numero_documento, :celular, :telefono, :genero_codigo, :rol_id, :universidad_id, :ubicacion_id, 'ESU001', true
                  )";
        $stmt = $this->db->prepare($query);

        // Encriptar password
        $password_hash = password_hash($datos['password'], PASSWORD_BCRYPT);

        $stmt->bindValue(':usuario', trim($datos['correo'] ?? ''));
        $stmt->bindValue(':correo', trim($datos['correo'] ?? ''));
        $stmt->bindValue(':password', $password_hash);
        $stmt->bindValue(':nombres', trim($datos['nombres'] ?? ''));
        $stmt->bindValue(':apellido_paterno', trim($datos['apellido_paterno'] ?? null));
        $stmt->bindValue(':apellido_materno', trim($datos['apellido_materno'] ?? null));
        $stmt->bindValue(':tipo_documento_codigo', !empty($datos['tipo_documento_codigo']) ? $datos['tipo_documento_codigo'] : null);
        $stmt->bindValue(':numero_documento', trim($datos['numero_documento'] ?? null));
        $stmt->bindValue(':celular', trim($datos['celular'] ?? null));
        $stmt->bindValue(':telefono', trim($datos['telefono'] ?? null));
        $stmt->bindValue(':genero_codigo', !empty($datos['genero_codigo']) ? $datos['genero_codigo'] : null);
        $stmt->bindValue(':rol_id', !empty($datos['rol_id']) ? $datos['rol_id'] : null);
        $stmt->bindValue(':universidad_id', !empty($datos['universidad_id']) ? $datos['universidad_id'] : null);
        $stmt->bindValue(':ubicacion_id', !empty($datos['ubicacion_id']) ? $datos['ubicacion_id'] : null);

        return $stmt->execute();
    }

    public function findById($id)
    {
        $query = "SELECT u.*, 
                         r.nombre AS rol_nombre, r.codigo AS rol_codigo,
                         uni.nombre AS universidad_nombre, uni.codigo AS universidad_siglas, uni.codigo AS universidad_codigo,
                         ub.nombre AS distrito_nombre,
                         td.nombre AS tipo_documento_nombre,
                         gen.nombre AS genero_nombre
                  FROM usuario u 
                  LEFT JOIN rol r ON u.rol_id = r.rol_id
                  LEFT JOIN universidad uni ON u.universidad_id = uni.universidad_id
                  LEFT JOIN ubicacion ub ON u.ubicacion_id = ub.ubicacion_id
                  LEFT JOIN catalogo td ON u.tipo_documento_codigo = td.codigo
                  LEFT JOIN catalogo gen ON u.genero_codigo = gen.codigo
                  WHERE u.usuario_id = :id 
                  LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function banear($id)
    {
        $query = "UPDATE usuario SET habilitado = false WHERE usuario_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function desbanear($id)
    {
        $query = "UPDATE usuario SET habilitado = true WHERE usuario_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function obtenerPasswordHash($id)
    {
        $query = "SELECT password FROM usuario WHERE usuario_id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? $row['password'] : null;
    }

    public function actualizarPassword($id, $nuevo_password_hash)
    {
        $query = "UPDATE usuario SET password = :password, modificado = CURRENT_TIMESTAMP WHERE usuario_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':password', $nuevo_password_hash);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function actualizarPerfil($id, $datos)
    {
        // Obtener latitud y longitud de la ubicación si se seleccionó una
        $latitud = null;
        $longitud = null;
        if (!empty($datos['ubicacion_id'])) {
            $queryUbicacion = "SELECT latitud, longitud FROM ubicacion WHERE ubicacion_id = :ubicacion_id LIMIT 1";
            $stmtUbicacion = $this->db->prepare($queryUbicacion);
            $stmtUbicacion->bindParam(':ubicacion_id', $datos['ubicacion_id']);
            $stmtUbicacion->execute();
            $ubicacion = $stmtUbicacion->fetch(PDO::FETCH_ASSOC);
            if ($ubicacion) {
                $latitud = $ubicacion['latitud'];
                $longitud = $ubicacion['longitud'];
            }
        }

        $query = "UPDATE usuario SET 
                    nombres = :nombres,
                    apellido_paterno = :apellido_paterno,
                    apellido_materno = :apellido_materno,
                    tipo_documento_codigo = :tipo_documento_codigo,
                    numero_documento = :numero_documento,
                    correo = :correo,
                    celular = :celular,
                    telefono = :telefono,
                    razon_social = :razon_social,
                    nombre_comercial = :nombre_comercial,
                    descripcion = :descripcion,
                    genero_codigo = :genero_codigo,
                    ubicacion_id = :ubicacion_id,
                    universidad_id = :universidad_id,
                    latitud = :latitud,
                    longitud = :longitud,
                    modificado = CURRENT_TIMESTAMP";

        // Solo actualizar url_foto si se envió una nueva
        if (isset($datos['url_foto'])) {
            $query .= ", url_foto = :url_foto";
        }

        $query .= " WHERE usuario_id = :id";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(':nombres', $datos['nombres'] ?? null);
        $stmt->bindValue(':apellido_paterno', $datos['apellido_paterno'] ?? null);
        $stmt->bindValue(':apellido_materno', $datos['apellido_materno'] ?? null);
        $stmt->bindValue(':tipo_documento_codigo', $datos['tipo_documento_codigo'] ?: null);
        $stmt->bindValue(':numero_documento', $datos['numero_documento'] ?? null);
        $stmt->bindValue(':correo', $datos['correo'] ?? null);
        $stmt->bindValue(':celular', $datos['celular'] ?? null);
        $stmt->bindValue(':telefono', $datos['telefono'] ?? null);
        $stmt->bindValue(':razon_social', $datos['razon_social'] ?? null);
        $stmt->bindValue(':nombre_comercial', $datos['nombre_comercial'] ?? null);
        $stmt->bindValue(':descripcion', $datos['descripcion'] ?? null);
        $stmt->bindValue(':genero_codigo', $datos['genero_codigo'] ?: null);
        $stmt->bindValue(':ubicacion_id', !empty($datos['ubicacion_id']) ? $datos['ubicacion_id'] : null);
        $stmt->bindValue(':universidad_id', !empty($datos['universidad_id']) ? $datos['universidad_id'] : null);
        $stmt->bindValue(':latitud', $latitud);
        $stmt->bindValue(':longitud', $longitud);
        $stmt->bindValue(':id', $id);

        if (isset($datos['url_foto'])) {
            $stmt->bindValue(':url_foto', $datos['url_foto']);
        }

        return $stmt->execute();
    }

    /**
     * Buscar usuarios paginados con filtros para el panel de administración
     */
    public function buscar($filtros = [], $pagina = 1, $por_pagina = 15)
    {
        $condiciones = [];
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $condiciones[] = "(u.nombres ILIKE :busqueda OR u.apellido_paterno ILIKE :busqueda OR u.correo ILIKE :busqueda OR u.numero_documento ILIKE :busqueda OR u.usuario ILIKE :busqueda)";
            $params[':busqueda'] = '%' . trim($filtros['busqueda']) . '%';
        }

        if (!empty($filtros['rol_id'])) {
            $condiciones[] = "u.rol_id = :rol_id";
            $params[':rol_id'] = $filtros['rol_id'];
        }

        if (isset($filtros['estado']) && $filtros['estado'] !== '') {
            if ($filtros['estado'] === 'activo') {
                $condiciones[] = "u.habilitado = true";
            } elseif ($filtros['estado'] === 'baneado') {
                $condiciones[] = "u.habilitado = false";
            }
        }

        $where = !empty($condiciones) ? "WHERE " . implode(" AND ", $condiciones) : "";
        $offset = ($pagina - 1) * $por_pagina;

        $query = "SELECT u.*, 
                         r.nombre AS rol_nombre, r.codigo AS rol_codigo,
                         uni.nombre AS universidad_nombre, uni.codigo AS universidad_siglas, uni.codigo AS universidad_codigo,
                         ub.nombre AS distrito_nombre,
                         td.nombre AS tipo_documento_nombre,
                         gen.nombre AS genero_nombre
                  FROM usuario u
                  LEFT JOIN rol r ON u.rol_id = r.rol_id
                  LEFT JOIN universidad uni ON u.universidad_id = uni.universidad_id
                  LEFT JOIN ubicacion ub ON u.ubicacion_id = ub.ubicacion_id
                  LEFT JOIN catalogo td ON u.tipo_documento_codigo = td.codigo
                  LEFT JOIN catalogo gen ON u.genero_codigo = gen.codigo
                  $where
                  ORDER BY u.creado DESC, u.nombres ASC
                  LIMIT :limite OFFSET :offset";

        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limite', (int)$por_pagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contar usuarios para paginación con filtros
     */
    public function contar($filtros = [])
    {
        $condiciones = [];
        $params = [];

        if (!empty($filtros['busqueda'])) {
            $condiciones[] = "(u.nombres ILIKE :busqueda OR u.apellido_paterno ILIKE :busqueda OR u.correo ILIKE :busqueda OR u.numero_documento ILIKE :busqueda OR u.usuario ILIKE :busqueda)";
            $params[':busqueda'] = '%' . trim($filtros['busqueda']) . '%';
        }

        if (!empty($filtros['rol_id'])) {
            $condiciones[] = "u.rol_id = :rol_id";
            $params[':rol_id'] = $filtros['rol_id'];
        }

        if (isset($filtros['estado']) && $filtros['estado'] !== '') {
            if ($filtros['estado'] === 'activo') {
                $condiciones[] = "u.habilitado = true";
            } elseif ($filtros['estado'] === 'baneado') {
                $condiciones[] = "u.habilitado = false";
            }
        }

        $where = !empty($condiciones) ? "WHERE " . implode(" AND ", $condiciones) : "";

        $query = "SELECT COUNT(*) FROM usuario u $where";
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

    /**
     * Actualizar usuario desde administración (Rol, Universidad, Datos personales, Estado)
     */
    public function actualizarAdmin($id, $datos)
    {
        $query = "UPDATE usuario SET 
                    nombres = :nombres,
                    apellido_paterno = :apellido_paterno,
                    apellido_materno = :apellido_materno,
                    correo = :correo,
                    usuario = :usuario,
                    celular = :celular,
                    telefono = :telefono,
                    tipo_documento_codigo = :tipo_doc,
                    numero_documento = :num_doc,
                    genero_codigo = :genero,
                    rol_id = :rol_id,
                    universidad_id = :universidad_id,
                    verificado = :verificado,
                    modificado = CURRENT_TIMESTAMP
                  WHERE usuario_id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nombres', trim($datos['nombres'] ?? ''));
        $stmt->bindValue(':apellido_paterno', trim($datos['apellido_paterno'] ?? null));
        $stmt->bindValue(':apellido_materno', trim($datos['apellido_materno'] ?? null));
        $stmt->bindValue(':correo', trim($datos['correo'] ?? ''));
        $stmt->bindValue(':usuario', trim($datos['correo'] ?? ''));
        $stmt->bindValue(':celular', trim($datos['celular'] ?? null));
        $stmt->bindValue(':telefono', trim($datos['telefono'] ?? null));
        $stmt->bindValue(':tipo_doc', !empty($datos['tipo_documento_codigo']) ? $datos['tipo_documento_codigo'] : null);
        $stmt->bindValue(':num_doc', trim($datos['numero_documento'] ?? null));
        $stmt->bindValue(':genero', !empty($datos['genero_codigo']) ? $datos['genero_codigo'] : null);
        $stmt->bindValue(':rol_id', !empty($datos['rol_id']) ? $datos['rol_id'] : null);
        $stmt->bindValue(':universidad_id', !empty($datos['universidad_id']) ? $datos['universidad_id'] : null);
        $stmt->bindValue(':verificado', isset($datos['verificado']) ? true : false, PDO::PARAM_BOOL);
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    /**
     * Alternar estado de habilitación (Banear / Desbanear temporal)
     */
    public function toggleHabilitado($id)
    {
        $query = "UPDATE usuario SET habilitado = NOT habilitado, modificado = CURRENT_TIMESTAMP WHERE usuario_id = :id RETURNING habilitado";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id);
        if ($stmt->execute()) {
            return $stmt->fetchColumn();
        }
        return null;
    }
}
