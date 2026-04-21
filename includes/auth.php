<?php
// ============================================================
// ILLUME — Authentication & Session Helpers
// ============================================================

require_once __DIR__ . '/../config/config.php';

// Start session if not already started
function session_start_once(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'secure'   => false, // true in production (HTTPS)
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

session_start_once();

// ─── Login ───────────────────────────────────────────────────
function login_user(array $user): void {
    session_regenerate_id(true);
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email']= $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['logged_in'] = true;
}

// ─── Logout ──────────────────────────────────────────────────
function logout_user(): void {
    $_SESSION = [];
    session_destroy();
    session_start_once();
}

// ─── Check if logged in ──────────────────────────────────────
function is_logged_in(): bool {
    return !empty($_SESSION['logged_in']) && !empty($_SESSION['user_id']);
}

// ─── Get current user ────────────────────────────────────────
function current_user(): ?array {
    if (!is_logged_in()) return null;
    return [
        'id'    => $_SESSION['user_id'],
        'name'  => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role'  => $_SESSION['user_role'],
    ];
}

function current_user_id(): ?int {
    return is_logged_in() ? (int)$_SESSION['user_id'] : null;
}

function current_role(): ?string {
    return is_logged_in() ? $_SESSION['user_role'] : null;
}

// ─── Role checks ─────────────────────────────────────────────
function is_founder(): bool { return current_role() === 'founder'; }
function is_staff(): bool   { return current_role() === 'staff'; }
function is_client(): bool  { return current_role() === 'client'; }

// ─── Role guards ─────────────────────────────────────────────
function require_login(string $redirect = '/auth/login.php'): void {
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . SITE_URL . $redirect);
        exit;
    }
}

function require_role(string|array $roles, string $redirect = '/'): void {
    require_login();
    $allowed = is_array($roles) ? $roles : [$roles];
    if (!in_array(current_role(), $allowed, true)) {
        header('Location: ' . SITE_URL . $redirect);
        exit;
    }
}

function require_founder(): void { require_role('founder', '/auth/login.php'); }
function require_staff_or_founder(): void { require_role(['staff', 'founder'], '/auth/login.php'); }
function require_client(): void { require_role('client', '/auth/login.php'); }

// ─── Redirect after login by role ────────────────────────────
function redirect_by_role(): void {
    $role = current_role();
    $dest = match($role) {
        'founder' => SITE_URL . '/founder/dashboard.php',
        'staff'   => SITE_URL . '/staff/dashboard.php',
        'client'  => SITE_URL . '/client/dashboard.php',
        default   => SITE_URL . '/',
    };
    header('Location: ' . $dest);
    exit;
}

// ─── CSRF ─────────────────────────────────────────────────────
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

function verify_csrf(): bool {
    $token = $_POST['csrf_token'] ?? '';
    return hash_equals(csrf_token(), $token);
}
