<?php
// ============================================================
// ILLUME — Founder: Orders Manager
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_founder();

$dash_title = 'Orders';
$active_nav = 'orders';
$pdo        = db();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf() && isset($_POST['update_status'])) {
    $oid    = (int)($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $allowed = ['intake','design','approval','production','delivery','complete','cancelled'];
    if ($oid && in_array($status, $allowed)) {
        $note = trim($_POST['note'] ?? "Status changed to: $status");
        $pdo->prepare("UPDATE orders SET status=?, updated_at=NOW() WHERE id=?")->execute([$status, $oid]);
        $pdo->prepare("INSERT INTO order_timeline (order_id, actor_id, action, note) VALUES (?,?,?,?)")
            ->execute([$oid, current_user_id(), 'status_update', $note ?: "Status updated to " . ucfirst($status)]);
        flash('order', 'Order status updated.', 'success');
    }
    header('Location: ' . SITE_URL . '/founder/orders.php');
    exit;
}

// Filters
$filter_status = $_GET['status'] ?? 'all';
$search        = trim($_GET['q'] ?? '');
$page          = max(1, (int)($_GET['page'] ?? 1));
$per_page      = 15;

$where = []; $params = [];
if ($filter_status !== 'all') { $where[] = 'o.status = ?'; $params[] = $filter_status; }
if ($search) {
    $where[] = '(o.title LIKE ? OR o.order_ref LIKE ? OR uc.name LIKE ?)';
    $params  = array_merge($params, ["%$search%","%$search%","%$search%"]);
}
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = $pdo->prepare("SELECT COUNT(*) FROM orders o JOIN users uc ON o.client_id=uc.id $where_sql");
$total->execute($params);
$total = (int)$total->fetchColumn();
$pg    = paginate($total, $per_page, $page);

$stmt = $pdo->prepare("
    SELECT o.*, uc.name AS client_name, us.name AS staff_name
    FROM orders o
    JOIN users uc ON o.client_id = uc.id
    LEFT JOIN users us ON o.assigned_staff_id = us.id
    $where_sql
    ORDER BY o.updated_at DESC
    LIMIT $per_page OFFSET {$pg['offset']}
");
$stmt->execute($params);
$orders = $stmt->fetchAll();

// Staff list for assignment
$staff_list = $pdo->query("SELECT id, name FROM users WHERE role='staff' AND status='active' ORDER BY name")->fetchAll();
// Count by status
$status_counts = $pdo->query("SELECT status, COUNT(*) FROM orders GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);

include __DIR__ . '/../includes/dash_header.php';
render_flash('order');
?>

<div class="dash-page-header">
  <div>
    <h2 class="dash-page-header__title">Orders</h2>
    <p class="dash-page-header__subtitle"><?= $total ?> total orders</p>
  </div>
</div>

<!-- Status tabs -->
<div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:1.5rem;overflow-x:auto;padding-bottom:0.5rem;">
  <?php
  $statuses = ['all'=>'All','intake'=>'Intake','design'=>'Design','approval'=>'Approval','production'=>'Production','delivery'=>'Delivery','complete'=>'Complete','cancelled'=>'Cancelled'];
  foreach ($statuses as $val => $label):
    $n = $val === 'all' ? $total : ($status_counts[$val] ?? 0);
  ?>
  <a href="?status=<?= $val ?>&q=<?= urlencode($search) ?>"
     class="btn btn--sm <?= $filter_status === $val ? 'btn--primary' : 'btn--ghost' ?>">
    <?= $label ?><?php if ($n): ?> <span style="opacity:0.7;">(<?= $n ?>)</span><?php endif; ?>
  </a>
  <?php endforeach; ?>
</div>

<!-- Search -->
<form method="GET" style="margin-bottom:1.5rem;">
  <input type="hidden" name="status" value="<?= e($filter_status) ?>">
  <div class="search-box">
    <i data-lucide="search"></i>
    <input type="text" name="q" placeholder="Search by title, ref, or client name…" value="<?= e($search) ?>">
  </div>
</form>

<!-- Table -->
<div class="table-wrap">
  <table>
    <thead>
      <tr>
        <th>Ref</th><th>Title</th><th>Client</th><th>Staff</th>
        <th>Budget</th><th>Due</th><th>Status</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($orders)): ?>
      <tr><td colspan="8" style="text-align:center;padding:3rem;color:var(--text-muted);">No orders found.</td></tr>
      <?php else: ?>
      <?php foreach ($orders as $o): ?>
      <tr>
        <td>
          <a href="order-details.php?id=<?= (int)$o['id'] ?>" style="font-family:monospace;font-size:0.72rem;color:var(--gold);text-decoration:underline;">
            <?= e($o['order_ref']) ?>
          </a>
        </td>
        <td>
          <a href="order-details.php?id=<?= (int)$o['id'] ?>" style="display:block;">
            <div style="font-weight:600;font-size:0.88rem;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= e($o['title']) ?></div>
            <div style="font-size:0.72rem;color:var(--text-muted);"><?= e($o['service_type']) ?></div>
          </a>
        </td>
        <td style="font-size:0.85rem;font-weight:500;"><?= e($o['client_name']) ?></td>
        <td style="font-size:0.82rem;color:var(--text-muted);"><?= e($o['staff_name'] ?: '—') ?></td>
        <td style="font-size:0.85rem;color:var(--gold);"><?= $o['budget'] ? format_currency((float)$o['budget'], $o['currency']) : '—' ?></td>
        <td style="font-size:0.78rem;color:var(--text-muted);"><?= $o['deadline'] ? format_date($o['deadline'], 'd M Y') : '—' ?></td>
        <td><?= order_status_badge($o['status']) ?></td>
        <td>
          <form method="POST" action="" style="display:flex;gap:0.35rem;align-items:center;">
            <?= csrf_field() ?>
            <input type="hidden" name="order_id" value="<?= (int)$o['id'] ?>">
            <select name="status" class="form-select" style="padding:0.2rem 0.5rem;font-size:0.78rem;width:auto;">
              <?php foreach (['intake','design','approval','production','delivery','complete','cancelled'] as $s): ?>
              <option value="<?= $s ?>" <?= $o['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
              <?php endforeach; ?>
            </select>
            <button type="submit" name="update_status" value="1" class="btn btn--secondary btn--sm">
              <i data-lucide="check" style="width:13px;height:13px;"></i>
            </button>
            <a href="order-details.php?id=<?= (int)$o['id'] ?>" class="btn btn--ghost btn--sm">
              <i data-lucide="eye" style="width:13px;height:13px;"></i>
            </a>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Pagination -->
<?php if ($pg['total_pages'] > 1): ?>
<div style="display:flex;justify-content:center;gap:0.5rem;margin-top:1.5rem;">
  <?php if ($pg['has_prev']): ?>
  <a href="?status=<?= e($filter_status) ?>&q=<?= urlencode($search) ?>&page=<?= $pg['current']-1 ?>" class="btn btn--ghost btn--sm">← Prev</a>
  <?php endif; ?>
  <span style="padding:0.4rem 0.75rem;font-size:0.85rem;color:var(--text-muted);"><?= $pg['current'] ?> / <?= $pg['total_pages'] ?></span>
  <?php if ($pg['has_next']): ?>
  <a href="?status=<?= e($filter_status) ?>&q=<?= urlencode($search) ?>&page=<?= $pg['current']+1 ?>" class="btn btn--ghost btn--sm">Next →</a>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/dash_footer.php'; ?>
