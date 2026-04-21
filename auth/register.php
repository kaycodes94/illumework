<?php
// ============================================================
// ILLUME — Auth Register
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Already logged in → redirect
if (is_logged_in()) redirect_by_role();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Security check failed. Please try again.';
    } else {
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        $role     = $_POST['role'] ?? 'client';
        $pcode    = trim($_POST['invite_code'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        // Validation
        if (!$name || !$email || !$password) {
            $error = 'Please fill in all required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } elseif ($role === 'staff' && strtoupper(trim($pcode)) !== strtoupper(STAFF_INVITE_CODE)) {
            $error = 'Invalid Staff Invite Code.';
        } else {
            try {
                // Check if email exists
                $stmt = db()->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
                $stmt->execute([$email]);
                
                if ($stmt->fetch()) {
                    $error = 'An account with this email already exists.';
                } else {
                    // Create user
                    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                    $stmt = db()->prepare("
                        INSERT INTO users (name, email, phone, password_hash, role, status) 
                        VALUES (?, ?, ?, ?, ?, 'active')
                    ");
                    $stmt->execute([$name, $email, $phone, $hash, $role]);
                    $userId = db()->lastInsertId();

                    // Successful registration -> Log them in
                    $stmt = db()->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt->execute([$userId]);
                    $user = $stmt->fetch();
                    
                    login_user($user);
                    redirect_by_role();
                }
            } catch (PDOException $e) {
                $error = 'Registration failed. Please try again later.';
            }
        }
    }
}

$page_title = 'Create Account';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Account — ILLUME</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/main.css">
  <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/auth.css">
</head>
<body style="cursor:auto;">

<!-- Custom Cursor -->
<div class="cursor-dot"></div>
<div class="cursor-ring"></div>

<div class="auth-page">

  <!-- Visual Panel -->
  <div class="auth-visual">
    <img src="<?= SITE_URL ?>/assets/img/philosophy.png" alt="ILLUME Couture" 
         style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;">
    <div class="auth-visual__overlay"></div>
    <div class="auth-visual__content">
      <span class="auth-visual__logo">ILLUME</span>
      <p class="auth-visual__quote">
        "Fashion is the armor to survive the reality of everyday life."
        <br><span style="font-size:0.85rem; color:rgba(255,255,255,0.7); font-style:normal; margin-top:0.5rem; display:block; font-family:var(--font-body);">— Bill Cunningham</span>
      </p>
    </div>
  </div>

  <!-- Form Panel -->
  <div class="auth-form-panel">
    <div class="auth-form-wrap" style="max-width: 440px;">
      <a href="<?= SITE_URL ?>/" class="auth-logo">ILLUME</a>
      <p class="auth-tagline">Join the Elite.</p>

      <h1 class="auth-title">Create Account</h1>
      <p class="auth-subtitle">Join the ILLUME universe and elevate your style.</p>

      <?php if ($error): ?>
      <div class="alert alert--error" style="margin-bottom: 1.5rem; background: rgba(220,38,38,0.05); color: #DC2626; padding: 0.75rem; border-radius: var(--r); font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem; border: 1px solid rgba(220,38,38,0.1);">
        <i data-lucide="alert-circle" style="width:16px;height:16px;"></i>
        <span><?= e($error) ?></span>
      </div>
      <?php endif; ?>

      <form method="POST" action="" id="register-form" novalidate>
        <?= csrf_field() ?>

        <div class="form-group">
          <label class="form-label">Full Name</label>
          <input type="text" name="name" class="form-input" value="<?= e($_POST['name'] ?? '') ?>" placeholder="e.g. Chioma Eze" required>
        </div>

        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-input" value="<?= e($_POST['email'] ?? '') ?>" placeholder="chioma@example.com" required>
        </div>

        <div class="role-selector">
          <label class="role-option">
            <input type="radio" name="role" value="client" <?= ($_POST['role'] ?? 'client') === 'client' ? 'checked' : '' ?>>
            <div class="role-card">
              <i data-lucide="user"></i>
              <span>Client</span>
            </div>
          </label>
          <label class="role-option">
            <input type="radio" name="role" value="staff" <?= ($_POST['role'] ?? '') === 'staff' ? 'checked' : '' ?>>
            <div class="role-card">
              <i data-lucide="briefcase"></i>
              <span>Staff</span>
            </div>
          </label>
        </div>

        <!-- Conditional Staff Invite Code -->
        <div id="staff-code-group" class="form-group <?= ($_POST['role'] ?? '') === 'staff' ? 'fade-in' : 'hidden' ?>">
          <label class="form-label">Staff Invite Code</label>
          <input type="text" name="invite_code" class="form-input" placeholder="Enter Invite Code" style="border-color: var(--aura-glass);">
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <div class="input-wrap">
            <input type="password" name="password" id="password" class="form-input" placeholder="••••••••" required>
            <button type="button" class="pw-toggle" id="pw-toggle" aria-label="Toggle password">
              <i data-lucide="eye"></i>
            </button>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Confirm Password</label>
          <input type="password" name="confirm_password" class="form-input" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn--primary btn--w-full" style="margin-top:1rem; padding:0.85rem;">
          <i data-lucide="user-plus"></i>
          Register Account
        </button>
      </form>

      <a href="<?= SITE_URL ?>/auth/login.php" class="auth-back">
        <i data-lucide="arrow-left"></i>
        Already have an account? Sign In
      </a>

      <!-- Demo hint (dev only) -->
      <?php if (defined('APP_ENV') && APP_ENV === 'development'): ?>
      <div style="margin-top:2rem; padding:1rem; background:var(--space-mid); border:1px solid var(--space-border); border-radius:var(--r); font-size:0.75rem; color:var(--text-muted);">
        <div style="margin-bottom:0.5rem; color:var(--aura); font-weight:600;">Dev Environment</div>
        <div>Staff Invite Code: <code><?= STAFF_INVITE_CODE ?></code></div>
      </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
<script>
  // Toggle Staff Invite Code field
  const roleRadios = document.querySelectorAll('input[name="role"]');
  const staffGroup = document.getElementById('staff-code-group');
  
  roleRadios.forEach(radio => {
    radio.addEventListener('change', (e) => {
      if (e.target.value === 'staff') {
        staffGroup.classList.remove('hidden');
        staffGroup.classList.add('fade-in');
      } else {
        staffGroup.classList.add('hidden');
        staffGroup.classList.remove('fade-in');
      }
    });
  });

  // Password toggle
  const toggle = document.getElementById('pw-toggle');
  const pwField = document.getElementById('password');
  toggle?.addEventListener('click', () => {
    const isText = pwField.type === 'text';
    pwField.type = isText ? 'password' : 'text';
    toggle.innerHTML = isText ? '<i data-lucide="eye"></i>' : '<i data-lucide="eye-off"></i>';
    if (typeof lucide !== 'undefined') lucide.createIcons();
  });
</script>
</body>
</html>
