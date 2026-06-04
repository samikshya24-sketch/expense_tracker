<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Directory — Expense Tracker</title>
  <link rel="stylesheet" href="/expense_tracker/public/assets/css/style.css?v=<?= time() ?>">
</head>
<body>
<div class="app">

  <header class="app-header">
    <button class="back-btn" onclick="window.location='/expense_tracker/public/profile'">&#8592; Back</button>
    <h1>User Directory</h1>
  </header>

  <?php if (!empty($flashSuccess)): ?>
    <div class="flash flash--success">
      <span class="flash__msg"><?= htmlspecialchars($flashSuccess) ?></span>
    </div>
  <?php endif; ?>

  <!-- Stats strip -->
  <div class="user-stats-strip">
    <div class="user-stat">
      <span class="user-stat__num"><?= $totalUsers ?></span>
      <span class="user-stat__lbl">Total</span>
    </div>
    <div class="user-stat">
      <span class="user-stat__num" style="color:var(--success)"><?= $activeUsers ?></span>
      <span class="user-stat__lbl">Active</span>
    </div>
    <div class="user-stat">
      <span class="user-stat__num" style="color:var(--text-muted)"><?= $totalUsers - $activeUsers ?></span>
      <span class="user-stat__lbl">Inactive</span>
    </div>
    <div class="user-stat">
      <span class="user-stat__num"><?= count($users) ?></span>
      <span class="user-stat__lbl">Showing</span>
    </div>
  </div>

  <!-- Search + Filter bar -->
  <div style="padding:0 16px 12px">
    <form method="GET" action="/expense_tracker/public/users" id="filter-form">
      <div class="user-search-row">
        <div style="position:relative;flex:1">
          <span class="search-icon">&#128269;</span>
          <input type="text" name="search"
            value="<?= htmlspecialchars($search) ?>"
            placeholder="Search name or email..."
            class="search-input"
            oninput="document.getElementById('filter-form').submit()">
        </div>
        <select name="status" class="filter-select"
                onchange="document.getElementById('filter-form').submit()">
          <option value=""       <?= $status === ''         ? 'selected' : '' ?>>All</option>
          <option value="active" <?= $status === 'active'   ? 'selected' : '' ?>>Active</option>
          <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>
      </div>

      <!-- Sort controls -->
      <div class="sort-row">
        <span class="sort-label">Sort:</span>
        <?php
        $sorts = ['name' => 'Name', 'email' => 'Email', 'created_at' => 'Joined'];
        foreach ($sorts as $key => $label):
          $isActive = $sort === $key;
          $newDir   = ($isActive && $dir === 'ASC') ? 'DESC' : 'ASC';
        ?>
          <a href="/expense_tracker/public/users?search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&sort=<?= $key ?>&dir=<?= $isActive ? $newDir : 'ASC' ?>"
             class="sort-btn <?= $isActive ? 'sort-btn--active' : '' ?>">
            <?= $label ?>
            <?php if ($isActive): ?>
              <?= $dir === 'ASC' ? '&#8593;' : '&#8595;' ?>
            <?php endif; ?>
          </a>
        <?php endforeach; ?>
        <?php if ($search || $status): ?>
          <a href="/expense_tracker/public/users" class="sort-btn sort-btn--clear">Clear &#10005;</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- User list -->
  <?php if (empty($users)): ?>
    <p class="empty-state" style="padding:40px 0">No users found matching your search.</p>
  <?php else: ?>
    <ul class="user-list">
      <?php foreach ($users as $u): ?>
        <?php $isMe = ((int)$u['id'] === \App\Core\Auth::id()); ?>
        <li class="user-list-item <?= $isMe ? 'user-list-item--me' : '' ?>">
          <div class="user-avatar-sm">
            <?= strtoupper(substr($u['name'], 0, 1)) ?>
          </div>
          <div class="user-list-info">
            <span class="user-list-name">
              <?= htmlspecialchars($u['name']) ?>
              <?php if ($isMe): ?>
                <span class="you-badge">You</span>
              <?php endif; ?>
            </span>

            <?php foreach ($users as $u): ?>
  <?php $isMe = ((int)$u['id'] === \App\Core\Auth::id()); ?>
  <li class="user-list-item <?= $isMe ? 'user-list-item--me' : '' ?>">

    <div class="user-avatar-sm">
      <?= strtoupper(substr($u['name'], 0, 1)) ?>
    </div>

    <div class="user-list-info">
      <span class="user-list-name">
        <?= htmlspecialchars($u['name']) ?>
        <?php if ($isMe): ?>
          <span class="you-badge">You</span>
        <?php endif; ?>
      </span>
      <span class="user-list-email"><?= htmlspecialchars($u['email']) ?></span>
      <span class="user-list-meta">
        Joined <?= date('M j, Y', strtotime($u['created_at'])) ?>
      </span>
    </div>

    <span class="user-status-dot <?= $u['is_active'] ? 'dot--active' : 'dot--inactive' ?>"></span>

    <?php if (\App\Core\Auth::isAdmin()): ?>
      <div class="exp-actions">

        <!-- Edit link — goes to edit page -->
        <a href="/expense_tracker/public/admin/user/edit?id=<?= $u['id'] ?>"
           class="action-btn action-btn--edit"
           title="Edit user">&#9998;</a>

        <!-- Activate / Deactivate — simple GET link, no JS -->
        <?php if (!$isMe): ?>
          <?php if ($u['is_active']): ?>
            <a href="/expense_tracker/public/admin/user/deactivate?id=<?= $u['id'] ?>"
               class="action-btn action-btn--delete"
               title="Deactivate user">&#128274;</a>
          <?php else: ?>
            <a href="/expense_tracker/public/admin/user/activate?id=<?= $u['id'] ?>"
               class="action-btn action-btn--edit"
               title="Activate user">&#128275;</a>
          <?php endif; ?>
        <?php endif; ?>

      </div>
    <?php endif; ?>

  </li>
<?php endforeach; ?>
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
    <button class="nav-btn active">
      <span>&#9881;</span><span>Account</span>
    </button>
  </nav>
</div>

<!-- Edit User Modal -->
<div class="modal-overlay" id="modal-edit-user" onclick="closeModal('modal-edit-user')">
  <div class="modal modal--sm" onclick="event.stopPropagation()">
    <div class="modal-header">
      <h3>Edit User</h3>
      <button class="modal-close" onclick="closeModal('modal-edit-user')">&#10005;</button>
    </div>
    <form action="/expense_tracker/public/admin/user/update" method="POST">
      <input type="hidden" name="id" id="edit-user-id">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="name" id="edit-user-name" required>
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" id="edit-user-email" required>
      </div>
      <div class="form-group">
        <label>Status</label>
        <select name="is_active" id="edit-user-status">
          <option value="1">Active</option>
          <option value="0">Inactive</option>
        </select>
      </div>
      <div class="form-group">
        <label>Role</label>
        <select name="is_admin" id="edit-user-admin">
          <option value="0">Regular User</option>
          <option value="1">Admin</option>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="closeModal('modal-edit-user')">Cancel</button>
        <button type="submit" class="btn-primary" style="width:auto;margin-top:0">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<script src="/expense_tracker/public/assets/js/app.js"></script>


</body>
</html>
