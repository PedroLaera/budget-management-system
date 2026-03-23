Projeto de teste: Budget Management System
Como rodar:

cp .env.example .env

    obs.: cria o arquivo de configuração local do projeto a partir do modelo (.env.example).
    
php artisan key:generate

    obs.: gera a chave de segurança da aplicação (APP_KEY), necessária para criptografia e funcionamento do Laravel.

docker compose up -d

    obs.: suba os containers e aguarde alguns segundos antes do próximo comando.

docker compose exec app php artisan migrate:fresh --seed

    obs.: cria as tabelas no banco e insere os produtos iniciais.

Acesse: http://localhost:8000
