<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\UniversidadModel;
use App\Models\Ubicacion;
use App\Models\AlojamientoUniversidad;

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

        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $por_pagina = 10;

        $filtros = [];
        if (!empty($_GET['busqueda'])) $filtros['busqueda'] = $_GET['busqueda'];
        if (isset($_GET['estado']) && $_GET['estado'] !== '') $filtros['estado'] = $_GET['estado'];

        $total = $universidadModel->contar($filtros);
        $total_paginas = max(1, ceil($total / $por_pagina));
        $pagina = min($pagina, $total_paginas);

        $universidades = $universidadModel->buscar($filtros, $pagina, $por_pagina);

        $data = [
            'titulo' => 'Gestión de Universidades',
            'universidades' => $universidades,
            'pagina' => $pagina,
            'total_paginas' => $total_paginas,
            'total' => $total,
            'filtros' => $filtros,
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
        if (!$id) exit('No ID provided');

        $universidadModel = new UniversidadModel();
        $universidad = $universidadModel->findById($id);
        if (!$universidad) exit('Universidad no encontrada con ID ' . $id);

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
                'ubicacion_id' => !empty($_POST['distrito']) ? $_POST['distrito'] : null,
                'direccion' => $_POST['direccion'] ?? '',
                'latitud' => !empty($_POST['latitud']) ? $_POST['latitud'] : null,
                'longitud' => !empty($_POST['longitud']) ? $_POST['longitud'] : null
            ];

            $universidadModel = new UniversidadModel();
            $universidad_id = $universidadModel->create($datos);

            // Sincronizar distancias con alojamientos cercanos
            if ($universidad_id && !empty($datos['latitud']) && !empty($datos['longitud'])) {
                $auModel = new AlojamientoUniversidad();
                $auModel->sincronizarParaUniversidad($universidad_id, $datos['latitud'], $datos['longitud']);
            }

            self::setFlash('success', 'Universidad creada exitosamente.');
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
                    'direccion' => $_POST['direccion'] ?? '',
                    'latitud' => !empty($_POST['latitud']) ? $_POST['latitud'] : null,
                    'longitud' => !empty($_POST['longitud']) ? $_POST['longitud'] : null
                ];

                $universidadModel = new UniversidadModel();
                $universidadModel->update($id, $datos);

                // Sincronizar distancias con alojamientos cercanos
                if (!empty($datos['latitud']) && !empty($datos['longitud'])) {
                    $auModel = new AlojamientoUniversidad();
                    $auModel->sincronizarParaUniversidad($id, $datos['latitud'], $datos['longitud']);
                }

                self::setFlash('success', 'Universidad actualizada exitosamente.');
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
