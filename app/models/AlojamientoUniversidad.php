<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class AlojamientoUniversidad {
    private $db;
    
    /** Radio máximo en km para considerar cercanía */
    const RADIO_MAX_KM = 5;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Calcula la distancia entre dos puntos usando la fórmula de Haversine.
     * @return float Distancia en kilómetros
     */
    public static function haversine($lat1, $lon1, $lat2, $lon2) {
        $R = 6371; // Radio de la Tierra en km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($R * $c, 2);
    }

    /**
     * Sincroniza las relaciones de cercanía cuando se guarda/actualiza una UNIVERSIDAD.
     * Calcula la distancia contra todos los alojamientos con coordenadas.
     */
    public function sincronizarParaUniversidad($universidad_id, $lat_uni, $lng_uni) {
        if (!$lat_uni || !$lng_uni) return;

        // Obtener todos los alojamientos con coordenadas
        $query = "SELECT alojamiento_id, latitud, longitud FROM alojamiento 
                  WHERE latitud IS NOT NULL AND longitud IS NOT NULL AND habilitado = true";
        $stmt = $this->db->query($query);
        $alojamientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Eliminar relaciones previas para esta universidad
        $delQuery = "DELETE FROM alojamiento_universidad WHERE universidad_id = :universidad_id";
        $delStmt = $this->db->prepare($delQuery);
        $delStmt->execute([':universidad_id' => $universidad_id]);

        // Insertar las nuevas relaciones dentro del radio
        $insQuery = "INSERT INTO alojamiento_universidad (alojamiento_id, universidad_id, distancia_km) 
                     VALUES (:alojamiento_id, :universidad_id, :distancia_km)";
        $insStmt = $this->db->prepare($insQuery);

        foreach ($alojamientos as $aloj) {
            $distancia = self::haversine($lat_uni, $lng_uni, $aloj['latitud'], $aloj['longitud']);
            if ($distancia <= self::RADIO_MAX_KM) {
                $insStmt->execute([
                    ':alojamiento_id' => $aloj['alojamiento_id'],
                    ':universidad_id' => $universidad_id,
                    ':distancia_km' => $distancia
                ]);
            }
        }
    }

    /**
     * Sincroniza las relaciones de cercanía cuando se guarda/actualiza un ALOJAMIENTO.
     * Calcula la distancia contra todas las universidades con coordenadas.
     */
    public function sincronizarParaAlojamiento($alojamiento_id, $lat_aloj, $lng_aloj) {
        if (!$lat_aloj || !$lng_aloj) return;

        // Obtener todas las universidades con coordenadas
        $query = "SELECT universidad_id, latitud, longitud FROM universidad 
                  WHERE latitud IS NOT NULL AND longitud IS NOT NULL AND habilitado = true";
        $stmt = $this->db->query($query);
        $universidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Eliminar relaciones previas para este alojamiento
        $delQuery = "DELETE FROM alojamiento_universidad WHERE alojamiento_id = :alojamiento_id";
        $delStmt = $this->db->prepare($delQuery);
        $delStmt->execute([':alojamiento_id' => $alojamiento_id]);

        // Insertar las nuevas relaciones dentro del radio
        $insQuery = "INSERT INTO alojamiento_universidad (alojamiento_id, universidad_id, distancia_km) 
                     VALUES (:alojamiento_id, :universidad_id, :distancia_km)";
        $insStmt = $this->db->prepare($insQuery);

        foreach ($universidades as $uni) {
            $distancia = self::haversine($lat_aloj, $lng_aloj, $uni['latitud'], $uni['longitud']);
            if ($distancia <= self::RADIO_MAX_KM) {
                $insStmt->execute([
                    ':alojamiento_id' => $alojamiento_id,
                    ':universidad_id' => $uni['universidad_id'],
                    ':distancia_km' => $distancia
                ]);
            }
        }
    }
}
