@extends('layouts.welcome')
@section('title', 'Dashboard — CayMark')

@section('content')
<div class="min-h-screen bg-gray-50 pb-12">

    {{-- ── COMPLETION BANNER ──────────────────────────────────────────── --}}
    <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 px-6 md:px-10 py-5 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0">
                <span class="material-icons-round text-white text-xl">lock_open</span>
            </div>
            <div>
                <p class="text-white font-semibold text-sm sm:text-base">Complete registration to start bidding or selling</p>
                <p class="text-blue-200 text-xs mt-0.5 hidden sm:block">Select a membership to unlock full access to CayMark auctions.</p>
            </div>
        </div>
        <a href="{{ route('finish.registration') }}"
           class="flex-shrink-0 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white text-blue-700 font-bold text-sm shadow-lg hover:bg-blue-50 transition-all hover:-translate-y-0.5">
            <span class="material-icons-round text-lg">arrow_forward</span>
            Choose Your Membership
        </a>
    </div>

    {{-- ── PAGE HEADER ────────────────────────────────────────────────── --}}
    <div class="px-6 md:px-10 pt-8 pb-2">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Welcome back, {{ Str::ucfirst($user->name) }} 👋
                </h1>
                <p class="text-gray-500 text-sm mt-1">Your guest dashboard — limited access until registration is complete.</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold border border-amber-200">
                <span class="material-icons-round text-base">pending</span>
                Registration Incomplete
            </span>
        </div>
    </div>

    {{-- ── TAB NAV ─────────────────────────────────────────────────────── --}}
    <div class="px-6 md:px-10 mt-6">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

            {{-- Tab buttons --}}
            <div class="border-b border-gray-100 bg-gray-50/60 px-2 pt-2 flex gap-1 overflow-x-auto">
                @php
                    $tabs = [
                        ['id' => 'dashboard',     'label' => 'Dashboard',        'icon' => 'dashboard'],
                        ['id' => 'account',       'label' => 'Account Settings', 'icon' => 'person'],
                        ['id' => 'notifications', 'label' => 'Notifications',    'icon' => 'notifications'],
                        ['id' => 'support',       'label' => 'Customer Support', 'icon' => 'support_agent'],
                    ];
                @endphp
                @foreach($tabs as $tab)
                    <button type="button"
                            id="tab-btn-{{ $tab['id'] }}"
                            onclick="switchTab('{{ $tab['id'] }}')"
                            class="guest-tab-btn flex items-center gap-2 px-4 py-2.5 rounded-t-lg text-sm font-medium whitespace-nowrap transition-all border-b-2
                                   {{ $activeTab === $tab['id']
                                       ? 'text-blue-700 border-blue-600 bg-white'
                                       : 'text-gray-500 border-transparent hover:text-gray-800 hover:bg-white/70' }}">
                        <span class="material-icons-round text-base">{{ $tab['icon'] }}</span>
                        {{ $tab['label'] }}
                        @if($tab['id'] === 'notifications')
                            @php $unread = $notifications->whereNull('read_at')->count(); @endphp
                            @if($unread > 0)
                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-600 text-white text-[10px] font-bold">{{ $unread }}</span>
                            @endif
                        @endif
                    </button>
                @endforeach
            </div>

            {{-- ════ TAB: DASHBOARD ════ --}}
            <div id="tab-content-dashboard" class="guest-tab-content {{ $activeTab !== 'dashboard' ? 'hidden' : '' }} p-6 md:p-8">

                {{-- Status card --}}
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-6 mb-8 flex flex-col sm:flex-row items-start gap-5">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <span class="material-icons-round text-amber-600 text-2xl">hourglass_empty</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg font-bold text-amber-900 mb-1">Registration Pending</h2>
                        <p class="text-amber-800 text-sm leading-relaxed">
                            You've created your CayMark account. To bid on auctions, save items, or list vehicles for sale,
                            select a membership and complete the short verification process.
                        </p>
                        <a href="{{ route('finish.registration') }}"
                           class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold text-sm hover:bg-blue-700 transition-all shadow hover:-translate-y-0.5">
                            <span class="material-icons-round text-lg">workspace_premium</span>
                            Complete Registration — Select Membership
                        </a>
                    </div>
                </div>

                {{-- Available vs restricted --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Available --}}
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4 flex items-center gap-2">
                            <span class="material-icons-round text-green-600 text-base">check_circle</span>
                            Available Now
                        </h3>
                        <ul class="space-y-3">
                            @foreach([
                                ['icon'=>'person','label'=>'View &amp; update your profile'],
                                ['icon'=>'lock','label'=>'Change your password'],
                                ['icon'=>'notifications','label'=>'View system notifications'],
                                ['icon'=>'search','label'=>'Browse site listings (read-only)'],
                                ['icon'=>'support_agent','label'=>'Contact customer support'],
                            ] as $feat)
                                <li class="flex items-center gap-3 text-sm text-gray-700">
                                    <span class="material-icons-round text-green-500 text-base">{{ $feat['icon'] }}</span>
                                    {!! $feat['label'] !!}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Restricted --}}
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4 flex items-center gap-2">
                            <span class="material-icons-round text-red-500 text-base">block</span>
                            Requires Full Registration
                        </h3>
                        <ul class="space-y-3">
                            @foreach([
                                ['icon'=>'gavel','label'=>'Place bids on auctions'],
                                ['icon'=>'shopping_cart','label'=>'Buy Now purchases'],
                                ['icon'=>'add_box','label'=>'Submit vehicle listings'],
                                ['icon'=>'bookmark','label'=>'Save &amp; watch items'],
                                ['icon'=>'mail','label'=>'Messaging Center'],
                                ['icon'=>'account_balance_wallet','label'=>'Payouts &amp; payments'],
                            ] as $feat)
                                <li class="flex items-center gap-3 text-sm text-gray-500">
                                    <span class="material-icons-round text-red-400 text-base">{{ $feat['icon'] }}</span>
                                    {!! $feat['label'] !!}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- ════ TAB: ACCOUNT SETTINGS ════ --}}
            <div id="tab-content-account" class="guest-tab-content {{ $activeTab !== 'account' ? 'hidden' : '' }} p-6 md:p-8">

                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-1">Account Settings</h2>
                    <p class="text-gray-500 text-sm">Manage your personal information and password.</p>
                </div>

                <div class="space-y-5 max-w-2xl">

                    {{-- Full Name (read-only) --}}
                    <div class="bg-white rounded-2xl border border-gray-200 p-5">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Full Name</label>
                        <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 rounded-xl border border-gray-200">
                            <span class="material-icons-round text-gray-400">person</span>
                            <span class="text-gray-900 font-medium">{{ $user->name }}</span>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="bg-white rounded-2xl border border-gray-200 p-5">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Email Address</label>
                        @php $emailChangePending = session('email_change_pending') || (new \App\Services\EmailChangeVerificationService())->hasPendingChange($user); @endphp
                        @if($emailChangePending)
                            @php $pendingNew = session('email_change_new') ?? (new \App\Services\EmailChangeVerificationService())->getPendingNewEmail($user); @endphp
                            <p class="text-sm text-gray-600 mb-3">
                                Enter the code sent to <strong>{{ $user->email }}</strong> to confirm the change to <strong>{{ $pendingNew }}</strong>.
                            </p>
                            <form method="POST" action="{{ route('basic-dashboard.update-email') }}" class="flex flex-col sm:flex-row gap-3">
                                @csrf
                                <input type="hidden" name="email" value="{{ $pendingNew }}">
                                <input type="text" name="code" value="{{ old('code') }}" placeholder="6-digit code" maxlength="6"
                                       class="w-40 border border-gray-300 rounded-xl px-4 py-3 font-mono text-center tracking-widest focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('code')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                                <button type="submit" class="px-5 py-3 rounded-xl bg-blue-600 text-white font-semibold text-sm hover:bg-blue-700 transition-all">Confirm change</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('basic-dashboard.update-email') }}" class="flex flex-col sm:flex-row gap-3">
                                @csrf
                                <div class="flex-1">
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                                </div>
                                <button type="submit"
                                        class="px-5 py-3 rounded-xl bg-blue-600 text-white font-semibold text-sm hover:bg-blue-700 transition-all flex items-center gap-2">
                                    <span class="material-icons-round text-base">send</span>
                                    Send verification code
                                </button>
                            </form>
                            <p class="text-xs text-gray-400 mt-2">A code will be sent to your current email to approve the change.</p>
                        @endif
                    </div>

                    {{-- Password --}}
                    <div class="bg-white rounded-2xl border border-gray-200 p-5">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Password</label>
                        <button type="button" onclick="document.getElementById('guestPasswordModal').classList.remove('hidden')"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 text-gray-700 font-semibold text-sm hover:bg-gray-100 transition-all">
                            <span class="material-icons-round text-base">lock</span>
                            Change Password
                        </button>
                    </div>

                    {{-- Documents --}}
                    @if($documents->count() > 0)
                        <div class="bg-white rounded-2xl border border-gray-200 p-5">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Documents</label>
                            <div class="space-y-3">
                                @foreach($documents as $document)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                                        <div class="flex items-center gap-3">
                                            <span class="material-icons-round text-blue-500">description</span>
                                            <div>
                                                <p class="font-medium text-gray-900 text-sm">{{ ucfirst(str_replace('_', ' ', $document->doc_type)) }}</p>
                                                @if($document->filename)
                                                    <p class="text-xs text-gray-400">{{ $document->filename }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        @if($document->path)
                                            <a href="{{ route('user.document.view', $document->id) }}" target="_blank"
                                               class="text-blue-600 text-sm font-medium hover:underline flex items-center gap-1">
                                                <span class="material-icons-round text-base">open_in_new</span> View
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-400 mt-3">Documents are view-only from the guest dashboard.</p>
                        </div>
                    @endif

                    {{-- Choose membership CTA --}}
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-5 flex items-center gap-4">
                        <span class="material-icons-round text-blue-600 text-3xl flex-shrink-0">workspace_premium</span>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 text-sm">Ready to unlock everything?</p>
                            <p class="text-gray-500 text-xs mt-0.5">Complete registration and choose your membership to access all features.</p>
                        </div>
                        <a href="{{ route('finish.registration') }}"
                           class="flex-shrink-0 px-4 py-2.5 rounded-xl bg-blue-600 text-white font-semibold text-sm hover:bg-blue-700 transition-all whitespace-nowrap">
                            Select Membership
                        </a>
                    </div>
                </div>
            </div>

            {{-- ════ TAB: NOTIFICATIONS ════ --}}
            <div id="tab-content-notifications" class="guest-tab-content {{ $activeTab !== 'notifications' ? 'hidden' : '' }} p-6 md:p-8">

                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 mb-1">Notifications</h2>
                        <p class="text-gray-500 text-sm">Account alerts and system messages.</p>
                    </div>
                    @if($notifications->whereNull('read_at')->count() > 0)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">
                            {{ $notifications->whereNull('read_at')->count() }} unread
                        </span>
                    @endif
                </div>

                @if($notifications->count() > 0)
                    <div class="space-y-3">
                        @foreach($notifications as $notification)
                            @php
                                $nData  = $notification->data;
                                $nTitle = $nData['title'] ?? null;
                                $nMsg   = $nData['message'] ?? ($nData['title'] ?? 'Notification');
                                $nLink  = $nData['link']  ?? null;
                                $nType  = $nData['type']  ?? 'info';
                                $isUnread = is_null($notification->read_at);

                                $iconMap = [
                                    'welcome'                => ['icon'=>'celebration',   'color'=>'text-blue-600',  'bg'=>'bg-blue-100'],
                                    'registration_reminder'  => ['icon'=>'workspace_premium','color'=>'text-amber-600','bg'=>'bg-amber-100'],
                                ];
                                $iconSet = $iconMap[$nType] ?? ['icon'=>'notifications','color'=>'text-gray-500','bg'=>'bg-gray-100'];
                            @endphp
                            <div class="flex items-start gap-4 p-4 rounded-2xl border {{ $isUnread ? 'bg-blue-50/40 border-blue-200' : 'bg-white border-gray-200' }} hover:border-blue-300 transition-all">
                                <div class="w-10 h-10 rounded-xl {{ $iconSet['bg'] }} flex items-center justify-center flex-shrink-0">
                                    <span class="material-icons-round {{ $iconSet['color'] }} text-xl">{{ $iconSet['icon'] }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    @if($nTitle)
                                        <p class="font-semibold text-gray-900 text-sm">{{ $nTitle }}</p>
                                    @endif
                                    <p class="text-gray-700 text-sm {{ $nTitle ? 'mt-0.5' : '' }}">{{ $nMsg }}</p>
                                    @if($nLink)
                                        <a href="{{ $nLink }}"
                                           class="inline-flex items-center gap-1 mt-2 text-blue-600 text-xs font-semibold hover:underline">
                                            <span class="material-icons-round text-sm">arrow_forward</span>
                                            Take action
                                        </a>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                                        <span class="material-icons-round text-xs">schedule</span>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                @if($isUnread)
                                    <span class="w-2.5 h-2.5 bg-blue-600 rounded-full flex-shrink-0 mt-1"></span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 py-16 text-center">
                        <span class="material-icons-round text-gray-300 text-5xl block mb-3">notifications_none</span>
                        <p class="text-gray-500 font-medium">No notifications yet</p>
                        <p class="text-gray-400 text-sm mt-1">System messages will appear here.</p>
                    </div>
                @endif
            </div>

            {{-- ════ TAB: CUSTOMER SUPPORT ════ --}}
            <div id="tab-content-support" class="guest-tab-content {{ $activeTab !== 'support' ? 'hidden' : '' }} p-6 md:p-8">

                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-1">Customer Support</h2>
                    <p class="text-gray-500 text-sm">Get help with your account or registration process.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 max-w-3xl">

                    {{-- Registration help --}}
                    <div class="bg-white rounded-2xl border border-gray-200 p-5 flex flex-col">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center mb-4">
                            <span class="material-icons-round text-blue-600">workspace_premium</span>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Complete Registration</h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-4 flex-1">Choose your membership and verify your identity to unlock all CayMark features.</p>
                        <a href="{{ route('finish.registration') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 text-white font-semibold text-sm hover:bg-blue-700 transition-all self-start">
                            <span class="material-icons-round text-base">arrow_forward</span>
                            Start Now
                        </a>
                    </div>

                    {{-- Help Center --}}
                    <div class="bg-white rounded-2xl border border-gray-200 p-5 flex flex-col">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center mb-4">
                            <span class="material-icons-round text-indigo-600">help_outline</span>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Help Center</h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-4 flex-1">Browse FAQs, guides, and answers to common questions about CayMark.</p>
                        <a href="{{ route('help-center') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold text-sm hover:bg-gray-50 transition-all self-start">
                            <span class="material-icons-round text-base">open_in_new</span>
                            Visit Help Center
                        </a>
                    </div>

                    {{-- Seller's Guide --}}
                    <div class="bg-white rounded-2xl border border-gray-200 p-5 flex flex-col">
                        <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center mb-4">
                            <span class="material-icons-round text-green-600">menu_book</span>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Seller's Guide</h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-4 flex-1">Planning to list a vehicle? Learn how CayMark auctions work for sellers.</p>
                        <a href="{{ route('sellers-guide') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold text-sm hover:bg-gray-50 transition-all self-start">
                            <span class="material-icons-round text-base">open_in_new</span>
                            Read Guide
                        </a>
                    </div>

                    {{-- Buyer's Guide --}}
                    <div class="bg-white rounded-2xl border border-gray-200 p-5 flex flex-col">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center mb-4">
                            <span class="material-icons-round text-purple-600">gavel</span>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Buyer's Guide</h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-4 flex-1">Planning to bid? Learn how the bidding process, Buy Now, and payments work.</p>
                        <a href="{{ route('buyer-guide') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold text-sm hover:bg-gray-50 transition-all self-start">
                            <span class="material-icons-round text-base">open_in_new</span>
                            Read Guide
                        </a>
                    </div>
                </div>

                {{-- Contact note --}}
                <div class="mt-8 max-w-3xl rounded-2xl border border-gray-200 bg-gray-50 p-5 flex items-start gap-4">
                    <span class="material-icons-round text-gray-400 text-2xl flex-shrink-0">info</span>
                    <div>
                        <p class="text-gray-700 text-sm font-medium">Need to contact support directly?</p>
                        <p class="text-gray-500 text-xs mt-1 leading-relaxed">
                            Full support ticket submission is available after completing registration.
                            In the meantime, visit our <a href="{{ route('help-center') }}" class="text-blue-600 hover:underline">Help Center</a>
                            or reach us at
                            <a href="mailto:support@caymark.com" class="text-blue-600 hover:underline">support@caymark.com</a>.
                        </p>
                    </div>
                </div>
            </div>

        </div>{{-- end main card --}}
    </div>{{-- end px wrapper --}}
</div>

{{-- ── PASSWORD CHANGE MODAL ───────────────────────────────────────────── --}}
<div id="guestPasswordModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/70">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 relative">
        <button type="button" onclick="document.getElementById('guestPasswordModal').classList.add('hidden')"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
            <span class="material-icons-round text-2xl">close</span>
        </button>
        <h3 class="text-xl font-bold text-gray-900 mb-1">Change Password</h3>
        <p class="text-gray-500 text-sm mb-6">Update your account password.</p>
        <form method="POST" action="{{ route('basic-dashboard.change-password') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Current Password</label>
                <input type="password" name="current_password" required
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">New Password</label>
                <input type="password" name="password" required minlength="8"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm New Password</label>
                <input type="password" name="password_confirmation" required minlength="8"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('guestPasswordModal').classList.add('hidden')"
                        class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition-all">
                    Cancel
                </button>
                <button type="submit"
                        class="px-5 py-2.5 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition-all shadow">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function switchTab(tabId) {
    // Hide all panels
    document.querySelectorAll('.guest-tab-content').forEach(el => el.classList.add('hidden'));
    // Reset all buttons
    document.querySelectorAll('.guest-tab-btn').forEach(btn => {
        btn.classList.remove('text-blue-700', 'border-blue-600', 'bg-white');
        btn.classList.add('text-gray-500', 'border-transparent');
    });
    // Show target
    const panel = document.getElementById('tab-content-' + tabId);
    if (panel) panel.classList.remove('hidden');
    const btn = document.getElementById('tab-btn-' + tabId);
    if (btn) {
        btn.classList.remove('text-gray-500', 'border-transparent');
        btn.classList.add('text-blue-700', 'border-blue-600', 'bg-white');
    }
    // Update URL without reload so sidebar active state stays correct
    const url = new URL(window.location);
    if (tabId === 'dashboard') {
        url.searchParams.delete('tab');
    } else {
        url.searchParams.set('tab', tabId);
    }
    history.replaceState({}, '', url);
}

// Close modal on backdrop click
document.getElementById('guestPasswordModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
</script>

@endsection
