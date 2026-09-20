// SpiritualShaadi — Modern Spiritual UI Interactions
(function () {
    // Mobile menu with backdrop / outside click support
    const toggle = document.querySelector('.mobile-toggle');
    const links  = document.querySelector('.nav-links');
    if (toggle && links) {
        const toggleMenu = (e) => {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const isOpen = links.classList.toggle('open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            toggle.classList.toggle('is-active', isOpen);
        };

        toggle.addEventListener('click', toggleMenu);

        // Close on click outside
        document.addEventListener('click', (e) => {
            if (links.classList.contains('open') && !links.contains(e.target) && !toggle.contains(e.target)) {
                links.classList.remove('open');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.classList.remove('is-active');
            }
        });

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && links.classList.contains('open')) {
                links.classList.remove('open');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.classList.remove('is-active');
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

    // Auto-scroll messages pane to bottom & keep pinned on keyboard open
    const msgBody = document.querySelector('.msg-pane-body');
    if (msgBody) {
        msgBody.scrollTop = msgBody.scrollHeight;
        // On mobile, when keyboard opens and viewport resizes, maintain scroll position
        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', () => {
                msgBody.scrollTop = msgBody.scrollHeight;
            });
        }
    }

    // Dismiss profile card (Pass action)
    window.dismissProfileCard = function (btn) {
        const card = btn.closest('.profile-card');
        if (!card) return;
        card.classList.add('is-dismissed');
        setTimeout(() => {
            card.remove();
        }, 360);
    };

    // Navigate to profile on card click (ignoring clicks on action buttons)
    window.navigateToProfile = function (e, url) {
        if (e.target.closest('.profile-card-actions, button, a, form, input')) {
            return;
        }
        window.location.href = url;
    };

    // Touch Swipe Left / Right Navigation on Member Profile View (Jeevansathi style)
    const profileSec = document.querySelector('.member-profile-section');
    if (profileSec) {
        const prevUrl = profileSec.getAttribute('data-prev-url');
        const nextUrl = profileSec.getAttribute('data-next-url');

        let touchStartX = 0;
        let touchStartY = 0;
        let touchEndX = 0;
        let touchEndY = 0;

        profileSec.addEventListener('touchstart', (e) => {
            if (!e.changedTouches || e.changedTouches.length === 0) return;
            touchStartX = e.changedTouches[0].clientX;
            touchStartY = e.changedTouches[0].clientY;
        }, { passive: true });

        profileSec.addEventListener('touchend', (e) => {
            if (!e.changedTouches || e.changedTouches.length === 0) return;
            touchEndX = e.changedTouches[0].clientX;
            touchEndY = e.changedTouches[0].clientY;

            const diffX = touchEndX - touchStartX;
            const diffY = touchEndY - touchStartY;

            // Ensure horizontal swipe is dominant (not vertical scrolling)
            if (Math.abs(diffX) > 65 && Math.abs(diffX) > Math.abs(diffY) * 1.5) {
                if (diffX < 0 && nextUrl) {
                    // Swiped Left -> Go to Next Profile
                    profileSec.classList.add('slide-out-left');
                    setTimeout(() => { window.location.href = nextUrl; }, 180);
                } else if (diffX > 0 && prevUrl) {
                    // Swiped Right -> Go to Previous Profile
                    profileSec.classList.add('slide-out-right');
                    setTimeout(() => { window.location.href = prevUrl; }, 180);
                }
            }
        }, { passive: true });

        // Keyboard arrow navigation on desktop (Left Arrow -> Prev, Right Arrow -> Next)
        document.addEventListener('keydown', (e) => {
            if (e.target && (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA')) return;
            if (e.key === 'ArrowLeft' && prevUrl) {
                profileSec.classList.add('slide-out-right');
                setTimeout(() => { window.location.href = prevUrl; }, 180);
            } else if (e.key === 'ArrowRight' && nextUrl) {
                profileSec.classList.add('slide-out-left');
                setTimeout(() => { window.location.href = nextUrl; }, 180);
            }
        });
    }

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

    // =====================================================================
    // Searchable Combobox Component (Spiritual Organizations & Dropdowns)
    // =====================================================================
    function initComboboxes() {
        const comboboxes = document.querySelectorAll('.custom-combobox');
        comboboxes.forEach(cb => {
            const trigger     = cb.querySelector('.combobox-trigger');
            const dropdown    = cb.querySelector('.combobox-dropdown');
            const searchInput = cb.querySelector('.combobox-search-input');
            const hiddenVal   = cb.querySelector('input[type="hidden"]');
            const displayText = cb.querySelector('.combobox-text');
            const clearBtn    = cb.querySelector('.combobox-clear');
            const optionsList = cb.querySelectorAll('.combobox-option');
            const emptyState  = cb.querySelector('.combobox-empty');
            const otherWrapId = cb.getAttribute('data-other-wrap');
            const otherInputId = cb.getAttribute('data-other-input');
            const otherWrap   = otherWrapId ? document.getElementById(otherWrapId) : (document.getElementById('spiritual_org_other_wrap') || (cb.closest('.field') ? cb.closest('.field').querySelector('.combobox-other-wrap') : null));
            const otherInput  = otherInputId ? document.getElementById(otherInputId) : (document.getElementById('spiritual_org_other_input') || (otherWrap ? otherWrap.querySelector('input') : null));

            if (!trigger || !dropdown) return;

            const openDropdown = () => {
                cb.classList.add('is-open');
                trigger.setAttribute('aria-expanded', 'true');
                if (searchInput) {
                    searchInput.value = '';
                    filterOptions('');
                    setTimeout(() => searchInput.focus(), 50);
                }
            };

            const closeDropdown = () => {
                cb.classList.remove('is-open');
                trigger.setAttribute('aria-expanded', 'false');
            };

            const toggleDropdown = () => {
                if (cb.classList.contains('is-open')) closeDropdown();
                else openDropdown();
            };

            const filterOptions = (query) => {
                const q = query.trim().toLowerCase();
                let matchCount = 0;
                optionsList.forEach(opt => {
                    if (opt.classList.contains('combobox-other-option')) {
                        opt.style.display = 'flex'; // Always visible
                        return;
                    }
                    const val = (opt.getAttribute('data-value') || '').toLowerCase();
                    const text = opt.textContent.toLowerCase();
                    if (val.includes(q) || text.includes(q)) {
                        opt.style.display = 'flex';
                        matchCount++;
                    } else {
                        opt.style.display = 'none';
                    }
                });

                if (emptyState) {
                    emptyState.style.display = (matchCount === 0 && q.length > 0) ? 'block' : 'none';
                }
            };

            const selectOption = (val, label) => {
                optionsList.forEach(o => {
                    if (o.getAttribute('data-value') === val) {
                        o.classList.add('is-selected');
                    } else {
                        o.classList.remove('is-selected');
                    }
                });

                if (val === 'Other') {
                    if (hiddenVal) hiddenVal.value = 'Other';
                    if (displayText) {
                        displayText.textContent = 'Other (Manual Entry)';
                        displayText.classList.remove('is-placeholder');
                    }
                    if (clearBtn) clearBtn.style.display = 'inline-flex';
                    if (otherWrap) {
                        otherWrap.style.display = 'block';
                        if (otherInput) {
                            setTimeout(() => otherInput.focus(), 60);
                        }
                    }
                } else if (val) {
                    if (hiddenVal) hiddenVal.value = val;
                    if (displayText) {
                        displayText.textContent = label || val;
                        displayText.classList.remove('is-placeholder');
                    }
                    if (clearBtn) clearBtn.style.display = 'inline-flex';
                    if (otherWrap) {
                        otherWrap.style.display = 'none';
                        if (otherInput) otherInput.value = '';
                    }
                } else {
                    resetSelection();
                }
                closeDropdown();
            };

            const resetSelection = () => {
                if (hiddenVal) hiddenVal.value = '';
                if (displayText) {
                    displayText.textContent = 'Select or search organization...';
                    displayText.classList.add('is-placeholder');
                }
                if (clearBtn) clearBtn.style.display = 'none';
                if (otherWrap) {
                    otherWrap.style.display = 'none';
                    if (otherInput) otherInput.value = '';
                }
                optionsList.forEach(o => o.classList.remove('is-selected'));
            };

            // Trigger click
            trigger.addEventListener('click', (e) => {
                if (e.target.closest('.combobox-clear')) return;
                toggleDropdown();
            });

            // Keyboard on trigger
            trigger.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
                    e.preventDefault();
                    openDropdown();
                }
            });

            // Clear button
            if (clearBtn) {
                clearBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    resetSelection();
                });
            }

            // Live search
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    filterOptions(e.target.value);
                });
                searchInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        closeDropdown();
                        trigger.focus();
                    }
                });
            }

            // Option clicks
            optionsList.forEach(opt => {
                opt.addEventListener('click', () => {
                    const val = opt.getAttribute('data-value');
                    const label = opt.textContent.trim().replace(/^✓\s*/, '');
                    selectOption(val, label);
                });
            });

            // "Select Other" button inside empty state
            if (emptyState) {
                const emptyOtherBtn = emptyState.querySelector('.select-other-btn');
                if (emptyOtherBtn) {
                    emptyOtherBtn.addEventListener('click', () => {
                        selectOption('Other', 'Other');
                        if (otherInput && searchInput && searchInput.value.trim()) {
                            otherInput.value = searchInput.value.trim();
                        }
                    });
                }
            }

            // Close on click outside
            document.addEventListener('click', (e) => {
                if (!cb.contains(e.target)) {
                    closeDropdown();
                }
            });

            // Close on escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && cb.classList.contains('is-open')) {
                    closeDropdown();
                }
            });
        });
    }

    // =====================================================================
    // Country & State Dynamic Switching (India vs Other)
    // =====================================================================
    function initLocationSelectors() {
        const countrySelect = document.getElementById('country_select');
        const countryOtherWrap = document.getElementById('country_other_wrap');
        const countryOtherInput = document.getElementById('country_other_input');
        const stateIndiaWrap = document.getElementById('state_india_wrap');
        const stateIndiaSelect = document.getElementById('state_india_select');
        const stateOtherWrap = document.getElementById('state_other_wrap');
        const stateOtherInput = document.getElementById('state_other_input');

        if (!countrySelect) return;

        const updateLocationUI = () => {
            const isIndia = countrySelect.value === 'India';
            if (isIndia) {
                if (stateIndiaWrap) stateIndiaWrap.style.display = 'block';
                if (stateOtherWrap) stateOtherWrap.style.display = 'none';
                if (countryOtherWrap) countryOtherWrap.style.display = 'none';
                if (stateIndiaSelect) {
                    stateIndiaSelect.disabled = false;
                    stateIndiaSelect.required = true;
                }
                if (stateOtherInput) {
                    stateOtherInput.disabled = true;
                    stateOtherInput.required = false;
                }
                if (countryOtherInput) {
                    countryOtherInput.disabled = true;
                    countryOtherInput.required = false;
                }
            } else {
                if (stateIndiaWrap) stateIndiaWrap.style.display = 'none';
                if (stateOtherWrap) stateOtherWrap.style.display = 'block';
                if (countryOtherWrap) countryOtherWrap.style.display = 'block';
                if (stateIndiaSelect) {
                    stateIndiaSelect.disabled = true;
                    stateIndiaSelect.required = false;
                }
                if (stateOtherInput) {
                    stateOtherInput.disabled = false;
                    stateOtherInput.required = true;
                }
                if (countryOtherInput) {
                    countryOtherInput.disabled = false;
                    countryOtherInput.required = true;
                    if (!countryOtherInput.value.trim()) {
                        setTimeout(() => countryOtherInput.focus(), 60);
                    }
                }
            }
        };

        countrySelect.addEventListener('change', updateLocationUI);
        updateLocationUI();
    }

    // =====================================================================
    // Spiritual Form Validation (Combobox & Checkbox mandatory guards)
    // =====================================================================
    function initProfileValidation() {
        const spiritualForm = document.getElementById('spiritual');
        if (!spiritualForm) return;

        spiritualForm.addEventListener('submit', (e) => {
            const pathVal = (document.getElementById('spiritual_path_value')?.value || '').trim();
            const otherInput = document.getElementById('spiritual_path_other_input');
            const pathField = document.getElementById('field-spiritual_path');

            if (!pathVal) {
                e.preventDefault();
                alert('Please select or specify your Spiritual Path / Lineage.');
                pathField?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                document.getElementById('spiritual_path_trigger')?.focus();
                return;
            }

            if (pathVal === 'Other' && otherInput && !otherInput.value.trim()) {
                e.preventDefault();
                alert('Please specify the name of your Spiritual Path / Tradition.');
                otherInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                otherInput.focus();
                return;
            }

            const commitmentsChecked = spiritualForm.querySelectorAll('#field-lifestyle_commitments input[type="checkbox"]:checked');
            if (commitmentsChecked.length === 0) {
                e.preventDefault();
                alert('Please select at least one Spiritual Lifestyle Commitment (e.g. Vegetarian, Vegan, No smoking, or No alcohol).');
                const commitmentField = document.getElementById('field-lifestyle_commitments');
                commitmentField?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }
        });
    }

    // Mobile Matches / Browse Filter Drawer Toggle (Jeevansathi-style)
    function initMobileFilters() {
        const filterBtn = document.getElementById('mobileFilterToggle');
        const filterForm = document.querySelector('.mobile-filters-collapsible');
        if (!filterBtn || !filterForm) return;

        filterBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const isOpen = filterForm.classList.toggle('is-open');
            filterBtn.classList.toggle('is-active', isOpen);
            if (isOpen) {
                filterForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initComboboxes();
            initLocationSelectors();
            initProfileValidation();
            initMobileFilters();
        });
    } else {
        initComboboxes();
        initLocationSelectors();
        initProfileValidation();
        initMobileFilters();
    }
})();
