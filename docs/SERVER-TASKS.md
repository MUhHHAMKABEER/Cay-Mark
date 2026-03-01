# Server / Hosting par kya karna hai (Security & Compliance)

Ye sab **server / hosting / process** pe karne hain; code mein ye change nahi kiye ja sakte.

---

## 1. HTTPS aur SSL certificate

- **Kya:** Website ko **HTTPS** pe chalao, **valid SSL certificate** (TLS 1.2 ya usse upar) use karo.
- **Kaise:**
  - Hosting panel (cPanel, Plesk, etc.) se “SSL/TLS” section mein certificate install karo (Let’s Encrypt free hai).
  - Ya cloudflare / apache-nginx config se HTTPS force karo.
- **Laravel:** Production par `APP_URL=https://yourdomain.com` set karo. Code mein `URL::forceScheme('https')` already production ke liye add hai.

---

## 2. Login / payment pages encrypted

- **Kya:** Saari login aur payment wali pages HTTPS pe hon.
- **Kaise:** Jab poora site HTTPS pe hoga (step 1), login aur payment URLs bhi automatically HTTPS pe chalenge. Koi alag code change nahi.

---

## 3. Session cookies secure (production)

- **Kya:** Production pe session cookies sirf HTTPS pe bheje jayein.
- **Kaise:** `.env` mein (sirf production) set karo:
  - `SESSION_SECURE_COOKIE=true`
  - Optional: `SESSION_ENCRYPT=true` (session data encrypt ho kar store hogi).

---

## 4. Malware monitoring

- **Kya:** Server / hosting pe malware scanning / monitoring.
- **Kaise:** Hosting provider ke security tools use karo (e.g. Imunify360, Sucuri, cPanel “Malware Scanner”). Ye server/hosting level pe hota hai, code change se nahi.

---

## 5. Regular system & software updates

- **Kya:** OS, PHP, Laravel, npm packages sab regularly update karo.
- **Kaise:**
  - Server: `apt update && apt upgrade` (Linux) ya hosting panel updates.
  - App: `composer update`, `npm update` (staging pe test karke phir production).
  - Ek schedule bana lo (e.g. mahine mein ek baar security updates).

---

## 6. Payment: Hosted page / PCI DSS

- **Kya:** Card data apne server par mat collect karo; **hosted payment page** ya gateway ka **secure iframe** use karo. Gateway ka **PCI DSS AOC** (Attestation of Compliance) file mein rakho.
- **Kaise:**
  - Stripe / Braintree / local gateway ka **hosted checkout** ya **Elements/iframe** integrate karo; card number apne form se hata do.
  - Gateway provider se **PCI DSS AOC** document le kar secure jagah (e.g. internal drive/docs) pe store karo. Ye business/ops process hai.

---

## 7. Two-factor authentication (2FA) for admin (recommended)

- **Kya:** Admin login pe 2FA (e.g. TOTP app se code).
- **Kaise:** Code mein 2FA add kiya ja sakta hai (e.g. Laravel Fortify 2FA). Agar abhi nahi kiya hai to baad mein package add karke admin-only 2FA enable karo.

---

## Short checklist (server / you)

| # | Task | Where |
|---|------|--------|
| 1 | HTTPS + valid SSL (TLS 1.2+) enable karo | Server / hosting panel |
| 2 | `APP_URL=https://...` set karo | Production `.env` |
| 3 | `SESSION_SECURE_COOKIE=true` (aur optional `SESSION_ENCRYPT=true`) | Production `.env` |
| 4 | Malware scanning / monitoring enable karo | Hosting / server security |
| 5 | OS, PHP, composer, npm regular update | Server + deployment process |
| 6 | Payment: hosted page / iframe + gateway PCI AOC on file | Gateway integration + docs |
| 7 | (Optional) Admin 2FA enable karo | Code + config |

Code side pe jo changes ho sakte the (HTTPS force, strong password, admin middleware, session env docs, activity logging) wo already implement ho chuke hain.
