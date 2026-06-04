<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Profile — Expense Tracker</title>
  <link rel="stylesheet" href="/expense_tracker/public/assets/css/style.css?v=<?= time() ?>">
</head>
<body>
<div class="app">

  <header class="app-header">
    <button class="back-btn" onclick="window.location='/expense_tracker/public/profile'">&#8592; Back</button>
    <h1>Edit Profile</h1>
  </header>

  <div style="padding:20px">

    <?php if (!empty($error)): ?>
      <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="/expense_tracker/public/profile/update" method="POST">

      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="name"
          value="<?= htmlspecialchars($_POST['name'] ?? $user['name'] ?? '') ?>"
          placeholder="Your full name" required minlength="2">
      </div>

      <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email"
          value="<?= htmlspecialchars($_POST['email'] ?? $user['email'] ?? '') ?>"
          placeholder="you@example.com" required>
      </div>

      <div class="form-hint">
        <span>&#9432;</span>
        Changing your email will update your login credential.
      </div>

      <button type="submit" class="btn-primary" style="margin-top:20px">
        Save Changes
      </button>
      <a href="/expense_tracker/public/profile" class="btn-cancel">Cancel</a>

    </form>
  </div>

</div>
</body>
</html>
