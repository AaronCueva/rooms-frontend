<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-key me-2 text-primary"></i><?php echo htmlspecialchars($titulo); ?>
        </h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-8 col-md-10">
            <div class="card shadow mb-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-lock fa-3x text-primary"></i>
                        </div>
                        <h5 class="fw-bold">Actualiza tu Seguridad</h5>
                        <p class="text-muted small">Asegúrate de usar una contraseña fuerte y que no uses en otros sitios web.</p>
                    </div>

                    <form action="/admin/perfil/password/actualizar" method="POST">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-gray-700">Contraseña Actual <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-unlock-alt text-muted"></i></span>
                                <input type="password" class="form-control" name="password_actual" required placeholder="Ingresa tu contraseña actual">
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3 mt-4">
                            <label class="form-label fw-bold text-gray-700">Nueva Contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-key text-muted"></i></span>
                                <input type="password" class="form-control" name="password_nuevo" id="password_nuevo" required placeholder="Mínimo 6 caracteres" minlength="6">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-gray-700">Confirmar Nueva Contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-check-circle text-muted"></i></span>
                                <input type="password" class="form-control" name="password_confirmacion" id="password_confirmacion" required placeholder="Repite la nueva contraseña" minlength="6">
                            </div>
                            <small id="password_match_message" class="text-danger d-none mt-1">Las contraseñas no coinciden.</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="btnSubmit">
                                <i class="fas fa-save me-2"></i>Guardar Nueva Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pwdNuevo = document.getElementById('password_nuevo');
    const pwdConf = document.getElementById('password_confirmacion');
    const msg = document.getElementById('password_match_message');
    const btnSubmit = document.getElementById('btnSubmit');

    function checkMatch() {
        if (pwdConf.value && pwdNuevo.value !== pwdConf.value) {
            msg.classList.remove('d-none');
            pwdConf.classList.add('is-invalid');
            btnSubmit.disabled = true;
        } else {
            msg.classList.add('d-none');
            pwdConf.classList.remove('is-invalid');
            btnSubmit.disabled = false;
        }
    }

    pwdNuevo.addEventListener('input', checkMatch);
    pwdConf.addEventListener('input', checkMatch);
});
</script>
