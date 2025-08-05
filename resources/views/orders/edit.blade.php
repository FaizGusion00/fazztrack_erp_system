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
                <div class="mt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-palette mr-2 text-primary-500"></i>
                        Design Views
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Front View -->
                        <div class="space-y-3">
                            <label for="design_front" class="block text-sm font-medium text-gray-700">Front View</label>
                            <div class="relative">
                                @if($order->design_front)
                                    <div class="mb-3">
                                        <img src="{{ Storage::url($order->design_front) }}" 
                                             alt="Front Design" 
                                             class="w-full h-32 object-cover rounded-lg border border-gray-200">
                                        <p class="text-xs text-gray-500 mt-1">Current: {{ basename($order->design_front) }}</p>
                                    </div>
                                @endif
                                <div class="flex items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <div class="text-center">
                                        <i class="fas fa-upload text-gray-400 text-2xl mb-2"></i>
                                        <p class="text-sm text-gray-600">Click to upload or drag & drop</p>
                                        <p class="text-xs text-gray-500">JPG, PNG up to 5MB</p>
                                    </div>
                                    <input type="file" 
                                           id="design_front" 
                                           name="design_front" 
                                           accept=".jpg,.jpeg,.png"
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                </div>
                            </div>
                        </div>

                        <!-- Back View -->
                        <div class="space-y-3">
                            <label for="design_back" class="block text-sm font-medium text-gray-700">Back View</label>
                            <div class="relative">
                                @if($order->design_back)
                                    <div class="mb-3">
                                        <img src="{{ Storage::url($order->design_back) }}" 
                                             alt="Back Design" 
                                             class="w-full h-32 object-cover rounded-lg border border-gray-200">
                                        <p class="text-xs text-gray-500 mt-1">Current: {{ basename($order->design_back) }}</p>
                                    </div>
                                @endif
                                <div class="flex items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <div class="text-center">
                                        <i class="fas fa-upload text-gray-400 text-2xl mb-2"></i>
                                        <p class="text-sm text-gray-600">Click to upload or drag & drop</p>
                                        <p class="text-xs text-gray-500">JPG, PNG up to 5MB</p>
                                    </div>
                                    <input type="file" 
                                           id="design_back" 
                                           name="design_back" 
                                           accept=".jpg,.jpeg,.png"
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                </div>
                            </div>
                        </div>

                        <!-- Left View -->
                        <div class="space-y-3">
                            <label for="design_left" class="block text-sm font-medium text-gray-700">Left View</label>
                            <div class="relative">
                                @if($order->design_left)
                                    <div class="mb-3">
                                        <img src="{{ Storage::url($order->design_left) }}" 
                                             alt="Left Design" 
                                             class="w-full h-32 object-cover rounded-lg border border-gray-200">
                                        <p class="text-xs text-gray-500 mt-1">Current: {{ basename($order->design_left) }}</p>
                                    </div>
                                @endif
                                <div class="flex items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <div class="text-center">
                                        <i class="fas fa-upload text-gray-400 text-2xl mb-2"></i>
                                        <p class="text-sm text-gray-600">Click to upload or drag & drop</p>
                                        <p class="text-xs text-gray-500">JPG, PNG up to 5MB</p>
                                    </div>
                                    <input type="file" 
                                           id="design_left" 
                                           name="design_left" 
                                           accept=".jpg,.jpeg,.png"
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                </div>
                            </div>
                        </div>

                        <!-- Right View -->
                        <div class="space-y-3">
                            <label for="design_right" class="block text-sm font-medium text-gray-700">Right View</label>
                            <div class="relative">
                                @if($order->design_right)
                                    <div class="mb-3">
                                        <img src="{{ Storage::url($order->design_right) }}" 
                                             alt="Right Design" 
                                             class="w-full h-32 object-cover rounded-lg border border-gray-200">
                                        <p class="text-xs text-gray-500 mt-1">Current: {{ basename($order->design_right) }}</p>
                                    </div>
                                @endif
                                <div class="flex items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <div class="text-center">
                                        <i class="fas fa-upload text-gray-400 text-2xl mb-2"></i>
                                        <p class="text-sm text-gray-600">Click to upload or drag & drop</p>
                                        <p class="text-xs text-gray-500">JPG, PNG up to 5MB</p>
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
@endsection 