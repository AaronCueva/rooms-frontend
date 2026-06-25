<div class="card auth-card border-0 shadow-lg my-5">
    <div class="row g-0">
        <!-- Imagen o Cover Premium -->
        <div class="col-lg-5 d-none d-lg-flex bg-register-image" style="background: url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80') center center / cover no-repeat;">
        </div>
        <div class="col-lg-7">
            <div class="p-5">
                <div class="text-center mb-4">
                    <h1 class="h3 text-gray-900 fw-bold mb-2">¡Únete a WS-ROOMS!</h1>
                    <p class="text-muted">Crea una cuenta administrativa y gestiona alojamientos de forma sencilla.</p>
                </div>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger shadow-sm border-0 border-start border-danger border-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form class="user" action="/register" method="POST">
                    
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Nombres Completos" required>
                        <label for="nombres">Nombres Completos</label>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" placeholder="Apellido Paterno" required>
                                <label for="apellido_paterno">Apellido Paterno</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" placeholder="Apellido Materno" required>
                                <label for="apellido_materno">Apellido Materno</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-5 mb-3 mb-md-0">
                            <div class="form-floating">
                                <select class="form-select" id="tipo_documento_codigo" name="tipo_documento_codigo" aria-label="Tipo de documento" required>
                                    <option value="" selected disabled>Seleccione...</option>
                                    <?php if(isset($tipos_documento) && is_array($tipos_documento)): ?>
                                        <?php foreach($tipos_documento as $tipo): ?>
                                            <option value="<?php echo htmlspecialchars($tipo['codigo']); ?>">
                                                <?php echo htmlspecialchars($tipo['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <label for="tipo_documento_codigo">Tipo Documento</label>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="numero_documento" name="numero_documento" placeholder="Número de Documento" required>
                                <label for="numero_documento">Número de Documento</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-7 mb-3 mb-md-0">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="correo" name="correo" placeholder="nombre@ejemplo.com" required>
                                <label for="correo">Correo Electrónico</label>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="celular" name="celular" placeholder="Número Celular" required>
                                <label for="celular">Celular</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                        <label for="password">Contraseña segura</label>
                    </div>

                    <button class="btn btn-primary btn-lg w-100 fw-semibold rounded-pill py-2 shadow-sm" type="submit">
                        Crear Cuenta
                    </button>
                    
                </form>
                
                <hr class="my-4">
                
                <div class="text-center">
                    <p class="text-muted mb-0">¿Ya tienes cuenta? <a href="/login" class="text-primary text-decoration-none fw-bold">Inicia Sesión aquí</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
