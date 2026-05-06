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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/main.css">
  <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/auth.css">
</head>
<body style="cursor:auto;">

<!-- Custom Cursor -->
<div class="cursor-dot"></div>
<div class="cursor-ring"></div>

<div class="auth-page">

  <!-- Visual Panel -->
  <div class="auth-visual" style="flex: 1; position: relative; overflow: hidden; display: block;">
    <img src="<?= SITE_URL ?>/assets/img/philosophy.png" alt="ILLUME Couture" 
         style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;">
    <div class="auth-visual__content" style="position: absolute; bottom: 0; left: 0; right: 0; padding: 64px; z-index: 2; background: linear-gradient(transparent, rgba(0,0,0,0.8));">
      <p class="auth-visual__quote" style="font-size: 1.2rem; color: #FFF; font-style: italic; line-height: 1.6; max-width: 400px; opacity: 0.9;">
        "crafted in light, radiance you can wear"
        <br><span style="font-size:0.85rem; color:rgba(255,255,255,0.7); font-style:normal; margin-top:0.5rem; display:block;">— Olewuezi Ikedichukwu Peace</span>
      </p>
    </div>
  </div>

  <!-- Form Panel -->
  <div class="auth-form" style="width: 100%; max-width: 500px; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 64px 48px; background: #FFF;">
    <div class="auth-form-inner" style="width: 100%; max-width: 440px;">
      <div style="text-align: center; margin-bottom: 3rem;">
        <a href="<?= SITE_URL ?>/" class="auth-logo" style="display: inline-flex; align-items: center; gap: 0.75rem; text-decoration: none; color: #000;">
          <span style="font-weight: 800; font-size: 1.8rem; letter-spacing: -0.02em;">ILLUME</span>
          <img src="<?= SITE_URL ?>/assets/img/logo.png" alt="Logo" style="height: 48px; width: auto;">
        </a>
      </div>
      <p class="auth-tagline" style="text-align: center; font-size: 0.75rem; letter-spacing: 0.2em; text-transform: uppercase; margin-bottom: 2rem; color: #666;">Join the Elite.</p>

      <h1 class="auth-title" style="font-size: 1.8rem; font-weight: 700; color: #000; margin-bottom: 0.5rem; text-align: center;">Create Account</h1>
      <p class="auth-subtitle" style="font-size: 0.95rem; color: #666; margin-bottom: 2rem; text-align: center;">Join the ILLUME universe and elevate your style.</p>

      <?php if ($error): ?>
      <div class="alert alert--error">
        <i data-lucide="alert-circle"></i>
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

        <div class="role-selector" style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
          <label class="role-option" style="flex: 1; cursor: pointer;">
            <input type="radio" name="role" value="client" <?= ($_POST['role'] ?? 'client') === 'client' ? 'checked' : '' ?> style="display: none;">
            <div class="role-card" style="padding: 1rem; border: 1px solid var(--divider); border-radius: var(--r); text-align: center; transition: all 0.3s;">
              <i data-lucide="user" style="display: block; margin: 0 auto 0.5rem;"></i>
              <span>Client</span>
            </div>
          </label>
          <label class="role-option" style="flex: 1; cursor: pointer;">
            <input type="radio" name="role" value="staff" <?= ($_POST['role'] ?? '') === 'staff' ? 'checked' : '' ?> style="display: none;">
            <div class="role-card" style="padding: 1rem; border: 1px solid var(--divider); border-radius: var(--r); text-align: center; transition: all 0.3s;">
              <i data-lucide="briefcase" style="display: block; margin: 0 auto 0.5rem;"></i>
              <span>Staff</span>
            </div>
          </label>
        </div>

        <div id="staff-code-group" class="form-group <?= ($_POST['role'] ?? '') === 'staff' ? '' : 'hidden' ?>">
          <label class="form-label">Staff Invite Code</label>
          <input type="text" name="invite_code" class="form-input" placeholder="Enter Invite Code">
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

      <a href="<?= SITE_URL ?>/auth/login.php" class="auth-back" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-top: 2rem; text-decoration: none; color: #666; font-size: 0.85rem;">
        <i data-lucide="arrow-left" style="width: 14px;"></i>
        Already have an account? Sign In
      </a>

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
      } else {
        staffGroup.classList.add('hidden');
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
