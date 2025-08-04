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
        <!-- My Assigned Jobs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tasks text-blue-500"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">My Jobs</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Job::where('assigned_user_id', auth()->user()->id)->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Pending Jobs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-500"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Job::where('assigned_user_id', auth()->user()->id)->where('status', 'Pending')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- In Progress Jobs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-play text-green-500"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">In Progress</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Job::where('assigned_user_id', auth()->user()->id)->where('status', 'In Progress')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Completed Today -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-purple-500"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Completed Today</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Job::where('assigned_user_id', auth()->user()->id)->where('status', 'Completed')->whereDate('end_time', today())->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- My Assigned Jobs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-user-check mr-2 text-primary-500"></i>
                    My Assigned Jobs
                </h3>
            </div>
        </div>
        <div class="p-6">
            @php
                $myJobs = \App\Models\Job::where('assigned_user_id', auth()->user()->id)
                    ->with(['order.client'])
                    ->latest()
                    ->take(10)
                    ->get();
            @endphp
            @if($myJobs->count() > 0)
                <div class="space-y-4">
                    @foreach($myJobs as $job)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-tasks text-primary-500"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $job->phase }} - Job #{{ $job->job_id }}</h4>
                                    <p class="text-sm text-gray-600">{{ $job->order->job_name }} • {{ $job->order->client->name }}</p>
                                    @if($job->start_time)
                                        <p class="text-xs text-green-600">Started: {{ $job->start_time->format('M d, H:i') }}</p>
                                    @endif
                                    @if($job->end_time)
                                        <p class="text-xs text-blue-600">Completed: {{ $job->end_time->format('M d, H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                @php
                                    $statusColors = [
                                        'Pending' => 'bg-yellow-100 text-yellow-800',
                                        'In Progress' => 'bg-blue-100 text-blue-800',
                                        'Completed' => 'bg-green-100 text-green-800',
                                        'On Hold' => 'bg-red-100 text-red-800'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$job->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $job->status }}
                                </span>
                                <a href="{{ route('jobs.show', $job) }}" 
                                   class="text-primary-600 hover:text-primary-700 text-xs font-medium">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-tasks text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No assigned jobs</h3>
                    <p class="text-gray-500">You don't have any jobs assigned to you yet.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Phase-Specific Jobs -->
    @if(auth()->user()->phase)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-tag mr-2 text-primary-500"></i>
                {{ auth()->user()->phase }} Phase Jobs
            </h3>
        </div>
        <div class="p-6">
            @php
                $phaseJobs = \App\Models\Job::where('phase', auth()->user()->phase)
                    ->with(['order.client', 'assignedUser'])
                    ->latest()
                    ->take(10)
                    ->get();
            @endphp
            @if($phaseJobs->count() > 0)
                <div class="space-y-4">
                    @foreach($phaseJobs as $job)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-tasks text-primary-500"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Job #{{ $job->job_id }}</h4>
                                    <p class="text-sm text-gray-600">{{ $job->order->job_name }} • {{ $job->order->client->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        Assigned to: {{ $job->assignedUser ? $job->assignedUser->name : 'Unassigned' }}
                                    </p>
                                    @if($job->start_time)
                                        <p class="text-xs text-green-600">Started: {{ $job->start_time->format('M d, H:i') }}</p>
                                    @endif
                                    @if($job->end_time)
                                        <p class="text-xs text-blue-600">Completed: {{ $job->end_time->format('M d, H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                @php
                                    $statusColors = [
                                        'Pending' => 'bg-yellow-100 text-yellow-800',
                                        'In Progress' => 'bg-blue-100 text-blue-800',
                                        'Completed' => 'bg-green-100 text-green-800',
                                        'On Hold' => 'bg-red-100 text-red-800'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$job->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $job->status }}
                                </span>
                                <a href="{{ route('jobs.show', $job) }}" 
                                   class="text-primary-600 hover:text-primary-700 text-xs font-medium">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-tasks text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No {{ auth()->user()->phase }} jobs</h3>
                    <p class="text-gray-500">No jobs are currently in the {{ auth()->user()->phase }} phase.</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Today's Activity -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-calendar-day mr-2 text-primary-500"></i>
                Today's Activity
            </h3>
        </div>
        <div class="p-6">
            @php
                $todayJobs = \App\Models\Job::where('assigned_user_id', auth()->user()->id)
                    ->where(function($query) {
                        $query->whereDate('start_time', today())
                              ->orWhereDate('end_time', today());
                    })
                    ->with(['order.client'])
                    ->get();
            @endphp
            @if($todayJobs->count() > 0)
                <div class="space-y-4">
                    @foreach($todayJobs as $job)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-tasks text-primary-500"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $job->phase }} - Job #{{ $job->job_id }}</h4>
                                    <p class="text-sm text-gray-600">{{ $job->order->job_name }}</p>
                                    @if($job->start_time && $job->start_time->isToday())
                                        <p class="text-xs text-green-600">Started at {{ $job->start_time->format('H:i') }}</p>
                                    @endif
                                    @if($job->end_time && $job->end_time->isToday())
                                        <p class="text-xs text-blue-600">Completed at {{ $job->end_time->format('H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                @php
                                    $statusColors = [
                                        'Pending' => 'bg-yellow-100 text-yellow-800',
                                        'In Progress' => 'bg-blue-100 text-blue-800',
                                        'Completed' => 'bg-green-100 text-green-800',
                                        'On Hold' => 'bg-red-100 text-red-800'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$job->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $job->status }}
                                </span>
                                <a href="{{ route('jobs.show', $job) }}" 
                                   class="text-primary-600 hover:text-primary-700 text-xs font-medium">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-calendar-day text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No activity today</h3>
                    <p class="text-gray-500">You haven't started or completed any jobs today.</p>
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