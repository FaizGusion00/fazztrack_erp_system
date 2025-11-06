@extends('layouts.app')

@section('title', 'Reports Dashboard - Fazztrack')

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
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-sm font-medium text-gray-500">Reports</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center">
                        <i class="fas fa-chart-bar mr-3 text-primary-600"></i>
                        Reports Dashboard
                    </h1>
                    <p class="text-gray-600">Comprehensive analytics and insights for your business</p>
                </div>
                <div class="mt-4 md:mt-0 flex items-center space-x-3">
                    <div class="relative">
                        <button id="exportDropdown" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors shadow-sm">
                            <i class="fas fa-download mr-2"></i>
                            Export Data
                            <i class="fas fa-chevron-down ml-2"></i>
                        </button>
                        <div id="exportMenu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                            <div class="py-1">
                                <a href="{{ route('reports.export') }}?type=orders&start_date={{ $startDate }}&end_date={{ $endDate }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-shopping-cart mr-2"></i>Export Orders
                                </a>
                                <a href="{{ route('reports.export') }}?type=jobs&start_date={{ $startDate }}&end_date={{ $endDate }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-tasks mr-2"></i>Export Jobs
                                </a>
                                <a href="{{ route('reports.export') }}?type=users&start_date={{ $startDate }}&end_date={{ $endDate }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-users mr-2"></i>Export Users
                                </a>
                                <a href="{{ route('reports.export') }}?type=financial&start_date={{ $startDate }}&end_date={{ $endDate }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-dollar-sign mr-2"></i>Export Financial
                                </a>
                            </div>
                        </div>
                    </div>
                    <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
                        <i class="fas fa-print mr-2"></i>
                        Print
                    </button>
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
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 transform hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <i class="fas fa-dollar-sign text-gray-800 text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-medium text-gray-800 opacity-90">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900">RM {{ number_format($stats['total_revenue'], 2) }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-800 opacity-90">Avg per order</span>
                    <span class="font-semibold text-gray-900">RM {{ number_format($stats['average_order_value'] ?? 0, 2) }}</span>
                </div>
            </div>

            <!-- Orders Card -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 transform hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <i class="fas fa-shopping-cart text-gray-800 text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-medium text-gray-800 opacity-90">Total Orders</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-800 opacity-90">Completed</span>
                    <span class="font-semibold text-gray-900">{{ $stats['completed_orders'] }} ({{ $stats['total_orders'] > 0 ? round(($stats['completed_orders'] / $stats['total_orders']) * 100, 1) : 0 }}%)</span>
                </div>
            </div>

            <!-- Jobs Card -->
            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 transform hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <i class="fas fa-tasks text-gray-800 text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-medium text-gray-800 opacity-90">Production Jobs</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_jobs'] }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-800 opacity-90">Completed</span>
                    <span class="font-semibold text-gray-900">{{ $stats['completed_jobs'] }} ({{ $stats['total_jobs'] > 0 ? round(($stats['completed_jobs'] / $stats['total_jobs']) * 100, 1) : 0 }}%)</span>
                </div>
            </div>

            <!-- Efficiency Card -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 transform hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <i class="fas fa-chart-line text-gray-800 text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-medium text-gray-800 opacity-90">Production Efficiency</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['production_efficiency'] }}%</p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-800 opacity-90">Completion rate</span>
                    <span class="font-semibold text-gray-900">
                        @if($stats['production_efficiency'] >= 80)
                            <i class="fas fa-arrow-up mr-1"></i>Excellent
                        @elseif($stats['production_efficiency'] >= 60)
                            <i class="fas fa-minus mr-1"></i>Good
                        @else
                            <i class="fas fa-arrow-down mr-1"></i>Needs Improvement
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Revenue Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-chart-area mr-2 text-green-500"></i>
                        Revenue Trend
                    </h3>
                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ count($chartData['revenue_chart']) }} days</span>
                </div>
                <canvas id="revenueChart" width="400" height="200"></canvas>
            </div>

            <!-- Orders Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-chart-bar mr-2 text-blue-500"></i>
                        Orders Trend
                    </h3>
                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ count($chartData['orders_chart']) }} days</span>
                </div>
                <canvas id="ordersChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Detailed Statistics -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Orders by Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-list-check mr-2 text-blue-500"></i>
                    Orders by Status
                </h3>
                @if(count($chartData['orders_by_status']) > 0)
                    <div class="mb-4">
                        <canvas id="orderStatusChart" height="200"></canvas>
                    </div>
                    <div class="space-y-3">
                        @foreach($chartData['orders_by_status'] as $status)
                            <div class="flex items-center justify-between p-2 rounded hover:bg-gray-50">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-2 status-indicator-{{ $loop->index }}"></div>
                                    <span class="text-sm font-medium text-gray-600">{{ $status->status }}</span>
                                </div>
                                <span class="text-sm font-bold text-gray-900">{{ $status->count }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-chart-pie text-gray-300 text-4xl mb-2"></i>
                        <p class="text-gray-500 text-sm">No data available</p>
                    </div>
                @endif
            </div>

            <!-- Jobs by Phase -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-layer-group mr-2 text-yellow-500"></i>
                    Jobs by Phase
                </h3>
                @if(count($chartData['jobs_by_phase']) > 0)
                    <div class="mb-4">
                        <canvas id="jobsPhaseChart" height="200"></canvas>
                    </div>
                    <div class="space-y-3">
                        @foreach($chartData['jobs_by_phase'] as $phase)
                            <div class="flex items-center justify-between p-2 rounded hover:bg-gray-50">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-2 phase-indicator-{{ $loop->index }}"></div>
                                    <span class="text-sm font-medium text-gray-600">{{ $phase->phase }}</span>
                                </div>
                                <span class="text-sm font-bold text-gray-900">{{ $phase->count }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-chart-pie text-gray-300 text-4xl mb-2"></i>
                        <p class="text-gray-500 text-sm">No data available</p>
                    </div>
                @endif
            </div>

            <!-- User Activity -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-users mr-2 text-purple-500"></i>
                    Users by Role
                </h3>
                @if(count($chartData['user_activity']) > 0)
                    <div class="mb-4">
                        <canvas id="userActivityChart" height="200"></canvas>
                    </div>
                    <div class="space-y-3">
                        @foreach($chartData['user_activity'] as $user)
                            <div class="flex items-center justify-between p-2 rounded hover:bg-gray-50">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-2 user-indicator-{{ $loop->index }}"></div>
                                    <span class="text-sm font-medium text-gray-600">{{ $user->role }}</span>
                                </div>
                                <span class="text-sm font-bold text-gray-900">{{ $user->count }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-chart-pie text-gray-300 text-4xl mb-2"></i>
                        <p class="text-gray-500 text-sm">No data available</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Top Clients -->
        @if(isset($stats['top_clients']) && count($stats['top_clients']) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8 hover:shadow-md transition-shadow">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <i class="fas fa-trophy mr-2 text-yellow-500"></i>
                Top Clients
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                @foreach($stats['top_clients'] as $client)
                    <div class="text-center p-4 rounded-lg border-2 border-gray-200 hover:border-primary-300 transition-colors">
                        <div class="text-3xl font-bold text-primary-600 mb-1">#{{ $loop->iteration }}</div>
                        <div class="text-sm font-medium text-gray-900 truncate mb-1">{{ $client->name }}</div>
                        <div class="text-xs text-gray-500">{{ $client->orders_count }} orders</div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

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
// Export dropdown toggle
document.getElementById('exportDropdown').addEventListener('click', function() {
    document.getElementById('exportMenu').classList.toggle('hidden');
});

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('exportDropdown');
    const menu = document.getElementById('exportMenu');
    if (!dropdown.contains(event.target)) {
        menu.classList.add('hidden');
    }
});

// Chart.js default configuration
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#64748b';

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: @json($chartData['revenue_chart']->pluck('date')),
        datasets: [{
            label: 'Revenue (RM)',
            data: @json($chartData['revenue_chart']->pluck('revenue')),
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: 'rgb(34, 197, 94)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: { size: 14 },
                bodyFont: { size: 13 },
                callbacks: {
                    label: function(context) {
                        return 'RM ' + context.parsed.y.toFixed(2);
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    callback: function(value) {
                        return 'RM ' + value.toFixed(0);
                    }
                }
            },
            x: {
                grid: {
                    display: false
                }
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
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderColor: 'rgb(59, 130, 246)',
            borderWidth: 2,
            borderRadius: 6,
            hoverBackgroundColor: 'rgba(59, 130, 246, 1)'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: { size: 14 },
                bodyFont: { size: 13 }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    stepSize: 1
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// Order Status Doughnut Chart
@if(count($chartData['orders_by_status']) > 0)
const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
const statusColors = [
    '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', 
    '#ec4899', '#14b8a6', '#f97316', '#06b6d4', '#6366f1'
];
const orderStatusChart = new Chart(orderStatusCtx, {
    type: 'doughnut',
    data: {
        labels: @json($chartData['orders_by_status']->pluck('status')),
        datasets: [{
            data: @json($chartData['orders_by_status']->pluck('count')),
            backgroundColor: statusColors.slice(0, {{ count($chartData['orders_by_status']) }}),
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12
            }
        }
    }
});

// Set status indicator colors
@foreach($chartData['orders_by_status'] as $status)
    document.querySelectorAll('.status-indicator-{{ $loop->index }}').forEach(el => {
        el.style.backgroundColor = statusColors[{{ $loop->index }}];
    });
@endforeach
@endif

// Jobs Phase Doughnut Chart
@if(count($chartData['jobs_by_phase']) > 0)
const jobsPhaseCtx = document.getElementById('jobsPhaseChart').getContext('2d');
const phaseColors = [
    '#eab308', '#f59e0b', '#f97316', '#ef4444', '#ec4899',
    '#d946ef', '#a855f7', '#8b5cf6', '#6366f1', '#3b82f6'
];
const jobsPhaseChart = new Chart(jobsPhaseCtx, {
    type: 'doughnut',
    data: {
        labels: @json($chartData['jobs_by_phase']->pluck('phase')),
        datasets: [{
            data: @json($chartData['jobs_by_phase']->pluck('count')),
            backgroundColor: phaseColors.slice(0, {{ count($chartData['jobs_by_phase']) }}),
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12
            }
        }
    }
});

// Set phase indicator colors
@foreach($chartData['jobs_by_phase'] as $phase)
    document.querySelectorAll('.phase-indicator-{{ $loop->index }}').forEach(el => {
        el.style.backgroundColor = phaseColors[{{ $loop->index }}];
    });
@endforeach
@endif

// User Activity Doughnut Chart
@if(count($chartData['user_activity']) > 0)
const userActivityCtx = document.getElementById('userActivityChart').getContext('2d');
const userColors = [
    '#8b5cf6', '#a855f7', '#c026d3', '#d946ef', '#ec4899',
    '#f43f5e', '#ef4444', '#f97316', '#f59e0b', '#eab308'
];
const userActivityChart = new Chart(userActivityCtx, {
    type: 'doughnut',
    data: {
        labels: @json($chartData['user_activity']->pluck('role')),
        datasets: [{
            data: @json($chartData['user_activity']->pluck('count')),
            backgroundColor: userColors.slice(0, {{ count($chartData['user_activity']) }}),
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12
            }
        }
    }
});

// Set user indicator colors
@foreach($chartData['user_activity'] as $user)
    document.querySelectorAll('.user-indicator-{{ $loop->index }}').forEach(el => {
        el.style.backgroundColor = userColors[{{ $loop->index }}];
    });
@endforeach
@endif
</script>

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
    .border {
        border: 1px solid #ddd !important;
    }
}
</style>
@endsection 