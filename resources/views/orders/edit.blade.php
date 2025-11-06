@extends('layouts.app')

@section('title', 'Edit Order - Fazztrack')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-edit mr-3 text-primary-500"></i>
                    Edit Order
                </h1>
                <p class="mt-2 text-gray-600">Update order #{{ $order->order_id }} details and files.</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('orders.show', $order) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Order
                </a>
            </div>
        </div>
    </div>

    <!-- Order Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form method="POST" action="{{ route('orders.update', $order) }}" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-primary-500"></i>
                    Basic Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Client <span class="text-red-500">*</span>
                        </label>
                        <select id="client_id" 
                                name="client_id" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('client_id') border-red-300 @enderror"
                                required>
                            <option value="">Select Client</option>
                            @foreach(\App\Models\Client::all() as $client)
                                <option value="{{ $client->client_id }}" {{ old('client_id', $order->client_id) == $client->client_id ? 'selected' : '' }}>
                                    {{ $client->name }} ({{ $client->customer_type }})
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Multiple Products Selection -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-medium text-gray-700">
                                Products <span class="text-red-500">*</span>
                            </label>
                            <button type="button" 
                                    onclick="addProductRow()"
                                    class="inline-flex items-center px-3 py-2 border border-primary-300 text-sm font-medium rounded-md text-primary-700 bg-primary-50 hover:bg-primary-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Add Product
                            </button>
                        </div>
                        
                        <div id="products-container" class="space-y-4">
                            @if($order->orderProducts && $order->orderProducts->count() > 0)
                                @foreach($order->orderProducts as $index => $orderProduct)
                                <div class="product-row bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                                            <select name="products[{{ $index }}][product_id]" required 
                                                    class="block w-full border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 product-select">
                                                <option value="">Select a product</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->product_id }}" 
                                                            {{ $orderProduct->product_id == $product->product_id ? 'selected' : '' }}>
                                                        {{ $product->name }} ({{ $product->size }}) - Stock: {{ $product->stock }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                                            <input type="number" 
                                                   name="products[{{ $index }}][quantity]" 
                                                   value="{{ $orderProduct->quantity }}" 
                                                   min="1" 
                                                   required
                                                   class="block w-full border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 quantity-input">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Comments</label>
                                            <input type="text" 
                                                   name="products[{{ $index }}][comments]" 
                                                   value="{{ $orderProduct->comments }}"
                                                   placeholder="Special instructions..."
                                                   class="block w-full border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3 flex items-center justify-between">
                                        <!-- <div class="text-sm text-gray-600">
                                            <span class="stock-info">Stock: <span class="font-medium">-</span></span>
                                        </div> -->
                                        <button type="button" 
                                                onclick="removeProductRow(this)"
                                                class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            <i class="fas fa-trash mr-1"></i>
                                            Remove
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <!-- Default product row if no existing products -->
                                <div class="product-row bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                                            <select name="products[0][product_id]" required 
                                                    class="block w-full border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 product-select">
                                                <option value="">Select a product</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->product_id }}" 
                                                            {{ old('product_id', $order->product_id) == $product->product_id ? 'selected' : '' }}>
                                                        {{ $product->name }} ({{ $product->size }}) - Stock: {{ $product->stock }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                                            <input type="number" 
                                                   name="products[0][quantity]" 
                                                   value="1" 
                                                   min="1" 
                                                   required
                                                   class="block w-full border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 quantity-input">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Comments</label>
                                            <input type="text" 
                                                   name="products[0][comments]" 
                                                   placeholder="Special instructions..."
                                                   class="block w-full border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3 flex items-center justify-between">
                                        <div class="text-sm text-gray-600">
                                            <span class="stock-info">Stock: <span class="font-medium">-</span></span>
                                        </div>
                                        <button type="button" 
                                                onclick="removeProductRow(this)"
                                                class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            <i class="fas fa-trash mr-1"></i>
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                <div class="text-sm text-blue-800">
                                    <p class="font-medium">Multiple Products Support</p>
                                    <p>You can add multiple products to this order. Each product can have its own quantity and special instructions.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="job_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Job Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="job_name" 
                               name="job_name" 
                               value="{{ old('job_name', $order->job_name) }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('job_name') border-red-300 @enderror"
                               placeholder="e.g., Custom T-Shirt Printing"
                               required>
                        @error('job_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="delivery_method" class="block text-sm font-medium text-gray-700 mb-2">
                            Delivery Method <span class="text-red-500">*</span>
                        </label>
                        <select id="delivery_method" 
                                name="delivery_method" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('delivery_method') border-red-300 @enderror"
                                required>
                            <option value="">Select Delivery Method</option>
                            <option value="Self Collect" {{ old('delivery_method', $order->delivery_method) == 'Self Collect' ? 'selected' : '' }}>Self Collect</option>
                            <option value="Shipping" {{ old('delivery_method', $order->delivery_method) == 'Shipping' ? 'selected' : '' }}>Shipping</option>
                    <option value="Grab" {{ old('delivery_method', $order->delivery_method) == 'Grab' ? 'selected' : '' }}>Grab</option>
                    <option value="Lalamove" {{ old('delivery_method', $order->delivery_method) == 'Lalamove' ? 'selected' : '' }}>Lalamove</option>
                    <option value="Bus" {{ old('delivery_method', $order->delivery_method) == 'Bus' ? 'selected' : '' }}>Bus</option>
                        </select>
                        @error('delivery_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="download_link" class="block text-sm font-medium text-gray-700 mb-2">
                            Download Link
                        </label>
                        <input type="url" 
                               id="download_link" 
                               name="download_link" 
                               value="{{ old('download_link', $order->download_link) }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('download_link') border-red-300 @enderror"
                               placeholder="https://example.com/download">
                        @error('download_link')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-money-bill-wave mr-2 text-primary-500"></i>
                    Payment Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="design_deposit" class="block text-sm font-medium text-gray-700 mb-2">
                            Design Deposit (RM) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="design_deposit" 
                               name="design_deposit" 
                               value="{{ old('design_deposit', $order->design_deposit) }}"
                               step="0.01"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('design_deposit') border-red-300 @enderror"
                               placeholder="0.00"
                               required>
                        @error('design_deposit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="production_deposit" class="block text-sm font-medium text-gray-700 mb-2">
                            Production Deposit (RM) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="production_deposit" 
                               name="production_deposit" 
                               value="{{ old('production_deposit', $order->production_deposit) }}"
                               step="0.01"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('production_deposit') border-red-300 @enderror"
                               placeholder="0.00"
                               required>
                        @error('production_deposit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="balance_payment" class="block text-sm font-medium text-gray-700 mb-2">
                            Balance Payment (RM) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="balance_payment" 
                               name="balance_payment" 
                               value="{{ old('balance_payment', $order->balance_payment) }}"
                               step="0.01"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('balance_payment') border-red-300 @enderror"
                               placeholder="0.00"
                               required>
                        @error('balance_payment')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Due Dates -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-calendar mr-2 text-primary-500"></i>
                    Due Dates
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="due_date_design" class="block text-sm font-medium text-gray-700 mb-2">
                            Design Due Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               id="due_date_design" 
                               name="due_date_design" 
                               value="{{ old('due_date_design', $order->due_date_design->format('Y-m-d')) }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('due_date_design') border-red-300 @enderror"
                               required>
                        @error('due_date_design')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="due_date_production" class="block text-sm font-medium text-gray-700 mb-2">
                            Production Due Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               id="due_date_production" 
                               name="due_date_production" 
                               value="{{ old('due_date_production', $order->due_date_production->format('Y-m-d')) }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('due_date_production') border-red-300 @enderror"
                               required>
                        @error('due_date_production')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- File Uploads -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-file-upload mr-2 text-primary-500"></i>
                    File Uploads
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="receipts" class="block text-sm font-medium text-gray-700 mb-2">Receipts (Multiple)</label>
                        <input type="file" 
                               id="receipts" 
                               name="receipts[]" 
                               multiple
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <p class="mt-1 text-sm text-gray-500">Leave empty to keep existing files</p>
                    </div>

                    <div>
                        <label for="job_sheet" class="block text-sm font-medium text-gray-700 mb-2">Job Sheet</label>
                        <input type="file" 
                               id="job_sheet" 
                               name="job_sheet" 
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <p class="mt-1 text-sm text-gray-500">Leave empty to keep existing file</p>
                    </div>
                </div>

                <!-- Design Views -->
                <div class="mt-8">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                        <h4 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3 shadow-sm">
                                <i class="fas fa-palette text-white text-lg"></i>
                            </div>
                            Design Views
                            <span class="ml-auto text-sm font-normal text-gray-600 bg-white px-3 py-1 rounded-full border border-white-200">
                                {{ collect([$order->design_front, $order->design_back, $order->design_left, $order->design_right])->filter()->count() }}/4 uploaded
                            </span>
                        </h4>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 lg:gap-6">
                            <!-- Front View -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-300">
                                <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <label for="design_front" class="text-sm font-semibold text-gray-800 flex items-center">
                                            <i class="fas fa-eye mr-2 text-blue-500"></i>
                                            Front View
                                        </label>
                                        @if($order->design_front)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                <i class="fas fa-check-circle mr-1.5"></i>Uploaded
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                                <i class="fas fa-clock mr-1.5"></i>Pending
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="p-4">
                                    @if($order->design_front)
                                        <div class="mb-4 relative group">
                                            <div class="relative overflow-hidden rounded-lg border-2 border-gray-200 hover:border-blue-300 transition-colors">
                                                <img src="@fileUrl($order->design_front)" 
                                                     alt="Front Design" 
                                                     class="w-full h-36 object-cover cursor-pointer hover:scale-105 transition-transform duration-300 design-image"
                                                     data-title="Front Design">
                                                <!-- Hover overlay - positioned to not block clicks -->
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                                                    <div class="absolute bottom-2 left-2 right-2 pointer-events-auto">
                                                        <div class="bg-white/90 backdrop-blur-sm rounded-lg px-2 py-1 text-center">
                                                            <span class="text-xs font-medium text-gray-800">
                                                                <i class="fas fa-expand-arrows-alt mr-1"></i>Click to enlarge
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-2 text-center font-medium">
                                                <i class="fas fa-file-image mr-1"></i>{{ basename($order->design_front) }}
                                            </p>
                                        </div>
                                    @endif
                                    
                                    <div class="relative">
                                        <div class="flex items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer transition-all duration-300 @if($order->design_front) border-green-300 bg-green-50 hover:bg-green-100 hover:border-green-400 @else border-gray-300 bg-gray-50 hover:bg-gray-100 hover:border-gray-400 @endif">
                                            <div class="text-center px-3">
                                                @if($order->design_front)
                                                    <i class="fas fa-edit text-green-500 text-2xl mb-2"></i>
                                                    <p class="text-sm font-medium text-green-700">Replace Image</p>
                                                    <p class="text-xs text-green-600">JPG, PNG up to 20MB</p>
                                                @else
                                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl mb-2"></i>
                                                    <p class="text-sm font-medium text-gray-600">Upload Image</p>
                                                    <p class="text-xs text-gray-500">Drag & drop or click to browse</p>
                                                @endif
                                            </div>
                                            <input type="file" 
                                                   id="design_front" 
                                                   name="design_front" 
                                                   accept=".jpg,.jpeg,.png"
                                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Back View -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-300">
                                <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <label for="design_back" class="text-sm font-semibold text-gray-800 flex items-center">
                                            <i class="fas fa-eye mr-2 text-blue-500"></i>
                                            Back View
                                        </label>
                                        @if($order->design_back)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                <i class="fas fa-check-circle mr-1.5"></i>Uploaded
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                                <i class="fas fa-clock mr-1.5"></i>Pending
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="p-4">
                                    @if($order->design_back)
                                        <div class="mb-4 relative group">
                                            <div class="relative overflow-hidden rounded-lg border-2 border-gray-200 hover:border-blue-300 transition-colors">
                                                <img src="@fileUrl($order->design_back)" 
                                                     alt="Back Design" 
                                                     class="w-full h-36 object-cover cursor-pointer hover:scale-105 transition-transform duration-300 design-image"
                                                     data-title="Back Design">
                                                <!-- Hover overlay - positioned to not block clicks -->
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                                                    <div class="absolute bottom-2 left-2 right-2 pointer-events-auto">
                                                        <div class="bg-white/90 backdrop-blur-sm rounded-lg px-2 py-1 text-center">
                                                            <span class="text-xs font-medium text-gray-800">
                                                                <i class="fas fa-expand-arrows-alt mr-1"></i>Click to enlarge
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-2 text-center font-medium">
                                                <i class="fas fa-file-image mr-1"></i>{{ basename($order->design_back) }}
                                            </p>
                                        </div>
                                    @endif
                                    
                                    <div class="relative">
                                        <div class="flex items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer transition-all duration-300 @if($order->design_back) border-green-300 bg-green-50 hover:bg-green-100 hover:border-green-400 @else border-gray-300 bg-gray-50 hover:bg-gray-100 hover:border-gray-400 @endif">
                                            <div class="text-center px-3">
                                                @if($order->design_back)
                                                    <i class="fas fa-edit text-green-500 text-2xl mb-2"></i>
                                                    <p class="text-sm font-medium text-green-700">Replace Image</p>
                                                    <p class="text-xs text-green-600">JPG, PNG up to 20MB</p>
                                                @else
                                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl mb-2"></i>
                                                    <p class="text-sm font-medium text-gray-600">Upload Image</p>
                                                    <p class="text-xs text-gray-500">Drag & drop or click to browse</p>
                                                @endif
                                            </div>
                                            <input type="file" 
                                                   id="design_back" 
                                                   name="design_back" 
                                                   accept=".jpg,.jpeg,.png"
                                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Left View -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-300">
                                <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <label for="design_left" class="text-sm font-semibold text-gray-800 flex items-center">
                                            <i class="fas fa-eye mr-2 text-blue-500"></i>
                                            Left View
                                        </label>
                                        @if($order->design_left)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                <i class="fas fa-check-circle mr-1.5"></i>Uploaded
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                                <i class="fas fa-clock mr-1.5"></i>Pending
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="p-4">
                                    @if($order->design_left)
                                        <div class="mb-4 relative group">
                                            <div class="relative overflow-hidden rounded-lg border-2 border-gray-200 hover:border-blue-300 transition-colors">
                                                <img src="@fileUrl($order->design_left)" 
                                                     alt="Left Design" 
                                                     class="w-full h-36 object-cover cursor-pointer hover:scale-105 transition-transform duration-300 design-image"
                                                     data-title="Left Design">
                                                <!-- Hover overlay - positioned to not block clicks -->
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                                                    <div class="absolute bottom-2 left-2 right-2 pointer-events-auto">
                                                        <div class="bg-white/90 backdrop-blur-sm rounded-lg px-2 py-1 text-center">
                                                            <span class="text-xs font-medium text-gray-800">
                                                                <i class="fas fa-expand-arrows-alt mr-1"></i>Click to enlarge
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-2 text-center font-medium">
                                                <i class="fas fa-file-image mr-1"></i>{{ basename($order->design_left) }}
                                            </p>
                                        </div>
                                    @endif
                                    
                                    <div class="relative">
                                        <div class="flex items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer transition-all duration-300 @if($order->design_left) border-green-300 bg-green-50 hover:bg-green-100 hover:border-green-400 @else border-gray-300 bg-gray-50 hover:bg-gray-100 hover:border-gray-400 @endif">
                                            <div class="text-center px-3">
                                                @if($order->design_left)
                                                    <i class="fas fa-edit text-green-500 text-2xl mb-2"></i>
                                                    <p class="text-sm font-medium text-green-700">Replace Image</p>
                                                    <p class="text-xs text-green-600">JPG, PNG up to 20MB</p>
                                                @else
                                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl mb-2"></i>
                                                    <p class="text-sm font-medium text-gray-600">Upload Image</p>
                                                    <p class="text-xs text-gray-500">Drag & drop or click to browse</p>
                                                @endif
                                            </div>
                                            <input type="file" 
                                                   id="design_left" 
                                                   name="design_left" 
                                                   accept=".jpg,.jpeg,.png"
                                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right View -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-300">
                                <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <label for="design_right" class="text-sm font-semibold text-gray-800 flex items-center">
                                            <i class="fas fa-eye mr-2 text-blue-500"></i>
                                            Right View
                                        </label>
                                        @if($order->design_right)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                <i class="fas fa-check-circle mr-1.5"></i>Uploaded
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                                <i class="fas fa-clock mr-1.5"></i>Pending
                                            </span>
                                        @endif
                                    </div>
                                
                                <div class="p-4">
                                    @if($order->design_right)
                                        <div class="mb-4 relative group">
                                            <div class="relative overflow-hidden rounded-lg border-2 border-gray-200 hover:border-blue-300 transition-colors">
                                                <img src="@fileUrl($order->design_right)" 
                                                     alt="Right Design" 
                                                     class="w-full h-36 object-cover cursor-pointer hover:scale-105 transition-transform duration-300 design-image"
                                                     data-title="Right Design">
                                                <!-- Hover overlay - positioned to not block clicks -->
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                                                    <div class="absolute bottom-2 left-2 right-2 pointer-events-auto">
                                                        <div class="bg-white/90 backdrop-blur-sm rounded-lg px-2 py-1 text-center">
                                                            <span class="text-xs font-medium text-gray-800">
                                                                <i class="fas fa-expand-arrows-alt mr-1"></i>Click to enlarge
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-2 text-center font-medium">
                                                <i class="fas fa-file-image mr-1"></i>{{ basename($order->design_right) }}
                                            </p>
                                        </div>
                                    @endif
                                    
                                    <div class="relative">
                                        <div class="flex items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer transition-all duration-300 @if($order->design_right) border-green-300 bg-green-50 hover:bg-green-100 hover:border-green-400 @else border-gray-300 bg-gray-50 hover:bg-gray-100 hover:border-gray-400 @endif">
                                            <div class="text-center px-3">
                                                @if($order->design_right)
                                                    <i class="fas fa-edit text-green-500 text-2xl mb-2"></i>
                                                    <p class="text-sm font-medium text-green-700">Replace Image</p>
                                                    <p class="text-xs text-green-600">JPG, PNG up to 20MB</p>
                                                @else
                                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl mb-2"></i>
                                                    <p class="text-sm font-medium text-gray-600">Upload Image</p>
                                                    <p class="text-xs text-gray-500">Drag & drop or click to browse</p>
                                                @endif
                                            </div>
                                            <input type="file" 
                                                   id="design_right" 
                                                   name="design_right" 
                                                   accept=".jpg,.jpeg,.png"
                                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <br>

            <!-- Remarks -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-comment mr-2 text-primary-500"></i>
                    Remarks
                </h3>
                
                <div>
                    <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                    <textarea id="remarks" 
                              name="remarks" 
                              rows="4"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('remarks') border-red-300 @enderror"
                              placeholder="Enter any additional notes or special instructions...">{{ old('remarks', $order->remarks) }}</textarea>
                    @error('remarks')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('orders.show', $order) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2 bg-primary-500 border border-transparent rounded-md font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Update Order
                </button>
            </div>
        </form>
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

// File preview functionality for design uploads
document.querySelectorAll('input[type="file"]').forEach(function(input) {
    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const container = input.closest('.relative');
                const existingPreview = container.querySelector('.file-preview');
                
                // Remove existing preview if any
                if (existingPreview) {
                    existingPreview.remove();
                }
                
                // Create new preview
                const preview = document.createElement('div');
                preview.className = 'file-preview mb-3 relative group';
                preview.innerHTML = `
                    <img src="${e.target.result}" alt="Preview" class="w-full h-32 object-cover rounded-lg border border-blue-200 cursor-pointer hover:opacity-90 transition-opacity duration-200 design-image" data-title="New Upload Preview">
                    <div class="absolute top-2 right-2 bg-blue-500 text-white px-2 py-1 rounded text-xs">
                        <i class="fas fa-eye mr-1"></i>New Preview
                    </div>
                    <p class="text-xs text-blue-600 mt-1">New file: ${file.name}</p>
                `;
                
                // Insert preview before the upload area
                const uploadArea = container.querySelector('.flex.items-center.justify-center');
                uploadArea.parentNode.insertBefore(preview, uploadArea);
                
                // Add click event listener to the new preview image
                const previewImg = preview.querySelector('.design-image');
                if (previewImg) {
                    previewImg.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        openImageModal(this.src, this.getAttribute('data-title'));
                    });
                }
                
                // Change upload area appearance to indicate replacement
                uploadArea.classList.remove('border-gray-300', 'bg-gray-50', 'hover:bg-gray-100');
                uploadArea.classList.add('border-blue-300', 'bg-blue-50', 'hover:bg-blue-100');
                
                const icon = uploadArea.querySelector('i');
                const text = uploadArea.querySelector('p');
                const subtext = uploadArea.querySelector('.text-xs');
                
                icon.className = 'fas fa-edit text-blue-500 text-2xl mb-2';
                text.textContent = 'Click to replace image';
                text.className = 'text-sm text-blue-700';
                subtext.textContent = 'JPG, PNG up to 20MB';
                subtext.className = 'text-xs text-blue-600';
                
                // Update the live counter
                updateUploadCounter();
            };
            reader.readAsDataURL(file);
        }
    });
});

// Function to update the upload counter
function updateUploadCounter() {
    const designInputs = ['design_front', 'design_back', 'design_left', 'design_right'];
    let uploadedCount = 0;
    
    designInputs.forEach(function(designType) {
        const input = document.querySelector(`input[name="${designType}"]`);
        const container = input.closest('.relative');
        const existingImage = container.querySelector('img[src*="' + designType + '"]');
        const newPreview = container.querySelector('.file-preview');
        
        // Count as uploaded if there's an existing image OR a new preview
        if (existingImage || newPreview) {
            uploadedCount++;
        }
    });
    
    // Update the counter display
    const counterElement = document.querySelector('.text-sm.font-normal.text-gray-600.bg-white.px-3.py-1.rounded-full.border.border-white-200');
    if (counterElement) {
        counterElement.textContent = `${uploadedCount}/4 uploaded`;
    }
}

// Initialize counter on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Setting up image modal functionality (Edit Page)...');
    
    // Initialize upload counter
    updateUploadCounter();
    
    // Setup product management
    setupProductManagement();
    
    // Function to setup image click events
    function setupImageEvents() {
        const designImages = document.querySelectorAll('.design-image');
        console.log('Found design images:', designImages.length);
        
        designImages.forEach(function(img, index) {
            console.log(`Setting up image ${index + 1}:`, img.src, img.getAttribute('data-title'));
            
            // Remove any existing click events to prevent duplicates
            img.removeEventListener('click', handleImageClick);
            
            // Add click event
            img.addEventListener('click', handleImageClick);
            
            // Ensure cursor shows pointer and add hover effect
            img.style.cursor = 'pointer';
            img.classList.add('hover:opacity-90', 'transition-opacity', 'duration-200');
            
            console.log(`Image ${index + 1} setup complete`);
        });
    }
    
    // Handle image clicks
    function handleImageClick(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('Image clicked!', this.src, this.getAttribute('data-title'));
        
        const imageSrc = this.src;
        const title = this.getAttribute('data-title') || 'Design Image';
        
        openImageModal(imageSrc, title);
    }
    
    // Initial setup
    setupImageEvents();
    
    // Fallback: If no images found initially, try again after a short delay
    setTimeout(function() {
        const designImages = document.querySelectorAll('.design-image');
        if (designImages.length === 0) {
            console.log('No images found initially, retrying...');
            setupImageEvents();
        }
    }, 500);
    
    // Also try to setup events when images load
    document.addEventListener('load', function(e) {
        if (e.target.tagName === 'IMG' && e.target.classList.contains('design-image')) {
            console.log('Image loaded, setting up event:', e.target.src);
            setupImageEvents();
        }
    }, true);
    
    console.log('Image modal setup complete (Edit Page)');
});

// Product management functions
let productRowCounter = {{ $order->orderProducts ? $order->orderProducts->count() : 1 }};

function setupProductManagement() {
    // Add event listeners to existing product rows
    document.querySelectorAll('.product-row').forEach(row => {
        addProductRowEventListeners(row);
    });
    
    // Check for duplicates initially
    checkForDuplicateProducts();
}

function addProductRow() {
    const productsContainer = document.getElementById('products-container');
    const productRow = document.querySelector('.product-row').cloneNode(true);
    
    // Update the index for the new row
    const newIndex = productRowCounter++;
    
    // Update all the name attributes
    productRow.querySelectorAll('[name]').forEach(element => {
        element.name = element.name.replace(/\[\d+\]/, `[${newIndex}]`);
    });
    
    // Clear the values
    productRow.querySelector('.product-select').value = '';
    productRow.querySelector('.quantity-input').value = '1';
    productRow.querySelector('input[name*="comments"]').value = '';
    productRow.querySelector('.stock-info .font-medium').textContent = '-';
    
    // Add event listeners
    addProductRowEventListeners(productRow);
    
    productsContainer.appendChild(productRow);
    
    // Check for duplicate products
    checkForDuplicateProducts();
}

function removeProductRow(button) {
    const productRow = button.closest('.product-row');
    if (document.querySelectorAll('.product-row').length > 1) {
        productRow.remove();
    }
}

function addProductRowEventListeners(productRow) {
    const productSelect = productRow.querySelector('.product-select');
    const quantityInput = productRow.querySelector('.quantity-input');
    const stockInfo = productRow.querySelector('.stock-info .font-medium');
    
    productSelect.addEventListener('change', function() {
        updateStockInfo(this, stockInfo);
        checkForDuplicateProducts();
    });
    
    quantityInput.addEventListener('input', function() {
        updateStockInfo(productSelect, stockInfo);
    });
}

function updateStockInfo(productSelect, stockInfoElement) {
    const productId = productSelect.value;
    if (productId) {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const stockText = selectedOption.text.match(/Stock: (\d+)/);
        if (stockText) {
            const stock = parseInt(stockText[1]);
            const quantity = parseInt(productSelect.closest('.product-row').querySelector('.quantity-input').value) || 1;
            stockInfoElement.textContent = stock;
            
            // Show warning if quantity exceeds stock
            if (quantity > stock) {
                stockInfoElement.classList.add('text-red-600');
                stockInfoElement.textContent += ` (Warning: Quantity exceeds stock)`;
            } else {
                stockInfoElement.classList.remove('text-red-600');
            }
        }
    } else {
        stockInfoElement.textContent = '-';
        stockInfoElement.classList.remove('text-red-600');
    }
}

function checkForDuplicateProducts() {
    const selectedProducts = new Set();
    const duplicateWarnings = [];
    
    document.querySelectorAll('.product-select').forEach((select, index) => {
        const productId = select.value;
        if (productId) {
            if (selectedProducts.has(productId)) {
                duplicateWarnings.push(`Row ${index + 1}: Duplicate product selected`);
                select.classList.add('border-red-500');
            } else {
                selectedProducts.add(productId);
                select.classList.remove('border-red-500');
            }
        }
    });
    
    // Show or hide duplicate warning
    let warningDiv = document.getElementById('duplicate-warning');
    if (duplicateWarnings.length > 0) {
        if (!warningDiv) {
            warningDiv = document.createElement('div');
            warningDiv.id = 'duplicate-warning';
            warningDiv.className = 'bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-4';
            warningDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                    <div class="text-sm text-yellow-800">
                        <p class="font-medium">Duplicate Products Detected</p>
                        <p>You have selected the same product multiple times. The system will automatically combine quantities and merge comments.</p>
                    </div>
                </div>
            `;
            document.getElementById('products-container').parentNode.appendChild(warningDiv);
        }
    } else if (warningDiv) {
        warningDiv.remove();
    }
}
</script>

@endsection 