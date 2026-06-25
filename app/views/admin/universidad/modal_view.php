<div class="modal fade" id="modalUniversidadAjax" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header border-bottom-0">
        <h5 class="modal-title fw-bold text-primary"><i class="fas fa-university me-2"></i>Detalle de Universidad</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-0">
        <div class="text-center mb-4">
            <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($universidad['nombre']); ?></h4>
            <p class="text-muted small mb-2"><?php echo htmlspecialchars($universidad['distrito_nombre'] ?? 'Ubicación no especificada'); ?></p>
            <?php if ($universidad['verificado']): ?>
                <div class="mb-3">
                    <span class="badge bg-success p-2"><i class="fas fa-check-circle"></i> Verificada</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="row">
            <div class="col-md-5">
                <p class="small text-muted text-uppercase fw-bold mb-1">Dirección</p>
                <p class="mb-3"><?php echo htmlspecialchars($universidad['direccion'] ?: 'No especificada'); ?></p>
                <p class="small text-muted text-uppercase fw-bold mb-1">Descripción</p>
                <p class="mb-0" style="font-size: 0.9rem;"><?php echo htmlspecialchars($universidad['descripcion'] ?: 'Sin descripción'); ?></p>
            </div>
            <div class="col-md-7 border-start">
                <p class="small text-muted text-uppercase fw-bold mb-2">Alojamientos Cercanos (<?php echo count($alojamientos); ?>)</p>
                <?php if (count($alojamientos) > 0): ?>
                    <div class="list-group list-group-flush" style="max-height: 250px; overflow-y: auto;">
                        <?php foreach ($alojamientos as $al): ?>
                            <div class="list-group-item px-0 py-2">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 fw-bold" style="font-size:0.9rem;"><?php echo htmlspecialchars($al['titulo']); ?></h6>
                                    <small class="text-primary fw-bold"><?php echo htmlspecialchars($al['distancia_km']); ?> km</small>
                                </div>
                                <p class="mb-1 small text-muted">
                                    <?php echo htmlspecialchars($al['nombres'] . ' ' . $al['apellido_paterno']); ?> | 
                                    <strong><?php echo htmlspecialchars($al['precio_mensual']); ?></strong>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-light text-center small py-2 text-muted">No se han vinculado alojamientos.</div>
                <?php endif; ?>
            </div>
        </div>
      </div>
      <div class="modal-footer border-top-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <a href="/admin/universidades/editar?id=<?php echo $universidad['universidad_id']; ?>" class="btn btn-info text-white">Editar Universidad</a>
      </div>
    </div>
  </div>
</div>
