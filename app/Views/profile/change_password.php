<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password — Expense Tracker</title>
  <link rel="stylesheet" href="/expense_tracker/public/assets/css/style.css">
</head>
<body>
<div class="app">

  <header class="app-header">
    <button class="back-btn" onclick="window.location='/expense_tracker/public/profile'">&#8592; Back</button>
    <h1>Change Password</h1>
  </header>

  <div style="padding:20px">

    <?php if (!empty($error)): ?>
      <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="/expense_tracker/public/profile/change-password" method="POST"
          onsubmit="return validatePasswordForm()">

      <div class="form-group">
        <label>Current Password</label>
        <div class="password-wrap">
          <input type="password" name="current_password" id="cur-pwd"
            placeholder="Enter current password" required>
          <button type="button" class="pwd-toggle" onclick="togglePwd('cur-pwd', this)">&#128065;</button>
        </div>
      </div>

      <div class="form-group">
        <label>New Password</label>
        <div class="password-wrap">
          <input type="password" name="new_password" id="new-pwd"
            placeholder="At least 6 characters" required minlength="6"
            oninput="checkStrength(this.value)">
          <button type="button" class="pwd-toggle" onclick="togglePwd('new-pwd', this)">&#128065;</button>
        </div>
        <!-- Strength bar -->
        <div class="strength-bar-wrap">
          <div class="strength-bar" id="strength-bar"></div>
        </div>
        <p class="strength-label" id="strength-label"></p>
      </div>

      <div class="form-group">
        <label>Confirm New Password</label>
        <div class="password-wrap">
          <input type="password" name="confirm_password" id="conf-pwd"
            placeholder="Repeat new password" required>
          <button type="button" class="pwd-toggle" onclick="togglePwd('conf-pwd', this)">&#128065;</button>
        </div>
        <p class="field-match" id="match-msg"></p>
      </div>

      <div class="form-hint">
        <span>&#9432;</span>
        Must be at least 6 characters and different from your current password.
      </div>

      <button type="submit" class="btn-primary" style="margin-top:20px">
        Update Password
      </button>
      <a href="/expense_tracker/public/profile" class="btn-cancel">Cancel</a>

    </form>
  </div>

</div>
<script>
function togglePwd(id, btn) {
  const f = document.getElementById(id);
  if (f.type === 'password') { f.type = 'text'; btn.style.opacity = '1'; }
  else { f.type = 'password'; btn.style.opacity = '.5'; }
}

function checkStrength(val) {
  const bar   = document.getElementById('strength-bar');
  const label = document.getElementById('strength-label');
  let score = 0;
  if (val.length >= 6)  score++;
  if (val.length >= 10) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;
  const levels = [
    { pct:'20%', color:'#DE350B', text:'Very weak' },
    { pct:'40%', color:'#FF8B00', text:'Weak' },
    { pct:'60%', color:'#FFBF00', text:'Fair' },
    { pct:'80%', color:'#00B8D9', text:'Strong' },
    { pct:'100%',color:'#36B37E', text:'Very strong' },
  ];
  const l = levels[Math.min(score, 4)];
  bar.style.width = l.pct;
  bar.style.background = l.color;
  label.textContent = val ? l.text : '';
  label.style.color = l.color;
}

document.getElementById('conf-pwd').addEventListener('input', function() {
  const msg = document.getElementById('match-msg');
  const newVal = document.getElementById('new-pwd').value;
  if (!this.value) { msg.textContent = ''; return; }
  if (this.value === newVal) {
    msg.textContent = '✓ Passwords match';
    msg.style.color = '#36B37E';
  } else {
    msg.textContent = '✗ Passwords do not match';
    msg.style.color = '#DE350B';
  }
});

function validatePasswordForm() {
  const np = document.getElementById('new-pwd').value;
  const cp = document.getElementById('conf-pwd').value;
  if (np !== cp) {
    alert('New passwords do not match.');
    return false;
  }
  return true;
}
</script>
</body>
</html>
