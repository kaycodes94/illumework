<?php
// ============================================================
// ILLUME — API: Design Approval / Revision
// POST: submission_id, action (approve|revision|reject), csrf_token
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . SITE_URL . '/');
    exit;
}
if (!verify_csrf()) {
    flash('error', 'Security check failed.', 'error');
    header('Location: ' . SITE_URL . '/client/dashboard.php');
    exit;
}

$sid    = (int)($_POST['submission_id'] ?? 0);
$action = $_POST['action'] ?? '';
$uid    = current_user_id();

if (!$sid || !in_array($action, ['approve','revision','reject'])) {
    header('Location: ' . SITE_URL . '/client/dashboard.php');
    exit;
}

$pdo = db();

// Fetch submission with order info
$stmt = $pdo->prepare("
    SELECT ds.*, o.client_id, o.id AS order_id
    FROM design_submissions ds
    JOIN orders o ON ds.order_id = o.id
    WHERE ds.id = ? LIMIT 1
");
$stmt->execute([$sid]);
$sub = $stmt->fetch();

if (!$sub) {
    flash('error','Submission not found.','error');
    header('Location: ' . SITE_URL . '/client/dashboard.php'); exit;
}

// Clients can only approve/revise their own order submissions
// Staff/founders can approve any
if (is_client() && $sub['client_id'] != $uid) {
    flash('error','Access denied.','error');
    header('Location: ' . SITE_URL . '/client/dashboard.php'); exit;
}

$approval_status = match($action) {
    'approve'  => 'approved',
    'revision' => 'revision_requested',
    'reject'   => 'rejected',
    default    => 'pending',
};

$feedback = trim($_POST['feedback'] ?? '');

$pdo->prepare("
    UPDATE design_submissions
    SET approval_status=?, client_feedback=?, reviewed_at=NOW()
    WHERE id=?
")->execute([$approval_status, $feedback, $sid]);

// Log on timeline
$action_label = match($action) {
    'approve'  => 'design_approved',
    'revision' => 'revision_requested',
    default    => 'design_rejected',
};
$pdo->prepare("INSERT INTO order_timeline (order_id, actor_id, action, note) VALUES (?,?,?,?)")
    ->execute([$sub['order_id'], $uid, $action_label, $feedback ?: "Design #{$sid} {$approval_status}."]);

// If approved → move order to production
if ($action === 'approve') {
    $pdo->prepare("UPDATE orders SET status='production', updated_at=NOW() WHERE id=? AND status='approval'")
        ->execute([$sub['order_id']]);
}

$msg = match($action) {
    'approve'  => 'Design approved! Your order has moved to production.',
    'revision' => 'Revision request sent. Our team will update you shortly.',
    default    => 'Submission rejected. Our team will follow up.',
};

flash('approval', $msg, $action === 'approve' ? 'success' : 'warning');

$redirect = is_client()
    ? SITE_URL . '/client/dashboard.php'
    : SITE_URL . '/founder/orders.php';

header('Location: ' . $redirect);
exit;
