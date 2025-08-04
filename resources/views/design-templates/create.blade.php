@extends('layouts.app')

@section('title', 'Create Design Template - Fazztrack')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-layer-group mr-3 text-primary-500"></i>
            Create Design Template
        </h1>
        <p class="mt-2 text-gray-600">Upload reusable design templates for future projects</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form method="POST" action="{{ route('design-templates.store') }}" enctype="multipart/form-data" class="p-6">
            @csrf

            <div class="space-y-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Template Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Template Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                                   class="block w-full border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="e.g., Classic T-Shirt Design">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                Category
                            </label>
                            <select name="category" id="category" 
                                    class="block w-full border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Select Category</option>
                                <option value="T-Shirt" {{ old('category') == 'T-Shirt' ? 'selected' : '' }}>T-Shirt</option>
                                <option value="Hoodie" {{ old('category') == 'Hoodie' ? 'selected' : '' }}>Hoodie</option>
                                <option value="Cap" {{ old('category') == 'Cap' ? 'selected' : '' }}>Cap</option>
                                <option value="Polo" {{ old('category') == 'Polo' ? 'selected' : '' }}>Polo</option>
                                <option value="Jacket" {{ old('category') == 'Jacket' ? 'selected' : '' }}>Jacket</option>
                                <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="3" 
                                  class="block w-full border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                                  placeholder="Describe the template, its features, and usage...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                            Tags
                        </label>
                        <input type="text" name="tags" id="tags" value="{{ old('tags') }}" 
                               class="block w-full border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                               placeholder="e.g., classic, modern, corporate, casual (comma separated)">
                        <p class="mt-1 text-sm text-gray-500">Add tags to help find this template later</p>
                        @error('tags')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Template Files -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Template Files</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                    <div>
                                <label for="template_files" class="block text-sm font-medium text-gray-700 mb-2">
                                    Design Files <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center justify-center w-full">
                                    <label for="template_files" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <i class="fas fa-file-upload text-gray-400 text-2xl mb-2"></i>
                                            <p class="mb-2 text-sm text-gray-500">
                                                <span class="font-semibold">Click to upload</span> or drag and drop
                                            </p>
                                            <p class="text-xs text-gray-500">JPG, PNG, PDF, PSD, AI, ZIP (MAX. 60MB each)</p>
                                        </div>
                                        <input id="template_files" name="template_files[]" type="file" class="hidden" multiple accept=".jpg,.jpeg,.png,.pdf,.psd,.ai,.zip" required>
                                    </label>
                                </div>
                                <div class="mt-2 text-sm text-gray-600">
                                    <p><strong>Supported formats:</strong></p>
                                    <ul class="list-disc list-inside space-y-1 mt-1">
                                        <li><strong>Images:</strong> JPG, PNG, PDF (for preview)</li>
                                        <li><strong>Design Files:</strong> PSD (Photoshop), AI (Illustrator)</li>
                                        <li><strong>Archives:</strong> ZIP (multiple files, max 60MB)</li>
                                    </ul>
                                </div>
                                @error('template_files')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                File Preview
                            </label>
                            <div id="file-preview" class="hidden">
                                <div id="file-list" class="space-y-2 max-h-32 overflow-y-auto">
                                    <!-- File previews will be shown here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visibility Settings -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Visibility Settings</h3>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_public" id="is_public" value="1" {{ old('is_public') ? 'checked' : '' }}
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="is_public" class="ml-2 block text-sm text-gray-900">
                            Make this template public (other designers can see and use it)
                        </label>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Leave unchecked to keep this template private to you</p>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('design-templates.index') }}" 
                   class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-save mr-2"></i>
                    Create Template
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// File upload preview functionality
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('template_files');
    const filePreview = document.getElementById('file-preview');
    const fileList = document.getElementById('file-list');

    fileInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        if (files.length > 0) {
            filePreview.classList.remove('hidden');
            fileList.innerHTML = ''; // Clear previous previews
            
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
        } else {
            filePreview.classList.add('hidden');
        }
    });
});
</script>
@endsection 