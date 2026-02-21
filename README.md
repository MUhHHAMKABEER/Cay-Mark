# CayMark — Island Exchange & Auction House

**The Bahamas' premier digital vehicle auction and marketplace.** CayMark connects buyers and sellers across the islands for cars, boats, and equipment—with live auctions, buy-now listings, and secure payments.

---

## About

CayMark is a full-stack web application for:

- **Buyers:** Browse and bid on auctions, buy now listings, manage watchlist, deposit/withdraw funds, pay for won items, and coordinate pickup.
- **Sellers:** Create auction or buy-now listings (with VIN/HIN decoder), manage payouts, and handle post-sale pickup.
- **Admins:** Approve listings, manage users, payouts, disputes, revenue tracking, and email templates.
- **Public:** Fee calculator, help center, video guides, tow provider directory, and contact.

Listings are organized by **island** (Bahamas) and support vehicles, marine vessels, and equipment.

---

## Tech Stack

| Layer        | Technology |
|-------------|------------|
| Backend     | **Laravel 12**, PHP 8.2+ |
| Frontend    | **Blade**, **Tailwind CSS**, **Alpine.js**, **Vite** |
| PDF         | barryvdh/laravel-dompdf |
| Real-time   | Pusher (optional) |
| Database    | MySQL / MariaDB or SQLite (dev) |

---

## Requirements

- **PHP** 8.2+
- **Composer** 2.x
- **Node.js** 18+ (for Vite / frontend assets)
- **MySQL** 5.7+ / **MariaDB** 10.3+ or **SQLite** (development)

---

## Installation

### 1. Clone and install PHP dependencies

```bash
git clone https://github.com/your-username/Cay-Mark.git
cd Cay-Mark
composer install
```

### 2. Environment and key

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configure environment

Edit `.env`:

- **Database:** Set `DB_CONNECTION`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (or keep `DB_CONNECTION=sqlite` and ensure `database/database.sqlite` exists).
- **App:** Set `APP_NAME`, `APP_URL`, and optionally `NOINDEX=true` for staging so search engines don’t index the site.

```env
APP_NAME="CayMark"
APP_URL=http://localhost

# Database (MySQL example)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=caymark
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Database

```bash
php artisan migrate
# Optional: seed data
# php artisan db:seed
```

### 5. Frontend assets

```bash
npm install
npm run build
```

For development with hot reload:

```bash
npm run dev
```

### 6. Storage and cache

```bash
php artisan storage:link
php artisan config:clear
php artisan cache:clear
```

### 7. Run the app

```bash
php artisan serve
```

Open `http://localhost:8000` (or your `APP_URL`).

---

## Project structure (high level)

```
app/
├── Http/Controllers/
│   ├── Buyer/          # Auctions, bids, marketplace, payments, deposits, messaging
│   ├── Seller/          # Listings, payouts, dashboard
│   ├── Admin/           # Email templates, etc.
│   └── Auth/            # Registration (multi-step), login, password
├── Models/              # User, Listing, Bid, Invoice, Payout, Chat, etc.
├── Services/            # Bidding, deposits, relisting
config/
├── islands.php          # Bahamas islands list (listing location & tow providers)
database/
├── migrations/          # users, listings, bids, invoices, payouts, chats, etc.
resources/
├── views/
│   ├── layouts/        # welcome, dashboard, guest, admin, Buyer, Seller
│   ├── partials/       # unified-header, noindex-meta, auction-listings
│   ├── Buyer/          # Dashboard, auction detail, payment, messaging
│   ├── Seller/         # Dashboard, submit listing
│   └── admin/          # Admin panels
routes/
├── web.php             # Public, auth, admin, auction, marketplace
├── buyer.php           # Buyer dashboard, messaging, support
├── seller.php          # Seller dashboard, listings, payouts
└── auth.php            # Login, registration, password reset
```

---

## Features overview

### Public

- Homepage with hero, popular auctions (active only), vehicle finder
- **Auctions** — Browse with filters (island, make, model, etc.), sort (newest/oldest, price)
- **Marketplace** — Buy-now listings
- **Getting Started**, **Services & Support**, **Contact Us**
- Fee calculator, help center, video guides, rules & policies
- Tow provider directory and signup
- Staging / noindex: set `NOINDEX=true` or host `kaymark.360webcoders.com` for `noindex,nofollow` and `robots.txt` disallow

### Buyers (auth)

- Bid on auctions, watchlist, buy now
- Dashboard: overview, bids, saved items, notifications
- Wallet: deposits and withdrawals
- Payment checkout (single/multiple), invoices
- Post-auction: messaging, pickup details, PIN confirmation, third-party pickup
- Messaging center, support

### Sellers (auth)

- Payout method setup (required before listing)
- Create listing: auction or buy now, VIN/HIN decoder, vehicle details, images
- Dashboard: active listings, recently finished, payouts
- Post-sale: pickup PIN, confirm pickup

### Admin

- Dashboard and analytics
- User management (suspend, ban, reset password)
- Listing review (approve/reject), extend auction
- Payouts, payments, withdrawals
- Buyer defaults, second-chance purchases, unpaid auctions
- Invoice log, email templates
- Disputes, notifications (placeholders where applicable)

---



## Staging / SEO

To avoid indexing a staging or duplicate site (e.g. **kaymark.360webcoders.com**):

- **Automatic:** If the request host is `kaymark.360webcoders.com`, the app adds `<meta name="robots" content="noindex, nofollow">` and serves `robots.txt` with `Disallow: /`.
- **Manual:** Set `NOINDEX=true` in `.env` on any server to enable the same behavior.

Production main site should **not** use this host and should **not** set `NOINDEX=true`.

---

## License

Proprietary / All rights reserved (or specify your license).

---

## Support

For support or feature requests, open an issue or contact the project maintainers.
