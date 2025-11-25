@extends('layouts.app')

@section('title', 'Production Dashboard - Fazztrack')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-cogs mr-3 text-primary-500"></i>
            Production Dashboard
        </h1>
        <p class="mt-2 text-gray-600">Manage your assigned jobs and track production progress.</p>
        @if(auth()->user()->phase)
        <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800">
            <i class="fas fa-tag mr-1"></i>
            Your Phase: {{ auth()->user()->phase }}
        </div>
        @endif
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Current Jobs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tasks text-blue-500"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Total Current Jobs</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $currentJobs->count() }}</p>
                </div>
            </div>
        </div>

        <!-- My Assigned Jobs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-check text-green-500"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">My Jobs</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $currentJobs->where('assigned_user_id', auth()->user()->id)->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Pending Jobs All -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-500"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Total Pending</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $currentJobs->where('status', 'Pending')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- In Progress All -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-play text-purple-500"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Total In Progress</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $currentJobs->where('status', 'In Progress')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- All Current Jobs by Phase -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-eye mr-2 text-primary-500"></i>
                All Current Jobs Progress View
            </h3>
            <p class="text-sm text-gray-600 mt-1">View progress of all jobs across all phases</p>
        </div>
        <div class="p-6">
            @if($jobsByPhase->count() > 0)
                <div class="space-y-8">
                    @foreach($jobsByPhase as $phase => $phaseJobs)
                        <div class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <i class="fas fa-tag mr-2 text-primary-500"></i>
                                    {{ $phase }} Phase
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                        {{ $phaseJobs->count() }} jobs
                                    </span>
                                </h4>
                                @if(auth()->user()->phase === $phase)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-star mr-1"></i>Your Phase
                                    </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($phaseJobs as $job)
                                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between mb-3">
                                            <div>
                                                <h5 class="font-medium text-gray-900">Job #{{ $job->job_id }}</h5>
                                                <p class="text-sm text-gray-600">{{ $job->order->job_name }}</p>
                                                <p class="text-xs text-gray-500">{{ $job->order->client->name }}</p>
                                            </div>
                                            @php
                                                $statusColors = [
                                                    'Pending' => 'bg-yellow-100 text-yellow-800',
                                                    'In Progress' => 'bg-blue-100 text-blue-800',
                                                    'Completed' => 'bg-green-100 text-green-800',
                                                    'On Hold' => 'bg-red-100 text-red-800'
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$job->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $job->status }}
                                            </span>
                                        </div>

                                        <!-- Progress Info -->
                                        <div class="space-y-2 text-xs">
                                            @if($job->status === 'Pending')
                                                {{-- For Pending jobs, show "Available" - no assignment needed --}}
                                                <div class="flex items-center text-green-600">
                                                    <i class="fas fa-check-circle w-3 mr-2"></i>
                                                    <span>Available - Ready to Start</span>
                                                </div>
                                            @elseif($job->assignedUser)
                                                {{-- For In Progress/Completed jobs, show who started it --}}
                                                <div class="flex items-center text-gray-600">
                                                    <i class="fas fa-user w-3 mr-2"></i>
                                                    <span>Started by: {{ $job->assignedUser->name }}</span>
                                                </div>
                                            @else
                                                {{-- Fallback for jobs without assigned user (shouldn't happen for In Progress) --}}
                                                <div class="flex items-center text-gray-500">
                                                    <i class="fas fa-info-circle w-3 mr-2"></i>
                                                    <span>No assignee</span>
                                                </div>
                                            @endif

                                            @if($job->status === 'In Progress' && $job->start_time)
                                                <div class="flex items-center text-blue-600">
                                                    <i class="fas fa-clock w-3 mr-2"></i>
                                                    <span>Started: {{ $job->start_time->diffForHumans() }}</span>
                                                </div>
                                            @endif

                                            @if($job->start_quantity && $job->end_quantity)
                                                <div class="flex items-center text-green-600">
                                                    <i class="fas fa-boxes w-3 mr-2"></i>
                                                    <span>{{ $job->start_quantity }} → {{ $job->end_quantity }}</span>
                                                    @if($job->reject_quantity)
                                                        <span class="ml-1 text-red-600">(Reject: {{ $job->reject_quantity }})</span>
                                                    @endif
                                                </div>
                                            @elseif($job->start_quantity)
                                                <div class="flex items-center text-blue-600">
                                                    <i class="fas fa-boxes w-3 mr-2"></i>
                                                    <span>Started with: {{ $job->start_quantity }}</span>
                                                </div>
                                            @endif

                                            @if($job->reject_quantity && $job->reject_status)
                                                <div class="flex items-center text-red-600">
                                                    <i class="fas fa-exclamation-triangle w-3 mr-2"></i>
                                                    <span>{{ $job->phase }}: {{ $job->reject_status }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Action button only for user's phase -->
                                        @if(auth()->user()->phase === $job->phase && $job->assigned_user_id === auth()->user()->id)
                                            <div class="mt-3 pt-3 border-t border-gray-200">
                                                <a href="{{ route('jobs.show', $job) }}"
                                                   class="inline-flex items-center px-3 py-1 bg-primary-600 text-white rounded text-xs font-medium hover:bg-primary-700 transition-colors">
                                                    <i class="fas fa-eye mr-1"></i>View Details
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-tasks text-gray-400 text-5xl mb-4"></i>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">No current jobs</h3>
                    <p class="text-gray-500">All jobs are currently completed or there are no active jobs in the system.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-history mr-2 text-primary-500"></i>
                Recent Activity (Last 7 Days)
            </h3>
            <p class="text-sm text-gray-600 mt-1">Completed jobs across all phases</p>
        </div>
        <div class="p-6">
            @if($recentCompletedJobs->count() > 0)
                <div class="space-y-4">
                    @foreach($recentCompletedJobs as $job)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-check-circle text-green-500"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $job->phase }} - Job #{{ $job->job_id }}</h4>
                                    <p class="text-sm text-gray-600">{{ $job->order->job_name }} • {{ $job->order->client->name }}</p>
                                    @if($job->assignedUser)
                                        <p class="text-xs text-blue-600">Completed by: {{ $job->assignedUser->name }}</p>
                                    @endif
                                    @if($job->end_time)
                                        <p class="text-xs text-gray-500">Completed: {{ $job->end_time->format('M d, Y H:i') }}</p>
                                    @endif
                                    @if($job->duration)
                                        <p class="text-xs text-purple-600">Duration: {{ $job->duration }} minutes</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Completed
                                </span>
                                @if($job->reject_quantity)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $job->reject_quantity }} rejects
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-history text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No recent activity</h3>
                    <p class="text-gray-500">No jobs have been completed in the last 7 days.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Auto-refresh dashboard every 30 seconds for live updates
setInterval(function() {
    location.reload();
}, 30000);
</script>
@endsection 