<?php
// ============================================================
// ILLUME — Auth Login
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Already logged in → redirect
if (is_logged_in()) redirect_by_role();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Security check failed. Please try again.';
    } else {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            $error = 'Please enter your email and password.';
        } else {
            try {
                $stmt = db()->prepare("SELECT * FROM users WHERE email = ? AND status = 'active' LIMIT 1");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password_hash'])) {
                    login_user($user);
                    // Update last login
                    db()->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
                    redirect_by_role();
                } else {
                    $error = 'Invalid email or password.';
                }
            } catch (PDOException $e) {
                $error = 'An error occurred. Please try again.';
            }
        }
    }
}

$page_title = 'Sign In';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In — ILLUME</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/main.css">
  <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/auth.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js" defer></script>
</head>
<body style="cursor:auto;">

<!-- Custom Cursor -->
<div class="cursor-dot"></div>
<div class="cursor-ring"></div>

<div class="auth-page">

  <!-- Visual Panel -->
  <div class="auth-visual">
    <img src="<?= SITE_URL ?>/assets/img/editorial.png" alt="ILLUME Fashion" 
         style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;">
    <div class="auth-visual__overlay"></div>
    <div class="auth-visual__content">
      <span class="auth-visual__logo">ILLUME</span>
      <p class="auth-visual__quote">
        "Luxury is the ease of a t-shirt in a very expensive dress."
        <br><span style="font-size:0.85rem; color:rgba(255,255,255,0.7); font-style:normal; margin-top:0.5rem; display:block; font-family:var(--font-body);">— Karl Lagerfeld</span>
      </p>
    </div>
  </div>

  <!-- Form Panel -->
  <div class="auth-form-panel">
    <div class="auth-form-wrap">
      <a href="<?= SITE_URL ?>/" class="auth-logo">ILLUME</a>
      <p class="auth-tagline">Fashion. Elevated.</p>

      <h1 class="auth-title">Welcome Back</h1>
      <p class="auth-subtitle">Sign in to your ILLUME portal to continue.</p>

      <?php if ($error): ?>
      <div class="alert alert--error">
        <i data-lucide="alert-circle"></i>
        <span><?= e($error) ?></span>
      </div>
      <?php endif; ?>

      <form method="POST" action="" id="login-form" novalidate>
        <?= csrf_field() ?>

        <div class="form-group">
          <label class="form-label" for="email">Email Address</label>
          <input
            type="email"
            id="email"
            name="email"
            class="form-input"
            value="<?= e($_POST['email'] ?? '') ?>"
            placeholder="your@email.com"
            autocomplete="email"
            required
          >
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="input-wrap">
            <input
              type="password"
              id="password"
              name="password"
              class="form-input"
              placeholder="••••••••"
              autocomplete="current-password"
              required
            >
            <button type="button" class="pw-toggle" id="pw-toggle" aria-label="Toggle password">
              <i data-lucide="eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn btn--primary btn--w-full" style="margin-top:0.5rem; padding:0.85rem;">
          <i data-lucide="log-in"></i>
          Sign In
        </button>
      </form>

      <div style="margin-top:2rem; text-align:center;">
        <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:0.5rem;">Don't have an account?</p>
        <a href="<?= SITE_URL ?>/auth/register.php" class="btn btn--secondary btn--w-full" style="padding:0.75rem;">
          <i data-lucide="user-plus"></i> Create Account
        </a>
      </div>

      <a href="<?= SITE_URL ?>/" class="auth-back">
        <i data-lucide="arrow-left"></i>
        Back to ILLUME
      </a>

      <!-- Demo credentials hint (dev only) -->
      <?php if (defined('APP_ENV') && APP_ENV === 'development'): ?>
      <div style="margin-top:2rem; padding:1rem; background:var(--space-mid); border:1px solid var(--space-border); border-radius:var(--r); font-size:0.75rem; color:var(--text-muted);">
        <div style="margin-bottom:0.5rem; color:var(--gold); font-weight:600;">Dev Credentials</div>
        <div>Founder: <code>founder@illume.ng</code> / <code>password</code></div>
        <div>Staff: <code>adaeze@illume.ng</code> / <code>password</code></div>
        <div>Client: <code>chioma@example.com</code> / <code>password</code></div>
      </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
<script>
  // Password toggle
  const toggle = document.getElementById('pw-toggle');
  const pwField = document.getElementById('password');
  toggle?.addEventListener('click', () => {
    const isText = pwField.type === 'text';
    pwField.type = isText ? 'password' : 'text';
    toggle.innerHTML = isText
      ? '<i data-lucide="eye"></i>'
      : '<i data-lucide="eye-off"></i>';
    if (typeof lucide !== 'undefined') lucide.createIcons();
  });
</script>
</body>
</html>
