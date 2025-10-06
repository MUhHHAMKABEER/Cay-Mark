@extends('layouts.Buyer')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://unpkg.com/shepherd.js/dist/css/shepherd.css">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">

<style>
    :root {
        --primary-dark: #0a1930;
        --primary-blue: #1a365d;
        --accent-gold: #d4af37;
        --accent-silver: #c0c0c0;
        --light-bg: #f8fafc;
        --text-dark: #1e293b;
        --text-light: #64748b;
        --buyer-bubble: #e3f2fd;
        --seller-bubble: #f0f4ff;
        --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.08);
        --shadow-medium: 0 8px 30px rgba(0, 0, 0, 0.12);
        --shadow-deep: 0 15px 40px rgba(0, 0, 0, 0.15);
        --border-radius: 12px;
        --transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    /* Main Content Area */
    .main-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* Top Bar */
    .top-bar {
        background: white;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: var(--shadow-soft);
        z-index: 10;
    }

    .top-bar h2 {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--primary-dark);
    }

    .user-menu {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .notification-icon {
        position: relative;
        cursor: pointer;
    }

    .notification-icon i {
        font-size: 1.2rem;
        color: var(--text-light);
    }

    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ef4444;
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 0.7rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--accent-gold) 0%, #f7ef8a 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }

    /* Chat Interface */
    .chat-interface {
        flex: 1;
        display: flex;
        padding: 20px;
        gap: 20px;
        overflow: hidden;
    }

    /* Conversations Panel */
    .conversations-panel {
        width: 350px;
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-soft);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .conversations-header {
        padding: 20px;
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-dark) 100%);
        color: white;
    }

    .conversations-header h3 {
        font-size: 1.2rem;
        margin-bottom: 5px;
    }

    .conversations-header p {
        font-size: 0.85rem;
        opacity: 0.8;
    }

    .conversations-search {
        padding: 15px 20px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .search-box {
        position: relative;
    }

    .search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-light);
    }

    .search-box input {
        width: 100%;
        padding: 10px 10px 10px 35px;
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 20px;
        outline: none;
        font-size: 0.9rem;
        transition: var(--transition);
    }

    .search-box input:focus {
        border-color: var(--accent-gold);
        box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
    }

    .conversations-list {
        flex: 1;
        overflow-y: auto;
        padding: 10px;
    }

    .conversation-item {
        display: flex;
        align-items: center;
        padding: 15px;
        border-radius: var(--border-radius);
        margin-bottom: 10px;
        cursor: pointer;
        transition: var(--transition);
        background: white;
        box-shadow: var(--shadow-soft);
        border-left: 3px solid transparent;
    }

    .conversation-item:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-medium);
        border-left: 3px solid var(--accent-gold);
    }

    .conversation-item.active {
        border-left: 3px solid var(--accent-gold);
        background: rgba(212, 175, 55, 0.05);
    }

    .avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        margin-right: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    .avatar::before {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--accent-gold) 0%, #f7ef8a 100%);
        z-index: -1;
    }

    .avatar.buyer::before {
        background: linear-gradient(135deg, var(--primary-blue) 0%, #6b8cff 100%);
    }

    .avatar.online::after {
        content: '';
        position: absolute;
        width: 12px;
        height: 12px;
        background: #10b981;
        border-radius: 50%;
        bottom: 2px;
        right: 2px;
        border: 2px solid white;
    }

    .conversation-info {
        flex: 1;
        min-width: 0; /* Important for text truncation */
    }

    .conversation-info h3 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .conversation-info p {
        font-size: 0.85rem;
        color: var(--text-light);
        /* Truncate to 2 lines */
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.4;
        max-height: 2.8em; /* 2 lines * line-height */
    }

    .conversation-meta {
        text-align: right;
        font-size: 0.75rem;
        color: var(--text-light);
        flex-shrink: 0;
    }

    .conversation-meta .time {
        margin-bottom: 4px;
        white-space: nowrap;
    }

    .unread-count {
        background: var(--accent-gold);
        color: white;
        border-radius: 10px;
        padding: 2px 6px;
        font-size: 0.7rem;
        display: inline-block;
    }

    /* Chat Area */
    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-soft);
        overflow: hidden;
    }

    .chat-header {
        padding: 20px 24px;
        background: white;
        box-shadow: var(--shadow-soft);
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .chat-user-info {
        display: flex;
        align-items: center;
    }

    .chat-user-info .avatar {
        width: 44px;
        height: 44px;
        margin-right: 12px;
    }

    .user-details h2 {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .user-details p {
        font-size: 0.85rem;
        color: var(--text-light);
    }

    .chat-actions {
        display: flex;
        gap: 12px;
    }

    .action-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.08);
        color: var(--text-light);
        cursor: pointer;
        transition: var(--transition);
    }

    .action-btn:hover {
        background: var(--light-bg);
        color: var(--primary-blue);
        transform: translateY(-2px);
        box-shadow: var(--shadow-soft);
    }

    .messages-container {
        flex: 1;
        padding: 24px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 16px;
        background: var(--light-bg);
        /* Custom scrollbar for messages container */
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, 0.2) rgba(0, 0, 0, 0.05);
    }

    /* Custom scrollbar for messages container */
    .messages-container::-webkit-scrollbar {
        width: 8px;
    }

    .messages-container::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 4px;
    }

    .messages-container::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 4px;
    }

    .messages-container::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.3);
    }

    .message {
        display: flex;
        max-width: 70%;
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .message.buyer {
        align-self: flex-start;
    }

    .message.seller {
        align-self: flex-end;
    }

    .message-bubble {
        padding: 14px 18px;
        border-radius: 18px;
        position: relative;
        box-shadow: var(--shadow-soft);
        transition: var(--transition);
        transform-style: preserve-3d;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .message.buyer .message-bubble {
        background: var(--buyer-bubble);
        border-bottom-left-radius: 4px;
        transform: perspective(500px) translateZ(5px);
    }

    .message.seller .message-bubble {
        background: var(--seller-bubble);
        border-bottom-right-radius: 4px;
        transform: perspective(500px) translateZ(5px);
    }

    .message-bubble:hover {
        transform: perspective(500px) translateZ(10px);
        box-shadow: var(--shadow-medium);
    }

    .message-content {
        margin-bottom: 6px;
        line-height: 1.4;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .message-time {
        font-size: 0.75rem;
        color: var(--text-light);
        text-align: right;
    }

    .message.buyer .message-time {
        text-align: left;
    }

    .input-area {
        padding: 20px 24px;
        background: white;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .message-input {
        flex: 1;
        padding: 14px 18px;
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 24px;
        outline: none;
        font-size: 0.95rem;
        transition: var(--transition);
        background: var(--light-bg);
    }

    .message-input:focus {
        border-color: var(--accent-gold);
        box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
    }

    .input-actions {
        display: flex;
        gap: 8px;
    }

    .icon-btn {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--light-bg);
        border: none;
        color: var(--text-light);
        cursor: pointer;
        transition: var(--transition);
    }

    .icon-btn:hover {
        background: var(--primary-blue);
        color: white;
        transform: translateY(-2px);
        box-shadow: var(--shadow-soft);
    }

    .send-btn {
        background: linear-gradient(135deg, var(--accent-gold) 0%, #f7ef8a 100%);
        color: white;
    }

    .send-btn:hover {
        background: linear-gradient(135deg, #c19b2c 0%, #e6d97a 100%);
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
    }

    /* Date separator */
    .date-separator {
        text-align: center;
        color: #6b7280;
        font-size: 0.85rem;
        margin: 20px 0;
        position: relative;
    }

    .date-separator::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: rgba(0, 0, 0, 0.1);
        z-index: 1;
    }

    .date-separator span {
        background: var(--light-bg);
        padding: 0 12px;
        position: relative;
        z-index: 2;
    }

    /* Mobile Responsiveness */
    @media (max-width: 1024px) {
        .conversations-panel {
            width: 300px;
        }
    }

    @media (max-width: 768px) {
        .seller-sidebar {
            width: 70px;
        }

        .seller-sidebar .logo h1 span,
        .seller-sidebar nav a span {
            display: none;
        }

        .seller-sidebar .logo h1 {
            justify-content: center;
        }

        .seller-sidebar nav a {
            justify-content: center;
            padding: 15px;
        }

        .conversations-panel {
            position: absolute;
            left: 70px;
            top: 0;
            bottom: 0;
            z-index: 90;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .conversations-panel.active {
            transform: translateX(0);
        }

        .chat-area {
            margin-left: 0;
        }

        .menu-toggle {
            display: block;
        }
    }
/* In your CSS file */
.messages-container::-webkit-scrollbar {
    width: 6px;
}

.messages-container::-webkit-scrollbar-thumb {
    background-color: rgba(107, 114, 128, 0.5); /* gray-500 */
    border-radius: 3px;
}

    /* Scrollbar Styling */
    ::-webkit-scrollbar {
        width: 6px;
    }

    ::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.3);
    }
</style>

<body>
<div class="dashboard-container">
    <main class="main-content">
        <div class="chat-interface">
            <!-- Conversations Panel -->
            <div class="conversations-panel">
                <div class="conversations-header">
                    <h3>Conversations</h3>
                    <p>Manage your seller messages</p>
                </div>

                <div class="conversations-search">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input id="conversationSearch" type="text" placeholder="Search conversations...">
                    </div>
                </div>

                <div class="conversations-list" id="conversationsList">
                    @foreach ($chats as $chat)
                        @php
                            $otherUser = auth()->id() === $chat->seller_id ? $chat->buyer : $chat->seller;
                            $lastMessage = $chat->messages->sortBy('created_at')->last();

                            // Truncate the last message if it's too long
                            $truncatedMessage = $lastMessage ? $lastMessage->message : 'No messages yet';
                            if (strlen($truncatedMessage) > 50) {
                                $truncatedMessage = substr($truncatedMessage, 0, 50) . '...';
                            }
                        @endphp

                        <a href="{{ route('buyer.messages', ['chat_id' => $chat->id]) }}"
                           class="conversation-item {{ isset($activeChat) && $chat->id === $activeChat->id ? 'active' : '' }}"
                           data-chat-id="{{ $chat->id }}">
                            <div class="avatar seller {{ isset($otherUser->online) && $otherUser->online ? 'online' : '' }}">
                                {{ isset($otherUser->name) ? substr($otherUser->name, 0, 2) : 'U' }}
                            </div>

                            <div class="conversation-info">
                                <h3>{{ $otherUser->name ?? 'Unknown User' }}</h3>
                                <p>{{ $truncatedMessage }}</p>
                            </div>

                            <div class="conversation-meta">
                                <div class="time">{{ $lastMessage?->created_at?->format('h:i A') }}</div>
                                @if (isset($chat->messages) && collect($chat->messages)->where('read', false)->count() > 0)
                                    <div class="unread-count">{{ collect($chat->messages)->where('read', false)->count() }}</div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Chat Area -->
            <div class="chat-area">
                <!-- Chat Header -->
                <div class="chat-header">
                    <div class="chat-user-info">
                        <div class="avatar seller online">
                            {{ isset($activeChat) ? substr($activeChat->seller->name ?? 'NA', 0, 2) : 'NA' }}
                        </div>
                        <div class="user-details">
                            <h2>
                                @if ($activeChat)
                                    {{ $activeChat->seller->name ?? 'Unknown Seller' }}
                                @else
                                    No active chat
                                @endif
                            </h2>
                            <p>
                                @if ($activeChat)
                                    Online • {{ $activeChat->listing->title ?? 'No listing info' }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="chat-actions">
                        <div class="action-btn"><i class="fas fa-phone-alt"></i></div>
                        <div class="action-btn"><i class="fas fa-video"></i></div>
                        <div class="action-btn"><i class="fas fa-ellipsis-v"></i></div>
                    </div>
                </div>

                <!-- Messages -->
               <div class="messages-container overflow-y-auto"
     style="max-height: 500px;"
     id="messagesContainer">
    @if ($activeChat)
        @php
            // ensure messages are sorted ascending by created_at
            $messages = $activeChat->messages->sortBy('created_at')->values();
            $lastDate = null;
        @endphp

        @foreach ($messages as $message)
            @php
                $msgDate = $message->created_at->toDateString();
                if ($lastDate !== $msgDate) {
                    $today = \Carbon\Carbon::today()->toDateString();
                    $yesterday = \Carbon\Carbon::yesterday()->toDateString();
                    if ($msgDate === $today) {
                        $label = 'Today';
                    } elseif ($msgDate === $yesterday) {
                        $label = 'Yesterday — ' . $message->created_at->format('d M Y');
                    } else {
                        $label = $message->created_at->format('d M Y');
                    }
                    $lastDate = $msgDate;
                } else {
                    $label = null;
                }

                $side = $message->sender_id === auth()->id() ? 'seller' : 'buyer';
            @endphp

            @if ($label)
                <div class="date-separator text-gray-500 text-sm my-5 text-center">
                    <span>{{ $label }}</span>
                </div>
            @endif

            <div class="message {{ $side }}" data-msg-id="{{ $message->id }}">
                <div class="message-bubble">
                    <div class="message-content">
                        {{ $message->message }}
                    </div>
                    <div class="message-time text-xs text-gray-400">
                        {{ $message->created_at->format('h:i A') }}
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="p-6 text-gray-500 text-center">
            Select a conversation to start.
        </div>
    @endif
</div>


                <!-- Input Area -->
                <div class="input-area">
                    <div class="input-actions">
                        <button class="icon-btn" id="attachBtn"><i class="fas fa-paperclip"></i></button>
                        <button class="icon-btn" id="emojiBtn"><i class="far fa-smile"></i></button>
                    </div>
                    <input type="text" class="message-input" id="messageInput" placeholder="Type your message..."
                           autocomplete="off" @if (!$activeChat) disabled @endif>
                    <button class="icon-btn send-btn" id="sendBtn" @if (!$activeChat) disabled @endif>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- scripts -->
<script src="{{ asset('js/alpine.js') }}"></script>
<script src="https://unpkg.com/shepherd.js/dist/js/shepherd.min.js"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.12.0/dist/echo.iife.js"></script>

<script>
    const chatId = @json($chatId ?? null);
    const userId = @json(auth()->id());
    const messagesContainer = document.getElementById('messagesContainer');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');

    // Format readable time
    function formatTime(isoString) {
        try {
            const dt = isoString ? new Date(isoString) : new Date();
            let hh = dt.getHours();
            const ampm = hh >= 12 ? 'PM' : 'AM';
            hh = (hh % 12) || 12;
            const mm = String(dt.getMinutes()).padStart(2, '0');
            return `${hh}:${mm} ${ampm}`;
        } catch (e) {
            return '';
        }
    }

    // Append message to DOM — buyer: left (incoming), seller: right (outgoing)
    function appendMessageToDOM(msg, options = {}) {
        const authorId = msg.user_id ?? msg.sender_id ?? null;
        const side = (authorId === userId) ? 'seller' : 'buyer'; // your message = right
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${side}`;
        if (msg.id) messageDiv.dataset.msgId = msg.id;
        if (options.isTemp) messageDiv.dataset.temp = '1';

        const bubble = document.createElement('div');
        bubble.className = 'message-bubble';

        const content = document.createElement('div');
        content.className = 'message-content';
        content.textContent = msg.message ?? '';

        const time = document.createElement('div');
        time.className = 'message-time';
        time.textContent = formatTime(msg.created_at);

        bubble.appendChild(content);
        bubble.appendChild(time);
        messageDiv.appendChild(bubble);

        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        return messageDiv;
    }

    // Send message to backend
    async function sendMessageToServer(chatIdLocal, messageText) {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        const url = `/buyer/messages/${chatIdLocal}/send`; // Buyer endpoint

        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: messageText })
        });

        if (!res.ok) {
            const text = await res.text();
            console.error('Send failed:', res.status, text);
            return null;
        }
        return await res.json();
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (messagesContainer) messagesContainer.scrollTop = messagesContainer.scrollHeight;

        // Send button click
        if (sendBtn) {
            sendBtn.addEventListener('click', async () => {
                if (!chatId) return alert('No active chat selected.');
                const text = messageInput.value.trim();
                if (!text) return;
                sendBtn.disabled = true;

                // Optimistic message display
                const tempEl = appendMessageToDOM({
                    message: text,
                    sender_id: userId,
                    created_at: new Date().toISOString()
                }, { isTemp: true });

                try {
                    const response = await sendMessageToServer(chatId, text);
                    if (response && response.success && response.message) {
                        const msg = response.message;
                        const temps = Array.from(messagesContainer.querySelectorAll('[data-temp="1"]'));
                        const found = temps.reverse().find(el =>
                            el.querySelector('.message-content')?.textContent === text
                        );

                        if (found) {
                            found.removeAttribute('data-temp');
                            if (msg.id) found.dataset.msgId = msg.id;
                            const timeEl = found.querySelector('.message-time');
                            if (timeEl) timeEl.textContent = formatTime(msg.created_at ?? new Date());
                        } else {
                            appendMessageToDOM({
                                id: msg.id,
                                message: msg.message ?? text,
                                sender_id: msg.sender_id ?? userId,
                                created_at: msg.created_at ?? new Date().toISOString()
                            });
                        }
                    } else {
                        console.error('Server returned error', response);
                        tempEl.remove();
                    }
                } catch (err) {
                    console.error('Error sending message:', err);
                    tempEl.remove();
                } finally {
                    messageInput.value = '';
                    sendBtn.disabled = false;
                    messageInput.focus();
                }
            });
        }

        // Send on Enter
        if (messageInput) {
            messageInput.addEventListener('keypress', e => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    sendBtn.click();
                }
            });
        }

        // Realtime updates (Pusher)
        try {
            window.Pusher = Pusher;
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: @json(config('broadcasting.connections.pusher.key')),
                cluster: @json(config('broadcasting.connections.pusher.options.cluster') ?? env('PUSHER_APP_CLUSTER', '')),
                forceTLS: true,
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }
            });

            if (chatId) {
                window.Echo.private(`chat.${chatId}`)
                    .listen('NewMessageSent', e => {
                        const payload = e.message ?? e;
                        const msg = {
                            id: payload.id ?? payload.message_id ?? null,
                            message: payload.message ?? payload.body ?? '',
                            sender_id: payload.sender_id ?? payload.user_id ?? payload.user?.id ?? null,
                            created_at: payload.created_at ?? new Date().toISOString()
                        };

                        // Prevent duplicates
                        if (msg.id && messagesContainer.querySelector(`[data-msg-id="${msg.id}"]`)) return;

                        // Replace optimistic message if same text
                        const temps = Array.from(messagesContainer.querySelectorAll('[data-temp="1"]'));
                        const found = temps.reverse().find(el =>
                            el.querySelector('.message-content')?.textContent === msg.message
                        );
                        if (found) {
                            if (msg.id) found.dataset.msgId = msg.id;
                            found.removeAttribute('data-temp');
                            const timeEl = found.querySelector('.message-time');
                            if (timeEl) timeEl.textContent = formatTime(msg.created_at);
                            return;
                        }

                        // Otherwise append new incoming
                        appendMessageToDOM(msg);
                    });
            }
        } catch (err) {
            console.warn('Echo/Pusher init failed:', err);
        }
    });
</script>

</body>
@endsection
