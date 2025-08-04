@extends('layouts.app')

@section('title', 'Designer Dashboard - Fazztrack')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-palette mr-3 text-primary-500"></i>
            Designer Dashboard
        </h1>
        <p class="mt-2 text-gray-600">Design upload, feedback management, and design approval workflow.</p>
    </div>

    <!-- Design Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Pending Designs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-500"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Pending Designs</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Order::where('status', 'Order Approved')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Approved Designs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-500"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Approved Designs</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Order::where('status', 'Design Approved')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Design Feedback -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-comments text-blue-500"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Design Feedback</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Order::where('status', 'On Hold')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-purple-500"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Order::count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Design Work Queue -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-palette mr-2 text-primary-500"></i>
                Design Work Queue
            </h3>
        </div>
        <div class="p-6">
            @php
                $pendingOrders = \App\Models\Order::where('status', 'Order Approved')->with('client')->latest()->get();
            @endphp
            @if($pendingOrders->count() > 0)
                <div class="space-y-4">
                    @foreach($pendingOrders as $order)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-shopping-cart text-primary-500"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $order->job_name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $order->client->name }} • Order #{{ $order->order_id }}</p>
                                        <p class="text-sm text-gray-500">Design Due: {{ $order->due_date_design->format('M d, Y') }}</p>
                                        @if($order->due_date_design->isPast())
                                            <span class="text-red-600 text-xs">Overdue</span>
                                        @elseif($order->due_date_design->diffInDays(now()) <= 3)
                                            <span class="text-yellow-600 text-xs">Due Soon</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">RM {{ number_format($order->design_deposit, 2) }}</p>
                                        <p class="text-xs text-gray-500">Design Deposit</p>
                                    </div>
                                
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-check-circle text-green-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No pending designs</h3>
                    <p class="text-gray-500">All design work has been completed.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Design Management -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-list mr-2 text-primary-500"></i>
                        Recent Orders
                    </h3>
                    <!-- <a href="{{ route('orders.index') }}" 
                       class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                        View All
                    </a> -->
                </div>
            </div>
            <div class="p-6">
                @if(\App\Models\Order::count() > 0)
                    <div class="space-y-4">
                        @foreach(\App\Models\Order::with('client')->latest()->take(5)->get() as $order)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-shopping-cart text-primary-500 text-sm"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900 text-sm">{{ $order->job_name }}</h4>
                                        <p class="text-xs text-gray-600">{{ $order->client->name }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @php
                                        $statusColors = [
                                            'Pending' => 'bg-yellow-100 text-yellow-800',
                                            'Approved' => 'bg-blue-100 text-blue-800',
                                            'On Hold' => 'bg-red-100 text-red-800',
                                            'In Progress' => 'bg-primary-100 text-primary-800',
                                            'Completed' => 'bg-green-100 text-green-800'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $order->status }}
                                    </span>
                                    <!-- <a href="{{ route('orders.show', $order) }}" 
                                       class="text-primary-600 hover:text-primary-700 text-xs">
                                        View
                                    </a> -->
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <i class="fas fa-shopping-cart text-gray-400 text-2xl mb-2"></i>
                        <p class="text-sm text-gray-500">No orders yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Design Status Overview -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-chart-pie mr-2 text-primary-500"></i>
                    Design Status Overview
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @php
                        $designStatuses = [
                            'Pending' => ['count' => \App\Models\Order::where('status', 'Pending')->count(), 'color' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fas fa-clock'],
                            'Approved' => ['count' => \App\Models\Order::where('status', 'Approved')->count(), 'color' => 'bg-blue-100 text-blue-800', 'icon' => 'fas fa-check-circle'],
                            'On Hold' => ['count' => \App\Models\Order::where('status', 'On Hold')->count(), 'color' => 'bg-red-100 text-red-800', 'icon' => 'fas fa-pause'],
                            'Completed' => ['count' => \App\Models\Order::where('status', 'Completed')->count(), 'color' => 'bg-green-100 text-green-800', 'icon' => 'fas fa-check']
                        ];
                    @endphp
                    @foreach($designStatuses as $status => $data)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 {{ $data['color'] }} rounded-lg flex items-center justify-center">
                                    <i class="{{ $data['icon'] }} text-sm"></i>
                                </div>
                                <span class="font-medium text-gray-900">{{ $status }}</span>
                            </div>
                            <span class="text-lg font-bold text-gray-900">{{ $data['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
@endsection 