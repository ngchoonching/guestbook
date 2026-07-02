<?php
require "db.php";
require "schema.php";
ensure_schema($conn);

// READ — fetch all messages, newest first.
$stmt = $conn->query("SELECT * FROM messages ORDER BY created_at DESC, id DESC");
$messages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Guestbook</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="wrap">
    <h1>Guestbook 123</h1>
    <p class="count"><?= count($messages) ?> message<?= count($messages) === 1 ? "" : "s" ?> so far</p>

    <?php if (isset($_GET["error"])): ?>
      <p class="error">
        <?php
          $errors = [
            "length" => "Message must be between 1 and 500 characters.",
            "empty"  => "Please fill in both your name and a message.",
          ];
          echo htmlspecialchars($errors[$_GET["error"]] ?? "Something went wrong.");
        ?>
      </p>
    <?php endif; ?>

    <!-- CREATE — the form posts to create.php -->
    <form class="card form" action="create.php" method="post">
      <input name="name" placeholder="Your name" maxlength="80" required>
      <textarea name="message" placeholder="Leave a message…" maxlength="500" required></textarea>
      <button type="submit">Sign the guestbook</button>
    </form>

    <!-- READ — one card per message -->
    <?php foreach ($messages as $row): ?>
      <article class="card message">
        <div class="meta">
          <strong><?= htmlspecialchars($row["name"]) ?></strong>
          <span class="date"><?= htmlspecialchars($row["created_at"]) ?></span>
        </div>
        <p><?= nl2br(htmlspecialchars($row["message"])) ?></p>
        <div class="actions">
          <a class="link" href="edit.php?id=<?= (int)$row["id"] ?>">Edit</a>
          <!-- DELETE — small form so it's a POST, not a clickable GET link -->
          <form action="delete.php" method="post" onsubmit="return confirm('Delete this message?');">
            <input type="hidden" name="id" value="<?= (int)$row["id"] ?>">
            <button type="submit" class="link danger">Delete</button>
          </form>
        </div>
      </article>
    <?php endforeach; ?>
  </main>
</body>
</html>
