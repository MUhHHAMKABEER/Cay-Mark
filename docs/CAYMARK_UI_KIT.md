# CayMark UI Kit (Section 1 — Feedback & Notifications)

Included globally via `@include('partials.caymark-ui-kit')` on all main layouts.

## JavaScript API

```javascript
// Toasts (auto-dismiss ~3.5s, swipe to dismiss on mobile)
showSuccess('Files uploaded successfully', 'You will receive review results in your email in 2-4 days');
showError('Files are larger than 5 MB', 'Try to compress your files and upload them again');

// Confirmation modal (returns Promise<boolean>)
CaymarkUI.confirm({
  title: 'Confirm your bid',
  description: 'Are you sure you want to place a bid of $8,000?',
  confirmText: 'Confirm Bid',
  cancelText: 'Cancel',
  danger: false, // true for destructive actions
}).then(function (ok) { if (ok) { /* proceed */ } });

// Skeleton loading
CaymarkUI.skeleton.show('#my-list', { variant: 'auction-grid', count: 6 });
CaymarkUI.skeleton.hide('#my-list');

// Or wrap fetch:
CaymarkUI.skeleton.fetch('#my-list', fetch('/api/items'), { variant: 'table', count: 5 });

// Multi-step progress (also available as Blade component)
CaymarkUI.updateProgress('#my-progress', 2, 3);
```

### Skeleton variants
- `auction-row` (default) — horizontal card
- `auction-grid` — grid of cards
- `table` — table rows
- `profile` — avatar + lines

## Blade components

```blade
<x-ui.empty-state variant="no-auctions" />
<x-ui.empty-state variant="no-watchlist" />
<x-ui.empty-state variant="no-notifications" />
<x-ui.empty-state variant="no-search-results" />

<x-ui.skeleton variant="auction-grid" :count="6" />

<x-ui.progress-steps
  :step="2"
  :total="3"
  :labels="['Vehicle Information', 'Condition + Media', 'Auction + Payment']"
/>
```

### Loading host pattern

```blade
<div id="auctions-host" class="cm-skeleton-host">
  <div data-cm-skeleton>@include or <x-ui.skeleton /></div>
  <div data-cm-content>
    {{-- real content --}}
  </div>
</div>
```

```javascript
CaymarkUI.setLoading(document.getElementById('auctions-host'), true, { variant: 'auction-grid', count: 6 });
// ... fetch ...
CaymarkUI.setLoading(host, false);
```

## Laravel flash
`session('success')`, `session('status')`, and `session('error')` automatically show toasts on page load.

---

# Section 2 — Forms & Input

Loaded via `caymark-ui-forms.js` on every page that includes the UI kit.

## Automatic behavior (all pages)

| Feature | Scope |
|--------|--------|
| **Inline validation** | All POST forms except `data-cm-validate="off"` and GET forms |
| **Password show/hide** | All `input[type=password]` without an existing toggle button |
| **Password strength** | Fields with `data-password-strength` |
| **Phone format** | `.js-phone-format` + optional `data-phone-country-select="#countrySelect"` |
| **Digits only** | `.js-digits-only` |
| **Character counter** | `textarea[maxlength]` or `data-char-max="500"` |

## Field attributes

```html
<input type="email" name="email" required data-cm-label="Email">
<input type="password" data-password-strength data-cm-validate="password-register" minlength="8">
<input type="password" data-cm-match="#password" name="password_confirmation">
<input class="js-digits-only js-phone-format" data-phone-country-select="#reg_phone_country" data-cm-validate="phone">
<textarea maxlength="300"></textarea>
```

## JavaScript API

```javascript
CaymarkUI.forms.validateForm(document.querySelector('#myForm'));
CaymarkUI.forms.validateField(document.querySelector('#email'));
CaymarkUI.forms.scorePassword('MyPass123!');
CaymarkUI.forms.formatPhone('5551234', '1242'); // → (242) 555-1234
```

## Opt out of validation

```html
<form data-cm-validate="off" id="bidForm">...</form>
```

## Custom error message

```html
<input data-cm-error="Email is required">
```

---

# Section 3 — Navigation & Layout

Loaded on every page that includes `partials/caymark-ui-kit` (all main layouts).

## Breadcrumbs

Automatic trails from `config/breadcrumbs.php` by route name. Rendered via `<x-ui.breadcrumbs />` on welcome, dashboard, and admin layouts.

### Override API

Highest priority wins:

1. `view()->share('cmBreadcrumbs', [...])` or controller/view `$cmBreadcrumbs`
2. `config/breadcrumbs.php` route map (supports `{listing_title}` placeholders from shared `listing` / `auctionListing`)
3. Minimal fallback from route name

```php
// In a controller before return view():
view()->share('cmBreadcrumbs', [
    ['label' => 'Home', 'route' => 'welcome'],
    ['label' => 'Auctions', 'route' => 'Auction.index'],
    ['label' => '2019 Honda Civic'], // last item = current page (no url)
]);
```

```blade
{{-- Optional explicit items --}}
<x-ui.breadcrumbs :items="$customCrumbs" />
```

Placeholders in config resolve from view-shared models, e.g. `{listing_title}` uses `year make model` from `auctionListing` or `listing`.

## Back to top

Fixed button (`#cm-back-to-top`) appears after scrolling 300px. Smooth-scrolls to top on click. Markup in `partials/caymark-ui-nav.blade.php`; behavior in `caymark-ui-nav.js`.

## Sticky header scroll shadow

Public header uses class `cm-site-header`. When `window.scrollY > 10`, JS adds `cm-header--scrolled` for enhanced box-shadow.

## Active nav (public header)

Main nav links use `cm-nav-link` and `cm-nav-link--active` with `aria-current="page"` when active. Auction tab is active on: `Auction.index`, `auction.show`, `auction.dashboard`, `listing.show`.

## Branded 404

`resources/views/errors/404.blade.php` — CayMark navy/gold styling, links to home and auctions, optional search form. Non-JSON `NotFoundHttpException` responses render this view via `bootstrap/app.php`.

---

# Section 4 — Auction-Specific UI

Loaded via `caymark-ui-auction.js` on every page that includes the UI kit.

## JavaScript API

```javascript
// Live countdowns — all [data-cm-countdown-end] elements; ticks every second
CaymarkUI.auction.initCountdowns();           // whole document
CaymarkUI.auction.initCountdowns(container);  // after AJAX inject

// Bid confirmation (vehicle name, amount, buyer fee estimate)
CaymarkUI.auction.confirmBid({
  vehicleName: '2019 Honda Civic',
  amount: 8500,
  buyerFee: 510, // optional; calculated via calcBuyerFee if omitted
}).then(function (ok) { if (ok) { /* submit bid */ } });

CaymarkUI.auction.calcBuyerFee(8500); // uses window.CaymarkUIAuctionConfig or 6% / $100 min

// Watchlist heart buttons ([data-cm-watchlist-heart])
CaymarkUI.auction.initWatchlistHearts();
CaymarkUI.auction.initOutbidBanners(); // dismiss handlers for outbid banner
```

Set buyer fee defaults on auction detail (optional):

```html
<script>
  window.CaymarkUIAuctionConfig = {
    buyerFeeRate: 0.06,
    buyerFeeMin: 100,
  };
</script>
```

### Countdown behavior

- Updates every second: `Days : Hours : Minutes : Seconds` (detail) or segmented grid display.
- Adds `cm-countdown--urgent` when under 1 hour remaining (red styling).
- Ended auctions show “Auction Ended” / “Ended”.

## Blade components

```blade
<x-ui.countdown :end="$endDate" :listing-id="$listing->id" variant="detail" />
<x-ui.countdown :end="$endDate" :listing-id="$listing->id" variant="grid" />

<x-ui.outbid-banner :show="$showOutbidBanner" />

<x-ui.watchlist-heart :listing="$listing" :in-watchlist="$inWatchlist" />
<x-ui.watchlist-heart :listing="$listing" :in-watchlist="$liked" variant="button" />

<x-ui.ending-soon-badge :end="$endDate" />
```

`:end` accepts ISO string or `Carbon` instance. Ending-soon badge renders only when auction ends in under 1 hour.

### Controller: outbid banner

In `AuctionController::show` (and `auctionDetailBuyer`):

```php
$showOutbidBanner = Auth::check()
    && $listing->bids()->where('user_id', Auth::id())->where('status', 'active')->exists()
    && $highestActiveBid
    && (int) $highestActiveBid->user_id !== (int) Auth::id();
```

## Routes used

- `listing.watchlist` — POST toggle (JSON)
- `auction.show` — detail page
- `Auction.index` — marketplace grid

---

# Section 5 — User Account Area

Loaded via `caymark-ui-account.js` on every page that includes the UI kit.

## Blade components

```blade
<x-ui.avatar :user="$user" size="sm|md|lg" />

<x-ui.profile-completion :user="$user" />

<x-ui.notification-bell
    :user="$user"
    :notifications="$recentNotifications"
    :unread-count="$unreadCount"
    :notifications-url="route('buyer.notifications')"
/>

<x-ui.activity-timeline :items="$activityTimeline" />
```

### Profile completion

`App\Helpers\ProfileCompletionHelper::evaluate($user)` returns:

- `percent` (0–100)
- `complete` (bool)
- `missingFields` — each with `label`, `field`, optional `route` / `url`

Shown on buyer/seller dashboard overview and account tabs when incomplete. Links route to `buyer.user`, `seller.account`, or `finish.registration` as appropriate.

### Notification bell

- Badge caps at **9+** for counts over 9
- Dropdown shows last ~8 notifications (`data.message`, relative time)
- Toggle via click; closes on outside click or **Escape**
- Optional mark-as-read on item click (`POST` `buyer.notifications.mark-read` / `seller.notifications.mark-read`)
- Integrated in `partials/unified-header.blade.php` for buyer and seller headers

### Activity timeline

`App\Services\UserActivityTimelineService` builds up to 15 recent items (newest first):

| Role | Event types |
|------|-------------|
| Buyer | Bids placed, auctions won, watchlist saves |
| Seller | Listings posted (+ buyer events if applicable) |

Passed as `$activityTimeline` from `BuyerDashboardService` / `SellerDashboardService`.

### Avatar

- Uses profile image column if present (`profile_image`, `profile_image_path`, etc.)
- Otherwise navy/gold initials circle with deterministic color from user id

## PHP helpers

```php
use App\Helpers\ProfileCompletionHelper;

$data = ProfileCompletionHelper::evaluate($user);
$done = ProfileCompletionHelper::isComplete($user);
```

```php
$items = app(\App\Services\UserActivityTimelineService::class)->buildFor($user);
```

---

# Section 6 — Mobile Specific

Loaded via `caymark-ui-mobile.js` and `partials/caymark-ui-mobile.blade.php` on every page that includes the UI kit (after `caymark-ui.js`).

## JavaScript API

```javascript
// Pull to refresh (max-width 768px only)
CaymarkUI.mobile.initPullToRefresh(container, onRefresh);
// onRefresh may return a Promise

// Bottom sheet
CaymarkUI.mobile.openBottomSheet('cm-auction-filter-sheet');
CaymarkUI.mobile.closeBottomSheet('cm-auction-filter-sheet');

CaymarkUI.mobile.isMobileViewport(); // matchMedia (max-width: 768px)
```

### Auto-init attributes

| Attribute | Behavior |
|-----------|----------|
| `data-cm-pull-refresh` | Pull-to-refresh on host (optional `data-cm-pull-on-refresh="globalFnName"`) |
| `data-cm-open-sheet="sheet-id"` | Opens bottom sheet on click |

### Auction index (`auction.blade.php`)

- **Pull to refresh:** `#cm-auction-pull-root` calls `window.__auctionRefresh()` → Alpine `applyFilters()` (AJAX listings + pagination).
- **Filters:** Sidebar visible at `xl` (1280px+). Below `xl`, floating **Filters** (`cm-mobile-filters-btn`) opens `<x-ui.filter-bottom-sheet id="cm-auction-filter-sheet">`.
- Shared fields: `partials/auction-vehicle-finder-fields.blade.php` (aside + sheet; same Alpine `filterData()` scope).

### Toast swipe (Section 1)

Horizontal swipe-to-dismiss on `max-width: 768px` or `pointer: coarse` (`caymark-ui.js` → `bindToastSwipe`). Vertical drags are ignored; threshold ~80px.

## Blade components

```blade
<x-ui.filter-bottom-sheet id="cm-auction-filter-sheet" title="Vehicle Finder">
    @include('partials.auction-vehicle-finder-fields')
</x-ui.filter-bottom-sheet>

<x-ui.fab-post-listing />
<x-ui.fab-post-listing href="{{ route('seller.listings.create') }}" />
```

### FAB — Post a Listing

| User | Default link |
|------|----------------|
| Seller | `seller.listings.create` |
| Buyer | `register` |
| Incomplete registration | `finish.registration` |
| Guest | `login` with `redirect` to listing create |

- Class: `.cm-fab-post-listing` (hidden at `xl+`; icon-only under 380px width).
- Included globally via `caymark-ui-mobile.blade.php`; hidden on admin and seller submit-listing routes.

## CSS classes

- `.cm-bottom-sheet`, `.cm-bottom-sheet--open` / `.is-open` — overlay + panel
- `.cm-mobile-filters-btn` — auction filters trigger
- `.cm-fab-post-listing` — post listing FAB (safe-area insets)
- `.cm-pull-refresh`, `.cm-pull-refresh-host` — pull indicator

Desktop (`xl+`): sidebar layout unchanged; mobile controls hidden via CSS.
