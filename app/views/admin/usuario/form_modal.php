<?php 
    $esCreacion = !empty($es_creacion);
    $accionUrl = $esCreacion ? '/admin/usuarios/guardar' : '/admin/usuarios/actualizar';
?>
<div class="modal-header <?= $esCreacion ? 'bg-primary' : 'bg-warning text-dark' ?> text-white py-3 px-4">
    <h6 class="modal-title d-flex align-items-center gap-2 mb-0 fw-bold <?= !$esCreacion ? 'text-dark' : '' ?>">
        <i class="fas <?= $esCreacion ? 'fa-user-plus' : 'fa-user-edit' ?>"></i>
        <?= $esCreacion ? 'Registrar Nuevo Usuario en el Sistema' : 'Editar Datos de Usuario: ' . htmlspecialchars($usuario['nombres'] ?? '') ?>
    </h6>
    <button type="button" class="btn-close <?= $esCreacion ? 'btn-close-white' : '' ?>" data-bs-dismiss="modal"></button>
</div>
<form method="POST" action="<?= $accionUrl ?>" id="formUsuarioAdmin">
    <?php if (!$esCreacion): ?>
        <input type="hidden" name="usuario_id" value="<?= $usuario['usuario_id'] ?>">
    <?php endif; ?>

    <div class="modal-body p-4 text-start">
        <?php if ($esCreacion): ?>
            <div class="alert alert-info border-0 shadow-sm small mb-4 d-flex align-items-center gap-2">
                <i class="fas fa-info-circle fs-5"></i>
                <div>
                    El usuario registrado con este formulario podrá iniciar sesión con las credenciales asignadas aquí.
                </div>
            </div>
        <?php endif; ?>

        <!-- Sección 1: Cuenta y Rol -->
        <h6 class="text-uppercase small fw-bold text-secondary border-bottom pb-2 mb-3">
            <i class="fas fa-key text-primary me-2"></i>Credenciales y Rol de Acceso
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <label class="form-label small fw-semibold text-dark">Correo Electrónico <span class="text-danger">*</span></label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                    <input type="email" name="correo" class="form-control" required 
                           value="<?= htmlspecialchars($usuario['correo'] ?? '') ?>" 
                           placeholder="ejemplo@universidad.edu.pe">
                </div>
            </div>

            <?php if ($esCreacion): ?>
                <div class="col-12 col-md-6">
                    <label class="form-label small fw-semibold text-dark">Contraseña Inicial <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control" required minlength="6" placeholder="Mínimo 6 caracteres">
                    </div>
                </div>
            <?php else: ?>
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="verificado" value="1" id="checkVerificado" <?= !empty($usuario['verificado']) ? 'checked' : '' ?>>
                        <label class="form-check-label small fw-semibold text-dark" for="checkVerificado">
                            Cuenta Verificada <i class="fas fa-check-circle text-primary ms-1"></i>
                        </label>
                    </div>
                </div>
            <?php endif; ?>

            <div class="col-12 col-md-6">
                <label class="form-label small fw-semibold text-dark">Rol en la Plataforma <span class="text-danger">*</span></label>
                <select name="rol_id" class="form-select form-select-sm" required>
                    <option value="">-- Seleccionar Rol --</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['rol_id'] ?>" <?= (($usuario['rol_id'] ?? '') == $r['rol_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label small fw-semibold text-dark">Universidad (Académico)</label>
                <select name="universidad_id" class="form-select form-select-sm">
                    <option value="">-- Sin Universidad asociada --</option>
                    <?php foreach ($universidades as $uni): ?>
                        <option value="<?= $uni['universidad_id'] ?>" <?= (($usuario['universidad_id'] ?? '') == $uni['universidad_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($uni['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Sección 2: Datos Personales -->
        <h6 class="text-uppercase small fw-bold text-secondary border-bottom pb-2 mb-3">
            <i class="fas fa-user text-primary me-2"></i>Información Personal y Contacto
        </h6>
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold text-dark">Nombres <span class="text-danger">*</span></label>
                <input type="text" name="nombres" class="form-control form-control-sm" required value="<?= htmlspecialchars($usuario['nombres'] ?? '') ?>">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold text-dark">Apellido Paterno</label>
                <input type="text" name="apellido_paterno" class="form-control form-control-sm" value="<?= htmlspecialchars($usuario['apellido_paterno'] ?? '') ?>">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold text-dark">Apellido Materno</label>
                <input type="text" name="apellido_materno" class="form-control form-control-sm" value="<?= htmlspecialchars($usuario['apellido_materno'] ?? '') ?>">
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold text-dark">Tipo de Documento</label>
                <select name="tipo_documento_codigo" class="form-select form-select-sm">
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($tipos_documento as $td): ?>
                        <option value="<?= $td['codigo'] ?>" <?= (($usuario['tipo_documento_codigo'] ?? '') === $td['codigo']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($td['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold text-dark">Número de Documento</label>
                <input type="text" name="numero_documento" class="form-control form-control-sm" value="<?= htmlspecialchars($usuario['numero_documento'] ?? '') ?>">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold text-dark">Género</label>
                <select name="genero_codigo" class="form-select form-select-sm">
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($generos as $gen): ?>
                        <option value="<?= $gen['codigo'] ?>" <?= (($usuario['genero_codigo'] ?? '') === $gen['codigo']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($gen['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label small fw-semibold text-dark">Celular / Móvil</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-mobile-alt text-muted"></i></span>
                    <input type="text" name="celular" class="form-control" value="<?= htmlspecialchars($usuario['celular'] ?? '') ?>">
                </div>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label small fw-semibold text-dark">Teléfono Fijo / Alternativo</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-phone text-muted"></i></span>
                    <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer bg-light px-4 py-3">
        <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn <?= $esCreacion ? 'btn-primary' : 'btn-warning text-dark' ?> btn-sm px-4 fw-semibold shadow-sm">
            <i class="fas fa-save me-1"></i> <?= $esCreacion ? 'Registrar Usuario' : 'Guardar Cambios' ?>
        </button>
    </div>
</form>
