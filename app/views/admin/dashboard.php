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
                <a href="/admin/blogs" class="btn btn-outline-secondary"><i class="fas fa-newspaper me-2"></i> Ver contenido</a>
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

<div class="row g-4 mt-1">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
            <div>
                <div class="stat-label">Reservas activas</div>
                <div class="stat-value">1,248</div>
                <div class="stat-caption">+8.4% frente al mes anterior</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div>
                <div class="stat-label">Usuarios activos</div>
                <div class="stat-value">24.5K</div>
                <div class="stat-caption">Interacciones de hoy</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-comments"></i></div>
            <div>
                <div class="stat-label">Foros abiertos</div>
                <div class="stat-value">186</div>
                <div class="stat-caption">Conversaciones en curso</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div>
                <div class="stat-label">Satisfacción</div>
                <div class="stat-value">4.9/5</div>
                <div class="stat-caption">Promedio de reseñas</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-xl-8">
        <div class="card panel-card">
            <div class="card-header">
                <h6 class="mb-0">Resumen de operaciones</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mini-panel">
                            <div class="mini-panel-title">Contenido publicado</div>
                            <div class="mini-panel-value">+32%</div>
                            <p class="mb-0 text-muted">Blogs y publicaciones esta semana</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mini-panel">
                            <div class="mini-panel-title">Tiempo de respuesta</div>
                            <div class="mini-panel-value">1.8h</div>
                            <p class="mb-0 text-muted">Promedio para soporte</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card panel-card">
            <div class="card-header">
                <h6 class="mb-0">Actividad reciente</h6>
            </div>
            <div class="card-body">
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div>
                            <div class="activity-title">Nuevo foro aprobado</div>
                            <div class="activity-time">Hace 10 min</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div>
                            <div class="activity-title">Reseña destacada publicada</div>
                            <div class="activity-time">Hace 37 min</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div>
                            <div class="activity-title">Se actualizó el menú principal</div>
                            <div class="activity-time">Hace 1 hora</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card panel-card mt-4">
    <div class="card-header">
        <h6 class="mb-0">Información del módulo</h6>
    </div>
    <div class="card-body">
        <p class="mb-2">El menú lateral se genera dinámicamente desde la base de datos, lo que permite adaptar el panel a distintos roles y permisos sin reescribir la interfaz.</p>
        <p class="mb-0 text-muted">Puedes ampliar esta vista agregando más módulos, métricas y acciones operativas según las necesidades del negocio.</p>
    </div>
</div>
</div>
