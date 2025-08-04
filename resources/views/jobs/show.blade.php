@extends('layouts.app')

@section('title', 'Job Details - Fazztrack')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-tasks mr-3 text-primary-500"></i>
                    Job #{{ $job->job_id }}
                </h1>
                <p class="mt-2 text-gray-600">{{ $job->phase }} Phase - {{ $job->order->job_name }}</p>
            </div>
            <a href="{{ url()->previous() }}" class="text-primary-600 hover:text-primary-700">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <!-- Job Information -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-info-circle mr-2 text-primary-500"></i>
                Job Information
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-4">Job Details</h4>
                    <div class="space-y-3">
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

                <div>
                    <h4 class="font-medium text-gray-900 mb-4">Order Information</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Order ID:</span>
                            <span class="text-sm text-gray-900">#{{ $job->order->order_id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Job Name:</span>
                            <span class="text-sm text-gray-900">{{ $job->order->job_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Client:</span>
                            <span class="text-sm text-gray-900">{{ $job->order->client->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Order Status:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($job->order->status === 'Order Created') bg-blue-100 text-blue-800
                                @elseif($job->order->status === 'Order Approved') bg-green-100 text-green-800
                                @elseif($job->order->status === 'Job Start') bg-yellow-100 text-yellow-800
                                @elseif($job->order->status === 'Job Complete') bg-purple-100 text-purple-800
                                @elseif($job->order->status === 'Order Finished') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $job->order->status }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Delivery Method:</span>
                            <span class="text-sm text-gray-900">{{ $job->order->delivery_method }}</span>
                        </div>
                        @if($job->order->due_date_production)
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Production Due:</span>
                            <span class="text-sm text-gray-900">{{ $job->order->due_date_production->format('M d, Y') }}</span>
                        </div>
                        @endif
                        @if($job->order->total_amount)
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Total Amount:</span>
                            <span class="text-sm text-gray-900">RM {{ number_format($job->order->total_amount, 2) }}</span>
                        </div>
                        @endif
                        @if($job->order->remarks)
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Order Remarks:</span>
                            <span class="text-sm text-gray-900">{{ $job->order->remarks }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code -->
    @if($job->qr_code)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-qrcode mr-2 text-primary-500"></i>
                QR Code
            </h3>
        </div>
        <div class="p-6">
            <div class="flex justify-center">
                <div class="bg-white p-4 rounded-lg border">
                    {!! QrCode::size(200)->generate($job->qr_code) !!}
                </div>
            </div>
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600">Scan this QR code to start or end the job</p>
                <p class="text-xs text-gray-500 mt-1">QR Code: {{ $job->qr_code }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Workflow Information -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-project-diagram mr-2 text-primary-500"></i>
                Workflow Information
            </h3>
        </div>
        <div class="p-6">
            @php
                $orderJobs = \App\Models\Job::where('order_id', $job->order_id)
                    ->with(['assignedUser'])
                    ->orderBy('id')
                    ->get();
            @endphp
            <div class="space-y-3">
                @foreach($orderJobs as $orderJob)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg @if($orderJob->id === $job->id) border-2 border-primary-500 @endif">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium
                                @if($orderJob->status === 'Completed') bg-green-100 text-green-800
                                @elseif($orderJob->status === 'In Progress') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $loop->iteration }}
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-900">{{ $orderJob->phase }}</span>
                                @if($orderJob->assignedUser)
                                    <p class="text-xs text-gray-500">Assigned to {{ $orderJob->assignedUser->name }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($orderJob->status === 'Completed') bg-green-100 text-green-800
                                @elseif($orderJob->status === 'In Progress') bg-blue-100 text-blue-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ $orderJob->status }}
                            </span>
                            @if($orderJob->id === $job->id)
                                <span class="text-xs text-primary-600 font-medium">Current Job</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection 