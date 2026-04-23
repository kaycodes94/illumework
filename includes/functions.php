<?php
// ============================================================
// ILLUME — Shared Utility Functions
// ============================================================

require_once __DIR__ . '/db.php';

// ─── Output Escaping ─────────────────────────────────────────
function e(mixed $val): string {
    return htmlspecialchars((string)($val ?? ''), ENT_QUOTES, 'UTF-8');
}

// ─── Flash Messages ──────────────────────────────────────────
function flash(string $key, string $message, string $type = 'info'): void {
    $_SESSION['flash'][$key] = ['message' => $message, 'type' => $type];
}

function get_flash(string $key): ?array {
    if (!empty($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

function render_flash(string $key): void {
    $f = get_flash($key);
    if ($f) {
        $icon = match($f['type']) {
            'success' => 'check-circle',
            'error'   => 'alert-circle',
            'warning' => 'alert-triangle',
            default   => 'info',
        };
        echo "<div class=\"alert alert--{$f['type']}\" role=\"alert\">
            <i data-lucide=\"{$icon}\"></i>
            <span>" . e($f['message']) . "</span>
        </div>";
    }
}

// ─── Order Reference Generator ───────────────────────────────
function generate_order_ref(): string {
    return 'ILL-' . strtoupper(substr(uniqid(), -6)) . '-' . date('Y');
}

// ─── Currency Formatting ─────────────────────────────────────
function format_currency(float $amount, string $currency = 'NGN'): string {
    $symbol = match($currency) {
        'NGN' => '₦',
        'USD' => '$',
        'GBP' => '£',
        'EUR' => '€',
        default => $currency . ' ',
    };
    return $symbol . number_format($amount, 2);
}

// ─── Date Formatting ─────────────────────────────────────────
function format_date(string $date, string $format = 'd M Y'): string {
    return (new DateTime($date))->format($format);
}

function time_ago(string $datetime): string {
    $now  = new DateTime();
    $then = new DateTime($datetime);
    $diff = $now->diff($then);
    if ($diff->y)  return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m)  return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d)  return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h)  return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i)  return $diff->i . ' min' . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'Just now';
}

// ─── Status Badges ───────────────────────────────────────────
function order_status_badge(string $status): string {
    $map = [
        'intake'      => ['label' => 'Intake',      'class' => 'badge--gold'],
        'design'      => ['label' => 'Design',      'class' => 'badge--aura'],
        'approval'    => ['label' => 'Awaiting Approval', 'class' => 'badge--warning'],
        'production'  => ['label' => 'Production',  'class' => 'badge--info'],
        'delivery'    => ['label' => 'Delivery',    'class' => 'badge--info'],
        'complete'    => ['label' => 'Complete',    'class' => 'badge--success'],
        'cancelled'   => ['label' => 'Cancelled',   'class' => 'badge--error'],
    ];
    $d = $map[$status] ?? ['label' => ucfirst($status), 'class' => 'badge--default'];
    return "<span class=\"badge {$d['class']}\">{$d['label']}</span>";
}

function consultation_status_badge(string $status): string {
    $map = [
        'new'       => ['label' => 'New',       'class' => 'badge--aura'],
        'contacted' => ['label' => 'Contacted', 'class' => 'badge--gold'],
        'converted' => ['label' => 'Converted', 'class' => 'badge--success'],
        'declined'  => ['label' => 'Declined',  'class' => 'badge--error'],
    ];
    $d = $map[$status] ?? ['label' => ucfirst($status), 'class' => 'badge--default'];
    return "<span class=\"badge {$d['class']}\">{$d['label']}</span>";
}

// ─── Pagination ──────────────────────────────────────────────
function paginate(int $total, int $per_page, int $current_page): array {
    $total_pages = (int)ceil($total / $per_page);
    $offset      = ($current_page - 1) * $per_page;
    return [
        'total'       => $total,
        'per_page'    => $per_page,
        'current'     => $current_page,
        'total_pages' => $total_pages,
        'offset'      => $offset,
        'has_prev'    => $current_page > 1,
        'has_next'    => $current_page < $total_pages,
    ];
}

// ─── File Upload ─────────────────────────────────────────────
function upload_file(array $file, string $subfolder = 'designs'): array {
    $allowed = ['image/jpeg','image/png','image/webp','image/gif','application/pdf'];
    if (!in_array($file['type'], $allowed)) {
        return ['success' => false, 'error' => 'File type not allowed.'];
    }
    $max_bytes = MAX_UPLOAD_MB * 1024 * 1024;
    if ($file['size'] > $max_bytes) {
        return ['success' => false, 'error' => 'File exceeds ' . MAX_UPLOAD_MB . 'MB limit.'];
    }
    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('illume_', true) . '.' . $ext;
    $dir      = UPLOAD_DIR . $subfolder . '/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $dest = $dir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return ['success' => false, 'error' => 'Failed to save file.'];
    }
    return [
        'success'   => true,
        'path'      => $subfolder . '/' . $filename,
        'url'       => UPLOAD_URL . $subfolder . '/' . $filename,
        'file_name' => $file['name'],
    ];
}

// ─── WhatsApp Link ───────────────────────────────────────────
function whatsapp_link(string $number, string $message = ''): string {
    $number  = preg_replace('/[^0-9]/', '', $number);
    $encoded = urlencode($message);
    return "https://wa.me/{$number}?text={$encoded}";
}

// ─── Active Nav Helper ───────────────────────────────────────
function is_active(string $page): string {
    $current = basename($_SERVER['PHP_SELF'], '.php');
    $target  = basename($page, '.php');
    return $current === $target ? 'active' : '';
}

// ─── Get all services ────────────────────────────────────────
function get_services(): array {
    try {
        $stmt = db()->query("SELECT * FROM services WHERE active=1 ORDER BY display_order ASC");
        return $stmt->fetchAll();
    } catch (PDOException) { return []; }
}

// ─── JSON response helper (for API endpoints) ────────────────
function json_response(mixed $data, int $code = 200): never {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// ─── Get Order Timeline ──────────────────────────────────────
function get_order_timeline(int $order_id): array {
    try {
        $stmt = db()->prepare("
            SELECT ot.*, u.name AS actor_name
            FROM order_timeline ot
            LEFT JOIN users u ON ot.actor_id = u.id
            WHERE ot.order_id = ?
            ORDER BY ot.created_at DESC
        ");
        $stmt->execute([$order_id]);
        return $stmt->fetchAll();
    } catch (PDOException) { return []; }
}

// ─── Get Design Submissions ──────────────────────────────────
function get_design_submissions(int $order_id): array {
    try {
        $stmt = db()->prepare("
            SELECT ds.*, u.name AS uploaded_by_name
            FROM design_submissions ds
            JOIN users u ON ds.uploaded_by = u.id
            WHERE ds.order_id = ?
            ORDER BY ds.created_at DESC
        ");
        $stmt->execute([$order_id]);
        return $stmt->fetchAll();
    } catch (PDOException) { return []; }
}
