// Spiritual Matrimony — light UI behaviors
(function () {
    // Mobile menu
    const toggle = document.querySelector('.mobile-toggle');
    const links  = document.querySelector('.nav-links');
    if (toggle && links) {
        toggle.addEventListener('click', () => links.classList.toggle('open'));
    }

    // Auto-dismiss flash messages
    document.querySelectorAll('.flash').forEach(f => {
        setTimeout(() => { f.style.transition = 'opacity .4s'; f.style.opacity = '0'; }, 4500);
        setTimeout(() => f.remove(), 5000);
    });

    // Smooth scroll for in-page anchors
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const id = a.getAttribute('href');
            if (id.length > 1) {
                const target = document.querySelector(id);
                if (target) { e.preventDefault(); target.scrollIntoView({behavior: 'smooth'}); }
            }
        });
    });

    // Auto-scroll messages pane to bottom
    const body = document.querySelector('.msg-pane-body');
    if (body) body.scrollTop = body.scrollHeight;
})();
