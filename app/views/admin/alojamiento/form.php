<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">
            <a href="/admin/alojamientos" class="text-decoration-none text-secondary me-2">
                <i class="fas fa-arrow-left"></i>
            </a>
            <?php echo htmlspecialchars($titulo ?? 'Formulario Alojamiento'); ?>
        </h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <?php $esEditar = isset($alojamiento); ?>
            <form action="<?php echo $esEditar ? '/admin/alojamientos/actualizar' : '/admin/alojamientos/guardar'; ?>"
                method="POST">

                <?php if ($esEditar): ?>
                    <input type="hidden" name="alojamiento_id" value="<?php echo $alojamiento['alojamiento_id']; ?>">
                <?php endif; ?>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Código *</label>
                        <input type="text" class="form-control" name="codigo" required
                            value="<?php echo htmlspecialchars($alojamiento['codigo'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Título *</label>
                        <input type="text" class="form-control" name="titulo" required
                            value="<?php echo htmlspecialchars($alojamiento['titulo'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tipo de Alojamiento *</label>
                        <select class="form-select" name="tipo_codigo" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach ($tipos as $tipo): ?>
                                <option value="<?php echo $tipo['codigo']; ?>" <?php echo ($esEditar && $alojamiento['TIPO_PUBLICACION_ALOJAMIENTO'] == $tipo['codigo']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($tipo['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Habitaciones *</label>
                        <input type="number" class="form-control" name="numero_habitaciones" min="0" required
                            value="<?php echo $alojamiento['numero_habitaciones'] ?? 0; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Baños *</label>
                        <input type="number" class="form-control" name="numero_banios" min="0" required
                            value="<?php echo $alojamiento['numero_banios'] ?? 0; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Tamaño (m2) *</label>
                        <input type="number" step="0.01" class="form-control" name="tamanio_m2" required
                            value="<?php echo $alojamiento['tamanio_m2'] ?? 0; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Género Exclusivo</label>
                        <select class="form-select" name="genero_exclusivo_codigo">
                            <?php foreach ($generos as $g): ?>
                                <option value="<?php echo $g['codigo']; ?>" <?php echo ($esEditar && $alojamiento['genero_exclusivo_codigo'] == $g['codigo']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($g['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Estado de Publicación *</label>
                        <select class="form-select" name="estado_codigo" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach ($estados as $est): ?>
                                <option value="<?php echo $est['codigo']; ?>" <?php echo ($esEditar && $alojamiento['estado_codigo'] == $est['codigo']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($est['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Precio Mensual *</label>
                        <input type="number" step="0.01" class="form-control" name="precio_mensual" required
                            value="<?php echo $alojamiento['precio_mensual'] ?? 0; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Moneda *</label>
                        <select class="form-select" name="moneda_codigo" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach ($monedas as $m): ?>
                                <option value="<?php echo $m['codigo']; ?>" <?php echo ($esEditar && $alojamiento['moneda_codigo'] == $m['codigo']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($m['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Garantía *</label>
                        <input type="number" step="0.01" class="form-control" name="garantia" required
                            value="<?php echo $alojamiento['garantia'] ?? 0; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Duración Mín. (Meses)</label>
                        <input type="number" class="form-control" name="duracion_minima_meses"
                            value="<?php echo $alojamiento['duracion_minima_meses'] ?? 0; ?>">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Precio Servicios Fijos</label>
                        <input type="number" step="0.01" class="form-control" name="precio_servicios"
                            value="<?php echo $alojamiento['precio_servicios'] ?? 0; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Fecha Disponible</label>
                        <input type="date" class="form-control" name="fecha_disponible"
                            value="<?php echo $alojamiento['fecha_disponible'] ?? ''; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Política de Casa</label>
                        <select class="form-select" name="politica_casa_id">
                            <option value="">Seleccionar...</option>
                            <?php foreach ($politicas as $pol): ?>
                                <option value="<?php echo $pol['politica_casa_id']; ?>" <?php echo ($esEditar && $alojamiento['politica_casa_id'] == $pol['politica_casa_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($pol['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Propietario *</label>
                        <select class="form-select" name="usuario_id" required>
                            <option value="">Seleccionar Propietario...</option>
                            <?php foreach ($propietarios as $prop): ?>
                                <option value="<?php echo $prop['usuario_id']; ?>" <?php echo ($esEditar && $alojamiento['usuario_id'] == $prop['usuario_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($prop['nombres'] . ' ' . $prop['apellido_paterno'] . ' (' . $prop['correo'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Calificación Promedio</label>
                        <input type="number" step="0.1" max="5" class="form-control" name="calificacion"
                            value="<?php echo $alojamiento['calificacion'] ?? 0; ?>">
                    </div>
                </div>

                <h6 class="fw-bold mt-4 mb-3 border-bottom pb-2">Ubicación</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Departamento *</label>
                        <select class="form-select" id="departamento" required onchange="cargarProvincias(this.value)">
                            <option value="">Seleccione...</option>
                            <?php foreach ($departamentos as $dep): ?>
                                <option value="<?php echo $dep['ubicacion_id']; ?>">
                                    <?php echo htmlspecialchars($dep['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Provincia *</label>
                        <select class="form-select" id="provincia" required onchange="cargarDistritos(this.value)"
                            disabled>
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Distrito *</label>
                        <select class="form-select" name="distrito" id="distrito" required disabled>
                            <option value="">Seleccione...</option>
                            <?php if ($esEditar && $alojamiento['ubicacion_id']): ?>
                                <option value="<?php echo $alojamiento['ubicacion_id']; ?>" selected>
                                    <?php echo htmlspecialchars($alojamiento['distrito_nombre']); ?>
                                </option>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted">Si edita, debe volver a seleccionar en cascada.</small>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Dirección Exacta *</label>
                    <input type="text" class="form-control" name="direccion" required
                        value="<?php echo htmlspecialchars($alojamiento['direccion'] ?? ''); ?>">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Descripción del Alojamiento</label>
                    <textarea class="form-control" name="descripcion"
                        rows="4"><?php echo htmlspecialchars($alojamiento['descripcion'] ?? ''); ?></textarea>
                </div>

                <h6 class="fw-bold mt-4 mb-3 border-bottom pb-2">Características Extra (Switches)</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="mascotas_permitidas"
                                <?php echo ($esEditar && $alojamiento['mascotas_permitidas']) ? 'checked' : ''; ?>>
                            <label class="form-check-label">Mascotas Permitidas</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="fumadores_permitidos"
                                <?php echo ($esEditar && $alojamiento['fumadores_permitidos']) ? 'checked' : ''; ?>>
                            <label class="form-check-label">Fumadores Permitidos</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="amoblado" <?php echo ($esEditar && $alojamiento['amoblado']) ? 'checked' : ''; ?>>
                            <label class="form-check-label">Amoblado</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="solo_verificados" <?php echo ($esEditar && $alojamiento['solo_verificados']) ? 'checked' : ''; ?>>
                            <label class="form-check-label">Solo Usuarios Verificados</label>
                        </div>
                    </div>
                    <div class="col-md-3 mt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="habilitado" <?php echo (!$esEditar || $alojamiento['habilitado']) ? 'checked' : ''; ?>>
                            <label class="form-check-label">Visible en la plataforma</label>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <a href="/admin/alojamientos" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>
                        <?php echo $esEditar ? 'Actualizar Alojamiento' : 'Guardar Alojamiento'; ?></button>
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
        } catch (error) {
            console.error('Error al cargar provincias:', error);
        }
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
        } catch (error) {
            console.error('Error al cargar distritos:', error);
        }
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