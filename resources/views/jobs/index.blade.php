@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-tasks mr-3 text-primary-500"></i>
                        Production Jobs
                    </h1>
                    <p class="mt-2 text-gray-600">Manage and track production jobs across all phases.</p>
                </div>
                @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                <a href="{{ route('jobs.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-primary-500 border border-transparent rounded-lg font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Create Job
                </a>
                @endif
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('jobs.index') }}" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Jobs</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search by job ID, order, or phase..."
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
                <div class="md:w-48">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status" name="status" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Status</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="On Hold" {{ request('status') == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                    </select>
                </div>
                <div class="md:w-48">
                    <label for="phase" class="block text-sm font-medium text-gray-700 mb-2">Phase</label>
                    <select id="phase" name="phase" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Phases</option>
                        <option value="PRINT" {{ request('phase') == 'PRINT' ? 'selected' : '' }}>PRINT</option>
                        <option value="PRESS" {{ request('phase') == 'PRESS' ? 'selected' : '' }}>PRESS</option>
                        <option value="CUT" {{ request('phase') == 'CUT' ? 'selected' : '' }}>CUT</option>
                        <option value="SEW" {{ request('phase') == 'SEW' ? 'selected' : '' }}>SEW</option>
                        <option value="QC" {{ request('phase') == 'QC' ? 'selected' : '' }}>QC</option>
                        <option value="IRON/PACKING" {{ request('phase') == 'IRON/PACKING' ? 'selected' : '' }}>IRON/PACKING</option>
                    </select>
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 transition-colors">
                        <i class="fas fa-search mr-1"></i>
                        Search
                    </button>
                    @if(request('search') || request('status') || request('phase'))
                        <a href="{{ route('jobs.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                            <i class="fas fa-times mr-1"></i>
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Jobs Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($jobs as $job)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <!-- Job Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-tasks text-primary-500 text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $job->phase }}</h3>
                                    <p class="text-sm text-gray-500">Job #{{ $job->job_id }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('jobs.show', $job) }}" 
                                   class="text-primary-600 hover:text-primary-700 p-1">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                                <a href="{{ route('jobs.edit', $job) }}" 
                                   class="text-gray-600 hover:text-gray-700 p-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                                @if($job->qr_code)
                                <a href="{{ route('jobs.qr', $job) }}" 
                                   target="_blank" 
                                   class="text-green-600 hover:text-green-700 p-1">
                                    <i class="fas fa-qrcode"></i>
                                </a>
                                @endif
                            </div>
                        </div>

                        <!-- Job Details -->
                        <div class="space-y-3 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-shopping-cart w-4 mr-2"></i>
                                <span class="truncate">Order #{{ $job->order->order_id }} - {{ $job->order->job_name }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-user w-4 mr-2"></i>
                                <span>{{ $job->order->client->name }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-clock w-4 mr-2"></i>
                                <span>{{ $job->phase }}</span>
                            </div>
                        </div>

                        <!-- Status and Progress -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Status</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($job->status === 'Pending') bg-yellow-100 text-yellow-800
                                    @elseif($job->status === 'In Progress') bg-blue-100 text-blue-800
                                    @elseif($job->status === 'Completed') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $job->status }}
                                </span>
                            </div>
                            
                            @if($job->start_quantity && $job->end_quantity)
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                @php
                                    $progress = $job->end_quantity > 0 ? ($job->end_quantity / $job->start_quantity) * 100 : 0;
                                @endphp
                                <div class="bg-primary-600 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>{{ $job->start_quantity }} → {{ $job->end_quantity }}</span>
                                <span>{{ number_format($progress, 1) }}%</span>
                            </div>
                            @endif
                        </div>

                        <!-- Time Tracking -->
                        @if($job->start_time)
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <span>Started:</span>
                                    <span>{{ $job->start_time->format('M d, H:i') }}</span>
                                </div>
                                @if($job->end_time)
                                <div class="flex justify-between">
                                    <span>Completed:</span>
                                    <span>{{ $job->end_time->format('M d, H:i') }}</span>
                                </div>
                                <div class="flex justify-between font-medium">
                                    <span>Duration:</span>
                                    <span>{{ $job->duration ?? 'N/A' }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex space-x-2">
                            @if($job->status === 'Pending' && auth()->user()->isProductionStaff())
                                <button onclick="startJob({{ $job->job_id }})" 
                                        class="flex-1 bg-green-600 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <i class="fas fa-play mr-1"></i> Start
                                </button>
                            @endif
                            
                            @if($job->status === 'In Progress' && auth()->user()->isProductionStaff())
                                <button onclick="endJob({{ $job->job_id }})" 
                                        class="flex-1 bg-red-600 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <i class="fas fa-stop mr-1"></i> End
                                </button>
                            @endif
                            
                            @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                                <button onclick="assignJob({{ $job->job_id }})" 
                                        class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-user-plus mr-1"></i> Assign
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-12">
                        <i class="fas fa-tasks text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No jobs found</h3>
                        <p class="text-gray-500">No production jobs match your current filters.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($jobs->hasPages())
        <div class="mt-8">
            {{ $jobs->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function startJob(jobId) {
    if (confirm('Are you sure you want to start this job?')) {
        fetch(`/jobs/${jobId}/start`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to start job');
        });
    }
}

function endJob(jobId) {
    if (confirm('Are you sure you want to end this job?')) {
        fetch(`/jobs/${jobId}/end`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to end job');
        });
    }
}

function assignJob(jobId) {
    // Open assignment modal or redirect to assignment page
    window.location.href = `/jobs/${jobId}/assign`;
}
</script>
@endsection 