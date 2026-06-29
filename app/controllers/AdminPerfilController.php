<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;
use App\Models\Catalogo;
use App\Models\Ubicacion;
use App\Models\UniversidadModel;

class AdminPerfilController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('/login');
        }
    }

    public function index()
    {
        $usuarioModel = new Usuario();
        $catalogoModel = new Catalogo();
        $ubicacionModel = new Ubicacion();
        $universidadModel = new UniversidadModel();

        $usuario_id = $_SESSION['usuario_id'];
        $usuario = $usuarioModel->findById($usuario_id);

        $generos = $catalogoModel->obtenerPorReferencia('GENERO');
        $tiposDocumento = $catalogoModel->obtenerPorReferencia('TIPO_DOCUMENTO');
        $departamentos = $ubicacionModel->obtenerDepartamentos();

        $jerarquia = null;
        if (!empty($usuario['ubicacion_id'])) {
            $jerarquia = $ubicacionModel->obtenerJerarquia($usuario['ubicacion_id']);
        }

        $universidad_nombre = '';
        if (!empty($usuario['universidad_id'])) {
            $universidad = $universidadModel->findById($usuario['universidad_id']);
            if ($universidad) {
                $universidad_nombre = $universidad['nombre'];
            }
        }

        $data = [
            'titulo' => 'Mi Perfil',
            'usuario' => $usuario,
            'generos' => $generos,
            'tiposDocumento' => $tiposDocumento,
            'departamentos' => $departamentos,
            'jerarquia' => $jerarquia,
            'universidad_nombre' => $universidad_nombre,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/perfil/index', $data, 'admin');
    }

    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario_id = $_SESSION['usuario_id'];
            
            $datos = [
                'nombres' => $_POST['nombres'] ?? null,
                'apellido_paterno' => $_POST['apellido_paterno'] ?? null,
                'apellido_materno' => $_POST['apellido_materno'] ?? null,
                'tipo_documento_codigo' => $_POST['tipo_documento_codigo'] ?? null,
                'numero_documento' => $_POST['numero_documento'] ?? null,
                'correo' => $_POST['correo'] ?? null,
                'celular' => $_POST['celular'] ?? null,
                'telefono' => $_POST['telefono'] ?? null,
                'razon_social' => $_POST['razon_social'] ?? null,
                'nombre_comercial' => $_POST['nombre_comercial'] ?? null,
                'descripcion' => $_POST['descripcion'] ?? null,
                'genero_codigo' => $_POST['genero_codigo'] ?? null,
                'ubicacion_id' => !empty($_POST['distrito']) ? $_POST['distrito'] : null,
                'universidad_id' => !empty($_POST['universidad_id']) ? $_POST['universidad_id'] : null,
            ];

            // Manejo de la subida de foto
            if (!empty($_FILES['foto_perfil']['name'])) {
                $uploadDir = __DIR__ . '/../../public/uploads/usuarios/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $maxSize = 2 * 1024 * 1024; // 2MB
                $ext = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];

                if ($_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK && $_FILES['foto_perfil']['size'] <= $maxSize && in_array($ext, $allowed)) {
                    $newName = uniqid('user_') . '.' . $ext;
                    $destPath = $uploadDir . $newName;

                    if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $destPath)) {
                        $datos['url_foto'] = '/public/uploads/usuarios/' . $newName;
                        
                        // Actualizar la sesión para que se refleje inmediatamente en el navbar
                        $_SESSION['url_foto'] = $datos['url_foto'];
                    }
                }
            }

            $usuarioModel = new Usuario();
            if ($usuarioModel->actualizarPerfil($usuario_id, $datos)) {
                // Actualizar nombres en sesión por si los cambió
                $_SESSION['nombres'] = $datos['nombres'];
                self::setFlash('success', 'Perfil actualizado correctamente.');
            } else {
                self::setFlash('error', 'Ocurrió un error al actualizar el perfil.');
            }
        }
        $this->redirect('/admin/perfil');
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
