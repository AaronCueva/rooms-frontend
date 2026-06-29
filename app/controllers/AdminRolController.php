<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Rol;
use App\Models\MenuMaestro;
use App\Models\MenuRol;

class AdminRolController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('/login');
        }
        // Idealmente validar aquí que el usuario actual tenga permisos para gestionar roles.
    }

    public function index()
    {
        $rolModel = new Rol();
        $roles = $rolModel->getAll();

        $data = [
            'titulo' => 'Gestión de Roles',
            'roles' => $roles,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/rol/index', $data, 'admin');
    }



    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = [
                'codigo' => $_POST['codigo'] ?? '',
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? ''
            ];

            $rolModel = new Rol();
            $rolModel->create($datos);
            self::setFlash('success', 'Rol creado exitosamente.');
        }
        $this->redirect('/admin/roles');
    }

    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['rol_id'] ?? null;
            if ($id) {
                $datos = [
                    'codigo' => $_POST['codigo'] ?? '',
                    'nombre' => $_POST['nombre'] ?? '',
                    'descripcion' => $_POST['descripcion'] ?? ''
                ];
                $rolModel = new Rol();
                $rolModel->update($id, $datos);
                self::setFlash('success', 'Rol actualizado exitosamente.');
            }
        }
        $this->redirect('/admin/roles');
    }

    public function toggleEstado()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? null;
            $estado = $_POST['estado'] ?? 0;

            if ($id) {
                $rolModel = new Rol();
                $rolModel->toggleHabilitado($id, $estado);
                self::setFlash('success', 'Estado del rol actualizado.');
            }
        }
        $this->redirect('/admin/roles');
    }

    // --- Permisos (Menu Rol) ---

    public function permisos()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/admin/roles');
        }

        $rolModel = new Rol();
        $rol = $rolModel->findById($id);

        if (!$rol) {
            $this->redirect('/admin/roles');
        }

        $menuMaestroModel = new MenuMaestro();
        $menus_arbol = $menuMaestroModel->obtenerTodosArbol();

        $menuRolModel = new MenuRol();
        $menus_asignados = $menuRolModel->getMenusByRol($id);

        $data = [
            'titulo' => 'Permisos del Rol: ' . $rol['nombre'],
            'rol' => $rol,
            'menus_arbol' => $menus_arbol,
            'menus_asignados' => $menus_asignados,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/rol/permisos', $data, 'admin');
    }

    public function guardarPermisos()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $rol_id = $_POST['rol_id'] ?? null;
            $menus_seleccionados = $_POST['menus'] ?? []; // Array de IDs de menús

            if ($rol_id) {
                $menuRolModel = new MenuRol();
                $exito = $menuRolModel->sincronizarPermisos($rol_id, $menus_seleccionados);
                
                if ($exito) {
                    self::setFlash('success', 'Permisos actualizados correctamente.');
                } else {
                    self::setFlash('error', 'Ocurrió un error al actualizar los permisos.');
                }
            }
        }
        $this->redirect('/admin/roles');
    }
}
