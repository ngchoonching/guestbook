# Guestbook — PHP & MySQL

A small PHP + MySQL app with full CRUD (Create, Read, Update, Delete).
Runs locally with Docker Compose, and deploys to **Render** using the
included `Dockerfile` plus an external MySQL database.

---

## What's in here

```
guestbook/
├─ Dockerfile              # builds the PHP+Apache image (used locally AND on Render)
├─ render-entrypoint.sh    # makes Apache listen on Render's $PORT
├─ docker-compose.yml      # LOCAL ONLY: web + db + phpMyAdmin
├─ db/
│  └─ init.sql             # LOCAL ONLY: seeds the table on first run
└─ src/
   ├─ db.php               # shared MySQL connection (reads env vars)
   ├─ schema.php           # creates + seeds the table (for Render)
   ├─ index.php            # READ   — list messages + add form
   ├─ create.php           # CREATE — save a new message
   ├─ edit.php             # UPDATE — edit one message
   ├─ delete.php           # DELETE — remove one message
   └─ style.css            # styling
```

The connection in `db.php` reads from environment variables
(`DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`, `DB_PORT`), so the **same code**
runs locally and on Render — only the values change.

---

## Run it locally

You need Docker Desktop.

```bash
docker compose up
```

- App:        http://localhost:8080
- phpMyAdmin: http://localhost:8081  (server `db`, user `appuser`, pass `apppass`)

You should see two seeded messages from Ada and Linus. Add, edit, and delete
messages to confirm CRUD works. Stop with `Ctrl+C`, or `docker compose down`.

---

## Deploy to Render

Render does **not** run `docker-compose.yml`. Instead you create two things:
a **MySQL database** (external) and a **Web Service** (built from the Dockerfile).

### Step 1 — Push to GitHub

```bash
cd guestbook
git init
git add .
git commit -m "Guestbook: PHP + MySQL CRUD"
git branch -M main
git remote add origin https://github.com/<your-username>/guestbook.git
git push -u origin main
```

### Step 2 — Get a MySQL database

Render's built-in managed database is PostgreSQL, not MySQL, so use a free
external MySQL host. Any of these work — pick one and create a database:

- **Aiven** (free MySQL plan)
- **Railway** (MySQL plugin)
- **PlanetScale** (MySQL-compatible)

From its dashboard, copy these five values — you'll paste them into Render:

| Value      | Example                  |
|------------|--------------------------|
| host       | `mysql-xxxx.aivencloud.com` |
| port       | `3306` (or whatever it gives) |
| database   | `guestbook` (create it if needed) |
| user       | `appuser` / `avnadmin` / etc. |
| password   | `••••••••`                |

> No need to run `init.sql` by hand — `schema.php` creates the table and
> seeds it automatically on the first page load.

### Step 3 — Create the Web Service on Render

1. In Render: **New → Web Service**.
2. Connect your GitHub repo (`guestbook`).
3. Render auto-detects the `Dockerfile`. Leave build/start commands blank.
4. Choose the **Free** instance type.
5. Under **Environment**, add these variables (from Step 2):

   ```
   DB_HOST = <your mysql host>
   DB_PORT = <your mysql port>
   DB_NAME = guestbook
   DB_USER = <your mysql user>
   DB_PASS = <your mysql password>
   ```

6. Click **Create Web Service**. Render builds the image and deploys it.

When the build finishes, open the `onrender.com` URL. You should see the
guestbook with the two seeded messages, and full CRUD should work.

---

## Things to expect

- **First load is slow / 502 then works:** Render free web services spin down
  when idle and take ~30–60s to wake on the next request. Reload once.
- **"Database connection failed":** double-check the five env vars, and that
  your MySQL host allows external connections (some require enabling public
  access or whitelisting `0.0.0.0/0`).
- **Table not appearing:** `schema.php` runs on first request — load the app
  once, then check your DB host's console.

---

## Safety notes (already implemented)

- **Prepared statements** for every query that takes user input (`create.php`,
  `edit.php`, `delete.php`) — safe from SQL injection.
- **`htmlspecialchars()`** on every value printed to the page — safe from XSS.
- **Post/Redirect/Get**: writes redirect back to `index.php` so a refresh
  doesn't re-submit.
