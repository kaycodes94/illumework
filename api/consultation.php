<?php
// ============================================================
// ILLUME — API: Consultation Submission
// POST: name, email, phone, whatsapp, service_type, occasion,
//       budget_range, timeline, message, csrf_token
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Method not allowed'], 405);
}
if (!verify_csrf()) {
    json_response(['error' => 'Invalid CSRF token'], 403);
}

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$whatsapp = trim($_POST['whatsapp'] ?? '');
$service  = trim($_POST['service_type'] ?? '');
$occasion = trim($_POST['occasion'] ?? '');
$budget   = trim($_POST['budget_range'] ?? '');
$timeline = trim($_POST['timeline'] ?? '');
$message  = trim($_POST['message'] ?? '');

// Validate
$errors = [];
if (!$name)                                  $errors[] = 'Name is required.';
if (!$email)                                 $errors[] = 'Email is required.';
elseif (!filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';
if (!$service)                               $errors[] = 'Please select a service.';

if ($errors) {
    json_response(['success'=>false,'errors'=>$errors], 422);
}

try {
    $stmt = db()->prepare("
        INSERT INTO consultations
            (name, email, phone, whatsapp, service_type, occasion, budget_range, timeline, message, status)
        VALUES (?,?,?,?,?,?,?,?,?,'new')
    ");
    $stmt->execute([
        $name, $email,
        $phone,
        $whatsapp ?: $phone,
        $service, $occasion, $budget, $timeline, $message,
    ]);

    $wa_msg = urlencode(
        "Hello ILLUME! 🌟\n\n" .
        "Consultation submitted:\nName: {$name}\nService: {$service}\nBudget: {$budget}\nTimeline: {$timeline}"
    );

    json_response([
        'success'      => true,
        'message'      => 'Consultation request received! We\'ll be in touch within 24 hours.',
        'whatsapp_url' => "https://wa.me/" . WHATSAPP_NUMBER . "?text={$wa_msg}",
    ]);

} catch (PDOException $e) {
    json_response(['success'=>false,'error'=>'Database error. Please try again.'], 500);
}
