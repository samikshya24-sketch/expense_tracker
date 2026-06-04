<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Delete Account — Expense Tracker</title>
  <link rel="stylesheet" href="/expense_tracker/public/assets/css/style.css">
</head>
<body>
<div class="app">

  <header class="app-header">
    <button class="back-btn" onclick="window.location='/expense_tracker/public/profile'">&#8592; Back</button>
    <h1>Account Options</h1>
  </header>

  <div style="padding:20px">

    <?php if (!empty($error)): ?>
      <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- ── DEACTIVATE (soft / reversible) ─────────────────── -->
    <div class="danger-card danger-card--warn">
      <h3 class="danger-title">&#9888; Deactivate account</h3>
      <p class="danger-desc">
        Temporarily disables your login. Your data is kept safe.
        An administrator can reactivate your account later.
      </p>
      <form action="/expense_tracker/public/profile/deactivate" method="POST"
            onsubmit="return confirm('Deactivate your account? You will be logged out.')">
        <div class="form-group" style="margin-bottom:10px">
          <label>Enter your password to confirm</label>
          <input type="password" name="password" placeholder="Your password" required>
        </div>
        <button type="submit" class="btn-warn">Deactivate my account</button>
      </form>
    </div>

    <!-- ── DELETE (hard / permanent) ──────────────────────── -->
    <div class="danger-card danger-card--danger">
      <h3 class="danger-title">&#128465; Delete account permanently</h3>
      <p class="danger-desc">
        This cannot be undone. All your expenses, budgets, and data
        will be permanently removed.
      </p>
      <form action="/expense_tracker/public/profile/delete" method="POST"
            onsubmit="return validateDelete()">
        <div class="form-group" style="margin-bottom:10px">
          <label>Enter your password</label>
          <input type="password" name="password" id="del-pwd" placeholder="Your password" required>
        </div>
        <div class="form-group" style="margin-bottom:10px">
          <label>Type <strong>DELETE</strong> to confirm</label>
          <input type="text" name="confirm" id="del-confirm"
            placeholder="DELETE" autocomplete="off" required>
        </div>
        <button type="submit" class="btn-danger" style="margin:0;width:100%">
          Permanently delete my account
        </button>
      </form>
    </div>

    <a href="/expense_tracker/public/profile" class="btn-cancel" style="text-align:center;display:block;margin-top:10px">
      Cancel — keep my account
    </a>

  </div>
</div>
<script>
function validateDelete() {
  const confirm = document.getElementById('del-confirm').value;
  if (confirm !== 'DELETE') {
    alert('Please type DELETE (in capitals) to confirm.');
    return false;
  }
  return window.confirm('This is permanent. Delete your account and all data?');
}
</script>
</body>
</html>
