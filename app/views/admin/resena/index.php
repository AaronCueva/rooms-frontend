<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1"><?php echo htmlspecialchars($titulo ?? 'Gestión de Reseñas'); ?></h1>
            <p class="text-muted small mb-0">Modera las opiniones, calificaciones y feedback de la comunidad estudiantil</p>
        </div>
        <span class="badge bg-primary fs-6 px-3 py-2 shadow-sm">
            <i class="fas fa-star me-1"></i> <?php echo $total ?? count($resenas ?? []); ?> reseñas
        </span>
    </div>

    <div class="card shadow mb-4 border-0 rounded-3">
        <div class="card-body">
            <form method="GET" action="/admin/resenas" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold mb-1 text-secondary">Buscar</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="busqueda" class="form-control" placeholder="Alojamiento, comentario o estudiante..." value="<?php echo htmlspecialchars($filtros['busqueda'] ?? ''); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1 text-secondary">Calificación</label>
                    <select name="calificacion" class="form-select form-select-sm">
                        <option value="">Todas las estrellas</option>
                        <option value="5" <?php echo (isset($filtros['calificacion']) && $filtros['calificacion'] == '5') ? 'selected' : ''; ?>>⭐⭐⭐⭐⭐ (5 Estrellas)</option>
                        <option value="4" <?php echo (isset($filtros['calificacion']) && $filtros['calificacion'] == '4') ? 'selected' : ''; ?>>⭐⭐⭐⭐☆ (4 Estrellas)</option>
                        <option value="3" <?php echo (isset($filtros['calificacion']) && $filtros['calificacion'] == '3') ? 'selected' : ''; ?>>⭐⭐⭐☆☆ (3 Estrellas)</option>
                        <option value="2" <?php echo (isset($filtros['calificacion']) && $filtros['calificacion'] == '2') ? 'selected' : ''; ?>>⭐⭐☆☆☆ (2 Estrellas)</option>
                        <option value="1" <?php echo (isset($filtros['calificacion']) && $filtros['calificacion'] == '1') ? 'selected' : ''; ?>>⭐☆☆☆☆ (1 Estrella)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold mb-1 text-secondary">Estado</label>
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <?php if (!empty($estados)): ?>
                            <?php foreach ($estados as $est): ?>
                                <option value="<?php echo $est['codigo']; ?>" <?php echo (isset($filtros['estado']) && $filtros['estado'] == $est['codigo']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($est['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="ESRS001" <?php echo (isset($filtros['estado']) && $filtros['estado'] === 'ESRS001') ? 'selected' : ''; ?>>Activo</option>
                            <option value="ESRS002" <?php echo (isset($filtros['estado']) && $filtros['estado'] === 'ESRS002') ? 'selected' : ''; ?>>Reportado</option>
                            <option value="ESRS003" <?php echo (isset($filtros['estado']) && $filtros['estado'] === 'ESRS003') ? 'selected' : ''; ?>>Oculto</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    <a href="/admin/resenas" class="btn btn-sm btn-outline-secondary flex-fill">
                        <i class="fas fa-times me-1"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4 border-0 rounded-3 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table foro-table table-hover align-middle mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light text-secondary small text-uppercase fw-bold">
                        <tr>
                            <th class="py-3 ps-4">Estudiante</th>
                            <th class="py-3">Alojamiento</th>
                            <th class="py-3 text-center">Calificación</th>
                            <th class="py-3">Comentario</th>
                            <th class="py-3">Fecha</th>
                            <th class="py-3 text-center">Estado</th>
                            <th class="py-3 text-center pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($resenas) && count($resenas) > 0): ?>
                            <?php foreach ($resenas as $item): ?>
                                <tr class="<?php echo (!isset($item['habilitado']) || $item['habilitado'] != 1) ? 'row-oculto bg-light text-muted' : ''; ?>">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-circle bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; flex-shrink: 0;">
                                                <?php 
                                                    $iniciales = strtoupper(substr($item['nombres'] ?? 'E', 0, 1) . substr($item['apellido_paterno'] ?? 'S', 0, 1));
                                                    echo $iniciales;
                                                ?>
                                            </div>
                                            <div>
                                                <span class="d-block fw-semibold text-dark small"><?php echo htmlspecialchars(($item['nombres'] ?? 'Estudiante') . ' ' . ($item['apellido_paterno'] ?? '')); ?></span>
                                                <small class="text-muted" style="font-size: 0.75rem;"><?php echo htmlspecialchars($item['correo'] ?? 'Sin correo'); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong class="d-block text-dark small text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($item['alojamiento_titulo'] ?? 'Alojamiento'); ?>">
                                                <i class="fas fa-home text-secondary me-1"></i> <?php echo htmlspecialchars($item['alojamiento_titulo'] ?? 'Alojamiento no disponible'); ?>
                                            </strong>
                                            <?php if (!empty($item['contrato_id'])): ?>
                                                <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 0.65rem;">
                                                    <i class="fas fa-file-contract me-1"></i> Alquiler verificado
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">Reseña directa</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="text-warning small text-nowrap" title="<?php echo $item['calificacion']; ?> estrellas">
                                            <?php 
                                                $calif = (int)($item['calificacion'] ?? 0);
                                                for ($i = 1; $i <= 5; $i++) {
                                                    if ($i <= $calif) {
                                                        echo '<i class="fas fa-star"></i> ';
                                                    } else {
                                                        echo '<i class="far fa-star text-muted opacity-50"></i> ';
                                                    }
                                                }
                                            ?>
                                        </div>
                                        <span class="fw-bold small text-dark">(<?php echo $calif; ?>/5)</span>
                                    </td>
                                    <td>
                                        <div style="max-width: 260px;">
                                            <p class="mb-1 small text-truncate" title="<?php echo htmlspecialchars($item['comentario'] ?? ''); ?>">
                                                "<?php echo htmlspecialchars($item['comentario'] ?? 'Sin comentario.'); ?>"
                                            </p>
                                            <?php if (!empty($item['respuesta_propietario'])): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.68rem;">
                                                    <i class="fas fa-reply me-1"></i> Propietario respondió
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="d-block small fw-semibold text-dark"><?php echo htmlspecialchars(date('d/m/Y', strtotime($item['fecha_creado'] ?? 'now'))); ?></span>
                                        <small class="text-muted" style="font-size: 0.75rem;"><?php echo htmlspecialchars(date('H:i', strtotime($item['fecha_creado'] ?? 'now'))); ?></small>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                            $cod = $item['estado_codigo'] ?? '';
                                            $hab = $item['habilitado'] ?? true;
                                            if (!$hab || $cod === 'ESRS003' || $cod === 'ESRA004'):
                                        ?>
                                            <span class="badge bg-dark text-white px-2 py-1">
                                                <i class="fas fa-eye-slash me-1"></i> Oculto
                                            </span>
                                        <?php elseif ($cod === 'ESRS002' || $cod === 'ESRA003'): ?>
                                            <span class="badge bg-danger text-white px-2 py-1 animate__animated animate__pulse animate__infinite">
                                                <i class="fas fa-exclamation-triangle me-1"></i> Reportado
                                            </span>
                                        <?php elseif ($cod === 'ESRS004'): ?>
                                            <span class="badge bg-warning text-dark px-2 py-1">
                                                <i class="fas fa-clock me-1"></i> Pendiente
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                                <i class="fas fa-check-circle me-1"></i> Activo
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="acciones-group d-flex justify-content-center gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-accion btn-accion-view" title="Ver detalle y moderar" onclick="abrirModalVerResena('<?php echo $item['resena_id']; ?>')">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                            <?php if ($cod === 'ESRS002' || $cod === 'ESRA003'): ?>
                                                <!-- Si está reportado, botón rápido para aprobar -->
                                                <form action="/admin/resenas/cambiar-estado" method="POST" class="d-inline form-confirm" data-title="¿Aprobar reseña reportada?" data-text="La reseña volverá al estado Activo y será visible en la plataforma." data-icon="question" data-confirm-text="Sí, aprobar">
                                                    <input type="hidden" name="id" value="<?php echo $item['resena_id']; ?>">
                                                    <input type="hidden" name="estado_codigo" value="ESRS001">
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Aprobar y retirar reporte">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <form action="/admin/resenas/toggle-estado" method="POST" class="d-inline form-confirm" data-title="<?php echo ($hab && $cod !== 'ESRS003') ? '¿Ocultar reseña?' : '¿Restaurar reseña?'; ?>" data-text="<?php echo ($hab && $cod !== 'ESRS003') ? 'La reseña ya no será visible para los estudiantes en el alojamiento.' : 'La reseña volverá a estar visible públicamente en el alojamiento.'; ?>" data-icon="question" data-confirm-text="<?php echo ($hab && $cod !== 'ESRS003') ? 'Sí, ocultar' : 'Sí, restaurar'; ?>">
                                                <input type="hidden" name="id" value="<?php echo $item['resena_id']; ?>">
                                                <?php if ($hab && $cod !== 'ESRS003'): ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger btn-accion btn-accion-hide" title="Ocultar reseña">
                                                        <i class="fas fa-eye-slash"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-success btn-accion btn-accion-restore" title="Restaurar reseña">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-star fa-3x mb-3 d-block opacity-25"></i>
                                    No se encontraron reseñas con los filtros seleccionados.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if (($total_paginas ?? 1) > 1): ?>
    <nav aria-label="Paginación de reseñas">
        <ul class="pagination pagination-sm justify-content-center">
            <li class="page-item <?php echo ($pagina <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo http_build_query(array_merge($filtros, ['pagina' => $pagina - 1])); ?>">Anterior</a>
            </li>
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                    <a class="page-link" href="?<?php echo http_build_query(array_merge($filtros, ['pagina' => $i])); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php echo ($pagina >= $total_paginas) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo http_build_query(array_merge($filtros, ['pagina' => $pagina + 1])); ?>">Siguiente</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<script>
function abrirModalVerResena(id) {
    const url = '/admin/resenas/ver-modal?id=' + id;
    fetch(url)
        .then(r => {
            if (!r.ok) throw new Error('Error al cargar la reseña');
            return r.text();
        })
        .then(html => {
            const modal = document.createElement('div');
            modal.innerHTML = `
                <div class="modal fade" tabindex="-1">
                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-width: 1000px;">
                        <div class="modal-content border-0 shadow-lg">
                            <div class="modal-header bg-dark text-white py-2 px-3">
                                <h6 class="modal-title d-flex align-items-center gap-2 mb-0">
                                    <i class="fas fa-star text-warning"></i> Moderación de Reseña
                                </h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            ${html}
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            const m = new bootstrap.Modal(modal.querySelector('.modal'));
            m.show();
            modal.querySelector('.modal').addEventListener('hidden.bs.modal', () => modal.remove());
        })
        .catch(err => {
            console.error(err);
            alert('Hubo un error al intentar abrir los detalles de la reseña.');
        });
}
</script>
