<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Rol {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene los roles específicos requeridos para el registro
     */
    public function obtenerRolesRegistro() {
        $query = "SELECT rol_id, codigo, nombre 
                  FROM rol 
                  WHERE codigo IN ('ADMIN_SOCIALES', 'ADMIN_RESERVAS', 'OWNER')
                  AND habilitado = true";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
