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
      Born From<br><span class="shimmer-text">Abuja.</span><br>Worn By the<br>World.
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
      <div style="grid-column: span 2; max-width: 900px; margin: 0 auto;">
        <div class="label-text" style="margin-bottom:1.5rem;">Vision & Heritage</div>
        <h2 style="margin-bottom:2.5rem;" class="reveal">
          What makes ILLUME unique?<br>
          <span class="shimmer-text">We illuminate identity.</span>
        </h2>
        
        <div style="display:grid; grid-template-columns:1.2fr 1fr; gap:4rem;">
          <div>
            <p style="margin-bottom:1.5rem; line-height:2; font-size:1.15rem; color:var(--text-primary);" class="reveal">
              <strong>Illume by Light Peace and Zan</strong> is an African luxury fashion brand dedicated to illuminating identity through refined craftsmanship and cultural elegance.
            </p>
            <p style="margin-bottom:1.5rem; line-height:1.9;" class="reveal reveal-delay-1">
              Born from Light Peace Limited, Illume exists at the intersection of heritage and modernity, translating African narratives into timeless, wearable art. Each garment is intentionally designed, honoring tradition while embracing contemporary global standards.
            </p>
          </div>
          <div>
            <p style="margin-bottom:1.5rem; line-height:1.9;" class="reveal reveal-delay-2">
              We believe fashion should not only adorn the body, but reflect dignity, confidence, and peace. To wear Illume is to carry light quietly, confidently, and proudly.
            </p>
            <div class="card card--glass" style="padding:1.5rem; border-left:3px solid var(--gold);">
              <p style="font-style:italic; line-height:1.8; font-size:0.95rem;" class="reveal reveal-delay-3">
                "It is a Light Peace / Zan partnership that brings different strengths and creativity into one piece, creating a synergy that transcends standard luxury."
              </p>
            </div>
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
        <div class="label-text" style="margin-bottom:1rem;">Origin & Evolution</div>
        <h2 style="margin-bottom:1.5rem;">The Journey to<br><span class="shimmer-text">Illumination</span></h2>
        
        <p style="line-height:1.9;margin-bottom:1.5rem;font-size:0.95rem;">
          Illume by Light Peace and Zan traces its roots back to early 2018, when what is now 
          a growing fashion house began as a simple but deeply intentional vision. At the time, 
          the journey did not start with abundance or structure — it began with conviction. 
        </p>
        
        <p style="line-height:1.9;margin-bottom:1.5rem;font-size:0.95rem;">
          In those early days, the foundation of what would become <strong>Light Peace Limited</strong> 
          was laid not as a grand institution, but as a purpose-driven craft. Each piece created 
          during that period carried more than design; it carried learning, discipline, and a 
          growing understanding that excellence is not an event, but a process.
        </p>
        
        <p style="line-height:1.9;margin-bottom:1.5rem;font-size:0.95rem;">
          Out of this evolution, <strong>Illume by Light Peace</strong> emerged as a defining expression of 
          that vision — a dedicated arm focused on African luxury, cultural identity, and 
          refined craftsmanship. Illume represents a transition:
        </p>

        <div style="display:flex; flex-direction:column; gap:0.75rem; margin-bottom:2rem; padding-left:1rem; border-left:2px solid var(--gold);">
          <div style="font-weight:600; font-size:1.1rem; color:var(--text-primary);">From Creation to Curation</div>
          <div style="font-weight:600; font-size:1.1rem; color:var(--gold-bright);">From Expression to Illumination</div>
        </div>

        <p style="line-height:1.9; font-size:0.95rem;">
          Today, Illume by Light Peace stands as a brand built on years of intentional growth — 
          committed to elevating African heritage into global luxury, while remaining grounded 
          in its core philosophy:
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ═══ VALUES ════════════════════════════════════════════════ -->
<section class="section--lg">
  <div class="container">
    <div class="section-header">
      <div class="section-divider"><span class="label-text">The ILLUME Foundation</span></div>
      <h2 class="reveal">Olewuezi Ikedichukwu Peace<br><span class="shimmer-text">& Susan Mtsevah</span></h2>
    </div>

    <!-- Vision & Mission -->
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:3rem; margin-bottom:5rem;">
      <div class="card card--glass reveal" style="padding:2.5rem; border-top:2px solid var(--gold);">
        <div class="label-text" style="margin-bottom:1rem; color:var(--gold);">Vision</div>
        <p style="font-size:1.15rem; line-height:1.75; font-weight:500;">
          To become a globally recognized African luxury fashion brand that illuminates identity, elevates culture, and expresses excellence through timeless, purposeful design.
        </p>
      </div>
      <div class="card card--glass reveal reveal-delay-1" style="padding:2.5rem; border-top:2px solid var(--aura);">
        <div class="label-text" style="margin-bottom:1rem; color:var(--aura);">Mission</div>
        <p style="font-size:1.15rem; line-height:1.75; font-weight:500;">
          To create luxury fashion pieces that blend African heritage, refined craftsmanship, and modern elegance, empowering individuals to express confidence, dignity, and inner radiance—while building a legacy rooted in light, peace, and excellence.
        </p>
      </div>
    </div>

    <div class="section-header">
      <div class="section-divider"><span class="label-text">Core Values</span></div>
    </div>

    <div class="grid-3" style="gap:1.5rem;">
      <?php
      $values = [
        ['icon'=>'sparkles',    'title'=>'Intentional Excellence',        'desc'=>'We do not create by chance—every detail is deliberate, every finish refined.'],
        ['icon'=>'crown',       'title'=>'Honored Heritage',              'desc'=>'Our culture is not a trend; it is a treasure we preserve and elevate.'],
        ['icon'=>'zap',         'title'=>'Quiet Power',                   'desc'=>'True luxury does not shout—it radiates with confidence and grace.'],
        ['icon'=>'fingerprint', 'title'=>'Authentic Identity',            'desc'=>'We design to express, not impress—remaining true to our roots and vision.'],
        ['icon'=>'award',       'title'=>'Craftsmanship Without Compromise','desc'=>'Quality is not an option; it is our standard.'],
        ['icon'=>'heart',       'title'=>'Purpose Beyond Fashion',        'desc'=>'Every creation carries meaning—beyond fabric, beyond form.'],
        ['icon'=>'hourglass',   'title'=>'Legacy Mindset',                'desc'=>'We build for impact that outlives seasons and speaks across generations.'],
      ];
      foreach ($values as $i => $v): ?>
      <div class="card card--glass reveal reveal-delay-<?= ($i%3)+1 ?>" style="padding:2rem;">
        <div style="
          width:52px;height:52px;border-radius:var(--r-lg);
          background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);
          display:flex;align-items:center;justify-content:center;
          color:var(--gold);margin-bottom:1.25rem;
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
