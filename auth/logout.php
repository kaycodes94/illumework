<?php
// ============================================================
// ILLUME — Logout Handler
// ============================================================
require_once __DIR__ . '/../includes/auth.php';

logout_user();

// Redirect back home
header('Location: ' . SITE_URL . '/');
exit;
