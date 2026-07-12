<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">
            <a href="/admin/foros" class="text-decoration-none text-secondary me-2">
                <i class="fas fa-arrow-left"></i>
            </a>
            <?php echo htmlspecialchars($titulo ?? 'Detalle del Foro'); ?>
        </h1>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" onclick="abrirModalEditarForo('<?php echo $foro['foro_id']; ?>')">
                <i class="fas fa-edit fa-sm"></i> Editar Foro
            </button>
            <form action="/admin/foros/toggle-estado" method="POST" class="d-inline form-confirm" data-title="<?php echo ($foro['habilitado'] == 1) ? '¿Ocultar foro?' : '¿Activar foro?'; ?>" data-text="<?php echo ($foro['habilitado'] == 1) ? 'El foro dejará de ser visible para los usuarios.' : 'El foro volverá a ser visible para los usuarios.'; ?>" data-icon="question" data-confirm-text="<?php echo ($foro['habilitado'] == 1) ? 'Sí, ocultar' : 'Sí, activar'; ?>">
                <input type="hidden" name="id" value="<?php echo $foro['foro_id']; ?>">
                <input type="hidden" name="estado" value="<?php echo ($foro['habilitado'] == 1) ? 0 : 1; ?>">
                <?php if ($foro['habilitado'] == 1): ?>
                    <button type="submit" class="btn btn-sm btn-warning shadow-sm">
                        <i class="fas fa-power-off fa-sm text-white-50"></i> Ocultar Foro
                    </button>
                <?php else: ?>
                    <button type="submit" class="btn btn-sm btn-success shadow-sm">
                        <i class="fas fa-check fa-sm text-white-50"></i> Activar Foro
                    </button>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="row">
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

        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-body">
                    <h4 class="font-weight-bold"><?php echo htmlspecialchars($foro['titulo']); ?></h4>
                    <p class="mt-3" style="white-space: pre-wrap;"><?php echo htmlspecialchars($foro['descripcion']); ?></p>

                    <?php if (isset($multimedia) && count($multimedia) > 0): ?>
                        <hr>
                        <div class="row g-2 mt-3">
                            <?php foreach ($multimedia as $item): ?>
                                <div class="col-md-4 col-6">
                                    <a href="<?php echo htmlspecialchars($item['url']); ?>" target="_blank">
                                        <img src="<?php echo htmlspecialchars($item['url']); ?>" class="img-fluid rounded" alt="Multimedia" style="max-height: 200px; object-fit: cover; width: 100%;">
                                    </a>
                                    <small class="text-muted d-block text-center mt-1"><?php echo htmlspecialchars($item['tipo_multimedia'] ?? ''); ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Moderación de Comentarios (<?php echo count($comentarios_padres ?? []); ?>)</h6>
                </div>
                <div class="card-body">
                    <?php if (isset($comentarios_padres) && count($comentarios_padres) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($comentarios_padres as $comentario): ?>
                                <?php echo renderComentario($comentario, $comentarios_hijos, $foro); ?>
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

<script>
function abrirModalEditarForo(id) {
    const url = '/admin/foros/editar-modal?id=' + id;
    fetch(url)
        .then(r => r.text())
        .then(html => {
            const modal = document.createElement('div');
            modal.innerHTML = `
                <div class="modal fade" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Editar Foro</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
        });
}

function abrirModalEditarComentario(id) {
    const url = '/admin/foros/comentario/editar-modal?id=' + id;
    fetch(url)
        .then(r => r.text())
        .then(html => {
            const modal = document.createElement('div');
            modal.innerHTML = `
                <div class="modal fade" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Editar Comentario</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
        });
}
</script>

<?php
function renderComentario($comentario, $comentarios_hijos, $foro, $extraClass = '') {
    $comentarioHabilitado = ($comentario['habilitado'] ?? false) == 1 || $comentario['habilitado'] === true;
    $tieneHijos = isset($comentarios_hijos[$comentario['foro_comentario_id']]);
    $esRespuesta = !empty($extraClass);
    ob_start();
?>
<div class="list-group-item px-0 py-3 <?php echo $comentarioHabilitado ? '' : 'bg-light opacity-75'; ?> <?php echo $extraClass; ?>">
    <div class="d-flex w-100 justify-content-between align-items-center mb-1">
        <div>
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($comentario['nombres']); ?>&background=random&size=24" class="rounded-circle me-2" alt="avatar">
            <strong class="mb-1"><?php echo htmlspecialchars($comentario['nombres'] . ' ' . $comentario['apellido_paterno']); ?></strong>
            <?php if ($esRespuesta): ?>
                <span class="badge bg-info ms-1"><i class="fas fa-reply me-1"></i>Respuesta</span>
            <?php endif; ?>
            <?php if (!$comentarioHabilitado): ?>
                <span class="badge bg-secondary ms-1">Oculto</span>
            <?php endif; ?>
            <?php if (isset($comentario['comentarista_habilitado']) && !$comentario['comentarista_habilitado']): ?>
                <span class="badge bg-dark ms-1"><i class="fas fa-ban me-1"></i>Baneado</span>
            <?php endif; ?>
        </div>
        <div class="d-flex align-items-center gap-2">
            <?php if ($comentario['comentarista_id'] != $_SESSION['usuario_id']): ?>
                <form action="/admin/foros/ban-usuario" method="POST" class="d-inline m-0 form-confirm" data-title="<?php echo ($comentario['comentarista_habilitado'] ?? true) ? '¿Banear usuario?' : '¿Desbanear usuario?'; ?>" data-text="<?php echo ($comentario['comentarista_habilitado'] ?? true) ? 'El usuario no podrá iniciar sesión ni comentar.' : 'El usuario volverá a poder iniciar sesión y comentar.'; ?>" data-icon="warning" data-confirm-text="<?php echo ($comentario['comentarista_habilitado'] ?? true) ? 'Sí, banear' : 'Sí, desbanear'; ?>">
                    <input type="hidden" name="usuario_id" value="<?php echo $comentario['comentarista_id']; ?>">
                    <input type="hidden" name="foro_id" value="<?php echo $foro['foro_id']; ?>">
                    <input type="hidden" name="accion" value="<?php echo ($comentario['comentarista_habilitado'] ?? true) ? 'banear' : 'desbanear'; ?>">
                    <button type="submit" class="btn btn-sm <?php echo ($comentario['comentarista_habilitado'] ?? true) ? 'btn-outline-dark' : 'btn-outline-success'; ?>" title="<?php echo ($comentario['comentarista_habilitado'] ?? true) ? 'Banear usuario' : 'Desbanear usuario'; ?>">
                        <i class="fas <?php echo ($comentario['comentarista_habilitado'] ?? true) ? 'fa-gavel' : 'fa-check'; ?>"></i>
                    </button>
                </form>
            <?php endif; ?>
            <small class="text-muted"><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($comentario['fecha_envio']))); ?></small>
        </div>
    </div>
    <p class="mb-2 ms-4 <?php echo $comentarioHabilitado ? '' : 'text-decoration-line-through text-muted'; ?>" style="padding-left: 10px;"><?php echo htmlspecialchars($comentario['mensaje']); ?></p>
    
    <div class="text-end">
        <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="abrirModalEditarComentario('<?php echo $comentario['foro_comentario_id']; ?>')" title="Editar comentario">
            <i class="fas fa-edit"></i> Editar
        </button>
        <?php if ($comentarioHabilitado): ?>
            <form action="/admin/foros/comentario/eliminar" method="POST" class="d-inline form-confirm" data-title="¿Ocultar comentario?" data-text="Dejará de ser visible y podrás mostrarlo después." data-icon="question" data-confirm-text="Sí, ocultar">
                <input type="hidden" name="comentario_id" value="<?php echo $comentario['foro_comentario_id']; ?>">
                <input type="hidden" name="foro_id" value="<?php echo $foro['foro_id']; ?>">
                <button type="submit" class="btn btn-sm btn-outline-warning">
                    <i class="fas fa-eye-slash me-1"></i> Ocultar
                </button>
            </form>
        <?php else: ?>
            <form action="/admin/foros/comentario/restaurar" method="POST" class="d-inline form-confirm" data-title="¿Mostrar comentario?" data-text="Volverá a ser visible para todos." data-icon="question" data-confirm-text="Sí, mostrar">
                <input type="hidden" name="comentario_id" value="<?php echo $comentario['foro_comentario_id']; ?>">
                <input type="hidden" name="foro_id" value="<?php echo $foro['foro_id']; ?>">
                <button type="submit" class="btn btn-sm btn-outline-info">
                    <i class="fas fa-eye me-1"></i> Mostrar
                </button>
            </form>
        <?php endif; ?>
    </div>

    <?php if ($tieneHijos && !$esRespuesta): ?>
        <?php foreach ($comentarios_hijos[$comentario['foro_comentario_id']] as $hijo): ?>
            <?php echo renderComentario($hijo, $comentarios_hijos, $foro, 'ms-4 border-start ps-3 mt-2'); ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php
    return ob_get_clean();
}
?>
