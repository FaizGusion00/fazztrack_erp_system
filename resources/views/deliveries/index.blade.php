@extends('layouts.app')

@section('title', 'Delivery Management - Fazztrack')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Delivery Management</h1>
                    <p class="text-gray-600">Track deliveries for completed orders - Self Collect & Shipping</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('deliveries.export') }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Export Data
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-truck text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Deliveries</p>
                        <p class="text-2xl font-bold text-gray-900" id="total-deliveries">-</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-gray-900" id="pending-deliveries">-</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Delivered</p>
                        <p class="text-2xl font-bold text-gray-900" id="delivered">-</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Failed</p>
                        <p class="text-2xl font-bold text-gray-900" id="failed-deliveries">-</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Method Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <i class="fas fa-shipping-fast text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Shipping Orders</p>
                        <p class="text-2xl font-bold text-gray-900" id="shipping-orders">-</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-orange-100 rounded-lg">
                        <i class="fas fa-handshake text-orange-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Self Collect</p>
                        <p class="text-2xl font-bold text-gray-900" id="self-collect">-</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <form method="GET" action="{{ route('deliveries.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                           placeholder="Order ID, tracking number, client...">
                </div>
                
                <div>
                    <label for="delivery_status" class="block text-sm font-medium text-gray-700 mb-2">Delivery Status</label>
                    <select id="delivery_status" name="delivery_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">All Status</option>
                        <option value="Pending" {{ request('delivery_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="In Transit" {{ request('delivery_status') == 'In Transit' ? 'selected' : '' }}>In Transit</option>
                        <option value="Delivered" {{ request('delivery_status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="Failed" {{ request('delivery_status') == 'Failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                
                <div>
                    <label for="delivery_method" class="block text-sm font-medium text-gray-700 mb-2">Delivery Method</label>
                    <select id="delivery_method" name="delivery_method" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">All Methods</option>
                        <option value="Self Collect" {{ request('delivery_method') == 'Self Collect' ? 'selected' : '' }}>Self Collect</option>
                        <option value="Shipping" {{ request('delivery_method') == 'Shipping' ? 'selected' : '' }}>Shipping</option>
                    </select>
                </div>
                
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select id="sort" name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="latest_added" {{ request('sort', 'latest_added') == 'latest_added' ? 'selected' : '' }}>Latest Added</option>
                        <option value="latest_updated" {{ request('sort') == 'latest_updated' ? 'selected' : '' }}>Latest Updated</option>
                        <option value="alphabetical" {{ request('sort') == 'alphabetical' ? 'selected' : '' }}>Alphabetical</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Deliveries Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Deliveries</h2>
            </div>

            @if($deliveries->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Order Details
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Delivery Method
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Delivery Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tracking Info
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($deliveries as $delivery)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">#{{ $delivery->order_id }}</div>
                                            <div class="text-sm text-gray-500">{{ $delivery->client->name ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-400">{{ $delivery->product->name ?? 'N/A' }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $methodColors = [
                                                'Self Collect' => 'bg-orange-100 text-orange-800',
                                                'Shipping' => 'bg-purple-100 text-purple-800',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $methodColors[$delivery->delivery_method] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $delivery->delivery_method }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $deliveryColors = [
                                                'Pending' => 'bg-yellow-100 text-yellow-800',
                                                'In Transit' => 'bg-blue-100 text-blue-800',
                                                'Delivered' => 'bg-green-100 text-green-800',
                                                'Failed' => 'bg-red-100 text-red-800',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $deliveryColors[$delivery->delivery_status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $delivery->delivery_status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($delivery->tracking_number)
                                            <div class="text-sm text-gray-900">{{ $delivery->tracking_number }}</div>
                                            @if($delivery->delivery_company)
                                                <div class="text-xs text-gray-500">{{ $delivery->delivery_company }}</div>
                                            @endif
                                        @else
                                            <span class="text-sm text-gray-400">No tracking info</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('deliveries.show', $delivery) }}" 
                                           class="text-primary-600 hover:text-primary-900 transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $deliveries->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <div class="mx-auto h-12 w-12 text-gray-400">
                        <i class="fas fa-truck text-4xl"></i>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No deliveries found</h3>
                    <p class="mt-1 text-sm text-gray-500">No completed orders ready for delivery tracking.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Load delivery statistics
document.addEventListener('DOMContentLoaded', function() {
    fetch('{{ route("deliveries.stats") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-deliveries').textContent = data.total_deliveries;
            document.getElementById('pending-deliveries').textContent = data.pending_deliveries;
            document.getElementById('delivered').textContent = data.delivered;
            document.getElementById('failed-deliveries').textContent = data.failed_deliveries;
            document.getElementById('shipping-orders').textContent = data.shipping_orders;
            document.getElementById('self-collect').textContent = data.self_collect;
        })
        .catch(error => {
            console.error('Error loading delivery statistics:', error);
        });
});
</script>
@endsection 