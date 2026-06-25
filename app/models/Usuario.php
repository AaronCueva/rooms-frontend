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
                    tipo_documento_codigo, numero_documento, celular, rol_id, ubicacion_id, estado_codigo, habilitado
                  ) 
                  VALUES (
                    :usuario,:correo, :password, :nombres, :apellido_paterno, :apellido_materno, 
                    :tipo_documento_codigo, :numero_documento, :celular, :rol_id, :ubicacion_id, 'ESU001', true
                  )";
        $stmt = $this->db->prepare($query);

        // Encriptar password
        $password_hash = password_hash($datos['password'], PASSWORD_BCRYPT);

        $stmt->bindParam(':usuario', $datos['correo']);
        $stmt->bindParam(':correo', $datos['correo']);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':nombres', $datos['nombres']);
        $stmt->bindParam(':apellido_paterno', $datos['apellido_paterno']);
        $stmt->bindParam(':apellido_materno', $datos['apellido_materno']);
        $stmt->bindParam(':tipo_documento_codigo', $datos['tipo_documento_codigo']);
        $stmt->bindParam(':numero_documento', $datos['numero_documento']);
        $stmt->bindParam(':celular', $datos['celular']);
        $stmt->bindParam(':rol_id', $datos['rol_id'], PDO::PARAM_NULL);
        $stmt->bindParam(':ubicacion_id', $datos['ubicacion_id'], PDO::PARAM_NULL);

        return $stmt->execute();
    }
}
