<div class="modal-body p-4">
    <div class="mb-4 pb-3 border-bottom">
        <h4 class="fw-bold text-dark mb-3" style="line-height: 1.4;">
            <?php echo htmlspecialchars($item['titulo'] ?? 'Sin título'); ?>
        </h4>
        <div class="d-flex flex-wrap align-items-center gap-3 text-muted small">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.75rem;">
                    <?php echo strtoupper(substr(trim($item['nombres'] ?? 'A'), 0, 1)); ?>
                </div>
                <span class="fw-semibold text-dark">
                    <?php echo htmlspecialchars(trim(($item['nombres'] ?? 'Admin') . ' ' . ($item['apellido_paterno'] ?? ''))); ?>
                </span>
            </div>
            <div>
                <i class="far fa-calendar-alt text-primary me-1"></i>
                <?php if (!empty($item['fecha_publicacion'])): ?>
                    Publicado el <?php echo date('d de F, Y - H:i', strtotime($item['fecha_publicacion'])); ?> hrs
                <?php else: ?>
                    <span class="fst-italic">Aún no publicado (Borrador)</span>
                <?php endif; ?>
            </div>
            <div>
                <?php 
                $cod = $item['estado_codigo'] ?? '';
                $bClass = 'bg-secondary';
                if ($cod === 'ESBL002') $bClass = 'bg-success';
                elseif ($cod === 'ESBL001') $bClass = 'bg-warning text-dark';
                ?>
                <span class="badge <?php echo $bClass; ?> px-2 py-1">
                    <?php echo htmlspecialchars($item['estado_nombre'] ?? $cod); ?>
                </span>
            </div>
        </div>
    </div>

    <div class="blog-preview-content px-1" style="font-size: 1.05rem; line-height: 1.8; color: #333; white-space: pre-wrap; font-family: system-ui, -apple-system, sans-serif;">
<?php echo htmlspecialchars($item['contenido'] ?? ''); ?>
    </div>
</div>
<div class="modal-footer bg-light px-4 py-3 d-flex justify-content-between align-items-center">
    <div class="text-muted small">
        ID de Referencia: <code><?php echo htmlspecialchars($item['blog_id'] ?? ''); ?></code>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">
            Cerrar
        </button>
        <button type="button" class="btn btn-primary px-3 shadow-sm" onclick="abrirModalEditarBlog('<?php echo htmlspecialchars($item['blog_id']); ?>')">
            <i class="fas fa-edit me-1"></i> Editar Artículo
        </button>
    </div>
</div>
