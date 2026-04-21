<?php
// ============================================================
// ILLUME Luxury Platform — Configuration
// ============================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'illume_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('SITE_NAME', 'ILLUME');
define('SITE_TAGLINE', 'Fashion. Elevated.');
define('SITE_URL', 'http://localhost/illumetest');
define('SITE_EMAIL', 'hello@illume.ng');
define('SITE_PHONE', '+234 800 000 0000');
define('WHATSAPP_NUMBER', '2348000000000'); // without +

define('UPLOAD_DIR', __DIR__ . '/../assets/uploads/');
define('UPLOAD_URL', SITE_URL . '/assets/uploads/');
define('MAX_UPLOAD_MB', 10);

define('SESSION_NAME', 'illume_session');
define('SESSION_LIFETIME', 86400 * 7); // 7 days

// Environment
define('APP_ENV', 'development'); // 'production' in prod
define('DEBUG', true);

// Security
define('STAFF_INVITE_CODE', 'ILLUME_PRO_2026');

// Error reporting
if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
