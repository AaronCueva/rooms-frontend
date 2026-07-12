<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\UniversidadModel;
use App\Models\Catalogo;

class AdminUsuarioController extends Controller
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
        $rolModel = new Rol();

        $filtros = [
            'busqueda' => $_GET['busqueda'] ?? '',
            'rol_id'   => $_GET['rol_id'] ?? '',
            'estado'   => $_GET['estado'] ?? ''
        ];

        $pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
        $por_pagina = 15;

        $usuarios = $usuarioModel->buscar($filtros, $pagina, $por_pagina);
        $total_usuarios = $usuarioModel->contar($filtros);
        $total_paginas = ceil($total_usuarios / $por_pagina);

        $roles = $rolModel->getAll();

        $data = [
            'titulo'         => 'Administración de Usuarios',
            'usuarios'       => $usuarios,
            'roles'          => $roles,
            'filtros'        => $filtros,
            'pagina_actual'  => $pagina,
            'total_paginas'  => $total_paginas,
            'total_usuarios' => $total_usuarios,
            'nombre_usuario' => $_SESSION['nombres'] ?? 'Administrador'
        ];

        $this->render('admin/usuario/index', $data, 'admin');
    }

    public function verModal()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "<div class='p-4 text-center text-red-600'>ID de usuario no especificado.</div>";
            return;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->findById($id);

        if (!$usuario) {
            echo "<div class='p-4 text-center text-red-600'>Usuario no encontrado.</div>";
            return;
        }

        $this->render('admin/usuario/view_modal', ['usuario' => $usuario], '');
    }

    public function crearModal()
    {
        $rolModel = new Rol();
        $uniModel = new UniversidadModel();
        $catalogoModel = new Catalogo();

        $data = [
            'roles' => $rolModel->getAll(),
            'universidades' => $uniModel->getAll(),
            'tipos_documento' => $catalogoModel->obtenerPorReferencia('TIPO_DOCUMENTO'),
            'generos' => $catalogoModel->obtenerPorReferencia('GENERO'),
            'es_creacion' => true,
            'usuario' => []
        ];

        $this->render('admin/usuario/form_modal', $data, '');
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $correo = trim($_POST['correo'] ?? '');
            $password = $_POST['password'] ?? '';
            $nombres = trim($_POST['nombres'] ?? '');
            $rol_id = $_POST['rol_id'] ?? null;

            if (empty($correo) || empty($password) || empty($nombres) || empty($rol_id)) {
                self::setFlash('error', 'Por favor complete los campos obligatorios: Nombres, Correo, Contraseña y Rol.');
                $this->redirect('/admin/usuarios');
                return;
            }

            $usuarioModel = new Usuario();
            if ($usuarioModel->findByEmail($correo)) {
                self::setFlash('error', 'El correo electrónico ya está registrado en el sistema.');
                $this->redirect('/admin/usuarios');
                return;
            }

            $datos = [
                'correo' => $correo,
                'password' => $password,
                'nombres' => $nombres,
                'apellido_paterno' => trim($_POST['apellido_paterno'] ?? ''),
                'apellido_materno' => trim($_POST['apellido_materno'] ?? ''),
                'tipo_documento_codigo' => !empty($_POST['tipo_documento_codigo']) ? $_POST['tipo_documento_codigo'] : null,
                'numero_documento' => trim($_POST['numero_documento'] ?? ''),
                'genero_codigo' => !empty($_POST['genero_codigo']) ? $_POST['genero_codigo'] : null,
                'celular' => trim($_POST['celular'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'rol_id' => $rol_id,
                'universidad_id' => !empty($_POST['universidad_id']) ? $_POST['universidad_id'] : null,
                'ubicacion_id' => null
            ];

            if ($usuarioModel->create($datos)) {
                self::setFlash('success', 'Usuario registrado exitosamente en el sistema.');
            } else {
                self::setFlash('error', 'Ocurrió un error al intentar crear el usuario.');
            }
        }
        $this->redirect('/admin/usuarios');
    }

    public function editarModal()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "<div class='p-4 text-center text-red-600'>ID no especificado.</div>";
            return;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->findById($id);

        if (!$usuario) {
            echo "<div class='p-4 text-center text-red-600'>Usuario no encontrado.</div>";
            return;
        }

        $rolModel = new Rol();
        $uniModel = new UniversidadModel();
        $catalogoModel = new Catalogo();

        $data = [
            'roles' => $rolModel->getAll(),
            'universidades' => $uniModel->getAll(),
            'tipos_documento' => $catalogoModel->obtenerPorReferencia('TIPO_DOCUMENTO'),
            'generos' => $catalogoModel->obtenerPorReferencia('GENERO'),
            'es_creacion' => false,
            'usuario' => $usuario
        ];

        $this->render('admin/usuario/form_modal', $data, '');
    }

    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['usuario_id'] ?? null;
            if (!$id) {
                self::setFlash('error', 'Identificador de usuario inválido.');
                $this->redirect('/admin/usuarios');
                return;
            }

            $usuarioModel = new Usuario();
            if ($usuarioModel->actualizarAdmin($id, $_POST)) {
                self::setFlash('success', 'Datos del usuario actualizados correctamente.');
            } else {
                self::setFlash('error', 'No se pudieron guardar los cambios del usuario.');
            }
        }
        $this->redirect('/admin/usuarios');
    }

    public function toggleEstado()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? $_POST['usuario_id'] ?? null;
            if ($id) {
                $usuarioModel = new Usuario();
                $nuevo_estado = $usuarioModel->toggleHabilitado($id);

                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'habilitado' => $nuevo_estado,
                        'mensaje' => $nuevo_estado ? 'Usuario reactivado exitosamente.' : 'Usuario inhabilitado / baneado temporalmente.'
                    ]);
                    return;
                }

                $msg = $nuevo_estado ? 'Usuario reactivado exitosamente.' : 'Usuario inhabilitado / baneado temporalmente.';
                self::setFlash('success', $msg);
            }
        }
        $this->redirect('/admin/usuarios');
    }

    /**
     * POST /admin/usuarios/verificar — aprueba la verificación de identidad de un estudiante.
     * Acepta AJAX (devuelve JSON) o form normal (flash + redirect).
     */
    public function verificarEstudiante()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/usuarios');
        }

        $id = $_POST['id'] ?? $_POST['usuario_id'] ?? null;
        if (!$id) {
            $this->json(['success' => false, 'mensaje' => 'Identificador de usuario inválido.']);
        }

        $usuarioModel = new Usuario();
        $ok = $usuarioModel->setVerificado($id, true);

        if ($this->esAjax()) {
            $this->json([
                'success' => $ok,
                'mensaje' => $ok ? 'Estudiante verificado correctamente.' : 'No se pudo verificar al estudiante.'
            ]);
        }

        self::setFlash($ok ? 'success' : 'error', $ok ? 'Estudiante verificado correctamente.' : 'No se pudo verificar al estudiante.');
        $this->redirect('/admin/usuarios');
    }

    /**
     * POST /admin/usuarios/desverificar — revoca la verificación de un estudiante.
     */
    public function desverificarEstudiante()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/usuarios');
        }

        $id = $_POST['id'] ?? $_POST['usuario_id'] ?? null;
        if (!$id) {
            $this->json(['success' => false, 'mensaje' => 'Identificador de usuario inválido.']);
        }

        $usuarioModel = new Usuario();
        $ok = $usuarioModel->setVerificado($id, false);

        if ($this->esAjax()) {
            $this->json([
                'success' => $ok,
                'mensaje' => $ok ? 'Verificación retirada.' : 'No se pudo actualizar el estado de verificación.'
            ]);
        }

        self::setFlash($ok ? 'success' : 'error', $ok ? 'Verificación retirada.' : 'No se pudo actualizar el estado de verificación.');
        $this->redirect('/admin/usuarios');
    }

    /**
     * ¿La petición actual es AJAX?
     */
    private function esAjax(): bool
    {
        return (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest')
            || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false);
    }

    /**
     * Devuelve JSON y termina.
     */
    private function json(array $payload): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }
}
