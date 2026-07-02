<div class="container-fluid">
    <!-- Header principal -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold">
                <i class="fas fa-medal text-warning me-2"></i>Gamificación, Referidos y Puntos NIDO
            </h1>
            <p class="text-muted small mb-0">Auditoría del programa de fidelización, ranking estudiantil, libro mayor y control de invitaciones</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary shadow-sm" onclick="abrirModalAjusteManual()">
                <i class="fas fa-plus-circle me-1"></i> Ajuste Manual de Puntos
            </button>
        </div>
    </div>

    <!-- Pestañas (Tabs de Navegación) -->
    <ul class="nav nav-tabs nav-fill mb-4 border-bottom shadow-sm bg-white rounded-top" id="puntosTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link py-3 fw-semibold <?php echo ($tab === 'leaderboard') ? 'active border-primary border-bottom-0 text-primary bg-light' : 'text-secondary'; ?>" 
               href="/admin/puntos?tab=leaderboard">
                <i class="fas fa-trophy me-2 text-warning"></i>Leaderboard y Saldos
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link py-3 fw-semibold <?php echo ($tab === 'movimientos') ? 'active border-primary border-bottom-0 text-primary bg-light' : 'text-secondary'; ?>" 
               href="/admin/puntos?tab=movimientos">
                <i class="fas fa-list-alt me-2 text-info"></i>Libro Mayor (Ledger Universal)
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link py-3 fw-semibold <?php echo ($tab === 'referidos') ? 'active border-primary border-bottom-0 text-primary bg-light' : 'text-secondary'; ?>" 
               href="/admin/puntos?tab=referidos">
                <i class="fas fa-user-friends me-2 text-success"></i>Programa de Referidos
            </a>
        </li>
    </ul>

    <!-- Contenido por Pestaña -->
    <div class="tab-content" id="puntosTabContent">
        
        <?php if ($tab === 'leaderboard'): ?>
            <!-- ========================================== -->
            <!-- TAB 1: LEADERBOARD Y SALDOS                -->
            <!-- ========================================== -->
            <div class="card shadow mb-4 border-0 rounded-3">
                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" action="/admin/puntos" class="row g-2 align-items-end mb-4">
                        <input type="hidden" name="tab" value="leaderboard">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold mb-1 text-secondary">Buscar Estudiante</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="busqueda" class="form-control" placeholder="Nombre o correo institucional..." value="<?php echo htmlspecialchars($filtros['busqueda'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold mb-1 text-secondary">Nivel NIDO</label>
                            <select name="nivel" class="form-select form-select-sm">
                                <option value="">Todos los niveles</option>
                                <option value="GOLD" <?php echo (($filtros['nivel'] ?? '') === 'GOLD') ? 'selected' : ''; ?>>🌟 Nido Gold (500+ pts)</option>
                                <option value="CONFIABLE" <?php echo (($filtros['nivel'] ?? '') === 'CONFIABLE') ? 'selected' : ''; ?>>🛡️ Inquilino Confiable (200-499 pts)</option>
                                <option value="NOVATO" <?php echo (($filtros['nivel'] ?? '') === 'NOVATO') ? 'selected' : ''; ?>>🌱 Novato (0-199 pts)</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary flex-fill">
                                <i class="fas fa-filter me-1"></i> Filtrar
                            </button>
                            <a href="/admin/puntos?tab=leaderboard" class="btn btn-sm btn-outline-secondary flex-fill">
                                <i class="fas fa-times me-1"></i> Limpiar
                            </a>
                        </div>
                    </form>

                    <!-- Tabla Leaderboard -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary small text-uppercase">
                                <tr>
                                    <th class="text-center" style="width: 60px;">Rank</th>
                                    <th>Estudiante</th>
                                    <th>Correo Institucional</th>
                                    <th class="text-center">Nivel NIDO</th>
                                    <th class="text-end">Saldo de Puntos</th>
                                    <th class="text-center" style="width: 150px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($leaderboard)): ?>
                                    <?php foreach ($leaderboard as $idx => $user): ?>
                                        <?php $rank = (($pagina - 1) * $por_pagina) + $idx + 1; ?>
                                        <tr>
                                            <td class="text-center fw-bold text-secondary">
                                                <?php if ($rank === 1): ?>
                                                    <span class="badge bg-warning text-dark px-2 py-1 fs-6">🥇 1</span>
                                                <?php elseif ($rank === 2): ?>
                                                    <span class="badge bg-secondary text-white px-2 py-1 fs-6">🥈 2</span>
                                                <?php elseif ($rank === 3): ?>
                                                    <span class="badge bg-info text-dark px-2 py-1 fs-6">🥉 3</span>
                                                <?php else: ?>
                                                    #<?php echo $rank; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td class="fw-semibold text-dark">
                                                <i class="fas fa-user-circle text-secondary me-2"></i>
                                                <?php echo htmlspecialchars($user['nombres'] ?? 'Estudiante'); ?>
                                            </td>
                                            <td class="text-muted small">
                                                <?php echo htmlspecialchars($user['correo'] ?? ''); ?>
                                            </td>
                                            <td class="text-center">
                                                <?php 
                                                    $nn = $user['nivel_nido'] ?? 'Novato';
                                                    if ($nn === 'Nido Gold') {
                                                        $badgeStyle = 'bg-warning text-dark border border-warning shadow-sm';
                                                        $icon = '🌟';
                                                    } elseif ($nn === 'Inquilino Confiable') {
                                                        $badgeStyle = 'bg-info text-white shadow-sm';
                                                        $icon = '🛡️';
                                                    } else {
                                                        $badgeStyle = 'bg-secondary text-white shadow-sm';
                                                        $icon = '🌱';
                                                    }
                                                ?>
                                                <span class="badge <?php echo $badgeStyle; ?> px-3 py-2 fs-6">
                                                    <?php echo $icon; ?> <?php echo htmlspecialchars($nn); ?>
                                                </span>
                                            </td>
                                            <td class="text-end fw-bold fs-6 text-primary">
                                                <?php echo number_format($user['puntos_acumulados']); ?> <span class="small text-muted fw-normal">pts</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary" title="Ver Historial de Movimientos" onclick="abrirModalLedger('<?php echo $user['usuario_id']; ?>', '<?php echo addslashes(htmlspecialchars($user['nombres'])); ?>')">
                                                        <i class="fas fa-history"></i> Ledger
                                                    </button>
                                                    <button type="button" class="btn btn-outline-success" title="Ajustar Puntos (+/-)" onclick="abrirModalAjusteManual('<?php echo $user['usuario_id']; ?>', '<?php echo addslashes(htmlspecialchars($user['nombres'])); ?>')">
                                                        <i class="fas fa-sliders-h"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fas fa-user-graduate fa-3x mb-3 d-block opacity-25"></i>
                                            No se encontraron estudiantes en el ranking con los filtros seleccionados.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <?php elseif ($tab === 'movimientos'): ?>
            <!-- ========================================== -->
            <!-- TAB 2: LIBRO MAYOR (LEDGER UNIVERSAL)      -->
            <!-- ========================================== -->
            <div class="card shadow mb-4 border-0 rounded-3">
                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" action="/admin/puntos" class="row g-2 align-items-end mb-4">
                        <input type="hidden" name="tab" value="movimientos">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold mb-1 text-secondary">Buscar Transacción</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="busqueda" class="form-control" placeholder="Estudiante, correo o descripción..." value="<?php echo htmlspecialchars($filtros['busqueda'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold mb-1 text-secondary">Tipo de Operación</label>
                            <select name="tipo_movimiento" class="form-select form-select-sm">
                                <option value="">Todos los tipos</option>
                                <?php if (!empty($tipos_movimiento)): ?>
                                    <?php foreach ($tipos_movimiento as $tm): ?>
                                        <option value="<?php echo $tm['codigo']; ?>" <?php echo (($filtros['tipo_movimiento'] ?? '') == $tm['codigo']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($tm['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary flex-fill">
                                <i class="fas fa-filter me-1"></i> Filtrar
                            </button>
                            <a href="/admin/puntos?tab=movimientos" class="btn btn-sm btn-outline-secondary flex-fill">
                                <i class="fas fa-times me-1"></i> Limpiar
                            </a>
                        </div>
                    </form>

                    <!-- Tabla Ledger -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary small text-uppercase">
                                <tr>
                                    <th>Fecha / Hora</th>
                                    <th>Estudiante</th>
                                    <th>Tipo de Movimiento</th>
                                    <th>Descripción / Justificación</th>
                                    <th class="text-end">Puntos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($movimientos)): ?>
                                    <?php foreach ($movimientos as $mov): ?>
                                        <tr>
                                            <td class="text-muted small text-nowrap">
                                                <i class="far fa-clock me-1"></i>
                                                <?php echo !empty($mov['fecha_creacion']) ? date('d/m/Y H:i', strtotime($mov['fecha_creacion'])) : (!empty($mov['creado']) ? date('d/m/Y H:i', strtotime($mov['creado'])) : '-'); ?>
                                            </td>
                                            <td class="fw-semibold text-dark">
                                                <?php echo htmlspecialchars($mov['usuario_nombres'] ?? 'Usuario Sistema'); ?>
                                                <div class="text-muted small fw-normal"><?php echo htmlspecialchars($mov['usuario_correo'] ?? ''); ?></div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border px-2 py-1">
                                                    <?php echo htmlspecialchars($mov['tipo_nombre'] ?? $mov['tipo_movimiento_codigo']); ?>
                                                </span>
                                            </td>
                                            <td class="text-secondary small">
                                                <?php echo htmlspecialchars($mov['descripcion']); ?>
                                            </td>
                                            <td class="text-end fw-bold <?php echo ($mov['puntos'] >= 0) ? 'text-success' : 'text-danger'; ?>">
                                                <?php echo ($mov['puntos'] >= 0) ? '+' : ''; ?><?php echo number_format($mov['puntos']); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fas fa-receipt fa-3x mb-3 d-block opacity-25"></i>
                                            No se encontraron transacciones en el Libro Mayor con los filtros seleccionados.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <?php elseif ($tab === 'referidos'): ?>
            <!-- ========================================== -->
            <!-- TAB 3: PROGRAMA DE REFERIDOS               -->
            <!-- ========================================== -->
            <div class="card shadow mb-4 border-0 rounded-3">
                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" action="/admin/puntos" class="row g-2 align-items-end mb-4">
                        <input type="hidden" name="tab" value="referidos">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold mb-1 text-secondary">Buscar Referido o Referidor</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="busqueda" class="form-control" placeholder="Nombre, correo o código de invitación..." value="<?php echo htmlspecialchars($filtros['busqueda'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold mb-1 text-secondary">Estado del Bono</label>
                            <select name="estado" class="form-select form-select-sm">
                                <option value="">Todos los estados</option>
                                <?php if (!empty($estados_referido)): ?>
                                    <?php foreach ($estados_referido as $est): ?>
                                        <option value="<?php echo $est['codigo']; ?>" <?php echo (($filtros['estado'] ?? '') == $est['codigo']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($est['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary flex-fill">
                                <i class="fas fa-filter me-1"></i> Filtrar
                            </button>
                            <a href="/admin/puntos?tab=referidos" class="btn btn-sm btn-outline-secondary flex-fill">
                                <i class="fas fa-times me-1"></i> Limpiar
                            </a>
                        </div>
                    </form>

                    <!-- Tabla Referidos -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary small text-uppercase">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Referidor (El que invitó)</th>
                                    <th>Referido (Nuevo estudiante)</th>
                                    <th>Código Usado</th>
                                    <th class="text-center">Estado del Bono</th>
                                    <th class="text-center" style="width: 180px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($referidos)): ?>
                                    <?php foreach ($referidos as $ref): ?>
                                        <?php
                                            $codEst = $ref['estado_codigo'] ?? '';
                                            $badgeClass = ($codEst === 'ESREF02') ? 'success' : (($codEst === 'ESREF01') ? 'warning text-dark' : 'danger');
                                            $iconoEst = ($codEst === 'ESREF02') ? 'check-circle' : (($codEst === 'ESREF01') ? 'clock' : 'times-circle');
                                        ?>
                                        <tr>
                                            <td class="text-muted small text-nowrap">
                                                <?php echo !empty($ref['fecha_creacion']) ? date('d/m/Y', strtotime($ref['fecha_creacion'])) : (!empty($ref['creado']) ? date('d/m/Y', strtotime($ref['creado'])) : '-'); ?>
                                            </td>
                                            <td class="fw-semibold text-dark">
                                                <?php echo htmlspecialchars($ref['referidor_nombres'] ?? 'Usuario Antiguo'); ?>
                                                <div class="text-muted small fw-normal"><?php echo htmlspecialchars($ref['referidor_correo'] ?? ''); ?></div>
                                            </td>
                                            <td class="fw-semibold text-primary">
                                                <i class="fas fa-user-plus me-1"></i>
                                                <?php echo htmlspecialchars($ref['referido_nombres'] ?? 'Nuevo Alumno'); ?>
                                                <div class="text-muted small fw-normal"><?php echo htmlspecialchars($ref['referido_correo'] ?? ''); ?></div>
                                            </td>
                                            <td>
                                                <span class="badge bg-dark font-monospace px-2 py-1">
                                                    <?php echo htmlspecialchars($ref['codigo'] ?? 'NIDO-REF'); ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-<?php echo $badgeClass; ?> px-3 py-2 shadow-sm">
                                                    <i class="fas fa-<?php echo $iconoEst; ?> me-1"></i>
                                                    <?php echo htmlspecialchars($ref['estado_nombre'] ?? $codEst); ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <?php if ($codEst === 'ESREF01'): ?>
                                                        <!-- Acreditar bono -->
                                                        <form action="/admin/puntos/referido/acreditar" method="POST" class="d-inline form-confirm" data-title="¿Acreditar Bonificación?" data-text="Se sumarán +200 puntos al referidor y +100 puntos al nuevo estudiante." data-icon="question" data-confirm-text="Sí, acreditar">
                                                            <input type="hidden" name="referido_id" value="<?php echo $ref['referido_id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-success" title="Aprobar y Acreditar Puntos">
                                                                <i class="fas fa-check me-1"></i> Acreditar
                                                            </button>
                                                        </form>
                                                        <!-- Anular / Rechazar -->
                                                        <form action="/admin/puntos/referido/anular" method="POST" class="d-inline form-confirm" data-title="¿Anular Invitación?" data-text="La invitación se marcará como cancelada y no se otorgarán puntos (sospecha de fraude)." data-icon="warning" data-confirm-text="Sí, anular">
                                                            <input type="hidden" name="referido_id" value="<?php echo $ref['referido_id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Anular o rechazar referido">
                                                                <i class="fas fa-ban"></i>
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="text-muted small italic">Sin acciones pendientes</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fas fa-users fa-3x mb-3 d-block opacity-25"></i>
                                            No se encontraron registros en el programa de referidos.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Paginación común -->
        <?php if (!empty($total_registros) || !empty($leaderboard) || !empty($movimientos) || !empty($referidos)): ?>
        <?php $t_pags = max(1, (int)($total_paginas ?? 1)); ?>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 p-3 bg-white border rounded-3 shadow-sm">
            <div class="small text-muted mb-2 mb-md-0">
                <i class="fas fa-layer-group me-1 text-primary"></i>
                Mostrando página <span class="fw-bold text-dark"><?php echo $pagina; ?></span> de <span class="fw-bold text-dark"><?php echo $t_pags; ?></span> 
                (<span class="fw-bold text-primary"><?php echo $total_registros ?? 0; ?></span> registros en total)
            </div>
            <nav aria-label="Paginación de Gamificación">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?php echo ($pagina <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?<?php echo http_build_query(array_merge($filtros, ['pagina' => $pagina - 1])); ?>"><i class="fas fa-chevron-left small me-1"></i>Anterior</a>
                    </li>
                    <?php for ($i = 1; $i <= $t_pags; $i++): ?>
                        <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                            <a class="page-link" href="?<?php echo http_build_query(array_merge($filtros, ['pagina' => $i])); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo ($pagina >= $t_pags) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?<?php echo http_build_query(array_merge($filtros, ['pagina' => $pagina + 1])); ?>">Siguiente<i class="fas fa-chevron-right small ms-1"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
        <?php endif; ?>

    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: AJUSTE MANUAL DE PUNTOS (+/-)       -->
<!-- ========================================== -->
<div class="modal fade" id="modalAjusteManual" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <h6 class="modal-title d-flex align-items-center gap-2 mb-0 fw-semibold">
                    <i class="fas fa-sliders-h text-warning"></i> Ajuste Manual de Puntos NIDO
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="/admin/puntos/ajuste-manual" method="POST" id="formAjusteManual">
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 px-3 small mb-3">
                        <i class="fas fa-info-circle me-1"></i> Este ajuste sumará o restará saldo directamente de la cuenta del estudiante y quedará registrado en el Libro Mayor con tu firma de usuario.
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Estudiante <span class="text-danger">*</span></label>
                        <select name="usuario_id" id="ajuste_usuario_id" class="form-select" required>
                            <option value="">-- Selecciona un estudiante --</option>
                            <?php if (!empty($estudiantes_lista)): ?>
                                <?php foreach ($estudiantes_lista as $est): ?>
                                    <option value="<?php echo $est['usuario_id']; ?>">
                                        <?php echo htmlspecialchars($est['nombres'] . ' (' . $est['correo'] . ') - ' . number_format($est['puntos_acumulados']) . ' pts'); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted" id="ajuste_usuario_nombre_display"></small>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Operación <span class="text-danger">*</span></label>
                            <select name="operacion" class="form-select" required>
                                <option value="suma">➕ Acreditar Puntos (+)</option>
                                <option value="resta">➖ Debitar Puntos (-)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Cantidad de Puntos <span class="text-danger">*</span></label>
                            <input type="number" name="puntos" class="form-control" placeholder="Ej: 200" min="1" max="10000" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Motivo / Justificación Obligatoria <span class="text-danger">*</span></label>
                        <textarea name="motivo" class="form-control" rows="3" placeholder="Ej: Premio por ganar concurso en el foro universitario..." required></textarea>
                        <div class="form-text small text-muted">Explica detalladamente por qué estás realizando este ajuste para futuras auditorías.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                        <i class="fas fa-save me-1"></i> Aplicar Ajuste
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL DINÁMICO PARA VER LEDGER INDIVIDUAL  -->
<!-- ========================================== -->
<div class="modal fade" id="modalLedgerIndividual" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <h6 class="modal-title d-flex align-items-center gap-2 mb-0 fw-semibold" id="ledgerModalTitle">
                    <i class="fas fa-history text-info"></i> Estado de Cuenta NIDO
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="ledgerModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted small mt-2">Cargando historial de transacciones...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function abrirModalAjusteManual(usuarioId = '', usuarioNombre = '') {
    const selectId = document.getElementById('ajuste_usuario_id');
    const displayNombre = document.getElementById('ajuste_usuario_nombre_display');
    
    if (usuarioId) {
        selectId.value = usuarioId;
        displayNombre.innerHTML = `<span class="badge bg-success mt-1"><i class="fas fa-check-circle me-1"></i> Seleccionado automáticamente: ${usuarioNombre}</span>`;
    } else {
        selectId.value = '';
        displayNombre.innerHTML = '<span class="text-muted small">Selecciona al alumno de la lista desplegable.</span>';
    }
    
    const modal = new bootstrap.Modal(document.getElementById('modalAjusteManual'));
    modal.show();
}

function abrirModalLedger(usuarioId, usuarioNombre) {
    document.getElementById('ledgerModalTitle').innerHTML = `<i class="fas fa-history text-info me-2"></i> Estado de Cuenta: ${usuarioNombre}`;
    const body = document.getElementById('ledgerModalBody');
    body.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="text-muted small mt-2">Cargando historial de transacciones...</p>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('modalLedgerIndividual'));
    modal.show();

    fetch('/admin/puntos/ledger-modal?usuario_id=' + usuarioId)
        .then(r => {
            if (!r.ok) throw new Error('Error de red');
            return r.text();
        })
        .then(html => {
            body.innerHTML = html;
        })
        .catch(err => {
            console.error(err);
            body.innerHTML = `<div class="alert alert-danger">Error al cargar el historial de puntos del estudiante.</div>`;
        });
}
</script>
