{{-- Noindex for staging / duplicate site so main site SEO is not affected --}}
@if(config('app.noindex') || request()->getHost() === 'kaymark.360webcoders.com')
    <meta name="robots" content="noindex, nofollow">
@endif
