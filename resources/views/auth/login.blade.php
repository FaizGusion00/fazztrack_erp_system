<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fazztrack</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    
    <!-- Data URI Favicon (works immediately) -->
    <link rel="icon" href="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzMiAzMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIj4KICA8ZGVmcz4KICAgIDxsaW5lYXJHcmFkaWVudCBpZD0iZ3JhZDEiIHgxPSIwJSIgeTE9IjAlIiB4Mj0iMTAwJSIgeTI9IjEwMCUiPgogICAgICA8c3RvcCBvZmZzZXQ9IjAlIiBzdHlsZT0ic3RvcC1jb2xvcjojM0I4MkY2O3N0b3Atb3BhY2l0eToxIiAvPgogICAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0eWxlPSJzdG9wLWNvbG9yOiMxRDRFRDg7c3RvcC1vcGFjaXR5OjEiIC8+CiAgICA8L2xpbmVhckdyYWRpZW50PgogIDwvZGVmcz4KICAKICA8Y2lyY2xlIGN4PSIxNiIgY3k9IjE2IiByPSIxNSIgZmlsbD0idXJsKCNncmFkMSkiIHN0cm9rZT0iIzFFNDBBRiIgc3Ryb2tlLXdpZHRoPSIxIi8+CiAgCiAgPHBhdGggZD0iTTggMTIgTDggMjQgTDI0IDI0IEwyNCAxMiBMMjIgMTAgTDIwIDEyIEwxOCAxMCBMMTYgMTIgTDE0IDEwIEwxMiAxMiBMMTAgMTAgWiIgZmlsbD0id2hpdGUiIHN0cm9rZT0iIzFFNDBBRiIgc3Ryb2tlLXdpZHRoPSIwLjUiLz4KICAKICA8cGF0aCBkPSJNMTAgMTAgTDE0IDggTDE2IDggTDE4IDggTDIyIDEwIiBmaWxsPSJub25lIiBzdHJva2U9IiMxRTQwQUYiIHN0cm9rZS13aWR0aD0iMSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+CiAgCiAgPHBhdGggZD0iTTggMTIgTDYgMTQgTDYgMTggTDggMjAiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzFFNDBBRiIgc3Ryb2tlLXdpZHRoPSIxIiBzdHJva2UtbGluZWNhcD0icm91bmQiLz4KICA8cGF0aCBkPSJNMjQgMTIgTDI2IDE0IEwyNiAxOCBMMjQgMjAiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzFFNDBBRiIgc3Ryb2tlLXdpZHRoPSIxIiBzdHJva2UtbGluZWNhcD0icm91bmQiLz4KICAKICA8Y2lyY2xlIGN4PSIxNiIgY3k9IjE4IiByPSIyIiBmaWxsPSIjM0I4MkY2IiBvcGFjaXR5PSIwLjgiLz4KICA8cGF0aCBkPSJNMTQgMTggTDE4IDE4IE0xNiAxNiBMMTYgMjAiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMC44IiBzdHJva2UtbGluZWNhcD0icm91bmQiLz4KICAKICA8Y2lyY2xlIGN4PSIxMiIgY3k9IjE2IiByPSIwLjMiIGZpbGw9IiMzQjgyRjYiIG9wYWNpdHk9IjAuNiIvPgogIDxjaXJjbGUgY3g9IjIwIiBjeT0iMTYiIHI9IjAuMyIgZmlsbD0iIzNCODJGNiIgb3BhY2l0eT0iMC42Ii8+CiAgPGNpcmNsZSBjeD0iMTQiIGN5PSIyMiIgcj0iMC4zIiBmaWxsPSIjM0I4MkY2IiBvcGFjaXR5PSIwLjYiLz4KICA8Y2lyY2xlIGN4PSIxOCIgY3k9IjIyIiByPSIwLjMiIGZpbGw9IiMzQjgyRjYiIG9wYWNpdHk9IjAuNiIvPgogIDwvc3ZnPg==">
    
    <!-- Security Headers -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    <meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self' https: data: 'unsafe-inline' 'unsafe-eval'; img-src 'self' data: https:; font-src 'self' https: data:;">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#1E90FF',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        .animated-gradient {
            background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #f5576c, #4facfe, #00f2fe);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }
        
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            25% { background-position: 100% 50%; }
            50% { background-position: 100% 100%; }
            75% { background-position: 0% 100%; }
        }
        
        .login-card {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .password-toggle {
            transition: all 0.2s ease;
        }
        
        .password-toggle:hover {
            transform: scale(1.05);
        }
        
        .remember-me-checkbox {
            accent-color: #1E90FF;
        }
        
        .security-indicator {
            transition: all 0.3s ease;
        }
        
        .security-indicator.weak { color: #ef4444; }
        .security-indicator.medium { color: #f59e0b; }
        .security-indicator.strong { color: #10b981; }
    </style>
</head>
<body class="animated-gradient min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm sm:max-w-md">
        <!-- Logo and Title -->
        <div class="text-center mb-8">
            <div class="mx-auto h-16 w-16 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mb-4 border border-white/30">
                <i class="fas fa-tshirt text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mb-1 drop-shadow-lg">Fazztrack</h2>
            <p class="text-white/90 text-sm">T-Shirt Printing Management</p>
        </div>

        <!-- Login Form -->
        <div class="login-card rounded-xl p-4 sm:p-6">
            <form method="POST" action="{{ route('login') }}" class="space-y-4" id="loginForm">
                @csrf
                
                <!-- Username Field -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-user mr-1"></i>Username
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" id="username" name="username" value="{{ old('username') }}"
                               class="block w-full pl-9 pr-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('username') border-red-300 @enderror"
                               placeholder="Enter username" required autofocus autocomplete="username">
                    </div>
                    @error('username')
                    <p class="mt-1 text-xs text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-lock mr-1"></i>Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400 text-sm"></i>
                        </div>
                        <input type="password" id="password" name="password"
                               class="block w-full pl-9 pr-12 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('password') border-red-300 @enderror"
                               placeholder="Enter password" required autocomplete="current-password">
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center password-toggle">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="passwordIcon"></i>
                        </button>
                    </div>
                    @error('password')
                    <p class="mt-1 text-xs text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Remember Me & Security Options -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="remember-me-checkbox h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            <i class="fas fa-clock mr-1"></i>Remember me
                        </label>
                    </div>
                </div>

                <!-- Login Button -->
                <div class="pt-2">
                    <button type="submit" id="loginButton"
                            class="group relative w-full flex justify-center py-2.5 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-500 hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt text-primary-300 group-hover:text-primary-200 text-sm"></i>
                        </span>
                        <span id="loginText">Sign In</span>
                        <span id="loginSpinner" class="hidden">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </div>
            </form>

            <!-- Demo Credentials -->
            <div class="mt-6 p-3 glass-effect rounded-lg">
                <div class="flex items-center mb-2">
                    <i class="fas fa-info-circle text-primary-500 mr-2 text-sm"></i>
                    <span class="text-xs font-medium text-gray-700">Demo Credentials</span>
                </div>
                <div class="space-y-1 text-xs text-gray-600">
                    <div class="flex justify-between"><span class="font-medium">SuperAdmin:</span><span>superadmin / admin123</span></div>
                    <div class="flex justify-between"><span class="font-medium">Admin:</span><span>admin / approver123</span></div>
                    <div class="flex justify-between"><span class="font-medium">Sales Manager:</span><span>sales / sales123</span></div>
                    <div class="flex justify-between"><span class="font-medium">Designer:</span><span>designer / designer123</span></div>
                    <div class="flex justify-between"><span class="font-medium">Print:</span><span>print / print123</span></div>
                    <div class="flex justify-between"><span class="font-medium">Press:</span><span>press / press123</span></div>
                    <div class="flex justify-between"><span class="font-medium">Cut:</span><span>cut / cut123</span></div>
                    <div class="flex justify-between"><span class="font-medium">Sew:</span><span>sew / sew123</span></div>
                    <div class="flex justify-between"><span class="font-medium">QC:</span><span>qc / qc1234</span></div>
                    <div class="flex justify-between"><span class="font-medium">Packing:</span><span>packing / packing123</span></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Remember Me functionality with localStorage
        const rememberCheckbox = document.getElementById('remember');
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        
        // Load saved credentials on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedCredentials = localStorage.getItem('fazztrack_credentials');
            if (savedCredentials) {
                try {
                    const credentials = JSON.parse(savedCredentials);
                    if (credentials.username && credentials.remember) {
                        usernameInput.value = credentials.username;
                        rememberCheckbox.checked = true;
                    }
                } catch (e) {
                    console.error('Error loading saved credentials:', e);
                    localStorage.removeItem('fazztrack_credentials');
                }
            }
        });
        
        // Save credentials when form is submitted
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            if (rememberCheckbox.checked) {
                const credentials = {
                    username: usernameInput.value,
                    remember: true,
                    timestamp: new Date().getTime()
                };
                localStorage.setItem('fazztrack_credentials', JSON.stringify(credentials));
            } else {
                localStorage.removeItem('fazztrack_credentials');
            }
        });
        
        // Clear saved credentials when checkbox is unchecked
        rememberCheckbox.addEventListener('change', function() {
            if (!this.checked) {
                localStorage.removeItem('fazztrack_credentials');
            }
        });
        
        // Password Toggle
        const togglePassword = document.getElementById('togglePassword');
        const passwordIcon = document.getElementById('passwordIcon');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            passwordIcon.classList.toggle('fa-eye');
            passwordIcon.classList.toggle('fa-eye-slash');
        });

        // Form Submission with Loading State
        const loginButton = document.getElementById('loginButton');
        const loginText = document.getElementById('loginText');
        const loginSpinner = document.getElementById('loginSpinner');

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            // Prevent double submission
            if (loginButton.disabled) {
                e.preventDefault();
                return;
            }

            // Show loading state
            loginButton.disabled = true;
            loginText.classList.add('hidden');
            loginSpinner.classList.remove('hidden');

            // Re-enable after 5 seconds if no response
            setTimeout(() => {
                if (loginButton.disabled) {
                    loginButton.disabled = false;
                    loginText.classList.remove('hidden');
                    loginSpinner.classList.add('hidden');
                }
            }, 5000);
        });

        // Auto-hide demo credentials on production
        if (window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
            const demoCredentials = document.querySelector('.glass-effect');
            if (demoCredentials) {
                demoCredentials.style.display = 'none';
            }
        }

        // Security: Prevent form resubmission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        // Security: Clear sensitive data on page unload (but keep saved credentials)
        window.addEventListener('beforeunload', function() {
            // Only clear password, keep username if remember me is checked
            if (!rememberCheckbox.checked) {
                passwordInput.value = '';
            }
        });
        
        // Auto-logout functionality for saved sessions
        function checkSessionExpiry() {
            const savedCredentials = localStorage.getItem('fazztrack_credentials');
            if (savedCredentials) {
                try {
                    const credentials = JSON.parse(savedCredentials);
                    const now = new Date().getTime();
                    const expiryTime = 30 * 24 * 60 * 60 * 1000; // 30 days
                    
                    if (now - credentials.timestamp > expiryTime) {
                        localStorage.removeItem('fazztrack_credentials');
                        console.log('Saved credentials expired');
                    }
                } catch (e) {
                    localStorage.removeItem('fazztrack_credentials');
                }
            }
        }
        
        // Check session expiry on page load
        checkSessionExpiry();
    </script>
</body>
</html>
