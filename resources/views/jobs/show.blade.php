@extends('layouts.app')

@section('title', 'Work Order #' . $job->job_id . ' - Fazztrack')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header with Print/Download buttons -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-file-alt mr-3 text-primary-500"></i>
                    Work Order #{{ $job->job_id }}
                </h1>
                <p class="mt-2 text-gray-600">{{ $job->phase }} Phase - {{ $job->order->job_name }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="printWorkOrder()" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-print mr-2"></i>
                    Print
                </button>
                <button onclick="downloadAsPDF()" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    Download PDF
                </button>
                <a href="{{ url()->previous() }}" class="text-primary-600 hover:text-primary-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <!-- Work Order Container -->
    <div id="workOrderContainer" class="bg-white rounded-lg shadow-lg border border-gray-200">
        <div class="p-8">
            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Left Column - Order Details -->
                <div class="space-y-6">
                    <!-- Customer Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-bold text-gray-900">Customer Information</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Customer name:</span>
                                <span class="text-sm text-gray-900">{{ $job->order->client->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Contact no:</span>
                                <span class="text-sm text-gray-900">{{ $job->order->client->phone }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Address:</span>
                                <span class="text-sm text-gray-900">{{ $job->order->client->address }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Order Value -->
                    {{-- Removed work order value display as per request --}}

                    <!-- Design Specifications -->
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Design no:</span>
                            <span class="text-sm text-gray-900">#{{ $job->order->order_id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Design quantity:</span>
                            <span class="text-sm text-gray-900">{{ $job->order->quantity ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Design Previews - Left Side -->
                    <div class="space-y-4">
                        @if($job->order->design_front)
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Design front</h4>
                            <div class="w-full h-48 bg-gray-100 rounded-lg border border-gray-200 overflow-hidden">
                                <img src="@fileUrl($job->order->design_front)" 
                                     alt="Front Design" 
                                     class="w-full h-full object-cover">
                            </div>
                        </div>
                        @endif

                        @if($job->order->design_left)
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Design left</h4>
                            <div class="w-full h-48 bg-gray-100 rounded-lg border border-gray-200 overflow-hidden">
                                <img src="@fileUrl($job->order->design_left)" 
                                     alt="Left Design" 
                                     class="w-full h-full object-cover">
                            </div>
                        </div>
                        @endif

                        @if($job->order->design_back)
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Design back</h4>
                            <div class="w-full h-48 bg-gray-100 rounded-lg border border-gray-200 overflow-hidden">
                                <img src="@fileUrl($job->order->design_back)" 
                                     alt="Back Design" 
                                     class="w-full h-full object-cover">
                            </div>
                        </div>
                        @endif

                        @if($job->order->design_right)
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Design right</h4>
                            <div class="w-full h-48 bg-gray-100 rounded-lg border border-gray-200 overflow-hidden">
                                <img src="@fileUrl($job->order->design_right)" 
                                     alt="Right Design" 
                                     class="w-full h-full object-cover">
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column - Internal Use -->
                <div class="space-y-6">
                    <!-- Confidential Notice -->
                    <div class="text-right">
                        <p class="text-lg font-bold text-gray-900">Confidential.</p>
                        <p class="text-sm text-gray-600">For internal use only.</p>
                    </div>

                    <!-- QR Code Section -->
                    @if($job->qr_code)
                    <div class="space-y-4">
                        <h4 class="text-sm font-medium text-gray-700">QR Code</h4>
                        <div class="w-full h-48 bg-white rounded-lg border border-gray-200 flex items-center justify-center">
                            <div class="text-center">
                                {!! QrCode::size(150)->generate($job->qr_code) !!}
                                <p class="text-xs text-gray-500 mt-2">{{ $job->qr_code }}</p>
                            </div>
                        </div>
                        
                        <!-- Manual Job ID for Manual Entry -->
                        <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="text-center">
                                <p class="text-xs font-medium text-blue-800 mb-1">Manual Entry (if QR scan fails)</p>
                                <div class="flex items-center justify-center space-x-2">
                                    <span class="text-sm font-bold text-blue-900">Job ID:</span>
                                    <span class="text-sm font-mono bg-white px-2 py-1 rounded border text-blue-900">{{ $job->job_id }}</span>
                                </div>
                                <p class="text-xs text-blue-600 mt-1">Enter this ID manually in the scanner</p>
                            </div>
                        </div>

                        <!-- Sales Manager Name -->
                        <div class="mt-4 pt-4 border-t border-blue-200">
                            <div class="text-center">
                                <div class="flex justify-center space-x-2">
                                    <span class="text-sm font-medium text-blue-800">Sales manager name:</span>
                                    <span class="text-sm font-bold text-blue-900">{{ auth()->user()->name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Job Details -->
                    <div class="pt-4 border-t border-gray-200">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Job ID:</span>
                                <span class="text-sm text-gray-900">#{{ $job->job_id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Phase:</span>
                                <span class="text-sm text-gray-900">{{ $job->phase }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Status:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($job->status === 'Pending') bg-yellow-100 text-yellow-800
                                    @elseif($job->status === 'In Progress') bg-blue-100 text-blue-800
                                    @elseif($job->status === 'Completed') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $job->status }}
                                </span>
                            </div>
                            @if($job->assignedUser)
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Assigned To:</span>
                                <span class="text-sm text-gray-900">{{ $job->assignedUser->name }}</span>
                            </div>
                            @endif
                            @if($job->order->product)
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Product:</span>
                                <span class="text-sm text-gray-900">{{ $job->order->product->name }} ({{ $job->order->product->size }})</span>
                            </div>
                            @if($job->order->product->comments)
                            <div class="mt-3 p-2 bg-blue-50 rounded border border-blue-200">
                                <span class="text-xs font-medium text-blue-800">Product Notes:</span>
                                <p class="text-xs text-blue-700 mt-1">{{ $job->order->product->comments }}</p>
                            </div>
                            @endif
                            @endif
                        </div>
                        
                        <!-- Manual Entry Info for Print -->
                        @if($job->qr_code)
                        <div class="mt-3 p-2 bg-gray-50 rounded border">
                            <div class="text-center">
                                <p class="text-xs font-medium text-gray-700 mb-1">Manual Entry</p>
                                <div class="flex items-center justify-center space-x-1">
                                    <span class="text-xs text-gray-600">Job ID:</span>
                                    <span class="text-xs font-mono bg-white px-1 py-0.5 rounded border text-gray-800">{{ $job->job_id }}</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">QR: {{ $job->qr_code }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information (Hidden in Print) -->
    <div class="mt-8 print:hidden">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-primary-500"></i>
                    Additional Information
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Time Tracking -->
                    <div>
                        <h4 class="font-medium text-gray-900 mb-4">Time Tracking</h4>
                        <div class="space-y-3">
                            @if($job->start_time)
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Started:</span>
                                <span class="text-sm text-gray-900">{{ $job->start_time->format('M d, Y H:i') }}</span>
                            </div>
                            @endif
                            @if($job->end_time)
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Completed:</span>
                                <span class="text-sm text-gray-900">{{ $job->end_time->format('M d, Y H:i') }}</span>
                            </div>
                            @endif
                            @if($job->duration)
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Duration:</span>
                                <span class="text-sm text-gray-900">{{ $job->duration }} minutes</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quantities -->
                    <div>
                        <h4 class="font-medium text-gray-900 mb-4">Quantities</h4>
                        <div class="space-y-3">
                            @if($job->start_quantity)
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Start Quantity:</span>
                                <span class="text-sm text-gray-900">{{ $job->start_quantity }}</span>
                            </div>
                            @endif
                            @if($job->end_quantity)
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">End Quantity:</span>
                                <span class="text-sm text-gray-900">{{ $job->end_quantity }}</span>
                            </div>
                            @endif
                            @if($job->reject_quantity)
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Reject Quantity:</span>
                                <span class="text-sm text-gray-900">{{ $job->reject_quantity }}</span>
                            </div>
                            @endif
                            @if($job->remarks)
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Remarks:</span>
                                <span class="text-sm text-gray-900">{{ $job->remarks }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    body { margin: 0; }
    .print\\:hidden { display: none !important; }
    #workOrderContainer { 
        box-shadow: none !important; 
        border: 1px solid #000 !important;
    }
    .bg-white { background: white !important; }
    .text-gray-900 { color: #000 !important; }
    .text-gray-500 { color: #333 !important; }
}
</style>

<script>
function printWorkOrder() {
    window.print();
}

function downloadAsPDF() {
    // Create a new window with the work order content
    const printWindow = window.open('', '_blank');
    const workOrderContent = document.getElementById('workOrderContainer').innerHTML;
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Work Order #{{ $job->job_id }}</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 20px; 
                    color: #000; 
                }
                .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
                .space-y-6 > * + * { margin-top: 24px; }
                .space-y-4 > * + * { margin-top: 16px; }
                .space-y-3 > * + * { margin-top: 12px; }
                .space-y-2 > * + * { margin-top: 8px; }
                .flex { display: flex; }
                .justify-between { justify-content: space-between; }
                .items-center { align-items: center; }
                .text-sm { font-size: 14px; }
                .text-lg { font-size: 18px; }
                .font-bold { font-weight: bold; }
                .font-medium { font-weight: 500; }
                .text-gray-900 { color: #000; }
                .text-gray-500 { color: #333; }
                .text-gray-600 { color: #666; }
                .text-gray-700 { color: #444; }
                .border-t { border-top: 1px solid #ddd; }
                .pt-4 { padding-top: 16px; }
                .mb-2 { margin-bottom: 8px; }
                .mb-4 { margin-bottom: 16px; }
                .w-full { width: 100%; }
                .h-48 { height: 192px; }
                .rounded-lg { border-radius: 8px; }
                .border { border: 1px solid #ddd; }
                .bg-gray-100 { background-color: #f3f4f6; }
                .overflow-hidden { overflow: hidden; }
                .object-cover { object-fit: cover; }
                .text-center { text-align: center; }
                .text-xs { font-size: 12px; }
                .mt-2 { margin-top: 8px; }
                .mt-3 { margin-top: 12px; }
                .p-2 { padding: 8px; }
                .p-3 { padding: 12px; }
                .bg-gray-50 { background-color: #f9fafb; }
                .font-mono { font-family: monospace; }
                .px-1 { padding-left: 4px; padding-right: 4px; }
                .py-0\\.5 { padding-top: 2px; padding-bottom: 2px; }
                .space-x-1 > * + * { margin-left: 4px; }
                .space-x-2 > * + * { margin-left: 8px; }
                img { max-width: 100%; height: auto; }
            </style>
        </head>
        <body>
            <div style="max-width: 800px; margin: 0 auto;">
                ${workOrderContent}
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    
    // Wait for images to load then print
    setTimeout(() => {
        printWindow.print();
    }, 1000);
}
</script>
@endsection 