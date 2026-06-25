<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Foro;
use App\Models\ForoComentario;
use App\Models\Usuario;

class AdminForoController extends Controller
{
    public function __construct()
    {
        // Proteger la ruta si requiere autenticación
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('/login');
        }
    }

    public function index()
    {
        $foroModel = new Foro();
        $foros = $foroModel->getAllForos();

        $data = [
            'titulo' => 'Gestión de Foros',
            'foros' => $foros,
            // Aquí podríamos enviar el nombre de usuario de la sesión para el menú superior
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        // Renderizar la vista dentro del layout de admin
        $this->render('admin/foro/index', $data, 'admin');
    }

    public function ver()
    {
        // Obtener el ID de la URL usando $_GET (ej: /admin/foros/ver?id=1)
        // Ya que el enrutador actual parece simple y no inyecta parámetros en la función
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirect('/admin/foros');
        }

        $foroModel = new Foro();
        $comentarioModel = new ForoComentario();

        $foro = $foroModel->findById($id);
        
        if (!$foro) {
            $this->redirect('/admin/foros');
        }

        $comentarios = $comentarioModel->getByForoId($id);

        $data = [
            'titulo' => 'Ver Foro: ' . $foro['titulo'],
            'foro' => $foro,
            'comentarios' => $comentarios,
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

        $foro = $foroModel->findById($id);
        
        if (!$foro) {
            echo '<div class="p-4 text-muted">Foro no encontrado.</div>';
            return;
        }

        $comentarios = $comentarioModel->getByForoId($id);

        $data = [
            'titulo' => 'Ver Foro: ' . $foro['titulo'],
            'foro' => $foro,
            'comentarios' => $comentarios,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/foro/view', $data, '');
    }

    public function toggleEstado()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? null;
            $estado = $_POST['estado'] ?? 0;

            if ($id) {
                $foroModel = new Foro();
                $foroModel->toggleEstado($id, $estado);
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
                } else {
                    $usuarioModel->desbanear($usuario_id);
                }
            }

            $this->redirect('/admin/foros/ver?id=' . $foro_id);
        }
    }
}
