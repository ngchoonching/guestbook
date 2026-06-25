<?php
require "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$id = (int)($_POST["id"] ?? 0);

// DELETE — WHERE id = ? removes exactly one row.
$stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;
