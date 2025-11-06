@extends('layouts.app')

@section('title', 'User Performance Report - Fazztrack')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
                        <i class="fas fa-home mr-2"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="{{ route('reports.index') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">
                            Reports
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-sm font-medium text-gray-500">User Performance</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-users mr-3 text-purple-600"></i>
                    User Performance Report
                </h1>
                <p class="text-gray-600">Staff productivity analysis for the selected period</p>
            </div>
            <div class="mt-4 md:mt-0 flex items-center space-x-3">
                <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors shadow-sm no-print">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
                <a href="{{ route('reports.export', ['type' => 'users', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors shadow-sm no-print">
                    <i class="fas fa-download mr-2"></i>Export CSV
                </a>
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors shadow-sm no-print">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
            </div>
        </div>

        <!-- Date Filter -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8 no-print">
            <form method="GET" action="{{ route('reports.users') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Apply Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        @php
            $totalUsers = $users->count();
            $activeUsers = $users->where('is_active', true)->count();
            $totalJobsAssigned = $users->sum('assigned_jobs_count');
            $totalDuration = $users->sum('assigned_jobs_sum_duration') ?? 0;
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-800 mb-1">Total Users</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $totalUsers }}</p>
                    </div>
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <i class="fas fa-users text-gray-800 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-800 mb-1">Active Users</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $activeUsers }}</p>
                    </div>
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <i class="fas fa-user-check text-gray-800 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-800 mb-1">Jobs Assigned</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $totalJobsAssigned }}</p>
                    </div>
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <i class="fas fa-tasks text-gray-800 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-800 mb-1">Total Duration</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($totalDuration, 0) }}</p>
                        <p class="text-xs text-gray-700">minutes</p>
                    </div>
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <i class="fas fa-clock text-gray-800 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        @php
            $topPerformers = $users->where('assigned_jobs_count', '>', 0)->sortByDesc('assigned_jobs_count')->take(3);
        @endphp
        @if($topPerformers->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            @foreach($topPerformers as $performer)
                <div class="bg-white rounded-xl shadow-sm border-2 {{ $loop->first ? 'border-yellow-400' : 'border-gray-200' }} p-6 text-center transform hover:scale-105 transition-transform">
                    @if($loop->first)
                        <i class="fas fa-trophy text-yellow-500 text-4xl mb-3"></i>
                    @elseif($loop->index == 1)
                        <i class="fas fa-medal text-gray-400 text-4xl mb-3"></i>
                    @else
                        <i class="fas fa-award text-orange-400 text-4xl mb-3"></i>
                    @endif
                    <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $performer->name }}</h3>
                    <p class="text-sm text-gray-500 mb-3">{{ $performer->role }}</p>
                    <div class="flex justify-center space-x-4 text-sm">
                        <div>
                            <p class="text-gray-500">Jobs</p>
                            <p class="text-lg font-bold text-primary-600">{{ $performer->assigned_jobs_count }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Duration</p>
                            <p class="text-lg font-bold text-green-600">{{ number_format($performer->assigned_jobs_sum_duration ?? 0, 0) }}m</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jobs Assigned</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Duration (min)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Duration (min)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                            @php
                                $avgDuration = $user->assigned_jobs_count > 0 && $user->assigned_jobs_sum_duration 
                                    ? $user->assigned_jobs_sum_duration / $user->assigned_jobs_count 
                                    : 0;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-primary-600 font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    {{ $user->assigned_jobs_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    {{ number_format($user->assigned_jobs_sum_duration ?? 0, 0) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($avgDuration > 0)
                                        <span class="font-semibold">{{ number_format($avgDuration, 2) }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($user->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-circle text-green-500 text-xs mr-1"></i>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-circle text-red-500 text-xs mr-1"></i>
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-user-slash text-gray-300 text-5xl mb-4"></i>
                                        <p class="text-gray-500 text-lg font-medium">No users found for this period</p>
                                        <p class="text-gray-400 text-sm mt-1">Try adjusting your date range</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    body {
        background: white !important;
    }
    .bg-gradient-to-br {
        background: white !important;
    }
    .shadow-sm, .shadow-lg {
        box-shadow: none !important;
    }
    table {
        page-break-inside: auto;
    }
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
}
</style>
@endsection
