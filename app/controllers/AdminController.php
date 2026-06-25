<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Menu;

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

        $data = [
            'titulo' => 'Dashboard',
            'menus' => $menus,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/dashboard', $data, 'admin');
    }
}
