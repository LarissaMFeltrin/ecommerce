@extends('layouts.admin')

@section('title', 'Detalhes da Empresa')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-building mr-2"></i>{{ $empresa->nome_fantasia ?: $empresa->nome }}
            </h1>
            <div>
                <a href="{{ route('admin.empresas.edit', $empresa) }}" class="btn btn-warning">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <a href="{{ route('admin.empresas.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Voltar
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            <!-- Informações Principais -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle mr-2"></i>Informações da Empresa
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Nome:</strong></td>
                                        <td>{{ $empresa->nome }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nome Fantasia:</strong></td>
                                        <td>{{ $empresa->nome_fantasia ?: 'Não informado' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>CNPJ:</strong></td>
                                        <td>{{ $empresa->cnpj }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $empresa->email }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Telefone:</strong></td>
                                        <td>{{ $empresa->telefone ?: 'Não informado' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Ramo de Atividade:</strong></td>
                                        <td>
                                            <span class="badge badge-info">{{ ucfirst($empresa->ramo_atividade) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Plano:</strong></td>
                                        <td>
                                            @switch($empresa->plano)
                                                @case('basico')
                                                    <span class="badge badge-secondary">Básico</span>
                                                @break

                                                @case('profissional')
                                                    <span class="badge badge-primary">Profissional</span>
                                                @break

                                                @case('enterprise')
                                                    <span class="badge badge-success">Enterprise</span>
                                                @break

                                                @default
                                                    <span class="badge badge-light">{{ ucfirst($empresa->plano) }}</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            @if ($empresa->ativo)
                                                <span class="badge badge-success">Ativa</span>
                                            @else
                                                <span class="badge badge-danger">Inativa</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tema Visual:</strong></td>
                                        <td>{{ ucfirst($empresa->tema) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Subdomínio:</strong></td>
                                        <td>
                                            @if ($empresa->dominio)
                                                <code>{{ $empresa->dominio }}.seudominio.com</code>
                                            @else
                                                <span class="text-muted">Não configurado</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Endereço -->
                @if ($empresa->endereco || $empresa->cidade || $empresa->estado)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-map-marker-alt mr-2"></i>Endereço
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-1">
                                <strong>Endereço:</strong> {{ $empresa->endereco ?: 'Não informado' }}
                            </p>
                            <p class="mb-1">
                                <strong>Cidade:</strong> {{ $empresa->cidade ?: 'Não informado' }}
                            </p>
                            <p class="mb-1">
                                <strong>Estado:</strong> {{ $empresa->estado ?: 'Não informado' }}
                            </p>
                            <p class="mb-0">
                                <strong>CEP:</strong> {{ $empresa->cep ?: 'Não informado' }}
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Contrato -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-file-contract mr-2"></i>Informações do Contrato
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <strong>Data de Início:</strong><br>
                                    {{ $empresa->data_contrato ? $empresa->data_contrato->format('d/m/Y') : 'Não informado' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <strong>Data de Vencimento:</strong><br>
                                    {{ $empresa->data_vencimento ? $empresa->data_vencimento->format('d/m/Y') : 'Não informado' }}
                                </p>
                            </div>
                        </div>

                        @if ($empresa->data_vencimento)
                            @if ($empresa->isVencida())
                                <div class="alert alert-danger mt-3">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>Atenção!</strong> Esta empresa está com contrato vencido.
                                </div>
                            @elseif($empresa->data_vencimento->diffInDays(now()) <= 30)
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-clock mr-2"></i>
                                    <strong>Atenção!</strong> O contrato vence em
                                    {{ $empresa->data_vencimento->diffInDays(now()) }} dias.
                                </div>
                            @else
                                <div class="alert alert-success mt-3">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    <strong>Contrato válido.</strong> Vence em
                                    {{ $empresa->data_vencimento->diffInDays(now()) }} dias.
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Descrição -->
                @if ($empresa->descricao)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-align-left mr-2"></i>Descrição
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $empresa->descricao }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Logo -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-image mr-2"></i>Logo da Empresa
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        @if ($empresa->logo)
                            <img src="{{ Storage::url($empresa->logo) }}" alt="Logo" class="img-fluid rounded"
                                style="max-height: 200px;">
                        @else
                            <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center"
                                style="width: 200px; height: 200px; margin: 0 auto;">
                                <i class="fas fa-building fa-4x"></i>
                            </div>
                            <p class="text-muted mt-2">Nenhum logo cadastrado</p>
                        @endif
                    </div>
                </div>

                <!-- Cores da Empresa -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-palette mr-2"></i>Cores da Empresa
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label"><strong>Cor Primária:</strong></label>
                            <div class="d-flex align-items-center">
                                <div class="color-preview mr-2"
                                    style="background-color: {{ $empresa->cor_primaria }}; width: 30px; height: 30px; border-radius: 4px;">
                                </div>
                                <code>{{ $empresa->cor_primaria }}</code>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Cor Secundária:</strong></label>
                            <div class="d-flex align-items-center">
                                <div class="color-preview mr-2"
                                    style="background-color: {{ $empresa->cor_secundaria }}; width: 30px; height: 30px; border-radius: 4px;">
                                </div>
                                <code>{{ $empresa->cor_secundaria }}</code>
                            </div>
                        </div>

                        <!-- Preview das Cores -->
                        <div class="mt-3">
                            <label class="form-label"><strong>Preview:</strong></label>
                            <div class="p-3 rounded"
                                style="background: linear-gradient(45deg, {{ $empresa->cor_primaria }}, {{ $empresa->cor_secundaria }});">
                                <h6 class="text-white text-center mb-0">Identidade Visual</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estatísticas -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar mr-2"></i>Estatísticas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="border-right">
                                    <h4 class="text-primary">{{ $estatisticas['total_usuarios'] }}</h4>
                                    <small class="text-muted">Usuários</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div>
                                    <h4 class="text-success">{{ $estatisticas['total_produtos'] }}</h4>
                                    <small class="text-muted">Produtos</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="border-right">
                                    <h4 class="text-info">{{ $estatisticas['total_pedidos'] }}</h4>
                                    <small class="text-muted">Pedidos</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div>
                                    <h4 class="text-warning">R$
                                        {{ number_format($estatisticas['total_vendas'] / 100, 2, ',', '.') }}</h4>
                                    <small class="text-muted">Vendas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ações Rápidas -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt mr-2"></i>Ações Rápidas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <form method="POST" action="{{ route('admin.empresas.toggle', $empresa) }}"
                                class="d-inline">
                                @csrf
                                <button type="submit"
                                    class="btn btn-{{ $empresa->ativo ? 'btn-warning' : 'btn-success' }} btn-block">
                                    <i class="fas fa-{{ $empresa->ativo ? 'pause' : 'play' }} mr-2"></i>
                                    {{ $empresa->ativo ? 'Desativar' : 'Ativar' }} Empresa
                                </button>
                            </form>

                            <a href="{{ route('admin.empresas.edit', $empresa) }}" class="btn btn-warning btn-block">
                                <i class="fas fa-edit mr-2"></i>Editar Empresa
                            </a>

                            <form method="POST" action="{{ route('admin.empresas.destroy', $empresa) }}"
                                onsubmit="return confirm('Tem certeza que deseja excluir esta empresa?')"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block">
                                    <i class="fas fa-trash mr-2"></i>Excluir Empresa
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Adicionar classes CSS personalizadas baseadas nas cores da empresa
            const style = document.createElement('style');
            style.textContent = `
        .color-preview {
            border: 2px solid #ddd;
        }
        .btn-primary {
            background-color: {{ $empresa->cor_primaria }} !important;
            border-color: {{ $empresa->cor_primaria }} !important;
        }
        .btn-primary:hover {
            background-color: {{ $empresa->cor_secundaria }} !important;
            border-color: {{ $empresa->cor_secundaria }} !important;
        }
    `;
            document.head.appendChild(style);
        });
    </script>
@endpush
