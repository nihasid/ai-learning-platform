# BrightSteps Learning

A stable Laravel 13 parent-guided, child-facing learning application scaffolded with:

- Laravel Breeze using Blade views
- Tailwind CSS and Vite
- Laravel Sanctum
- UUID primary keys for users
- Laravel scheduler support
- Docker Compose runtime

Parents can manage child profiles, create curriculum activities across literacy, numeracy, motor skills, and social-emotional learning, and track child progress. Children use a colorful tap-friendly play mode with big buttons and browser speech guidance.

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

This project requires the PHP version specified by Composer.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run dev
php artisan serve
```
