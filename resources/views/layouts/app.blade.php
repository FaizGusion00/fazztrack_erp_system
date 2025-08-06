<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Fazztrack - T-Shirt Printing App')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Data URI Favicon (works immediately) -->
    <link rel="icon" href="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzMiAzMiIgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIj4KICA8ZGVmcz4KICAgIDxsaW5lYXJHcmFkaWVudCBpZD0iZ3JhZDEiIHgxPSIwJSIgeTE9IjAlIiB4Mj0iMTAwJSIgeTI9IjEwMCUiPgogICAgICA8c3RvcCBvZmZzZXQ9IjAlIiBzdHlsZT0ic3RvcC1jb2xvcjojM0I4MkY2O3N0b3Atb3BhY2l0eToxIiAvPgogICAgICA8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0eWxlPSJzdG9wLWNvbG9yOiMxRDRFRDg7c3RvcC1vcGFjaXR5OjEiIC8+CiAgICA8L2xpbmVhckdyYWRpZW50PgogIDwvZGVmcz4KICAKICA8Y2lyY2xlIGN4PSIxNiIgY3k9IjE2IiByPSIxNSIgZmlsbD0idXJsKCNncmFkMSkiIHN0cm9rZT0iIzFFNDBBRiIgc3Ryb2tlLXdpZHRoPSIxIi8+CiAgCiAgPHBhdGggZD0iTTggMTIgTDggMjQgTDI0IDI0IEwyNCAxMiBMMjIgMTAgTDIwIDEyIEwxOCAxMCBMMTYgMTIgTDE0IDEwIEwxMiAxMiBMMTAgMTAgWiIgZmlsbD0id2hpdGUiIHN0cm9rZT0iIzFFNDBBRiIgc3Ryb2tlLXdpZHRoPSIwLjUiLz4KICAKICA8cGF0aCBkPSJNMTAgMTAgTDE0IDggTDE2IDggTDE4IDggTDIyIDEwIiBmaWxsPSJub25lIiBzdHJva2U9IiMxRTQwQUYiIHN0cm9rZS13aWR0aD0iMSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+CiAgCiAgPHBhdGggZD0iTTggMTIgTDYgMTQgTDYgMTggTDggMjAiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzFFNDBBRiIgc3Ryb2tlLXdpZHRoPSIxIiBzdHJva2UtbGluZWNhcD0icm91bmQiLz4KICA8cGF0aCBkPSJNMjQgMTIgTDI2IDE0IEwyNiAxOCBMMjQgMjAiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzFFNDBBRiIgc3Ryb2tlLXdpZHRoPSIxIiBzdHJva2UtbGluZWNhcD0icm91bmQiLz4KICAKICA8Y2lyY2xlIGN4PSIxNiIgY3k9IjE4IiByPSIyIiBmaWxsPSIjM0I4MkY2IiBvcGFjaXR5PSIwLjgiLz4KICA8cGF0aCBkPSJNMTQgMTggTDE4IDE4IE0xNiAxNiBMMTYgMjAiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMC44IiBzdHJva2UtbGluZWNhcD0icm91bmQiLz4KICAKICA8Y2lyY2xlIGN4PSIxMiIgY3k9IjE2IiByPSIwLjMiIGZpbGw9IiMzQjgyRjYiIG9wYWNpdHk9IjAuNiIvPgogIDxjaXJjbGUgY3g9IjIwIiBjeT0iMTYiIHI9IjAuMyIgZmlsbD0iIzNCODJGNiIgb3BhY2l0eT0iMC42Ii8+CiAgPGNpcmNsZSBjeD0iMTQiIGN5PSIyMiIgcj0iMC4zIiBmaWxsPSIjM0I4MkY2IiBvcGFjaXR5PSIwLjYiLz4KICA8Y2lyY2xlIGN4PSIxOCIgY3k9IjIyIiByPSIwLjMiIGZpbGw9IiMzQjgyRjYiIG9wYWNpdHk9IjAuNiIvPgogIDwvc3ZnPg==">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="bg-white">
    @auth
        <!-- Navigation -->
        <nav class="bg-white shadow-lg border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ auth()->user()->isSuperAdmin() ? route('dashboard') : (auth()->user()->isAdmin() ? route('admin.admin-dashboard') : (auth()->user()->isSalesManager() ? route('sales.dashboard') : (auth()->user()->isDesigner() ? route('designer.dashboard') : (auth()->user()->isProductionStaff() ? route('production.dashboard') : route('dashboard'))))) }}" class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tshirt text-white text-sm"></i>
                            </div>
                            <span class="text-xl font-bold text-primary-500">Fazztrack</span>
                        </a>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Mobile menu button -->
                        <div class="md:hidden">
                            <button type="button" id="mobile-menu-button" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700">
                                <i class="fas fa-bars text-xl"></i>
                            </button>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden md:flex items-center space-x-4">
                            @if(auth()->user()->isSuperAdmin())
                                <a href="{{ route('clients.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-users mr-1"></i>Clients
                                </a>
                                <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-box mr-1"></i>Products
                                </a>
                                <a href="{{ route('designs.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-palette mr-1"></i>Designs
                                </a>
                                <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-shopping-cart mr-1"></i>Orders
                                </a>
                                <a href="{{ route('jobs.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-tasks mr-1"></i>Jobs
                                </a>
                                <a href="{{ route('users.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-users-cog mr-1"></i>Users
                                </a>
                                <a href="{{ route('reports.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-chart-bar mr-1"></i>Reports
                                </a>
                            @elseif(auth()->user()->isAdmin())
                                <a href="{{ route('clients.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-users mr-1"></i>Clients
                                </a>
                                <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-box mr-1"></i>Products
                                </a>
                                <a href="{{ route('designs.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-palette mr-1"></i>Designs
                                </a>
                                <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-shopping-cart mr-1"></i>Orders
                                </a>
                            @elseif(auth()->user()->isSalesManager())
                                <a href="{{ route('clients.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-users mr-1"></i>Clients
                                </a>
                                <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-box mr-1"></i>Products
                                </a>
                                <a href="{{ route('designs.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-palette mr-1"></i>Designs
                                </a>
                                <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-shopping-cart mr-1"></i>Orders
                                </a>
                                <a href="{{ route('jobs.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-tasks mr-1"></i>Jobs
                                </a>
                                                    @elseif(auth()->user()->isDesigner())
                            <a href="{{ route('designs.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                <i class="fas fa-palette mr-1"></i>Designs
                            </a>
                            <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                <i class="fas fa-shopping-cart mr-1"></i>Orders
                            </a>
                        @elseif(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->isSalesManager())
                            <a href="{{ route('designs.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                <i class="fas fa-palette mr-1"></i>Designs
                            </a>
                            @elseif(auth()->user()->isProductionStaff())
                                <a href="{{ route('jobs.scanner') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-qrcode mr-1"></i>QR Scanner
                                </a>
                                <a href="{{ route('production.offline') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-wifi-slash mr-1"></i>Offline Mode
                                </a>
                            @endif
                        </div>
                        
                        <!-- User Menu -->
                        <div class="relative">
                            <button id="user-menu-button" class="flex items-center space-x-2 text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-primary-500"></i>
                                </div>
                                <span>{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <div class="px-4 py-2 text-xs text-gray-500 border-b">
                                    {{ auth()->user()->role }}
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Mobile Sidebar -->
        <div id="mobile-sidebar" class="fixed inset-0 z-50 hidden">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity" onclick="closeMobileSidebar()"></div>
            
            <!-- Sidebar -->
            <div class="fixed inset-y-0 left-0 flex flex-col w-64 bg-white shadow-xl transform transition-transform duration-300 ease-in-out -translate-x-full" id="sidebar-content">
                <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tshirt text-white text-sm"></i>
                        </div>
                        <span class="text-xl font-bold text-primary-500">Fazztrack</span>
                    </div>
                    <button onclick="closeMobileSidebar()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="flex-1 px-4 py-6 space-y-4">
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-500 hover:bg-primary-50 rounded-md transition-colors">
                            <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                        </a>
                        <a href="{{ route('users.index') }}" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-500 hover:bg-primary-50 rounded-md transition-colors">
                            <i class="fas fa-users mr-3"></i>Users
                        </a>
                        <a href="{{ route('clients.index') }}" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-500 hover:bg-primary-50 rounded-md transition-colors">
                            <i class="fas fa-user-friends mr-3"></i>Clients
                        </a>
                        <a href="{{ route('products.index') }}" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-500 hover:bg-primary-50 rounded-md transition-colors">
                            <i class="fas fa-box mr-3"></i>Products
                        </a>
                        <a href="{{ route('orders.index') }}" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-500 hover:bg-primary-50 rounded-md transition-colors">
                            <i class="fas fa-shopping-cart mr-3"></i>Orders
                        </a>
                        <a href="{{ route('jobs.index') }}" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-500 hover:bg-primary-50 rounded-md transition-colors">
                            <i class="fas fa-tasks mr-3"></i>Jobs
                        </a>
                        <a href="{{ route('designs.index') }}" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-500 hover:bg-primary-50 rounded-md transition-colors">
                            <i class="fas fa-palette mr-3"></i>Designs
                        </a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.admin-dashboard') }}" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-500 hover:bg-primary-50 rounded-md transition-colors">
                            <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                        </a>
                        <a href="{{ route('products.index') }}" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-500 hover:bg-primary-50 rounded-md transition-colors">
                            <i class="fas fa-box mr-3"></i>Products
                        </a>
                        <a href="{{ route('orders.index') }}" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-500 hover:bg-primary-50 rounded-md transition-colors">
                            <i class="fas fa-shopping-cart mr-3"></i>Orders
                        </a>
                        <a href="{{ route('designs.index') }}" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-500 hover:bg-primary-50 rounded-md transition-colors">
                            <i class="fas fa-palette mr-3"></i>Designs
                        </a>
                    @elseif(auth()->user()->isSalesManager())
                        <a href="{{ route('sales.dashboard') }}" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-500 hover:bg-primary-50 rounded-md transition-colors">
                            <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                        </a>
                        <a href="{{ route('clients.index') }}" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-500 hover:bg-primary-50 rounded-md transition-colors">
                            <i class="fas fa-users mr-3"></i>Clients
                        </a>
                        <a href="{{ route('products.index') }}" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-500 hover:bg-primary-50 rounded-md transition-colors">
                            <i class="fas fa-box mr-3"></i>Products
                        </a>
                        <a href="{{ route('orders.index') }}" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-500 hover:bg-primary-50 rounded-md transition-colors">
                            <i class="fas fa-shopping-cart mr-3"></i>Orders
                        </a>
                        <a href="{{ route('designs.index') }}" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-500 hover:bg-primary-50 rounded-md transition-colors">
                            <i class="fas fa-palette mr-3"></i>Designs
                        </a>
                    @elseif(auth()->user()->isDesigner())
                        <a href="{{ route('designer.dashboard') }}" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-500 hover:bg-primary-50 rounded-md transition-colors">
                            <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                        </a>
                        <a href="{{ route('designs.index') }}" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-500 hover:bg-primary-50 rounded-md transition-colors">
                            <i class="fas fa-palette mr-3"></i>Designs
                        </a>
                        <a href="{{ route('orders.index') }}" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-500 hover:bg-primary-50 rounded-md transition-colors">
                            <i class="fas fa-shopping-cart mr-3"></i>Orders
                        </a>
                    @elseif(auth()->user()->isProductionStaff())
                        <a href="{{ route('production.dashboard') }}" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-500 hover:bg-primary-50 rounded-md transition-colors">
                            <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                        </a>
                        <a href="{{ route('jobs.scanner') }}" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-500 hover:bg-primary-50 rounded-md transition-colors">
                            <i class="fas fa-qrcode mr-3"></i>QR Scanner
                        </a>
                        <a href="{{ route('production.offline') }}" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-500 hover:bg-primary-50 rounded-md transition-colors">
                            <i class="fas fa-wifi-slash mr-3"></i>Offline Mode
                        </a>
                    @endif
                    
                    <hr class="border-gray-200">
                    
                    <div class="flex items-center px-3 py-2">
                        <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-primary-500"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->role }}</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('logout') }}" class="px-3">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-3 py-2 text-gray-700 hover:text-red-500 hover:bg-red-50 rounded-md transition-colors">
                            <i class="fas fa-sign-out-alt mr-3"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endauth

    <!-- Main Content -->
    <main class="@auth min-h-screen @endauth">
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-green-500 hover:text-green-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span class="font-medium">Please fix the following errors:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- JavaScript -->
    <script>
        // User menu toggle
        document.getElementById('user-menu-button')?.addEventListener('click', function() {
            const menu = document.getElementById('user-menu');
            menu.classList.toggle('hidden');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('user-menu');
            const button = document.getElementById('user-menu-button');
            
            if (menu && !menu.contains(event.target) && !button.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        // Mobile sidebar functionality
        document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
            openMobileSidebar();
        });

        function openMobileSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const sidebarContent = document.getElementById('sidebar-content');
            
            sidebar.classList.remove('hidden');
            setTimeout(() => {
                sidebarContent.classList.remove('-translate-x-full');
            }, 10);
        }

        function closeMobileSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const sidebarContent = document.getElementById('sidebar-content');
            
            sidebarContent.classList.add('-translate-x-full');
            setTimeout(() => {
                sidebar.classList.add('hidden');
            }, 300);
        }

        // Close sidebar when clicking on a link
        document.querySelectorAll('#mobile-sidebar a').forEach(link => {
            link.addEventListener('click', function() {
                closeMobileSidebar();
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html> 