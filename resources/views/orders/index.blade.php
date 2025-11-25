@extends('layouts.app')

@section('title', 'Orders - Fazztrack')

@section('content')
@php
    $currentUser = auth()->user();
    $canManageHold = $currentUser->isSuperAdmin() || $currentUser->isSalesManager();
@endphp
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

    <!-- Tabs & View Toggle -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <nav class="flex space-x-8" aria-label="Tabs">
            <a href="{{ route('orders.index', array_merge(request()->except('tab'), ['tab' => 'active'])) }}" 
               class="border-primary-500 text-primary-600 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'active' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-clock mr-2"></i>
                Active Orders
                <span class="ml-2 bg-primary-100 text-primary-800 text-xs font-medium px-2 py-0.5 rounded-full">{{ $activeCount }}</span>
            </a>
            <a href="{{ route('orders.index', array_merge(request()->except('tab'), ['tab' => 'completed'])) }}" 
               class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'completed' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-check-circle mr-2"></i>
                Completed Orders
                <span class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded-full">{{ $completedCount }}</span>
            </a>
        </nav>
        <div class="mt-3 sm:mt-0 sm:ml-auto flex items-center space-x-2">
            @php $view = request('view', 'table'); @endphp
            <a href="{{ route('orders.index', array_merge(request()->except('page'), ['view' => 'table'])) }}"
               class="px-3 py-2 rounded-md border text-sm font-medium {{ $view === 'table' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                <i class="fas fa-table mr-1"></i> Table
            </a>
            <a href="{{ route('orders.index', array_merge(request()->except('page'), ['view' => 'cards'])) }}"
               class="px-3 py-2 rounded-md border text-sm font-medium {{ $view === 'cards' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                <i class="fas fa-th-large mr-1"></i> Cards
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('orders.index') }}" class="flex flex-col md:flex-row gap-4">
            <input type="hidden" name="tab" value="{{ $activeTab }}">
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
            @if($activeTab === 'active')
            <div class="md:w-48">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="status" name="status" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Status</option>
                    <option value="Order Created" {{ request('status') == 'Order Created' ? 'selected' : '' }}>Order Created</option>
                    <option value="Order Approved" {{ request('status') == 'Order Approved' ? 'selected' : '' }}>Order Approved</option>
                    <option value="Design Review" {{ request('status') == 'Design Review' ? 'selected' : '' }}>Design Review</option>
                    <option value="Design Approved" {{ request('status') == 'Design Approved' ? 'selected' : '' }}>Design Approved</option>
                    <option value="Job Created" {{ request('status') == 'Job Created' ? 'selected' : '' }}>Job Created</option>
                    <option value="Job Start" {{ request('status') == 'Job Start' ? 'selected' : '' }}>Job Start</option>
                    <option value="Job Complete" {{ request('status') == 'Job Complete' ? 'selected' : '' }}>Job Complete</option>
                    <option value="Order Packaging" {{ request('status') == 'Order Packaging' ? 'selected' : '' }}>Order Packaging</option>
                    <option value="Order Finished" {{ request('status') == 'Order Finished' ? 'selected' : '' }}>Order Finished</option>
                    <option value="On Hold" {{ request('status') == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                </select>
            </div>
            @endif
            <div class="md:w-48">
                <label for="delivery_method" class="block text-sm font-medium text-gray-700 mb-2">Delivery</label>
                <select id="delivery_method" name="delivery_method" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Methods</option>
                    <option value="Self Collect" {{ request('delivery_method') == 'Self Collect' ? 'selected' : '' }}>Self Collect</option>
                    <option value="Shipping" {{ request('delivery_method') == 'Shipping' ? 'selected' : '' }}>Shipping</option>
                </select>
            </div>
            <div class="md:w-48">
                <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                <select id="sort" name="sort" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="latest_added" {{ request('sort', 'latest_added') == 'latest_added' ? 'selected' : '' }}>Latest Added</option>
                    <option value="latest_updated" {{ request('sort') == 'latest_updated' ? 'selected' : '' }}>Latest Updated</option>
                    <option value="alphabetical" {{ request('sort') == 'alphabetical' ? 'selected' : '' }}>Alphabetical</option>
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 transition-colors">
                    <i class="fas fa-search mr-1"></i>
                    Search
                </button>
                @if(request('search') || request('status') || request('delivery_method') || request('sort'))
                    <a href="{{ route('orders.index', ['tab' => $activeTab]) }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                        <i class="fas fa-times mr-1"></i>
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    @php $view = request('view', 'table'); @endphp
    @if($view === 'table')
        <!-- Orders Table (default) -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $order->order_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->job_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->client->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->delivery_method }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->status === 'Completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">{{ $order->status }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 mr-2"><i class="fas fa-eye mr-1"></i>View</a>
                                <a href="{{ route('orders.edit', $order) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50"><i class="fas fa-edit mr-1"></i>Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
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
                        @if($order->status === 'Completed')
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-calendar-check w-4 mr-2"></i>
                                <span>Delivered: {{ $order->delivery_date ? $order->delivery_date->format('M d, Y') : 'N/A' }}</span>
                            </div>
                            @if($order->tracking_number)
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-barcode w-4 mr-2"></i>
                                <span>Tracking: {{ $order->tracking_number }}</span>
                            </div>
                            @endif
                        @elseif($order->delivery_status === 'Failed')
                            <div class="flex items-center text-sm text-red-600">
                                <i class="fas fa-exclamation-triangle w-4 mr-2"></i>
                                <span>Delivery Failed</span>
                            </div>
                            @if($order->delivery_notes)
                            <div class="flex items-center text-sm text-red-600">
                                <i class="fas fa-comment w-4 mr-2"></i>
                                <span>{{ Str::limit($order->delivery_notes, 30) }}</span>
                            </div>
                            @endif
                        @else
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-calendar w-4 mr-2"></i>
                                <span>Due: {{ $order->due_date_production->format('M d, Y') }}</span>
                            </div>
                        @endif
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
                                'Completed' => 'bg-green-100 text-green-800',
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
                        @if($order->status === 'Completed')
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                <span>Order Status</span>
                                <span class="text-green-600 font-medium">100% Complete</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: 100%"></div>
                            </div>
                            <div class="mt-2 text-xs text-green-600">
                                <i class="fas fa-check-circle mr-1"></i>
                                Order delivered successfully
                            </div>
                        @else
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                <span>Production Progress</span>
                                @php
                                    $completedJobs = $order->jobs->where('status', 'Completed')->count();
                                    $totalPhases = 5; // PRINT, PRESS, CUT, SEW, QC
                                    $progress = $totalPhases > 0 ? ($completedJobs / $totalPhases) * 100 : 0;
                                @endphp
                                <span class="progress-text">{{ $completedJobs }}/{{ $totalPhases }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="progress-bar bg-primary-500 h-2 rounded-full transition-all duration-300" data-progress="{{ $progress ?? 0 }}"></div>
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-2">
                        <a href="{{ route('orders.show', $order) }}" 
                           class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-primary-300 text-sm font-medium rounded-md text-primary-700 bg-primary-50 hover:bg-primary-100 transition-colors">
                            <i class="fas fa-eye mr-1"></i>
                            View
                        </a>
                        @if(!in_array($order->status, ['Completed', 'Order Finished'], true))
                        <a href="{{ route('orders.edit', $order) }}" 
                           class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <i class="fas fa-edit mr-1"></i>
                            Edit
                        </a>
                        @else
                        <a href="{{ route('deliveries.show', $order) }}" 
                           class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-green-300 text-sm font-medium rounded-md text-green-700 bg-green-50 hover:bg-green-100 transition-colors">
                            <i class="fas fa-truck mr-1"></i>
                            Delivery
                        </a>
                        @endif
                    </div>

                    <!-- Order Actions -->
                    @if($currentUser->isAdmin() || $currentUser->isSuperAdmin() || $currentUser->isSalesManager())
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="flex space-x-2">
                                @if($order->status === 'Order Created' && ($currentUser->isAdmin() || $currentUser->isSuperAdmin()))
                                    <form method="POST" action="{{ route('orders.approve', $order) }}" class="flex-1">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-3 py-2 border border-green-300 text-sm font-medium rounded-md text-green-700 bg-green-50 hover:bg-green-100 transition-colors">
                                            <i class="fas fa-check mr-1"></i>
                                            Approve
                                        </button>
                                    </form>
                                @endif

                                @if($canManageHold && in_array($order->status, $holdEligibleStatuses ?? [], true))
                                    <form method="POST" action="{{ route('orders.hold', $order) }}" class="flex-1">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-3 py-2 border border-yellow-300 text-sm font-medium rounded-md text-yellow-700 bg-yellow-50 hover:bg-yellow-100 transition-colors">
                                            <i class="fas fa-pause mr-1"></i>
                                            Hold
                                        </button>
                                    </form>
                                @endif

                                @if($canManageHold && $order->status === 'On Hold')
                                    <form method="POST" action="{{ route('orders.resume', $order) }}" class="flex-1">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-3 py-2 border border-blue-300 text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors">
                                            <i class="fas fa-play mr-1"></i>
                                            Resume
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
    @endif

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
    
    // Initialize progress bars
    document.querySelectorAll('[data-progress]').forEach(function(bar) {
        const progress = bar.getAttribute('data-progress');
        if (progress !== null && progress !== '') {
            bar.style.width = progress + '%';
        }
    });
</script>
@endpush
@endsection 