<div class="card auth-card py-4 px-3 px-md-4 mx-auto" style="max-width: 1200px; max-height: 100vh; overflow-y: auto;">
    <div class="text-center mb-4">
        <h2 class="fw-bold mb-1"><span class="text-dark">APP-</span><span class="text-brand">ROOMS</span></h2>

        <h4 class="fw-bold mt-3 mb-2">Crear Cuenta</h4>
        <p class="text-muted" style="font-size: 0.9rem;">Crea una cuenta para gestionar alojamientos de forma sencilla.
        </p>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger p-2 text-center" style="font-size: 0.9rem;" role="alert">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="/register" method="POST">
        <div class="row g-3 mb-3">
            <div class="col-md-12">
                <label for="nombres" class="form-label fw-semibold" style="font-size: 0.85rem;">Nombres Completos<span
                        class="text-brand">*</span></label>
                <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Ingresa nombres"
                    style="background-color: #f8f9fa; border: 1px solid #e9ecef;" required>
            </div>

            <div class="col-md-6">
                <label for="apellido_paterno" class="form-label fw-semibold" style="font-size: 0.85rem;">Apellido
                    Paterno<span class="text-brand">*</span></label>
                <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno"
                    placeholder="Apellido paterno" style="background-color: #f8f9fa; border: 1px solid #e9ecef;"
                    required>
            </div>
            <div class="col-md-6">
                <label for="apellido_materno" class="form-label fw-semibold" style="font-size: 0.85rem;">Apellido
                    Materno<span class="text-brand">*</span></label>
                <input type="text" class="form-control" id="apellido_materno" name="apellido_materno"
                    placeholder="Apellido materno" style="background-color: #f8f9fa; border: 1px solid #e9ecef;"
                    required>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-5">
                <label for="tipo_documento_codigo" class="form-label fw-semibold" style="font-size: 0.85rem;">Tipo
                    Documento<span class="text-brand">*</span></label>
                <select class="form-select" id="tipo_documento_codigo" name="tipo_documento_codigo"
                    style="background-color: #f8f9fa; border: 1px solid #e9ecef;" required>
                    <option value="" selected disabled>Seleccione...</option>
                    <?php if (isset($tipos_documento) && is_array($tipos_documento)): ?>
                        <?php foreach ($tipos_documento as $tipo): ?>
                            <option value="<?php echo htmlspecialchars($tipo['codigo']); ?>">
                                <?php echo htmlspecialchars($tipo['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-7">
                <label for="numero_documento" class="form-label fw-semibold" style="font-size: 0.85rem;">Número
                    Documento<span class="text-brand">*</span></label>
                <input type="text" class="form-control" id="numero_documento" name="numero_documento"
                    placeholder="Número" style="background-color: #f8f9fa; border: 1px solid #e9ecef;" required>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-7">
                <label for="correo" class="form-label fw-semibold" style="font-size: 0.85rem;">Correo Electrónico<span
                        class="text-brand">*</span></label>
                <input type="email" class="form-control" id="correo" name="correo" placeholder="nombre@ejemplo.com"
                    style="background-color: #f8f9fa; border: 1px solid #e9ecef;" required>
            </div>
            <div class="col-md-5">
                <label for="celular" class="form-label fw-semibold" style="font-size: 0.85rem;">Celular<span
                        class="text-brand">*</span></label>
                <input type="text" class="form-control" id="celular" name="celular" placeholder="Número celular"
                    style="background-color: #f8f9fa; border: 1px solid #e9ecef;" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="rol_id" class="form-label fw-semibold" style="font-size: 0.85rem;">Rol<span
                    class="text-brand">*</span></label>
            <select class="form-select" id="rol_id" name="rol_id"
                style="background-color: #f8f9fa; border: 1px solid #e9ecef;" required>
                <option value="" selected disabled>Seleccione su rol...</option>
                <?php if (isset($roles) && is_array($roles)): ?>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?php echo htmlspecialchars($rol['rol_id']); ?>">
                            <?php echo htmlspecialchars($rol['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <!-- Ubicación en cascada -->
        <h6 class="fw-bold mt-4 mb-2" style="font-size: 0.9rem;">Ubicación</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label for="departamento" class="form-label fw-semibold" style="font-size: 0.85rem;">Departamento<span
                        class="text-brand">*</span></label>
                <select class="form-select" id="departamento" name="departamento"
                    style="background-color: #f8f9fa; border: 1px solid #e9ecef;" required
                    onchange="cargarProvincias(this.value)">
                    <option value="" selected disabled>Seleccione...</option>
                    <?php if (isset($departamentos) && is_array($departamentos)): ?>
                        <?php foreach ($departamentos as $dep): ?>
                            <option value="<?php echo htmlspecialchars($dep['ubicacion_id']); ?>">
                                <?php echo htmlspecialchars($dep['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="provincia" class="form-label fw-semibold" style="font-size: 0.85rem;">Provincia<span
                        class="text-brand">*</span></label>
                <select class="form-select" id="provincia" name="provincia"
                    style="background-color: #f8f9fa; border: 1px solid #e9ecef;" required
                    onchange="cargarDistritos(this.value)" disabled>
                    <option value="" selected disabled>Seleccione...</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="distrito" class="form-label fw-semibold" style="font-size: 0.85rem;">Distrito<span
                        class="text-brand">*</span></label>
                <select class="form-select" id="distrito" name="distrito"
                    style="background-color: #f8f9fa; border: 1px solid #e9ecef;" required disabled>
                    <option value="" selected disabled>Seleccione...</option>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label fw-semibold" style="font-size: 0.85rem;">Contraseña<span
                    class="text-brand">*</span></label>
            <div class="input-group">
                <input type="password" class="form-control" id="password" name="password"
                    placeholder="Ingresa la contraseña"
                    style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-right: none;" required>
                <span class="input-group-text bg-light"
                    style="border: 1px solid #e9ecef; border-left: none; cursor: pointer;" onclick="togglePassword()">
                    <i class="fas fa-eye text-muted" id="toggleIcon"></i>
                </span>
            </div>
        </div>

        <div class="text-center mb-4">
            <small class="text-muted" style="font-size: 0.75rem;">Al registrarte aceptas los <a href="#"
                    class="text-secondary fw-semibold text-decoration-none">Términos de uso de APP-ROOMS</a></small>
        </div>

        <div class="d-grid mb-3">
            <button class="btn btn-brand w-100 py-2" type="submit">Crear Cuenta</button>
        </div>

        <div class="text-center mt-3">
            <span class="text-muted" style="font-size: 0.9rem;">¿Ya tienes cuenta?</span>
            <a href="/login" class="text-brand fw-bold text-decoration-none ms-1" style="font-size: 0.9rem;">Inicia
                sesión aquí</a>
        </div>
    </form>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    async function cargarProvincias(departamento_id) {
        const provinciaSelect = document.getElementById('provincia');
        const distritoSelect = document.getElementById('distrito');

        // Resetear combos hijos
        provinciaSelect.innerHTML = '<option value="" selected disabled>Seleccione...</option>';
        distritoSelect.innerHTML = '<option value="" selected disabled>Seleccione...</option>';
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

        // Resetear combo distrito
        distritoSelect.innerHTML = '<option value="" selected disabled>Seleccione...</option>';
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
</script>