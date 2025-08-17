# 🚀 Guia de Deploy - Sistema de E-commerce

Este documento fornece instruções detalhadas para fazer o deploy do sistema de e-commerce em diferentes ambientes.

## 📋 Pré-requisitos

### Servidor
- **Sistema Operacional**: Ubuntu 20.04+ ou CentOS 8+
- **RAM**: Mínimo 2GB, recomendado 4GB+
- **CPU**: 2 cores+
- **Disco**: Mínimo 20GB de espaço livre
- **Rede**: Acesso à internet e porta 80/443 abertas

### Software
- **PHP**: 8.2+
- **MySQL**: 8.0+ ou PostgreSQL 13+
- **Nginx**: 1.18+ ou Apache 2.4+
- **Redis**: 6.0+ (opcional, mas recomendado)
- **Composer**: 2.0+
- **Node.js**: 18+ e NPM

## 🏗️ Estrutura de Deploy

### Ambientes
1. **Desenvolvimento**: Para desenvolvimento local
2. **Staging**: Para testes antes da produção
3. **Produção**: Ambiente final para usuários

### Estratégia de Deploy
- **Deploy Automático**: Via GitHub Actions
- **Rollback**: Capacidade de reverter para versão anterior
- **Zero Downtime**: Deploy sem interrupção do serviço

## 🐳 Deploy com Docker (Recomendado)

### 1. Preparar o Servidor
```bash
# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Instalar Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Adicionar usuário ao grupo docker
sudo usermod -aG docker $USER
```

### 2. Configurar Variáveis de Ambiente
```bash
# Copiar arquivo de exemplo
cp env.example .env

# Editar configurações
nano .env
```

**Configurações de Produção:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com

DB_CONNECTION=mysql
DB_HOST=db
DB_DATABASE=ecommerce_prod
DB_USERNAME=ecommerce_user
DB_PASSWORD=senha_forte_aqui

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-app
MAIL_ENCRYPTION=tls
```

### 3. Deploy
```bash
# Iniciar serviços
docker-compose up -d

# Verificar status
docker-compose ps

# Ver logs
docker-compose logs -f
```

## 🖥️ Deploy Manual (Sem Docker)

### 1. Configurar Servidor Web

#### Nginx
```bash
# Instalar Nginx
sudo apt install nginx -y

# Configurar site
sudo nano /etc/nginx/sites-available/ecommerce
```

**Configuração Nginx:**
```nginx
server {
    listen 80;
    server_name seudominio.com www.seudominio.com;
    root /var/www/ecommerce/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Ativar site
sudo ln -s /etc/nginx/sites-available/ecommerce /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### PHP-FPM
```bash
# Instalar PHP-FPM
sudo apt install php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-redis -y

# Configurar PHP
sudo nano /etc/php/8.2/fpm/php.ini
```

**Configurações PHP importantes:**
```ini
memory_limit = 512M
upload_max_filesize = 40M
post_max_size = 40M
max_execution_time = 600
date.timezone = America/Sao_Paulo
```

### 2. Configurar Banco de Dados
```bash
# Instalar MySQL
sudo apt install mysql-server -y

# Configurar MySQL
sudo mysql_secure_installation

# Criar banco e usuário
sudo mysql -u root -p
```

```sql
CREATE DATABASE ecommerce_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ecommerce_user'@'localhost' IDENTIFIED BY 'senha_forte_aqui';
GRANT ALL PRIVILEGES ON ecommerce_prod.* TO 'ecommerce_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Deploy da Aplicação
```bash
# Clonar repositório
cd /var/www
sudo git clone https://github.com/seu-usuario/ecommerce-laravel.git ecommerce
sudo chown -R www-data:www-data ecommerce
cd ecommerce

# Instalar dependências
composer install --optimize-autoloader --no-dev
npm ci
npm run build

# Configurar ambiente
cp env.example .env
nano .env

# Gerar chave da aplicação
php artisan key:generate

# Configurar permissões
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Executar migrações
php artisan migrate --force

# Otimizar aplicação
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔐 Configuração de SSL

### Certbot (Let's Encrypt)
```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obter certificado
sudo certbot --nginx -d seudominio.com -d www.seudominio.com

# Renovação automática
sudo crontab -e
```

**Adicionar linha:**
```
0 12 * * * /usr/bin/certbot renew --quiet
```

## 📊 Monitoramento e Logs

### Supervisor (Para Filas)
```bash
# Instalar Supervisor
sudo apt install supervisor -y

# Configurar Laravel Queue
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

**Configuração:**
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ecommerce/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/laravel-worker.log
stopwaitsecs=3600
```

```bash
# Ativar Supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### Logs
```bash
# Ver logs da aplicação
tail -f /var/www/ecommerce/storage/logs/laravel.log

# Ver logs do Nginx
sudo tail -f /var/log/nginx/access.log
sudo tail -f /var/log/nginx/error.log

# Ver logs do PHP-FPM
sudo tail -f /var/log/php8.2-fpm.log
```

## 🔄 Deploy Automático

### GitHub Actions
O projeto já inclui configuração para CI/CD automático. Para ativar:

1. **Configurar Secrets no GitHub:**
   - `DEPLOY_HOST`: IP do servidor
   - `DEPLOY_USER`: Usuário do servidor
   - `DEPLOY_KEY`: Chave SSH privada

2. **Configurar Deploy:**
   - Push para `main` → Deploy em produção
   - Push para `develop` → Deploy em staging

### Script de Deploy
```bash
#!/bin/bash
# deploy.sh

echo "🚀 Iniciando deploy..."

# Atualizar código
git pull origin main

# Instalar dependências
composer install --optimize-autoloader --no-dev
npm ci
npm run build

# Executar migrações
php artisan migrate --force

# Limpar caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Otimizar
php artisan optimize

# Reiniciar serviços
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx

echo "✅ Deploy concluído!"
```

## 🚨 Troubleshooting

### Problemas Comuns

#### Erro 500
```bash
# Verificar logs
tail -f storage/logs/laravel.log

# Verificar permissões
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### Erro de Conexão com Banco
```bash
# Testar conexão
php artisan tinker
DB::connection()->getPdo();

# Verificar configurações
php artisan config:show database
```

#### Performance Lenta
```bash
# Verificar cache
php artisan cache:clear
php artisan config:cache

# Verificar Redis
redis-cli ping

# Verificar filas
php artisan queue:work --once
```

## 📈 Otimizações de Produção

### Cache
- **Redis**: Para cache de sessões e dados
- **OPcache**: Para cache de código PHP
- **Nginx**: Para cache de arquivos estáticos

### Performance
- **CDN**: Para imagens e assets
- **Compressão**: Gzip para arquivos
- **Minificação**: CSS e JavaScript otimizados

### Segurança
- **Firewall**: UFW ou iptables
- **Fail2ban**: Proteção contra ataques
- **Backup**: Automático e criptografado

## 🔄 Backup e Restore

### Backup Automático
```bash
#!/bin/bash
# backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backup/ecommerce"

# Backup do banco
mysqldump -u ecommerce_user -p ecommerce_prod > $BACKUP_DIR/db_$DATE.sql

# Backup dos arquivos
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/ecommerce

# Manter apenas últimos 7 backups
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
```

### Cron para Backup
```bash
# Adicionar ao crontab
0 2 * * * /var/www/ecommerce/backup.sh
```

---

**Para suporte técnico, consulte a documentação ou abra uma issue no repositório.**
