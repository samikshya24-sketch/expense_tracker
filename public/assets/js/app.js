// app.js — panel toggle, keypad, category selection, modals, filter, validation

// ── PANEL SWITCHING ──────────────────────────────────────────
function switchPanel(name) {
  document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
  const panel = document.getElementById('panel-' + name);
  if (panel) panel.classList.add('active');
  const btn = document.getElementById('nav-' + name);
  if (btn) btn.classList.add('active');
}

// ── FLASH BANNER AUTO-DISMISS ────────────────────────────────
// Auto-hide the flash banner after 4 seconds
window.addEventListener('DOMContentLoaded', () => {
  const banner = document.getElementById('flash-banner');
  if (banner) {
    setTimeout(() => {
      banner.classList.add('flash--hiding');
      setTimeout(() => banner.remove(), 400);
    }, 4000);
  }
});

function dismissFlash() {
  const banner = document.getElementById('flash-banner');
  if (banner) {
    banner.classList.add('flash--hiding');
    setTimeout(() => banner.remove(), 400);
  }
}

// ── NUMERIC KEYPAD ───────────────────────────────────────────
let currentAmount = '';

function keyPress(key) {
  if (key === 'del') {
    currentAmount = currentAmount.slice(0, -1);
  } else if (key === '.') {
    if (!currentAmount.includes('.')) {
      currentAmount += currentAmount === '' ? '0.' : '.';
    }
  } else {
    if (currentAmount === '0') currentAmount = '';
    if (currentAmount.includes('.')) {
      const dec = currentAmount.split('.')[1];
      if (dec && dec.length >= 2) return;
    }
    currentAmount += key;
  }
  const display = document.getElementById('amount-display');
  const input   = document.getElementById('amount-input');
  if (display) display.textContent = currentAmount || '0';
  if (input)   input.value         = currentAmount;

  // Clear amount hint if user has entered something
  if (currentAmount && parseFloat(currentAmount) > 0) {
    const hint = document.getElementById('amt-hint');
    if (hint) hint.style.display = 'none';
    if (display) display.classList.remove('amount-display--error');
  }
}

// ── CATEGORY SELECTION ───────────────────────────────────────
function selectCategory(btn) {
  document.querySelectorAll('.cat-select-btn').forEach(b => {
    b.classList.remove('selected');
    b.style.borderColor = '';
  });
  btn.classList.add('selected');
  btn.style.borderColor = btn.dataset.color;
  const hidden = document.getElementById('selected_category');
  if (hidden) hidden.value = btn.dataset.id;

  // Clear category hint once selected
  const hint = document.getElementById('cat-hint');
  if (hint) hint.style.display = 'none';
}

// ── ADD FORM VALIDATION (inline hints, no browser alert) ─────
// Called by the form's onsubmit. Returns false to block submit if invalid.
function validateAddForm(e) {
  const cat     = document.getElementById('selected_category').value;
  const amt     = document.getElementById('amount-input').value;
  const catHint = document.getElementById('cat-hint');
  const amtHint = document.getElementById('amt-hint');
  const display = document.getElementById('amount-display');
  let valid = true;

  // Category check
  if (!cat) {
    if (catHint) catHint.style.display = 'block';
    // Shake the category grid
    const grid = document.querySelector('.cat-select-grid');
    if (grid) { grid.classList.add('shake'); setTimeout(() => grid.classList.remove('shake'), 500); }
    valid = false;
  } else {
    if (catHint) catHint.style.display = 'none';
  }

  // Amount check
  if (!amt || parseFloat(amt) <= 0) {
    if (amtHint) amtHint.style.display = 'block';
    if (display) display.classList.add('amount-display--error');
    valid = false;
  } else {
    if (amtHint) amtHint.style.display = 'none';
    if (display) display.classList.remove('amount-display--error');
  }

  if (!valid) {
    e.preventDefault();
    // Scroll to top of add panel so user can see the hints
    const panel = document.getElementById('panel-add');
    if (panel) panel.scrollTop = 0;
  }
  return valid;
}

// ── MODAL HELPERS ────────────────────────────────────────────
function openModal(id) {
  const m = document.getElementById(id);
  if (m) { m.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
}
function closeModal(id) {
  const m = document.getElementById(id);
  if (m) { m.style.display = 'none'; document.body.style.overflow = ''; }
}
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal-overlay').forEach(m => { m.style.display = 'none'; });
    document.body.style.overflow = '';
  }
});

// ── EDIT EXPENSE ─────────────────────────────────────────────
function openEditExpense(exp) {
  document.getElementById('edit-exp-id').value     = exp.id;
  document.getElementById('edit-exp-cat').value    = exp.category_id;
  document.getElementById('edit-exp-amount').value = exp.amount;
  document.getElementById('edit-exp-note').value   = exp.note || '';
  document.getElementById('edit-exp-date').value   = exp.spent_date;
  openModal('modal-edit-expense');
}

// ── DELETE EXPENSE ───────────────────────────────────────────
function confirmDeleteExpense(id) {
  document.getElementById('confirm-delete-expense-link').href =
    '/expense_tracker/public/expense/delete?id=' + id;
  openModal('modal-delete-expense');
}

// ── EDIT BUDGET ──────────────────────────────────────────────
function openEditBudget(id, monthYear, amount) {
  document.getElementById('edit-bud-month').value         = monthYear;
  document.getElementById('edit-bud-month-display').value = monthYear;
  document.getElementById('edit-bud-amount').value        = amount;
  openModal('modal-edit-budget');
}

// ── DELETE BUDGET ────────────────────────────────────────────
function confirmDeleteBudget(id) {
  document.getElementById('confirm-delete-budget-link').href =
    '/expense_tracker/public/budget/delete?id=' + id;
  openModal('modal-delete-budget');
}

// ── FILTER TOGGLE ────────────────────────────────────────────
function toggleFilter() {
  const bar = document.getElementById('filter-bar');
  if (bar) {
    if (bar.style.display === 'none' || bar.style.display === '') {
      bar.style.display = 'block';
    } else {
      bar.style.display = 'none';
    }
  }
}

// ── SEARCH TOGGLE ────────────────────────────────────────────
function toggleSearch() {
  const bar = document.getElementById('search-bar');
  if (bar) {
    if (bar.style.display === 'none' || bar.style.display === '') {
      bar.style.display = 'block';
      const input = bar.querySelector('input[name="search"]');
      if (input) input.focus();
    } else {
      bar.style.display = 'none';
    }
  }
}

