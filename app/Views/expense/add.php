<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Expense</title>
  <link rel="stylesheet" href="/expense_tracker/public/assets/css/style.css">
</head>
<body>
<div class="app">
  <div class="panel active" id="panel-add">
    <header class="app-header">
      <button class="back-btn" onclick="history.back()">&#8592; Back</button>
      <h1>Add Expense</h1>
    </header>
    <form action="/expense_tracker/public/expense/save" method="POST" id="add-form">
      <section class="add-section">
        <h2>Category</h2>
        <div class="cat-select-grid">
          <?php foreach ($categories as $c): ?>
            <button type="button" class="cat-select-btn"
              data-id="<?= $c['id'] ?>" data-color="<?= $c['color'] ?>"
              onclick="selectCategory(this)">
              <span><?= $c['icon'] ?></span>
              <span><?= htmlspecialchars($c['name']) ?></span>
            </button>
          <?php endforeach; ?>
        </div>
        <input type="hidden" name="category_id" id="selected_category">
      </section>
      <section class="add-section">
        <h2>Amount (DKK)</h2>
        <div class="amount-display" id="amount-display">0</div>
        <input type="hidden" name="amount" id="amount-input">
        <div class="keypad">
          <?php foreach (['1','2','3','4','5','6','7','8','9','.','0','del'] as $k): ?>
            <button type="button" class="key" onclick="keyPress('<?= $k ?>')">
              <?= $k === 'del' ? '&#9003;' : $k ?>
            </button>
          <?php endforeach; ?>
        </div>
      </section>
      <section class="add-section">
        <h2>Details</h2>
        <div class="form-group">
          <label>Note (optional)</label>
          <input type="text" name="note" placeholder="What was this for?">
        </div>
        <div class="form-group">
          <label>Date (leave empty for today)</label>
          <input type="date" name="spent_date">
        </div>
        <button type="submit" class="btn-primary">Save Expense</button>
      </section>
    </form>
  </div>
  <nav class="bottom-nav">
    <button class="nav-btn" onclick="window.location='/expense_tracker/public/'"><span>&#8962;</span><span>Home</span></button>
    <button class="nav-btn nav-add active"><span class="add-icon">+</span></button>
    <button class="nav-btn" onclick="window.location='/expense_tracker/public/user'"><span>&#9881;</span><span>Account</span></button>
  </nav>
</div>
<script src="/expense_tracker/public/assets/js/app.js"></script>
</body>
</html>
