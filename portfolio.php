<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Portfolio';
$page_desc = 'Explore the ILLUME portfolio — a visual archive of bespoke couture, bridal masterpieces, and editorial fashion.';
include __DIR__ . '/includes/header.php';

$categories = [
  'Bespoke',
  'Asoebi',
  'African Wears',
  'Casuals',
  'Custom Dressmaking',
  'Beading',
  'Fashion Consultancy',
  'Fashion Illustration'
];
$items = [
  ['cat' => 'Bespoke', 'title' => 'The Executive Silk', 'sub' => 'Artisanal Tailoring', 'size' => 'tall'],
  ['cat' => 'Asoebi', 'title' => 'Heritage Bloom', 'sub' => 'Event Coordination', 'size' => 'wide'],
  ['cat' => 'African Wears', 'title' => 'Modern Heritage', 'sub' => 'Avant-Garde Prints', 'size' => 'tall'],
  ['cat' => 'Casuals', 'title' => 'Linen Resort', 'sub' => 'Premium Leisure', 'size' => 'normal'],
  ['cat' => 'Custom Dressmaking', 'title' => 'The Master Fit', 'sub' => 'Bespoke Construction', 'size' => 'normal'],
  ['cat' => 'Beading', 'title' => 'Pearl & Petal', 'sub' => 'Hand Embroidery', 'size' => 'tall'],
  ['cat' => 'Fashion Consultancy', 'title' => 'Identity Audit', 'sub' => 'Style Strategy', 'size' => 'normal'],
  ['cat' => 'Fashion Illustration', 'title' => 'Genesis Sketch', 'sub' => 'Visual Rendering', 'size' => 'normal'],
];
?>

<!-- ═══ HERO ═════════════════════════════════════════════════ -->
<section style="
  min-height: 45svh; display: flex; align-items: center;
  padding-top: var(--nav-h); position: relative; overflow: hidden;
  background: white; border-bottom: 1px solid var(--divider);
">
  <div class="container" style="position: relative; z-index: 1;">
    <div class="label-text" style="margin-bottom: var(--s2);">Our Work</div>
    <h1 style="font-size: clamp(2.5rem, 8vw, 4.5rem); font-weight: 800; line-height: 1.1; letter-spacing: -0.04em; margin-bottom: var(--s6); max-width: 700px;">
      A Visual Archive of<br><span class="gold-text">Excellence.</span>
    </h1>
    <p style="font-size: 1.15rem; max-width: 500px; line-height: 1.6; color: var(--warm-taupe);">
      Every piece in this gallery represents a story, a conversation,
      and a life elevated through the power of exceptional craft.
    </p>
  </div>
</section>

<!-- ═══ FILTERS ═══════════════════════════════════════════════ -->
<section style="padding:2.5rem 0;border-bottom:1px solid var(--divider);">
  <div class="container">
    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;justify-content:center;">
      <?php foreach ($categories as $i => $cat): ?>
        <button
          data-filter="<?= $i === 0 ? 'all' : e($cat) ?>"
          class="btn btn--ghost btn--sm <?= $i === 0 ? 'active' : '' ?>"
          style="<?= $i === 0 ? 'border-color:var(--champagne);color:var(--champagne);background:rgba(213, 168, 76, 0.05);' : '' ?>"
          onclick="
          document.querySelectorAll('[data-filter]').forEach(b=>{
            b.style.borderColor='';b.style.color='';b.style.background='';
          });
          this.style.borderColor='var(--champagne)';
          this.style.color='var(--champagne)';
          this.style.background='rgba(213, 168, 76, 0.05)';
        "
        ><?= e($cat) ?></button>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══ GRID ══════════════════════════════════════════════════ -->
<section class="section--lg">
  <div class="container">
    <div style="columns:3;column-gap:1rem;" id="portfolio-grid">
      <?php
      $images = [
        'Bespoke' => 'svc_bespoke_1776779653752.png',
        'Bridals and Asoebi' => 'bridal.png',
        'Suits and Dinner Dress' => 'editorial.png',
        'African Wears' => 'couture.png',
        'Casuals' => 'rtw.png',
        'Custom Dressmaking' => 'philosophy.png',
        'Beading' => 'editorial.png',
        'Fashion Consultancy' => 'philosophy.png',
        'Fashion Illustration' => 'philosophy.png'
      ];
      $serviceMap = [
        'Bespoke' => 'bespoke-couture',
        'Bridals and Asoebi' => 'asoebi',
        'Asoebi' => 'asoebi',
        'Suits and Dinner Dress' => 'bespoke-couture',
        'African Wears' => 'african-wears',
        'Casuals' => 'casuals',
        'Custom Dressmaking' => 'dressmaking',
        'Beading' => 'beading',
        'Fashion Consultancy' => 'consultancy',
        'Fashion Illustration' => 'illustration'
      ];
      foreach ($items as $i => $item):
        $img = $images[$item['cat']] ?? 'luxury.jpg';
        $slug = $serviceMap[$item['cat']] ?? '';
        ?>
        <a
          href="<?= $slug ? 'services.php#' . $slug : '#' ?>"
          class="portfolio-item reveal"
          data-category="<?= e($item['cat']) ?>"
          style="margin-bottom:1.5rem;break-inside:avoid;display:block;text-decoration:none;"
        >
          <div class="portfolio-card" style="height:auto;">
            <img src="<?= SITE_URL ?>/assets/js/../../assets/img/<?= $img ?>" alt="<?= e($item['title']) ?>"
                 style="width:100%; height:<?= $item['size'] === 'tall' ? '420px' : ($item['size'] === 'wide' ? '240px' : '300px') ?>; object-fit:cover;">

            <!-- Info overlay -->
            <div class="portfolio-card__overlay"></div>
            <div class="portfolio-card__content" style="padding: 1.5rem;">
               <!-- Category badge -->
              <div style="margin-bottom:0.5rem;">
                <span class="badge" style="font-size:0.6rem; background:rgba(255,255,255,0.1); backdrop-filter:blur(4px); color:var(--champagne); border:1px solid rgba(255,255,255,0.1);"><?= e($item['cat']) ?></span>
              </div>
              <div style="font-size:1.1rem;font-weight:700;color:#FFF;margin-bottom:0.3rem;font-family:var(--font-display);"><?= e($item['title']) ?></div>
              <div style="font-size:0.8rem;color:rgba(255,255,255,0.7);"><?= e($item['sub']) ?></div>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══ CTA ════════════════════════════════════════════════════ -->
<section class="section" style="border-top:1px solid var(--divider);text-align:center;">
  <div class="container">
    <h2 class="reveal" style="margin-bottom:1rem;">Your Story<br><span class="gold-text">Belongs Here.</span></h2>
    <p class="reveal reveal-delay-1" style="max-width:440px;margin:0 auto 2rem;">
      Commission a piece and let your garment take its place in the ILLUME archive.
    </p>
    <a href="consultation.php" class="btn btn--primary btn--lg reveal reveal-delay-2">
      <i data-lucide="calendar"></i> Begin Your Journey
    </a>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
