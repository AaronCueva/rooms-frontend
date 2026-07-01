<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Multimedia {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getByForoId($foro_id) {
        $query = "SELECT * FROM multimedia WHERE foro_id = :foro_id AND habilitado = true ORDER BY orden ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':foro_id' => $foro_id]);
        return $stmt->fetchAll();
    }

    public function getByAlojamientoId($alojamiento_id) {
        $query = "SELECT * FROM multimedia WHERE alojamiento_id = :alojamiento_id AND habilitado = true ORDER BY orden ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':alojamiento_id' => $alojamiento_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $query = "SELECT * FROM multimedia WHERE multimedia_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($datos) {
        $query = "INSERT INTO multimedia (url, tipo_codigo, nombre, orden, usuario_id, alojamiento_id, foro_id, creado_por)
                  VALUES (:url, :tipo_codigo, :nombre, :orden, :usuario_id, :alojamiento_id, :foro_id, :creado_por)
                  RETURNING multimedia_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':url'            => $datos['url'] ?? null,
            ':tipo_codigo'    => $datos['tipo_codigo'] ?? null,
            ':nombre'         => $datos['nombre'] ?? null,
            ':orden'          => $datos['orden'] ?? 0,
            ':usuario_id'     => $datos['usuario_id'] ?? null,
            ':alojamiento_id' => $datos['alojamiento_id'] ?? null,
            ':foro_id'        => $datos['foro_id'] ?? null,
            ':creado_por'     => $datos['creado_por'] ?? null
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['multimedia_id'] : null;
    }

    public function delete($id) {
        $query = "UPDATE multimedia SET habilitado = false, modificado = CURRENT_TIMESTAMP WHERE multimedia_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
    }

    public function hardDelete($id) {
        // Elimina físicamente el registro
        $query = "DELETE FROM multimedia WHERE multimedia_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
    }
}
