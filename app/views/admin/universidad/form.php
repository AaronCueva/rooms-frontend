<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">
            <a href="/admin/universidades" class="text-decoration-none text-secondary me-2">
                <i class="fas fa-arrow-left"></i>
            </a>
            <?php echo htmlspecialchars($titulo); ?>
        </h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <?php $esEditar = isset($universidad); ?>
            <form action="<?php echo $esEditar ? '/admin/universidades/actualizar' : '/admin/universidades/guardar'; ?>" method="POST">
                
                <?php if ($esEditar): ?>
                    <input type="hidden" name="universidad_id" value="<?php echo $universidad['universidad_id']; ?>">
                <?php endif; ?>

                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Nombre de la Universidad *</label>
                        <input type="text" class="form-control" name="nombre" required value="<?php echo htmlspecialchars($universidad['nombre'] ?? ''); ?>">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3"><?php echo htmlspecialchars($universidad['descripcion'] ?? ''); ?></textarea>
                    </div>
                </div>

                <h6 class="fw-bold mt-4 mb-3 border-bottom pb-2">Ubicación</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Departamento</label>
                        <select class="form-select" id="departamento" onchange="cargarProvincias(this.value)">
                            <option value="">Seleccione...</option>
                            <?php foreach ($departamentos as $dep): ?>
                                <option value="<?php echo $dep['ubicacion_id']; ?>">
                                    <?php echo htmlspecialchars($dep['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Provincia</label>
                        <select class="form-select" id="provincia" onchange="cargarDistritos(this.value)" disabled>
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Distrito</label>
                        <select class="form-select" name="distrito" id="distrito" disabled>
                            <option value="">Seleccione...</option>
                            <?php if ($esEditar && $universidad['ubicacion_id']): ?>
                                <option value="<?php echo $universidad['ubicacion_id']; ?>" selected>
                                    <?php echo htmlspecialchars($universidad['distrito_nombre']); ?>
                                </option>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted">Si edita, vuelva a seleccionar en cascada.</small>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-semibold">Dirección Exacta</label>
                    <input type="text" class="form-control" name="direccion" value="<?php echo htmlspecialchars($universidad['direccion'] ?? ''); ?>">
                </div>

                <h6 class="fw-bold mt-4 mb-3 border-bottom pb-2">Estado</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="verificado" <?php echo ($esEditar && $universidad['verificado']) ? 'checked' : ''; ?>>
                            <label class="form-check-label text-success fw-bold"><i class="fas fa-check-circle"></i> Sello de Universidad Verificada</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="habilitado" <?php echo (!$esEditar || $universidad['habilitado']) ? 'checked' : ''; ?>>
                            <label class="form-check-label">Habilitada en la plataforma</label>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <a href="/admin/universidades" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> <?php echo $esEditar ? 'Actualizar' : 'Guardar'; ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
async function cargarProvincias(departamento_id) {
    const provinciaSelect = document.getElementById('provincia');
    const distritoSelect = document.getElementById('distrito');
    
    provinciaSelect.innerHTML = '<option value="">Seleccione...</option>';
    distritoSelect.innerHTML = '<option value="">Seleccione...</option>';
    provinciaSelect.disabled = true;
    distritoSelect.disabled = true;

    if (!departamento_id) return;

    try {
        const response = await fetch('/api/ubicaciones?referencia_id=' + departamento_id);
        const data = await response.json();
        if (data.length > 0) {
            data.forEach(provincia => {
                const option = document.createElement('option');
                option.value = provincia.ubicacion_id;
                option.textContent = provincia.nombre;
                provinciaSelect.appendChild(option);
            });
            provinciaSelect.disabled = false;
        }
    } catch (e) {}
}

async function cargarDistritos(provincia_id) {
    const distritoSelect = document.getElementById('distrito');
    distritoSelect.innerHTML = '<option value="">Seleccione...</option>';
    distritoSelect.disabled = true;
    if (!provincia_id) return;

    try {
        const response = await fetch('/api/ubicaciones?referencia_id=' + provincia_id);
        const data = await response.json();
        if (data.length > 0) {
            data.forEach(distrito => {
                const option = document.createElement('option');
                option.value = distrito.ubicacion_id;
                option.textContent = distrito.nombre;
                distritoSelect.appendChild(option);
            });
            distritoSelect.disabled = false;
        }
    } catch (e) {}
}

    // Si estamos editando y tenemos la jerarquía, precargamos
    <?php if (isset($jerarquia) && $jerarquia): ?>
        window.addEventListener('DOMContentLoaded', async () => {
            const depId = '<?php echo $jerarquia['departamento_id']; ?>';
            const provId = '<?php echo $jerarquia['provincia_id']; ?>';
            const distId = '<?php echo $jerarquia['distrito_id']; ?>';
            
            document.getElementById('departamento').value = depId;
            await cargarProvincias(depId);
            document.getElementById('provincia').value = provId;
            await cargarDistritos(provId);
            document.getElementById('distrito').value = distId;
        });
    <?php endif; ?>
</script>
