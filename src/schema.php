<?php
// Ensures the messages table exists and has seed data.
// On the local stack, db/init.sql also does this — but Render does NOT run
// init.sql automatically, so we create the table from PHP on first use.
// This is safe to call on every request (IF NOT EXISTS / conditional seed).

function ensure_schema(mysqli $conn): void
{
    $conn->query(
        "CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(80) NOT NULL,
            message VARCHAR(500) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );

    // Seed only if the table is empty.
    $res = $conn->query("SELECT COUNT(*) AS c FROM messages");
    $count = (int)($res->fetch_assoc()["c"] ?? 0);

    if ($count === 0) {
        $stmt = $conn->prepare("INSERT INTO messages (name, message) VALUES (?, ?)");
        $seed = [
            ["Ada", "Welcome to the guestbook!"],
            ["Linus", "Hello from the database."],
        ];
        foreach ($seed as [$n, $m]) {
            $stmt->bind_param("ss", $n, $m);
            $stmt->execute();
        }
    }
}
