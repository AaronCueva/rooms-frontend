<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo htmlspecialchars($titulo ?? 'Gestión de Alojamientos'); ?></h1>
        <a href="/admin/alojamientos/crear" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Nuevo Alojamiento
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Listado de Alojamientos</h6>
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
        </div>
    </div>
</div>
