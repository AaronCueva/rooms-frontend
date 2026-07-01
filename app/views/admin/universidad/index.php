<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo htmlspecialchars($titulo ?? 'Gestión de Universidades'); ?></h1>
        <a href="/admin/universidades/crear" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Nueva Universidad
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="/admin/universidades" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold mb-1">Buscar</label>
                    <input type="text" name="busqueda" class="form-control form-control-sm" placeholder="Nombre o código..." value="<?php echo htmlspecialchars($filtros['busqueda'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold mb-1">Estado</label>
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="1" <?php echo (isset($filtros['estado']) && $filtros['estado'] === '1') ? 'selected' : ''; ?>>Activo</option>
                        <option value="0" <?php echo (isset($filtros['estado']) && $filtros['estado'] === '0') ? 'selected' : ''; ?>>Oculto</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">
                        <i class="fas fa-search me-1"></i> Filtrar
                    </button>
                    <a href="/admin/universidades" class="btn btn-sm btn-outline-secondary flex-fill">
                        <i class="fas fa-times me-1"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Listado de Universidades (<?php echo $total ?? 0; ?>)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Ubicación</th>
                            <th>Verificado</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($universidades) && count($universidades) > 0): ?>
                            <?php foreach ($universidades as $uni): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($uni['nombre']); ?></strong></td>
                                    <td><small><?php echo htmlspecialchars(substr($uni['descripcion'], 0, 80)) . (strlen($uni['descripcion']) > 80 ? '...' : ''); ?></small></td>
                                    <td><?php echo htmlspecialchars($uni['distrito_nombre'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if ($uni['verificado']): ?>
                                            <span class="badge bg-success"><i class="fas fa-check-circle"></i> Verificado</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No verificado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($uni['habilitado'] == 1): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Oculto</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-primary" title="Ver Detalles" onclick="abrirModalUniversidad('<?php echo $uni['universidad_id']; ?>')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="/admin/universidades/editar?id=<?php echo $uni['universidad_id']; ?>" class="btn btn-sm btn-info text-white" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="/admin/universidades/toggle-estado" method="POST" class="d-inline form-confirm" 
                                                  data-title="¿Cambiar estado?" 
                                                  data-text="Esta universidad cambiará su visibilidad en la plataforma.">
                                                <input type="hidden" name="id" value="<?php echo $uni['universidad_id']; ?>">
                                                <input type="hidden" name="estado" value="<?php echo ($uni['habilitado'] == 1) ? 0 : 1; ?>">
                                                <?php if ($uni['habilitado'] == 1): ?>
                                                    <button type="submit" class="btn btn-sm btn-warning" title="Ocultar">
                                                        <i class="fas fa-eye-slash"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="submit" class="btn btn-sm btn-success" title="Activar">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No hay universidades registradas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <?php if (isset($total_paginas) && $total_paginas > 1): ?>
            <div class="d-flex justify-content-center mt-4">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?php echo ($pagina <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $pagina - 1; ?>&busqueda=<?php echo urlencode($filtros['busqueda'] ?? ''); ?>&estado=<?php echo urlencode($filtros['estado'] ?? ''); ?>">Anterior</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <li class="page-item <?php echo ($pagina == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="?pagina=<?php echo $i; ?>&busqueda=<?php echo urlencode($filtros['busqueda'] ?? ''); ?>&estado=<?php echo urlencode($filtros['estado'] ?? ''); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo ($pagina >= $total_paginas) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $pagina + 1; ?>&busqueda=<?php echo urlencode($filtros['busqueda'] ?? ''); ?>&estado=<?php echo urlencode($filtros['estado'] ?? ''); ?>">Siguiente</a>
                    </li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Contenedor dinámico para el modal -->
<div id="modalContainer"></div>

<script>
async function abrirModalUniversidad(id) {
    try {
        const response = await fetch('/admin/universidades/ver-modal?id=' + id);
        const html = await response.text();
        console.log("Response HTML:", html);
        document.getElementById('modalContainer').innerHTML = html;
        const modalElement = document.getElementById('modalUniversidadAjax');
        if (!modalElement) {
            Swal.fire('Error PHP', `<div style="text-align:left; max-height: 200px; overflow: auto;">${html}</div>`, 'error');
            return;
        }
        var myModal = new bootstrap.Modal(modalElement);
        myModal.show();
    } catch(error) {
        console.error(error);
        Swal.fire('Error JS', error.message, 'error');
    }
}
</script>
