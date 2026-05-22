@props(['show' => false])

@if($show)
<div
    {{ $attributes->merge(['class' => 'cm-outbid-banner']) }}
    role="alert"
    data-cm-outbid-banner
>
    <span class="material-icons cm-outbid-banner__icon" aria-hidden="true">trending_down</span>
    <p class="cm-outbid-banner__text">
        You've been outbid — place a higher bid to stay in
    </p>
    <button type="button" class="cm-outbid-banner__dismiss" data-cm-outbid-dismiss aria-label="Dismiss">
        <span class="material-icons" aria-hidden="true">close</span>
    </button>
</div>
@endif
