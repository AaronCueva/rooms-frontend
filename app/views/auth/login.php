<div class="card auth-card py-4 px-3 px-md-5 mx-auto" style="max-width: 450px;">
    <div class="text-center mb-4">
        <!-- Logo Text -->
        <h2 class="fw-bold mb-1"><span class="text-dark">APP-</span><span class="text-brand">ROOMS</span></h2>
        
        <h4 class="fw-bold mt-4 mb-2">Iniciar sesión</h4>
        <p class="text-muted" style="font-size: 0.9rem;">¡Prepárate para conocer todo APP-ROOMS!</p>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger p-2 text-center" style="font-size: 0.9rem;" role="alert">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert-success p-2 text-center" style="font-size: 0.9rem;" role="alert">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <form action="/login" method="POST">
        <div class="mb-3">
            <label for="correo" class="form-label fw-semibold" style="font-size: 0.85rem;">Correo del usuario<span class="text-brand">*</span></label>
            <input type="email" class="form-control" id="correo" name="correo" placeholder="Ingresa correo del usuario" style="background-color: #f8f9fa; border: 1px solid #e9ecef;" required>
        </div>
        
        <div class="mb-3">
            <label for="password" class="form-label fw-semibold" style="font-size: 0.85rem;">Contraseña<span class="text-brand">*</span></label>
            <div class="input-group">
                <input type="password" class="form-control" id="password" name="password" placeholder="Ingresa la contraseña" style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-right: none;" required>
                <span class="input-group-text bg-light" style="border: 1px solid #e9ecef; border-left: none; cursor: pointer;" onclick="togglePassword()">
                    <i class="fas fa-eye text-muted" id="toggleIcon"></i>
                </span>
            </div>
        </div>

        <div class="text-center mb-4">
            <small class="text-muted" style="font-size: 0.75rem;">Al iniciar sesión aceptas los <a href="#" class="text-secondary fw-semibold text-decoration-none">Términos de uso de APP-ROOMS</a></small>
        </div>

        <div class="d-grid mb-3">
            <button class="btn btn-brand w-100 py-2" type="submit">Iniciar sesión</button>
        </div>
        
        <div class="text-center mt-4">
            <a href="/register" class="text-brand fw-bold text-decoration-none" style="font-size: 0.9rem;">Registrarme</a>
            <span class="mx-2 text-muted">|</span>
            <a href="#" class="text-brand fw-bold text-decoration-none" style="font-size: 0.9rem;">Olvidé mi contraseña</a>
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
</script>