<?php
// ============================================================
// ILLUME — Client Dashboard
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_client();

$dash_title = 'My Dashboard';
$active_nav = 'dashboard';
$pdo        = db();
$uid        = current_user_id();

// My orders
$orders = $pdo->prepare("
    SELECT o.*, u.name AS staff_name
    FROM orders o
    LEFT JOIN users u ON o.assigned_staff_id = u.id
    WHERE o.client_id = ?
    ORDER BY o.updated_at DESC
");
$orders->execute([$uid]);
$orders = $orders->fetchAll();

// Pending approvals
$pending = $pdo->prepare("
    SELECT ds.*, o.title AS order_title
    FROM design_submissions ds
    JOIN orders o ON ds.order_id = o.id
    WHERE o.client_id = ? AND ds.approval_status = 'pending'
    ORDER BY ds.created_at DESC
");
$pending->execute([$uid]);
$pending = $pending->fetchAll();

include __DIR__ . '/../includes/dash_header.php';
?>

<!-- ── Page Header ──────────────────────────────────────── -->
<div class="dash-page-header">
  <div>
    <h2 class="dash-page-header__title">Welcome back, <?= e(explode(' ', $user['name'])[0]) ?> ✦</h2>
    <p class="dash-page-header__subtitle"><?= date('l, F j, Y') ?> · Your ILLUME journey</p>
  </div>
  <a href="<?= SITE_URL ?>/consultation.php" class="btn btn--primary btn--sm">
    <i data-lucide="plus"></i> New Consultation
  </a>
</div>

<!-- ── Pending approval alert ───────────────────────────── -->
<?php if (!empty($pending)): ?>
<div class="alert alert--info" style="margin-bottom:1.5rem;">
  <i data-lucide="eye"></i>
  <span>
    You have <strong><?= count($pending) ?> design<?= count($pending) > 1 ? 's' : '' ?></strong> awaiting your review and approval.
    <a href="#approvals" style="color:var(--plasma);text-decoration:underline;margin-left:0.5rem;">Review now →</a>
  </span>
</div>
<?php endif; ?>

<!-- ── My Orders ─────────────────────────────────────────── -->
<div style="margin-bottom:2rem;">
  <h4 style="margin-bottom:1rem;">My Orders</h4>

  <?php if (empty($orders)): ?>
  <div class="glass" style="padding:3rem;text-align:center;">
    <i data-lucide="package" style="width:48px;height:48px;color:var(--text-muted);margin-bottom:1rem;"></i>
    <p style="color:var(--text-muted);margin-bottom:1.5rem;">You don't have any orders yet.</p>
    <a href="<?= SITE_URL ?>/consultation.php" class="btn btn--primary">
      <i data-lucide="calendar"></i> Book Your First Consultation
    </a>
  </div>
  <?php else: ?>
  <div style="display:flex;flex-direction:column;gap:1rem;">
    <?php foreach ($orders as $order): ?>
    <a href="order-details.php?id=<?= (int)$order['id'] ?>" class="glass" style="padding:1.5rem;border-radius:var(--r-xl);transition:all 0.3s;display:block;text-decoration:none;" onmouseenter="this.style.borderColor='var(--gold-glass)'" onmouseleave="this.style.borderColor=''">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1rem;">
        <div>
          <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.3rem;">
            <span style="font-family:monospace;font-size:0.75rem;color:var(--gold);"><?= e($order['order_ref']) ?></span>
            <?= order_status_badge($order['status']) ?>
          </div>
          <h5 style="font-size:1rem;margin-bottom:0.25rem;color:var(--text-primary);"><?= e($order['title']) ?></h5>
          <span style="font-size:0.82rem;color:var(--text-muted);"><?= e($order['service_type']) ?></span>
        </div>
        <div style="text-align:right;">
          <?php if ($order['budget']): ?>
          <div style="font-size:1.1rem;font-weight:700;color:var(--gold);"><?= format_currency((float)$order['budget'], $order['currency']) ?></div>
          <?php endif; ?>
          <?php if ($order['deadline']): ?>
          <div style="font-size:0.78rem;color:var(--text-muted);margin-top:0.25rem;">
            Due: <?= format_date($order['deadline']) ?>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Order status track -->
      <?php
      $statuses = ['intake','design','approval','production','delivery','complete'];
      $currentIdx = array_search($order['status'], $statuses);
      ?>
      <div class="order-status-track">
        <?php foreach ($statuses as $si => $s): ?>
        <div class="status-step <?= $si < $currentIdx ? 'done' : ($si === $currentIdx ? 'current' : '') ?>">
          <div class="status-dot">
            <?php if ($si < $currentIdx): ?>
            <i data-lucide="check" style="width:12px;height:12px;"></i>
            <?php endif; ?>
          </div>
          <span class="status-step-label"><?= ucfirst($s) ?></span>
        </div>
        <?php endforeach; ?>
      </div>

      <?php if ($order['staff_name']): ?>
      <div style="font-size:0.78rem;color:var(--text-muted);margin-top:0.75rem;">
        <i data-lucide="user" style="width:12px;height:12px;display:inline;"></i>
        Assigned to: <?= e($order['staff_name']) ?>
      </div>
      <?php endif; ?>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- ── Design Approvals ──────────────────────────────────── -->
<?php if (!empty($pending)): ?>
<div id="approvals">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
    <h4>Designs Awaiting Your Approval</h4>
    <span class="badge badge--warning"><?= count($pending) ?> pending</span>
  </div>
  <div class="design-cards">
    <?php foreach ($pending as $d): ?>
    <div class="design-card">
      <div class="design-card__img" style="
        background:linear-gradient(135deg,var(--space-mid),rgba(201,168,76,0.08));
        display:flex;align-items:center;justify-content:center;
      ">
        <i data-lucide="image" style="width:40px;height:40px;color:var(--text-muted);"></i>
      </div>
      <div class="design-card__body">
        <div style="font-size:0.7rem;color:var(--text-muted);margin-bottom:0.25rem;"><?= e($d['order_title']) ?></div>
        <div style="font-weight:600;font-size:0.9rem;margin-bottom:0.5rem;"><?= e($d['title'] ?: 'Design Submission') ?></div>
        <p style="font-size:0.8rem;line-height:1.5;"><?= e($d['notes'] ?: 'No notes provided.') ?></p>
      </div>
      <div class="design-card__actions">
        <form method="POST" action="<?= SITE_URL ?>/api/design.php" style="display:contents;">
          <?= csrf_field() ?>
          <input type="hidden" name="submission_id" value="<?= (int)$d['id'] ?>">
          <button type="submit" name="action" value="approve" class="btn btn--plasma btn--sm btn--w-full">
            <i data-lucide="check-circle"></i> Approve
          </button>
        </form>
        <form method="POST" action="<?= SITE_URL ?>/api/design.php" style="display:contents;">
          <?= csrf_field() ?>
          <input type="hidden" name="submission_id" value="<?= (int)$d['id'] ?>">
          <button type="submit" name="action" value="revision" class="btn btn--ghost btn--sm btn--w-full">
            <i data-lucide="edit-3"></i> Request Revision
          </button>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- ── Quick Actions ─────────────────────────────────────── -->
<div style="margin-top:2rem;display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;">
  <a href="<?= SITE_URL ?>/consultation.php" class="glass" style="padding:1.25rem;text-align:center;border-radius:var(--r-xl);text-decoration:none;transition:all 0.3s;" onmouseenter="this.style.borderColor='var(--gold-glass)'" onmouseleave="this.style.borderColor=''">
    <i data-lucide="calendar" style="width:24px;height:24px;color:var(--gold);display:block;margin:0 auto 0.5rem;"></i>
    <div style="font-size:0.85rem;font-weight:600;">New Consultation</div>
  </a>
  <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>" class="glass" style="padding:1.25rem;text-align:center;border-radius:var(--r-xl);text-decoration:none;transition:all 0.3s;" target="_blank" rel="noopener" onmouseenter="this.style.borderColor='var(--plasma-glass)'" onmouseleave="this.style.borderColor=''">
    <i data-lucide="message-circle" style="width:24px;height:24px;color:var(--plasma);display:block;margin:0 auto 0.5rem;"></i>
    <div style="font-size:0.85rem;font-weight:600;">WhatsApp ILLUME</div>
  </a>
  <a href="<?= SITE_URL ?>/client/profile.php" class="glass" style="padding:1.25rem;text-align:center;border-radius:var(--r-xl);text-decoration:none;transition:all 0.3s;" onmouseenter="this.style.borderColor='var(--gold-glass)'" onmouseleave="this.style.borderColor=''">
    <i data-lucide="user" style="width:24px;height:24px;color:var(--gold);display:block;margin:0 auto 0.5rem;"></i>
    <div style="font-size:0.85rem;font-weight:600;">My Profile</div>
  </a>
</div>

<?php include __DIR__ . '/../includes/dash_footer.php'; ?>
