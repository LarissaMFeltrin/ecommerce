<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - Painel Administrativo</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Fallback para FontAwesome e Bootstrap Icons -->
    <script>
        // Verificar se FontAwesome carregou corretamente
        window.addEventListener('load', function() {
            if (typeof FontAwesome === 'undefined') {
                console.warn('FontAwesome não carregou, usando fallback');
                document.body.classList.add('fa-fallback');
            }

            // Verificar se Bootstrap Icons carregou corretamente
            const biTest = document.createElement('i');
            biTest.className = 'bi bi-eye';
            document.body.appendChild(biTest);

            const computedStyle = window.getComputedStyle(biTest, '::before');
            if (computedStyle.content === 'none' || computedStyle.content === '') {
                console.warn('Bootstrap Icons não carregou, usando fallback');
                document.body.classList.add('bi-fallback');
            }

            document.body.removeChild(biTest);
        });
    </script>
    <!-- Custom CSS -->
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin: 0.25rem 0;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link i {
            width: 20px;
            margin-right: 0.5rem;
        }

        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
        }

        .btn-group .btn {
            margin-right: 0.25rem;
        }

        .btn-group .btn:last-child {
            margin-right: 0;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
        }

        .badge {
            font-size: 0.70rem;
        }

        .alert {
            border: none;
            border-radius: 0.5rem;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-primary {
            background-color: #667eea;
            border-color: #667eea;
        }

        .btn-primary:hover {
            background-color: #5a6fd8;
            border-color: #5a6fd8;
        }

        /* Fallback para FontAwesome */
        .fa-fallback .fas::before {
            content: attr(data-icon);
        }

        /* Fallback para Bootstrap Icons */
        .bi-fallback .bi::before {
            content: attr(data-icon);
        }

        /* Fallbacks específicos para Bootstrap Icons */
        .bi-fallback .bi.bi-eye::before {
            content: "👁";
        }

        .bi-fallback .bi.bi-pencil::before {
            content: "✏";
        }

        .bi-fallback .bi.bi-person-x::before {
            content: "🚫";
        }

        .bi-fallback .bi.bi-trash::before {
            content: "🗑";
        }

        .bi-fallback .bi.bi-plus-circle::before {
            content: "➕";
        }

        .bi-fallback .bi.bi-shield-check::before {
            content: "🛡️";
        }

        .bi-fallback .bi.bi-people::before {
            content: "👥";
        }

        .bi-fallback .bi.bi-tags::before {
            content: "🏷️";
        }

        .bi-fallback .bi.bi-box-seam::before {
            content: "📦";
        }

        .bi-fallback .bi.bi-search::before {
            content: "🔍";
        }

        .border-left-primary {
            border-left: 0.25rem solid #667eea !important;
        }

        .border-left-success {
            border-left: 0.25rem solid #28a745 !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #ffc107 !important;
        }

        .border-left-info {
            border-left: 0.25rem solid #17a2b8 !important;
        }

        .text-gray-300 {
            color: #dddfeb !important;
        }

        .text-gray-800 {
            color: #5a5c69 !important;
        }

        .text-xs {
            font-size: 0.7rem;
        }

        .h-100 {
            height: 100% !important;
        }

        .py-2 {
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
        }

        .shadow {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
        }

        .h-3 {
            font-size: 1.5rem;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .text-gray-800 {
            color: #5a5c69 !important;
        }

        .mr-2 {
            margin-right: 0.5rem !important;
        }

        .mb-4 {
            margin-bottom: 1.5rem !important;
        }

        .mt-4 {
            margin-top: 1.5rem !important;
        }

        .my-4 {
            margin-top: 1.5rem !important;
            margin-bottom: 1.5rem !important;
        }

        .mt-3 {
            margin-top: 1rem !important;
        }

        .mb-3 {
            margin-bottom: 1rem !important;
        }

        .mb-2 {
            margin-bottom: 0.5rem !important;
        }

        .mb-1 {
            margin-bottom: 0.25rem !important;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .p-4 {
            padding: 1.5rem !important;
        }

        .p-3 {
            padding: 1rem !important;
        }

        .py-4 {
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }

        .py-3 {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }

        .px-3 {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }

        .px-4 {
            padding-left: 1.5rem !important;
            padding-right: 1.5rem !important;
        }

        .rounded {
            border-radius: 0.375rem !important;
        }

        .text-white {
            color: white !important;
        }

        .text-white-50 {
            color: rgba(255, 255, 255, 0.5) !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .small {
            font-size: 0.875rem;
        }

        .d-flex {
            display: flex !important;
        }

        .justify-content-between {
            justify-content: space-between !important;
        }

        .justify-content-center {
            justify-content: center !important;
        }

        .align-items-center {
            align-items: center !important;
        }

        .d-inline {
            display: inline !important;
        }

        .d-block {
            display: block !important;
        }

        .d-grid {
            display: grid !important;
        }

        .gap-2 {
            gap: 0.5rem !important;
        }

        .btn-block {
            display: block;
            width: 100%;
        }

        .ml-2 {
            margin-left: 0.5rem !important;
        }

        .mr-2 {
            margin-right: 0.5rem !important;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">
                            <i class="fas fa-crown mr-2"></i>Admin Panel
                        </h4>
                    </div>

                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                                href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.empresas.*') ? 'active' : '' }}"
                                href="{{ route('admin.empresas.index') }}">
                                <i class="fas fa-building"></i>
                                Empresas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.administradores.*') ? 'active' : '' }}"
                                href="{{ route('admin.administradores.index') }}">
                                <i class="fas fa-users-cog"></i>
                                Administradores
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}"
                                href="{{ route('admin.usuarios.index') }}">
                                <i class="fas fa-users"></i>
                                Usuários
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.produtos.*') ? 'active' : '' }}"
                                href="{{ route('admin.produtos.index') }}">
                                <i class="fas fa-box"></i>
                                Produtos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.pedidos.*') ? 'active' : '' }}"
                                href="{{ route('admin.pedidos.index') }}">
                                <i class="fas fa-shopping-cart"></i>
                                Pedidos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.categorias.*') ? 'active' : '' }}"
                                href="{{ route('admin.categorias.index') }}">
                                <i class="fas fa-tags"></i>
                                Categorias
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.configuracoes.*') ? 'active' : '' }}"
                                href="{{ route('admin.configuracoes.index') }}">
                                <i class="fas fa-cog"></i>
                                Configurações
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link" href="{{ route('home') }}">
                                <i class="fas fa-home"></i>
                                Voltar ao Site
                            </a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link p-0">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Sair
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Top navbar -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#sidebarMenu">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="navbar-nav ms-auto">
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                    data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle mr-2"></i>{{ Auth::user()->nome }}
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('home') }}">Meu Perfil</a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Sair</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Page content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('styles')
    @stack('scripts')
</body>

</html>
