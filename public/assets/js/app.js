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

    // Auto-scroll messages pane & keep chat header pinned at top when mobile keyboard opens
    const isChatPage = document.body.classList.contains('is-chat-page');
    const msgBody = document.querySelector('.msg-pane-body');
    const chatPane = document.querySelector('body.is-chat-page .msg-pane');
    const chatInput = document.querySelector('body.is-chat-page .chat-input');

    if (msgBody) {
        msgBody.scrollTop = msgBody.scrollHeight;
    }

    if (isChatPage && chatPane) {
        const lockChatViewport = () => {
            // Force document scroll back to 0,0 so header never scrolls off top
            window.scrollTo(0, 0);
            if (document.documentElement) document.documentElement.scrollTop = 0;
            if (document.body) document.body.scrollTop = 0;

            if (window.visualViewport) {
                const vv = window.visualViewport;
                // Bound the chat pane to exactly the visual viewport height & full width
                chatPane.style.height = `${vv.height}px`;
                chatPane.style.width = '100%';
                if (vv.offsetTop > 0) {
                    chatPane.style.transform = `translateY(${vv.offsetTop}px)`;
                } else {
                    chatPane.style.transform = 'none';
                }
            }

            if (msgBody) {
                msgBody.scrollTop = msgBody.scrollHeight;
            }
        };

        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', lockChatViewport);
            window.visualViewport.addEventListener('scroll', lockChatViewport);
        }

        if (chatInput) {
            chatInput.addEventListener('focus', () => {
                // When keypad starts opening, immediately reset scroll and update
                requestAnimationFrame(lockChatViewport);
                setTimeout(lockChatViewport, 80);
                setTimeout(lockChatViewport, 250);
                setTimeout(lockChatViewport, 450);
            });
            chatInput.addEventListener('blur', () => {
                requestAnimationFrame(lockChatViewport);
                setTimeout(lockChatViewport, 120);
            });
        }

        // Prevent window-level bounce/scroll on touchmove outside the message body
        document.addEventListener('touchmove', (e) => {
            if (!e.target.closest('.msg-pane-body')) {
                if (e.target.closest('.msg-pane-head, .msg-pane-form')) {
                    e.preventDefault();
                }
            }
        }, { passive: false });
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
                // Close any other open combobox first
                document.querySelectorAll('.custom-combobox.is-open').forEach(other => {
                    if (other !== cb) {
                        other.classList.remove('is-open');
                        const otherTrigger = other.querySelector('.combobox-trigger');
                        if (otherTrigger) otherTrigger.setAttribute('aria-expanded', 'false');
                        const parentField = other.closest('.field');
                        if (parentField) parentField.classList.remove('is-combobox-open');
                    }
                });

                cb.classList.add('is-open');
                trigger.setAttribute('aria-expanded', 'true');
                const parentField = cb.closest('.field');
                if (parentField) parentField.classList.add('is-combobox-open');

                if (searchInput) {
                    searchInput.value = '';
                    filterOptions('');
                    setTimeout(() => searchInput.focus(), 50);
                }
            };

            const closeDropdown = () => {
                cb.classList.remove('is-open');
                trigger.setAttribute('aria-expanded', 'false');
                const parentField = cb.closest('.field');
                if (parentField) parentField.classList.remove('is-combobox-open');
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
                    if (!q || val.includes(q) || text.includes(q)) {
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
                const placeholder = cb.getAttribute('data-placeholder') || 'Select or search...';
                if (displayText) {
                    displayText.textContent = placeholder;
                    displayText.classList.add('is-placeholder');
                }
                if (clearBtn) clearBtn.style.display = 'none';
                if (otherWrap) {
                    otherWrap.style.display = 'none';
                    if (otherInput) otherInput.value = '';
                }
                optionsList.forEach(o => {
                    if ((o.getAttribute('data-value') || '') === '') {
                        o.classList.add('is-selected');
                    } else {
                        o.classList.remove('is-selected');
                    }
                });
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
                    trigger.focus();
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
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        const firstVisible = Array.from(optionsList).find(o => o.style.display !== 'none');
                        if (firstVisible) {
                            const val = firstVisible.getAttribute('data-value');
                            const label = firstVisible.getAttribute('data-label') || firstVisible.textContent.trim().replace(/^✓\s*/, '');
                            selectOption(val, label);
                        }
                    }
                });
            }

            // Option clicks
            optionsList.forEach(opt => {
                opt.addEventListener('click', () => {
                    const val = opt.getAttribute('data-value');
                    const label = opt.getAttribute('data-label') || opt.textContent.trim().replace(/^✓\s*/, '');
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

    // Automatic smooth-scrolling for inputs/textareas when mobile virtual keyboard opens
    document.addEventListener('focusin', (e) => {
        if (!e.target) return;
        const tag = e.target.tagName;
        if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT') {
            setTimeout(() => {
                try {
                    e.target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } catch(err) {}
            }, 300);
        }
    }, true);

    // -------------------------------------------------------------
    // Global 1-Click Clipboard Copy Helper
    // -------------------------------------------------------------
    window.copyToClipboard = function(text, triggerEl) {
        if (!text) return;
        const fallbackCopy = () => {
            const temp = document.createElement('textarea');
            temp.value = text;
            temp.style.position = 'fixed';
            temp.style.opacity = '0';
            document.body.appendChild(temp);
            temp.select();
            try {
                document.execCommand('copy');
                showCopiedFeedback(triggerEl);
            } catch (err) {
                console.error('Fallback copy failed', err);
            }
            document.body.removeChild(temp);
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                showCopiedFeedback(triggerEl);
            }).catch(() => {
                fallbackCopy();
            });
        } else {
            fallbackCopy();
        }
    };

    function showCopiedFeedback(el) {
        if (!el) return;
        const originalHtml = el.innerHTML;
        el.classList.add('copied');
        el.innerHTML = '✓ Copied!';
        setTimeout(() => {
            el.innerHTML = originalHtml;
            el.classList.remove('copied');
        }, 2200);
    }

    // -------------------------------------------------------------
    // Legal Document Dynamic Table of Contents (TOC) & Smooth Scroll
    // -------------------------------------------------------------
    function initLegalTOC() {
        const tocList = document.getElementById('legalTocList');
        const docBody = document.getElementById('legalDocBody');
        const tocCard = document.getElementById('legalTocCard');
        if (!tocList || !docBody) return;

        const headings = docBody.querySelectorAll('h2, h3');
        if (!headings || headings.length === 0) {
            if (tocCard) tocCard.style.display = 'none';
            return;
        }

        tocList.innerHTML = '';
        headings.forEach((heading, idx) => {
            if (!heading.id) {
                heading.id = 'policy-sec-' + (idx + 1);
            }
            const cleanTitle = heading.textContent.replace(/^◈\s*/, '').trim();
            const link = document.createElement('a');
            link.href = '#' + heading.id;
            link.className = 'legal-toc-item';
            link.textContent = cleanTitle;
            link.addEventListener('click', (e) => {
                e.preventDefault();
                heading.scrollIntoView({ behavior: 'smooth', block: 'start' });
                history.replaceState(null, null, '#' + heading.id);
            });
            tocList.appendChild(link);
        });

        // IntersectionObserver for active heading highlighting
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const id = entry.target.id;
                        tocList.querySelectorAll('.legal-toc-item').forEach(item => {
                            if (item.getAttribute('href') === '#' + id) {
                                item.classList.add('active');
                            } else {
                                item.classList.remove('active');
                            }
                        });
                    }
                });
            }, { rootMargin: '-80px 0px -70% 0px' });

            headings.forEach(h => observer.observe(h));
        }
    }

    // -------------------------------------------------------------
    // Contact Form Quick Subject Topic Pills
    // -------------------------------------------------------------
    function initQuickSubjectPills() {
        const pills = document.querySelectorAll('.subject-pill');
        const subjectInput = document.getElementById('contactSubjectInput') || document.querySelector('input[name="subject"]');
        if (!pills.length || !subjectInput) return;

        pills.forEach(pill => {
            pill.addEventListener('click', () => {
                const topic = pill.getAttribute('data-subject') || pill.textContent.trim();
                subjectInput.value = topic;
                pills.forEach(p => p.classList.remove('active'));
                pill.classList.add('active');
                subjectInput.focus();
            });
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initComboboxes();
            initLocationSelectors();
            initProfileValidation();
            initMobileFilters();
            initLegalTOC();
            initQuickSubjectPills();
        });
    } else {
        initComboboxes();
        initLocationSelectors();
        initProfileValidation();
        initMobileFilters();
        initLegalTOC();
        initQuickSubjectPills();
    }
})();
