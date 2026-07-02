# Módulos del Administrador Social (ADMIN_SOCIALES)

**Proyecto:** Nido Universitario (Rooms)  
**Versión:** 1.0  
**Fecha:** 01/07/2026  
**Referencia:** DF-NidoUniversitario-v1.0 (Secciones 3.3.6, 3.7, 5.4, 5.6)  
**Rama:** `feature/jesusHuerta`

---

## Índice

1. [Descripción General del Rol](#1-descripción-general-del-rol)
2. [Estado Actual de Implementación](#2-estado-actual-de-implementación)
3. [Módulo 1: Gestión de Foros (Implementado)](#3-módulo-1-gestión-de-foros-implementado)
4. [Módulo 2: Moderación de Reseñas y Calificaciones](#4-módulo-2-moderación-de-reseñas-y-calificaciones)
5. [Módulo 3: Blog / Guía del Universitario](#5-módulo-3-blog--guía-del-universitario)
6. [Módulo 4: Reacciones en la Comunidad](#6-módulo-4-reacciones-en-la-comunidad)
7. [Módulo 5: Gamificación, Referidos y Puntos NIDO](#7-módulo-5-gamificación-referidos-y-puntos-nido)
8. [Módulo 6: Supervisión de Mensajería y Mediación de Disputas](#8-módulo-6-supervisión-de-mensajería-y-mediación-de-disputas)
9. [Resumen de Archivos a Crear](#9-resumen-de-archivos-a-crear)
10. [Registro de Rutas en index.php](#10-registro-de-rutas-en-indexphp)

---

## 1. Descripción General del Rol

El **Administrador Social (`ADMIN_SOCIALES`)** es el perfil responsable de la moderación, salud y crecimiento de la **comunidad universitaria** dentro de la plataforma Nido Universitario. Su ámbito de responsabilidad abarca todo el contenido generado por los usuarios (foros, reseñas, blog), el programa de fidelización (puntos NIDO y referidos) y la intervención en disputas sociales (mediación de chat).

### Alcance Funcional (según DF-NidoUniversitario-v1.0)

| Sección del DF | Módulo Funcional | Responsabilidad del ADMIN_SOCIALES |
| :--- | :--- | :--- |
| 3.7.1 | Foro por Universidad | Moderar hilos, ocultar contenido inapropiado, banear usuarios |
| 3.3.6, 5.4 | Reseñas y Calificaciones | Revisar reseñas reportadas, ocultar contenido falso/ofensivo |
| 3.7.2 | Blog / Guía del Universitario | Crear, editar y publicar artículos editoriales |
| 3.7.1 | Reacciones en Foro | Visualizar métricas de engagement y reacciones |
| 3.4.4, 3.7.3, 5.6 | Gamificación y Referidos | Auditar puntos NIDO, gestionar programa de referidos |
| 4.5, 5.4, 5.5 | Mediación en Mensajería | Supervisar chats en disputa y enviar avisos comunitarios |

---

## 2. Estado Actual de Implementación

### Módulos ya construidos

| Módulo | Modelo(s) | Controlador | Vistas | Estado |
| :--- | :--- | :--- | :--- | :--- |
| Gestión de Foros | `Foro.php`, `ForoComentario.php` | `AdminForoController.php` | `admin/foro/index.php`, `view.php`, `form.php`, `comentario_form.php` | ✅ Completo |

### Módulos pendientes de desarrollo

| Módulo | Modelo(s) existentes | Modelo(s) a crear | Controlador a crear |
| :--- | :--- | :--- | :--- |
| Moderación de Reseñas | `Resena.php`, `ReseniaAlojamiento.php` | — | `AdminResenaController.php` |
| Blog / Guía del Universitario | — | `Blog.php` | `AdminBlogController.php` |
| Reacciones en la Comunidad | — | `ForoReaccion.php` | (Integración en `AdminForoController`) |
| Gamificación y Referidos | — | `Referido.php`, `PuntoMovimiento.php` | `AdminGamificacionController.php` |
| Supervisión de Mensajería | — | `Chat.php`, `ChatUsuario.php`, `Mensaje.php` | `AdminMensajeriaController.php` |

---

## 3. Módulo 1: Gestión de Foros (Implementado)

> **Estado: ✅ Completo** — Se documenta como referencia de patrón arquitectónico.

### 3.1. Descripción Funcional (DF 3.7.1)

El foro es el espacio de discusión principal de la comunidad, organizado por universidad. Los estudiantes crean publicaciones categorizadas (Tips de vida universitaria, Comparte gastos, Venta de objetos, Recomendaciones de zonas, Preguntas sobre alquileres) y otros usuarios responden con comentarios en hilo.

El `ADMIN_SOCIALES` puede:
- Listar todos los foros con filtros por categoría, estado y búsqueda de texto.
- Ver el detalle completo de un foro con sus comentarios organizados en árbol (padres e hijos).
- Editar el título, descripción, categoría y universidad de un foro.
- Ocultar (soft delete) o restaurar un foro.
- Eliminar (soft delete) o restaurar un comentario individual.
- Editar el contenido de un comentario.
- Banear o desbanear al usuario autor del contenido.

### 3.2. Tablas del Modelo ER involucradas

| Tabla | Campos Clave |
| :--- | :--- |
| `foro` | `foro_id` (PK), `titulo`, `descripcion`, `categoria_codigo`, `estado_codigo`, `habilitado`, `usuario_id` (FK), `universidad_id` (FK), `fecha_creacion` |
| `foro_comentario` | `foro_comentario_id` (PK), `foro_id` (FK), `usuario_id` (FK), `comentario_padre_id` (FK recursivo), `mensaje`, `habilitado`, `fecha_envio` |
| `foro_reaccion` | `foro_reaccion` (PK), `foro_id` (FK), `usuario_id` (FK), `tipo_reaccion_codigo` |

### 3.3. Archivos Implementados

| Capa | Archivo | Métodos Principales |
| :--- | :--- | :--- |
| **Modelo** | `app/models/Foro.php` | `getAllForos()`, `buscar($filtros, $pagina, $por_pagina)`, `contar($filtros)`, `findById($id)`, `toggleEstado($id, $estado)`, `actualizar($id, $datos)` |
| **Modelo** | `app/models/ForoComentario.php` | `getByForoId($foro_id)`, `eliminar($id)`, `restaurar($id)`, `actualizar($id, $mensaje)` |
| **Controlador** | `app/controllers/AdminForoController.php` | `index()`, `ver()`, `verModal()`, `editarForoModal()`, `actualizarForo()`, `editarComentarioModal()`, `actualizarComentario()`, `toggleEstado()`, `eliminarComentario()`, `restaurarComentario()`, `toggleBanUsuario()` |
| **Vista** | `app/views/admin/foro/index.php` | Tabla paginada con filtros, modales de ver/editar |
| **Vista** | `app/views/admin/foro/view.php` | Detalle del foro con árbol de comentarios |
| **Vista** | `app/views/admin/foro/form.php` | Formulario de edición de foro (sin layout) |
| **Vista** | `app/views/admin/foro/comentario_form.php` | Formulario de edición de comentario (sin layout) |

### 3.4. Patrones Arquitectónicos de Referencia

Los siguientes patrones implementados en el módulo de foros deben replicarse en todos los módulos nuevos:

1. **Paginación con filtros:** Método `buscar($filtros, $pagina, $por_pagina)` en el modelo con `ILIKE` para búsquedas y `LIMIT/OFFSET` para paginación.
2. **Modales para ver y editar:** Los métodos `verModal()` y `editarForoModal()` renderizan sin layout (tercer parámetro vacío: `$this->render('vista', $data, '')`) y se cargan vía `fetch()` desde JavaScript.
3. **Soft Delete:** `habilitado = true/false` en lugar de `DELETE FROM`. Filas ocultas con clase CSS `.row-oculto`.
4. **Mensajes Flash:** `$this->setFlash('success', 'mensaje')` seguido de `$this->redirect()` (patrón PRG).
5. **Protección de rutas:** Validación de sesión en el `__construct()` del controlador.

---

## 4. Módulo 2: Moderación de Reseñas y Calificaciones

> **Estado: 🔴 Pendiente** — Modelos parcialmente disponibles (`Resena.php`, `ReseniaAlojamiento.php`).

### 4.1. Descripción Funcional (DF 3.3.6 y 5.4)

Las reseñas son calificaciones vinculadas a contratos reales de alquiler: solo un estudiante que haya completado al menos un mes de contrato puede dejar una reseña. Cada reseña incluye una calificación numérica (1 a 5 estrellas), un comentario textual y la posibilidad de que el propietario escriba una réplica (`respuesta_propietario`).

El `ADMIN_SOCIALES` puede:
- Listar todas las reseñas del sistema con filtros por calificación, estado (habilitado/oculto) y búsqueda por texto o alojamiento.
- Ver el detalle completo de una reseña: datos del estudiante, del alojamiento, del contrato asociado, la calificación y la réplica del propietario.
- Ocultar (soft delete) una reseña reportada como falsa, ofensiva o inapropiada.
- Restaurar una reseña previamente ocultada si se determina que era legítima.
- Consultar estadísticas de calificación por alojamiento.

### 4.2. Tablas del Modelo ER involucradas

| Tabla | Campos Clave | Relaciones |
| :--- | :--- | :--- |
| `resena` | `resena_id` (PK), `calificacion`, `comentario`, `respuesta_propietario`, `fecha_creado`, `estado_codigo`, `habilitado` | `contrato_id` (FK → `contrato`) |
| `resenia_alojamiento` | `resenia_alojamiento_id` (PK), `calificacion`, `comentario`, `habilitado`, `creado` | `alojamiento_id` (FK → `alojamiento`), `estudiante_id` (FK → `usuario`) |
| `contrato` | `contrato_id` (PK), `fecha_inicio`, `fecha_fin`, `monto_renta` | `reserva_id` (FK → `reserva`) |
| `reserva` | `reserva_id` (PK) | `usuario_id` (FK), `alojamiento_id` (FK) |

### 4.3. Diseño Técnico

#### 4.3.1. Modelo: `app/models/Resena.php` (Existente — ampliar)

El modelo `Resena.php` ya existe con los métodos `getByAlojamientoId()`, `getByContratoId()`, `findById()` y `toggleEstado()`. Se necesita agregar:

```php
/**
 * Métodos a agregar en Resena.php
 */

/**
 * Buscar reseñas con filtros y paginación.
 * JOIN con contrato → reserva → usuario (estudiante) y alojamiento.
 */
public function buscar($filtros = [], $pagina = 1, $por_pagina = 10)
{
    // Filtros: busqueda (texto en comentario), calificacion (1-5),
    //          estado (habilitado true/false)
    // JOIN: contrato → reserva → usuario + alojamiento
    // ORDER BY: fecha_creado DESC
    // LIMIT/OFFSET para paginación
}

/**
 * Contar total de reseñas con los mismos filtros (para paginación).
 */
public function contar($filtros = [])

/**
 * Obtener todas las reseñas del sistema con datos completos
 * (estudiante, alojamiento, contrato).
 */
public function getAll()
```

#### 4.3.2. Modelo: `app/models/ReseniaAlojamiento.php` (Existente — ampliar)

El modelo `ReseniaAlojamiento.php` ya existe con `getByAlojamientoId()`, `findById()` y `toggleEstado()`. Se necesita agregar:

```php
/**
 * Métodos a agregar en ReseniaAlojamiento.php
 */

/**
 * Buscar reseñas de alojamiento con filtros y paginación.
 * JOIN con usuario (estudiante) y alojamiento.
 */
public function buscar($filtros = [], $pagina = 1, $por_pagina = 10)

/**
 * Contar total con filtros.
 */
public function contar($filtros = [])
```

#### 4.3.3. Controlador: `app/controllers/AdminResenaController.php` (Crear)

```
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Resena;
use App\Models\ReseniaAlojamiento;

class AdminResenaController extends Controller
{
    __construct()           → Validar sesión activa
    index()                 → Listar reseñas con filtros (calificación, estado, búsqueda) y paginación
    verModal()              → Ver detalle de reseña en modal (sin layout)
    toggleEstado()          → POST: Ocultar/restaurar una reseña (soft delete)
}
```

**Detalle de cada método:**

| Método | Verbo | Descripción | Modelo(s) usado(s) |
| :--- | :--- | :--- | :--- |
| `index()` | GET | Recibe filtros vía `$_GET` (`busqueda`, `calificacion`, `estado`), pagina los resultados y renderiza la tabla. Muestra badges con estrellas ⭐ según la calificación. | `ReseniaAlojamiento->buscar()`, `ReseniaAlojamiento->contar()` |
| `verModal()` | GET | Recibe `$_GET['id']`, busca la reseña con sus datos de estudiante, alojamiento y contrato. Renderiza sin layout para inyección en modal. | `ReseniaAlojamiento->findById()` |
| `toggleEstado()` | POST | Recibe `$_POST['id']`, invoca `toggleEstado()` del modelo. Flash de éxito y redirección PRG a `/admin/resenas`. | `ReseniaAlojamiento->toggleEstado()` |

#### 4.3.4. Vistas: `app/views/admin/resena/`

**`index.php`** — Tabla principal con:
- Encabezado: Título "Gestión de Reseñas" + badge con total de registros.
- Barra de filtros:
  - Input de búsqueda (texto en comentario o nombre de alojamiento).
  - Select de calificación: Todas, 1⭐, 2⭐, 3⭐, 4⭐, 5⭐.
  - Select de estado: Todos, Activos, Ocultos.
  - Botón "Filtrar".
- Tabla estándar (`.foro-table`):

| Columna | Contenido |
| :--- | :--- |
| Estudiante | Avatar con inicial (`.autor-avatar`) + nombre completo |
| Alojamiento | Título del alojamiento |
| Calificación | Estrellas visuales (⭐ × N) con número |
| Comentario | Texto truncado a 80 caracteres |
| Fecha | `fecha_creado` formateada |
| Estado | Badge `.status-activo` o `.status-oculto` |
| Acciones | `.btn-accion-view` (ver modal), `.btn-accion-hide` / `.btn-accion-restore` (ocultar/restaurar) |

- Filas ocultas con clase `.row-oculto`.
- Paginación al pie.
- Modal reutilizable para ver detalle y confirmar acciones.

**`view.php`** — Detalle en modal (sin layout):
- Card con foto de perfil del estudiante, nombre completo y correo.
- Card del alojamiento: título, código y dirección.
- Calificación visual con estrellas grandes.
- Texto completo del comentario.
- Réplica del propietario (si existe), en un bloque con fondo diferenciado.
- Fechas del contrato asociado (inicio y fin).

#### 4.3.5. Rutas a registrar en `index.php`

```php
// Rutas de Administrador (Reseñas - ADMIN_SOCIALES)
$router->get('/admin/resenas', 'AdminResenaController', 'index');
$router->get('/admin/resenas/ver-modal', 'AdminResenaController', 'verModal');
$router->post('/admin/resenas/toggle-estado', 'AdminResenaController', 'toggleEstado');
```

---

## 5. Módulo 3: Blog / Guía del Universitario

> **Estado: 🔴 Pendiente** — Modelo y controlador por crear desde cero.

### 5.1. Descripción Funcional (DF 3.7.2)

Sección editorial con artículos creados por el equipo de Nido Universitario. Según el DF, incluye:
- **Guías por ciudad:** "Cómo vivir cerca de la UNI en Lima sin gastar más de S/600".
- **Tips de convivencia:** seguridad, negociación de alquiler, derechos del inquilino.
- **Comparativas de zonas universitarias** por ciudad.
- **Noticias y tendencias** del mercado de alquiler estudiantil.

El `ADMIN_SOCIALES` puede:
- Listar todos los artículos del blog con filtros por estado (Borrador/Publicado/Oculto) y búsqueda.
- Crear un nuevo artículo con título, contenido enriquecido, categoría e imagen de portada.
- Editar un artículo existente.
- Cambiar el estado de un artículo: Borrador → Publicado → Oculto.
- Eliminar (soft delete) un artículo.
- Vista previa del artículo antes de publicar.

### 5.2. Tabla del Modelo ER involucrada

| Tabla | Campos Clave | Relaciones |
| :--- | :--- | :--- |
| `blog` | `blog_id` (PK), `titulo`, `contenido`, `fecha_publicacion`, `estado_codigo`, `habilitado` | `usuario_id` (FK → `usuario`) |

**Nota sobre `estado_codigo`:** Se recomienda registrar los siguientes valores en la tabla `CATALOGO` con `referencia_codigo = 'ESTADO_BLOG'`:

| Código | Nombre | Descripción |
| :--- | :--- | :--- |
| `ESBL001` | Borrador | Artículo en proceso de redacción |
| `ESBL002` | Publicado | Artículo visible para todos los usuarios |
| `ESBL003` | Oculto | Artículo retirado de la vista pública |

### 5.3. Diseño Técnico

#### 5.3.1. Modelo: `app/models/Blog.php` (Crear)

```php
namespace App\Models;

use App\Core\Database;
use PDO;

class Blog {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Buscar artículos con filtros y paginación.
     * JOIN con usuario (autor).
     * Filtros: busqueda (titulo o contenido), estado_codigo.
     */
    public function buscar($filtros = [], $pagina = 1, $por_pagina = 10)

    /**
     * Contar total de artículos con filtros.
     */
    public function contar($filtros = [])

    /**
     * Obtener un artículo por ID con datos del autor.
     */
    public function findById($id)

    /**
     * Crear un nuevo artículo.
     * Campos: titulo, contenido, estado_codigo, usuario_id.
     * La fecha_publicacion se setea cuando el estado pasa a ESBL002.
     */
    public function crear($datos)

    /**
     * Actualizar un artículo existente.
     * Si el nuevo estado_codigo es ESBL002 y antes no lo era, se setea fecha_publicacion = NOW().
     */
    public function actualizar($id, $datos)

    /**
     * Soft delete: habilitado = false.
     */
    public function eliminar($id)

    /**
     * Restaurar: habilitado = true.
     */
    public function restaurar($id)
}
```

#### 5.3.2. Controlador: `app/controllers/AdminBlogController.php` (Crear)

```
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Blog;
use App\Models\Catalogo;

class AdminBlogController extends Controller
{
    __construct()           → Validar sesión activa
    index()                 → Listar artículos con filtros y paginación
    verModal()              → Ver artículo completo en modal (sin layout)
    crearModal()            → Formulario de creación en modal (sin layout)
    guardar()               → POST: Crear artículo
    editarModal()           → Formulario de edición en modal (sin layout)
    actualizar()            → POST: Actualizar artículo
    eliminar()              → POST: Soft delete
    restaurar()             → POST: Restaurar artículo
}
```

**Detalle de cada método:**

| Método | Verbo | Descripción | Modelo(s) |
| :--- | :--- | :--- | :--- |
| `index()` | GET | Filtra por `busqueda` y `estado`. Pagina resultados con `buscar()` y `contar()`. | `Blog`, `Catalogo` |
| `verModal()` | GET | Muestra el artículo completo con datos del autor. Sin layout. | `Blog` |
| `crearModal()` | GET | Renderiza formulario vacío con los estados del catálogo. Sin layout. | `Catalogo` |
| `guardar()` | POST | Valida campos, invoca `Blog->crear()`. Asigna `usuario_id` desde `$_SESSION['usuario_id']`. Flash + redirect PRG. | `Blog` |
| `editarModal()` | GET | Carga artículo existente y renderiza formulario pre-rellenado. Sin layout. | `Blog`, `Catalogo` |
| `actualizar()` | POST | Valida campos, invoca `Blog->actualizar()`. Flash + redirect PRG. | `Blog` |
| `eliminar()` | POST | Invoca `Blog->eliminar()`. Flash + redirect PRG. | `Blog` |
| `restaurar()` | POST | Invoca `Blog->restaurar()`. Flash + redirect PRG. | `Blog` |

#### 5.3.3. Vistas: `app/views/admin/blog/`

**`index.php`** — Tabla principal con:
- Encabezado: "Gestión del Blog" + badge total + botón "Nuevo Artículo" (`.btn btn-primary`, `id="btnCrear"`).
- Barra de filtros:
  - Input de búsqueda (título o contenido).
  - Select de estado: Todos, Borrador, Publicado, Oculto.
  - Botón "Filtrar".
- Tabla estándar (`.foro-table`):

| Columna | Contenido |
| :--- | :--- |
| Ícono | `.foro-icon` con `fa-newspaper` |
| Título | Texto del título (max 60 char truncado) |
| Autor | Avatar inicial (`.autor-avatar`) + nombre del usuario |
| Estado | Badge dinámico: Borrador (`.badge bg-warning`), Publicado (`.badge bg-success`), Oculto (`.badge bg-secondary`) |
| Fecha | `fecha_publicacion` si está publicado, sino "Sin publicar" |
| Acciones | `.btn-accion-view`, `.btn-accion-edit`, `.btn-accion-hide` / `.btn-accion-restore` |

- Paginación estándar al pie.
- Modal reutilizable para ver, crear y editar.

**`form.php`** — Formulario (sin layout, para inyección en modal):
- Campo oculto `id` (solo en edición).
- `Título` — input text, required, maxlength 120.
- `Contenido` — textarea con editor de texto enriquecido (se puede usar un textarea grande inicialmente y mejorar con un editor WYSIWYG como Summernote en una fase posterior).
- `Estado` — select con opciones del catálogo (`ESTADO_BLOG`).
- Botones: "Cancelar" (`data-bs-dismiss="modal"`) y "Guardar" (`type="submit"`).

**`view.php`** — Detalle en modal (sin layout):
- Título del artículo como `<h4>`.
- Metadatos: nombre del autor, fecha de publicación, estado.
- Contenido completo del artículo.

#### 5.3.4. Rutas a registrar en `index.php`

```php
// Rutas de Administrador (Blog - ADMIN_SOCIALES)
$router->get('/admin/blog', 'AdminBlogController', 'index');
$router->get('/admin/blog/ver-modal', 'AdminBlogController', 'verModal');
$router->get('/admin/blog/crear-modal', 'AdminBlogController', 'crearModal');
$router->post('/admin/blog/guardar', 'AdminBlogController', 'guardar');
$router->get('/admin/blog/editar-modal', 'AdminBlogController', 'editarModal');
$router->post('/admin/blog/actualizar', 'AdminBlogController', 'actualizar');
$router->post('/admin/blog/eliminar', 'AdminBlogController', 'eliminar');
$router->post('/admin/blog/restaurar', 'AdminBlogController', 'restaurar');
```

### 5.4. Consideraciones de Diseño UI

- El editor de contenido debe soportar al mínimo: negritas, cursivas, listas, enlaces y encabezados.
- La vista previa del artículo debe renderizar el HTML del contenido de forma segura (sanitizar con `htmlspecialchars()` donde corresponda o usar una librería de Markdown si se opta por ese formato).
- Las imágenes de portada se gestionan a través de la tabla `MULTIMEDIA` vinculando `blog_id` (requeriría agregar la columna `blog_id` a `multimedia` o usar la relación existente `usuario_id` + un tipo especial).

---

## 6. Módulo 4: Reacciones en la Comunidad

> **Estado: 🟡 Pendiente** — La tabla `FORO_REACCION` ya existe en la BD y el modelo `Foro.php` ya cuenta reacciones en sus queries.

### 6.1. Descripción Funcional (DF 3.7.1)

El documento funcional define tres tipos de reacción para los foros:
- 👍 **Me sirve**
- ❤️ **Gracias**
- 🔥 **Top recomendación**

Cada usuario puede dejar solo una reacción por foro (relación 1:1 entre `USUARIO` y `FORO` por `FORO_REACCION`).

El `ADMIN_SOCIALES` puede:
- Ver el conteo de reacciones por tipo en cada foro.
- Consultar qué usuarios reaccionaron a un foro específico.
- Identificar los foros con mayor engagement (Top Reacciones).
- Eliminar reacciones de usuarios baneados o sospechosos.

### 6.2. Tabla del Modelo ER involucrada

| Tabla | Campos Clave | Relaciones |
| :--- | :--- | :--- |
| `foro_reaccion` | `foro_reaccion` (PK), `tipo_reaccion_codigo` | `foro_id` (FK → `foro`), `usuario_id` (FK → `usuario`) |

**Nota sobre `tipo_reaccion_codigo`:** Se recomienda registrar en `CATALOGO` con `referencia_codigo = 'TIPO_REACCION'`:

| Código | Nombre | Ícono |
| :--- | :--- | :--- |
| `TRFO001` | Me sirve | 👍 |
| `TRFO002` | Gracias | ❤️ |
| `TRFO003` | Top recomendación | 🔥 |

### 6.3. Diseño Técnico

#### 6.3.1. Modelo: `app/models/ForoReaccion.php` (Crear)

```php
namespace App\Models;

use App\Core\Database;
use PDO;

class ForoReaccion {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtener todas las reacciones de un foro con datos del usuario.
     */
    public function getByForoId($foro_id)

    /**
     * Contar reacciones por tipo para un foro.
     * Retorna array: [['tipo_reaccion_codigo' => 'TRFO001', 'total' => 5], ...]
     */
    public function contarPorTipo($foro_id)

    /**
     * Obtener ranking de foros por total de reacciones.
     * JOIN con foro y universidad.
     */
    public function rankingForos($limite = 10)

    /**
     * Eliminar una reacción específica (hard delete).
     */
    public function eliminar($foro_reaccion_id)

    /**
     * Eliminar todas las reacciones de un usuario (para complementar el baneo).
     */
    public function eliminarPorUsuario($usuario_id)
}
```

#### 6.3.2. Integración en `AdminForoController.php` (Ampliar)

No se requiere un controlador separado. Se amplía el controlador existente con:

| Método nuevo | Verbo | Descripción |
| :--- | :--- | :--- |
| `reaccionesModal()` | GET | Muestra las reacciones desglosadas por tipo de un foro específico. Sin layout, para modal. |
| `eliminarReaccion()` | POST | Elimina una reacción individual (hard delete). Flash + redirect PRG. |

#### 6.3.3. Mejoras en vistas existentes

**En `app/views/admin/foro/index.php`:**
- Agregar una columna "Reacciones" en la tabla que muestre el total de reacciones con ícono de corazón (ya se tiene `total_reacciones` en el query del modelo `Foro.php`).
- Agregar un botón de acción adicional con ícono `fa-chart-bar` para abrir el modal de desglose de reacciones.

**En `app/views/admin/foro/view.php`:**
- Agregar sección "Reacciones" debajo de los comentarios que muestre el desglose por tipo (👍 × N, ❤️ × N, 🔥 × N) y la lista de usuarios que reaccionaron.

#### 6.3.4. Rutas adicionales en `index.php`

```php
// Rutas adicionales de Foros (Reacciones)
$router->get('/admin/foros/reacciones-modal', 'AdminForoController', 'reaccionesModal');
$router->post('/admin/foros/reaccion/eliminar', 'AdminForoController', 'eliminarReaccion');
```

---

## 7. Módulo 5: Gamificación, Referidos y Puntos NIDO

> **Estado: 🔴 Pendiente** — Modelos y controlador por crear desde cero.

### 7.1. Descripción Funcional (DF 3.4.4, 3.7.3 y 5.6)

Nido Universitario incorpora un programa de fidelización basado en puntos ("Puntos NIDO") para incentivar la participación y retención de estudiantes. Según el DF:

**Acciones que generan puntos:**

| Acción | Puntos |
| :--- | :--- |
| Pago puntual de renta (antes del vencimiento) | Según configuración |
| Referir un amigo que se registra | 50 puntos NIDO |
| Referido realiza su primera reserva | 200 puntos (referidor) + 100 puntos (referido) |
| Compartir alojamiento en redes (genera visita) | 10 puntos |
| Escribir una reseña verificada | Según configuración |
| Participar en foro | Según configuración |
| Racha de pagos (3, 6, 12 meses consecutivos) | Badge especial |

**Niveles de perfil:**
- **Novato** → **Inquilino Confiable** → **Nido Gold**
- El nivel mejora las posibilidades de aceptación en reservas competidas.

**Canje de puntos:**
- Descuentos en próxima renta.
- Acceso a alojamientos exclusivos.
- Beneficios de aliados (librerías, cafés universitarios, plataformas de streaming).

El `ADMIN_SOCIALES` puede:
- Ver el ranking (Leaderboard) de estudiantes con más puntos acumulados.
- Consultar el historial detallado de movimientos de puntos de un usuario específico (libro mayor / ledger).
- Realizar ajustes manuales de puntos (compensación o corrección) con motivo documentado.
- Listar todos los referidos registrados con su estado (pendiente, acreditado).
- Ver estadísticas globales del programa: total de puntos en circulación, promedio por usuario, referidos exitosos.

### 7.2. Tablas del Modelo ER involucradas

| Tabla | Campos Clave | Relaciones |
| :--- | :--- | :--- |
| `referido` | `referido_id` (PK), `puntos_otorgados`, `estado_codigo`, `fecha_creacion` | `usuario_referidor_id` (FK → `usuario`), `usuario_referido_id` (FK → `usuario`) |
| `punto_movimiento` | `punto_movimiento_id` (PK), `tipo_movimiento_codigo`, `puntos`, `descripcion`, `fecha_creacion` | `usuario_id` (FK → `usuario`), `referido_id` (FK → `referido`) |
| `usuario` | `puntos_acumulados` | Campo de saldo total de puntos |

**Nota sobre `tipo_movimiento_codigo`:** Se recomienda registrar en `CATALOGO` con `referencia_codigo = 'TIPO_MOVIMIENTO_PUNTO'`:

| Código | Nombre | Descripción |
| :--- | :--- | :--- |
| `TMPT001` | Ganancia por referido | Puntos otorgados por referir un amigo |
| `TMPT002` | Ganancia por reseña | Puntos por escribir una reseña verificada |
| `TMPT003` | Ganancia por pago puntual | Puntos por pagar antes del vencimiento |
| `TMPT004` | Ganancia por participación | Puntos por actividad en foros |
| `TMPT005` | Canje de puntos | Descuento (resta de puntos) |
| `TMPT006` | Ajuste manual (admin) | Compensación o corrección manual |
| `TMPT007` | Bono de bienvenida | Puntos otorgados al referido nuevo |

### 7.3. Diseño Técnico

#### 7.3.1. Modelo: `app/models/Referido.php` (Crear)

```php
namespace App\Models;

use App\Core\Database;
use PDO;

class Referido {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Buscar referidos con filtros y paginación.
     * JOIN con usuario referidor y usuario referido.
     * Filtros: busqueda (nombre referidor o referido), estado_codigo.
     */
    public function buscar($filtros = [], $pagina = 1, $por_pagina = 10)

    /**
     * Contar total con filtros.
     */
    public function contar($filtros = [])

    /**
     * Obtener un referido por ID con datos de ambos usuarios.
     */
    public function findById($id)

    /**
     * Obtener estadísticas globales:
     * - Total referidos registrados
     * - Total referidos acreditados
     * - Total puntos otorgados por referidos
     */
    public function getEstadisticas()
}
```

#### 7.3.2. Modelo: `app/models/PuntoMovimiento.php` (Crear)

```php
namespace App\Models;

use App\Core\Database;
use PDO;

class PuntoMovimiento {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtener historial de movimientos de un usuario.
     * Incluye tipo_movimiento (nombre del catálogo) y referido (si aplica).
     * Orden: fecha_creacion DESC.
     */
    public function getByUsuarioId($usuario_id, $pagina = 1, $por_pagina = 20)

    /**
     * Obtener ranking de usuarios por puntos_acumulados.
     * JOIN con usuario. TOP N usuarios.
     */
    public function getRanking($limite = 20)

    /**
     * Registrar un nuevo movimiento de puntos.
     * Además, actualiza usuario.puntos_acumulados sumando (o restando) los puntos.
     * IMPORTANTE: Usar transacción para atomicidad.
     */
    public function registrar($datos)
    // $datos = [
    //     'usuario_id'             => int,
    //     'tipo_movimiento_codigo' => string (ej. 'TMPT006'),
    //     'puntos'                 => int (positivo = ganancia, negativo = descuento),
    //     'descripcion'            => string,
    //     'referido_id'            => int|null
    // ]

    /**
     * Obtener estadísticas globales del programa:
     * - Total de puntos en circulación (SUM puntos_acumulados de todos los usuarios)
     * - Promedio de puntos por usuario
     * - Total de movimientos registrados
     */
    public function getEstadisticasGlobales()
}
```

#### 7.3.3. Controlador: `app/controllers/AdminGamificacionController.php` (Crear)

```
namespace App\Controllers;

use App\Core\Controller;
use App\Models\PuntoMovimiento;
use App\Models\Referido;
use App\Models\Usuario;

class AdminGamificacionController extends Controller
{
    __construct()              → Validar sesión activa
    index()                    → Dashboard de gamificación (ranking, stats, referidos)
    historialUsuario()         → Ver movimientos de puntos de un usuario (modal)
    ajusteManual()             → POST: Registrar ajuste manual de puntos
    referidos()                → Listar todos los referidos con filtros y paginación
    verReferidoModal()         → Ver detalle de un referido en modal
}
```

**Detalle de cada método:**

| Método | Verbo | Descripción | Modelo(s) |
| :--- | :--- | :--- | :--- |
| `index()` | GET | Página principal con 3 secciones: KPIs en cards (total puntos en circulación, promedio por usuario, total referidos acreditados), tabla de ranking Top 20 y últimos movimientos recientes. | `PuntoMovimiento`, `Referido` |
| `historialUsuario()` | GET | Recibe `$_GET['usuario_id']`. Muestra tabla paginada de todos los movimientos de un usuario (tipo, puntos, descripción, fecha). Sin layout, para modal. | `PuntoMovimiento`, `Usuario` |
| `ajusteManual()` | POST | Recibe `$_POST['usuario_id']`, `$_POST['puntos']` (puede ser negativo), `$_POST['descripcion']`. Registra movimiento tipo `TMPT006`. Flash + redirect PRG. | `PuntoMovimiento` |
| `referidos()` | GET | Lista todos los referidos con filtros (búsqueda, estado) y paginación. | `Referido` |
| `verReferidoModal()` | GET | Muestra detalle del referido: datos del referidor, datos del referido, puntos otorgados, estado. Sin layout. | `Referido` |

#### 7.3.4. Vistas: `app/views/admin/gamificacion/`

**`index.php`** — Dashboard de gamificación:
- **Fila de KPIs** (Cards en `row` con `col-md-4`):
  - Card 1: "Puntos en Circulación" — ícono `fa-coins`, color dorado. Total de puntos acumulados en el sistema.
  - Card 2: "Promedio por Estudiante" — ícono `fa-user-graduate`, color azul. Promedio de puntos por usuario.
  - Card 3: "Referidos Exitosos" — ícono `fa-user-plus`, color verde. Total de referidos con estado acreditado.
- **Tabla Ranking Top 20** (`.foro-table`):

| # | Estudiante | Universidad | Puntos Acumulados | Nivel | Acciones |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | Avatar + Nombre | Nombre univ. | Número con formato | Badge (Novato/Confiable/Gold) | Ver historial (modal) + Ajustar puntos (modal) |

- **Modal de Historial** (`id="historialModal"`): Tabla de movimientos del usuario seleccionado.
- **Modal de Ajuste Manual** (`id="ajusteModal"`): Formulario con campos: `usuario_id` (hidden), `puntos` (input number, permite negativos), `descripcion` (textarea, required). Botones: Cancelar + "Aplicar Ajuste".

**`referidos.php`** — Lista de referidos:
- Tabla (`.foro-table`):

| Columna | Contenido |
| :--- | :--- |
| Referidor | Avatar + nombre del que invitó |
| Referido | Avatar + nombre del invitado |
| Puntos Otorgados | Número |
| Estado | Badge (Pendiente / Acreditado) |
| Fecha | `fecha_creacion` formateada |
| Acciones | `.btn-accion-view` (ver detalle en modal) |

#### 7.3.5. Rutas a registrar en `index.php`

```php
// Rutas de Administrador (Gamificación - ADMIN_SOCIALES)
$router->get('/admin/gamificacion', 'AdminGamificacionController', 'index');
$router->get('/admin/gamificacion/historial', 'AdminGamificacionController', 'historialUsuario');
$router->post('/admin/gamificacion/ajuste', 'AdminGamificacionController', 'ajusteManual');
$router->get('/admin/gamificacion/referidos', 'AdminGamificacionController', 'referidos');
$router->get('/admin/gamificacion/referido/ver-modal', 'AdminGamificacionController', 'verReferidoModal');
```

---

## 8. Módulo 6: Supervisión de Mensajería y Mediación de Disputas

> **Estado: 🔴 Pendiente** — Modelos y controlador por crear. Este módulo es más complejo y se recomienda como última prioridad.

### 8.1. Descripción Funcional (DF 4.5, 5.4 y 5.5)

El chat interno permite la negociación directa entre estudiantes y propietarios sin salir de la plataforma. Cuando surge una disputa (alojamiento no coincide, problemas de convivencia, incumplimiento contractual), el caso se escala al `ADMIN_SOCIALES` quien actúa como mediador.

El `ADMIN_SOCIALES` puede:
- Listar chats activos en la plataforma (solo lectura, no participar en conversaciones normales).
- Acceder a la conversación completa de un chat cuando hay una disputa activa (caso de mediación).
- Revisar evidencias adjuntas (fotos, documentos) enviadas dentro del chat.
- Emitir una resolución formal sobre la disputa.
- Enviar avisos masivos a la comunidad estudiantil.

### 8.2. Tablas del Modelo ER involucradas

| Tabla | Campos Clave | Relaciones |
| :--- | :--- | :--- |
| `chat` | `chat_id` (PK), `fecha_creacion` | — |
| `chat_usuario` | `chat_usuario_id` (PK) | `chat_id` (FK → `chat`), `usuario_id` (FK → `usuario`) |
| `mensaje` | `mensaje_id` (PK), `contenido`, `fecha_envio`, `estado_lectura_codigo` | `chat_id` (FK → `chat`), `usuario_id` (FK → `usuario`), `multimedia_id` (FK → `multimedia`) |

### 8.3. Diseño Técnico

#### 8.3.1. Modelo: `app/models/Chat.php` (Crear)

```php
namespace App\Models;

use App\Core\Database;
use PDO;

class Chat {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtener todos los chats con datos de los participantes.
     * Incluye: nombre de cada participante, último mensaje,
     * fecha del último mensaje, total de mensajes.
     */
    public function getAll($pagina = 1, $por_pagina = 20)

    /**
     * Obtener un chat por ID con la lista de participantes.
     */
    public function findById($id)

    /**
     * Contar chats totales (para paginación).
     */
    public function contar()
}
```

#### 8.3.2. Modelo: `app/models/Mensaje.php` (Crear)

```php
namespace App\Models;

use App\Core\Database;
use PDO;

class Mensaje {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtener todos los mensajes de un chat con datos del remitente.
     * JOIN con usuario. Incluye multimedia adjunta si existe.
     * Orden: fecha_envio ASC (cronológico).
     */
    public function getByChatId($chat_id)

    /**
     * Buscar mensajes que contengan texto específico (para investigación).
     */
    public function buscar($chat_id, $texto)

    /**
     * Contar mensajes de un chat.
     */
    public function contarPorChat($chat_id)
}
```

#### 8.3.3. Controlador: `app/controllers/AdminMensajeriaController.php` (Crear)

```
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Chat;
use App\Models\Mensaje;
use App\Models\Multimedia;
use App\Models\Usuario;

class AdminMensajeriaController extends Controller
{
    __construct()           → Validar sesión activa
    index()                 → Listar chats activos con paginación
    verChat()               → Ver conversación completa de un chat (solo lectura)
    verChatModal()          → Ver conversación en modal (sin layout)
}
```

**Detalle de cada método:**

| Método | Verbo | Descripción | Modelo(s) |
| :--- | :--- | :--- | :--- |
| `index()` | GET | Lista todos los chats con participantes, último mensaje y fecha. Paginado. | `Chat` |
| `verChat()` | GET | Página completa con el hilo de mensajes, avatares de los participantes y adjuntos multimedia. Solo lectura. | `Chat`, `Mensaje`, `Multimedia` |
| `verChatModal()` | GET | Versión modal (sin layout) del hilo de mensajes. | `Chat`, `Mensaje` |

#### 8.3.4. Vistas: `app/views/admin/mensajeria/`

**`index.php`** — Lista de chats:
- Tabla (`.foro-table`):

| Columna | Contenido |
| :--- | :--- |
| Participantes | Avatares (`.autor-avatar`) + nombres de los 2 usuarios |
| Último Mensaje | Texto truncado a 60 char |
| Fecha | Fecha del último mensaje, formato relativo ("hace 2 horas") |
| Total Mensajes | Número con ícono `fa-comment` |
| Acciones | `.btn-accion-view` (ver conversación) |

**`view.php`** — Visor de chat (solo lectura):
- Encabezado con datos de los participantes (nombres, avatares, roles).
- Contenedor de mensajes estilo chat:
  - Mensajes del participante A alineados a la izquierda con fondo claro.
  - Mensajes del participante B alineados a la derecha con fondo de color primario.
  - Cada burbuja muestra: nombre, hora de envío, contenido, estado de lectura.
  - Los adjuntos multimedia se muestran como miniaturas clicables.
- Banner superior: "🔒 Modo de supervisión — Solo lectura".

#### 8.3.5. Rutas a registrar en `index.php`

```php
// Rutas de Administrador (Mensajería - ADMIN_SOCIALES)
$router->get('/admin/mensajeria', 'AdminMensajeriaController', 'index');
$router->get('/admin/mensajeria/ver', 'AdminMensajeriaController', 'verChat');
$router->get('/admin/mensajeria/ver-modal', 'AdminMensajeriaController', 'verChatModal');
```

### 8.4. Consideraciones de Privacidad y Seguridad

- El acceso a las conversaciones del chat es una capacidad **excepcional** y debe estar restringida exclusivamente al rol `ADMIN_SOCIALES` y `SUPER_ADMIN`.
- Toda revisión de chat debe quedar registrada en un log de auditoría (quién accedió, cuándo, a qué conversación).
- La interfaz debe mostrar claramente que es un visor de **solo lectura** — el administrador no puede enviar mensajes en nombre de los usuarios.
- Se recomienda que el acceso a chats solo se active cuando existe una reserva con `estado_codigo = 'EN_DISPUTA'` vinculada a los participantes del chat.

---

## 9. Resumen de Archivos a Crear

### 9.1. Modelos (`app/models/`)

| Archivo | Estado | Descripción |
| :--- | :--- | :--- |
| `Resena.php` | 🟡 Ampliar | Agregar `buscar()`, `contar()`, `getAll()` |
| `ReseniaAlojamiento.php` | 🟡 Ampliar | Agregar `buscar()`, `contar()` |
| `Blog.php` | 🔴 Crear | CRUD completo de artículos |
| `ForoReaccion.php` | 🔴 Crear | Gestión de reacciones en foros |
| `Referido.php` | 🔴 Crear | Gestión de referidos entre usuarios |
| `PuntoMovimiento.php` | 🔴 Crear | Libro mayor de puntos NIDO |
| `Chat.php` | 🔴 Crear | Gestión de salas de chat |
| `Mensaje.php` | 🔴 Crear | Gestión de mensajes de chat |

### 9.2. Controladores (`app/controllers/`)

| Archivo | Estado | Métodos |
| :--- | :--- | :--- |
| `AdminForoController.php` | 🟡 Ampliar | Agregar `reaccionesModal()`, `eliminarReaccion()` |
| `AdminResenaController.php` | 🔴 Crear | `index()`, `verModal()`, `toggleEstado()` |
| `AdminBlogController.php` | 🔴 Crear | `index()`, `verModal()`, `crearModal()`, `guardar()`, `editarModal()`, `actualizar()`, `eliminar()`, `restaurar()` |
| `AdminGamificacionController.php` | 🔴 Crear | `index()`, `historialUsuario()`, `ajusteManual()`, `referidos()`, `verReferidoModal()` |
| `AdminMensajeriaController.php` | 🔴 Crear | `index()`, `verChat()`, `verChatModal()` |

### 9.3. Vistas (`app/views/admin/`)

| Directorio | Archivos | Estado |
| :--- | :--- | :--- |
| `admin/resena/` | `index.php`, `view.php` | 🔴 Crear |
| `admin/blog/` | `index.php`, `form.php`, `view.php` | 🔴 Crear |
| `admin/gamificacion/` | `index.php`, `referidos.php` | 🔴 Crear |
| `admin/mensajeria/` | `index.php`, `view.php` | 🔴 Crear |

### 9.4. Registros en Catálogo (`catalogo`)

| `referencia_codigo` | Códigos a insertar |
| :--- | :--- |
| `ESTADO_BLOG` | `ESBL001` (Borrador), `ESBL002` (Publicado), `ESBL003` (Oculto) |
| `TIPO_REACCION` | `TRFO001` (Me sirve), `TRFO002` (Gracias), `TRFO003` (Top recomendación) |
| `TIPO_MOVIMIENTO_PUNTO` | `TMPT001` a `TMPT007` (ver sección 7.2) |

---

## 10. Registro de Rutas en `index.php`

Todas las rutas nuevas que se deben agregar en `index.php` para completar el rol `ADMIN_SOCIALES`:

```php
// =====================================================
// RUTAS ADMIN_SOCIALES
// =====================================================

// --- Foros (Existentes) ---
$router->get('/admin/foros', 'AdminForoController', 'index');
$router->get('/admin/foros/ver', 'AdminForoController', 'ver');
$router->get('/admin/foros/ver-modal', 'AdminForoController', 'verModal');
$router->post('/admin/foros/toggle-estado', 'AdminForoController', 'toggleEstado');
$router->post('/admin/foros/comentario/eliminar', 'AdminForoController', 'eliminarComentario');
$router->post('/admin/foros/comentario/restaurar', 'AdminForoController', 'restaurarComentario');
$router->post('/admin/foros/ban-usuario', 'AdminForoController', 'toggleBanUsuario');
$router->get('/admin/foros/editar-modal', 'AdminForoController', 'editarForoModal');
$router->post('/admin/foros/actualizar', 'AdminForoController', 'actualizarForo');
$router->get('/admin/foros/comentario/editar-modal', 'AdminForoController', 'editarComentarioModal');
$router->post('/admin/foros/comentario/actualizar', 'AdminForoController', 'actualizarComentario');

// --- Foros: Reacciones (Nuevas) ---
$router->get('/admin/foros/reacciones-modal', 'AdminForoController', 'reaccionesModal');
$router->post('/admin/foros/reaccion/eliminar', 'AdminForoController', 'eliminarReaccion');

// --- Reseñas (Nuevas) ---
$router->get('/admin/resenas', 'AdminResenaController', 'index');
$router->get('/admin/resenas/ver-modal', 'AdminResenaController', 'verModal');
$router->post('/admin/resenas/toggle-estado', 'AdminResenaController', 'toggleEstado');

// --- Blog (Nuevas) ---
$router->get('/admin/blog', 'AdminBlogController', 'index');
$router->get('/admin/blog/ver-modal', 'AdminBlogController', 'verModal');
$router->get('/admin/blog/crear-modal', 'AdminBlogController', 'crearModal');
$router->post('/admin/blog/guardar', 'AdminBlogController', 'guardar');
$router->get('/admin/blog/editar-modal', 'AdminBlogController', 'editarModal');
$router->post('/admin/blog/actualizar', 'AdminBlogController', 'actualizar');
$router->post('/admin/blog/eliminar', 'AdminBlogController', 'eliminar');
$router->post('/admin/blog/restaurar', 'AdminBlogController', 'restaurar');

// --- Gamificación y Referidos (Nuevas) ---
$router->get('/admin/gamificacion', 'AdminGamificacionController', 'index');
$router->get('/admin/gamificacion/historial', 'AdminGamificacionController', 'historialUsuario');
$router->post('/admin/gamificacion/ajuste', 'AdminGamificacionController', 'ajusteManual');
$router->get('/admin/gamificacion/referidos', 'AdminGamificacionController', 'referidos');
$router->get('/admin/gamificacion/referido/ver-modal', 'AdminGamificacionController', 'verReferidoModal');

// --- Mensajería y Mediación (Nuevas) ---
$router->get('/admin/mensajeria', 'AdminMensajeriaController', 'index');
$router->get('/admin/mensajeria/ver', 'AdminMensajeriaController', 'verChat');
$router->get('/admin/mensajeria/ver-modal', 'AdminMensajeriaController', 'verChatModal');
```

---

## Prioridad de Implementación Recomendada

| Prioridad | Módulo | Justificación |
| :--- | :--- | :--- |
| **1 (Alta)** | Moderación de Reseñas | Los modelos `Resena.php` y `ReseniaAlojamiento.php` ya existen. Solo falta el controlador y la vista. Victoria rápida. |
| **2 (Alta)** | Reacciones en Foros | La tabla `foro_reaccion` ya existe y el modelo `Foro.php` ya cuenta reacciones. Solo falta el modelo `ForoReaccion.php` y ampliar el controlador existente. |
| **3 (Media)** | Blog / Guía del Universitario | La tabla `blog` existe en el ER. Requiere creación completa (modelo + controlador + vistas) pero sigue el patrón CRUD estándar. |
| **4 (Media)** | Gamificación y Referidos | Requiere 2 modelos nuevos y un controlador con lógica de transacciones (atomicidad al registrar puntos). |
| **5 (Baja)** | Supervisión de Mensajería | Módulo más complejo con implicaciones de privacidad. Depende de que el sistema de chat del lado del usuario esté implementado primero. |
