<div class="modal-body p-3" style="max-height: 75vh; overflow-y: auto;">
    <div class="row g-3">
        <!-- Columna Izquierda (4 cols): Datos del Estudiante y Alojamiento -->
        <div class="col-md-4 border-end-md pe-md-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="avatar-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-6" style="width: 40px; height: 40px; flex-shrink: 0;">
                    <?php 
                        $iniciales = strtoupper(substr($item['nombres'] ?? 'E', 0, 1) . substr($item['apellido_paterno'] ?? 'S', 0, 1));
                        echo $iniciales;
                    ?>
                </div>
                <div class="overflow-hidden">
                    <h6 class="fw-bold mb-0 text-dark small text-truncate"><?php echo htmlspecialchars(($item['nombres'] ?? 'Estudiante') . ' ' . ($item['apellido_paterno'] ?? '')); ?></h6>
                    <span class="badge bg-primary-subtle text-primary" style="font-size: 0.65rem;">Estudiante</span>
                </div>
            </div>

            <div class="mb-2" style="font-size: 0.8rem;">
                <div class="text-secondary fw-semibold"><i class="fas fa-envelope me-1 text-muted"></i> Correo</div>
                <div class="text-dark text-truncate" title="<?php echo htmlspecialchars($item['correo'] ?? ''); ?>"><?php echo htmlspecialchars($item['correo'] ?? 'No especificado'); ?></div>
            </div>

            <?php if (!empty($item['celular'])): ?>
            <div class="mb-2" style="font-size: 0.8rem;">
                <div class="text-secondary fw-semibold"><i class="fas fa-phone me-1 text-muted"></i> Teléfono</div>
                <div class="text-dark"><?php echo htmlspecialchars($item['celular']); ?></div>
            </div>
            <?php endif; ?>

            <hr class="my-2">

            <h6 class="fw-bold text-dark text-uppercase mb-2" style="font-size: 0.75rem;"><i class="fas fa-home me-1 text-secondary"></i> Alojamiento</h6>
            
            <div class="p-2 bg-light rounded-2 mb-2 border">
                <strong class="d-block text-dark small mb-1 text-truncate" title="<?php echo htmlspecialchars($item['alojamiento_titulo'] ?? ''); ?>"><?php echo htmlspecialchars($item['alojamiento_titulo'] ?? 'Alojamiento'); ?></strong>
                <?php if (!empty($item['alojamiento_direccion'])): ?>
                    <p class="text-muted mb-1 text-truncate" style="font-size: 0.72rem;" title="<?php echo htmlspecialchars($item['alojamiento_direccion']); ?>"><i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($item['alojamiento_direccion']); ?></p>
                <?php endif; ?>
                <?php if (!empty($item['precio_mensual'])): ?>
                    <span class="badge bg-success-subtle text-success" style="font-size: 0.68rem;">Renta: S/ <?php echo htmlspecialchars($item['precio_mensual']); ?>/mes</span>
                <?php endif; ?>
            </div>

            <?php if (!empty($item['contrato_id'])): ?>
                <div class="p-2 bg-info-subtle border border-info-subtle rounded-2 text-dark mb-2" style="font-size: 0.75rem;">
                    <div class="fw-bold text-info-emphasis mb-1"><i class="fas fa-file-contract me-1"></i> Contrato Alquiler</div>
                    <div class="d-flex justify-content-between">
                        <span><strong>Inicio:</strong> <?php echo date('d/m/y', strtotime($item['contrato_fecha_inicio'])); ?></span>
                        <span><strong>Fin:</strong> <?php echo date('d/m/y', strtotime($item['contrato_fecha_fin'])); ?></span>
                    </div>
                    <?php if (!empty($item['monto_renta'])): ?>
                        <div class="mt-1"><strong>Monto:</strong> S/ <?php echo htmlspecialchars($item['monto_renta']); ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($item['prop_nombres'])): ?>
                <div class="text-muted text-truncate" style="font-size: 0.72rem;" title="<?php echo htmlspecialchars($item['prop_nombres'] . ' ' . ($item['prop_ap'] ?? '') . ' (' . ($item['prop_correo'] ?? '') . ')'); ?>">
                    <strong>Propietario:</strong> <?php echo htmlspecialchars($item['prop_nombres'] . ' ' . ($item['prop_ap'] ?? '')); ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Columna Derecha (8 cols): Contenido de la Reseña y Moderación -->
        <div class="col-md-8 ps-md-3 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">Calificación:</span>
                        <div class="text-warning fs-6">
                            <?php 
                                $calif = (int)($item['calificacion'] ?? 0);
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $calif) {
                                        echo '<i class="fas fa-star"></i>';
                                    } else {
                                        echo '<i class="far fa-star text-muted opacity-25"></i>';
                                    }
                                }
                            ?>
                            <span class="fw-bold text-dark ms-1">(<?php echo $calif; ?>/5)</span>
                        </div>
                    </div>
                    <div class="text-end" style="font-size: 0.8rem;">
                        <span class="text-muted">Publicada:</span>
                        <strong class="text-dark ms-1"><?php echo date('d/m/Y H:i', strtotime($item['fecha_creado'] ?? 'now')); ?></strong>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="fw-bold text-dark small text-uppercase mb-0" style="font-size: 0.75rem;"><i class="fas fa-comment-dots me-1 text-primary"></i> Comentario del Estudiante</h6>
                        <?php 
                            $cod = $item['estado_codigo'] ?? '';
                            $hab = $item['habilitado'] ?? true;
                            if (!$hab || $cod === 'ESRA004'):
                        ?>
                            <span class="badge bg-dark">Oculto</span>
                        <?php elseif ($cod === 'ESRA003'): ?>
                            <span class="badge bg-danger">Reportado</span>
                        <?php elseif ($cod === 'ESRS004'): ?>
                            <span class="badge bg-warning text-dark">Pendiente</span>
                        <?php else: ?>
                            <span class="badge bg-success">Activo</span>
                        <?php endif; ?>
                    </div>

                    <div class="p-3 bg-light rounded-2 border-start border-3 border-warning text-dark shadow-sm" style="white-space: pre-wrap; font-size: 0.9rem; max-height: 180px; overflow-y: auto;">"<?php echo htmlspecialchars($item['comentario'] ?? 'Sin comentario de texto.'); ?>"</div>
                </div>

                <?php if (!empty($item['respuesta_propietario'])): ?>
                    <div class="mb-3">
                        <h6 class="fw-bold text-success text-uppercase mb-1" style="font-size: 0.75rem;"><i class="fas fa-reply me-1"></i> Réplica del Propietario</h6>
                        <div class="p-2 px-3 bg-success-subtle text-success-emphasis rounded-2 border border-success-subtle" style="white-space: pre-wrap; font-size: 0.85rem; max-height: 100px; overflow-y: auto;">"<?php echo htmlspecialchars($item['respuesta_propietario']); ?>"</div>
                    </div>
                <?php else: ?>
                    <div class="mb-3 p-2 bg-light rounded-2 text-muted text-center font-monospace" style="font-size: 0.75rem;">
                        <i class="fas fa-comment-slash me-1"></i> El propietario aún no ha respondido a esta reseña.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Panel de Decisiones de Moderación Compacto en una sola fila -->
            <div class="p-2 px-3 bg-light border rounded-2 mt-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-1">
                    <i class="fas fa-shield-alt text-primary fs-6 me-1"></i>
                    <span class="fw-bold text-dark small">Moderación:</span>
                    <span class="text-muted" style="font-size: 0.75rem;">Cambiar estado público</span>
                </div>
                
                <div class="d-flex flex-wrap gap-2">
                    <?php if ($cod !== 'ESRA001' || !$hab): ?>
                        <form action="/admin/resenas/cambiar-estado" method="POST" class="form-confirm mb-0" data-title="¿Aprobar reseña?" data-text="La reseña se marcará como ACTIVA y será visible públicamente en la plataforma." data-icon="question" data-confirm-text="Sí, activar">
                            <input type="hidden" name="id" value="<?php echo $item['resena_id']; ?>">
                            <input type="hidden" name="estado_codigo" value="ESRA001">
                            <button type="submit" class="btn btn-sm btn-success fw-semibold py-1 px-2" style="font-size: 0.8rem;">
                                <i class="fas fa-check-circle me-1"></i> Aprobar / Activar
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if ($cod !== 'ESRA003'): ?>
                        <form action="/admin/resenas/cambiar-estado" method="POST" class="form-confirm mb-0" data-title="¿Marcar como reportada?" data-text="La reseña se marcará en revisión por reporte." data-icon="question" data-confirm-text="Sí, reportar">
                            <input type="hidden" name="id" value="<?php echo $item['resena_id']; ?>">
                            <input type="hidden" name="estado_codigo" value="ESRA003">
                            <button type="submit" class="btn btn-sm btn-outline-warning text-dark fw-semibold py-1 px-2" style="font-size: 0.8rem;">
                                <i class="fas fa-exclamation-triangle me-1"></i> Marcar Reportada
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if ($cod !== 'ESRA004' && $hab): ?>
                        <form action="/admin/resenas/cambiar-estado" method="POST" class="form-confirm mb-0" data-title="¿Ocultar reseña?" data-text="La reseña será retirada inmediatamente de la vista pública por violar las normas." data-icon="question" data-confirm-text="Sí, ocultar">
                            <input type="hidden" name="id" value="<?php echo $item['resena_id']; ?>">
                            <input type="hidden" name="estado_codigo" value="ESRA004">
                            <button type="submit" class="btn btn-sm btn-danger fw-semibold py-1 px-2" style="font-size: 0.8rem;">
                                <i class="fas fa-eye-slash me-1"></i> Ocultar
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer bg-light px-3 py-2 border-top">
    <button type="button" class="btn btn-sm btn-secondary px-3" data-bs-dismiss="modal">Cerrar</button>
</div>
