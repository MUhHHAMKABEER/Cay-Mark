# CayMark — Post-Auction Messaging & Pickup Coordination: Spec Compliance

This checklist maps the **POST-AUCTION MESSAGING & PICKUP COORDINATION** spec to the codebase.  
**Status:** ✅ Implemented | ⚠️ Partial | ❌ Missing

---

## 1. SYSTEM PURPOSE

| Spec | Status | Notes |
|------|--------|--------|
| Communication only for pickup coordination (no chat/negotiation) | ✅ | Thread is structured (buttons/forms), no free-text chat |
| Prohibited: price negotiation, sharing contact info, off-platform arrangements | ⚠️ | Content filter blocks phone/email/social; **seller has optional "Your Contact Number" in thread** — spec says contact data not allowed |
| Structured, filtered, logged, locked until payment | ✅ | Buttons/forms; ContentFilterService; thread locked until payment |
| Payout impossible without verified pickup confirmation | ✅ | `PickupPinOps::confirm` → `createPayoutAfterPickup`; PIN required |

---

## 2. MESSAGING PORTAL UNLOCK

| Spec | Status | Notes |
|------|--------|--------|
| Thread active only when auction ended + buyer payment successful | ✅ | `PostAuctionThread::isUnlocked()`, `thread-locked` view when not paid |
| Until payment: messaging fully locked, no communication | ✅ | `showThread` returns locked view with "Make Payment" |
| Once payment: thread created automatically, both parties notified | ⚠️ | Thread created/unlocked in `BuyerPaymentOps::unlockPostAuctionThread`; **dashboard notifications to both parties on unlock are TODO** (see PaymentController) |

---

## 3. MESSAGE THREAD DISPLAY

| Spec | Status | Notes |
|------|--------|--------|
| One thread per sold item | ✅ | One `PostAuctionThread` per invoice |
| Thread header: Item title | ✅ | `thread.blade.php`: `{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}` |
| Thread intro: Item thumbnail | ✅ | Listing image in intro panel |
| SOLD BY: Seller name | ✅ | `{{ $seller->name }}` |
| SOLD ON: Auction end date | ✅ | `$invoice->sale_date` (sale date used; spec says "Auction end date") |

---

## 4. COMMUNICATION RESTRICTIONS

| Spec | Status | Notes |
|------|--------|--------|
| System-controlled only; text chat disabled | ✅ | No free-text chat; only buttons/forms |
| Blocked: phone, email, social links, outside contact, off-platform payment/pickup | ✅ | `ContentFilterService`: phone, email, social/URL patterns; address validation |
| Mandatory notice in every thread | ✅ | "Messaging & Pickup Rules" panel with prohibited behaviours |

---

## 5. SELLER — SEND PICKUP DETAILS

| Spec | Status | Notes |
|------|--------|--------|
| Button: [ SEND PICKUP DETAILS ] | ✅ | `post-auction.thread`: "Send Pickup Details" form |
| Structured form (no free typing) | ✅ | Form with date, time, address, notes |
| Fields: Pickup date (calendar), time (time selector), street address (validated), directions/notes (optional) | ✅ | `PostAuctionPickupDetailsRequest`; `ContentFilterService` on notes; address number validation |
| Contact data not allowed in fields | ✅ | Content filter applied to directions_notes |
| Buyer receives formatted notice (DATE, TIME, ADDRESS, NOTES) | ✅ | "Pickup Details" section with DATE/TIME/ADDRESS/NOTES |

---

## 6. BUYER RESPONSE FUNCTIONS

| Spec | Status | Notes |
|------|--------|--------|
| [ ACCEPT PICKUP DETAILS ] | ✅ | `post-auction.accept-pickup`; marks appointment final |
| [ REQUEST DATE / TIME CHANGE ] | ✅ | Modal with date/time; address and notes cannot be changed |
| Seller can approve or counter with new date/time | ✅ | `respondToChangeRequest` (approve/counter); `PickupChangeRequest::approve()`, `counter()` |
| Request–counter cycle until both accept | ✅ | Status flow: pending → approved or countered; buyer can see counter and re-request |

---

## 7. THIRD-PARTY PICKUP AUTHORIZATION

| Spec | Status | Notes |
|------|--------|--------|
| Button: [ AUTHORIZE THIRD-PARTY PICKUP ] | ✅ | In thread for buyer |
| Fields: Authorized person/company name, Pickup type (Tow Company / Individual) | ✅ | `authorized_name`, `pickup_type` (tow_company, individual) |
| Sellers cannot refuse third-party authorizations | ✅ | No refusal flow; recording only |
| At handoff: ID + buyer’s Pickup PIN | ✅ | Spec described; PIN shown to buyer only |

---

## 8. PICKUP PIN SYSTEM

| Spec | Status | Notes |
|------|--------|--------|
| 4-digit PIN generated after payment | ✅ | `Listing::generatePickupPin()` (4 digits); called after payment / when thread opens |
| PIN visible to buyer only: Auction Won dashboard + thread | ⚠️ | **Thread:** PIN shown to buyer ✅. **Dashboard "Auction Won" section:** PIN not currently shown ✅ (spec: "Auction Won" dashboard section) — **missing in Won cards** |
| Seller never sees PIN in advance | ✅ | PIN only on listing; seller sees it only at handoff when buyer provides it |
| At handoff: ID + PIN; seller enters via [ CONFIRM PICKUP ] | ✅ | Seller dashboard and thread: Confirm Pickup with PIN entry |
| Valid PIN → COMPLETED, payout begins, listing archived | ✅ | `confirmPickup` → `createPayoutAfterPickup`; status/archiving per flow |
| Invalid PIN → rejected, payout locked, failures flagged | ✅ | Invalid PIN returns error; payout only after confirm |
| PIN single-use, expires after success; no admin bypass except dispute | ✅ | `confirmPickup` sets `pickup_pin = null` after use; no bypass in code |

---

## 9. SUPPORT ACCESS

| Spec | Status | Notes |
|------|--------|--------|
| [ CONTACT CAYMARK SUPPORT ] in thread | ✅ | "Contact CayMark Support" button (links to buyer support route) |

---

## 10. COMPLETE PICKUP WORKFLOW

| Step | Status |
|------|--------|
| Auction ends → Payment complete → Messaging unlocks | ✅ |
| Seller sends pickup details → Buyer accepts or requests change | ✅ |
| Appointment confirmed → PIN issued | ✅ |
| Handoff → Seller enters PIN → Pickup confirmed → Payout triggered | ✅ |

---

## 11. REQUIRED TECHNICAL MODULES

| Module | Status | Notes |
|--------|--------|--------|
| Button-only restricted messaging portal | ✅ | No chat; buttons/forms only |
| Pickup scheduling change request logic | ✅ | Request + approve/counter flow |
| PIN generation, storage, validation | ✅ | `Listing::generatePickupPin`, `confirmPickup`, `verifyPickupPin` |
| Seller dashboard PIN entry tool | ✅ | Seller dashboard + thread confirm pickup |
| Payment-based messaging lock/unlock | ✅ | Thread unlocked in `BuyerPaymentOps` after payment |
| Real-time dashboard notifications | ⚠️ | Many notifications exist; **thread-unlock notifications to both parties** not sent |
| Payout trigger tied to PIN confirmation | ✅ | `PickupPinOps::confirm` → `createPayoutAfterPickup` |
| Content filtering for form inputs | ✅ | `ContentFilterService` used in pickup details |
| Permanent message/action audit logging | ⚠️ | DB records exist (threads, pickup details, change requests, PIN confirm); **no dedicated audit log table/UI** for "permanent audit" of every action |

---

## 12. SECURITY & ENFORCEMENT

| Spec | Status | Notes |
|------|--------|--------|
| All thread activity permanently logged | ⚠️ | Stored in DB; no explicit audit log table/export |
| Physical transfer requires PIN validation | ✅ | Payout only after `confirmPickup` |
| Payout only after confirmed pickup | ✅ | `createPayoutAfterPickup` after PIN confirm |
| No unattended handoff without ID + PIN | ✅ | Enforced by workflow (PIN required) |
| No admin overrides outside disputes | ✅ | No bypass in code |

---

## Summary: Implemented vs Gaps

**Mostly implemented:**  
Portal unlock/lock, thread display, seller pickup form, buyer accept/change request, approve/counter flow, third-party authorization, PIN generation/validation/single-use, payout on PIN confirm, content filtering, mandatory notice, support button.

**Gaps / partial:**  
1. **PIN in buyer "Auction Won" dashboard** — Spec says PIN visible in "Auction Won" dashboard section; currently only in thread.  
2. **Thread-unlock notifications** — "Dashboard notifications inform both parties" when thread unlocks; not sent (TODO in code).  
3. **Seller optional phone in thread** — Spec: no contact data; thread has "Your Contact Number (Optional)" for seller; consider removing or gating.  
4. **Explicit audit logging** — Spec asks for "permanent message and action audit logging"; data is in DB but there is no dedicated audit log module/UI.  
5. **SOLD ON** — Using invoice `sale_date`; spec says "Auction end date"; align if different in your domain.
