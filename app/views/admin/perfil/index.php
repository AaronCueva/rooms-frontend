<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Mi Perfil</h1>
    </div>

    <form action="/admin/perfil/actualizar" method="POST" enctype="multipart/form-data">
        <div class="row">
            <!-- Columna Izquierda: Foto y Puntos -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-body text-center">
                        <?php 
                        $foto = !empty($usuario['url_foto']) ? $usuario['url_foto'] : '/public/assets/img/undraw_profile.svg'; 
                        ?>
                        <img id="previewFoto" class="img-profile rounded-circle mb-3" src="<?php echo htmlspecialchars($foto); ?>" alt="Foto de perfil" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #eaecf4;">
                        
                        <div class="mb-3">
                            <label for="foto_perfil" class="form-label btn btn-sm btn-outline-primary w-100">
                                <i class="fas fa-camera me-1"></i> Cambiar Foto
                            </label>
                            <input class="form-control d-none" type="file" id="foto_perfil" name="foto_perfil" accept="image/png, image/jpeg, image/jpg, image/webp" onchange="previewImage(this)">
                        </div>

                        <div class="mt-4 pt-4 border-top">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Puntos Acumulados
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                                <i class="fas fa-star text-warning me-1"></i>
                                <?php echo number_format($usuario['puntos_acumulados'] ?? 0); ?>
                            </div>
                            <small class="text-muted">No editable</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Datos -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Información Personal</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Nombres *</label>
                                <input type="text" class="form-control" name="nombres" value="<?php echo htmlspecialchars($usuario['nombres'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Apellido Paterno *</label>
                                <input type="text" class="form-control" name="apellido_paterno" value="<?php echo htmlspecialchars($usuario['apellido_paterno'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Apellido Materno *</label>
                                <input type="text" class="form-control" name="apellido_materno" value="<?php echo htmlspecialchars($usuario['apellido_materno'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Tipo de Documento</label>
                                <select class="form-select" name="tipo_documento_codigo">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($tiposDocumento as $td): ?>
                                        <option value="<?php echo $td['codigo']; ?>" <?php echo ($usuario['tipo_documento_codigo'] == $td['codigo']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($td['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Número Documento</label>
                                <input type="text" class="form-control" name="numero_documento" value="<?php echo htmlspecialchars($usuario['numero_documento'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Género</label>
                                <select class="form-select" name="genero_codigo">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($generos as $g): ?>
                                        <option value="<?php echo $g['codigo']; ?>" <?php echo ($usuario['genero_codigo'] == $g['codigo']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($g['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Correo Electrónico *</label>
                                <input type="email" class="form-control" name="correo" value="<?php echo htmlspecialchars($usuario['correo'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Celular</label>
                                <input type="text" class="form-control" name="celular" value="<?php echo htmlspecialchars($usuario['celular'] ?? ''); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Teléfono</label>
                                <input type="text" class="form-control" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>">
                            </div>
                        </div>

                        <h6 class="m-0 font-weight-bold text-primary border-bottom pb-2 mb-3">Ubicación y Universidad</h6>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Departamento</label>
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
                                <label class="form-label fw-bold small">Provincia</label>
                                <select class="form-select" id="provincia" onchange="cargarDistritos(this.value)" disabled>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Distrito</label>
                                <select class="form-select" name="distrito" id="distrito" disabled>
                                    <option value="">Seleccione...</option>
                                    <?php if ($jerarquia && $usuario['ubicacion_id']): ?>
                                        <option value="<?php echo $usuario['ubicacion_id']; ?>" selected>
                                            <?php echo htmlspecialchars($jerarquia['distrito_nombre']); ?>
                                        </option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small">Universidad</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light" id="display_universidad" readonly value="<?php echo htmlspecialchars($universidad_nombre); ?>" placeholder="Ninguna universidad seleccionada">
                                <input type="hidden" name="universidad_id" id="universidad_id" value="<?php echo htmlspecialchars($usuario['universidad_id'] ?? ''); ?>">
                                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#modalUniversidad">
                                    <i class="fas fa-search me-1"></i> Buscar
                                </button>
                                <button class="btn btn-outline-danger" type="button" onclick="limpiarUniversidad()" title="Quitar Universidad">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <h6 class="m-0 font-weight-bold text-primary border-bottom pb-2 mb-3">Información Empresarial (Opcional)</h6>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Razón Social</label>
                                <input type="text" class="form-control" name="razon_social" value="<?php echo htmlspecialchars($usuario['razon_social'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Nombre Comercial</label>
                                <input type="text" class="form-control" name="nombre_comercial" value="<?php echo htmlspecialchars($usuario['nombre_comercial'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold small">Descripción de la Empresa</label>
                            <textarea class="form-control" name="descripcion" rows="3"><?php echo htmlspecialchars($usuario['descripcion'] ?? ''); ?></textarea>
                        </div>

                        <div class="text-end border-top pt-3">
                            <a href="/admin/perfil/password" class="btn btn-secondary me-2"><i class="fas fa-key me-1"></i> Cambiar Contraseña</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Guardar Cambios</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal Buscador de Universidades -->
<div class="modal fade" id="modalUniversidad" tabindex="-1" aria-labelledby="modalUniversidadLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUniversidadLabel"><i class="fas fa-university me-2 text-primary"></i>Buscar Universidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="buscadorUniversidad" placeholder="Escribe el nombre de la universidad..." onkeyup="buscarUniversidadEnModal()">
                    <button class="btn btn-primary" type="button" onclick="buscarUniversidadEnModal()"><i class="fas fa-search"></i></button>
                </div>
                <div class="list-group" id="listaUniversidades" style="max-height: 300px; overflow-y: auto;">
                    <div class="text-center p-3 text-muted">Escribe para buscar...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewFoto').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function limpiarUniversidad() {
        document.getElementById('universidad_id').value = '';
        document.getElementById('display_universidad').value = '';
    }

    async function buscarUniversidadEnModal() {
        const query = document.getElementById('buscadorUniversidad').value;
        const lista = document.getElementById('listaUniversidades');
        
        if (query.length < 2) {
            if(query.length === 0) lista.innerHTML = '<div class="text-center p-3 text-muted">Escribe para buscar...</div>';
            return;
        }

        lista.innerHTML = '<div class="text-center p-3"><i class="fas fa-spinner fa-spin text-primary fs-3"></i></div>';

        try {
            const response = await fetch('/api/universidades/buscar?q=' + encodeURIComponent(query));
            const data = await response.json();
            
            lista.innerHTML = '';
            if (data.length === 0) {
                lista.innerHTML = '<div class="text-center p-3 text-muted">No se encontraron resultados</div>';
                return;
            }

            data.forEach(u => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'list-group-item list-group-item-action';
                btn.innerHTML = `<div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 fw-bold">${u.nombre}</h6>
                                 </div>
                                 <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>${u.direccion || 'Sin dirección'}</small>`;
                btn.onclick = () => {
                    document.getElementById('universidad_id').value = u.id;
                    document.getElementById('display_universidad').value = u.nombre;
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalUniversidad'));
                    modal.hide();
                };
                lista.appendChild(btn);
            });
        } catch (error) {
            console.error('Error fetching universities:', error);
            lista.innerHTML = '<div class="text-center p-3 text-danger">Error al buscar universidades</div>';
        }
    }

    // ============ Combos en Cascada para Ubicación ============
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

    // Precargar jerarquía de ubicación si el usuario la tiene
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
