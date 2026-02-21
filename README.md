# PixelHavn

PixelHavn is a Habbo-inspired MVP web game by Nextarch Studio.

## Stack

- Backend: PHP 8.2 + CodeIgniter 4.7.0
- DB: MariaDB 11
- Cache/session/rate-limit backing: Redis 7
- Realtime: Node.js 20 + Socket.IO 4
- Containers: Docker Compose (app + nginx + realtime + mariadb + redis)

## Features

- Username/email + password auth
- Optional passkeys (WebAuthn) registration/login
- Admin switch to disable password login (`auth_password_enabled`)
- Dashboard, room list/view, chat panel, presence list, simple move sync
- Inventory + item placement endpoint
- Admin panel: users/roles moderation, rooms, items, auth settings
- CI4 API for realtime integration

## Repository Structure

- `app/` CI4 code (controllers, models, filters, services, migrations, seeders, views)
- `realtime/` Socket.IO service
- `docker/` Dockerfiles and runtime scripts
- `docker-compose.yml` full local stack

## Quick Start

1. Copy env:

```bash
cp .env.example .env
```

2. Start stack:

```bash
docker compose up --build
```

3. Open app:

- Web: `http://localhost:8080`
- Realtime socket endpoint: `http://localhost:3001`

On first app boot, entrypoint runs:

- `php spark migrate --all`
- `php spark db:seed DatabaseSeeder`

## Seeded Data

- Admin user:
  - username: `admin`
  - password: `Admin123!`
  - forced change on first login
- Roles:
  - `Developer`, `Chief`, `Accounting`, `Office`, `Driver`, `User`
- Public room:
  - `Lobby`
- Items:
  - 10 starter items seeded
- Starter inventory:
  - Admin gets initial items
  - New users automatically get starter items on registration

## Core Endpoints

### Pages

- `GET /login`
- `GET /register`
- `GET /dashboard`
- `GET /rooms`
- `GET /rooms/{id}`
- `GET /inventory`
- `GET /passkeys`
- `GET /admin`

### API (CI4)

- `POST /api/realtime/auth` issue Socket.IO JWT for authenticated session user
- `POST /api/realtime/event` realtime server ingest (shared-secret protected)
- `GET /api/rooms/{id}`
- `GET /api/rooms/{id}/placed-items`
- `POST /api/rooms/{id}/place-item`

## Realtime Events

Socket.IO rooms follow `room:{roomId}` convention.

Client events:

- `joinRoom`
- `leaveRoom`
- `chatMessage`
- `move`
- `placeItem`

Server broadcasts:

- `presenceUpdate`
- `chatMessage`
- `move`
- `placeItem`

## Security Choices

- Password hashing: Argon2id (`password_hash`)
- CSRF: enabled globally for web forms (`api/realtime/*` excluded for server-to-server event ingest)
- Input validation: CI4 validator on auth, room/item/admin/API payloads
- Authorization:
  - `Developer`: full access
  - `Chief`: admin/moderation/room/item management
  - `Accounting`, `Office`: user/report visibility only, no bans
  - `Driver`, `User`: standard gameplay
- Session storage: Redis (`RedisHandler`)
- Chat rate limiting: realtime service + Redis counters (`REALTIME_CHAT_LIMIT` per window)
- Socket auth token:
  - JWT HS256
  - issued by CI4 `/api/realtime/auth`
  - verified in Node with shared JWT secret
  - scoped with `aud=pixelhavn-realtime` and `room_id`
- Realtime ingest auth:
  - `X-Realtime-Secret` header checked by `RealtimeIngestFilter`

## Required Environment Variables

From `.env.example`:

- App: `CI_ENVIRONMENT`, `app.baseURL`
- MariaDB: `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DB_ROOT_PASSWORD`
- CI4 DB override: `database.default.*`
- Redis/session/cache: `cache.*`, `session.*`
- JWT: `jwt.secret`
- Realtime shared secret: `realtime.sharedSecret`
- Realtime service: `REALTIME_*`
- Passkeys/WebAuthn: `webauthn.rpId`, `webauthn.origin`

## Development Commands

Inside app container:

```bash
docker compose exec app php spark migrate --all
docker compose exec app php spark db:seed DatabaseSeeder
docker compose exec app php spark routes
docker compose exec app php vendor/bin/phpunit
```

Inside realtime container:

```bash
docker compose exec realtime npm run start
```

## Tests

- `tests/feature/AuthFeatureTest.php`
- `tests/feature/RoomApiFeatureTest.php`

Run:

```bash
docker compose exec app php vendor/bin/phpunit
```

## Notes

- Set strong random values for `jwt.secret` and `realtime.sharedSecret` before production.
- Passkeys require HTTPS in production and a valid RP ID/origin pair.

## Pelican/Pterodactyl Egg

- Egg file: `PixelHavn.json`
- Runtime image: `ghcr.io/nextarch-studio/yolks:php8.2-node20`
- Pelican startup runs as non-root, so PHP/Node/Composer must already exist in the runtime image.
- Import `PixelHavn.json` in panel, create server, set DB/Redis vars, then start server.
- Build/push runtime image:

```bash
docker build -t ghcr.io/nextarch-studio/yolks:php8.2-node20 -f docker/pelican-runtime/Dockerfile .
docker push ghcr.io/nextarch-studio/yolks:php8.2-node20
```
