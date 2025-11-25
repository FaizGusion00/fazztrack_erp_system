@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create New Order</h1>
                    <p class="mt-2 text-gray-600">Add a new work order for T-shirt printing</p>
                </div>
                <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Orders
                </a>
            </div>
        </div>

        <!-- Order Form -->
        <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Work Order Details</h3>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Client Selection -->
                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Customer <span class="text-red-500">*</span>
                        </label>
                        <select name="client_id" id="client_id" required class="block w-full border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Select a customer</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->client_id }}" {{ old('client_id') == $client->client_id ? 'selected' : '' }}>
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
                            <!-- Product Row Template -->
                            <div class="product-row bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                                        <select name="products[0][product_id]" required 
                                                class="block w-full border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 product-select">
                                            <option value="">Select a product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->product_id }}">
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
>>>>>>> 3710a4358d7c142e15038a7986c16e95d72df9e6
                                    </div>
                                </div>
                            </div>
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

                    <!-- Job Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="job_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Job Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="job_name" id="job_name" value="{{ old('job_name') }}" required 
                                   class="block w-full border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="e.g., Company T-Shirts, Event T-Shirts">
                            @error('job_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="delivery_method" class="block text-sm font-medium text-gray-700 mb-2">
                                Delivery Method <span class="text-red-500">*</span>
                            </label>
                            <select name="delivery_method" id="delivery_method" required class="block w-full border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Select delivery method</option>
                                <option value="Self Collect" {{ old('delivery_method') == 'Self Collect' ? 'selected' : '' }}>Self Collect</option>
                                <option value="Shipping" {{ old('delivery_method') == 'Shipping' ? 'selected' : '' }}>Shipping</option>
                    <option value="Grab" {{ old('delivery_method') == 'Grab' ? 'selected' : '' }}>Grab</option>
                    <option value="Lalamove" {{ old('delivery_method') == 'Lalamove' ? 'selected' : '' }}>Lalamove</option>
                    <option value="Bus" {{ old('delivery_method') == 'Bus' ? 'selected' : '' }}>Bus</option>
                            </select>
                            @error('delivery_method')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Payment Details</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="design_deposit" class="block text-sm font-medium text-gray-700 mb-2">
                                    Design Deposit (RM) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="design_deposit" id="design_deposit" value="{{ old('design_deposit') }}" step="0.01" required 
                                       class="block w-full border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="0.00">
                                @error('design_deposit')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="production_deposit" class="block text-sm font-medium text-gray-700 mb-2">
                                    Production Deposit (RM) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="production_deposit" id="production_deposit" value="{{ old('production_deposit') }}" step="0.01" required 
                                       class="block w-full border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="0.00">
                                @error('production_deposit')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="balance_payment" class="block text-sm font-medium text-gray-700 mb-2">
                                    Balance Payment (RM) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="balance_payment" id="balance_payment" value="{{ old('balance_payment') }}" step="0.01" required 
                                       class="block w-full border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="0.00">
                                @error('balance_payment')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Total Amount Display -->
                        <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-medium text-green-800">Total Amount:</span>
                                <span class="text-2xl font-bold text-green-800" id="total-amount">RM 0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Due Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="due_date_design" class="block text-sm font-medium text-gray-700 mb-2">
                                Design Due Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="due_date_design" id="due_date_design" value="{{ old('due_date_design') }}" required 
                                   class="block w-full border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                            @error('due_date_design')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="due_date_production" class="block text-sm font-medium text-gray-700 mb-2">
                                Production Due Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="due_date_production" id="due_date_production" value="{{ old('due_date_production') }}" required 
                                   class="block w-full border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                            @error('due_date_production')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- File Uploads -->
                    <div class="space-y-4">
                        <h4 class="text-md font-medium text-gray-900">File Uploads</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="receipts" class="block text-sm font-medium text-gray-700 mb-2">
                                    Receipts
                                </label>
                                <div class="flex items-center justify-center w-full">
                                    <label for="receipts" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <i class="fas fa-file-upload text-gray-400 text-2xl mb-2"></i>
                                            <p class="mb-2 text-sm text-gray-500">
                                                <span class="font-semibold">Click to upload</span> or drag and drop
                                            </p>
                                            <p class="text-xs text-gray-500">PDF, JPG, PNG (MAX. 10MB)</p>
                                        </div>
                                        <input id="receipts" name="receipts[]" type="file" class="hidden" multiple accept=".pdf,.jpg,.jpeg,.png">
                                    </label>
                                </div>
                                @error('receipts')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="job_sheets" class="block text-sm font-medium text-gray-700 mb-2">
                                    Job Sheet
                                </label>
                                <div class="flex items-center justify-center w-full">
                                    <label for="job_sheets" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <i class="fas fa-file-upload text-gray-400 text-2xl mb-2"></i>
                                            <p class="mb-2 text-sm text-gray-500">
                                                <span class="font-semibold">Click to upload</span> or drag and drop
                                            </p>
                                            <p class="text-xs text-gray-500">PDF, JPG, PNG (MAX. 10MB)</p>
                                            <p class="text-xs text-gray-400 mt-1">You can select multiple files at once</p>
                                        </div>
                                        <input id="job_sheets" name="job_sheets[]" type="file" class="hidden" multiple accept=".pdf,.jpg,.jpeg,.png">
                                    </label>
                                </div>
                                @error('job_sheets')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @error('job_sheets.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- File Preview - Separated by Type -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <!-- Receipts Preview -->
                            <div id="receipts-preview" class="hidden">
                                <h5 class="text-sm font-medium text-gray-700 mb-2">Receipts:</h5>
                                <div id="receipts-list" class="space-y-2"></div>
                            </div>
                            
                            <!-- Job Sheets Preview -->
                            <div id="job-sheets-preview" class="hidden">
                                <h5 class="text-sm font-medium text-gray-700 mb-2">Job Sheets:</h5>
                                <div id="job-sheets-list" class="space-y-2"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Design Views (Optional) -->
                    <div class="space-y-4">
                        <h4 class="text-md font-medium text-gray-900">Design Views <span class="text-gray-500 text-sm font-normal">(Optional)</span></h4>
                        <div>
                            <label for="design_images" class="block text-sm font-medium text-gray-700 mb-2">
                                Design Images (Multiple) <span class="text-gray-500 font-normal text-xs">- Optional, for customer reference only</span>
                            </label>
                            <div class="flex items-center justify-center w-full">
                                <label for="design_images" class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fas fa-images text-gray-400 text-2xl mb-2"></i>
                                        <p class="mb-2 text-sm text-gray-500">
                                            <span class="font-semibold">Click to upload</span> or drag and drop
                                        </p>
                                        <p class="text-xs text-gray-500">JPG, PNG (MAX. 20MB per image)</p>
                                        <p class="text-xs text-gray-400 mt-1">You can select multiple images at once</p>
                                        </div>
=======
                        <div>
                            <label for="design_images" class="block text-sm font-medium text-gray-700 mb-2">
                                Design Images (Multiple) <span class="text-gray-500 font-normal text-xs">- Optional, for customer reference only</span>
                            </label>
                            <div class="flex items-center justify-center w-full">
                                <label for="design_images" class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fas fa-images text-gray-400 text-2xl mb-2"></i>
                                        <p class="mb-2 text-sm text-gray-500">
                                            <span class="font-semibold">Click to upload</span> or drag and drop
                                        </p>
                                        <p class="text-xs text-gray-500">JPG, PNG (MAX. 20MB per image)</p>
                                        <p class="text-xs text-gray-400 mt-1">You can select multiple images at once</p>
                                    </div>
>>>>>>> 3710a4358d7c142e15038a7986c16e95d72df9e6
                                    <input id="design_images" name="design_images[]" type="file" class="hidden" accept=".jpg,.jpeg,.png" multiple>
                                </label>
                            </div>
                            <div class="mt-2 bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <p class="text-xs text-blue-800">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <strong>Note:</strong> Design images are optional during order creation. If customer provides reference images, you can upload them here. Sales manager can add or update design images later when finalizing the order.
                                </p>
                            </div>
                            @error('design_images')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('design_images.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            
                            <!-- Image Preview Container -->
                            <div id="design-images-preview" class="mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 hidden">
                                <h5 class="col-span-full text-sm font-medium text-gray-700 mb-2">Selected Images:</h5>
                            </div>
                        </div>
                    </div>

                    <!-- Remarks -->
                    <div>
                        <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">
                            Remarks
                        </label>
                        <textarea name="remarks" id="remarks" rows="4" 
                                  class="block w-full border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                  placeholder="Additional notes, special instructions, or requirements...">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('orders.index') }}" 
                   class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-save mr-2"></i>
                    Create Order
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Calculate total amount in real-time
function calculateTotal() {
    const designDeposit = parseFloat(document.getElementById('design_deposit').value) || 0;
    const productionDeposit = parseFloat(document.getElementById('production_deposit').value) || 0;
    const balancePayment = parseFloat(document.getElementById('balance_payment').value) || 0;
    
    const total = designDeposit + productionDeposit + balancePayment;
    document.getElementById('total-amount').textContent = 'RM ' + total.toFixed(2);
}

// Add event listeners to payment fields
document.getElementById('design_deposit').addEventListener('input', calculateTotal);
document.getElementById('production_deposit').addEventListener('input', calculateTotal);
document.getElementById('balance_payment').addEventListener('input', calculateTotal);

// Calculate initial total
calculateTotal();

// File upload preview functionality - Separated by type
document.addEventListener('DOMContentLoaded', function() {
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
                    newHeading.textContent = 'Selected Images:';
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
                                    <img src="${e.target.result}" alt="Preview ${index + 1}" class="w-full h-32 object-cover">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 flex items-center justify-center">
                                        <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 truncate" title="${file.name}">${file.name}</p>
                                <p class="text-xs text-gray-400">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                            `;
                            designImagesPreview.appendChild(previewItem);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                designImagesPreview.classList.add('hidden');
            }
        });
    }
});

// Product row management functions
let productRowCounter = 1;

function addProductRow() {
    const productsContainer = document.getElementById('products-container');
    const productRow = document.querySelector('.product-row').cloneNode(true);
    
    // Update the index for the new row
    const newIndex = productRowCounter++;
    
    // Update all the name attributes
    productRow.querySelectorAll('[name]').forEach(element => {
        element.name = element.name.replace('[0]', `[${newIndex}]`);
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
    if (duplicatedProductSelect && duplicatedProductSelect.value) {
        updateStockInfo(duplicatedProductSelect, duplicatedStockInfo);
    } else {
        duplicatedStockInfo.textContent = '-';
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

// Add event listeners to the initial product row
document.addEventListener('DOMContentLoaded', function() {
    const initialProductRow = document.querySelector('.product-row');
    if (initialProductRow) {
        addProductRowEventListeners(initialProductRow);
    }
});
</script>
@endsection 