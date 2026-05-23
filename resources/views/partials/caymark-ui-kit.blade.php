{{--
    CayMark global UI kit
    Section 1: CaymarkUI.showSuccess, showError, confirm, skeleton, updateProgress
    Section 2: CaymarkUI.forms — validation, password toggle/strength, phone format, char counter
    Section 3: breadcrumbs, back-to-top, header scroll shadow (caymark-ui-nav.js)
    Section 4: CaymarkUI.auction — countdown, watchlist heart, confirmBid (caymark-ui-auction.js)
    Section 5: notification bell dropdown (caymark-ui-account.js)
    Section 6: pull-to-refresh, filter bottom sheet, FAB (caymark-ui-mobile.js)
    Blade: <x-ui.empty-state>, <x-ui.skeleton>, <x-ui.progress-steps>, <x-ui.breadcrumbs>,
           <x-ui.countdown>, <x-ui.outbid-banner>, <x-ui.watchlist-heart>, <x-ui.ending-soon-badge>,
           <x-ui.avatar>, <x-ui.profile-completion>, <x-ui.notification-bell>, <x-ui.activity-timeline>,
           <x-ui.filter-bottom-sheet>, <x-ui.fab-post-listing>
--}}
<link rel="stylesheet" href="{{ asset('css/caymark-ui.css') }}">

<div id="caymark-toast-host" aria-live="polite" aria-relevant="additions"></div>

<div id="cm-confirm-overlay" class="cm-confirm-overlay" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="cm-confirm-title">
    <div class="cm-confirm-dialog">
        <h2 id="cm-confirm-title" class="cm-confirm-dialog__title" data-cm-confirm-title>Confirm</h2>
        <p class="cm-confirm-dialog__desc" data-cm-confirm-desc></p>
        <div class="cm-confirm-dialog__actions">
            <button type="button" class="cm-confirm-dialog__btn cm-confirm-dialog__btn--cancel" data-cm-confirm-cancel>Cancel</button>
            <button type="button" class="cm-confirm-dialog__btn cm-confirm-dialog__btn--confirm" data-cm-confirm-ok>Confirm</button>
        </div>
    </div>
</div>

@php
    $flashSuccess = session('success') ?? session('status');
    $flashError   = session('error');
    if (is_array($flashError)) {
        $flashError = implode(' ', $flashError);
    }
    // Fold validation errors into the global error toast (first message wins)
    if (empty($flashError) && isset($errors) && $errors->any()) {
        $flashError = $errors->first();
    }
@endphp
<script>
    window.CaymarkUIFlash = {
        @if(!empty($flashSuccess)) success: @json($flashSuccess), @endif
        @if(!empty($flashError))   error:   @json($flashError),   @endif
    };
</script>
@include('partials.caymark-ui-nav')
@include('partials.caymark-ui-mobile')
<script src="{{ asset('js/caymark-ui.js') }}" defer></script>
<script src="{{ asset('js/caymark-ui-forms.js') }}" defer></script>
<script src="{{ asset('js/caymark-ui-nav.js') }}" defer></script>
<script src="{{ asset('js/caymark-ui-auction.js') }}" defer></script>
<script src="{{ asset('js/caymark-ui-account.js') }}" defer></script>
<script src="{{ asset('js/caymark-ui-mobile.js') }}" defer></script>
