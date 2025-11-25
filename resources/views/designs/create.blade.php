@extends('layouts.app')

@section('title', 'Upload Design - Fazztrack')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-upload mr-3 text-primary-500"></i>
                    Upload Design
                </h1>
                <p class="mt-2 text-gray-600">Upload raw files for Order #{{ $order->order_id }}</p>
            </div>
            <a href="{{ route('designs.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Designs
            </a>
        </div>
    </div>

    <!-- Order Information -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Order Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="text-sm font-medium text-gray-500">Order ID:</span>
                    <span class="text-sm text-gray-900 ml-2">#{{ $order->order_id }}</span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Job Name:</span>
                    <span class="text-sm text-gray-900 ml-2">{{ $order->job_name }}</span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Client:</span>
                    <span class="text-sm text-gray-900 ml-2">{{ $order->client ? $order->client->name : 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Due Date:</span>
                    <span class="text-sm text-gray-900 ml-2">{{ $order->due_date_design ? $order->due_date_design->format('M d, Y') : 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Design Upload Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Upload Raw Files</h3>
        </div>
        <form method="POST" action="{{ route('designs.store', $order) }}" enctype="multipart/form-data" class="p-6">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->order_id }}">
            
            <!-- Design Files Upload -->
            <div class="mb-6">
                <label for="design_files" class="block text-sm font-medium text-gray-700 mb-2">
                    RAW Design Files <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="file" 
                           id="design_files" 
                           name="design_files[]" 
                           multiple
                           accept=".png,.jpg,.jpeg,.gif,.ai,.eps,.pdf,.psd,.rar,.zip,.7z"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 @error('design_files') border-red-300 @enderror"
                           required>
                </div>
                <p class="mt-2 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    You can upload multiple files at once. Supported formats: PNG, JPG, AI, EPS, PDF, PSD, RAR, ZIP, 7Z
                </p>
                @error('design_files')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('design_files.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                
                <!-- File Preview -->
                <div id="file-preview" class="mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 hidden">
                    <h5 class="col-span-full text-sm font-medium text-gray-700 mb-2">Selected Files:</h5>
                </div>
            </div>

            <!-- Design Notes -->
            <div class="mb-6">
                <label for="design_notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Design Notes
                </label>
                <textarea id="design_notes" 
                          name="design_notes" 
                          rows="4"
                          class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('design_notes') border-red-300 @enderror"
                          placeholder="Add any notes about the design, special requirements, or instructions...">{{ old('design_notes') }}</textarea>
                @error('design_notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('designs.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-primary-500 border border-transparent rounded-md font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-upload mr-2"></i>
                    Upload Design
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Multiple file preview functionality
document.getElementById('design_files').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    const previewContainer = document.getElementById('file-preview');
    
    if (files.length === 0) {
        previewContainer.classList.add('hidden');
        return;
    }
    
    // Clear existing previews (except heading)
    const heading = previewContainer.querySelector('h5');
    previewContainer.innerHTML = '';
    if (heading) {
        previewContainer.appendChild(heading);
    } else {
        const newHeading = document.createElement('h5');
        newHeading.className = 'col-span-full text-sm font-medium text-gray-700 mb-2';
        newHeading.textContent = 'Selected Files:';
        previewContainer.appendChild(newHeading);
    }
    
    previewContainer.classList.remove('hidden');
    
    files.forEach((file, index) => {
        const fileItem = document.createElement('div');
        fileItem.className = 'border border-gray-200 rounded-lg p-3 bg-gray-50';
        
        const isImage = file.type.startsWith('image/');
        const fileExtension = file.name.split('.').pop().toLowerCase();
        const isArchive = ['rar', 'zip', '7z'].includes(fileExtension);
        
        let previewContent = '';
        if (isImage) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewContent = `
                    <img src="${e.target.result}" alt="${file.name}" class="w-full h-24 object-cover rounded mb-2">
                    <p class="text-xs text-gray-700 font-medium truncate" title="${file.name}">${file.name}</p>
                    <p class="text-xs text-gray-500">${(file.size / 1024).toFixed(1)} KB</p>
                `;
                fileItem.innerHTML = previewContent;
            };
            reader.readAsDataURL(file);
        } else {
            const iconClass = isArchive ? 'fa-file-archive' : 'fa-file';
            const iconColor = isArchive ? 'text-purple-500' : 'text-blue-500';
            previewContent = `
                <div class="flex items-center justify-center h-24 mb-2">
                    <i class="fas ${iconClass} ${iconColor} text-4xl"></i>
                </div>
                <p class="text-xs text-gray-700 font-medium truncate" title="${file.name}">${file.name}</p>
                <p class="text-xs text-gray-500">${(file.size / 1024).toFixed(1)} KB</p>
            `;
            fileItem.innerHTML = previewContent;
        }
        
        previewContainer.appendChild(fileItem);
    });
});
</script>
@endsection