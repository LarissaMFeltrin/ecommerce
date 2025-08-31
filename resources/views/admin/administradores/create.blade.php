@extends('layouts.admin')

@section('title', 'Criar Administrador')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i>
                        Criar Novo Administrador
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.administradores.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome Completo *</label>
                                    <input type="text" class="form-control @error('nome') is-invalid @enderror"
                                        id="nome" name="nome" value="{{ old('nome') }}" required>
                                    @error('nome')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tipo" class="form-label">Tipo de Acesso *</label>
                                    <select class="form-select @error('tipo') is-invalid @enderror" id="tipo"
                                        name="tipo" required>
                                        <option value="">Selecione o tipo</option>
                                        <option value="admin" {{ old('tipo') == 'admin' ? 'selected' : '' }}>Administrador
                                        </option>
                                        <option value="super_admin" {{ old('tipo') == 'super_admin' ? 'selected' : '' }}>
                                            Super Administrador</option>
                                    </select>
                                    @error('tipo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Este email será usado para fazer login no sistema.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="senha" class="form-label">Senha *</label>
                                    <input type="password" class="form-control @error('senha') is-invalid @enderror"
                                        id="senha" name="senha" required>
                                    @error('senha')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Mínimo de 6 caracteres.</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="senha_confirmation" class="form-label">Confirmar Senha *</label>
                                    <input type="password" class="form-control" id="senha_confirmation"
                                        name="senha_confirmation" required>
                                    <div class="form-text">Digite a mesma senha novamente.</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.administradores.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-2"></i>Criar Administrador
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Informações sobre Tipos -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Tipos de Administrador
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary">
                            <i class="bi bi-shield me-2"></i>Administrador
                        </h6>
                        <p class="text-muted small mb-0">
                            Acesso completo ao painel administrativo, pode gerenciar produtos, pedidos, usuários e
                            configurações.
                        </p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-danger">
                            <i class="bi bi-shield-star me-2"></i>Super Administrador
                        </h6>
                        <p class="text-muted small mb-0">
                            Acesso total ao sistema, incluindo criação de outros administradores e configurações avançadas.
                        </p>
                    </div>

                    <hr>

                    <div class="alert alert-info">
                        <i class="bi bi-lightbulb me-2"></i>
                        <strong>Dica:</strong> Use "Super Administrador" apenas para usuários de confiança que precisam de
                        acesso total ao sistema.
                    </div>
                </div>
            </div>

            <!-- Dicas de Segurança -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-shield-lock me-2"></i>
                        Dicas de Segurança
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Use senhas fortes (mínimo 8 caracteres)
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Evite usar dados pessoais na senha
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Troque a senha periodicamente
                        </li>
                        <li>
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Não compartilhe credenciais
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validação de senha em tempo real
            const senha = document.getElementById('senha');
            const confirmarSenha = document.getElementById('senha_confirmation');

            function validarSenhas() {
                if (senha.value !== confirmarSenha.value) {
                    confirmarSenha.setCustomValidity('As senhas não coincidem');
                } else {
                    confirmarSenha.setCustomValidity('');
                }
            }

            senha.addEventListener('input', validarSenhas);
            confirmarSenha.addEventListener('input', validarSenhas);

            // Validação de força da senha
            senha.addEventListener('input', function() {
                const valor = this.value;
                let forca = 0;

                if (valor.length >= 6) forca++;
                if (valor.length >= 8) forca++;
                if (/[a-z]/.test(valor)) forca++;
                if (/[A-Z]/.test(valor)) forca++;
                if (/[0-9]/.test(valor)) forca++;
                if (/[^A-Za-z0-9]/.test(valor)) forca++;

                // Remover classes anteriores
                this.classList.remove('is-valid', 'is-warning', 'is-danger');

                // Adicionar classe baseada na força
                if (forca >= 4) {
                    this.classList.add('is-valid');
                } else if (forca >= 2) {
                    this.classList.add('is-warning');
                } else if (valor.length > 0) {
                    this.classList.add('is-danger');
                }
            });
        });
    </script>
@endpush
