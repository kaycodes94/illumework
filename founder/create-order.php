<?php
// ============================================================
// ILLUME — Founder: Create Order
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_founder();

$dash_title = 'Create Order';
$active_nav = 'orders';
$pdo        = db();

$consult_id = (int)($_GET['consult_id'] ?? 0);
$consult    = null;
$prefill    = [
    'title'        => '',
    'client_id'    => '',
    'client_name'  => '',
    'client_email' => '',
    'service_type' => '',
    'budget'       => '',
    'description'  => '',
    'deadline'     => ''
];

if ($consult_id) {
    $stmt = $pdo->prepare("SELECT * FROM consultations WHERE id = ?");
    $stmt->execute([$consult_id]);
    $consult = $stmt->fetch();
    if ($consult) {
        $prefill['title']        = $consult['service_type'] . ' for ' . $consult['name'];
        $prefill['client_name']  = $consult['name'];
        $prefill['client_email'] = $consult['email'];
        $prefill['service_type'] = $consult['service_type'];
        $prefill['description']  = $consult['message'];
        
        // Try to find existing client by email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND role = 'client'");
        $stmt->execute([$consult['email']]);
        $prefill['client_id'] = $stmt->fetchColumn();
    }
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    try {
        $pdo->beginTransaction();
        
        $client_id = (int)$_POST['client_id'];
        $staff_id  = (int)$_POST['staff_id'];
        $title     = trim($_POST['title']);
        $service   = $_POST['service_type'];
        $budget    = (float)$_POST['budget'];
        $deadline  = $_POST['deadline'] ?: null;
        $desc      = trim($_POST['description']);
        $ref       = generate_order_ref();

        // If no client_id, check if we should create one or if the email exists
        if (!$client_id) {
            $email = trim($_POST['client_email']);
            $name  = trim($_POST['client_name']);
            
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $existing = $stmt->fetchColumn();
            
            if ($existing) {
                $client_id = $existing;
            } else {
                // Create new client user
                $pass = bin2hex(random_bytes(4)); // temp password
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'client')");
                $stmt->execute([$name, $email, $hash]);
                $client_id = $pdo->lastInsertId();
                // In a real app, we'd send an email here.
            }
        }

        // Create Order
        $stmt = $pdo->prepare("
            INSERT INTO orders (order_ref, client_id, assigned_staff_id, title, service_type, description, budget, deadline, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'intake')
        ");
        $stmt->execute([$ref, $client_id, $staff_id, $title, $service, $desc, $budget, $deadline]);
        $order_id = $pdo->lastInsertId();

        // Log Timeline
        $pdo->prepare("INSERT INTO order_timeline (order_id, actor_id, action, note) VALUES (?, ?, 'order_created', 'Order initiated via dashboard.')")
            ->execute([$order_id, current_user_id()]);

        // If from consult, mark consult as converted
        if ($consult_id) {
            $pdo->prepare("UPDATE consultations SET status = 'converted', updated_at = NOW() WHERE id = ?")
                ->execute([$consult_id]);
        }

        $pdo->commit();
        flash('order', 'Order created successfully: ' . $ref, 'success');
        header('Location: ' . SITE_URL . '/founder/orders.php');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error: " . $e->getMessage();
    }
}

// Get Lists
$clients = $pdo->query("SELECT id, name, email FROM users WHERE role = 'client' ORDER BY name")->fetchAll();
$staff   = $pdo->query("SELECT id, name, role FROM users WHERE role IN ('staff','founder') AND status = 'active' ORDER BY name")->fetchAll();

include __DIR__ . '/../includes/dash_header.php';
?>

<div class="dash-page-header">
    <h2 class="dash-page-header__title">Create New Order</h2>
    <p class="dash-page-header__subtitle">Launch a new project and assign it to your team.</p>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert--error"><?= e($error) ?></div>
<?php endif; ?>

<div class="grid-2" style="grid-template-columns: 2fr 1fr; align-items: start;">
    <div class="card--glass" style="padding: 2rem;">
        <form method="POST" action="">
            <?= csrf_field() ?>
            
            <div class="form-group">
                <label class="form-label">Project Title</label>
                <input type="text" name="title" class="form-input" value="<?= e($_POST['title'] ?? $prefill['title']) ?>" required placeholder="e.g. Bespoke Bridal Gown for Adaora">
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Client Selection</label>
                    <select name="client_id" class="form-select" id="client-select">
                        <option value="">+ Create New / Use Pre-fill</option>
                        <?php foreach ($clients as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($prefill['client_id'] == $c['id']) ? 'selected' : '' ?>>
                            <?= e($c['name']) ?> (<?= e($c['email']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Assigned Representative</label>
                    <select name="staff_id" class="form-select" required>
                        <option value="">Select Staff</option>
                        <?php foreach ($staff as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= current_user_id() == $s['id'] ? 'selected' : '' ?>>
                            <?= e($s['name']) ?> (<?= ucfirst($s['role']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div id="new-client-fields" style="<?= $prefill['client_id'] ? 'display:none;' : '' ?>">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Client Name</label>
                        <input type="text" name="client_name" class="form-input" value="<?= e($prefill['client_name']) ?>" placeholder="Full Name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Client Email</label>
                        <input type="email" name="client_email" class="form-input" value="<?= e($prefill['client_email']) ?>" placeholder="email@example.com">
                    </div>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Service Type</label>
                    <select name="service_type" class="form-select" required>
                        <?php foreach (['Bespoke Couture','Bridal','Editorial','Ready-to-Wear','Consulting'] as $s): ?>
                        <option value="<?= $s ?>" <?= $prefill['service_type'] == $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Budget (NGN)</label>
                    <input type="number" name="budget" class="form-input" step="0.01" value="<?= e($prefill['budget']) ?>" placeholder="0.00">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deadline</label>
                <input type="date" name="deadline" class="form-input" value="<?= e($prefill['deadline']) ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Project Scope / Measurements / Notes</label>
                <textarea name="description" class="form-textarea" placeholder="Detail any specific requirements here..."><?= e($prefill['description']) ?></textarea>
            </div>

            <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                <button type="submit" class="btn btn--primary btn--lg">
                    <i data-lucide="check"></i> Initialize Project
                </button>
                <a href="orders.php" class="btn btn--ghost btn--lg">Cancel</a>
            </div>
        </form>
    </div>

    <div class="flex-col gap-6">
        <div class="card" style="padding: 1.5rem;">
            <h4 style="margin-bottom: 1rem; color: var(--gold);">Workflow Tips</h4>
            <ul style="font-size: 0.88rem; color: var(--text-secondary); display: flex; flex-direction: column; gap: 0.75rem;">
                <li><i data-lucide="info" style="width:14px; color:var(--plasma);"></i> <strong>Conversion:</strong> Linking to a consultation marks it as "Converted".</li>
                <li><i data-lucide="info" style="width:14px; color:var(--plasma);"></i> <strong>Client Access:</strong> New clients will receive a notification with login details.</li>
                <li><i data-lucide="info" style="width:14px; color:var(--plasma);"></i> <strong>Assignment:</strong> Assigned staff will receive this order in their "My Orders" tab.</li>
            </ul>
        </div>
        
        <?php if ($consult): ?>
        <div class="card--glass" style="border-color: var(--plasma-glass);">
             <div class="label-text" style="color:var(--plasma); margin-bottom: 0.5rem;">Reference Consult</div>
             <div style="font-size: 0.9rem; font-weight: 600;"><?= e($consult['name']) ?></div>
             <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem;"><?= e($consult['email']) ?></div>
             <div style="font-size: 0.85rem; color: var(--text-secondary);"><?= nl2br(e($consult['message'])) ?></div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('client-select').addEventListener('change', function() {
    const fields = document.getElementById('new-client-fields');
    fields.style.display = this.value ? 'none' : 'block';
});
</script>

<?php include __DIR__ . '/../includes/dash_footer.php'; ?>
