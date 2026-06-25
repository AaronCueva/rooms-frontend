<div class="p-3">
    <form action="/admin/foros/comentario/actualizar" method="POST">
        <input type="hidden" name="comentario_id" value="<?php echo $comentario['foro_comentario_id']; ?>">
        <input type="hidden" name="foro_id" value="<?php echo $comentario['foro_id']; ?>">

        <div class="mb-3">
            <label class="form-label fw-semibold small">Mensaje</label>
            <textarea name="mensaje" class="form-control" rows="6" required><?php echo htmlspecialchars($comentario['mensaje'] ?? ''); ?></textarea>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div>
