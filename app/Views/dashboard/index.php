<?php
use App\Core\Auth;
use App\Models\Budget;

$categories_add = $allCategories;
$filterCatId    = (int)($_GET['cat']  ?? 0);
$filterFrom     = $_GET['from'] ?? '';
$filterTo       = $_GET['to']   ?? '';
$searchQuery    = $_GET['search'] ?? '';
$hasFilter      = $filterCatId || $filterFrom || $filterTo;
$hasSearch      = !empty($searchQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Expense Tracker</title>
  <link rel="stylesheet" href="/expense_tracker/public/assets/css/style.css?v=<?= time() ?>">
</head>
<body>
<div class="app">

  <!-- ══ PANEL 1: DASHBOARD ════════════════════════════════════ -->
  <div class="panel active" id="panel-home">
    <header class="app-header">
      <h1>My Expenses</h1>
      <span class="month-label"><?= date('F Y') ?></span>
    </header>

    <!-- ── Flash Banners ─────────────────────────────────────── -->
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
    <?php elseif (!empty($flashWarning)): ?>
      <div class="flash flash--warning" id="flash-banner">
        <span class="flash__icon">⚠</span>
        <span class="flash__msg"><?= htmlspecialchars($flashWarning) ?></span>
        <button class="flash__close" onclick="dismissFlash()">&#10005;</button>
      </div>
    <?php endif; ?>

    <!-- ── Overall Budget Summary Card ──────────────────────── -->
    <div class="summary-card <?= $overBudget ? 'over' : 'under' ?>">
      <span class="label">Spent this month</span>
      <span class="amount">DKK <?= number_format($monthlyTotal, 2) ?></span>
      <?php if ($budget > 0): ?>
        <?php if ($overBudget): ?>
          <span class="over-text">⚠ DKK <?= number_format(abs($remaining), 2) ?> over budget</span>
        <?php else: ?>
          <span class="under-text">DKK <?= number_format($remaining, 2) ?> remaining</span>
        <?php endif; ?>
        <div class="budget-progress-wrap">
          <?php $pct = min(100, ($monthlyTotal / $budget) * 100); ?>
          <div class="budget-progress-bar">
            <div class="budget-progress-fill <?= $overBudget ? 'fill--over' : ($pct >= 80 ? 'fill--warning' : 'fill--ok') ?>"
                 style="width:<?= number_format($pct, 1) ?>%"></div>
          </div>
          <span class="budget-pct"><?= number_format($pct, 0) ?>% of DKK <?= number_format($budget, 2) ?></span>
        </div>
      <?php else: ?>
        <a href="#" onclick="switchPanel('user')" class="set-budget-link">Set a monthly budget &rarr;</a>
      <?php endif; ?>
    </div>

    <!-- ── Category Tiles with Budget Status ─────────────────── -->
    <section class="categories-section">
      <h2>This Month by Category</h2>

      <?php
      // Collect any over/on-budget categories to show alert strip
      $overCats    = array_filter($categories, fn($c) => $c['budget_status'] === 'over'    && $c['total'] > 0);
      $warningCats = array_filter($categories, fn($c) => $c['budget_status'] === 'warning' && $c['total'] > 0);
      ?>

      <?php if (!empty($overCats)): ?>
        <div class="cat-alert cat-alert--over">
          <span>🔴</span>
          <span>
            <strong>Over budget:</strong>
            <?= implode(', ', array_map(fn($c) => htmlspecialchars($c['name']), $overCats)) ?>
          </span>
        </div>
      <?php endif; ?>

      <?php if (!empty($warningCats)): ?>
        <div class="cat-alert cat-alert--warning">
          <span>🟡</span>
          <span>
            <strong>Near limit:</strong>
            <?= implode(', ', array_map(fn($c) => htmlspecialchars($c['name']), $warningCats)) ?>
          </span>
        </div>
      <?php endif; ?>

      <div class="category-tiles">
        <?php foreach ($categories as $cat): ?>
          <?php if ($cat['total'] > 0): ?>
            <div class="cat-tile cat-tile--<?= $cat['budget_status'] ?>"
                 style="border-color:<?= $cat['color'] ?>">
              <div class="cat-tile-top">
                <span class="cat-icon"><?= $cat['icon'] ?></span>
                <?php if ($cat['budget_status'] === 'over'): ?>
                  <span class="cat-status-badge badge--over">Over</span>
                <?php elseif ($cat['budget_status'] === 'warning'): ?>
                  <span class="cat-status-badge badge--warning">Near</span>
                <?php elseif ($cat['budget_status'] === 'ok'): ?>
                  <span class="cat-status-badge badge--ok">OK</span>
                <?php endif; ?>
              </div>
              <span class="cat-name"><?= htmlspecialchars($cat['name']) ?></span>
              <span class="cat-amount">DKK <?= number_format($cat['total'], 2) ?></span>
              <?php if ($cat['per_cat_budget'] > 0): ?>
                <?php $catPct = min(100, ($cat['total'] / $cat['per_cat_budget']) * 100); ?>
                <div class="cat-mini-bar">
                  <div class="cat-mini-fill <?= $cat['budget_status'] === 'over' ? 'fill--over' : ($cat['budget_status'] === 'warning' ? 'fill--warning' : 'fill--ok') ?>"
                       style="width:<?= number_format($catPct, 1) ?>%"></div>
                </div>
                <span class="cat-budget-limit">of DKK <?= number_format($cat['per_cat_budget'], 2) ?></span>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- ── Expense History with Filter ───────────────────────── -->
    <section class="history-section">
      <div class="section-title-row">
        <h2>All Expenses</h2>
        <div style="display:flex;gap:8px">
          <button class="icon-btn <?= $hasSearch ? 'icon-btn--active' : '' ?>"
                  onclick="toggleSearch()" title="Search expenses">
            &#128269; Search<?= $hasSearch ? ' &#9679;' : '' ?>
          </button>
          <button class="icon-btn <?= $hasFilter ? 'icon-btn--active' : '' ?>"
                  onclick="toggleFilter()" title="Filter expenses">
            &#9776; Filter<?= $hasFilter ? ' &#9679;' : '' ?>
          </button>
        </div>
      </div>

      <!-- Search Bar -->
      <div class="filter-bar" id="search-bar" style="display:<?= $hasSearch ? 'block' : 'none' ?>;margin-bottom:12px">
        <form method="GET" action="/expense_tracker/public/" class="filter-form" style="padding:0">
          <input type="hidden" name="cat" value="<?= htmlspecialchars($filterCatId ?: '') ?>">
          <input type="hidden" name="from" value="<?= htmlspecialchars($filterFrom ?: '') ?>">
          <input type="hidden" name="to" value="<?= htmlspecialchars($filterTo ?: '') ?>">
          
          <div class="user-search-row" style="margin-bottom:0">
            <div style="position:relative;flex:1">
              <span class="search-icon">&#128269;</span>
              <input type="text" name="search"
                value="<?= htmlspecialchars($searchQuery ?: '') ?>"
                placeholder="Search note or category name..."
                class="search-input">
            </div>
            <button type="submit" class="btn-filter-apply">Go</button>
            <?php if ($hasSearch): ?>
              <a href="/expense_tracker/public/?cat=<?= $filterCatId ?>&from=<?= $filterFrom ?>&to=<?= $filterTo ?>" class="btn-filter-clear">Clear</a>
            <?php endif; ?>
          </div>
        </form>
      </div>

      <!-- Filter Bar -->
      <div class="filter-bar" id="filter-bar" style="display:<?= $hasFilter ? 'block' : 'none' ?>">
        <form method="GET" action="/expense_tracker/public/" class="filter-form">
          <input type="hidden" name="search" value="<?= htmlspecialchars($searchQuery ?: '') ?>">
          <div class="filter-row">
            <div class="filter-group">
              <label>Category</label>
              <select name="cat">
                <option value="">All</option>
                <?php foreach ($allCategories as $c): ?>
                  <option value="<?= $c['id'] ?>" <?= $filterCatId == $c['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="filter-group">
              <label>From</label>
              <input type="date" name="from" value="<?= htmlspecialchars($filterFrom) ?>">
            </div>
            <div class="filter-group">
              <label>To</label>
              <input type="date" name="to" value="<?= htmlspecialchars($filterTo) ?>">
            </div>
          </div>
          <div class="filter-actions">
            <button type="submit" class="btn-filter-apply">Apply</button>
            <a href="/expense_tracker/public/?search=<?= urlencode($searchQuery) ?>" class="btn-filter-clear">Clear</a>
          </div>
        </form>
      </div>

      <?php if ($hasFilter || $hasSearch): ?>
        <p class="filter-active-notice">Filtered results &mdash; <a href="/expense_tracker/public/">show all</a></p>
      <?php endif; ?>

      <?php if (empty($expenses)): ?>
        <p class="empty-state">No expenses found. Tap + to add one.</p>
      <?php else: ?>
        <ul class="expense-list">
          <?php foreach ($expenses as $exp): ?>
            <li class="expense-item">
              <span class="exp-dot" style="background:<?= $exp['color'] ?>"></span>
              <div class="exp-details">
                <span class="exp-cat"><?= $exp['icon'] ?> <?= htmlspecialchars($exp['cat_name']) ?></span>
                <?php if ($exp['note']): ?>
                  <span class="exp-note"><?= htmlspecialchars($exp['note']) ?></span>
                <?php endif; ?>
              </div>
              <div class="exp-right">
                <span class="exp-amount">DKK <?= number_format($exp['amount'], 2) ?></span>
                <span class="exp-date"><?= $exp['spent_date'] ?></span>
              </div>
              <div class="exp-actions">
                <button class="action-btn action-btn--edit"
                        onclick='openEditExpense(<?= json_encode($exp) ?>)'
                        title="Edit">&#9998;</button>
                <button class="action-btn action-btn--delete"
                        onclick="confirmDeleteExpense(<?= $exp['id'] ?>)"
                        title="Delete">&#10005;</button>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </section>
  </div>

  <!-- ══ PANEL 2: ADD EXPENSE ══════════════════════════════════ -->
  <div class="panel" id="panel-add">
    <header class="app-header">
      <button class="back-btn" onclick="switchPanel('home')">&#8592; Back</button>
      <h1>Add Expense</h1>
    </header>

    <!-- Inline validation error shown by JS -->
    <div class="inline-error" id="add-error" style="display:none"></div>

    <form action="/expense_tracker/public/expense/save" method="POST" id="add-form"
          onsubmit="return validateAddForm(event)">
      <section class="add-section">
        <h2>Category</h2>
        <p class="field-hint" id="cat-hint" style="display:none;color:var(--danger);font-size:.8rem;margin-bottom:8px">
          ⚠ Please select a category
        </p>
        <div class="cat-select-grid">
          <?php foreach ($categories_add as $c): ?>
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
        <p class="field-hint" id="amt-hint" style="display:none;color:var(--danger);font-size:.8rem;margin-bottom:8px">
          ⚠ Please enter an amount greater than zero
        </p>
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

  <!-- ══ PANEL 3: ACCOUNT / BUDGET ═════════════════════════════ -->
<div class="panel" id="panel-user">
  <header class="app-header"><h1>Account</h1></header>

  <div class="user-card">
    <p class="user-name"><?= htmlspecialchars(Auth::name()) ?></p>
  </div>

  <!-- Profile & Settings Links -->
  <div style="padding:0 16px;margin-top:12px;display:flex;flex-direction:column;gap:10px">

    <a href="/expense_tracker/public/profile" class="profile-action-card">
      <div class="pac-icon pac-icon--blue">&#9998;</div>
      <div class="pac-text">
        <span class="pac-title">My Profile</span>
        <span class="pac-sub">Edit name, email, password</span>
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

  </div>

  <!-- ── BUDGET SECTION ──────────────────────────────────── -->
  <section class="add-section" style="margin-top:16px">

    <!-- Section label -->
    <div class="section-title-row">
      <h2 style="color:var(--primary);font-size:.9rem;font-weight:700;
                 text-transform:uppercase;letter-spacing:.06em">
        &#128181; Budget
      </h2>
      <a href="/expense_tracker/public/user"
         style="font-size:.78rem;color:var(--primary);text-decoration:none;
                background:#DEEBFF;padding:4px 10px;border-radius:6px;font-weight:600">
        Manage all &rarr;
      </a>
    </div>

    <!-- Current month budget summary -->
    <div class="budget-inline-card">
      <div class="budget-inline-top">
        <span class="budget-inline-label">Budget for <?= date('F Y') ?></span>
        <?php if ($budget > 0): ?>
          <span class="budget-inline-amount">DKK <?= number_format($budget, 2) ?></span>
        <?php else: ?>
          <span class="budget-inline-none">Not set</span>
        <?php endif; ?>
      </div>
      <?php if ($budget > 0): ?>
        <div class="budget-progress-bar" style="margin-top:8px">
          <?php $pct = min(100, ($monthlyTotal / $budget) * 100); ?>
          <div class="budget-progress-fill <?= $overBudget ? 'fill--over' : ($pct >= 80 ? 'fill--warning' : 'fill--ok') ?>"
               style="width:<?= number_format($pct,1) ?>%"></div>
        </div>
        <span style="font-size:.72rem;color:var(--text-muted);margin-top:4px;display:block">
          DKK <?= number_format($monthlyTotal,2) ?> spent —
          <?= $overBudget
              ? '<span style="color:var(--danger);font-weight:600">DKK '.number_format(abs($remaining),2).' over</span>'
              : '<span style="color:var(--success);font-weight:600">DKK '.number_format($remaining,2).' left</span>' ?>
        </span>
      <?php endif; ?>
    </div>

    <!-- Quick Add Budget form -->
    <form action="/expense_tracker/public/budget/save" method="POST"
          style="margin-top:12px">
      <div class="budget-form-row">
        <div class="form-group" style="margin-bottom:0">
          <label style="font-size:.75rem">Month</label>
          <input type="month" name="month_year" value="<?= date('Y-m') ?>" required>
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label style="font-size:.75rem">Amount (DKK)</label>
          <input type="number" name="amount" step="0.01" min="0.01"
                 placeholder="e.g. 5000" required>
        </div>
      </div>
      <button type="submit" class="btn-primary" style="margin-top:10px">
        &#43; Add / Update Budget
      </button>
    </form>

    <!-- Recent budgets list with edit & delete -->
    <?php
    $recentBudgets = Budget::getAllByUser(Auth::id());
    $recentBudgets = array_slice($recentBudgets, 0, 3); // show last 3
    ?>
    <?php if (!empty($recentBudgets)): ?>
      <div style="margin-top:14px">
        <p style="font-size:.72rem;color:var(--text-muted);
                  text-transform:uppercase;letter-spacing:.05em;
                  margin-bottom:8px;font-weight:700">Recent Budgets</p>
        <?php foreach ($recentBudgets as $b): ?>
          <div class="budget-row-inline">
            <div class="budget-row-info">
              <span class="budget-row-month">&#128197; <?= htmlspecialchars($b['month_year']) ?></span>
              <span class="budget-row-amt">DKK <?= number_format($b['amount'], 2) ?></span>
            </div>
            <div class="exp-actions">
              <!-- Edit — goes to budget settings page with pre-selected month -->
              <a href="/expense_tracker/public/user?edit_id=<?= $b['id'] ?>&edit_month=<?= urlencode($b['month_year']) ?>&edit_amount=<?= $b['amount'] ?>"
                 class="action-btn action-btn--edit" title="Edit">&#9998;</a>
              <!-- Delete -->
              <a href="/expense_tracker/public/budget/delete?id=<?= $b['id'] ?>"
                 class="action-btn action-btn--delete"
                 title="Delete"
                 onclick="return confirm('Delete budget for <?= htmlspecialchars($b['month_year']) ?>?')">&#10005;</a>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (count(Budget::getAllByUser(Auth::id())) > 3): ?>
          <a href="/expense_tracker/public/user"
             style="font-size:.78rem;color:var(--primary);display:block;
                    text-align:center;margin-top:8px;text-decoration:none">
            View all budgets &rarr;
          </a>
        <?php endif; ?>
      </div>
    <?php endif; ?>

  </section>




<!-- ══ MODAL: EDIT EXPENSE ════════════════════════════════════ -->
<div class="modal-overlay" id="modal-edit-expense" onclick="closeModal('modal-edit-expense')">
  <div class="modal" onclick="event.stopPropagation()">
    <div class="modal-header">
      <h3>Edit Expense</h3>
      <button class="modal-close" onclick="closeModal('modal-edit-expense')">&#10005;</button>
    </div>
    <form action="/expense_tracker/public/expense/update" method="POST">
      <input type="hidden" name="id" id="edit-exp-id">
      <div class="form-group">
        <label>Category</label>
        <select name="category_id" id="edit-exp-cat" required>
          <?php foreach ($allCategories as $c): ?>
            <option value="<?= $c['id'] ?>"><?= $c['icon'] ?> <?= htmlspecialchars($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Amount (DKK)</label>
        <input type="number" name="amount" id="edit-exp-amount" step="0.01" min="0.01" required>
      </div>
      <div class="form-group">
        <label>Note (optional)</label>
        <input type="text" name="note" id="edit-exp-note" placeholder="What was this for?">
      </div>
      <div class="form-group">
        <label>Date</label>
        <input type="date" name="spent_date" id="edit-exp-date" required>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="closeModal('modal-edit-expense')">Cancel</button>
        <button type="submit" class="btn-primary" style="width:auto;margin-top:0">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<!-- ══ MODAL: DELETE EXPENSE ══════════════════════════════════ -->
<div class="modal-overlay" id="modal-delete-expense" onclick="closeModal('modal-delete-expense')">
  <div class="modal modal--sm" onclick="event.stopPropagation()">
    <div class="modal-header">
      <h3>Delete Expense</h3>
      <button class="modal-close" onclick="closeModal('modal-delete-expense')">&#10005;</button>
    </div>
    <p class="modal-body-text">Are you sure you want to delete this expense? This cannot be undone.</p>
    <div class="modal-footer">
      <button type="button" class="btn-secondary" onclick="closeModal('modal-delete-expense')">Cancel</button>
      <a href="#" id="confirm-delete-expense-link" class="btn-danger-sm">Yes, Delete</a>
    </div>
  </div>
</div>


  <a href="/expense_tracker/public/logout" class="btn-danger btn-full" style="margin-top:16px">
    Log Out
  </a>
  </div>
</div>

<!-- Bottom Nav -->
<nav class="bottom-nav">
  <button class="nav-btn active" id="nav-home" onclick="switchPanel('home')">
    <span>&#8962;</span><span>Home</span>
  </button>
  <button class="nav-btn nav-add" onclick="switchPanel('add')">
    <span class="add-icon">+</span>
  </button>
  <button class="nav-btn" id="nav-user" onclick="switchPanel('user')">
    <span>&#9881;</span><span>Account</span>
  </button>
</nav>

<script src="/expense_tracker/public/assets/js/app.js?v=<?= time() ?>"></script>
</body>
</html>
