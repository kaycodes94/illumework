<?php
// ============================================================
// ILLUME — Public Header
// Variables expected: $page_title, $page_desc (optional)
// ============================================================
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$page_title = isset($page_title) ? $page_title . ' — ILLUME' : 'ILLUME | Fashion. Elevated.';
$page_desc  = $page_desc  ?? 'ILLUME is a high-end Nigerian fashion production and consulting brand offering bespoke couture, bridal design, and editorial styling.';
$page_url   = SITE_URL . $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= e($page_desc) ?>">
  <meta name="theme-color" content="#201710">
  <meta property="og:title"       content="<?= e($page_title) ?>">
  <meta property="og:description" content="<?= e($page_desc) ?>">
  <meta property="og:url"         content="<?= e($page_url) ?>">
  <meta property="og:type"        content="website">
  <title><?= e($page_title) ?></title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Styles -->
  <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/main.css">
  <?php if (isset($extra_css)) echo $extra_css; ?>

  <!-- Lenis smooth scroll -->
  <script src="https://cdn.jsdelivr.net/npm/@studio-freight/lenis@1.0.42/dist/lenis.min.js" defer></script>
</head>
<body>

<!-- Page Loader -->
<div id="page-loader" class="page-loader">
  <div class="loader-logo" style="display: flex; align-items: center; gap: 1.5rem;">
    <img src="<?= SITE_URL ?>/assets/img/logo.png" alt="Logo" class="logo-img" style="height: 80px; width: auto;">
    <span style="font-weight: 800; font-size: 3rem; letter-spacing: -0.02em; color: var(--black);">ILLUME</span>
  </div>
  <div class="loader-bar"><div class="loader-bar-fill"></div></div>
</div>

<!-- Custom Cursor -->
<div class="cursor-dot"></div>
<div class="cursor-ring"></div>

<!-- Scroll Progress -->
<div class="scroll-progress"></div>

<nav class="nav" id="main-nav">
  <div class="nav__inner">

    <!-- Logo -->
    <a href="<?= SITE_URL ?>/" class="nav__logo" style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none;">
      <img src="<?= SITE_URL ?>/assets/img/logo.png" alt="Logo" class="logo-img" style="height: 42px; width: auto;">
      <span style="font-weight: 700; font-size: 1.25rem; letter-spacing: -0.02em; color: var(--black);">ILLUME</span>
    </a>

    <!-- Desktop Links -->
    <div class="nav__links">
      <a href="<?= SITE_URL ?>/"               class="nav__link <?= is_active('index') ?>">Home</a>
      <a href="<?= SITE_URL ?>/about.php"      class="nav__link <?= is_active('about') ?>">About</a>
      <a href="<?= SITE_URL ?>/services.php"   class="nav__link <?= is_active('services') ?>">Services</a>
      <a href="<?= SITE_URL ?>/portfolio.php"  class="nav__link <?= is_active('portfolio') ?>">Portfolio</a>
      <a href="<?= SITE_URL ?>/contact.php"    class="nav__link <?= is_active('contact') ?>">Contact</a>
    </div>

    <!-- Actions -->
    <div class="nav__actions">
      <?php if (is_logged_in()): ?>
        <?php $r = current_role();
          $dash = match($r) { 'founder'=>'/founder/dashboard.php','staff'=>'/staff/dashboard.php',default=>'/client/dashboard.php' }; ?>
        <a href="<?= SITE_URL . $dash ?>" class="btn btn--secondary btn--sm">Dashboard</a>
        <a href="<?= SITE_URL ?>/auth/logout.php" class="btn btn--ghost btn--sm" style="opacity: 0.6; font-size: 0.75rem;">Sign Out</a>
      <?php else: ?>
        <a href="<?= SITE_URL ?>/auth/login.php" class="btn btn--ghost btn--sm">Sign In</a>
      <?php endif; ?>
      <a href="<?= SITE_URL ?>/consultation.php" class="btn btn--primary btn--sm">Consultation</a>
      <!-- Hamburger -->
      <button class="nav__hamburger" aria-label="Open menu">
        <span></span><span></span><span></span>
      </button>
    </div>

  </div>
</nav>

<!-- Mobile Nav -->
<div class="nav__mobile">
  <button class="mobile-close btn btn--icon btn--ghost" aria-label="Close menu">
    <i data-lucide="x"></i>
  </button>
  <a href="<?= SITE_URL ?>/"               class="nav__link">Home</a>
  <a href="<?= SITE_URL ?>/about.php"      class="nav__link">About</a>
  <a href="<?= SITE_URL ?>/services.php"   class="nav__link">Services</a>
  <a href="<?= SITE_URL ?>/portfolio.php"  class="nav__link">Portfolio</a>
  <a href="<?= SITE_URL ?>/contact.php"    class="nav__link">Contact</a>
  <div style="display:flex;gap:1rem;flex-wrap:wrap;justify-content:center;margin-top:1rem;">
    <?php if (is_logged_in()): ?>
      <a href="<?= SITE_URL . $dash ?>" class="btn btn--secondary">Dashboard</a>
      <a href="<?= SITE_URL ?>/auth/logout.php" class="btn btn--ghost">Sign Out</a>
    <?php else: ?>
      <a href="<?= SITE_URL ?>/auth/login.php" class="btn btn--ghost">Sign In</a>
    <?php endif; ?>
    <a href="<?= SITE_URL ?>/consultation.php" class="btn btn--primary">Book Consultation</a>
  </div>
</div>
<!-- end header php -->
