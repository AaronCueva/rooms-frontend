<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Ubicacion {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene todos los departamentos (TPU002) del país (Perú)
     */
    public function obtenerDepartamentos() {
        $query = "SELECT ubicacion_id, nombre 
                  FROM ubicacion 
                  WHERE tipo_ubicacion_codigo = 'TPU002' 
                  ORDER BY nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtiene las ubicaciones hijas basadas en la referencia_id del padre
     */
    public function obtenerPorReferencia($referencia_id) {
        $query = "SELECT ubicacion_id, nombre 
                  FROM ubicacion 
                  WHERE referencia_id = :referencia_id 
                  ORDER BY nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':referencia_id', $referencia_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerJerarquia($distrito_id) {
        $q = "SELECT ubicacion_id, referencia_id FROM ubicacion WHERE ubicacion_id = :id";
        
        $stmt1 = $this->db->prepare($q);
        $stmt1->bindParam(':id', $distrito_id);
        $stmt1->execute();
        $distrito = $stmt1->fetch();
        if (!$distrito) return null;

        $stmt2 = $this->db->prepare($q);
        $stmt2->bindParam(':id', $distrito['referencia_id']);
        $stmt2->execute();
        $provincia = $stmt2->fetch();
        if (!$provincia) return null;

        return [
            'distrito_id' => $distrito['ubicacion_id'],
            'provincia_id' => $provincia['ubicacion_id'],
            'departamento_id' => $provincia['referencia_id']
        ];
    }
}
