<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800 fw-bold"><?php echo htmlspecialchars($titulo ?? 'Dashboard'); ?></h1>
    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generar Reporte</a>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Reservas Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2 border-0" style="border-left: .25rem solid #4e73df !important;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Reservas (Mensual)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">$40,000</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Usuarios Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2 border-0" style="border-left: .25rem solid #1cc88a !important;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Usuarios Registrados</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">215,000</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow mb-4 border-0">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary">Bienvenido Administrador</h6>
            </div>
            <div class="card-body">
                <p>El menú de la izquierda es generado dinámicamente desde la base de datos `menu_maestro` haciendo JOIN con la tabla `menu_rol` dependiendo del rol del usuario actual.</p>
                <p class="mb-0">Para probar más módulos, agregue más registros a su base de datos o edite los Controladores y Modelos en el backend.</p>
            </div>
        </div>
    </div>
</div>
