<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo htmlspecialchars($titulo ?? 'Gestión de Alojamientos'); ?></h1>
        <a href="/admin/alojamientos/crear" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Nuevo Alojamiento
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="/admin/alojamientos" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold mb-1">Buscar</label>
                    <input type="text" name="busqueda" class="form-control form-control-sm" placeholder="Título o código..." value="<?php echo htmlspecialchars($filtros['busqueda'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold mb-1">Estado</label>
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <?php foreach ($estados as $est): ?>
                            <option value="<?php echo $est['codigo']; ?>" <?php echo (isset($filtros['estado']) && $filtros['estado'] == $est['codigo']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($est['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">
                        <i class="fas fa-search me-1"></i> Filtrar
                    </button>
                    <a href="/admin/alojamientos" class="btn btn-sm btn-outline-secondary flex-fill">
                        <i class="fas fa-times me-1"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Listado de Alojamientos (<?php echo $total ?? 0; ?>)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Título</th>
                            <th>Propietario</th>
                            <th>Tipo</th>
                            <th>Ubicación</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($alojamientos) && count($alojamientos) > 0): ?>
                            <?php foreach ($alojamientos as $item): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($item['codigo']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($item['titulo']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($item['nombres'] . ' ' . $item['apellido_paterno']); ?><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($item['correo']); ?></small>
                                    </td>
                                    <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($item['tipo_nombre'] ?? 'N/A'); ?></span></td>
                                    <td><?php echo htmlspecialchars($item['distrito_nombre'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($item['precio_mensual']); ?></td>
                                    <td>
                                        <?php if ($item['habilitado'] == 1): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Oculto</span>
                                        <?php endif; ?>
                                        <?php if ($item['estado_codigo'] === 'APROBADO'): ?>
                                            <span class="badge bg-primary">Aprobado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="/admin/alojamientos/ver?id=<?php echo $item['alojamiento_id']; ?>" class="btn btn-sm btn-primary" title="Ver Detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="/admin/alojamientos/editar?id=<?php echo $item['alojamiento_id']; ?>" class="btn btn-sm btn-info text-white" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="/admin/alojamientos/toggle-estado" method="POST" class="d-inline form-confirm"
                                                  data-title="¿Cambiar estado?" 
                                                  data-text="Esta acción modificará la visibilidad del alojamiento.">
                                                <input type="hidden" name="id" value="<?php echo $item['alojamiento_id']; ?>">
                                                <input type="hidden" name="estado" value="<?php echo ($item['habilitado'] == 1) ? 0 : 1; ?>">
                                                <?php if ($item['habilitado'] == 1): ?>
                                                    <button type="submit" class="btn btn-sm btn-warning" title="Ocultar">
                                                        <i class="fas fa-eye-slash"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="submit" class="btn btn-sm btn-success" title="Activar">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    No hay alojamientos registrados.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <?php if (isset($total_paginas) && $total_paginas > 1): ?>
            <div class="d-flex justify-content-center mt-4">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?php echo ($pagina <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $pagina - 1; ?>&busqueda=<?php echo urlencode($filtros['busqueda'] ?? ''); ?>&estado=<?php echo urlencode($filtros['estado'] ?? ''); ?>">Anterior</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <li class="page-item <?php echo ($pagina == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="?pagina=<?php echo $i; ?>&busqueda=<?php echo urlencode($filtros['busqueda'] ?? ''); ?>&estado=<?php echo urlencode($filtros['estado'] ?? ''); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo ($pagina >= $total_paginas) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $pagina + 1; ?>&busqueda=<?php echo urlencode($filtros['busqueda'] ?? ''); ?>&estado=<?php echo urlencode($filtros['estado'] ?? ''); ?>">Siguiente</a>
                    </li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
