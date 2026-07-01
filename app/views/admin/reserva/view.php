<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <a href="/admin/reservas" class="text-decoration-none text-secondary me-2"><i class="fas fa-arrow-left"></i></a>
            <?php echo htmlspecialchars($titulo); ?>
        </h1>
        <div>
            <a href="/admin/reservas/editar?id=<?php echo $reserva['reserva_id']; ?>" class="btn btn-sm btn-info shadow-sm text-white">
                <i class="fas fa-edit fa-sm text-white-50"></i> Editar
            </a>
            <?php if ($reserva['estado_codigo'] == 'APROBADA'): ?>
                <a href="/admin/contratos/crear?reserva_id=<?php echo $reserva['reserva_id']; ?>" class="btn btn-sm btn-success shadow-sm">
                    <i class="fas fa-file-signature fa-sm text-white-50"></i> Generar Contrato
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Detalles de la Reserva -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Información General</h6>
                    <?php 
                        $estado_clase = 'bg-secondary';
                        if($reserva['estado_codigo'] == 'PENDIENTE') $estado_clase = 'bg-warning text-dark';
                        if($reserva['estado_codigo'] == 'APROBADA') $estado_clase = 'bg-success';
                        if($reserva['estado_codigo'] == 'RECHAZADA') $estado_clase = 'bg-danger';
                        if($reserva['estado_codigo'] == 'FINALIZADA') $estado_clase = 'bg-info text-dark';
                    ?>
                    <span class="badge <?php echo $estado_clase; ?> px-3 py-2" style="font-size: 0.9rem;">
                        <?php echo htmlspecialchars($reserva['estado_codigo']); ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <small class="text-muted text-uppercase fw-bold d-block">Fecha Solicitud</small>
                            <span><?php echo date('d/m/Y H:i', strtotime($reserva['fecha_solicitud'])); ?></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted text-uppercase fw-bold d-block">Fecha Ingreso</small>
                            <span><i class="fas fa-calendar-check text-primary me-1"></i> <?php echo date('d/m/Y', strtotime($reserva['fecha_ingreso'])); ?></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted text-uppercase fw-bold d-block">Duración Propuesta</small>
                            <span><i class="fas fa-clock text-primary me-1"></i> <?php echo $reserva['duracion_meses']; ?> meses</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <small class="text-muted text-uppercase fw-bold d-block mb-2">Mensaje de Presentación</small>
                        <div class="p-3 bg-light rounded border border-left-primary">
                            <?php if (!empty($reserva['mensaje_presentacion'])): ?>
                                <p class="mb-0" style="white-space: pre-wrap; font-style: italic;">"<?php echo htmlspecialchars($reserva['mensaje_presentacion']); ?>"</p>
                            <?php else: ?>
                                <p class="mb-0 text-muted fst-italic">El estudiante no dejó ningún mensaje de presentación.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted text-uppercase fw-bold d-block">Fecha de Respuesta (Propietario)</small>
                            <span><?php echo !empty($reserva['fecha_respuesta']) ? date('d/m/Y H:i', strtotime($reserva['fecha_respuesta'])) : '<span class="text-muted fst-italic">Aún sin respuesta</span>'; ?></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted text-uppercase fw-bold d-block">Monto Total Estimado</small>
                            <span class="fs-5 fw-bold text-success"><?php echo number_format($reserva['monto_total'], 2); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjetas Laterales -->
        <div class="col-lg-4">
            
            <!-- Estudiante -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-graduate me-2"></i>Postulante (Estudiante)</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <?php if (!empty($reserva['usuario_foto'])): ?>
                            <img src="<?php echo htmlspecialchars($reserva['usuario_foto']); ?>" class="rounded-circle img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px;">
                                <i class="fas fa-user fa-3x text-white"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h5 class="text-center fw-bold"><?php echo htmlspecialchars($reserva['nombres'] . ' ' . $reserva['apellido_paterno'] . ' ' . $reserva['apellido_materno']); ?></h5>
                    <hr>
                    <p class="mb-1"><i class="fas fa-envelope text-muted me-2"></i> <a href="mailto:<?php echo $reserva['correo']; ?>"><?php echo htmlspecialchars($reserva['correo']); ?></a></p>
                    <p class="mb-0"><i class="fas fa-phone text-muted me-2"></i> <?php echo htmlspecialchars($reserva['celular'] ?? 'No registrado'); ?></p>
                </div>
            </div>

            <!-- Alojamiento y Propietario -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-home me-2"></i>Alojamiento de Interés</h6>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold mb-1">
                        <a href="/admin/alojamientos/ver?id=<?php echo $reserva['alojamiento_id']; ?>"><?php echo htmlspecialchars('['.$reserva['alojamiento_codigo'].'] ' . $reserva['alojamiento_titulo']); ?></a>
                    </h6>
                    <p class="small text-muted mb-3"><i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($reserva['alojamiento_direccion']); ?></p>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Precio Base:</span>
                        <span class="fw-bold"><?php echo htmlspecialchars($reserva['precio_mensual'] . ' ' . $reserva['moneda_codigo']); ?> / mes</span>
                    </div>
                    <hr>
                    <p class="mb-1 small fw-bold text-uppercase text-muted">Propietario</p>
                    <p class="mb-1"><?php echo htmlspecialchars($reserva['propietario_nombres'] . ' ' . $reserva['propietario_apellido']); ?></p>
                    <p class="mb-0 small text-primary"><i class="fas fa-envelope me-1"></i> <a href="mailto:<?php echo $reserva['propietario_correo']; ?>"><?php echo htmlspecialchars($reserva['propietario_correo']); ?></a></p>
                </div>
            </div>

        </div>
    </div>
</div>
