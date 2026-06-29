<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Menu;
use App\Models\DashboardModel;

class AdminController extends Controller
{

    public function __construct()
    {
        // Protección de ruta: Solo usuarios logueados pueden acceder al panel
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('/login');
        }
    }

    public function dashboard()
    {
        $menuModel = new Menu();
        $rol_id = $_SESSION['rol_id'] ?? 1;
        $menus = $menuModel->getMenuByRol($rol_id);

        $dashboardModel = new DashboardModel();
        
        $totalReservas = $dashboardModel->getTotalReservasActivas();
        $totalUsuarios = $dashboardModel->getTotalUsuariosActivos();
        $totalForos = $dashboardModel->getTotalForosAbiertos();
        $promedioSatisfaccion = $dashboardModel->getPromedioSatisfaccion();
        
        $reservasMeses = $dashboardModel->getReservasUltimosMeses(6);
        $alojamientosTipo = $dashboardModel->getAlojamientosPorTipo();

        $data = [
            'titulo' => 'Dashboard',
            'menus' => $menus,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador',
            'totalReservas' => $totalReservas,
            'totalUsuarios' => $totalUsuarios,
            'totalForos' => $totalForos,
            'promedioSatisfaccion' => $promedioSatisfaccion,
            'reservasMeses' => $reservasMeses,
            'alojamientosTipo' => $alojamientosTipo
        ];

        $this->render('admin/dashboard', $data, 'admin');
    }
}
