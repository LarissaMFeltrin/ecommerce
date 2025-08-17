.PHONY: help install test build docker-up docker-down docker-restart docker-logs docker-shell

help: ## Mostra esta mensagem de ajuda
	@echo "Comandos disponíveis:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

install: ## Instala as dependências do projeto
	composer install
	npm install

test: ## Executa os testes
	php artisan test

test-coverage: ## Executa os testes com cobertura
	php artisan test --coverage-html coverage

build: ## Compila os assets
	npm run build

docker-up: ## Inicia os containers Docker
	docker-compose up -d

docker-down: ## Para os containers Docker
	docker-compose down

docker-restart: ## Reinicia os containers Docker
	docker-compose restart

docker-logs: ## Mostra os logs dos containers
	docker-compose logs -f

docker-shell: ## Acessa o shell do container da aplicação
	docker-compose exec app bash

migrate: ## Executa as migrações
	php artisan migrate

migrate-fresh: ## Executa as migrações do zero
	php artisan migrate:fresh --seed

seed: ## Executa os seeders
	php artisan db:seed

cache-clear: ## Limpa todos os caches
	php artisan cache:clear
	php artisan config:clear
	php artisan route:clear
	php artisan view:clear

optimize: ## Otimiza a aplicação para produção
	php artisan optimize
	php artisan config:cache
	php artisan route:cache
	php artisan view:cache

code-quality: ## Executa ferramentas de qualidade de código
	./vendor/bin/pint
	./vendor/bin/phpstan analyse app --level=8
	./vendor/bin/php-cs-fixer fix --dry-run --diff

security-check: ## Verifica vulnerabilidades de segurança
	./vendor/bin/security-checker security:check composer.lock

setup: ## Configura o ambiente de desenvolvimento
	cp .env.example .env
	php artisan key:generate
	composer install
	npm install
	php artisan migrate --seed
	npm run build

production: ## Prepara a aplicação para produção
	composer install --optimize-autoloader --no-dev
	npm ci
	npm run build
	php artisan optimize
	php artisan config:cache
	php artisan route:cache
	php artisan view:cache
