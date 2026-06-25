<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class AlojamientoPolitica
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtiene las políticas asignadas a un alojamiento
     */
    public function getByAlojamientoId($alojamiento_id)
    {
        $query = "SELECT ap.alojamiento_politica_id, ap.politica_casa_id, ap.habilitado,
                         pc.codigo, pc.nombre, pc.descripcion
                  FROM alojamiento_politica ap
                  INNER JOIN politica_casa pc ON ap.politica_casa_id = pc.politica_casa_id
                  WHERE ap.alojamiento_id = :alojamiento_id
                  ORDER BY pc.nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':alojamiento_id', $alojamiento_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtiene solo los IDs de las políticas asignadas a un alojamiento
     */
    public function getIdsByAlojamientoId($alojamiento_id)
    {
        $query = "SELECT politica_casa_id FROM alojamiento_politica WHERE alojamiento_id = :alojamiento_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':alojamiento_id', $alojamiento_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Sincroniza las políticas de un alojamiento:
     * elimina las actuales e inserta las nuevas seleccionadas.
     */
    public function sincronizarPoliticas($alojamiento_id, $politicas_ids)
    {
        // Eliminar todas las políticas actuales
        $queryDelete = "DELETE FROM alojamiento_politica WHERE alojamiento_id = :alojamiento_id";
        $stmtDelete = $this->db->prepare($queryDelete);
        $stmtDelete->bindParam(':alojamiento_id', $alojamiento_id);
        $stmtDelete->execute();

        // Insertar las nuevas
        if (!empty($politicas_ids)) {
            $queryInsert = "INSERT INTO alojamiento_politica (alojamiento_id, politica_casa_id, habilitado)
                            VALUES (:alojamiento_id, :politica_casa_id, true)";
            $stmtInsert = $this->db->prepare($queryInsert);

            foreach ($politicas_ids as $politica_id) {
                $stmtInsert->bindParam(':alojamiento_id', $alojamiento_id);
                $stmtInsert->bindParam(':politica_casa_id', $politica_id);
                $stmtInsert->execute();
            }
        }
    }
}
