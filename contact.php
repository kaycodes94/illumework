<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Contact';
$page_desc = 'Get in touch with ILLUME. Visit our Abuja atelier or reach us by email, phone, or WhatsApp.';

$sent = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_form'])) {
  if (!verify_csrf()) {
    $error = 'Security check failed.';
  } else {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if (!$name || !$email || !$message) {
      $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $error = 'Invalid email address.';
    } else {
      // In production: send email via mail() or SMTP
      $sent = true;
    }
  }
}

include __DIR__ . '/includes/header.php';
?>

<!-- ═══ HERO ═════════════════════════════════════════════════ -->
<section style="
  min-height: 40svh; display: flex; align-items: center;
  padding-top: var(--nav-h); position: relative; overflow: hidden;
  background: white; border-bottom: 1px solid var(--divider);
">
  <div class="container" style="position: relative; z-index: 1;">
    <div class="label-text" style="margin-bottom: var(--s2);">Get In Touch</div>
    <h1 style="font-size: clamp(2.5rem, 8vw, 4.5rem); font-weight: 800; line-height: 1.1; letter-spacing: -0.04em; margin-bottom: var(--s6);">
      Let's Start a<br><span class="gold-text">Conversation.</span>
    </h1>
    <p style="font-size: 1.15rem; max-width: 500px; line-height: 1.6; color: var(--warm-taupe);">
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
          ['icon' => 'mail', 'label' => 'Email Us', 'value' => SITE_EMAIL, 'link' => 'mailto:' . SITE_EMAIL, 'color' => 'var(--champagne)'],
          ['icon' => 'phone', 'label' => 'Call Us', 'value' => SITE_PHONE, 'link' => 'tel:' . preg_replace('/\s+/', '', SITE_PHONE), 'color' => 'var(--champagne)'],
          ['icon' => 'message-circle', 'label' => 'WhatsApp', 'value' => '+' . WHATSAPP_NUMBER, 'link' => 'https://wa.me/' . WHATSAPP_NUMBER . '?text=Hello%20ILLUME!', 'color' => '#25D366'],
          ['icon' => 'map-pin', 'label' => 'Business Locations', 'value' => 'Kubwa Abuja & Abakaliki Ebonyi', 'link' => '#map', 'color' => 'var(--champagne)'],
        ];
        foreach ($contacts as $c): ?>
          <a href="<?= e($c['link']) ?>" class="card card--glass" style="
          display:flex;align-items:center;gap:1rem;
          padding:1.25rem 1.5rem;margin-bottom:1rem;
          text-decoration:none;transition:all 0.3s;
        " <?= str_starts_with($c['link'], 'http') ? 'target="_blank" rel="noopener"' : '' ?>>
            <div style="
            width: 48px; height: 48px; border-radius: var(--r); /* Rounded corners */
            background: var(--soft-ivory); border: 1px solid var(--divider);
            display: flex; align-items: center; justify-content: center;
            color: <?= $c['color'] ?>;
          ">
              <i data-lucide="<?= e($c['icon']) ?>" style="width: 22px; height: 22px;"></i>
            </div>
            <div>
              <div
                style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--text-muted);margin-bottom:0.2rem;">
                <?= e($c['label']) ?></div>
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
            'Mon – Fri' => '9:00 AM – 6:00 PM',
            'Saturday' => '10:00 AM – 4:00 PM',
            'Sunday' => 'By Appointment Only',
          ];
          foreach ($hours as $day => $time): ?>
            <div
              style="display:flex;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid var(--divider);">
              <span style="font-size:0.85rem;color:var(--warm-taupe);"><?= $day ?></span>
              <span style="font-size:0.85rem;font-weight:500; color: var(--black);"><?= $time ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- RIGHT: Contact Form -->
      <div>
        <div class="label-text" style="margin-bottom:1.5rem;">Send a Message</div>

        <?php if ($sent): ?>
          <div style="text-align:center;padding:3rem 2rem;">
            <div
              style="width:64px;height:64px;border-radius:50%;background: var(--champagne);display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;box-shadow:0 10px 20px rgba(213, 168, 76, 0.2);">
              <i data-lucide="check" style="width:28px;height:28px;color:white;"></i>
            </div>
            <h3 style="margin-bottom:0.75rem; color: var(--black);">Message Sent!</h3>
            <p style="color:var(--warm-taupe);">We'll get back to you within 24 hours. Talk soon.</p>
          </div>
        <?php else: ?>

          <?php if ($error): ?>
            <div class="alert alert--error" style="margin-bottom:1.5rem;"><i data-lucide="alert-circle"></i>
              <span><?= e($error) ?></span></div>
          <?php endif; ?>

          <form method="POST" action="contact.php" style="padding:2.5rem;border-radius:var(--r-lg); background: white; border: 1px solid var(--divider); box-shadow: var(--shadow-md);">
            <?= csrf_field() ?>
            <input type="hidden" name="contact_form" value="1">

            <div class="grid-2" style="gap:1rem;">
              <div class="form-group">
                <label class="form-label" for="c_name">Full Name *</label>
                <input type="text" id="c_name" name="name" class="form-input" placeholder="Your name" required
                  value="<?= e($_POST['name'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label class="form-label" for="c_email">Email *</label>
                <input type="email" id="c_email" name="email" class="form-input" placeholder="your@email.com" required
                  value="<?= e($_POST['email'] ?? '') ?>">
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="c_subject">Subject</label>
              <input type="text" id="c_subject" name="subject" class="form-input" placeholder="What's this about?"
                value="<?= e($_POST['subject'] ?? '') ?>">
            </div>

            <div class="form-group">
              <label class="form-label" for="c_message">Message *</label>
              <textarea id="c_message" name="message" class="form-textarea" rows="6"
                placeholder="Tell us what's on your mind…" required><?= e($_POST['message'] ?? '') ?></textarea>
            </div>

            <div style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center;justify-content:space-between;">
              <span style="font-size:0.8rem;color:var(--text-muted);">Or reach us instantly via WhatsApp</span>
              <div style="display:flex;gap:0.75rem;">
                <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>?text=Hello%20ILLUME!" class="btn btn--ghost" target="_blank"
                  rel="noopener">
                  <i data-lucide="message-circle"></i> WhatsApp
                </a>
                <button type="submit" class="btn btn--primary">
                  <i data-lucide="send"></i> Send Message
                </button>
              </div>
            </div>
          </form>
        <?php endif; ?>

        <!-- Map -->
        <div id="map" style="
          margin-top:2rem;
          border:1px solid var(--divider);
          border-radius:var(--r-lg);
          height:320px;
          position:relative;overflow:hidden;
          box-shadow: var(--shadow-sm);
        ">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126071.16694602816!2d7.262512613149887!3d9.130147660232435!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x104e766f7d08899b%3A0xe963d5961d668388!2sKubwa%2C%20Abuja!5e0!3m2!1sen!2sng!4v1713870000000!5m2!1sen!2sng"
            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
      </div>

    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>