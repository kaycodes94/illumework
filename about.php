<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Our Story';
$page_desc  = 'Learn about ILLUME — a luxury Nigerian fashion house rooted in craft, identity, and the belief that clothing is a language.';
include __DIR__ . '/includes/header.php';
?>

<!-- ═══ ABOUT HERO ════════════════════════════════════════════ -->
<section style="
  min-height:75svh; display:flex; align-items:center;
  position:relative; overflow:hidden; padding-top:var(--nav-h);
  background: var(--void);
">
  <!-- Immersive Background -->
  <img src="<?= SITE_URL ?>/assets/img/editorial.png" alt="ILLUME Background" 
       style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:0.15; filter: grayscale(1);">
  <div style="position:absolute; inset:0; background:radial-gradient(circle at 30% 50%, var(--void) 0%, transparent 100%);"></div>
  
  <!-- Decorative gold grid -->
  <div style="
    position:absolute; inset:0;
    background-image:
      linear-gradient(rgba(201,168,76,0.035) 1px, transparent 1px),
      linear-gradient(90deg, rgba(201,168,76,0.035) 1px, transparent 1px);
    background-size:60px 60px;
    pointer-events:none;
  "></div>
  <div style="position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,var(--gold-glass),transparent);"></div>
  <div style="position:absolute;bottom:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,var(--gold-glass),transparent);"></div>

  <!-- Glow orb -->
  <div style="
    position:absolute;top:50%;right:10%;
    transform:translateY(-50%);
    width:500px;height:500px;border-radius:50%;
    background:radial-gradient(circle,rgba(201,168,76,0.07),transparent);
    filter:blur(80px);pointer-events:none;
  "></div>

  <div class="container" style="position:relative;z-index:1;">
    <div class="label-text" style="margin-bottom:1rem;">Our Story</div>
    <h1 style="font-size:clamp(3rem,8vw,6.5rem);font-weight:800;line-height:0.95;letter-spacing:-0.04em;margin-bottom:1.5rem;max-width:700px;">
      Born From<br><span class="shimmer-text">Lagos.</span><br>Worn By the<br>World.
    </h1>
    <p style="font-size:1.1rem;max-width:520px;line-height:1.8;color:var(--text-secondary);">
      ILLUME was founded on a single belief: that Nigerian fashion deserves to occupy
      its rightful place at the global table of luxury.
    </p>
  </div>
</section>

<!-- ═══ BRAND STORY ═══════════════════════════════════════════ -->
<section class="section--lg">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:6rem;align-items:start;">
      <div>
        <div class="label-text" style="margin-bottom:1rem;">The Beginning</div>
        <h2 style="margin-bottom:1.5rem;" class="reveal">
          A Stitch in<br>the Right Direction
        </h2>
        <p style="margin-bottom:1.25rem;line-height:1.9;" class="reveal reveal-delay-1">
          ILLUME began in a small Lagos atelier in 2018, with nothing but a sewing machine,
          a burning conviction, and a handful of clients who trusted the vision. Our founder
          had spent years studying design in both Lagos and London, learning that luxury
          wasn't a Western monopoly — it was a language anyone could master.
        </p>
        <p style="margin-bottom:1.25rem;line-height:1.9;" class="reveal reveal-delay-2">
          The name ILLUME comes from the French word for "illuminate." Because that's what
          we believe great fashion does — it illuminates who you are, amplifies your presence,
          and makes a room feel your arrival before you say a word.
        </p>
        <p style="line-height:1.9;" class="reveal reveal-delay-3">
          Seven years later, ILLUME has dressed CEOs, brides, musicians, diplomats, and
          creatives across 12 countries. The atelier has grown. The philosophy hasn't moved an inch.
        </p>
      </div>

      <div class="reveal-right" style="position:relative;padding-top:2rem;">
        <!-- Timeline -->
        <div class="timeline">
          <?php
          $milestones = [
            ['year'=>'2018','title'=>'The Atelier Opens','desc'=>'ILLUME is founded in Victoria Island, Lagos with a team of 3.'],
            ['year'=>'2019','title'=>'First Major Bridal Collection','desc'=>'Our debut bridal line receives national press coverage.'],
            ['year'=>'2021','title'=>'International Clients','desc'=>'First clients from the UK and US. The waitlist begins.'],
            ['year'=>'2022','title'=>'Lagos Fashion Week','desc'=>'ILLUME debuts at Lagos Fashion Week to standing ovations.'],
            ['year'=>'2024','title'=>'Consulting Division Launches','desc'=>'We open our fashion consulting arm for brands and individuals.'],
            ['year'=>'2025','title'=>'The ILLUME Platform','desc'=>'We launch our digital platform for a seamless client experience.'],
          ];
          foreach ($milestones as $m): ?>
          <div class="timeline-item">
            <div style="display:flex;align-items:baseline;gap:0.75rem;margin-bottom:0.35rem;">
              <span style="font-family:var(--font-display);font-size:1.1rem;font-weight:700;color:var(--gold);"><?= $m['year'] ?></span>
              <span style="font-weight:600;color:var(--text-primary);font-size:0.95rem;"><?= e($m['title']) ?></span>
            </div>
            <p style="font-size:0.85rem;line-height:1.65;"><?= e($m['desc']) ?></p>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ FOUNDER BIO ════════════════════════════════════════════ -->
<section class="section" style="background:var(--space);border-top:1px solid var(--space-border);border-bottom:1px solid var(--space-border);">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1.4fr;gap:5rem;align-items:center;">

      <!-- Portrait -->
      <div class="reveal-left">
        <div style="
          aspect-ratio:3/4; border-radius:var(--r-2xl);
          border:1px solid var(--gold-glass);
          position:relative;overflow:hidden;
          box-shadow: 0 40px 80px rgba(0,0,0,0.1);
        ">
          <img src="<?= SITE_URL ?>/assets/img/philosophy.png" alt="ILLUME Founder" 
               style="width:100%; height:100%; object-fit:cover;">
               
          <!-- Corner accents -->
          <div style="position:absolute;top:1.5rem;right:1.5rem;width:28px;height:28px;border-top:2px solid #FFF;border-right:2px solid #FFF;"></div>
          <div style="position:absolute;bottom:1.5rem;left:1.5rem;width:28px;height:28px;border-bottom:2px solid #FFF;border-left:2px solid #FFF;"></div>
          
          <!-- Label overlay -->
          <div style="position:absolute; bottom:0; left:0; right:0; padding:2rem; background:linear-gradient(to top, rgba(0,0,0,0.6), transparent);">
             <div class="label-text" style="color:var(--gold-bright);">Creative Visionary</div>
          </div>
        </div>
      </div>

      <div class="reveal-right">
        <div class="label-text" style="margin-bottom:1rem;">Meet the Founder</div>
        <h2 style="margin-bottom:0.5rem;">The Mind<br><span class="shimmer-text">Behind ILLUME</span></h2>
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:2rem;">
          <div style="width:32px;height:1px;background:var(--gold);"></div>
          <span style="font-size:0.88rem;color:var(--text-muted);">Creative Director & Head Designer</span>
        </div>
        <p style="line-height:1.9;margin-bottom:1.25rem;">
          Trained at the Art School of London and Central Saint Martins,
          our founder returned to Nigeria with one mission: to prove that our continent's
          fashion story deserves to be told in the language of luxury.
        </p>
        <p style="line-height:1.9;margin-bottom:1.25rem;">
          With over 15 years in the fashion industry — spanning design, production, styling,
          and brand consulting — she brings a rare combination of technical mastery
          and creative fearlessness to every ILLUME piece.
        </p>
        <p style="line-height:1.9;margin-bottom:2rem;">
          "I want every woman who wears ILLUME to feel like the room was designed around her."
          <span style="display:block;font-size:0.8rem;color:var(--gold);margin-top:0.5rem;font-style:normal;">— Founder, ILLUME</span>
        </p>
        <a href="consultation.php"  class="btn btn--primary">
          <i data-lucide="calendar"></i> Work With Us
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ═══ VALUES ════════════════════════════════════════════════ -->
<section class="section--lg">
  <div class="container">
    <div class="section-header">
      <div class="section-divider"><span class="label-text">What Drives Us</span></div>
      <h2 class="reveal">The ILLUME<br><span class="shimmer-text">Standard</span></h2>
    </div>

    <div class="grid-3" style="gap:1.5rem;">
      <?php
      $values = [
        ['icon'=>'gem',        'color'=>'var(--gold)',   'title'=>'Uncompromising Quality',  'desc'=>'We source only the finest fabrics from Lagos, London, Milan, and beyond. Quality isn\'t a feature — it\'s the foundation.'],
        ['icon'=>'fingerprint','color'=>'var(--plasma)', 'title'=>'Radical Individuality',   'desc'=>'We don\'t do cookie-cutter. Every ILLUME piece is born from the unique DNA of the person wearing it.'],
        ['icon'=>'heart',      'color'=>'#FF6B9D',       'title'=>'Client Devotion',         'desc'=>'Our clients aren\'t customers. They\'re collaborators. Partners in the creative process from the first sketch to the final fitting.'],
        ['icon'=>'zap',        'color'=>'var(--gold)',   'title'=>'Craft as Culture',        'desc'=>'We believe Nigerian craftsmanship stands shoulder-to-shoulder with anything the world has to offer. We\'re proving it, stitch by stitch.'],
        ['icon'=>'globe',      'color'=>'var(--plasma)', 'title'=>'Global Vision',           'desc'=>'Rooted in Lagos. Worn in London, New York, and Accra. ILLUME represents the world-class potential of African fashion.'],
        ['icon'=>'clock',      'color'=>'#FF6B9D',       'title'=>'Timeless Over Trendy',    'desc'=>'Trends fade. Identity endures. We design pieces that will feel just as powerful in 2035 as the day they were made.'],
      ];
      foreach ($values as $i => $v): ?>
      <div class="card card--glass reveal reveal-delay-<?= ($i%3)+1 ?>">
        <div style="
          width:52px;height:52px;border-radius:var(--r-lg);
          background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);
          display:flex;align-items:center;justify-content:center;
          color:<?= $v['color'] ?>;margin-bottom:1.25rem;
        ">
          <i data-lucide="<?= $v['icon'] ?>" style="width:22px;height:22px;"></i>
        </div>
        <h4 style="margin-bottom:0.75rem;"><?= e($v['title']) ?></h4>
        <p style="font-size:0.88rem;line-height:1.75;"><?= e($v['desc']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══ CTA ════════════════════════════════════════════════════ -->
<section class="section" style="background:var(--space);border-top:1px solid var(--space-border);text-align:center;">
  <div class="container">
    <h2 class="reveal" style="margin-bottom:1rem;">Ready to Become<br><span class="shimmer-text">Part of the Story?</span></h2>
    <p class="reveal reveal-delay-1" style="max-width:480px;margin:0 auto 2rem;font-size:1.05rem;">Your journey with ILLUME begins with a single conversation.</p>
    <div class="reveal reveal-delay-2" style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
      <a href="consultation.php" class="btn btn--primary btn--lg"><i data-lucide="calendar"></i>Book Consultation</a>
      <a href="services.php" class="btn btn--ghost btn--lg"><i data-lucide="sparkles"></i>Explore Services</a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
