@extends('layouts.app')

@section('title', 'Design Templates - Fazztrack')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-layer-group mr-3 text-primary-500"></i>
            Design Templates
        </h1>
        <p class="mt-2 text-gray-600">Upload and manage reusable design templates</p>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('design-templates.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Templates</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search by name, description, or tags..."
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
            <div class="flex items-end space-x-2">
                <select name="category" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Categories</option>
                    <option value="T-Shirt" {{ request('category') == 'T-Shirt' ? 'selected' : '' }}>T-Shirt</option>
                    <option value="Hoodie" {{ request('category') == 'Hoodie' ? 'selected' : '' }}>Hoodie</option>
                    <option value="Cap" {{ request('category') == 'Cap' ? 'selected' : '' }}>Cap</option>
                    <option value="Polo" {{ request('category') == 'Polo' ? 'selected' : '' }}>Polo</option>
                    <option value="Jacket" {{ request('category') == 'Jacket' ? 'selected' : '' }}>Jacket</option>
                    <option value="Other" {{ request('category') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 transition-colors">
                    <i class="fas fa-search mr-1"></i>
                    Search
                </button>
                @if(request('search') || request('category'))
                    <a href="{{ route('design-templates.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                        <i class="fas fa-times mr-1"></i>
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($templates as $template)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="p-6">
                    <!-- Template Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-layer-group text-purple-500 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $template->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $template->category ?: 'Uncategorized' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($template->is_public)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-globe mr-1"></i>Public
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-lock mr-1"></i>Private
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Template Details -->
                    <div class="space-y-3 mb-4">
                        @if($template->description)
                            <div class="text-sm text-gray-600">
                                {{ Str::limit($template->description, 100) }}
                            </div>
                        @endif
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-user w-4 mr-2"></i>
                            <span>Created by {{ $template->designer->name }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-file w-4 mr-2"></i>
                            <span>{{ count($template->template_files) }} file(s)</span>
                        </div>
                        @if($template->tags)
                            <div class="flex flex-wrap gap-1">
                                @foreach($template->tags_array as $tag)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ trim($tag) }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-calendar w-4 mr-2"></i>
                            <span>Created {{ $template->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('design-templates.show', $template) }}" 
                           class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-primary-300 text-sm font-medium rounded-md text-primary-700 bg-primary-50 hover:bg-primary-100 transition-colors">
                            <i class="fas fa-eye mr-1"></i>
                            View Template
                        </a>
                        @if(auth()->user()->id === $template->created_by)
                            <a href="{{ route('design-templates.edit', $template) }}" 
                               class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <i class="fas fa-edit"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <i class="fas fa-layer-group text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No design templates found</h3>
                    <p class="text-gray-500 mb-4">Create your first template to get started.</p>
                    <a href="{{ route('design-templates.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Create Template
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($templates->hasPages())
        <div class="mt-8">
            {{ $templates->links() }}
        </div>
    @endif
</div>
@endsection 