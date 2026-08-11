# Docker (production / server)

Local development stays the same (`composer run dev`). Use Docker only on the server.

## Files


| File                  | Purpose                                                  |
| --------------------- | -------------------------------------------------------- |
| `Dockerfile`          | Builds PHP-FPM app + Nginx images (Vite assets baked in) |
| `docker-compose.yml`  | `app`, `nginx`, `mysql`, `queue`, `scheduler`            |
| `.env.docker.example` | Server env template (`DB_HOST=mysql`)                    |
| `docker/`             | Nginx, PHP, and entrypoint scripts                       |


## First deploy on the server

```bash
git clone https://github.com/Abrahamsiasy/hanibal bet-app
cd bet-app

cp .env.docker.example .env
# Edit .env: APP_KEY, APP_URL, DB_PASSWORD, MYSQL_ROOT_PASSWORD, admin passwords
```

Generate an app key on any machine with PHP/Laravel, or temporarily:

```bash
docker compose run --rm --no-deps -e RUN_MIGRATIONS=false -e CACHE_CONFIG=false app php artisan key:generate --show
```

Paste the value into `.env` as `APP_KEY=base64:...`.

Then build and start:

```bash
docker compose up -d --build
```

App listens on port `APP_PORT` (default **80**).

## Updates after `git pull`

```bash
cd bet-app
git pull
docker compose up -d --build
```

Migrations run automatically when the `app` container starts.

## Useful commands

```bash
# Logs
docker compose logs -f app nginx queue

# Shell into app
docker compose exec app sh

# Artisan
docker compose exec app php artisan about
docker compose exec app php artisan migrate --force

# Seed (only if you want demo users)
docker compose exec app php artisan db:seed --force

# Stop
docker compose down

# Stop and wipe DB + uploaded files volumes (destructive)
docker compose down -v
```



## Persistence

Named volumes keep data across rebuilds:

- `mysql_data` — database
- `storage_public` — uploaded images (`events/`, `participants/`, etc.)
- `storage_logs` — Laravel logs



## Notes

- Do **not** commit `.env`. Keep secrets only on the server.
- `public/build` is produced inside the image; you do not need Node on the server.
- Put a reverse proxy (Caddy/Nginx/Cloudflare) in front for HTTPS when you have a domain.
- To avoid exposing MySQL publicly, remove the `ports:` section under `mysql` in `docker-compose.yml`.

