<?php 
$isEdit = !empty($item);
$actionUrl = $isEdit ? '/admin/blog/actualizar' : '/admin/blog/guardar';
?>
<form action="<?php echo $actionUrl; ?>" method="POST" class="form-confirm" data-title="<?php echo $isEdit ? '¿Guardar cambios en el artículo?' : '¿Publicar/Guardar este artículo?'; ?>" data-text="El artículo será guardado en la Guía del Universitario bajo el estado seleccionado." data-icon="question" data-confirm-text="<?php echo $isEdit ? 'Sí, actualizar' : 'Sí, guardar'; ?>">
    <div class="modal-body p-4">
        <?php if ($isEdit): ?>
            <input type="hidden" name="blog_id" value="<?php echo htmlspecialchars($item['blog_id']); ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label class="form-label small fw-bold text-secondary text-uppercase">Título del Artículo <span class="text-danger">*</span></label>
            <input type="text" name="titulo" class="form-control form-control-lg fw-semibold" placeholder="Ej: 5 Consejos para elegir tu primer cuarto universitario..." value="<?php echo htmlspecialchars($item['titulo'] ?? ''); ?>" required maxlength="150" style="font-size: 1.05rem;">
            <div class="form-text small text-muted">Escribe un título llamativo y descriptivo para la comunidad estudiantil.</div>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold text-secondary text-uppercase">Estado de Publicación <span class="text-danger">*</span></label>
            <select name="estado_codigo" class="form-select fw-medium" required>
                <?php if (!empty($estados)): ?>
                    <?php foreach ($estados as $est): 
                        $sel = ($isEdit && ($item['estado_codigo'] ?? '') == $est['codigo']) ? 'selected' : '';
                        if (!$isEdit && $est['codigo'] === 'ESBL002') $sel = 'selected'; // Por defecto Publicado en creación
                    ?>
                        <option value="<?php echo $est['codigo']; ?>" <?php echo $sel; ?>>
                            <?php echo htmlspecialchars($est['nombre']); ?> 
                            <?php echo $est['codigo'] === 'ESBL002' ? '(Visible para todos)' : ($est['codigo'] === 'ESBL001' ? '(Privado - Solo tú)' : '(Archivado)'); ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="ESBL002" <?php echo (!$isEdit || ($item['estado_codigo'] ?? '') === 'ESBL002') ? 'selected' : ''; ?>>PUBLICADO (Visible para todos)</option>
                    <option value="ESBL001" <?php echo ($isEdit && ($item['estado_codigo'] ?? '') === 'ESBL001') ? 'selected' : ''; ?>>BORRADOR (Privado - Solo tú)</option>
                    <option value="ESBL003" <?php echo ($isEdit && ($item['estado_codigo'] ?? '') === 'ESBL003') ? 'selected' : ''; ?>>OCULTO (Archivado)</option>
                <?php endif; ?>
            </select>
        </div>

        <div class="mb-2">
            <label class="form-label small fw-bold text-secondary text-uppercase">Contenido / Cuerpo de la Guía <span class="text-danger">*</span></label>
            <textarea name="contenido" class="form-control" rows="10" placeholder="Redacta aquí los consejos, pasos o aviso oficial..." required style="font-size: 0.95rem; line-height: 1.6; font-family: system-ui, -apple-system, sans-serif;"><?php echo htmlspecialchars($item['contenido'] ?? ''); ?></textarea>
            <div class="form-text small text-muted d-flex align-items-center gap-1 mt-2">
                <i class="fas fa-info-circle text-primary"></i> 
                <span><strong>Tip de redacción:</strong> Deja una línea en blanco entre párrafos o usa listas (guiones u números) para que la lectura sea ligera y amena en móviles.</span>
            </div>
        </div>
    </div>
    <div class="modal-footer bg-light px-4 py-3">
        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancelar
        </button>
        <button type="submit" class="btn btn-primary px-4 shadow-sm">
            <i class="fas fa-save me-1"></i> <?php echo $isEdit ? 'Guardar Cambios' : 'Publicar Artículo'; ?>
        </button>
    </div>
</form>
