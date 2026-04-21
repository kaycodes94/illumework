<?php
// ============================================================
// ILLUME — Founder: Analytics (Stub)
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_founder();

$dash_title = 'Analytics';
$active_nav = 'analytics';
$pdo        = db();

// Revenue by month (last 6 months)
$revenue = $pdo->query("
    SELECT DATE_FORMAT(created_at,'%b %Y') AS month,
           SUM(budget) AS total,
           COUNT(*) AS count
    FROM orders
    WHERE status='complete'
      AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at,'%Y-%m')
    ORDER BY MIN(created_at) ASC
")->fetchAll();

// Service breakdown
$by_service = $pdo->query("
    SELECT service_type, COUNT(*) AS n
    FROM orders
    WHERE service_type IS NOT NULL AND service_type != ''
    GROUP BY service_type ORDER BY n DESC LIMIT 6
")->fetchAll();

// Consultation conversion
$conv = $pdo->query("
    SELECT status, COUNT(*) AS n FROM consultations GROUP BY status
")->fetchAll(PDO::FETCH_KEY_PAIR);

// Monthly new clients
$new_clients = $pdo->query("
    SELECT DATE_FORMAT(created_at,'%b %Y') AS m, COUNT(*) AS n
    FROM users WHERE role='client'
    AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at,'%Y-%m')
    ORDER BY MIN(created_at) ASC
")->fetchAll();

include __DIR__ . '/../includes/dash_header.php';
?>

<div class="dash-page-header">
  <div>
    <h2 class="dash-page-header__title">Analytics</h2>
    <p class="dash-page-header__subtitle">Business performance overview</p>
  </div>
</div>

<!-- Quick KPIs -->
<div class="kpi-grid" style="margin-bottom:2rem;">
  <?php
  $total_rev  = $pdo->query("SELECT COALESCE(SUM(budget),0) FROM orders WHERE status='complete'")->fetchColumn();
  $total_ord  = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
  $total_cli  = $pdo->query("SELECT COUNT(*) FROM users WHERE role='client'")->fetchColumn();
  $total_con  = $pdo->query("SELECT COUNT(*) FROM consultations")->fetchColumn();
  $conv_rate  = $total_con > 0 ? round(($conv['converted']??0) / $total_con * 100) : 0;

  $kpis = [
    ['label'=>'Total Revenue',       'value'=>format_currency((float)$total_rev), 'sub'=>'Completed orders',       'color'=>'var(--gold)',   'icon'=>'trending-up'],
    ['label'=>'Total Orders',        'value'=>$total_ord,                         'sub'=>'All time',               'color'=>'var(--plasma)', 'icon'=>'package'],
    ['label'=>'Total Clients',       'value'=>$total_cli,                         'sub'=>'Registered',             'color'=>'var(--gold)',   'icon'=>'users'],
    ['label'=>'Consultation Rate',   'value'=>$conv_rate.'%',                     'sub'=>'Consultations converted','color'=>'var(--success)','icon'=>'percent'],
  ];
  foreach ($kpis as $kpi): ?>
  <div class="kpi-card">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:0.75rem;">
      <div class="kpi-label"><?= e($kpi['label']) ?></div>
      <div style="width:34px;height:34px;border-radius:var(--r);background:rgba(255,255,255,0.04);display:flex;align-items:center;justify-content:center;color:<?= $kpi['color'] ?>;">
        <i data-lucide="<?= $kpi['icon'] ?>" style="width:15px;height:15px;"></i>
      </div>
    </div>
    <div class="kpi-value" style="color:<?= $kpi['color'] ?>;"><?= $kpi['value'] ?></div>
    <div style="font-size:0.72rem;color:var(--text-muted);margin-top:0.3rem;"><?= $kpi['sub'] ?></div>
  </div>
  <?php endforeach; ?>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">

  <!-- Revenue Over Time -->
  <div class="glass" style="padding:1.5rem;border-radius:var(--r-xl);">
    <div style="font-size:0.85rem;font-weight:600;margin-bottom:1.25rem;display:flex;align-items:center;justify-content:space-between;">
      Revenue (Completed Orders)
      <span class="badge badge--gold">Last 6 Months</span>
    </div>
    <?php if (empty($revenue)): ?>
    <div style="text-align:center;padding:2rem;color:var(--text-muted);font-size:0.85rem;">No completed orders yet</div>
    <?php else: ?>
    <?php $max_rev = max(array_column($revenue,'total')) ?: 1; ?>
    <div style="display:flex;flex-direction:column;gap:0.6rem;">
      <?php foreach ($revenue as $r): ?>
      <div style="display:flex;align-items:center;gap:0.75rem;">
        <span style="width:64px;font-size:0.72rem;color:var(--text-muted);flex-shrink:0;"><?= e($r['month']) ?></span>
        <div style="flex:1;background:var(--space-border);border-radius:var(--r-full);height:8px;overflow:hidden;">
          <div style="width:<?= round($r['total']/$max_rev*100) ?>%;background:linear-gradient(90deg,var(--gold),var(--gold-bright));height:100%;border-radius:var(--r-full);"></div>
        </div>
        <span style="font-size:0.75rem;font-weight:600;color:var(--gold);min-width:80px;text-align:right;"><?= format_currency((float)$r['total']) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- Service Breakdown -->
  <div class="glass" style="padding:1.5rem;border-radius:var(--r-xl);">
    <div style="font-size:0.85rem;font-weight:600;margin-bottom:1.25rem;">Orders by Service</div>
    <?php if (empty($by_service)): ?>
    <div style="text-align:center;padding:2rem;color:var(--text-muted);font-size:0.85rem;">No orders yet</div>
    <?php else: ?>
    <?php $max_s = max(array_column($by_service,'n')) ?: 1; ?>
    <div style="display:flex;flex-direction:column;gap:0.6rem;">
      <?php foreach ($by_service as $s): ?>
      <div style="display:flex;align-items:center;gap:0.75rem;">
        <span style="flex:1;font-size:0.78rem;color:var(--text-secondary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= e($s['service_type']) ?></span>
        <div style="width:100px;background:var(--space-border);border-radius:var(--r-full);height:6px;overflow:hidden;flex-shrink:0;">
          <div style="width:<?= round($s['n']/$max_s*100) ?>%;background:var(--plasma);height:100%;border-radius:var(--r-full);"></div>
        </div>
        <span style="font-size:0.75rem;font-weight:600;color:var(--plasma);width:20px;text-align:right;"><?= (int)$s['n'] ?></span>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

</div>

<!-- Consultation Funnel -->
<div class="glass" style="padding:1.5rem;border-radius:var(--r-xl);">
  <div style="font-size:0.85rem;font-weight:600;margin-bottom:1.25rem;">Consultation Funnel</div>
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;text-align:center;">
    <?php
    $funnel = [
      'new'       => ['label'=>'Received',  'color'=>'var(--plasma)'],
      'contacted' => ['label'=>'Contacted', 'color'=>'var(--gold)'],
      'converted' => ['label'=>'Converted', 'color'=>'var(--success)'],
      'declined'  => ['label'=>'Declined',  'color'=>'var(--danger)'],
    ];
    $total_c = array_sum($conv) ?: 1;
    foreach ($funnel as $key => $f): $n = $conv[$key] ?? 0; ?>
    <div>
      <div style="font-family:var(--font-display);font-size:2rem;font-weight:700;color:<?= $f['color'] ?>;"><?= $n ?></div>
      <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--text-muted);margin-bottom:0.5rem;"><?= $f['label'] ?></div>
      <div style="background:var(--space-border);border-radius:var(--r-full);height:4px;overflow:hidden;">
        <div style="width:<?= round($n/$total_c*100) ?>%;background:<?= $f['color'] ?>;height:100%;"></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<?php include __DIR__ . '/../includes/dash_footer.php'; ?>
