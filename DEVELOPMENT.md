# 🚀 Guia de Desenvolvimento - Sistema de E-commerce

Este documento fornece informações detalhadas para desenvolvedores que trabalham no sistema de e-commerce.

## 🏗️ Arquitetura do Sistema

### Estrutura de Diretórios
```
ecommerce/
├── app/
│   ├── Console/           # Comandos Artisan personalizados
│   ├── Http/
│   │   ├── Controllers/   # Controladores da aplicação
│   │   ├── Middleware/    # Middlewares personalizados
│   │   └── Requests/      # Validações de formulários
│   ├── Models/            # Modelos Eloquent
│   ├── Notifications/     # Notificações do sistema
│   └── Providers/         # Service Providers
├── database/
│   ├── factories/         # Factories para testes
│   ├── migrations/        # Migrações do banco
│   └── seeders/          # Seeders para dados iniciais
├── resources/
│   ├── views/             # Templates Blade
│   ├── css/               # Estilos CSS
│   └── js/                # JavaScript
└── routes/                # Definição de rotas
```

## 🗄️ Modelos e Relacionamentos

### Produto
```php
class Produto extends Model
{
    // Relacionamentos
    public function categoria()        // belongsTo Categoria
    public function carrinho()         // hasMany Carrinho
    public function itensPedido()      // hasMany ItemPedido
    public function avaliacoes()       // hasMany Avaliacao
    
    // Scopes
    public function scopeAtivo()       // Produtos ativos
    public function scopeEmEstoque()   // Produtos com estoque > 0
}
```

### Usuario
```php
class Usuario extends Authenticatable
{
    // Relacionamentos
    public function pedidos()          // hasMany Pedido
    public function enderecos()        // hasMany Endereco
    public function avaliacoes()       // hasMany Avaliacao
    public function carrinho()         // hasMany Carrinho
}
```

### Pedido
```php
class Pedido extends Model
{
    // Relacionamentos
    public function usuario()          // belongsTo Usuario
    public function itens()            // hasMany ItemPedido
    public function pagamento()        // hasOne Pagamento
    public function endereco()         // belongsTo Endereco
}
```

## 🔐 Sistema de Autenticação

### Middlewares Personalizados

#### admin
```php
// Verifica se o usuário é administrador
Route::middleware(['auth', 'admin'])->group(function () {
    // Rotas administrativas
});
```

#### ajax.auth
```php
// Middleware para requisições AJAX autenticadas
Route::post('/api/endpoint')->middleware('ajax.auth');
```

### Controle de Acesso
- **Usuários comuns**: Acesso a perfil, carrinho, pedidos
- **Administradores**: Acesso completo ao sistema
- **Visitantes**: Acesso apenas a produtos e registro/login

## 🛣️ Sistema de Rotas

### Agrupamento por Funcionalidade
```php
// Rotas públicas
Route::get('/', 'HomeController@index')->name('home');

// Rotas de produtos
Route::prefix('produtos')->group(function () {
    Route::get('/', 'ProdutoController@index')->name('produtos.index');
    Route::get('/{produto}', 'ProdutoController@show')->name('produtos.show');
});

// Rotas protegidas
Route::middleware('auth')->group(function () {
    Route::prefix('perfil')->group(function () {
        Route::get('/', 'AuthController@perfil')->name('perfil');
        Route::put('/', 'AuthController@atualizarPerfil')->name('perfil.atualizar');
    });
});
```

## 💳 Sistema de Pagamentos

### Fluxo de Pagamento
1. **Criação do Pedido**: Usuário finaliza checkout
2. **Processamento**: Sistema processa pagamento via gateway
3. **Confirmação**: Webhook recebe confirmação
4. **Atualização**: Status do pedido é atualizado

### Webhooks
```php
Route::post('/webhooks/pagamento', 'PagamentoController@webhook')
    ->name('webhooks.pagamento');
```

### Status de Pagamento
- `pendente`: Aguardando processamento
- `aprovado`: Pagamento confirmado
- `rejeitado`: Pagamento negado
- `cancelado`: Pagamento cancelado
- `reembolsado`: Valor devolvido

## ⭐ Sistema de Avaliações

### Moderação de Conteúdo
- **Aprovação automática**: Avaliações de usuários verificados
- **Moderação manual**: Avaliações que precisam de revisão
- **Sistema de denúncias**: Usuários podem reportar conteúdo inadequado

### Métricas de Qualidade
- **Rating médio**: Calculado automaticamente
- **Total de avaliações**: Contador em tempo real
- **Distribuição de notas**: Histograma de avaliações

## 🛒 Sistema de Carrinho

### Persistência de Dados
- **Sessão**: Carrinho temporário para visitantes
- **Banco de dados**: Carrinho persistente para usuários logados
- **Sincronização**: Merge automático ao fazer login

### Operações Disponíveis
```php
// Adicionar produto
POST /carrinho/adicionar

// Atualizar quantidade
PUT /carrinho/{item}

// Remover produto
DELETE /carrinho/{item}

// Limpar carrinho
DELETE /carrinho
```

## 📊 Dashboard Administrativo

### Métricas Principais
- **Vendas**: Total de vendas por período
- **Produtos**: Produtos mais vendidos
- **Usuários**: Novos cadastros
- **Avaliações**: Avaliações pendentes de moderação

### Funcionalidades
- **Gestão de produtos**: CRUD completo
- **Controle de estoque**: Atualização em massa
- **Gestão de usuários**: Bloqueio/desbloqueio
- **Relatórios**: Exportação de dados

## 🔧 Configurações de Ambiente

### Desenvolvimento
```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_DATABASE=ecommerce_dev
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
```

### Produção
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com

DB_CONNECTION=mysql
DB_DATABASE=ecommerce_prod
DB_USERNAME=prod_user
DB_PASSWORD=strong_password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

## 🧪 Testes

### Estrutura de Testes
```
tests/
├── Feature/               # Testes de funcionalidades
│   ├── ProdutoTest.php
│   ├── CarrinhoTest.php
│   └── PedidoTest.php
├── Unit/                  # Testes unitários
│   ├── Models/
│   └── Services/
└── TestCase.php           # Classe base para testes
```

### Exemplo de Teste
```php
class ProdutoTest extends TestCase
{
    public function test_produto_pode_ser_criado()
    {
        $produto = Produto::factory()->create();
        
        $this->assertDatabaseHas('produtos', [
            'id' => $produto->id
        ]);
    }
}
```

## 📦 Deploy e Manutenção

### Comandos de Deploy
```bash
# Atualizar código
git pull origin main

# Instalar dependências
composer install --optimize-autoloader --no-dev

# Compilar assets
npm run build

# Executar migrações
php artisan migrate --force

# Limpar caches
php artisan optimize

# Reiniciar serviços
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

### Monitoramento
- **Logs**: `storage/logs/laravel.log`
- **Performance**: Cache Redis
- **Banco de dados**: Queries lentas
- **Erros**: Integração com serviços externos

## 🚀 Otimizações Recomendadas

### Performance
- **Cache de consultas**: Redis para queries frequentes
- **Lazy loading**: Evitar N+1 queries
- **Compressão**: Gzip para assets estáticos
- **CDN**: Para imagens e arquivos estáticos

### Segurança
- **HTTPS**: Certificado SSL obrigatório
- **Rate limiting**: Proteção contra ataques
- **Validação**: Sanitização de entrada
- **Logs de auditoria**: Rastreamento de ações

## 📚 Recursos Adicionais

### Documentação
- [Laravel 12.x](https://laravel.com/docs)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [Blade Templates](https://laravel.com/docs/blade)

### Ferramentas Úteis
- **Laravel Debugbar**: Debug em desenvolvimento
- **Laravel Telescope**: Monitoramento de aplicação
- **Laravel Horizon**: Monitoramento de filas

---

**Para dúvidas técnicas, consulte a documentação oficial do Laravel ou abra uma issue no repositório.**
