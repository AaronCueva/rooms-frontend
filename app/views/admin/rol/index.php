<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo htmlspecialchars($titulo); ?></h1>
        <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRol" data-action="create">
            <i class="fas fa-plus fa-sm text-white-50"></i> Nuevo Rol
        </button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($roles)): ?>
                            <?php foreach($roles as $rol): ?>
                                <tr>
                                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($rol['codigo']); ?></span></td>
                                    <td class="fw-bold"><?php echo htmlspecialchars($rol['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($rol['descripcion']); ?></td>
                                    <td>
                                        <?php if($rol['habilitado']): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <!-- Botón de Permisos -->
                                        <a href="/admin/roles/permisos?id=<?php echo $rol['rol_id']; ?>" class="btn btn-warning btn-sm me-1" title="Configurar Permisos">
                                            <i class="fas fa-key text-dark"></i>
                                        </a>

                                        <!-- Botón Editar (abre modal) -->
                                        <button type="button" class="btn btn-info btn-sm text-white me-1" title="Editar"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalRol"
                                            data-action="edit"
                                            data-id="<?php echo $rol['rol_id']; ?>"
                                            data-codigo="<?php echo htmlspecialchars($rol['codigo']); ?>"
                                            data-nombre="<?php echo htmlspecialchars($rol['nombre']); ?>"
                                            data-descripcion="<?php echo htmlspecialchars($rol['descripcion']); ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <!-- Cambiar Estado -->
                                        <form action="/admin/roles/toggle" method="POST" class="d-inline form-confirm" data-title="¿Cambiar estado del rol?">
                                            <input type="hidden" name="id" value="<?php echo $rol['rol_id']; ?>">
                                            <input type="hidden" name="estado" value="<?php echo $rol['habilitado'] ? 0 : 1; ?>">
                                            <button type="submit" class="btn btn-sm <?php echo $rol['habilitado'] ? 'btn-danger' : 'btn-success'; ?>" title="<?php echo $rol['habilitado'] ? 'Desactivar' : 'Activar'; ?>">
                                                <i class="fas <?php echo $rol['habilitado'] ? 'fa-ban' : 'fa-check'; ?>"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No hay roles registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rol (Crear / Editar) -->
<div class="modal fade" id="modalRol" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary" id="modalRolLabel">Nuevo Rol</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formRol" action="/admin/roles/guardar" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="rol_id" id="rol_id" value="">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Código del Rol <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase" name="codigo" id="codigo" required placeholder="Ej. ADMIN_FINANZAS">
                        <small class="text-muted">Código único en mayúsculas sin espacios.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Rol <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nombre" id="nombre" required placeholder="Ej. Administrador de Finanzas">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea class="form-control" name="descripcion" id="descripcion" rows="3" placeholder="Breve descripción..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('modalRol');
    if (modalEl) {
        modalEl.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            const action = btn.getAttribute('data-action');

            const form = document.getElementById('formRol');
            const title = document.getElementById('modalRolLabel');

            if (action === 'edit') {
                form.action = '/admin/roles/actualizar';
                title.textContent = 'Editar Rol';
                document.getElementById('rol_id').value = btn.getAttribute('data-id');
                document.getElementById('codigo').value = btn.getAttribute('data-codigo');
                document.getElementById('nombre').value = btn.getAttribute('data-nombre');
                document.getElementById('descripcion').value = btn.getAttribute('data-descripcion');
            } else {
                form.action = '/admin/roles/guardar';
                title.textContent = 'Nuevo Rol';
                document.getElementById('rol_id').value = '';
                document.getElementById('codigo').value = '';
                document.getElementById('nombre').value = '';
                document.getElementById('descripcion').value = '';
            }
        });
    }
});
</script>
