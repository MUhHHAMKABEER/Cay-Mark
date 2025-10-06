@extends('layouts.Seller')

@section('content')
    <!-- CSRF token (useful for AJAX and Echo auth) -->
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <link rel="stylesheet" href="https://unpkg.com/shepherd.js/dist/css/shepherd.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
<style>.conversations-list {
    display: flex;
    flex-direction: column;
    max-height: 80vh;
    overflow-y: auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

/* Scrollbar styling */
.conversations-list::-webkit-scrollbar {
    width: 8px;
}
.conversations-list::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #999 0%, #666 100%);
    border-radius: 8px;
}
.conversations-list::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #777 0%, #444 100%);
}
.conversations-list::-webkit-scrollbar-track {
    background: transparent;
}

.conversation-item {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    text-decoration: none;
    color: #333;
    border-bottom: 1px solid #eee;
    transition: background 0.2s ease;
}

.conversation-item:hover {
    background: #f9f9f9;
}

.conversation-item.active {
    background: #eef5ff;
    border-left: 4px solid #007bff;
}

.avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #007bff;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 12px;
    position: relative;
}

.avatar.online::after {
    content: '';
    position: absolute;
    right: 2px;
    bottom: 2px;
    width: 10px;
    height: 10px;
    background: #2ecc71;
    border: 2px solid #fff;
    border-radius: 50%;
}

.conversation-info {
    flex: 1;
    overflow: hidden;
}

.conversation-info h3 {
    font-size: 14px;
    font-weight: 600;
    margin: 0;
    color: #222;
}

.conversation-info .message-preview {
    font-size: 13px;
    color: #666;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 200px;
}

.conversation-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    min-width: 60px;
}

.conversation-meta .time {
    font-size: 12px;
    color: #999;
}

.unread-count {
    background: #ff4757;
    color: white;
    font-size: 11px;
    font-weight: bold;
    border-radius: 10px;
    padding: 2px 6px;
    margin-top: 4px;
}

.no-conversations {
    text-align: center;
    padding: 20px;
    color: #666;
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
                ? collect($chat->messages)->where('read', false)->count()
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
                <div class="time">{{ $lastMessage?->created_at?->format('h:i A') }}</div>
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
                                <div class="avatar buyer online">
                                    {{ isset($activeChat)
                                        ? substr($activeChat->buyer_id == auth()->id() ? $activeChat->seller->name : $activeChat->buyer->name, 0, 2)
                                        : 'MB' }}
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
                            <button class="icon-btn send-btn" id="sendBtn"
                                @if (!$activeChat) disabled @endif>
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
            // close context after short timeout to free resources
            setTimeout(() => { try { ctx.close(); } catch (e) {} }, (duration + 0.1) * 1000);
        } catch (e) {}
    }

    function playSound(kind = 'send') {
        // kind: 'send' or 'receive'
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
        } catch (e) {
            // fallback
        }
        // fallback beep
        if (kind === 'send') playBeep(0.06, 900);
        else playBeep(0.08, 600);
    }

    // helper: format time from ISO string or Date
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

    // Update conversation item in sidebar (preview text, time, unread)
    function updateConversationItemDOM(chatIdLocal, previewText, timeText, isOwnMessage = false) {
        const conv = document.querySelector(`.conversation-item[data-chat-id="${chatIdLocal}"]`);
        if (!conv) return;

        const previewEl = conv.querySelector('.conversation-info p');
        if (previewEl) previewEl.textContent = previewText;

        const timeEl = conv.querySelector('.conversation-meta .time');
        if (timeEl) timeEl.textContent = timeText;

        // unread badge logic: only increment when not active and message is from other user
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
                // active chat — hide unread because the user is viewing it
                if (badge) badge.style.display = 'none';
            }
        } else {
            // message is sent by me -> hide unread indicator (my action clears my own unread)
            if (badge) badge.style.display = 'none';
        }

        // Optionally bring conversation to top for recency. Uncomment if you want this:
        // const parent = conv.parentElement;
        // if (parent) parent.prepend(conv);
    }

    // append message and return created element
    function appendMessageToDOM(messageObj, options = {}) {
        // messageObj: { id, message, user_id, sender_id, created_at }
        const authorId = messageObj.user_id ?? messageObj.sender_id ?? null;
        const side = (authorId === userId) ? 'seller' : 'buyer'; // seller=right (you), buyer=left (other)
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

    // send message to server via AJAX
    async function sendMessageToServer(chatIdLocal, messageText) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const baseUrl = "{{ url('/seller/Seller_Chat') }}"; // keep same as your routes or change to buyer route
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
        // hide unread badge for the currently active (server-side) chat item
        document.querySelectorAll('.conversation-item').forEach(ci => {
            if (ci.classList.contains('active')) {
                const uc = ci.querySelector('.unread-count');
                if (uc) uc.style.display = 'none';
            }
        });

        // send button behavior
        if (sendBtn) {
            sendBtn.addEventListener('click', async function() {
                if (!chatId) return alert('No active chat selected.');
                const text = messageInput.value.trim();
                if (!text) return;
                sendBtn.disabled = true;

                // optimistic UI: add temp message (no id)
                const tempEl = appendMessageToDOM({
                    message: text,
                    user_id: userId,
                    created_at: new Date().toISOString()
                }, { isTemp: true });

                // update sidebar preview/time immediately for sent message
                updateConversationItemDOM(chatId, text, formatTime(new Date().toISOString()), true);

                // play send sound immediately for UX (you can instead play on server confirmation)
                playSound('send');

                // send to server
                try {
                    const response = await sendMessageToServer(chatId, text);
                    if (response && response.success && response.message) {
                        const msg = response.message;
                        // find the last temp element with same content and replace
                        const temps = Array.from(messagesContainer.querySelectorAll('[data-temp="1"]'));
                        const found = temps.reverse().find(el => el.querySelector('.message-content')?.textContent === text);
                        if (found) {
                            found.removeAttribute('data-temp');
                            if (msg.id) found.setAttribute('data-msg-id', msg.id);
                            const timeEl = found.querySelector('.message-time');
                            if (timeEl) timeEl.textContent = formatTime(msg.created_at ?? msg.createdAt ?? new Date().toISOString());
                        } else {
                            // fallback: append server message
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

        // Enter key to send
        if (messageInput) {
            messageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    sendBtn.click();
                }
            });
        }

        // conversation search
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

        // initial scroll
        if (messagesContainer) messagesContainer.scrollTop = messagesContainer.scrollHeight;

        // Initialize Pusher/Echo for receiving broadcasts
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
                        // NewMessageSent broadcastWith returns { message: { ... } }
                        const payload = e.message ?? e;
                        const inner = payload.message ?? payload; // support both shapes

                        const messageText = inner.body ?? inner.message ?? inner.message_text ?? '';
                        const msgId = inner.id ?? inner.message_id ?? null;
                        // prefer sender_id (consistent with your DB), fallback to user_id or nested user.id
                        const sender = inner.sender_id ?? inner.user_id ?? (inner.user && inner.user.id) ?? null;
                        const createdAt = inner.created_at ?? inner.createdAt ?? new Date().toISOString();

                        // if msgId present and an element with same data-msg-id exists => already shown
                        if (msgId && messagesContainer.querySelector(`[data-msg-id="${msgId}"]`)) {
                            // update sidebar preview/time in case it's newer
                            updateConversationItemDOM(chatId, messageText, formatTime(createdAt), sender === userId);
                            return; // duplicate
                        }

                        // try to match to a temp optimistic message and replace if sender == me
                        if (messageText) {
                            const temps = Array.from(messagesContainer.querySelectorAll('[data-temp="1"]'));
                            if (temps.length) {
                                // match by content; choose last-most temp with same text
                                const found = temps.reverse().find(el => el.querySelector('.message-content')?.textContent === messageText);
                                if (found) {
                                    if (msgId) found.setAttribute('data-msg-id', msgId);
                                    found.removeAttribute('data-temp');
                                    const timeEl = found.querySelector('.message-time');
                                    if (timeEl) timeEl.textContent = formatTime(createdAt);
                                    // update conversation preview
                                    updateConversationItemDOM(chatId, messageText, formatTime(createdAt), sender === userId);
                                    // if message from other user play receive sound
                                    if (sender !== userId) playSound('receive');
                                    return;
                                }
                            }
                        }

                        // otherwise append message normally
                        const newEl = appendMessageToDOM({
                            id: msgId,
                            message: messageText,
                            user_id: inner.user_id ?? inner.user?.id ?? null,
                            sender_id: sender,
                            created_at: createdAt
                        });

                        // update conversation preview + unread count
                        updateConversationItemDOM(chatId, messageText, formatTime(createdAt), sender === userId);

                        // play appropriate sound
                        if (sender === userId) {
                            // this is our sent message (e.g. other tab echoed back)
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


    </body>
@endsection
