/* ---------- Wedding content ---------- */
const wedding = {
    bride: {
        firstName: 'Mara',
        fullName: 'Mara Reyes'
    },
    groom: {
        firstName: 'Julian',
        fullName: 'Julian Santos'
    },
    // Replace these sample details with the final event information.
    dateTime: '2026-12-12T15:00:00+08:00',
    displayDate: 'December 12, 2026',
    displayTime: '3:00 PM',
    location: 'Manila, Philippines',
    hashtag: 'MaraAndJulian2026',
    storyQuote: 'The best things in our story have always arrived gently: a shared laugh, a long walk, and the quiet certainty that home could be a person.',
    storyBody: 'What began as easy conversation became our favorite place to return to. We are grateful for every season that brought us here, and we would be honored to celebrate this next chapter with you.',
    rsvp: {
        deadline: 'November 15, 2026',
        email: 'rsvp@example.com'
    },
    venues: {
        ceremony: {
            name: 'San Agustin Church',
            address: 'General Luna Street, Intramuros, Manila',
            time: '3:00 PM',
            note: 'The ceremony begins promptly. Please arrive 20 minutes early.',
            mapUrl: ''
        },
        reception: {
            name: 'The Manila Hotel',
            address: 'One Rizal Park, Manila',
            time: '5:30 PM',
            note: 'Dinner, toasts, and dancing will follow.',
            mapUrl: ''
        }
    }
};

const fields = {
    brideFirst: wedding.bride.firstName,
    brideFull: wedding.bride.fullName,
    groomFirst: wedding.groom.firstName,
    groomFull: wedding.groom.fullName,
    initials: `${wedding.bride.firstName.charAt(0)} & ${wedding.groom.firstName.charAt(0)}`,
    displayDate: wedding.displayDate,
    displayTime: wedding.displayTime,
    location: wedding.location,
    hashtag: `#${wedding.hashtag}`,
    storyQuote: wedding.storyQuote,
    storyBody: wedding.storyBody,
    rsvpDeadline: wedding.rsvp.deadline,
    rsvpEmail: wedding.rsvp.email,
    ceremonyVenue: wedding.venues.ceremony.name,
    ceremonyAddress: wedding.venues.ceremony.address,
    ceremonyTime: wedding.venues.ceremony.time,
    ceremonyNote: wedding.venues.ceremony.note,
    receptionVenue: wedding.venues.reception.name,
    receptionAddress: wedding.venues.reception.address,
    receptionTime: wedding.venues.reception.time,
    receptionNote: wedding.venues.reception.note
};

document.title = `${wedding.bride.firstName} & ${wedding.groom.firstName} - We're Getting Married`;

document.querySelectorAll('[data-field]').forEach((el) => {
    const value = fields[el.dataset.field];
    if (value) el.textContent = value;
});

document.querySelectorAll('[data-map]').forEach((link) => {
    const venue = wedding.venues[link.dataset.map];
    if (!venue) return;

    const query = [venue.name, venue.address].filter(Boolean).join(' ');
    link.href = venue.mapUrl || `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(query)}`;
    link.target = '_blank';
    link.rel = 'noopener';
});

const rsvpContact = document.getElementById('rsvp-contact');
if (rsvpContact) {
    rsvpContact.href = `mailto:${wedding.rsvp.email}`;
    rsvpContact.textContent = wedding.rsvp.email;
}

/* ---------- Scroll reveal ---------- */
const revealEls = document.querySelectorAll('.reveal, .reveal-scale');

if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -8% 0px' });

    revealEls.forEach((el) => io.observe(el));
} else {
    revealEls.forEach((el) => {
        el.classList.add('is-visible');
    });
}

/* ---------- Countdown ---------- */
const weddingDate = new Date(wedding.dateTime);
const countdownNodes = {
    days: document.getElementById('cd-days'),
    hours: document.getElementById('cd-hours'),
    mins: document.getElementById('cd-mins'),
    secs: document.getElementById('cd-secs')
};

function updateCountdown() {
    if (Number.isNaN(weddingDate.getTime())) return;

    const now = new Date();
    const diff = Math.max(0, weddingDate - now);

    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
    const mins = Math.floor((diff / (1000 * 60)) % 60);
    const secs = Math.floor((diff / 1000) % 60);

    countdownNodes.days.textContent = String(days).padStart(2, '0');
    countdownNodes.hours.textContent = String(hours).padStart(2, '0');
    countdownNodes.mins.textContent = String(mins).padStart(2, '0');
    countdownNodes.secs.textContent = String(secs).padStart(2, '0');
}

if (Object.values(countdownNodes).every(Boolean)) {
    updateCountdown();
    setInterval(updateCountdown, 1000);
}

/* ---------- RSVP mail handoff ---------- */
const rsvpForm = document.getElementById('rsvp-form');
const rsvpThanks = document.getElementById('rsvp-thanks');
const rsvpStatus = document.getElementById('rsvp-status');

function formatRsvpBody(data) {
    const attending = data.attend === 'yes' ? 'Joyfully accepts' : 'Regretfully declines';
    return [
        `Name: ${data.guestName}`,
        `Response: ${attending}`,
        `Number of guests: ${data.guests || '1'}`,
        '',
        'Message:',
        data.message || '(No message)',
        '',
        `Submitted from the ${wedding.bride.firstName} & ${wedding.groom.firstName} wedding invitation.`
    ].join('\n');
}

if (rsvpForm) {
    rsvpForm.addEventListener('submit', (e) => {
        e.preventDefault();

        if (!rsvpForm.reportValidity()) return;

        const data = Object.fromEntries(new FormData(rsvpForm).entries());
        const savedAt = new Date().toISOString();
        localStorage.setItem('weddingRsvpDraft', JSON.stringify({ ...data, savedAt }));

        const subject = `RSVP for ${wedding.bride.firstName} & ${wedding.groom.firstName}`;
        const body = formatRsvpBody(data);
        const mailtoUrl = `mailto:${wedding.rsvp.email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;

        rsvpStatus.textContent = 'Opening your email app with your RSVP details. You can review and send it from there.';
        rsvpThanks.hidden = false;
        rsvpThanks.textContent = `Thank you, ${data.guestName.split(' ')[0]}! Your RSVP draft is ready in your email app.`;
        window.location.href = mailtoUrl;
    });
}

/* ---------- Scroll-drawn gold thread (signature element) ---------- */
(function () {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const wrap = document.createElement('div');
    wrap.id = 'thread-wrap';
    wrap.innerHTML = `<svg id="thread-svg" preserveAspectRatio="none"><path id="thread-path"/></svg>`;
    document.body.prepend(wrap);

    function buildPath() {
        const docHeight = Math.max(document.documentElement.scrollHeight, document.body.scrollHeight, window.innerHeight);
        wrap.style.height = docHeight + 'px';
        const svg = document.getElementById('thread-svg');
        svg.setAttribute('viewBox', `0 0 100 ${docHeight}`);
        svg.style.height = docHeight + 'px';

        const isMobile = window.innerWidth <= 700;
        const leftX = isMobile ? 13 : 7;
        const rightX = isMobile ? 87 : 93;

        let d = `M ${leftX} 0`;
        const segments = Math.max(6, Math.floor(docHeight / 420));
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
    let rebuildFrame = 0;

    function onScroll() {
        const scrollTop = window.scrollY;
        const maxScroll = Math.max(1, document.documentElement.scrollHeight - window.innerHeight);
        const progress = Math.min(1, Math.max(0, scrollTop / maxScroll));
        path.style.strokeDashoffset = len - (len * progress);
    }

    function rebuildAndRefresh() {
        const rebuilt = buildPath();
        path = rebuilt.path;
        len = rebuilt.len;
        onScroll();
    }

    function scheduleRebuild() {
        cancelAnimationFrame(rebuildFrame);
        rebuildFrame = requestAnimationFrame(rebuildAndRefresh);
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', scheduleRebuild);
    window.addEventListener('load', scheduleRebuild);

    // Catches height changes from late-loading images/fonts, which resize
    // alone won't on mobile (this is the main mobile fix)
    if ('ResizeObserver' in window) {
        const ro = new ResizeObserver(scheduleRebuild);
        ro.observe(document.body);
    }

    onScroll();
})();
