<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'Expense Tracker' ?></title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <div class="app">
    <?= $content ?? '' ?>
  </div>
  <nav class="bottom-nav">
    <button class="nav-btn" id="nav-home" onclick="switchPanel('home')">
      <span>&#8962;</span>
      <span>Home</span>
    </button>
    <button class="nav-btn nav-add" onclick="switchPanel('add')">
      <span class="add-icon">add budget</span>
    </button>
    <button class="nav-btn" id="nav-user" onclick="switchPanel('user')">
      <span>&#9881;</span>
      <span>Account</span>
    </button>
  </nav>
  <script src="/assets/js/app.js"></script>
</body>
</html>
