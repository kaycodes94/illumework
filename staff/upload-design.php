<?php
// ============================================================
// ILLUME — Staff: Upload Design Submission
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_staff_or_founder();

$dash_title = 'Upload Design';
$active_nav = 'upload-design';
$pdo        = db();
$uid        = current_user_id();

// Get orders assigned to this staff (or all if founder)
if (is_founder()) {
    $orders_list = $pdo->query("
        SELECT o.id, o.order_ref, o.title, o.status, u.name AS client_name
        FROM orders o JOIN users u ON o.client_id=u.id
        WHERE o.status NOT IN ('complete','cancelled')
        ORDER BY o.created_at DESC
    ")->fetchAll();
} else {
    $stmt = $pdo->prepare("
        SELECT o.id, o.order_ref, o.title, o.status, u.name AS client_name
        FROM orders o JOIN users u ON o.client_id=u.id
        WHERE o.assigned_staff_id=? AND o.status NOT IN ('complete','cancelled')
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$uid]);
    $orders_list = $stmt->fetchAll();
}

$preselect_order = (int)($_GET['order'] ?? 0);
$success = false;
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $order_id = (int)($_POST['order_id'] ?? 0);
    $title    = trim($_POST['title'] ?? '');
    $notes    = trim($_POST['notes'] ?? '');

    if (!$order_id) { $error = 'Please select an order.'; }
    elseif (empty($_FILES['design_file']['name'])) { $error = 'Please upload a design file.'; }
    else {
        $upload = upload_file($_FILES['design_file'], 'designs');
        if (!$upload['success']) {
            $error = $upload['error'];
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO design_submissions (order_id, uploaded_by, file_path, file_name, title, notes, approval_status)
                VALUES (?,?,?,?,?,?,'pending')
            ");
            $stmt->execute([$order_id, $uid, $upload['path'], $upload['file_name'], $title ?: 'Design Submission', $notes]);

            // Update order status to approval if in design
            $pdo->prepare("UPDATE orders SET status='approval', updated_at=NOW() WHERE id=? AND status='design'")->execute([$order_id]);
            // Timeline
            $pdo->prepare("INSERT INTO order_timeline (order_id, actor_id, action, note) VALUES (?,?,'design_upload','Design submitted for client approval')")->execute([$order_id, $uid]);

            $success = true;
            flash('upload', 'Design uploaded successfully! Client has been notified for approval.', 'success');
            header('Location: ' . SITE_URL . '/staff/upload-design.php');
            exit;
        }
    }
}

include __DIR__ . '/../includes/dash_header.php';
render_flash('upload');
?>

<div class="dash-page-header">
  <div>
    <h2 class="dash-page-header__title">Upload Design</h2>
    <p class="dash-page-header__subtitle">Submit designs for client approval</p>
  </div>
</div>

<div style="max-width:640px;">
  <?php if ($error): ?>
  <div class="alert alert--error" style="margin-bottom:1.5rem;">
    <i data-lucide="alert-circle"></i> <span><?= e($error) ?></span>
  </div>
  <?php endif; ?>

  <div class="glass" style="padding:2.5rem;border-radius:var(--r-2xl);">
    <form method="POST" action="" enctype="multipart/form-data" novalidate>
      <?= csrf_field() ?>

      <div class="form-group">
        <label class="form-label" for="order_id">Select Order *</label>
        <select name="order_id" id="order_id" class="form-select" required>
          <option value="">Choose an order…</option>
          <?php foreach ($orders_list as $o): ?>
          <option value="<?= (int)$o['id'] ?>" <?= $preselect_order===$o['id']?'selected':'' ?>>
            [<?= e($o['order_ref']) ?>] <?= e($o['title']) ?> — <?= e($o['client_name']) ?>
          </option>
          <?php endforeach; ?>
        </select>
        <?php if (empty($orders_list)): ?>
        <p class="form-error">No active orders assigned to you.</p>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label class="form-label" for="title">Submission Title</label>
        <input type="text" name="title" id="title" class="form-input" placeholder="e.g. Initial Sketch, Version 2, Final Design…">
      </div>

      <div class="form-group">
        <label class="form-label" for="design_file">Design File * <span style="color:var(--text-muted);font-weight:400;">(JPG, PNG, PDF — max <?= MAX_UPLOAD_MB ?>MB)</span></label>
        <div id="drop-zone" style="
          border:1.5px dashed var(--space-border);
          border-radius:var(--r-lg);padding:3rem 2rem;
          text-align:center;cursor:none;
          transition:all 0.3s;position:relative;
        ">
          <input type="file" name="design_file" id="design_file" accept=".jpg,.png,.webp,.pdf,.gif"
                 style="position:absolute;inset:0;opacity:0;cursor:none;" required>
          <i data-lucide="upload-cloud" style="width:40px;height:40px;color:var(--gold);display:block;margin:0 auto 1rem;"></i>
          <div style="font-size:0.9rem;font-weight:600;margin-bottom:0.35rem;">Drop file here or click to browse</div>
          <div id="file-name" style="font-size:0.78rem;color:var(--text-muted);">JPG, PNG, WEBP, PDF — up to <?= MAX_UPLOAD_MB ?>MB</div>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="notes">Notes for Client</label>
        <textarea name="notes" id="notes" class="form-textarea" rows="4"
          placeholder="Describe the design choices, fabric options, or anything the client should know when reviewing…"></textarea>
      </div>

      <div style="display:flex;gap:0.75rem;">
        <a href="<?= SITE_URL ?>/staff/dashboard.php" class="btn btn--ghost">Cancel</a>
        <button type="submit" class="btn btn--primary" <?= empty($orders_list)?'disabled':'' ?>>
          <i data-lucide="upload"></i> Submit Design for Approval
        </button>
      </div>
    </form>
  </div>
</div>

<script>
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('design_file');
const fileName  = document.getElementById('file-name');

fileInput.addEventListener('change', () => {
  if (fileInput.files[0]) {
    fileName.textContent = fileInput.files[0].name;
    dropZone.style.borderColor = 'var(--gold)';
    dropZone.style.background  = 'var(--gold-faint)';
  }
});
['dragover','dragenter'].forEach(e => dropZone.addEventListener(e, ev => {
  ev.preventDefault();
  dropZone.style.borderColor = 'var(--gold)';
}));
['dragleave','drop'].forEach(e => dropZone.addEventListener(e, ev => {
  ev.preventDefault();
  dropZone.style.borderColor = '';
  if (ev.type === 'drop' && ev.dataTransfer.files[0]) {
    fileInput.files = ev.dataTransfer.files;
    fileName.textContent = ev.dataTransfer.files[0].name;
  }
}));
</script>

<?php include __DIR__ . '/../includes/dash_footer.php'; ?>
