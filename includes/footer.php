<?php
// ============================================================
// ILLUME — Public Footer
// ============================================================
?>

<!-- ═══ FOOTER ═══════════════════════════════════════════════ -->
<footer class="footer">
  <div class="container">

    <div class="footer__grid">
      <!-- Brand -->
      <div>
        <span class="footer__logo">ILLUME</span>
        <p class="footer__tagline">
          We don't just dress bodies — we architect identities.<br>
          Fashion is language. We make yours unforgettable.
        </p>
        <div class="footer__social">
          <a href="#" class="footer__social-link" aria-label="Instagram">
            <i data-lucide="instagram"></i>
          </a>
          <a href="#" class="footer__social-link" aria-label="Facebook">
            <i data-lucide="facebook"></i>
          </a>
          <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>" class="footer__social-link" aria-label="WhatsApp" target="_blank" rel="noopener">
            <i data-lucide="message-circle"></i>
          </a>
          <a href="#" class="footer__social-link" aria-label="Pinterest">
            <i data-lucide="image"></i>
          </a>
        </div>
      </div>

      <!-- Navigation -->
      <div>
        <h6 class="footer__heading">Explore</h6>
        <nav class="footer__links">
          <a href="<?= SITE_URL ?>/"              class="footer__link">Home</a>
          <a href="<?= SITE_URL ?>/about.php"     class="footer__link">Our Story</a>
          <a href="<?= SITE_URL ?>/services.php"  class="footer__link">Services</a>
          <a href="<?= SITE_URL ?>/portfolio.php" class="footer__link">Portfolio</a>
          <a href="<?= SITE_URL ?>/contact.php"   class="footer__link">Contact</a>
        </nav>
      </div>

      <!-- Services -->
      <div>
        <h6 class="footer__heading">Services</h6>
        <nav class="footer__links">
          <a href="<?= SITE_URL ?>/services.php#bespoke"            class="footer__link">Bespoke</a>
          <a href="<?= SITE_URL ?>/services.php#asoebi"             class="footer__link">Asoebi</a>
          <a href="<?= SITE_URL ?>/services.php#african-wears"      class="footer__link">African Wears</a>
          <a href="<?= SITE_URL ?>/services.php#beading"            class="footer__link">Beading</a>
          <a href="<?= SITE_URL ?>/services.php#casuals"            class="footer__link">Casuals</a>
          <a href="<?= SITE_URL ?>/services.php#dressmaking"        class="footer__link">Custom Dressmaking</a>
          <a href="<?= SITE_URL ?>/services.php#consultancy"        class="footer__link">Fashion Consultancy</a>
          <a href="<?= SITE_URL ?>/services.php#illustration"       class="footer__link">Fashion Illustration</a>
        </nav>
      </div>

      <!-- Contact -->
      <div>
        <h6 class="footer__heading">Connect</h6>
        <nav class="footer__links">
          <a href="mailto:<?= SITE_EMAIL ?>" class="footer__link" style="display:flex;align-items:center;gap:0.4rem;">
            <i data-lucide="mail" style="width:13px;height:13px;"></i> <?= SITE_EMAIL ?>
          </a>
          <a href="tel:<?= preg_replace('/\s+/','',(string)SITE_PHONE) ?>" class="footer__link" style="display:flex;align-items:center;gap:0.4rem;">
            <i data-lucide="phone" style="width:13px;height:13px;"></i> <?= SITE_PHONE ?>
          </a>
          <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>?text=Hello%20ILLUME!" class="footer__link" style="display:flex;align-items:center;gap:0.4rem;" target="_blank" rel="noopener">
            <i data-lucide="message-circle" style="width:13px;height:13px;"></i> WhatsApp Us
          </a>
          <a href="<?= SITE_URL ?>/consultation.php" class="footer__link" style="display:flex;align-items:center;gap:0.4rem;">
            <i data-lucide="calendar" style="width:13px;height:13px;"></i> Book a Consultation
          </a>
        </nav>
      </div>
    </div>

    <!-- Bottom Bar -->
    <div class="footer__bottom">
      <span>&copy; <?= date('Y') ?> ILLUME. All rights reserved.</span>
      <span>Crafted in Abuja &nbsp;·&nbsp; Worn Worldwide</span>
      <span style="display:flex;gap:1rem;">
        <a href="#" class="footer__link" style="font-size:0.75rem;">Privacy Policy</a>
        <a href="#" class="footer__link" style="font-size:0.75rem;">Terms</a>
      </span>
    </div>

  </div>
</footer>

<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<!-- Main JS -->
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
<?php if (isset($extra_js)) echo $extra_js; ?>

</body>
</html>
