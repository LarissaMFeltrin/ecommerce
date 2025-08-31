@extends('layouts.admin')

@section('title', 'Editar Empresa')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-edit mr-2"></i>Editar Empresa: {{ $empresa->nome_fantasia ?: $empresa->nome }}
            </h1>
            <div>
                <a href="{{ route('admin.empresas.show', $empresa) }}" class="btn btn-info">
                    <i class="fas fa-eye mr-2"></i>Ver Detalhes
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

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.empresas.update', $empresa) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Informações Básicas -->
                        <div class="col-md-6">
                            <h5 class="mb-3">
                                <i class="fas fa-info-circle mr-2"></i>Informações Básicas
                            </h5>

                            <div class="form-group">
                                <label for="nome" class="form-label">Nome da Empresa *</label>
                                <input type="text" class="form-control @error('nome') is-invalid @enderror"
                                    id="nome" name="nome" value="{{ old('nome', $empresa->nome) }}" required>
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="nome_fantasia" class="form-label">Nome Fantasia</label>
                                <input type="text" class="form-control @error('nome_fantasia') is-invalid @enderror"
                                    id="nome_fantasia" name="nome_fantasia"
                                    value="{{ old('nome_fantasia', $empresa->nome_fantasia) }}">
                                @error('nome_fantasia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="cnpj" class="form-label">CNPJ *</label>
                                <input type="text" class="form-control @error('cnpj') is-invalid @enderror"
                                    id="cnpj" name="cnpj" value="{{ old('cnpj', $empresa->cnpj) }}" required>
                                @error('cnpj')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $empresa->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="text" class="form-control @error('telefone') is-invalid @enderror"
                                    id="telefone" name="telefone" value="{{ old('telefone', $empresa->telefone) }}">
                                @error('telefone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Endereço -->
                        <div class="col-md-6">
                            <h5 class="mb-3">
                                <i class="fas fa-map-marker-alt mr-2"></i>Endereço
                            </h5>

                            <div class="form-group">
                                <label for="endereco" class="form-label">Endereço</label>
                                <input type="text" class="form-control @error('endereco') is-invalid @enderror"
                                    id="endereco" name="endereco" value="{{ old('endereco', $empresa->endereco) }}">
                                @error('endereco')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cidade" class="form-label">Cidade</label>
                                        <input type="text" class="form-control @error('cidade') is-invalid @enderror"
                                            id="cidade" name="cidade" value="{{ old('cidade', $empresa->cidade) }}">
                                        @error('cidade')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="estado" class="form-label">Estado</label>
                                        <select class="form-control @error('estado') is-invalid @enderror" id="estado"
                                            name="estado">
                                            <option value="">Selecione</option>
                                            @foreach (['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $uf)
                                                <option value="{{ $uf }}"
                                                    {{ old('estado', $empresa->estado) == $uf ? 'selected' : '' }}>
                                                    {{ $uf }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('estado')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="cep" class="form-label">CEP</label>
                                        <input type="text" class="form-control @error('cep') is-invalid @enderror"
                                            id="cep" name="cep" value="{{ old('cep', $empresa->cep) }}">
                                        @error('cep')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <!-- Configurações da Loja -->
                        <div class="col-md-6">
                            <h5 class="mb-3">
                                <i class="fas fa-store mr-2"></i>Configurações da Loja
                            </h5>

                            <div class="form-group">
                                <label for="ramo_atividade" class="form-label">Ramo de Atividade *</label>
                                <select class="form-control @error('ramo_atividade') is-invalid @enderror"
                                    id="ramo_atividade" name="ramo_atividade" required>
                                    <option value="">Selecione o ramo</option>
                                    @foreach ($ramos as $codigo => $nome)
                                        <option value="{{ $codigo }}"
                                            {{ old('ramo_atividade', $empresa->ramo_atividade) == $codigo ? 'selected' : '' }}>
                                            {{ $nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('ramo_atividade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="tema" class="form-label">Tema Visual *</label>
                                <select class="form-control @error('tema') is-invalid @enderror" id="tema"
                                    name="tema" required>
                                    <option value="default"
                                        {{ old('tema', $empresa->tema) == 'default' ? 'selected' : '' }}>Padrão</option>
                                    <option value="perfumes"
                                        {{ old('tema', $empresa->tema) == 'perfumes' ? 'selected' : '' }}>Perfumes</option>
                                    <option value="roupas"
                                        {{ old('tema', $empresa->tema) == 'roupas' ? 'selected' : '' }}>Roupas</option>
                                    <option value="eletronicos"
                                        {{ old('tema', $empresa->tema) == 'eletronicos' ? 'selected' : '' }}>Eletrônicos
                                    </option>
                                    <option value="casa" {{ old('tema', $empresa->tema) == 'casa' ? 'selected' : '' }}>
                                        Casa</option>
                                </select>
                                @error('tema')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="dominio" class="form-label">Subdomínio</label>
                                <div class="input-group">
                                    <input type="text" class="form-control @error('dominio') is-invalid @enderror"
                                        id="dominio" name="dominio" value="{{ old('dominio', $empresa->dominio) }}"
                                        placeholder="minhaempresa">
                                    <div class="input-group-append">
                                        <span class="input-group-text">.seudominio.com</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Deixe em branco para usar URL padrão</small>
                                @error('dominio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control @error('descricao') is-invalid @enderror" id="descricao" name="descricao"
                                    rows="3">{{ old('descricao', $empresa->descricao) }}</textarea>
                                @error('descricao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Personalização Visual -->
                        <div class="col-md-6">
                            <h5 class="mb-3">
                                <i class="fas fa-palette mr-2"></i>Personalização Visual
                            </h5>

                            <div class="form-group">
                                <label for="cor_primaria" class="form-label">Cor Primária *</label>
                                <input type="color" class="form-control @error('cor_primaria') is-invalid @enderror"
                                    id="cor_primaria" name="cor_primaria"
                                    value="{{ old('cor_primaria', $empresa->cor_primaria) }}" required>
                                @error('cor_primaria')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="cor_secundaria" class="form-label">Cor Secundária *</label>
                                <input type="color" class="form-control @error('cor_secundaria') is-invalid @enderror"
                                    id="cor_secundaria" name="cor_secundaria"
                                    value="{{ old('cor_secundaria', $empresa->cor_secundaria) }}" required>
                                @error('cor_secundaria')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="logo" class="form-label">Logo da Empresa</label>
                                @if ($empresa->logo)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($empresa->logo) }}" alt="Logo atual"
                                            class="img-thumbnail" style="max-height: 100px;">
                                        <small class="d-block text-muted">Logo atual</small>
                                    </div>
                                @endif
                                <input type="file" class="form-control-file @error('logo') is-invalid @enderror"
                                    id="logo" name="logo" accept="image/*">
                                <small class="form-text text-muted">Formatos aceitos: JPG, PNG, GIF. Máximo: 2MB. Deixe em
                                    branco para manter o logo atual.</small>
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="plano" class="form-label">Plano *</label>
                                <select class="form-control @error('plano') is-invalid @enderror" id="plano"
                                    name="plano" required>
                                    <option value="">Selecione o plano</option>
                                    @foreach ($planos as $codigo => $plano)
                                        <option value="{{ $codigo }}"
                                            {{ old('plano', $empresa->plano) == $codigo ? 'selected' : '' }}>
                                            {{ $plano['nome'] }} - R$ {{ number_format($plano['preco'], 2, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('plano')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <!-- Contrato -->
                        <div class="col-md-6">
                            <h5 class="mb-3">
                                <i class="fas fa-file-contract mr-2"></i>Informações do Contrato
                            </h5>

                            <div class="form-group">
                                <label for="data_contrato" class="form-label">Data de Início do Contrato *</label>
                                <input type="date" class="form-control @error('data_contrato') is-invalid @enderror"
                                    id="data_contrato" name="data_contrato"
                                    value="{{ old('data_contrato', $empresa->data_contrato ? $empresa->data_contrato->format('Y-m-d') : '') }}"
                                    required>
                                @error('data_contrato')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="data_vencimento" class="form-label">Data de Vencimento *</label>
                                <input type="date" class="form-control @error('data_vencimento') is-invalid @enderror"
                                    id="data_vencimento" name="data_vencimento"
                                    value="{{ old('data_vencimento', $empresa->data_vencimento ? $empresa->data_vencimento->format('Y-m-d') : '') }}"
                                    required>
                                @error('data_vencimento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Preview das Cores -->
                        <div class="col-md-6">
                            <h5 class="mb-3">
                                <i class="fas fa-eye mr-2"></i>Preview das Cores
                            </h5>

                            <div class="card">
                                <div class="card-body text-center">
                                    <div id="color-preview" class="p-4 rounded"
                                        style="background: linear-gradient(45deg, {{ $empresa->cor_primaria }}, {{ $empresa->cor_secundaria }});">
                                        <h6 class="text-white mb-2">Preview da Identidade Visual</h6>
                                        <p class="text-white-50 small">As cores serão aplicadas automaticamente</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Status da Empresa -->
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">
                                <i class="fas fa-toggle-on mr-2"></i>Status da Empresa
                            </h5>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="ativo" name="ativo"
                                        value="1" {{ old('ativo', $empresa->ativo) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="ativo">
                                        Empresa Ativa
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Desmarque para desativar temporariamente esta empresa
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save mr-2"></i>Atualizar Empresa
                        </button>
                        <a href="{{ route('admin.empresas.show', $empresa) }}" class="btn btn-secondary btn-lg ml-2">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Preview das cores em tempo real
            const corPrimaria = document.getElementById('cor_primaria');
            const corSecundaria = document.getElementById('cor_secundaria');
            const preview = document.getElementById('color-preview');

            function atualizarPreview() {
                preview.style.background = `linear-gradient(45deg, ${corPrimaria.value}, ${corSecundaria.value})`;
            }

            corPrimaria.addEventListener('input', atualizarPreview);
            corSecundaria.addEventListener('input', atualizarPreview);

            // Validação de datas
            const dataContrato = document.getElementById('data_contrato');
            const dataVencimento = document.getElementById('data_vencimento');

            dataContrato.addEventListener('change', function() {
                if (dataVencimento.value && this.value >= dataVencimento.value) {
                    alert('A data de início deve ser anterior à data de vencimento.');
                    this.value = '';
                }
            });

            dataVencimento.addEventListener('change', function() {
                if (dataContrato.value && this.value <= dataContrato.value) {
                    alert('A data de vencimento deve ser posterior à data de início.');
                    this.value = '';
                }
            });

            // Atualizar preview inicial
            atualizarPreview();
        });
    </script>
@endpush
