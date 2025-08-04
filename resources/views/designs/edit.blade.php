@extends('layouts.app')

@section('title', 'Edit Design - Fazztrack')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-edit mr-3 text-primary-500"></i>
                    Revise Design
                </h1>
                <p class="mt-2 text-gray-600">Update design files for Order #{{ $design->order->order_id }}</p>
            </div>
            <a href="{{ route('designs.show', $design) }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Design
            </a>
        </div>
    </div>

    <!-- Current Design Information -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Current Design Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="text-sm font-medium text-gray-500">Design ID:</span>
                    <span class="text-sm text-gray-900 ml-2">#{{ $design->design_id }}</span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Version:</span>
                    <span class="text-sm text-gray-900 ml-2">{{ $design->version }}</span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Status:</span>
                    <span class="text-sm text-gray-900 ml-2">{{ $design->status }}</span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Created:</span>
                    <span class="text-sm text-gray-900 ml-2">{{ $design->created_at->format('M d, Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback from Previous Version -->
    @if($design->feedback)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Feedback to Address</h3>
        </div>
        <div class="p-6">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-800">{{ $design->feedback }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Design Upload Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Update Design Files</h3>
        </div>
        <form method="POST" action="{{ route('designs.update', $design) }}" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            
            <!-- Design Files -->
            <div class="mb-6">
                <h4 class="text-md font-medium text-gray-900 mb-4">Design Views</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="design_front" class="block text-sm font-medium text-gray-700 mb-2">
                            Front Design <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="file" 
                                   id="design_front" 
                                   name="design_front" 
                                   accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 @error('design_front') border-red-300 @enderror">
                        </div>
                        @error('design_front')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if(isset($design->getDesignFilesArray()['design_front']))
                        <p class="mt-1 text-xs text-gray-500">Current: {{ basename($design->getDesignFilesArray()['design_front']) }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="design_back" class="block text-sm font-medium text-gray-700 mb-2">
                            Back Design <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="file" 
                                   id="design_back" 
                                   name="design_back" 
                                   accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 @error('design_back') border-red-300 @enderror">
                        </div>
                        @error('design_back')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if(isset($design->getDesignFilesArray()['design_back']))
                        <p class="mt-1 text-xs text-gray-500">Current: {{ basename($design->getDesignFilesArray()['design_back']) }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="design_left" class="block text-sm font-medium text-gray-700 mb-2">
                            Left Design
                        </label>
                        <div class="relative">
                            <input type="file" 
                                   id="design_left" 
                                   name="design_left" 
                                   accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 @error('design_left') border-red-300 @enderror">
                        </div>
                        @error('design_left')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if(isset($design->getDesignFilesArray()['design_left']))
                        <p class="mt-1 text-xs text-gray-500">Current: {{ basename($design->getDesignFilesArray()['design_left']) }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="design_right" class="block text-sm font-medium text-gray-700 mb-2">
                            Right Design
                        </label>
                        <div class="relative">
                            <input type="file" 
                                   id="design_right" 
                                   name="design_right" 
                                   accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 @error('design_right') border-red-300 @enderror">
                        </div>
                        @error('design_right')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if(isset($design->getDesignFilesArray()['design_right']))
                        <p class="mt-1 text-xs text-gray-500">Current: {{ basename($design->getDesignFilesArray()['design_right']) }}</p>
                        @endif
                    </div>
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
                          placeholder="Add any notes about the design, special requirements, or instructions...">{{ old('design_notes', $design->design_notes) }}</textarea>
                @error('design_notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('designs.show', $design) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-primary-500 border border-transparent rounded-md font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Update Design
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// File preview functionality
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // You can add file preview functionality here
            console.log('File selected:', file.name);
        }
    });
});
</script>
@endsection 