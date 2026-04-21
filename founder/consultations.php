<?php
// ============================================================
// ILLUME — Founder: Consultations Manager
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_founder();

$dash_title = 'Consultations';
$active_nav = 'consultations';
$pdo        = db();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $cid    = (int)($_POST['consult_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $allowed = ['new','contacted','converted','declined'];
    if ($cid && in_array($status, $allowed)) {
        $pdo->prepare("UPDATE consultations SET status=?, updated_at=NOW() WHERE id=?")
            ->execute([$status, $cid]);
        flash('consult', 'Status updated successfully.', 'success');
    }
    header('Location: ' . SITE_URL . '/founder/consultations.php');
    exit;
}

// Filters
$filter_status = $_GET['status'] ?? 'all';
$search        = trim($_GET['q'] ?? '');
$page          = max(1, (int)($_GET['page'] ?? 1));
$per_page      = 15;

$where  = [];
$params = [];
if ($filter_status !== 'all') { $where[] = 'status = ?'; $params[] = $filter_status; }
if ($search) { $where[] = '(name LIKE ? OR email LIKE ? OR service_type LIKE ?)'; $params = array_merge($params, ["%$search%","%$search%","%$search%"]); }

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = $pdo->prepare("SELECT COUNT(*) FROM consultations $where_sql");
$total->execute($params);
$total = (int)$total->fetchColumn();
$pg    = paginate($total, $per_page, $page);

$stmt = $pdo->prepare("SELECT * FROM consultations $where_sql ORDER BY created_at DESC LIMIT $per_page OFFSET {$pg['offset']}");
$stmt->execute($params);
$consults = $stmt->fetchAll();

// Status counts
$counts = $pdo->query("SELECT status, COUNT(*) as n FROM consultations GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);

include __DIR__ . '/../includes/dash_header.php';
render_flash('consult');
?>

<div class="dash-page-header">
  <div>
    <h2 class="dash-page-header__title">Consultations</h2>
    <p class="dash-page-header__subtitle"><?= $total ?> total requests</p>
  </div>
</div>

<!-- Status filter tabs -->
<div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:1.5rem;">
  <?php
  $tabs = ['all'=>'All','new'=>'New','contacted'=>'Contacted','converted'=>'Converted','declined'=>'Declined'];
  foreach ($tabs as $val => $label):
    $n = $val === 'all' ? $total : ($counts[$val] ?? 0);
    $active_tab = $filter_status === $val;
  ?>
  <a href="?status=<?= $val ?>&q=<?= urlencode($search) ?>"
     class="btn btn--sm <?= $active_tab ? 'btn--primary' : 'btn--ghost' ?>">
    <?= $label ?>
    <?php if ($n > 0): ?><span style="opacity:0.8;margin-left:2px;">(<?= $n ?>)</span><?php endif; ?>
  </a>
  <?php endforeach; ?>
</div>

<!-- Search -->
<form method="GET" action="" style="margin-bottom:1.5rem;">
  <input type="hidden" name="status" value="<?= e($filter_status) ?>">
  <div class="search-box">
    <i data-lucide="search"></i>
    <input type="text" name="q" placeholder="Search by name, email, or service…" value="<?= e($search) ?>">
    <?php if ($search): ?>
    <a href="?status=<?= e($filter_status) ?>" style="color:var(--text-muted);font-size:0.8rem;">Clear</a>
    <?php endif; ?>
  </div>
</form>

<!-- Table -->
<div class="table-wrap">
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Name</th>
        <th>Service</th>
        <th>Budget</th>
        <th>Timeline</th>
        <th>Status</th>
        <th>Received</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($consults)): ?>
      <tr><td colspan="8" style="text-align:center;padding:3rem;color:var(--text-muted);">No consultations found.</td></tr>
      <?php else: ?>
      <?php foreach ($consults as $c): ?>
      <tr>
        <td style="font-family:monospace;font-size:0.75rem;color:var(--text-muted);"><?= (int)$c['id'] ?></td>
        <td>
          <div style="font-weight:600;font-size:0.9rem;"><?= e($c['name']) ?></div>
          <div style="font-size:0.75rem;color:var(--text-muted);"><?= e($c['email']) ?></div>
          <?php if ($c['whatsapp']): ?>
          <a href="<?= e(whatsapp_link($c['whatsapp'], "Hello {$c['name']}! This is ILLUME following up on your consultation request.")) ?>"
             class="btn btn--sm" style="padding:2px 8px;font-size:0.65rem;color:var(--plasma);border-color:var(--plasma-glass);margin-top:4px;"
             target="_blank" rel="noopener">
            <i data-lucide="message-circle" style="width:10px;height:10px;"></i> WhatsApp
          </a>
          <?php endif; ?>
        </td>
        <td style="font-size:0.85rem;"><?= e($c['service_type'] ?: '—') ?></td>
        <td style="font-size:0.82rem;"><?= e($c['budget_range'] ?: '—') ?></td>
        <td style="font-size:0.82rem;"><?= e($c['timeline'] ?: '—') ?></td>
        <td><?= consultation_status_badge($c['status']) ?></td>
        <td style="font-size:0.78rem;color:var(--text-muted);"><?= time_ago($c['created_at']) ?></td>
        <td>
          <form method="POST" action="" style="display:flex;gap:0.35rem;flex-wrap:wrap;">
            <?= csrf_field() ?>
            <input type="hidden" name="consult_id" value="<?= (int)$c['id'] ?>">
            <?php
            $transitions = ['new'=>['contacted','declined'],'contacted'=>['converted','declined'],'converted'=>[],'declined'=>['new']];
            foreach ($transitions[$c['status']] ?? [] as $next):
              $btnClass = match($next) { 'converted'=>'btn--plasma','declined'=>'btn--danger', default=>'btn--ghost' };
            ?>
            <button name="status" value="<?= $next ?>" class="btn btn--sm <?= $btnClass ?>">
              <?= ucfirst($next) ?>
            </button>
            <?php endforeach; ?>

            <?php if ($c['status'] !== 'converted'): ?>
            <a href="create-order.php?consult_id=<?= (int)$c['id'] ?>" class="btn btn--plasma btn--sm">
              <i data-lucide="plus-circle" style="width:13px;height:13px;"></i> Convert
            </a>
            <?php endif; ?>
            <?php if ($c['message']): ?>
            <button type="button" class="btn btn--ghost btn--sm" data-modal-open="msg-<?= (int)$c['id'] ?>">
              <i data-lucide="eye" style="width:13px;height:13px;"></i>
            </button>
            <?php endif; ?>
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

<!-- Message modals -->
<?php foreach ($consults as $c): if (!$c['message']) continue; ?>
<div class="modal-backdrop" id="msg-<?= (int)$c['id'] ?>">
  <div class="modal">
    <div class="modal__header">
      <h4>Message from <?= e($c['name']) ?></h4>
      <button class="btn btn--icon btn--ghost" data-modal-close><i data-lucide="x"></i></button>
    </div>
    <div class="modal__body">
      <div style="background:var(--space-mid);border-radius:var(--r);padding:1.25rem;font-size:0.9rem;line-height:1.75;color:var(--text-secondary);">
        <?= nl2br(e($c['message'])) ?>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>

<?php include __DIR__ . '/../includes/dash_footer.php'; ?>
