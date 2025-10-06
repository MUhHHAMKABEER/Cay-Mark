@extends('layouts.welcome')
@section('content')
@section('title', 'Login - CayMark')
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CayMark</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        },
                        gray: {
                            50: '#f9fafb',
                            100: '#f3f4f6',
                            200: '#e5e7eb',
                            300: '#d1d5db',
                            700: '#374151',
                            800: '#1f2937',
                            900: '#111827',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                        'slide-in-left': 'slideInLeft 0.8s ease-out forwards',
                        'slide-in-right': 'slideInRight 0.8s ease-out forwards',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        slideInLeft: {
                            '0%': { opacity: '0', transform: 'translateX(-20px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' }
                        },
                        slideInRight: {
                            '0%': { opacity: '0', transform: 'translateX(20px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .brand-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }

        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-field {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            color: #374151;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .input-field:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background: white;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            transition: color 0.3s ease;
        }

        .input-field:focus + .input-icon {
            color: #3b82f6;
        }

        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .toggle-password:hover {
            color: #3b82f6;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 10px;
            background: white;
            border: 1px solid #e5e7eb;
            color: #6b7280;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .social-btn:hover {
            background: #f9fafb;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            color: #3b82f6;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .feature-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: rgba(59, 130, 246, 0.1);
            margin-right: 1rem;
            color: #3b82f6;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .checkbox-container input {
            display: none;
        }

        .checkmark {
            width: 20px;
            height: 20px;
            border: 2px solid #d1d5db;
            border-radius: 5px;
            margin-right: 0.5rem;
            position: relative;
            transition: all 0.3s ease;
        }

        .checkbox-container input:checked + .checkmark {
            background: #3b82f6;
            border-color: #3b82f6;
        }

        .checkbox-container input:checked + .checkmark::after {
            content: '';
            position: absolute;
            left: 6px;
            top: 2px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .header {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .footer {
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }

            .brand-section {
                padding: 2rem;
                border-radius: 20px 20px 0 0;
            }

            .form-section {
                border-radius: 0 0 20px 20px;
                margin-top: -20px;
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <!-- Header -->


    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center p-4 py-12">
        <!-- Login Container -->
        <div class="w-full max-w-6xl rounded-2xl overflow-hidden flex login-container animate-fade-in-up">
            <!-- Brand Section -->
            <div class="w-full md:w-2/5 p-8 md:p-12 text-white brand-section brand-gradient animate-slide-in-left">
                <div class="flex flex-col h-full justify-center">
                    <!-- Logo -->
                    <div class="flex items-center mb-8">
                        <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center shadow-lg mr-3">
                            <i class="fas fa-chart-network text-white text-xl"></i>
                        </div>
                        <h1 class="text-2xl font-bold">CayMark</h1>
                    </div>

                    <!-- Welcome Message -->
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">Welcome Back</h2>
                    <p class="text-blue-100 mb-8">Sign in to access your CayMark dashboard</p>

                    <!-- Features List -->
                    <div class="space-y-4 mt-8">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold">Enterprise Security</h3>
                                <p class="text-sm text-blue-100">Bank-level encryption & protection</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-rocket"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold">Lightning Fast</h3>
                                <p class="text-sm text-blue-100">Optimized for performance</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold">Advanced Analytics</h3>
                                <p class="text-sm text-blue-100">Data-driven insights</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="flex mt-12 pt-8 border-t border-blue-400/30">
                        <div class="pr-6 border-r border-blue-400/30">
                            <p class="text-2xl font-bold">15K+</p>
                            <p class="text-sm text-blue-100">Active Users</p>
                        </div>
                        <div class="px-6 border-r border-blue-400/30">
                            <p class="text-2xl font-bold">99.9%</p>
                            <p class="text-sm text-blue-100">Uptime</p>
                        </div>
                        <div class="pl-6">
                            <p class="text-2xl font-bold">24/7</p>
                            <p class="text-sm text-blue-100">Support</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="w-full md:w-3/5 p-8 md:p-12 form-section glass-card animate-slide-in-right">
                <div class="max-w-md mx-auto">
                    <!-- Form Header -->
                    <div class="text-center mb-8">
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Login to Your Account</h2>
                        <p class="text-gray-600 mt-2">Enter your credentials to continue</p>
                    </div>

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" novalidate>
                        @csrf

                        {{-- Session status --}}
                        @if (session('status'))
                            <div class="rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm mb-6">
                                <i class="fas fa-check-circle mr-2"></i> {{ session('status') }}
                            </div>
                        @endif

                        <!-- Email Address -->
                        <div class="input-group">
                            <input id="email" type="email" name="email" required class="input-field" placeholder="Email address" value="{{ old('email') }}" />
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            @error('email')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="input-group">
                            <input id="password" type="password" name="password" required class="input-field" placeholder="Password" />
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div class="toggle-password" id="toggle-password">
                                <i class="fas fa-eye"></i>
                            </div>
                            @error('password')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between mb-6">
                            <label class="checkbox-container">
                                <input id="remember_me" type="checkbox" name="remember" />
                                <span class="checkmark"></span>
                                <span class="text-gray-700 text-sm">Remember me</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a class="text-sm text-blue-500 hover:text-blue-700 font-medium transition duration-150 ease-in-out" href="{{ route('password.request') }}">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <div class="mb-6">
                            <button type="submit" class="btn-primary w-full flex justify-center items-center">
                                <span>Sign In</span>
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Divider -->
                    <div class="relative my-8">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-3 bg-white text-gray-500">Or continue with</span>
                        </div>
                    </div>

                    <!-- Social Login -->
                    <div class="flex justify-center space-x-4 mb-8">
                        <button type="button" class="social-btn">
                            <i class="fab fa-google"></i>
                        </button>
                        <button type="button" class="social-btn">
                            <i class="fab fa-apple"></i>
                        </button>
                        <button type="button" class="social-btn">
                            <i class="fab fa-microsoft"></i>
                        </button>
                    </div>

                    <!-- Sign Up Link -->
                    <div class="text-center text-sm text-gray-600">
                        Don't have an account?
                        <a href="#" class="font-medium text-blue-500 hover:text-blue-700 transition duration-150 ease-in-out ml-1">
                            Sign up for free
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <script>
        // Password visibility toggle
        document.getElementById('toggle-password').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Add focus effects to form inputs
        const inputs = document.querySelectorAll('.input-field');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('ring-2', 'ring-blue-500/30');
            });

            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('ring-2', 'ring-blue-500/30');
            });
        });
    </script>
</body>


@endsection
