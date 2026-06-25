<div class="card auth-card">
    <div class="row g-0">
        <div class="col-md-5 auth-cover d-none d-md-flex">
            <h2 class="fw-bold mb-3 text-center">Bienvenido de nuevo</h2>
            <p class="text-center mb-0 opacity-75">Ingresa al panel de administración de APP-ROOMS para gestionar
                reservas, alojamientos y usuarios.</p>
        </div>
        <div class="col-md-7 p-4 p-md-5">
            <div class="text-center mb-4">
                <h3 class="fw-bold">Iniciar Sesión</h3>
                <p class="text-muted">Ingresa tus credenciales para continuar</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form action="/login" method="POST">
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="correo" name="correo" placeholder="nombre@ejemplo.com"
                        required>
                    <label for="correo">Correo Electrónico</label>
                </div>
                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña"
                        required>
                    <label for="password">Contraseña</label>
                </div>
                <div class="d-grid mb-3">
                    <button class="btn btn-primary btn-lg fw-semibold" type="submit">Ingresar</button>
                </div>
                <div class="text-center">
                    <p class="text-muted mb-0">¿No tienes cuenta? <a href="/register"
                            class="text-decoration-none fw-semibold">Regístrate aquí</a></p>
                </div>
            </form>
        </div>
    </div>
</div>