@extends('layouts.app')

@section('title', 'Order Details - Fazztrack')

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
                    Order Details
                </h1>
                <p class="mt-2 text-gray-600">Order #{{ $order->order_id }} - {{ $order->job_name }}</p>
            </div>
            <div class="flex items-center space-x-3">
                @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                <a href="{{ route('orders.edit', $order) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Order
                </a>
                @endif
                <a href="{{ route('orders.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Orders
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-primary-500"></i>
                        Order Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Order ID</h4>
                            <p class="text-lg font-semibold text-gray-900">#{{ $order->order_id }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Job Name</h4>
                            <p class="text-lg font-semibold text-gray-900">{{ $order->job_name }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Status</h4>
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
                                    'On Hold' => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $order->status }}
                            </span>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Delivery Method</h4>
                            <p class="text-lg text-gray-900">{{ $order->delivery_method }}</p>
                        </div>
                        @if($order->orderProducts && $order->orderProducts->count() > 0)
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-3">Order Products</h4>
                            <div class="space-y-3">
                                @foreach($order->orderProducts as $orderProduct)
                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0">
                                        @if($orderProduct->product->first_image_url)
                                            <img src="{{ $orderProduct->product->first_image_url }}" 
                                                 alt="{{ $orderProduct->product->name }}" 
                                                 class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm">
                                        @else
                                            <div class="w-12 h-12 rounded-full bg-gray-200 border-2 border-white shadow-sm flex items-center justify-center">
                                                <i class="fas fa-box text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Product Details -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                    {{ $orderProduct->product->name }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    Size: {{ $orderProduct->product->size }} | 
                                                    Stock: {{ $orderProduct->product->stock }}
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Qty: {{ $orderProduct->quantity }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        @if($orderProduct->comments)
                                        <div class="mt-2">
                                            <p class="text-xs text-gray-600 bg-white px-2 py-1 rounded border">
                                                <i class="fas fa-comment mr-1 text-gray-400"></i>
                                                {{ $orderProduct->comments }}
                                            </p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @elseif($order->product)
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Product</h4>
                            <p class="text-lg text-gray-900">{{ $order->product->name }} ({{ $order->product->size }})</p>
                        </div>
                        @endif
                    </div>

                    <!-- Payment Summary -->
                    <div class="mt-8">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Payment Summary</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h5 class="text-sm font-medium text-gray-500 mb-1">Design Deposit</h5>
                                <p class="text-xl font-bold text-gray-900">RM {{ number_format($order->design_deposit, 2) }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h5 class="text-sm font-medium text-gray-500 mb-1">Production Deposit</h5>
                                <p class="text-xl font-bold text-gray-900">RM {{ number_format($order->production_deposit, 2) }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h5 class="text-sm font-medium text-gray-500 mb-1">Balance Payment</h5>
                                <p class="text-xl font-bold text-gray-900">RM {{ number_format($order->balance_payment, 2) }}</p>
                            </div>
                        </div>
                        <div class="mt-4 bg-primary-50 rounded-lg p-4">
                            <h5 class="text-sm font-medium text-primary-700 mb-1">Total Amount</h5>
                            <p class="text-2xl font-bold text-primary-900">RM {{ number_format($order->design_deposit + $order->production_deposit + $order->balance_payment, 2) }}</p>
                        </div>
                    </div>

                    <!-- Due Dates -->
                    <div class="mt-8">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Due Dates</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h5 class="text-sm font-medium text-gray-500 mb-1">Design Due Date</h5>
                                <p class="text-lg font-semibold text-gray-900">{{ $order->due_date_design->format('M d, Y') }}</p>
                                @if($order->due_date_design->isPast())
                                    <span class="text-red-600 text-sm">Overdue</span>
                                @elseif($order->due_date_design->diffInDays(now()) <= 3)
                                    <span class="text-yellow-600 text-sm">Due Soon</span>
                                @endif
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h5 class="text-sm font-medium text-gray-500 mb-1">Production Due Date</h5>
                                <p class="text-lg font-semibold text-gray-900">{{ $order->due_date_production->format('M d, Y') }}</p>
                                @if($order->due_date_production->isPast())
                                    <span class="text-red-600 text-sm">Overdue</span>
                                @elseif($order->due_date_production->diffInDays(now()) <= 3)
                                    <span class="text-yellow-600 text-sm">Due Soon</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Remarks -->
                    @if($order->remarks)
                    <div class="mt-8">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Remarks</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-700">{{ $order->remarks }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Client Information -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-user mr-2 text-primary-500"></i>
                        Client Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-1">Client Name</h4>
                            <p class="text-lg font-semibold text-gray-900">{{ $order->client->name }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-1">Customer Type</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                {{ $order->client->customer_type }}
                            </span>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-1">Email</h4>
                            <p class="text-gray-900">{{ $order->client->email }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-1">Phone</h4>
                            <p class="text-gray-900">{{ $order->client->phone }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-1">Billing Address</h4>
                            <p class="text-gray-900 text-sm">{{ $order->client->billing_address }}</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <a href="{{ route('clients.show', $order->client) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-primary-300 text-sm font-medium rounded-md text-primary-700 bg-primary-50 hover:bg-primary-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                            <i class="fas fa-eye mr-2"></i>
                            View Client Details
                        </a>
                    </div>
                </div>
            </div>

            <!-- Order Actions -->
            @if($currentUser->isAdmin() || $currentUser->isSuperAdmin() || $currentUser->isSalesManager())
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-cogs mr-2 text-primary-500"></i>
                        Order Actions
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @if($order->status === 'Order Created' && ($currentUser->isAdmin() || $currentUser->isSuperAdmin()))
                            <form method="POST" action="{{ route('orders.approve', $order) }}" class="w-full">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                    <i class="fas fa-check mr-2"></i>
                                    Approve Payment
                                </button>
                            </form>
                        @endif

                        @if($canManageHold && in_array($order->status, $holdEligibleStatuses ?? [], true))
                            <form method="POST" action="{{ route('orders.hold', $order) }}" class="w-full space-y-3">
                                @csrf
                                <div>
                                    <label for="status_comment" class="block text-xs font-medium text-gray-500 mb-1">
                                        Reason (optional)
                                    </label>
                                    <textarea name="status_comment"
                                              id="status_comment"
                                              rows="2"
                                              class="w-full rounded-md border border-gray-300 focus:ring-primary-500 focus:border-primary-500 text-sm"
                                              placeholder="Explain why this order should be on hold (optional)"></textarea>
                                </div>
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                    <i class="fas fa-pause mr-2"></i>
                                    Put On Hold
                                </button>
                            </form>
                        @endif

                        @if($canManageHold && $order->status === 'On Hold')
                            <form method="POST" action="{{ route('orders.resume', $order) }}" class="w-full">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                    <i class="fas fa-play mr-2"></i>
                                    Resume Order
                                </button>
                            </form>
                        @endif

                        @if($order->status === 'Design Approved' && $canManageHold)
                            <button onclick="showCreateJobsModal()" class="w-full inline-flex items-center justify-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                <i class="fas fa-tasks mr-2"></i>
                                Create Production Jobs
                            </button>
                        @endif

                        @if(in_array($order->status, ['Job Start', 'Order Finished'], true) && $canManageHold)
                            <button onclick="showCreateJobsModal()" class="w-full inline-flex items-center justify-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                <i class="fas fa-tasks mr-2"></i>
                                Create Additional Jobs
                            </button>
                        @endif

                        @if($order->status === 'In Progress' && $order->jobs->where('status', 'Completed')->count() === $order->jobs->count() && $order->jobs->count() > 0)
                            <form method="POST" action="{{ route('orders.complete', $order) }}" class="w-full">
                                @csrf
                                <button type="submit" 
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Mark as Completed
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            @if($order->statusLogs->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-history mr-2 text-primary-500"></i>
                        Status Timeline
                    </h3>
                </div>
                <div class="p-6 space-y-4 max-h-96 overflow-y-auto">
                    @foreach($order->statusLogs as $log)
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 rounded-full bg-primary-50 flex items-center justify-center text-primary-600">
                                <i class="fas fa-flag"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $log->new_status }}
                                    </p>
                                    <span class="text-xs text-gray-500">
                                        {{ $log->created_at->format('d M Y, h:i A') }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    by {{ $log->user->name ?? 'System' }}
                                    @if($log->previous_status)
                                        (from {{ $log->previous_status }})
                                    @endif
                                </p>
                                @if($log->comment)
                                    <p class="text-sm text-gray-700 mt-1">
                                        {{ $log->comment }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Production Jobs -->
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-tasks mr-2 text-primary-500"></i>
                        Production Jobs
                    </h3>
                    @if($canManageHold)
                        @if($order->status === 'Order Approved' && $order->jobs->count() === 0)
                            <button onclick="showCreateJobsModal()" 
                                    class="inline-flex items-center px-4 py-2 bg-primary-500 border border-transparent rounded-md font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Create Jobs
                            </button>
                        @endif
                    @endif
                </div>
            </div>
            <div class="p-6">
                @if($order->jobs->count() > 0)
                    <!-- Job Progress Overview -->
                    <div class="mb-6">
                        @php
                            $totalJobs = $order->jobs->count();
                            $completedJobs = $order->jobs->where('status', 'Completed')->count();
                            $progress = $totalJobs > 0 ? ($completedJobs / $totalJobs) * 100 : 0;
                        @endphp
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Production Progress</span>
                            <span class="text-sm font-medium text-gray-900">{{ $completedJobs }}/{{ $totalJobs }} completed</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-primary-600 h-2 rounded-full transition-all duration-300" data-progress="{{ $progress ?? 0 }}" style="width: 0%;"></div>
                        </div>
                    </div>

                    <!-- Jobs List -->
                    <div class="space-y-4">
                        @foreach($order->jobs->sortBy('phase') as $job)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-tasks text-primary-500"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $job->phase }}</h4>
                                            <p class="text-sm text-gray-600">Job #{{ $job->job_id }}</p>
                                            @if($job->assignedUser)
                                                <p class="text-xs text-gray-500">Assigned to: {{ $job->assignedUser->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        @php
                                            $jobStatusColors = [
                                                'Pending' => 'bg-yellow-100 text-yellow-800',
                                                'In Progress' => 'bg-blue-100 text-blue-800',
                                                'Completed' => 'bg-green-100 text-green-800',
                                                'On Hold' => 'bg-red-100 text-red-800'
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $jobStatusColors[$job->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $job->status }}
                                        </span>
                                        <a href="{{ route('jobs.show', $job) }}" 
                                           class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                            View Details
                                        </a>
                                    </div>
                                </div>

                                <!-- Job Progress -->
                                @if($job->start_quantity && $job->end_quantity)
                                <div class="mt-3">
                                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                                        <span>Progress</span>
                                        <span>{{ number_format(($job->end_quantity / $job->start_quantity) * 100, 1) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1">
                                        @php
                                            $jobProgress = ($job->end_quantity / $job->start_quantity) * 100;
                                        @endphp
                                        <div class="bg-green-600 h-1 rounded-full job-progress-bar" data-progress="{{ $jobProgress ?? 0 }}" style="width: 0%;"></div>
                                    </div>
                                </div>
                                @endif

                                <!-- Time Tracking -->
                                @if($job->start_time)
                                <div class="mt-3 text-xs text-gray-500">
                                    <span>Started: {{ $job->start_time->format('M d, Y H:i') }}</span>
                                    @if($job->end_time)
                                        <span class="ml-3">Completed: {{ $job->end_time->format('M d, Y H:i') }}</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-tasks text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No production jobs yet</h3>
                        <p class="text-gray-500 mb-4">
                            @if($order->status === 'Order Approved')
                                Create production jobs to start the manufacturing process.
                            @else
                                Jobs will be created after the order is approved.
                            @endif
                        </p>
                        @if($order->status === 'Order Approved' && $canManageHold)
                            <button onclick="showCreateJobsModal()" 
                                    class="inline-flex items-center px-4 py-2 bg-primary-500 border border-transparent rounded-md font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Create Production Jobs
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- File Downloads -->
    @if($order->receipts()->count() > 0 || $order->job_sheet || $order->download_link)
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-download mr-2 text-primary-500"></i>
                    Files & Downloads
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @if($order->receipts()->count() > 0)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-receipt mr-2 text-primary-500"></i>
                                Receipts ({{ $order->receipts()->count() }})
                            </h4>
                            <div class="space-y-2">
                                @foreach($order->receipts()->orderBy('uploaded_at', 'desc')->get() as $receipt)
                                    @php
                                        $receiptFileExt = strtolower(pathinfo($receipt->file_name, PATHINFO_EXTENSION));
                                        $receiptIsImage = in_array($receiptFileExt, ['jpg', 'jpeg', 'png', 'gif']);
                                        $receiptFileType = $receiptIsImage ? 'image' : 'pdf';
                                    @endphp
                                    @php
                                        $receiptUrl = asset('storage/' . $receipt->file_path);
                                        $receiptNameEscaped = htmlspecialchars($receipt->file_name, ENT_QUOTES, 'UTF-8');
                                    @endphp
                                    <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer"
                                         onclick="openFilePreviewModal('{{ $receiptUrl }}', '{{ $receiptNameEscaped }}', '{{ $receiptFileType }}')">
                                        <div class="flex-1">
                                            <div class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                                                <i class="fas {{ in_array(strtolower(pathinfo($receipt->file_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']) ? 'fa-image' : 'fa-file-pdf' }} mr-1"></i>
                                                {{ $receipt->file_name }}
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">
                                                <i class="far fa-clock mr-1"></i>
                                                Uploaded: {{ $receipt->uploaded_at->format('d M Y, h:i A') }}
                                            </p>
                                        </div>
                                        <div class="ml-3 text-xs text-gray-400">
                                            {{ number_format($receipt->file_size / 1024, 1) }} KB
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($order->job_sheet)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3">Job Sheet</h4>
                            @php
                                // Handle both single string and JSON array formats
                                $jobSheets = [];
                                if ($order->job_sheet) {
                                    $decoded = json_decode($order->job_sheet, true);
                                    if (is_array($decoded)) {
                                        $jobSheets = $decoded;
                                    } else {
                                        // Old format: single string
                                        $jobSheets = [$order->job_sheet];
                                    }
                                }
                            @endphp
                            <div class="space-y-2">
                                @foreach($jobSheets as $jobSheet)
                                @php
                                    $fileExt = strtolower(pathinfo($jobSheet, PATHINFO_EXTENSION));
                                    $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif']);
                                    $fileType = $isImage ? 'image' : 'pdf';
                                @endphp
                                <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer"
                                     onclick="openFilePreviewModal('@fileUrl($jobSheet)', '{{ basename($jobSheet) }}', '{{ $fileType }}')">
                                    <div class="flex items-center text-sm text-primary-600 hover:text-primary-700 font-medium">
                                        <i class="fas {{ in_array(strtolower(pathinfo($jobSheet, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']) ? 'fa-image' : 'fa-file-alt' }} mr-2"></i>
                                        {{ basename($jobSheet) }}
                                    </div>
                                    <i class="fas fa-external-link-alt text-xs text-gray-400"></i>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($order->download_link)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Raw Files</h4>
                            @php
                                $designFiles = json_decode($order->download_link, true) ?: [];
                            @endphp
                            @foreach($designFiles as $type => $file)
                                <a href="@fileUrl($file)" 
                                   target="_blank"
                                   class="inline-flex items-center text-sm text-primary-600 hover:text-primary-700">
                                    <i class="fas fa-image mr-1"></i>
                                    {{ ucfirst(str_replace('design_', '', $type)) }} View
                                </a>
                                @if(!$loop->last)<br>@endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Raw Files -->
    @php
        $designFiles = $order->getDesignFilesArray();
        $designImages = [];
        // Support both old format (keyed) and new format (array)
        if (is_array($designFiles)) {
            foreach ($designFiles as $key => $value) {
                if (is_numeric($key)) {
                    // New format: array of paths
                    $designImages[] = $value;
                } else {
                    // Old format: keyed array (design_front, design_back, etc.)
                    if (!empty($value)) {
                        $designImages[] = $value;
                    }
                }
            }
        }
    @endphp
    @if(count($designImages) > 0)
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-palette mr-2 text-primary-500"></i>
                    Design Images ({{ count($designImages) }})
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-2">
                    @foreach($designImages as $index => $imagePath)
                    @php
                        // Ensure we have a valid path
                        if (empty($imagePath)) {
                            continue;
                        }
                        // Generate URL using StorageService
                        $imageUrl = \App\Services\StorageService::url($imagePath);
                        // Fallback to asset if StorageService returns null
                        if (!$imageUrl && $imagePath) {
                            $imageUrl = asset('storage/' . ltrim($imagePath, '/'));
                        }
                    @endphp
                    @if($imageUrl)
                    <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer"
                         onclick="openImageModal('{{ $imageUrl }}', 'Design Image {{ $index + 1 }}')">
                        <div class="flex items-center space-x-3 flex-1 min-w-0">
                            <i class="fas fa-image text-primary-500 text-lg"></i>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-primary-600 hover:text-primary-700 truncate">
                                    Design Image {{ $index + 1 }}
                                </p>
                                <p class="text-xs text-gray-500 truncate mt-0.5" title="{{ basename($imagePath) }}">
                                    {{ basename($imagePath) }}
                                </p>
                            </div>
                        </div>
                        <i class="fas fa-external-link-alt text-gray-400 text-xs ml-3"></i>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Create Jobs Modal -->
<div id="createJobsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Create Production Jobs</h3>
            <form method="POST" action="{{ route('orders.jobs.create', $order) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Next Production Phase</label>
                        <div class="space-y-2">
                            @php
                                $phases = ['PRINT', 'PRESS', 'CUT', 'SEW', 'QC'];
                                $completedPhases = $order->jobs->where('status', 'Completed')->pluck('phase')->toArray();
                                $availablePhases = array_diff($phases, $completedPhases);
                                
                                // Find the next phase to create
                                $nextPhase = null;
                                foreach ($phases as $phase) {
                                    if (!in_array($phase, $completedPhases)) {
                                        $nextPhase = $phase;
                                        break;
                                    }
                                }
                            @endphp
                            
                            @if($nextPhase)
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" 
                                           name="phase" 
                                           value="{{ $nextPhase }}"
                                           checked
                                           class="h-4 w-4 text-primary-600 border-gray-300 focus:ring-primary-500">
                                    <span class="ml-3 text-sm font-medium text-gray-700">{{ $nextPhase }}</span>
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Next Phase
                                    </span>
                                </label>
                                
                                @if(count($availablePhases) > 1)
                                    <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <p class="text-xs text-yellow-800">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Only the next phase is recommended. Creating jobs out of order may cause workflow issues.
                                        </p>
                                    </div>
                                @endif
                            @else
                                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                        <span class="text-sm text-green-800">All production phases are completed!</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-3 mt-6">
                    <button type="button" 
                            onclick="hideCreateJobsModal()"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    @if($nextPhase)
                        <button type="submit" 
                                class="px-4 py-2 bg-primary-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-primary-600">
                            Create {{ $nextPhase }} Job
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-95 hidden z-50 flex items-center justify-center p-4">
    <div class="relative w-full h-full max-w-7xl max-h-full">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden w-full h-full flex flex-col">
            <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50">
                <h3 id="modalTitle" class="text-xl font-semibold text-gray-900"></h3>
                <div class="flex items-center space-x-2">
                    <button onclick="downloadImage()" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg" title="Download Image">
                        <i class="fas fa-download text-lg"></i>
                    </button>
                    <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg" title="Close">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>
            <div class="flex-1 p-4 flex items-center justify-center overflow-hidden bg-gray-900">
                <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg shadow-lg cursor-zoom-in" onclick="toggleZoom()">
            </div>
            <div class="p-4 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <span id="imageInfo"></span>
                    <div class="flex items-center space-x-4">
                        <button onclick="rotateImage(-90)" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg" title="Rotate Left">
                            <i class="fas fa-undo text-lg"></i>
                        </button>
                        <button onclick="rotateImage(90)" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg" title="Rotate Right">
                            <i class="fas fa-redo text-lg"></i>
                        </button>
                        <button onclick="resetImage()" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg" title="Reset">
                            <i class="fas fa-sync-alt text-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentImageRotation = 0;
let isZoomed = false;

function showCreateJobsModal() {
    document.getElementById('createJobsModal').classList.remove('hidden');
}

function hideCreateJobsModal() {
    document.getElementById('createJobsModal').classList.add('hidden');
}

function openImageModal(imageSrc, title) {
    console.log('Opening modal for:', imageSrc, title);
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalTitle = document.getElementById('modalTitle');
    const imageInfo = document.getElementById('imageInfo');
    
    if (!modal || !modalImage || !modalTitle) {
        console.error('Modal elements not found:', { modal, modalImage, modalTitle });
        return;
    }
    
    // Reset image state
    currentImageRotation = 0;
    isZoomed = false;
    
    modalImage.src = imageSrc;
    modalTitle.textContent = title;
    
    // Get image filename for info display
    const filename = imageSrc.split('/').pop();
    imageInfo.textContent = `File: ${filename}`;
    
    // Reset image transform
    modalImage.style.transform = 'rotate(0deg)';
    modalImage.classList.remove('cursor-zoom-out');
    modalImage.classList.add('cursor-zoom-in');
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    console.log('Modal opened successfully');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function toggleZoom() {
    const modalImage = document.getElementById('modalImage');
    if (!modalImage) return;
    
    if (isZoomed) {
        modalImage.style.transform = `rotate(${currentImageRotation}deg) scale(1)`;
        modalImage.classList.remove('cursor-zoom-out');
        modalImage.classList.add('cursor-zoom-in');
        isZoomed = false;
    } else {
        modalImage.style.transform = `rotate(${currentImageRotation}deg) scale(2)`;
        modalImage.classList.remove('cursor-zoom-in');
        modalImage.classList.add('cursor-zoom-out');
        isZoomed = true;
    }
}

function rotateImage(degrees) {
    const modalImage = document.getElementById('modalImage');
    if (!modalImage) return;
    
    currentImageRotation += degrees;
    const scale = isZoomed ? 2 : 1;
    modalImage.style.transform = `rotate(${currentImageRotation}deg) scale(${scale})`;
}

function resetImage() {
    const modalImage = document.getElementById('modalImage');
    if (!modalImage) return;
    
    currentImageRotation = 0;
    isZoomed = false;
    modalImage.style.transform = 'rotate(0deg) scale(1)';
    modalImage.classList.remove('cursor-zoom-out');
    modalImage.classList.add('cursor-zoom-in');
}

function downloadImage() {
    const modalImage = document.getElementById('modalImage');
    if (!modalImage || !modalImage.src) {
        console.error('No image to download');
        return;
    }
    
    console.log('Attempting to download image:', modalImage.src);
    
    // Get the image title for better filename
    const modalTitle = document.getElementById('modalTitle');
    const title = modalTitle ? modalTitle.textContent : 'Design Image';
    
    // Create a more descriptive filename
    let filename = title.replace(/[^a-zA-Z0-9]/g, '_') + '.png';
    
    // Try to extract original filename from URL
    try {
        const url = new URL(modalImage.src);
        const pathParts = url.pathname.split('/');
        const originalFilename = pathParts[pathParts.length - 1];
        if (originalFilename && originalFilename.includes('.')) {
            filename = originalFilename;
        }
    } catch (e) {
        console.log('Could not parse URL, using default filename');
    }
    
    console.log('Downloading as:', filename);
    
    // Method 1: Try direct download first (works for same-origin images)
    try {
        const link = document.createElement('a');
        link.href = modalImage.src;
        link.download = filename;
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        console.log('Download initiated via direct link');
        return;
    } catch (e) {
        console.log('Direct download failed, trying canvas method:', e);
    }
    
    // Method 2: Canvas method for cross-origin images
    try {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        
        // Set canvas size to match image
        canvas.width = modalImage.naturalWidth || modalImage.width;
        canvas.height = modalImage.naturalHeight || modalImage.height;
        
        // Draw the image to canvas
        ctx.drawImage(modalImage, 0, 0);
        
        // Convert to blob and download
        canvas.toBlob(function(blob) {
            if (blob) {
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = filename;
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Clean up
                setTimeout(() => URL.revokeObjectURL(url), 100);
                console.log('Download completed via canvas method');
            } else {
                console.error('Failed to create blob from canvas');
                showDownloadError();
            }
        }, 'image/png');
        
    } catch (e) {
        console.error('Canvas method failed:', e);
        showDownloadError();
    }
}

function showDownloadError() {
    // Show user-friendly error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
    errorDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <span>Download failed. Right-click image and "Save as..." instead.</span>
        </div>
    `;
    document.body.appendChild(errorDiv);
    
    // Remove error message after 5 seconds
    setTimeout(() => {
        if (errorDiv.parentNode) {
            errorDiv.parentNode.removeChild(errorDiv);
        }
    }, 5000);
}

// Close modal when clicking outside
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});

// Initialize progress bars
document.addEventListener('DOMContentLoaded', function() {
    // Set progress bar widths
    document.querySelectorAll('[data-progress]').forEach(function(bar) {
        const progress = bar.getAttribute('data-progress');
        if (progress !== null && progress !== '') {
            bar.style.width = progress + '%';
        }
    });
    
    // Set job progress bar widths
    document.querySelectorAll('.job-progress-bar').forEach(function(bar) {
        const progress = bar.getAttribute('data-progress');
        if (progress !== null && progress !== '') {
            bar.style.width = progress + '%';
        }
    });
});

// File Preview Modal (for Receipts and Job Sheets)
let currentPreviewUrl = '';
let currentPreviewFileName = '';

function openFilePreviewModal(fileUrl, fileName, fileType) {
    currentPreviewUrl = fileUrl;
    currentPreviewFileName = fileName;
    
    const modal = document.getElementById('filePreviewModal');
    const modalTitle = document.getElementById('filePreviewTitle');
    const modalImage = document.getElementById('filePreviewImage');
    const modalPdf = document.getElementById('filePreviewPdf');
    const loadingIndicator = document.getElementById('filePreviewLoading');
    
    modalTitle.textContent = fileName;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    loadingIndicator.classList.remove('hidden');
    
    // Show appropriate preview based on file type
    if (fileType === 'image' || fileType.includes('image')) {
        modalImage.src = fileUrl;
        modalImage.classList.remove('hidden');
        modalPdf.classList.add('hidden');
    } else {
        // PDF or other document
        modalPdf.src = fileUrl + '#toolbar=0';
        modalPdf.classList.remove('hidden');
        modalImage.classList.add('hidden');
    }
}

function closeFilePreviewModal() {
    const modal = document.getElementById('filePreviewModal');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
    
    // Clear preview
    document.getElementById('filePreviewImage').src = '';
    document.getElementById('filePreviewPdf').src = '';
}

function downloadPreviewFile() {
    if (currentPreviewUrl) {
        const link = document.createElement('a');
        link.href = currentPreviewUrl;
        link.download = currentPreviewFileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFilePreviewModal();
    }
});
</script>

<!-- File Preview Modal (Receipts & Job Sheets) -->
<div id="filePreviewModal" class="fixed inset-0 bg-black bg-opacity-95 hidden z-50 flex items-center justify-center p-4">
    <div class="relative w-full h-full max-w-7xl max-h-full">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden w-full h-full flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50">
                <h3 id="filePreviewTitle" class="text-xl font-semibold text-gray-900 truncate flex-1 mr-4"></h3>
                <div class="flex items-center space-x-2">
                    <button onclick="downloadPreviewFile()" 
                            class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg" 
                            title="Download File">
                        <i class="fas fa-download text-lg"></i>
                    </button>
                    <button onclick="closeFilePreviewModal()" 
                            class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg" 
                            title="Close">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Content -->
            <div class="flex-1 p-4 flex items-center justify-center overflow-hidden bg-gray-900 relative">
                <!-- Loading Indicator -->
                <div id="filePreviewLoading" class="absolute inset-0 flex items-center justify-center bg-gray-900 bg-opacity-75 z-10">
                    <div class="text-white text-center">
                        <i class="fas fa-spinner fa-spin text-4xl mb-4"></i>
                        <p>Loading preview...</p>
                    </div>
                </div>
                
                <!-- Image Preview -->
                <img id="filePreviewImage" 
                     src="" 
                     alt="" 
                     class="max-w-full max-h-full object-contain rounded-lg shadow-lg hidden"
                     onload="document.getElementById('filePreviewLoading').classList.add('hidden')"
                     onerror="this.parentElement.innerHTML='<div class=&quot;text-white text-center&quot;><i class=&quot;fas fa-exclamation-triangle text-4xl mb-4&quot;></i><p>Failed to load image</p></div>'">
                
                <!-- PDF Preview -->
                <iframe id="filePreviewPdf" 
                        src="" 
                        class="w-full h-full border-0 rounded-lg hidden"
                        style="min-height: 600px;"
                        onload="document.getElementById('filePreviewLoading').classList.add('hidden')">
                </iframe>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-hide loading after timeout
setTimeout(() => {
    const loadingIndicator = document.getElementById('filePreviewLoading');
    if (loadingIndicator && !loadingIndicator.classList.contains('hidden')) {
        loadingIndicator.classList.add('hidden');
    }
}, 3000);

// Close modal when clicking outside
document.getElementById('filePreviewModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeFilePreviewModal();
    }
});
</script>
@endsection 