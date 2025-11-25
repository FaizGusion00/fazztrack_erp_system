@extends('layouts.app')

@section('title', 'Admin Dashboard - Fazztrack')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-user-shield mr-3 text-primary-500"></i>
            Admin Dashboard
        </h1>
        <p class="mt-2 text-gray-600">Payment approval and design review management</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Pending Approvals -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pending Approvals</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_approvals'] }}</p>
                </div>
            </div>
        </div>

        <!-- Design Reviews -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-palette text-purple-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Design Reviews</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['design_reviews'] }}</p>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">RM {{ number_format($stats['total_revenue'], 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Monthly Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">RM {{ number_format($stats['monthly_revenue'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Pending Orders for Approval -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Pending Payment Approvals</h3>
                    <a href="{{ route('orders.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                        View All
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($pending_orders->count() > 0)
                    <div class="space-y-4">
                        @foreach($pending_orders as $order)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-shopping-cart text-yellow-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $order->job_name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $order->client->name }} • Order #{{ $order->order_id }}</p>
                                        <p class="text-sm text-gray-500">RM {{ number_format($order->total_amount, 2) }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('orders.show', $order) }}" 
                                       class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                        Review
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-check-circle text-green-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No pending approvals</h3>
                        <p class="text-gray-500">All orders have been reviewed.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pending Designs for Review -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Pending Design Reviews</h3>
                    <a href="{{ route('designs.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                        View All
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($pending_designs->count() > 0)
                    <div class="space-y-4">
                        @foreach($pending_designs as $design)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-palette text-purple-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $design->order->job_name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $design->order->client->name }} • {{ $design->designer->name }}</p>
                                        <p class="text-sm text-gray-500">Version {{ $design->version }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('designs.show', $design) }}" 
                                       class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                        Review
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-check-circle text-green-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No pending designs</h3>
                        <p class="text-gray-500">All designs have been reviewed.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('orders.index') }}" 
                   class="group relative bg-blue-50 rounded-lg p-4 hover:bg-blue-100 transition-colors">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center group-hover:bg-blue-600 transition-colors">
                                <i class="fas fa-shopping-cart text-white"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-blue-900">Review Orders</p>
                            <p class="text-xs text-blue-600">Approve payments</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('designs.index') }}" 
                   class="group relative bg-purple-50 rounded-lg p-4 hover:bg-purple-100 transition-colors">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center group-hover:bg-purple-600 transition-colors">
                                <i class="fas fa-palette text-white"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-purple-900">Review Designs</p>
                            <p class="text-xs text-purple-600">Approve designs</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('clients.index') }}" 
                   class="group relative bg-green-50 rounded-lg p-4 hover:bg-green-100 transition-colors">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center group-hover:bg-green-600 transition-colors">
                                <i class="fas fa-users text-white"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-green-900">Manage Clients</p>
                            <p class="text-xs text-green-600">View client info</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 