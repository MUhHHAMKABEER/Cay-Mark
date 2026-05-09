@php
    use App\Models\PostAuctionThread;
    $listing = $activeThread->listing;
    $invoice = $activeThread->invoice;
    $title = trim(($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? '')) ?: 'Listing #' . $activeThread->listing_id;
    $img = $listing && $listing->images?->first()
        ? (str_contains($listing->images->first()->image_path, '/')
            ? asset($listing->images->first()->image_path)
            : asset('uploads/listings/' . $listing->images->first()->image_path))
        : null;
    $latestPickup = $activeThread->latestPickupDetail;
    $pinCode = $listing?->pickupCodeDisplay();
    $pendingChangeRequests = $activeThread->changeRequests->whereIn('status', ['pending'])->values();
    $pendingDeliveryRequests = $activeThread->deliveryRequests->where('status', 'pending')->values();
    $supportPhone = config('support.phone', config('support.inbox', 'support@caymark.com'));
    $supportEmail = config('support.inbox', 'support@caymark.com');
    $exchangesUsed = (int) $activeThread->exchanges_count;
    $maxExchanges = PostAuctionThread::MAX_EXCHANGES;
    $canAcceptOrChange = $latestPickup && in_array($latestPickup->status, ['pending', 'change_requested']) && ! $activeThread->pickup_confirmed;
    $sellerPinReady = $isSeller && $latestPickup && $latestPickup->status === 'confirmed' && ! $activeThread->pickup_confirmed;
@endphp

<div class="messaging-topbar">
    <a href="{{ route('messaging.index') }}" class="back-link">
        <span class="material-icons-round" style="font-size: 1rem;">arrow_back</span>
        Back to conversations
    </a>
    <div style="font-size: 1rem; font-weight: 700; color: #0f172a;">{{ $title }}</div>
    <div style="display:flex; align-items:center; gap: 0.875rem;">
        @if ($invoice)
            <span class="order-num">{{ $isSeller ? 'Sale #S-' : 'Purchase #O-' }}{{ $invoice->invoice_number ?? $invoice->id }}</span>
        @endif
        @if ($listing)
            <a href="{{ route('listing.show', $listing->id) }}" class="view-item">
                View Item <span class="material-icons-round" style="font-size: 0.95rem;">open_in_new</span>
            </a>
        @endif
    </div>
</div>

<div class="messaging-main-body">
    @if ($pinCode && ! $activeThread->pickup_confirmed)
        <div class="pickup-code-band">
            <div class="lock-icon"><span class="material-icons-round">lock</span></div>
            @if ($isBuyer)
                <div>
                    <div class="label">Pickup Code</div>
                    <div class="code">{{ $pinCode }}</div>
                </div>
                <div class="hint">
                    <span class="material-icons-round" style="font-size: 1rem; color:#d97706;">warning_amber</span>
                    Please present this code to your seller after vehicle pickup.
                </div>
                <button type="button" class="copy-btn" id="pickup-copy-btn" onclick="copyPickupCode('{{ $pinCode }}')">
                    <span class="material-icons-round" style="font-size:1rem;">content_copy</span> Copy Code
                </button>
            @else
                <div>
                    <div class="label">Pickup Code</div>
                    <div class="code" style="color:#92400e; font-size:1rem;">Awaiting buyer code at pickup</div>
                </div>
                <div class="hint">
                    <span class="material-icons-round" style="font-size: 1rem; color:#d97706;">info</span>
                    Ask the buyer for their 6-digit pickup code, then enter it below to complete the sale.
                </div>
            @endif
        </div>
    @endif

    <div class="header-card">
        @if ($img)
            <img src="{{ $img }}" alt="" class="car-thumb">
        @else
            <div style="width:140px; height:100px; border-radius: 10px; background: #e2e8f0; display:flex; align-items:center; justify-content:center;">
                <span class="material-icons-round" style="color:#94a3b8;">directions_car</span>
            </div>
        @endif
        <div>
            <div style="font-size: 1.05rem; font-weight: 700; color: #0f172a;">{{ $title }}</div>
            <div class="meta-row">
                @if ($isSeller)
                    <span class="material-icons-round">shopping_bag</span>
                    <span><strong>Purchased by:</strong> {{ $activeThread->buyer?->name ?? '—' }}</span>
                @else
                    <span class="material-icons-round">storefront</span>
                    <span><strong>Sold by:</strong> {{ $activeThread->seller?->name ?? '—' }}</span>
                @endif
            </div>
            <div class="meta-row">
                <span class="material-icons-round">event</span>
                <span><strong>Auction Ended:</strong> {{ optional($invoice?->sale_date)?->format('M d, Y') ?? '—' }}</span>
            </div>
            <div class="meta-row">
                <span class="material-icons-round">payments</span>
                <span><strong>Payment Made:</strong> {{ optional($invoice?->paid_at)?->format('M d, Y') ?? optional($activeThread->unlocked_at)?->format('M d, Y') ?? '—' }}</span>
            </div>
            <div class="pills">
                @if ($activeThread->pickup_confirmed)
                    <span class="pill pill-completed"><span class="material-icons-round" style="font-size: 0.85rem;">check_circle</span> Sold • Completed</span>
                @else
                    <span class="pill pill-paid"><span class="material-icons-round" style="font-size: 0.85rem;">check</span> Payment Completed</span>
                @endif
                <span class="pill" style="background:#dbeafe; color:#1e40af;">
                    <span class="material-icons-round" style="font-size: 0.85rem;">lock_open</span> Messaging Unlocked
                </span>
            </div>
        </div>
    </div>

    <div class="system-notice">
        <span class="material-icons-round">shield</span>
        <span>All communication is monitored and secured by CayMark. Please do <strong>not</strong> share phone numbers, emails, or external links — they will be blocked automatically.</span>
    </div>

    @if ($activeThread->flagged_for_admin)
        <div class="flagged-banner">
            <span class="material-icons-round">flag</span>
            <span>The CayMark team has been notified and may step in to help. You can keep editing this thread while we review.</span>
        </div>
    @endif

    <div class="convo-section-title">
        <span class="material-icons-round">forum</span>
        Conversation
    </div>

    @forelse ($events as $event)
        @include('messaging._event', ['event' => $event])
    @empty
        <div style="padding: 1.5rem; text-align: center; color: #94a3b8; font-size: 0.875rem; background:#f8fafc; border-radius: 12px;">
            No updates yet. {{ $isSeller ? 'Send the pickup schedule below to get started.' : 'Waiting for the seller to send pickup details.' }}
        </div>
    @endforelse

    @if ($isBuyer && $canAcceptOrChange && $latestPickup)
        <div style="margin-top: 1rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius: 12px; padding: 1rem;">
            <div style="font-size: 0.875rem; color:#475569; margin-bottom: 8px;">
                You can accept the schedule, propose a date/time change, or request a new location.
            </div>
            <div class="inline-actions">
                <form method="POST" action="{{ route('messaging.thread.accept-pickup', $activeThread->id) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-accept">
                        <span class="material-icons-round" style="font-size:1.1rem;">check</span> Accept Schedule
                    </button>
                </form>
                <button type="button" class="btn btn-secondary" onclick="openMessagingModal('modal-change')">
                    <span class="material-icons-round" style="font-size:1.1rem;">edit_calendar</span> Propose Change
                </button>
                <button type="button" class="btn btn-secondary" onclick="openMessagingModal('modal-location')">
                    <span class="material-icons-round" style="font-size:1.1rem;">location_on</span> Request New Location
                </button>
            </div>
        </div>
    @endif

    @if ($isSeller && $pendingChangeRequests->isNotEmpty())
        <div style="margin-top: 1.25rem;">
            <div class="convo-section-title">
                <span class="material-icons-round">edit_calendar</span> Pending Change Requests
            </div>
            @foreach ($pendingChangeRequests as $cr)
                <div class="event-card from-buyer">
                    <div class="event-head">
                        <div class="who"><span class="material-icons-round">person</span><span>{{ $activeThread->buyer?->name ?? 'Buyer' }} requested {{ $cr->isLocationRequest() ? 'a new location' : 'a date / time change' }}</span></div>
                        <div class="when">{{ $cr->requested_at?->format('M d, Y g:i A') }}</div>
                    </div>
                    <div class="event-body">
                        @if ($cr->requested_pickup_date)<div class="field-line"><span class="field-label">New date:</span> {{ $cr->requested_pickup_date->format('l, F d, Y') }}</div>@endif
                        @if ($cr->requested_pickup_time)<div class="field-line"><span class="field-label">New time:</span> {{ $cr->requested_pickup_time->format('g:i A') }}</div>@endif
                        @if ($cr->requested_location)<div class="field-line"><span class="field-label">Proposed location:</span> {{ $cr->requested_location }}</div>@endif
                        @if ($cr->additional_notes)<div class="field-line"><span class="field-label">Notes:</span> {{ $cr->additional_notes }}</div>@endif
                    </div>
                    <form method="POST" action="{{ route('messaging.change.respond', $cr->id) }}" style="margin-top: 10px;">
                        @csrf
                        <input type="hidden" name="action" value="approve">
                        <div style="display:flex; gap: 8px; flex-wrap: wrap;">
                            <button type="submit" class="btn btn-accept" style="padding: 0.5rem 0.875rem; border-radius: 8px; background:#10b981; color:#fff; border:none; font-weight:600; cursor:pointer; font-size: 0.85rem;">
                                <span class="material-icons-round" style="font-size:1rem; vertical-align:middle;">check</span> Approve
                            </button>
                        </div>
                    </form>
                    @unless ($cr->isLocationRequest())
                        <details style="margin-top: 8px;">
                            <summary style="cursor:pointer; font-size: 0.8rem; color:#475569;">Counter with different date/time</summary>
                            <form method="POST" action="{{ route('messaging.change.respond', $cr->id) }}" style="margin-top: 8px; display:grid; grid-template-columns: 1fr 1fr auto; gap: 8px;">
                                @csrf
                                <input type="hidden" name="action" value="counter">
                                <input type="date" name="countered_pickup_date" min="{{ now()->toDateString() }}" style="padding: 0.4rem; border:1px solid #cbd5e1; border-radius:6px;">
                                <input type="time" name="countered_pickup_time" style="padding: 0.4rem; border:1px solid #cbd5e1; border-radius:6px;">
                                <button type="submit" class="btn btn-secondary" style="padding: 0.5rem 0.875rem; border-radius: 8px;">Send Counter</button>
                            </form>
                        </details>
                    @endunless
                </div>
            @endforeach
        </div>
    @endif

    @if ($isSeller && $pendingDeliveryRequests->isNotEmpty())
        <div style="margin-top: 1.25rem;">
            <div class="convo-section-title">
                <span class="material-icons-round">local_shipping</span> Pending Delivery Requests
            </div>
            @foreach ($pendingDeliveryRequests as $dr)
                <div class="event-card from-buyer">
                    <div class="event-head">
                        <div class="who"><span class="material-icons-round">person</span><span>{{ $activeThread->buyer?->name ?? 'Buyer' }} requested delivery</span></div>
                        <div class="when">{{ $dr->submitted_at?->format('M d, Y g:i A') }}</div>
                    </div>
                    <div class="event-body">
                        <div class="field-line"><span class="field-label">Address:</span> {{ $dr->delivery_address }}</div>
                        @if ($dr->preferred_date)<div class="field-line"><span class="field-label">Preferred date:</span> {{ $dr->preferred_date->format('l, F d, Y') }}</div>@endif
                        @if ($dr->preferred_time)<div class="field-line"><span class="field-label">Preferred time:</span> {{ $dr->preferred_time->format('g:i A') }}</div>@endif
                        @if ($dr->additional_notes)<div class="field-line"><span class="field-label">Notes:</span> {{ $dr->additional_notes }}</div>@endif
                    </div>
                    <form method="POST" action="{{ route('messaging.delivery.respond', $dr->id) }}" style="margin-top: 10px; display:flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                        @csrf
                        <input type="text" name="response_notes" maxlength="500" placeholder="Optional response note" style="flex:1; min-width: 200px; padding: 0.5rem; border:1px solid #cbd5e1; border-radius: 8px; font-size: 0.85rem;">
                        <button type="submit" name="action" value="approve" class="btn btn-accept" style="padding: 0.5rem 0.875rem; border-radius: 8px; background:#10b981; color:#fff; border:none; font-weight:600; cursor:pointer; font-size: 0.85rem;">Approve</button>
                        <button type="submit" name="action" value="reject" class="btn btn-secondary" style="padding: 0.5rem 0.875rem; border-radius: 8px; background:#fff; border:1px solid #cbd5e1; color:#475569; cursor:pointer; font-weight:600; font-size: 0.85rem;">Reject</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif

    @if ($sellerPinReady)
        <div style="margin-top: 1.25rem; background:#fff; border:2px solid #10b981; border-radius: 14px; padding: 1.25rem;">
            <div style="font-weight: 700; color:#065f46; font-size: 0.95rem; margin-bottom: 4px;">
                <span class="material-icons-round" style="font-size: 1.1rem; vertical-align:middle;">vpn_key</span>
                Confirm Pickup with Buyer's PIN
            </div>
            <p style="font-size: 0.85rem; color:#475569; margin-bottom: 0.75rem;">Ask the buyer for the 6-digit code shown in their dashboard, then enter it here to mark this transaction complete and trigger your payout.</p>
            <button type="button" class="btn btn-accept" onclick="openMessagingModal('modal-pin')" style="background:#10b981; color:#fff; border:none; padding: 0.625rem 1.25rem; border-radius: 10px; font-weight: 600; cursor: pointer;">
                Enter Pickup PIN
            </button>
        </div>
    @endif

    @if ($isSeller && ! $latestPickup && ! $activeThread->pickup_confirmed)
        <div style="margin-top: 1.25rem; background:#fff; border:2px dashed #cbd5e1; border-radius: 14px; padding: 1.25rem; text-align:center;">
            <div style="font-weight: 700; color:#0f172a; font-size: 0.95rem;">Send pickup schedule to buyer</div>
            <p style="font-size: 0.85rem; color:#475569; margin: 6px 0 12px;">Provide the date, time and location so the buyer can collect their vehicle.</p>
            <button type="button" class="btn btn-accept" onclick="openMessagingModal('modal-pickup-form')" style="background:#2563eb; color:#fff; border:none; padding: 0.625rem 1.25rem; border-radius: 10px; font-weight: 600; cursor: pointer;">
                <span class="material-icons-round" style="font-size:1rem; vertical-align:middle;">event</span> Send Pickup Schedule
            </button>
        </div>
    @endif

    {{-- Action chip rows (matches mockup) --}}
    @if (! $activeThread->pickup_confirmed)
        <div class="action-row">
            @if ($isBuyer)
                <button type="button" class="action-chip" onclick="openMessagingModal('modal-delivery')">
                    <span class="material-icons-round">local_shipping</span> Request Delivery
                </button>
                <button type="button" class="action-chip" onclick="openMessagingModal('modal-third-party')">
                    <span class="material-icons-round">group</span> Third-Party / Tow
                </button>
                @if ($latestPickup && ! $activeThread->pickup_confirmed)
                    <button type="button" class="action-chip" onclick="openMessagingModal('modal-change')">
                        <span class="material-icons-round">edit_calendar</span> Request Time Change
                    </button>
                @endif
                <button type="button" class="action-chip" onclick="openMessagingModal('modal-other')">
                    <span class="material-icons-round">help_outline</span> Other Request
                </button>
                <button type="button" class="action-chip danger" onclick="openMessagingModal('modal-report')">
                    <span class="material-icons-round">flag</span> Report Issue
                </button>
            @endif

            @if ($isSeller)
                @if ($latestPickup)
                    <form method="POST" action="{{ route('messaging.thread.resend-schedule', $activeThread->id) }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="action-chip" style="width:100%;">
                            <span class="material-icons-round">replay</span> Resend Schedule
                        </button>
                    </form>
                @endif
                <button type="button" class="action-chip danger" onclick="openMessagingModal('modal-report')">
                    <span class="material-icons-round">flag</span> Report Issue
                </button>
                <form method="POST" action="{{ route('messaging.thread.request-assistance', $activeThread->id) }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="action-chip" style="width:100%;">
                        <span class="material-icons-round">support</span> Request CayMark Assistance
                    </button>
                </form>
                @if ($activeThread->pickup_confirmed === false && ! $activeThread->seller_ready_at)
                    <form method="POST" action="{{ route('messaging.thread.mark-ready', $activeThread->id) }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="action-chip" style="width:100%;">
                            <span class="material-icons-round">directions_car</span> Mark as Ready for Pickup
                        </button>
                    </form>
                @endif
                <button type="button" class="action-chip" onclick="openMessagingModal('modal-other')">
                    <span class="material-icons-round">help_outline</span> Other Request
                </button>
            @endif

            @if ($isBuyer)
                <form method="POST" action="{{ route('messaging.thread.request-assistance', $activeThread->id) }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="action-chip" style="width:100%;">
                        <span class="material-icons-round">support</span> Request CayMark Assistance
                    </button>
                </form>
            @endif
        </div>
    @endif

    @if ($isBuyer && $activeThread->pickup_confirmed && ! $activeThread->buyer_completion_confirmed_at)
        <div style="margin-top: 1.25rem;">
            <form method="POST" action="{{ route('messaging.thread.confirm-sale', $activeThread->id) }}">
                @csrf
                <button type="submit" class="btn btn-accept" style="background:#10b981; color:#fff; border:none; padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 600; cursor: pointer; font-size: 0.95rem;">
                    <span class="material-icons-round" style="font-size:1.1rem; vertical-align:middle;">verified</span>
                    Confirm Sale Completed
                </button>
            </form>
        </div>
    @endif
</div>

<div class="footer-card">
    <div class="help">
        <span class="help-title">Need Help?</span>
        <span class="help-row"><span class="material-icons-round">phone</span> {{ $supportPhone }}</span>
        <span class="help-row"><span class="material-icons-round">email</span> {{ $supportEmail }}</span>
    </div>
    <div class="exchange-counter">
        Negotiation exchanges: <strong>{{ $exchangesUsed }} of {{ $maxExchanges }}</strong> used
        @if ($exchangesUsed >= $maxExchanges)
            <div style="color:#b91c1c; font-weight:600; margin-top:4px;">CayMark has been notified.</div>
        @endif
    </div>
</div>

@include('messaging.modals._third-party-modal')
@include('messaging.modals._delivery-modal')
@include('messaging.modals._location-modal')
@include('messaging.modals._change-modal')
@include('messaging.modals._other-request-modal')
@include('messaging.modals._report-issue-modal')
@if ($isSeller && ! $latestPickup)
    @include('messaging.modals._pickup-form-modal')
@endif
@if ($sellerPinReady)
    @include('messaging.modals._pin-modal')
@endif
