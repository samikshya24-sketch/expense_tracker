<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit User — Expense Tracker</title>
  <link rel="stylesheet" href="/expense_tracker/public/assets/css/style.css">
</head>
<body>
<div class="app">
  <header class="app-header">
    <a href="/expense_tracker/public/users" class="back-btn">&#8592; Back</a>
    <h1>Edit User</h1>
  </header>

  <?php if (!empty($error)): ?>
    <div class="flash flash--error">
      <span class="flash__msg"><?= htmlspecialchars($error) ?></span>
    </div>
  <?php endif; ?>

  <div style="padding:20px">
    <form action="/expense_tracker/public/admin/user/update" method="POST">
      <input type="hidden" name="id" value="<?= $user['id'] ?>">

      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="name"
          value="<?= htmlspecialchars($_POST['name'] ?? $user['name']) ?>"
          required minlength="2">
      </div>

      <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email"
          value="<?= htmlspecialchars($_POST['email'] ?? $user['email']) ?>"
          required>
      </div>

      <div class="form-group">
        <label>Status</label>
        <select name="is_active">
          <option value="1" <?= $user['is_active'] ? 'selected' : '' ?>>Active</option>
          <option value="0" <?= !$user['is_active'] ? 'selected' : '' ?>>Inactive</option>
        </select>
      </div>

      <div class="form-group">
        <label>Role</label>
        <select name="is_admin">
          <option value="0" <?= !$user['is_admin'] ? 'selected' : '' ?>>Regular User</option>
          <option value="1" <?= $user['is_admin'] ? 'selected' : '' ?>>Admin</option>
        </select>
      </div>

      <button type="submit" class="btn-primary">Save Changes</button>
      <a href="/expense_tracker/public/users" class="btn-cancel">Cancel</a>
    </form>
  </div>

</div>
</body>
</html>