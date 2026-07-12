# PLAN — Verificación de estudiante desde el panel admin (rooms-frontend)

> Objetivo: que desde el módulo de **Usuarios** del admin se pueda **ver el documento de verificación del estudiante** (carnet/constancia) y **verificar / desverificar a un inquilino (estudiante)**, con un **cambio visual claro** entre verificados y no verificados. Hoy el módulo no expone esa opción y no muestra el documento.

Fecha: 2026-07-12 · Proyecto: `rooms-frontend` (panel admin)

---

## 0. Hallazgos previos (estado actual)

| Aspecto | Estado | Archivo / línea |
|---|---|---|
| Rutas admin de usuarios | `/admin/usuarios`, `ver-modal`, `crear-modal`, `guardar`, `editar-modal`, `actualizar`, `toggle-estado` | `index.php:147-153` |
| `AdminUsuarioController` | No tiene acción de verificar/desverificar | `app/controllers/AdminUsuarioController.php` |
| Modelo `Usuario::findById` | `SELECT u.*` → **ya trae** `url_verificacion_estudiante` y `verificado` | `app/models/Usuario.php:60-78` |
| Modal editar — checkbox `verificado` | ✅ Existe (guarda vía `actualizarAdmin`) | `app/views/admin/usuario/form_modal.php:51-58` |
| Modal ver — ficha del usuario | **No** muestra documento de verificación ni acción de verificar | `app/views/admin/usuario/view_modal.php` |
| Tabla listado — indicador verificado | Sólo un `<i fa-check-circle>` azul junto al nombre | `app/views/admin/usuario/index.php:126-128` |
| Acciones por fila | Ver / Editar / Banear — **no** verificar | `app/views/admin/usuario/index.php:171-185` |
| `AzureStorage` | ✅ Existe en `app/core/AzureStorage.php` (no se necesita para ver, sólo para servir la URL que ya está en BD) | — |
| Columna `url_verificacion_estudiante` | Existe en BD, poblada por el inquilino al subir doc | confirmado vía `information_schema` |

**Rol de "estudiante/inquilino":** el listado ya identifica el rol por nombre (`Estudiante` → badge `bg-primary`, `index.php:112`). Usaremos `stripos($rolNombre,'Estudiante') !== false` (o el `rol_codigo`) para mostrar las acciones de verificación sólo a inquilinos.

---

## 1. Tareas

### T1 — Modelo `app/models/Usuario.php`: métodos de verificación

Agregar:

```php
/**
 * Marca a un usuario como verificado (true) o no verificado (false).
 */
public function setVerificado($id, bool $verificado): bool
{
    $sql = "UPDATE usuario SET verificado = :v, modificado = CURRENT_TIMESTAMP
            WHERE usuario_id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':v', $verificado, PDO::PARAM_BOOL);
    $stmt->bindValue(':id', $id);
    return $stmt->execute();
}
```

> No hace falta un getter específico: `findById` ya trae `verificado` y `url_verificacion_estudiante`.

### T2 — Controller `app/controllers/AdminUsuarioController.php`

**T2.1** Nuevo método `verificarEstudiante()` (POST, AJAX):

```php
public function verificarEstudiante()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('/admin/usuarios'); }
    $id = $_POST['id'] ?? $_POST['usuario_id'] ?? null;
    if (!$id) { $this->json(['success' => false, 'mensaje' => 'ID inválido.']); }

    $usuarioModel = new Usuario();
    $ok = $usuarioModel->setVerificado($id, true);

    $esAjax = (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest');
    if ($esAjax) {
        $this->json(['success' => $ok, 'mensaje' => $ok ? 'Estudiante verificado correctamente.' : 'No se pudo verificar.']);
    }
    self::setFlash($ok ? 'success' : 'error', $ok ? 'Estudiante verificado.' : 'No se pudo verificar.');
    $this->redirect('/admin/usuarios');
}

public function desverificarEstudiante()
{
    // espejo de verificarEstudiante con setVerificado($id, false)
}
```

(Agregar helper `json()` privado si no existe — el controller no lo tiene hoy.)

**T2.2** `verModal()`: ya pasa `$usuario` a la vista. Sin cambio de controller; la vista leerá `$usuario['url_verificacion_estudiante']` y `$usuario['rol_codigo']`/`rol_nombre`.

### T3 — Rutas en `index.php`

Agregar tras la línea 153:

```php
$router->post('/admin/usuarios/verificar', 'AdminUsuarioController', 'verificarEstudiante');
$router->post('/admin/usuarios/desverificar', 'AdminUsuarioController', 'desverificarEstudiante');
```

### T4 — Vista `app/views/admin/usuario/view_modal.php`: mostrar documento + acciones

Después del bloque "Información del Sistema" (antes de `</div>` del modal-body), agregar una sección condicional sólo para estudiantes:

```php
<?php $esEstudiante = stripos($usuario['rol_nombre'] ?? '', 'Estudiante') !== false
                   || ($usuario['rol_codigo'] ?? '') === 'EST'; ?>
<?php if ($esEstudiante): ?>
    <div class="col-12 mt-2">
        <h6 class="text-uppercase small fw-bold text-secondary border-bottom pb-2 mb-3">
            <i class="fas fa-shield-alt text-success me-2"></i>Verificación de identidad
        </h6>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 rounded-3 border">
            <div>
                <?php if (!empty($usuario['verificado'])): ?>
                    <span class="badge bg-success px-3 py-2"><i class="fas fa-check-circle me-1"></i>Verificado</span>
                <?php elseif (!empty($usuario['url_verificacion_estudiante'])): ?>
                    <span class="badge bg-warning text-dark px-3 py-2"><i class="fas fa-hourglass-half me-1"></i>En revisión</span>
                <?php else: ?>
                    <span class="badge bg-secondary px-3 py-2"><i class="fas fa-clock me-1"></i>Sin documento</span>
                <?php endif; ?>
            </div>
            <div class="d-flex gap-2">
                <?php if (!empty($usuario['url_verificacion_estudiante'])):
                    $urlDoc = $usuario['url_verificacion_estudiante'];
                    $esImg  = preg_match('/\.(jpg|jpeg|png|webp|gif)(\?|$)/i', $urlDoc);
                ?>
                    <?php if ($esImg): ?>
                        <button type="button" class="btn btn-outline-info btn-sm"
                                onclick="abrirVisorDocumento('<?= htmlspecialchars($urlDoc, ENT_QUOTES) ?>')">
                            <i class="fas fa-eye me-1"></i> Ver documento
                        </button>
                    <?php else: ?>
                        <a href="<?= htmlspecialchars($urlDoc) ?>" target="_blank" rel="noopener"
                           class="btn btn-outline-info btn-sm">
                            <i class="fas fa-file-pdf me-1"></i> Abrir documento
                        </a>
                    <?php endif; ?>
                    <?php if (empty($usuario['verificado'])): ?>
                        <button type="button" class="btn btn-success btn-sm fw-semibold"
                                onclick="verificarEstudiante('<?= $usuario['usuario_id'] ?>', '<?= htmlspecialchars(addslashes($usuario['nombres']), ENT_QUOTES) ?>')">
                            <i class="fas fa-check me-1"></i> Aprobar verificación
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-outline-danger btn-sm"
                                onclick="desverificarEstudiante('<?= $usuario['usuario_id'] ?>', '<?= htmlspecialchars(addslashes($usuario['nombres']), ENT_QUOTES) ?>')">
                            <i class="fas fa-times me-1"></i> Quitar verificación
                        </button>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="text-muted small">El estudiante aún no ha subido su documento.</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
```

Agregar al `<script>` de la vista (o al `index.php` que carga el modal) las funciones `verificarEstudiante(id,nombre)`, `desverificarEstudiante(id,nombre)` y `abrirVisorDocumento(url)`:

```js
function verificarEstudiante(id, nombre) {
    Swal.fire({
        title: '¿Verificar estudiante?',
        text: `Se marcará a "${nombre}" como estudiante verificado.`,
        icon: 'question', showCancelButton: true,
        confirmButtonColor: '#198754', cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, verificar'
    }).then(r => {
        if (!r.isConfirmed) return;
        const fd = new FormData(); fd.append('id', id);
        fetch('/admin/usuarios/verificar', { method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body: fd })
          .then(r => r.json()).then(d => {
              Swal.fire({ icon: d.success?'success':'error', title: d.success?'Verificado':'Error', text: d.mensaje, timer: 1500, showConfirmButton:false })
                .then(() => { if (d.success) location.reload(); });
          });
    });
}
function desverificarEstudiante(id, nombre) { /* espejo → /admin/usuarios/desverificar */ }

function abrirVisorDocumento(url) {
    Swal.fire({
        title: 'Documento de verificación',
        html: `<img src="${url}" alt="documento" style="max-width:100%;max-height:70vh;border-radius:8px;">`,
        width: 700, showCloseButton: true, showConfirmButton: false,
        background: '#fff'
    });
}
```

### T5 — Vista `app/views/admin/usuario/index.php`: cambio visual en la tabla

**T5.1** Reforzar el indicador de verificado/no verificado en la columna "Usuario" (líneas 126-128). Reemplazar el simple `<i>` por un badge visible sólo para estudiantes:

```php
<?php $esEst = stripos($rolNombre, 'Estudiante') !== false; ?>
<?php if ($esEst && !empty($u['verificado'])): ?>
    <span class="badge bg-success bg-opacity-10 text-success border border-success ms-1" style="font-size:.7rem;">
        <i class="fas fa-check-circle me-1"></i>Verificado
    </span>
<?php elseif ($esEst && !empty($u['url_verificacion_estudiante'])): ?>
    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning ms-1" style="font-size:.7rem;">
        <i class="fas fa-hourglass-half me-1"></i>En revisión
    </span>
<?php elseif ($esEst): ?>
    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary ms-1" style="font-size:.7rem;">
        <i class="fas fa-clock me-1"></i>No verificado
    </span>
<?php endif; ?>
```

**T5.2** Agregar un botón de verificación rápida en las acciones de fila (líneas 172-184), sólo para estudiantes con documento:

```php
<?php if ($esEst && !empty($u['url_verificacion_estudiante'])): ?>
    <button type="button" class="btn btn-sm <?= !empty($u['verificado']) ? 'btn-outline-secondary' : 'btn-outline-success' ?>"
            title="<?= !empty($u['verificado']) ? 'Quitar verificación' : 'Verificar estudiante' ?>"
            onclick="verUsuario('<?= $u['usuario_id'] ?>')">
        <i class="fas fa-shield-alt"></i>
    </button>
<?php endif; ?>
```

> Este botón abre el modal de ficha (que tras T4 ya muestra el documento + acción de aprobar). Así el admin ve el doc antes de verificar. (Alternativa: hacer toggle directo por AJAX sin abrir modal — pero ver el documento antes de aprobar es más seguro y es lo que pidió el usuario.)

**T5.3** (Opcional) Tinte sutil de fila para verificados: en `<tr class="...">` agregar `style="background: rgba(25,135,84,0.04);"` cuando `$esEst && !empty($u['verificado'])`.

---

## 2. Orden de ejecución

1. T1 (modelo: `setVerificado`)
2. T2 (controller: `verificarEstudiante`, `desverificarEstudiante`, helper `json`)
3. T3 (rutas en `index.php`)
4. T4 (modal `view_modal.php` + JS)
5. T5 (tabla `index.php`)
6. Verificación end-to-end (sección 4)

## 3. Archivos tocados (resumen)

- `app/models/Usuario.php` (agregar `setVerificado`)
- `app/controllers/AdminUsuarioController.php` (agregar `verificarEstudiante`, `desverificarEstudiante`, `json`)
- `index.php` (2 rutas nuevas)
- `app/views/admin/usuario/view_modal.php` (sección verificación + JS)
- `app/views/admin/usuario/index.php` (badge por fila + botón escudo)

## 4. Verificación

- [ ] Un inquilino sube su carnet desde `rooms-inquilino-frontend` → en BD `usuario.url_verificacion_estudiante` queda con URL Azure.
- [ ] Admin → Usuarios → el estudiante muestra badge "En revisión" y botón escudo en la fila.
- [ ] Click en escudo → abre ficha → sección "Verificación de identidad" → "Ver documento" muestra la imagen/PDF.
- [ ] "Aprobar verificación" → confirmación → `verificado=true` en BD → la fila pasa a badge "Verificado" (verde) y el inquilino ve "Estudiante verificado" en su perfil.
- [ ] "Quitar verificación" → vuelve a "En revisión" (si tiene doc) o "No verificado".
- [ ] Un usuario que NO es estudiante (propietario/admin) no muestra la sección ni el botón escudo.
- [ ] Un estudiante sin documento subido muestra "Sin documento" y no botón de aprobar.
