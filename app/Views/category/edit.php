<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Category — Expense Tracker</title>
  <link rel="stylesheet" href="/expense_tracker/public/assets/css/style.css">
</head>
<body>
<div class="app">

  <header class="app-header">
    <a href="/expense_tracker/public/categories" class="back-btn">&#8592; Back</a>
    <h1>Edit Category</h1>
  </header>

  <div style="padding:20px">

    <?php if (!empty($error)): ?>
      <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="/expense_tracker/public/categories/update" method="POST">
      <input type="hidden" name="id" value="<?= $category['id'] ?>">

      <div class="form-group">
        <label>Category Name</label>
        <input type="text" name="name"
          value="<?= htmlspecialchars($_POST['name'] ?? $category['name']) ?>"
          required>
      </div>

      <div class="form-group">
        <label>Icon (emoji)</label>
        <input type="text" name="icon"
          value="<?= htmlspecialchars($_POST['icon'] ?? $category['icon']) ?>"
          placeholder="e.g. 🛒">
      </div>

      <div class="form-group">
        <label>Colour</label>
        <div style="display:flex;gap:10px;align-items:center">
          <input type="color" name="color"
            value="<?= htmlspecialchars($_POST['color'] ?? $category['color']) ?>"
            style="width:48px;height:40px;border:1px solid var(--border);
                   border-radius:8px;padding:2px;cursor:pointer">
          <span style="font-size:.82rem;color:var(--text-muted)">
            Current: <?= htmlspecialchars($category['color']) ?>
          </span>
        </div>
      </div>

      <!-- Colour presets -->
      <div class="form-group">
        <label>Quick colour presets</label>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
          <?php foreach ([
            '#FF8B00','#0052CC','#6554C0','#FF5630',
            '#36B37E','#00B8D9','#403294','#8B949E'
          ] as $preset): ?>
            <a href="#"
               onclick="document.querySelector('input[name=color]').value='<?= $preset ?>'"
               style="width:28px;height:28px;border-radius:50%;
                      background:<?= $preset ?>;display:inline-block;
                      border:2px solid white;box-shadow:0 0 0 1px #ccc"></a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Live preview -->
      <div class="form-group">
        <label>Preview</label>
        <div style="background:var(--surface);border-radius:var(--radius);
                    padding:14px;border-left:4px solid <?= htmlspecialchars($category['color']) ?>;
                    box-shadow:var(--shadow);display:inline-block;min-width:120px">
          <span style="font-size:1.4rem;display:block;margin-bottom:4px">
            <?= htmlspecialchars($category['icon']) ?>
          </span>
          <span style="font-size:.8rem;color:var(--text-muted)">
            <?= htmlspecialchars($category['name']) ?>
          </span>
        </div>
      </div>

      <button type="submit" class="btn-primary">Save Changes</button>
      <a href="/expense_tracker/public/categories" class="btn-cancel">Cancel</a>

    </form>
  </div>
</div>
</body>
</html>