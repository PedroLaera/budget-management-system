Projeto de teste: Budget Management System

Estrutura:

- `backend`: API Laravel e banco de dados
- `frontend`: aplicação React (Vite)

Como rodar com Docker:

1. Criar arquivo de ambiente do backend:

    cp backend/.env.example backend/.env

2. Subir os containers:

    docker compose up -d

3. Gerar chave do Laravel:

    docker compose exec app php artisan key:generate

4. Rodar migrations e seed:
   
    docker compose exec app php artisan migrate:fresh --seed

Acessos:

- Backend (API): `http://localhost:8000`
- Frontend (React): `http://localhost:5173`

Rotas principais da API:

- http://localhost:8000/api/products - produtos existentes
- http://localhost:8000/api/budgets  - orçamentos depois de feitos

Execução local sem Docker:

_____________Backend:

cd backend

composer install

cp .env.example .env

php artisan key:generate

php artisan migrate:fresh --seed

php artisan serve

_____________Frontend:

cd frontend

npm install

npm run dev
