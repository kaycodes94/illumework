<?php
// ============================================================
// ILLUME — Shared Dashboard Header/Sidebar
// Required vars: $dash_title (string), $active_nav (string)
// ============================================================
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
require_login();

$user   = current_user();
$role   = current_role();
$initials = implode('', array_map(fn($w) => strtoupper($w[0]), array_slice(explode(' ', $user['name']), 0, 2)));

// Nav items per role
$nav = [];
if ($role === 'founder') {
    $nav = [
        'overview' => [
            ['href'=>'/founder/dashboard.php',      'icon'=>'layout-dashboard', 'label'=>'Dashboard'],
        ],
        'management' => [
            ['href'=>'/founder/consultations.php',  'icon'=>'calendar',         'label'=>'Consultations', 'badge_key'=>'new_consults'],
            ['href'=>'/founder/orders.php',         'icon'=>'package',          'label'=>'Orders'],
            ['href'=>'/founder/clients.php',        'icon'=>'users',            'label'=>'Clients'],
            ['href'=>'/founder/staff.php',          'icon'=>'user-check',       'label'=>'Staff'],
        ],
        'insights' => [
            ['href'=>'/founder/analytics.php',      'icon'=>'bar-chart-2',      'label'=>'Analytics'],
        ],
    ];
} elseif ($role === 'staff') {
    $nav = [
        'overview' => [
            ['href'=>'/staff/dashboard.php',        'icon'=>'layout-dashboard', 'label'=>'Dashboard'],
        ],
        'work' => [
            ['href'=>'/staff/orders.php',           'icon'=>'package',          'label'=>'My Orders'],
            ['href'=>'/staff/upload-design.php',    'icon'=>'upload',           'label'=>'Upload Design'],
        ],
    ];
} else {
    $nav = [
        'overview' => [
            ['href'=>'/client/dashboard.php',       'icon'=>'layout-dashboard', 'label'=>'My Dashboard'],
        ],
        'my account' => [
            ['href'=>'/client/orders.php',          'icon'=>'package',          'label'=>'My Orders'],
            ['href'=>'/client/profile.php',         'icon'=>'user',             'label'=>'Profile'],
        ],
    ];
}

$dash_title = $dash_title ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($dash_title) ?> — ILLUME</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/main.css">
  <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/dashboard.css">
</head>
<body style="cursor:auto;">

<!-- Custom Cursor -->
<div class="cursor-dot"></div>
<div class="cursor-ring"></div>

<div class="dash-layout">

  <!-- ══ SIDEBAR ══════════════════════════════════════════════ -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar__header" style="padding: 32px 24px;">
      <a href="<?= SITE_URL ?>/" class="sidebar__logo" style="position: relative; display: flex; align-items: center; justify-content: center; text-decoration: none; color: var(--black); width: 100%; height: 50px; margin-bottom: 0.5rem;">
        <span style="font-weight: 800; font-size: 1.5rem; letter-spacing: -0.02em; position: relative; z-index: 2;">ILLUME</span>
        <img src="<?= SITE_URL ?>/assets/img/logo.png" alt="Logo" class="logo-img" style="height: 48px; position: absolute; opacity: 0.15; z-index: 1;">
      </a>
      <div class="sidebar__role-badge" style="text-align: center; font-size: 0.65rem; letter-spacing: 0.1em; text-transform: uppercase; color: var(--warm-taupe);"><?= ucfirst(e($role)) ?> Portal</div>
    </div>

    <nav class="sidebar__nav">
      <?php foreach ($nav as $section => $links): ?>
        <div class="sidebar__section-label"><?= ucfirst($section) ?></div>
        <?php foreach ($links as $link): ?>
        <a
          href="<?= SITE_URL . e($link['href']) ?>"
          class="sidebar__link <?= ($active_nav ?? '') === basename($link['href'],'.php') ? 'active' : '' ?>"
        >
          <i data-lucide="<?= e($link['icon']) ?>"></i>
          <span><?= e($link['label']) ?></span>
          <?php if (!empty($link['badge_key']) && !empty($badges[$link['badge_key']])): ?>
          <span class="sidebar__badge"><?= (int)$badges[$link['badge_key']] ?></span>
          <?php endif; ?>
        </a>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </nav>

    <div class="sidebar__footer">
      <div class="sidebar__user">
        <div class="sidebar__avatar"><?= e($initials) ?></div>
        <div style="min-width:0;">
          <div class="sidebar__user-name"><?= e($user['name']) ?></div>
          <div class="sidebar__user-email"><?= e($user['email']) ?></div>
        </div>
      </div>
      <a href="<?= SITE_URL ?>/auth/logout.php" class="btn btn--ghost btn--sm btn--w-full">
        <i data-lucide="log-out"></i> Sign Out
      </a>
    </div>
  </aside>

  <!-- Mobile overlay -->
  <div class="sidebar-overlay" id="sidebar-overlay"></div>

  <!-- ══ MAIN ═════════════════════════════════════════════════ -->
  <main class="dash-main">

    <!-- Topbar -->
    <header class="dash-topbar">
      <div style="display:flex;align-items:center;gap:1rem;">
        <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Toggle menu">
          <i data-lucide="menu"></i>
        </button>
        <h1 class="dash-topbar__title"><?= e($dash_title) ?></h1>
      </div>
      <div class="dash-topbar__actions">
        <a href="<?= SITE_URL ?>/" class="topbar-icon-btn" title="Public Site" style="text-decoration:none;">
          <i data-lucide="external-link"></i>
        </a>
        <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>" class="topbar-icon-btn" title="WhatsApp" target="_blank" rel="noopener" style="text-decoration:none;">
          <i data-lucide="message-circle"></i>
        </a>
        <div class="sidebar__avatar" style="width:36px;height:36px;font-size:0.8rem;"><?= e($initials) ?></div>
      </div>
    </header>

    <!-- Content wrapper opens here — closed in dash_footer.php -->
    <div class="dash-content">
