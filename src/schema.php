<?php
// Ensures the messages table exists and has seed data (PostgreSQL / PDO).
// Safe to call on every request. Render does not run an init script for
// managed Postgres, so the app creates its own schema on first use.

function ensure_schema(PDO $conn): void
{
    $conn->exec(
        "CREATE TABLE IF NOT EXISTS messages (
            id         SERIAL PRIMARY KEY,
            name       VARCHAR(80)  NOT NULL,
            message    VARCHAR(500) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    );

    // Seed only if the table is empty.
    $count = (int)$conn->query("SELECT COUNT(*) FROM messages")->fetchColumn();

    if ($count === 0) {
        $stmt = $conn->prepare("INSERT INTO messages (name, message) VALUES (?, ?)");
        $seed = [
            ["Ada", "Welcome to the guestbook!"],
            ["Linus", "Hello from the database."],
        ];
        foreach ($seed as $row) {
            $stmt->execute($row);
        }
    }
}
