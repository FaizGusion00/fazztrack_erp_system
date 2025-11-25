@extends('layouts.app')

@section('title', $product->name . ' - Fazztrack')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                    <p class="text-gray-600">Product Details</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('products.edit', $product) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Product
                    </a>
                    <a href="{{ route('products.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Products
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Product Images -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Product Images</h2>
                </div>
                <div class="p-6">
                    @if($product->images && count($product->images) > 0)
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($product->image_urls as $index => $imageUrl)
                                <div class="relative group">
                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }} - Image {{ $index + 1 }}" 
                                         class="w-full h-48 object-cover rounded-lg cursor-pointer hover:opacity-90 transition-opacity"
                                         onclick="openImageModal('{{ $imageUrl }}', '{{ $product->name }}')">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all rounded-lg flex items-center justify-center">
                                        <i class="fas fa-search text-white opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="mx-auto h-12 w-12 text-gray-400">
                                <i class="fas fa-image text-4xl"></i>
                            </div>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No images</h3>
                            <p class="mt-1 text-sm text-gray-500">No product images uploaded yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Product Information -->
            <div class="space-y-6">
                <!-- Basic Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Product Information</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Name</label>
                                <p class="text-sm text-gray-900 font-medium">{{ $product->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Category</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $product->category ?? 'N/A' }}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Size</label>
                                <p class="text-sm text-gray-900">{{ $product->size }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Color</label>
                                <p class="text-sm text-gray-900">{{ $product->color ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Material</label>
                                <p class="text-sm text-gray-900">{{ $product->material ?? 'N/A' }}</p>
                            </div>
                        </div>

                        @if($product->description)
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Description</label>
                                <p class="text-sm text-gray-900">{{ $product->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Stock Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Stock Information</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Current Stock</label>
                                <div class="flex items-center">
                                    <span class="text-2xl font-bold text-gray-900 mr-3">{{ $product->stock }}</span>
                                    @if($product->stock <= 0)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Out of Stock
                                        </span>
                                    @elseif($product->stock < 10)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Low Stock
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            In Stock
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                                @if($product->status === 'Active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Stock Update Form -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Update Stock</h3>
                            <form action="{{ route('products.stock.update', $product) }}" method="POST" class="flex items-center space-x-3">
                                @csrf
                                <input type="number" name="stock" value="{{ $product->stock }}" min="0" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <button type="submit" 
                                        class="px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 transition-colors">
                                    Update
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                @if($product->comments)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Comments & Notes</h2>
                        </div>
                        <div class="p-6">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-info-circle text-blue-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-800 whitespace-pre-wrap">{{ $product->comments }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Related Orders -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Related Orders</h2>
                    </div>
                    <div class="p-6">
                        @if($product->orders->count() > 0)
                            <div class="space-y-3">
                                @foreach($product->orders->take(5) as $order)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Order #{{ $order->order_id }}</p>
                                            <p class="text-xs text-gray-500">{{ $order->client->name }}</p>
                                        </div>
                                        <a href="{{ route('orders.show', $order) }}" 
                                           class="text-primary-600 hover:text-primary-900 text-sm">
                                            View Order
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            @if($product->orders->count() > 5)
                                <p class="text-sm text-gray-500 mt-3">And {{ $product->orders->count() - 5 }} more orders...</p>
                            @endif
                        @else
                            <p class="text-sm text-gray-500">No orders found for this product.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="relative max-w-4xl max-h-full mx-4">
        <button onclick="closeImageModal()" class="absolute -top-10 right-0 text-white hover:text-gray-300">
            <i class="fas fa-times text-2xl"></i>
        </button>
        <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain">
        <p id="modalCaption" class="text-white text-center mt-4"></p>
    </div>
</div>

<script>
function openImageModal(imageUrl, productName) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalCaption = document.getElementById('modalCaption');
    
    modalImage.src = imageUrl;
    modalCaption.textContent = productName;
    modal.classList.remove('hidden');
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
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