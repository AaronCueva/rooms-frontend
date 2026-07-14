<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Resena;
use App\Models\Catalogo;

class AdminResenaController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('/login');
        }
    }

    public function index()
    {
        $resenaModel = new Resena();
        $catalogoModel = new Catalogo();

        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $por_pagina = 10;

        $filtros = [];
        if (!empty($_GET['busqueda'])) $filtros['busqueda'] = trim($_GET['busqueda']);
        if (!empty($_GET['calificacion'])) $filtros['calificacion'] = $_GET['calificacion'];
        if (isset($_GET['estado']) && $_GET['estado'] !== '') $filtros['estado'] = $_GET['estado'];

        $total = $resenaModel->contar($filtros);
        $total_paginas = max(1, ceil($total / $por_pagina));
        $pagina = min($pagina, $total_paginas);

        $resenas = $resenaModel->buscar($filtros, $pagina, $por_pagina);
        $estados = $catalogoModel->obtenerPorReferencia('ESTADO_RESENIA_ALOJAMIENTO');

        $data = [
            'titulo' => 'Gestión de Reseñas',
            'resenas' => $resenas,
            'estados' => $estados,
            'pagina' => $pagina,
            'total_paginas' => $total_paginas,
            'total' => $total,
            'filtros' => $filtros,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/resena/index', $data, 'admin');
    }

    public function verModal()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo '<div class="p-4 text-muted">Reseña no encontrada.</div>';
            return;
        }

        $resenaModel = new Resena();
        $item = $resenaModel->findDetalleById($id);

        if (!$item) {
            $item = $resenaModel->findById($id);
            if (!$item) {
                echo '<div class="p-4 text-muted">Reseña no encontrada.</div>';
                return;
            }
        }

        $data = [
            'titulo' => 'Ver Reseña #' . substr($item['resena_id'], 0, 8),
            'item' => $item
        ];

        $this->render('admin/resena/view', $data, '');
    }

    public function toggleEstado()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            if ($id) {
                $resenaModel = new Resena();
                $resenaModel->toggleEstado($id);
                $this->setFlash('success', 'El estado de visibilidad de la reseña ha sido actualizado correctamente.');
            }
        }
        $this->redirect('/admin/resenas');
    }

    public function cambiarEstado()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $estado_codigo = $_POST['estado_codigo'] ?? null;
            if ($id && $estado_codigo) {
                $resenaModel = new Resena();
                $resenaModel->cambiarEstado($id, $estado_codigo);
                $this->setFlash('success', 'El estado de moderación ha sido actualizado correctamente.');
            }
        }
        $this->redirect('/admin/resenas');
    }
}
