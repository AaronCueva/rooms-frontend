<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0"><?php echo htmlspecialchars($titulo ?? 'Gestión de Foros'); ?></h1>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Listado de Foros de la Comunidad</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Categoría</th>
                            <th>Fecha</th>
                            <th>Interacciones</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($foros) && count($foros) > 0): ?>
                            <?php foreach ($foros as $foro): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($foro['titulo']); ?></strong>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($foro['nombres'] . ' ' . $foro['apellido_paterno']); ?><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($foro['universidad_nombre'] ?? 'Sin universidad'); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-dark"><?php echo htmlspecialchars($foro['categoria_nombre'] ?? 'General'); ?></span>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($foro['fecha_creacion']))); ?>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <small><i class="fas fa-comment me-1"></i> <?php echo htmlspecialchars($foro['total_comentarios'] ?? 0); ?> comentarios</small>
                                            <small><i class="fas fa-heart me-1 text-danger"></i> <?php echo htmlspecialchars($foro['total_reacciones'] ?? 0); ?> reacciones</small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (isset($foro['habilitado']) && $foro['habilitado'] == 1): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Oculto</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <!-- Botón Ver Detalles -->
                                            <a href="/admin/foros/ver?id=<?php echo $foro['foro_id']; ?>" class="btn btn-sm btn-primary" title="Ver foro y moderar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <!-- Botón Toggle Estado -->
                                            <form action="/admin/foros/toggle-estado" method="POST" class="d-inline">
                                                <input type="hidden" name="id" value="<?php echo $foro['foro_id']; ?>">
                                                <input type="hidden" name="estado" value="<?php echo ($foro['habilitado'] == 1) ? 0 : 1; ?>">
                                                <?php if ($foro['habilitado'] == 1): ?>
                                                    <button type="submit" class="btn btn-sm btn-warning" title="Ocultar foro" onclick="return confirm('¿Estás seguro de ocultar este foro?');">
                                                        <i class="fas fa-eye-slash"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="submit" class="btn btn-sm btn-success" title="Activar foro">
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
                                <td colspan="7" class="text-center py-4 text-muted">
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
