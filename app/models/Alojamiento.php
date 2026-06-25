<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Alojamiento
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Lista todos los alojamientos con datos del propietario y ubicación
     */
    public function getAll()
    {
        $query = "SELECT a.*,
                         u.nombres, u.apellido_paterno, u.correo,
                         ub.nombre as distrito_nombre,
                         cat_tipo.nombre as tipo_nombre,
                         cat_estado.nombre as estado_nombre
                  FROM alojamiento a
                  LEFT JOIN usuario u ON a.usuario_id = u.usuario_id
                  LEFT JOIN ubicacion ub ON a.ubicacion_id = ub.ubicacion_id
                  LEFT JOIN catalogo cat_tipo ON a.tipo_codigo = cat_tipo.codigo
                  LEFT JOIN catalogo cat_estado ON a.estado_codigo = cat_estado.codigo
                  ORDER BY a.alojamiento_id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtiene un alojamiento con todos sus datos
     */
    public function findById($id)
    {
        $query = "SELECT a.*,
                         u.nombres, u.apellido_paterno, u.correo, u.celular as propietario_celular,
                         ub.nombre as distrito_nombre,
                         cat_tipo.nombre as tipo_nombre,
                         cat_estado.nombre as estado_nombre,
                         cat_genero.nombre as genero_exclusivo_nombre,
                         cat_moneda.nombre as moneda_nombre,
                         (SELECT COUNT(*) FROM favorito f WHERE f.alojamiento_id = a.alojamiento_id) as total_favoritos
                  FROM alojamiento a
                  LEFT JOIN usuario u ON a.usuario_id = u.usuario_id
                  LEFT JOIN ubicacion ub ON a.ubicacion_id = ub.ubicacion_id
                  LEFT JOIN catalogo cat_tipo ON a.tipo_codigo = cat_tipo.codigo
                  LEFT JOIN catalogo cat_estado ON a.estado_codigo = cat_estado.codigo
                  LEFT JOIN catalogo cat_genero ON a.genero_exclusivo_codigo = cat_genero.codigo
                  LEFT JOIN catalogo cat_moneda ON a.moneda_codigo = cat_moneda.codigo
                  WHERE a.alojamiento_id = :id
                  LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Crea un nuevo alojamiento
     */
    public function create($datos)
    {
        $query = "INSERT INTO alojamiento (
                    codigo, titulo, tipo_codigo, numero_habitaciones, numero_banos,
                    bano_privado, tamano_m2, genero_exclusivo_codigo, mascotas_permitidas, fumadores_permitidos,
                    descripcion, usuario_id, ubicacion_id, direccion, latitud, longitud,
                    precio_mensual, moneda_codigo, garantia, duracion_minima_meses,
                    fecha_disponible, estado_codigo, habilitado, precio_servicios, amoblado,
                    solo_verificados, calificacion
                  ) VALUES (
                    :codigo, :titulo, :tipo_codigo, :numero_habitaciones, :numero_banos,
                    :bano_privado, :tamano_m2, :genero_exclusivo_codigo, :mascotas_permitidas, :fumadores_permitidos,
                    :descripcion, :usuario_id, :ubicacion_id, :direccion, :latitud, :longitud,
                    :precio_mensual, :moneda_codigo, :garantia, :duracion_minima_meses,
                    :fecha_disponible, :estado_codigo, :habilitado, :precio_servicios, :amoblado,
                    :solo_verificados, :calificacion
                  ) RETURNING alojamiento_id";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':codigo', $datos['codigo']);
        $stmt->bindParam(':titulo', $datos['titulo']);
        $stmt->bindParam(':tipo_codigo', $datos['tipo_codigo']);
        $stmt->bindParam(':numero_habitaciones', $datos['numero_habitaciones'], PDO::PARAM_INT);
        $stmt->bindParam(':numero_banos', $datos['numero_banios'], PDO::PARAM_INT);
        $stmt->bindParam(':bano_privado', $datos['bano_privado'], PDO::PARAM_BOOL);
        $stmt->bindParam(':tamano_m2', $datos['tamanio_m2']);
        $stmt->bindParam(':genero_exclusivo_codigo', $datos['genero_exclusivo_codigo']);
        $stmt->bindParam(':mascotas_permitidas', $datos['mascotas_permitidas'], PDO::PARAM_BOOL);
        $stmt->bindParam(':fumadores_permitidos', $datos['fumadores_permitidos'], PDO::PARAM_BOOL);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':usuario_id', $datos['usuario_id']);
        $stmt->bindParam(':ubicacion_id', $datos['ubicacion_id']);
        $stmt->bindParam(':direccion', $datos['direccion']);
        $stmt->bindParam(':latitud', $datos['latitud']);
        $stmt->bindParam(':longitud', $datos['longitud']);
        $stmt->bindParam(':precio_mensual', $datos['precio_mensual']);
        $stmt->bindParam(':moneda_codigo', $datos['moneda_codigo']);
        $stmt->bindParam(':garantia', $datos['garantia']);
        $stmt->bindParam(':duracion_minima_meses', $datos['duracion_minima_meses'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha_disponible', $datos['fecha_disponible']);
        $stmt->bindParam(':estado_codigo', $datos['estado_codigo']);
        $stmt->bindParam(':habilitado', $datos['habilitado'], PDO::PARAM_BOOL);
        $stmt->bindParam(':precio_servicios', $datos['precio_servicios']);
        $stmt->bindParam(':amoblado', $datos['amoblado'], PDO::PARAM_BOOL);
        $stmt->bindParam(':solo_verificados', $datos['solo_verificados'], PDO::PARAM_BOOL);
        $stmt->bindParam(':calificacion', $datos['calificacion']);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['alojamiento_id'] : null;
    }

    /**
     * Actualiza un alojamiento existente
     */
    public function update($id, $datos)
    {
        $query = "UPDATE alojamiento SET
                    codigo = :codigo, titulo = :titulo, tipo_codigo = :tipo_codigo,
                    numero_habitaciones = :numero_habitaciones, numero_banos = :numero_banos,
                    bano_privado = :bano_privado, tamano_m2 = :tamano_m2,
                    genero_exclusivo_codigo = :genero_exclusivo_codigo,
                    mascotas_permitidas = :mascotas_permitidas, fumadores_permitidos = :fumadores_permitidos,
                    descripcion = :descripcion, usuario_id = :usuario_id,
                    ubicacion_id = :ubicacion_id, direccion = :direccion,
                    latitud = :latitud, longitud = :longitud,
                    precio_mensual = :precio_mensual, moneda_codigo = :moneda_codigo,
                    garantia = :garantia, duracion_minima_meses = :duracion_minima_meses,
                    fecha_disponible = :fecha_disponible, estado_codigo = :estado_codigo,
                    habilitado = :habilitado, precio_servicios = :precio_servicios,
                    amoblado = :amoblado, solo_verificados = :solo_verificados, calificacion = :calificacion
                  WHERE alojamiento_id = :id";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':codigo', $datos['codigo']);
        $stmt->bindParam(':titulo', $datos['titulo']);
        $stmt->bindParam(':tipo_codigo', $datos['tipo_codigo']);
        $stmt->bindParam(':numero_habitaciones', $datos['numero_habitaciones'], PDO::PARAM_INT);
        $stmt->bindParam(':numero_banos', $datos['numero_banios'], PDO::PARAM_INT);
        $stmt->bindParam(':bano_privado', $datos['bano_privado'], PDO::PARAM_BOOL);
        $stmt->bindParam(':tamano_m2', $datos['tamanio_m2']);
        $stmt->bindParam(':genero_exclusivo_codigo', $datos['genero_exclusivo_codigo']);
        $stmt->bindParam(':mascotas_permitidas', $datos['mascotas_permitidas'], PDO::PARAM_BOOL);
        $stmt->bindParam(':fumadores_permitidos', $datos['fumadores_permitidos'], PDO::PARAM_BOOL);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':usuario_id', $datos['usuario_id']);
        $stmt->bindParam(':ubicacion_id', $datos['ubicacion_id']);
        $stmt->bindParam(':direccion', $datos['direccion']);
        $stmt->bindParam(':latitud', $datos['latitud']);
        $stmt->bindParam(':longitud', $datos['longitud']);
        $stmt->bindParam(':precio_mensual', $datos['precio_mensual']);
        $stmt->bindParam(':moneda_codigo', $datos['moneda_codigo']);
        $stmt->bindParam(':garantia', $datos['garantia']);
        $stmt->bindParam(':duracion_minima_meses', $datos['duracion_minima_meses'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha_disponible', $datos['fecha_disponible']);
        $stmt->bindParam(':estado_codigo', $datos['estado_codigo']);
        $stmt->bindParam(':habilitado', $datos['habilitado'], PDO::PARAM_BOOL);
        $stmt->bindParam(':precio_servicios', $datos['precio_servicios']);
        $stmt->bindParam(':amoblado', $datos['amoblado'], PDO::PARAM_BOOL);
        $stmt->bindParam(':solo_verificados', $datos['solo_verificados'], PDO::PARAM_BOOL);
        $stmt->bindParam(':calificacion', $datos['calificacion']);

        return $stmt->execute();
    }

    /**
     * Cambia estado habilitado/deshabilitado
     */
    public function toggleHabilitado($id, $estado)
    {
        $query = "UPDATE alojamiento SET habilitado = :estado WHERE alojamiento_id = :id";
        $stmt = $this->db->prepare($query);
        $estadoVal = $estado ? true : false;
        $stmt->bindParam(':estado', $estadoVal, PDO::PARAM_BOOL);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Aprobar un alojamiento (cambiar estado_codigo a aprobado)
     */
    public function aprobar($id, $estado_codigo)
    {
        $query = "UPDATE alojamiento SET estado_codigo = :estado_codigo WHERE alojamiento_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':estado_codigo', $estado_codigo);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Obtiene propietarios (usuarios con rol OWNER) para el combo
     */
    public function getPropietarios()
    {
        $query = "SELECT u.usuario_id, u.nombres, u.apellido_paterno, u.correo
                  FROM usuario u
                  INNER JOIN rol r ON u.rol_id = r.rol_id
                  WHERE r.codigo = 'OWNER' AND u.habilitado = true
                  ORDER BY u.nombres ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
