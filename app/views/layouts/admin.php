<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración | WS-ROOMS</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/public/css/style.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fc; overflow-x: hidden; }
        .sidebar { min-height: 100vh; background-color: #4e73df; background-image: linear-gradient(180deg, #4e73df 10%, #224abe 100%); background-size: cover; width: 250px; position: fixed; top: 0; left: 0; z-index: 100; transition: all 0.3s; }
        .sidebar-brand { height: 4.375rem; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: 800; padding: 1.5rem 1rem; text-align: center; color: #fff; text-decoration: none; letter-spacing: 0.05rem; }
        .sidebar hr { border-top: 1px solid rgba(255, 255, 255, 0.15); margin: 0 1rem 1rem; }
        .sidebar .nav-item .nav-link { color: rgba(255, 255, 255, 0.8); font-weight: 700; padding: 1rem; display: block; text-decoration: none; }
        .sidebar .nav-item .nav-link:hover, .sidebar .nav-item .nav-link.active { color: #fff; }
        .sidebar .nav-item .nav-link i { font-size: 0.85rem; margin-right: 0.25rem; }
        .content-wrapper { margin-left: 250px; width: calc(100% - 250px); transition: all 0.3s; min-height: 100vh; display: flex; flex-direction: column; }
        .topbar { height: 4.375rem; background-color: #fff; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); }
        .main-content { padding: 1.5rem; flex-grow: 1; }
        @media (max-width: 768px) {
            .sidebar { margin-left: -250px; }
            .sidebar.toggled { margin-left: 0; }
            .content-wrapper { margin-left: 0; width: 100%; }
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/admin">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-bed"></i>
                </div>
                <div class="sidebar-brand-text mx-3">WS-ROOMS</div>
            </a>
            <hr class="sidebar-divider my-0">
            
            <ul class="nav flex-column mb-auto mt-2">
                <!-- Menú Comunidad (Red Social) -->
                <li class="nav-item mt-3">
                    <div class="px-3 text-white-50 text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.05rem;">
                        Red Social
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/admin/foros">
                        <i class="fas fa-fw fa-comments"></i>
                        <span>Foros</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/admin/blogs">
                        <i class="fas fa-fw fa-newspaper"></i>
                        <span>Blogs</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/admin/resenias">
                        <i class="fas fa-fw fa-star"></i>
                        <span>Reseñas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/admin/chats">
                        <i class="fas fa-fw fa-chart-bar"></i>
                        <span>Monitor de Chats</span>
                    </a>
                </li>
                
                <hr class="sidebar-divider d-none d-md-block my-3 w-100" style="border-top: 1px solid rgba(255, 255, 255, 0.15);">

                <!-- Carga dinámica del menú -->
                <?php if(isset($menus) && is_array($menus)): ?>
                    <?php foreach($menus as $menu): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo htmlspecialchars($menu['url'] ?? '#'); ?>">
                                <i class="fas fa-fw fa-tachometer-alt"></i>
                                <span><?php echo htmlspecialchars($menu['nombre'] ?? 'Menu'); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link text-white-50">Sin menús</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow px-4">
                <!-- Sidebar Toggle (Topbar) -->
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
                
                <!-- Topbar Navbar -->
                <ul class="navbar-nav ms-auto">
                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="me-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($nombre_usuario ?? 'Admin'); ?></span>
                            <img class="img-profile rounded-circle" src="https://ui-avatars.com/api/?name=<?php echo urlencode($nombre_usuario ?? 'Admin'); ?>&background=random" width="30" height="30">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i> Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i> Salir</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
            
            <!-- Main Content -->
            <div class="main-content">
                <?php echo $content; ?>
            </div>
            
            <!-- Footer -->
            <footer class="sticky-footer bg-white mt-auto py-3">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; WS-ROOMS 2026</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggleTop').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('toggled');
        });
    </script>
</body>
</html>
