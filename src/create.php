<?php
require "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$name    = trim($_POST["name"] ?? "");
$message = trim($_POST["message"] ?? "");

if ($name === "" || $message === "") {
    header("Location: index.php?error=empty");
    exit;
}
if (mb_strlen($name) > 80 || mb_strlen($message) > 500) {
    header("Location: index.php?error=length");
    exit;
}

// CREATE — prepared statement keeps user input as data, never SQL.
$stmt = $conn->prepare("INSERT INTO messages (name, message) VALUES (?, ?)");
$stmt->execute([$name, $message]);

// Redirect so a browser refresh won't re-submit (Post/Redirect/Get).
header("Location: index.php");
exit;
