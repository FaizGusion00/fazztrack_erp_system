@extends('layouts.app')

@section('title', 'Design Management - Fazztrack')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-palette mr-3 text-primary-500"></i>
            Design Management
        </h1>
        <p class="mt-2 text-gray-600">Upload designs, manage feedback, and track design approval workflow.</p>
    </div>

    <!-- Tabs -->
    <div class="mb-6">
        <nav class="flex space-x-8" aria-label="Tabs">
            <button onclick="showTab('orders')" class="border-primary-500 text-primary-600 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm" id="orders-tab">
                Orders
            </button>
            <button onclick="showTab('completed')" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm" id="completed-tab">
                Completed Designs
            </button>
            @if(auth()->user()->isDesigner())
            <button onclick="showTab('templates')" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm" id="templates-tab">
                Templates
            </button>
            @endif
        </nav>
    </div>

    <!-- Orders Tab Content -->
    <div id="orders-content" class="tab-content">
        <!-- Search and Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('designs.index') }}" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Orders</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search by job name, order ID, or client..."
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 transition-colors">
                        <i class="fas fa-search mr-1"></i>
                        Search
                    </button>
                    @if(request('search'))
                        <a href="{{ route('designs.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                            <i class="fas fa-times mr-1"></i>
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Orders Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($orders as $order)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <!-- Order Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-shopping-cart text-primary-500 text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $order->job_name }}</h3>
                                    <p class="text-sm text-gray-500">Order #{{ $order->order_id }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @php
                                    $statusColors = [
                                        'Order Created' => 'bg-yellow-100 text-yellow-800',
                                        'Order Approved' => 'bg-blue-100 text-blue-800',
                                        'Design Review' => 'bg-purple-100 text-purple-800',
                                        'Design Approved' => 'bg-indigo-100 text-indigo-800',
                                        'Job Created' => 'bg-orange-100 text-orange-800',
                                        'Job Start' => 'bg-primary-100 text-primary-800',
                                        'Job Complete' => 'bg-teal-100 text-teal-800',
                                        'Order Packaging' => 'bg-pink-100 text-pink-800',
                                        'Order Finished' => 'bg-green-100 text-green-800',
                                        'On Hold' => 'bg-red-100 text-red-800'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $order->status }}
                                </span>
                            </div>
                        </div>

                        <!-- Order Details -->
                        <div class="space-y-3 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-user w-4 mr-2"></i>
                                <span class="truncate">{{ $order->client->name }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-calendar w-4 mr-2"></i>
                                <span>Design Due: {{ $order->due_date_design->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-money-bill w-4 mr-2"></i>
                                <span>RM {{ number_format($order->design_deposit, 2) }}</span>
                            </div>
                        </div>

                        <!-- Design History -->
                        <div class="mb-4">
                            @if($order->designs->count() > 0)
                                <div class="space-y-2">
                                    <h4 class="text-sm font-medium text-gray-700">Design History:</h4>
                                    @foreach($order->designs->sortByDesc('created_at')->take(3) as $design)
                                        @php
                                            $designStatusColors = [
                                                'Pending Review' => 'bg-yellow-100 text-yellow-800',
                                                'Approved' => 'bg-green-100 text-green-800',
                                                'Rejected' => 'bg-red-100 text-red-800'
                                            ];
                                        @endphp
                                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded text-xs">
                                            <div class="flex items-center space-x-2">
                                                <span class="font-medium">v{{ $design->version }}</span>
                                                <span class="text-gray-500">by {{ $design->designer->name }}</span>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $designStatusColors[$design->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ $design->status }}
                                                </span>
                                                <a href="{{ route('designs.show', $design) }}" class="text-primary-600 hover:text-primary-700">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($order->designs->count() > 3)
                                        <div class="text-xs text-gray-500 text-center">
                                            +{{ $order->designs->count() - 3 }} more versions
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-sm text-gray-500">No designs uploaded yet</div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-2">
                            @if(auth()->user()->isDesigner())
                                <a href="{{ route('designs.create', $order) }}" 
                                   class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-primary-300 text-sm font-medium rounded-md text-primary-700 bg-primary-50 hover:bg-primary-100 transition-colors">
                                    <i class="fas fa-upload mr-1"></i>
                                    Upload Design
                                </a>
                            @endif
                            @if($order->designs->count() > 0)
                                <a href="{{ route('designs.show', $order->designs->sortByDesc('created_at')->first()) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-12">
                        <i class="fas fa-palette text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No orders requiring designs</h3>
                        <p class="text-gray-500">Orders that need design work will appear here.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- Completed Designs Tab Content -->
    <div id="completed-content" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Completed Designs</h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                @php
                    $completedDesigns = \App\Models\Design::with(['order.client', 'designer'])
                        ->where('status', 'Approved')
                        ->latest()
                        ->get();
                @endphp
                
                @forelse($completedDesigns as $design)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <!-- Design Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-check-circle text-green-500 text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $design->order->job_name }}</h3>
                                        <p class="text-sm text-gray-500">Order #{{ $design->order->order_id }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Approved
                                    </span>
                                </div>
                            </div>

                            <!-- Design Details -->
                            <div class="space-y-3 mb-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-user w-4 mr-2"></i>
                                    <span class="truncate">{{ $design->order->client->name }}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-palette w-4 mr-2"></i>
                                    <span>Designer: {{ $design->designer->name }}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-code-branch w-4 mr-2"></i>
                                    <span>Version: {{ $design->version }}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-calendar w-4 mr-2"></i>
                                    <span>Approved: {{ $design->approved_at ? $design->approved_at->format('M d, Y') : 'N/A' }}</span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('designs.show', $design) }}" 
                                   class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-primary-300 text-sm font-medium rounded-md text-primary-700 bg-primary-50 hover:bg-primary-100 transition-colors">
                                    <i class="fas fa-eye mr-1"></i>
                                    View Design
                                </a>
                                <a href="{{ route('orders.show', $design->order) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-shopping-cart"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="text-center py-12">
                            <i class="fas fa-check-circle text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No completed designs</h3>
                            <p class="text-gray-500">Approved designs will appear here.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Templates Tab Content -->
    @if(auth()->user()->isDesigner())
    <div id="templates-content" class="tab-content hidden">
        <div class="text-center py-12">
            <i class="fas fa-layer-group text-gray-400 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Design Templates</h3>
            <p class="text-gray-500 mb-4">Upload and manage reusable design templates.</p>
            <a href="{{ route('design-templates.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Create Template
            </a>
        </div>
    </div>
    @endif
</div>

<script>
function showTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => content.classList.add('hidden'));
    
    // Remove active class from all tabs
    const tabs = document.querySelectorAll('[id$="-tab"]');
    tabs.forEach(tab => {
        tab.classList.remove('border-primary-500', 'text-primary-600');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById(tabName + '-content').classList.remove('hidden');
    
    // Add active class to selected tab
    document.getElementById(tabName + '-tab').classList.remove('border-transparent', 'text-gray-500');
    document.getElementById(tabName + '-tab').classList.add('border-primary-500', 'text-primary-600');
}
</script>
@endsection 