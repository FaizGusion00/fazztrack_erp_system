@extends('layouts.app')

@section('title', 'Order Details - Fazztrack')

@section('content')
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
                                    'Pending' => 'bg-yellow-100 text-yellow-800',
                                    'Approved' => 'bg-blue-100 text-blue-800',
                                    'On Hold' => 'bg-red-100 text-red-800',
                                    'In Progress' => 'bg-primary-100 text-primary-800',
                                    'Completed' => 'bg-green-100 text-green-800'
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
                        @if($order->product)
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Product</h4>
                            <p class="text-lg text-gray-900">{{ $order->product->name }} ({{ $order->product->size }})</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Product Price</h4>
                            <p class="text-lg text-gray-900">RM {{ number_format($order->product->price, 2) }}</p>
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
            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-cogs mr-2 text-primary-500"></i>
                        Order Actions
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @if($order->status === 'Order Created')
                            <form method="POST" action="{{ route('orders.approve', $order) }}" class="w-full">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                    <i class="fas fa-check mr-2"></i>
                                    Approve Payment
                                </button>
                            </form>
                        @elseif($order->status === 'Order Approved')
                            <form method="POST" action="{{ route('orders.hold', $order) }}" class="w-full">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                    <i class="fas fa-pause mr-2"></i>
                                    Put On Hold
                                </button>
                            </form>
                        @elseif($order->status === 'On Hold')
                            <form method="POST" action="{{ route('orders.resume', $order) }}" class="w-full">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                    <i class="fas fa-play mr-2"></i>
                                    Resume Order
                                </button>
                            </form>
                        @elseif($order->status === 'Design Approved')
                            @if(auth()->user()->isSalesManager() || auth()->user()->isSuperAdmin())
                                <button onclick="showCreateJobsModal()" class="w-full inline-flex items-center justify-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                    <i class="fas fa-tasks mr-2"></i>
                                    Create Production Jobs
                                </button>
                            @endif
                        @elseif($order->status === 'Job Start')
                            @if(auth()->user()->isSalesManager() || auth()->user()->isSuperAdmin())
                                <button onclick="showCreateJobsModal()" class="w-full inline-flex items-center justify-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                    <i class="fas fa-tasks mr-2"></i>
                                    Create Additional Jobs
                                </button>
                            @endif
                        @elseif($order->status === 'Order Finished')
                            @if(auth()->user()->isSalesManager() || auth()->user()->isSuperAdmin())
                                <button onclick="showCreateJobsModal()" class="w-full inline-flex items-center justify-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                    <i class="fas fa-tasks mr-2"></i>
                                    Create Additional Jobs
                                </button>
                            @endif
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
                    @if(auth()->user()->isSalesManager() || auth()->user()->isSuperAdmin())
                        @if($order->status === 'Approved' && $order->jobs->count() === 0)
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
                            <div class="bg-primary-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
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
                                        <div class="bg-green-600 h-1 rounded-full" style="width: {{ $jobProgress }}%"></div>
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
                            @if($order->status === 'Approved')
                                Create production jobs to start the manufacturing process.
                            @else
                                Jobs will be created after the order is approved.
                            @endif
                        </p>
                        @if($order->status === 'Approved' && (auth()->user()->isSalesManager() || auth()->user()->isSuperAdmin()))
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
    @if($order->receipts || $order->job_sheet || $order->download_link)
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
                    @if($order->receipts)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Receipts</h4>
                            @php
                                $receipts = json_decode($order->receipts, true) ?: [$order->receipts];
                            @endphp
                            @foreach($receipts as $receipt)
                                <a href="{{ Storage::url($receipt) }}" 
                                   target="_blank"
                                   class="inline-flex items-center text-sm text-primary-600 hover:text-primary-700">
                                    <i class="fas fa-file-pdf mr-1"></i>
                                    Receipt {{ $loop->iteration }}
                                </a>
                                @if(!$loop->last)<br>@endif
                            @endforeach
                        </div>
                    @endif

                    @if($order->job_sheet)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Job Sheet</h4>
                            <a href="{{ Storage::url($order->job_sheet) }}" 
                               target="_blank"
                               class="inline-flex items-center text-sm text-primary-600 hover:text-primary-700">
                                <i class="fas fa-file-alt mr-1"></i>
                                Download Job Sheet
                            </a>
                        </div>
                    @endif

                    @if($order->download_link)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Design Files</h4>
                            @php
                                $designFiles = json_decode($order->download_link, true) ?: [];
                            @endphp
                            @foreach($designFiles as $type => $file)
                                <a href="{{ Storage::url($file) }}" 
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

    <!-- Design Files -->
    @if($order->design_front || $order->design_back || $order->design_left || $order->design_right)
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-palette mr-2 text-primary-500"></i>
                    Design Files
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @if($order->design_front)
                    <div class="space-y-3">
                        <h4 class="text-sm font-medium text-gray-700">Front View</h4>
                        <div class="relative group">
                            <img src="{{ Storage::url($order->design_front) }}" 
                                 alt="Front Design" 
                                 class="w-full h-48 object-cover rounded-lg border border-gray-200 hover:shadow-md transition-shadow cursor-pointer"
                                 onclick="openImageModal('{{ Storage::url($order->design_front) }}', 'Front Design')">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">{{ basename($order->design_front) }}</p>
                    </div>
                    @endif

                    @if($order->design_back)
                    <div class="space-y-3">
                        <h4 class="text-sm font-medium text-gray-700">Back View</h4>
                        <div class="relative group">
                            <img src="{{ Storage::url($order->design_back) }}" 
                                 alt="Back Design" 
                                 class="w-full h-48 object-cover rounded-lg border border-gray-200 hover:shadow-md transition-shadow cursor-pointer"
                                 onclick="openImageModal('{{ Storage::url($order->design_back) }}', 'Back Design')">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">{{ basename($order->design_back) }}</p>
                    </div>
                    @endif

                    @if($order->design_left)
                    <div class="space-y-3">
                        <h4 class="text-sm font-medium text-gray-700">Left View</h4>
                        <div class="relative group">
                            <img src="{{ Storage::url($order->design_left) }}" 
                                 alt="Left Design" 
                                 class="w-full h-48 object-cover rounded-lg border border-gray-200 hover:shadow-md transition-shadow cursor-pointer"
                                 onclick="openImageModal('{{ Storage::url($order->design_left) }}', 'Left Design')">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">{{ basename($order->design_left) }}</p>
                    </div>
                    @endif

                    @if($order->design_right)
                    <div class="space-y-3">
                        <h4 class="text-sm font-medium text-gray-700">Right View</h4>
                        <div class="relative group">
                            <img src="{{ Storage::url($order->design_right) }}" 
                                 alt="Right Design" 
                                 class="w-full h-48 object-cover rounded-lg border border-gray-200 hover:shadow-md transition-shadow cursor-pointer"
                                 onclick="openImageModal('{{ Storage::url($order->design_right) }}', 'Right Design')">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">{{ basename($order->design_right) }}</p>
                    </div>
                    @endif
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
                                $phases = ['PRINT', 'PRESS', 'CUT', 'SEW', 'QC', 'IRON/PACKING'];
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
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center">
    <div class="relative max-w-4xl max-h-full mx-4">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 text-2xl">
            <i class="fas fa-times"></i>
        </button>
        <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain">
        <h3 id="modalTitle" class="absolute bottom-4 left-4 text-white text-lg font-medium"></h3>
    </div>
</div>

<script>
function showCreateJobsModal() {
    document.getElementById('createJobsModal').classList.remove('hidden');
}

function hideCreateJobsModal() {
    document.getElementById('createJobsModal').classList.add('hidden');
}

function openImageModal(imageSrc, title) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
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
</script>
@endsection 