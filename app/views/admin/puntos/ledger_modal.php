<?php if (!empty($movimientos)): ?>
    <?php
        $saldo_total = 0;
        foreach ($movimientos as $m) {
            $saldo_total += (int)$m['puntos'];
        }
    ?>
    <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3 mb-3 border">
        <div>
            <h6 class="mb-0 fw-bold text-dark">Total Acumulado en Ledger</h6>
            <small class="text-muted">Balance histórico calculado por transacciones</small>
        </div>
        <div class="fs-4 fw-bold text-primary">
            <?php echo number_format($saldo_total); ?> <span class="fs-6 fw-normal text-muted">pts</span>
        </div>
    </div>

    <div class="table-responsive" style="max-height: 400px;">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light text-secondary small text-uppercase position-sticky top-0 shadow-sm">
                <tr>
                    <th style="width: 130px;">Fecha</th>
                    <th>Operación</th>
                    <th>Descripción / Motivo</th>
                    <th class="text-end" style="width: 100px;">Puntos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movimientos as $mov): ?>
                    <tr>
                        <td class="text-muted small text-nowrap">
                            <i class="far fa-calendar-alt me-1"></i>
                            <?php echo date('d/m/Y H:i', strtotime($mov['fecha_creacion'])); ?>
                        </td>
                        <td>
                            <span class="badge bg-secondary px-2 py-1 small">
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
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="text-center py-5 text-muted">
        <i class="fas fa-receipt fa-3x mb-3 d-block opacity-25"></i>
        Este estudiante aún no registra transacciones ni movimientos de puntos en su historial.
    </div>
<?php endif; ?>
