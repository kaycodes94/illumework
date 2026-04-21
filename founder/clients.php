<?php
// ============================================================
// ILLUME — Founder: Client Directory
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_founder();

$dash_title = 'Clients';
$active_nav = 'clients';
$pdo        = db();
$search     = trim($_GET['q'] ?? '');
$page       = max(1, (int)($_GET['page'] ?? 1));
$per_page   = 15;

$where  = ["u.role = 'client'"];
$params = [];
if ($search) {
    $where[] = "(u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
    $params  = ["%$search%", "%$search%", "%$search%"];
}
$where_sql = 'WHERE ' . implode(' AND ', $where);

$total = $pdo->prepare("SELECT COUNT(*) FROM users u $where_sql");
$total->execute($params);
$total = (int)$total->fetchColumn();
$pg    = paginate($total, $per_page, $page);

$stmt = $pdo->prepare("
    SELECT u.*,
        (SELECT COUNT(*) FROM orders o WHERE o.client_id = u.id) AS order_count,
        (SELECT COUNT(*) FROM consultations c WHERE c.email = u.email) AS consult_count,
        (SELECT MAX(o2.created_at) FROM orders o2 WHERE o2.client_id = u.id) AS last_order
    FROM users u
    $where_sql
    ORDER BY u.created_at DESC
    LIMIT $per_page OFFSET {$pg['offset']}
");
$stmt->execute($params);
$clients = $stmt->fetchAll();

include __DIR__ . '/../includes/dash_header.php';
?>

<div class="dash-page-header">
  <div>
    <h2 class="dash-page-header__title">Client Directory</h2>
    <p class="dash-page-header__subtitle"><?= $total ?> registered clients</p>
  </div>
  <a href="<?= SITE_URL ?>/auth/register.php" class="btn btn--primary btn--sm">
    <i data-lucide="user-plus"></i> Add Client
  </a>
</div>

<!-- Search -->
<form method="GET" style="margin-bottom:1.5rem;">
  <div class="search-box">
    <i data-lucide="search"></i>
    <input type="text" name="q" placeholder="Search by name, email, or phone…" value="<?= e($search) ?>">
    <?php if ($search): ?><a href="?" style="color:var(--text-muted);font-size:0.8rem;">Clear</a><?php endif; ?>
  </div>
</form>

<!-- Client Grid -->
<?php if (empty($clients)): ?>
<div class="glass" style="padding:3rem;text-align:center;">
  <i data-lucide="users" style="width:48px;height:48px;color:var(--text-muted);display:block;margin:0 auto 1rem;"></i>
  <p style="color:var(--text-muted);">No clients found.</p>
</div>
<?php else: ?>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1rem;">
  <?php foreach ($clients as $cl): ?>
  <?php
  $initials = implode('', array_map(fn($w) => strtoupper($w[0]), array_slice(explode(' ', $cl['name']), 0, 2)));
  ?>
  <div class="glass" style="padding:1.5rem;border-radius:var(--r-xl);transition:all 0.3s;" onmouseenter="this.style.borderColor='var(--gold-glass)'" onmouseleave="this.style.borderColor=''">
    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
      <div style="
        width:48px;height:48px;border-radius:50%;flex-shrink:0;
        background:linear-gradient(135deg,var(--gold),var(--gold-bright));
        display:flex;align-items:center;justify-content:center;
        font-family:var(--font-display);font-weight:700;font-size:1rem;color:var(--void);
      "><?= e($initials) ?></div>
      <div style="min-width:0;">
        <div style="font-weight:600;font-size:0.95rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= e($cl['name']) ?></div>
        <div style="font-size:0.75rem;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= e($cl['email']) ?></div>
      </div>
      <span class="badge <?= $cl['status']==='active' ? 'badge--success' : 'badge--error' ?>" style="margin-left:auto;flex-shrink:0;">
        <?= ucfirst($cl['status']) ?>
      </span>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;margin-bottom:1rem;">
      <div style="background:var(--space);border-radius:var(--r);padding:0.6rem;text-align:center;">
        <div style="font-size:1.2rem;font-weight:700;color:var(--gold);"><?= (int)$cl['order_count'] ?></div>
        <div style="font-size:0.65rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--text-muted);">Orders</div>
      </div>
      <div style="background:var(--space);border-radius:var(--r);padding:0.6rem;text-align:center;">
        <div style="font-size:1.2rem;font-weight:700;color:var(--plasma);"><?= (int)$cl['consult_count'] ?></div>
        <div style="font-size:0.65rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--text-muted);">Consults</div>
      </div>
    </div>

    <?php if ($cl['last_order']): ?>
    <div style="font-size:0.75rem;color:var(--text-muted);margin-bottom:1rem;">
      Last order: <?= time_ago($cl['last_order']) ?>
    </div>
    <?php endif; ?>

    <div style="display:flex;gap:0.5rem;">
      <?php if ($cl['whatsapp'] || $cl['phone']): ?>
      <a href="<?= e(whatsapp_link($cl['whatsapp'] ?: $cl['phone'], "Hello {$cl['name']}! This is ILLUME.")) ?>"
         class="btn btn--ghost btn--sm" style="flex:1;justify-content:center;" target="_blank" rel="noopener">
        <i data-lucide="message-circle" style="width:14px;height:14px;"></i> WhatsApp
      </a>
      <?php endif; ?>
      <a href="mailto:<?= e($cl['email']) ?>" class="btn btn--ghost btn--sm" style="flex:1;justify-content:center;">
        <i data-lucide="mail" style="width:14px;height:14px;"></i> Email
      </a>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Pagination -->
<?php if ($pg['total_pages'] > 1): ?>
<div style="display:flex;justify-content:center;gap:0.5rem;margin-top:2rem;">
  <?php if ($pg['has_prev']): ?><a href="?q=<?= urlencode($search) ?>&page=<?= $pg['current']-1 ?>" class="btn btn--ghost btn--sm">← Prev</a><?php endif; ?>
  <span style="padding:0.4rem 0.75rem;font-size:0.85rem;color:var(--text-muted);"><?= $pg['current'] ?> / <?= $pg['total_pages'] ?></span>
  <?php if ($pg['has_next']): ?><a href="?q=<?= urlencode($search) ?>&page=<?= $pg['current']+1 ?>" class="btn btn--ghost btn--sm">Next →</a><?php endif; ?>
</div>
<?php endif; ?>
<?php endif; ?>

<?php include __DIR__ . '/../includes/dash_footer.php'; ?>
