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
                                <label for="job_sheet" class="block text-sm font-medium text-gray-700 mb-2">
                                    Job Sheet
                                </label>
                                <div class="flex items-center justify-center w-full">
                                    <label for="job_sheet" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <i class="fas fa-file-upload text-gray-400 text-2xl mb-2"></i>
                                            <p class="mb-2 text-sm text-gray-500">
                                                <span class="font-semibold">Click to upload</span> or drag and drop
                                            </p>
                                            <p class="text-xs text-gray-500">PDF, JPG, PNG (MAX. 10MB)</p>
                                        </div>
                                        <input id="job_sheet" name="job_sheet" type="file" class="hidden" accept=".pdf,.jpg,.jpeg,.png">
                                    </label>
                                </div>
                                @error('job_sheet')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- File Preview -->
                        <div id="file-preview" class="hidden">
                            <h5 class="text-sm font-medium text-gray-700 mb-2">Uploaded Files:</h5>
                            <div id="file-list" class="space-y-2"></div>
                        </div>
                    </div>

                    <!-- Design Views -->
                    <div class="space-y-4">
                        <h4 class="text-md font-medium text-gray-900">Design Views</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="design_front" class="block text-sm font-medium text-gray-700 mb-2">
                                    Design Front
                                </label>
                                <div class="flex items-center justify-center w-full">
                                    <label for="design_front" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <i class="fas fa-image text-gray-400 text-2xl mb-2"></i>
                                            <p class="mb-2 text-sm text-gray-500">
                                                <span class="font-semibold">Click to upload</span> front design
                                            </p>
                                            <p class="text-xs text-gray-500">JPG, PNG (MAX. 5MB)</p>
                                        </div>
                                        <input id="design_front" name="design_front" type="file" class="hidden" accept=".jpg,.jpeg,.png">
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label for="design_back" class="block text-sm font-medium text-gray-700 mb-2">
                                    Design Back
                                </label>
                                <div class="flex items-center justify-center w-full">
                                    <label for="design_back" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <i class="fas fa-image text-gray-400 text-2xl mb-2"></i>
                                            <p class="mb-2 text-sm text-gray-500">
                                                <span class="font-semibold">Click to upload</span> back design
                                            </p>
                                            <p class="text-xs text-gray-500">JPG, PNG (MAX. 5MB)</p>
                                        </div>
                                        <input id="design_back" name="design_back" type="file" class="hidden" accept=".jpg,.jpeg,.png">
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label for="design_left" class="block text-sm font-medium text-gray-700 mb-2">
                                    Design Left
                                </label>
                                <div class="flex items-center justify-center w-full">
                                    <label for="design_left" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <i class="fas fa-image text-gray-400 text-2xl mb-2"></i>
                                            <p class="mb-2 text-sm text-gray-500">
                                                <span class="font-semibold">Click to upload</span> left design
                                            </p>
                                            <p class="text-xs text-gray-500">JPG, PNG (MAX. 5MB)</p>
                                        </div>
                                        <input id="design_left" name="design_left" type="file" class="hidden" accept=".jpg,.jpeg,.png">
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label for="design_right" class="block text-sm font-medium text-gray-700 mb-2">
                                    Design Right
                                </label>
                                <div class="flex items-center justify-center w-full">
                                    <label for="design_right" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <i class="fas fa-image text-gray-400 text-2xl mb-2"></i>
                                            <p class="mb-2 text-sm text-gray-500">
                                                <span class="font-semibold">Click to upload</span> right design
                                            </p>
                                            <p class="text-xs text-gray-500">JPG, PNG (MAX. 5MB)</p>
                                        </div>
                                        <input id="design_right" name="design_right" type="file" class="hidden" accept=".jpg,.jpeg,.png">
                                    </label>
                                </div>
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

// File upload preview functionality
document.addEventListener('DOMContentLoaded', function() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    const filePreview = document.getElementById('file-preview');
    const fileList = document.getElementById('file-list');

    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            if (files.length > 0) {
                filePreview.classList.remove('hidden');
                
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
                    fileList.appendChild(fileItem);
                });
            }
        });
    });
});
</script>
@endsection 