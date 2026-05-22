@props([
    'id' => 'cm-filter-sheet',
    'title' => 'Filters',
])

<div
    id="{{ $id }}"
    class="cm-bottom-sheet"
    aria-hidden="true"
    role="dialog"
    aria-modal="true"
    aria-labelledby="{{ $id }}-title"
>
    <div class="cm-bottom-sheet__overlay" data-cm-sheet-close tabindex="-1"></div>
    <div class="cm-bottom-sheet__panel">
        <div class="cm-bottom-sheet__handle" data-cm-sheet-handle aria-hidden="true"></div>
        <header class="cm-bottom-sheet__header">
            <h2 id="{{ $id }}-title" class="cm-bottom-sheet__title">{{ $title }}</h2>
            <button type="button" class="cm-bottom-sheet__close" data-cm-sheet-close aria-label="Close filters">
                <span class="material-icons" aria-hidden="true">close</span>
            </button>
        </header>
        <div class="cm-bottom-sheet__body vehicle-finder filter-scroll">
            {{ $slot }}
        </div>
        <footer class="cm-bottom-sheet__footer">
            <button type="button" class="cm-bottom-sheet__btn cm-bottom-sheet__btn--ghost" data-cm-sheet-close>
                Cancel
            </button>
            <button
                type="button"
                class="cm-bottom-sheet__btn cm-bottom-sheet__btn--primary"
                data-cm-sheet-apply="{{ $id }}"
            >
                Apply Filters
            </button>
        </footer>
    </div>
</div>
