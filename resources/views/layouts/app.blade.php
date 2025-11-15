<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Permissions-Policy" content="camera=(self), microphone=(self)">
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
                                <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-shopping-cart mr-1"></i>Orders
                                </a>
                                <a href="{{ route('designs.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-palette mr-1"></i>Designs
                                </a>
                                <a href="{{ route('jobs.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-industry mr-1"></i>Production
                                </a>
                                <a href="{{ route('deliveries.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-truck mr-1"></i>Deliveries
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
                                <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-shopping-cart mr-1"></i>Orders
                                </a>
                                <a href="{{ route('designs.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-palette mr-1"></i>Designs
                                </a>
                                <a href="{{ route('deliveries.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-truck mr-1"></i>Deliveries
                                </a>
                            @elseif(auth()->user()->isSalesManager())
                                <a href="{{ route('clients.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-users mr-1"></i>Clients
                                </a>
                                <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-box mr-1"></i>Products
                                </a>
                                <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-shopping-cart mr-1"></i>Orders
                                </a>
                                <a href="{{ route('designs.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-palette mr-1"></i>Designs
                                </a>
                                <a href="{{ route('jobs.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-industry mr-1"></i>Production
                                </a>
                                                    @elseif(auth()->user()->isDesigner())
                            <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                <i class="fas fa-shopping-cart mr-1"></i>Orders
                            </a>
                            <a href="{{ route('designs.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                <i class="fas fa-palette mr-1"></i>Designs
                            </a>
                        @elseif(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->isSalesManager())
                            <a href="{{ route('designs.index') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                <i class="fas fa-palette mr-1"></i>Designs
                            </a>
                            @elseif(auth()->user()->isProductionStaff())
                                <a href="{{ route('production.dashboard') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                                </a>
                                <a href="{{ route('jobs.scanner') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-qrcode mr-1"></i>QR Scanner
                                </a>
                                {{-- Commented out offline mode --}}
                                {{-- <a href="{{ route('production.offline') }}" class="text-gray-700 hover:text-primary-500 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-wifi-slash mr-1"></i>Offline Mode
                                </a> --}}
                            @endif
                        </div>
                        
                        <!-- User Menu -->
                        <div class="relative">
                            @php
                                $roleIcons = [
                                    'SuperAdmin' => ['icon' => 'fas fa-crown', 'bg' => 'bg-red-100', 'text' => 'text-red-600'],
                                    'Admin' => ['icon' => 'fas fa-user-shield', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
                                    'Sales Manager' => ['icon' => 'fas fa-user-tie', 'bg' => 'bg-purple-100', 'text' => 'text-purple-600'],
                                    'Designer' => ['icon' => 'fas fa-palette', 'bg' => 'bg-green-100', 'text' => 'text-green-600'],
                                    'Production Staff' => ['icon' => 'fas fa-cogs', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-600'],
                                ];
                                $roleConfig = $roleIcons[auth()->user()->role] ?? ['icon' => 'fas fa-user', 'bg' => 'bg-gray-100', 'text' => 'text-gray-600'];
                            @endphp
                            <button id="user-menu-button" class="flex items-center space-x-3 text-gray-700 hover:text-primary-500 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                <div class="w-10 h-10 {{ $roleConfig['bg'] }} rounded-full flex items-center justify-center shadow-md">
                                    <i class="{{ $roleConfig['icon'] }} {{ $roleConfig['text'] }} text-lg"></i>
                                </div>
                                <span class="font-medium">{{ auth()->user()->name }}</span>
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
            <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity" onclick="closeMobileSidebar()"></div>
            
            <!-- Sidebar -->
            <div class="fixed inset-y-0 left-0 flex flex-col w-80 bg-white shadow-2xl transform transition-transform duration-300 ease-in-out -translate-x-full" id="sidebar-content">
                <!-- Header -->
                <div class="flex items-center justify-between h-20 px-6 border-b border-gray-100 bg-gradient-to-r from-primary-500 to-primary-600">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                            <i class="fas fa-tshirt text-white text-lg"></i>
                        </div>
                        <div>
                            <span class="text-xl font-bold text-white">Fazztrack</span>
                            <p class="text-xs text-white/80">Management System</p>
                        </div>
                    </div>
                    <button onclick="closeMobileSidebar()" class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center text-white hover:bg-white/30 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Navigation -->
                <div class="flex-1 px-6 py-8 space-y-2 overflow-y-auto">
                    <!-- Navigation Section -->
                    <div class="mb-8">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 px-2">Navigation</h3>
                        <div class="space-y-1">
                            @if(auth()->user()->isSuperAdmin())
                                <a href="{{ route('dashboard') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-tachometer-alt text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Dashboard</span>
                                </a>
                                <a href="{{ route('users.index') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-users text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Users</span>
                                </a>
                                <a href="{{ route('clients.index') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-user-friends text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Clients</span>
                                </a>
                                <a href="{{ route('products.index') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-box text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Products</span>
                                </a>
                                <a href="{{ route('orders.index') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-shopping-cart text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Orders</span>
                                </a>
                                <a href="{{ route('jobs.index') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-industry text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Production</span>
                                </a>
                                <a href="{{ route('deliveries.index') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-truck text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Deliveries</span>
                                </a>
                                <a href="{{ route('designs.index') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-palette text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Designs</span>
                                </a>
                                <a href="{{ route('reports.index') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-chart-bar text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Reports</span>
                                </a>
                            @elseif(auth()->user()->isAdmin())
                                <a href="{{ route('admin.admin-dashboard') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-tachometer-alt text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Dashboard</span>
                                </a>
                                <a href="{{ route('products.index') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-box text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Products</span>
                                </a>
                                <a href="{{ route('orders.index') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-shopping-cart text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Orders</span>
                                </a>
                                <a href="{{ route('deliveries.index') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-truck text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Deliveries</span>
                                </a>
                                <a href="{{ route('designs.index') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-palette text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Designs</span>
                                </a>
                            @elseif(auth()->user()->isSalesManager())
                                <a href="{{ route('sales.dashboard') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-tachometer-alt text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Dashboard</span>
                                </a>
                                <a href="{{ route('clients.index') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-users text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Clients</span>
                                </a>
                                <a href="{{ route('products.index') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-box text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Products</span>
                                </a>
                                <a href="{{ route('orders.index') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-shopping-cart text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Orders</span>
                                </a>
                                <a href="{{ route('designs.index') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-palette text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Designs</span>
                                </a>
                            @elseif(auth()->user()->isDesigner())
                                <a href="{{ route('designer.dashboard') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-tachometer-alt text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Dashboard</span>
                                </a>
                                <a href="{{ route('designs.index') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-palette text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Designs</span>
                                </a>
                                <a href="{{ route('orders.index') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-shopping-cart text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Orders</span>
                                </a>
                            @elseif(auth()->user()->isProductionStaff())
                                <a href="{{ route('production.dashboard') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-tachometer-alt text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Dashboard</span>
                                </a>
                                <a href="{{ route('jobs.scanner') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-qrcode text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">QR Scanner</span>
                                </a>
                                {{-- Commented out offline mode --}}
                                {{-- <a href="{{ route('production.offline') }}" class="group flex items-center px-4 py-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all duration-200">
                                    <div class="w-10 h-10 bg-gray-100 group-hover:bg-primary-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                        <i class="fas fa-wifi-slash text-gray-600 group-hover:text-primary-600"></i>
                                    </div>
                                    <span class="font-medium">Offline Mode</span>
                                </a> --}}
                            @endif
                        </div>
                    </div>
                    
                    <!-- User Profile Section -->
                    <div class="border-t border-gray-100 pt-6">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 px-2">Account</h3>
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 mb-4">
                            <div class="flex items-center">
                                @php
                                    $roleIcons = [
                                        'SuperAdmin' => ['icon' => 'fas fa-crown', 'bg' => 'bg-red-100', 'text' => 'text-red-600'],
                                        'Admin' => ['icon' => 'fas fa-user-shield', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
                                        'Sales Manager' => ['icon' => 'fas fa-user-tie', 'bg' => 'bg-purple-100', 'text' => 'text-purple-600'],
                                        'Designer' => ['icon' => 'fas fa-palette', 'bg' => 'bg-green-100', 'text' => 'text-green-600'],
                                        'Production Staff' => ['icon' => 'fas fa-cogs', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-600'],
                                    ];
                                    $roleConfig = $roleIcons[auth()->user()->role] ?? ['icon' => 'fas fa-user', 'bg' => 'bg-gray-100', 'text' => 'text-gray-600'];
                                @endphp
                                <div class="w-14 h-14 {{ $roleConfig['bg'] }} rounded-full flex items-center justify-center mr-4 shadow-lg">
                                    <i class="{{ $roleConfig['icon'] }} {{ $roleConfig['text'] }} text-2xl"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-600 font-medium">{{ auth()->user()->role }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ auth()->user()->email }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="group flex items-center w-full px-4 py-3 text-gray-700 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all duration-200">
                                <div class="w-10 h-10 bg-gray-100 group-hover:bg-red-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                                    <i class="fas fa-sign-out-alt text-gray-600 group-hover:text-red-600"></i>
                                </div>
                                <span class="font-medium">Logout</span>
                            </button>
                        </form>
                    </div>
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