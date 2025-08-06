@extends('layouts.app')

@section('title', 'Delivery Details - Fazztrack')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Delivery Details</h1>
                    <p class="text-gray-600">Order #{{ $order->order_id }} - {{ $order->client->name ?? 'N/A' }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('deliveries.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Deliveries
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Order Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-shopping-cart mr-3 text-primary-500"></i>
                    Order Information
                </h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Order ID:</span>
                        <span class="text-sm text-gray-900">#{{ $order->order_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Client:</span>
                        <span class="text-sm text-gray-900">{{ $order->client->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Product:</span>
                        <span class="text-sm text-gray-900">{{ $order->product->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Job Name:</span>
                        <span class="text-sm text-gray-900">{{ $order->job_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Total Amount:</span>
                        <span class="text-sm font-bold text-gray-900">RM {{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Delivery Method:</span>
                        @php
                            $methodColors = [
                                'Self Collect' => 'bg-orange-100 text-orange-800',
                                'Shipping' => 'bg-purple-100 text-purple-800',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $methodColors[$order->delivery_method] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $order->delivery_method }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Order Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $order->status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Delivery Summary -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-truck mr-3 text-primary-500"></i>
                    Delivery Summary
                </h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Delivery Status:</span>
                        @php
                            $deliveryColors = [
                                'Pending' => 'bg-yellow-100 text-yellow-800',
                                'In Transit' => 'bg-blue-100 text-blue-800',
                                'Delivered' => 'bg-green-100 text-green-800',
                                'Failed' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $deliveryColors[$order->delivery_status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $order->delivery_status }}
                        </span>
                    </div>
                    @if($order->tracking_number)
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Tracking Number:</span>
                            <span class="text-sm text-gray-900">{{ $order->tracking_number }}</span>
                        </div>
                    @endif
                    @if($order->delivery_company)
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Delivery Company:</span>
                            <span class="text-sm text-gray-900">{{ $order->delivery_company }}</span>
                        </div>
                    @endif
                    @if($order->delivery_date)
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Delivery Date:</span>
                            <span class="text-sm text-gray-900">{{ $order->delivery_date->format('M d, Y H:i') }}</span>
                        </div>
                    @endif
                    @if($order->delivery_notes)
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Delivery Notes:</span>
                            <span class="text-sm text-gray-900">{{ $order->delivery_notes }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Delivery Management -->
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6 flex items-center">
                <i class="fas fa-truck mr-3 text-primary-500"></i>
                Delivery Management
            </h3>
            
            <form id="delivery-form" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="delivery_status" class="block text-sm font-medium text-gray-700 mb-2">Delivery Status</label>
                        <select id="delivery_status" name="delivery_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="Pending" {{ $order->delivery_status === 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="In Transit" {{ $order->delivery_status === 'In Transit' ? 'selected' : '' }}>In Transit</option>
                            <option value="Delivered" {{ $order->delivery_status === 'Delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="Failed" {{ $order->delivery_status === 'Failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-2">Tracking Number</label>
                        <input type="text" id="tracking_number" name="tracking_number" value="{{ $order->tracking_number }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                               placeholder="Enter tracking number">
                    </div>
                    
                    <div>
                        <label for="delivery_company" class="block text-sm font-medium text-gray-700 mb-2">Delivery Company</label>
                        <input type="text" id="delivery_company" name="delivery_company" value="{{ $order->delivery_company }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                               placeholder="e.g., PosLaju, DHL, etc.">
                    </div>
                    
                    <div>
                        <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-2">Delivery Date</label>
                        <input type="datetime-local" id="delivery_date" name="delivery_date" 
                               value="{{ $order->delivery_date ? $order->delivery_date->format('Y-m-d\TH:i') : '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
                
                <div>
                    <label for="delivery_notes" class="block text-sm font-medium text-gray-700 mb-2">Delivery Notes</label>
                    <textarea id="delivery_notes" name="delivery_notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                              placeholder="Add delivery notes...">{{ $order->delivery_notes }}</textarea>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Update Delivery Status
                    </button>
                </div>
            </form>
        </div>

        <!-- Proof of Delivery -->
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6 flex items-center">
                <i class="fas fa-file-image mr-3 text-primary-500"></i>
                Proof of Delivery
            </h3>
            
            <div class="space-y-4">
                @if($order->proof_of_delivery_path)
                    <div class="flex justify-center">
                        <img src="{{ asset('storage/' . $order->proof_of_delivery_path) }}" alt="Proof of Delivery" class="max-w-md h-auto rounded-md">
                    </div>
                @else
                    <p class="text-sm text-gray-500">No proof of delivery uploaded yet.</p>
                @endif
                
                <form id="proof-of-delivery-form" class="space-y-4">
                    @csrf
                    <div>
                        <label for="proof_of_delivery" class="block text-sm font-medium text-gray-700 mb-2">Upload Proof of Delivery</label>
                        <input type="file" id="proof_of_delivery" name="proof_of_delivery" accept="image/*" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        <p class="mt-1 text-xs text-gray-500">PNG, JPG, or JPEG files up to 5MB.</p>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 transition-colors">
                            <i class="fas fa-upload mr-2"></i>
                            Upload Proof
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('delivery-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("deliveries.update-delivery", $order) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert(data.message);
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating delivery status');
    });
});

document.getElementById('proof-of-delivery-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("deliveries.upload-proof", $order) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert(data.message);
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error uploading proof of delivery');
    });
});
</script>
@endsection 