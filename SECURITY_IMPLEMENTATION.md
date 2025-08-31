# 🔒 Implementação de Segurança Multi-Tenant

## 📋 Visão Geral

Este documento descreve a implementação de segurança implementada no sistema de e-commerce para isolar dados entre empresas (multi-tenancy), garantindo que cada empresa veja apenas seus próprios dados.

## 🏗️ Arquitetura Implementada

### 1. Estrutura de Banco de Dados

#### Campos Adicionados
- **`empresa_id`** em todas as tabelas principais:
  - `usuarios`
  - `administradores`
  - `produtos`
  - `categorias`
  - `pedidos`
  - `avaliacoes`
  - `carrinho`
  - `enderecos`
  - `pagamentos`
  - `cupons`
  - `itens_pedido`
  - `formas_pagamento`
  - `configuracoes_sistema`

#### Relacionamentos
- Todas as entidades principais agora têm relacionamento com `Empresa`
- Chaves estrangeiras com `onDelete('cascade')` para manter integridade

### 2. Tipos de Administrador



#### Super Administrador (`super_admin`)
- **Acesso total** ao sistema
- Pode ver dados de **todas** as empresas
- Pode criar/editar empresas
- Pode criar outros administradores
- **NÃO** tem `empresa_id` definido

#### Administrador de Empresa (`admin`)
- **Acesso restrito** apenas aos dados da sua empresa
- **NÃO** pode ver dados de outras empresas
- Pode gerenciar produtos, usuários, pedidos da sua empresa
- **SEMPRE** tem `empresa_id` definido

### 3. Middleware de Segurança

#### AdminMiddleware Atualizado
```php
// Verifica tipo de administrador
if ($admin->isSuperAdmin()) {
    $request->attributes->set('admin_tipo', 'super_admin');
    $request->attributes->set('empresa_id', null);
} elseif ($admin->isEmpresaAdmin()) {
    $request->attributes->set('admin_tipo', 'empresa_admin');
    $request->attributes->set('empresa_id', $admin->empresa_id);
    $request->merge(['empresa_id' => $admin->empresa_id]);
}
```

## 🛡️ Mecanismos de Segurança

### 1. Isolamento de Dados
- **Todas as consultas** são filtradas por `empresa_id`
- **Scopes automáticos** em todos os modelos
- **Validação de propriedade** antes de qualquer operação

### 2. Verificações de Acesso
```php
// Exemplo de verificação em controladores
if ($empresaId && $adminTipo === 'empresa_admin') {
    if ($produto->empresa_id != $empresaId) {
        abort(403, 'Acesso negado. Produto não pertence à sua empresa.');
    }
}
```

### 3. Filtros Automáticos
```php
// Exemplo de filtro automático
$queryProdutos = Produto::with('categoria');

if ($empresaId && $adminTipo === 'empresa_admin') {
    $queryProdutos->porEmpresa($empresaId);
}
```

## 🚀 Como Usar

### 1. Executar Migrações
```bash
# Executar todas as migrações
php artisan migrate

# Se precisar reverter
php artisan migrate:rollback
```

### 2. Popular Banco com Empresas
```bash
# Executar seeder de empresas
php artisan db:seed --class=EmpresaSeeder
```

### 3. Criar Administradores
```bash
# Criar super administrador
php artisan admin:criar --email=admin@loja.com --nome="Super Admin" --senha=123456 --tipo=super_admin

# Criar administrador de empresa
php artisan admin:criar --email=contato@casadecoracao.com --nome="Admin Casa Decoração" --senha=123456 --tipo=admin --empresa=1
```

### 4. Criar Empresa + Administrador
```bash
# Comando interativo que cria empresa e admin
php artisan admin:criar
```

## 📊 Exemplo de Funcionamento

### Cenário 1: Super Administrador
- **Usuário**: `admin@loja.com`
- **Tipo**: `super_admin`
- **Acesso**: Todos os dados de todas as empresas
- **Dashboard**: Estatísticas globais do sistema

### Cenário 2: Administrador de Empresa
- **Usuário**: `contato@casadecoracao.com`
- **Tipo**: `admin`
- **Empresa**: Casa de Decoração (ID: 1)
- **Acesso**: Apenas dados da empresa ID 1
- **Dashboard**: Estatísticas apenas da Casa de Decoração

## 🔍 Verificação de Segurança

### 1. Testar Isolamento
```bash
# Testar sistema multi-tenant
php artisan sistema:testar-multi-tenant
```

### 2. Verificar Acesso
- Fazer login como `contato@casadecoracao.com`
- Acessar `/admin/produtos`
- Verificar se só aparecem produtos da empresa
- Tentar acessar produtos de outras empresas (deve dar erro 403)

### 3. Verificar Logs
- Verificar logs de acesso em `storage/logs/laravel.log`
- Verificar se há tentativas de acesso não autorizado

## ⚠️ Pontos de Atenção

### 1. Dados Existentes
- **ATENÇÃO**: Se já existem dados no banco, eles ficarão com `empresa_id = NULL`
- Considere criar um script para migrar dados existentes

### 2. Performance
- Todas as consultas agora incluem filtro por `empresa_id`
- Certifique-se de que o campo está indexado
- Monitore performance das consultas

### 3. Backup
- **SEMPRE** faça backup antes de executar as migrações
- Teste em ambiente de desenvolvimento primeiro

## 🧪 Testes Recomendados

### 1. Testes de Acesso
- [ ] Super admin pode ver todas as empresas
- [ ] Admin de empresa só vê sua empresa
- [ ] Tentativas de acesso não autorizado são bloqueadas

### 2. Testes de Dados
- [ ] Produtos são filtrados por empresa
- [ ] Usuários são filtrados por empresa
- [ ] Pedidos são filtrados por empresa
- [ ] Avaliações são filtradas por empresa

### 3. Testes de Operações
- [ ] Criar produto em empresa específica
- [ ] Editar produto de outra empresa (deve falhar)
- [ ] Excluir produto de outra empresa (deve falhar)

## 🔧 Solução de Problemas

### Erro: "Acesso negado. Produto não pertence à sua empresa."
- **Causa**: Tentativa de acessar dados de outra empresa
- **Solução**: Verificar se o usuário está logado na empresa correta

### Erro: "Administrador sem empresa associada"
- **Causa**: Administrador não tem `empresa_id` definido
- **Solução**: Atualizar registro do administrador com empresa válida

### Erro: "Foreign key constraint fails"
- **Causa**: Tentativa de excluir empresa com dados relacionados
- **Solução**: Excluir dados relacionados primeiro ou usar soft delete

## 📈 Próximos Passos

### 1. Implementações Futuras
- [ ] Soft delete para empresas
- [ ] Logs de auditoria mais detalhados
- [ ] Relatórios por empresa
- [ ] Backup automático por empresa

### 2. Melhorias de Performance
- [ ] Cache por empresa
- [ ] Índices otimizados
- [ ] Consultas em lote

### 3. Funcionalidades Adicionais
- [ ] Templates personalizados por empresa
- [ ] Configurações específicas por empresa
- [ ] Integrações por empresa

## 📞 Suporte

Para dúvidas ou problemas com a implementação de segurança:

1. Verifique os logs em `storage/logs/laravel.log`
2. Execute `php artisan route:list` para verificar rotas
3. Verifique permissões de banco de dados
4. Teste com dados de exemplo usando os seeders

---

**⚠️ IMPORTANTE: Esta implementação é crítica para a segurança do sistema. Teste completamente antes de colocar em produção!**
