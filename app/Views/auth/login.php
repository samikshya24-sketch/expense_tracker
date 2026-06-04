<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — Expense Tracker</title>
  <link rel="stylesheet" href="/expense_tracker/public/assets/css/style.css?v=<?= time() ?>">
</head>
<body>
  <div class="auth-page">
    <div class="auth-card">
      <h1>Expense Tracker</h1>
      <?php if (!empty($error ?? '')): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form action="/expense_tracker/public/login" method="POST">
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" placeholder="you@example.com" required autofocus>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="••••••" required>
        </div>
        <button type="submit" class="btn-primary">Log In</button>
      </form>
      <p class="auth-switch">
        No account? <a href="/expense_tracker/public/register">Register</a>
      </p>
    </div>
  </div>
</body>
</html>
