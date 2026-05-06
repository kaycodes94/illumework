<?php
// ============================================================
// ILLUME — Consultation Booking (Multi-Step Form)
// ============================================================
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Book a Consultation';
$page_desc  = 'Start your ILLUME journey. Book a free consultation and let us craft something extraordinary together.';

// Defined services as per user request
$services = [
    ['name' => 'Bespoke Couture',      'slug' => 'bespoke',    'icon' => 'scissors'],
    ['name' => 'Bridals & Asoebi',    'slug' => 'bridal',     'icon' => 'heart'],
    ['name' => 'African Wears',       'slug' => 'african',    'icon' => 'palmtree'],
    ['name' => 'Casuals',             'slug' => 'casual',     'icon' => 'shirt'],
    ['name' => 'Custom Dressmaking',  'slug' => 'dress',      'icon' => 'needle'],
    ['name' => 'Beading',             'slug' => 'beading',    'icon' => 'sparkles'],
    ['name' => 'Fashion Consultancy', 'slug' => 'consult',    'icon' => 'compass'],
    ['name' => 'Fashion Illustration','slug' => 'illustrate', 'icon' => 'pen-tool'],
];

$success    = false;
$error      = '';

// Pre-select service from URL param
$preselect  = $_GET['service'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Security check failed. Please try again.';
    } else {
        $name       = trim($_POST['name'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $phone      = trim($_POST['phone'] ?? '');
        $whatsapp   = trim($_POST['whatsapp'] ?? '');
        $service    = trim($_POST['service_type'] ?? '');
        $occasion   = trim($_POST['occasion'] ?? '');
        $budget     = trim($_POST['budget_range'] ?? '');
        $timeline   = trim($_POST['timeline'] ?? '');
        $message    = trim($_POST['message'] ?? '');

        if (!$name || !$email || !$service) {
            $error = 'Please fill in all required fields (Name, Email, Service).';
        } else {
            try {
                $stmt = db()->prepare("
                    INSERT INTO consultations (name, email, phone, whatsapp, service_type, occasion, budget_range, timeline, message)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$name, $email, $phone, $whatsapp, $service, $occasion, $budget, $timeline, $message]);
                $success = true;

                // WhatsApp message generation
                $wa_msg = urlencode(
                    "Hello ILLUME,\n" .
                    "I've just submitted a consultation request!\n\n" .
                    "Name: {$name}\n" .
                    "Service: {$service}\n" .
                    "Budget: {$budget}\n" .
                    "Timeline: {$timeline}\n\n" .
                    "I look forward to hearing from you!"
                );
            } catch (PDOException $e) {
                $error = 'An error occurred. Please email us directly at ' . SITE_EMAIL;
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<!-- ═══ HERO ═════════════════════════════════════════════════ -->
<section style="
  min-height: 40svh; display: flex; align-items: center;
  padding-top: var(--nav-h); position: relative; overflow: hidden;
  background: white; border-bottom: 1px solid var(--divider);
">
  <div class="container" style="position: relative; z-index: 1; text-align: center;">
    <div class="label-text" style="margin-bottom: var(--s2);">Free Consultation</div>
    <h1 style="font-size: clamp(2.5rem, 8vw, 4rem); font-weight: 800; line-height: 1.1; letter-spacing: -0.04em; margin-bottom: var(--s6);">
      Let's Build Something<br><span class="gold-text">Extraordinary.</span>
    </h1>
    <p style="max-width: 540px; margin: 0 auto; font-size: 1.15rem; line-height: 1.6; color: var(--warm-taupe);">
      Tell us about your vision. We'll respond within 24 hours to schedule your discovery call.
    </p>
  </div>
</section>

<!-- ═══ FORM ══════════════════════════════════════════════════ -->
<section class="section--lg">
  <div class="container--sm">

    <?php if ($success): ?>
    <!-- SUCCESS STATE -->
    <div style="text-align: center; padding: var(--s16) var(--s4);">
      <div style="
        width: 80px; height: 80px; border-radius: var(--r-full); 
        background: var(--soft-ivory);
        border: 1px solid var(--divider);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto var(--s6);
      ">
        <i data-lucide="check" style="width: 36px; height: 36px; color: var(--champagne);"></i>
      </div>
      <h2 style="margin-bottom:1rem;">Request Received!</h2>
      <p style="max-width:440px;margin:0 auto 2rem;line-height:1.8;font-size:1.05rem;">
        Thank you <strong><?= e($_POST['name'] ?? '') ?></strong>. We'll be in touch within 24 hours.
        In the meantime, feel free to reach us directly on WhatsApp.
      </p>
      <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
        <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>?text=<?= isset($wa_msg) ? $wa_msg : '' ?>"
           class="btn btn--primary btn--lg" target="_blank" rel="noopener">
          <i data-lucide="message-circle"></i> Continue on WhatsApp
        </a>
        <a href="/" class="btn btn--ghost btn--lg">
          <i data-lucide="home"></i> Back to Home
        </a>
      </div>
    </div>

    <?php else: ?>
    <!-- MULTI-STEP FORM -->
    <?php if ($error): ?>
    <div class="alert alert--error" style="margin-bottom:2rem;">
      <i data-lucide="alert-circle"></i> <span><?= e($error) ?></span>
    </div>
    <?php endif; ?>

    <!-- Step Indicators -->
    <div style="margin-bottom:1rem;">
      <div class="form-step-indicator">
        <span class="form-step-label active" id="step-label-0">Service</span>
        <span class="form-step-label"         id="step-label-1">Details</span>
        <span class="form-step-label"         id="step-label-2">Budget</span>
        <span class="form-step-label"         id="step-label-3">Your Info</span>
      </div>
      <div class="form-steps">
        <div class="form-step active" id="step-bar-0"></div>
        <div class="form-step"       id="step-bar-1"></div>
        <div class="form-step"       id="step-bar-2"></div>
        <div class="form-step"       id="step-bar-3"></div>
      </div>
    </div>

    <form method="POST" action="consultation.php" id="consult-form" novalidate>
      <?= csrf_field() ?>

      <!-- ── STEP 1: Service ── -->
      <div class="form-panel active" id="panel-0" style="padding: 2.5rem; border-radius: var(--r-lg); background: white; box-shadow: var(--shadow-md); border: 1px solid var(--divider);">
        <h3 style="margin-bottom:0.5rem; color: var(--black);">Which service interests you?</h3>
        <p style="font-size:0.9rem;margin-bottom:2rem; color: var(--warm-taupe);">Select the primary service. You can discuss scope during the consultation.</p>

        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:0.75rem;margin-bottom:2rem;">
          <?php foreach ($services as $svc): ?>
          <label style="cursor:pointer; position: relative;">
            <input type="radio" name="service_type" value="<?= e($svc['name']) ?>"
                   <?= ($preselect === $svc['slug'] ? 'checked' : '') ?>
                   style="display:none;" class="service-radio">
            <div class="service-radio-card" style="
              padding:1.25rem;border:2px solid var(--divider);border-radius:var(--r-lg);
              transition:all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
              display:flex;align-items:center;gap:0.75rem;
              background: white; position: relative;
            ">
              <div class="icon-box" style="
                width:36px;height:36px;border-radius:var(--r-sm);flex-shrink:0;
                background: var(--soft-ivory); border:1px solid var(--divider);
                display:flex;align-items:center;justify-content:center;color:var(--champagne);
                transition: inherit;
              ">
                <i data-lucide="<?= e($svc['icon']) ?>" style="width:16px;height:16px;"></i>
              </div>
              <div>
                <div class="service-name" style="font-size:0.85rem;font-weight:600;line-height:1.2; color: var(--black); transition: inherit;"><?= e($svc['name']) ?></div>
              </div>
            </div>
          </label>
          <?php endforeach; ?>
        </div>

        <div style="display:flex;justify-content:flex-end;">
          <button type="button" class="btn btn--primary next-btn" data-next="1">
            Next Step <i data-lucide="arrow-right"></i>
          </button>
        </div>
      </div>

      <!-- ── STEP 2: Occasion ── -->
      <div class="form-panel" id="panel-1" style="display:none; padding: 2.5rem; border-radius: var(--r-lg); background: white; box-shadow: var(--shadow-md); border: 1px solid var(--divider);">
        <h3 style="margin-bottom:0.5rem; color: var(--black);">Project Details</h3>
        <p style="font-size:0.9rem;margin-bottom:2rem; color: var(--warm-taupe);">Help us understand the context of your request.</p>

        <div class="form-group">
          <label class="form-label">Specific Occasion or Project Title</label>
          <input type="text" name="occasion" class="form-input" placeholder="e.g. Traditional Wedding, Wardrobe Overhaul, Brand Launch...">
        </div>

        <div class="form-group">
          <label class="form-label">Desired Completion Date (Optional)</label>
          <input type="text" name="timeline" class="form-input" placeholder="e.g. Mid-August 2026">
        </div>

        <div style="display:flex;justify-content:space-between;">
          <button type="button" class="btn btn--ghost prev-btn" data-prev="0">
            <i data-lucide="arrow-left"></i> Back
          </button>
          <button type="button" class="btn btn--primary next-btn" data-next="2">
            Next Step <i data-lucide="arrow-right"></i>
          </button>
        </div>
      </div>

      <!-- ── STEP 3: Budget ── -->
      <div class="form-panel" id="panel-2" style="display:none; padding: 2.5rem; border-radius: var(--r-lg); background: white; box-shadow: var(--shadow-md); border: 1px solid var(--divider);">
        <h3 style="margin-bottom:0.5rem; color: var(--black);">Estimated Budget Range</h3>
        <p style="font-size:0.9rem;margin-bottom:2rem; color: var(--warm-taupe);">This helps us recommend the best materials and complexity level.</p>

        <div style="display:grid;grid-template-columns:repeat(1,1fr);gap:0.75rem;margin-bottom:2rem;">
          <?php 
          $budgets = [
              'Under 100k', '100k - 250k', '250k - 500k', '500k - 1M', '1M - 3M', 'Luxury / Bespoke (3M+)'
          ];
          foreach ($budgets as $b): 
          ?>
          <label style="cursor:pointer;">
            <input type="radio" name="budget_range" value="<?= e($b) ?>" style="display:none;" class="budget-radio">
            <div class="budget-card" style="padding:1rem 1.25rem;border:2px solid var(--divider);border-radius:var(--r-lg);text-align:center;font-size:0.9rem;transition:all 0.3s; color: var(--black);">
              <?= e($b) ?>
            </div>
          </label>
          <?php endforeach; ?>
        </div>

        <div style="display:flex;justify-content:space-between;">
          <button type="button" class="btn btn--ghost prev-btn" data-prev="1">
            <i data-lucide="arrow-left"></i> Back
          </button>
          <button type="button" class="btn btn--primary next-btn" data-next="3">
            Next Step <i data-lucide="arrow-right"></i>
          </button>
        </div>
      </div>

      <!-- ── STEP 4: Personal Info ── -->
      <div class="form-panel" id="panel-3" style="display:none; padding: 2.5rem; border-radius: var(--r-lg); background: white; box-shadow: var(--shadow-md); border: 1px solid var(--divider);">
        <h3 style="margin-bottom:0.5rem; color: var(--black);">Final Details</h3>
        <p style="font-size:0.9rem;margin-bottom:2rem; color: var(--warm-taupe);">How should we contact you?</p>

        <div class="form-group">
          <label class="form-label">Full Name*</label>
          <input type="text" name="name" class="form-input" placeholder="Enter your name" required>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
          <div class="form-group">
            <label class="form-label">Email Address*</label>
            <input type="email" name="email" class="form-input" placeholder="Enter your email" required>
          </div>
          <div class="form-group">
            <label class="form-label">Phone Number</label>
            <input type="tel" name="phone" class="form-input" placeholder="+234...">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Message / Special Requests</label>
          <textarea name="message" class="form-input" rows="4" placeholder="Anything else we should know?"></textarea>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;">
          <button type="button" class="btn btn--ghost prev-btn" data-prev="2">
            <i data-lucide="arrow-left"></i> Back
          </button>
          <button type="submit" class="btn btn--primary btn--lg" style="animation:pulse-glow 3s ease infinite;">
            <i data-lucide="send"></i> Submit Request
          </button>
        </div>
      </div>

    </form>
    <?php endif; ?>

  </div>
</section>

<style>
.service-radio-card, .budget-card {
  border: 2px solid var(--divider) !important;
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
  position: relative;
  overflow: hidden;
}

.service-radio:checked + .service-radio-card,
.service-radio-card.selected {
  border-color: #D5A84C !important;
  background: #D5A84C !important;
  color: white !important;
  transform: translateY(-4px) scale(1.02);
  box-shadow: 0 12px 24px rgba(213, 168, 76, 0.3);
}

.service-radio:checked + .service-radio-card .service-name,
.service-radio-card.selected .service-name {
  color: white !important;
}

.service-radio:checked + .service-radio-card i,
.service-radio-card.selected i {
  color: white !important;
  transform: scale(1.1);
}

.service-radio:checked + .service-radio-card .icon-box,
.service-radio-card.selected .icon-box {
  background: rgba(255, 255, 255, 0.2) !important;
  border-color: rgba(255, 255, 255, 0.3) !important;
}

.budget-radio:checked + .budget-card,
.budget-card.selected {
  border-color: #D5A84C !important;
  background: #D5A84C !important;
  color: white !important;
  font-weight: 700;
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(213, 168, 76, 0.2);
}

/* Custom Check Indicator */
.service-radio-card::after {
  content: '✓';
  position: absolute;
  top: 10px;
  right: 10px;
  width: 20px;
  height: 20px;
  background: white;
  color: #D5A84C;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  opacity: 0;
  transform: scale(0.5);
  transition: all 0.3s ease;
  z-index: 3;
}

.service-radio:checked + .service-radio-card::after,
.service-radio-card.selected::after {
  opacity: 1;
  transform: scale(1);
}
</style>

<script>
// Multi-step logic
const nextBtns = document.querySelectorAll('.next-btn');
const prevBtns = document.querySelectorAll('.prev-btn');
const panels = document.querySelectorAll('.form-panel');
const labels = document.querySelectorAll('.form-step-label');
const bars = document.querySelectorAll('.form-step');

nextBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    const next = btn.dataset.next;
    panels.forEach(p => p.style.display = 'none');
    document.getElementById(`panel-${next}`).style.display = 'block';
    
    labels.forEach((l, i) => i <= next ? l.classList.add('active') : l.classList.remove('active'));
    bars.forEach((b, i) => i <= next ? b.classList.add('active') : b.classList.remove('active'));
  });
});

prevBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    const prev = btn.dataset.prev;
    panels.forEach(p => p.style.display = 'none');
    document.getElementById(`panel-${prev}`).style.display = 'block';
    
    labels.forEach((l, i) => i > prev ? l.classList.remove('active') : null);
    bars.forEach((b, i) => i > prev ? b.classList.add('active') : null); // Note: bars handle active state differently in CSS usually
  });
});

// Service radio styling
document.querySelectorAll('.service-radio').forEach(radio => {
  radio.addEventListener('change', () => {
    document.querySelectorAll('.service-radio-card').forEach(c => c.classList.remove('selected'));
    radio.nextElementSibling.classList.add('selected');
  });
  if (radio.checked) radio.nextElementSibling.classList.add('selected');
});

// Budget radio styling
document.querySelectorAll('.budget-radio').forEach(radio => {
  radio.addEventListener('change', () => {
    document.querySelectorAll('.budget-card').forEach(c => c.classList.remove('selected'));
    radio.nextElementSibling.classList.add('selected');
  });
  if (radio.checked) radio.nextElementSibling.classList.add('selected');
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
