<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo htmlspecialchars($titulo); ?></h1>
        <a href="/admin/reservas/crear" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Nueva Reserva
        </a>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros de Búsqueda</h6>
        </div>
        <div class="card-body">
            <form action="/admin/reservas" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="busqueda" placeholder="Buscar por usuario o código de alojamiento..." value="<?php echo htmlspecialchars($filtros['busqueda'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="estado">
                        <option value="">Todos los estados</option>
                        <option value="PENDIENTE" <?php echo (isset($filtros['estado']) && $filtros['estado'] == 'PENDIENTE') ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="APROBADA" <?php echo (isset($filtros['estado']) && $filtros['estado'] == 'APROBADA') ? 'selected' : ''; ?>>Aprobada</option>
                        <option value="RECHAZADA" <?php echo (isset($filtros['estado']) && $filtros['estado'] == 'RECHAZADA') ? 'selected' : ''; ?>>Rechazada</option>
                        <option value="FINALIZADA" <?php echo (isset($filtros['estado']) && $filtros['estado'] == 'FINALIZADA') ? 'selected' : ''; ?>>Finalizada</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Buscar</button>
                </div>
                <div class="col-md-2">
                    <a href="/admin/reservas" class="btn btn-secondary w-100"><i class="fas fa-eraser"></i> Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Reservas -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha Solicitud</th>
                            <th>Usuario (Estudiante)</th>
                            <th>Alojamiento</th>
                            <th>Ingreso / Duración</th>
                            <th>Estado</th>
                            <th>Habilitado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($reservas) > 0): ?>
                            <?php foreach ($reservas as $r): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($r['fecha_solicitud'])); ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($r['nombres'] . ' ' . $r['apellido_paterno']); ?></strong>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($r['correo']); ?></small>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($r['alojamiento_codigo']); ?></strong>
                                        <br><small class="text-muted text-truncate d-inline-block" style="max-width: 150px;"><?php echo htmlspecialchars($r['alojamiento_titulo']); ?></small>
                                    </td>
                                    <td>
                                        <i class="fas fa-calendar-alt text-primary"></i> <?php echo date('d/m/Y', strtotime($r['fecha_ingreso'])); ?>
                                        <br><small class="text-muted"><i class="fas fa-clock text-primary"></i> <?php echo $r['duracion_meses']; ?> meses</small>
                                    </td>
                                    <td>
                                        <?php 
                                            $estado_clase = 'bg-secondary';
                                            if($r['estado_codigo'] == 'PENDIENTE') $estado_clase = 'bg-warning text-dark';
                                            if($r['estado_codigo'] == 'APROBADA') $estado_clase = 'bg-success';
                                            if($r['estado_codigo'] == 'RECHAZADA') $estado_clase = 'bg-danger';
                                            if($r['estado_codigo'] == 'FINALIZADA') $estado_clase = 'bg-info text-dark';
                                        ?>
                                        <span class="badge <?php echo $estado_clase; ?>"><?php echo htmlspecialchars($r['estado_codigo']); ?></span>
                                    </td>
                                    <td>
                                        <form action="/admin/reservas/toggle-estado" method="POST" class="form-confirm" data-title="¿Cambiar estado?">
                                            <input type="hidden" name="id" value="<?php echo $r['reserva_id']; ?>">
                                            <input type="hidden" name="estado" value="<?php echo $r['habilitado'] ? 0 : 1; ?>">
                                            <button type="submit" class="btn btn-sm <?php echo $r['habilitado'] ? 'btn-success' : 'btn-danger'; ?>">
                                                <i class="fas <?php echo $r['habilitado'] ? 'fa-check' : 'fa-times'; ?>"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="/admin/reservas/ver?id=<?php echo $r['reserva_id']; ?>" class="btn btn-sm btn-info text-white" title="Ver detalles"><i class="fas fa-eye"></i></a>
                                        <a href="/admin/reservas/editar?id=<?php echo $r['reserva_id']; ?>" class="btn btn-sm btn-primary" title="Editar"><i class="fas fa-edit"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">No se encontraron reservas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <?php if ($total_paginas > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo ($pagina <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?pagina=<?php echo $pagina - 1; ?><?php echo !empty($filtros['busqueda']) ? '&busqueda='.urlencode($filtros['busqueda']) : ''; ?><?php echo !empty($filtros['estado']) ? '&estado='.urlencode($filtros['estado']) : ''; ?>">Anterior</a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <li class="page-item <?php echo ($pagina == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?pagina=<?php echo $i; ?><?php echo !empty($filtros['busqueda']) ? '&busqueda='.urlencode($filtros['busqueda']) : ''; ?><?php echo !empty($filtros['estado']) ? '&estado='.urlencode($filtros['estado']) : ''; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?php echo ($pagina >= $total_paginas) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?pagina=<?php echo $pagina + 1; ?><?php echo !empty($filtros['busqueda']) ? '&busqueda='.urlencode($filtros['busqueda']) : ''; ?><?php echo !empty($filtros['estado']) ? '&estado='.urlencode($filtros['estado']) : ''; ?>">Siguiente</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>
