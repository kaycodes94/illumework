<?php
// ============================================================
// ILLUME — Staff: Order Details
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_staff_or_founder();

$order_id = (int)($_GET['id'] ?? 0);
$pdo      = db();
$uid      = current_user_id();

// Staff can only see orders assigned to them, Founders see all.
$sql = "
    SELECT o.*, uc.name AS client_name, uc.email AS client_email, us.name AS staff_name
    FROM orders o
    JOIN users uc ON o.client_id = uc.id
    LEFT JOIN users us ON o.assigned_staff_id = us.id
    WHERE o.id = ?
";
if (!is_founder()) {
    $sql .= " AND o.assigned_staff_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$order_id, $uid]);
} else {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$order_id]);
}

$order = $stmt->fetch();

if (!$order) {
    flash('order', 'Order not found or access denied.', 'error');
    header('Location: ' . SITE_URL . '/staff/orders.php');
    exit;
}

$timeline    = get_order_timeline($order_id);
$submissions = get_design_submissions($order_id);

$dash_title = 'Track ' . $order['order_ref'];
$active_nav = 'orders';
include __DIR__ . '/../includes/dash_header.php';
?>

<div class="dash-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.5rem;">
            <a href="orders.php" style="color:var(--text-muted);"><i data-lucide="arrow-left" style="width:18px;"></i></a>
            <span class="label-text" style="color:var(--gold);"><?= e($order['order_ref']) ?></span>
            <?= order_status_badge($order['status']) ?>
        </div>
        <h2 class="dash-page-header__title"><?= e($order['title']) ?></h2>
    </div>
    <div style="display:flex;gap:0.75rem;">
        <a href="upload-design.php?order=<?= $order_id ?>" class="btn btn--primary btn--sm">
            <i data-lucide="upload"></i> Upload Design
        </a>
        <button class="btn btn--ghost btn--sm" data-modal-open="status-modal">
            Update Status
        </button>
    </div>
</div>

<div class="grid-2" style="grid-template-columns: 2fr 1fr; gap: 2rem;">
    
    <div class="flex-col gap-8">
        
        <!-- Status Track -->
        <div class="order-hero" style="background:var(--space-mid);">
            <?php
            $statuses = ['intake','design','approval','production','delivery','complete'];
            $currentIdx = array_search($order['status'], $statuses);
            ?>
            <div class="order-status-track">
                <?php foreach ($statuses as $si => $s): ?>
                <div class="status-step <?= $si < $currentIdx ? 'done' : ($si === $currentIdx ? 'current' : '') ?>">
                    <div class="status-dot"></div>
                    <span class="status-step-label"><?= ucfirst($s) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Designs -->
        <section>
            <h3 style="font-size:1.1rem;margin-bottom:1.5rem;">Design Submissions</h3>
            <?php if (empty($submissions)): ?>
            <div class="glass" style="padding:2.5rem;text-align:center;">
                <p style="color:var(--text-muted);font-size:0.88rem;">No designs have been uploaded for this project.</p>
            </div>
            <?php else: ?>
            <div class="design-cards">
                <?php foreach ($submissions as $ds): ?>
                <div class="design-card">
                    <img src="<?= SITE_URL ?>/assets/uploads/<?= e($ds['file_path']) ?>" class="design-card__img" alt="Design">
                    <div class="design-card__body">
                        <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem;">
                             <span class="badge <?= $ds['status']==='approved'?'badge--success':($ds['status']==='rejected'?'badge--error':'badge--warning') ?>">
                                <?= ucfirst($ds['status']) ?>
                             </span>
                             <span style="font-size:0.7rem;color:var(--text-muted);"><?= format_date($ds['created_at']) ?></span>
                        </div>
                        <div style="font-weight:600;font-size:0.88rem;"><?= e($ds['version_name']) ?></div>
                        <?php if ($ds['feedback']): ?>
                        <p style="font-size:0.78rem;color:var(--danger);margin-top:0.5rem;background:rgba(255,76,106,0.05);padding:0.5rem;border-radius:4px;">
                            <strong>Revision Request:</strong> <?= e($ds['feedback']) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>

        <!-- Client Info -->
        <div class="card" style="padding:1.5rem;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div>
                    <div class="kpi-label">Client</div>
                    <div style="font-weight:600;"><?= e($order['client_name']) ?></div>
                    <div style="font-size:0.8rem;color:var(--text-muted);"><?= e($order['client_email']) ?></div>
                </div>
                <div>
                    <div class="kpi-label">Deadline</div>
                    <div style="font-weight:600;"><?= $order['deadline'] ? format_date($order['deadline']) : 'No Deadline' ?></div>
                </div>
            </div>
            <div style="margin-top:1.5rem;">
                <div class="kpi-label">Requirements</div>
                <p style="font-size:0.88rem;color:var(--text-secondary);"><?= nl2br(e($order['description'])) ?></p>
            </div>
        </div>
    </div>

    <!-- Timeline Sidebar -->
    <div class="card--glass" style="padding: 1.5rem;">
        <h3 style="font-size:1rem;margin-bottom:1.5rem;">Timeline</h3>
        <div class="timeline">
            <?php foreach ($timeline as $t): ?>
            <div class="timeline-item">
                <div class="timeline-time"><?= time_ago($t['created_at']) ?></div>
                <div class="timeline-action">
                    <?php
                    $actMap = ['status_update'=>'Status Transformed','order_created'=>'Project Start','design_upload'=>'Design Uploaded','design_approved'=>'Client Approved','design_rejected'=>'Revision Needed'];
                    echo $actMap[$t['action']] ?? ucfirst($t['action']);
                    ?>
                </div>
                <div class="timeline-note"><?= e($t['note']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>

<!-- Status Modal (Copied from founder for consistency, maybe restricted later) -->
<div class="modal-backdrop" id="status-modal">
    <div class="modal">
        <form method="POST" action="../founder/orders.php"> <?php /* We reuse the founder handler if it's there, but staff can't normally POST there. Let's redirect to a shared handler or just handle here. */ ?>
            <?= csrf_field() ?>
            <input type="hidden" name="order_id" value="<?= (int)$order_id ?>">
            <input type="hidden" name="update_status" value="1">
            <div class="modal__header">
                <h4>Update Phase</h4>
                <button type="button" class="btn btn--icon btn--ghost" data-modal-close><i data-lucide="x"></i></button>
            </div>
            <div class="modal__body">
                <div class="form-group">
                    <label class="form-label">Next Phase</label>
                    <select name="status" class="form-select">
                        <?php foreach (['intake','design','approval','production','delivery','complete','cancelled'] as $s): ?>
                        <option value="<?= $s ?>" <?= $order['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal__footer">
                <button type="submit" class="btn btn--primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/dash_footer.php'; ?>
