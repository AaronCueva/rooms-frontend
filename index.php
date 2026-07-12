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
$router->get('/api/universidades/buscar', 'AdminUniversidadController', 'buscarApi');

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
$router->get('/admin/foros/editar-modal', 'AdminForoController', 'editarForoModal');
$router->get('/admin/foros/comentario/editar-modal', 'AdminForoController', 'editarComentarioModal');
$router->post('/admin/foros/actualizar', 'AdminForoController', 'actualizarForo');
$router->post('/admin/foros/comentario/actualizar', 'AdminForoController', 'actualizarComentario');

// Rutas de Administrador (Red Social - Reseñas)
$router->get('/admin/resenas', 'AdminResenaController', 'index');
$router->get('/admin/resenas/ver-modal', 'AdminResenaController', 'verModal');
$router->post('/admin/resenas/toggle-estado', 'AdminResenaController', 'toggleEstado');
$router->post('/admin/resenas/cambiar-estado', 'AdminResenaController', 'cambiarEstado');

// Rutas de Administrador (Red Social - Blog)
$router->get('/admin/blog', 'AdminBlogController', 'index');
$router->get('/admin/blog/ver-modal', 'AdminBlogController', 'verModal');
$router->get('/admin/blog/crear-modal', 'AdminBlogController', 'crearModal');
$router->post('/admin/blog/guardar', 'AdminBlogController', 'guardar');
$router->get('/admin/blog/editar-modal', 'AdminBlogController', 'editarModal');
$router->post('/admin/blog/actualizar', 'AdminBlogController', 'actualizar');
$router->post('/admin/blog/cambiar-estado', 'AdminBlogController', 'cambiarEstado');
$router->post('/admin/blog/eliminar', 'AdminBlogController', 'eliminar');

// Rutas de Administrador (Red Social - Gamificación y Puntos NIDO)
$router->get('/admin/puntos', 'AdminPuntosController', 'index');
$router->post('/admin/puntos/ajuste-manual', 'AdminPuntosController', 'ajusteManual');
$router->post('/admin/puntos/referido/acreditar', 'AdminPuntosController', 'acreditarReferido');
$router->post('/admin/puntos/referido/anular', 'AdminPuntosController', 'anularReferido');
$router->get('/admin/puntos/ledger-modal', 'AdminPuntosController', 'ledgerModal');


// Rutas de Administrador (Alojamientos)
$router->get('/admin/alojamientos', 'AdminAlojamientoController', 'index');
$router->get('/admin/alojamientos/ver', 'AdminAlojamientoController', 'ver');
$router->get('/admin/alojamientos/crear', 'AdminAlojamientoController', 'crear');
$router->post('/admin/alojamientos/guardar', 'AdminAlojamientoController', 'guardar');
$router->get('/admin/alojamientos/editar', 'AdminAlojamientoController', 'editar');
$router->post('/admin/alojamientos/actualizar', 'AdminAlojamientoController', 'actualizar');
$router->post('/admin/alojamientos/aprobar', 'AdminAlojamientoController', 'aprobar');
$router->post('/admin/alojamientos/toggle-estado', 'AdminAlojamientoController', 'toggleEstado');
$router->post('/admin/alojamientos/servicio/agregar', 'AdminAlojamientoController', 'agregarServicio');
$router->post('/admin/alojamientos/servicio/eliminar', 'AdminAlojamientoController', 'eliminarServicio');
$router->post('/admin/alojamientos/descuento/guardar', 'AdminAlojamientoController', 'guardarDescuento');
$router->post('/admin/alojamientos/descuento/eliminar', 'AdminAlojamientoController', 'eliminarDescuento');
$router->post('/admin/alojamientos/beneficio/guardar', 'AdminAlojamientoController', 'guardarBeneficio');
$router->post('/admin/alojamientos/beneficio/eliminar', 'AdminAlojamientoController', 'eliminarBeneficio');

// Rutas de Administrador (Universidades)
$router->get('/admin/universidades', 'AdminUniversidadController', 'index');
$router->get('/admin/universidades/ver-modal', 'AdminUniversidadController', 'verModal');
$router->get('/admin/universidades/crear', 'AdminUniversidadController', 'crear');
$router->post('/admin/universidades/guardar', 'AdminUniversidadController', 'guardar');
$router->get('/admin/universidades/editar', 'AdminUniversidadController', 'editar');
$router->post('/admin/universidades/actualizar', 'AdminUniversidadController', 'actualizar');
$router->post('/admin/universidades/toggle-estado', 'AdminUniversidadController', 'toggleEstado');

// Rutas de Administrador (Reservas)
$router->get('/admin/reservas', 'AdminReservaController', 'index');
$router->get('/admin/reservas/ver', 'AdminReservaController', 'ver');
$router->get('/admin/reservas/crear', 'AdminReservaController', 'crear');
$router->post('/admin/reservas/guardar', 'AdminReservaController', 'guardar');
$router->get('/admin/reservas/editar', 'AdminReservaController', 'editar');
$router->post('/admin/reservas/actualizar', 'AdminReservaController', 'actualizar');
$router->post('/admin/reservas/toggle-estado', 'AdminReservaController', 'toggleEstado');

// Rutas de Administrador (Contratos)
$router->get('/admin/contratos', 'AdminContratoController', 'index');
$router->get('/admin/contratos/ver', 'AdminContratoController', 'ver');
$router->get('/admin/contratos/crear', 'AdminContratoController', 'crear');
$router->post('/admin/contratos/guardar', 'AdminContratoController', 'guardar');
$router->get('/admin/contratos/editar', 'AdminContratoController', 'editar');
$router->post('/admin/contratos/actualizar', 'AdminContratoController', 'actualizar');
$router->post('/admin/contratos/toggle-estado', 'AdminContratoController', 'toggleEstado');

// Rutas de Administrador (Alojamientos - Extensiones)
$router->post('/admin/alojamientos/imagen/subir', 'AdminAlojamientoController', 'subirImagen');
$router->post('/admin/alojamientos/imagen/eliminar', 'AdminAlojamientoController', 'eliminarImagen');
$router->post('/admin/alojamientos/resena/toggle', 'AdminAlojamientoController', 'toggleResena');

// Rutas de Administrador (Roles y Permisos)
$router->get('/admin/roles', 'AdminRolController', 'index');
$router->post('/admin/roles/guardar', 'AdminRolController', 'guardar');
$router->post('/admin/roles/actualizar', 'AdminRolController', 'actualizar');
$router->post('/admin/roles/toggle', 'AdminRolController', 'toggleEstado');
$router->get('/admin/roles/permisos', 'AdminRolController', 'permisos');
$router->post('/admin/roles/permisos/guardar', 'AdminRolController', 'guardarPermisos');

// Rutas de Administrador (Perfil / Contraseña)
$router->get('/admin/perfil', 'AdminPerfilController', 'index');
$router->post('/admin/perfil/actualizar', 'AdminPerfilController', 'actualizar');
$router->get('/admin/perfil/password', 'AdminPerfilController', 'password');
$router->post('/admin/perfil/password/actualizar', 'AdminPerfilController', 'actualizarPassword');

// Rutas de Administrador (Usuarios)
$router->get('/admin/usuarios', 'AdminUsuarioController', 'index');
$router->get('/admin/usuarios/ver-modal', 'AdminUsuarioController', 'verModal');
$router->get('/admin/usuarios/crear-modal', 'AdminUsuarioController', 'crearModal');
$router->post('/admin/usuarios/guardar', 'AdminUsuarioController', 'guardar');
$router->get('/admin/usuarios/editar-modal', 'AdminUsuarioController', 'editarModal');
$router->post('/admin/usuarios/actualizar', 'AdminUsuarioController', 'actualizar');
$router->post('/admin/usuarios/toggle-estado', 'AdminUsuarioController', 'toggleEstado');
$router->post('/admin/usuarios/verificar', 'AdminUsuarioController', 'verificarEstudiante');
$router->post('/admin/usuarios/desverificar', 'AdminUsuarioController', 'desverificarEstudiante');

// Ejecutar ruta
$router->dispatch();

