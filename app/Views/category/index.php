
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Categories — Expense Tracker</title>
  <link rel="stylesheet" href="/expense_tracker/public/assets/css/style.css">
</head>
<body>
<div class="app">

  <header class="app-header">
    <button class="back-btn" onclick="window.location='/expense_tracker/public/'">
      &#8592; Home
    </button>
    <h1>Categories</h1>
  </header>

  <?php if (!empty($flashSuccess)): ?>
    <div class="flash flash--success">
      <span class="flash__icon">&#10003;</span>
      <span class="flash__msg"><?= htmlspecialchars($flashSuccess) ?></span>
    </div>
  <?php elseif (!empty($flashError)): ?>
    <div class="flash flash--error">
      <span class="flash__icon">&#10005;</span>
      <span class="flash__msg"><?= htmlspecialchars($flashError) ?></span>
    </div>
  <?php endif; ?>

  <div style="padding:16px">

    <!-- Search / Filter bar -->
    <form method="GET" action="/expense_tracker/public/categories">
      <div class="user-search-row">
        <div style="position:relative;flex:1">
          <span class="search-icon">&#128269;</span>
          <input type="text" name="search"
            value="<?= htmlspecialchars($search) ?>"
            placeholder="Search categories..."
            class="search-input">
        </div>
        <button type="submit" class="btn-filter-apply">Search</button>
        <?php if ($search): ?>
          <a href="/expense_tracker/public/categories" class="btn-filter-clear">Clear</a>
        <?php endif; ?>
      </div>
    </form>

    <!-- Add new category button -->
    <a href="/expense_tracker/public/categories/add"
       class="btn-primary"
       style="display:block;text-align:center;text-decoration:none;margin-top:12px">
      &#43; Add New Category
    </a>

  </div>

  <!-- Category list -->
  <?php if (empty($categories)): ?>
    <p class="empty-state">
      <?= $search ? 'No categories match your search.' : 'No categories found.' ?>
    </p>
  <?php else: ?>
    <ul class="expense-list" style="padding:0 16px 80px">
      <?php foreach ($categories as $cat): ?>
        <li class="expense-item">
          <!-- Colour dot -->
          <span class="exp-dot" style="background:<?= htmlspecialchars($cat['color']) ?>;
                width:14px;height:14px"></span>

          <!-- Icon + name -->
          <div class="exp-details">
            <span class="exp-cat" style="font-size:1rem">
              <?= htmlspecialchars($cat['icon']) ?>
              <?= htmlspecialchars($cat['name']) ?>
            </span>
            <span class="exp-note"><?= htmlspecialchars($cat['color']) ?></span>
          </div>

          <!-- Edit / Delete -->
          <div class="exp-actions">
            <a href="/expense_tracker/public/categories/edit?id=<?= $cat['id'] ?>"
               class="action-btn action-btn--edit" title="Edit">&#9998;</a>
            <a href="/expense_tracker/public/categories/delete?id=<?= $cat['id'] ?>"
               class="action-btn action-btn--delete"
               title="Delete"
               onclick="return confirm('Delete category \'<?= htmlspecialchars(addslashes($cat['name'])) ?>\'? This will fail if any expenses use it.')">
               &#10005;</a>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <nav class="bottom-nav">
    <button class="nav-btn" onclick="window.location='/expense_tracker/public/'">
      <span>&#8962;</span><span>Home</span>
    </button>
    <button class="nav-btn nav-add" onclick="window.location='/expense_tracker/public/add'">
      <span class="add-icon">+</span>
    </button>
    <button class="nav-btn" onclick="window.location='/expense_tracker/public/user'">
      <span>&#9881;</span><span>Account</span>
    </button>
  </nav>

</div>
</body>
</html>