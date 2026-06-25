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

// Rutas de Administrador
$router->get('/admin', 'AdminController', 'dashboard');

// Ejecutar ruta
$router->dispatch();
