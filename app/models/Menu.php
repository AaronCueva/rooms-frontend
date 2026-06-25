<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Menu {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getMenuByRol($rol_id) {
        // Obtenemos los menús basados en el rol del usuario
        // Tablas: menu_maestro, menu_rol
        $query = "
            SELECT m.menu_maestro_id, m.nombre, m.url, m.descripcion
            FROM menu_maestro m
            INNER JOIN menu_rol mr ON m.menu_maestro_id = mr.menu_maestro_id
            WHERE mr.rol_id = :rol_id AND m.habilitado = 1
            ORDER BY m.orden ASC
        ";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':rol_id', $rol_id);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            // Si las tablas no existen, devolveremos un menú por defecto temporal para no romper la app en la prueba
            return [
                ['url' => '/admin', 'nombre' => 'Dashboard (Default)', 'descripcion' => 'Dashboard'],
                ['url' => '/admin/reservas', 'nombre' => 'Reservas', 'descripcion' => 'Gestión de Reservas'],
                ['url' => '/admin/usuarios', 'nombre' => 'Usuarios', 'descripcion' => 'Gestión de Usuarios']
            ];
        }
    }
}
