<?php
// ============================================================
// ILLUME — Founder Dashboard
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_founder();

$dash_title = 'Founder Dashboard';
$active_nav = 'dashboard';

// ── KPIs ──────────────────────────────────────────────────
$pdo = db();
$active_orders     = $pdo->query("SELECT COUNT(*) FROM orders WHERE status NOT IN ('complete','cancelled')")->fetchColumn();
$new_consults      = $pdo->query("SELECT COUNT(*) FROM consultations WHERE status='new'")->fetchColumn();
$pending_approvals = $pdo->query("SELECT COUNT(*) FROM design_submissions WHERE approval_status='pending'")->fetchColumn();
$total_clients     = $pdo->query("SELECT COUNT(*) FROM users WHERE role='client' AND status='active'")->fetchColumn();
$total_revenue     = $pdo->query("SELECT COALESCE(SUM(budget),0) FROM orders WHERE status='complete'")->fetchColumn();

// Badge counts for sidebar
$badges = ['new_consults' => $new_consults];

// ── Recent Orders ─────────────────────────────────────────
$recent_orders = $pdo->query("
    SELECT o.*, u.name AS client_name
    FROM orders o
    JOIN users u ON o.client_id = u.id
    ORDER BY o.created_at DESC LIMIT 6
")->fetchAll();

// ── Recent Consultations ──────────────────────────────────
$recent_consults = $pdo->query("
    SELECT * FROM consultations ORDER BY created_at DESC LIMIT 5
")->fetchAll();

include __DIR__ . '/../includes/dash_header.php';
?>

<!-- ── PAGE HEADER ──────────────────────────────────────── -->
<div class="dash-page-header">
  <div>
    <h2 class="dash-page-header__title">Good <?= (date('H') < 12 ? 'Morning' : (date('H') < 17 ? 'Afternoon' : 'Evening')) ?>, <?= e(explode(' ', $user['name'])[0]) ?> ✦</h2>
    <p class="dash-page-header__subtitle"><?= date('l, F j, Y') ?> · Here's your ILLUME overview</p>
  </div>
  <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
    <a href="<?= SITE_URL ?>/founder/consultations.php" class="btn btn--ghost btn--sm">
      <i data-lucide="calendar"></i> Consultations
      <?php if ($new_consults > 0): ?>
      <span style="
        background:var(--aura);color:var(--void);
        font-size:0.65rem;font-weight:700;
        padding:1px 7px;border-radius:9999px;
      "><?= (int)$new_consults ?></span>
      <?php endif; ?>
    </a>
    <a href="<?= SITE_URL ?>/founder/orders.php" class="btn btn--primary btn--sm">
      <i data-lucide="plus"></i> New Order
    </a>
  </div>
</div>

<!-- ── KPI GRID ─────────────────────────────────────────── -->
<div class="kpi-grid">
  <?php
  $kpis = [
    ['label'=>'Active Orders',      'value'=>$active_orders,                         'suffix'=>'',   'icon'=>'package',    'color'=>'var(--gold)',   'sub'=>'In production'],
    ['label'=>'New Consultations',  'value'=>$new_consults,                          'suffix'=>'',   'icon'=>'calendar',   'color'=>'var(--aura)',   'sub'=>'Awaiting contact'],
    ['label'=>'Pending Approvals',  'value'=>$pending_approvals,                     'suffix'=>'',   'icon'=>'eye',        'color'=>'var(--warning)','sub'=>'Design reviews'],
    ['label'=>'Active Clients',     'value'=>$total_clients,                         'suffix'=>'',   'icon'=>'users',      'color'=>'var(--gold)',   'sub'=>'On the platform'],
    ['label'=>'Revenue (Complete)', 'value'=>format_currency((float)$total_revenue), 'suffix'=>null, 'icon'=>'trending-up','color'=>'var(--success)','sub'=>'Completed orders'],
  ];
  foreach ($kpis as $kpi): ?>
  <div class="kpi-card">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1rem;">
      <div class="kpi-label"><?= e($kpi['label']) ?></div>
      <div style="
        width:36px;height:36px;border-radius:var(--r);
        background:rgba(255,255,255,0.04);
        display:flex;align-items:center;justify-content:center;
        color:<?= $kpi['color'] ?>;
      ">
        <i data-lucide="<?= e($kpi['icon']) ?>" style="width:16px;height:16px;"></i>
      </div>
    </div>
    <div class="kpi-value" style="color:<?= $kpi['color'] ?>;">
      <?= is_string($kpi['value']) ? e($kpi['value']) : $kpi['value'] ?>
    </div>
    <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.35rem;"><?= e($kpi['sub']) ?></div>
  </div>
  <?php endforeach; ?>
</div>

<!-- ── MAIN GRID ─────────────────────────────────────────── -->
<div style="display:grid;grid-template-columns:1.4fr 1fr;gap:1.5rem;">

  <!-- Recent Orders Table -->
  <div>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
      <h4>Recent Orders</h4>
      <a href="<?= SITE_URL ?>/founder/orders.php" class="btn btn--ghost btn--sm">View All</a>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Ref</th>
            <th>Client</th>
            <th>Service</th>
            <th>Status</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($recent_orders)): ?>
          <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:2rem;">No orders yet</td></tr>
          <?php else: ?>
          <?php foreach ($recent_orders as $order): ?>
          <tr onclick="location.href='<?= SITE_URL ?>/founder/orders.php'" style="cursor:pointer;">
            <td><span style="font-family:monospace;font-size:0.78rem;color:var(--gold);"><?= e($order['order_ref']) ?></span></td>
            <td style="font-weight:500;"><?= e($order['client_name']) ?></td>
            <td style="font-size:0.82rem;max-width:140px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= e($order['service_type']) ?></td>
            <td><?= order_status_badge($order['status']) ?></td>
            <td style="font-size:0.8rem;color:var(--text-muted);"><?= time_ago($order['created_at']) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Recent Consultations Feed -->
  <div>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
      <h4>New Consultations</h4>
      <a href="<?= SITE_URL ?>/founder/consultations.php" class="btn btn--ghost btn--sm">View All</a>
    </div>
    <div class="activity-feed">
      <div class="activity-feed__header">
        <span>Incoming Requests</span>
        <?php if ($new_consults > 0): ?>
        <span class="badge badge--aura"><?= (int)$new_consults ?> new</span>
        <?php endif; ?>
      </div>
      <?php if (empty($recent_consults)): ?>
      <div style="padding:2rem;text-align:center;color:var(--text-muted);font-size:0.88rem;">No consultations yet</div>
      <?php else: ?>
      <?php foreach ($recent_consults as $c): ?>
      <a href="<?= SITE_URL ?>/founder/consultations.php" class="activity-feed__item" style="text-decoration:none;">
        <div class="activity-feed__icon">
          <i data-lucide="user"></i>
        </div>
        <div class="activity-feed__text">
          <span style="font-weight:600;color:var(--text-primary);"><?= e($c['name']) ?></span>
          <br>
          <?= e($c['service_type'] ?: 'General inquiry') ?>
          <br>
          <?= consultation_status_badge($c['status']) ?>
        </div>
        <div class="activity-feed__time"><?= time_ago($c['created_at']) ?></div>
      </a>
      <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Quick WhatsApp -->
    <div class="glass" style="padding:1.25rem;margin-top:1rem;text-align:center;">
      <div style="font-size:0.78rem;color:var(--text-muted);margin-bottom:0.75rem;">Quick client reach-out</div>
      <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>" class="btn btn--ghost btn--sm btn--w-full" target="_blank" rel="noopener">
        <i data-lucide="message-circle"></i> Open WhatsApp
      </a>
    </div>
  </div>

</div>

<?php include __DIR__ . '/../includes/dash_footer.php'; ?>
