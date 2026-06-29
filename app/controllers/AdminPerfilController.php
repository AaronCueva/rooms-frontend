<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;

class AdminPerfilController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('/login');
        }
    }

    public function password()
    {
        $data = [
            'titulo' => 'Cambiar Contraseña',
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];
        $this->render('admin/perfil/password', $data, 'admin');
    }

    public function actualizarPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $actual = $_POST['password_actual'] ?? '';
            $nuevo = $_POST['password_nuevo'] ?? '';
            $confirmacion = $_POST['password_confirmacion'] ?? '';
            $usuario_id = $_SESSION['usuario_id'];

            if ($nuevo !== $confirmacion) {
                self::setFlash('error', 'Las contraseñas nuevas no coinciden.');
                $this->redirect('/admin/perfil/password');
                return;
            }

            if (strlen($nuevo) < 6) {
                self::setFlash('error', 'La nueva contraseña debe tener al menos 6 caracteres.');
                $this->redirect('/admin/perfil/password');
                return;
            }

            $usuarioModel = new Usuario();
            $hashActual = $usuarioModel->obtenerPasswordHash($usuario_id);

            if (password_verify($actual, $hashActual)) {
                $nuevoHash = password_hash($nuevo, PASSWORD_BCRYPT);
                if ($usuarioModel->actualizarPassword($usuario_id, $nuevoHash)) {
                    self::setFlash('success', 'Tu contraseña ha sido actualizada correctamente.');
                } else {
                    self::setFlash('error', 'Hubo un error al actualizar la contraseña. Inténtalo de nuevo.');
                }
            } else {
                self::setFlash('error', 'La contraseña actual es incorrecta.');
            }
        }
        $this->redirect('/admin/perfil/password');
    }
}
