'use strict';

/**
 * ILLUME — Main Interaction Engine
 * Minimalist, Premium, Performant.
 */

// ─── PAGE LOADER ─────────────────────────────────────────────
const pageLoader = document.getElementById('page-loader');
window.addEventListener('load', () => {
  setTimeout(() => pageLoader?.classList.add('hidden'), 900);
});

// ─── CUSTOM CURSOR ───────────────────────────────────────────
const cursorDot  = document.querySelector('.cursor-dot');
const cursorRing = document.querySelector('.cursor-ring');

if (cursorDot && cursorRing) {
  let mouseX = -200, mouseY = -200;
  let ringX  = -200, ringY  = -200;

  document.addEventListener('mousemove', e => {
    mouseX = e.clientX;
    mouseY = e.clientY;
    cursorDot.style.transform = `translate3d(calc(${mouseX}px - 50%), calc(${mouseY}px - 50%), 0)`;
  });

  (function animateCursor() {
    ringX += (mouseX - ringX) * 0.15; /* Increased from 0.11 for higher responsiveness */
    ringY += (mouseY - ringY) * 0.15;
    cursorRing.style.transform = `translate3d(calc(${ringX}px - 50%), calc(${ringY}px - 50%), 0)`;
    requestAnimationFrame(animateCursor);
  })();

  const observe = () => {
    document.querySelectorAll('a, button, .btn, .service-card, .card, .portfolio-item, input, textarea, select')
      .forEach(el => {
        el.addEventListener('mouseenter', () => cursorRing.classList.add('hovering'));
        el.addEventListener('mouseleave', () => cursorRing.classList.remove('hovering'));
      });
  };
  observe();
  document.addEventListener('DOMContentLoaded', observe);
}

// ─── NAVIGATION ──────────────────────────────────────────────
const nav = document.querySelector('.nav');
if (nav) {
  const onScroll = () => nav.classList.toggle('scrolled', window.scrollY > 50);
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
}

// ─── MOBILE NAV ──────────────────────────────────────────────
const hamburger = document.querySelector('.nav__hamburger');
const mobileNav = document.querySelector('.nav__mobile');

if (hamburger && mobileNav) {
  hamburger.addEventListener('click', () => {
    mobileNav.classList.toggle('open');
    document.body.style.overflow = mobileNav.classList.contains('open') ? 'hidden' : '';
  });
}

// ─── LENIS SMOOTH SCROLL ─────────────────────────────────────
if (typeof Lenis !== 'undefined') {
  const lenis = new Lenis({ 
    lerp: 0.14, /* Increased from 0.1 to improve 'refresh' feel and responsiveness */
    smoothWheel: true 
  });
  (function raf(time) {
    lenis.raf(time);
    requestAnimationFrame(raf);
  })(0);
}

// ─── SCROLL REVEAL ───────────────────────────────────────────
const revealObs = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll('.reveal, .reveal-left, .reveal-right').forEach(el => revealObs.observe(el));

// ─── LUCIDE ICONS ────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  if (typeof lucide !== 'undefined') lucide.createIcons();
});
