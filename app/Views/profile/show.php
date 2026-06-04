<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile — Expense Tracker</title>
  <link rel="stylesheet" href="/expense_tracker/public/assets/css/style.css?v=<?= time() ?>">
</head>
<body>
<div class="app">

  <header class="app-header">
    <button class="back-btn" onclick="window.location='/expense_tracker/public/'">&#8592; Home</button>
    <h1>My Profile</h1>
  </header>

  <?php if (!empty($flashSuccess)): ?>
    <div class="flash flash--success" id="flash-banner">
      <span class="flash__icon">&#10003;</span>
      <span class="flash__msg"><?= htmlspecialchars($flashSuccess) ?></span>
      <button class="flash__close" onclick="this.parentElement.remove()">&#10005;</button>
    </div>
  <?php elseif (!empty($flashError)): ?>
    <div class="flash flash--error" id="flash-banner">
      <span class="flash__icon">&#10005;</span>
      <span class="flash__msg"><?= htmlspecialchars($flashError) ?></span>
      <button class="flash__close" onclick="this.parentElement.remove()">&#10005;</button>
    </div>
  <?php endif; ?>

  <!-- Profile Card -->
  <div style="margin:16px">
    <div class="profile-card">
      <div class="profile-avatar">
        <?= strtoupper(substr($user['name'], 0, 1)) ?>
      </div>
      <div class="profile-info">
        <h2 class="profile-name"><?= htmlspecialchars($user['name']) ?></h2>
        <p class="profile-email"><?= htmlspecialchars($user['email']) ?></p>
        <span class="profile-badge <?= $user['is_active'] ? 'badge--active' : 'badge--inactive' ?>">
          <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
        </span>
      </div>
    </div>
    <p class="profile-joined">Member since <?= date('F j, Y', strtotime($user['created_at'])) ?></p>
  </div>

  <!-- Action Cards -->
  <div style="padding:0 16px;display:flex;flex-direction:column;gap:10px">

    <a href="/expense_tracker/public/profile/edit" class="profile-action-card">
      <div class="pac-icon pac-icon--blue">&#9998;</div>
      <div class="pac-text">
        <span class="pac-title">Edit profile</span>
        <span class="pac-sub">Update your name and email</span>
      </div>
      <span class="pac-arrow">&#8250;</span>
    </a>

    <a href="/expense_tracker/public/profile/change-password" class="profile-action-card">
      <div class="pac-icon pac-icon--green">&#128274;</div>
      <div class="pac-text">
        <span class="pac-title">Change password</span>
        <span class="pac-sub">Keep your account secure</span>
      </div>
      <span class="pac-arrow">&#8250;</span>
    </a>

  
    <?php if (\App\Core\Auth::isAdmin()): ?>
  <a href="/expense_tracker/public/users" class="profile-action-card">
    <div class="pac-icon pac-icon--purple">&#128101;</div>
    <div class="pac-text">
      <span class="pac-title">User Directory</span>
      <span class="pac-sub">Search and filter all users</span>
    </div>
    <span class="pac-arrow">&#8250;</span>
  </a>
<?php endif; ?>


    <a href="/expense_tracker/public/profile/delete" class="profile-action-card profile-action-card--danger">
      <div class="pac-icon pac-icon--red">&#128465;</div>
      <div class="pac-text">
        <span class="pac-title">Delete account</span>
        <span class="pac-sub">Permanently remove all your data</span>
      </div>
      <span class="pac-arrow">&#8250;</span>
    </a>

  </div>

  <!-- Bottom Nav -->
  <nav class="bottom-nav">
    <button class="nav-btn" onclick="window.location='/expense_tracker/public/'">
      <span>&#8962;</span><span>Home</span>
    </button>
    <button class="nav-btn nav-add" onclick="window.location='/expense_tracker/public/add'">
      <span class="add-icon">+</span>
    </button>
    <button class="nav-btn active">
      <span>&#9881;</span><span>Account</span>
    </button>
  </nav>
</div>

<script src="/expense_tracker/public/assets/js/app.js?v=<?= time() ?>"></script>
</body>
</html>
