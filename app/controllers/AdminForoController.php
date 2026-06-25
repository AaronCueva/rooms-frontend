<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Foro;
use App\Models\ForoComentario;
use App\Models\Usuario;
use App\Models\Multimedia;
use App\Models\Catalogo;

class AdminForoController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('/login');
        }
    }

    public function index()
    {
        $foroModel = new Foro();
        $catalogoModel = new Catalogo();

        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $por_pagina = 10;

        $filtros = [];
        if (!empty($_GET['busqueda'])) $filtros['busqueda'] = $_GET['busqueda'];
        if (!empty($_GET['categoria'])) $filtros['categoria'] = $_GET['categoria'];
        if (isset($_GET['estado']) && $_GET['estado'] !== '') $filtros['estado'] = $_GET['estado'];

        $total = $foroModel->contar($filtros);
        $total_paginas = max(1, ceil($total / $por_pagina));
        $pagina = min($pagina, $total_paginas);

        $foros = $foroModel->buscar($filtros, $pagina, $por_pagina);
        $categorias = $catalogoModel->obtenerPorReferencia('CATEGORIA_FORO');

        $data = [
            'titulo' => 'Gestion de Foros',
            'foros' => $foros,
            'categorias' => $categorias,
            'pagina' => $pagina,
            'total_paginas' => $total_paginas,
            'total' => $total,
            'filtros' => $filtros,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/foro/index', $data, 'admin');
    }

    public function ver()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirect('/admin/foros');
        }

        $foroModel = new Foro();
        $comentarioModel = new ForoComentario();
        $multimediaModel = new Multimedia();

        $foro = $foroModel->findById($id);
        
        if (!$foro) {
            $this->redirect('/admin/foros');
        }

        $comentarios = $comentarioModel->getByForoId($id);
        $multimedia = $multimediaModel->getByForoId($id);

        // Organizar comentarios en arbol (padres e hijos)
        $comentarios_padres = [];
        $comentarios_hijos = [];
        foreach ($comentarios as $c) {
            if (empty($c['comentario_padre_id'])) {
                $comentarios_padres[] = $c;
            } else {
                $comentarios_hijos[$c['comentario_padre_id']][] = $c;
            }
        }

        $data = [
            'titulo' => 'Ver Foro: ' . $foro['titulo'],
            'foro' => $foro,
            'comentarios_padres' => $comentarios_padres,
            'comentarios_hijos' => $comentarios_hijos,
            'multimedia' => $multimedia,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/foro/view', $data, 'admin');
    }

    public function verModal()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo '<div class="p-4 text-muted">Foro no encontrado.</div>';
            return;
        }

        $foroModel = new Foro();
        $comentarioModel = new ForoComentario();
        $multimediaModel = new Multimedia();

        $foro = $foroModel->findById($id);
        
        if (!$foro) {
            echo '<div class="p-4 text-muted">Foro no encontrado.</div>';
            return;
        }

        $comentarios = $comentarioModel->getByForoId($id);
        $multimedia = $multimediaModel->getByForoId($id);

        $comentarios_padres = [];
        $comentarios_hijos = [];
        foreach ($comentarios as $c) {
            if (empty($c['comentario_padre_id'])) {
                $comentarios_padres[] = $c;
            } else {
                $comentarios_hijos[$c['comentario_padre_id']][] = $c;
            }
        }

        $data = [
            'titulo' => 'Ver Foro: ' . $foro['titulo'],
            'foro' => $foro,
            'comentarios_padres' => $comentarios_padres,
            'comentarios_hijos' => $comentarios_hijos,
            'multimedia' => $multimedia,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/foro/view', $data, '');
    }

    public function editarForoModal()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo '<div class="p-4 text-muted">Foro no encontrado.</div>';
            return;
        }

        $foroModel = new Foro();
        $catalogoModel = new Catalogo();

        $foro = $foroModel->findById($id);
        if (!$foro) {
            echo '<div class="p-4 text-muted">Foro no encontrado.</div>';
            return;
        }

        $categorias = $catalogoModel->obtenerPorReferencia('CATEGORIA_FORO');
        $db = \App\Core\Database::getInstance()->getConnection();
        $universidades = $db->query("SELECT universidad_id, nombre FROM universidad WHERE habilitado = true ORDER BY nombre")->fetchAll();

        $data = [
            'foro' => $foro,
            'categorias' => $categorias,
            'universidades' => $universidades,
        ];

        $this->render('admin/foro/form', $data, '');
    }

    public function actualizarForo()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? null;
            if ($id) {
                $foroModel = new Foro();
                $foroModel->actualizar($id, $_POST);
                $this->setFlash('success', 'Foro actualizado correctamente.');
            } else {
                $this->setFlash('error', 'Error al actualizar el foro.');
            }
            $this->redirect('/admin/foros');
        }
    }

    public function editarComentarioModal()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo '<div class="p-4 text-muted">Comentario no encontrado.</div>';
            return;
        }

        $comentarioModel = new ForoComentario();
        $comentario = null;

        // Obtener solo este comentario - busqueda directa
        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM foro_comentario WHERE foro_comentario_id = ?");
        $stmt->execute([$id]);
        $comentario = $stmt->fetch();

        if (!$comentario) {
            echo '<div class="p-4 text-muted">Comentario no encontrado.</div>';
            return;
        }

        $data = ['comentario' => $comentario];
        $this->render('admin/foro/comentario_form', $data, '');
    }

    public function actualizarComentario()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['comentario_id'] ?? null;
            $foro_id = $_POST['foro_id'] ?? null;

            if ($id && isset($_POST['mensaje'])) {
                $comentarioModel = new ForoComentario();
                $comentarioModel->actualizar($id, $_POST['mensaje']);
                $this->setFlash('success', 'Comentario actualizado correctamente.');
            } else {
                $this->setFlash('error', 'Error al actualizar el comentario.');
            }

            $this->redirect('/admin/foros/ver?id=' . $foro_id);
        }
    }

    public function toggleEstado()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? null;
            $estado = $_POST['estado'] ?? 0;

            if ($id) {
                $foroModel = new Foro();
                $foroModel->toggleEstado($id, $estado);
                $nuevoEstado = $estado ? 'activado' : 'oculto';
                $this->setFlash('success', "Foro $nuevoEstado correctamente.");
            } else {
                $this->setFlash('error', 'Error al cambiar el estado del foro.');
            }
        }
        
        $this->redirect('/admin/foros');
    }

    public function eliminarComentario()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $comentario_id = $_POST['comentario_id'] ?? null;
            $foro_id = $_POST['foro_id'] ?? null;

            if ($comentario_id) {
                $comentarioModel = new ForoComentario();
                $comentarioModel->eliminar($comentario_id);
                $this->setFlash('success', 'Comentario ocultado correctamente.');
            } else {
                $this->setFlash('error', 'Error al ocultar el comentario.');
            }

            if ($foro_id) {
                $this->redirect('/admin/foros/ver?id=' . $foro_id);
            } else {
                $this->redirect('/admin/foros');
            }
        }
    }

    public function restaurarComentario()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $comentario_id = $_POST['comentario_id'] ?? null;
            $foro_id = $_POST['foro_id'] ?? null;

            if ($comentario_id) {
                $comentarioModel = new ForoComentario();
                $comentarioModel->restaurar($comentario_id);
                $this->setFlash('success', 'Comentario restaurado correctamente.');
            } else {
                $this->setFlash('error', 'Error al restaurar el comentario.');
            }

            if ($foro_id) {
                $this->redirect('/admin/foros/ver?id=' . $foro_id);
            } else {
                $this->redirect('/admin/foros');
            }
        }
    }

    public function toggleBanUsuario()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario_id = $_POST['usuario_id'] ?? null;
            $foro_id = $_POST['foro_id'] ?? null;
            $accion = $_POST['accion'] ?? 'banear';

            if ($usuario_id) {
                $usuarioModel = new Usuario();

                if ($accion == 'banear') {
                    $usuarioModel->banear($usuario_id);
                    $this->setFlash('success', 'Usuario baneado correctamente.');
                } else {
                    $usuarioModel->desbanear($usuario_id);
                    $this->setFlash('success', 'Usuario desbaneado correctamente.');
                }
            } else {
                $this->setFlash('error', 'Error al cambiar el estado del usuario.');
            }

            $this->redirect('/admin/foros/ver?id=' . $foro_id);
        }
    }
}
