<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">
            <a href="/admin/alojamientos" class="text-decoration-none text-secondary me-2">
                <i class="fas fa-arrow-left"></i>
            </a>
            <?php echo htmlspecialchars($titulo); ?>
        </h1>
        
        <div>
            <?php if ($alojamiento['estado_codigo'] !== 'EPA003'): ?>
                <form action="/admin/alojamientos/aprobar" method="POST" class="d-inline form-confirm" data-title="¿Aprobar esta publicación?" data-icon="question">
                    <input type="hidden" name="id" value="<?php echo $alojamiento['alojamiento_id']; ?>">
                    <button type="submit" class="btn btn-sm btn-success shadow-sm">
                        <i class="fas fa-check-circle fa-sm text-white-50"></i> Aprobar Alojamiento
                    </button>
                </form>
            <?php endif; ?>
            <a href="/admin/alojamientos/editar?id=<?php echo $alojamiento['alojamiento_id']; ?>" class="btn btn-sm btn-info shadow-sm text-white">
                <i class="fas fa-edit fa-sm text-white-50"></i> Editar General
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header border-bottom-0">
            <ul class="nav nav-tabs card-header-tabs" id="alojamientoTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">General</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="imagenes-tab" data-bs-toggle="tab" data-bs-target="#imagenes" type="button" role="tab">Imágenes (<?php echo count($imagenes ?? []); ?>)</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="servicios-tab" data-bs-toggle="tab" data-bs-target="#servicios" type="button" role="tab">Servicios Extras</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="politica-tab" data-bs-toggle="tab" data-bs-target="#politica" type="button" role="tab">Política</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="descuentos-tab" data-bs-toggle="tab" data-bs-target="#descuentos" type="button" role="tab">Descuentos</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="beneficios-tab" data-bs-toggle="tab" data-bs-target="#beneficios" type="button" role="tab">Beneficios</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="resenas-tab" data-bs-toggle="tab" data-bs-target="#resenas" type="button" role="tab">Reseñas (<?php echo count($resenas ?? []); ?>)</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="favoritos-tab" data-bs-toggle="tab" data-bs-target="#favoritos" type="button" role="tab">Favoritos (<?php echo $alojamiento['total_favoritos']; ?>)</button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="alojamientoTabsContent">
                
                <!-- TAB: GENERAL -->
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h6 class="fw-bold text-primary border-bottom pb-2">Información Principal</h6>
                            <table class="table table-sm table-borderless">
                                <tr><th width="35%">Código:</th><td><?php echo htmlspecialchars($alojamiento['codigo']); ?></td></tr>
                                <tr><th>Tipo:</th><td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($alojamiento['tipo_nombre']); ?></span></td></tr>
                                <tr><th>Estado:</th><td><span class="badge <?php echo ($alojamiento['estado_codigo']=='APROBADO') ? 'bg-success' : 'bg-warning text-dark'; ?>"><?php echo htmlspecialchars($alojamiento['estado_nombre']); ?></span></td></tr>
                                <tr><th>Habitaciones:</th><td><?php echo $alojamiento['numero_habitaciones']; ?></td></tr>
                                <tr><th>Baños:</th><td><?php echo $alojamiento['numero_banos']; ?></td></tr>
                                <tr><th>Baño Privado:</th><td><?php echo $alojamiento['bano_privado'] ? '<i class="fas fa-check text-success"></i> Sí' : '<i class="fas fa-times text-danger"></i> No'; ?></td></tr>
                                <tr><th>Tamaño:</th><td><?php echo $alojamiento['tamano_m2']; ?> m²</td></tr>
                                <tr><th>Distrito:</th><td><?php echo htmlspecialchars($alojamiento['distrito_nombre']); ?></td></tr>
                                <tr><th>Dirección:</th><td><?php echo htmlspecialchars($alojamiento['direccion']); ?></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6 mb-4">
                            <h6 class="fw-bold text-primary border-bottom pb-2">Precios y Restricciones</h6>
                            <table class="table table-sm table-borderless">
                                <tr><th width="35%">Precio:</th><td><strong><?php echo htmlspecialchars($alojamiento['precio_mensual'] . ' ' . $alojamiento['moneda_nombre']); ?> /mes</strong></td></tr>
                                <tr><th>Garantía:</th><td><?php echo htmlspecialchars($alojamiento['garantia'] . ' ' . $alojamiento['moneda_nombre']); ?></td></tr>
                                <tr><th>Precio Servicios:</th><td><?php echo htmlspecialchars($alojamiento['precio_servicios'] . ' ' . $alojamiento['moneda_nombre']); ?></td></tr>
                                <tr><th>Duración Mínima:</th><td><?php echo $alojamiento['duracion_minima_meses']; ?> meses</td></tr>
                                <tr><th>Género:</th><td><?php echo htmlspecialchars($alojamiento['genero_exclusivo_nombre'] ?? 'Mixto'); ?></td></tr>
                                <tr><th>Mascotas:</th><td><?php echo $alojamiento['mascotas_permitidas'] ? '<i class="fas fa-check text-success"></i> Sí' : '<i class="fas fa-times text-danger"></i> No'; ?></td></tr>
                                <tr><th>Fumadores:</th><td><?php echo $alojamiento['fumadores_permitidos'] ? '<i class="fas fa-check text-success"></i> Sí' : '<i class="fas fa-times text-danger"></i> No'; ?></td></tr>
                                <tr><th>Amoblado:</th><td><?php echo $alojamiento['amoblado'] ? '<i class="fas fa-check text-success"></i> Sí' : '<i class="fas fa-times text-danger"></i> No'; ?></td></tr>
                            </table>
                        </div>
                        <div class="col-12">
                            <h6 class="fw-bold text-primary border-bottom pb-2">Descripción</h6>
                            <p style="white-space: pre-wrap;"><?php echo htmlspecialchars($alojamiento['descripcion']); ?></p>
                        </div>
                        <?php if (!empty($alojamiento['latitud']) && !empty($alojamiento['longitud'])): ?>
                        <div class="col-12">
                            <h6 class="fw-bold text-primary border-bottom pb-2"><i class="fas fa-map-marker-alt me-1"></i> Ubicación en el Mapa</h6>
                            <div id="mapaDetalle" style="height: 300px; border-radius: 8px; border: 2px solid #dee2e6;"></div>
                            <small class="text-muted mt-1 d-block">Lat: <?php echo $alojamiento['latitud']; ?>, Lng: <?php echo $alojamiento['longitud']; ?></small>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- TAB: IMÁGENES -->
                <div class="tab-pane fade" id="imagenes" role="tabpanel">
                    <div class="d-flex justify-content-between mb-3">
                        <h6 class="fw-bold text-primary">Galería de Imágenes</h6>
                    </div>
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <form action="/admin/alojamientos/imagen/subir" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="alojamiento_id" value="<?php echo $alojamiento['alojamiento_id']; ?>">
                                <div class="row align-items-end">
                                    <div class="col-md-8">
                                        <label class="form-label small fw-bold">Subir Imágenes (máx 5MB c/u)</label>
                                        <input type="file" name="imagenes[]" class="form-control form-control-sm" multiple accept="image/*" required>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fas fa-upload me-1"></i> Subir</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <?php if (!empty($imagenes)): ?>
                            <?php foreach ($imagenes as $img): ?>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card shadow-sm h-100">
                                        <img src="<?php echo htmlspecialchars($img['url']); ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="<?php echo htmlspecialchars($img['nombre']); ?>">
                                        <div class="card-body p-2 text-center">
                                            <small class="text-muted d-block text-truncate"><?php echo htmlspecialchars($img['nombre']); ?></small>
                                            <form action="/admin/alojamientos/imagen/eliminar" method="POST" class="form-confirm mt-1" data-title="¿Eliminar esta imagen?">
                                                <input type="hidden" name="multimedia_id" value="<?php echo $img['multimedia_id']; ?>">
                                                <input type="hidden" name="alojamiento_id" value="<?php echo $alojamiento['alojamiento_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> Eliminar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12"><div class="alert alert-info">No hay imágenes cargadas para este alojamiento.</div></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- TAB: SERVICIOS -->
                <div class="tab-pane fade" id="servicios" role="tabpanel">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="fw-bold">Asignar Nuevo Servicio</h6>
                                    <form action="/admin/alojamientos/servicio/agregar" method="POST">
                                        <input type="hidden" name="alojamiento_id" value="<?php echo $alojamiento['alojamiento_id']; ?>">
                                        <div class="mb-2">
                                            <label class="form-label small">Servicio</label>
                                            <select class="form-select form-select-sm" name="servicio_id" required>
                                                <option value="">Seleccionar...</option>
                                                <?php foreach ($servicios_disponibles as $s): ?>
                                                    <option value="<?php echo $s['servicio_id']; ?>"><?php echo htmlspecialchars($s['nombre']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small">Precio Extra (Opcional)</label>
                                            <input type="number" step="0.01" class="form-control form-control-sm" name="precio" value="0">
                                        </div>
                                        <button class="btn btn-sm btn-primary w-100 mt-2" type="submit">Agregar Servicio</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr><th>Servicio</th><th>Precio Adicional</th><th>Acción</th></tr>
                                </thead>
                                <tbody>
                                    <?php if(count($servicios_asignados)>0): ?>
                                        <?php foreach($servicios_asignados as $sa): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($sa['servicio_nombre']); ?></strong><br><small class="text-muted"><?php echo htmlspecialchars($sa['servicio_descripcion']); ?></small></td>
                                                <td><?php echo $sa['precio'] > 0 ? htmlspecialchars($sa['precio']) : 'Gratis / Incluido'; ?></td>
                                                <td>
                                                    <form action="/admin/alojamientos/servicio/eliminar" method="POST" class="form-confirm" data-title="¿Eliminar servicio asignado?" data-text="Se quitará este servicio del alojamiento.">
                                                        <input type="hidden" name="alojamiento_servicio_id" value="<?php echo $sa['alojamiento_servicio_id']; ?>">
                                                        <input type="hidden" name="alojamiento_id" value="<?php echo $alojamiento['alojamiento_id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="3" class="text-center text-muted">No hay servicios extras asignados.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- TAB: POLITICA -->
                <div class="tab-pane fade" id="politica" role="tabpanel">
                    <?php if (!empty($politicas_asignadas)): ?>
                        <div class="row g-3">
                            <?php foreach ($politicas_asignadas as $pol): ?>
                                <div class="col-md-4">
                                    <div class="card border-left-info shadow-sm h-100">
                                        <div class="card-body">
                                            <h6 class="fw-bold text-info"><?php echo htmlspecialchars($pol['nombre']); ?></h6>
                                            <small class="text-muted"><?php echo htmlspecialchars($pol['codigo']); ?></small>
                                            <?php if (!empty($pol['descripcion'])): ?>
                                                <p class="mt-2 mb-0 small" style="white-space:pre-wrap;"><?php echo htmlspecialchars($pol['descripcion']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">No se han asignado Políticas de Casa a este alojamiento.</div>
                    <?php endif; ?>
                </div>

                <!-- TAB: DESCUENTOS -->
                <div class="tab-pane fade" id="descuentos" role="tabpanel">
                    <div class="d-flex justify-content-between mb-3">
                        <h6 class="fw-bold text-primary">Descuentos Aplicados</h6>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalDescuento"><i class="fas fa-plus"></i> Nuevo Descuento</button>
                    </div>
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr><th>Nombre</th><th>Motivo</th><th>Monto</th><th>Vigencia</th><th>Acciones</th></tr>
                        </thead>
                        <tbody>
                            <?php if(count($descuentos)>0): ?>
                                <?php foreach($descuentos as $desc): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($desc['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($desc['motivo']); ?></td>
                                        <td><strong class="text-success">- <?php echo htmlspecialchars($desc['monto']); ?></strong></td>
                                        <td><small>Del: <?php echo date('d/m/Y', strtotime($desc['inicio'])); ?><br>Al: <?php echo date('d/m/Y', strtotime($desc['fin'])); ?></small></td>
                                        <td>
                                            <form action="/admin/alojamientos/descuento/eliminar" method="POST" class="form-confirm" data-title="¿Eliminar descuento?">
                                                <input type="hidden" name="descuento_id" value="<?php echo $desc['descuento_id']; ?>">
                                                <input type="hidden" name="alojamiento_id" value="<?php echo $alojamiento['alojamiento_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center text-muted">No hay descuentos vigentes.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- TAB: BENEFICIOS -->
                <div class="tab-pane fade" id="beneficios" role="tabpanel">
                    <div class="d-flex justify-content-between mb-3">
                        <h6 class="fw-bold text-primary">Beneficios Ofrecidos</h6>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalBeneficio"><i class="fas fa-plus"></i> Nuevo Beneficio</button>
                    </div>
                    <div class="row">
                        <?php if(count($beneficios)>0): ?>
                            <?php foreach($beneficios as $ben): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100 shadow-sm <?php echo $ben['habilitado']?'border-left-success':'border-left-secondary'; ?>">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="fw-bold"><?php echo htmlspecialchars($ben['nombre']); ?></h6>
                                                <form action="/admin/alojamientos/beneficio/eliminar" method="POST" class="form-confirm" data-title="¿Eliminar beneficio?">
                                                    <input type="hidden" name="beneficio_id" value="<?php echo $ben['beneficio_id']; ?>">
                                                    <input type="hidden" name="alojamiento_id" value="<?php echo $alojamiento['alojamiento_id']; ?>">
                                                    <button type="submit" class="btn btn-link text-danger p-0 m-0 border-0"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </div>
                                            <p class="small text-muted mb-0 mt-2"><?php echo htmlspecialchars($ben['descripcion']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12"><div class="alert alert-info">No se han registrado beneficios adicionales.</div></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- TAB: RESEÑAS -->
                <div class="tab-pane fade" id="resenas" role="tabpanel">
                    <h6 class="fw-bold text-primary mb-3">Reseñas de Usuarios</h6>
                    <?php if (!empty($resenas)): ?>
                        <?php foreach ($resenas as $res): ?>
                            <div class="card mb-3 <?php echo $res['habilitado'] ? '' : 'border-danger opacity-50'; ?>">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong><?php echo htmlspecialchars($res['nombres'] . ' ' . $res['apellido_paterno']); ?></strong>
                                            <small class="text-muted d-block"><?php echo htmlspecialchars($res['correo']); ?></small>
                                        </div>
                                        <div class="text-end">
                                            <div class="mb-1">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?php echo $i <= $res['calificacion'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                                <?php endfor; ?>
                                                <span class="ms-1 fw-bold"><?php echo $res['calificacion']; ?>/5</span>
                                            </div>
                                            <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($res['creado'])); ?></small>
                                        </div>
                                    </div>
                                    <p class="mt-2 mb-1" style="white-space:pre-wrap;"><?php echo htmlspecialchars($res['comentario']); ?></p>
                                    <?php if (!empty($res['respuesta_propietario'])): ?>
                                        <div class="alert alert-light mt-2 mb-0 py-2 px-3">
                                            <small class="fw-bold text-primary"><i class="fas fa-reply me-1"></i>Respuesta del Propietario:</small>
                                            <p class="mb-0 small mt-1"><?php echo htmlspecialchars($res['respuesta_propietario']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                    <div class="mt-2 d-flex justify-content-end align-items-center">
                                        <form action="/admin/alojamientos/resena/toggle" method="POST" class="form-confirm" data-title="¿Cambiar estado de la reseña?" data-text="<?php echo $res['habilitado'] ? 'Se ocultará la reseña.' : 'Se volverá a mostrar.'; ?>">
                                            <input type="hidden" name="resena_id" value="<?php echo $res['resenia_alojamiento_id']; ?>">
                                            <input type="hidden" name="alojamiento_id" value="<?php echo $alojamiento['alojamiento_id']; ?>">
                                            <?php if ($res['habilitado']): ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-eye-slash me-1"></i>Ocultar</button>
                                            <?php else: ?>
                                                <button type="submit" class="btn btn-sm btn-outline-success"><i class="fas fa-eye me-1"></i>Mostrar</button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info">Aún no hay reseñas para este alojamiento.</div>
                    <?php endif; ?>
                </div>

                <!-- TAB: FAVORITOS -->
                <div class="tab-pane fade" id="favoritos" role="tabpanel">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr><th>Usuario</th><th>Correo/Teléfono</th><th>Universidad</th><th>Fecha Guardado</th></tr>
                        </thead>
                        <tbody>
                            <?php if(count($favoritos)>0): ?>
                                <?php foreach($favoritos as $fav): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($fav['nombres'] . ' ' . $fav['apellido_paterno']); ?></td>
                                        <td><?php echo htmlspecialchars($fav['correo']); ?><br><small><?php echo htmlspecialchars($fav['celular']); ?></small></td>
                                        <td><?php echo htmlspecialchars($fav['universidad_nombre'] ?? 'N/A'); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($fav['creado'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-muted">Aún nadie ha guardado este alojamiento en favoritos.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Descuento -->
<div class="modal fade" id="modalDescuento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/alojamientos/descuento/guardar" method="POST">
                <input type="hidden" name="alojamiento_id" value="<?php echo $alojamiento['alojamiento_id']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Descuento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label>Nombre del Descuento</label><input type="text" name="nombre" class="form-control" required></div>
                    <div class="mb-3"><label>Monto</label><input type="number" step="0.01" name="monto" class="form-control" required></div>
                    <div class="mb-3"><label>Motivo</label><input type="text" name="motivo" class="form-control"></div>
                    <div class="row">
                        <div class="col-6 mb-3"><label>Inicio</label><input type="date" name="inicio" class="form-control" required></div>
                        <div class="col-6 mb-3"><label>Fin</label><input type="date" name="fin" class="form-control" required></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Nuevo Beneficio -->
<div class="modal fade" id="modalBeneficio" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/alojamientos/beneficio/guardar" method="POST">
                <input type="hidden" name="alojamiento_id" value="<?php echo $alojamiento['alojamiento_id']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Beneficio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label>Nombre</label><input type="text" name="nombre" class="form-control" required></div>
                    <div class="mb-3"><label>Descripción</label><textarea name="descripcion" class="form-control" rows="3"></textarea></div>
                    <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="habilitado" checked><label class="form-check-label">Habilitado</label></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php if (!empty($alojamiento['latitud']) && !empty($alojamiento['longitud'])): ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const lat = <?php echo $alojamiento['latitud']; ?>;
    const lng = <?php echo $alojamiento['longitud']; ?>;
    const map = L.map('mapaDetalle').setView([lat, lng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19, attribution: '© OpenStreetMap'
    }).addTo(map);
    L.marker([lat, lng]).addTo(map)
        .bindPopup('<strong><?php echo htmlspecialchars(addslashes($alojamiento['titulo'])); ?></strong><br><?php echo htmlspecialchars(addslashes($alojamiento['direccion'])); ?>')
        .openPopup();

    // Forzar re-render si el mapa está en un tab oculto
    document.getElementById('general-tab').addEventListener('shown.bs.tab', function () {
        map.invalidateSize();
    });
});
</script>
<?php endif; ?>
