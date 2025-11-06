@extends('layouts.app')

@section('title', 'View Design Template - Fazztrack')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-layer-group mr-3 text-primary-500"></i>
                    {{ $template->name }}
                </h1>
                <p class="mt-2 text-gray-600">Design template details and files</p>
            </div>
            <div class="flex items-center space-x-2">
                @if(auth()->user()->id === $template->created_by)
                    <a href="{{ route('design-templates.edit', $template) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Template
                    </a>
                @endif
                <a href="{{ route('design-templates.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Templates
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Template Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Template Information</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $template->name }}</p>
                </div>

                @if($template->description)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $template->description }}</p>
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $template->category ?: 'Uncategorized' }}</p>
                </div>

                @if($template->tags)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tags</label>
                    <div class="mt-1 flex flex-wrap gap-1">
                        @foreach($template->tags_array as $tag)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ trim($tag) }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700">Created By</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $template->designer->name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Created Date</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $template->created_at->format('M d, Y H:i') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Visibility</label>
                    <p class="mt-1 text-sm text-gray-900">
                        @if($template->is_public)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-globe mr-1"></i>Public
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-lock mr-1"></i>Private
                            </span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Template Files -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Template Files</h3>
            
            @if(count($template->template_files) > 0)
                <div class="space-y-3">
                    @foreach($template->template_files as $index => $filePath)
                        @php
                            $fileName = basename($filePath);
                            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                            $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']);
                            $isDesignFile = in_array($fileExtension, ['psd', 'ai']);
                            $isArchive = $fileExtension === 'zip';
                        @endphp
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center
                                    @if($isImage) bg-blue-100 text-blue-600
                                    @elseif($isDesignFile) bg-purple-100 text-purple-600
                                    @elseif($isArchive) bg-orange-100 text-orange-600
                                    @else bg-gray-100 text-gray-600
                                    @endif">
                                    @if($isImage)
                                        <i class="fas fa-image"></i>
                                    @elseif($isDesignFile)
                                        <i class="fas fa-palette"></i>
                                    @elseif($isArchive)
                                        <i class="fas fa-file-archive"></i>
                                    @else
                                        <i class="fas fa-file"></i>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $fileName }}</p>
                                    <p class="text-xs text-gray-500">
                                        @if($isImage) Image file
                                        @elseif($isDesignFile) Design file
                                        @elseif($isArchive) Archive file
                                        @else Document
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="@fileUrl($filePath)" 
                                   target="_blank"
                                   class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-download mr-1"></i>
                                    Download
                                </a>
                                @if($isImage)
                                    <a href="@fileUrl($filePath)" 
                                       target="_blank"
                                       class="inline-flex items-center px-3 py-1 border border-primary-300 text-xs font-medium rounded-md text-primary-700 bg-primary-50 hover:bg-primary-100 transition-colors">
                                        <i class="fas fa-eye mr-1"></i>
                                        Preview
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-file text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">No files uploaded for this template</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 