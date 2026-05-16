# ai-task-manager

A stable Laravel 13 application scaffolded with:

- Laravel Breeze using Blade views
- Tailwind CSS and Vite
- Laravel Sanctum
- UUID primary keys for users
- Laravel scheduler support
- Docker Compose runtime

## Run With Docker

Start Docker Desktop, then run:

```bash
docker compose up --build
```

The app will be available at:

```text
http://localhost:8000
```

On first startup, the container creates `.env`, creates the SQLite database file, generates the app key, and runs migrations.

## Useful Commands

```bash
docker compose exec app php artisan test
docker compose exec app php artisan migrate
docker compose exec app php artisan schedule:list
docker compose exec app npm run dev
```

The `scheduler` service runs `php artisan schedule:work`. A default scheduled task is included to prune expired Sanctum tokens daily.

## Local Development Without Docker

This project requires PHP 8.3 or newer.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run dev
php artisan serve
```
