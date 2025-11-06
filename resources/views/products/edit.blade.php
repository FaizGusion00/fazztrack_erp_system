@extends('layouts.app')

@section('title', 'Edit ' . $product->name . ' - Fazztrack')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Product</h1>
                    <p class="text-gray-600">Update product information and inventory</p>
                </div>
                <a href="{{ route('products.show', $product) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Product
                </a>
            </div>
        </div>

        <!-- Product Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Edit Product Information</h2>
            </div>

            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="description" name="description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select id="category" name="category"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">Select Category</option>
                                <option value="T-Shirt" {{ old('category', $product->category) == 'T-Shirt' ? 'selected' : '' }}>T-Shirt</option>
                                <option value="Hoodie" {{ old('category', $product->category) == 'Hoodie' ? 'selected' : '' }}>Hoodie</option>
                                <option value="Cap" {{ old('category', $product->category) == 'Cap' ? 'selected' : '' }}>Cap</option>
                                <option value="Polo" {{ old('category', $product->category) == 'Polo' ? 'selected' : '' }}>Polo</option>
                                <option value="Jacket" {{ old('category', $product->category) == 'Jacket' ? 'selected' : '' }}>Jacket</option>
                                <option value="Other" {{ old('category', $product->category) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="size" class="block text-sm font-medium text-gray-700 mb-2">Size *</label>
                            <input type="text" id="size" name="size" value="{{ old('size', $product->size) }}" required
                                   class="block w-full border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="e.g., S, M, L, XL, XXL">
                            @error('size')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Pricing and Stock -->
                    <div class="space-y-6">
                        <div>
                            <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">Current Stock *</label>
                            <input type="number" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                            @error('stock')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                            <input type="text" id="color" name="color" value="{{ old('color', $product->color) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                            @error('color')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="material" class="block text-sm font-medium text-gray-700 mb-2">Material</label>
                            <input type="text" id="material" name="material" value="{{ old('material', $product->material) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                            @error('material')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                            <select id="status" name="status" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="Active" {{ old('status', $product->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ old('status', $product->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Current Images -->
                @if($product->images && count($product->images) > 0)
                    <div class="mt-8">
                        <label class="block text-sm font-medium text-gray-700 mb-4">Current Images</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($product->image_urls as $index => $imageUrl)
                                <div class="relative group">
                                    <img src="{{ $imageUrl }}" alt="Current Image {{ $index + 1 }}" 
                                         class="w-full h-24 object-cover rounded-lg">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all rounded-lg flex items-center justify-center">
                                        <span class="text-white opacity-0 group-hover:opacity-100 transition-opacity text-xs">Current</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Upload new images to replace the current ones.</p>
                    </div>
                @endif

                <!-- Product Images -->
                <div class="mt-8">
                    <label class="block text-sm font-medium text-gray-700 mb-4">Upload New Images</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-400 transition-colors">
                        <div class="space-y-4">
                            <div class="mx-auto h-12 w-12 text-gray-400">
                                <i class="fas fa-cloud-upload-alt text-3xl"></i>
                            </div>
                            <div>
                                <label for="images" class="cursor-pointer">
                                    <span class="text-sm font-medium text-primary-600 hover:text-primary-500">
                                        Click to upload new images
                                    </span>
                                    <span class="text-sm text-gray-500"> or drag and drop</span>
                                </label>
                                <input id="images" name="images[]" type="file" multiple accept="image/*" class="hidden">
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB each</p>
                        </div>
                    </div>
                    <div id="imagePreview" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                    @error('images.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Comments Section -->
                <div class="mt-8">
                    <label for="comments" class="block text-sm font-medium text-gray-700 mb-2">Comments/Notes</label>
                    <textarea id="comments" name="comments" rows="4" placeholder="Add any special notes, instructions, or comments about this product..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('comments', $product->comments) }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">These comments will be displayed in related pages like orders and job sheets.</p>
                    @error('comments')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="mt-8 flex items-center justify-end space-x-4">
                    <a href="{{ route('products.show', $product) }}" 
                       class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 transition-colors">
                        Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Image preview functionality
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    Array.from(e.target.files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview" class="w-full h-24 object-cover rounded-lg">
                    <button type="button" onclick="removeImage(${index})" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    });
});

function removeImage(index) {
    const input = document.getElementById('images');
    const dt = new DataTransfer();
    const { files } = input;
    
    for (let i = 0; i < files.length; i++) {
        if (i !== index) {
            dt.items.add(files[i]);
        }
    }
    
    input.files = dt.files;
    
    // Re-trigger preview
    const event = new Event('change');
    input.dispatchEvent(event);
}

// Drag and drop functionality
const dropZone = document.querySelector('.border-dashed');
const fileInput = document.getElementById('images');

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-primary-400');
});

dropZone.addEventListener('dragleave', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-primary-400');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-primary-400');
    
    const files = e.dataTransfer.files;
    fileInput.files = files;
    
    // Trigger change event
    const event = new Event('change');
    fileInput.dispatchEvent(event);
});
</script>
@endsection 