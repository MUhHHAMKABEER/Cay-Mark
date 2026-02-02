# Payment Sandbox – Demo Gateway

The app uses a **demo payment gateway** for sandbox testing. No real bank API or Stripe keys are required for local/testing environments.

## Configuration

In `.env` (or `.env.example` for reference):

```env
# Payment – sandbox uses DemoPaymentGateway (no real charges)
PAYMENT_SANDBOX=true

# Optional: for future live Stripe integration
# STRIPE_KEY=
# STRIPE_SECRET=
```

- `PAYMENT_SANDBOX=true` (default) → all charges go through `App\Services\DemoPaymentGateway` (simulated).
- Set to `false` when you plug in a real gateway (e.g. Stripe); the demo gateway still simulates if no live gateway is configured.

## Test card numbers (sandbox only)

Use these card numbers **only** in sandbox. Any expiry (e.g. 12/26) and CVC (e.g. 123) work.

| Card number           | Result              |
|-----------------------|---------------------|
| **4242 4242 4242 4242** | Success             |
| **4000 0000 0000 0002** | Card declined       |
| **4000 0000 0000 9995** | Insufficient funds  |

Any other card number in sandbox returns an error asking you to use one of the test cards above.

## Secure connection (HTTPS)

Browser autofill for payment methods is disabled on **insecure (HTTP)** pages. For the best test experience (and before going live), serve the app over **HTTPS** (e.g. local HTTPS or a staging URL with SSL).
