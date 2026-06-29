<div class="container-fluid">
    <div class="page-hero card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-xl-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="eyebrow">Centro de operaciones</div>
                    <h2 class="h3 fw-bold mb-2">Bienvenido de nuevo, <?php echo htmlspecialchars($nombre_usuario ?? 'Administrador'); ?></h2>
                    <p class="text-muted mb-3">Gestiona comunidades, contenido y operaciones con una vista ejecutiva diseñada para equipos de alto rendimiento.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="/admin/foros" class="btn btn-primary"><i class="fas fa-comments me-2"></i> Revisar foros</a>
                        <a href="/admin/alojamientos" class="btn btn-outline-secondary"><i class="fas fa-home me-2"></i> Ver alojamientos</a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="hero-metric">
                        <div class="hero-metric-label">Disponibilidad del sistema</div>
                        <div class="hero-metric-value">99.8%</div>
                        <div class="hero-metric-caption">Durante los últimos 30 días</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Métricas -->
    <div class="row g-4 mt-1">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card shadow-sm border-0 h-100">
                <div class="stat-icon bg-primary text-white p-3 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                    <i class="fas fa-calendar-check fs-4"></i>
                </div>
                <div>
                    <div class="stat-label text-muted fw-bold">Reservas activas</div>
                    <div class="stat-value fs-2 fw-bolder"><?php echo number_format($totalReservas); ?></div>
                    <div class="stat-caption text-success small"><i class="fas fa-arrow-up"></i> Aprobadas y pendientes</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card shadow-sm border-0 h-100">
                <div class="stat-icon bg-success text-white p-3 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                    <i class="fas fa-users fs-4"></i>
                </div>
                <div>
                    <div class="stat-label text-muted fw-bold">Usuarios activos</div>
                    <div class="stat-value fs-2 fw-bolder"><?php echo number_format($totalUsuarios); ?></div>
                    <div class="stat-caption text-success small"><i class="fas fa-arrow-up"></i> Habilitados en el sistema</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card shadow-sm border-0 h-100">
                <div class="stat-icon bg-warning text-white p-3 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                    <i class="fas fa-comments fs-4"></i>
                </div>
                <div>
                    <div class="stat-label text-muted fw-bold">Foros abiertos</div>
                    <div class="stat-value fs-2 fw-bolder"><?php echo number_format($totalForos); ?></div>
                    <div class="stat-caption text-success small">Conversaciones activas</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card shadow-sm border-0 h-100">
                <div class="stat-icon bg-info text-white p-3 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                    <i class="fas fa-star fs-4"></i>
                </div>
                <div>
                    <div class="stat-label text-muted fw-bold">Satisfacción</div>
                    <div class="stat-value fs-2 fw-bolder"><?php echo number_format($promedioSatisfaccion, 1); ?>/5</div>
                    <div class="stat-caption text-success small">Promedio de reseñas</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row g-4 mt-4">
        <!-- Gráfico de Reservas (Líneas) -->
        <div class="col-xl-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="m-0 font-weight-bold text-primary">Reservas (Últimos 6 meses)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="position: relative; height:300px; width:100%">
                        <canvas id="reservasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Tipos de Alojamiento (Doughnut) -->
        <div class="col-xl-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="m-0 font-weight-bold text-primary">Tipos de Alojamiento</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2" style="position: relative; height:250px; width:100%">
                        <canvas id="alojamientosTipoChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Cuarto
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Mini-Dpto
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Dpto Completo
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ---------------------------------------------------------
    // Gráfico de Reservas por Mes (Bar Chart / Line Chart)
    // ---------------------------------------------------------
    const reservasDataRaw = <?php echo json_encode($reservasMeses); ?>;
    
    // Preparar arrays para Chart.js
    const labelsMeses = reservasDataRaw.map(item => {
        // Formatear mes (ej. de '2023-10' a 'Oct 2023' o simplemente dejar el raw si se desea)
        return item.mes;
    });
    const totalesReservas = reservasDataRaw.map(item => item.total);

    const ctxReservas = document.getElementById('reservasChart');
    if (ctxReservas) {
        new Chart(ctxReservas, {
            type: 'line',
            data: {
                labels: labelsMeses,
                datasets: [{
                    label: 'Nuevas Reservas',
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: totalesReservas,
                    fill: true
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: { left: 10, right: 25, top: 25, bottom: 0 }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0 // Mostrar solo números enteros
                        },
                        grid: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // ---------------------------------------------------------
    // Gráfico de Tipos de Alojamiento (Doughnut Chart)
    // ---------------------------------------------------------
    const alojamientosTipoRaw = <?php echo json_encode($alojamientosTipo); ?>;
    
    const labelsTipos = alojamientosTipoRaw.map(item => item.nombre);
    const totalesTipos = alojamientosTipoRaw.map(item => item.total);

    const ctxTipos = document.getElementById('alojamientosTipoChart');
    if (ctxTipos) {
        new Chart(ctxTipos, {
            type: 'doughnut',
            data: {
                labels: labelsTipos,
                datasets: [{
                    data: totalesTipos,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    }
                },
                cutout: '70%',
            }
        });
    }
});
</script>
