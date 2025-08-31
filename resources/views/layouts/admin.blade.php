<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administração') - {{ config('app.name', 'E-commerce') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin: 0.25rem 0;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .sidebar .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
        }

        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .top-navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .stats-card .card-body {
            padding: 1.5rem;
        }

        .stats-card .stats-number {
            font-size: 2rem;
            font-weight: bold;
        }

        .stats-card .stats-label {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .table-responsive {
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .btn-admin {
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-admin:hover {
            transform: translateY(-1px);
        }

        .alert {
            border-radius: 0.5rem;
            border: none;
        }

        .form-control,
        .form-select {
            border-radius: 0.5rem;
            border: 1px solid #dee2e6;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .pagination .page-link {
            border-radius: 0.5rem;
            margin: 0 0.125rem;
        }

        .dropdown-menu {
            border-radius: 0.5rem;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .modal-content {
            border-radius: 1rem;
            border: none;
        }

        .modal-header {
            border-bottom: 1px solid #dee2e6;
            border-radius: 1rem 1rem 0 0;
        }

        .modal-footer {
            border-top: 1px solid #dee2e6;
            border-radius: 0 0 1rem 1rem;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar p-3">
                    <div class="text-center mb-4">
                        <h4 class="fw-bold">
                            <i class="bi bi-gear-fill me-2"></i>
                            Admin
                        </h4>
                        <small class="text-muted">Painel de Controle</small>
                    </div>

                    <nav class="nav flex-column">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                            href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2"></i>
                            Dashboard
                        </a>

                        <a class="nav-link {{ request()->routeIs('admin.produtos.*') ? 'active' : '' }}"
                            href="{{ route('admin.produtos.index') }}">
                            <i class="bi bi-box-seam"></i>
                            Produtos
                        </a>

                        <a class="nav-link {{ request()->routeIs('admin.categorias.*') ? 'active' : '' }}"
                            href="{{ route('admin.categorias.index') }}">
                            <i class="bi bi-tags"></i>
                            Categorias
                        </a>

                        <a class="nav-link {{ request()->routeIs('admin.pedidos.*') ? 'active' : '' }}"
                            href="{{ route('admin.pedidos.index') }}">
                            <i class="bi bi-cart-check"></i>
                            Pedidos
                        </a>

                        <a class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}"
                            href="{{ route('admin.usuarios.index') }}">
                            <i class="bi bi-people"></i>
                            Usuários
                        </a>

                        <a class="nav-link {{ request()->routeIs('admin.avaliacoes.*') ? 'active' : '' }}"
                            href="{{ route('admin.avaliacoes.index') }}">
                            <i class="bi bi-star"></i>
                            Avaliações
                        </a>

                        <a class="nav-link {{ request()->routeIs('admin.relatorios.*') ? 'active' : '' }}"
                            href="{{ route('admin.relatorios.index') }}">
                            <i class="bi bi-graph-up"></i>
                            Relatórios
                        </a>

                        <a class="nav-link {{ request()->routeIs('admin.administradores.*') ? 'active' : '' }}"
                            href="{{ route('admin.administradores.index') }}">
                            <i class="bi bi-shield-check"></i>
                            Administradores
                        </a>

                        <a class="nav-link {{ request()->routeIs('admin.configuracoes.*') ? 'active' : '' }}"
                            href="{{ route('admin.configuracoes.index') }}">
                            <i class="bi bi-sliders"></i>
                            Configurações
                        </a>

                        <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">

                        <a class="nav-link" href="{{ route('home') }}">
                            <i class="bi bi-house"></i>
                            Voltar ao Site
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link text-start w-100 p-0">
                                <i class="bi bi-box-arrow-right"></i>
                                Sair
                            </button>
                        </form>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-0">
                <div class="main-content">
                    <!-- Top Navbar -->
                    <div class="top-navbar">
                        <div class="container-fluid">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="mb-0">@yield('page-title', 'Dashboard')</h5>
                                    <small class="text-muted">@yield('page-subtitle', 'Visão geral do sistema')</small>
                                </div>
                                <div class="col-auto">
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                            <i class="bi bi-person-circle me-2"></i>
                                            {{ Auth::user()->nome }}
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('perfil') }}">
                                                    <i class="bi bi-person me-2"></i>Meu Perfil
                                                </a></li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('logout') }}">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="bi bi-box-arrow-right me-2"></i>Sair
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Page Content -->
                    <div class="container-fluid py-4">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Erro!</strong> Por favor, verifique os campos abaixo:
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts personalizados -->
    @stack('scripts')

    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Confirm delete actions
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('form[data-confirm]');
            deleteForms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    if (!confirm(this.dataset.confirm)) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>

</html>
