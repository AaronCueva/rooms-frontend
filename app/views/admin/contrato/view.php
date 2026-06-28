<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <a href="/admin/contratos" class="text-decoration-none text-secondary me-2"><i class="fas fa-arrow-left"></i></a>
            <?php echo htmlspecialchars($titulo); ?>
        </h1>
        <div>
            <a href="/admin/contratos/editar?id=<?php echo $contrato['contrato_id']; ?>" class="btn btn-sm btn-info shadow-sm text-white">
                <i class="fas fa-edit fa-sm text-white-50"></i> Editar
            </a>
            <?php if (!empty($contrato['documento_url'])): ?>
                <a href="<?php echo htmlspecialchars($contrato['documento_url']); ?>" target="_blank" class="btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-print fa-sm text-white-50"></i> Ver Documento Físico
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Detalles del Contrato -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-signature me-2"></i>Información del Contrato</h6>
                    <?php 
                        $estado_clase = 'bg-secondary';
                        if($contrato['estado_codigo'] == 'ACTIVO') $estado_clase = 'bg-success';
                        if($contrato['estado_codigo'] == 'FINALIZADO') $estado_clase = 'bg-info text-dark';
                        if($contrato['estado_codigo'] == 'CANCELADO') $estado_clase = 'bg-danger';
                    ?>
                    <span class="badge <?php echo $estado_clase; ?> px-3 py-2" style="font-size: 0.9rem;">
                        <?php echo htmlspecialchars($contrato['estado_codigo']); ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <small class="text-muted text-uppercase fw-bold d-block">Fecha Inicio</small>
                            <span><i class="fas fa-play text-success me-1"></i> <?php echo date('d/m/Y', strtotime($contrato['fecha_inicio'])); ?></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted text-uppercase fw-bold d-block">Fecha Fin</small>
                            <span><i class="fas fa-stop text-danger me-1"></i> <?php echo date('d/m/Y', strtotime($contrato['fecha_fin'])); ?></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted text-uppercase fw-bold d-block">Día de Pago (Mensual)</small>
                            <span><i class="fas fa-calendar-day text-primary me-1"></i> Día <?php echo $contrato['fecha_pago_mensual']; ?> de cada mes</span>
                        </div>
                    </div>

                    <h6 class="fw-bold border-bottom pb-2 mb-3">Detalle Financiero</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="p-3 bg-light rounded text-center">
                                <small class="text-muted text-uppercase fw-bold d-block">Renta Mensual</small>
                                <span class="fs-5 fw-bold text-primary"><?php echo number_format($contrato['monto_renta'], 2); ?></span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3 bg-light rounded text-center">
                                <small class="text-muted text-uppercase fw-bold d-block">Garantía Retenida</small>
                                <span class="fs-5 fw-bold text-secondary"><?php echo number_format($contrato['monto_garantia'], 2); ?></span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3 bg-light rounded text-center">
                                <small class="text-muted text-uppercase fw-bold d-block">Cargo Plataforma</small>
                                <span class="fs-5 fw-bold text-info"><?php echo number_format($contrato['cargo_plataforma'], 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reseña del Contrato (Al finalizar) -->
            <div class="card shadow mb-4 border-left-<?php echo $resena ? 'success' : 'warning'; ?>">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-star me-2"></i>Reseña del Estudiante (Al finalizar)</h6>
                </div>
                <div class="card-body">
                    <?php if ($resena): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i <= $resena['calificacion'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                <?php endfor; ?>
                                <span class="ms-2 fw-bold"><?php echo $resena['calificacion']; ?>/5</span>
                            </div>
                            <small class="text-muted"><?php echo date('d/m/Y', strtotime($resena['fecha_creado'])); ?></small>
                        </div>
                        <p class="mb-2" style="white-space: pre-wrap; font-style: italic;">"<?php echo htmlspecialchars($resena['comentario']); ?>"</p>
                        
                        <?php if (!empty($resena['respuesta_propietario'])): ?>
                            <div class="alert alert-secondary mt-3 p-3 mb-0">
                                <small class="fw-bold text-primary d-block mb-1"><i class="fas fa-reply me-1"></i> Respuesta del Propietario:</small>
                                <p class="mb-0 small fst-italic">"<?php echo htmlspecialchars($resena['respuesta_propietario']); ?>"</p>
                            </div>
                        <?php endif; ?>

                        <div class="mt-3">
                            <form action="/admin/alojamientos/resena/toggle" method="POST" class="d-inline form-confirm" data-title="¿Cambiar visibilidad de la reseña?">
                                <input type="hidden" name="resena_id" value="<?php echo $resena['resena_id']; ?>">
                                <input type="hidden" name="alojamiento_id" value="<?php echo $contrato['alojamiento_id']; ?>">
                                <?php if ($resena['habilitado']): ?>
                                    <span class="badge bg-success me-2">Visible al público</span>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Ocultar</button>
                                <?php else: ?>
                                    <span class="badge bg-danger me-2">Oculta al público</span>
                                    <button type="submit" class="btn btn-sm btn-outline-success">Mostrar</button>
                                <?php endif; ?>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="text-center p-3">
                            <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                            <p class="mb-0 text-muted fst-italic">Aún no hay reseña registrada para este contrato.</p>
                            <?php if ($contrato['estado_codigo'] == 'FINALIZADO'): ?>
                                <small class="text-warning">El estudiante ya puede dejar su reseña.</small>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Tarjetas Laterales -->
        <div class="col-lg-4">
            
            <!-- Inquilino -->
            <div class="card shadow mb-4 border-bottom-primary">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-check me-2"></i>Inquilino (Estudiante)</h6>
                </div>
                <div class="card-body text-center">
                    <?php if (!empty($contrato['usuario_foto'])): ?>
                        <img src="<?php echo htmlspecialchars($contrato['usuario_foto']); ?>" class="rounded-circle img-thumbnail mb-3" style="width: 80px; height: 80px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-user fa-2x text-white"></i>
                        </div>
                    <?php endif; ?>
                    <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($contrato['nombres'] . ' ' . $contrato['apellido_paterno']); ?></h5>
                    <p class="text-muted small mb-3"><?php echo htmlspecialchars($contrato['correo']); ?></p>
                    <p class="mb-0"><i class="fas fa-phone text-muted me-2"></i> <?php echo htmlspecialchars($contrato['celular'] ?? 'N/A'); ?></p>
                </div>
            </div>

            <!-- Alojamiento y Propietario -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-home me-2"></i>Alojamiento Rentado</h6>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold mb-1">
                        <a href="/admin/alojamientos/ver?id=<?php echo $contrato['alojamiento_id']; ?>"><?php echo htmlspecialchars('['.$contrato['alojamiento_codigo'].'] ' . $contrato['alojamiento_titulo']); ?></a>
                    </h6>
                    <p class="small text-muted mb-3"><i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($contrato['alojamiento_direccion']); ?></p>
                    <hr>
                    <p class="mb-1 small fw-bold text-uppercase text-muted">Propietario (Arrendador)</p>
                    <p class="mb-1"><?php echo htmlspecialchars($contrato['propietario_nombres'] . ' ' . $contrato['propietario_apellido']); ?></p>
                    <p class="mb-0 small text-primary"><i class="fas fa-envelope me-1"></i> <a href="mailto:<?php echo $contrato['propietario_correo']; ?>"><?php echo htmlspecialchars($contrato['propietario_correo']); ?></a></p>
                </div>
            </div>

            <!-- Reserva Origen -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-link me-2"></i>Reserva Origen</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>ID Reserva:</strong> <a href="/admin/reservas/ver?id=<?php echo $contrato['reserva_id']; ?>">R-<?php echo $contrato['reserva_id']; ?></a></p>
                    <p class="mb-1"><strong>Solicitada el:</strong> <?php echo date('d/m/Y', strtotime($contrato['fecha_solicitud'])); ?></p>
                    <p class="mb-0"><strong>Mensaje Original:</strong> <span class="text-muted fst-italic">"<?php echo htmlspecialchars(substr($contrato['mensaje_presentacion'] ?? '', 0, 50)); ?>..."</span></p>
                </div>
            </div>

        </div>
    </div>
</div>
