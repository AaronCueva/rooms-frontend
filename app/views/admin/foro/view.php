<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">
            <a href="/admin/foros" class="text-decoration-none text-secondary me-2">
                <i class="fas fa-arrow-left"></i>
            </a>
            <?php echo htmlspecialchars($titulo ?? 'Detalle del Foro'); ?>
        </h1>
        
        <form action="/admin/foros/toggle-estado" method="POST" class="d-inline">
            <input type="hidden" name="id" value="<?php echo $foro['foro_id']; ?>">
            <input type="hidden" name="estado" value="<?php echo ($foro['habilitado'] == 1) ? 0 : 1; ?>">
            <?php if ($foro['habilitado'] == 1): ?>
                <button type="submit" class="btn btn-sm btn-warning shadow-sm" onclick="return confirm('¿Estás seguro de ocultar este foro?');">
                    <i class="fas fa-eye-slash fa-sm text-white-50"></i> Ocultar Foro
                </button>
            <?php else: ?>
                <button type="submit" class="btn btn-sm btn-success shadow-sm">
                    <i class="fas fa-check fa-sm text-white-50"></i> Activar Foro
                </button>
            <?php endif; ?>
        </form>
    </div>

    <div class="row">
        <!-- Detalles del Foro -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información del Hilo</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img class="img-profile rounded-circle mb-2" src="https://ui-avatars.com/api/?name=<?php echo urlencode($foro['nombres'] . ' ' . $foro['apellido_paterno']); ?>&background=random" width="80" height="80">
                        <h5 class="font-weight-bold"><?php echo htmlspecialchars($foro['nombres'] . ' ' . $foro['apellido_paterno']); ?></h5>
                        <p class="text-muted small mb-0"><?php echo htmlspecialchars($foro['correo']); ?></p>
                        <span class="badge bg-primary mt-2"><?php echo htmlspecialchars($foro['universidad_nombre'] ?? 'General'); ?></span>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <span class="text-muted small text-uppercase font-weight-bold">Categoría</span>
                        <p class="mb-0"><?php echo htmlspecialchars($foro['categoria_nombre'] ?? 'Sin categoría'); ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <span class="text-muted small text-uppercase font-weight-bold">Fecha de Publicación</span>
                        <p class="mb-0"><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($foro['fecha_creacion']))); ?></p>
                    </div>

                    <div class="mb-3">
                        <span class="text-muted small text-uppercase font-weight-bold">Estado</span>
                        <p class="mb-0">
                            <?php if ($foro['habilitado'] == 1): ?>
                                <span class="badge bg-success">Visible (Activo)</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Oculto</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <div class="mb-0 text-center">
                        <div class="p-3 rounded d-inline-block" style="background: rgba(255, 255, 255, 0.05);">
                            <i class="fas fa-heart text-danger"></i> <strong class="ms-1"><?php echo htmlspecialchars($foro['total_reacciones'] ?? 0); ?> Reacciones</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido y Comentarios -->
        <div class="col-xl-8 col-lg-7">
            <!-- Contenido del Post -->
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-body">
                    <h4 class="font-weight-bold"><?php echo htmlspecialchars($foro['titulo']); ?></h4>
                    <p class="mt-3" style="white-space: pre-wrap;"><?php echo htmlspecialchars($foro['descripcion']); ?></p>
                </div>
            </div>

            <!-- Listado de Comentarios -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Moderación de Comentarios (<?php echo count($comentarios ?? []); ?>)</h6>
                </div>
                <div class="card-body">
                    <?php if (isset($comentarios) && count($comentarios) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($comentarios as $comentario): ?>
                                <?php $comentarioHabilitado = ($comentario['habilitado'] ?? false) == 1 || $comentario['habilitado'] === true; ?>
                                <div class="list-group-item px-0 py-3 <?php echo $comentarioHabilitado ? '' : 'bg-light opacity-75'; ?>">
                                    <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                        <div>
                                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($comentario['nombres']); ?>&background=random&size=24" class="rounded-circle me-2" alt="avatar">
                                            <strong class="mb-1"><?php echo htmlspecialchars($comentario['nombres'] . ' ' . $comentario['apellido_paterno']); ?></strong>
                                            <?php if (!$comentarioHabilitado): ?>
                                                <span class="badge bg-danger ms-1">Eliminado</span>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-muted"><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($comentario['fecha_envio']))); ?></small>
                                    </div>
                                    <p class="mb-2 ms-4 <?php echo $comentarioHabilitado ? '' : 'text-decoration-line-through text-muted'; ?>" style="padding-left: 10px;"><?php echo htmlspecialchars($comentario['mensaje']); ?></p>
                                    
                                    <div class="text-end">
                                        <?php if ($comentarioHabilitado): ?>
                                            <form action="/admin/foros/comentario/eliminar" method="POST" class="d-inline">
                                                <input type="hidden" name="comentario_id" value="<?php echo $comentario['foro_comentario_id']; ?>">
                                                <input type="hidden" name="foro_id" value="<?php echo $foro['foro_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este comentario? Quedará oculto y podrá restaurarse luego.');">
                                                    <i class="fas fa-trash-alt me-1"></i> Eliminar Comentario
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form action="/admin/foros/comentario/restaurar" method="POST" class="d-inline">
                                                <input type="hidden" name="comentario_id" value="<?php echo $comentario['foro_comentario_id']; ?>">
                                                <input type="hidden" name="foro_id" value="<?php echo $foro['foro_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('¿Restaurar este comentario? Volverá a ser visible.');">
                                                    <i class="fas fa-rotate-left me-1"></i> Restaurar Comentario
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-comments fa-3x mb-3 opacity-50"></i>
                            <p>No hay comentarios en este foro todavía.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
