@extends('layouts.app')

@section('title', 'Reports Dashboard - Fazztrack')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Reports Dashboard</h1>
                    <p class="text-gray-600">Comprehensive analytics and insights for your business</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('reports.export') }}?type=orders&start_date={{ $startDate }}&end_date={{ $endDate }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Export Data
                    </a>
                </div>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="period" class="block text-sm font-medium text-gray-700 mb-2">Quick Period</label>
                    <select id="period" name="period" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Last Week</option>
                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Last Month</option>
                        <option value="quarter" {{ $period == 'quarter' ? 'selected' : '' }}>Last Quarter</option>
                        <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Last Year</option>
                    </select>
                </div>
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

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Revenue Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900">RM {{ number_format($stats['total_revenue'], 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Orders Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Orders</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
                        <p class="text-sm text-gray-500">{{ $stats['completed_orders'] }} completed</p>
                    </div>
                </div>
            </div>

            <!-- Jobs Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <i class="fas fa-tasks text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Production Jobs</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_jobs'] }}</p>
                        <p class="text-sm text-gray-500">{{ $stats['completed_jobs'] }} completed</p>
                    </div>
                </div>
            </div>

            <!-- Efficiency Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Production Efficiency</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['production_efficiency'] }}%</p>
                        <p class="text-sm text-gray-500">Completion rate</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Revenue Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue Trend</h3>
                <canvas id="revenueChart" width="400" height="200"></canvas>
            </div>

            <!-- Orders Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Orders Trend</h3>
                <canvas id="ordersChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Detailed Statistics -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Orders by Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Orders by Status</h3>
                <div class="space-y-3">
                    @foreach($chartData['orders_by_status'] as $status)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">{{ $status->status }}</span>
                            <span class="text-sm font-bold text-gray-900">{{ $status->count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Jobs by Phase -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Jobs by Phase</h3>
                <div class="space-y-3">
                    @foreach($chartData['jobs_by_phase'] as $phase)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">{{ $phase->phase }}</span>
                            <span class="text-sm font-bold text-gray-900">{{ $phase->count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- User Activity -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Users by Role</h3>
                <div class="space-y-3">
                    @foreach($chartData['user_activity'] as $user)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">{{ $user->role }}</span>
                            <span class="text-sm font-bold text-gray-900">{{ $user->count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Reports</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('reports.orders') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="p-2 bg-blue-100 rounded-lg mr-3">
                        <i class="fas fa-shopping-cart text-blue-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Order Report</p>
                        <p class="text-sm text-gray-500">Detailed order analysis</p>
                    </div>
                </a>

                <a href="{{ route('reports.production') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                        <i class="fas fa-tasks text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Production Report</p>
                        <p class="text-sm text-gray-500">Job and phase analysis</p>
                    </div>
                </a>

                <a href="{{ route('reports.users') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="p-2 bg-green-100 rounded-lg mr-3">
                        <i class="fas fa-users text-green-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">User Performance</p>
                        <p class="text-sm text-gray-500">Staff productivity analysis</p>
                    </div>
                </a>

                <a href="{{ route('reports.financial') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="p-2 bg-purple-100 rounded-lg mr-3">
                        <i class="fas fa-chart-bar text-purple-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Financial Report</p>
                        <p class="text-sm text-gray-500">Revenue and payment analysis</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: @json($chartData['revenue_chart']->pluck('date')),
        datasets: [{
            label: 'Revenue (RM)',
            data: @json($chartData['revenue_chart']->pluck('revenue')),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Orders Chart
const ordersCtx = document.getElementById('ordersChart').getContext('2d');
const ordersChart = new Chart(ordersCtx, {
    type: 'bar',
    data: {
        labels: @json($chartData['orders_chart']->pluck('date')),
        datasets: [{
            label: 'Orders',
            data: @json($chartData['orders_chart']->pluck('count')),
            backgroundColor: 'rgba(34, 197, 94, 0.8)',
            borderColor: 'rgb(34, 197, 94)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endsection 