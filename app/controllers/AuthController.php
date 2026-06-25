<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;

class AuthController extends Controller
{

    public function login()
    {
        if (isset($_SESSION['usuario_id'])) {
            $this->redirect('/admin');
        }
        $this->render('auth/login', [], 'auth');
    }

    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $correo = $_POST['correo'] ?? '';
            $password = $_POST['password'] ?? '';

            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->findByEmail($correo);

            if ($usuario && password_verify($password, $usuario['password'])) {
                // Verificar si el usuario está baneado
                if (isset($usuario['habilitado']) && !$usuario['habilitado']) {
                    $this->render('auth/login', ['error' => 'Tu cuenta ha sido suspendida. Contacta al administrador.'], 'auth');
                    return;
                }

                // Iniciar sesión
                $_SESSION['usuario_id'] = $usuario['usuario_id'];
                $_SESSION['rol_id'] = $usuario['rol_id'];
                $_SESSION['nombres'] = $usuario['nombres'];

                $this->redirect('/admin');
            } else {
                // Credenciales incorrectas
                $this->render('auth/login', ['error' => 'Correo o contraseña incorrectos'], 'auth');
            }
        }
    }

    public function register()
    {
        if (isset($_SESSION['usuario_id'])) {
            $this->redirect('/admin');
        }

        $catalogoModel = new \App\Models\Catalogo();
        $tipos_documento = $catalogoModel->obtenerPorReferencia('TIPO_DOCUMENTO');

        $rolModel = new \App\Models\Rol();
        $roles = $rolModel->obtenerRolesRegistro();

        $ubicacionModel = new \App\Models\Ubicacion();
        $departamentos = $ubicacionModel->obtenerDepartamentos();

        $this->render('auth/register', [
            'tipos_documento' => $tipos_documento,
            'roles' => $roles,
            'departamentos' => $departamentos
        ], 'auth');
    }

    public function storeUser()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = [
                'correo' => $_POST['correo'] ?? '',
                'password' => $_POST['password'] ?? '',
                'nombres' => $_POST['nombres'] ?? '',
                'apellido_paterno' => $_POST['apellido_paterno'] ?? '',
                'apellido_materno' => $_POST['apellido_materno'] ?? '',
                'tipo_documento_codigo' => $_POST['tipo_documento_codigo'] ?? '',
                'numero_documento' => $_POST['numero_documento'] ?? '',
                'celular' => $_POST['celular'] ?? '',
                'rol_id' => $_POST['rol_id'] ?? null,
                'ubicacion_id' => $_POST['distrito'] ?? null // The selected district is the ubicacion
            ];

            $usuarioModel = new Usuario();

            // Validar si existe el correo
            if ($usuarioModel->findByEmail($datos['correo'])) {
                // Volvemos a cargar los combos para la vista
                $catalogoModel = new \App\Models\Catalogo();
                $tipos_documento = $catalogoModel->obtenerPorReferencia('TIPO_DOCUMENTO');
                $rolModel = new \App\Models\Rol();
                $roles = $rolModel->obtenerRolesRegistro();
                $ubicacionModel = new \App\Models\Ubicacion();
                $departamentos = $ubicacionModel->obtenerDepartamentos();

                $this->render('auth/register', [
                    'error' => 'El correo ya está registrado.',
                    'tipos_documento' => $tipos_documento,
                    'roles' => $roles,
                    'departamentos' => $departamentos
                ], 'auth');
                return;
            }

            if ($usuarioModel->create($datos)) {
                // Registro exitoso, redirigir a login
                $this->render('auth/login', ['success' => 'Registro exitoso. Inicie sesión.'], 'auth');
            } else {
                $catalogoModel = new \App\Models\Catalogo();
                $tipos_documento = $catalogoModel->obtenerPorReferencia('TIPO_DOCUMENTO');
                $rolModel = new \App\Models\Rol();
                $roles = $rolModel->obtenerRolesRegistro();
                $ubicacionModel = new \App\Models\Ubicacion();
                $departamentos = $ubicacionModel->obtenerDepartamentos();

                $this->render('auth/register', [
                    'error' => 'Error al registrar usuario.',
                    'tipos_documento' => $tipos_documento,
                    'roles' => $roles,
                    'departamentos' => $departamentos
                ], 'auth');
            }
        }
    }

    public function getUbicaciones()
    {
        header('Content-Type: application/json');
        $referencia_id = $_GET['referencia_id'] ?? null;

        if ($referencia_id) {
            $ubicacionModel = new \App\Models\Ubicacion();
            $ubicaciones = $ubicacionModel->obtenerPorReferencia($referencia_id);
            echo json_encode($ubicaciones);
        } else {
            echo json_encode([]);
        }
        exit;
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('/login');
    }
}
