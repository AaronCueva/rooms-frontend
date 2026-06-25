<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1"><?php echo htmlspecialchars($titulo ?? 'Gestión de Foros'); ?></h1>
            <p class="text-muted small mb-0">Modera los foros y comentarios de la comunidad</p>
        </div>
        <span class="badge bg-primary fs-6 px-3 py-2">
            <i class="fas fa-comments me-1"></i> <?php echo count($foros ?? []); ?> foros
        </span>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table foro-table mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Categoría</th>
                            <th>Fecha</th>
                            <th class="text-center">Interacciones</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($foros) && count($foros) > 0): ?>
                            <?php foreach ($foros as $foro): ?>
                                <tr class="<?php echo (!isset($foro['habilitado']) || $foro['habilitado'] != 1) ? 'row-oculto' : ''; ?>">
                                    <td class="td-titulo">
                                        <div class="d-flex align-items-center gap-2">
                                            <div>
                                                <strong class="d-block text-truncate" style="max-width: 280px;"><?php echo htmlspecialchars($foro['titulo']); ?></strong>
                                          
                                            </div>
                                        </div>
                                    </td>
                                    <td class="td-autor">
                                        <div class="d-flex align-items-center gap-2">
                                       
                                            <div>
                                                <span class="d-block fw-semibold small"><?php echo htmlspecialchars($foro['nombres'] . ' ' . $foro['apellido_paterno']); ?></span>
                                                <small class="text-muted"><?php echo htmlspecialchars($foro['universidad_nombre'] ?? 'Sin universidad'); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="td-categoria">
                                        <span class="cat-badge"><?php echo htmlspecialchars($foro['categoria_nombre'] ?? 'General'); ?></span>
                                    </td>
                                    <td class="td-fecha">
                                        <span class="d-block small fw-semibold"><?php echo htmlspecialchars(date('d/m/Y', strtotime($foro['fecha_creacion']))); ?></span>
                                        <small class="text-muted"><?php echo htmlspecialchars(date('H:i', strtotime($foro['fecha_creacion']))); ?></small>
                                    </td>
                                    <td class="td-interacciones text-center">
                                        <div class="interacciones-group">
                                            <span class="interaccion-item">
                                                <i class="fas fa-comment"></i>
                                                <span><?php echo htmlspecialchars($foro['total_comentarios'] ?? 0); ?></span>
                                            </span>
                                            <span class="interaccion-item">
                                                <i class="fas fa-heart"></i>
                                                <span><?php echo htmlspecialchars($foro['total_reacciones'] ?? 0); ?></span>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="td-estado text-center">
                                        <?php if (isset($foro['habilitado']) && $foro['habilitado'] == 1): ?>
                                            <span class="status-badge status-activo">
                                                <span class="status-dot"></span> Activo
                                            </span>
                                        <?php else: ?>
                                            <span class="status-badge status-oculto">
                                                <span class="status-dot"></span> Oculto
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="td-acciones text-center">
                                        <div class="acciones-group">
                                            <a href="/admin/foros/ver?id=<?php echo $foro['foro_id']; ?>" class="btn-accion btn-accion-view" title="Ver foro y moderar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <form action="/admin/foros/toggle-estado" method="POST" class="d-inline">
                                                <input type="hidden" name="id" value="<?php echo $foro['foro_id']; ?>">
                                                <input type="hidden" name="estado" value="<?php echo ($foro['habilitado'] == 1) ? 0 : 1; ?>">
                                                <?php if ($foro['habilitado'] == 1): ?>
                                                    <button type="submit" class="btn-accion btn-accion-hide" title="Ocultar foro" onclick="return confirm('¿Estás seguro de ocultar este foro?');">
                                                        <i class="fas fa-power-off"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="submit" class="btn-accion btn-accion-restore" title="Activar foro">
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
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block opacity-25"></i>
                                    No hay foros registrados en el sistema.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

