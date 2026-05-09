@php
    /** @var \App\Models\MessagingThreadEvent $event */
    use App\Models\MessagingThreadEvent;
    $payload = $event->payload ?? [];
    $cardClass = match ($event->actor_role) {
        MessagingThreadEvent::ROLE_BUYER => 'from-buyer',
        MessagingThreadEvent::ROLE_SELLER => 'from-seller',
        MessagingThreadEvent::ROLE_SYSTEM, MessagingThreadEvent::ROLE_ADMIN => 'from-system',
        default => 'from-system',
    };
    if (in_array($event->type, [MessagingThreadEvent::TYPE_PICKUP_CONFIRMED, MessagingThreadEvent::TYPE_SALE_COMPLETED_CONFIRMED], true)) {
        $cardClass = 'confirmed';
    }
    $title = match ($event->type) {
        MessagingThreadEvent::TYPE_SCHEDULE_PROPOSED => 'Pickup Schedule Proposed by Seller',
        MessagingThreadEvent::TYPE_SCHEDULE_RESENT => 'Pickup Schedule Resent by Seller',
        MessagingThreadEvent::TYPE_CHANGE_REQUESTED => 'Date / Time Change Requested by Buyer',
        MessagingThreadEvent::TYPE_LOCATION_REQUESTED => 'New Location Requested by Buyer',
        MessagingThreadEvent::TYPE_CHANGE_APPROVED => 'Updated Schedule from Seller',
        MessagingThreadEvent::TYPE_CHANGE_COUNTERED => 'Counter-Offer from Seller',
        MessagingThreadEvent::TYPE_DELIVERY_REQUESTED => 'Delivery Requested by Buyer',
        MessagingThreadEvent::TYPE_DELIVERY_RESPONDED => 'Delivery Request Response',
        MessagingThreadEvent::TYPE_THIRD_PARTY_AUTHORIZED => 'Third-Party Pickup Authorized',
        MessagingThreadEvent::TYPE_PICKUP_CONFIRMED => 'Pickup Confirmed',
        MessagingThreadEvent::TYPE_READY_FOR_PICKUP => 'Vehicle Marked Ready for Pickup',
        MessagingThreadEvent::TYPE_SALE_COMPLETED_CONFIRMED => 'Buyer Confirmed Sale Completed',
        MessagingThreadEvent::TYPE_OTHER_REQUEST => 'Other Request',
        MessagingThreadEvent::TYPE_ISSUE_REPORTED => 'Issue Reported to CayMark Support',
        MessagingThreadEvent::TYPE_ASSISTANCE_REQUESTED => 'CayMark Assistance Requested',
        MessagingThreadEvent::TYPE_ADMIN_FLAGGED => 'Thread Flagged for Admin',
        MessagingThreadEvent::TYPE_ADMIN_UNFLAGGED => 'Admin Cleared Flag',
        default => 'Update',
    };
    $icon = match ($event->type) {
        MessagingThreadEvent::TYPE_SCHEDULE_PROPOSED, MessagingThreadEvent::TYPE_SCHEDULE_RESENT, MessagingThreadEvent::TYPE_CHANGE_APPROVED, MessagingThreadEvent::TYPE_CHANGE_COUNTERED => 'event',
        MessagingThreadEvent::TYPE_CHANGE_REQUESTED => 'edit_calendar',
        MessagingThreadEvent::TYPE_LOCATION_REQUESTED => 'location_on',
        MessagingThreadEvent::TYPE_DELIVERY_REQUESTED, MessagingThreadEvent::TYPE_DELIVERY_RESPONDED => 'local_shipping',
        MessagingThreadEvent::TYPE_THIRD_PARTY_AUTHORIZED => 'group',
        MessagingThreadEvent::TYPE_PICKUP_CONFIRMED => 'check_circle',
        MessagingThreadEvent::TYPE_READY_FOR_PICKUP => 'directions_car',
        MessagingThreadEvent::TYPE_SALE_COMPLETED_CONFIRMED => 'verified',
        MessagingThreadEvent::TYPE_OTHER_REQUEST => 'help_outline',
        MessagingThreadEvent::TYPE_ISSUE_REPORTED => 'flag',
        MessagingThreadEvent::TYPE_ASSISTANCE_REQUESTED => 'support',
        MessagingThreadEvent::TYPE_ADMIN_FLAGGED, MessagingThreadEvent::TYPE_ADMIN_UNFLAGGED => 'shield',
        default => 'forum',
    };
@endphp
<div class="event-card {{ $cardClass }}">
    <div class="event-head">
        <div class="who">
            <span class="material-icons-round">{{ $icon }}</span>
            <span>{{ $title }}</span>
        </div>
        <div class="when">{{ $event->created_at?->format('M d, Y g:i A') }}</div>
    </div>
    <div class="event-body">
        @if (! empty($payload['pickup_date']))
            <div class="field-line"><span class="field-label">Date:</span> {{ \Carbon\Carbon::parse($payload['pickup_date'])->format('l, F d, Y') }}</div>
        @endif
        @if (! empty($payload['pickup_time']))
            <div class="field-line"><span class="field-label">Time:</span> {{ \Carbon\Carbon::parse($payload['pickup_time'])->format('g:i A') }}</div>
        @endif
        @if (! empty($payload['street_address']))
            <div class="field-line"><span class="field-label">Location:</span> {{ $payload['street_address'] }}</div>
        @endif
        @if (! empty($payload['requested_pickup_date']))
            <div class="field-line"><span class="field-label">New date:</span> {{ \Carbon\Carbon::parse($payload['requested_pickup_date'])->format('l, F d, Y') }}</div>
        @endif
        @if (! empty($payload['requested_pickup_time']))
            <div class="field-line"><span class="field-label">New time:</span> {{ \Carbon\Carbon::parse($payload['requested_pickup_time'])->format('g:i A') }}</div>
        @endif
        @if (! empty($payload['countered_pickup_date']))
            <div class="field-line"><span class="field-label">Countered date:</span> {{ \Carbon\Carbon::parse($payload['countered_pickup_date'])->format('l, F d, Y') }}</div>
        @endif
        @if (! empty($payload['countered_pickup_time']))
            <div class="field-line"><span class="field-label">Countered time:</span> {{ \Carbon\Carbon::parse($payload['countered_pickup_time'])->format('g:i A') }}</div>
        @endif
        @if (! empty($payload['requested_location']))
            <div class="field-line"><span class="field-label">Proposed location:</span> {{ $payload['requested_location'] }}</div>
        @endif
        @if (! empty($payload['delivery_address']))
            <div class="field-line"><span class="field-label">Delivery address:</span> {{ $payload['delivery_address'] }}</div>
        @endif
        @if (! empty($payload['preferred_date']))
            <div class="field-line"><span class="field-label">Preferred date:</span> {{ \Carbon\Carbon::parse($payload['preferred_date'])->format('l, F d, Y') }}</div>
        @endif
        @if (! empty($payload['preferred_time']))
            <div class="field-line"><span class="field-label">Preferred time:</span> {{ \Carbon\Carbon::parse($payload['preferred_time'])->format('g:i A') }}</div>
        @endif
        @if (! empty($payload['authorized_name']))
            <div class="field-line"><span class="field-label">Authorized:</span> {{ $payload['authorized_name'] }} ({{ ucfirst(str_replace('_', ' ', $payload['pickup_type'] ?? '')) }})</div>
        @endif
        @if (! empty($payload['action']))
            <div class="field-line"><span class="field-label">Decision:</span> {{ ucfirst($payload['action']) }}</div>
        @endif
        @if (! empty($payload['response_notes']))
            <div class="field-line"><span class="field-label">Notes:</span> {{ $payload['response_notes'] }}</div>
        @endif
        @if (! empty($payload['additional_notes']) || ! empty($payload['directions_notes']))
            <div class="field-line"><span class="field-label">Notes:</span> {{ $payload['additional_notes'] ?? $payload['directions_notes'] }}</div>
        @endif
        @if (! empty($payload['subject']))
            <div class="field-line"><span class="field-label">Subject:</span> {{ $payload['subject'] }}</div>
        @endif
        @if (! empty($payload['body']))
            <div class="field-line">{{ $payload['body'] }}</div>
        @endif
        @if (! empty($payload['public_ticket_number']))
            <div class="field-line"><span class="field-label">Ticket #</span> {{ $payload['public_ticket_number'] }}</div>
        @endif
        @if (! empty($payload['reason']))
            <div class="field-line"><span class="field-label">Reason:</span> {{ str_replace('_', ' ', $payload['reason']) }}</div>
        @endif
    </div>
</div>
