<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class MenuRol {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getMenusByRol($rol_id) {
        $query = "SELECT menu_maestro_id FROM menu_rol WHERE rol_id = :rol_id AND habilitado = true";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':rol_id' => $rol_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN); // Devuelve un array plano de IDs
    }

    public function sincronizarPermisos($rol_id, $menus_ids) {
        try {
            $this->db->beginTransaction();

            // 1. Deshabilitar todos los permisos actuales (Soft delete)
            $queryDisable = "UPDATE menu_rol SET habilitado = false WHERE rol_id = :rol_id";
            $stmtDisable = $this->db->prepare($queryDisable);
            $stmtDisable->execute([':rol_id' => $rol_id]);

            // 2. Insertar o reactivar los nuevos
            if (!empty($menus_ids)) {
                $queryUpsert = "
                    INSERT INTO menu_rol (menu_maestro_id, rol_id, habilitado)
                    VALUES (:menu_id, :rol_id, true)
                    ON CONFLICT (menu_maestro_id, rol_id) DO UPDATE SET habilitado = true
                ";
                // Note: The ON CONFLICT requires a unique constraint on (menu_maestro_id, rol_id).
                // Si la DB no lo tiene, haremos un fallback más seguro para Postgres genérico sin constraints únicos explícitos conocidos
                
                // Opción más segura sin ON CONFLICT si no estamos seguros del constraint único:
                // Delete físico y luego Insert
                
                $queryDeleteFisico = "DELETE FROM menu_rol WHERE rol_id = :rol_id";
                $stmtDel = $this->db->prepare($queryDeleteFisico);
                $stmtDel->execute([':rol_id' => $rol_id]);

                $queryInsert = "INSERT INTO menu_rol (menu_maestro_id, rol_id, habilitado) VALUES (:menu_id, :rol_id, true)";
                $stmtInsert = $this->db->prepare($queryInsert);

                foreach ($menus_ids as $menu_id) {
                    $stmtInsert->execute([
                        ':menu_id' => $menu_id,
                        ':rol_id' => $rol_id
                    ]);
                }
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
