// SpiritualShaadi — light UI behaviors
(function () {
    // Mobile menu
    const toggle = document.querySelector('.mobile-toggle');
    const links  = document.querySelector('.nav-links');
    if (toggle && links) {
        toggle.addEventListener('click', () => links.classList.toggle('open'));
    }

    // Navbar elevation on scroll
    const nav = document.querySelector('.nav');
    if (nav) {
        const handleScroll = () => {
            if (window.scrollY > 15) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        };
        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll();
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

    // Show / Hide password toggle
    document.addEventListener('change', e => {
        if (!e.target || !e.target.classList.contains('show-password-checkbox')) return;
        const checked = e.target.checked;
        const singleTarget = e.target.getAttribute('data-target');
        const multipleTargets = e.target.getAttribute('data-targets');

        if (singleTarget) {
            const input = document.getElementById(singleTarget);
            if (input) input.type = checked ? 'text' : 'password';
        } else if (multipleTargets) {
            multipleTargets.split(',').forEach(id => {
                const input = document.getElementById(id.trim());
                if (input) input.type = checked ? 'text' : 'password';
            });
        } else {
            const container = e.target.closest('.field') || e.target.parentElement;
            if (container) {
                const inputs = container.querySelectorAll('input[type="password"], input[data-pw-toggle]');
                inputs.forEach(inp => {
                    inp.setAttribute('data-pw-toggle', '1');
                    inp.type = checked ? 'text' : 'password';
                });
            }
        }
    });
})();
