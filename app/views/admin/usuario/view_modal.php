<?php 
    $avatarUrl = !empty($usuario['url_foto']) ? $usuario['url_foto'] : 'https://ui-avatars.com/api/?name=' . urlencode($usuario['nombres'] . ' ' . ($usuario['apellido_paterno'] ?? '')) . '&background=0d6efd&color=fff&size=128';
?>
<div class="modal-header bg-dark text-white py-3 px-4">
    <h6 class="modal-title d-flex align-items-center gap-2 mb-0 fw-semibold">
        <i class="fas fa-id-badge text-info"></i> Ficha Detallada del Usuario
    </h6>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body p-4 text-start">
    <!-- Encabezado del Perfil -->
    <div class="d-flex flex-column flex-sm-row align-items-center gap-3 p-3 bg-light rounded-3 mb-4 shadow-sm border">
        <img src="<?= $avatarUrl ?>" alt="Avatar" class="rounded-circle shadow object-fit-cover border border-3 border-white" width="80" height="80" onerror="this.src='https://ui-avatars.com/api/?name=User&background=6c757d&color=fff'">
        <div class="text-center text-sm-start flex-grow-1">
            <h5 class="mb-1 fw-bold text-dark d-flex align-items-center justify-content-center justify-content-sm-start gap-2">
                <?= htmlspecialchars($usuario['nombres'] . ' ' . ($usuario['apellido_paterno'] ?? '') . ' ' . ($usuario['apellido_materno'] ?? '')) ?>
                <?php if (!empty($usuario['verificado'])): ?>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary small" style="font-size: 0.7rem;"><i class="fas fa-check-circle me-1"></i>Verificado</span>
                <?php endif; ?>
            </h5>
            <p class="text-muted small mb-2"><i class="fas fa-envelope me-1 text-secondary"></i><?= htmlspecialchars($usuario['correo']) ?></p>
            <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-sm-start">
                <span class="badge bg-dark px-3 py-1"><?= htmlspecialchars(!empty($usuario['rol_nombre']) ? $usuario['rol_nombre'] : 'Usuario NIDO') ?></span>
                <?php if (!empty($usuario['habilitado'])): ?>
                    <span class="badge bg-success px-3 py-1"><i class="fas fa-circle small me-1"></i>Activo</span>
                <?php else: ?>
                    <span class="badge bg-danger px-3 py-1"><i class="fas fa-ban small me-1"></i>Baneado / Inhabilitado</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Detalles en 2 Columnas -->
    <div class="row g-4">
        <div class="col-12 col-md-6">
            <h6 class="text-uppercase small fw-bold text-secondary border-bottom pb-2 mb-3">
                <i class="fas fa-user text-primary me-2"></i>Datos Personales
            </h6>
            <ul class="list-group list-group-flush small">
                <li class="list-group-item d-flex justify-content-between px-0 py-2">
                    <span class="text-muted">Documento:</span>
                    <strong class="text-dark"><?= htmlspecialchars(($usuario['tipo_documento_nombre'] ?: ($usuario['tipo_documento_codigo'] ?? 'DOC')) . ' - ' . ($usuario['numero_documento'] ?? '--')) ?></strong>
                </li>
                <li class="list-group-item d-flex justify-content-between px-0 py-2">
                    <span class="text-muted">Celular / Móvil:</span>
                    <strong class="text-dark"><?= htmlspecialchars($usuario['celular'] ?: '--') ?></strong>
                </li>
                <li class="list-group-item d-flex justify-content-between px-0 py-2">
                    <span class="text-muted">Teléfono Fijo:</span>
                    <strong class="text-dark"><?= htmlspecialchars($usuario['telefono'] ?: '--') ?></strong>
                </li>
                <li class="list-group-item d-flex justify-content-between px-0 py-2">
                    <span class="text-muted">Género:</span>
                    <strong class="text-dark"><?= htmlspecialchars($usuario['genero_nombre'] ?: '--') ?></strong>
                </li>
            </ul>
        </div>

        <div class="col-12 col-md-6">
            <h6 class="text-uppercase small fw-bold text-secondary border-bottom pb-2 mb-3">
                <i class="fas fa-graduation-cap text-success me-2"></i>Información del Sistema
            </h6>
            <ul class="list-group list-group-flush small">
                <li class="list-group-item d-flex justify-content-between px-0 py-2">
                    <span class="text-muted">Universidad:</span>
                    <strong class="text-dark text-end"><?= htmlspecialchars($usuario['universidad_nombre'] ?: '--') ?></strong>
                </li>
                <li class="list-group-item d-flex justify-content-between px-0 py-2">
                    <span class="text-muted">Distrito / Residencia:</span>
                    <strong class="text-dark"><?= htmlspecialchars($usuario['distrito_nombre'] ?: '--') ?></strong>
                </li>
                <li class="list-group-item d-flex justify-content-between px-0 py-2">
                    <span class="text-muted">Puntos Acumulados:</span>
                    <strong class="text-warning fs-6"><i class="fas fa-coins me-1"></i><?= number_format($usuario['puntos_acumulados'] ?? 0) ?></strong>
                </li>
                <li class="list-group-item d-flex justify-content-between px-0 py-2">
                    <span class="text-muted">Fecha de Registro:</span>
                    <strong class="text-dark"><?= date('d/m/Y H:i', strtotime($usuario['creado'] ?? 'now')) ?></strong>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="modal-footer bg-light px-4 py-3 d-flex justify-content-between">
    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cerrar</button>
    <button type="button" class="btn btn-warning btn-sm px-4 fw-semibold" onclick="editarUsuario('<?= $usuario['usuario_id'] ?>')">
        <i class="fas fa-edit me-1"></i> Editar este usuario
    </button>
</div>
