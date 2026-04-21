<?php
// ============================================================
// ILLUME — Staff: My Orders (full list)
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_staff_or_founder();

$dash_title = 'My Orders';
$active_nav = 'orders';
$pdo        = db();
$uid        = current_user_id();

$filter = $_GET['status'] ?? 'all';

$where  = is_founder() ? [] : ['o.assigned_staff_id = ?'];
$params = is_founder() ? [] : [$uid];
if ($filter !== 'all') { $where[] = 'o.status = ?'; $params[] = $filter; }
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$orders = $pdo->prepare("
    SELECT o.*, uc.name AS client_name
    FROM orders o
    JOIN users uc ON o.client_id = uc.id
    $where_sql
    ORDER BY o.updated_at DESC
");
$orders->execute($params);
$orders = $orders->fetchAll();

include __DIR__ . '/../includes/dash_header.php';
?>

<div class="dash-page-header">
  <div>
    <h2 class="dash-page-header__title">My Orders</h2>
    <p class="dash-page-header__subtitle"><?= count($orders) ?> order<?= count($orders)!==1?'s':'' ?></p>
  </div>
  <a href="<?= SITE_URL ?>/staff/upload-design.php" class="btn btn--primary btn--sm">
    <i data-lucide="upload"></i> Upload Design
  </a>
</div>

<!-- Filter tabs -->
<div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:1.5rem;">
  <?php foreach (['all'=>'All','design'=>'Design','approval'=>'Approval','production'=>'Production','complete'=>'Complete'] as $val=>$label): ?>
  <a href="?status=<?= $val ?>" class="btn btn--sm <?= $filter===$val?'btn--primary':'btn--ghost' ?>"><?= $label ?></a>
  <?php endforeach; ?>
</div>

<?php if (empty($orders)): ?>
<div class="glass" style="padding:3rem;text-align:center;border-radius:var(--r-xl);">
  <i data-lucide="package" style="width:40px;height:40px;color:var(--text-muted);display:block;margin:0 auto 1rem;"></i>
  <p style="color:var(--text-muted);">No orders found.</p>
</div>
<?php else: ?>
<div class="table-wrap">
  <table>
    <thead>
      <tr><th>Ref</th><th>Title</th><th>Client</th><th>Status</th><th>Deadline</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($orders as $o): ?>
      <tr>
        <td><span style="font-family:monospace;font-size:0.72rem;color:var(--gold);"><?= e($o['order_ref']) ?></span></td>
        <td>
          <div style="font-weight:600;font-size:0.88rem;"><?= e($o['title']) ?></div>
          <div style="font-size:0.72rem;color:var(--text-muted);"><?= e($o['service_type']) ?></div>
        </td>
        <td style="font-size:0.85rem;"><?= e($o['client_name']) ?></td>
        <td><?= order_status_badge($o['status']) ?></td>
        <td style="font-size:0.78rem;color:var(--text-muted);"><?= $o['deadline'] ? format_date($o['deadline'],'d M Y') : '—' ?></td>
        <td>
          <div style="display:flex;gap:0.35rem;">
            <a href="order-details.php?id=<?= (int)$o['id'] ?>" class="btn btn--secondary btn--sm">
              <i data-lucide="eye" style="width:13px;height:13px;"></i> Details
            </a>
            <a href="<?= SITE_URL ?>/staff/upload-design.php?order=<?= (int)$o['id'] ?>" class="btn btn--ghost btn--sm">
              <i data-lucide="upload" style="width:13px;height:13px;"></i> Upload
            </a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/dash_footer.php'; ?>
