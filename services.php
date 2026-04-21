<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Services';
$page_desc  = 'Explore ILLUME\'s luxury fashion services — from bespoke couture to editorial styling and fashion consulting.';
$services = get_services();
include __DIR__ . '/includes/header.php';
?>

<!-- ═══ SERVICES HERO ══════════════════════════════════════════ -->
<section style="
  min-height:55svh;display:flex;align-items:center;
  padding-top:var(--nav-h);position:relative;overflow:hidden;
  background:linear-gradient(135deg,var(--void) 0%,var(--space) 100%);
">
  <div style="
    position:absolute;inset:0;
    background-image:linear-gradient(rgba(201,168,76,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(201,168,76,0.03) 1px,transparent 1px);
    background-size:60px 60px;pointer-events:none;
  "></div>
  <div style="position:absolute;bottom:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,var(--gold-glass),transparent);"></div>

  <div class="container" style="position:relative;z-index:1;">
    <div class="label-text" style="margin-bottom:1rem;">What We Offer</div>
    <h1 style="font-size:clamp(3rem,8vw,6rem);font-weight:800;line-height:0.95;letter-spacing:-0.04em;margin-bottom:1.5rem;max-width:650px;">
      Services Built<br>for Those Who<br><span class="shimmer-text">Demand More.</span>
    </h1>
    <p style="font-size:1.05rem;max-width:500px;line-height:1.8;color:var(--text-secondary);">
      Every service we offer is designed around one goal:
      to make you look and feel extraordinary.
    </p>
  </div>
</section>

<!-- ═══ ALL SERVICES ══════════════════════════════════════════ -->
<section class="section--lg">
  <div class="container">

    <?php foreach ($services as $i => $svc): ?>
    <div id="<?= e($svc['slug']) ?>" style="
      display:grid;
      grid-template-columns:<?= $i % 2 === 0 ? '1fr 1.2fr' : '1.2fr 1fr' ?>;
      gap:5rem;align-items:center;
      padding:4rem 0;
      <?= $i > 0 ? 'border-top:1px solid var(--space-border);' : '' ?>
    ">

      <?php if ($i % 2 !== 0): ?>
      <!-- Visual on right for odd items — shows left on even below -->
      <div class="reveal-left">
        <div class="label-text" style="margin-bottom:0.75rem;"><?= sprintf('%02d', $i+1) ?> / <?= sprintf('%02d', count($services)) ?></div>
        <h2 style="margin-bottom:1rem;"><?= e($svc['name']) ?></h2>
        <p style="font-size:1.05rem;line-height:1.85;margin-bottom:1.5rem;"><?= e($svc['description']) ?></p>

        <div style="padding:1.25rem;background:var(--space);border:1px solid var(--space-border);border-radius:var(--r-lg);margin-bottom:2rem;">
          <div style="display:flex;justify-content:space-between;align-items:center;">
            <div>
              <div class="label-text" style="color:var(--text-muted);margin-bottom:0.25rem;">Starting From</div>
              <div style="font-family:var(--font-display);font-size:1.8rem;font-weight:700;color:var(--gold);">
                <?= format_currency((float)$svc['starting_price'], $svc['currency']) ?>
              </div>
            </div>
            <div style="text-align:right;">
              <div class="label-text" style="color:var(--text-muted);margin-bottom:0.25rem;">Currency</div>
              <div style="font-weight:600;"><?= e($svc['currency']) ?></div>
            </div>
          </div>
        </div>

        <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
          <a href="consultation.php?service=<?= urlencode($svc['slug']) ?>" class="btn btn--primary">
            <i data-lucide="calendar"></i> Request This Service
          </a>
          <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>?text=Hello%20ILLUME!%20I%27m%20interested%20in%20your%20<?= urlencode($svc['name']) ?>%20service."
             class="btn btn--ghost" target="_blank" rel="noopener">
            <i data-lucide="message-circle"></i> WhatsApp
          </a>
        </div>
      </div>

      <div class="reveal-right">
        <div style="
          aspect-ratio:4/3;border-radius:var(--r-2xl);
          background:linear-gradient(135deg,var(--space-mid),rgba(201,168,76,0.06));
          border:1px solid var(--gold-glass);
          display:flex;align-items:center;justify-content:center;
          position:relative;overflow:hidden;
        ">
          <div style="position:absolute;inset:0;background:radial-gradient(circle at 50% 50%,rgba(201,168,76,0.08),transparent 65%);"></div>
          <i data-lucide="<?= e($svc['icon']) ?>" style="width:80px;height:80px;color:var(--gold);opacity:0.5;position:relative;z-index:1;"></i>
          <div style="position:absolute;bottom:1.5rem;left:1.5rem;right:1.5rem;">
            <div class="label-text"><?= e($svc['name']) ?></div>
          </div>
          <div style="position:absolute;top:1rem;right:1rem;width:20px;height:20px;border-top:1px solid var(--gold-glass);border-right:1px solid var(--gold-glass);"></div>
          <div style="position:absolute;bottom:1rem;left:1rem;width:20px;height:20px;border-bottom:1px solid var(--gold-glass);border-left:1px solid var(--gold-glass);"></div>
        </div>
      </div>

      <?php else: ?>
      <!-- Visual LEFT, text RIGHT for even items -->
      <div class="reveal-left">
        <div style="
          aspect-ratio:4/3;border-radius:var(--r-2xl);
          background:linear-gradient(160deg,var(--space-mid),rgba(0,255,209,0.04));
          border:1px solid var(--space-border);
          display:flex;align-items:center;justify-content:center;
          position:relative;overflow:hidden;
        ">
          <div style="position:absolute;inset:0;background:radial-gradient(circle at 40% 60%,rgba(0,255,209,0.06),transparent 60%);"></div>
          <i data-lucide="<?= e($svc['icon']) ?>" style="width:80px;height:80px;color:var(--plasma);opacity:0.4;position:relative;z-index:1;"></i>
          <div style="position:absolute;bottom:1.5rem;left:1.5rem;right:1.5rem;">
            <div class="label-text" style="color:var(--plasma);"><?= e($svc['name']) ?></div>
          </div>
          <div style="position:absolute;top:1rem;right:1rem;width:20px;height:20px;border-top:1px solid var(--plasma-glass);border-right:1px solid var(--plasma-glass);"></div>
          <div style="position:absolute;bottom:1rem;left:1rem;width:20px;height:20px;border-bottom:1px solid var(--plasma-glass);border-left:1px solid var(--plasma-glass);"></div>
        </div>
      </div>

      <div class="reveal-right">
        <div class="label-text" style="margin-bottom:0.75rem;"><?= sprintf('%02d', $i+1) ?> / <?= sprintf('%02d', count($services)) ?></div>
        <h2 style="margin-bottom:1rem;"><?= e($svc['name']) ?></h2>
        <p style="font-size:1.05rem;line-height:1.85;margin-bottom:1.5rem;"><?= e($svc['description']) ?></p>

        <div style="padding:1.25rem;background:var(--space);border:1px solid var(--space-border);border-radius:var(--r-lg);margin-bottom:2rem;">
          <div style="display:flex;justify-content:space-between;align-items:center;">
            <div>
              <div class="label-text" style="color:var(--text-muted);margin-bottom:0.25rem;">Starting From</div>
              <div style="font-family:var(--font-display);font-size:1.8rem;font-weight:700;color:var(--gold);">
                <?= format_currency((float)$svc['starting_price'], $svc['currency']) ?>
              </div>
            </div>
            <div style="text-align:right;">
              <div class="label-text" style="color:var(--text-muted);margin-bottom:0.25rem;">Currency</div>
              <div style="font-weight:600;"><?= e($svc['currency']) ?></div>
            </div>
          </div>
        </div>

        <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
          <a href="consultation.php?service=<?= urlencode($svc['slug']) ?>" class="btn btn--primary">
            <i data-lucide="calendar"></i> Request This Service
          </a>
          <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>?text=Hello%20ILLUME!%20I%27m%20interested%20in%20<?= urlencode($svc['name']) ?>."
             class="btn btn--ghost" target="_blank" rel="noopener">
            <i data-lucide="message-circle"></i> WhatsApp
          </a>
        </div>
      </div>
      <?php endif; ?>

    </div>
    <?php endforeach; ?>

  </div>
</section>

<!-- ═══ CTA ════════════════════════════════════════════════════ -->
<section class="section" style="background:var(--space);border-top:1px solid var(--space-border);text-align:center;">
  <div class="container">
    <h2 class="reveal" style="margin-bottom:1rem;">Not Sure Which<br><span class="shimmer-text">Service You Need?</span></h2>
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
