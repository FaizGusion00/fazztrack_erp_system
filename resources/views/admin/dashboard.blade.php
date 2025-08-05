@extends('layouts.app')

@section('title', 'SuperAdmin Dashboard - Fazztrack')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-6">
        <!-- Compact Enhanced Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-900 bg-clip-text text-transparent flex items-center">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg sm:rounded-xl flex items-center justify-center mr-2 sm:mr-3 lg:mr-4 shadow-lg">
                            <i class="fas fa-crown text-white text-sm sm:text-base lg:text-xl"></i>
                        </div>
                        <span class="hidden sm:inline">SuperAdmin Dashboard</span>
                        <span class="sm:hidden">Dashboard</span>
                    </h1>
                    <p class="mt-2 sm:mt-3 text-sm sm:text-base lg:text-lg text-gray-600">Welcome back! Here's your business overview.</p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ now()->format('l, F j, Y') }}</p>
                </div>
                <div class="hidden md:flex items-center space-x-3 mt-4 sm:mt-0">
                    <div class="bg-white/80 backdrop-blur-sm rounded-lg px-3 py-2 shadow-sm border border-white/20">
                        <span class="text-xs sm:text-sm text-gray-600">Last updated:</span>
                        <span class="text-xs sm:text-sm font-medium text-gray-900 ml-1">{{ now()->format('g:i A') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compact Enhanced Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-6 sm:mb-8">
            <!-- Total Revenue -->
            <div class="group relative bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl sm:rounded-2xl shadow-sm border border-green-200 p-3 sm:p-4 lg:p-6 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-br from-green-400/10 to-emerald-400/10 rounded-xl sm:rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-dollar-sign text-white text-sm sm:text-base lg:text-lg"></i>
                        </div>
                        <div class="text-right">
                            <div class="w-2 h-2 sm:w-3 sm:h-3 bg-green-500 rounded-full animate-pulse"></div>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Total Revenue</p>
                    <p class="text-lg sm:text-xl lg:text-2xl xl:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">RM {{ number_format($stats['total_revenue'], 2) }}</p>
                    <div class="flex items-center text-xs sm:text-sm">
                        <i class="fas fa-arrow-up text-green-600 mr-1"></i>
                        <span class="text-green-600 font-medium">+12.5%</span>
                        <span class="text-gray-500 ml-1 hidden sm:inline">vs last month</span>
                    </div>
                </div>
            </div>

            <!-- Monthly Revenue -->
            <div class="group relative bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl sm:rounded-2xl shadow-sm border border-blue-200 p-3 sm:p-4 lg:p-6 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-400/10 to-indigo-400/10 rounded-xl sm:rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-chart-line text-white text-sm sm:text-base lg:text-lg"></i>
                        </div>
                        <div class="text-right">
                            <div class="w-2 h-2 sm:w-3 sm:h-3 bg-blue-500 rounded-full animate-pulse"></div>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Monthly Revenue</p>
                    <p class="text-lg sm:text-xl lg:text-2xl xl:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">RM {{ number_format($stats['monthly_revenue'], 2) }}</p>
                    <div class="flex items-center text-xs sm:text-sm">
                        <i class="fas fa-arrow-up text-blue-600 mr-1"></i>
                        <span class="text-blue-600 font-medium">+8.3%</span>
                        <span class="text-gray-500 ml-1 hidden sm:inline">vs last month</span>
                    </div>
                </div>
            </div>

            <!-- Average Order Value -->
            <div class="group relative bg-gradient-to-br from-purple-50 to-violet-100 rounded-xl sm:rounded-2xl shadow-sm border border-purple-200 p-3 sm:p-4 lg:p-6 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-400/10 to-violet-400/10 rounded-xl sm:rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-purple-500 to-violet-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-calculator text-white text-sm sm:text-base lg:text-lg"></i>
                        </div>
                        <div class="text-right">
                            <div class="w-2 h-2 sm:w-3 sm:h-3 bg-purple-500 rounded-full animate-pulse"></div>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Avg Order Value</p>
                    <p class="text-lg sm:text-xl lg:text-2xl xl:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">RM {{ number_format($stats['average_order_value'], 2) }}</p>
                    <div class="flex items-center text-xs sm:text-sm">
                        <i class="fas fa-arrow-up text-purple-600 mr-1"></i>
                        <span class="text-purple-600 font-medium">+5.2%</span>
                        <span class="text-gray-500 ml-1 hidden sm:inline">vs last month</span>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="group relative bg-gradient-to-br from-orange-50 to-amber-100 rounded-xl sm:rounded-2xl shadow-sm border border-orange-200 p-3 sm:p-4 lg:p-6 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-400/10 to-amber-400/10 rounded-xl sm:rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-orange-500 to-amber-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-shopping-cart text-white text-sm sm:text-base lg:text-lg"></i>
                        </div>
                        <div class="text-right">
                            <div class="w-2 h-2 sm:w-3 sm:h-3 bg-orange-500 rounded-full animate-pulse"></div>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Total Orders</p>
                    <p class="text-lg sm:text-xl lg:text-2xl xl:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">{{ $stats['total_orders'] }}</p>
                    <div class="flex items-center text-xs sm:text-sm">
                        <i class="fas fa-arrow-up text-orange-600 mr-1"></i>
                        <span class="text-orange-600 font-medium">+15.7%</span>
                        <span class="text-gray-500 ml-1 hidden sm:inline">vs last month</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compact Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8 mb-6 sm:mb-8">
            <!-- Revenue Chart -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-sm border border-white/20 p-4 sm:p-6 lg:p-8 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900">Revenue Overview</h3>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs sm:text-sm text-gray-500">Last 12 Months</span>
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                    </div>
                </div>
                <div class="relative h-48 sm:h-64 lg:h-80">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Order Status Chart -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-sm border border-white/20 p-4 sm:p-6 lg:p-8 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900">Order Status Distribution</h3>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs sm:text-sm text-gray-500">Real-time</span>
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    </div>
                </div>
                <div class="relative h-48 sm:h-64 lg:h-80">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Compact Statistics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 lg:gap-8 mb-6 sm:mb-8">
            <!-- Order Status Cards -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-sm border border-white/20 p-4 sm:p-6 lg:p-8 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900">Order Status</h3>
                    <i class="fas fa-chart-pie text-blue-500"></i>
                </div>
                <div class="space-y-3 sm:space-y-4">
                    <div class="flex items-center justify-between p-3 sm:p-4 bg-yellow-50 rounded-lg sm:rounded-xl border border-yellow-200">
                        <div class="flex items-center">
                            <div class="w-2 h-2 sm:w-3 sm:h-3 bg-yellow-500 rounded-full mr-2 sm:mr-3"></div>
                            <span class="text-xs sm:text-sm font-medium text-gray-700">Pending</span>
                        </div>
                        <span class="text-sm sm:text-base lg:text-lg font-bold text-yellow-600">{{ $stats['pending_orders'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 sm:p-4 bg-blue-50 rounded-lg sm:rounded-xl border border-blue-200">
                        <div class="flex items-center">
                            <div class="w-2 h-2 sm:w-3 sm:h-3 bg-blue-500 rounded-full mr-2 sm:mr-3"></div>
                            <span class="text-xs sm:text-sm font-medium text-gray-700">In Progress</span>
                        </div>
                        <span class="text-sm sm:text-base lg:text-lg font-bold text-blue-600">{{ $stats['in_progress_orders'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 sm:p-4 bg-green-50 rounded-lg sm:rounded-xl border border-green-200">
                        <div class="flex items-center">
                            <div class="w-2 h-2 sm:w-3 sm:h-3 bg-green-500 rounded-full mr-2 sm:mr-3"></div>
                            <span class="text-xs sm:text-sm font-medium text-gray-700">Completed</span>
                        </div>
                        <span class="text-sm sm:text-base lg:text-lg font-bold text-green-600">{{ $stats['completed_orders'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Business Metrics -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-sm border border-white/20 p-4 sm:p-6 lg:p-8 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900">Business Metrics</h3>
                    <i class="fas fa-chart-bar text-indigo-500"></i>
                </div>
                <div class="space-y-3 sm:space-y-4">
                    <div class="flex items-center justify-between p-3 sm:p-4 bg-indigo-50 rounded-lg sm:rounded-xl border border-indigo-200">
                        <div class="flex items-center">
                            <i class="fas fa-users text-indigo-500 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                            <span class="text-xs sm:text-sm font-medium text-gray-700">Total Clients</span>
                        </div>
                        <span class="text-sm sm:text-base lg:text-lg font-bold text-indigo-600">{{ $stats['total_clients'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 sm:p-4 bg-purple-50 rounded-lg sm:rounded-xl border border-purple-200">
                        <div class="flex items-center">
                            <i class="fas fa-briefcase text-purple-500 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                            <span class="text-xs sm:text-sm font-medium text-gray-700">Total Jobs</span>
                        </div>
                        <span class="text-sm sm:text-base lg:text-lg font-bold text-purple-600">{{ $stats['total_jobs'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 sm:p-4 bg-teal-50 rounded-lg sm:rounded-xl border border-teal-200">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-teal-500 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                            <span class="text-xs sm:text-sm font-medium text-gray-700">Active Orders</span>
                        </div>
                        <span class="text-sm sm:text-base lg:text-lg font-bold text-teal-600">{{ $stats['total_orders'] - $stats['completed_orders'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Top Clients -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-sm border border-white/20 p-4 sm:p-6 lg:p-8 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900">Top Clients</h3>
                    <i class="fas fa-trophy text-amber-500"></i>
                </div>
                <div class="space-y-3 sm:space-y-4">
                    @foreach($top_clients as $index => $client)
                    <div class="flex items-center justify-between p-3 sm:p-4 {{ $index === 0 ? 'bg-amber-50 border border-amber-200' : 'bg-gray-50 border border-gray-200' }} rounded-lg sm:rounded-xl">
                        <div class="flex items-center">
                            @if($index === 0)
                                <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-r from-amber-400 to-yellow-500 rounded-full flex items-center justify-center mr-2 sm:mr-3">
                                    <i class="fas fa-crown text-white text-xs"></i>
                                </div>
                            @else
                                <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gray-200 rounded-full flex items-center justify-center mr-2 sm:mr-3">
                                    <span class="text-xs font-bold text-gray-600">{{ $index + 1 }}</span>
                                </div>
                            @endif
                            <span class="text-xs sm:text-sm font-medium text-gray-700 truncate">{{ $client->name }}</span>
                        </div>
                        <span class="text-xs sm:text-sm font-bold text-gray-900">RM {{ number_format($client->orders_sum_total_amount ?? 0, 2) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Compact Recent Orders Table -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-sm border border-white/20 overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900">Recent Orders</h3>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs sm:text-sm text-gray-500">Latest transactions</span>
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order ID</th>
                            <th class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden sm:table-cell">Client</th>
                            <th class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Job Name</th>
                            <th class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">Amount</th>
                            <th class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden lg:table-cell">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white/50 divide-y divide-gray-200">
                        @foreach($recent_orders as $order)
                        <tr class="hover:bg-gray-50/50 transition-colors duration-200">
                            <td class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 whitespace-nowrap">
                                <span class="text-xs sm:text-sm font-bold text-gray-900">#{{ $order->order_id }}</span>
                            </td>
                            <td class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 whitespace-nowrap hidden sm:table-cell">
                                <div class="flex items-center">
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full flex items-center justify-center mr-2 sm:mr-3">
                                        <span class="text-white text-xs font-bold">{{ substr($order->client->name, 0, 1) }}</span>
                                    </div>
                                    <span class="text-xs sm:text-sm font-medium text-gray-900">{{ $order->client->name }}</span>
                                </div>
                            </td>
                            <td class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 whitespace-nowrap">
                                <span class="text-xs sm:text-sm text-gray-900">{{ $order->job_name }}</span>
                            </td>
                            <td class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'Order Created' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'Order Approved' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'Design Review' => 'bg-purple-100 text-purple-800 border-purple-200',
                                        'Design Approved' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                        'Job Created' => 'bg-orange-100 text-orange-800 border-orange-200',
                                        'Job Start' => 'bg-primary-100 text-primary-800 border-primary-200',
                                        'Job Complete' => 'bg-teal-100 text-teal-800 border-teal-200',
                                        'Order Packaging' => 'bg-pink-100 text-pink-800 border-pink-200',
                                        'Order Finished' => 'bg-green-100 text-green-800 border-green-200',
                                        'On Hold' => 'bg-red-100 text-red-800 border-red-200'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold border {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 whitespace-nowrap hidden md:table-cell">
                                <span class="text-xs sm:text-sm font-bold text-gray-900">RM {{ number_format($order->total_amount, 2) }}</span>
                            </td>
                            <td class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 whitespace-nowrap hidden lg:table-cell">
                                <span class="text-xs sm:text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Enhanced Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: @json(array_column($revenueData, 'month')),
        datasets: [{
            label: 'Revenue (RM)',
            data: @json(array_column($revenueData, 'revenue')),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: 'rgb(59, 130, 246)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
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
                        return 'RM ' + value.toLocaleString();
                    },
                    font: {
                        size: 10
                    }
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        size: 10
                    }
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
});

// Enhanced Order Status Chart
const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
const orderStatusChart = new Chart(orderStatusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'In Progress', 'Completed'],
        datasets: [{
            data: [{{ $stats['pending_orders'] }}, {{ $stats['in_progress_orders'] }}, {{ $stats['completed_orders'] }}],
            backgroundColor: [
                'rgb(245, 158, 11)',
                'rgb(59, 130, 246)',
                'rgb(34, 197, 94)'
            ],
            borderColor: [
                'rgb(245, 158, 11)',
                'rgb(59, 130, 246)',
                'rgb(34, 197, 94)'
            ],
            borderWidth: 2,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    usePointStyle: true,
                    font: {
                        size: 10
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Add smooth animations and interactions
document.addEventListener('DOMContentLoaded', function() {
    // Animate cards on load
    const cards = document.querySelectorAll('.group');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Add hover effects for table rows
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
            this.style.transition = 'transform 0.2s ease';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>
@endsection 