<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>@yield('title', 'Crowz Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/shepherd.js/dist/css/shepherd.css" />
{{-- @vite(['resources/css/app.css', 'resources/js/app.js'])/ --}}

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #FDFBF8;
            color: #333;
        }

        .main-content {
            padding: 2rem;
            width: calc(100% - 288px);
        }

        .sidebar {
            width: 288px;
            background-color: #F9F1EC;
            padding: 2rem;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
    </style>
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

       /* Ensure the page layout gives chat-area real vertical space */
html, body {
  height: 100%;
}
.dashboard-container {
  min-height: 100vh; /* ensure full viewport height */
}

/* chat interface is already flex; ensure it uses available height */
.chat-interface {
  display: flex;
  height: calc(100vh - 80px); /* adjust 80px if you have header/topbar height */
  gap: 20px;
}

/* IMPORTANT: allow child flex items to shrink (enables inner scrolling) */
.chat-area {
  flex: 1 1 auto;
  display: flex;
  flex-direction: column;
  min-height: 0;     /* <-- critical so .messages-container can overflow */
  overflow: hidden;  /* keep overall chat-area from showing page scrollbar */
}

/* messages container becomes the internal scroller */
.messages-container {
  flex: 1 1 auto;       /* take remaining vertical space */
  min-height: 0;        /* important for proper overflow in nested flex */
  overflow-y: auto;     /* the inner scrollbar */
  -webkit-overflow-scrolling: touch;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  background: var(--light-bg);
}

/* Optional: set a visual max height for small screens if you prefer */
@media (max-width: 768px) {
  .chat-interface { height: calc(100vh - 56px); }
  .messages-container { padding: 16px; }
}

/* Optional scrollbar styling (already in your styles but safe to keep) */
.messages-container::-webkit-scrollbar { width: 8px; }
.messages-container::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.18); border-radius: 4px; }


        /* Dashboard Layout */
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar (existing seller layout) */
        .seller-sidebar {
            width: 250px;
            background: linear-gradient(180deg, var(--primary-dark) 0%, var(--primary-blue) 100%);
            color: white;
            padding: 20px 0;
            box-shadow: var(--shadow-deep);
            z-index: 100;
        }

        .seller-sidebar .logo {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .seller-sidebar .logo h1 {
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .seller-sidebar .logo h1 i {
            color: var(--accent-gold);
        }

        .seller-sidebar nav ul {
            list-style: none;
        }

        .seller-sidebar nav li {
            margin-bottom: 5px;
        }

        .seller-sidebar nav a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: var(--transition);
            gap: 10px;
        }

        .seller-sidebar nav a:hover, .seller-sidebar nav a.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid var(--accent-gold);
        }

        .seller-sidebar nav a i {
            width: 20px;
            text-align: center;
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
        }

        .conversation-info h3 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .conversation-info p {
            font-size: 0.85rem;
            color: var(--text-light);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-meta {
            text-align: right;
            font-size: 0.75rem;
            color: var(--text-light);
        }

        .conversation-meta .time {
            margin-bottom: 4px;
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
</head>

<body class="flex">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />


    {{-- Sidebar --}}
    @include('partials.Sellersidebar')

    {{-- Main Content --}}
    <main class="main-content" style="margin-left: 230px">
        @yield('content')
    </main>

</body>
<script src="https://unpkg.com/shepherd.js/dist/js/shepherd.min.js"></script>

</html>
