<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Order - Fazztrack</title>
    
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
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .animate-pulse-slow {
            animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-sm shadow-sm border-b border-white/20 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/" class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tshirt text-white text-sm"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900">Fazztrack</span>
                </a>
                <div class="flex items-center space-x-4">
                    <!-- Staff login removed for external customer view -->
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20">
            <div class="text-center">
                <!-- Floating Elements -->
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <div class="absolute top-20 left-10 w-20 h-20 bg-blue-200 rounded-full opacity-20 animate-float"></div>
                    <div class="absolute top-40 right-20 w-16 h-16 bg-indigo-200 rounded-full opacity-20 animate-float" style="animation-delay: -2s;"></div>
                    <div class="absolute bottom-20 left-20 w-12 h-12 bg-purple-200 rounded-full opacity-20 animate-float" style="animation-delay: -4s;"></div>
                </div>
                
                <div class="relative z-10">
                    <div class="w-24 h-24 sm:w-32 sm:h-32 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-8 shadow-2xl">
                        <i class="fas fa-search text-white text-3xl sm:text-4xl"></i>
                    </div>
                    
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-900 bg-clip-text text-transparent mb-6">
                        Track Your Order
                    </h1>
                    
                    <p class="text-lg sm:text-xl text-gray-600 max-w-3xl mx-auto mb-12 leading-relaxed">
                        Enter your order ID to track the real-time progress of your T-shirt printing order. 
                        Get instant updates on every phase of production.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Form -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-white/20 p-8 sm:p-12">
            <div class="text-center mb-8">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">Order Tracking</h2>
                <p class="text-gray-600">Find your order by entering the order ID</p>
            </div>

            <form method="POST" action="{{ route('tracking.search.post') }}" class="max-w-md mx-auto">
                @csrf
                
                <div class="mb-6">
                    <label for="order_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Order ID
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-hashtag text-gray-400"></i>
                        </div>
                        <input type="text" 
                               class="block w-full pl-12 pr-4 py-4 text-lg border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 @error('order_id') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror" 
                               id="order_id" 
                               name="order_id" 
                               value="{{ old('order_id') }}" 
                               placeholder="Enter your order ID (e.g., 1, 2, 3...)" 
                               required 
                               autofocus>
                    </div>
                    @error('order_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-4 px-6 rounded-xl font-semibold text-lg hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 shadow-lg transform hover:scale-105">
                    <i class="fas fa-search mr-3"></i>
                    Track Order
                </button>
            </form>

            <div class="text-center mt-6">
                <p class="text-sm text-gray-500 flex items-center justify-center">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Don't have your order ID? Contact our sales team.
                </p>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
            <!-- Real-time Updates -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-white/20 p-6 sm:p-8 text-center group hover:shadow-lg transition-all duration-300">
                <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Real-time Updates</h3>
                <p class="text-gray-600 leading-relaxed">
                    Track your order progress in real-time with detailed status updates. 
                    Get instant notifications when your order moves to the next phase.
                </p>
            </div>

            <!-- Mobile Friendly -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-white/20 p-6 sm:p-8 text-center group hover:shadow-lg transition-all duration-300">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-mobile-alt text-white text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Mobile Friendly</h3>
                <p class="text-gray-600 leading-relaxed">
                    Access your order status from any device, anywhere, anytime. 
                    Responsive design that works perfectly on phones, tablets, and desktops.
                </p>
            </div>

            <!-- Secure Tracking -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-white/20 p-6 sm:p-8 text-center group hover:shadow-lg transition-all duration-300">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-shield-alt text-white text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Secure Tracking</h3>
                <p class="text-gray-600 leading-relaxed">
                    Your order information is secure and only accessible with your order ID. 
                    We protect your data with industry-standard security measures.
                </p>
            </div>
        </div>
    </section>

    <!-- Additional Features -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-white/20 p-8 sm:p-12">
            <div class="text-center mb-8">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">Why Choose Fazztrack?</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Experience the future of order tracking with our advanced production management system.
                </p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tachometer-alt text-blue-600"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Fast Production</h4>
                    <p class="text-sm text-gray-600">Efficient workflow management</p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Quality Assured</h4>
                    <p class="text-sm text-gray-600">Multiple quality check phases</p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-purple-600"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Expert Team</h4>
                    <p class="text-sm text-gray-600">Skilled production staff</p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shipping-fast text-orange-600"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Quick Delivery</h4>
                    <p class="text-sm text-gray-600">Fast turnaround times</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white/80 backdrop-blur-sm border-t border-white/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-tshirt text-white text-sm"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900">Fazztrack</span>
                </div>
                <p class="text-gray-600">
                    T-Shirt Printing Management System
                </p>
            </div>
        </div>
    </footer>
</body>
</html> 