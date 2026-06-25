# Estándares y Convenciones del Frontend (Rooms)

Este documento define los patrones de UI/UX, componentes y convenciones para el desarrollo del frontend, con el objetivo de mantener consistencia visual y funcional en toda la aplicación.

---

## 1. Tablas Estandarizadas

Todas las tablas del admin deben seguir el mismo patrón visual definido en `admin-theme.css` (clase `.foro-table`).

### Estructura base

```php
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1"><?php echo htmlspecialchars($titulo ?? 'Título'); ?></h1>
            <p class="text-muted small mb-0">Subtítulo o descripción</p>
        </div>
        <span class="badge bg-primary fs-6 px-3 py-2">
            <i class="fas fa-icon me-1"></i> <?php echo count($items ?? []); ?> registros
        </span>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center">
            <h6 class="m-0 fw-bold"><i class="fas fa-list me-2"></i>Listado de [Entidad]</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table foro-table mb-0">
                    <thead>
                        <tr>
                            <th>Columna</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>...</td>
                                <td class="text-center">
                                    <div class="acciones-group">
                                        <button class="btn-accion btn-accion-view" title="Ver" data-id="<?php echo $item['id']; ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-accion btn-accion-edit" title="Editar" data-id="<?php echo $item['id']; ?>">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <button class="btn-accion btn-accion-delete" title="Eliminar" data-id="<?php echo $item['id']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
```

### Botones de acción disponibles

| Clase | Icono | Propósito | Color hover |
|---|---|---|---|
| `btn-accion-view` | `fa-eye` | Ver detalles | Rojo (primary) |
| `btn-accion-edit` | `fa-pen` | Editar registro | Azul |
| `btn-accion-hide` | `fa-power-off` | Ocultar/Deshabilitar | Ámbar |
| `btn-accion-restore` | `fa-check` | Restaurar/Activar | Verde |
| `btn-accion-delete` | `fa-trash` | Eliminar | Rojo |

Para agregar un nuevo color de hover, añadir en `admin-theme.css`:

```css
.btn-accion-MIACCION:hover {
  background: #COLOR;
  border-color: #COLOR;
  color: white;
}
```

### Badges de estado

```php
<span class="status-badge status-activo"><span class="status-dot"></span> Activo</span>
<span class="status-badge status-oculto"><span class="status-dot"></span> Oculto</span>
```

### Avatar con inicial

```php
<div class="autor-avatar"><?php echo strtoupper(substr($nombre, 0, 1)); ?></div>
```

### Interacciones (grupo compacto)

```php
<div class="interacciones-group">
    <span class="interaccion-item">
        <i class="fas fa-comment"></i>
        <span><?php echo $total; ?></span>
    </span>
</div>
```

---

## 2. Modal para Ver Detalles

Siempre que se requiera ver detalles de un registro, usar un modal centralizado con fetch asíncrono. No redirigir a una página separada.

### Controlador

Agregar un método que renderice la vista **sin layout** (cadena vacía como tercer parámetro):

```php
public function verModal()
{
    $id = $_GET['id'] ?? null;
    if (!$id) {
        echo '<div class="p-4 text-muted">Registro no encontrado.</div>';
        return;
    }

    $model = new MiModelo();
    $item = $model->findById($id);

    if (!$item) {
        echo '<div class="p-4 text-muted">Registro no encontrado.</div>';
        return;
    }

    $data = [
        'titulo' => 'Ver: ' . $item['nombre'],
        'item' => $item,
    ];

    $this->render('ruta/de/vista', $data, '');
}
```

### Ruta

```php
$router->get('/admin/mi-entity/ver-modal', 'MiController', 'verModal');
```

### Vista parcial

La vista se renderiza sin layout, por lo que solo debe contener el HTML del contenido. Puede reutilizar la misma vista de detalle existente:

```php
$this->render('admin/mi-entity/view', $data, '');
```

### HTML del modal + JS (en la vista del listado)

```php
<!-- Modal para Ver -->
<div class="modal fade" id="entityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="entityModalLabel">Detalle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="entityModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('entityModal');
    const modalBody = document.getElementById('entityModalBody');

    modal.addEventListener('hidden.bs.modal', function () {
        modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>';
    });

    document.querySelectorAll('.btn-view-foro').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>';

            fetch('/admin/mi-entity/ver-modal?id=' + id)
                .then(function(response) { return response.text(); })
                .then(function(html) {
                    modalBody.innerHTML = html;
                    var bsModal = new bootstrap.Modal(modal);
                    bsModal.show();
                })
                .catch(function() {
                    modalBody.innerHTML = '<div class="alert alert-danger m-3">Error al cargar.</div>';
                    var bsModal = new bootstrap.Modal(modal);
                    bsModal.show();
                });
        });
    });
});
</script>
```

---

## 3. Modal para Editar Registro

### Controlador

Dos métodos: uno para renderizar el formulario (GET) y otro para procesar (POST).

```php
public function editarModal()
{
    $id = $_GET['id'] ?? null;
    if (!$id) {
        echo '<div class="p-4 text-muted">Registro no encontrado.</div>';
        return;
    }

    $model = new MiModelo();
    $item = $model->findById($id);

    if (!$item) {
        echo '<div class="p-4 text-muted">Registro no encontrado.</div>';
        return;
    }

    $data = [
        'item' => $item,
    ];

    $this->render('admin/mi-entity/form', $data, '');
}

public function actualizar()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['id'] ?? null;
        // Validar y actualizar...

        $model = new MiModelo();
        $model->actualizar($id, $_POST);

        // Redirigir de vuelta al listado
        $this->redirect('/admin/mi-entity');
    }
}
```

### Rutas

```php
$router->get('/admin/mi-entity/editar-modal', 'MiController', 'editarModal');
$router->post('/admin/mi-entity/actualizar', 'MiController', 'actualizar');
```

### Formulario en modal

La vista `form.php` solo contiene el formulario (sin layout):

```php
<div class="p-3">
    <form action="/admin/mi-entity/actualizar" method="POST">
        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">

        <div class="mb-3">
            <label class="form-label fw-semibold small">Nombre</label>
            <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($item['nombre']); ?>" required>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div>
```

### JS para abrir modal de edición

```javascript
document.querySelectorAll('.btn-accion-edit').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var id = this.getAttribute('data-id');
        modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>';

        fetch('/admin/mi-entity/editar-modal?id=' + id)
            .then(function(response) { return response.text(); })
            .then(function(html) {
                modalBody.innerHTML = html;
                var bsModal = new bootstrap.Modal(modal);
                bsModal.show();
            });
    });
});
```

---

## 4. Modal para Crear Registro

Similar al de editar pero sin ID y con acción POST diferente.

### Controlador

```php
public function crearModal()
{
    $data = [
        // Valores por defecto si aplica
    ];
    $this->render('admin/mi-entity/form', $data, '');
}

public function guardar()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $model = new MiModelo();
        $model->crear($_POST);
        $this->redirect('/admin/mi-entity');
    }
}
```

### Rutas

```php
$router->get('/admin/mi-entity/crear-modal', 'MiController', 'crearModal');
$router->post('/admin/mi-entity/guardar', 'MiController', 'guardar');
```

### Botón flotante en el listado

```php
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1">Listado</h1>
        <p class="text-muted small mb-0">Descripción</p>
    </div>
    <button type="button" class="btn btn-primary" id="btnCrear">
        <i class="fas fa-plus me-1"></i> Nuevo Registro
    </button>
</div>
```

### JS para abrir modal de creación

```javascript
document.getElementById('btnCrear').addEventListener('click', function() {
    modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>';

    fetch('/admin/mi-entity/crear-modal')
        .then(function(response) { return response.text(); })
        .then(function(html) {
            modalBody.innerHTML = html;
            var bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        });
});
```

---

## 5. Soft Delete (Eliminación Lógica)

No usar `DELETE FROM`. En su lugar, usar una columna `habilitado` (BOOLEAN, default `true`).

### Modelo

```php
public function eliminar($id) {
    $query = "UPDATE mi_tabla SET habilitado = false WHERE id = :id";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}

public function restaurar($id) {
    $query = "UPDATE mi_tabla SET habilitado = true WHERE id = :id";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}
```

### Vistas

- Los registros con `habilitado = false` se muestran con opacidad reducida (clase `row-oculto`)
- El badge muestra "Oculto" en lugar de "Activo"
- El botón de acción cambia entre "Eliminar" y "Restaurar"

---

## 6. Resumen de componentes CSS disponibles

| Componente | Clase CSS | Uso |
|---|---|---|
| Tabla estándar | `.foro-table` | Todas las tablas del admin |
| Botón acción ver | `.btn-accion.btn-accion-view` | Ver detalles (modal) |
| Botón acción editar | `.btn-accion.btn-accion-edit` | Editar registro (modal) |
| Botón acción eliminar | `.btn-accion.btn-accion-hide` | Soft delete |
| Botón acción restaurar | `.btn-accion.btn-accion-restore` | Restaurar soft delete |
| Badge activo | `.status-badge.status-activo` | Estado habilitado |
| Badge oculto | `.status-badge.status-oculto` | Estado deshabilitado |
| Avatar inicial | `.autor-avatar` | Círculo con inicial |
| Grupo interacciones | `.interacciones-group` | Métricas compactas |
| Icono entidad | `.foro-icon` | Icono cuadrado con gradiente |
| Fila oculta | `.row-oculto` | Para registros deshabilitados |

### Migración SQL para agregar soft delete

```sql
ALTER TABLE mi_tabla
ADD COLUMN IF NOT EXISTS habilitado BOOLEAN NOT NULL DEFAULT true;

UPDATE mi_tabla SET habilitado = true WHERE habilitado IS NULL;
```

---

## 7. Flujo completo para nueva entidad

1. Crear **modelo** con métodos: `getAll()`, `findById()`, `crear()`, `actualizar()`, `eliminar()`, `restaurar()`
2. Crear **controlador** con métodos: `index()`, `verModal()`, `crearModal()`, `guardar()`, `editarModal()`, `actualizar()`, `eliminar()`, `restaurar()`
3. Registrar **rutas** en `index.php`
4. Crear **vistas**: `index.php` (tabla + modales), `form.php` (formulario reutilizable), `view.php` (detalle)
5. Agregar columna `habilitado` mediante migración SQL si aplica
