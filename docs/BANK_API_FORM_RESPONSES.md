# Bank API Form – Suggested Responses (Organization Information)

Use these answers to fill in the bank’s Word form. Replace any **[YOUR …]** placeholders with your actual details.

---

## Organization Information

| Field | Suggested response |
|-------|---------------------|
| **Corporate Name** | [YOUR LEGAL ENTITY NAME – e.g. CayMark Ltd. or your registered company name] |
| **Contact Name** | [YOUR NAME] |
| **Title** | [e.g. Director, Technical Director, or Operations Manager] |
| **Phone** | [YOUR PHONE NUMBER] |
| **E-mail** | [YOUR BUSINESS E-MAIL] |

---

## 1. Please provide a brief description of your business.

**Suggested text (copy into the form):**

> CayMark is an online vehicle auction platform operating in The Bahamas. We connect buyers and sellers for timed auctions of vehicles including cars, boats, and motorcycles. Sellers list vehicles with reserve prices; buyers place bids and pay deposits and commissions through the platform. We facilitate the full auction lifecycle from listing through post-auction coordination (e.g. tow arrangements). The business is web-based and all transactions are conducted over the Internet.

*(Adjust if your legal entity name or scope is different.)*

---

## 2. Explain briefly the types of goods and/or services being offered via the Internet.

**Suggested text:**

> CayMark offers an online marketplace and auction service for motor vehicles (cars, boats, motorcycles). Services provided via the Internet include: (1) listing and promotion of vehicles for auction; (2) real-time bidding and auction management; (3) collection of buyer deposits and seller commissions; (4) post-auction coordination and documentation. Payment for deposits, commissions, and related fees is processed online through our platform.

---

## 3. Explain briefly the target market for goods/services.

**Suggested text:**

> Our primary target market is buyers and sellers of vehicles in The Bahamas who wish to transact through a structured online auction. This includes private sellers, dealerships, and individual or commercial buyers seeking cars, boats, or motorcycles. The platform is aimed at users who prefer transparent, time-bound auctions and online payment over traditional offline sales.

---

## 4. Estimated annual Internet sales: USD

**Suggested approach:**  
Enter your best estimate of total annual **Internet-derived revenue** in USD (e.g. commissions, fees, or other sales that flow through the platform).  
Example placeholder if you prefer to keep it generic: **_[YOUR ESTIMATE]** USD** (e.g. 50,000 or 100,000 – replace with your figure).

---

## 5. Average ticket size for Internet sales: USD

**Suggested approach:**  
Enter the typical transaction value in USD (e.g. average deposit + commission per auction, or average order value).  
Example: **_[YOUR ESTIMATE]** USD** (e.g. 500–2,000 depending on your typical auction size – replace with your figure).

---

## 6. Approximate number of annual internet transactions: USD

**Note:** The form label says “USD” but the question asks for “number of … transactions.” Interpret as **number of transactions per year** (e.g. number of paid auctions or payment transactions). If the bank expects a dollar amount, they will clarify.

**Suggested response:**  
**_[YOUR ESTIMATE]** transactions per year** (e.g. 100, 500, 1,000 – replace with your estimate).

---

## Summary of what you still need to provide

- **Corporate Name** – Your registered/legal company name.  
- **Contact Name, Title, Phone, E-mail** – Person the bank can contact (often technical or operations).  
- **Questions 4, 5, 6** – Your own estimates for annual Internet sales (USD), average ticket (USD), and number of annual Internet transactions. Use realistic figures; the bank may use them for risk/volume assessment.

---

## General Requirements – Questions 9–13

### 9. Provide the URL from which the transactions will be submitted and the responses received:

**Suggested response:**

> **https://[YOUR_PRODUCTION_DOMAIN]/buyer/payment/process**

Replace `[YOUR_PRODUCTION_DOMAIN]` with your live site domain (e.g. `caymark.com` or `www.caymark.com`). In your CayMark app, payment form submissions are sent to this endpoint via POST; the server processes the payment and returns the response in the same request. Use **HTTPS** only for production.

---

### 10. Confirm the server is for TLS 1.2 transactions only

**Suggested response:** **Yes**

Ensure your production web server (and any load balancer) is configured to use **TLS 1.2** (or 1.3) only and that older protocols (SSLv3, TLS 1.0, TLS 1.1) are disabled. Your hosting or DevOps team can verify this.

---

### 11. Does the website allow cardholders to enter a three-digit card security code (CVV2/CVC2) and four-digit AMEX CID code?

**Suggested response:** **Yes**

CayMark’s checkout and registration flows collect a card security code:
- **CVV2/CVC2 (3-digit):** Collected on the payment checkout page (`payment-checkout-single.blade.php` / multiple) and in registration and tow-provider signup. The field is labeled “CVC” and accepts the 3-digit code.
- **AMEX CID (4-digit):** The same input field can accept 4 digits for American Express cards. If you want to be explicit for AMEX, you can add a short note or label (e.g. “3 digits, or 4 for Amex”) on the form; functionally the field already supports it.

---

### 12. Please confirm if unused services on the web server (e.g. FTP, Telnet, etc.) have been disabled or removed.

**Suggested response:** **Yes**

Confirm with your hosting provider or server administrator that unnecessary services (FTP, Telnet, and similar) are disabled or removed on the server(s) that host the application. This is standard security practice for production.

---

### 13. Does the web site support tokenization with 3-D Secure services (Visa Secure, MasterCard, Discover and AMEX Secure Code)?

**Suggested response:** **No** (at current stage)

CayMark currently uses a **demo/sandbox payment gateway** and does **not** yet implement:
- **Tokenization** (storing or using tokenized card data instead of raw PAN), or  
- **3-D Secure** (Visa Secure, Mastercard Identity Check, Discover ProtectBuy, AMEX SafeKey).

When you integrate the bank’s (or a gateway’s) live API, ask whether they support tokenization and 3-D Secure. If they do, you can add support in the app and then answer **Yes** on a future form or renewal. Some banks require or strongly prefer 3-D Secure for card-not-present transactions.

---

## Summary – Questions 9–13

| Question | Suggested answer |
|----------|-------------------|
| **9. Transaction URL** | `https://[YOUR_PRODUCTION_DOMAIN]/buyer/payment/process` |
| **10. TLS 1.2 only** | **Yes** (ensure server is configured accordingly) |
| **11. CVV2/CVC2 and AMEX CID** | **Yes** |
| **12. Unused services disabled** | **Yes** (confirm with host) |
| **13. Tokenization + 3-D Secure** | **No** (implement when gateway supports it) |

If you have more sections of the form, share them and the same style of answers can be added.
