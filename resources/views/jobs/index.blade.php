@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-6">
        <!-- Compact Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-900 bg-clip-text text-transparent flex items-center">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg sm:rounded-xl flex items-center justify-center mr-2 sm:mr-3 lg:mr-4 shadow-lg">
                            <i class="fas fa-tasks text-white text-sm sm:text-base lg:text-xl"></i>
                        </div>
                        <span class="hidden sm:inline">Production Jobs</span>
                        <span class="sm:hidden">Jobs</span>
                    </h1>
                    <p class="mt-2 sm:mt-3 text-sm sm:text-base lg:text-lg text-gray-600">Manage and track production jobs across all phases.</p>
                </div>
                {{-- @if(auth()->user()->isSuperAdmin() || auth()->user()->isSalesManager())
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('jobs.create') }}" 
                       class="inline-flex items-center px-3 sm:px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 border border-transparent rounded-lg font-medium text-white hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 shadow-lg">
                        <i class="fas fa-plus mr-2"></i>
                        <span class="hidden sm:inline">Create Job</span>
                        <span class="sm:hidden">Add</span>
                    </a>
                </div>
                @endif --}}
            </div>
        </div>

        <!-- Compact Search and Filters -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-sm border border-white/20 p-4 sm:p-6 mb-6">
            <form method="GET" action="{{ route('jobs.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="lg:col-span-2">
                    <label for="search" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Search Jobs</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search by job ID, order, or phase..."
                               class="block w-full pl-10 pr-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div>
                    <label for="status" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Status</label>
                    <select id="status" name="status" 
                            class="block w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="On Hold" {{ request('status') == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                    </select>
                </div>
                <div>
                    <label for="phase" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Phase</label>
                    <select id="phase" name="phase" 
                            class="block w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Phases</option>
                        <option value="PRINT" {{ request('phase') == 'PRINT' ? 'selected' : '' }}>PRINT</option>
                        <option value="PRESS" {{ request('phase') == 'PRESS' ? 'selected' : '' }}>PRESS</option>
                        <option value="CUT" {{ request('phase') == 'CUT' ? 'selected' : '' }}>CUT</option>
                        <option value="SEW" {{ request('phase') == 'SEW' ? 'selected' : '' }}>SEW</option>
                        <option value="QC" {{ request('phase') == 'QC' ? 'selected' : '' }}>QC</option>
                    </select>
                </div>
                <div>
                    <label for="sort" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Sort By</label>
                    <select id="sort" name="sort" 
                            class="block w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="latest_added" {{ request('sort', 'latest_added') == 'latest_added' ? 'selected' : '' }}>Latest Added</option>
                        <option value="latest_updated" {{ request('sort') == 'latest_updated' ? 'selected' : '' }}>Latest Updated</option>
                        <option value="alphabetical" {{ request('sort') == 'alphabetical' ? 'selected' : '' }}>Alphabetical</option>
                    </select>
                </div>
                <div class="lg:col-span-4 flex items-end space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 text-xs sm:text-sm font-medium">
                        <i class="fas fa-search mr-1"></i>
                        Search
                    </button>
                    @if(request('search') || request('status') || request('phase') || request('sort'))
                        <a href="{{ route('jobs.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-300 text-xs sm:text-sm font-medium">
                            <i class="fas fa-times mr-1"></i>
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Jobs List Organized by Order -->
        <div class="space-y-6">
            @php
                $jobsByOrder = $jobs->groupBy('order_id');
            @endphp
            
            @forelse($jobsByOrder as $orderId => $orderJobs)
                @php
                    $firstJob = $orderJobs->first();
                    $order = $firstJob->order;
                    $client = $order->client;
                @endphp
                
                <!-- Order Header Card -->
                <div class="bg-white/80 backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-sm border border-white/20 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <!-- Order Summary Header -->
                    <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center space-x-3 sm:space-x-4">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg">
                                    <span class="text-white text-sm sm:text-base font-bold">{{ substr($client->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h3 class="text-lg sm:text-xl font-bold text-gray-900">Order #{{ $order->order_id }}</h3>
                                    <p class="text-sm sm:text-base text-gray-600">{{ $order->job_name }}</p>
                                    <p class="text-xs sm:text-sm text-gray-500">{{ $client->name }}</p>
                                </div>
                            </div>
                            <div class="mt-3 sm:mt-0 flex items-center space-x-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $orderJobs->count() }} Jobs
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    RM {{ number_format($order->total_amount, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Jobs List for this Order -->
                    <div class="divide-y divide-gray-200">
                        @foreach($orderJobs as $job)
                        <div class="p-4 sm:p-6 lg:p-8 hover:bg-gray-50/50 transition-colors duration-200">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0 lg:space-x-6">
                                <!-- Job Info -->
                                <div class="flex-1">
                                    <div class="flex items-start space-x-3 sm:space-x-4">
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-primary-400 to-primary-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg">
                                            <i class="fas fa-tasks text-white text-sm sm:text-base"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <h4 class="text-lg sm:text-xl font-semibold text-gray-900">{{ $job->phase }}</h4>
                                                <span class="text-sm sm:text-base text-gray-500">#{{ $job->job_id }}</span>
                                            </div>
                                            
                                            <!-- Job Details Grid -->
                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 text-xs sm:text-sm">
                                                <div class="flex items-center text-gray-600">
                                                    <i class="fas fa-clock w-4 mr-2 text-blue-500"></i>
                                                    <span>{{ $job->phase }}</span>
                                                </div>
                                                <div class="flex items-center text-gray-600">
                                                    <i class="fas fa-calendar w-4 mr-2 text-green-500"></i>
                                                    <span>{{ $job->created_at->format('M d, Y') }}</span>
                                                </div>
                                                @if($job->start_quantity && $job->end_quantity)
                                                <div class="flex items-center text-gray-600">
                                                    <i class="fas fa-boxes w-4 mr-2 text-purple-500"></i>
                                                    <span>{{ $job->start_quantity }} → {{ $job->end_quantity }}</span>
                                                    @if($job->reject_quantity)
                                                        <span class="ml-2 text-red-600 font-semibold">(Reject: {{ $job->reject_quantity }})</span>
                                                    @endif
                                                </div>
                                                @endif
                                                @if($job->reject_quantity && $job->reject_status)
                                                <div class="flex items-center text-gray-600">
                                                    <i class="fas fa-exclamation-triangle w-4 mr-2 text-red-500"></i>
                                                    <span class="text-red-600">{{ $job->phase }}: {{ $job->reject_status }}</span>
                                                </div>
                                                @endif
                                                @if($job->start_time)
                                                <div class="flex items-center text-gray-600">
                                                    <i class="fas fa-play w-4 mr-2 text-orange-500"></i>
                                                    <span>{{ $job->start_time->format('H:i') }}</span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status and Progress -->
                                <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                                    <!-- Status Badge -->
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                            @if($job->status === 'Pending') bg-yellow-100 text-yellow-800 border border-yellow-200
                                            @elseif($job->status === 'In Progress') bg-blue-100 text-blue-800 border border-blue-200
                                            @elseif($job->status === 'Completed') bg-green-100 text-green-800 border border-green-200
                                            @else bg-red-100 text-red-800 border border-red-200
                                            @endif">
                                            <div class="w-2 h-2 rounded-full mr-2
                                                @if($job->status === 'Pending') bg-yellow-500
                                                @elseif($job->status === 'In Progress') bg-blue-500
                                                @elseif($job->status === 'Completed') bg-green-500
                                                @else bg-red-500
                                                @endif"></div>
                                            {{ $job->status }}
                                        </span>
                                    </div>

                                    <!-- Progress Bar -->
                                    @if($job->start_quantity && $job->end_quantity)
                                    <div class="flex-1 min-w-0 sm:min-w-[200px]">
                                        <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                                            <span>Progress</span>
                                            @php
                                                $progress = $job->end_quantity > 0 ? ($job->end_quantity / $job->start_quantity) * 100 : 0;
                                            @endphp
                                            <span>{{ number_format($progress, 1) }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all duration-300" 
                                                 style="width: {{ $progress }}%"></div>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center space-x-2">
                                    <!-- View Details -->
                                    <a href="{{ route('jobs.show', $job) }}" 
                                       class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 text-xs sm:text-sm font-medium shadow-sm">
                                        <i class="fas fa-eye mr-1"></i>
                                        <span class="hidden sm:inline">Details</span>
                                    </a>
                                    
                                    <!-- QR Code -->
                                    @if($job->qr_code)
                                    <a href="{{ route('jobs.qr', $job) }}" 
                                       target="_blank" 
                                       class="inline-flex items-center px-3 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all duration-300 text-xs sm:text-sm font-medium shadow-sm">
                                        <i class="fas fa-qrcode"></i>
                                    </a>
                                    @endif
                                    
                                    <!-- Edit (Admin Only) -->
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSalesManager())
                                    <a href="{{ route('jobs.edit', $job) }}" 
                                       class="inline-flex items-center px-3 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-300 text-xs sm:text-sm font-medium shadow-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    
                                    <!-- Job Actions -->
                                    <div class="flex space-x-1">
                                        @if($job->status === 'Pending' && auth()->user()->isProductionStaff())
                                            <button onclick="startJob({{ $job->job_id }})" 
                                                    class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-300 text-xs sm:text-sm font-medium shadow-sm">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        @endif
                                        
                                        @if($job->status === 'In Progress' && auth()->user()->isProductionStaff())
                                            <button onclick="endJob({{ $job->job_id }})" 
                                                    class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-300 text-xs sm:text-sm font-medium shadow-sm">
                                                <i class="fas fa-stop"></i>
                                            </button>
                                        @endif
                                        
                                        {{-- @if(auth()->user()->isSuperAdmin() || auth()->user()->isSalesManager())
                                            <button onclick="assignJob({{ $job->job_id }})" 
                                                    class="inline-flex items-center px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all duration-300 text-xs sm:text-sm font-medium shadow-sm">
                                                <i class="fas fa-user-plus"></i>
                                            </button>
                                        @endif --}}
                                    </div>
                                </div>
                            </div>

                            <!-- Time Tracking Details -->
                            @if($job->start_time)
                            <div class="mt-4 p-3 sm:p-4 bg-gray-50/50 rounded-lg border border-gray-200">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs sm:text-sm text-gray-600">
                                    <div class="flex justify-between sm:justify-start">
                                        <span class="font-medium">Started:</span>
                                        <span class="sm:ml-2">{{ $job->start_time->format('M d, H:i') }}</span>
                                    </div>
                                    @if($job->end_time)
                                    <div class="flex justify-between sm:justify-start">
                                        <span class="font-medium">Completed:</span>
                                        <span class="sm:ml-2">{{ $job->end_time->format('M d, H:i') }}</span>
                                    </div>
                                    <div class="flex justify-between sm:justify-start">
                                        <span class="font-medium">Duration:</span>
                                        <span class="sm:ml-2 text-green-600 font-semibold">{{ $job->duration ?? 'N/A' }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-white/80 backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-sm border border-white/20 p-8 sm:p-12">
                    <div class="text-center">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-tasks text-gray-400 text-2xl sm:text-3xl"></i>
                        </div>
                        <h3 class="text-lg sm:text-xl font-medium text-gray-900 mb-2">No jobs found</h3>
                        <p class="text-sm sm:text-base text-gray-500">No production jobs match your current filters.</p>
                        {{-- @if(auth()->user()->isSuperAdmin() || auth()->user()->isSalesManager())
                        <div class="mt-6">
                            <a href="{{ route('jobs.create') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 text-sm font-medium">
                                <i class="fas fa-plus mr-2"></i>
                                Create First Job
                            </a>
                        </div>
                        @endif --}}
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

// function assignJob(jobId) {
//     // Open assignment modal or redirect to assignment page
//     window.location.href = `/jobs/${jobId}/assign`;
// }

// Add smooth animations
document.addEventListener('DOMContentLoaded', function() {
    // Animate cards on load
    const cards = document.querySelectorAll('.bg-white\\/80');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endsection 