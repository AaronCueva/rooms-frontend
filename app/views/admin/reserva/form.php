<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <a href="/admin/reservas" class="text-decoration-none text-secondary me-2"><i class="fas fa-arrow-left"></i></a>
            <?php echo htmlspecialchars($titulo); ?>
        </h1>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form action="<?php echo $reserva ? '/admin/reservas/actualizar' : '/admin/reservas/guardar'; ?>" method="POST">
                        <?php if ($reserva): ?>
                            <input type="hidden" name="reserva_id" value="<?php echo $reserva['reserva_id']; ?>">
                        <?php endif; ?>

                        <h5 class="fw-bold text-primary mb-3">Información de la Reserva</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Usuario (Estudiante)</label>
                                <select class="form-select form-select-sm select2" name="usuario_id" required>
                                    <option value="">Seleccione un usuario...</option>
                                    <?php foreach ($usuarios as $u): ?>
                                        <option value="<?php echo $u['usuario_id']; ?>" <?php echo ($reserva && $reserva['usuario_id'] == $u['usuario_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($u['nombres'] . ' ' . $u['apellido_paterno'] . ' (' . $u['correo'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Alojamiento</label>
                                <select class="form-select form-select-sm select2" name="alojamiento_id" required>
                                    <option value="">Seleccione un alojamiento...</option>
                                    <?php foreach ($alojamientos as $a): ?>
                                        <option value="<?php echo $a['alojamiento_id']; ?>" <?php echo ($reserva && $reserva['alojamiento_id'] == $a['alojamiento_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars('['.$a['codigo'].'] ' . $a['titulo']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Fecha de Ingreso Propuesta</label>
                                <input type="date" class="form-control" name="fecha_ingreso" value="<?php echo $reserva ? date('Y-m-d', strtotime($reserva['fecha_ingreso'])) : ''; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Duración (Meses)</label>
                                <input type="number" class="form-control" name="duracion_meses" value="<?php echo $reserva ? $reserva['duracion_meses'] : '6'; ?>" min="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Monto Total Estimado (Opcional)</label>
                                <input type="number" step="0.01" class="form-control" name="monto_total" value="<?php echo $reserva ? $reserva['monto_total'] : '0.00'; ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mensaje de Presentación</label>
                            <textarea class="form-control" name="mensaje_presentacion" rows="3" placeholder="Mensaje del estudiante para el propietario..."><?php echo $reserva ? htmlspecialchars($reserva['mensaje_presentacion']) : ''; ?></textarea>
                        </div>

                        <?php if ($reserva): ?>
                        <hr>
                        <h5 class="fw-bold text-primary mb-3">Gestión del Propietario</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Estado de la Reserva</label>
                                <select class="form-select" name="estado_codigo">
                                    <?php foreach ($estados_reserva as $estado): ?>
                                        <?php $estado_codigo = $estado['codigo'] ?? ''; ?>
                                        <option value="<?php echo htmlspecialchars($estado_codigo); ?>" <?php echo $reserva['estado_codigo'] == $estado_codigo ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($estado['nombre'] ?? $estado_codigo); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Fecha de Respuesta (Si aplica)</label>
                                <input type="datetime-local" class="form-control" name="fecha_respuesta" value="<?php echo !empty($reserva['fecha_respuesta']) ? date('Y-m-d\TH:i', strtotime($reserva['fecha_respuesta'])) : ''; ?>">
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="/admin/reservas" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Guardar Reserva</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos y Scripts para Select2 (Mejora UX para listados largos) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: $(this).data('placeholder')
    });
});
</script>
