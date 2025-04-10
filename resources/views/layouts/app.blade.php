<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Ingresos - @yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/filament-style.css') }}">
    <style>
        :root {
            --sidebar-width: 280px;
            --topbar-height: 64px;
            --primary-color: #6366F1;
            --primary-hover: #4F46E5;
            --sidebar-bg: #111827;
            --sidebar-item-hover: rgba(255, 255, 255, 0.1);
            --sidebar-item-active: rgba(99, 102, 241, 0.2);
            --content-bg: #1a1d23;
        }
        
        body {
            overflow-x: hidden;
            background-color: var(--content-bg);
        }
        
        .navbar {
            height: var(--topbar-height);
            background-color: var(--sidebar-bg) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0 1.5rem;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            color: white;
        }
        
        .sidebar-wrapper {
            position: fixed;
            top: var(--topbar-height);
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-menu {
            padding: 1rem 0;
        }
        
        .sidebar-section {
            margin-bottom: 1rem;
        }
        
        .sidebar-section-header {
            padding: 0.75rem 1.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: rgba(255, 255, 255, 0.5);
        }
        
        .sidebar-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            margin: 0.25rem 0.75rem;
            border-radius: 0.375rem;
        }
        
        .sidebar-item:hover {
            background-color: var(--sidebar-item-hover);
            color: white;
        }
        
        .sidebar-item.active {
            background-color: var(--sidebar-item-active);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
        }
        
        .sidebar-icon {
            margin-right: 0.75rem;
            font-size: 1.25rem;
            width: 1.5rem;
            text-align: center;
        }
        
        .sidebar-badge {
            margin-left: auto;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            background-color: var(--primary-color);
            color: white;
        }
        
        .content-wrapper {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            min-height: calc(100vh - var(--topbar-height));
            transition: all 0.3s;
        }
        
        @media (max-width: 991.98px) {
            .sidebar-wrapper {
                transform: translateX(-100%);
            }
            
            .sidebar-wrapper.show {
                transform: translateX(0);
            }
            
            .content-wrapper {
                margin-left: 0;
            }
        }
        
        .user-dropdown .dropdown-menu {
            background-color: #1F2937;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        
        .user-dropdown .dropdown-item {
            color: rgba(255, 255, 255, 0.7);
            padding: 0.75rem 1rem;
        }
        
        .user-dropdown .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .user-dropdown .dropdown-divider {
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        .search-bar {
            position: relative;
            max-width: 300px;
        }
        
        .search-bar .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 9999px;
            padding-left: 2.5rem;
            color: white;
        }
        
        .search-bar .form-control:focus {
            background-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.5);
        }
        
        .search-bar .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-bar-chart-fill me-2 text-primary"></i>
                Sistema de Ingresos
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto d-flex align-items-center">
                    <div class="search-bar me-3">
                        <i class="bi bi-search search-icon"></i>
                        <input class="form-control" type="search" placeholder="Buscar..." aria-label="Search">
                    </div>
                    <div class="user-dropdown dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <div class="avatar bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                     style="width: 32px; height: 32px;">
                                    <i class="bi bi-person-fill text-white"></i>
                                </div>
                                <span class="d-none d-md-inline-block">Admin</span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Perfil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Configuración</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="sidebar-wrapper">
        <div class="sidebar-menu">
            <div class="sidebar-section">
                <div class="sidebar-section-header">Principal</div>
                <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 sidebar-icon"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            
            <div class="sidebar-section">
                <div class="sidebar-section-header">Cuentas</div>
                <a href="{{ route('accounts.index') }}" class="sidebar-item {{ request()->routeIs('accounts.index') ? 'active' : '' }}">
                    <i class="bi bi-globe sidebar-icon"></i>
                    <span>Todas las Cuentas</span>
                </a>
                <a href="{{ route('accounts.create') }}" class="sidebar-item {{ request()->routeIs('accounts.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle sidebar-icon"></i>
                    <span>Nueva Cuenta</span>
                </a>
                <a href="{{ route('accounts.expiring') }}" class="sidebar-item {{ request()->routeIs('accounts.expiring') ? 'active' : '' }}">
                    <i class="bi bi-exclamation-triangle sidebar-icon"></i>
                    <span>Cuentas por Vencer</span>
                    @php
                        $expiringCount = \App\Models\Account::whereDate('expiration_date', '<=', now()->addDays(30))
                            ->whereDate('expiration_date', '>=', now())
                            ->count();
                    @endphp
                    @if($expiringCount > 0)
                        <span class="sidebar-badge">{{ $expiringCount }}</span>
                    @endif
                </a>
            </div>
            
            <div class="sidebar-section">
                <div class="sidebar-section-header">Facturas</div>
                <a href="{{ route('invoices.index') }}" class="sidebar-item {{ request()->routeIs('invoices.index') ? 'active' : '' }}">
                    <i class="bi bi-receipt sidebar-icon"></i>
                    <span>Todas las Facturas</span>
                </a>
                <a href="{{ route('invoices.create') }}" class="sidebar-item {{ request()->routeIs('invoices.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle sidebar-icon"></i>
                    <span>Nueva Factura</span>
                </a>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Sidebar toggle para dispositivos móviles
        document.addEventListener('DOMContentLoaded', function() {
            const navbarToggler = document.querySelector('.navbar-toggler');
            const sidebarWrapper = document.querySelector('.sidebar-wrapper');
            
            navbarToggler.addEventListener('click', function() {
                sidebarWrapper.classList.toggle('show');
            });
            
            // Cerrar sidebar al hacer clic en un enlace en dispositivos móviles
            const sidebarItems = document.querySelectorAll('.sidebar-item');
            sidebarItems.forEach(item => {
                item.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        sidebarWrapper.classList.remove('show');
                    }
                });
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
