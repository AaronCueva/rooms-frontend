<?php
session_start();

// Autocargador básico para namespaces App\*
spl_autoload_register(function ($class) {
    // Reemplaza App por el directorio base de la app
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';

    // Verifica si la clase utiliza el prefijo
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Obtiene el nombre relativo de la clase
    $relative_class = substr($class, $len);

    // Reemplaza los separadores de namespace con separadores de directorio, 
    // y añade la extensión .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Si el archivo existe, lo requiere
    if (file_exists($file)) {
        require $file;
    }
});

// Importar el Router
use App\Core\Router;

$router = new Router();

// Definición de Rutas
$router->get('/', 'AuthController', 'login');
$router->get('/login', 'AuthController', 'login');
$router->post('/login', 'AuthController', 'authenticate');
$router->get('/register', 'AuthController', 'register');
$router->post('/register', 'AuthController', 'storeUser');
$router->get('/logout', 'AuthController', 'logout');
$router->get('/api/ubicaciones', 'AuthController', 'getUbicaciones');

// Rutas de Administrador (Generales)
$router->get('/admin', 'AdminController', 'dashboard');

// Rutas de Administrador (Red Social - Foros)
$router->get('/admin/foros', 'AdminForoController', 'index');
$router->get('/admin/foros/ver', 'AdminForoController', 'ver');
$router->get('/admin/foros/ver-modal', 'AdminForoController', 'verModal');
$router->post('/admin/foros/toggle-estado', 'AdminForoController', 'toggleEstado');
$router->post('/admin/foros/comentario/eliminar', 'AdminForoController', 'eliminarComentario');
$router->post('/admin/foros/comentario/restaurar', 'AdminForoController', 'restaurarComentario');
$router->post('/admin/foros/ban-usuario', 'AdminForoController', 'toggleBanUsuario');

// Ejecutar ruta
$router->dispatch();
