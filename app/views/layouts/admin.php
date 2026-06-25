<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración | WS-ROOMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="/public/css/admin-theme.css">
</head>
<body class="admin-body">
    <?php $current_uri = $_SERVER['REQUEST_URI']; ?>
    <div class="admin-shell d-flex">
        <nav class="sidebar admin-sidebar" id="sidebar">
            <a class="sidebar-brand" href="/admin">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="sidebar-brand-text">
                    <span>WS-ROOMS</span>
                    <small>Enterprise Admin</small>
                </div>
            </a>

            <div class="sidebar-status">
                <div class="status-dot"></div>
                <span>Sistema operativo</span>
            </div>

            <ul class="nav flex-column mt-3">
                <li class="nav-item">
                    <div class="nav-section-label">Plataforma</div>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_uri == '/admin' || $current_uri == '/admin/') ? 'active' : ''; ?>" href="/admin">
                        <i class="fas fa-fw fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <div class="nav-section-label">Red social</div>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($current_uri, '/admin/foros') !== false) ? 'active' : ''; ?>" href="/admin/foros">
                        <i class="fas fa-fw fa-comments"></i>
                        <span>Foros</span>
                        <span class="sidebar-badge">12</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($current_uri, '/admin/blogs') !== false) ? 'active' : ''; ?>" href="/admin/blogs">
                        <i class="fas fa-fw fa-newspaper"></i>
                        <span>Blogs</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($current_uri, '/admin/resenias') !== false) ? 'active' : ''; ?>" href="/admin/resenias">
                        <i class="fas fa-fw fa-star"></i>
                        <span>Reseñas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($current_uri, '/admin/chats') !== false) ? 'active' : ''; ?>" href="/admin/chats">
                        <i class="fas fa-fw fa-chart-bar"></i>
                        <span>Monitor de Chats</span>
                    </a>
                </li>

                <li class="nav-item">
                    <div class="nav-section-label">Operaciones</div>
                </li>
                <?php if(isset($menus) && is_array($menus)): ?>
                    <?php foreach($menus as $menu): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo htmlspecialchars($menu['url'] ?? '#'); ?>">
                                <i class="fas fa-fw fa-layer-group"></i>
                                <span><?php echo htmlspecialchars($menu['nombre'] ?? 'Menu'); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link text-white-50">Sin menús configurados</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

        <div class="content-wrapper">
            <nav class="navbar navbar-expand admin-topbar static-top px-4">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle me-3">
                    <i class="fa fa-bars"></i>
                </button>

                <div class="topbar-title d-none d-lg-block">
                    <h6 class="mb-0">Centro de operaciones</h6>
                    <small>Gestión inteligente de la plataforma</small>
                </div>

                <div class="d-flex align-items-center gap-3 ms-auto">
                    <div class="topbar-chip">
                        <i class="fas fa-circle"></i>
                        <span>Online</span>
                    </div>
                    <div class="topbar-search d-none d-xl-flex">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Buscar...">
                    </div>
                    <a class="icon-btn" href="#"><i class="fas fa-bell"></i></a>
                    <div class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img class="img-profile rounded-circle me-2" src="https://ui-avatars.com/api/?name=<?php echo urlencode($nombre_usuario ?? 'Admin'); ?>&background=dc2626&color=fff" width="36" height="36">
                            <span class="d-none d-lg-inline small fw-semibold"><?php echo htmlspecialchars($nombre_usuario ?? 'Admin'); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i> Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i> Salir</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="main-content">
                <?php echo $content; ?>
            </div>

            <footer class="admin-footer mt-auto">
                <div class="container-fluid d-flex flex-column flex-sm-row justify-content-between align-items-center">
                    <div class="footer-text mb-2 mb-sm-0">
                        &copy; <?php echo date('Y'); ?> <strong>WS-ROOMS Enterprise</strong>. Todos los derechos reservados.
                    </div>
                    <div class="footer-links">
                        <a href="#">Soporte</a>
                        <a href="#">Privacidad</a>
                        <a href="#">Términos de Servicio</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const toggle = document.getElementById('sidebarToggleTop');
        if (toggle) {
            toggle.addEventListener('click', function() {
                document.getElementById('sidebar').classList.toggle('toggled');
            });
        }
    </script>
</body>
</html>
