@extends('layouts.welcome')
@section('title', 'Page Not Found - CayMark')

@section('content')
<section class="cm-error-404">
    <p class="cm-error-404__code" aria-hidden="true">404</p>
    <h1 class="cm-error-404__title font-heading">Page not found</h1>
    <p class="cm-error-404__desc">
        The page you are looking for may have been moved, removed, or never existed.
        Try searching our auctions or return to the homepage.
    </p>

    <div class="cm-error-404__actions">
        <a href="{{ route('welcome') }}" class="cm-error-404__btn cm-error-404__btn--primary">
            <span class="material-icons text-lg" aria-hidden="true">home</span>
            Back to Home
        </a>
        <a href="{{ route('Auction.index') }}" class="cm-error-404__btn cm-error-404__btn--secondary">
            <span class="material-icons text-lg" aria-hidden="true">gavel</span>
            Browse Auctions
        </a>
    </div>

    <form action="{{ route('Auction.index') }}" method="GET" class="cm-error-404__search" role="search">
        <label for="cm-404-search" class="sr-only">Search auctions</label>
        <input
            type="search"
            id="cm-404-search"
            name="search"
            placeholder="Search by make, model, island…"
            autocomplete="off"
        >
    </form>
</section>
@endsection
