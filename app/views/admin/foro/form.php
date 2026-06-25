<div class="p-3">
    <form action="/admin/foros/actualizar" method="POST">
        <input type="hidden" name="id" value="<?php echo $foro['foro_id']; ?>">

        <div class="mb-3">
            <label class="form-label fw-semibold small">Título</label>
            <input type="text" name="titulo" class="form-control" value="<?php echo htmlspecialchars($foro['titulo'] ?? ''); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold small">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="4" required><?php echo htmlspecialchars($foro['descripcion'] ?? ''); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold small">Categoría</label>
            <select name="categoria_codigo" class="form-select" required>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo $cat['codigo']; ?>" <?php echo ($foro['categoria_codigo'] == $cat['codigo']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold small">Universidad</label>
            <select name="universidad_id" class="form-select">
                <option value="">Sin universidad</option>
                <?php foreach ($universidades as $uni): ?>
                    <option value="<?php echo $uni['universidad_id']; ?>" <?php echo ($foro['universidad_id'] == $uni['universidad_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($uni['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div>
