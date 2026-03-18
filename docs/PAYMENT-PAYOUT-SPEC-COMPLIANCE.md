# CayMark — Payment & Payout System: Spec Compliance Checklist

This document maps the **CAYMARK — PAYMENT & PAYOUT SYSTEM** spec to the current codebase.  
**Status:** ✅ Implemented | ⚠️ Partial | ❌ Missing

---

## 1. PURPOSE & OVERVIEW

| Spec | Status | Notes |
|------|--------|--------|
| Buyer checkout workflows | ✅ | Path A (single) and Path B (multi) backend exist |
| Invoice creation and storage | ✅ | `InvoiceService`, PDF generation, dashboard storage |
| Backend transaction logging | ✅ | `Payment` model, admin payment management |
| Payout eligibility after PIN | ✅ | `PickupPinOps`, `PayoutService::createPayoutAfterPickup` |
| Seller payout setup and enforcement | ⚠️ | Enforced on dashboard UI; direct listing create/store not blocked |

---

## 2. PAYMENT PATH A — SINGLE ITEM CHECKOUT

| Spec | Status | Notes |
|------|--------|--------|
| Win notification + invoice + "Make Payment" link | ✅ | `NotificationService`, `AuctionWonNotification`, `route('buyer.payment.checkout-single', $invoice->id)` |
| Payment page: item summary, invoice breakdown, total, card fields | ✅ | `Buyer/payment-checkout-single.blade.php` |
| Success: gateway, buyer/seller notifications, pickup unlock, transaction record | ✅ | `BuyerPaymentOps::processPayment`, notifications, PIN generation |
| Failed: error message, unpaid, failure notification | ✅ | Back validation + notifications |

---

## 3. PAYMENT PATH B — BUYER DASHBOARD MULTI-ITEM CHECKOUT

| Spec | Status | Notes |
|------|--------|--------|
| Dashboard → Auctions tab | ✅ | Buyer dashboard, tab `auctions` |
| Sections: **Active** / **Outstanding** / **Completed** | ⚠️ | Implemented as **Current** / **Won** / **Lost**. Won = unpaid (Pay Now) + paid (Purchase Complete). Naming differs; behaviour similar. |
| Outstanding: item title, amount due, **selectable checkbox** | ❌ | Won section has **Pay Now** per item only. No checkboxes for multi-select. |
| "Make Payment" → multi-item payment page | ⚠️ | Backend: `checkoutMultiple`, `processPayment` with `invoice_ids`. **View `Buyer.payment-checkout-multiple` is missing** (would 404). |
| Combined grand total, one payment, per-invoice records, per-item pickup unlock | ✅ | `BuyerPaymentOps::processPayment` loops invoices, creates payment per item, unlocks pickup per item |

**Gaps:**  
- Add **Outstanding** (unpaid won) with **checkboxes** and one **Make Payment** for selected items.  
- Create **`resources/views/Buyer/payment-checkout-multiple.blade.php`** (or wire to existing single-checkout flow for multiple invoices).

---

## 4. INVOICE REQUIREMENTS

| Spec | Status | Notes |
|------|--------|--------|
| Auto-generated when auction ends | ✅ | `InvoiceService` on auction end |
| Buyer: winning bid, buyer fees, taxes (if any), total | ✅ | Invoice model + PDF |
| Seller: winning bid, seller fees, net payout | ✅ | Calculated in payout flow |
| Read-only, downloadable, stored, per item | ✅ | Dashboard, `buyer.invoice.download`, admin invoice log |
| Shared with buyer/seller/finance | ✅ | Dashboards + admin |

---

## 5. SELLER PAYOUT SETUP REQUIREMENT

| Spec | Status | Notes |
|------|--------|--------|
| Required before creating/submitting listing | ⚠️ | **Dashboard:** "Payout Settings Required" and Submit New Listing hidden until payout set. **Missing:** `listings.create` and `ListingController::store()` do **not** check payout method; seller can open create form directly and submit. |
| Message: "A valid payout method is required before creating a listing." | ⚠️ | Shown on dashboard only; not enforced on server on listing submit. |
| Bank wire only; bank name, account holder, account number, routing/SWIFT, instructions | ✅ | `SellerPayoutMethod`, encrypted, `Seller.payout-method-setup` |
| Encrypted, cannot edit while listings active | ✅ | `SellerPayoutMethod` encryption + `lock()` when active listings |

**Gap:** In `ListingController::create()` and/or `store()`, enforce `PayoutMethodController::sellerHasPayoutMethod($user->id)` and return error with spec message if false.

---

## 6. SELLER NOTIFICATION FLOW

| Spec | Status | Notes |
|------|--------|--------|
| Sale notification (item sold, winning bid, fees, net estimate) | ✅ | e.g. `auctionSold`, NotificationService |
| Payment completed: "Buyer has successfully paid", payout after PIN, 2–5 days | ✅ | Payment success + payout-processing/pending notifications |
| In Seller dashboard Notifications tab | ✅ | Seller notifications |

---

## 7. PAYOUT TRIGGER PROCESS

| Spec | Status | Notes |
|------|--------|--------|
| Payout eligible when: buyer paid + seller confirms pickup with PIN | ✅ | `PostAuctionMessageController::confirmPickupWithPin`, `PickupPinOps::confirm` |
| PIN confirmation: sale marked Pickup Confirmed, listing locked, timestamp, payout-eligible | ✅ | `Listing::confirmPickup`, `PayoutService::createPayoutAfterPickup` |

---

## 8. PAYOUT RECORD CREATION

| Spec | Status | Notes |
|------|--------|--------|
| Seller name, buyer name (optional), item title/ID, gross, fees, net (locked), reference, method, status, timestamp | ✅ | `Payout` model + `createPayoutAfterPickup` |
| Status: Pending, Processing, Sent, On Hold, Paid Successfully | ✅ | Status field + admin updates |
| Payout History in seller dashboard | ✅ | Seller payouts / payout history |
| Seller notification: "payout processing, 2–5 business days" | ✅ | `payout-processing-started` email + `transactionCompletedPayoutPending` |

---

## 9. FINANCE HANDLING PROCESS

| Spec | Status | Notes |
|------|--------|--------|
| Payout records in Finance backend | ✅ | Admin payout management |
| Finance: review, manual wire, update status (Sent, On Hold, Paid Successfully), date sent, notes | ✅ | Admin payout status/notes |
| Finance **cannot** edit: sale amounts, fees, net, seller bank details | ✅ | UI/backend restrict edits to status, date, notes |
| Audit: timestamp, admin user, action | ⚠️ | `AdminActivityLog::log` used for payout status update; ensure all payout actions logged |

---

## 10. ADMIN PAYMENT & PAYOUT LOGS

| Spec | Status | Notes |
|------|--------|--------|
| **Payment log:** Buyer, Seller, Item, Invoice total, Platform fee, Seller payout, Method, Status, Gateway transaction ID, Timestamp | ⚠️ | `admin.payment-management`: has Buyer, Item/Invoice, Amount, Method, Status, Date. **Missing in table:** Seller name, Platform fee retained, Seller payout amount, Gateway transaction ID (data exist on `Payment` model). |
| **Payout log:** Seller, Buyer (opt), Item, Payout amount (locked), Fees, Invoice total, Method, Status, Created, Sent date, Finance notes | ✅ | Payout model + admin payout views (payments + payouts combined in `paymentPayoutLogs`). **View file:** `admin.payment-payout-logs` is returned by controller but **view may be missing** (not in standard admin view list); confirm or create. |
| Search & filtering (date, buyer, seller, status, ID) | ✅ | Admin payment/payout filters (JS client-side + query params where used) |
| **CSV export** | ❌ | Not implemented for payment or payout logs. |

**Gaps:**  
- Add columns (and CSV export) to payment log: seller name, platform fee, seller payout, gateway transaction ID.  
- Ensure `admin.payment-payout-logs` view exists and shows required payout columns.  
- Add CSV export for both payment and payout logs.

---

## 11. SELLER DASHBOARD PAYOUT HISTORY

| Spec | Status | Notes |
|------|--------|--------|
| Item title, sale amount, seller fees, net payout, status, date initiated, date paid | ✅ | Seller payout history / payouts page |
| "Paid Successfully" message: funds in 1–2 days | ✅ | Email/notification on payout completion |

---

## 12. SUMMARY FLOW

| Step | Status |
|------|--------|
| Auction Ends | ✅ |
| Buyer Pays | ✅ (Path A ✅; Path B backend ✅, UI/view incomplete) |
| Invoice Generated (per item) | ✅ |
| Messaging Unlocks | ✅ |
| Seller Sends Pickup Info → Buyer Accepts | ✅ |
| Pickup PIN Issued → Handoff → Seller Enters PIN | ✅ |
| Payout Record Created | ✅ |
| Finance Sends Wire → Payout Completed | ✅ (admin status updates + audit) |

---

## Summary: What’s Done vs Missing

- **Mostly complete:** Path A, invoices, payout setup (UI), PIN → payout, payout record, seller payout history, finance status/notes, audit for payout updates.  
- **Partial / to fix:**  
  - **Path B:** Add Outstanding section with checkboxes + multi-item "Make Payment", and add **`payment-checkout-multiple`** view.  
  - **Payout enforcement:** Block listing create/store when seller has no valid payout method (with spec message).  
  - **Admin payment log:** Show (and export) seller name, platform fee, seller payout, gateway transaction ID.  
  - **Admin payout log:** Confirm `payment-payout-logs` view exists; add CSV export.  
- **Optional (spec alignment):** Rename buyer dashboard sections to Active / Outstanding / Completed if you want exact spec wording.
