// SpiritualShaadi — Modern Spiritual UI Interactions
(function () {
    // Mobile menu with backdrop / outside click support
    const toggle = document.querySelector('.mobile-toggle');
    const links  = document.querySelector('.nav-links');
    if (toggle && links) {
        toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = links.classList.toggle('open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        // Close on click outside
        document.addEventListener('click', (e) => {
            if (links.classList.contains('open') && !links.contains(e.target) && !toggle.contains(e.target)) {
                links.classList.remove('open');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && links.classList.contains('open')) {
                links.classList.remove('open');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // Navbar elevation on scroll with smooth glass blur transition
    const nav = document.querySelector('.nav');
    if (nav) {
        let ticking = false;
        const handleScroll = () => {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    if (window.scrollY > 15) {
                        nav.classList.add('scrolled');
                    } else {
                        nav.classList.remove('scrolled');
                    }
                    ticking = false;
                });
                ticking = true;
            }
        };
        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll();
    }

    // Auto-dismiss flash messages with gentle fade
    document.querySelectorAll('.flash').forEach(f => {
        setTimeout(() => {
            f.style.transition = 'opacity 0.5s cubic-bezier(0.16, 1, 0.3, 1), transform 0.5s ease';
            f.style.opacity = '0';
            f.style.transform = 'translateY(-6px)';
        }, 4500);
        setTimeout(() => f.remove(), 5000);
    });

    // Smooth scroll for in-page anchors
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const id = a.getAttribute('href');
            if (id.length > 1) {
                const target = document.querySelector(id);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });

    // Auto-scroll messages pane to bottom
    const msgBody = document.querySelector('.msg-pane-body');
    if (msgBody) msgBody.scrollTop = msgBody.scrollHeight;

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
