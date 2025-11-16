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
                                        <div class="flex items-center space-x-3">
                                            <button type="button" 
                                                    onclick="duplicateProductRow(this)"
                                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                                                    title="Duplicate this product">
                                                <i class="fas fa-copy mr-1"></i>
                                                Copy
                                            </button>
                                            <button type="button" 
                                                    onclick="removeProductRow(this)"
                                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                <i class="fas fa-trash mr-1"></i>
                                                Remove
                                            </button>
                                        </div>
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
                                        <div class="flex items-center space-x-3">
                                            <button type="button" 
                                                    onclick="duplicateProductRow(this)"
                                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                                                    title="Duplicate this product">
                                                <i class="fas fa-copy mr-1"></i>
                                                Copy
                                            </button>
                                            <button type="button" 
                                                    onclick="removeProductRow(this)"
                                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                <i class="fas fa-trash mr-1"></i>
                                                Remove
                                            </button>
                                        </div>
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
                        <p class="mt-1 text-sm text-gray-500">Leave empty to keep existing files. New files will be added to existing ones.</p>
                        
                        <!-- Existing Receipts Display -->
                        @if($order->receipts()->count() > 0)
                        <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                            <h5 class="text-sm font-medium text-gray-700 mb-2">Existing Receipts ({{ $order->receipts()->count() }}):</h5>
                            <div class="space-y-2" id="existing-receipts-list">
                                @foreach($order->receipts()->orderBy('uploaded_at', 'desc')->get() as $receipt)
                                @php
                                    $receiptFileExt = strtolower(pathinfo($receipt->file_name, PATHINFO_EXTENSION));
                                    $receiptIsImage = in_array($receiptFileExt, ['jpg', 'jpeg', 'png', 'gif']);
                                    $receiptFileType = $receiptIsImage ? 'image' : 'pdf';
                                @endphp
                                <div class="flex items-center justify-between py-2 px-3 bg-white rounded-lg hover:bg-gray-100 transition-colors existing-receipt-item" data-receipt-id="{{ $receipt->id }}">
                                    <div class="flex items-center flex-1 min-w-0">
                                        <span class="text-gray-600 truncate text-xs flex-1 mr-2 cursor-pointer"
                                              onclick="openFilePreviewModal('{{ asset('storage/' . $receipt->file_path) }}', '{{ $receipt->file_name }}', '{{ $receiptFileType }}')">
                                            <i class="fas {{ $receiptIsImage ? 'fa-image' : 'fa-file-pdf' }} mr-1"></i>
                                            {{ $receipt->file_name }}
                                        </span>
                                        <span class="text-gray-400 text-xs mr-2">{{ number_format($receipt->file_size / 1024, 1) }} KB</span>
                                    </div>
                                    <button type="button" 
                                            onclick="removeReceipt({{ $receipt->id }})"
                                            class="text-red-600 hover:text-red-800 hover:bg-red-50 p-1 rounded transition-colors"
                                            title="Delete receipt">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                    <input type="hidden" name="delete_receipts[]" id="delete_receipt_{{ $receipt->id }}" value="">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <div>
                        <label for="job_sheets" class="block text-sm font-medium text-gray-700 mb-2">Job Sheet (Multiple)</label>
                        <input type="file" 
                               id="job_sheets" 
                               name="job_sheets[]" 
                               multiple
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <p class="mt-1 text-sm text-gray-500">Leave empty to keep existing files. New files will be added to existing ones.</p>
                        
                        <!-- Existing Job Sheets Display -->
                        @php
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
                        @if(count($jobSheets) > 0)
                        <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                            <h5 class="text-sm font-medium text-gray-700 mb-2">Existing Job Sheets ({{ count($jobSheets) }}):</h5>
                            <div class="space-y-2" id="existing-job-sheets-list">
                                @foreach($jobSheets as $index => $jobSheet)
                                @php
                                    $fileExt = strtolower(pathinfo($jobSheet, PATHINFO_EXTENSION));
                                    $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif']);
                                    $fileType = $isImage ? 'image' : 'pdf';
                                @endphp
                                <div class="flex items-center justify-between text-xs py-2 px-3 bg-white rounded-lg hover:bg-gray-100 transition-colors existing-job-sheet-item" data-job-sheet-index="{{ $index }}" data-job-sheet-path="{{ $jobSheet }}">
                                    <span class="text-gray-600 truncate flex-1 mr-2 cursor-pointer"
                                          onclick="openFilePreviewModal('@fileUrl($jobSheet)', '{{ basename($jobSheet) }}', '{{ $fileType }}')">
                                        <i class="fas {{ $isImage ? 'fa-image' : 'fa-file-alt' }} mr-1"></i>
                                        {{ basename($jobSheet) }}
                                    </span>
                                    <button type="button" 
                                            onclick="removeJobSheet('{{ $jobSheet }}', {{ $index }})"
                                            class="text-red-600 hover:text-red-800 hover:bg-red-50 p-1 rounded transition-colors ml-2"
                                            title="Delete job sheet">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                    <input type="hidden" name="delete_job_sheets[]" id="delete_job_sheet_{{ $index }}" value="">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- File Preview - Separated by Type -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <!-- Receipts Preview -->
                    <div id="receipts-preview" class="hidden">
                        <h5 class="text-sm font-medium text-gray-700 mb-2">New Receipts to Upload:</h5>
                        <div id="receipts-list" class="space-y-2"></div>
                    </div>
                    
                    <!-- Job Sheets Preview -->
                    <div id="job-sheets-preview" class="hidden">
                        <h5 class="text-sm font-medium text-gray-700 mb-2">New Job Sheets to Upload:</h5>
                        <div id="job-sheets-list" class="space-y-2"></div>
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
                            @php
                                $existingImages = $order->getDesignFilesArray();
                                $imageCount = is_array($existingImages) ? count(array_filter($existingImages)) : 0;
                            @endphp
                            <span class="ml-auto text-sm font-normal text-gray-600 bg-white px-3 py-1 rounded-full border border-white-200">
                                {{ $imageCount }} image{{ $imageCount !== 1 ? 's' : '' }} uploaded
                            </span>
                        </h4>
                        
                        <!-- Existing Images Gallery -->
                        @php
                            $designFiles = $order->getDesignFilesArray();
                            $existingDesignImages = [];
                            // Support both old format (keyed) and new format (array)
                            if (is_array($designFiles)) {
                                foreach ($designFiles as $key => $value) {
                                    if (is_numeric($key)) {
                                        // New format: array of paths
                                        $existingDesignImages[] = $value;
                                    } else {
                                        // Old format: keyed array (design_front, design_back, etc.)
                                        if (!empty($value)) {
                                            $existingDesignImages[] = $value;
                                        }
                                    }
                                }
                            }
                        @endphp
                        
                        @if(count($existingDesignImages) > 0)
                        <div class="mb-6">
                            <h5 class="text-sm font-medium text-gray-700 mb-3">Existing Design Images:</h5>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="existing-design-images">
                                @foreach($existingDesignImages as $index => $imagePath)
                                <div class="relative group existing-image-item" data-image-path="{{ $imagePath }}" data-image-index="{{ $index }}">
                                    <div class="relative overflow-hidden rounded-lg border-2 border-gray-200 hover:border-blue-300 transition-colors">
                                        <img src="@fileUrl($imagePath)" 
                                             alt="Design Image {{ $index + 1 }}" 
                                             class="w-full h-36 object-cover cursor-pointer hover:scale-105 transition-transform duration-300 design-image"
                                             data-title="Design Image {{ $index + 1 }}">
                                        <!-- Delete Button -->
                                        <button type="button" 
                                                onclick="removeDesignImage('{{ $imagePath }}', {{ $index }})"
                                                class="absolute top-2 right-2 bg-red-600 text-white p-1.5 rounded-full opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-700 z-10"
                                                title="Delete image">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
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
                                    <p class="text-xs text-gray-500 mt-2 text-center truncate" title="{{ basename($imagePath) }}">
                                        <i class="fas fa-file-image mr-1"></i>{{ basename($imagePath) }}
                                    </p>
                                    <input type="hidden" name="delete_design_images[]" id="delete_design_image_{{ $index }}" value="">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <!-- Upload New Images -->
                        <div class="mt-6">
                            <label for="design_images" class="block text-sm font-medium text-gray-700 mb-2">
                                Add More Design Images (Multiple)
                            </label>
                            <div class="flex items-center justify-center w-full">
                                <label for="design_images" class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-gray-50 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fas fa-images text-gray-400 text-2xl mb-2"></i>
                                        <p class="mb-2 text-sm text-gray-500">
                                            <span class="font-semibold">Click to upload</span> or drag and drop
                                        </p>
                                        <p class="text-xs text-gray-500">JPG, PNG (MAX. 20MB per image)</p>
                                        <p class="text-xs text-gray-400 mt-1">You can select multiple images at once</p>
                                    </div>
                                    <input id="design_images" name="design_images[]" type="file" class="hidden" accept=".jpg,.jpeg,.png" multiple>
                                </label>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Upload additional design images. New images will be added to existing ones.
                            </p>
                            @error('design_images')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('design_images.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            
                            <!-- New Images Preview Container -->
                            <div id="design-images-preview" class="mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 hidden">
                                <h5 class="col-span-full text-sm font-medium text-gray-700 mb-2">New Images to Upload:</h5>
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Setting up image modal functionality (Edit Page)...');
    
    // Setup product management
    setupProductManagement();
    
    // Design images preview functionality
    const designImagesInput = document.getElementById('design_images');
    const designImagesPreview = document.getElementById('design-images-preview');
    
    if (designImagesInput && designImagesPreview) {
        designImagesInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            if (files.length > 0) {
                designImagesPreview.classList.remove('hidden');
                
                // Clear previous previews (except the heading)
                const heading = designImagesPreview.querySelector('h5');
                designImagesPreview.innerHTML = '';
                if (heading) {
                    designImagesPreview.appendChild(heading);
                } else {
                    const newHeading = document.createElement('h5');
                    newHeading.className = 'col-span-full text-sm font-medium text-gray-700 mb-2';
                    newHeading.textContent = 'New Images to Upload:';
                    designImagesPreview.appendChild(newHeading);
                }
                
                files.forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const previewItem = document.createElement('div');
                            previewItem.className = 'relative group';
                            previewItem.innerHTML = `
                                <div class="relative overflow-hidden rounded-lg border-2 border-gray-200 hover:border-blue-300 transition-colors">
                                    <img src="${e.target.result}" alt="Preview ${index + 1}" class="w-full h-32 object-cover cursor-pointer design-image" data-title="New Upload Preview ${index + 1}">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 flex items-center justify-center">
                                        <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 truncate" title="${file.name}">${file.name}</p>
                                <p class="text-xs text-gray-400">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                            `;
                            designImagesPreview.appendChild(previewItem);
                            
                            // Add click event listener to the new preview image
                            const previewImg = previewItem.querySelector('.design-image');
                            if (previewImg) {
                                previewImg.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    openImageModal(this.src, this.getAttribute('data-title'));
                                });
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                designImagesPreview.classList.add('hidden');
            }
        });
    }
    
    // File upload preview functionality - Separated by type
    // Receipts preview
    const receiptsInput = document.getElementById('receipts');
    const receiptsPreview = document.getElementById('receipts-preview');
    const receiptsList = document.getElementById('receipts-list');
    
    if (receiptsInput && receiptsPreview && receiptsList) {
        receiptsInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            if (files.length > 0) {
                receiptsPreview.classList.remove('hidden');
                receiptsList.innerHTML = ''; // Clear previous previews
                
                files.forEach(file => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'flex items-center justify-between p-2 bg-gray-100 rounded';
                    fileItem.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas fa-file text-gray-500 mr-2"></i>
                            <span class="text-sm text-gray-700">${file.name}</span>
                        </div>
                        <span class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</span>
                    `;
                    receiptsList.appendChild(fileItem);
                });
            } else {
                receiptsPreview.classList.add('hidden');
            }
        });
    }
    
    // Job sheets preview
    const jobSheetsInput = document.getElementById('job_sheets');
    const jobSheetsPreview = document.getElementById('job-sheets-preview');
    const jobSheetsList = document.getElementById('job-sheets-list');
    
    if (jobSheetsInput && jobSheetsPreview && jobSheetsList) {
        jobSheetsInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            if (files.length > 0) {
                jobSheetsPreview.classList.remove('hidden');
                jobSheetsList.innerHTML = ''; // Clear previous previews
                
                files.forEach(file => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'flex items-center justify-between p-2 bg-gray-100 rounded';
                    fileItem.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas fa-file text-gray-500 mr-2"></i>
                            <span class="text-sm text-gray-700">${file.name}</span>
                        </div>
                        <span class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</span>
                    `;
                    jobSheetsList.appendChild(fileItem);
                });
            } else {
                jobSheetsPreview.classList.add('hidden');
            }
        });
    }
    
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

function duplicateProductRow(button) {
    const productRow = button.closest('.product-row');
    const productsContainer = document.getElementById('products-container');
    
    // Clone the product row
    const duplicatedRow = productRow.cloneNode(true);
    
    // Update the index for the new row
    const newIndex = productRowCounter++;
    
    // Update all the name attributes
    duplicatedRow.querySelectorAll('[name]').forEach(element => {
        const nameAttr = element.getAttribute('name');
        if (nameAttr) {
            // Extract current index from name (e.g., products[0][product_id] -> 0)
            const match = nameAttr.match(/\[(\d+)\]/);
            if (match) {
                element.name = nameAttr.replace(`[${match[1]}]`, `[${newIndex}]`);
            }
        }
    });
    
    // Copy all values from original row
    const originalProductSelect = productRow.querySelector('.product-select');
    const originalQuantityInput = productRow.querySelector('.quantity-input');
    const originalCommentsInput = productRow.querySelector('input[name*="comments"]');
    
    const duplicatedProductSelect = duplicatedRow.querySelector('.product-select');
    const duplicatedQuantityInput = duplicatedRow.querySelector('.quantity-input');
    const duplicatedCommentsInput = duplicatedRow.querySelector('input[name*="comments"]');
    const duplicatedStockInfo = duplicatedRow.querySelector('.stock-info .font-medium');
    
    // Copy values
    if (originalProductSelect && duplicatedProductSelect) {
        duplicatedProductSelect.value = originalProductSelect.value;
    }
    if (originalQuantityInput && duplicatedQuantityInput) {
        duplicatedQuantityInput.value = originalQuantityInput.value;
    }
    if (originalCommentsInput && duplicatedCommentsInput) {
        duplicatedCommentsInput.value = originalCommentsInput.value;
    }
    
    // Update stock info if product is selected
    if (duplicatedProductSelect && duplicatedStockInfo) {
        if (duplicatedProductSelect.value) {
            updateStockInfo(duplicatedProductSelect, duplicatedStockInfo);
        } else {
            duplicatedStockInfo.textContent = '-';
        }
    }
    
    // Add event listeners to the duplicated row
    addProductRowEventListeners(duplicatedRow);
    
    // Insert the duplicated row after the original row
    productRow.parentNode.insertBefore(duplicatedRow, productRow.nextSibling);
    
    // Check for duplicate products
    checkForDuplicateProducts();
}

function removeProductRow(button) {
    const productRow = button.closest('.product-row');
    if (document.querySelectorAll('.product-row').length > 1) {
        productRow.remove();
        // Re-check for duplicates after removal
        checkForDuplicateProducts();
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

// File deletion functions
function removeReceipt(receiptId) {
    if (confirm('Are you sure you want to delete this receipt?')) {
        const receiptItem = document.querySelector(`[data-receipt-id="${receiptId}"]`);
        const deleteInput = document.getElementById(`delete_receipt_${receiptId}`);
        
        if (deleteInput) {
            deleteInput.value = receiptId;
        }
        
        if (receiptItem) {
            receiptItem.style.opacity = '0.5';
            receiptItem.style.pointerEvents = 'none';
            receiptItem.querySelector('button').disabled = true;
        }
    }
}

function removeJobSheet(jobSheetPath, index) {
    if (confirm('Are you sure you want to delete this job sheet?')) {
        const jobSheetItem = document.querySelector(`[data-job-sheet-index="${index}"]`);
        const deleteInput = document.getElementById(`delete_job_sheet_${index}`);
        
        if (deleteInput) {
            deleteInput.value = jobSheetPath;
        }
        
        if (jobSheetItem) {
            jobSheetItem.style.opacity = '0.5';
            jobSheetItem.style.pointerEvents = 'none';
            jobSheetItem.querySelector('button').disabled = true;
        }
    }
}

function removeDesignImage(imagePath, index) {
    if (confirm('Are you sure you want to delete this design image?')) {
        const imageItem = document.querySelector(`[data-image-index="${index}"]`);
        const deleteInput = document.getElementById(`delete_design_image_${index}`);
        
        if (deleteInput) {
            deleteInput.value = imagePath;
        }
        
        if (imageItem) {
            imageItem.style.opacity = '0.5';
            imageItem.style.pointerEvents = 'none';
            imageItem.querySelector('button').disabled = true;
        }
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

<!-- File Preview Modal (Receipts & Job Sheets) - Same as show page -->
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
                     onerror="this.parentElement.innerHTML='<div class=\\'text-white text-center\\'><i class=\\'fas fa-exclamation-triangle text-4xl mb-4\\'></i><p>Failed to load image</p></div>'">
                
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
// File Preview Modal Functions (for Receipts and Job Sheets)
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