<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Contact';
$page_desc  = 'Get in touch with ILLUME. Visit our Lagos atelier or reach us by email, phone, or WhatsApp.';

$sent  = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_form'])) {
    if (!verify_csrf()) { $error = 'Security check failed.'; }
    else {
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        if (!$name || !$email || !$message) { $error = 'Please fill in all required fields.'; }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $error = 'Invalid email address.'; }
        else {
            // In production: send email via mail() or SMTP
            $sent = true;
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<!-- ═══ HERO ═════════════════════════════════════════════════ -->
<section style="padding:calc(var(--nav-h)+3rem) 0 3rem;background:var(--space);border-bottom:1px solid var(--space-border);position:relative;overflow:hidden;">
  <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(201,168,76,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(201,168,76,0.03) 1px,transparent 1px);background-size:60px 60px;pointer-events:none;"></div>
  <div class="container" style="position:relative;z-index:1;">
    <div class="label-text" style="margin-bottom:1rem;">Get In Touch</div>
    <h1 style="font-size:clamp(3rem,7vw,5.5rem);line-height:0.95;margin-bottom:1.5rem;">
      Let's Start a<br><span class="shimmer-text">Conversation.</span>
    </h1>
    <p style="font-size:1.05rem;max-width:460px;line-height:1.8;color:var(--text-secondary);">
      Whether you have a question, a vision, or just want to say hello —
      we're always on the other side of the line.
    </p>
  </div>
</section>

<!-- ═══ CONTACT CONTENT ═══════════════════════════════════════ -->
<section class="section--lg">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1.4fr;gap:5rem;align-items:start;">

      <!-- LEFT: Info Cards -->
      <div>
        <div class="label-text" style="margin-bottom:1.5rem;">Contact Information</div>

        <?php
        $contacts = [
          ['icon'=>'mail',           'label'=>'Email Us',        'value'=>SITE_EMAIL,           'link'=>'mailto:'.SITE_EMAIL,        'color'=>'var(--gold)'],
          ['icon'=>'phone',          'label'=>'Call Us',         'value'=>SITE_PHONE,           'link'=>'tel:'.preg_replace('/\s+/','', SITE_PHONE), 'color'=>'var(--plasma)'],
          ['icon'=>'message-circle', 'label'=>'WhatsApp',        'value'=>'+' . WHATSAPP_NUMBER,'link'=>'https://wa.me/'.WHATSAPP_NUMBER.'?text=Hello%20ILLUME!', 'color'=>'#25D366'],
          ['icon'=>'map-pin',        'label'=>'Visit Our Atelier','value'=>'Victoria Island, Lagos, Nigeria', 'link'=>'#map', 'color'=>'var(--gold)'],
        ];
        foreach ($contacts as $c): ?>
        <a href="<?= e($c['link']) ?>" class="card card--glass" style="
          display:flex;align-items:center;gap:1rem;
          padding:1.25rem 1.5rem;margin-bottom:1rem;
          text-decoration:none;transition:all 0.3s;
        "
        <?= str_starts_with($c['link'],'http') ? 'target="_blank" rel="noopener"' : '' ?>
        >
          <div style="
            width:46px;height:46px;border-radius:var(--r-lg);flex-shrink:0;
            background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);
            display:flex;align-items:center;justify-content:center;
            color:<?= $c['color'] ?>;
          ">
            <i data-lucide="<?= e($c['icon']) ?>" style="width:20px;height:20px;"></i>
          </div>
          <div>
            <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--text-muted);margin-bottom:0.2rem;"><?= e($c['label']) ?></div>
            <div style="font-size:0.9rem;font-weight:500;color:var(--text-primary);"><?= e($c['value']) ?></div>
          </div>
          <i data-lucide="arrow-right" style="width:16px;height:16px;color:var(--text-muted);margin-left:auto;"></i>
        </a>
        <?php endforeach; ?>

        <!-- Hours -->
        <div class="card card--glass" style="padding:1.5rem;margin-top:0.5rem;">
          <div class="label-text" style="margin-bottom:1rem;">Studio Hours</div>
          <?php
          $hours = [
            'Mon – Fri'  => '9:00 AM – 6:00 PM',
            'Saturday'   => '10:00 AM – 4:00 PM',
            'Sunday'     => 'By Appointment Only',
          ];
          foreach ($hours as $day => $time): ?>
          <div style="display:flex;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid var(--space-border);">
            <span style="font-size:0.85rem;color:var(--text-muted);"><?= $day ?></span>
            <span style="font-size:0.85rem;font-weight:500;"><?= $time ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- RIGHT: Contact Form -->
      <div>
        <div class="label-text" style="margin-bottom:1.5rem;">Send a Message</div>

        <?php if ($sent): ?>
        <div style="text-align:center;padding:3rem 2rem;">
          <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,var(--gold),var(--gold-bright));display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;box-shadow:0 0 30px var(--gold-glow);">
            <i data-lucide="check" style="width:28px;height:28px;color:var(--void);"></i>
          </div>
          <h3 style="margin-bottom:0.75rem;">Message Sent!</h3>
          <p style="color:var(--text-secondary);">We'll get back to you within 24 hours. Talk soon.</p>
        </div>
        <?php else: ?>

        <?php if ($error): ?>
        <div class="alert alert--error" style="margin-bottom:1.5rem;"><i data-lucide="alert-circle"></i> <span><?= e($error) ?></span></div>
        <?php endif; ?>

        <form method="POST" action="contact.php" class="glass" style="padding:2.5rem;border-radius:var(--r-2xl);">
          <?= csrf_field() ?>
          <input type="hidden" name="contact_form" value="1">

          <div class="grid-2" style="gap:1rem;">
            <div class="form-group">
              <label class="form-label" for="c_name">Full Name *</label>
              <input type="text" id="c_name" name="name" class="form-input" placeholder="Your name" required value="<?= e($_POST['name']??'') ?>">
            </div>
            <div class="form-group">
              <label class="form-label" for="c_email">Email *</label>
              <input type="email" id="c_email" name="email" class="form-input" placeholder="your@email.com" required value="<?= e($_POST['email']??'') ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="c_subject">Subject</label>
            <input type="text" id="c_subject" name="subject" class="form-input" placeholder="What's this about?" value="<?= e($_POST['subject']??'') ?>">
          </div>

          <div class="form-group">
            <label class="form-label" for="c_message">Message *</label>
            <textarea id="c_message" name="message" class="form-textarea" rows="6" placeholder="Tell us what's on your mind…" required><?= e($_POST['message']??'') ?></textarea>
          </div>

          <div style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center;justify-content:space-between;">
            <span style="font-size:0.8rem;color:var(--text-muted);">Or reach us instantly via WhatsApp</span>
            <div style="display:flex;gap:0.75rem;">
              <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>?text=Hello%20ILLUME!" class="btn btn--ghost" target="_blank" rel="noopener">
                <i data-lucide="message-circle"></i> WhatsApp
              </a>
              <button type="submit" class="btn btn--primary">
                <i data-lucide="send"></i> Send Message
              </button>
            </div>
          </div>
        </form>
        <?php endif; ?>

        <!-- Map Placeholder -->
        <div id="map" style="
          margin-top:2rem;
          background:var(--space);
          border:1px solid var(--space-border);
          border-radius:var(--r-xl);
          height:220px;
          display:flex;align-items:center;justify-content:center;
          position:relative;overflow:hidden;
        ">
          <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(201,168,76,0.04) 1px,transparent 1px),linear-gradient(90deg,rgba(201,168,76,0.04) 1px,transparent 1px);background-size:30px 30px;"></div>
          <div style="text-align:center;position:relative;z-index:1;">
            <i data-lucide="map-pin" style="width:32px;height:32px;color:var(--gold);margin-bottom:0.75rem;"></i>
            <div style="font-size:0.9rem;font-weight:600;">Victoria Island, Lagos</div>
            <div style="font-size:0.8rem;color:var(--text-muted);margin-top:0.25rem;">Nigeria</div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
