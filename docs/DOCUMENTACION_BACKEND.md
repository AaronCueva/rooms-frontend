# Estándares y Convenciones del Backend (Rooms)

Este documento define la arquitectura, convenciones y mejores prácticas para el desarrollo en el backend de la aplicación, con el objetivo de facilitar el trabajo colaborativo y mantener el código ordenado y escalable.

## 1. Arquitectura del Proyecto (MVC Custom)
El proyecto utiliza una arquitectura **MVC (Modelo-Vista-Controlador)** personalizada implementada en PHP nativo, utilizando `PDO` para la conexión a la base de datos (PostgreSQL vía Supabase) y un sistema de enrutamiento propio.

### Estructura de Directorios Principal
- `/app/controllers/`: Contiene la lógica de negocio. Gestionan las peticiones HTTP, se comunican con los modelos y renderizan las vistas.
- `/app/models/`: Encargados de la interacción directa con la base de datos (Ejecución de consultas SQL).
- `/app/views/`: Archivos HTML mezclados con PHP (`.php`) para las interfaces gráficas. Contiene subcarpetas por módulo y una subcarpeta `layouts/` para plantillas base.
- `/app/core/`: Archivos del núcleo de la aplicación (`Router`, `Controller`, `Database`). No deberían ser modificados comúnmente en el día a día.
- `/index.php`: Punto de entrada único de la aplicación (Front Controller) y lugar donde se centralizan las rutas.

---

## 2. Convenciones de Nomenclatura

- **Clases (Controladores y Modelos):** **PascalCase**. Ejemplo: `AdminController`, `Usuario`, `Catalogo`.
- **Métodos y Variables:** **camelCase**. Ejemplo: `storeUser()`, `findByEmail()`, `$passwordHash`, `$menuModel`.
- **Archivos de Clases:** El nombre del archivo `.php` debe coincidir exactamente con el nombre de la clase (respetando mayúsculas y minúsculas).
- **Archivos de Vistas:** **snake_case** o **kebab-case**, siempre en minúsculas. Ejemplo: `dashboard.php`, `register.php`.
- **Base de Datos:** Se estila usar `snake_case` para las tablas y columnas (ej. `apellido_paterno`, `tipo_documento_codigo`).

---

## 3. Definición de Rutas (Endpoints)

Todas las rutas web se centralizan en el archivo `index.php`. El enrutador (`App\Core\Router`) se encarga de llamar a la acción adecuada en el controlador indicado.

### ¿Cómo registrar una nueva ruta?
En `index.php`, utiliza la instancia `$router`:

```php
// GET: Para visualizar páginas o traer datos
$router->get('/ruta-amigable', 'NombreControlador', 'nombreMetodo');

// POST: Para enviar formularios o crear/modificar recursos
$router->post('/ruta-amigable/guardar', 'NombreControlador', 'nombreMetodoStore');
```

---

## 4. Controladores (`app/controllers/`)

Todo controlador nuevo debe ubicarse en `app/controllers/`, declarar el namespace `App\Controllers` y heredar de la clase `App\Core\Controller`.

### Ejemplo Base de Controlador
```php
<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Ejemplo; // Siempre importar los modelos necesarios

class EjemploController extends Controller
{
    public function __construct()
    {
        // EJEMPLO: Proteger la ruta si requiere autenticación
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('/login');
        }
    }

    public function index()
    {
        // 1. Instanciar el modelo
        $modelo = new Ejemplo();
        
        // 2. Obtener datos
        $datos = $modelo->getAll();

        // 3. Preparar variables para la vista
        $data = [
            'titulo' => 'Lista de Ejemplos',
            'ejemplos' => $datos
        ];

        // 4. Renderizar: render('ruta/de/la/vista', variables, 'nombre_del_layout')
        $this->render('ejemplos/index', $data, 'main');
    }
}
```

### Reglas Críticas para Controladores:
1. **Cero SQL:** No debe existir código SQL dentro del controlador. Toda interacción con la base de datos se delega a los Modelos.
2. **Redirecciones Seguras:** Al finalizar una acción exitosa por POST (creación, edición), redirecciona con `$this->redirect('/ruta');` en lugar de renderizar una vista directamente, previniendo el reenvío de formularios (Post/Redirect/Get).

---

## 5. Modelos y Base de Datos (`app/models/`)

Todos los modelos van en `app/models/` y representan entidades o entidades lógicas.

### Ejemplo Base de Modelo
```php
<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Ejemplo {
    private $db;

    public function __construct() {
        // Se inyecta la conexión PDO única (Singleton)
        $this->db = Database::getInstance()->getConnection();
    }

    public function findById($id) {
        $query = "SELECT * FROM ejemplos WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        // Binding del parámetro
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(); // Retorna un array asociativo del registro
    }
}
```

### Reglas Críticas para Modelos:
1. **Prevenir Inyección SQL:** **SIEMPRE** usa Sentencias Preparadas (`prepare` y `bindParam`). Nunca concatenes variables PHP directamente en el string del query (`$query = "SELECT * FROM t WHERE id = $id"` ❌).
2. **Fetch Modes por defecto:** La conexión de base de datos ya está configurada para retornar arreglos asociativos (`PDO::FETCH_ASSOC`), por lo que `$resultado['columna']` funcionará por defecto.

---

## 6. Vistas y Layouts (`app/views/`)

La función base de controlador `$this->render('vista', $data, 'layout')` opera de la siguiente manera:
1. Toma las variables del array `$data` y las vuelve disponibles en la vista (usando `extract()`). Por ejemplo, `'titulo' => 'Hola'` en `$data`, estará disponible en la vista como `$titulo`.
2. Procesa la `vista` (ej. `admin/dashboard.php`) y captura su HTML resultante en una variable interna llamada `$content`.
3. Inyecta ese `$content` dentro de un diseño maestro o *layout* ubicado en `app/views/layouts/layout.php`.

---

## 7. Mejores Prácticas y Seguridad
- **Passwords:** Cualquier manejo de contraseñas de usuario debe realizarse utilizando `password_hash($pass, PASSWORD_BCRYPT)` para almacenarlas y `password_verify()` para validarlas.
- **Namespaces:** El sistema implementa un autocargador PSR-4 básico en `index.php`. Es mandatorio que todos los archivos tengan el namespace correcto para que PHP los pueda importar sin necesidad de usar `require` o `include` manuales.
- **Sesiones:** La sesión (`$_SESSION`) está centralizada e iniciada en `index.php`. No es necesario invocar `session_start()` en ningún controlador.
