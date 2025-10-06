<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Video background container */
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .video-background video {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translateX(-50%) translateY(-50%);
            object-fit: cover;
        }

        .video-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);

            /* visually on top but ignore pointer events so clicks go through to page content */
            z-index: 2;
            pointer-events: none;
            touch-action: none;
            /* mobile: allow touches to pass through */
        }




        /* Control buttons for video */
        .video-controls {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 10;
            display: flex;
            gap: 10px;
        }

        .video-controls button {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .video-controls button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {


            .video-controls {
                bottom: 10px;
                right: 10px;
            }
        }
    </style>
</head>

<body>
    <!-- Video Background -->
    <div class="video-background">
        <video id="bgVideo" autoplay muted loop playsinline>
            <source src="{{ asset('video/invideo-ai-1080 Cinematic Porsche on a Snowy Mountain Cl 2025-08-19.mp4') }}"
                type="video/mp4">

        </video>
    </div>

    <!-- Video overlay for better readability -->
    <div class="video-overlay"></div>


    @yield('content')



    <!-- Video controls -->
    <div class="video-controls">
        <button id="playPauseBtn">
            <svg id="playIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                fill="currentColor" style="display: none;">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                    clip-rule="evenodd" />
            </svg>
            <svg id="pauseIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
            </svg>
        </button>
        <button id="muteBtn">
            <svg id="volumeOnIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.984 5.984 0 01-1.757 4.243 1 1 0 01-1.415-1.415A3.984 3.984 0 0013 10a3.983 3.983 0 00-1.172-2.828 1 1 0 010-1.415z"
                    clip-rule="evenodd" />
            </svg>
            <svg id="volumeOffIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                fill="currentColor" style="display: none;">
                <path fill-rule="evenodd"
                    d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM12.293 7.293a1 1 0 011.414 0L15 8.586l1.293-1.293a1 1 0 111.414 1.414L16.414 10l1.293 1.293a1 1 0 01-1.414 1.414L15 11.414l-1.293 1.293a1 1 0 01-1.414-1.414L13.586 10l-1.293-1.293a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    <script>
        // Dynamically position bubbles for floating effect
        document.addEventListener('DOMContentLoaded', function() {
            const bubbles = document.querySelectorAll('.bg-bubbles li');
            bubbles.forEach(bubble => {
                const randomSize = Math.random() * 60 + 20;
                const randomPos = Math.random() * 100;
                const randomDelay = Math.random() * 5;
                const randomDuration = Math.random() * 15 + 10;

                bubble.style.width = `${randomSize}px`;
                bubble.style.height = `${randomSize}px`;
                bubble.style.left = `${randomPos}%`;
                bubble.style.animationDelay = `${randomDelay}s`;
                bubble.style.animationDuration = `${randomDuration}s`;
            });

            // Video controls functionality
            const video = document.getElementById('bgVideo');
            const playPauseBtn = document.getElementById('playPauseBtn');
            const muteBtn = document.getElementById('muteBtn');
            const playIcon = document.getElementById('playIcon');
            const pauseIcon = document.getElementById('pauseIcon');
            const volumeOnIcon = document.getElementById('volumeOnIcon');
            const volumeOffIcon = document.getElementById('volumeOffIcon');

            // Play/Pause functionality
            playPauseBtn.addEventListener('click', function() {
                if (video.paused) {
                    video.play();
                    playIcon.style.display = 'none';
                    pauseIcon.style.display = 'block';
                } else {
                    video.pause();
                    playIcon.style.display = 'block';
                    pauseIcon.style.display = 'none';
                }
            });

            // Mute/Unmute functionality
            muteBtn.addEventListener('click', function() {
                if (video.muted) {
                    video.muted = false;
                    volumeOnIcon.style.display = 'block';
                    volumeOffIcon.style.display = 'none';
                } else {
                    video.muted = true;
                    volumeOnIcon.style.display = 'none';
                    volumeOffIcon.style.display = 'block';
                }
            });

            // Update button states based on video status
            video.addEventListener('play', function() {
                playIcon.style.display = 'none';
                pauseIcon.style.display = 'block';
            });

            video.addEventListener('pause', function() {
                playIcon.style.display = 'block';
                pauseIcon.style.display = 'none';
            });
        });
    </script>
</body>

</html>
