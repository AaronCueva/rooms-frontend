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

                <h6 class="fw-bold mt-4 mb-3 border-bottom pb-2"><i class="fas fa-map-marker-alt me-1 text-primary"></i> Ubicación</h6>
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

                <!-- Mapa Interactivo -->
                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0"><i class="fas fa-map me-1 text-success"></i> Ubicación en el Mapa</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnObtenerUbicacion">
                                <i class="fas fa-crosshairs me-1"></i> Obtener mi ubicación actual
                            </button>
                        </div>
                        <div id="mapaUniversidad" style="height: 350px; border-radius: 8px; border: 2px solid #dee2e6;"></div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Latitud</label>
                                <input type="text" class="form-control form-control-sm" name="latitud" id="inputLatitud" readonly
                                    value="<?php echo htmlspecialchars($universidad['latitud'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Longitud</label>
                                <input type="text" class="form-control form-control-sm" name="longitud" id="inputLongitud" readonly
                                    value="<?php echo htmlspecialchars($universidad['longitud'] ?? ''); ?>">
                            </div>
                        </div>
                        <small class="text-muted mt-1 d-block">Haz clic en el mapa o arrastra el marcador para seleccionar la ubicación exacta de la universidad.</small>
                    </div>
                </div>

                <h6 class="fw-bold mt-4 mb-3 border-bottom pb-2"><i class="fas fa-toggle-on me-1 text-primary"></i> Estado</h6>
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

        const map = L.map('mapaUniversidad').setView([lat, lng], zoomInicial);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        let marker = L.marker([lat, lng], { draggable: true }).addTo(map);

        // Si no hay coordenadas previas, ocultar el marcador
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
