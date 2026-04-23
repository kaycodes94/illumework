<?php
// ============================================================
// ILLUME — Staff Dashboard
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_staff_or_founder();

$dash_title = 'Staff Dashboard';
$active_nav = 'dashboard';
$pdo        = db();
$uid        = current_user_id();

// My assigned orders
$my_orders = $pdo->prepare("
    SELECT o.*, u.name AS client_name
    FROM orders o
    JOIN users u ON o.client_id = u.id
    WHERE o.assigned_staff_id = ?
    ORDER BY o.updated_at DESC
");
$my_orders->execute([$uid]);
$my_orders = $my_orders->fetchAll();

// My recent uploads
$my_uploads = $pdo->prepare("
    SELECT ds.*, o.title AS order_title
    FROM design_submissions ds
    JOIN orders o ON ds.order_id = o.id
    WHERE ds.uploaded_by = ?
    ORDER BY ds.created_at DESC LIMIT 5
");
$my_uploads->execute([$uid]);
$my_uploads = $my_uploads->fetchAll();

$active_count  = count(array_filter($my_orders, fn($o) => !in_array($o['status'],['complete','cancelled'])));
$pending_count = count(array_filter($my_orders, fn($o) => $o['status'] === 'approval'));

include __DIR__ . '/../includes/dash_header.php';
?>

<div class="dash-page-header">
  <div>
    <h2 class="dash-page-header__title">My Workspace ✦</h2>
    <p class="dash-page-header__subtitle"><?= date('l, F j, Y') ?> · Your assigned work</p>
  </div>
  <a href="<?= SITE_URL ?>/staff/upload-design.php" class="btn btn--primary btn--sm">
    <i data-lucide="upload"></i> Upload Design
  </a>
</div>

<!-- KPI Cards -->
<div class="kpi-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:2rem;">
  <div class="kpi-card">
    <div class="kpi-label">Active Orders</div>
    <div class="kpi-value" style="color:var(--gold);"><?= $active_count ?></div>
    <div style="font-size:0.75rem;color:var(--text-muted);">Currently assigned</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Awaiting Approval</div>
    <div class="kpi-value" style="color:var(--warning);"><?= $pending_count ?></div>
    <div style="font-size:0.75rem;color:var(--text-muted);">Client review pending</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Total Orders</div>
    <div class="kpi-value" style="color:var(--text-primary);"><?= count($my_orders) ?></div>
    <div style="font-size:0.75rem;color:var(--text-muted);">All time</div>
  </div>
</div>

<!-- My Orders -->
<div style="display:grid;grid-template-columns:1.3fr 1fr;gap:1.5rem;">
  <div>
    <h4 style="margin-bottom:1rem;">My Assigned Orders</h4>
    <?php if (empty($my_orders)): ?>
    <div class="glass" style="padding:2.5rem;text-align:center;">
      <i data-lucide="package" style="width:40px;height:40px;color:var(--text-muted);display:block;margin:0 auto 1rem;"></i>
      <p style="color:var(--text-muted);font-size:0.9rem;">No orders assigned yet.</p>
    </div>
    <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:0.75rem;">
      <?php foreach ($my_orders as $order): ?>
      <div class="glass" style="padding:1.25rem;border-radius:var(--r-xl);">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:0.75rem;margin-bottom:0.75rem;">
          <div>
            <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.25rem;">
              <span style="font-family:monospace;font-size:0.72rem;color:var(--gold);"><?= e($order['order_ref']) ?></span>
              <?= order_status_badge($order['status']) ?>
            </div>
            <div style="font-weight:600;font-size:0.9rem;"><?= e($order['title']) ?></div>
            <div style="font-size:0.78rem;color:var(--text-muted);">Client: <?= e($order['client_name']) ?></div>
          </div>
          <div style="display:flex;gap:0.35rem;flex-shrink:0;">
            <a href="order-details.php?id=<?= (int)$order['id'] ?>" class="btn btn--secondary btn--sm">
              <i data-lucide="eye" style="width:12px;height:12px;"></i>
            </a>
            <a href="<?= SITE_URL ?>/staff/upload-design.php?order=<?= (int)$order['id'] ?>" class="btn btn--ghost btn--sm">
              <i data-lucide="upload" style="width:12px;height:12px;"></i>
            </a>
          </div>
        </div>
        <?php if ($order['deadline']): ?>
        <div style="font-size:0.75rem;color:var(--text-muted);">
          <i data-lucide="clock" style="width:12px;height:12px;display:inline;"></i>
          Deadline: <?= format_date($order['deadline']) ?>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- Recent Uploads -->
  <div>
    <h4 style="margin-bottom:1rem;">My Recent Uploads</h4>
    <div class="activity-feed">
      <div class="activity-feed__header">Design Submissions</div>
      <?php if (empty($my_uploads)): ?>
      <div style="padding:2rem;text-align:center;font-size:0.85rem;color:var(--text-muted);">No uploads yet</div>
      <?php else: ?>
      <?php foreach ($my_uploads as $u): ?>
      <div class="activity-feed__item">
        <div class="activity-feed__icon" style="background:var(--aura-dim);border-color:var(--aura-glass);color:var(--aura);">
          <i data-lucide="image"></i>
        </div>
        <div class="activity-feed__text">
          <span style="font-weight:600;color:var(--text-primary);font-size:0.85rem;"><?= e($u['title'] ?: 'Design') ?></span>
          <br>
          <span style="font-size:0.78rem;"><?= e($u['order_title']) ?></span>
          <br>
          <?php
          $as = $u['approval_status'];
          $map = ['pending'=>'badge--gold','approved'=>'badge--success','revision_requested'=>'badge--warning','rejected'=>'badge--error'];
          echo "<span class=\"badge {$map[$as]}\" style=\"font-size:0.6rem;\">".ucfirst(str_replace('_',' ',$as))."</span>";
          ?>
        </div>
        <div class="activity-feed__time"><?= time_ago($u['created_at']) ?></div>
      </div>
      <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <a href="<?= SITE_URL ?>/staff/upload-design.php" class="btn btn--primary btn--w-full btn--sm" style="margin-top:1rem;">
      <i data-lucide="upload"></i> Upload New Design
    </a>
  </div>
</div>

<?php include __DIR__ . '/../includes/dash_footer.php'; ?>
