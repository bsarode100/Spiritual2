<?php
/**
 * Bottom Sheet / Picker Modal for Spiritual Path Selection
 * Inspired by modern matrimonial apps (Jeevansathi, Shaadi.com) with sacred Vedic aesthetics.
 */
$pathIconMap = [
    'Advaita Vedanta'                                          => '🪷',
    'Akshar-Purushottam Darshan'                               => '🕉️',
    'Ashtanga Yoga'                                            => '🧘',
    'Atma Vichara (Self-Enquiry)'                              => '🕯️',
    'Bhakti Yoga'                                              => '🕉️',
    'Brahm Gyan (Realization of Formless God)'                 => '✨',
    'Dynamic / Cathartic Meditation'                           => '🌀',
    'Engaged Buddhism'                                         => '☸️',
    'Gaudiya Vaishnavism'                                      => '🌸',
    'Gayatri Mantra Sadhana'                                   => '☀️',
    'Hatha Yoga'                                               => '🧘',
    'Integral Yoga (Purna Yoga / Yoga of Synthesis)'           => '🌿',
    'Jnana Yoga'                                               => '🕯️',
    'Karma Yoga'                                               => '🤝',
    'Kashmir Shaivism'                                         => '✨',
    'Kayakalpa'                                                => '🌿',
    'Kriya Yoga'                                               => '🧘',
    'Kundalini Yoga'                                           => '⚡',
    'Maha Yoga'                                                => '🪷',
    'Mahamudra'                                                => '☸️',
    'Mantra Meditation / Chanting'                             => '📿',
    'Nichiren Buddhism'                                        => '☸️',
    'Pranahuti (Yogic Transmission)'                           => '🕊️',
    'Pranayama'                                                => '💨',
    'Raja Yoga'                                                => '👑',
    'Seva (Selfless Service)'                                  => '🤝',
    'Shaktipat Initiation'                                     => '⚡',
    'Siddha Yoga'                                              => '✨',
    'Sikh Dharma'                                              => '🕊️',
    'Supramental Consciousness'                                => '🌟',
    'Surat Shabd Yoga (Meditation on Inner Light and Sound)'   => '🎶',
    'Syncretic Mysticism / Neo-Sannyas'                        => '🪷',
    'Tamil Siddhar Tradition'                                  => '🌿',
    'Theravada Buddhism'                                       => '☸️',
    'Tibetan Buddhism'                                         => '☸️',
    'Vedic Philosophy'                                         => '📜',
    'Vipassana (Insight) Meditation'                           => '☸️',
    'Yagya / Homa (Fire Rituals)'                              => '🔥',
    'Zen Buddhism'                                             => '🪷',
];
$allPaths = function_exists('spiritual_paths') ? spiritual_paths() : [];
?>
<div class="spiritual-sheet-overlay" id="spiritualPathModal" role="dialog" aria-modal="true" aria-labelledby="spiritualSheetTitle" aria-hidden="true">
    <div class="spiritual-sheet-backdrop" id="spiritualSheetBackdrop"></div>
    <div class="spiritual-sheet">
        <!-- Drag Handle (Mobile) -->
        <div class="spiritual-sheet-drag" aria-hidden="true">
            <span class="spiritual-sheet-handle"></span>
        </div>

        <!-- Header -->
        <div class="spiritual-sheet-header">
            <div>
                <span class="spiritual-sheet-eyebrow">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--c-saffron);"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    Sacred Tradition &amp; Sadhana
                </span>
                <h3 class="spiritual-sheet-title" id="spiritualSheetTitle">Select Spiritual Path</h3>
            </div>
            <button type="button" class="spiritual-sheet-close" id="spiritualSheetClose" aria-label="Close spiritual path selector">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <!-- Sticky Search Box inside sheet (Keyboard opens ONLY when user explicitly searches) -->
        <div class="spiritual-sheet-search-wrap">
            <div class="spiritual-sheet-search-box">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" class="spiritual-sheet-search-input" id="spiritualSheetSearch" placeholder="Search 37+ traditions (e.g. Advaita, Bhakti, Kriya)..." autocomplete="off">
                <button type="button" class="spiritual-sheet-search-clear" id="spiritualSheetSearchClear" title="Clear search" aria-label="Clear search" style="display: none;">×</button>
            </div>
        </div>

        <!-- Quick Popular Filter Pills (Horizontal Scroll) -->
        <div class="spiritual-sheet-pills-bar" aria-label="Popular spiritual paths">
            <button type="button" class="spiritual-sheet-pill" data-value="">✨ All Paths</button>
            <button type="button" class="spiritual-sheet-pill" data-value="Advaita Vedanta">🪷 Advaita Vedanta</button>
            <button type="button" class="spiritual-sheet-pill" data-value="Bhakti Yoga">🕉️ Bhakti Yoga</button>
            <button type="button" class="spiritual-sheet-pill" data-value="Kriya Yoga">🧘 Kriya Yoga</button>
            <button type="button" class="spiritual-sheet-pill" data-value="Vipassana (Insight) Meditation">☸️ Vipassana</button>
            <button type="button" class="spiritual-sheet-pill" data-value="Gaudiya Vaishnavism">🌸 Gaudiya Vaishnavism</button>
            <button type="button" class="spiritual-sheet-pill" data-value="Kashmir Shaivism">✨ Kashmir Shaivism</button>
            <button type="button" class="spiritual-sheet-pill" data-value="Kundalini Yoga">⚡ Kundalini Yoga</button>
            <button type="button" class="spiritual-sheet-pill" data-value="Sikh Dharma">🕊️ Sikh Dharma</button>
        </div>

        <!-- Scrollable Tradition Items List -->
        <div class="spiritual-sheet-list" id="spiritualSheetList" role="listbox">
            <!-- Special Option: Any / All Spiritual Paths -->
            <div class="spiritual-sheet-item" data-value="" data-label="All Spiritual Paths" role="option">
                <div class="spiritual-sheet-item-left">
                    <span class="spiritual-sheet-item-icon">✨</span>
                    <div class="spiritual-sheet-item-text">
                        <strong class="spiritual-sheet-item-name">All Spiritual Paths (Any)</strong>
                        <span class="spiritual-sheet-item-sub">Browse seekers from all sacred paths and lineages</span>
                    </div>
                </div>
                <div class="spiritual-sheet-item-radio" aria-hidden="true"></div>
            </div>

            <div class="spiritual-sheet-divider"></div>

            <!-- List of Predefined Traditions -->
            <?php foreach ($allPaths as $pathName): 
                $icon = $pathIconMap[$pathName] ?? '🪷';
            ?>
                <div class="spiritual-sheet-item" data-value="<?= e($pathName) ?>" data-label="<?= e($pathName) ?>" role="option">
                    <div class="spiritual-sheet-item-left">
                        <span class="spiritual-sheet-item-icon"><?= $icon ?></span>
                        <div class="spiritual-sheet-item-text">
                            <strong class="spiritual-sheet-item-name"><?= e($pathName) ?></strong>
                        </div>
                    </div>
                    <div class="spiritual-sheet-item-radio" aria-hidden="true"></div>
                </div>
            <?php endforeach; ?>

            <!-- Empty Search State -->
            <div class="spiritual-sheet-empty" id="spiritualSheetEmpty" style="display: none;">
                <div style="font-size: 2.2rem; margin-bottom: 0.5rem;">🪷</div>
                <div style="font-weight: 600; color: var(--c-ink); margin-bottom: 0.25rem;">No matching tradition found</div>
                <div style="font-size: 0.86rem; color: var(--c-muted); max-width: 320px; margin: 0 auto 1rem;">Try searching another keyword or choose "All Spiritual Paths".</div>
                <button type="button" class="btn btn-ghost btn-sm" id="spiritualSheetResetSearch">View All Traditions</button>
            </div>
        </div>

        <!-- Footer Bar -->
        <div class="spiritual-sheet-footer">
            <button type="button" class="spiritual-sheet-clear-btn" id="spiritualSheetClearAll">
                Clear Selection
            </button>
            <button type="button" class="btn btn-primary btn-sm spiritual-sheet-done-btn" id="spiritualSheetDone">
                Done
            </button>
        </div>
    </div>
</div>
