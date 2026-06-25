# Guestbook — PHP & PostgreSQL (Render-ready)

A PHP + Apache app with full CRUD over a **PostgreSQL** database. Built to
deploy on **Render**, using Render's free managed PostgreSQL — no external
database or paid disk required.

Uses PDO with the `pdo_pgsql` driver.

```
gb-pg/
├─ Dockerfile           # PHP 8.2 + Apache + pdo_pgsql
├─ entrypoint.sh        # listens on the host-provided $PORT (else 80)
├─ docker-compose.yml   # local dev: app + Postgres + Adminer
└─ src/
   ├─ db.php            # PDO connection (DATABASE_URL or discrete DB_* vars)
   ├─ schema.php        # creates + seeds the table on first use
   ├─ index.php         # READ   — list + add form
   ├─ create.php        # CREATE
   ├─ edit.php          # UPDATE
   ├─ delete.php        # DELETE
   └─ style.css
```

## How the connection is configured

The app reads **either**:

- `DATABASE_URL` — a single connection string. Render's managed Postgres
  provides this automatically when you link the database to the service. This
  is the easiest path. Format: `postgres://user:pass@host:port/dbname`
- or the discrete vars `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`
  (used by the local compose file).

`DATABASE_URL` takes priority if both are present. SSL mode defaults to
`require` (Render needs it); locally compose sets `DB_SSLMODE=disable`.

## Run locally

```bash
docker compose up
```

- App:     http://localhost:8080
- Adminer: http://localhost:8081  (System: PostgreSQL, Server: `db`,
  user `appuser`, pass `apppass`, database `guestbook`)

You should see two seeded messages. Add, edit, delete to exercise CRUD.

## Deploy to Render

### 1. Create the database
In Render: **New → PostgreSQL**. Pick the free plan. When it's ready, note that
Render exposes its connection string as an environment variable you can link.

### 2. Push this repo to GitHub
```bash
git init && git add . && git commit -m "Guestbook: PHP + PostgreSQL"
git branch -M main
git remote add origin https://github.com/<you>/gb-pg.git
git push -u origin main
```

### 3. Create the web service
- **New → Web Service**, connect the repo. Render detects the Dockerfile.
- Choose the **Free** instance type.
- Under **Environment**, add the database connection. The simplest way:
  add a variable named `DATABASE_URL` and use the
  **"Add from database"** / Internal Connection String of your Postgres
  instance so the two are linked.
- Create the service and wait for the build.

`schema.php` creates and seeds the table on the first page load, so there's no
manual SQL step. Open the `onrender.com` URL — you should see the guestbook.

## Notes
- First request after the free service idles is slow (cold start) — reload once.
- Use Render's **Internal** database URL for the web service (faster, and it
  keeps the database off the public internet). The External URL is for
  connecting from your laptop.
