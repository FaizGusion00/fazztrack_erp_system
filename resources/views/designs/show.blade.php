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
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Order Information</h3>
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
            </div>
        </div>
    </div>

    <!-- Design Files -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Design Files</h3>
        </div>
        <div class="p-6">
            @php
                $designFiles = $design->getDesignFilesArray();
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if(isset($designFiles['design_front']))
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Front Design</h4>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <img src="{{ Storage::url($designFiles['design_front']) }}" 
                             alt="Front Design" 
                             class="w-full h-48 object-cover rounded-lg">
                        <a href="{{ Storage::url($designFiles['design_front']) }}" 
                           target="_blank"
                           class="inline-flex items-center mt-2 text-sm text-primary-600 hover:text-primary-700">
                            <i class="fas fa-download mr-1"></i>
                            Download
                        </a>
                    </div>
                </div>
                @endif

                @if(isset($designFiles['design_back']))
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Back Design</h4>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <img src="{{ Storage::url($designFiles['design_back']) }}" 
                             alt="Back Design" 
                             class="w-full h-48 object-cover rounded-lg">
                        <a href="{{ Storage::url($designFiles['design_back']) }}" 
                           target="_blank"
                           class="inline-flex items-center mt-2 text-sm text-primary-600 hover:text-primary-700">
                            <i class="fas fa-download mr-1"></i>
                            Download
                        </a>
                    </div>
                </div>
                @endif

                @if(isset($designFiles['design_left']))
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Left Design</h4>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <img src="{{ Storage::url($designFiles['design_left']) }}" 
                             alt="Left Design" 
                             class="w-full h-48 object-cover rounded-lg">
                        <a href="{{ Storage::url($designFiles['design_left']) }}" 
                           target="_blank"
                           class="inline-flex items-center mt-2 text-sm text-primary-600 hover:text-primary-700">
                            <i class="fas fa-download mr-1"></i>
                            Download
                        </a>
                    </div>
                </div>
                @endif

                @if(isset($designFiles['design_right']))
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Right Design</h4>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <img src="{{ Storage::url($designFiles['design_right']) }}" 
                             alt="Right Design" 
                             class="w-full h-48 object-cover rounded-lg">
                        <a href="{{ Storage::url($designFiles['design_right']) }}" 
                           target="_blank"
                           class="inline-flex items-center mt-2 text-sm text-primary-600 hover:text-primary-700">
                            <i class="fas fa-download mr-1"></i>
                            Download
                        </a>
                    </div>
                </div>
                @endif
            </div>
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
            <h3 class="text-lg font-medium text-gray-900">Feedback</h3>
        </div>
        <div class="p-6">
            <p class="text-gray-700">{{ $design->feedback }}</p>
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->isSalesManager())
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
<div id="approve-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Approve Design</h3>
                <button onclick="hideApproveModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="text-gray-700 mb-4">Are you sure you want to approve this design? This action cannot be undone.</p>
            <div class="flex space-x-3">
                <button onclick="hideApproveModal()" 
                        class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                    Cancel
                </button>
                <form method="POST" action="{{ route('designs.approve', $design) }}" class="flex-1">
                    @csrf
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                        <i class="fas fa-check mr-2"></i>
                        Approve
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="reject-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Reject Design</h3>
                <button onclick="hideRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('designs.reject', $design) }}">
                @csrf
                <div class="mb-4">
                    <label for="feedback" class="block text-sm font-medium text-gray-700 mb-2">
                        Feedback <span class="text-red-500">*</span>
                    </label>
                    <textarea id="feedback" 
                              name="feedback" 
                              rows="4"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Provide feedback for the designer..."
                              required></textarea>
                </div>
                <div class="flex space-x-3">
                    <button type="button" 
                            onclick="hideRejectModal()"
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showApproveModal() {
    document.getElementById('approve-modal').classList.remove('hidden');
}

function hideApproveModal() {
    document.getElementById('approve-modal').classList.add('hidden');
}

function showRejectModal() {
    document.getElementById('reject-modal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('reject-modal').classList.add('hidden');
}
</script>
@endsection 