# Security & Compliance Checklist – CayMark

Ye list batati hai ki kaun si cheezein **already implemented** hain aur kaun si **abhi add karni hain**.

---

## ✅ Already in place (implemented / partially)

| # | Requirement | Status | Notes |
|---|-------------|--------|--------|
| 1 | **Protection against SQL injection** | ✅ Done | Laravel Eloquent / Query Builder use ho raha hai; raw queries parameterised honi chahiye. |
| 2 | **Protection against XSS** | ✅ Done | Blade `{{ }}` auto-escape karta hai. |
| 3 | **Server-side validation of form inputs** | ✅ Done | Form Requests (40+ files) aur controller `validate()` use ho rahe hain. |
| 4 | **Secure session management** | ✅ Partial | `config/session.php`: driver database, `http_only` true, `same_site` lax. **Production par `SESSION_SECURE_COOKIE=true` aur `SESSION_ENCRYPT=true` set karo.** |
| 5 | **Strong authentication (hashing)** | ✅ Done | `Hash::make()` / bcrypt sab signup/password update pe use ho raha hai. |
| 6 | **Strong password policies** | ✅ Partial | Registration: `Password::min(8)`, profile: `Password::defaults()`. **Optional:** `Password::defaults()` ko `mixedCase()->numbers()->symbols()->uncompromised()` se strengthen karo (AppServiceProvider). |
| 7 | **Role-based access (concept)** | ✅ Partial | `User.role` (buyer/seller/admin) hai; sidebar role ke hisaab se dikhata hai. **Admin routes par middleware nahi hai – ye add karna baaki hai.** |
| 8 | **Activity logging (user context)** | ✅ Partial | Admin user-details pe bidding/payment activity log dikh raha hai. **System-wide audit log (admin actions, logins) abhi limited hai.** |

---

## ❌ Need to be added / configured

| # | Requirement | Status | What to do |
|---|-------------|--------|------------|
| 1 | **Website must use HTTPS (TLS 1.2+)** | ❌ Config | Production server par valid SSL certificate lagao. Laravel mein `AppServiceProvider::boot()` mein `URL::forceScheme('https')` use karo jab `APP_ENV=production`. |
| 2 | **All login pages encrypted** | ❌ Env | HTTPS enable karne se login bhi encrypted ho jayega. Ensure `APP_URL` https hai. |
| 3 | **All payment redirections encrypted** | ❌ Env | Same – HTTPS + payment URLs https se serve karo. |
| 4 | **Hosting: malware monitoring** | ❌ Hosting | Ye server/hosting level pe hai (e.g. Sucuri, Imunify360, cPanel security). Code change se nahi hota. |
| 5 | **Prevent malicious script injection** | ⚠️ Partial | Laravel/Blade default escaping theek hai. **Optional:** Content-Security-Policy (CSP) header add karo. |
| 6 | **Hosted payment page or secure iFrame only** | ❌ Not done | Abhi card number/expiry/CVV apne form se collect ho raha hai (`DemoPaymentGateway`, `payment-checkout-single`, tow-provider signup, complete-registration). **Compliance ke liye:** Real gateway ka hosted payment page ya PCI-compliant iframe (e.g. Stripe Elements / hosted checkout) use karo; card data apne server par mat bhejo. |
| 7 | **Gateway PCI DSS compliance (AOC on file)** | ❌ Process | Jab real payment provider integrate karoge, unka PCI DSS AOC (Attestation of Compliance) file mein maintain karo – ye business/ops process hai. |
| 8 | **Regular system and software updates** | ❌ Process | `composer update`, `npm update`, OS/panel security patches schedule karo – process/policy. |
| 9 | **Role-based access for backend (admin)** | ❌ Not enforced | Admin routes (`routes/web.php` prefix `admin`) par **auth + role=admin** middleware nahi hai. **Add:** `EnsureUserIsAdmin` (ya similar) middleware banao aur saari admin routes ko us middleware se wrap karo. |
| 10 | **Activity logging and monitoring** | ⚠️ Partial | User activity (bids, payments) admin user-details pe hai. **Add:** Admin actions (approve listing, suspend user, etc.) aur login/failed-login ke liye dedicated audit log table + logging. |
| 11 | **Two-factor authentication (2FA) for admin** | ❌ Not done | Koi 2FA/TOTP implementation nahi mili. **Recommended:** Admin users ke liye 2FA enable karo (e.g. `laravel/fortify` with 2FA ya dedicated 2FA package). |

---

## Quick reference – code/config locations

- **Session / cookies:** `config/session.php` (`secure`, `encrypt`, `http_only`, `same_site`).
- **HTTPS:** `AppServiceProvider::boot()` – `URL::forceScheme('https')` when production.
- **Password rules:** `App\Http\Controllers\Auth\RegisteredUserController` (min 8), `PasswordController` / `NewPasswordController` (`Password::defaults()`). Strengthen in `App\Providers\AppServiceProvider` with `Password::defaults()`.
- **Admin routes:** `routes/web.php` – `Route::prefix('admin')->group(...)` – yahan middleware add karna hai.
- **Payment:** `App\Services\DemoPaymentGateway`, `BuyerPaymentOps`, views: `Buyer/payment-checkout-single.blade.php`, `auth/complete-registration.blade.php`, `tow-provider/signup.blade.php` – card fields hata kar hosted/iframe flow use karna hoga.
- **Activity log:** `AdminController::getUserActivityLog()` – isko extend karke admin actions + auth events ke liye audit log add kiya ja sakta hai.

---

**Summary:** SQL injection, XSS, server-side validation, hashed passwords, aur basic session/role structure theek hain. **Code mein ab add ho chuka hai:** HTTPS force (production), strong password defaults, admin route middleware (`auth` + `admin`), session secure/encrypt env docs, admin activity audit log. **Server/process pe kya karna hai:** See **docs/SERVER-TASKS.md**.
