@extends('layouts.dashboard')

@section('title', 'Messaging Center - Seller Dashboard')

@section('content')
    <!-- CSRF token (useful for AJAX and Echo auth) -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://unpkg.com/shepherd.js/dist/css/shepherd.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .chat-interface {
            display: flex;
            gap: 1.5rem;
            height: calc(100vh - 120px);
            max-height: 800px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        /* Conversations Panel */
        .conversations-panel {
            width: 350px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .conversations-header {
            padding: 1.5rem;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
        }

        .conversations-header h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0 0 0.25rem 0;
        }

        .conversations-header p {
            font-size: 0.875rem;
            margin: 0;
            opacity: 0.9;
        }

        .conversations-search {
            padding: 1rem 1.5rem;
            background: white;
            border-bottom: 1px solid #e5e7eb;
        }

        .search-box {
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .search-box input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .search-box input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .conversations-list {
            flex: 1;
            overflow-y: auto;
            background: white;
        }

        .conversations-list::-webkit-scrollbar {
            width: 6px;
        }

        .conversations-list::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }

        .conversations-list::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        .conversation-item {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            text-decoration: none;
            color: #1f2937;
            border-bottom: 1px solid #f3f4f6;
            transition: background 0.2s ease;
        }

        .conversation-item:hover {
            background: #f9fafb;
        }

        .conversation-item.active {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
        }

        .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
            margin-right: 1rem;
            position: relative;
            flex-shrink: 0;
        }

        .avatar.online::after {
            content: '';
            position: absolute;
            right: 2px;
            bottom: 2px;
            width: 12px;
            height: 12px;
            background: #10b981;
            border: 2px solid white;
            border-radius: 50%;
        }

        .conversation-info {
            flex: 1;
            overflow: hidden;
            min-width: 0;
        }

        .conversation-info h3 {
            font-size: 0.9375rem;
            font-weight: 600;
            margin: 0 0 0.25rem 0;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-info .message-preview {
            font-size: 0.8125rem;
            color: #6b7280;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-meta {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            min-width: 60px;
            flex-shrink: 0;
        }

        .conversation-meta .time {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-bottom: 0.25rem;
        }

        .unread-count {
            background: #ef4444;
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 10px;
            padding: 0.125rem 0.5rem;
            min-width: 20px;
            text-align: center;
        }

        .no-conversations {
            text-align: center;
            padding: 3rem 1.5rem;
            color: #6b7280;
        }

        /* Chat Area */
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
        }

        .chat-header {
            padding: 1rem 1.5rem;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-details h2 {
            font-size: 1.125rem;
            font-weight: 600;
            margin: 0 0 0.25rem 0;
            color: #111827;
        }

        .user-details p {
            font-size: 0.875rem;
            margin: 0;
            color: #6b7280;
        }

        .chat-actions {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid #e5e7eb;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s;
        }

        .action-btn:hover {
            background: #f9fafb;
            border-color: #d1d5db;
            color: #111827;
        }

        /* Messages Container */
        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            background: #f9fafb;
        }

        .messages-container::-webkit-scrollbar {
            width: 6px;
        }

        .messages-container::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }

        .message {
            display: flex;
            margin-bottom: 1rem;
        }

        .message.seller {
            justify-content: flex-end;
        }

        .message.buyer {
            justify-content: flex-start;
        }

        .message-bubble {
            max-width: 70%;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            position: relative;
        }

        .message.seller .message-bubble {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message.buyer .message-bubble {
            background: white;
            color: #111827;
            border: 1px solid #e5e7eb;
            border-bottom-left-radius: 4px;
        }

        .message-content {
            font-size: 0.9375rem;
            line-height: 1.5;
            margin-bottom: 0.25rem;
        }

        .message-time {
            font-size: 0.75rem;
            opacity: 0.7;
        }

        /* Input Area */
        .input-area {
            padding: 1rem 1.5rem;
            background: white;
            border-top: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .input-actions {
            display: flex;
            gap: 0.5rem;
        }

        .icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid #e5e7eb;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s;
        }

        .icon-btn:hover:not(:disabled) {
            background: #f9fafb;
            border-color: #d1d5db;
            color: #111827;
        }

        .icon-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .message-input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            font-size: 0.9375rem;
            transition: all 0.2s;
        }

        .message-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .message-input:disabled {
            background: #f9fafb;
            cursor: not-allowed;
        }

        .send-btn {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: none;
            color: white;
        }

        .send-btn:hover:not(:disabled) {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .chat-interface {
                flex-direction: column;
                height: auto;
            }

            .conversations-panel {
                width: 100%;
                max-height: 300px;
            }

            .chat-area {
                min-height: 400px;
            }
        }
    </style>

    <div class="p-6">
        <div class="chat-interface">
            <!-- Conversations Panel -->
            <div class="conversations-panel">
                <div class="conversations-header">
                    <h3>Conversations</h3>
                    <p>Manage your customer messages</p>
                </div>

                <div class="conversations-search">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input id="conversationSearch" type="text" placeholder="Search conversations...">
                    </div>
                </div>

                <div class="conversations-list" id="conversationsList">
                    @forelse ($chats as $chat)
                        @php
                            $otherUser = auth()->id() === $chat->buyer_id ? $chat->seller : $chat->buyer;
                            $lastMessage = $chat->messages->last();
                            $unreadCount = isset($chat->messages)
                                ? collect($chat->messages)->where('read', false)->where('sender_id', '!=', auth()->id())->count()
                                : 0;
                        @endphp

                        <a href="{{ route('seller.chat', $chat->id) }}"
                           class="conversation-item {{ isset($activeChat) && $chat->id === $activeChat->id ? 'active' : '' }}"
                           data-chat-id="{{ $chat->id }}">
                            <!-- Avatar -->
                            <div class="avatar buyer {{ isset($otherUser->online) && $otherUser->online ? 'online' : '' }}">
                                {{ isset($otherUser->name) ? strtoupper(substr($otherUser->name, 0, 2)) : 'U' }}
                            </div>

                            <!-- Info -->
                            <div class="conversation-info">
                                <h3>{{ $otherUser->name ?? 'Unknown User' }}</h3>
                                <p class="message-preview">
                                    {{ \Illuminate\Support\Str::limit($lastMessage->message ?? 'No messages yet', 40) }}
                                </p>
                            </div>

                            <!-- Meta -->
                            <div class="conversation-meta">
                                <div class="time">{{ $lastMessage?->created_at?->format('h:i A') ?? '' }}</div>
                                @if ($unreadCount > 0)
                                    <div class="unread-count">{{ $unreadCount }}</div>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="no-conversations">
                            <p>No conversations yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Chat Area -->
            <div class="chat-area">
                <!-- Chat Header -->
                <div class="chat-header">
                    <div class="chat-user-info">
                        <div class="avatar buyer {{ isset($activeChat) && $activeChat->buyer_id != auth()->id() ? 'online' : '' }}">
                            @if ($activeChat)
                                {{ strtoupper(substr(auth()->id() === $activeChat->buyer_id ? $activeChat->seller->name : $activeChat->buyer->name, 0, 2)) }}
                            @else
                                MB
                            @endif
                        </div>
                        <div class="user-details">
                            <h2>
                                @if ($activeChat)
                                    {{ auth()->id() === $activeChat->buyer_id ? $activeChat->seller->name : $activeChat->buyer->name }}
                                @else
                                    No active chat
                                @endif
                            </h2>
                            <p>
                                @if ($activeChat)
                                    @if($activeChat->listing)
                                        {{ $activeChat->listing->year ?? '' }} {{ $activeChat->listing->make ?? '' }} {{ $activeChat->listing->model ?? '' }}
                                    @else
                                        No listing info
                                    @endif
                                @else
                                    Select a conversation to start
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
                <div class="messages-container" id="messagesContainer">
                    @if ($activeChat)
                        @foreach ($activeChat->messages as $message)
                            @php
                                $side = $message->sender_id === auth()->id() ? 'seller' : 'buyer';
                            @endphp
                            <div class="message {{ $side }}" data-msg-id="{{ $message->id }}">
                                <div class="message-bubble">
                                    <div class="message-content">
                                        {{ $message->message }}
                                    </div>
                                    <div class="message-time">
                                        {{ $message->created_at->format('h:i A') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="flex items-center justify-center h-full text-gray-500">
                            <div class="text-center">
                                <i class="fas fa-comments text-4xl mb-4 opacity-50"></i>
                                <p>Select a conversation to start.</p>
                            </div>
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
                    <button class="icon-btn send-btn" id="sendBtn"
                        @if (!$activeChat) disabled @endif>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- scripts -->
    <script src="{{ asset('js/alpine.js') }}"></script>
    <script src="https://unpkg.com/shepherd.js/dist/js/shepherd.min.js"></script>

    <!-- Pusher + Echo -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.12.0/dist/echo.iife.js"></script>
    <script>
        // server-side state
        const chatId = @json($chatId ?? null);
        const userId = @json(auth()->id());

        const messagesContainer = document.getElementById('messagesContainer');
        const messageInput = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');

        // --- Sounds (use files in public/sounds or fallback to WebAudio beep) ---
        const sendSoundSrc = "{{ asset('sounds/send.mp3') }}";
        const receiveSoundSrc = "{{ asset('sounds/receive.mp3') }}";
        let sendAudio, receiveAudio;

        try {
            sendAudio = new Audio(sendSoundSrc); sendAudio.preload = 'auto';
            receiveAudio = new Audio(receiveSoundSrc); receiveAudio.preload = 'auto';
        } catch (e) {
            sendAudio = null;
            receiveAudio = null;
        }

        function playBeep(duration = 0.06, frequency = 800, type = 'sine') {
            try {
                const Ctx = window.AudioContext || window.webkitAudioContext;
                if (!Ctx) return;
                const ctx = new Ctx();
                const o = ctx.createOscillator();
                const g = ctx.createGain();
                o.type = type;
                o.frequency.value = frequency;
                o.connect(g);
                g.connect(ctx.destination);
                const now = ctx.currentTime;
                g.gain.setValueAtTime(0.0001, now);
                g.gain.exponentialRampToValueAtTime(0.12, now + 0.01);
                g.gain.exponentialRampToValueAtTime(0.0001, now + duration);
                o.start(now);
                o.stop(now + duration + 0.02);
                setTimeout(() => { try { ctx.close(); } catch (e) {} }, (duration + 0.1) * 1000);
            } catch (e) {}
        }

        function playSound(kind = 'send') {
            try {
                if (kind === 'send' && sendAudio) {
                    sendAudio.currentTime = 0;
                    sendAudio.play().catch(() => playBeep(0.06, 900));
                    return;
                }
                if (kind === 'receive' && receiveAudio) {
                    receiveAudio.currentTime = 0;
                    receiveAudio.play().catch(() => playBeep(0.08, 600));
                    return;
                }
            } catch (e) {}
            if (kind === 'send') playBeep(0.06, 900);
            else playBeep(0.08, 600);
        }

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

        function updateConversationItemDOM(chatIdLocal, previewText, timeText, isOwnMessage = false) {
            const conv = document.querySelector(`.conversation-item[data-chat-id="${chatIdLocal}"]`);
            if (!conv) return;

            const previewEl = conv.querySelector('.conversation-info p');
            if (previewEl) previewEl.textContent = previewText;

            const timeEl = conv.querySelector('.conversation-meta .time');
            if (timeEl) timeEl.textContent = timeText;

            let badge = conv.querySelector('.unread-count');
            if (!isOwnMessage) {
                if (!conv.classList.contains('active')) {
                    if (badge) {
                        const current = parseInt(badge.textContent) || 0;
                        badge.textContent = current + 1;
                        badge.style.display = 'inline-block';
                    } else {
                        const meta = conv.querySelector('.conversation-meta');
                        if (meta) {
                            badge = document.createElement('div');
                            badge.className = 'unread-count';
                            badge.textContent = '1';
                            meta.appendChild(badge);
                        }
                    }
                } else {
                    if (badge) badge.style.display = 'none';
                }
            } else {
                if (badge) badge.style.display = 'none';
            }
        }

        function appendMessageToDOM(messageObj, options = {}) {
            const authorId = messageObj.user_id ?? messageObj.sender_id ?? null;
            const side = (authorId === userId) ? 'seller' : 'buyer';
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${side}`;
            if (messageObj.id) messageDiv.setAttribute('data-msg-id', messageObj.id);
            if (options.isTemp) messageDiv.setAttribute('data-temp', '1');

            const bubbleDiv = document.createElement('div');
            bubbleDiv.className = 'message-bubble';

            const contentDiv = document.createElement('div');
            contentDiv.className = 'message-content';
            contentDiv.textContent = messageObj.message;

            const timeDiv = document.createElement('div');
            timeDiv.className = 'message-time';
            timeDiv.textContent = formatTime(messageObj.created_at);

            bubbleDiv.appendChild(contentDiv);
            bubbleDiv.appendChild(timeDiv);
            messageDiv.appendChild(bubbleDiv);

            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;

            return messageDiv;
        }

        async function sendMessageToServer(chatIdLocal, messageText) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const baseUrl = "{{ url('/seller/Seller_Chat') }}";
            const url = `${baseUrl}/${chatIdLocal}/message`;

            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    message: messageText
                })
            });

            if (!res.ok) {
                const text = await res.text();
                console.error('Failed to send message:', res.status, text);
                return null;
            }

            return await res.json();
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.conversation-item').forEach(ci => {
                if (ci.classList.contains('active')) {
                    const uc = ci.querySelector('.unread-count');
                    if (uc) uc.style.display = 'none';
                }
            });

            if (sendBtn) {
                sendBtn.addEventListener('click', async function() {
                    if (!chatId) return alert('No active chat selected.');
                    const text = messageInput.value.trim();
                    if (!text) return;
                    sendBtn.disabled = true;

                    const tempEl = appendMessageToDOM({
                        message: text,
                        user_id: userId,
                        created_at: new Date().toISOString()
                    }, { isTemp: true });

                    updateConversationItemDOM(chatId, text, formatTime(new Date().toISOString()), true);
                    playSound('send');

                    try {
                        const response = await sendMessageToServer(chatId, text);
                        if (response && response.success && response.message) {
                            const msg = response.message;
                            const temps = Array.from(messagesContainer.querySelectorAll('[data-temp="1"]'));
                            const found = temps.reverse().find(el => el.querySelector('.message-content')?.textContent === text);
                            if (found) {
                                found.removeAttribute('data-temp');
                                if (msg.id) found.setAttribute('data-msg-id', msg.id);
                                const timeEl = found.querySelector('.message-time');
                                if (timeEl) timeEl.textContent = formatTime(msg.created_at ?? msg.createdAt ?? new Date().toISOString());
                            } else {
                                appendMessageToDOM({
                                    id: msg.id,
                                    message: msg.message ?? msg.body ?? text,
                                    user_id: msg.user_id ?? msg.sender_id ?? userId,
                                    created_at: msg.created_at ?? msg.createdAt
                                });
                            }
                        } else {
                            console.error('Server responded but success !== true', response);
                            tempEl.remove();
                        }
                    } catch (err) {
                        console.error(err);
                        tempEl.remove();
                    } finally {
                        messageInput.value = '';
                        sendBtn.disabled = false;
                        messageInput.focus();
                    }
                });
            }

            if (messageInput) {
                messageInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        sendBtn.click();
                    }
                });
            }

            const convSearch = document.getElementById('conversationSearch');
            if (convSearch) {
                convSearch.addEventListener('input', function() {
                    const q = this.value.toLowerCase();
                    document.querySelectorAll('#conversationsList .conversation-item').forEach(item => {
                        const text = item.textContent.toLowerCase();
                        item.style.display = text.indexOf(q) !== -1 ? '' : 'none';
                    });
                });
            }

            if (messagesContainer) messagesContainer.scrollTop = messagesContainer.scrollHeight;

            try {
                window.Pusher = Pusher;

                window.Echo = new Echo({
                    broadcaster: 'pusher',
                    key: '{{ config("broadcasting.connections.pusher.key") }}',
                    cluster: '{{ config("broadcasting.connections.pusher.options.cluster") ?? (env("PUSHER_APP_CLUSTER") ?? "") }}',
                    forceTLS: true,
                    auth: {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    }
                });

                if (chatId) {
                    window.Echo.private(`chat.${chatId}`)
                        .listen('NewMessageSent', (e) => {
                            const payload = e.message ?? e;
                            const inner = payload.message ?? payload;

                            const messageText = inner.body ?? inner.message ?? inner.message_text ?? '';
                            const msgId = inner.id ?? inner.message_id ?? null;
                            const sender = inner.sender_id ?? inner.user_id ?? (inner.user && inner.user.id) ?? null;
                            const createdAt = inner.created_at ?? inner.createdAt ?? new Date().toISOString();

                            if (msgId && messagesContainer.querySelector(`[data-msg-id="${msgId}"]`)) {
                                updateConversationItemDOM(chatId, messageText, formatTime(createdAt), sender === userId);
                                return;
                            }

                            if (messageText) {
                                const temps = Array.from(messagesContainer.querySelectorAll('[data-temp="1"]'));
                                if (temps.length) {
                                    const found = temps.reverse().find(el => el.querySelector('.message-content')?.textContent === messageText);
                                    if (found) {
                                        if (msgId) found.setAttribute('data-msg-id', msgId);
                                        found.removeAttribute('data-temp');
                                        const timeEl = found.querySelector('.message-time');
                                        if (timeEl) timeEl.textContent = formatTime(createdAt);
                                        updateConversationItemDOM(chatId, messageText, formatTime(createdAt), sender === userId);
                                        if (sender !== userId) playSound('receive');
                                        return;
                                    }
                                }
                            }

                            const newEl = appendMessageToDOM({
                                id: msgId,
                                message: messageText,
                                user_id: inner.user_id ?? inner.user?.id ?? null,
                                sender_id: sender,
                                created_at: createdAt
                            });

                            updateConversationItemDOM(chatId, messageText, formatTime(createdAt), sender === userId);

                            if (sender === userId) {
                                playSound('send');
                            } else {
                                playSound('receive');
                            }
                        });
                }
            } catch (err) {
                console.warn('Echo/Pusher not initialized:', err);
            }
        });
    </script>
@endsection
