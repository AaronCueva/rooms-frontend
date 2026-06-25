<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\UniversidadModel;
use App\Models\Ubicacion;

class AdminUniversidadController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('/login');
        }
    }

    public function index()
    {
        $universidadModel = new UniversidadModel();
        $universidades = $universidadModel->getAll();

        $data = [
            'titulo' => 'Gestión de Universidades',
            'universidades' => $universidades,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/universidad/index', $data, 'admin');
    }

    public function ver()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/admin/universidades');
        }

        $universidadModel = new UniversidadModel();
        $universidad = $universidadModel->findById($id);

        if (!$universidad) {
            $this->redirect('/admin/universidades');
        }

        $alojamientos = $universidadModel->getAlojamientosRelacionados($id);

        $data = [
            'titulo' => 'Detalle de la Universidad',
            'universidad' => $universidad,
            'alojamientos' => $alojamientos,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/universidad/view', $data, 'admin');
    }

    public function verModal()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) exit;

        $universidadModel = new UniversidadModel();
        $universidad = $universidadModel->findById($id);
        if (!$universidad) exit;

        $alojamientos = $universidadModel->getAlojamientosRelacionados($id);

        extract([
            'universidad' => $universidad,
            'alojamientos' => $alojamientos
        ]);
        
        require_once __DIR__ . '/../views/admin/universidad/modal_view.php';
    }

    public function crear()
    {
        $this->cargarDatosFormulario('Crear Nueva Universidad');
    }

    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/admin/universidades');
        }

        $universidadModel = new UniversidadModel();
        $universidad = $universidadModel->findById($id);

        if (!$universidad) {
            $this->redirect('/admin/universidades');
        }

        $this->cargarDatosFormulario('Editar Universidad', $universidad);
    }

    private function cargarDatosFormulario($titulo, $universidad = null)
    {
        $ubicacionModel = new Ubicacion();
        $departamentos = $ubicacionModel->obtenerDepartamentos();
        
        $jerarquia = null;
        if ($universidad && !empty($universidad['ubicacion_id'])) {
            $jerarquia = $ubicacionModel->obtenerJerarquia($universidad['ubicacion_id']);
        }

        $data = [
            'titulo' => $titulo,
            'universidad' => $universidad,
            'jerarquia' => $jerarquia,
            'departamentos' => $departamentos,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/universidad/form', $data, 'admin');
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = [
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'verificado' => isset($_POST['verificado']) ? 1 : 0,
                'habilitado' => isset($_POST['habilitado']) ? 1 : 0,
                'ubicacion_id' => !empty($_POST['distrito']) ? $_POST['distrito'] : null, // De ubicacion en cascada
                'direccion' => $_POST['direccion'] ?? ''
            ];

            $universidadModel = new UniversidadModel();
            $universidadModel->create($datos);
        }
        $this->redirect('/admin/universidades');
    }

    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['universidad_id'] ?? null;
            if ($id) {
                $datos = [
                    'nombre' => $_POST['nombre'] ?? '',
                    'descripcion' => $_POST['descripcion'] ?? '',
                    'verificado' => isset($_POST['verificado']) ? 1 : 0,
                    'habilitado' => isset($_POST['habilitado']) ? 1 : 0,
                    'ubicacion_id' => !empty($_POST['distrito']) ? $_POST['distrito'] : null,
                    'direccion' => $_POST['direccion'] ?? ''
                ];

                $universidadModel = new UniversidadModel();
                $universidadModel->update($id, $datos);
            }
        }
        $this->redirect('/admin/universidades');
    }

    public function toggleEstado()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? null;
            $estado = $_POST['estado'] ?? 0;

            if ($id) {
                $universidadModel = new UniversidadModel();
                $universidadModel->toggleHabilitado($id, $estado);
            }
        }
        $this->redirect('/admin/universidades');
    }
}
