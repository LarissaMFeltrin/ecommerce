# 🛒 Sistema de E-commerce Laravel

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/licenses/MIT)

Um sistema completo de e-commerce desenvolvido com Laravel 12, oferecendo funcionalidades robustas para gestão de produtos, pedidos, pagamentos e experiência do usuário.

## ✨ Funcionalidades Principais

### 🛍️ Gestão de Produtos
- Cadastro e edição de produtos com categorias
- Controle de estoque em tempo real
- Sistema de imagens para produtos
- Busca e filtros avançados
- Categorização inteligente

### 🛒 Sistema de Carrinho
- Adição/remoção de produtos
- Atualização de quantidades
- Persistência de carrinho por usuário
- Cálculo automático de totais

### 📦 Gestão de Pedidos
- Processo completo de checkout
- Múltiplas formas de pagamento
- Controle de status de pedidos
- Histórico de compras
- Cancelamento e reembolso

### 💳 Sistema de Pagamentos
- Integração com gateways de pagamento
- Processamento seguro de transações
- Webhooks para notificações
- Gestão de reembolsos

### ⭐ Sistema de Avaliações
- Avaliações de produtos por usuários
- Moderação de conteúdo
- Respostas de administradores
- Sistema de utilidade (útil/não útil)

### 👤 Gestão de Usuários
- Registro e autenticação
- Perfis personalizáveis
- Histórico de pedidos
- Gestão de endereços

### 🏢 Painel Administrativo
- Dashboard de vendas
- Gestão de produtos e categorias
- Controle de usuários
- Moderação de avaliações
- Relatórios financeiros

## 🚀 Tecnologias Utilizadas

- **Backend**: Laravel 12.x
- **Frontend**: Blade Templates + Vite
- **Banco de Dados**: MySQL/PostgreSQL
- **PHP**: 8.2+
- **Cache**: Redis (opcional)
- **Fila de Jobs**: Laravel Queue

## 📋 Pré-requisitos

- PHP 8.2 ou superior
- Composer 2.0+
- Node.js 18+ e NPM
- MySQL 8.0+ ou PostgreSQL 13+
- Extensões PHP: BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

## 🛠️ Instalação

### 1. Clone o repositório
```bash
git clone https://github.com/seu-usuario/ecommerce-laravel.git
cd ecommerce-laravel
```

### 2. Instale as dependências PHP
```bash
composer install
```

### 3. Instale as dependências Node.js
```bash
npm install
```

### 4. Configure o ambiente
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure o banco de dados
Edite o arquivo `.env` com suas configurações de banco:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 6. Execute as migrações
```bash
php artisan migrate
```

### 7. Execute os seeders (opcional)
```bash
php artisan db:seed
```

### 8. Compile os assets
```bash
npm run build
```

### 9. Inicie o servidor
```bash
php artisan serve
```

## 🗄️ Estrutura do Banco de Dados

### Tabelas Principais
- **usuarios**: Dados dos usuários cadastrados
- **produtos**: Catálogo de produtos
- **categorias**: Categorização de produtos
- **pedidos**: Pedidos realizados pelos usuários
- **itens_pedido**: Itens de cada pedido
- **pagamentos**: Transações de pagamento
- **avaliacoes**: Avaliações dos produtos
- **carrinho**: Carrinho de compras
- **enderecos**: Endereços de entrega
- **cupons**: Sistema de cupons de desconto

## 🔐 Autenticação e Autorização

O sistema utiliza o sistema de autenticação nativo do Laravel com middlewares personalizados:

- **auth**: Verifica se o usuário está logado
- **admin**: Verifica se o usuário é administrador
- **ajax.auth**: Middleware para requisições AJAX autenticadas

## 🛣️ Rotas Principais

### Públicas
- `/` - Página inicial
- `/produtos` - Lista de produtos
- `/produtos/{produto}` - Detalhes do produto
- `/categoria/{slug}` - Produtos por categoria
- `/login` - Página de login
- `/register` - Página de registro

### Protegidas (requerem login)
- `/perfil` - Perfil do usuário
- `/carrinho` - Carrinho de compras
- `/pedidos` - Histórico de pedidos
- `/avaliacoes/minhas` - Avaliações do usuário

### Administrativas
- `/admin/avaliacoes` - Moderação de avaliações
- `/admin/produtos` - Gestão de produtos
- `/admin/usuarios` - Gestão de usuários

## 🔧 Configurações

### Variáveis de Ambiente Importantes
```env
APP_NAME="Seu E-commerce"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce
DB_USERNAME=usuario
DB_PASSWORD=senha

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha
MAIL_ENCRYPTION=tls
```

## 📊 Comandos Artisan Úteis

```bash
# Limpar cache
php artisan cache:clear

# Limpar configurações
php artisan config:clear

# Limpar rotas
php artisan route:clear

# Limpar views
php artisan view:clear

# Listar rotas
php artisan route:list

# Executar testes
php artisan test

# Criar usuário administrador
php artisan make:admin

# Gerar dados de teste
php artisan db:seed --class=TestDataSeeder
```

## 🧪 Testes

O projeto inclui testes automatizados para garantir a qualidade do código:

```bash
# Executar todos os testes
php artisan test

# Executar testes específicos
php artisan test --filter=ProdutoTest

# Executar testes com cobertura
php artisan test --coverage
```

## 🚀 Deploy

### Produção
1. Configure as variáveis de ambiente para produção
2. Execute `composer install --optimize-autoloader --no-dev`
3. Execute `npm run build`
4. Configure o servidor web (Apache/Nginx)
5. Configure o supervisor para filas
6. Configure o cron para tarefas agendadas

### Docker (opcional)
```bash
# Usando Laravel Sail
./vendor/bin/sail up -d

# Ou com Docker Compose personalizado
docker-compose up -d
```

## 📈 Monitoramento e Logs

- **Logs de acesso**: Tabela `logs_acesso`
- **Logs de Laravel**: `storage/logs/`
- **Monitoramento de erros**: Integração com serviços externos
- **Métricas de performance**: Cache e queries otimizadas

## 🔒 Segurança

- Validação de entrada em todos os formulários
- Proteção CSRF em todas as rotas
- Sanitização de dados
- Controle de acesso baseado em roles
- Logs de auditoria para ações críticas

## 🤝 Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📝 Licença

Este projeto está licenciado sob a Licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## 📞 Suporte

- **Issues**: [GitHub Issues](https://github.com/seu-usuario/ecommerce-laravel/issues)
- **Documentação**: [Wiki do Projeto](https://github.com/seu-usuario/ecommerce-laravel/wiki)
- **Email**: suporte@seudominio.com

## 🙏 Agradecimentos

- [Laravel](https://laravel.com) - Framework PHP
- [Bootstrap](https://getbootstrap.com) - Framework CSS
- [Vite](https://vitejs.dev) - Build tool
- Comunidade Laravel Brasil

---

**Desenvolvido com ❤️ usando Laravel**

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
