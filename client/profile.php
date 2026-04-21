<?php
// ============================================================
// ILLUME — Client: Profile
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_client();

$dash_title = 'My Profile';
$active_nav = 'profile';
$pdo        = db();
$uid        = current_user_id();

$user_row = $pdo->prepare("SELECT * FROM users WHERE id=? LIMIT 1");
$user_row->execute([$uid]);
$profile = $user_row->fetch();

$error   = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $name     = trim($_POST['name'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $whatsapp = trim($_POST['whatsapp'] ?? '');
    $new_pw   = $_POST['new_password'] ?? '';
    $conf_pw  = $_POST['confirm_password'] ?? '';

    if (!$name) { $error = 'Name is required.'; }
    elseif ($new_pw && strlen($new_pw) < 8) { $error = 'Password must be at least 8 characters.'; }
    elseif ($new_pw && $new_pw !== $conf_pw) { $error = 'Passwords do not match.'; }
    else {
        $hash = $new_pw ? password_hash($new_pw, PASSWORD_BCRYPT, ['cost'=>12]) : $profile['password_hash'];
        $pdo->prepare("UPDATE users SET name=?, phone=?, whatsapp=?, password_hash=? WHERE id=?")
            ->execute([$name, $phone, $whatsapp ?: $phone, $hash, $uid]);
        // Update session name
        $_SESSION['user_name'] = $name;
        $success = true;
        $profile['name']     = $name;
        $profile['phone']    = $phone;
        $profile['whatsapp'] = $whatsapp;
    }
}

include __DIR__ . '/../includes/dash_header.php';
?>

<div class="dash-page-header">
  <h2 class="dash-page-header__title">My Profile</h2>
</div>

<div style="max-width:600px;">

  <?php if ($success): ?>
  <div class="alert alert--success" style="margin-bottom:1.5rem;">
    <i data-lucide="check-circle"></i> <span>Profile updated successfully!</span>
  </div>
  <?php endif; ?>
  <?php if ($error): ?>
  <div class="alert alert--error" style="margin-bottom:1.5rem;">
    <i data-lucide="alert-circle"></i> <span><?= e($error) ?></span>
  </div>
  <?php endif; ?>

  <!-- Avatar card -->
  <div class="glass" style="padding:2rem;border-radius:var(--r-2xl);margin-bottom:1.5rem;display:flex;align-items:center;gap:1.5rem;">
    <?php $initials = implode('',array_map(fn($w)=>strtoupper($w[0]),array_slice(explode(' ',$profile['name']),0,2))); ?>
    <div style="
      width:72px;height:72px;border-radius:50%;flex-shrink:0;
      background:linear-gradient(135deg,var(--gold),var(--gold-bright));
      display:flex;align-items:center;justify-content:center;
      font-family:var(--font-display);font-weight:800;font-size:1.6rem;color:var(--void);
      box-shadow:0 0 30px var(--gold-glow);
    "><?= e($initials) ?></div>
    <div>
      <div style="font-size:1.2rem;font-weight:700;"><?= e($profile['name']) ?></div>
      <div style="font-size:0.85rem;color:var(--text-muted);"><?= e($profile['email']) ?></div>
      <div style="margin-top:0.5rem;">
        <span class="badge badge--gold">Client</span>
        <?php if ($profile['status']==='active'): ?>
        <span class="badge badge--success" style="margin-left:0.35rem;">Active</span>
        <?php endif; ?>
      </div>
    </div>
    <div style="margin-left:auto;font-size:0.75rem;color:var(--text-muted);text-align:right;">
      Member since<br><?= format_date($profile['created_at'],'M Y') ?>
    </div>
  </div>

  <!-- Update form -->
  <div class="glass" style="padding:2rem;border-radius:var(--r-2xl);">
    <form method="POST" action="" novalidate>
      <?= csrf_field() ?>

      <div class="form-group">
        <label class="form-label" for="name">Full Name *</label>
        <input type="text" name="name" id="name" class="form-input" value="<?= e($profile['name']) ?>" required>
      </div>

      <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input type="email" class="form-input" value="<?= e($profile['email']) ?>" disabled style="opacity:0.5;">
        <div style="font-size:0.72rem;color:var(--text-muted);margin-top:0.3rem;">Email cannot be changed. Contact ILLUME for help.</div>
      </div>

      <div class="grid-2" style="gap:1rem;">
        <div class="form-group">
          <label class="form-label" for="phone">Phone Number</label>
          <input type="tel" name="phone" id="phone" class="form-input" value="<?= e($profile['phone'] ?? '') ?>" placeholder="+234 800 000 0000">
        </div>
        <div class="form-group">
          <label class="form-label" for="whatsapp">WhatsApp Number</label>
          <input type="tel" name="whatsapp" id="whatsapp" class="form-input" value="<?= e($profile['whatsapp'] ?? '') ?>" placeholder="Leave blank if same as phone">
        </div>
      </div>

      <div style="border-top:1px solid var(--space-border);margin:1.5rem 0;padding-top:1.5rem;">
        <div style="font-size:0.78rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--text-muted);margin-bottom:1rem;">Change Password (optional)</div>
        <div class="grid-2" style="gap:1rem;">
          <div class="form-group">
            <label class="form-label" for="new_password">New Password</label>
            <input type="password" name="new_password" id="new_password" class="form-input" placeholder="Min. 8 characters">
          </div>
          <div class="form-group">
            <label class="form-label" for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-input" placeholder="Repeat new password">
          </div>
        </div>
      </div>

      <button type="submit" class="btn btn--primary">
        <i data-lucide="save"></i> Save Changes
      </button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../includes/dash_footer.php'; ?>
