@extends('layouts.app')

@section('title', 'Design Details - Fazztrack')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-palette mr-3 text-primary-500"></i>
                    Design Details
                </h1>
                <p class="mt-2 text-gray-600">Design #{{ $design->design_id }} for Order #{{ $design->order->order_id }}</p>
            </div>
            <a href="{{ route('designs.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Designs
            </a>
        </div>
    </div>

    <!-- Design Information -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Design Information</h3>
                @php
                    $statusColors = [
                        'Pending Review' => 'bg-yellow-100 text-yellow-800',
                        'Approved' => 'bg-green-100 text-green-800',
                        'Rejected' => 'bg-red-100 text-red-800'
                    ];
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$design->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $design->status }}
                </span>
            </div>
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
                    <span class="text-sm font-medium text-gray-500">Designer:</span>
                    <span class="text-sm text-gray-900 ml-2">{{ $design->designer->name }}</span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Created:</span>
                    <span class="text-sm text-gray-900 ml-2">{{ $design->created_at->format('M d, Y H:i') }}</span>
                </div>
                @if($design->approved_by)
                <div>
                    <span class="text-sm font-medium text-gray-500">Approved by:</span>
                    <span class="text-sm text-gray-900 ml-2">{{ $design->approvedBy->name }}</span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Approved at:</span>
                    <span class="text-sm text-gray-900 ml-2">{{ $design->approved_at->format('M d, Y H:i') }}</span>
                </div>
                @endif
                @if($design->rejected_by)
                <div>
                    <span class="text-sm font-medium text-gray-500">Rejected by:</span>
                    <span class="text-sm text-gray-900 ml-2">{{ $design->rejectedBy->name }}</span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Rejected at:</span>
                    <span class="text-sm text-gray-900 ml-2">{{ $design->rejected_at->format('M d, Y H:i') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Order Information -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Order Information</h3>
            <a href="{{ route('orders.show', $design->order) }}" 
               class="text-sm text-primary-600 hover:text-primary-700">
                <i class="fas fa-external-link-alt mr-1"></i>
                View Full Order
            </a>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="text-sm font-medium text-gray-500">Order ID:</span>
                    <span class="text-sm text-gray-900 ml-2">#{{ $design->order->order_id }}</span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Job Name:</span>
                    <span class="text-sm text-gray-900 ml-2">{{ $design->order->job_name }}</span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Client:</span>
                    <span class="text-sm text-gray-900 ml-2">{{ $design->order->client->name }}</span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Due Date:</span>
                    <span class="text-sm text-gray-900 ml-2">{{ $design->order->due_date_design->format('M d, Y') }}</span>
                </div>
                @if($design->order->products->count() > 0)
                <div class="md:col-span-2">
                    <span class="text-sm font-medium text-gray-500">Products:</span>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($design->order->products as $product)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $product->name }} ({{ $product->pivot->quantity }})
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Order Reference Images (For Designer) -->
    @if(auth()->user()->isDesigner())
        @php
            $orderDesignFiles = $design->order->getDesignFilesArray();
            $referenceImages = [];
            if (is_array($orderDesignFiles)) {
                foreach ($orderDesignFiles as $key => $value) {
                    if (is_numeric($key)) {
                        $referenceImages[] = $value;
                    } else {
                        if (!empty($value)) {
                            $referenceImages[] = $value;
                        }
                    }
                }
            }
        @endphp
        @if(count($referenceImages) > 0)
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg shadow-sm border border-blue-200 mb-6">
            <div class="px-6 py-4 border-b border-blue-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-images mr-2 text-blue-500"></i>
                    Customer Reference Images
                </h3>
                <p class="text-sm text-gray-600 mt-1">Reference images provided by customer for design work</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($referenceImages as $index => $imagePath)
                    <div class="relative group">
                        <div class="relative overflow-hidden rounded-lg border-2 border-gray-200 hover:border-blue-300 transition-colors">
                            <img src="@fileUrl($imagePath)" 
                                 alt="Reference Image {{ $index + 1 }}" 
                                 class="w-full h-32 object-cover cursor-pointer hover:scale-105 transition-transform duration-300 design-image"
                                 data-title="Reference Image {{ $index + 1 }}"
                                 onclick="openImageModal('@fileUrl($imagePath)', 'Reference Image {{ $index + 1 }}')">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                                <div class="absolute bottom-2 left-2 right-2 pointer-events-auto">
                                    <div class="bg-white/90 backdrop-blur-sm rounded-lg px-2 py-1 text-center">
                                        <span class="text-xs font-medium text-gray-800">
                                            <i class="fas fa-expand-arrows-alt mr-1"></i>Click to enlarge
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 text-center truncate" title="{{ basename($imagePath) }}">
                            {{ basename($imagePath) }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    @endif

    <!-- Raw Files -->
    @php
        $designFiles = $design->getDesignFilesArray();
        $fileList = [];
        $fileUrls = [];
        
        // Handle both old format (associative array) and new format (indexed array)
        if (!empty($designFiles)) {
            // Check if new format (indexed array with objects)
            if (isset($designFiles[0]) && is_array($designFiles[0]) && isset($designFiles[0]['path'])) {
                // New format
                $fileList = $designFiles;
                foreach ($designFiles as $file) {
                    if (isset($file['path']) && !empty($file['path'])) {
                        $fileUrls[] = \App\Services\StorageService::url($file['path']);
                    }
                }
            } else {
                // Old format - convert to new format
                foreach ($designFiles as $key => $path) {
                    if (is_string($path) && !empty($path)) {
                        $fileList[] = [
                            'path' => $path,
                            'original_name' => ucfirst(str_replace('_', ' ', $key)) . '.' . pathinfo($path, PATHINFO_EXTENSION),
                            'size' => 0,
                            'mime_type' => 'image/jpeg',
                        ];
                        $fileUrls[] = \App\Services\StorageService::url($path);
                    }
                }
            }
        }
    @endphp
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div id="design-files-data" data-urls="{{ htmlspecialchars(json_encode($fileUrls), ENT_QUOTES, 'UTF-8') }}" style="display: none;"></div>
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Raw Files (Version {{ $design->version }})</h3>
            @if((auth()->user()->isSuperAdmin() || auth()->user()->isSalesManager()) && count($fileList) > 0)
            <button onclick="downloadAllDesigns()" 
                    class="inline-flex items-center px-3 py-1.5 border border-primary-300 text-sm font-medium rounded-md text-primary-700 bg-primary-50 hover:bg-primary-100 transition-colors">
                <i class="fas fa-download mr-2"></i>
                Download All
            </button>
            @endif
        </div>
        <div class="p-6">
            
            @if(count($fileList) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($fileList as $index => $file)
                    @php
                        $filePath = is_array($file) ? $file['path'] : $file;
                        $fileName = is_array($file) && isset($file['original_name']) ? $file['original_name'] : (is_array($file) ? basename($filePath) : basename($filePath));
                        $fileUrl = \App\Services\StorageService::url($filePath);
                        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        $isArchive = in_array($fileExtension, ['rar', 'zip', '7z']);
                        $fileSize = is_array($file) && isset($file['size']) ? $file['size'] : 0;
                        $fileSizeFormatted = $fileSize > 0 ? ($fileSize > 1024 * 1024 ? number_format($fileSize / (1024 * 1024), 2) . ' MB' : number_format($fileSize / 1024, 1) . ' KB') : 'Unknown';
                    @endphp
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow bg-white">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 truncate" title="{{ $fileName }}">
                                    {{ $fileName }}
                                </h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $fileSizeFormatted }}</p>
                            </div>
                        </div>
                        
                        @if($isImage)
                        <div class="mb-3">
                            <img src="{{ $fileUrl }}" 
                                 alt="{{ $fileName }}" 
                                 class="w-full h-32 object-cover rounded-lg cursor-pointer hover:opacity-90 transition-opacity border border-gray-200"
                                 data-image-url="{{ htmlspecialchars($fileUrl, ENT_QUOTES, 'UTF-8') }}"
                                 data-image-title="{{ htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8') }}"
                                 onclick="openImageModal(this.dataset.imageUrl, this.dataset.imageTitle)"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-full h-32 bg-gray-100 rounded-lg flex items-center justify-center hidden">
                                <i class="fas fa-image text-gray-400 text-2xl"></i>
                            </div>
                        </div>
                        @else
                        <div class="mb-3 flex items-center justify-center h-32 bg-gray-50 rounded-lg border border-gray-200">
                            @if($isArchive)
                                <i class="fas fa-file-archive text-purple-500 text-4xl"></i>
                            @elseif($fileExtension === 'pdf')
                                <i class="fas fa-file-pdf text-red-500 text-4xl"></i>
                            @elseif(in_array($fileExtension, ['ai', 'eps']))
                                <i class="fas fa-file-image text-blue-500 text-4xl"></i>
                            @else
                                <i class="fas fa-file text-gray-400 text-4xl"></i>
                            @endif
                        </div>
                        @endif
                        
                        <div class="flex items-center space-x-2">
                            <a href="{{ $fileUrl }}" 
                               download="{{ $fileName }}"
                               target="_blank"
                               class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-primary-500 text-white text-sm font-medium rounded-md hover:bg-primary-600 transition-colors">
                                <i class="fas fa-download mr-2"></i>
                                Download
                            </a>
                            @if($isImage)
                            <button data-image-url="{{ htmlspecialchars($fileUrl, ENT_QUOTES, 'UTF-8') }}"
                                    data-image-title="{{ htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8') }}"
                                    onclick="openImageModal(this.dataset.imageUrl, this.dataset.imageTitle)" 
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <i class="fas fa-expand"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <i class="fas fa-file text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500">No design files uploaded yet.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Design Notes -->
    @if($design->design_notes)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Design Notes</h3>
        </div>
        <div class="p-6">
            <p class="text-gray-700">{{ $design->design_notes }}</p>
        </div>
    </div>
    @endif

    <!-- Feedback -->
    @if($design->feedback)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-comment-dots mr-2 text-red-500"></i>
                Feedback from Reviewer
            </h3>
        </div>
        <div class="p-6">
            <div class="bg-red-50 border-l-4 border-red-400 p-4">
                <p class="text-gray-700">{{ $design->feedback }}</p>
                @if($design->rejected_by)
                <p class="text-sm text-gray-500 mt-2">
                    <i class="fas fa-user mr-1"></i>
                    Rejected by {{ $design->rejectedBy->name }} on {{ $design->rejected_at->format('M d, Y H:i') }}
                </p>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Version History -->
    @if($versionHistory->count() > 1)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-history mr-2 text-purple-500"></i>
                Version History ({{ $versionHistory->count() }} versions)
            </h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($versionHistory as $version)
                <div class="relative pl-8 pb-4 {{ !$loop->last ? 'border-l-2 border-gray-200' : '' }}">
                    <div class="absolute left-0 top-0 w-4 h-4 bg-white border-2 {{ $version->design_id === $design->design_id ? 'border-primary-500' : ($version->status === 'Approved' ? 'border-green-500' : ($version->status === 'Rejected' ? 'border-red-500' : 'border-yellow-500')) }} rounded-full -ml-2"></div>
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="font-semibold text-gray-900">Version {{ $version->version }}</span>
                                @if($version->design_id === $design->design_id)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary-100 text-primary-800">
                                    Current
                                </span>
                                @endif
                                @php
                                    $statusColors = [
                                        'Pending Review' => 'bg-yellow-100 text-yellow-800',
                                        'Approved' => 'bg-green-100 text-green-800',
                                        'Rejected' => 'bg-red-100 text-red-800'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$version->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $version->status }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-user-edit mr-1"></i>
                                {{ $version->designer->name }} • {{ $version->created_at->format('M d, Y H:i') }}
                            </p>
                            @if($version->feedback)
                            <p class="text-sm text-red-600 mt-1">
                                <i class="fas fa-comment mr-1"></i>
                                {{ \Illuminate\Support\Str::limit($version->feedback, 100) }}
                            </p>
                            @endif
                            @if($version->approved_by)
                            <p class="text-sm text-green-600 mt-1">
                                <i class="fas fa-check-circle mr-1"></i>
                                Approved by {{ $version->approvedBy->name }} on {{ $version->approved_at->format('M d, Y') }}
                            </p>
                            @endif
                        </div>
                        <a href="{{ route('designs.show', $version) }}" 
                           class="ml-4 inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-eye mr-1"></i>
                            View
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSalesManager())
        @if($design->status === 'Pending Review')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Design Review</h3>
            </div>
            <div class="p-6">
                <div class="flex space-x-4">
                    <button onclick="showApproveModal()" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        <i class="fas fa-check mr-2"></i>
                        Approve Design
                    </button>
                    <button onclick="showRejectModal()" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Reject Design
                    </button>
                </div>
            </div>
        </div>
        @endif
    @endif

    @if(auth()->user()->isDesigner() && $design->designer_id === auth()->user()->id)
        @if($design->status === 'Rejected')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Design Actions</h3>
            </div>
            <div class="p-6">
                <a href="{{ route('designs.edit', $design) }}" 
                   class="inline-flex items-center px-4 py-2 bg-primary-500 border border-transparent rounded-md font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Revise Design
                </a>
            </div>
        </div>
        @endif
    @endif
</div>

<!-- Approve Modal -->
<div id="approve-modal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 transition-opacity duration-300">
    <div class="relative top-10 mx-auto p-0 w-full max-w-md mb-10">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95" id="approve-modal-content">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-check text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-white">Approve Design</h3>
                    </div>
                    <button onclick="hideApproveModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mx-auto mb-4">
                        <i class="fas fa-check-circle text-green-600 text-3xl"></i>
                    </div>
                    <p class="text-gray-700 text-center mb-2">Are you sure you want to approve this design?</p>
                    <p class="text-sm text-gray-500 text-center">This will mark the design as approved and update the order status.</p>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium">Design Details:</p>
                            <p class="text-xs mt-1">Version {{ $design->version }} • Order #{{ $design->order->order_id }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <button onclick="hideApproveModal()"
                            class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                        Cancel
                    </button>
                    <form method="POST" action="{{ route('designs.approve', $design) }}" class="flex-1">
                        @csrf
                        <button type="submit"
                                class="w-full px-4 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all font-medium shadow-lg">
                            <i class="fas fa-check mr-2"></i>
                            Approve Design
                        </button>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="reject-modal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 transition-opacity duration-300">
    <div class="relative top-10 mx-auto p-0 w-full max-w-md mb-10">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95" id="reject-modal-content">
            <div class="bg-gradient-to-r from-red-500 to-pink-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-times text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-white">Reject Design</h3>
                    </div>
                    <button onclick="hideRejectModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('designs.reject', $design) }}" id="reject-form">
                    @csrf
                    <div class="mb-4">
                        <div class="flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mx-auto mb-4">
                            <i class="fas fa-times-circle text-red-600 text-3xl"></i>
                        </div>
                        <p class="text-gray-700 text-center mb-2">Please provide feedback for the designer</p>
                        <p class="text-sm text-gray-500 text-center">The designer will use this feedback to revise the design.</p>
                    </div>
                    <div class="mb-4">
                        <label for="feedback" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-comment-dots mr-1"></i>
                            Feedback <span class="text-red-500">*</span>
                        </label>
                        <textarea id="feedback" 
                                  name="feedback" 
                                  rows="5"
                                  class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none"
                                  placeholder="Provide detailed feedback for the designer. What needs to be changed or improved?"
                                  required></textarea>
                        <p class="text-xs text-gray-500 mt-1">Minimum 10 characters required</p>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 mr-2"></i>
                            <div class="text-sm text-yellow-800">
                                <p class="font-medium">Note:</p>
                                <p class="text-xs mt-1">Rejecting will allow the designer to create a new version with your feedback.</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" 
                                onclick="hideRejectModal()"
                                class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-2.5 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-lg hover:from-red-600 hover:to-pink-700 transition-all font-medium shadow-lg">
                            <i class="fas fa-times mr-2"></i>
                            Reject Design
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-95 hidden z-50 flex items-center justify-center p-4">
    <div class="relative w-full h-full max-w-7xl max-h-full">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden w-full h-full flex flex-col">
            <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50">
                <h3 id="modalTitle" class="text-xl font-semibold text-gray-900"></h3>
                <div class="flex items-center space-x-2">
                    <button onclick="downloadImage()" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg" title="Download Image">
                        <i class="fas fa-download text-lg"></i>
                    </button>
                    <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg" title="Close">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>
            <div class="flex-1 p-4 flex items-center justify-center overflow-hidden bg-gray-900">
                <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg shadow-lg cursor-zoom-in" onclick="toggleZoom()">
            </div>
        </div>
    </div>
</div>

<script>
// Design file URLs for download - read from data attribute
const designFileUrlsData = document.getElementById('design-files-data')?.getAttribute('data-urls');
const designFileUrls = designFileUrlsData ? JSON.parse(designFileUrlsData) : [];

let currentImageUrl = '';
let isZoomed = false;

function openImageModal(imageUrl, title) {
    if (!imageUrl) return;
    
    currentImageUrl = imageUrl;
    const modalImage = document.getElementById('modalImage');
    const modalTitle = document.getElementById('modalTitle');
    const imageModal = document.getElementById('imageModal');
    
    if (!modalImage || !modalTitle || !imageModal) {
        console.error('Modal elements not found');
        return;
    }
    
    modalImage.src = imageUrl;
    modalTitle.textContent = title || 'Image Preview';
    imageModal.classList.remove('hidden');
    isZoomed = false;
    modalImage.classList.remove('scale-150');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    currentImageUrl = '';
    isZoomed = false;
}

function downloadImage() {
    if (currentImageUrl) {
        const link = document.createElement('a');
        link.href = currentImageUrl;
        link.download = currentImageUrl.split('/').pop();
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

function toggleZoom() {
    const img = document.getElementById('modalImage');
    if (isZoomed) {
        img.classList.remove('scale-150');
        isZoomed = false;
    } else {
        img.classList.add('scale-150');
        isZoomed = true;
    }
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});

// Close modal when clicking outside
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

function downloadAllDesigns() {
    const files = designFileUrls;
    
    if (files.length === 0) {
        alert('No raw files to download');
        return;
    }
    
    // Download each file
    files.forEach((url, index) => {
        setTimeout(() => {
            const link = document.createElement('a');
            link.href = url;
            link.download = url.split('/').pop();
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }, index * 200); // Stagger downloads
    });
}

function showApproveModal() {
    const modal = document.getElementById('approve-modal');
    const content = document.getElementById('approve-modal-content');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    // Animate in
    setTimeout(() => {
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }, 10);
}

function hideApproveModal() {
    const modal = document.getElementById('approve-modal');
    const content = document.getElementById('approve-modal-content');
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 300);
}

function showRejectModal() {
    const modal = document.getElementById('reject-modal');
    const content = document.getElementById('reject-modal-content');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    // Animate in
    setTimeout(() => {
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }, 10);
    // Focus on textarea
    setTimeout(() => {
        document.getElementById('feedback').focus();
    }, 100);
}

function hideRejectModal() {
    const modal = document.getElementById('reject-modal');
    const content = document.getElementById('reject-modal-content');
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        // Clear form
        document.getElementById('reject-form').reset();
    }, 300);
}

// Validate feedback length
document.addEventListener('DOMContentLoaded', function() {
    const feedbackTextarea = document.getElementById('feedback');
    if (feedbackTextarea) {
        feedbackTextarea.addEventListener('input', function() {
            const length = this.value.length;
            const submitBtn = this.closest('form').querySelector('button[type="submit"]');
            if (length < 10) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        });
    }
});

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideApproveModal();
        hideRejectModal();
    }
});
</script>
@endsection 