<?php
// Shared PostgreSQL connection (PDO) — used by every page.
//
// Render provides its managed Postgres connection as a single DATABASE_URL,
// so we read that if present. For local development you can instead set the
// discrete DB_* variables (see docker-compose.yml). DATABASE_URL wins if both
// are set.
//
// DATABASE_URL format: postgres://user:password@host:port/dbname

function pg_settings_from_env(): array
{
    $url = getenv("DATABASE_URL");
    if ($url) {
        $p = parse_url($url);
        if ($p === false || !isset($p["host"])) {
            http_response_code(500);
            die("Configuration error: DATABASE_URL is set but could not be parsed.");
        }
        return [
            "host" => $p["host"],
            "port" => $p["port"] ?? 5432,
            "name" => isset($p["path"]) ? ltrim($p["path"], "/") : "",
            "user" => $p["user"] ?? "",
            "pass" => isset($p["pass"]) ? urldecode($p["pass"]) : "",
        ];
    }

    return [
        "host" => getenv("DB_HOST") ?: "",
        "port" => (int)(getenv("DB_PORT") ?: 5432),
        "name" => getenv("DB_NAME") ?: "",
        "user" => getenv("DB_USER") ?: "",
        "pass" => getenv("DB_PASS") ?: "",
    ];
}

$cfg = pg_settings_from_env();

// Fail loudly if configuration is missing.
$missing = [];
foreach (["host", "name", "user"] as $k) {
    if ($cfg[$k] === "" || $cfg[$k] === false) {
        $missing[] = strtoupper("db_" . $k);
    }
}
if ($missing) {
    http_response_code(500);
    die(
        "Configuration error: database is not configured.\n\n" .
        "Set DATABASE_URL (Render's managed Postgres provides this automatically " .
        "when you link the database), or set DB_HOST, DB_PORT, DB_NAME, DB_USER " .
        "and DB_PASS for local development.\n\n" .
        "Missing: " . htmlspecialchars(implode(", ", $missing))
    );
}

// Render's managed Postgres requires SSL. sslmode=require is safe to send to a
// local container too (it negotiates, falling back if unsupported is NOT
// guaranteed, so for purely-local use you can drop it — see docker-compose).
$sslmode = getenv("DB_SSLMODE") ?: "require";

$dsn = sprintf(
    "pgsql:host=%s;port=%d;dbname=%s;sslmode=%s",
    $cfg["host"],
    (int)$cfg["port"],
    $cfg["name"],
    $sslmode
);

try {
    $conn = new PDO($dsn, $cfg["user"], $cfg["pass"], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    die(
        "Database connection failed.\n\n" .
        "Tried host: " . htmlspecialchars($cfg["host"]) . " (port " . (int)$cfg["port"] . ")\n" .
        "Error: " . htmlspecialchars($e->getMessage()) . "\n\n" .
        "Check the host/port/credentials. On Render, the simplest setup is to " .
        "add DATABASE_URL from your managed Postgres instance."
    );
}
