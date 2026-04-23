<?php
// ============================================================
// ILLUME — Consultation Booking (Multi-Step Form)
// ============================================================
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Book a Consultation';
$page_desc  = 'Start your ILLUME journey. Book a free consultation and let us craft something extraordinary together.';
$services   = get_services();
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
            $error = 'Please fill in all required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
            try {
                $stmt = db()->prepare("INSERT INTO consultations
                    (name, email, phone, whatsapp, service_type, occasion, budget_range, timeline, message, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'new')");
                $stmt->execute([$name, $email, $phone, $whatsapp ?: $phone, $service, $occasion, $budget, $timeline, $message]);
                $success = true;

                // Build WhatsApp redirect message
                $wa_msg = urlencode(
                    "Hello ILLUME! 🌟\n\n" .
                    "I've just submitted a consultation request.\n" .
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
<section style="padding:calc(var(--nav-h) + 3rem) 0 3rem;background:var(--space);border-bottom:1px solid var(--space-border);position:relative;overflow:hidden;">
  <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(201,168,76,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(201,168,76,0.03) 1px,transparent 1px);background-size:60px 60px;pointer-events:none;"></div>
  <div style="position:absolute;top:0;right:15%;width:400px;height:400px;background:radial-gradient(circle,rgba(201,168,76,0.06),transparent);filter:blur(80px);pointer-events:none;"></div>
  <div class="container" style="position:relative;z-index:1;text-align:center;">
    <div class="label-text" style="margin-bottom:1rem;">Free Consultation</div>
    <h1 style="font-size:clamp(2.5rem,6vw,5rem);line-height:1;margin-bottom:1rem;">
      Let's Build Something<br><span class="shimmer-text">Extraordinary.</span>
    </h1>
    <p style="max-width:520px;margin:0 auto;font-size:1.05rem;line-height:1.8;color:var(--text-secondary);">
      Tell us about your vision. We'll respond within 24 hours to schedule your discovery call.
    </p>
  </div>
</section>

<!-- ═══ FORM ══════════════════════════════════════════════════ -->
<section class="section--lg">
  <div class="container--sm">

    <?php if ($success): ?>
    <!-- SUCCESS STATE -->
    <div style="text-align:center;padding:4rem 2rem;">
      <div style="
        width:80px;height:80px;border-radius:50%;
        background:linear-gradient(135deg,var(--gold),var(--gold-bright));
        display:flex;align-items:center;justify-content:center;
        margin:0 auto 2rem;
        box-shadow:0 0 40px var(--gold-glow);
      ">
        <i data-lucide="check" style="width:36px;height:36px;color:var(--void);"></i>
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
      <div class="form-panel glass" style="padding:2.5rem;border-radius:var(--r-2xl);">
        <h3 style="margin-bottom:0.5rem;">Which service interests you?</h3>
        <p style="font-size:0.9rem;margin-bottom:2rem;">Select the primary service. You can discuss scope during the consultation.</p>

        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:0.75rem;margin-bottom:2rem;">
          <?php foreach ($services as $svc): ?>
          <label style="cursor:none;">
            <input type="radio" name="service_type" value="<?= e($svc['name']) ?>"
                   <?= ($preselect === $svc['slug'] ? 'checked' : '') ?>
                   style="display:none;" class="service-radio">
            <div class="service-radio-card" style="
              padding:1.25rem;border:1px solid var(--space-border);border-radius:var(--r-lg);
              transition:all 0.3s var(--ease-out);cursor:none;
              display:flex;align-items:center;gap:0.75rem;
            ">
              <div style="
                width:36px;height:36px;border-radius:var(--r);flex-shrink:0;
                background:var(--gold-faint);border:1px solid var(--gold-glass);
                display:flex;align-items:center;justify-content:center;color:var(--gold);
              ">
                <i data-lucide="<?= e($svc['icon']) ?>" style="width:16px;height:16px;"></i>
              </div>
              <div>
                <div style="font-size:0.85rem;font-weight:600;line-height:1.2;"><?= e($svc['name']) ?></div>
              </div>
            </div>
          </label>
          <?php endforeach; ?>
        </div>

        <div class="form-group">
          <label class="form-label">Not sure? Tell us briefly what you need</label>
          <input type="text" name="occasion" class="form-input" placeholder="e.g. Wedding dress, wardrobe revamp, photoshoot styling…">
        </div>

        <div style="display:flex;justify-content:flex-end;">
          <button type="button" data-next class="btn btn--primary">
            Next Step <i data-lucide="arrow-right"></i>
          </button>
        </div>
      </div>

      <!-- ── STEP 2: Details ── -->
      <div class="form-panel glass hidden" style="padding:2.5rem;border-radius:var(--r-2xl);">
        <h3 style="margin-bottom:0.5rem;">Tell us about your project</h3>
        <p style="font-size:0.9rem;margin-bottom:2rem;">The more detail you share, the better we can prepare for your consultation.</p>

        <div class="form-group">
          <label class="form-label" for="timeline">Desired Timeline</label>
          <select name="timeline" id="timeline" class="form-select">
            <option value="">Select timeline…</option>
            <option value="ASAP (within 2 weeks)">ASAP (within 2 weeks)</option>
            <option value="1 month">1 month</option>
            <option value="2-3 months">2–3 months</option>
            <option value="4-6 months">4–6 months</option>
            <option value="6+ months">6+ months</option>
            <option value="Flexible">Flexible</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="message">Describe your vision <span style="color:var(--gold);">*</span></label>
          <textarea name="message" id="message" class="form-textarea" rows="6"
            placeholder="Tell us about the occasion, your style, any inspirations, special requirements, or anything that will help us understand your vision…"></textarea>
        </div>

        <div style="display:flex;justify-content:space-between;gap:1rem;">
          <button type="button" data-prev class="btn btn--ghost">
            <i data-lucide="arrow-left"></i> Back
          </button>
          <button type="button" data-next class="btn btn--primary">
            Next Step <i data-lucide="arrow-right"></i>
          </button>
        </div>
      </div>

      <!-- ── STEP 3: Budget ── -->
      <div class="form-panel glass hidden" style="padding:2.5rem;border-radius:var(--r-2xl);">
        <h3 style="margin-bottom:0.5rem;">Budget & Preferences</h3>
        <p style="font-size:0.9rem;margin-bottom:2rem;">This helps us tailor the right recommendation for you. All budgets are respected.</p>

        <div class="form-group">
          <label class="form-label">Budget Range (NGN)</label>
          <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:0.75rem;margin-bottom:1rem;">
            <?php
            $budgets = [
              'Under ₦100,000'    => 'Under ₦100,000',
              '₦100k – ₦250k'    => '₦100k – ₦250k',
              '₦250k – ₦500k'    => '₦250k – ₦500k',
              '₦500k – ₦1M'      => '₦500k – ₦1M',
              '₦1M – ₦3M'        => '₦1M – ₦3M',
              '₦3M+'              => '₦3M+',
            ];
            foreach ($budgets as $val => $label): ?>
            <label style="cursor:none;">
              <input type="radio" name="budget_range" value="<?= e($val) ?>" style="display:none;" class="budget-radio">
              <div style="
                padding:1rem;border:1px solid var(--space-border);border-radius:var(--r);
                font-size:0.88rem;font-weight:500;text-align:center;
                transition:all 0.25s;cursor:none;
              " class="budget-card"><?= e($label) ?></div>
            </label>
            <?php endforeach; ?>
          </div>
          <input type="hidden" name="budget_range" id="budget_hidden">
        </div>

        <div style="display:flex;justify-content:space-between;gap:1rem;">
          <button type="button" data-prev class="btn btn--ghost">
            <i data-lucide="arrow-left"></i> Back
          </button>
          <button type="button" data-next class="btn btn--primary">
            Final Step <i data-lucide="arrow-right"></i>
          </button>
        </div>
      </div>

      <!-- ── STEP 4: Contact Info ── -->
      <div class="form-panel glass hidden" style="padding:2.5rem;border-radius:var(--r-2xl);">
        <h3 style="margin-bottom:0.5rem;">Your Details</h3>
        <p style="font-size:0.9rem;margin-bottom:2rem;">How should we reach you? All information is kept strictly private.</p>

        <div class="grid-2" style="gap:1rem;">
          <div class="form-group">
            <label class="form-label" for="name">Full Name <span style="color:var(--gold);">*</span></label>
            <input type="text" name="name" id="name" class="form-input" placeholder="Your full name" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="email">Email Address <span style="color:var(--gold);">*</span></label>
            <input type="email" name="email" id="email" class="form-input" placeholder="your@email.com" required>
          </div>
        </div>

        <div class="grid-2" style="gap:1rem;">
          <div class="form-group">
            <label class="form-label" for="phone">Phone Number</label>
            <input type="tel" name="phone" id="phone" class="form-input" placeholder="+234 800 000 0000">
          </div>
          <div class="form-group">
            <label class="form-label" for="whatsapp">WhatsApp Number</label>
            <input type="tel" name="whatsapp" id="whatsapp" class="form-input" placeholder="Same as phone? Leave blank">
          </div>
        </div>

        <div style="
          padding:1.25rem;background:var(--gold-faint);
          border:1px solid var(--gold-glass);border-radius:var(--r);
          display:flex;align-items:flex-start;gap:0.75rem;
          margin-bottom:1.5rem;font-size:0.85rem;line-height:1.6;
        ">
          <i data-lucide="shield-check" style="width:16px;height:16px;color:var(--gold);flex-shrink:0;margin-top:2px;"></i>
          <span style="color:var(--text-secondary);">Your information is kept strictly confidential. We only use it to follow up on your consultation request.</span>
        </div>

        <div style="display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
          <button type="button" data-prev class="btn btn--ghost">
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
.service-radio:checked + .service-radio-card,
.service-radio-card.selected {
  border-color: var(--gold);
  background: var(--gold-faint);
  box-shadow: 0 0 20px var(--gold-faint);
}
.budget-radio:checked + .budget-card,
.budget-card.selected {
  border-color: var(--gold);
  background: var(--gold-faint);
  color: var(--gold);
}
</style>

<script>
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
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
