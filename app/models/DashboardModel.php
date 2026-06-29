<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class DashboardModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Cuenta total de reservas activas (EPA001, EPA003)
     */
    public function getTotalReservasActivas()
    {
        $query = "SELECT COUNT(*) FROM reserva WHERE estado_codigo IN ('EPA001', 'EPA003')";
        $stmt = $this->db->query($query);
        return $stmt->fetchColumn() ?: 0;
    }

    /**
     * Cuenta total de usuarios activos (habilitado = true)
     */
    public function getTotalUsuariosActivos()
    {
        $query = "SELECT COUNT(*) FROM usuario WHERE habilitado = true";
        $stmt = $this->db->query($query);
        return $stmt->fetchColumn() ?: 0;
    }

    /**
     * Cuenta total de foros abiertos (habilitado = true)
     */
    public function getTotalForosAbiertos()
    {
        $query = "SELECT COUNT(*) FROM foro WHERE habilitado = true";
        $stmt = $this->db->query($query);
        return $stmt->fetchColumn() ?: 0;
    }

    /**
     * Promedio de satisfacción de reseñas
     */
    public function getPromedioSatisfaccion()
    {
        $query = "SELECT AVG(calificacion) FROM resenia_alojamiento WHERE habilitado = true AND calificacion IS NOT NULL";
        $stmt = $this->db->query($query);
        $promedio = $stmt->fetchColumn();
        return $promedio ? round($promedio, 1) : 0;
    }

    /**
     * Obtiene el conteo de reservas de los últimos 6 meses
     */
    public function getReservasUltimosMeses($meses = 6)
    {
        // Esta query obtiene la cantidad agrupada por mes en formato YYYY-MM
        $query = "SELECT TO_CHAR(fecha_solicitud, 'YYYY-MM') as mes, COUNT(*) as total 
                  FROM reserva 
                  WHERE fecha_solicitud >= CURRENT_DATE - INTERVAL '$meses months'
                  GROUP BY TO_CHAR(fecha_solicitud, 'YYYY-MM')
                  ORDER BY mes ASC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene la distribución de alojamientos por tipo (con nombres del catálogo)
     */
    public function getAlojamientosPorTipo()
    {
        $query = "SELECT c.nombre, COUNT(a.alojamiento_id) as total
                  FROM alojamiento a
                  INNER JOIN catalogo c ON a.tipo_codigo = c.codigo
                  WHERE a.habilitado = true
                  GROUP BY c.nombre
                  ORDER BY total DESC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
