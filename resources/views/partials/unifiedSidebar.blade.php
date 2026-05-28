@php
    $user = Auth::user();
    $role = $user->role ?? '';          // null → '' = guest / incomplete registration
    $currentRoute = request()->route()?->getName() ?? '';
    $currentTab   = request()->query('tab', '');

    $menuItems = [];

    if ($role === 'admin') {
        $menuItems = [
            // ── OVERVIEW ──────────────────────────────────────────────────────
            ['section' => 'OVERVIEW', 'route' => 'admin.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard', 'match_routes' => ['admin.dashboard.analytics'], 'prefix_match' => false],
            // ── USERS ─────────────────────────────────────────────────────────
            ['section' => 'USERS', 'route' => 'admin.users', 'icon' => 'people', 'label' => 'User Management', 'match_routes' => ['admin.users.view', 'admin.users.update', 'admin.users.reset-password', 'admin.users.toggle-status'], 'prefix_match' => false],
            ['route' => 'admin.memberships', 'icon' => 'card_membership', 'label' => 'Memberships'],
            // ── LISTINGS & AUCTIONS ───────────────────────────────────────────
            ['section' => 'LISTINGS & AUCTIONS', 'route' => 'admin.listing-review', 'icon' => 'fact_check', 'label' => 'Listing Review', 'match_routes' => ['admin.listings.approval-detail'], 'prefix_match' => false],
            ['route' => 'admin.active-listings', 'icon' => 'directions_car', 'label' => 'Active Auctions'],
            ['route' => 'admin.boosts-addons', 'icon' => 'rocket_launch', 'label' => 'Boosts & Add-ons'],
            // ── FINANCE ───────────────────────────────────────────────────────
            ['section' => 'FINANCE', 'route' => 'admin.payments', 'icon' => 'account_balance_wallet', 'label' => 'Sales / Payouts', 'match_routes' => ['admin.pending-payments', 'admin.payouts', 'admin.payment-payout-logs', 'admin.invoice-log', 'admin.unpaid-auctions', 'admin.buyer-defaults', 'admin.second-chance-purchases'], 'prefix_match' => false],
            ['route' => 'admin.security-deposits', 'icon' => 'security', 'label' => 'Security Deposits', 'prefix_match' => false],
            ['route' => 'admin.pending-payments', 'icon' => 'schedule', 'label' => 'Pending Payments'],
            // ── OPERATIONS ────────────────────────────────────────────────────
            ['section' => 'OPERATIONS', 'route' => 'admin.disputes', 'icon' => 'gavel', 'label' => 'Disputes Center', 'match_routes' => ['admin.disputes.view', 'admin.disputes.update-status'], 'prefix_match' => false],
            ['route' => 'admin.messaging.flags.index', 'icon' => 'flag', 'label' => 'Messaging Flags', 'match_routes' => ['admin.messaging.flags.show', 'admin.messaging.flags.unflag'], 'prefix_match' => false],
            ['route' => 'admin.support-tickets', 'icon' => 'support_agent', 'label' => 'Support Tickets'],
            // ── SYSTEM ────────────────────────────────────────────────────────
            ['section' => 'SYSTEM', 'route' => 'admin.notifications', 'icon' => 'notifications', 'label' => 'Notifications'],
            ['route' => 'admin.email-templates', 'icon' => 'mail', 'label' => 'Email Templates', 'match_routes' => ['admin.email-templates.edit', 'admin.email-templates.preview', 'admin.email-templates.update', 'admin.email-templates.restore'], 'prefix_match' => false],
            ['route' => 'admin.reports-analytics', 'icon' => 'bar_chart', 'label' => 'Reports & Analytics', 'match_routes' => ['admin.user-activity-insights', 'admin.revenue-tracking', 'admin.revenue-tracking.export'], 'prefix_match' => false],
        ];
        $roleLabel = 'Administrator';
        $roleBadge = 'ADMIN';
        $dashboardRoute = 'admin.dashboard';
    } elseif ($role === 'seller') {
        $menuItems = [
            ['route' => 'welcome', 'icon' => 'home', 'label' => 'Home'],
            ['route' => 'seller.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['route' => 'seller.account', 'icon' => 'person', 'label' => 'Account settings'],
            ['route' => 'seller.listings.create', 'icon' => 'add_box', 'label' => 'Submission', 'match_routes' => ['seller.listings.edit', 'seller.listings.update', 'seller.listings.success', 'seller.listings.store'], 'prefix_match' => false],
            ['route' => 'seller.auctions', 'icon' => 'gavel', 'label' => 'Auctions', 'match_routes' => ['seller.listings.show']],
            ['route' => 'seller.notifications', 'icon' => 'notifications', 'label' => 'Notifications'],
            ['route' => 'messaging.index', 'icon' => 'mail', 'label' => 'Messaging Center', 'match_routes' => ['messaging.thread.show', 'seller.chat', 'seller.chat.show', 'seller.chat.message']],
            ['route' => 'seller.support', 'icon' => 'support_agent', 'label' => 'Customer Support'],
        ];
        $roleLabel = $user->business_license_path ? 'Business Seller' : 'Individual Seller';
        $roleBadge = 'SELLER';
        $dashboardRoute = 'seller.dashboard';
    } elseif ($role === '') {
        // Guest / incomplete registration — only 4 items, no registration portal in sidebar
        $menuItems = [
            ['route' => 'dashboard.default', 'tab' => '',              'icon' => 'dashboard',     'label' => 'Dashboard'],
            ['route' => 'dashboard.default', 'tab' => 'account',       'icon' => 'person',        'label' => 'Account Settings'],
            ['route' => 'dashboard.default', 'tab' => 'notifications', 'icon' => 'notifications', 'label' => 'Notifications'],
            ['route' => 'dashboard.default', 'tab' => 'support',       'icon' => 'support_agent', 'label' => 'Customer Support'],
        ];
        $roleLabel = 'Guest';
        $roleBadge = 'GUEST';
        $dashboardRoute = 'dashboard.default';
    } else {
        $menuItems = [
            ['route' => 'welcome', 'icon' => 'home', 'label' => 'Home'],
            ['route' => 'buyer.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['route' => 'buyer.user', 'icon' => 'person', 'label' => 'Account settings', 'match_routes' => ['buyer.profile']],
            ['route' => 'buyer.auctions', 'icon' => 'gavel', 'label' => 'Auctions'],
            ['route' => 'buyer.deposit-withdrawal', 'icon' => 'account_balance_wallet', 'label' => 'Security Deposit', 'match_routes' => ['buyer.deposit.add', 'buyer.deposit-withdrawal.request'], 'prefix_match' => false],
            ['route' => 'buyer.saved-items', 'icon' => 'bookmark', 'label' => 'Saved Items', 'match_routes' => ['buyer.watchlist', 'watchlist.index']],
            ['route' => 'buyer.notifications', 'icon' => 'notifications', 'label' => 'Notifications'],
            ['route' => 'messaging.index', 'icon' => 'mail', 'label' => 'Messaging Center', 'match_routes' => ['messaging.thread.show', 'buyer.messaging-center', 'buyer.messages']],
            ['route' => 'buyer.customer-support', 'icon' => 'support_agent', 'label' => 'Customer Support'],
        ];
        $roleLabel = 'Buyer';
        $roleBadge = 'BUYER';
        $dashboardRoute = 'buyer.dashboard';
    }

    if (!isset($roleBadge)) {
        $roleBadge = $role !== '' ? strtoupper($role) : 'GUEST';
    }
    $roleBadgeBg = match($roleBadge) {
        'ADMIN'  => 'background:#7c3aed;',
        'SELLER' => 'background:#0a1930;',
        'BUYER'  => 'background:#0a1930;',
        'GUEST'  => 'background:#64748b;',
        default  => 'background:#0a1930;',
    };

    // Default messaging routes to collapsed if user has no saved preference yet.
    $defaultCollapsed = request()->routeIs('messaging.index', 'messaging.thread.show');
@endphp

<style>
    :root {
        --cm-sidebar-width: 240px;
        --cm-sidebar-collapsed: 70px;
        --cm-navy: #0a1930;
        --cm-navy-2: #1a365d;
        --cm-blue: #2563eb;
        --cm-blue-light: #eef2ff;
        --cm-text: #1f2937;
        --cm-text-muted: #6b7280;
        --cm-icon: #64748b;
        --cm-border: #e5e7eb;
        --cm-border-soft: #f1f5f9;
        --cm-hover-bg: #eef2f7;
        --cm-danger: #dc2626;
        --cm-danger-bg: #fef2f2;
        --cm-radius: 10px;
        --cm-ease: cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Layout shell --------------------------------------------------------- */
    body { transition: padding-left 0.3s var(--cm-ease); }

    .unified-sidebar {
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        width: var(--cm-sidebar-width);
        background: #ffffff;
        color: var(--cm-text);
        display: flex;
        flex-direction: column;
        z-index: 100;
        box-shadow: 2px 0 16px rgba(15, 23, 42, 0.06);
        border-right: 1px solid var(--cm-border-soft);
        transition: width 0.3s var(--cm-ease), transform 0.3s var(--cm-ease);
        overflow: visible;
    }

    body.sidebar-collapsed .unified-sidebar { width: var(--cm-sidebar-collapsed); }

    /* Collapse toggle ------------------------------------------------------ */
    .sidebar-collapse-toggle {
        position: absolute;
        top: 28px;
        right: -14px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #ffffff;
        border: 1px solid var(--cm-border);
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.12);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: var(--cm-icon);
        z-index: 110;
        transition: background 0.15s var(--cm-ease), color 0.15s var(--cm-ease), box-shadow 0.15s var(--cm-ease);
        padding: 0;
    }
    .sidebar-collapse-toggle:hover { background: var(--cm-blue-light); color: var(--cm-blue); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2); }
    .sidebar-collapse-toggle .material-icons-round { font-size: 18px; transition: transform 0.3s var(--cm-ease); }
    body.sidebar-collapsed .sidebar-collapse-toggle .material-icons-round { transform: rotate(180deg); }

    /* Logo ----------------------------------------------------------------- */
    .unified-sidebar .logo {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.75rem 1.25rem 1.5rem;
        border-bottom: 1px solid var(--cm-border-soft);
        flex-shrink: 0;
        text-decoration: none;
        background: #ffffff;
    }
    .unified-sidebar .logo img.logo-full { height: 44px; width: auto; max-width: 100%; object-fit: contain; transition: opacity 0.2s var(--cm-ease); }
    .unified-sidebar .logo img.logo-icon { height: 32px; width: 32px; object-fit: contain; display: none; }

    body.sidebar-collapsed .unified-sidebar .logo { padding: 1.25rem 0.5rem 1.1rem; }
    body.sidebar-collapsed .unified-sidebar .logo img.logo-full { display: none; }
    body.sidebar-collapsed .unified-sidebar .logo img.logo-icon { display: block; }

    /* User profile --------------------------------------------------------- */
    .unified-sidebar .user-profile {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.1rem;
        margin: 0;
        border-bottom: 1px solid var(--cm-border-soft);
        background: #ffffff;
        text-decoration: none;
        color: inherit;
        flex-shrink: 0;
        transition: background 0.15s var(--cm-ease);
    }
    .unified-sidebar .user-profile:hover { background: var(--cm-hover-bg); text-decoration: none; }

    .unified-sidebar .user-profile-avatar,
    .unified-sidebar .user-profile-initials {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1a365d 0%, #2563eb 100%);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
    }

    .unified-sidebar .user-profile-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.3rem;
        min-width: 0;
        flex: 1;
        opacity: 1;
        transition: opacity 0.15s var(--cm-ease);
    }

    .unified-sidebar .user-name {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    .unified-sidebar .user-role {
        display: inline-flex;
        align-items: center;
        font-size: 0.625rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #ffffff;
        background: var(--cm-navy);
        padding: 0.15rem 0.5rem;
        border-radius: 4px;
        line-height: 1.4;
        border: none;
        box-shadow: none;
    }

    body.sidebar-collapsed .unified-sidebar .user-profile { justify-content: center; padding: 1rem 0.25rem; }
    body.sidebar-collapsed .unified-sidebar .user-profile-meta { display: none; }

    /* ── Business Seller profile card ─────────────────────────────── */
    .unified-sidebar .user-profile--business {
        background: #063466;
        border-bottom: 2px solid rgba(255,255,255,0.08);
        padding: 1rem 1.1rem;
    }
    .unified-sidebar .user-profile--business:hover {
        background: #074585;
    }
    .biz-profile-card {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        width: 100%;
    }
    .biz-avatar-wrap {
        position: relative;
        flex-shrink: 0;
    }
    .biz-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(255,255,255,0.12);
        border: 1.5px solid rgba(255,255,255,0.22);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        font-weight: 700;
        color: #ffffff;
        letter-spacing: 0.04em;
    }
    .biz-verified-dot {
        position: absolute;
        bottom: -4px;
        right: -4px;
        width: 15px;
        height: 15px;
        background: #22c55e;
        border-radius: 50%;
        border: 2px solid #063466;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .biz-name {
        font-size: 0.8125rem;
        font-weight: 700;
        color: #ffffff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.25;
    }
    .biz-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.08em;
        color: rgba(255,255,255,0.85);
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.18);
        padding: 2px 7px 2px 5px;
        border-radius: 999px;
        white-space: nowrap;
        margin-top: 4px;
    }
    body.sidebar-collapsed .unified-sidebar .user-profile--business { justify-content: center; padding: 1rem 0.25rem; }

    /* ── Individual Seller profile card ───────────────────────────── */
    .unified-sidebar .user-profile--casual {
        background: #ffffff;
        border-bottom: 1px solid var(--cm-border-soft);
        padding: 1rem 1.1rem;
        position: relative;
    }
    .unified-sidebar .user-profile--casual::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: #063466;
        border-radius: 0 2px 2px 0;
    }
    .unified-sidebar .user-profile--casual { padding-left: calc(1.1rem + 3px); }
    .unified-sidebar .user-profile--casual:hover { background: var(--cm-hover-bg); }
    .casual-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.08em;
        color: #063466;
        background: #e8eef6;
        border: 1px solid #c3d4e8;
        padding: 2px 7px 2px 5px;
        border-radius: 999px;
        white-space: nowrap;
        margin-top: 3px;
    }

    /* Navigation — scroll only inner wrapper so flyout tooltips are not clipped */
    .unified-sidebar nav {
        flex: 1;
        min-height: 0;
        display: flex;
        flex-direction: column;
        overflow: visible;
        padding: 0.85rem 0;
    }
    .unified-sidebar .sidebar-nav-scroll {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
    }
    .unified-sidebar .sidebar-nav-scroll::-webkit-scrollbar { width: 6px; }
    .unified-sidebar .sidebar-nav-scroll::-webkit-scrollbar-track { background: transparent; }
    .unified-sidebar .sidebar-nav-scroll::-webkit-scrollbar-thumb { background: rgba(15, 23, 42, 0.15); border-radius: 10px; }
    .unified-sidebar .sidebar-nav-scroll::-webkit-scrollbar-thumb:hover { background: rgba(15, 23, 42, 0.28); }

    .unified-sidebar nav ul { list-style: none; padding: 0 0.65rem; margin: 0; }
    .unified-sidebar nav li { margin: 0 0 0.35rem; position: relative; }

    .unified-sidebar nav a {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 0.7rem 0.85rem;
        border-radius: var(--cm-radius);
        color: #4b5563;
        font-weight: 500;
        font-size: 0.9rem;
        text-decoration: none;
        transition: background 0.15s var(--cm-ease), color 0.15s var(--cm-ease);
    }
    .unified-sidebar nav a:hover { background: var(--cm-hover-bg); color: #0f172a; text-decoration: none; }
    .unified-sidebar nav a:hover .nav-icon-wrap .material-icons-round { color: #0f172a; }

    .unified-sidebar nav a.active {
        background: linear-gradient(135deg, var(--cm-navy) 0%, var(--cm-navy-2) 100%);
        color: #ffffff;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(10, 25, 48, 0.18);
    }
    .unified-sidebar nav a.active .nav-icon-wrap .material-icons-round { color: #ffffff; }

    .unified-sidebar nav .nav-icon-wrap {
        position: relative;
        flex-shrink: 0;
        width: 24px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .unified-sidebar nav .nav-icon-wrap .material-icons-round {
        font-size: 1.35rem;
        color: var(--cm-icon);
        transition: color 0.15s var(--cm-ease);
        line-height: 1;
    }
    .unified-sidebar nav .nav-label {
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-family: inherit;
        font-size: inherit;
    }

    body.sidebar-collapsed .unified-sidebar nav ul { padding: 0 0.5rem; }
    body.sidebar-collapsed .unified-sidebar nav a { justify-content: center; padding: 0.7rem 0.3rem; gap: 0; }
    body.sidebar-collapsed .unified-sidebar nav .nav-label { display: none; }

    /* Section group labels ------------------------------------------------- */
    .unified-sidebar .sidebar-section-label {
        margin: 20px 0 6px;
        padding: 0 0.85rem;
        list-style: none;
        pointer-events: none;
        user-select: none;
    }
    .unified-sidebar .sidebar-section-label:first-child {
        margin-top: 4px; /* first label sits flush with nav top padding */
    }
    .unified-sidebar .sidebar-section-label span {
        display: block;
        font-size: 10px;
        font-weight: 700;
        color: #9aa0a8;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        line-height: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: clip;
    }

    /* Collapsed: hide label text, show a thin divider line instead */
    body.sidebar-collapsed .unified-sidebar .sidebar-section-label {
        margin: 10px 0.5rem 6px;
        padding: 0;
        border-top: 1px solid var(--cm-border-soft);
    }
    body.sidebar-collapsed .unified-sidebar .sidebar-section-label:first-child {
        display: none; /* no divider needed above the very first item */
    }
    body.sidebar-collapsed .unified-sidebar .sidebar-section-label span {
        display: none;
    }

    /* Notification badge --------------------------------------------------- */
    .unified-sidebar .sidebar-notification-badge {
        position: absolute;
        top: -4px;
        right: -6px;
        min-width: 16px;
        height: 16px;
        padding: 0 4px;
        background: #ef4444;
        color: #ffffff;
        font-size: 0.6rem;
        font-weight: 700;
        line-height: 1;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #ffffff;
        box-shadow: 0 1px 3px rgba(239, 68, 68, 0.35);
        pointer-events: none;
    }
    .unified-sidebar nav a.active .sidebar-notification-badge { border-color: var(--cm-navy); }
    .unified-sidebar .sidebar-notification-badge[style*="display: none"] { display: none !important; }

    /* Fixed flyout label (collapsed sidebar only) — positioned via JS */
    .sidebar-flyout-tooltip {
        position: fixed;
        left: 0;
        top: 0;
        z-index: 10050;
        max-width: min(280px, calc(100vw - 24px));
        padding: 7px 12px;
        font-size: 0.8125rem;
        font-weight: 500;
        line-height: 1.35;
        color: #fafafa;
        background: #2a2a2e;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 8px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(0, 0, 0, 0.2);
        pointer-events: none;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.12s ease, visibility 0.12s ease;
    }
    .sidebar-flyout-tooltip.is-visible {
        opacity: 1;
        visibility: visible;
    }
    .sidebar-flyout-tooltip::before {
        content: '';
        position: absolute;
        right: 100%;
        top: 50%;
        transform: translateY(-50%);
        margin-right: -1px;
        border: 6px solid transparent;
        border-right-color: #2a2a2e;
    }
    .sidebar-flyout-tooltip.sidebar-flyout-tooltip--caret-right::before {
        right: auto;
        left: 100%;
        margin-right: 0;
        margin-left: -1px;
        border-right-color: transparent;
        border-left-color: #2a2a2e;
    }

    /* Logout --------------------------------------------------------------- */
    .unified-sidebar .logout-section {
        margin-top: auto;
        padding: 0.75rem 0.65rem 1rem;
        border-top: 1px solid var(--cm-border-soft);
        background: #ffffff;
        flex-shrink: 0;
    }
    .unified-sidebar .logout-section form { margin: 0; }

    .unified-sidebar .logout-btn {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 0.7rem 0.85rem;
        border-radius: var(--cm-radius);
        color: var(--cm-danger);
        font-weight: 600;
        font-size: 0.9rem;
        background: none;
        border: none;
        cursor: pointer;
        width: 100%;
        text-align: left;
        transition: background 0.15s var(--cm-ease), color 0.15s var(--cm-ease);
    }
    .unified-sidebar .logout-btn:hover { background: var(--cm-danger-bg); color: var(--cm-danger); }
    .unified-sidebar .logout-btn .material-icons-round {
        font-size: 1.35rem;
        color: var(--cm-danger);
        flex-shrink: 0;
        width: 24px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }
    .unified-sidebar .logout-btn .logout-label { flex: 1; white-space: nowrap; }

    body.sidebar-collapsed .unified-sidebar .logout-btn { justify-content: center; padding: 0.7rem 0.3rem; gap: 0; }
    body.sidebar-collapsed .unified-sidebar .logout-btn .logout-label { display: none; }

    /* Mobile drawer -------------------------------------------------------- */
    .sidebar-mobile-toggle {
        display: none;
        position: fixed;
        top: 12px;
        left: 12px;
        z-index: 95;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #ffffff;
        border: 1px solid var(--cm-border);
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.12);
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: var(--cm-text);
        padding: 0;
    }
    .sidebar-mobile-toggle .material-icons-round { font-size: 22px; }

    .sidebar-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        z-index: 99;
        opacity: 0;
        transition: opacity 0.25s var(--cm-ease);
    }
    body.sidebar-mobile-open .sidebar-backdrop { display: block; opacity: 1; }

    @media (max-width: 768px) {
        .sidebar-mobile-toggle { display: inline-flex; }
        .sidebar-collapse-toggle { display: none; }

        .unified-sidebar {
            width: var(--cm-sidebar-width) !important;
            transform: translateX(-100%);
            box-shadow: 0 0 30px rgba(15, 23, 42, 0.2);
        }
        body.sidebar-mobile-open .unified-sidebar { transform: translateX(0); }

        body.sidebar-collapsed .unified-sidebar { width: var(--cm-sidebar-width) !important; }
        body.sidebar-collapsed .unified-sidebar .logo img.logo-full { display: block; }
        body.sidebar-collapsed .unified-sidebar .logo img.logo-icon { display: none; }
        body.sidebar-collapsed .unified-sidebar .user-profile-meta { display: flex; }
        body.sidebar-collapsed .unified-sidebar nav .nav-label { display: block; }
        body.sidebar-collapsed .unified-sidebar .logout-btn .logout-label { display: block; }
        body.sidebar-collapsed .unified-sidebar nav a { justify-content: flex-start; padding: 0.7rem 0.85rem; gap: 0.85rem; }
        body.sidebar-collapsed .unified-sidebar .logout-btn { justify-content: flex-start; padding: 0.7rem 0.85rem; gap: 0.85rem; }
        body.sidebar-collapsed .unified-sidebar .user-profile { justify-content: flex-start; padding: 1rem 1.1rem; }
    }
</style>

<button type="button" class="sidebar-mobile-toggle" id="sidebarMobileToggle" aria-label="Open menu">
    <span class="material-icons-round">menu</span>
</button>

<div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

<aside class="unified-sidebar" id="unifiedSidebar">
    <button type="button" class="sidebar-collapse-toggle" id="sidebarCollapseToggle" aria-label="Collapse sidebar" title="Collapse sidebar">
        <span class="material-icons-round">chevron_left</span>
    </button>

    <a href="{{ $role === 'admin' ? route('admin.dashboard') : route('welcome') }}" class="logo" aria-label="CayMark home">
        <img class="logo-full" src="{{ asset(config('logos.sidebar', 'Logos/Caymark Logo.png')) }}" alt="CayMark" />
        <img class="logo-icon" src="{{ asset(config('logos.sidebar', 'Logos/Caymark Logo.png')) }}" alt="CayMark" />
    </a>

    @php
        $displayName = trim((string) ($user->name ?? ''));
        $profileRoute = match($role) {
            'seller' => 'seller.account',
            'buyer'  => 'buyer.user',
            ''       => null,       // guest — use tab URL
            default  => 'profile.edit',  // admin, etc.
        };
        $profileUrl = $profileRoute ? route($profileRoute) : route('dashboard.default').'?tab=account';
    @endphp
    @if($role === 'seller' && $user->business_license_path)
    {{-- ═══ BUSINESS SELLER ═══ --}}
    @php
        $bpParts    = preg_split('/\s+/u', trim($user->name ?? ''), -1, PREG_SPLIT_NO_EMPTY);
        $bpInitials = count($bpParts) >= 2
            ? mb_strtoupper(mb_substr($bpParts[0],0,1) . mb_substr($bpParts[count($bpParts)-1],0,1))
            : (count($bpParts) === 1 ? mb_strtoupper(mb_substr($bpParts[0],0,2)) : '?');
    @endphp
    <a href="{{ $profileUrl }}" class="user-profile user-profile--business" title="{{ $displayName ?: 'Account' }} · Business Seller">
        <div class="biz-profile-card">
            <div class="biz-avatar-wrap">
                <div class="biz-avatar">{{ $bpInitials }}</div>
                <span class="biz-verified-dot">
                    <span class="material-icons-round" style="font-size:8px;color:#ffffff;line-height:1">check</span>
                </span>
            </div>
            <div class="user-profile-meta" style="gap:0">
                <span class="biz-name">{{ Str::ucfirst($user->name) }}</span>
                <span class="biz-badge">
                    <span class="material-icons-round" style="font-size:9px;line-height:1">verified</span>
                    BUSINESS SELLER
                </span>
            </div>
        </div>
    </a>

    @elseif($role === 'seller')
    {{-- ═══ INDIVIDUAL / CASUAL SELLER ═══ --}}
    <a href="{{ $profileUrl }}" class="user-profile user-profile--casual" title="{{ $displayName ?: 'Account' }} · Individual Seller">
        <x-ui.avatar :user="$user" size="sm" class="user-profile-avatar" />
        <div class="user-profile-meta">
            <span class="user-name">{{ Str::ucfirst($user->name) }}</span>
            <span class="casual-badge">
                <span class="material-icons-round" style="font-size:9px;line-height:1">storefront</span>
                INDIVIDUAL SELLER
            </span>
        </div>
    </a>

    @else
    {{-- ═══ DEFAULT (buyer, admin, guest) ═══ --}}
    <a href="{{ $profileUrl }}" class="user-profile" data-sidebar-flyout="Account settings" title="{{ $displayName ?: 'Account' }} · {{ $roleLabel }}">
        <x-ui.avatar :user="$user" size="sm" class="user-profile-avatar" />
        <div class="user-profile-meta">
            <span class="user-name">{{ Str::ucfirst($user->name) }}</span>
            <span class="user-role" style="{{ $roleBadgeBg }}">{{ $roleBadge }}</span>
        </div>
    </a>
    @endif

    <nav>
        <div class="sidebar-nav-scroll">
        <ul>
            @foreach($menuItems as $item)
                @if(isset($item['section']))
                    <li class="sidebar-section-label" aria-hidden="true">
                        <span>{{ $item['section'] }}</span>
                    </li>
                @endif
                @php
                    $isActive = false;
                    if ($item['route'] !== '#') {
                        $routeName   = $item['route'];
                        $matchRoutes = $item['match_routes'] ?? [];

                        // Tab-based items (used by guest/basic role)
                        if (array_key_exists('tab', $item)) {
                            $itemTab  = (string) $item['tab'];
                            $isActive = ($currentRoute === $routeName && $currentTab === $itemTab);
                        } else {
                            if ($currentRoute !== '' && count($matchRoutes) > 0 && in_array($currentRoute, $matchRoutes, true)) {
                                $isActive = true;
                            }

                            if (!$isActive) {
                                $isActive = ($currentRoute === $routeName);
                                if (!$isActive && $currentRoute !== '' && ($item['prefix_match'] ?? true)) {
                                    $routeParts   = explode('.', $routeName);
                                    $currentParts = explode('.', $currentRoute);
                                    if (count($routeParts) >= 2 && count($currentParts) >= 2) {
                                        $isActive = $routeParts[0] === $currentParts[0] &&
                                                    $routeParts[1] === $currentParts[1];
                                    }
                                }
                            }
                        }
                    }

                    $url = '#';
                    if ($item['route'] !== '#') {
                        $url = route($item['route']);
                        // Append tab param for guest tab-based items
                        if (array_key_exists('tab', $item) && $item['tab'] !== '') {
                            $url .= '?tab=' . $item['tab'];
                        }
                    }
                    $tourId = ($role ?: 'guest') . '-' . \Illuminate\Support\Str::slug($item['label']);
                    $unreadCount = 0;
                    if ($item['icon'] === 'notifications' && isset($user)) {
                        $unreadCount = $user->unreadNotifications()->count();
                    }
                @endphp
                <li>
                    <a href="{{ $url }}"
                       class="{{ $isActive ? 'active' : '' }}"
                       data-tour-id="{{ $tourId }}"
                       data-sidebar-flyout="{{ $item['label'] }}"
                       @if($item['route'] === '#') onclick="return false;" @endif>
                        <span class="nav-icon-wrap">
                            <span class="material-icons-round">{{ $item['icon'] }}</span>
                            @if($item['icon'] === 'notifications' && isset($user))
                                <span class="sidebar-notification-badge" style="display: {{ $unreadCount > 0 ? 'inline-flex' : 'none' }};">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                            @endif
                        </span>
                        <span class="nav-label">{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        </div>
    </nav>

    <div class="logout-section">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn" data-tour-id="{{ $role }}-logout" data-sidebar-flyout="Log out" onclick="event.preventDefault(); this.closest('form').submit();">
                <span class="material-icons-round">logout</span>
                <span class="logout-label">Log out</span>
            </button>
        </form>
    </div>

    <div id="sidebarFlyoutTooltip" class="sidebar-flyout-tooltip" role="tooltip" hidden></div>
</aside>

<script>
    (function () {
        var STORAGE_KEY = 'caymark.sidebar.collapsed';
        var defaultCollapsed = @json((bool) $defaultCollapsed);
        var stored = null;
        try { stored = window.localStorage.getItem(STORAGE_KEY); } catch (e) { stored = null; }

        var collapsed;
        if (stored === '1') {
            collapsed = true;
        } else if (stored === '0') {
            collapsed = false;
        } else {
            collapsed = defaultCollapsed;
        }

        function applyCollapsed(state) {
            document.body.classList.toggle('sidebar-collapsed', !!state);
            var toggle = document.getElementById('sidebarCollapseToggle');
            if (toggle) {
                toggle.setAttribute('aria-label', state ? 'Expand sidebar' : 'Collapse sidebar');
                toggle.setAttribute('title', state ? 'Expand sidebar' : 'Collapse sidebar');
            }
        }

        applyCollapsed(collapsed);

        document.addEventListener('DOMContentLoaded', function () {
            var flyout = document.getElementById('sidebarFlyoutTooltip');
            var navScroll = document.querySelector('.unified-sidebar .sidebar-nav-scroll');
            var hideFlyoutTimer = null;

            function hideFlyout() {
                if (hideFlyoutTimer) {
                    clearTimeout(hideFlyoutTimer);
                    hideFlyoutTimer = null;
                }
                if (!flyout) return;
                flyout.classList.remove('is-visible');
                flyout.classList.remove('sidebar-flyout-tooltip--caret-right');
                flyout.hidden = true;
                flyout.textContent = '';
                flyout.style.left = '';
                flyout.style.top = '';
            }

            function shouldShowFlyout() {
                return window.innerWidth > 768
                    && document.body.classList.contains('sidebar-collapsed')
                    && !document.body.classList.contains('sidebar-mobile-open');
            }

            function anchorRectFor(el) {
                var wrap = el.querySelector('.nav-icon-wrap');
                if (wrap) return wrap.getBoundingClientRect();
                var avatar = el.querySelector('.user-profile-avatar, .user-profile-initials');
                if (avatar) return avatar.getBoundingClientRect();
                var mi = el.querySelector('.material-icons-round');
                if (mi) return mi.getBoundingClientRect();
                return el.getBoundingClientRect();
            }

            function showFlyoutFor(el) {
                if (!flyout || !shouldShowFlyout()) return;
                var label = el.getAttribute('data-sidebar-flyout');
                if (!label) return;

                var rect = anchorRectFor(el);
                flyout.textContent = label;
                flyout.hidden = false;
                flyout.classList.remove('is-visible', 'sidebar-flyout-tooltip--caret-right');
                flyout.style.left = '-9999px';
                flyout.style.top = '0';
                flyout.style.opacity = '0';
                flyout.style.visibility = 'visible';

                var tw = flyout.offsetWidth;
                var th = flyout.offsetHeight;
                var gap = 10;
                var pad = 12;
                var left = rect.right + gap;
                if (left + tw > window.innerWidth - pad) {
                    left = rect.left - gap - tw;
                    flyout.classList.add('sidebar-flyout-tooltip--caret-right');
                }
                left = Math.max(pad, Math.min(left, window.innerWidth - tw - pad));

                var top = rect.top + rect.height / 2 - th / 2;
                top = Math.max(pad, Math.min(top, window.innerHeight - th - pad));

                flyout.style.left = left + 'px';
                flyout.style.top = top + 'px';
                flyout.style.opacity = '';
                flyout.style.visibility = '';
                requestAnimationFrame(function () {
                    flyout.classList.add('is-visible');
                });
            }

            document.querySelectorAll('.unified-sidebar [data-sidebar-flyout]').forEach(function (el) {
                el.addEventListener('mouseenter', function () {
                    if (hideFlyoutTimer) {
                        clearTimeout(hideFlyoutTimer);
                        hideFlyoutTimer = null;
                    }
                    showFlyoutFor(el);
                });
                el.addEventListener('mouseleave', function () {
                    hideFlyoutTimer = setTimeout(hideFlyout, 100);
                });
                el.addEventListener('focus', function () {
                    if (hideFlyoutTimer) {
                        clearTimeout(hideFlyoutTimer);
                        hideFlyoutTimer = null;
                    }
                    showFlyoutFor(el);
                });
                el.addEventListener('blur', function () {
                    hideFlyoutTimer = setTimeout(hideFlyout, 100);
                });
            });

            if (navScroll) {
                navScroll.addEventListener('scroll', hideFlyout);
            }
            window.addEventListener('resize', hideFlyout);
            window.addEventListener('scroll', hideFlyout, true);

            var collapseToggle = document.getElementById('sidebarCollapseToggle');
            if (collapseToggle) {
                collapseToggle.addEventListener('click', function () {
                    hideFlyout();
                    collapsed = !document.body.classList.contains('sidebar-collapsed');
                    applyCollapsed(collapsed);
                    try { window.localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0'); } catch (e) {}
                });
            }

            var mobileToggle = document.getElementById('sidebarMobileToggle');
            var backdrop = document.getElementById('sidebarBackdrop');
            function closeMobile() { document.body.classList.remove('sidebar-mobile-open'); }
            function openMobile() { document.body.classList.add('sidebar-mobile-open'); }
            if (mobileToggle) {
                mobileToggle.addEventListener('click', function () {
                    if (document.body.classList.contains('sidebar-mobile-open')) { closeMobile(); } else { openMobile(); }
                });
            }
            if (backdrop) backdrop.addEventListener('click', closeMobile);
            document.querySelectorAll('.unified-sidebar nav a').forEach(function (a) {
                a.addEventListener('click', function () {
                    if (window.innerWidth <= 768) closeMobile();
                });
            });
            window.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeMobile();
            });
        });
    })();
</script>
