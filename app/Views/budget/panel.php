<?php use App\Core\Auth; ?>
<!DOCTYPE html>
<html lang="en">
<section class="add-section">
  <h2>Budget History</h2>

  <!-- Filter bar -->
  <form method="GET" action="/expense_tracker/public/user" class="filter-form" style="margin-bottom:12px">
    <div class="filter-row">
      <div class="filter-group">
        <label>Month From</label>
        <input type="month" name="from" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>">
      </div>
      <div class="filter-group">
        <label>Month To</label>
        <input type="month" name="to" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>">
      </div>
      <div class="filter-group">
        <label>Sort</label>
        <select name="sort">
          <option value="desc" <?= ($_GET['sort'] ?? 'desc') === 'desc' ? 'selected' : '' ?>>Newest first</option>
          <option value="asc"  <?= ($_GET['sort'] ?? 'desc') === 'asc'  ? 'selected' : '' ?>>Oldest first</option>
        </select>
      </div>
    </div>
    <div class="filter-actions">
      <button type="submit" class="btn-filter-apply">Apply</button>
      <a href="/expense_tracker/public/user" class="btn-filter-clear">Clear</a>
    </div>
  </form>
<body>
<div class="app">
  <div class="panel active" id="panel-user">
    <header class="app-header"><h1>Account</h1></header>

    <?php if (!empty($flashSuccess)): ?>
      <div class="flash flash--success" id="flash-banner">
        <span class="flash__icon">✓</span>
        <span class="flash__msg"><?= htmlspecialchars($flashSuccess) ?></span>
        <button class="flash__close" onclick="dismissFlash()">&#10005;</button>
      </div>
    <?php elseif (!empty($flashError)): ?>
      <div class="flash flash--error" id="flash-banner">
        <span class="flash__icon">✕</span>
        <span class="flash__msg"><?= htmlspecialchars($flashError) ?></span>
        <button class="flash__close" onclick="dismissFlash()">&#10005;</button>
      </div>
    <?php endif; ?>

    <div class="user-card">
      <p class="user-name"><?= htmlspecialchars($user['name'] ?? '') ?></p>
      <p style="color:#6B778C;font-size:.85rem;margin-top:4px">
        <?= htmlspecialchars($user['email'] ?? '') ?>
      </p>
    </div>

    <!-- Add / Update Budget -->
    <section class="add-section">
  <h2>Add / Update Budget</h2>
  <form action="/expense_tracker/public/budget/save" method="POST" class="budget-form">
    <div class="budget-form-row">
      <div class="form-group">
        <label>Month</label>
        <input type="month" name="month_year"
          value="<?= htmlspecialchars($_GET['edit_month'] ?? date('Y-m')) ?>"
          required>
      </div>
      <div class="form-group">
        <label>Amount (DKK)</label>
        <input type="number" name="amount" step="0.01" min="0.01"
          value="<?= htmlspecialchars($_GET['edit_amount'] ?? '') ?>"
          placeholder="e.g. 5000" required>
      </div>
    </div>
    <button type="submit" class="btn-primary">Save Budget</button>
  </form>
</section>

    <!-- Budget History -->
    <section class="add-section">
      <h2>Budget History</h2>
      <?php if (empty($allBudgets)): ?>
  <p class="empty-state" style="padding:20px 0">
    <?= (!empty($_GET['from']) || !empty($_GET['to']))
        ? 'No budgets found for the selected date range.'
        : 'No budgets set yet.' ?>
  </p>
      <?php else: ?>
        <div class="budget-table-wrap">
          <table class="budget-table">
            <thead>
              <tr><th>Month</th><th>Amount</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <?php foreach ($allBudgets as $b): ?>
                <tr>
                  <td><?= htmlspecialchars($b['month_year']) ?></td>
                  <td>DKK <?= number_format($b['amount'], 2) ?></td>
                  <td>
                    <div class="exp-actions">
                      <button class="action-btn action-btn--edit"
                              onclick="openEditBudget(<?= $b['id'] ?>, '<?= $b['month_year'] ?>', <?= $b['amount'] ?>)"
                              title="Edit">&#9998;</button>
                      <button class="action-btn action-btn--delete"
                              onclick="confirmDeleteBudget(<?= $b['id'] ?>)"
                              title="Delete">&#10005;</button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>

    <a href="/expense_tracker/public/logout" class="btn-danger btn-full">Log Out</a>
  </div>

  <!-- Edit Budget Modal -->
  <div class="modal-overlay" id="modal-edit-budget" onclick="closeModal('modal-edit-budget')">
    <div class="modal modal--sm" onclick="event.stopPropagation()">
      <div class="modal-header">
        <h3>Edit Budget</h3>
        <button class="modal-close" onclick="closeModal('modal-edit-budget')">&#10005;</button>
      </div>
      <form action="/expense_tracker/public/budget/save" method="POST">
        <input type="hidden" name="month_year" id="edit-bud-month">
        <div class="form-group">
          <label>Month</label>
          <input type="text" id="edit-bud-month-display" disabled
                 style="background:#F4F5F7;color:#6B778C">
        </div>
        <div class="form-group">
          <label>New Amount (DKK)</label>
          <input type="number" name="amount" id="edit-bud-amount"
                 step="0.01" min="0.01" required>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary"
                  onclick="closeModal('modal-edit-budget')">Cancel</button>
          <button type="submit" class="btn-primary"
                  style="width:auto;margin-top:0">Save</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Budget Modal -->
  <div class="modal-overlay" id="modal-delete-budget" onclick="closeModal('modal-delete-budget')">
    <div class="modal modal--sm" onclick="event.stopPropagation()">
      <div class="modal-header">
        <h3>Delete Budget</h3>
        <button class="modal-close" onclick="closeModal('modal-delete-budget')">&#10005;</button>
      </div>
      <p class="modal-body-text">Are you sure you want to delete this budget entry?</p>
      <div class="modal-footer">
        <button type="button" class="btn-secondary"
                onclick="closeModal('modal-delete-budget')">Cancel</button>
        <a href="#" id="confirm-delete-budget-link" class="btn-danger-sm">Yes, Delete</a>
      </div>
    </div>
  </div>

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
