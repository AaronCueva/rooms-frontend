<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">
            <a href="/admin/universidades" class="text-decoration-none text-secondary me-2">
                <i class="fas fa-arrow-left"></i>
            </a>
            Detalle de la Universidad
        </h1>
        <a href="/admin/universidades/editar?id=<?php echo $universidad['universidad_id']; ?>" class="btn btn-sm btn-info shadow-sm text-white">
            <i class="fas fa-edit fa-sm text-white-50"></i> Editar Información
        </a>
    </div>

    <div class="row">
        <!-- Tarjeta de Info Principal -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-university fa-4x text-gray-300"></i>
                    </div>
                    <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($universidad['nombre']); ?></h5>
                    <p class="text-muted small mb-3"><?php echo htmlspecialchars($universidad['distrito_nombre']); ?></p>
                    
                    <?php if ($universidad['verificado']): ?>
                        <span class="badge bg-success mb-3 p-2"><i class="fas fa-check-circle"></i> Verificada</span>
                    <?php endif; ?>
                    
                    <hr>
                    <div class="text-start">
                        <p class="small text-muted text-uppercase fw-bold mb-1">Dirección</p>
                        <p class="mb-3"><?php echo htmlspecialchars($universidad['direccion']); ?></p>

                        <p class="small text-muted text-uppercase fw-bold mb-1">Descripción</p>
                        <p class="mb-0" style="font-size: 0.9rem;"><?php echo htmlspecialchars($universidad['descripcion']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alojamientos Relacionados -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Alojamientos Cercanos Relacionados</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Alojamiento</th>
                                    <th>Propietario</th>
                                    <th>Precio</th>
                                    <th>Distancia</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($alojamientos) && count($alojamientos) > 0): ?>
                                    <?php foreach($alojamientos as $al): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($al['codigo']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($al['titulo']); ?></td>
                                            <td><?php echo htmlspecialchars($al['nombres'] . ' ' . $al['apellido_paterno']); ?></td>
                                            <td><?php echo htmlspecialchars($al['precio_mensual']); ?></td>
                                            <td><strong class="text-primary"><?php echo htmlspecialchars($al['distancia_km']); ?> km</strong></td>
                                            <td>
                                                <a href="/admin/alojamientos/ver?id=<?php echo $al['alojamiento_id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Ver
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No se han vinculado alojamientos a esta universidad.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
