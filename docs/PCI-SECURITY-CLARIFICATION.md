# PCI & Security – Clarification (CayMark)

Answers to your questions based on the current codebase and what still needs to be done.

---

## 1. Do we enforce minimum password length and complexity for both users and administrators?

**Yes.** The same rules apply to all users (including admins).

- **Where:** `AppServiceProvider` sets `Password::defaults()`; used in registration, profile password update, and password reset.
- **Rules:** Minimum 8 characters, must include letters (mixed case) and numbers.
- **Code:** `app/Providers/AppServiceProvider.php` (Password::min(8)->letters()->mixedCase()->numbers()).

**Optional:** To align with stricter PCI guidance, you can add symbols (e.g. `->symbols()`) in `AppServiceProvider`. Admin password resets use the same rules.

---

## 2. Is two-factor authentication (2FA) enabled for admin accounts?

**Yes.** 2FA (TOTP) is required for admin accounts.

- **Flow:** After an admin logs in with email/password, they are redirected to either set up 2FA (first time) or enter a 6-digit code from their authenticator app. Admin panel access is blocked until 2FA is verified for that session.
- **Implementation:** `TwoFactorController`, routes `admin.2fa.setup`, `admin.2fa.challenge`, `admin.2fa.verify`, `admin.2fa.confirm`; `EnsureUserIsAdmin` middleware enforces 2FA. Uses PragmaRX/Google2FA and optional QR code (e.g. Google Authenticator, Authy).
- **Note:** Regular users (buyers/sellers) do not use 2FA; it applies only to users with role `admin`.

---

## 3. Are account lockouts triggered after multiple failed login attempts?

**Yes.**

- **Where:** Login is rate-limited in `App\Http\Requests\Auth\LoginRequest`.
- **Behaviour:** After **5 failed attempts** (per email + IP), the user is locked out for a period; a `Lockout` event is fired. Throttle duration is driven by Laravel’s default rate limiter (e.g. 60 seconds or as configured).
- **Code:** `RateLimiter::tooManyAttempts($this->throttleKey(), 5)` and `event(new Lockout($this))`.

This applies to all logins (including admin). There is no separate lockout policy for admins in code.

---

## 4. Are all administrative activities logged within the system?

**Yes.** Important admin actions are logged to the `admin_activity_logs` table.

- **What is logged:** Listing approve/reject, user update/suspend/ban/password reset, listing edit, auction extend, payment release/hold/status update, payout status update, buyer default resolve/close.
- **Where:** `App\Models\AdminActivityLog` and calls from `AdminActionHub` and `AdminController`. Each record includes admin user, action, target type/id, old/new values (where relevant), IP, and user agent.
- **Table:** `admin_activity_logs` (migration already run).

**Gap:** View-only actions (e.g. opening a report) are not logged. If you need a full audit trail of every admin page view, that would require additional middleware or logging.

---

## 5. How long are user and admin session timeouts configured for?

**Same for everyone.** There is no separate session config for admins.

- **Where:** `config/session.php`.
- **Default:** `SESSION_LIFETIME` = **120 minutes** (2 hours) of idle time. Configurable via `.env`: `SESSION_LIFETIME=120`.
- **Optional:** `SESSION_EXPIRE_ON_CLOSE` – if true, session ends when the browser is closed.

**Recommendation:** For PCI, shorter idle timeouts for admin (e.g. 15–30 minutes) are often recommended. That would require either a separate session lifetime for admin routes or custom middleware that invalidates admin sessions after a shorter idle period.

---

## 6. Are system logs retained for an appropriate period and actively monitored?

**Partially.**

- **Retention:** Laravel’s default `single` channel writes to one file and does not rotate by date. The `daily` channel (if you set `LOG_CHANNEL=daily` and optionally `LOG_DAILY_DAYS=14`) keeps logs for a configurable number of days (e.g. 14). So retention is configurable via env, not hard-coded.
- **Monitoring:** The application only writes logs; it does not perform active monitoring, alerting, or log analysis. Monitoring (e.g. failed logins, errors, suspicious activity) is done via your hosting/log management (e.g. Papertrail, CloudWatch, SIEM, or internal ops).

**Recommendation:** Use `daily` (or similar) with a retention period that meets your policy (e.g. 90 days for PCI) and ensure a process exists to review and monitor logs (or use a log aggregation/monitoring service).

---

## 7. Do we have a documented incident response plan in place in the event of a security breach?

**What an incident response plan is**

An **incident response plan** is a **documented process** (policy/procedure), not something in the code. It describes what the organization will do when a **security incident** occurs (e.g. data breach, ransomware, unauthorized access, cardholder data exposure). It usually includes:

- **Definition of an incident** (e.g. confirmed breach of cardholder or PII data, system compromise).
- **Roles and responsibilities** (who leads response, who handles communications, who talks to the bank/processor).
- **Steps to take:** contain the incident, preserve evidence, notify affected parties and (where required) the payment brands/processor, restore systems, and conduct a post-incident review.
- **Contacts:** internal (IT, legal, management) and external (processor, legal counsel, forensics if needed).
- **Communication:** when and how to notify customers, regulators, and card brands (e.g. PCI DSS and card brand rules).
- **Review:** updating the plan and fixing gaps after an incident.

**Current status**

The codebase does not and cannot “implement” this plan. It is a **business/operational document** that CayMark (or the responsible person, e.g. Amya Fowler) should create and keep on file.

**Recommendation:** Draft a short **Incident Response Plan** document that covers the points above and states that in the event of a suspected or confirmed security breach, CayMark will follow that process (including notifying the payment processor and, if applicable, card brands and affected users). This supports both PCI expectations and good practice.

---

## Summary table

| Area | Status | Notes |
|------|--------|--------|
| Password length & complexity | ✅ Yes | Min 8, letters (mixed case), numbers; same for users and admins |
| 2FA for admin | ✅ Yes | TOTP required; setup then challenge each login |
| Account lockout | ✅ Yes | 5 failed attempts → lockout (all logins) |
| Admin activity logging | ✅ Yes | Key actions in `admin_activity_logs` |
| Session timeout | ✅ Configured | 120 min default; consider shorter for admin |
| Log retention & monitoring | ⚠️ Partial | Retention via config/daily driver; monitoring is operational |
| Incident response plan | ❌ Document | Create a written policy/procedure; not in code |

---

**Bottom line:** Password policy, lockout, and admin activity logging are in place in code. 2FA for admin, shorter admin session timeout, log retention/monitoring setup, and a documented incident response plan are the main items to add or confirm outside the application.
