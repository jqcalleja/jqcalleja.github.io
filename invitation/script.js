/* ---------- Scroll reveal ---------- */
const revealEls = document.querySelectorAll('.reveal, .reveal-scale');
const io = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            io.unobserve(entry.target);
        }
    });
}, { threshold: 0.15, rootMargin: '0px 0px -8% 0px' });
revealEls.forEach(el => io.observe(el));

/* ---------- Countdown ---------- */
// REPLACE with your actual wedding date & time, in ISO format
const weddingDate = new Date('2026-12-12T15:00:00');

function updateCountdown() {
    const now = new Date();
    let diff = weddingDate - now;
    if (diff < 0) diff = 0;

    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
    const mins = Math.floor((diff / (1000 * 60)) % 60);
    const secs = Math.floor((diff / 1000) % 60);

    document.getElementById('cd-days').textContent = String(days).padStart(2, '0');
    document.getElementById('cd-hours').textContent = String(hours).padStart(2, '0');
    document.getElementById('cd-mins').textContent = String(mins).padStart(2, '0');
    document.getElementById('cd-secs').textContent = String(secs).padStart(2, '0');
}
updateCountdown();
setInterval(updateCountdown, 1000);

/* ---------- RSVP form (visual only — hook up to your backend/Formspree/Google Form) ---------- */
const rsvpForm = document.getElementById('rsvp-form');
const rsvpThanks = document.getElementById('rsvp-thanks');
rsvpForm.addEventListener('submit', function (e) {
    e.preventDefault();
    // TODO: send form data to your endpoint of choice here
    rsvpForm.querySelectorAll('input, textarea, button').forEach(el => el.style.display = 'none');
    rsvpThanks.style.display = 'block';
});

/* ---------- Scroll-drawn gold thread (signature element) ---------- */
(function () {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const wrap = document.createElement('div');
    wrap.id = 'thread-wrap';
    wrap.innerHTML = `<svg id="thread-svg" preserveAspectRatio="none"><path id="thread-path"/></svg>`;
    document.body.prepend(wrap);

    function buildPath() {
        const docHeight = document.documentElement.scrollHeight;
        wrap.style.height = docHeight + 'px';
        const svg = document.getElementById('thread-svg');
        svg.setAttribute('viewBox', `0 0 100 ${docHeight}`);
        svg.style.height = docHeight + 'px';

        const isMobile = window.innerWidth <= 700;
        const leftX = isMobile ? 20 : 8;
        const rightX = isMobile ? 80 : 92;

        let d = `M ${leftX} 0`;
        const segments = Math.max(6, Math.floor(docHeight / 400));
        for (let i = 1; i <= segments; i++) {
            const y = (docHeight / segments) * i;
            const x = i % 2 === 0 ? leftX : rightX;
            const midX = i % 2 === 0 ? rightX : leftX;
            const midY = y - (docHeight / segments) / 2;
            d += ` Q ${midX} ${midY}, ${x} ${y}`;
        }
        const path = document.getElementById('thread-path');
        path.setAttribute('d', d);
        const len = path.getTotalLength();
        path.style.strokeDasharray = len;
        path.style.strokeDashoffset = len;
        return { path, len };
    }

    let { path, len } = buildPath();

    function onScroll() {
        const scrollTop = window.scrollY;
        const maxScroll = document.documentElement.scrollHeight - window.innerHeight;
        const progress = Math.min(1, scrollTop / maxScroll);
        path.style.strokeDashoffset = len - (len * progress);
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', () => {
        const rebuilt = buildPath();
        path = rebuilt.path;
        len = rebuilt.len;
        onScroll();
    });
    onScroll();
})();