<?php
require "db.php";

// POST — save the edited message.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id      = (int)($_POST["id"] ?? 0);
    $message = trim($_POST["message"] ?? "");

    if ($message === "" || mb_strlen($message) > 500) {
        header("Location: edit.php?id=" . $id . "&error=length");
        exit;
    }

    // UPDATE — WHERE id = ? targets exactly one row.
    $stmt = $conn->prepare("UPDATE messages SET message = ? WHERE id = ?");
    $stmt->execute([$message, $id]);

    header("Location: index.php");
    exit;
}

// GET — load the one row to pre-fill the form.
$id   = (int)($_GET["id"] ?? 0);
$stmt = $conn->prepare("SELECT * FROM messages WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit message</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="wrap">
    <h1>Edit message</h1>
    <p class="count">by <?= htmlspecialchars($row["name"]) ?></p>

    <?php if (isset($_GET["error"])): ?>
      <p class="error">Message must be between 1 and 500 characters.</p>
    <?php endif; ?>

    <form class="card form" action="edit.php" method="post">
      <input type="hidden" name="id" value="<?= (int)$row["id"] ?>">
      <textarea name="message" maxlength="500" required><?= htmlspecialchars($row["message"]) ?></textarea>
      <div class="actions">
        <button type="submit">Save changes</button>
        <a class="link" href="index.php">Cancel</a>
      </div>
    </form>
  </main>
</body>
</html>
