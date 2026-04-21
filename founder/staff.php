<?php
// ============================================================
// ILLUME — Founder: Staff Management
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_founder();

$dash_title = 'Staff';
$active_nav = 'staff';
$pdo        = db();
$msg        = '';

// Toggle active status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf() && isset($_POST['toggle_status'])) {
    $uid    = (int)$_POST['user_id'];
    $status = $_POST['current_status'] === 'active' ? 'inactive' : 'active';
    $pdo->prepare("UPDATE users SET status=? WHERE id=? AND role IN ('staff','founder')")->execute([$status,$uid]);
    flash('staff','Status updated.','success');
    header('Location: ' . SITE_URL . '/founder/staff.php'); exit;
}

$staff = $pdo->query("
    SELECT u.*,
        (SELECT COUNT(*) FROM orders o WHERE o.assigned_staff_id=u.id) AS order_count,
        (SELECT COUNT(*) FROM orders o WHERE o.assigned_staff_id=u.id AND o.status NOT IN ('complete','cancelled')) AS active_orders
    FROM users u
    WHERE u.role IN ('staff','founder')
    ORDER BY u.role DESC, u.name ASC
")->fetchAll();

include __DIR__ . '/../includes/dash_header.php';
render_flash('staff');
?>

<div class="dash-page-header">
  <div>
    <h2 class="dash-page-header__title">Staff Management</h2>
    <p class="dash-page-header__subtitle"><?= count($staff) ?> team member<?= count($staff) !== 1 ? 's' : '' ?></p>
  </div>
  <a href="<?= SITE_URL ?>/auth/register.php?role=staff" class="btn btn--primary btn--sm">
    <i data-lucide="user-plus"></i> Add Staff
  </a>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1rem;">
  <?php foreach ($staff as $s): ?>
  <?php $initials = implode('',array_map(fn($w)=>strtoupper($w[0]),array_slice(explode(' ',$s['name']),0,2))); ?>
  <div class="glass" style="padding:1.5rem;border-radius:var(--r-xl);">
    <!-- Header -->
    <div style="display:flex;align-items:center;gap:0.875rem;margin-bottom:1.25rem;">
      <div style="
        width:52px;height:52px;border-radius:50%;flex-shrink:0;
        background:<?= $s['role']==='founder' ? 'linear-gradient(135deg,var(--gold),var(--gold-bright))' : 'var(--space-light)' ?>;
        border:<?= $s['role']==='founder' ? 'none' : '1px solid var(--space-border)' ?>;
        display:flex;align-items:center;justify-content:center;
        font-family:var(--font-display);font-weight:700;font-size:1.1rem;
        color:<?= $s['role']==='founder' ? 'var(--void)' : 'var(--text-secondary)' ?>;
      "><?= e($initials) ?></div>
      <div style="flex:1;min-width:0;">
        <div style="font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= e($s['name']) ?></div>
        <div style="font-size:0.75rem;color:var(--text-muted);"><?= e($s['email']) ?></div>
      </div>
      <div style="display:flex;flex-direction:column;align-items:flex-end;gap:0.35rem;flex-shrink:0;">
        <span class="badge <?= $s['role']==='founder' ? 'badge--gold' : 'badge--plasma' ?>">
          <?= ucfirst($s['role']) ?>
        </span>
        <span class="badge <?= $s['status']==='active' ? 'badge--success' : 'badge--error' ?>">
          <?= ucfirst($s['status']) ?>
        </span>
      </div>
    </div>

    <!-- Stats -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;margin-bottom:1.25rem;">
      <div style="background:var(--space);border-radius:var(--r);padding:0.6rem;text-align:center;">
        <div style="font-size:1.3rem;font-weight:700;color:var(--gold);"><?= (int)$s['active_orders'] ?></div>
        <div style="font-size:0.6rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--text-muted);">Active Orders</div>
      </div>
      <div style="background:var(--space);border-radius:var(--r);padding:0.6rem;text-align:center;">
        <div style="font-size:1.3rem;font-weight:700;color:var(--text-secondary);"><?= (int)$s['order_count'] ?></div>
        <div style="font-size:0.6rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--text-muted);">Total Orders</div>
      </div>
    </div>

    <?php if ($s['last_login']): ?>
    <div style="font-size:0.75rem;color:var(--text-muted);margin-bottom:1rem;">
      Last login: <?= time_ago($s['last_login']) ?>
    </div>
    <?php endif; ?>

    <!-- Actions (only founder can toggle staff) -->
    <?php if ($s['role'] !== 'founder'): ?>
    <form method="POST" action="">
      <?= csrf_field() ?>
      <input type="hidden" name="user_id" value="<?= (int)$s['id'] ?>">
      <input type="hidden" name="current_status" value="<?= e($s['status']) ?>">
      <button type="submit" name="toggle_status" value="1"
              class="btn btn--sm btn--w-full <?= $s['status']==='active' ? 'btn--danger' : 'btn--ghost' ?>">
        <i data-lucide="<?= $s['status']==='active' ? 'user-x' : 'user-check' ?>" style="width:14px;height:14px;"></i>
        <?= $s['status']==='active' ? 'Deactivate' : 'Activate' ?>
      </button>
    </form>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
</div>

<?php include __DIR__ . '/../includes/dash_footer.php'; ?>
