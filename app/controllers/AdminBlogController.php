<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Blog;
use App\Models\Catalogo;

class AdminBlogController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('/login');
        }
    }

    public function index()
    {
        $blogModel = new Blog();
        $catalogoModel = new Catalogo();

        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $por_pagina = 10;

        $filtros = [];
        if (!empty($_GET['busqueda'])) $filtros['busqueda'] = trim($_GET['busqueda']);
        if (!empty($_GET['estado'])) $filtros['estado'] = $_GET['estado'];

        $total = $blogModel->contar($filtros);
        $total_paginas = max(1, ceil($total / $por_pagina));
        $pagina = min($pagina, $total_paginas);

        $artículos = $blogModel->buscar($filtros, $pagina, $por_pagina);
        $estados = $catalogoModel->obtenerPorReferencia('ESTADO_BLOG');

        $data = [
            'titulo' => 'Blog / Guía del Universitario',
            'articulos' => $artículos,
            'estados' => $estados,
            'pagina' => $pagina,
            'total_paginas' => $total_paginas,
            'total' => $total,
            'filtros' => $filtros,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/blog/index', $data, 'admin');
    }

    public function verModal()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo '<div class="p-4 text-muted">Artículo no encontrado.</div>';
            return;
        }

        $blogModel = new Blog();
        $item = $blogModel->findById($id);

        if (!$item) {
            echo '<div class="p-4 text-muted">Artículo no encontrado.</div>';
            return;
        }

        $data = [
            'titulo' => 'Vista Previa del Artículo',
            'item' => $item
        ];

        $this->render('admin/blog/view', $data, '');
    }

    public function crearModal()
    {
        $catalogoModel = new Catalogo();
        $estados = $catalogoModel->obtenerPorReferencia('ESTADO_BLOG');

        $data = [
            'titulo' => 'Redactar Nuevo Artículo',
            'item' => null,
            'estados' => $estados
        ];

        $this->render('admin/blog/form', $data, '');
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = trim($_POST['titulo'] ?? '');
            $contenido = trim($_POST['contenido'] ?? '');
            $estado_codigo = $_POST['estado_codigo'] ?? 'ESBL001';

            if (!empty($titulo) && !empty($contenido)) {
                $blogModel = new Blog();
                $datos = [
                    'titulo' => $titulo,
                    'contenido' => $contenido,
                    'estado_codigo' => $estado_codigo,
                    'usuario_id' => $_SESSION['usuario_id'],
                    'creado_por' => $_SESSION['correo'] ?? $_SESSION['nombres'] ?? 'Admin'
                ];

                if ($blogModel->crear($datos)) {
                    $this->setFlash('success', 'El artículo ha sido creado y registrado correctamente.');
                } else {
                    $this->setFlash('error', 'Ocurrió un error al intentar guardar el artículo.');
                }
            } else {
                $this->setFlash('error', 'El título y el contenido son campos obligatorios.');
            }
        }
        $this->redirect('/admin/blog');
    }

    public function editarModal()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo '<div class="p-4 text-muted">Artículo no encontrado.</div>';
            return;
        }

        $blogModel = new Blog();
        $item = $blogModel->findById($id);

        if (!$item) {
            echo '<div class="p-4 text-muted">Artículo no encontrado.</div>';
            return;
        }

        $catalogoModel = new Catalogo();
        $estados = $catalogoModel->obtenerPorReferencia('ESTADO_BLOG');

        $data = [
            'titulo' => 'Editar Artículo',
            'item' => $item,
            'estados' => $estados
        ];

        $this->render('admin/blog/form', $data, '');
    }

    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['blog_id'] ?? null;
            $titulo = trim($_POST['titulo'] ?? '');
            $contenido = trim($_POST['contenido'] ?? '');
            $estado_codigo = $_POST['estado_codigo'] ?? 'ESBL001';

            if ($id && !empty($titulo) && !empty($contenido)) {
                $blogModel = new Blog();
                $datos = [
                    'titulo' => $titulo,
                    'contenido' => $contenido,
                    'estado_codigo' => $estado_codigo,
                    'modificado_por' => $_SESSION['correo'] ?? $_SESSION['nombres'] ?? 'Admin'
                ];

                if ($blogModel->actualizar($id, $datos)) {
                    $this->setFlash('success', 'El artículo ha sido actualizado correctamente.');
                } else {
                    $this->setFlash('error', 'No se pudieron aplicar los cambios al artículo.');
                }
            } else {
                $this->setFlash('error', 'Datos incompletos o inválidos para la actualización.');
            }
        }
        $this->redirect('/admin/blog');
    }

    public function cambiarEstado()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $estado_codigo = $_POST['estado_codigo'] ?? null;
            if ($id && $estado_codigo) {
                $blogModel = new Blog();
                $user = $_SESSION['correo'] ?? $_SESSION['nombres'] ?? 'Admin';
                $blogModel->cambiarEstado($id, $estado_codigo, $user);
                $this->setFlash('success', 'El estado del artículo ha sido modificado exitosamente.');
            }
        }
        $this->redirect('/admin/blog');
    }

    public function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            if ($id) {
                $blogModel = new Blog();
                $user = $_SESSION['correo'] ?? $_SESSION['nombres'] ?? 'Admin';
                $blogModel->eliminar($id, $user);
                $this->setFlash('success', 'El artículo ha sido archivado/eliminado correctamente.');
            }
        }
        $this->redirect('/admin/blog');
    }
}
