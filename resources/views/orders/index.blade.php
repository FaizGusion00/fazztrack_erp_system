@extends('layouts.app')

@section('title', 'Orders - Fazztrack')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-shopping-cart mr-3 text-primary-500"></i>
                    Orders
                </h1>
                <p class="mt-2 text-gray-600">Manage job orders and track production progress.</p>
            </div>
            <a href="{{ route('orders.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-primary-500 border border-transparent rounded-lg font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Create Order
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('orders.index') }}" class="flex flex-col md:flex-row gap-4">
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
                           placeholder="Search by job name, client, or order ID..."
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
            <div class="md:w-48">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="status" name="status" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Status</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="On Hold" {{ request('status') == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                    <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="md:w-48">
                <label for="delivery_method" class="block text-sm font-medium text-gray-700 mb-2">Delivery</label>
                <select id="delivery_method" name="delivery_method" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Methods</option>
                    <option value="Self Collect" {{ request('delivery_method') == 'Self Collect' ? 'selected' : '' }}>Self Collect</option>
                    <option value="Shipping" {{ request('delivery_method') == 'Shipping' ? 'selected' : '' }}>Shipping</option>
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 transition-colors">
                    <i class="fas fa-search mr-1"></i>
                    Search
                </button>
                @if(request('search') || request('status') || request('delivery_method'))
                    <a href="{{ route('orders.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
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
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow" data-order-id="{{ $order->order_id }}">
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
                            <a href="{{ route('orders.show', $order) }}" 
                               class="text-primary-600 hover:text-primary-700 p-1">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('orders.edit', $order) }}" 
                               class="text-gray-600 hover:text-gray-700 p-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if((auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()) && $order->status === 'Order Created')
                                <form method="POST" action="{{ route('orders.approve', $order) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-700 p-1" title="Approve Payment">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="space-y-3 mb-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-user w-4 mr-2"></i>
                            <span class="truncate">{{ $order->client->name }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-truck w-4 mr-2"></i>
                            <span>{{ $order->delivery_method }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-calendar w-4 mr-2"></i>
                            <span>Due: {{ $order->due_date_production->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-money-bill w-4 mr-2"></i>
                            <span>RM {{ number_format($order->design_deposit + $order->production_deposit + $order->balance_payment, 2) }}</span>
                        </div>
                    </div>

                    <!-- Status and Progress -->
                    <div class="mb-4">
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
                        <span class="order-status inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $order->status }}
                        </span>
                        
                        @if($order->status_comment)
                            <p class="text-xs text-gray-500 mt-1">{{ $order->status_comment }}</p>
                        @endif
                    </div>

                    <!-- Jobs Progress -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                            <span>Production Progress</span>
                            @php
                                $completedJobs = $order->jobs->where('status', 'Completed')->count();
                                $totalPhases = 6; // PRINT, PRESS, CUT, SEW, QC, IRON/PACKING
                                $progress = $totalPhases > 0 ? ($completedJobs / $totalPhases) * 100 : 0;
                            @endphp
                            <span class="progress-text">{{ $completedJobs }}/{{ $totalPhases }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="progress-bar bg-primary-500 h-2 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-2">
                        <a href="{{ route('orders.show', $order) }}" 
                           class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-primary-300 text-sm font-medium rounded-md text-primary-700 bg-primary-50 hover:bg-primary-100 transition-colors">
                            <i class="fas fa-eye mr-1"></i>
                            View
                        </a>
                        <a href="{{ route('orders.edit', $order) }}" 
                           class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <i class="fas fa-edit mr-1"></i>
                            Edit
                        </a>
                    </div>

                    <!-- Admin Actions -->
                    @if(auth()->user()->isAdmin())
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="flex space-x-2">
                                @if($order->status === 'Pending')
                                    <form method="POST" action="{{ route('orders.approve', $order) }}" class="flex-1">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-3 py-2 border border-green-300 text-sm font-medium rounded-md text-green-700 bg-green-50 hover:bg-green-100 transition-colors">
                                            <i class="fas fa-check mr-1"></i>
                                            Approve
                                        </button>
                                    </form>
                                @elseif($order->status === 'Approved')
                                    <form method="POST" action="{{ route('orders.hold', $order) }}" class="flex-1">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-3 py-2 border border-yellow-300 text-sm font-medium rounded-md text-yellow-700 bg-yellow-50 hover:bg-yellow-100 transition-colors">
                                            <i class="fas fa-pause mr-1"></i>
                                            Hold
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="col-span-full">
                <div class="text-center py-12">
                    <div class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-shopping-cart text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No orders yet</h3>
                    <p class="text-gray-500 mb-6">Create your first order to get started with production.</p>
                    <a href="{{ route('orders.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-primary-500 border border-transparent rounded-md font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Create First Order
                    </a>
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

<script>
// Function to refresh order statuses
function refreshOrderStatuses() {
    const orderCards = document.querySelectorAll('[data-order-id]');
    
    orderCards.forEach(card => {
        const orderId = card.getAttribute('data-order-id');
        const statusElement = card.querySelector('.order-status');
        const progressElement = card.querySelector('.progress-text');
        const progressBar = card.querySelector('.progress-bar');
        
        if (statusElement && progressElement && progressBar) {
            fetch(`/orders/${orderId}/status`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update status badge
                        statusElement.textContent = data.order.status;
                        statusElement.className = `order-status inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                            data.order.status === 'Job Created' ? 'bg-gray-100 text-gray-800' :
                            data.order.status === 'Job Start' ? 'bg-blue-100 text-blue-800' :
                            data.order.status === 'Job Complete' ? 'bg-green-100 text-green-800' :
                            data.order.status === 'Order Finished' ? 'bg-purple-100 text-purple-800' :
                            'bg-yellow-100 text-yellow-800'
                        }`;
                        
                        // Update progress
                        const completedJobs = data.completed_jobs || 0;
                        const totalPhases = 6;
                        const progress = totalPhases > 0 ? (completedJobs / totalPhases) * 100 : 0;
                        
                        progressElement.textContent = `${completedJobs}/${totalPhases}`;
                        progressBar.style.width = `${progress}%`;
                    }
                })
                .catch(error => {
                    console.error('Error refreshing order status:', error);
                });
        }
    });
}

// Set up periodic refresh every 30 seconds
setInterval(refreshOrderStatuses, 30000);

// Also refresh when page becomes visible
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        refreshOrderStatuses();
    }
});
</script>

@push('scripts')
<script>
    // Search functionality
    document.getElementById('search').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const orderCards = document.querySelectorAll('[data-order-name]');
        
        orderCards.forEach(card => {
            const orderName = card.getAttribute('data-order-name').toLowerCase();
            const clientName = card.getAttribute('data-client-name').toLowerCase();
            const orderId = card.getAttribute('data-order-id').toLowerCase();
            
            if (orderName.includes(searchTerm) || clientName.includes(searchTerm) || orderId.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Filter by status
    document.getElementById('status').addEventListener('change', function() {
        const selectedStatus = this.value;
        const orderCards = document.querySelectorAll('[data-order-status]');
        
        orderCards.forEach(card => {
            const orderStatus = card.getAttribute('data-order-status');
            
            if (!selectedStatus || orderStatus === selectedStatus) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Filter by delivery method
    document.getElementById('delivery_method').addEventListener('change', function() {
        const selectedMethod = this.value;
        const orderCards = document.querySelectorAll('[data-delivery-method]');
        
        orderCards.forEach(card => {
            const deliveryMethod = card.getAttribute('data-delivery-method');
            
            if (!selectedMethod || deliveryMethod === selectedMethod) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>
@endpush
@endsection 