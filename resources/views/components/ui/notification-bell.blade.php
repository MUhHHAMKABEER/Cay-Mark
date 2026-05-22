@props([
    'user' => null,
    'notifications' => collect(),
    'unreadCount' => 0,
    'notificationsUrl' => '#',
    'markReadUrlTemplate' => null,
])

@php
    $user = $user ?? auth()->user();
    $items = $notifications instanceof \Illuminate\Support\Collection ? $notifications : collect($notifications);
    $badge = $unreadCount > 9 ? '9+' : (string) $unreadCount;
    $markReadTemplate = $markReadUrlTemplate ?? (
        $user && $user->role === 'seller' && \Illuminate\Support\Facades\Route::has('seller.notifications.mark-read')
            ? route('seller.notifications.mark-read', ['id' => '__ID__'])
            : (\Illuminate\Support\Facades\Route::has('buyer.notifications.mark-read')
                ? route('buyer.notifications.mark-read', ['id' => '__ID__'])
                : null)
    );
@endphp

<div
    {{ $attributes->merge(['class' => 'cm-notification-bell']) }}
    data-cm-notification-bell
    @if($markReadTemplate) data-cm-mark-read-url="{{ $markReadTemplate }}" @endif
>
    <button
        type="button"
        class="cm-notification-bell__trigger"
        aria-expanded="false"
        aria-haspopup="true"
        aria-controls="cm-notification-panel-{{ $user?->id ?? 'guest' }}"
        title="Notifications"
    >
        <span class="material-icons-round" aria-hidden="true">notifications</span>
        @if($unreadCount > 0)
            <span class="cm-notification-bell__badge" aria-label="{{ $unreadCount }} unread">{{ $badge }}</span>
        @endif
    </button>

    <div
        id="cm-notification-panel-{{ $user?->id ?? 'guest' }}"
        class="cm-notification-bell__panel"
        role="menu"
        aria-hidden="true"
        hidden
    >
        <div class="cm-notification-bell__panel-head">
            <span class="cm-notification-bell__panel-title">Notifications</span>
            @if($unreadCount > 0)
                <span class="cm-notification-bell__panel-count">{{ $unreadCount }} unread</span>
            @endif
        </div>

        @if($items->isEmpty())
            <p class="cm-notification-bell__empty">No notifications yet.</p>
        @else
            <ul class="cm-notification-bell__list">
                @foreach($items as $notification)
                    @php
                        $message = is_array($notification->data) ? ($notification->data['message'] ?? '') : '';
                        if (! is_string($message) || trim($message) === '') {
                            $message = 'You have a new notification.';
                        }
                        $preview = \Illuminate\Support\Str::limit(trim($message), 72);
                        $isUnread = empty($notification->read_at);
                        $itemUrl = $notificationsUrl;
                    @endphp
                    <li>
                        <a
                            href="{{ $itemUrl }}"
                            class="cm-notification-bell__item {{ $isUnread ? 'is-unread' : '' }}"
                            role="menuitem"
                            data-notification-id="{{ $notification->id }}"
                            @if($isUnread) data-cm-mark-read-on-click @endif
                        >
                            <span class="cm-notification-bell__item-text">{{ $preview }}</span>
                            <time class="cm-notification-bell__item-time" datetime="{{ $notification->created_at?->toIso8601String() }}">
                                {{ $notification->created_at?->diffForHumans() }}
                            </time>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif

        <a href="{{ $notificationsUrl }}" class="cm-notification-bell__view-all">View all notifications</a>
    </div>
</div>
