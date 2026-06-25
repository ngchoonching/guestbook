<?php
require "db.php";

// Only accept POST.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$name    = trim($_POST["name"] ?? "");
$message = trim($_POST["message"] ?? "");

// Validation — reject empty.
if ($name === "" || $message === "") {
    header("Location: index.php?error=empty");
    exit;
}
// Validation — reject over-long.
if (strlen($name) > 80 || strlen($message) > 500) {
    header("Location: index.php?error=length");
    exit;
}

// CREATE — prepared statement keeps user input as data, never SQL.
$stmt = $conn->prepare("INSERT INTO messages (name, message) VALUES (?, ?)");
$stmt->bind_param("ss", $name, $message);
$stmt->execute();

// Redirect so a browser refresh won't re-submit (Post/Redirect/Get).
header("Location: index.php");
exit;
