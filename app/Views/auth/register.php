<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register — Expense Tracker</title>
  <link rel="stylesheet" href="/expense_tracker/public/assets/css/style.css?v=<?= time() ?>">
</head>
<body>
  <div class="auth-page">
    <div class="auth-card">
      <h1>Create Account</h1>
      <?php if (!empty($error ?? '')): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form action="/expense_tracker/public/register" method="POST">
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" name="name"
            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
            placeholder="Your full name" required>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
            placeholder="you@example.com" required>
        </div>
        <div class="form-group">
          <label>Password (min 6 characters)</label>
          <input type="password" name="password" placeholder="••••••" required>
        </div>
        <button type="submit" class="btn-primary">Register</button>
      </form>
      <p class="auth-switch">
        Already have an account? <a href="/expense_tracker/public/login">Log in</a>
      </p>
    </div>
  </div>
</body>
</html>
