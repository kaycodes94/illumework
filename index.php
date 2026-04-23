<?php
// ============================================================
// ILLUME — Homepage
// ============================================================
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Fashion. Elevated.';
$page_desc  = 'ILLUME is a luxury Nigerian fashion house offering Bespoke, Asoebi, African Wears, and high-end Custom Dressmaking.';

$services = get_services();
include __DIR__ . '/includes/header.php';
?>

<!-- ═══ HERO ═════════════════════════════════════════════════ -->
<section class="hero" id="hero">
  <canvas id="hero-canvas"></canvas>
  <div class="hero__overlay"></div>

  <div class="hero__content">
    <div class="hero__label">
      <span>Abuja · Nigeria · Est. 2018</span>
    </div>

    <h1 class="hero__title">
      <span class="word">Fashion</span><br>
      <span class="word gold-word">ELEVATED.</span>
    </h1>

    <p class="hero__subtitle">
      What makes ILLUME unique? We don’t just design fashion—we illuminate identity. 
      Every thread is intentional. Every silhouette, a statement.
      Welcome to the future of Nigerian luxury fashion.
    </p>

    <div class="hero__cta">
      <a href="consultation.php" class="btn btn--primary btn--lg">
        <i data-lucide="calendar"></i>
        Book a Consultation
      </a>
      <a href="portfolio.php" class="btn btn--ghost btn--lg">
        <i data-lucide="eye"></i>
        View Portfolio
      </a>
    </div>
  </div>

  <div class="hero__scroll-hint">
    <span>Scroll</span>
    <div class="scroll-line"></div>
  </div>
</section>

<!-- ═══ STATS TICKER ══════════════════════════════════════════ -->
<section style="padding: 4rem 0; border-top: 1px solid var(--space-border); border-bottom: 1px solid var(--space-border); background: var(--space);">
  <div class="container">
    <div style="display:grid; grid-template-columns: repeat(4,1fr); gap:2rem; text-align:center;">

      <div class="reveal">
        <div style="font-family:var(--font-display); font-size:clamp(2rem,5vw,3.5rem); font-weight:800; background:linear-gradient(135deg,var(--gold),var(--gold-bright)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; line-height:1;"
             data-counter data-target="7" data-suffix="+">0+</div>
        <div class="label-text" style="margin-top:0.5rem;">Years of Craft</div>
      </div>

      <div class="reveal reveal-delay-1">
        <div style="font-family:var(--font-display); font-size:clamp(2rem,5vw,3.5rem); font-weight:800; background:linear-gradient(135deg,var(--gold),var(--gold-bright)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; line-height:1;"
             data-counter data-target="340" data-suffix="+">0+</div>
        <div class="label-text" style="margin-top:0.5rem;">Clients Served</div>
      </div>

      <div class="reveal reveal-delay-2">
        <div style="font-family:var(--font-display); font-size:clamp(2rem,5vw,3.5rem); font-weight:800; background:linear-gradient(135deg,var(--gold),var(--gold-bright)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; line-height:1;"
             data-counter data-target="1200" data-suffix="+">0+</div>
        <div class="label-text" style="margin-top:0.5rem;">Pieces Created</div>
      </div>

      <div class="reveal reveal-delay-3">
        <div style="font-family:var(--font-display); font-size:clamp(2rem,5vw,3.5rem); font-weight:800; background:linear-gradient(135deg,var(--gold),var(--gold-bright)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; line-height:1;"
             data-counter data-target="12" data-suffix="">0</div>
        <div class="label-text" style="margin-top:0.5rem;">Countries Worn</div>
      </div>

    </div>
  </div>
</section>

<!-- ═══ SERVICES PREVIEW ══════════════════════════════════════ -->
<section class="section--lg">
  <div class="container">
    <div class="section-header">
      <div class="section-divider">
        <span class="label-text">What We Do</span>
      </div>
      <h2 class="reveal">Services Built<br>for the <span class="shimmer-text">Extraordinary</span></h2>
      <p class="reveal reveal-delay-1">
        From bespoke couture to full brand styling, every service
        is engineered to make you unforgettable.
      </p>
    </div>

    <div class="grid-3" style="gap:1.5rem;">
      <?php foreach (array_slice($services, 0, 3) as $i => $svc): ?>
      <div class="service-card reveal reveal-delay-<?= $i + 1 ?>" id="service-<?= e($svc['slug']) ?>" 
           onclick="window.location.href='services.php#<?= e($svc['slug']) ?>'" style="cursor:pointer;">
        <div class="service-icon">
          <i data-lucide="<?= e($svc['icon']) ?>"></i>
        </div>
        <div class="label-text" style="margin-bottom:0.5rem;"><?= e($svc['name']) ?></div>
        <h3 style="font-size:1.3rem; margin-bottom:0.75rem;"><?= e($svc['short_desc']) ?></h3>
        <p style="font-size:0.88rem; line-height:1.7; margin-bottom:1.25rem;"><?= e($svc['description']) ?></p>
        <div style="display:flex; align-items:center; justify-content:flex-end; margin-top:auto;">
          <a href="consultation.php?service=<?= urlencode($svc['slug']) ?>" class="btn btn--ghost btn--sm" onclick="event.stopPropagation();">
            Inquire <i data-lucide="arrow-right" style="width:14px;height:14px;"></i>
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center" style="margin-top:3rem;">
      <a href="services.php" class="btn btn--secondary btn--lg">
        View All Services <i data-lucide="arrow-right"></i>
      </a>
    </div>
  </div>
</section>

<!-- ═══ PHILOSOPHY SECTION ════════════════════════════════════ -->
<section class="section" style="background:var(--space); border-top:1px solid var(--space-border); border-bottom:1px solid var(--space-border);">
  <div class="container">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:5rem; align-items:center;">

      <!-- Left: Visual -->
      <div class="reveal-left" style="position:relative;">
        <div style="
          aspect-ratio:3/4;
          border-radius:var(--r-2xl);
          overflow:hidden; position:relative;
          box-shadow: 0 30px 60px rgba(0,0,0,0.1);
        ">
          <img src="<?= SITE_URL ?>/assets/img/philosophy.png" alt="ILLUME Bespoke Couture" style="width:100%; height:100%; object-fit:cover;">
          
          <!-- Decorative overlay -->
          <div style="position:absolute; inset:0; background:linear-gradient(to bottom, transparent 60%, rgba(0,0,0,0.4) 100%);"></div>
          
          <div style="position:absolute; bottom:2rem; left:2rem; z-index:1;">
            <div style="
              font-family:var(--font-display);
              font-size:4rem;
              font-weight:800;
              color: #FFF;
              line-height:1;
              margin-bottom:0.5rem;
            ">I.</div>
            <div class="label-text" style="color:var(--gold-bright);">Olewuezi Ikedichukwu Peace & Susan Mtsevah</div>
          </div>
          
          <!-- Corner accents -->
          <div style="position:absolute;top:1.5rem;right:1.5rem;width:24px;height:24px;border-top:2px solid #FFF;border-right:2px solid #FFF;border-radius:0 var(--r-sm) 0 0;"></div>
          <div style="position:absolute;bottom:1.5rem;right:1.5rem;width:24px;height:24px;border-bottom:2px solid #FFF;border-left:2px solid #FFF;border-radius:0 0 0 var(--r-sm); transform: rotate(180deg);"></div>
        </div>

        <!-- Floating accent card -->
        <div class="glass" style="
          position:absolute; bottom:-2rem; right:-2rem;
          padding:1.25rem 1.5rem;
          display:flex; align-items:center; gap:0.75rem;
        ">
          <div style="width:10px;height:10px;border-radius:50%;background:var(--aura);box-shadow:0 0 12px var(--aura);flex-shrink:0;"></div>
          <div>
            <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--text-muted);">Design Time</div>
            <div style="font-size:0.95rem;font-weight:600;color:var(--text-primary);">2 – 6 Weeks</div>
          </div>
        </div>
      </div>

      <!-- Right: Text -->
      <div class="reveal-right">
        <div class="label-text" style="margin-bottom:1rem;">Our Philosophy</div>
        <h2 style="margin-bottom:1.5rem;">
          Fashion That<br>
          <span class="shimmer-text">Commands Space</span>
        </h2>
        <p style="margin-bottom:1.5rem; font-size:1.05rem; line-height:1.8; font-style: italic; color: var(--gold-bright);">
          Illume by Light Peace was born from a simple conviction—<br>
          that fashion should not just be seen, but felt.
        </p>
        <p style="margin-bottom:1.5rem; line-height:1.8;">
          Here, African identity is not explained; it is elevated. 
          Every piece is crafted with intention to reflect dignity, confidence, and quiet strength.
        </p>
        <p style="margin-bottom:2rem; line-height:1.8; font-weight: 500; letter-spacing: 0.02em;">
          This is more than fashion. It is light expressed. It is legacy in motion.
        </p>

        <div style="display:flex; flex-direction:column; gap:1rem; margin-bottom:2rem;">
          <?php
          $pillars = [
            ['icon'=>'zap',        'title'=>'Precision Craft',     'desc'=>'Master tailors with 10–20 years of experience.'],
            ['icon'=>'shield-check','title'=>'Quality Materials',   'desc'=>'Only the finest fabrics sourced locally and globally.'],
            ['icon'=>'users',       'title'=>'Client-Centered',     'desc'=>'Your satisfaction isn\'t a goal — it\'s our baseline.'],
          ];
          foreach ($pillars as $p): ?>
          <div style="display:flex; align-items:flex-start; gap:1rem;">
            <div style="
              width:40px; height:40px; border-radius:var(--r);
              background:var(--gold-faint); border:1px solid var(--gold-glass);
              display:flex; align-items:center; justify-content:center;
              color:var(--gold); flex-shrink:0;
            ">
              <i data-lucide="<?= $p['icon'] ?>" style="width:16px;height:16px;"></i>
            </div>
            <div>
              <div style="font-weight:600; margin-bottom:0.2rem;"><?= $p['title'] ?></div>
              <div style="font-size:0.85rem; color:var(--text-muted);"><?= $p['desc'] ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <a href="about.php" class="btn btn--primary">
          Our Story <i data-lucide="arrow-right"></i>
        </a>
      </div>

    </div>
  </div>
</section>

<!-- ═══ PORTFOLIO PREVIEW ══════════════════════════════════════ -->
<section class="section--lg">
  <div class="container">
    <div class="section-header">
      <div class="section-divider">
        <span class="label-text">Portfolio</span>
      </div>
      <h2 class="reveal">Work That<br><span class="shimmer-text">Speaks First</span></h2>
      <p class="reveal reveal-delay-1">A glimpse into the ILLUME universe. Each piece tells a story only you can finish.</p>
    </div>

    <!-- Portfolio Grid (CSS-based placeholder visual showcase) -->
    <div style="display:grid; grid-template-columns:repeat(3,1fr); grid-template-rows:auto; gap:1.5rem; margin-bottom:4rem;">
      <?php
      $portfolioItems = [
        ['label'=>'Bespoke',             'sub'=>'Artisanal Tailoring', 'img'=>'svc_bespoke_1776779653752.png', 'url'=>'services.php#bespoke-couture'],
        ['label'=>'Asoebi',              'sub'=>'Heritage Matrimony',  'img'=>'bridal.png',                   'url'=>'services.php#asoebi'],
        ['label'=>'African Wears',       'sub'=>'Modern Tradition',    'img'=>'couture.png',                  'url'=>'services.php#african-wears'],
        ['label'=>'Custom Dressmaking',   'sub'=>'Technical Couture',   'img'=>'philosophy.png',               'url'=>'services.php#dressmaking'],
      ];
      foreach ($portfolioItems as $idx => $item):
        $gridArea = '';
        if ($idx === 0) $gridArea = 'grid-row: span 2;';
        if ($idx === 3) $gridArea = 'grid-column: span 2;';
      ?>
      <a href="<?= e($item['url']) ?>" class="portfolio-item reveal reveal-delay-<?= ($idx % 3) + 1 ?>" style="<?= $gridArea ?> text-decoration:none;">
        <div style="
          background: var(--space-mid);
          border: 1px solid var(--space-border);
          border-radius: var(--r-xl);
          height: <?= $idx === 0 ? '580px' : '280px' ?>;
          display:flex; align-items:flex-end;
          position:relative; overflow:hidden; cursor:pointer;
          transition: all 0.5s var(--ease-out);
        "
        onmouseenter="this.querySelector('img').style.transform='scale(1.08)'; this.style.borderColor='var(--aura-glass)';"
        onmouseleave="this.querySelector('img').style.transform='scale(1)'; this.style.borderColor='var(--space-border)';"
        >
          <img src="<?= SITE_URL ?>/assets/img/<?= $item['img'] ?>" alt="<?= e($item['label']) ?>" 
               style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; transition: transform 0.8s var(--ease-out);">
          
          <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 60%);"></div>

          <div style="position:relative; z-index:1; padding:2rem; width:100%;">
            <div class="label-text" style="color:var(--gold-bright); margin-bottom:0.3rem;"><?= $item['label'] ?></div>
            <div style="font-size:0.9rem; color:#FFF; font-weight:500;"><?= $item['sub'] ?></div>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

    <div class="text-center">
      <a href="portfolio.php" class="btn btn--secondary btn--lg">
        Full Portfolio <i data-lucide="arrow-right"></i>
      </a>
    </div>
  </div>
</section>

<!-- ═══ TESTIMONIALS ══════════════════════════════════════════ -->
<section class="section" style="background:var(--space); border-top:1px solid var(--space-border); border-bottom:1px solid var(--space-border);">
  <div class="container">
    <div class="section-header">
      <div class="section-divider"><span class="label-text">Testimonials</span></div>
      <h2 class="reveal">Worn. Adored.<br><span class="shimmer-text">Remembered.</span></h2>
    </div>

    <div class="grid-3" style="gap:1.5rem;">
      <?php
      $testimonials = [
        [
          'name'    => 'Adaora Nwachukwu',
          'title'   => 'CEO, Adaora Skincare · Abuja',
          'quote'   => 'ILLUME transformed my entire relationship with fashion. My bespoke pieces don\'t just fit — they feel like armor. I walk into every boardroom differently.',
          'initial' => 'A',
        ],
        [
          'name'    => 'Funmi Adeyemi',
          'title'   => 'Bride, 2024 · Abuja',
          'quote'   => 'My wedding dress was everything I dreamed and more. They listened to every detail and somehow created something better than I could have imagined. Magic.',
          'initial' => 'F',
        ],
        [
          'name'    => 'Emeka Obi',
          'title'   => 'Creative Director · London/Abuja',
          'quote'   => 'As someone who works in fashion, I\'m hard to impress. ILLUME managed to surprise me every single time. Extraordinary craft, extraordinary people.',
          'initial' => 'E',
        ],
      ];
      foreach ($testimonials as $i => $t): ?>
      <div class="card card--glass reveal reveal-delay-<?= $i + 1 ?>" style="padding:2rem;">
        <!-- Stars -->
        <div style="display:flex;gap:3px;margin-bottom:1.25rem;">
          <?php for ($s = 0; $s < 5; $s++): ?>
          <i data-lucide="star" style="width:14px;height:14px;color:var(--gold);fill:var(--gold);"></i>
          <?php endfor; ?>
        </div>
        <p style="font-size:0.95rem; line-height:1.75; font-style:italic; margin-bottom:1.5rem; color:var(--text-secondary);">
          "<?= e($t['quote']) ?>"
        </p>
        <div style="display:flex;align-items:center;gap:0.75rem;margin-top:auto; border-top:1px solid var(--space-border); padding-top:1.25rem;">
          <div style="
            width:42px; height:42px; border-radius:50%;
            background:linear-gradient(135deg,var(--gold),var(--gold-bright));
            display:flex; align-items:center; justify-content:center;
            font-family:var(--font-display); font-weight:700; color:var(--void);
            flex-shrink:0;
          "><?= htmlspecialchars($t['initial']) ?></div>
          <div>
            <div style="font-weight:600; font-size:0.9rem;"><?= e($t['name']) ?></div>
            <div style="font-size:0.75rem; color:var(--text-muted);"><?= e($t['title']) ?></div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══ CONSULTATION CTA ═══════════════════════════════════════ -->
<section class="section--lg" style="position:relative; overflow:hidden;">
  <!-- Background glow -->
  <div style="
    position:absolute; top:50%; left:50%;
    transform:translate(-50%,-50%);
    width:600px; height:300px;
    background:radial-gradient(ellipse, rgba(201,168,76,0.08), transparent);
    filter:blur(60px);
    pointer-events:none;
  "></div>

  <div class="container" style="position:relative;z-index:1;text-align:center;">
    <div class="section-divider" style="justify-content:center;margin-bottom:1.5rem;">
      <span class="label-text">Ready to Begin?</span>
    </div>

    <h2 class="reveal" style="font-size:clamp(2.5rem,6vw,5rem); margin-bottom:1.5rem; max-width:700px; margin-left:auto; margin-right:auto;">
      Your Signature<br><span class="shimmer-text">Piece Awaits.</span>
    </h2>

    <p class="reveal reveal-delay-1" style="font-size:1.1rem; max-width:520px; margin:0 auto 2.5rem; line-height:1.8;">
      Every extraordinary garment starts with a single conversation.
      Book your free consultation and let's begin building yours.
    </p>

    <div class="reveal reveal-delay-2" style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
      <a href="consultation.php" class="btn btn--primary btn--lg" style="animation:pulse-glow 3s ease infinite;">
        <i data-lucide="calendar"></i>
        Book Free Consultation
      </a>
      <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>?text=Hello%20ILLUME!%20I%27d%20like%20to%20discuss%20a%20project."
         class="btn btn--ghost btn--lg" target="_blank" rel="noopener">
        <i data-lucide="message-circle"></i>
        WhatsApp Us Directly
      </a>
    </div>

    <!-- Trust badges -->
    <div style="display:flex;justify-content:center;gap:2.5rem;margin-top:3rem;flex-wrap:wrap;" class="reveal reveal-delay-3">
      <?php
      $trust = [
        ['icon'=>'shield-check', 'text'=>'Free Consultation'],
        ['icon'=>'clock',        'text'=>'Response in 24hrs'],
        ['icon'=>'lock',         'text'=>'Private & Confidential'],
        ['icon'=>'award',        'text'=>'Satisfaction Guaranteed'],
      ];
      foreach ($trust as $tr): ?>
      <div style="display:flex;align-items:center;gap:0.5rem;color:var(--text-muted);font-size:0.82rem;">
        <i data-lucide="<?= $tr['icon'] ?>" style="width:15px;height:15px;color:var(--gold);"></i>
        <span><?= $tr['text'] ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
