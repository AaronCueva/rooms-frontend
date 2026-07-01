<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo htmlspecialchars($titulo); ?></h1>
        <a href="/admin/contratos/crear" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Nuevo Contrato
        </a>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros de Búsqueda</h6>
        </div>
        <div class="card-body">
            <form action="/admin/contratos" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="busqueda" placeholder="Buscar por usuario o código de alojamiento..." value="<?php echo htmlspecialchars($filtros['busqueda'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="estado">
                        <option value="">Todos los estados</option>
                        <?php foreach ($estados_contrato as $estado): ?>
                            <?php $estado_codigo = $estado['codigo'] ?? ''; ?>
                            <option value="<?php echo htmlspecialchars($estado_codigo); ?>" <?php echo (isset($filtros['estado']) && $filtros['estado'] == $estado_codigo) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($estado['nombre'] ?? $estado_codigo); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Buscar</button>
                </div>
                <div class="col-md-2">
                    <a href="/admin/contratos" class="btn btn-secondary w-100"><i class="fas fa-eraser"></i> Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Contratos -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha Firma</th>
                            <th>Inquilino (Estudiante)</th>
                            <th>Alojamiento</th>
                            <th>Período</th>
                            <th>Estado</th>
                            <th>Doc.</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($contratos) > 0): ?>
                            <?php foreach ($contratos as $c): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($c['creado'])); ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($c['nombres'] . ' ' . $c['apellido_paterno']); ?></strong>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($c['alojamiento_codigo']); ?></strong>
                                        <br><small class="text-muted text-truncate d-inline-block" style="max-width: 150px;"><?php echo htmlspecialchars($c['alojamiento_titulo']); ?></small>
                                    </td>
                                    <td>
                                        <small class="d-block"><strong>Inicio:</strong> <?php echo date('d/m/Y', strtotime($c['fecha_inicio'])); ?></small>
                                        <small class="d-block"><strong>Fin:</strong> <?php echo date('d/m/Y', strtotime($c['fecha_fin'])); ?></small>
                                    </td>
                                    <td>
                                        <?php 
                                            $estado_nombre = $c['estado_codigo'];
                                            foreach (($estados_contrato ?? []) as $estado) {
                                                if (($estado['codigo'] ?? '') === $c['estado_codigo']) {
                                                    $estado_nombre = $estado['nombre'] ?? $estado['codigo'] ?? $c['estado_codigo'];
                                                    break;
                                                }
                                            }

                                            $estado_clase = 'bg-secondary';
                                            if (in_array($c['estado_codigo'], ['ESCO001', 'ACTIVO'], true)) $estado_clase = 'bg-success';
                                            if (in_array($c['estado_codigo'], ['ESCO002', 'FINALIZADO'], true)) $estado_clase = 'bg-info text-dark';
                                            if (in_array($c['estado_codigo'], ['ESCO003', 'CANCELADO'], true)) $estado_clase = 'bg-danger';
                                        ?>
                                        <span class="badge <?php echo $estado_clase; ?>"><?php echo htmlspecialchars($estado_nombre); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($c['documento_url'])): ?>
                                            <a href="<?php echo htmlspecialchars($c['documento_url']); ?>" target="_blank" class="text-primary" title="Ver Documento"><i class="fas fa-file-pdf fa-lg"></i></a>
                                        <?php else: ?>
                                            <span class="text-muted"><i class="fas fa-times"></i></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="/admin/contratos/ver?id=<?php echo $c['contrato_id']; ?>" class="btn btn-sm btn-info text-white" title="Ver detalles"><i class="fas fa-eye"></i></a>
                                        <a href="/admin/contratos/editar?id=<?php echo $c['contrato_id']; ?>" class="btn btn-sm btn-primary" title="Editar"><i class="fas fa-edit"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">No se encontraron contratos.</td>
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
