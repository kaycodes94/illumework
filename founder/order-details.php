<?php
// ============================================================
// ILLUME — Founder: Order Details
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_founder();

$order_id = (int)($_GET['id'] ?? 0);
$pdo      = db();

$stmt = $pdo->prepare("
    SELECT o.*, uc.name AS client_name, uc.email AS client_email, us.name AS staff_name
    FROM orders o
    JOIN users uc ON o.client_id = uc.id
    LEFT JOIN users us ON o.assigned_staff_id = us.id
    WHERE o.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    flash('order', 'Order not found.', 'error');
    header('Location: ' . SITE_URL . '/founder/orders.php');
    exit;
}

$timeline    = get_order_timeline($order_id);
$submissions = get_design_submissions($order_id);

$dash_title = 'Order ' . $order['order_ref'];
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
        <a href="<?= e(whatsapp_link(WHATSAPP_NUMBER, "Update on Order {$order['order_ref']}: ")) ?>" target="_blank" class="btn btn--ghost btn--sm">
            <i data-lucide="message-circle"></i> WhatsApp Client
        </a>
        <button class="btn btn--primary btn--sm" data-modal-open="status-modal">
            Update Status
        </button>
    </div>
</div>

<div class="grid-2" style="grid-template-columns: 2fr 1fr; gap: 2rem;">
    
    <div class="flex-col gap-8">
        
        <!-- Progress Track -->
        <div class="order-hero">
            <div style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--text-muted);margin-bottom:1.5rem;">Visual Identity Journey</div>
            <?php
            $statuses = ['intake','design','approval','production','delivery','complete'];
            $currentIdx = array_search($order['status'], $statuses);
            ?>
            <div class="order-status-track">
                <?php foreach ($statuses as $si => $s): ?>
                <div class="status-step <?= $si < $currentIdx ? 'done' : ($si === $currentIdx ? 'current' : '') ?>">
                    <div class="status-dot">
                        <?php if ($si < $currentIdx): ?><i data-lucide="check" style="width:12px;height:12px;"></i><?php endif; ?>
                    </div>
                    <span class="status-step-label"><?= ucfirst($s) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Design Submissions -->
        <section>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
                <h3 style="font-size:1.2rem;font-family:var(--font-display);">Design Solutions</h3>
                <span class="label-text" style="font-size:0.6rem;"><?= count($submissions) ?> Submissions</span>
            </div>
            
            <?php if (empty($submissions)): ?>
            <div class="glass" style="padding:3rem;text-align:center;border-radius:var(--r-xl);">
                <i data-lucide="image" style="width:32px;color:var(--text-muted);margin-bottom:1rem;"></i>
                <p style="font-size:0.88rem;color:var(--text-muted);">No designs submitted yet.</p>
            </div>
            <?php else: ?>
            <div class="design-cards">
                <?php foreach ($submissions as $ds): ?>
                <div class="design-card">
                    <img src="<?= SITE_URL . '/assets/uploads/' . e($ds['file_path']) ?>" class="design-card__img" alt="Design">
                    <div class="design-card__body">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
                            <span class="badge <?= $ds['status']==='approved'?'badge--success':($ds['status']==='rejected'?'badge--error':'badge--warning') ?>" style="font-size:0.6rem;">
                                <?= ucfirst($ds['status']) ?>
                            </span>
                            <span style="font-size:0.7rem;color:var(--text-muted);"><?= format_date($ds['created_at']) ?></span>
                        </div>
                        <div style="font-weight:600;font-size:0.88rem;margin-bottom:0.25rem;"><?= e($ds['version_name'] ?: 'Submission') ?></div>
                        <?php if ($ds['feedback']): ?>
                        <div style="font-size:0.8rem;color:var(--text-secondary);background:var(--void);padding:0.5rem;border-radius:var(--r-sm);margin-top:0.5rem;">
                            <strong>Feedback:</strong> <?= e($ds['feedback']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>

        <!-- Order Information Table -->
        <div class="card" style="padding: 1.5rem;">
             <h3 style="font-size:1.1rem;margin-bottom:1.25rem;">Project Parameters</h3>
             <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
                 <div>
                     <div class="kpi-label">Client</div>
                     <div style="font-weight:600;"><?= e($order['client_name']) ?></div>
                     <div style="font-size:0.8rem;color:var(--text-muted);"><?= e($order['client_email']) ?></div>
                 </div>
                 <div>
                     <div class="kpi-label">Project Manager</div>
                     <div style="font-weight:600;"><?= e($order['staff_name'] ?: 'Not Assigned') ?></div>
                 </div>
                 <div>
                     <div class="kpi-label">Budget</div>
                     <div style="font-weight:600;color:var(--gold);"><?= format_currency((float)$order['budget'],$order['currency']) ?></div>
                 </div>
                 <div>
                     <div class="kpi-label">Delivery Date</div>
                     <div style="font-weight:600;"><?= $order['deadline'] ? format_date($order['deadline']) : 'TBD' ?></div>
                 </div>
             </div>
             <div style="margin-top:1.5rem;border-top:1px solid var(--space-border);padding-top:1.25rem;">
                 <div class="kpi-label">Description / Scope</div>
                 <div style="font-size:0.9rem;color:var(--text-secondary);line-height:1.6;"><?= nl2br(e($order['description'])) ?></div>
             </div>
        </div>
    </div>

    <!-- Timeline Sidebar -->
    <div class="flex-col gap-6">
        <div class="card--glass" style="padding: 1.5rem;">
            <h3 style="font-size:1.1rem;margin-bottom:1.5rem;font-family:var(--font-display);">Event Log</h3>
            <div class="timeline">
                <?php foreach ($timeline as $t): ?>
                <div class="timeline-item">
                    <div class="timeline-time"><?= time_ago($t['created_at']) ?></div>
                    <div class="timeline-action">
                        <?php
                        $actions = [
                            'status_update'    => 'Status Change',
                            'order_created'    => 'Order Initiated',
                            'design_upload'    => 'Design Uploaded',
                            'design_approved'  => 'Design Approved',
                            'design_rejected'  => 'Revision Requested',
                            'note_added'       => 'Note Added'
                        ];
                        echo $actions[$t['action']] ?? ucfirst($t['action']);
                        ?>
                    </div>
                    <div class="timeline-note"><?= e($t['note']) ?></div>
                    <div style="font-size:0.65rem;color:var(--gold);margin-top:0.25rem;">by <?= e($t['actor_name']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal-backdrop" id="status-modal">
    <div class="modal">
        <form method="POST" action="orders.php">
            <?= csrf_field() ?>
            <input type="hidden" name="order_id" value="<?= (int)$order_id ?>">
            <input type="hidden" name="update_status" value="1">
            <div class="modal__header">
                <h4>Modify Order Status</h4>
                <button type="button" class="btn btn--icon btn--ghost" data-modal-close><i data-lucide="x"></i></button>
            </div>
            <div class="modal__body">
                <div class="form-group">
                    <label class="form-label">New Phase</label>
                    <select name="status" class="form-select">
                        <?php foreach (['intake','design','approval','production','delivery','complete','cancelled'] as $s): ?>
                        <option value="<?= $s ?>" <?= $order['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Internal Note (Optional)</label>
                    <textarea name="note" class="form-textarea" placeholder="Describe the reason for this change..."></textarea>
                </div>
            </div>
            <div class="modal__footer">
                <button type="button" class="btn btn--ghost" data-modal-close>Cancel</button>
                <button type="submit" class="btn btn--primary">Update Order</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/dash_footer.php'; ?>
