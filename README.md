# Laravel Task Board — Railway Deployment Test

A minimal Kanban-style task board built with Laravel 11, PostgreSQL, Redis, and Laravel Horizon.
Its purpose is to verify that Railway can correctly build and run a PHP/Laravel application with a
database, cache, and background-worker process.

## Features

- Create and delete boards
- Create, delete, and move tasks across three statuses: **To Do**, **In Progress**, **Done**
- When a task is moved to **Done**, a queued job (`TaskCompletedJob`) is dispatched and logged — this
  exercises the Redis queue driver and the Horizon worker
- `/health` endpoint — returns JSON with connectivity status for PostgreSQL and Redis
- `/horizon` dashboard — Horizon queue monitoring UI

---

## Deploying to Railway

### 1. Create a new Railway project

Log in to [Railway](https://railway.com) and create a new **Empty Project**.

### 2. Add a PostgreSQL service

Inside the project, click **+ New** → **Database** → **PostgreSQL**.

Railway will automatically provision the database and expose the following variables:

| Variable | Description |
|---|---|
| `DB_HOST` | Postgres host |
| `DB_PORT` | Postgres port (usually 5432) |
| `DB_DATABASE` | Database name |
| `DB_USERNAME` | Database user |
| `DB_PASSWORD` | Database password |

### 3. Add a Redis service

Click **+ New** → **Database** → **Redis**.

Railway will expose:

| Variable | Description |
|---|---|
| `REDIS_HOST` | Redis host |
| `REDIS_PORT` | Redis port (usually 6379) |
| `REDIS_PASSWORD` | Redis password |

### 4. Deploy the web service

Click **+ New** → **GitHub Repo** and select this repository.

Railway will detect the `railway.toml` / `nixpacks.toml` and build automatically.

**Link the Postgres and Redis services** to this service so Railway injects their variables.

Set the following additional environment variables in the Railway dashboard:

| Variable | Value |
|---|---|
| `APP_KEY` | Generate with `php artisan key:generate --show`, or use `base64:` + 32 random bytes |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | Your Railway-generated URL, e.g. `https://task-board-production.up.railway.app` |
| `REDIS_CLIENT` | `predis` |
| `CACHE_STORE` | `redis` |
| `SESSION_DRIVER` | `redis` |
| `QUEUE_CONNECTION` | `redis` |
| `LOG_CHANNEL` | `stderr` |

> **Generating APP_KEY without a local PHP install:**
> Run `php artisan key:generate --show` in a temporary Docker container:
> ```
> docker run --rm php:8.2-cli php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
> ```

### 5. Deploy the queue worker (Horizon)

Railway services are single-process. To run the Horizon worker alongside the web process:

1. In the same Railway project, click **+ New** → **GitHub Repo** and select the same repository.
2. In the new service settings, go to **Settings** → **Deploy** and set the **Custom Start Command** to:
   ```
   php artisan horizon
   ```
3. Link the same PostgreSQL and Redis services and copy all the same environment variables.
4. This service does not need a public domain or health check.

> The worker service shares the same code, same database, and same Redis instance as the web service.
> Horizon's dashboard at `/horizon` on the web service will show jobs processed by the worker.

### 6. Seed the database (optional)

After the first successful deploy, run the seeder via a Railway one-off command:

```
php artisan db:seed
```

You can trigger this from Railway's dashboard under the web service → **Shell**, or by temporarily
changing the start command to include `&& php artisan db:seed`.

---

## Environment Variable Reference

```env
APP_NAME="Task Board"
APP_ENV=production
APP_KEY=base64:...            # Required — generate before first deploy
APP_DEBUG=false
APP_URL=https://...           # Your Railway domain

LOG_CHANNEL=stderr

# PostgreSQL (injected by Railway when Postgres service is linked)
DB_CONNECTION=pgsql
DB_HOST=
DB_PORT=5432
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

# Redis (injected by Railway when Redis service is linked)
REDIS_CLIENT=predis
REDIS_HOST=
REDIS_PORT=6379
REDIS_PASSWORD=

# Cache, session, queue
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

---

## Local Development

Requirements: PHP 8.2+, Composer, PostgreSQL, Redis.

```bash
composer install
cp .env.example .env
# Edit .env with your local DB and Redis credentials
php artisan key:generate
php artisan migrate --seed
php artisan serve          # Web server at http://localhost:8000
php artisan horizon        # Queue worker (separate terminal)
```

Visit `http://localhost:8000/health` to verify connectivity.

---

## Project Structure

```
app/
  Http/Controllers/
    BoardController.php     # CRUD for boards
    TaskController.php      # CRUD + status moves for tasks
    HealthController.php    # /health endpoint
  Jobs/
    TaskCompletedJob.php    # Queued job dispatched when task → done
  Models/
    Board.php
    Task.php
database/
  migrations/               # users, boards, tasks, jobs tables
  seeders/
    DatabaseSeeder.php      # Sample boards and tasks
resources/views/
  layouts/app.blade.php
  boards/
    index.blade.php         # Board list
    show.blade.php          # Kanban board view
routes/
  web.php
railway.toml                # Railway build + deploy config
nixpacks.toml               # Nixpacks PHP 8.2 + extensions config
```

## Endpoints

| Method | Path | Description |
|---|---|---|
| GET | `/` | Board list |
| POST | `/boards` | Create board |
| GET | `/boards/{board}` | Kanban board view |
| DELETE | `/boards/{board}` | Delete board |
| POST | `/boards/{board}/tasks` | Create task |
| PATCH | `/tasks/{task}` | Update task status |
| DELETE | `/tasks/{task}` | Delete task |
| GET | `/health` | Health check (JSON) |
| GET | `/horizon` | Horizon dashboard |
| GET | `/up` | Laravel built-in readiness check |
