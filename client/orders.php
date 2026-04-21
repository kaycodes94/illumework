<?php
// ============================================================
// ILLUME — Client: My Orders (full list)
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_client();

$dash_title = 'My Orders';
$active_nav = 'orders';
$pdo        = db();
$uid        = current_user_id();

$orders = $pdo->prepare("
    SELECT o.*, us.name AS staff_name
    FROM orders o
    LEFT JOIN users us ON o.assigned_staff_id = us.id
    WHERE o.client_id = ?
    ORDER BY o.updated_at DESC
");
$orders->execute([$uid]);
$orders = $orders->fetchAll();

include __DIR__ . '/../includes/dash_header.php';
?>

<div class="dash-page-header">
  <div>
    <h2 class="dash-page-header__title">My Orders</h2>
    <p class="dash-page-header__subtitle"><?= count($orders) ?> order<?= count($orders)!==1?'s':'' ?></p>
  </div>
  <a href="<?= SITE_URL ?>/consultation.php" class="btn btn--primary btn--sm">
    <i data-lucide="plus"></i> New Consultation
  </a>
</div>

<?php if (empty($orders)): ?>
<div class="glass" style="padding:4rem;text-align:center;border-radius:var(--r-2xl);">
  <i data-lucide="package" style="width:56px;height:56px;color:var(--text-muted);display:block;margin:0 auto 1.25rem;"></i>
  <h3 style="margin-bottom:0.75rem;">No Orders Yet</h3>
  <p style="color:var(--text-muted);max-width:380px;margin:0 auto 2rem;">
    Start by booking a consultation. We'll create your order once we've discussed your vision.
  </p>
  <a href="<?= SITE_URL ?>/consultation.php" class="btn btn--primary">
    <i data-lucide="calendar"></i> Book Consultation
  </a>
</div>
<?php else: ?>
<div style="display:flex;flex-direction:column;gap:1.25rem;">
  <?php foreach ($orders as $order): ?>
  <div class="glass" style="padding:2rem;border-radius:var(--r-xl);">
    <!-- Header row -->
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.25rem;">
      <div>
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.4rem;flex-wrap:wrap;">
          <span style="font-family:monospace;font-size:0.75rem;color:var(--gold);"><?= e($order['order_ref']) ?></span>
          <?= order_status_badge($order['status']) ?>
          <?php if ($order['service_type']): ?>
          <span style="font-size:0.78rem;color:var(--text-muted);"><?= e($order['service_type']) ?></span>
          <?php endif; ?>
        </div>
        <h4 style="font-size:1.1rem;"><?= e($order['title']) ?></h4>
        <?php if ($order['description']): ?>
        <p style="font-size:0.85rem;color:var(--text-muted);margin-top:0.3rem;max-width:500px;"><?= e(mb_strimwidth($order['description'],0,120,'…')) ?></p>
        <?php endif; ?>
      </div>
      <div style="text-align:right;flex-shrink:0;">
        <?php if ($order['budget']): ?>
        <div style="font-size:1.2rem;font-weight:700;color:var(--gold);"><?= format_currency((float)$order['budget'],$order['currency']) ?></div>
        <?php endif; ?>
        <?php if ($order['deadline']): ?>
        <div style="font-size:0.78rem;color:var(--text-muted);margin-top:0.2rem;">
          <i data-lucide="clock" style="width:11px;height:11px;display:inline;"></i>
          Due <?= format_date($order['deadline']) ?>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Progress track -->
    <?php
    $statuses = ['intake','design','approval','production','delivery','complete'];
    $currentIdx = array_search($order['status'], $statuses);
    ?>
    <?php if ($order['status'] !== 'cancelled'): ?>
    <div class="order-status-track" style="margin-bottom:1rem;">
      <?php foreach ($statuses as $si => $s): ?>
      <div class="status-step <?= $si < $currentIdx ? 'done' : ($si === $currentIdx ? 'current' : '') ?>">
        <div class="status-dot">
          <?php if ($si < $currentIdx): ?><i data-lucide="check" style="width:11px;height:11px;"></i><?php endif; ?>
        </div>
        <span class="status-step-label"><?= ucfirst($s) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="alert alert--error" style="margin-bottom:1rem;padding:0.5rem 0.75rem;font-size:0.82rem;">
      <i data-lucide="x-circle"></i> <span>This order was cancelled.</span>
    </div>
    <?php endif; ?>

    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.5rem;">
      <div style="font-size:0.75rem;color:var(--text-muted);">
        Created <?= time_ago($order['created_at']) ?>
        <?php if ($order['staff_name']): ?>
        · Assigned to <strong><?= e($order['staff_name']) ?></strong>
        <?php endif; ?>
      </div>
      <a href="order-details.php?id=<?= (int)$order['id'] ?>" class="btn btn--plasma btn--sm">
        <i data-lucide="activity"></i> Track Progress
      </a>
      <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>?text=Hello%20ILLUME!%20I%20have%20a%20question%20about%20my%20order%20<?= urlencode($order['order_ref']) ?>."
         class="btn btn--ghost btn--sm" target="_blank" rel="noopener">
        <i data-lucide="message-circle"></i> Ask Support
      </a>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/dash_footer.php'; ?>
