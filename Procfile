# Procfile — used by Railway when deploying separate services from this repo.
#
# Railway runs ONE process per service. Deploy this repo twice:
#   - Service 1 uses the "web" command (set via railway.toml or the dashboard)
#   - Service 2 overrides the start command with: php artisan horizon
#
# The web process migrates the DB on each deploy before starting the server.
web: php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
worker: php artisan horizon
