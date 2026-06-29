<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <a href="/admin/contratos" class="text-decoration-none text-secondary me-2"><i class="fas fa-arrow-left"></i></a>
            <?php echo htmlspecialchars($titulo); ?>
        </h1>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form action="<?php echo $contrato ? '/admin/contratos/actualizar' : '/admin/contratos/guardar'; ?>" method="POST" enctype="multipart/form-data">
                        <?php if ($contrato): ?>
                            <input type="hidden" name="contrato_id" value="<?php echo $contrato['contrato_id']; ?>">
                        <?php endif; ?>

                        <h5 class="fw-bold text-primary mb-3">Vincular a Reserva (Aprobada)</h5>
                        <div class="mb-4">
                            <?php if ($contrato): ?>
                                <!-- Si estamos editando, mostramos la reserva actual seleccionada y bloqueada para cambio directo (o permitimos cambiarla si es necesario) -->
                                <input type="hidden" name="reserva_id" value="<?php echo $contrato['reserva_id']; ?>">
                                <input type="text" class="form-control" value="Reserva #<?php echo $contrato['reserva_id']; ?> | <?php echo htmlspecialchars($contrato['nombres'] . ' ' . $contrato['apellido_paterno'] . ' - Aloj: [' . $contrato['alojamiento_codigo'] . ']'); ?>" readonly disabled>
                            <?php else: ?>
                                <!-- Si estamos creando, mostramos lista de reservas aprobadas -->
                                <select class="form-select select2" name="reserva_id" required>
                                    <option value="">Seleccione una reserva...</option>
                                    <?php 
                                    // Si viene el ID por GET (desde el botón "Generar Contrato")
                                    $reserva_get_id = $_GET['reserva_id'] ?? null;
                                    foreach ($reservas as $res): 
                                        // Solo mostrar reservas aprobadas
                                        if($res['estado_codigo'] != 'APROBADA') continue;
                                    ?>
                                        <option value="<?php echo $res['reserva_id']; ?>" <?php echo ($reserva_get_id == $res['reserva_id']) ? 'selected' : ''; ?>>
                                            R-<?php echo $res['reserva_id']; ?> | Estudiante: <?php echo htmlspecialchars($res['nombres'] . ' ' . $res['apellido_paterno']); ?> | Aloj: [<?php echo htmlspecialchars($res['alojamiento_codigo']); ?>]
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>

                        <h5 class="fw-bold text-primary mb-3">Detalles del Contrato</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Fecha de Inicio</label>
                                <input type="date" class="form-control" name="fecha_inicio" value="<?php echo $contrato ? date('Y-m-d', strtotime($contrato['fecha_inicio'])) : ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Fecha de Fin</label>
                                <input type="date" class="form-control" name="fecha_fin" value="<?php echo $contrato ? date('Y-m-d', strtotime($contrato['fecha_fin'])) : ''; ?>" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Monto Renta Mensual</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control" name="monto_renta" value="<?php echo $contrato ? $contrato['monto_renta'] : '0.00'; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Monto Garantía</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control" name="monto_garantia" value="<?php echo $contrato ? $contrato['monto_garantia'] : '0.00'; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Día de Pago (Mensual)</label>
                                <input type="number" class="form-control" name="fecha_pago_mensual" value="<?php echo $contrato ? $contrato['fecha_pago_mensual'] : '1'; ?>" min="1" max="31" required>
                                <small class="text-muted">Día del mes (1-31)</small>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Cargo por Plataforma (Opcional)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control" name="cargo_plataforma" value="<?php echo $contrato ? $contrato['cargo_plataforma'] : '0.00'; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Estado del Contrato</label>
                                <select class="form-select" name="estado_codigo">
                                    <option value="ACTIVO" <?php echo ($contrato && $contrato['estado_codigo'] == 'ACTIVO') ? 'selected' : ''; ?>>Activo</option>
                                    <option value="FINALIZADO" <?php echo ($contrato && $contrato['estado_codigo'] == 'FINALIZADO') ? 'selected' : ''; ?>>Finalizado (Terminado naturalmente)</option>
                                    <option value="CANCELADO" <?php echo ($contrato && $contrato['estado_codigo'] == 'CANCELADO') ? 'selected' : ''; ?>>Cancelado (Roto antes de tiempo)</option>
                                </select>
                            </div>
                        </div>

                        <h5 class="fw-bold text-primary mb-3">Documento Físico / Digital</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Subir Contrato Firmado (PDF, JPG, PNG)</label>
                            <input type="file" class="form-control" name="documento" accept=".pdf,image/*">
                            <small class="text-muted">Máximo 5MB. <?php echo $contrato && !empty($contrato['documento_url']) ? 'Dejar en blanco para mantener el documento actual.' : ''; ?></small>
                            
                            <?php if ($contrato && !empty($contrato['documento_url'])): ?>
                                <div class="mt-2 p-2 border rounded bg-light">
                                    <i class="fas fa-file-contract text-primary me-2"></i> Documento actual: 
                                    <a href="<?php echo htmlspecialchars($contrato['documento_url']); ?>" target="_blank" class="fw-bold text-decoration-none">Ver Documento</a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="/admin/contratos" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Guardar Contrato</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: "Seleccione..."
    });
});
</script>
