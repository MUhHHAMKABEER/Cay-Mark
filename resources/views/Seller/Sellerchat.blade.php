@extends('layouts.dashboard')

@section('title', 'Messaging Center - Seller Dashboard')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <style>
        .msg-layout { display: flex; gap: 0; height: calc(100vh - 100px); max-height: 820px; background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); overflow: hidden; border: 1px solid #e5e7eb; }
        .msg-sidebar { width: 320px; min-width: 280px; display: flex; flex-direction: column; background: #fafafa; border-right: 1px solid #e5e7eb; }
        .msg-sidebar-head { padding: 1.25rem 1.5rem; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: #fff; }
        .msg-sidebar-head h1 { font-size: 1.25rem; font-weight: 700; margin: 0 0 0.2rem 0; }
        .msg-sidebar-head p { font-size: 0.8125rem; margin: 0; opacity: 0.92; }
        .msg-search-wrap { padding: 0.75rem 1rem; background: #fff; border-bottom: 1px solid #e5e7eb; }
        .msg-search-wrap input { width: 100%; padding: 0.6rem 0.75rem 0.6rem 2.25rem; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 0.875rem; }
        .msg-search-wrap input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.15); }
        .msg-search-wrap .search-icon { position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); color: #9ca3af; pointer-events: none; }
        .msg-list { flex: 1; overflow-y: auto; }
        .msg-list::-webkit-scrollbar { width: 6px; }
        .msg-list::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
        .msg-conv { display: flex; align-items: center; padding: 0.875rem 1rem; text-decoration: none; color: #111827; border-bottom: 1px solid #f3f4f6; transition: background .15s; }
        .msg-conv:hover { background: #f3f4f6; }
        .msg-conv.active { background: #eff6ff; border-left: 4px solid #3b82f6; }
        .msg-avatar { width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.8125rem; margin-right: 0.75rem; flex-shrink: 0; }
        .msg-conv-info { flex: 1; min-width: 0; }
        .msg-conv-info .name { font-size: 0.9375rem; font-weight: 600; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .msg-conv-info .preview { font-size: 0.8125rem; color: #6b7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 0.15rem; }
        .msg-conv-meta { display: flex; flex-direction: column; align-items: flex-end; flex-shrink: 0; }
        .msg-conv-meta .time { font-size: 0.6875rem; color: #9ca3af; }
        .msg-unread { background: #ef4444; color: #fff; font-size: 0.6875rem; font-weight: 600; border-radius: 10px; padding: 0.1rem 0.4rem; margin-top: 0.2rem; }
        .msg-empty-side { text-align: center; padding: 2.5rem 1rem; color: #6b7280; font-size: 0.9375rem; }
        .msg-main { flex: 1; display: flex; flex-direction: column; background: #fff; min-width: 0; }
        .msg-main-head { padding: 0.875rem 1.25rem; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; background: #fff; }
        .msg-main-head .user-block { display: flex; align-items: center; gap: 0.75rem; }
        .msg-main-head .user-block h2 { font-size: 1rem; font-weight: 600; margin: 0 0 0.1rem 0; color: #111827; }
        .msg-main-head .user-block p { font-size: 0.8125rem; margin: 0; color: #6b7280; }
        .msg-main-head .head-actions { display: flex; gap: 0.35rem; }
        .msg-head-btn { width: 36px; height: 36px; border-radius: 50%; border: 1px solid #e5e7eb; background: #fff; display: flex; align-items: center; justify-content: center; color: #6b7280; cursor: pointer; transition: all .2s; }
        .msg-head-btn:hover { background: #f9fafb; color: #111827; }
        .msg-body { flex: 1; overflow-y: auto; padding: 1rem 1.25rem; background: #f8fafc; }
        .msg-body::-webkit-scrollbar { width: 6px; }
        .msg-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .msg-row { display: flex; margin-bottom: 0.75rem; }
        .msg-row.seller { justify-content: flex-end; }
        .msg-row.buyer { justify-content: flex-start; }
        .msg-bubble { max-width: 72%; padding: 0.65rem 1rem; border-radius: 14px; }
        .msg-row.seller .msg-bubble { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #fff; border-bottom-right-radius: 4px; }
        .msg-row.buyer .msg-bubble { background: #fff; color: #111827; border: 1px solid #e5e7eb; border-bottom-left-radius: 4px; }
        .msg-bubble .text { font-size: 0.9375rem; line-height: 1.45; }
        .msg-bubble .time { font-size: 0.6875rem; opacity: 0.85; margin-top: 0.2rem; }
        .msg-footer { padding: 0.75rem 1rem; border-top: 1px solid #e5e7eb; display: flex; align-items: center; gap: 0.5rem; background: #fff; }
        .msg-footer .btn-icon { width: 38px; height: 38px; border-radius: 50%; border: 1px solid #e5e7eb; background: #fff; display: flex; align-items: center; justify-content: center; color: #6b7280; cursor: pointer; transition: all .2s; }
        .msg-footer .btn-icon:hover:not(:disabled) { background: #f9fafb; color: #111827; }
        .msg-footer .btn-icon:disabled { opacity: 0.5; cursor: not-allowed; }
        .msg-footer input { flex: 1; padding: 0.6rem 1rem; border: 1px solid #e5e7eb; border-radius: 22px; font-size: 0.9375rem; }
        .msg-footer input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.12); }
        .msg-footer input:disabled { background: #f9fafb; cursor: not-allowed; }
        .msg-send { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border: none; color: #fff; }
        .msg-send:hover:not(:disabled) { opacity: 0.95; transform: scale(1.02); }
        .msg-empty-main { display: flex; align-items: center; justify-content: center; height: 100%; min-height: 280px; color: #9ca3af; }
        .msg-empty-main .inner { text-align: center; }
        .msg-empty-main .material-icons-round { font-size: 3.5rem; opacity: 0.5; margin-bottom: 0.5rem; }
        @media (max-width: 768px) { .msg-layout { flex-direction: column; height: auto; max-height: none; } .msg-sidebar { width: 100%; max-height: 260px; } }
    </style>

    <div class="p-4 md:p-6">
        <div class="msg-layout">
            <!-- Sidebar: Conversations -->
            <div class="msg-sidebar">
                <div class="msg-sidebar-head">
                    <h1>Messaging</h1>
                    <p>Manage your customer conversations</p>
                </div>
                <div class="msg-search-wrap">
                    <div class="relative">
                        <span class="material-icons-round search-icon" style="font-size:1.2rem;">search</span>
                        <input id="conversationSearch" type="text" placeholder="Search conversations...">
                    </div>
                </div>
                <div class="msg-list" id="conversationsList">
                    @forelse ($chats as $chat)
                        @php
                            $otherUser = auth()->id() === $chat->buyer_id ? $chat->seller : $chat->buyer;
                            $lastMessage = $chat->messages->last();
                            $unreadCount = $chat->messages ? collect($chat->messages)->where('read', false)->where('sender_id', '!=', auth()->id())->count() : 0;
                        @endphp
                        <a href="{{ route('seller.chat', $chat->id) }}"
                           class="msg-conv {{ isset($activeChat) && $activeChat->id === $chat->id ? 'active' : '' }}"
                           data-chat-id="{{ $chat->id }}">
                            <div class="msg-avatar">{{ $otherUser ? strtoupper(substr($otherUser->name ?? 'U', 0, 2)) : 'U' }}</div>
                            <div class="msg-conv-info">
                                <div class="name">{{ $otherUser->name ?? 'Unknown' }}</div>
                                <div class="preview">{{ $lastMessage ? \Illuminate\Support\Str::limit($lastMessage->message, 38) : 'No messages yet' }}</div>
                            </div>
                            <div class="msg-conv-meta">
                                <span class="time">{{ $lastMessage?->created_at?->format('h:i A') ?? '' }}</span>
                                @if ($unreadCount > 0)<span class="msg-unread">{{ $unreadCount }}</span>@endif
                            </div>
                        </a>
                    @empty
                        <div class="msg-empty-side">No conversations yet.</div>
                    @endforelse
                </div>
            </div>

            <!-- Main: Chat -->
            <div class="msg-main">
                <div class="msg-main-head">
                    <div class="user-block">
                        <div class="msg-avatar">
                            @if ($activeChat)
                                @php $other = auth()->id() === $activeChat->buyer_id ? $activeChat->seller : $activeChat->buyer; @endphp
                                {{ $other ? strtoupper(substr($other->name ?? 'U', 0, 2)) : '—' }}
                            @else
                                —
                            @endif
                        </div>
                        <div>
                            <h2>{{ $activeChat ? (auth()->id() === $activeChat->buyer_id ? $activeChat->seller->name : $activeChat->buyer->name) : 'No active chat' }}</h2>
                            <p>
                                @if ($activeChat && $activeChat->listing)
                                    {{ trim(($activeChat->listing->year ?? '') . ' ' . ($activeChat->listing->make ?? '') . ' ' . ($activeChat->listing->model ?? '')) ?: 'Conversation' }}
                                @else
                                    {{ $activeChat ? 'Conversation' : 'Select a conversation to start' }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="head-actions">
                        <button type="button" class="msg-head-btn" title="More"><span class="material-icons-round" style="font-size:1.2rem;">more_vert</span></button>
                    </div>
                </div>

                <div class="msg-body" id="messagesContainer">
                    @if ($activeChat)
                        @foreach ($activeChat->messages as $message)
                            @php $side = $message->sender_id === auth()->id() ? 'seller' : 'buyer'; @endphp
                            <div class="msg-row {{ $side }}" data-msg-id="{{ $message->id }}">
                                <div class="msg-bubble">
                                    <div class="text">{{ $message->message }}</div>
                                    <div class="time">{{ $message->created_at->format('h:i A') }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="msg-empty-main">
                            <div class="inner">
                                <span class="material-icons-round">chat_bubble_outline</span>
                                <p>Select a conversation to start.</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="msg-footer">
                    <input type="text" id="messageInput" placeholder="Type your message..." autocomplete="off" @if (!$activeChat) disabled @endif>
                    <button type="button" class="btn-icon msg-send" id="sendBtn" @if (!$activeChat) disabled @endif><span class="material-icons-round" style="font-size:1.25rem;">send</span></button>
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
            const conv = document.querySelector(`.msg-conv[data-chat-id="${chatIdLocal}"]`);
            if (!conv) return;

            const previewEl = conv.querySelector('.msg-conv-info .preview');
            if (previewEl) previewEl.textContent = previewText;

            const timeEl = conv.querySelector('.msg-conv-meta .time');
            if (timeEl) timeEl.textContent = timeText;

            let badge = conv.querySelector('.msg-unread');
            if (!isOwnMessage) {
                if (!conv.classList.contains('active')) {
                    if (badge) {
                        const current = parseInt(badge.textContent) || 0;
                        badge.textContent = current + 1;
                        badge.style.display = 'inline-block';
                    } else {
                        const meta = conv.querySelector('.msg-conv-meta');
                        if (meta) {
                            badge = document.createElement('span');
                            badge.className = 'msg-unread';
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
            messageDiv.className = `msg-row ${side}`;
            if (messageObj.id) messageDiv.setAttribute('data-msg-id', messageObj.id);
            if (options.isTemp) messageDiv.setAttribute('data-temp', '1');

            const bubbleDiv = document.createElement('div');
            bubbleDiv.className = 'msg-bubble';

            const contentDiv = document.createElement('div');
            contentDiv.className = 'text';
            contentDiv.textContent = messageObj.message;

            const timeDiv = document.createElement('div');
            timeDiv.className = 'time';
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
            const baseUrl = "{{ url(route('seller.chat')) }}";
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
            document.querySelectorAll('.msg-conv').forEach(ci => {
                if (ci.classList.contains('active')) {
                    const uc = ci.querySelector('.msg-unread');
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
                            const found = temps.reverse().find(el => el.querySelector('.text')?.textContent === text);
                            if (found) {
                                found.removeAttribute('data-temp');
                                if (msg.id) found.setAttribute('data-msg-id', msg.id);
                                const timeEl = found.querySelector('.time');
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
                    document.querySelectorAll('#conversationsList .msg-conv').forEach(item => {
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
                                    const found = temps.reverse().find(el => el.querySelector('.text')?.textContent === messageText);
                                    if (found) {
                                        if (msgId) found.setAttribute('data-msg-id', msgId);
                                        found.removeAttribute('data-temp');
                                        const timeEl = found.querySelector('.time');
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
