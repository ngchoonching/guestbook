<?php
// Shared MySQL connection — used by every page.
// Reads connection details from environment variables so the SAME code
// works locally (docker compose) and on Render (env vars in dashboard).
// The fallback values match the local docker-compose.yml.

$host = getenv("DB_HOST") ?: "localhost";
$user = getenv("DB_USER") ?: "appuser";
$pass = getenv("DB_PASS") ?: "apppass";
$name = getenv("DB_NAME") ?: "guestbook";
$port = (int)(getenv("DB_PORT") ?: 3306);

$conn = new mysqli($host, $user, $pass, $name, $port);

if ($conn->connect_error) {
    http_response_code(500);
    die("Database connection failed: " . htmlspecialchars($conn->connect_error));
}

$conn->set_charset("utf8mb4");
