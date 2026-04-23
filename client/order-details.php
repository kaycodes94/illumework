<?php
// ============================================================
// ILLUME — Client: Order Details & Design Approval
// ============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_client();

$order_id = (int)($_GET['id'] ?? 0);
$pdo      = db();
$uid      = current_user_id();

// Verify ownership
$stmt = $pdo->prepare("
    SELECT o.*, us.name AS staff_name
    FROM orders o
    LEFT JOIN users us ON o.assigned_staff_id = us.id
    WHERE o.id = ? AND o.client_id = ?
");
$stmt->execute([$order_id, $uid]);
$order = $stmt->fetch();

if (!$order) {
    flash('order', 'Order not found.', 'error');
    header('Location: ' . SITE_URL . '/client/orders.php');
    exit;
}

$timeline    = get_order_timeline($order_id);
$submissions = get_design_submissions($order_id);

$dash_title = 'Track Order: ' . $order['order_ref'];
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
    <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>?text=Hello%20ILLUME!%20I%20have%20a%20question%20about%20order%20<?= e($order['order_ref']) ?>." target="_blank" class="btn btn--secondary btn--sm">
        <i data-lucide="message-circle"></i> Chat Support
    </a>
</div>

<div class="grid-2" style="grid-template-columns: 2fr 1fr; gap: 2rem;">
    
    <div class="flex-col gap-8">
        
        <!-- Status Track -->
        <div class="order-hero" style="background:var(--void);box-shadow:inset 0 0 40px rgba(0,0,0,0.01);">
            <div style="font-size:0.72rem;letter-spacing:0.1em;text-transform:uppercase;color:var(--text-muted);margin-bottom:1.5rem;">Production Status</div>
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

        <!-- Design Approval Section -->
        <section>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
                <h3 style="font-size:1.2rem;font-family:var(--font-display);">Design Solutions</h3>
                <?php if ($order['status'] === 'approval'): ?>
                <span class="badge badge--aura animate-pulse">Decision Required</span>
                <?php endif; ?>
            </div>

            <?php if (empty($submissions)): ?>
            <div class="glass" style="padding:4rem;text-align:center;border-radius:var(--r-xl);">
                <i data-lucide="pen-tool" style="width:42px;height:42px;color:var(--text-muted);display:block;margin:0 auto 1.25rem;"></i>
                <h4 style="margin-bottom:0.5rem;">Design in Progress</h4>
                <p style="color:var(--text-muted);font-size:0.88rem;">Our master designers are currently drafting your piece. You'll be notified when the first concept is ready for review.</p>
            </div>
            <?php else: ?>
            <div class="design-cards">
                <?php foreach ($submissions as $ds): ?>
                <div class="card--glass" style="padding:0;overflow:hidden;border-radius:var(--r-xl);">
                    <div style="aspect-ratio:16/9;background:var(--space-mid);position:relative;">
                        <img src="<?= SITE_URL ?>/assets/uploads/<?= e($ds['file_path']) ?>" style="width:100%;height:100%;object-fit:cover;" alt="Design Concept">
                        <div style="position:absolute;top:1rem;right:1rem;">
                            <span class="badge <?= $ds['status']==='approved'?'badge--success':($ds['status']==='rejected'?'badge--error':'badge--warning') ?>">
                                <?= ucfirst($ds['status']) ?>
                            </span>
                        </div>
                    </div>
                    <div style="padding:1.5rem;">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1rem;">
                            <div>
                                <div style="font-family:var(--font-display);font-weight:700;font-size:1.1rem;"><?= e($ds['version_name']) ?></div>
                                <div style="font-size:0.75rem;color:var(--text-muted);">Uploaded <?= format_date($ds['created_at']) ?></div>
                            </div>
                        </div>

                        <?php if ($ds['status'] === 'pending' && $order['status'] === 'approval'): ?>
                        <div style="display:flex;gap:0.75rem;margin-top:1.5rem;">
                            <button class="btn btn--aura btn--sm flex-1" onclick="handleDesign(<?= $ds['id'] ?>, 'approve')">
                                <i data-lucide="check"></i> Approve Design
                            </button>
                            <button class="btn btn--ghost btn--sm" style="color:var(--danger);" onclick="openRevisionModal(<?= $ds['id'] ?>)">
                                <i data-lucide="rotate-ccw"></i> Request Revision
                            </button>
                        </div>
                        <?php endif; ?>

                        <?php if ($ds['feedback']): ?>
                        <div style="margin-top:1.25rem;padding:1rem;background:var(--void);border-radius:var(--r);font-size:0.85rem;border:1px solid var(--space-border);">
                            <div class="label-text" style="font-size:0.6rem;margin-bottom:0.4rem;color:var(--text-muted);">Your Feedback</div>
                            <p style="color:var(--text-secondary);"><?= e($ds['feedback']) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>

        <!-- Order Summary Card -->
        <div class="card" style="padding: 1.5rem;">
            <div style="display:grid;grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));gap:1.5rem;">
                <div>
                    <div class="kpi-label">Project Manager</div>
                    <div style="font-weight:600;"><?= e($order['staff_name'] ?: 'ILLUME Studio') ?></div>
                </div>
                <div>
                     <div class="kpi-label">Budget</div>
                     <div style="font-weight:600;color:var(--gold);"><?= format_currency((float)$order['budget'],$order['currency']) ?></div>
                </div>
                <div>
                    <div class="kpi-label">Expected Completion</div>
                    <div style="font-weight:600;"><?= $order['deadline'] ? format_date($order['deadline']) : 'Consulting Phase' ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline Sidebar -->
    <div class="flex-col gap-6">
        <div class="card--glass" style="padding: 1.5rem;">
            <h3 style="font-size:1rem;margin-bottom:1.5rem;font-family:var(--font-display);">Milestones</h3>
            <div class="timeline">
                <?php foreach ($timeline as $t): 
                    // Hide internal actor names and small technical logs from client
                    $display_action = match($t['action']) {
                        'order_created'   => 'Project Started',
                        'design_upload'   => 'New Design Ready',
                        'design_approved' => 'Design Approved',
                        'design_rejected' => 'Revision In Progress',
                        'status_update'   => 'Phase Update',
                        default => null
                    };
                    if (!$display_action) continue;
                ?>
                <div class="timeline-item">
                    <div class="timeline-time"><?= format_date($t['created_at'],'d M') ?></div>
                    <div class="timeline-action"><?= $display_action ?></div>
                    <div class="timeline-note"><?= e($t['note']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; border-color: var(--gold-glass);">
            <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.75rem;">
                <i data-lucide="help-circle" style="width:18px;color:var(--gold);"></i>
                <h4 style="font-size:0.95rem;">Need Assistance?</h4>
            </div>
            <p style="font-size:0.8rem;color:var(--text-muted);line-height:1.6;margin-bottom:1.25rem;">
                Not sure about a design or have new measurements? Our studio team is ready to help via WhatsApp.
            </p>
            <a href="<?= whatsapp_link(WHATSAPP_NUMBER, "Hi! I have a question about my project " . $order['order_ref']) ?>" target="_blank" class="btn btn--secondary btn--sm btn--w-full">
                Contact Studio
            </a>
        </div>
    </div>
</div>

<!-- Revision Modal -->
<div class="modal-backdrop" id="revision-modal">
    <div class="modal">
        <div class="modal__header">
            <h4>Request Revision</h4>
            <button class="btn btn--icon btn--ghost" data-modal-close><i data-lucide="x"></i></button>
        </div>
        <div class="modal__body">
            <p style="font-size:0.88rem;color:var(--text-muted);margin-bottom:1.25rem;">
                Please describe the changes you'd like to see. Be as specific as possible (colors, fit, silhouette, etc.)
            </p>
            <textarea id="revision-feedback" class="form-textarea" placeholder="e.g. I love the silhouette, but could we explore a darker shade of gold for the embroidery?"></textarea>
            <input type="hidden" id="revision-id">
        </div>
        <div class="modal__footer">
            <button class="btn btn--ghost" data-modal-close>Cancel</button>
            <button class="btn btn--primary" onclick="submitRevision()">Submit Request</button>
        </div>
    </div>
</div>

<script>
async function handleDesign(submissionId, action, feedback = '') {
    if (action === 'approve' && !confirm('Are you sure you want to approve this design for production?')) return;
    
    const formData = new FormData();
    formData.append('submission_id', submissionId);
    formData.append('action', action);
    formData.append('feedback', feedback);
    formData.append('csrf_token', '<?= csrf_token() ?>');

    try {
        const res = await fetch('<?= SITE_URL ?>/api/design.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Something went wrong.');
        }
    } catch (err) {
        alert('Network error. Please try again.');
    }
}

function openRevisionModal(id) {
    document.getElementById('revision-id').value = id;
    document.getElementById('revision-modal').classList.add('open');
}

function submitRevision() {
    const id = document.getElementById('revision-id').value;
    const feedback = document.getElementById('revision-feedback').value;
    if (!feedback.trim()) return alert('Please provide some feedback.');
    handleDesign(id, 'revision', feedback);
}
</script>

<?php include __DIR__ . '/../includes/dash_footer.php'; ?>
