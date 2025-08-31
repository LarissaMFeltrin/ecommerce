@extends('layouts.admin')

@section('title', 'Configurações')
@section('page-title', 'Configurações do Sistema')
@section('page-subtitle', 'Personalize sua loja e configure parâmetros')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <!-- Configurações Gerais -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Configurações Gerais
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.configuracoes.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nome_loja" class="form-label">Nome da Loja *</label>
                                    <input type="text" class="form-control @error('nome_loja') is-invalid @enderror"
                                        id="nome_loja" name="nome_loja" value="{{ old('nome_loja', 'Minha Loja') }}"
                                        required>
                                    @error('nome_loja')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email_contato" class="form-label">Email de Contato *</label>
                                    <input type="email" class="form-control @error('email_contato') is-invalid @enderror"
                                        id="email_contato" name="email_contato"
                                        value="{{ old('email_contato', 'contato@minhaloja.com') }}" required>
                                    @error('email_contato')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefone_contato" class="form-label">Telefone de Contato</label>
                                    <input type="text" class="form-control" id="telefone_contato" name="telefone_contato"
                                        value="{{ old('telefone_contato', '(11) 99999-9999') }}"
                                        placeholder="(11) 99999-9999">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cnpj" class="form-label">CNPJ</label>
                                    <input type="text" class="form-control" id="cnpj" name="cnpj"
                                        value="{{ old('cnpj') }}" placeholder="00.000.000/0000-00">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="endereco_loja" class="form-label">Endereço da Loja</label>
                            <textarea class="form-control" id="endereco_loja" name="endereco_loja" rows="2"
                                placeholder="Rua Exemplo, 123 - Bairro - Cidade/UF - CEP">{{ old('endereco_loja') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="descricao_loja" class="form-label">Descrição da Loja</label>
                            <textarea class="form-control" id="descricao_loja" name="descricao_loja" rows="3"
                                placeholder="Descreva sua loja para os clientes...">{{ old('descricao_loja') }}</textarea>
                            <div class="form-text">Esta descrição aparecerá na página inicial e pode ajudar no SEO.</div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Salvar Configurações Gerais
                        </button>
                    </form>
                </div>
            </div>

            <!-- Configurações de Frete -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-truck me-2"></i>
                        Configurações de Frete
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.configuracoes.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="frete_gratis_acima" class="form-label">Frete Grátis Acima de (R$)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" class="form-control" id="frete_gratis_acima"
                                            name="frete_gratis_acima" value="{{ old('frete_gratis_acima', 100) }}"
                                            step="0.01" min="0">
                                    </div>
                                    <div class="form-text">Deixe em branco para desabilitar frete grátis.</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="taxa_frete" class="form-label">Taxa de Frete Padrão (R$)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" class="form-control" id="taxa_frete" name="taxa_frete"
                                            value="{{ old('taxa_frete', 15.9) }}" step="0.01" min="0"
                                            required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="regioes_frete" class="form-label">Regiões com Frete Especial</label>
                            <textarea class="form-control" id="regioes_frete" name="regioes_frete" rows="3"
                                placeholder="Ex: São Paulo: R$ 10,00&#10;Rio de Janeiro: R$ 12,00&#10;Outros: R$ 15,90">{{ old('regioes_frete') }}</textarea>
                            <div class="form-text">Uma região por linha. Formato: Região: Valor</div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Salvar Configurações de Frete
                        </button>
                    </form>
                </div>
            </div>

            <!-- Configurações de Pagamento -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-credit-card me-2"></i>
                        Configurações de Pagamento
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.configuracoes.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Formas de Pagamento Aceitas</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="pagamento_cartao"
                                            value="1" checked>
                                        <label class="form-check-label">
                                            <i class="bi bi-credit-card me-2"></i>Cartão de Crédito
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="pagamento_pix"
                                            value="1" checked>
                                        <label class="form-check-label">
                                            <i class="bi bi-qr-code me-2"></i>PIX
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="pagamento_boleto"
                                            value="1">
                                        <label class="form-check-label">
                                            <i class="bi bi-upc-scan me-2"></i>Boleto
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="parcelas_maximas" class="form-label">Máximo de Parcelas</label>
                                    <select class="form-select" id="parcelas_maximas" name="parcelas_maximas">
                                        <option value="1">1x sem juros</option>
                                        <option value="2">2x sem juros</option>
                                        <option value="3" selected>3x sem juros</option>
                                        <option value="6">6x sem juros</option>
                                        <option value="12">12x com juros</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="valor_minimo_parcela" class="form-label">Valor Mínimo por Parcela
                                        (R$)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" class="form-control" id="valor_minimo_parcela"
                                            name="valor_minimo_parcela" value="{{ old('valor_minimo_parcela', 5.0) }}"
                                            step="0.01" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Salvar Configurações de Pagamento
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Configurações de SEO -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-search me-2"></i>
                        Configurações de SEO
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.configuracoes.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="meta_title" class="form-label">Meta Title</label>
                            <input type="text" class="form-control" id="meta_title" name="meta_title"
                                value="{{ old('meta_title') }}" placeholder="Título para SEO">
                            <div class="form-text">Título que aparece nos resultados de busca.</div>
                        </div>

                        <div class="mb-3">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea class="form-control" id="meta_description" name="meta_description" rows="3"
                                placeholder="Descrição para SEO">{{ old('meta_description') }}</textarea>
                            <div class="form-text">Descrição que aparece nos resultados de busca.</div>
                        </div>

                        <div class="mb-3">
                            <label for="meta_keywords" class="form-label">Meta Keywords</label>
                            <input type="text" class="form-control" id="meta_keywords" name="meta_keywords"
                                value="{{ old('meta_keywords') }}" placeholder="palavra-chave1, palavra-chave2">
                            <div class="form-text">Palavras-chave separadas por vírgula.</div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Salvar SEO
                        </button>
                    </form>
                </div>
            </div>

            <!-- Configurações de Notificações -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-bell me-2"></i>
                        Notificações
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.configuracoes.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="notificar_novos_pedidos"
                                    value="1" checked>
                                <label class="form-check-label">
                                    Notificar novos pedidos
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="notificar_estoque_baixo"
                                    value="1" checked>
                                <label class="form-check-label">
                                    Notificar estoque baixo
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="notificar_avaliacoes"
                                    value="1" checked>
                                <label class="form-check-label">
                                    Notificar novas avaliações
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email_notificacoes" class="form-label">Email para Notificações</label>
                            <input type="email" class="form-control" id="email_notificacoes" name="email_notificacoes"
                                value="{{ old('email_notificacoes') }}" placeholder="admin@minhaloja.com">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Salvar Notificações
                        </button>
                    </form>
                </div>
            </div>

            <!-- Informações do Sistema -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Informações do Sistema
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Versão do Laravel:</strong>
                        <span class="badge bg-info">{{ app()->version() }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Ambiente:</strong>
                        <span class="badge bg-{{ app()->environment() === 'production' ? 'success' : 'warning' }}">
                            {{ app()->environment() }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <strong>Timezone:</strong>
                        <span class="text-muted">{{ config('app.timezone') }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Locale:</strong>
                        <span class="text-muted">{{ config('app.locale') }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Debug:</strong>
                        <span class="badge bg-{{ config('app.debug') ? 'danger' : 'success' }}">
                            {{ config('app.debug') ? 'Ativado' : 'Desativado' }}
                        </span>
                    </div>

                    <hr>

                    <div class="text-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="limparCache()">
                            <i class="bi bi-trash me-2"></i>Limpar Cache
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function limparCache() {
            if (confirm('Tem certeza que deseja limpar o cache do sistema?')) {
                // Aqui você pode implementar uma chamada AJAX para limpar o cache
                alert('Cache limpo com sucesso!');
            }
        }

        // Validação de CNPJ
        document.getElementById('cnpj').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 14) value = value.slice(0, 14);

            if (value.length > 12) {
                value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1.$2.$3/$4-$5');
            } else if (value.length > 8) {
                value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})$/, '$1.$2.$3/$4');
            } else if (value.length > 5) {
                value = value.replace(/^(\d{2})(\d{3})(\d{3})$/, '$1.$2.$3');
            } else if (value.length > 2) {
                value = value.replace(/^(\d{2})(\d{3})$/, '$1.$2');
            }

            e.target.value = value;
        });

        // Validação de telefone
        document.getElementById('telefone_contato').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);

            if (value.length > 10) {
                value = value.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
            } else if (value.length > 6) {
                value = value.replace(/^(\d{2})(\d{4})(\d{4})$/, '($1) $2-$3');
            } else if (value.length > 2) {
                value = value.replace(/^(\d{2})(\d{4})$/, '($1) $2');
            }

            e.target.value = value;
        });
    </script>
@endpush
