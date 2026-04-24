'use strict';
// ============================================================
// ILLUME — Main JavaScript
// Custom Cursor · Three.js Particles · Scroll Reveal · Lenis
// ============================================================

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
    ringX += (mouseX - ringX) * 0.11;
    ringY += (mouseY - ringY) * 0.11;
    cursorRing.style.transform = `translate3d(calc(${ringX}px - 50%), calc(${ringY}px - 50%), 0)`;
    requestAnimationFrame(animateCursor);
  })();

  const observe = () => {
    document.querySelectorAll('a, button, .btn, .service-card, .portfolio-item, input, textarea, select, [data-hover]')
      .forEach(el => {
        el.addEventListener('mouseenter', () => cursorRing.classList.add('hovering'));
        el.addEventListener('mouseleave', () => cursorRing.classList.remove('hovering'));
      });
  };
  observe();
  document.addEventListener('DOMContentLoaded', observe);
}

// ─── SCROLL PROGRESS BAR ─────────────────────────────────────
const scrollBar = document.querySelector('.scroll-progress');
if (scrollBar) {
  window.addEventListener('scroll', () => {
    const pct = window.scrollY / (document.body.scrollHeight - window.innerHeight);
    scrollBar.style.transform = `scaleX(${pct})`;
    scrollBar.style.willChange = 'transform';
  }, { passive: true });
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
const mobileClose = document.querySelector('.mobile-close');

if (hamburger && mobileNav) {
  hamburger.addEventListener('click', () => {
    mobileNav.classList.add('open');
    document.body.style.overflow = 'hidden';
  });
  const closeMenu = () => {
    mobileNav.classList.remove('open');
    document.body.style.overflow = '';
  };
  mobileClose?.addEventListener('click', closeMenu);
  mobileNav.querySelectorAll('.nav__link').forEach(l => l.addEventListener('click', closeMenu));
}

// ─── LENIS SMOOTH SCROLL ─────────────────────────────────────
if (typeof Lenis !== 'undefined') {
  const lenis = new Lenis({ lerp: 0.14, smoothWheel: true });
  (function raf(time) {
    lenis.raf(time);
    requestAnimationFrame(raf);
  })(0);
}

// ─── INTERSECTION OBSERVER — SCROLL REVEAL ───────────────────
const revealObs = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
      revealObs.unobserve(entry.target);
    }
  });
}, { threshold: 0.08, rootMargin: '0px 0px -50px 0px' });

document.querySelectorAll('.reveal, .reveal-left, .reveal-right').forEach(el => revealObs.observe(el));

// ─── COUNTER ANIMATION ───────────────────────────────────────
function animateCount(el) {
  const target   = parseInt(el.dataset.target, 10);
  const duration = 1800;
  const prefix   = el.dataset.prefix || '';
  const suffix   = el.dataset.suffix || '';
  const start    = performance.now();

  (function step(now) {
    const progress = Math.min((now - start) / duration, 1);
    const ease = 1 - Math.pow(1 - progress, 4);
    el.textContent = prefix + Math.round(ease * target).toLocaleString() + suffix;
    if (progress < 1) requestAnimationFrame(step);
  })(start);
}

const counterObs = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      animateCount(entry.target);
      counterObs.unobserve(entry.target);
    }
  });
}, { threshold: 0.5 });

document.querySelectorAll('[data-counter]').forEach(el => counterObs.observe(el));

// ─── THREE.JS HERO PARTICLES ─────────────────────────────────
const heroCanvas = document.getElementById('hero-canvas');
if (heroCanvas && typeof THREE !== 'undefined') {
  const renderer = new THREE.WebGLRenderer({ canvas: heroCanvas, antialias: false, alpha: true });
  renderer.setPixelRatio(Math.min(devicePixelRatio, 1.5));
  renderer.setSize(window.innerWidth, window.innerHeight);
  renderer.setClearColor(0xFAFAFA, 0);

  const scene  = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(70, window.innerWidth / window.innerHeight, 0.1, 100);
  camera.position.z = 6;

  const COUNT = window.innerWidth < 768 ? 400 : 1000;
  const positions = new Float32Array(COUNT * 3);
  const colors    = new Float32Array(COUNT * 3);

  const bronze     = new THREE.Color('#8B7355');
  const indigo     = new THREE.Color('#52177c');
  const softGray   = new THREE.Color('#D1D5DB');

  for (let i = 0; i < COUNT; i++) {
    const i3 = i * 3;
    positions[i3]     = (Math.random() - 0.5) * 22;
    positions[i3+1]   = (Math.random() - 0.5) * 16;
    positions[i3+2]   = (Math.random() - 0.5) * 12;

    const r = Math.random();
    const c = r < 0.30 ? bronze : r < 0.75 ? indigo : softGray;
    colors[i3] = c.r; colors[i3+1] = c.g; colors[i3+2] = c.b;
  }

  function createCircleTexture() {
    const canvas = document.createElement('canvas');
    canvas.width = 64; canvas.height = 64;
    const ctx = canvas.getContext('2d');
    ctx.beginPath(); ctx.arc(32, 32, 30, 0, Math.PI * 2);
    ctx.fillStyle = '#ffffff'; ctx.fill();
    return new THREE.CanvasTexture(canvas);
  }

  const geo = new THREE.BufferGeometry();
  geo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
  geo.setAttribute('color',    new THREE.BufferAttribute(colors, 3));

  const mat = new THREE.PointsMaterial({
    size: 0.035,
    vertexColors: true,
    transparent: true,
    opacity: 0.35,
    sizeAttenuation: true,
    map: createCircleTexture(),
    alphaTest: 0.05
  });

  const particles = new THREE.Points(geo, mat);
  scene.add(particles);

  let mox = 0, moy = 0;
  window.addEventListener('mousemove', e => {
    mox = (e.clientX / window.innerWidth  - 0.5) * 1.2;
    moy = (e.clientY / window.innerHeight - 0.5) * 1.0;
  }, { passive: true });

  window.addEventListener('resize', () => {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
  });

  const clock = new THREE.Clock();
  (function tick() {
    const t = clock.getElapsedTime();
    particles.rotation.y = t * 0.025 + mox * 0.35;
    particles.rotation.x = t * 0.008 + moy * 0.2;
    renderer.render(scene, camera);
    requestAnimationFrame(tick);
  })();
}

// ─── HOLOGRAPHIC CARD TILT ───────────────────────────────────
document.querySelectorAll('.service-card, .card--tilt').forEach(card => {
  card.addEventListener('mousemove', e => {
    const { left, top, width, height } = card.getBoundingClientRect();
    const x = ((e.clientX - left) / width  - 0.5) * 2;
    const y = ((e.clientY - top)  / height - 0.5) * 2;
    card.style.transform = `perspective(900px) rotateX(${y * -6}deg) rotateY(${x * 6}deg) translateY(-6px)`;
  });
  card.addEventListener('mouseleave', () => card.style.transform = '');
});

// ─── MULTI-STEP CONSULTATION FORM ────────────────────────────
const consultForm = document.getElementById('consult-form');
if (consultForm) {
  let current = 0;
  const panels      = [...consultForm.querySelectorAll('.form-panel')];
  const stepBars    = [...document.querySelectorAll('.form-step')];
  const stepLabels  = [...document.querySelectorAll('.form-step-label')];

  function goStep(n) {
    panels.forEach((p, i) => p.classList.toggle('hidden', i !== n));
    stepBars.forEach((b, i)  => {
      b.classList.toggle('active', i === n);
      b.classList.toggle('done',   i < n);
    });
    stepLabels.forEach((l, i) => l.classList.toggle('active', i === n));
    current = n;
  }

  consultForm.querySelectorAll('[data-next]').forEach(btn =>
    btn.addEventListener('click', () => { if (current < panels.length - 1) goStep(current + 1); }));
  consultForm.querySelectorAll('[data-prev]').forEach(btn =>
    btn.addEventListener('click', () => { if (current > 0) goStep(current - 1); }));

  goStep(0);
}

// ─── PORTFOLIO FILTER ────────────────────────────────────────
document.querySelectorAll('[data-filter]').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const cat = btn.dataset.filter;
    document.querySelectorAll('[data-category]').forEach(item => {
      const show = cat === 'all' || item.dataset.category === cat;
      item.style.opacity   = show ? '1' : '0.15';
      item.style.transform = show ? 'scale(1)' : 'scale(0.93)';
      item.style.transition = 'all 0.4s ease';
    });
  });
});

// ─── MODALS ──────────────────────────────────────────────────
document.querySelectorAll('[data-modal-open]').forEach(btn => {
  btn.addEventListener('click', () => {
    const id = btn.dataset.modalOpen;
    document.getElementById(id)?.classList.add('open');
    document.body.style.overflow = 'hidden';
  });
});
document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
  backdrop.addEventListener('click', e => {
    if (e.target === backdrop) {
      backdrop.classList.remove('open');
      document.body.style.overflow = '';
    }
  });
});
document.querySelectorAll('[data-modal-close]').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.closest('.modal-backdrop')?.classList.remove('open');
    document.body.style.overflow = '';
  });
});

// ─── LUCIDE ICONS ────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  if (typeof lucide !== 'undefined') lucide.createIcons();
});

// ─── DASHBOARD SIDEBAR TOGGLE ────────────────────────────────
const sidebarToggle = document.querySelector('.sidebar-toggle');
const sidebar       = document.querySelector('.sidebar');
const sidebarOverlay = document.querySelector('.sidebar-overlay');

if (sidebarToggle && sidebar) {
  sidebarToggle.addEventListener('click', () => {
    sidebar.classList.toggle('open');
    sidebarOverlay?.classList.toggle('open');
    document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
  });
  sidebarOverlay?.addEventListener('click', () => {
    sidebar.classList.remove('open');
    sidebarOverlay.classList.remove('open');
    document.body.style.overflow = '';
  });
}
