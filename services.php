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
  <div class="container--sm">

    <div style="display:grid; gap:4rem;">
      <?php foreach ($services as $i => $svc): ?>
      <div id="<?= e($svc['slug']) ?>" class="reveal" style="
        padding-bottom:4rem;
        <?= $i < (count($services)-1) ? 'border-bottom:1px solid var(--space-border);' : '' ?>
      ">
        <div style="display:flex; align-items:flex-start; gap:2rem;">
          <div style="
            width:50px; height:50px; border-radius:50%;
            background:var(--gold-faint); border:1px solid var(--gold-glass);
            display:flex; align-items:center; justify-content:center;
            color:var(--gold); flex-shrink:0; margin-top:0.5rem;
          ">
            <i data-lucide="<?= e($svc['icon']) ?>" style="width:20px;height:20px;"></i>
          </div>
          
          <div style="flex-grow:1;">
            <div class="label-text" style="color:var(--text-muted); margin-bottom:1rem;"><?= sprintf('%02d', $i+1) ?> — <?= e($svc['slug']) ?></div>
            <h2 style="margin-bottom:1.5rem;"><?= e($svc['name']) ?></h2>
            <p style="font-size:1.15rem; line-height:2; color:var(--text-secondary); margin-bottom:2.5rem; max-width:700px;">
              <?= e($svc['description']) ?>
            </p>
            
            <a href="consultation.php?service=<?= urlencode($svc['slug']) ?>" class="btn btn--primary" style="padding:0.85rem 2rem;">
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
