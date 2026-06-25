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

                <!-- Información básica -->
                <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="fas fa-info-circle me-1 text-primary"></i> Información Principal</h6>
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
                                <option value="<?php echo $tipo['codigo']; ?>" <?php echo ($esEditar && $alojamiento['tipo_codigo'] == $tipo['codigo']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($tipo['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Características físicas -->
                <h6 class="fw-bold mt-4 mb-3 border-bottom pb-2"><i class="fas fa-ruler-combined me-1 text-primary"></i> Características Físicas</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Habitaciones *</label>
                        <input type="number" class="form-control" name="numero_habitaciones" min="0" required
                            value="<?php echo $alojamiento['numero_habitaciones'] ?? 0; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Baños *</label>
                        <input type="number" class="form-control" name="numero_banios" min="0" required
                            value="<?php echo $alojamiento['numero_banos'] ?? 0; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Tamaño (m²) *</label>
                        <input type="number" step="0.01" class="form-control" name="tamanio_m2" required
                            value="<?php echo $alojamiento['tamano_m2'] ?? 0; ?>">
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

                <!-- Precios -->
                <h6 class="fw-bold mt-4 mb-3 border-bottom pb-2"><i class="fas fa-money-bill-wave me-1 text-primary"></i> Precios y Condiciones</h6>
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
                        <label class="form-label fw-semibold">Calificación Promedio</label>
                        <input type="number" step="0.1" max="5" class="form-control" name="calificacion"
                            value="<?php echo $alojamiento['calificacion'] ?? 0; ?>">
                    </div>
                </div>

                <!-- Propietario -->
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
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
                </div>

                <!-- Políticas de Casa (Múltiples) -->
                <h6 class="fw-bold mt-4 mb-3 border-bottom pb-2"><i class="fas fa-clipboard-list me-1 text-primary"></i> Políticas de Casa</h6>
                <div class="row g-2 mb-4">
                    <?php if (!empty($politicas)): ?>
                        <?php foreach ($politicas as $pol): ?>
                            <?php
                                $checked = in_array($pol['politica_casa_id'], $politicas_seleccionadas ?? []) ? 'checked' : '';
                            ?>
                            <div class="col-md-4">
                                <div class="form-check border rounded p-3 h-100">
                                    <input class="form-check-input" type="checkbox" name="politicas[]"
                                        value="<?php echo $pol['politica_casa_id']; ?>"
                                        id="pol_<?php echo $pol['politica_casa_id']; ?>" <?php echo $checked; ?>>
                                    <label class="form-check-label fw-semibold" for="pol_<?php echo $pol['politica_casa_id']; ?>">
                                        <?php echo htmlspecialchars($pol['nombre']); ?>
                                    </label>
                                    <?php if (!empty($pol['descripcion'])): ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($pol['descripcion']); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-warning mb-0">No hay políticas de casa registradas.</div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Ubicación -->
                <h6 class="fw-bold mt-4 mb-3 border-bottom pb-2"><i class="fas fa-map-marker-alt me-1 text-primary"></i> Ubicación</h6>
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
                        <select class="form-select" id="provincia" required onchange="cargarDistritos(this.value)" disabled>
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
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Dirección Exacta *</label>
                    <input type="text" class="form-control" name="direccion" required
                        value="<?php echo htmlspecialchars($alojamiento['direccion'] ?? ''); ?>">
                </div>

                <!-- Mapa Interactivo -->
                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0"><i class="fas fa-map me-1 text-success"></i> Ubicación en el Mapa</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnObtenerUbicacion">
                                <i class="fas fa-crosshairs me-1"></i> Obtener mi ubicación actual
                            </button>
                        </div>
                        <div id="mapaAlojamiento" style="height: 350px; border-radius: 8px; border: 2px solid #dee2e6;"></div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Latitud</label>
                                <input type="text" class="form-control form-control-sm" name="latitud" id="inputLatitud" readonly
                                    value="<?php echo htmlspecialchars($alojamiento['latitud'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Longitud</label>
                                <input type="text" class="form-control form-control-sm" name="longitud" id="inputLongitud" readonly
                                    value="<?php echo htmlspecialchars($alojamiento['longitud'] ?? ''); ?>">
                            </div>
                        </div>
                        <small class="text-muted mt-1 d-block">Haz clic en el mapa o arrastra el marcador para seleccionar la ubicación exacta del inmueble.</small>
                    </div>
                </div>

                <!-- Descripción -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Descripción del Alojamiento</label>
                    <textarea class="form-control" name="descripcion" rows="4"><?php echo htmlspecialchars($alojamiento['descripcion'] ?? ''); ?></textarea>
                </div>

                <!-- Switches -->
                <h6 class="fw-bold mt-4 mb-3 border-bottom pb-2"><i class="fas fa-toggle-on me-1 text-primary"></i> Características Extra</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="bano_privado"
                                <?php echo ($esEditar && $alojamiento['bano_privado']) ? 'checked' : ''; ?>>
                            <label class="form-check-label">Baño Privado</label>
                        </div>
                    </div>
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
                    <div class="col-md-3 mt-3">
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

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // ============ Combos en Cascada ============
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
                data.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.ubicacion_id;
                    opt.textContent = p.nombre;
                    provinciaSelect.appendChild(opt);
                });
                provinciaSelect.disabled = false;
            }
        } catch (e) { console.error('Error provincias:', e); }
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
                data.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.ubicacion_id;
                    opt.textContent = d.nombre;
                    distritoSelect.appendChild(opt);
                });
                distritoSelect.disabled = false;
            }
        } catch (e) { console.error('Error distritos:', e); }
    }

    // Precargar jerarquía si estamos editando
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

    // ============ Mapa Interactivo con Leaflet ============
    document.addEventListener('DOMContentLoaded', function () {
        const inputLat = document.getElementById('inputLatitud');
        const inputLng = document.getElementById('inputLongitud');

        // Coordenadas iniciales: si hay datos guardados los usamos, sino centro de Perú
        let lat = parseFloat(inputLat.value) || -12.0464;
        let lng = parseFloat(inputLng.value) || -77.0428;
        let zoomInicial = (inputLat.value && inputLng.value) ? 16 : 6;

        const map = L.map('mapaAlojamiento').setView([lat, lng], zoomInicial);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        let marker = L.marker([lat, lng], { draggable: true }).addTo(map);

        // Si no hay coordenadas previas, ocultar el marcador hasta que se seleccione
        if (!inputLat.value || !inputLng.value) {
            map.removeLayer(marker);
        }

        function actualizarCoordenadas(latlng) {
            inputLat.value = latlng.lat.toFixed(7);
            inputLng.value = latlng.lng.toFixed(7);
        }

        // Click en el mapa para mover el marcador
        map.on('click', function (e) {
            if (!map.hasLayer(marker)) {
                marker = L.marker(e.latlng, { draggable: true }).addTo(map);
                marker.on('dragend', function (ev) {
                    actualizarCoordenadas(ev.target.getLatLng());
                });
            } else {
                marker.setLatLng(e.latlng);
            }
            actualizarCoordenadas(e.latlng);
        });

        // Arrastrar marcador
        marker.on('dragend', function (e) {
            actualizarCoordenadas(e.target.getLatLng());
        });

        // Botón "Obtener mi ubicación"
        document.getElementById('btnObtenerUbicacion').addEventListener('click', function () {
            if (!navigator.geolocation) {
                Swal.fire('Error', 'Tu navegador no soporta geolocalización.', 'error');
                return;
            }
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Obteniendo...';

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const latlng = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    map.setView([latlng.lat, latlng.lng], 17);
                    if (!map.hasLayer(marker)) {
                        marker = L.marker([latlng.lat, latlng.lng], { draggable: true }).addTo(map);
                        marker.on('dragend', function (ev) {
                            actualizarCoordenadas(ev.target.getLatLng());
                        });
                    } else {
                        marker.setLatLng([latlng.lat, latlng.lng]);
                    }
                    actualizarCoordenadas(latlng);
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-crosshairs me-1"></i> Obtener mi ubicación actual';
                    Swal.fire({
                        toast: true, position: 'top-end', icon: 'success',
                        title: 'Ubicación obtenida', showConfirmButton: false, timer: 2000
                    });
                },
                (error) => {
                    Swal.fire('Error', 'No se pudo obtener la ubicación: ' + error.message, 'error');
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-crosshairs me-1"></i> Obtener mi ubicación actual';
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        });
    });
</script>