<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;

class AuthController extends Controller {

    public function login() {
        if (isset($_SESSION['usuario_id'])) {
            $this->redirect('/admin');
        }
        $this->render('auth/login', [], 'auth');
    }

    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $correo = $_POST['correo'] ?? '';
            $password = $_POST['password'] ?? '';

            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->findByEmail($correo);

            if ($usuario && password_verify($password, $usuario['password'])) {
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

    public function register() {
        if (isset($_SESSION['usuario_id'])) {
            $this->redirect('/admin');
        }

        // Cargar tipos de documento desde el catálogo
        $catalogoModel = new \App\Models\Catalogo();
        $tipos_documento = $catalogoModel->obtenerPorReferencia('TIPO_DOCUMENTO');

        $this->render('auth/register', ['tipos_documento' => $tipos_documento], 'auth');
    }

    public function storeUser() {
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
                // Para las pruebas, le asignaremos null al rol_id o podrías consultar el rol "Administrador" de la BD
                'rol_id' => null 
            ];

            $usuarioModel = new Usuario();
            
            // Validar si existe el correo
            if ($usuarioModel->findByEmail($datos['correo'])) {
                // Volvemos a cargar los tipos de documento para la vista
                $catalogoModel = new \App\Models\Catalogo();
                $tipos_documento = $catalogoModel->obtenerPorReferencia('TIPO_DOCUMENTO');
                
                $this->render('auth/register', [
                    'error' => 'El correo ya está registrado.',
                    'tipos_documento' => $tipos_documento
                ], 'auth');
                return;
            }

            if ($usuarioModel->create($datos)) {
                // Registro exitoso, redirigir a login
                $this->render('auth/login', ['success' => 'Registro exitoso. Inicie sesión.'], 'auth');
            } else {
                $catalogoModel = new \App\Models\Catalogo();
                $tipos_documento = $catalogoModel->obtenerPorReferencia('TIPO_DOCUMENTO');
                
                $this->render('auth/register', [
                    'error' => 'Error al registrar usuario.',
                    'tipos_documento' => $tipos_documento
                ], 'auth');
            }
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect('/login');
    }
}
