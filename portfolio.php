<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Portfolio';
$page_desc  = 'Explore the ILLUME portfolio — a visual archive of bespoke couture, bridal masterpieces, and editorial fashion.';
include __DIR__ . '/includes/header.php';

$categories = ['All','Bespoke Couture','Bridal','Editorial','Ready-to-Wear','Consulting'];
$items = [
  ['cat'=>'Bespoke Couture', 'title'=>'Crimson Power Suit',    'sub'=>'Client: Adaora N.','c1'=>'#1a0402','c2'=>'rgba(201,60,60,0.15)','size'=>'tall'],
  ['cat'=>'Bridal',          'title'=>'Celestial Bridal Gown', 'sub'=>'Collection 2024',  'c1'=>'#08080f','c2'=>'rgba(200,200,255,0.12)','size'=>'normal'],
  ['cat'=>'Editorial',       'title'=>'LFW Runway Look',       'sub'=>'Lagos, 2023',       'c1'=>'#0c000e','c2'=>'rgba(180,0,255,0.1)','size'=>'normal'],
  ['cat'=>'Bespoke Couture', 'title'=>'Gold Evening Gown',     'sub'=>'Private Collection','c1'=>'#0e0900','c2'=>'rgba(201,168,76,0.18)','size'=>'wide'],
  ['cat'=>'Ready-to-Wear',   'title'=>'Harmattan Collection',  'sub'=>'Season 2024',       'c1'=>'#001008','c2'=>'rgba(0,200,150,0.12)','size'=>'normal'],
  ['cat'=>'Bridal',          'title'=>'Lagos White Wedding',   'sub'=>'Client: Funmi A.', 'c1'=>'#0a0a14','c2'=>'rgba(100,100,255,0.1)','size'=>'tall'],
  ['cat'=>'Editorial',       'title'=>'Vogue Africa Shoot',    'sub'=>'Campaign 2024',     'c1'=>'#140000','c2'=>'rgba(255,50,50,0.08)','size'=>'normal'],
  ['cat'=>'Bespoke Couture', 'title'=>'Midnight Silk Corset',  'sub'=>'Private Client',    'c1'=>'#030310','c2'=>'rgba(0,100,255,0.1)','size'=>'normal'],
  ['cat'=>'Consulting',      'title'=>'Brand Identity Revamp', 'sub'=>'Client: TechFashion','c1'=>'#0d0d0a','c2'=>'rgba(201,168,76,0.08)','size'=>'wide'],
];
?>

<!-- ═══ HERO ═════════════════════════════════════════════════ -->
<section style="padding:calc(var(--nav-h) + 3rem) 0 3rem;background:var(--space);border-bottom:1px solid var(--space-border);position:relative;overflow:hidden;">
  <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(201,168,76,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(201,168,76,0.03) 1px,transparent 1px);background-size:60px 60px;pointer-events:none;"></div>
  <div class="container" style="position:relative;z-index:1;">
    <div class="label-text" style="margin-bottom:1rem;">Our Work</div>
    <h1 style="font-size:clamp(3rem,8vw,6rem);font-weight:800;line-height:0.95;letter-spacing:-0.04em;margin-bottom:1.5rem;max-width:650px;">
      A Visual<br>Archive of<br><span class="shimmer-text">Excellence.</span>
    </h1>
    <p style="font-size:1.05rem;max-width:500px;line-height:1.8;color:var(--text-secondary);">
      Every piece in this gallery represents a story, a conversation,
      and a life elevated through the power of exceptional fashion.
    </p>
  </div>
</section>

<!-- ═══ FILTERS ═══════════════════════════════════════════════ -->
<section style="padding:2.5rem 0;border-bottom:1px solid var(--space-border);background:var(--space);">
  <div class="container">
    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;justify-content:center;">
      <?php foreach ($categories as $i => $cat): ?>
      <button
        data-filter="<?= $i === 0 ? 'all' : e($cat) ?>"
        class="btn btn--ghost btn--sm <?= $i === 0 ? 'active' : '' ?>"
        style="<?= $i === 0 ? 'border-color:var(--gold);color:var(--gold);background:var(--gold-faint);' : '' ?>"
        onclick="
          document.querySelectorAll('[data-filter]').forEach(b=>{
            b.style.borderColor='';b.style.color='';b.style.background='';
          });
          this.style.borderColor='var(--gold)';
          this.style.color='var(--gold)';
          this.style.background='var(--gold-faint)';
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
        'Bespoke Couture' => 'couture.png',
        'Bridal'          => 'bridal.png',
        'Editorial'       => 'editorial.png',
        'Ready-to-Wear'   => 'rtw.png',
        'Consulting'      => 'philosophy.png'
      ];
      foreach ($items as $i => $item):
        $img = $images[$item['cat']] ?? 'luxury.jpg';
      ?>
      <div
        class="portfolio-item reveal"
        data-category="<?= e($item['cat']) ?>"
        style="margin-bottom:1.5rem;break-inside:avoid;"
      >
        <div style="
          background:var(--space-mid);
          border:1px solid var(--space-border);
          border-radius:var(--r-xl);
          height:auto;
          position:relative;overflow:hidden;
          transition:all 0.5s var(--ease-out);
          display:flex;flex-direction:column;justify-content:flex-end;
          box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        "
        onmouseenter="this.querySelector('img').style.transform='scale(1.05)'; this.style.borderColor='var(--aura-glass)';"
        onmouseleave="this.querySelector('img').style.transform='scale(1)'; this.style.borderColor='var(--space-border)';"
        >
          <img src="<?= SITE_URL ?>/assets/img/<?= $img ?>" alt="<?= e($item['title']) ?>"
               style="width:100%; height:<?= $item['size']==='tall' ? '420px' : ($item['size']==='wide' ? '240px' : '300px') ?>; object-fit:cover; transition: transform 0.8s var(--ease-out);">

          <!-- Info overlay -->
          <div class="portfolio-item__overlay" style="opacity:1;background:linear-gradient(180deg,transparent 20%,rgba(0,0,0,0.85));padding:2rem;">
             <!-- Category badge -->
            <div style="margin-bottom:0.75rem;">
              <span class="badge" style="font-size:0.65rem; background:rgba(255,255,255,0.1); backdrop-filter:blur(4px); color:var(--gold-bright); border:1px solid rgba(255,255,255,0.1);"><?= e($item['cat']) ?></span>
            </div>
            <div style="font-size:1.1rem;font-weight:700;color:#FFF;margin-bottom:0.3rem;font-family:var(--font-display);"><?= e($item['title']) ?></div>
            <div style="font-size:0.8rem;color:rgba(255,255,255,0.7);"><?= e($item['sub']) ?></div>
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
    <h2 class="reveal" style="margin-bottom:1rem;">Your Story<br><span class="shimmer-text">Belongs Here.</span></h2>
    <p class="reveal reveal-delay-1" style="max-width:440px;margin:0 auto 2rem;">
      Commission a piece and let your garment take its place in the ILLUME archive.
    </p>
    <a href="consultation.php" class="btn btn--primary btn--lg reveal reveal-delay-2">
      <i data-lucide="calendar"></i> Begin Your Journey
    </a>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
