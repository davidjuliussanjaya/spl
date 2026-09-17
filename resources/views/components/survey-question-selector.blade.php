@props([
    'daftarSoal',
    'selectedSoalIds' => [],
    'locked' => false,
])

@php
    $selectedSoalIds = collect($selectedSoalIds)->map(fn ($id) => (int) $id)->all();
    $categories = $daftarSoal
        ->groupBy(fn ($soal) => $soal->kategori?->id ?? 'tanpa-kategori')
        ->map(function ($questions, $key) {
            return [
                'key' => (string) $key,
                'name' => $questions->first()?->kategori?->nama_kategori ?? 'Tanpa kategori',
                'questions' => $questions->sortBy(fn ($soal) => $soal->kode ?? $soal->id)->values(),
            ];
        })
        ->sortBy('name')
        ->values();
    $selectedCategoryKeys = $categories
        ->filter(fn ($category) => $category['questions']->contains(fn ($soal) => in_array($soal->id, $selectedSoalIds, true)))
        ->pluck('key')
        ->all();
    $selectorId = 'survey-question-selector-' . uniqid();
    $badgeType = ['rating' => 'Rating', 'multiple_choice' => 'Pilihan', 'essay' => 'Esai'];
@endphp

<div id="{{ $selectorId }}" class="spl-question-selector" data-locked="{{ $locked ? 'true' : 'false' }}">
    <div class="spl-question-selector-summary">
        <div>
            <span class="spl-question-selector-kicker">Susun instrumen</span>
            <p>Seret kategori ke area digunakan. Klik kategori untuk memilih pertanyaan yang aktif.</p>
        </div>
        <span class="spl-question-selector-count"><strong data-selected-count>0</strong> pertanyaan aktif</span>
    </div>

    <div class="spl-question-selector-workspace">
        <section class="spl-category-zone" aria-labelledby="{{ $selectorId }}-available-label">
            <div class="spl-category-zone-header">
                <div>
                    <span class="spl-category-zone-label" id="{{ $selectorId }}-available-label">Kategori tersedia</span>
                    <span class="spl-category-zone-note">Seret atau tambahkan kategori</span>
                </div>
                <div class="spl-category-zone-actions">
                    @unless($locked)
                        <button type="button" class="spl-category-select-all" data-select-all-categories>Pilih semua</button>
                    @endunless
                    <i class="bi bi-collection" aria-hidden="true"></i>
                </div>
            </div>
            <div class="spl-category-list" data-zone="available">
                @foreach($categories as $category)
                    @if(! in_array($category['key'], $selectedCategoryKeys, true))
                        <article class="spl-category-card" draggable="{{ $locked ? 'false' : 'true' }}" data-category="{{ $category['key'] }}" tabindex="0">
                            <span class="spl-category-drag" aria-hidden="true"><i class="bi bi-grip-vertical"></i></span>
                            <button type="button" class="spl-category-card-main" data-show-category="{{ $category['key'] }}">
                                <span class="spl-category-card-name">{{ $category['name'] }}</span>
                                <span class="spl-category-card-meta">{{ $category['questions']->count() }} pertanyaan</span>
                            </button>
                            @unless($locked)
                                <button type="button" class="spl-category-card-action" data-add-category="{{ $category['key'] }}" aria-label="Tambahkan kategori {{ $category['name'] }}"><i class="bi bi-plus-lg"></i></button>
                            @endunless
                        </article>
                    @endif
                @endforeach
            </div>
        </section>

        <section class="spl-category-zone spl-category-zone-selected" aria-labelledby="{{ $selectorId }}-selected-label">
            <div class="spl-category-zone-header">
                <div>
                    <span class="spl-category-zone-label" id="{{ $selectorId }}-selected-label">Kategori digunakan</span>
                    <span class="spl-category-zone-note">Kategori ini tersedia di survei</span>
                </div>
                <i class="bi bi-check2-circle" aria-hidden="true"></i>
            </div>
            <div class="spl-category-list spl-category-dropzone" data-zone="selected">
                <p class="spl-category-empty" data-empty-state>Letakkan kategori di sini.</p>
                @foreach($categories as $category)
                    @if(in_array($category['key'], $selectedCategoryKeys, true))
                        <article class="spl-category-card is-selected" draggable="{{ $locked ? 'false' : 'true' }}" data-category="{{ $category['key'] }}" tabindex="0">
                            <span class="spl-category-drag" aria-hidden="true"><i class="bi bi-grip-vertical"></i></span>
                            <button type="button" class="spl-category-card-main" data-show-category="{{ $category['key'] }}">
                                <span class="spl-category-card-name">{{ $category['name'] }}</span>
                                <span class="spl-category-card-meta">{{ $category['questions']->count() }} pertanyaan</span>
                            </button>
                            @unless($locked)
                                <button type="button" class="spl-category-card-action is-remove" data-remove-category="{{ $category['key'] }}" aria-label="Hapus kategori {{ $category['name'] }}"><i class="bi bi-x-lg"></i></button>
                            @endunless
                        </article>
                    @endif
                @endforeach
            </div>
        </section>
    </div>

    <section class="spl-question-detail" aria-live="polite">
        <div class="spl-question-detail-empty" data-detail-empty>
            <i class="bi bi-hand-index-thumb"></i>
            <span>Pilih kategori untuk melihat dan mengatur pertanyaannya.</span>
        </div>

        @foreach($categories as $category)
            <div class="spl-question-panel" data-question-panel="{{ $category['key'] }}" hidden>
                <div class="spl-question-panel-header">
                    <div>
                        <span class="spl-question-selector-kicker">Detail kategori</span>
                        <h6>{{ $category['name'] }}</h6>
                    </div>
                    <span class="spl-question-panel-status" data-category-status="{{ $category['key'] }}"></span>
                </div>
                <p class="spl-question-panel-help" data-category-help="{{ $category['key'] }}"></p>
                <div class="spl-question-list">
                    @foreach($category['questions'] as $soal)
                        @php
                            $isSelected = in_array($soal->id, $selectedSoalIds, true);
                            $isCategorySelected = in_array($category['key'], $selectedCategoryKeys, true);
                            $fakultas = $soal->peruntukan_fakultas ?? 'Umum';
                        @endphp
                        <label class="spl-question-item" data-question-category="{{ $category['key'] }}">
                            <input type="checkbox" name="soal_pilihan[]" value="{{ $soal->id }}" class="form-check-input" data-question-input
                                {{ $isSelected ? 'checked' : '' }}
                                {{ (! $isCategorySelected || $locked) ? 'disabled' : '' }}>
                            <span class="spl-question-item-copy">
                                <span class="spl-question-item-text">{{ $soal->soal }}</span>
                                <span class="spl-question-item-meta">
                                    <span>{{ $soal->kode }}</span>
                                    <span>{{ $badgeType[$soal->jenis_soal] ?? $soal->jenis_soal }}</span>
                                    <span>{{ $fakultas }}</span>
                                </span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
    </section>
</div>

@once
    @push('styles')
        <style>
            .spl-question-selector { border: 1px solid var(--spl-border); border-radius: 12px; overflow: hidden; }
            .spl-question-selector-summary { align-items: center; background: #f8fafc; border-bottom: 1px solid var(--spl-border); display: flex; gap: 1rem; justify-content: space-between; padding: 1rem 1.1rem; }
            .spl-question-selector-kicker, .spl-category-zone-label { color: var(--spl-brand); display: block; font-size: .68rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
            .spl-question-selector-summary p { color: var(--spl-muted); font-size: .78rem; margin: .22rem 0 0; }
            .spl-question-selector-count { background: #fff; border: 1px solid #bfdbfe; border-radius: 999px; color: var(--spl-muted); flex: 0 0 auto; font-size: .74rem; padding: .35rem .62rem; }
            .spl-question-selector-count strong { color: var(--spl-brand); }
            .spl-question-selector-workspace { display: grid; gap: 1rem; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); padding: 1rem; }
            .spl-category-zone { background: #fff; border: 1px solid var(--spl-border); border-radius: 10px; min-height: 190px; padding: .85rem; }
            .spl-category-zone-selected { background: #fbfdff; border-color: #bfdbfe; }
            .spl-category-zone-header { align-items: flex-start; display: flex; gap: .75rem; justify-content: space-between; margin-bottom: .75rem; }
            .spl-category-zone-actions { align-items: center; display: flex; gap: .5rem; }
            .spl-category-zone-header > i { color: var(--spl-brand); font-size: 1rem; }
            .spl-category-zone-actions > i { color: var(--spl-brand); font-size: 1rem; }
            .spl-category-select-all { background: #fff; border: 1px solid #bfdbfe; border-radius: 6px; color: var(--spl-brand); font-size: .68rem; font-weight: 750; padding: .28rem .45rem; white-space: nowrap; }
            .spl-category-select-all:hover, .spl-category-select-all:focus { background: var(--spl-brand-soft); border-color: var(--spl-brand); }
            .spl-category-zone-note { color: var(--spl-muted); display: block; font-size: .72rem; margin-top: .15rem; }
            .spl-category-list { display: flex; flex-direction: column; gap: .5rem; max-height: 312px; min-height: 106px; overflow-y: auto; padding-right: .2rem; scrollbar-color: #cbd5e1 transparent; scrollbar-width: thin; }
            .spl-category-list::-webkit-scrollbar { width: 6px; }
            .spl-category-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
            .spl-category-dropzone.is-drag-over { background: #eff6ff; border-radius: 8px; outline: 2px dashed #93c5fd; outline-offset: 3px; }
            .spl-category-card { align-items: center; background: #fff; border: 1px solid var(--spl-border); border-radius: 8px; display: flex; gap: .45rem; min-height: 56px; padding: .45rem; transition: border-color .16s ease, box-shadow .16s ease, opacity .16s ease; }
            .spl-category-card:hover, .spl-category-card:focus-within, .spl-category-card.is-active { border-color: #93c5fd; box-shadow: 0 3px 10px rgba(37, 99, 235, .08); }
            .spl-category-card.is-selected { border-left: 3px solid var(--spl-brand); }
            .spl-category-card.is-dragging { opacity: .45; }
            .spl-category-drag { color: #94a3b8; cursor: grab; font-size: 1rem; padding: .25rem; }
            .spl-category-card-main { background: transparent; border: 0; min-width: 0; padding: .15rem; text-align: left; width: 100%; }
            .spl-category-card-name { color: var(--spl-text); display: block; font-size: .79rem; font-weight: 750; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .spl-category-card-meta { color: var(--spl-muted); display: block; font-size: .68rem; margin-top: .12rem; }
            .spl-category-card-action { align-items: center; background: var(--spl-brand-soft); border: 0; border-radius: 6px; color: var(--spl-brand); display: inline-flex; flex: 0 0 28px; height: 28px; justify-content: center; width: 28px; }
            .spl-category-card-action.is-remove { background: #f1f5f9; color: #64748b; }
            .spl-category-empty { align-items: center; color: #94a3b8; display: flex; font-size: .74rem; font-style: italic; justify-content: center; margin: 0; min-height: 96px; text-align: center; }
            .spl-question-detail { border-top: 1px solid var(--spl-border); padding: 1rem; }
            .spl-question-detail-empty { align-items: center; color: var(--spl-muted); display: flex; flex-direction: column; font-size: .78rem; gap: .45rem; justify-content: center; min-height: 160px; text-align: center; }
            .spl-question-detail-empty i { color: #93c5fd; font-size: 1.5rem; }
            .spl-question-panel-header { align-items: center; display: flex; gap: 1rem; justify-content: space-between; }
            .spl-question-panel-header h6 { color: var(--spl-text); font-size: .98rem; font-weight: 800; margin: .2rem 0 0; }
            .spl-question-panel-status { background: #f1f5f9; border-radius: 999px; color: #64748b; font-size: .69rem; font-weight: 700; padding: .28rem .5rem; text-align: center; }
            .spl-question-panel-status.is-active { background: #dcfce7; color: #15803d; }
            .spl-question-panel-help { color: var(--spl-muted); font-size: .75rem; margin: .7rem 0; }
            .spl-question-list { border: 1px solid var(--spl-border); border-radius: 8px; overflow: hidden; }
            .spl-question-item { align-items: flex-start; border-bottom: 1px solid #edf2f7; cursor: pointer; display: flex; gap: .7rem; margin: 0; padding: .75rem .85rem; }
            .spl-question-item:last-child { border-bottom: 0; }
            .spl-question-item:hover { background: #f8fbff; }
            .spl-question-item input { flex: 0 0 auto; margin-top: .16rem; }
            .spl-question-item input:disabled { cursor: not-allowed; }
            .spl-question-item-copy { min-width: 0; }
            .spl-question-item-text { color: #334155; display: block; font-size: .81rem; line-height: 1.45; }
            .spl-question-item-meta { display: flex; flex-wrap: wrap; gap: .3rem; margin-top: .35rem; }
            .spl-question-item-meta span { background: #f1f5f9; border-radius: 999px; color: #64748b; font-size: .65rem; font-weight: 700; padding: .16rem .4rem; }
            .spl-question-item:has(input:disabled) { background: #f8fafc; cursor: default; opacity: .72; }
            @media (max-width: 767.98px) {
                .spl-question-selector-summary { align-items: flex-start; flex-direction: column; }
                .spl-question-selector-workspace { grid-template-columns: 1fr; }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.spl-question-selector').forEach(function (selector) {
                    const locked = selector.dataset.locked === 'true';
                    const availableZone = selector.querySelector('[data-zone="available"]');
                    const selectedZone = selector.querySelector('[data-zone="selected"]');
                    const detailEmpty = selector.querySelector('[data-detail-empty]');
                    let activeCategory = null;
                    let draggedCard = null;

                    const categoryCards = function (key) {
                        return selector.querySelectorAll('[data-category="' + CSS.escape(key) + '"]');
                    };
                    const panel = function (key) {
                        return selector.querySelector('[data-question-panel="' + CSS.escape(key) + '"]');
                    };
                    const isSelected = function (key) {
                        const card = selector.querySelector('[data-category="' + CSS.escape(key) + '"]');
                        return card && card.closest('[data-zone="selected"]') !== null;
                    };
                    const updateEmptyState = function () {
                        const hasSelected = selectedZone.querySelector('.spl-category-card');
                        selectedZone.querySelector('[data-empty-state]').hidden = Boolean(hasSelected);
                    };
                    const updateCount = function () {
                        const count = Array.from(selector.querySelectorAll('[data-question-input]'))
                            .filter(function (input) { return !input.disabled && input.checked; }).length;
                        selector.querySelector('[data-selected-count]').textContent = count;
                    };
                    const updatePanel = function (key) {
                        const categoryPanel = panel(key);
                        if (!categoryPanel) return;
                        const enabled = isSelected(key);
                        const inputs = categoryPanel.querySelectorAll('[data-question-input]');
                        const status = categoryPanel.querySelector('[data-category-status]');
                        const help = categoryPanel.querySelector('[data-category-help]');
                        inputs.forEach(function (input) { input.disabled = locked || !enabled; });
                        status.textContent = enabled ? 'Kategori digunakan' : 'Belum digunakan';
                        status.classList.toggle('is-active', enabled);
                        help.textContent = enabled
                            ? 'Aktifkan hanya pertanyaan yang ingin ditampilkan pada survei.'
                            : 'Tambahkan kategori ini terlebih dahulu untuk mengaktifkan pertanyaannya.';
                        updateCount();
                    };
                    const showCategory = function (key) {
                        activeCategory = key;
                        selector.querySelectorAll('[data-question-panel]').forEach(function (item) { item.hidden = true; });
                        selector.querySelectorAll('.spl-category-card').forEach(function (item) { item.classList.toggle('is-active', item.dataset.category === key); });
                        detailEmpty.hidden = true;
                        panel(key).hidden = false;
                        updatePanel(key);
                    };
                    const moveCategory = function (key, destination) {
                        if (locked) return;
                        const card = selector.querySelector('[data-category="' + CSS.escape(key) + '"]');
                        if (!card || !destination) return;
                        destination.appendChild(card);
                        card.classList.toggle('is-selected', destination === selectedZone);
                        card.querySelectorAll('[data-add-category], [data-remove-category]').forEach(function (button) { button.remove(); });
                        const action = document.createElement('button');
                        const adding = destination === availableZone;
                        action.type = 'button';
                        action.className = 'spl-category-card-action' + (adding ? '' : ' is-remove');
                        action.dataset[adding ? 'addCategory' : 'removeCategory'] = key;
                        action.setAttribute('aria-label', (adding ? 'Tambahkan' : 'Hapus') + ' kategori');
                        action.innerHTML = '<i class="bi bi-' + (adding ? 'plus-lg' : 'x-lg') + '"></i>';
                        card.appendChild(action);
                        const inputs = panel(key).querySelectorAll('[data-question-input]');
                        inputs.forEach(function (input) {
                            input.disabled = destination === availableZone;
                            input.checked = destination === selectedZone;
                        });
                        updateEmptyState();
                        showCategory(key);
                    };

                    selector.addEventListener('click', function (event) {
                        const selectAll = event.target.closest('[data-select-all-categories]');
                        const add = event.target.closest('[data-add-category]');
                        const remove = event.target.closest('[data-remove-category]');
                        const show = event.target.closest('[data-show-category]');
                        if (selectAll) {
                            Array.from(availableZone.querySelectorAll('.spl-category-card')).forEach(function (card) {
                                moveCategory(card.dataset.category, selectedZone);
                            });
                            return;
                        }
                        if (add) { moveCategory(add.dataset.addCategory, selectedZone); return; }
                        if (remove) { moveCategory(remove.dataset.removeCategory, availableZone); return; }
                        if (show) { showCategory(show.dataset.showCategory); }
                    });
                    selector.addEventListener('change', function (event) {
                        if (event.target.matches('[data-question-input]')) updateCount();
                    });

                    if (!locked) {
                        selector.querySelectorAll('.spl-category-card').forEach(function (card) {
                            card.addEventListener('dragstart', function (event) {
                                draggedCard = card;
                                card.classList.add('is-dragging');
                                event.dataTransfer.effectAllowed = 'move';
                            });
                            card.addEventListener('dragend', function () {
                                card.classList.remove('is-dragging');
                                draggedCard = null;
                                selector.querySelectorAll('.spl-category-list').forEach(function (zone) { zone.classList.remove('is-drag-over'); });
                            });
                        });
                        [availableZone, selectedZone].forEach(function (zone) {
                            zone.addEventListener('dragover', function (event) { event.preventDefault(); zone.classList.add('is-drag-over'); });
                            zone.addEventListener('dragleave', function () { zone.classList.remove('is-drag-over'); });
                            zone.addEventListener('drop', function (event) {
                                event.preventDefault();
                                zone.classList.remove('is-drag-over');
                                if (draggedCard) moveCategory(draggedCard.dataset.category, zone);
                            });
                        });
                    }

                    updateEmptyState();
                    updateCount();
                    const firstCard = selector.querySelector('.spl-category-card');
                    if (firstCard) showCategory(firstCard.dataset.category);
                });
            });
        </script>
    @endpush
@endonce
