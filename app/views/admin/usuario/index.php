<div class="container-fluid">
    <!-- Header principal -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold">
                <i class="fas fa-users text-primary me-2"></i>Administración de Usuarios
            </h1>
            <p class="text-muted small mb-0">Gestión de cuentas, asignación de roles, auditoría de perfiles y control de acceso (ban/reactivación)</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary shadow-sm fw-semibold" onclick="abrirModalCrear()">
                <i class="fas fa-user-plus me-1"></i> Nuevo Usuario
            </button>
        </div>
    </div>

    <!-- Mensajes Flash -->
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 bg-success text-white" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 bg-danger text-white" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Tarjeta de Filtros -->
    <div class="card shadow-sm mb-4 border-0 rounded-3">
        <div class="card-body p-3 bg-light rounded-3">
            <form method="GET" action="/admin/usuarios" class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold text-secondary mb-1">Buscar por Nombre, Correo o DNI</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="busqueda" class="form-control border-start-0" placeholder="Ej: Jesus Huerta, correo@uni.pe, 7123..." value="<?= htmlspecialchars($filtros['busqueda'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold text-secondary mb-1">Filtrar por Rol</label>
                    <select name="rol_id" class="form-select form-select-sm">
                        <option value="">-- Todos los roles --</option>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['rol_id'] ?>" <?= ($filtros['rol_id'] ?? '') == $r['rol_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold text-secondary mb-1">Estado de Acceso</label>
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">-- Todos los estados --</option>
                        <option value="activo" <?= ($filtros['estado'] ?? '') === 'activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="baneado" <?= ($filtros['estado'] ?? '') === 'baneado' ? 'selected' : '' ?>>Inhabilitado / Baneado</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    <?php if (!empty($filtros['busqueda']) || !empty($filtros['rol_id']) || !empty($filtros['estado'])): ?>
                        <a href="/admin/usuarios" class="btn btn-outline-secondary btn-sm" title="Limpiar filtros">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Tarjeta Principal con la Tabla -->
    <div class="card shadow mb-4 border-0 rounded-3">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center border-bottom">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-1"></i> Listado de Usuarios Registrados 
                <span class="badge bg-primary rounded-pill ms-2"><?= $total_usuarios ?> totales</span>
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Usuario</th>
                            <th>Rol</th>
                            <th>Universidad</th>
                            <th>Puntos NIDO</th>
                            <th>Estado</th>
                            <th>Registro</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php if (empty($usuarios)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-user-slash fa-3x mb-3 text-secondary d-block opacity-50"></i>
                                    <p class="mb-0 fw-semibold">No se encontraron usuarios que coincidan con los filtros aplicados.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($usuarios as $u): ?>
                                <?php 
                                    // Determinar color de badge por rol
                                    $rolNombre = !empty($u['rol_nombre']) ? $u['rol_nombre'] : 'Sin Rol';
                                    $badgeColor = 'bg-secondary';
                                    if (stripos($rolNombre, 'Admin') !== false) $badgeColor = 'bg-danger';
                                    elseif (stripos($rolNombre, 'Estudiante') !== false) $badgeColor = 'bg-primary';
                                    elseif (stripos($rolNombre, 'Propietario') !== false) $badgeColor = 'bg-success';
                                    elseif (stripos($rolNombre, 'Moderador') !== false || stripos($rolNombre, 'Social') !== false) $badgeColor = 'bg-info text-dark';

                                    // Avatar por defecto o foto real
                                    $avatarUrl = !empty($u['url_foto']) ? $u['url_foto'] : 'https://ui-avatars.com/api/?name=' . urlencode($u['nombres'] . ' ' . $u['apellido_paterno']) . '&background=random&color=fff&size=128';
                                ?>
                                <tr class="<?= !$u['habilitado'] ? 'bg-light opacity-75' : '' ?>">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="<?= $avatarUrl ?>" alt="Avatar" class="rounded-circle shadow-sm object-fit-cover" width="45" height="45" onerror="this.src='https://ui-avatars.com/api/?name=User&background=6c757d&color=fff'">
                                            <div>
                                                <div class="fw-bold text-dark d-flex align-items-center gap-1">
                                                    <?= htmlspecialchars($u['nombres'] . ' ' . $u['apellido_paterno']) ?>
                                                    <?php if (!empty($u['verificado'])): ?>
                                                        <i class="fas fa-check-circle text-primary small" title="Cuenta Verificada" data-bs-toggle="tooltip"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="small text-muted"><i class="fas fa-envelope me-1 opacity-75"></i><?= htmlspecialchars($u['correo']) ?></div>
                                                <?php if (!empty($u['numero_documento'])): ?>
                                                    <div class="small text-secondary" style="font-size: 0.75rem;"><i class="fas fa-id-card me-1 opacity-75"></i><?= htmlspecialchars($u['numero_documento']) ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge <?= $badgeColor ?> px-3 py-2 rounded-pill fw-semibold shadow-sm">
                                            <?= htmlspecialchars($rolNombre) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($u['universidad_nombre'])): ?>
                                            <div class="fw-semibold small text-dark"><?= htmlspecialchars($u['universidad_siglas'] ?: $u['universidad_nombre']) ?></div>
                                            <div class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars((strlen($u['universidad_nombre']) > 25 ? substr($u['universidad_nombre'], 0, 25) . '...' : $u['universidad_nombre'])) ?></div>
                                        <?php else: ?>
                                            <span class="text-muted small">--</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1 fw-bold text-warning">
                                            <i class="fas fa-coins"></i>
                                            <span class="text-dark"><?= number_format($u['puntos_acumulados'] ?? 0) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($u['habilitado']): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 rounded-pill">
                                                <i class="fas fa-circle small me-1"></i> Activo
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1 rounded-pill">
                                                <i class="fas fa-ban small me-1"></i> Baneado
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="small text-secondary"><?= date('d/m/Y', strtotime($u['creado'] ?? 'now')) ?></div>
                                        <div class="text-muted" style="font-size: 0.75rem;"><?= date('H:i', strtotime($u['creado'] ?? 'now')) ?></div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group shadow-sm">
                                            <button type="button" class="btn btn-sm btn-outline-info" title="Ver Detalle" onclick="verUsuario('<?= $u['usuario_id'] ?>')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning" title="Editar Usuario" onclick="editarUsuario('<?= $u['usuario_id'] ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm <?= $u['habilitado'] ? 'btn-outline-danger' : 'btn-outline-success' ?>" 
                                                    title="<?= $u['habilitado'] ? 'Inhabilitar / Banear' : 'Reactivar acceso' ?>" 
                                                    onclick="toggleEstadoUsuario('<?= $u['usuario_id'] ?>', <?= $u['habilitado'] ? 'true' : 'false' ?>, '<?= htmlspecialchars(addslashes($u['nombres'])) ?>')">
                                                <i class="fas <?= $u['habilitado'] ? 'fa-user-slash' : 'fa-user-check' ?>"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginación -->
        <?php if ($total_paginas > 1): ?>
            <div class="card-footer bg-white py-3 d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    Mostrando página <strong class="text-dark"><?= $pagina_actual ?></strong> de <strong class="text-dark"><?= $total_paginas ?></strong> (<?= $total_usuarios ?> registros en total)
                </div>
                <nav aria-label="Navegación de usuarios">
                    <ul class="pagination pagination-sm mb-0">
                        <?php if ($pagina_actual > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?pagina=<?= $pagina_actual - 1 ?>&busqueda=<?= urlencode($filtros['busqueda']) ?>&rol_id=<?= $filtros['rol_id'] ?>&estado=<?= $filtros['estado'] ?>">Anterior</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $pagina_actual - 2); $i <= min($total_paginas, $pagina_actual + 2); $i++): ?>
                            <li class="page-item <?= $i == $pagina_actual ? 'active' : '' ?>">
                                <a class="page-link" href="?pagina=<?= $i ?>&busqueda=<?= urlencode($filtros['busqueda']) ?>&rol_id=<?= $filtros['rol_id'] ?>&estado=<?= $filtros['estado'] ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($pagina_actual < $total_paginas): ?>
                            <li class="page-item">
                                <a class="page-link" href="?pagina=<?= $pagina_actual + 1 ?>&busqueda=<?= urlencode($filtros['busqueda']) ?>&rol_id=<?= $filtros['rol_id'] ?>&estado=<?= $filtros['estado'] ?>">Siguiente</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL DINÁMICO DE USUARIOS                 -->
<!-- ========================================== -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-3" id="modalUsuarioContent">
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="text-muted small mt-2">Cargando información del usuario...</p>
            </div>
        </div>
    </div>
</div>

<script>
let modalUsuarioInstancia = null;

document.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.getElementById('modalUsuario');
    if (modalElement && typeof bootstrap !== 'undefined') {
        modalUsuarioInstancia = new bootstrap.Modal(modalElement);
    }
    // Inicializar tooltips de Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function mostrarCargandoModal() {
    document.getElementById('modalUsuarioContent').innerHTML = `
        <div class="modal-body text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="text-muted small mt-2">Cargando información...</p>
        </div>
    `;
    if (modalUsuarioInstancia) modalUsuarioInstancia.show();
}

function verUsuario(id) {
    mostrarCargandoModal();
    fetch(`/admin/usuarios/ver-modal?id=${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalUsuarioContent').innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('modalUsuarioContent').innerHTML = `<div class="p-4 text-center text-danger">Error al cargar los datos del usuario.</div>`;
        });
}

function abrirModalCrear() {
    mostrarCargandoModal();
    fetch(`/admin/usuarios/crear-modal`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalUsuarioContent').innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('modalUsuarioContent').innerHTML = `<div class="p-4 text-center text-danger">Error al cargar el formulario.</div>`;
        });
}

function editarUsuario(id) {
    mostrarCargandoModal();
    fetch(`/admin/usuarios/editar-modal?id=${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalUsuarioContent').innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('modalUsuarioContent').innerHTML = `<div class="p-4 text-center text-danger">Error al cargar el formulario de edición.</div>`;
        });
}

function toggleEstadoUsuario(id, esActivo, nombreUsuario) {
    const accion = esActivo ? 'Inhabilitar (Banear)' : 'Reactivar acceso';
    const desc = esActivo ? 
        `El usuario "${nombreUsuario}" perderá acceso temporalmente a la plataforma Nido Universitario.` : 
        `El usuario "${nombreUsuario}" podrá volver a iniciar sesión y utilizar la plataforma.`;
    const btnColor = esActivo ? '#d33' : '#198754';
    const btnText = esActivo ? 'Sí, inhabilitar' : 'Sí, reactivar';

    Swal.fire({
        title: `¿${accion}?`,
        text: desc,
        icon: esActivo ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonColor: btnColor,
        cancelButtonColor: '#6c757d',
        confirmButtonText: btnText,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Enviar POST via AJAX o formulario
            const formData = new FormData();
            formData.append('id', id);

            fetch('/admin/usuarios/toggle-estado', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '¡Actualizado!',
                        text: data.mensaje,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', 'No se pudo cambiar el estado del usuario.', 'error');
                }
            })
            .catch(() => {
                // Si falla el JSON, recargar de todas formas (respaldo Flash)
                window.location.reload();
            });
        }
    });
}
</script>
