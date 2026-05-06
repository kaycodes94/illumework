<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Services';
$page_desc = 'Explore ILLUME\'s luxury fashion services — from bespoke couture to editorial styling and fashion consulting.';
$services = get_services();
include __DIR__ . '/includes/header.php';
?>

<!-- ═══ HERO ═════════════════════════════════════════════════ -->
<section style="
  min-height: 45svh; display: flex; align-items: center;
  padding-top: var(--nav-h); position: relative; overflow: hidden;
  background: white; border-bottom: 1px solid var(--divider);
">
  <div class="container" style="position: relative; z-index: 1;">
    <div class="label-text" style="margin-bottom: var(--s2);">What We Offer</div>
    <h1 style="font-size: clamp(2.5rem, 8vw, 4rem); font-weight: 800; line-height: 1.1; letter-spacing: -0.04em; margin-bottom: var(--s6); max-width: 700px;">
      Services Built<br>for the <span class="gold-text">Extraordinary.</span>
    </h1>
    <p style="font-size: 1.15rem; max-width: 500px; line-height: 1.6; color: var(--warm-taupe);">
      Every service we offer is designed around one goal:
      to make you look and feel exceptional.
    </p>
  </div>
</section>

<!-- ═══ ALL SERVICES ══════════════════════════════════════════ -->
<section class="section--lg">
  <div class="container--sm">

    <div style="display:grid; gap:4rem;">
      <?php foreach ($services as $i => $svc): ?>
        <div id="<?= e($svc['slug']) ?>" class="reveal" style="
        padding-bottom:4rem;
        <?= $i < (count($services) - 1) ? 'border-bottom:1px solid var(--divider);' : '' ?>
      ">
          <div style="display:flex; align-items:flex-start; gap:2rem;">
            <div style="
            width: 56px; height: 56px; border-radius: var(--r); /* Rounded corners */
            background: var(--soft-ivory); border: 1px solid var(--divider);
            display: flex; align-items: center; justify-content: center;
            color: var(--champagne); flex-shrink: 0; margin-top: 0.5rem;
          ">
            <i data-lucide="<?= e($svc['icon']) ?>" style="width: 24px; height: 24px;"></i>
          </div>

            <div style="flex-grow:1;">
              <div class="label-text" style="color:var(--text-muted); margin-bottom:1rem;"><?= sprintf('%02d', $i + 1) ?> —
                <?= e($svc['slug']) ?></div>
              <h2 style="margin-bottom:1.5rem;"><?= e($svc['name']) ?></h2>
              <p
                style="font-size:1.15rem; line-height:2; color:var(--text-muted); margin-bottom:2.5rem; max-width:700px;">
                <?= e($svc['description']) ?>
              </p>

              <a href="consultation.php?service=<?= urlencode($svc['slug']) ?>" class="btn btn--primary"
                style="padding:0.85rem 2rem;">
                Request Consultation
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>

<!-- ═══ CTA ════════════════════════════════════════════════════ -->
<section class="section" style="border-top:1px solid var(--divider);text-align:center;">
  <div class="container">
    <h2 class="reveal" style="margin-bottom:1rem;">Not Sure Which<br><span class="gold-text">Service You Need?</span>
    </h2>
    <p class="reveal reveal-delay-1" style="max-width:480px;margin:0 auto 2rem;font-size:1.05rem;">
      Book a free 30-minute discovery call. We'll listen, assess, and point you
      in the right direction — no pressure, no obligation.
    </p>
    <a href="consultation.php" class="btn btn--primary btn--lg reveal reveal-delay-2">
      <i data-lucide="calendar"></i> Book Free Discovery Call
    </a>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>