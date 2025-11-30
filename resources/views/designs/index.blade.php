@extends('layouts.app')

@section('title', 'Design Management - Fazztrack')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-palette mr-3 text-primary-500"></i>
                    Design Management
                </h1>
                <p class="mt-2 text-gray-600">Manage designs, review submissions, and track approval workflow.</p>
            </div>
        </div>
    </div>

    <!-- Tabs & View Toggle -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <nav class="flex space-x-8" aria-label="Tabs">
            <a href="{{ route('designs.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'all'])) }}" 
               class="border-primary-500 text-primary-600 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('tab', 'all') === 'all' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-list mr-2"></i>
                All Designs
            </a>
            <a href="{{ route('designs.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'pending'])) }}" 
               class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('tab') === 'pending' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-clock mr-2"></i>
                Pending Review
                <span class="ml-2 bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
            </a>
            <a href="{{ route('designs.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'approved'])) }}" 
               class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('tab') === 'approved' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-check-circle mr-2"></i>
                Approved
                <span class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded-full">{{ $approvedCount }}</span>
            </a>
            <a href="{{ route('designs.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'rejected'])) }}" 
               class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('tab') === 'rejected' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-times-circle mr-2"></i>
                Rejected
                <span class="ml-2 bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded-full">{{ $rejectedCount }}</span>
            </a>
            @if(auth()->user()->isDesigner())
            <a href="{{ route('designs.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'pola'])) }}" 
               class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('tab') === 'pola' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-ruler-combined mr-2"></i>
                Pola Design
                <span class="ml-2 bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full">{{ $polaCount ?? 0 }}</span>
            </a>
            @endif
        </nav>
        <div class="mt-3 sm:mt-0 sm:ml-auto flex items-center space-x-2">
            @php $view = request('view', 'table'); @endphp
            <a href="{{ route('designs.index', array_merge(request()->except('page'), ['view' => 'table'])) }}"
               class="px-3 py-2 rounded-md border text-sm font-medium {{ $view === 'table' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                <i class="fas fa-table mr-1"></i> Table
            </a>
            <a href="{{ route('designs.index', array_merge(request()->except('page'), ['view' => 'cards'])) }}"
               class="px-3 py-2 rounded-md border text-sm font-medium {{ $view === 'cards' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                <i class="fas fa-th-large mr-1"></i> Cards
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('designs.index') }}" class="flex flex-col md:flex-row gap-4">
            <input type="hidden" name="tab" value="{{ request('tab', 'all') }}">
            <input type="hidden" name="view" value="{{ $view }}">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Designs</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search by design ID, order, client, or designer..."
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
            <div class="md:w-48">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="status" name="status" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Status</option>
                    <option value="Pending Review" {{ request('status') == 'Pending Review' ? 'selected' : '' }}>Pending Review</option>
                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="md:w-48">
                <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                <select id="sort" name="sort" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="latest_added" {{ request('sort', 'latest_added') == 'latest_added' ? 'selected' : '' }}>Latest Added</option>
                    <option value="latest_updated" {{ request('sort') == 'latest_updated' ? 'selected' : '' }}>Latest Updated</option>
                    <option value="version" {{ request('sort') == 'version' ? 'selected' : '' }}>Version</option>
                    <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>Status</option>
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 transition-colors">
                    <i class="fas fa-search mr-1"></i>
                    Search
                </button>
                @if(request('search') || request('status') || request('sort'))
                    <a href="{{ route('designs.index', ['tab' => request('tab', 'all'), 'view' => $view]) }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                        <i class="fas fa-times mr-1"></i>
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Orders Needing Design (for Designers only - All Designs tab only) -->
    @if(auth()->user()->isDesigner() && isset($ordersNeedingDesign) && $ordersNeedingDesign->count() > 0 && (request('tab', 'all') === 'all'))
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-tasks mr-2 text-primary-500"></i>
                Orders Needing Design Work
                <span class="ml-2 bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full">{{ $ordersNeedingDesign->count() }}</span>
            </h3>
            <p class="text-sm text-gray-500 mt-1">These orders have been approved and are waiting for design upload.</p>
        </div>
        
        @if($view === 'table')
        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($ordersNeedingDesign as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $order->order_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->job_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->client->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $order->due_date_design->format('M d, Y') }}
                            @if($order->due_date_design->isPast())
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-exclamation-circle mr-1"></i> Overdue
                                </span>
                            @elseif($order->due_date_design->diffInDays(now()) <= 3)
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> Due Soon
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('designs.create', ['order_id' => $order->order_id]) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-primary-500 text-white rounded-md hover:bg-primary-600 mr-2">
                                <i class="fas fa-upload mr-1"></i>
                                Upload Design
                            </a>
                            <a href="{{ route('orders.show', $order) }}" 
                               class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <i class="fas fa-eye mr-1"></i>
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <!-- Cards View -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($ordersNeedingDesign as $order)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">{{ $order->job_name }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $order->client->name }}</p>
                            <p class="text-xs text-gray-500 mt-1">Order #{{ $order->order_id }}</p>
                        </div>
                    </div>
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-calendar w-4 mr-2 text-gray-400"></i>
                            <span>Due: {{ $order->due_date_design->format('M d, Y') }}</span>
                        </div>
                        @if($order->due_date_design->isPast())
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-exclamation-circle mr-1"></i> Overdue
                            </span>
                        @elseif($order->due_date_design->diffInDays(now()) <= 3)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i> Due Soon
                            </span>
                        @endif
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('designs.create', ['order_id' => $order->order_id]) }}" 
                           class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-primary-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-primary-600 transition-colors">
                            <i class="fas fa-upload mr-2"></i>
                            Upload Design
                        </a>
                        <a href="{{ route('orders.show', $order) }}" 
                           class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
                           title="View Order">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Pola Design Section (Designer Only) --}}
    @if(auth()->user()->isDesigner() && request('tab', 'all') === 'pola' && isset($ordersNeedingPola))
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-ruler-combined mr-2 text-primary-500"></i>
                Pola (Pattern) Link Management
                <span class="ml-2 bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded-full">{{ $ordersNeedingPola->count() }}</span>
            </h3>
            <p class="text-sm text-gray-500 mt-1">Manage pola (pattern) links for orders with approved designs. You can add or update pola links until production jobs are completed.</p>
        </div>
        <div class="p-6">
            @if($view === 'table')
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pola Link (Google Drive)</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($ordersNeedingPola as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $order->order_id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->job_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->client->name }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <form method="POST" action="{{ route('orders.pola.update', $order) }}" class="flex items-center space-x-2">
                                        @csrf
                                        <input type="url" 
                                               name="pola_link" 
                                               value="{{ old('pola_link', $order->pola_link) }}"
                                               placeholder="https://drive.google.com/..."
                                               class="flex-1 px-3 py-1.5 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                               required>
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 bg-primary-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-primary-600 transition-colors">
                                            <i class="fas fa-save mr-1"></i>
                                            {{ $order->pola_link ? 'Update' : 'Add' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50"><i class="fas fa-eye mr-1"></i>View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($ordersNeedingPola as $order)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $order->job_name }}</h4>
                                <p class="text-sm text-gray-600 mt-1">{{ $order->client->name }}</p>
                                <p class="text-xs text-gray-500 mt-1">Order #{{ $order->order_id }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('orders.pola.update', $order) }}" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Pola Link (Google Drive)</label>
                                <input type="url" 
                                       name="pola_link" 
                                       value="{{ old('pola_link', $order->pola_link) }}"
                                       placeholder="https://drive.google.com/..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       required>
                            </div>
                            <div class="flex space-x-2">
                                <button type="submit" 
                                        class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-primary-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-primary-600 transition-colors">
                                    <i class="fas fa-save mr-2"></i>
                                    {{ $order->pola_link ? 'Update Link' : 'Add Link' }}
                                </button>
                                <a href="{{ route('orders.show', $order) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
                                   title="View Order">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    @elseif(auth()->user()->isDesigner() && request('tab', 'all') === 'pola' && (!isset($ordersNeedingPola) || $ordersNeedingPola->count() === 0))
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
        <div class="flex flex-col items-center">
            <i class="fas fa-ruler-combined text-gray-400 text-4xl mb-2"></i>
            <p class="text-gray-500">No orders with approved designs found at this time.</p>
        </div>
    </div>
    @endif

    @if(request('tab', 'all') !== 'pola')
    @if($view === 'table')
        <!-- Designs Table View -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Design ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Designer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Version</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($designs as $design)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $design->design_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>
                                    <div class="font-medium">{{ $design->order->job_name }}</div>
                                    <div class="text-xs text-gray-500">Order #{{ $design->order->order_id }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $design->order->client->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $design->designer->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    v{{ $design->version }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @php
                                    $statusColors = [
                                        'Pending Review' => 'bg-yellow-100 text-yellow-800',
                                        'Approved' => 'bg-green-100 text-green-800',
                                        'Rejected' => 'bg-red-100 text-red-800'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$design->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $design->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $design->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('designs.show', $design) }}" 
                                   class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-palette text-gray-400 text-4xl mb-2"></i>
                                    <p>No designs found.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
        </div>
        <!-- Pagination -->
        @if($designs->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $designs->links() }}
        </div>
        @endif
        </div>
    @else
        <!-- Designs Card View -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($designs as $design)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="p-6">
                    <!-- Design Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-palette text-purple-500 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Design #{{ $design->design_id }}</h3>
                                <p class="text-sm text-gray-500">v{{ $design->version }}</p>
                            </div>
                        </div>
                        @php
                            $statusColors = [
                                'Pending Review' => 'bg-yellow-100 text-yellow-800',
                                'Approved' => 'bg-green-100 text-green-800',
                                'Rejected' => 'bg-red-100 text-red-800'
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$design->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $design->status }}
                        </span>
                    </div>

                    <!-- Design Details -->
                    <div class="space-y-3 mb-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-shopping-cart w-4 mr-2 text-gray-400"></i>
                            <span class="font-medium">{{ $design->order->job_name }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-user w-4 mr-2 text-gray-400"></i>
                            <span>{{ $design->order->client->name }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-user-edit w-4 mr-2 text-gray-400"></i>
                            <span>Designer: {{ $design->designer->name }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-calendar w-4 mr-2 text-gray-400"></i>
                            <span>Created: {{ $design->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($design->approved_by)
                        <div class="flex items-center text-sm text-green-600">
                            <i class="fas fa-check-circle w-4 mr-2"></i>
                            <span>Approved by {{ $design->approvedBy->name }}</span>
                        </div>
                        @endif
                        @if($design->rejected_by)
                        <div class="flex items-center text-sm text-red-600">
                            <i class="fas fa-times-circle w-4 mr-2"></i>
                            <span>Rejected by {{ $design->rejectedBy->name }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Raw Files Preview -->
                    @php
                        $designFiles = $design->getDesignFilesArray();
                        $fileCount = count(array_filter($designFiles));
                    @endphp
                    @if($fileCount > 0)
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">
                                <i class="fas fa-images mr-1"></i>
                                {{ $fileCount }} file{{ $fileCount > 1 ? 's' : '' }} uploaded
                            </span>
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex space-x-2">
                        <a href="{{ route('designs.show', $design) }}" 
                           class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-primary-300 text-sm font-medium rounded-md text-primary-700 bg-primary-50 hover:bg-primary-100 transition-colors">
                            <i class="fas fa-eye mr-2"></i>
                            View Details
                        </a>
                        <a href="{{ route('orders.show', $design->order) }}" 
                           class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors"
                           title="View Order">
                            <i class="fas fa-shopping-cart"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12">
                    <div class="text-center">
                        <i class="fas fa-palette text-gray-400 text-5xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No designs found</h3>
                        <p class="text-gray-500">No designs match your current filters.</p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
        <!-- Pagination -->
        @if($designs->hasPages())
        <div class="mt-6">
            {{ $designs->links() }}
        </div>
        @endif
    @endif
    @endif
</div>
@endsection
