@extends('layouts.app')

@section('title', 'Financial Report - Fazztrack')

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
                        <span class="text-sm font-medium text-gray-500">Financial Report</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-dollar-sign mr-3 text-green-600"></i>
                    Financial Report
                </h1>
                <p class="text-gray-600">Revenue and payment analysis for the selected period</p>
            </div>
            <div class="mt-4 md:mt-0 flex items-center space-x-3">
                <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors shadow-sm no-print">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
                <a href="{{ route('reports.export', ['type' => 'financial', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
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
            <form method="GET" action="{{ route('reports.financial') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white/20 rounded-lg">
                        <i class="fas fa-dollar-sign text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-sm opacity-90 mb-1">Total Revenue</p>
                        <p class="text-2xl font-bold">RM {{ number_format($financialStats['total_revenue'], 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white/20 rounded-lg">
                        <i class="fas fa-paint-brush text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-sm opacity-90 mb-1">Design Deposits</p>
                        <p class="text-2xl font-bold">RM {{ number_format($financialStats['design_deposits'], 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white/20 rounded-lg">
                        <i class="fas fa-industry text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-sm opacity-90 mb-1">Production Deposits</p>
                        <p class="text-2xl font-bold">RM {{ number_format($financialStats['production_deposits'], 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white/20 rounded-lg">
                        <i class="fas fa-wallet text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-sm opacity-90 mb-1">Balance Payments</p>
                        <p class="text-2xl font-bold">RM {{ number_format($financialStats['balance_payments'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Order Value -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Average Order Value</p>
                    <p class="text-4xl font-bold text-gray-900">RM {{ number_format($financialStats['average_order_value'], 2) }}</p>
                </div>
                <div class="p-4 bg-gradient-to-br from-primary-100 to-primary-200 rounded-xl">
                    <i class="fas fa-chart-line text-primary-600 text-4xl"></i>
                </div>
            </div>
        </div>

        <!-- Revenue Chart and Payment Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Revenue Trend Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-chart-area mr-2 text-green-500"></i>
                    Revenue Trend
                </h3>
                @if(count($financialStats['revenue_by_month']) > 0)
                    <canvas id="revenueChart" height="250"></canvas>
                @else
                    <div class="flex items-center justify-center h-64">
                        <div class="text-center">
                            <i class="fas fa-chart-line text-gray-300 text-5xl mb-3"></i>
                            <p class="text-gray-500">No revenue data available</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Payment Breakdown Doughnut -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-chart-pie mr-2 text-purple-500"></i>
                    Payment Breakdown
                </h3>
                @if($financialStats['total_revenue'] > 0)
                    <canvas id="paymentBreakdownChart" height="250"></canvas>
                @else
                    <div class="flex items-center justify-center h-64">
                        <div class="text-center">
                            <i class="fas fa-chart-pie text-gray-300 text-5xl mb-3"></i>
                            <p class="text-gray-500">No payment data available</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Revenue by Month Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 pt-6 pb-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                    Revenue by Month
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($financialStats['revenue_by_month'] as $row)
                            @php
                                $monthName = DateTime::createFromFormat('!m', $row->month)->format('F');
                                $percentage = $financialStats['total_revenue'] > 0 
                                    ? round(($row->revenue / $financialStats['total_revenue']) * 100, 1) 
                                    : 0;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold text-gray-900">{{ $monthName }} {{ $row->year }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                    RM {{ number_format($row->revenue, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-full bg-gray-200 rounded-full h-2 mr-3" style="max-width: 150px;">
                                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700">{{ $percentage }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-calendar-times text-gray-300 text-5xl mb-4"></i>
                                        <p class="text-gray-500 text-lg font-medium">No revenue data found</p>
                                        <p class="text-gray-400 text-sm mt-1">Try adjusting your date range</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($financialStats['revenue_by_month']) > 0)
                    <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                        <tr>
                            <td class="px-6 py-4 text-left text-sm font-bold text-gray-900">TOTAL</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                                RM {{ number_format($financialStats['total_revenue'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">100%</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Trend Chart
@if(count($financialStats['revenue_by_month']) > 0)
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
const revenueLabels = @json($financialStats['revenue_by_month']->map(function($item) {
    return DateTime::createFromFormat('!m', $item->month)->format('M') . ' ' . $item->year;
})->values());
const revenueData = @json($financialStats['revenue_by_month']->pluck('revenue'));

const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: revenueLabels,
        datasets: [{
            label: 'Revenue (RM)',
            data: revenueData,
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointBackgroundColor: 'rgb(34, 197, 94)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
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
                ticks: {
                    callback: function(value) {
                        return 'RM ' + value.toFixed(0);
                    }
                }
            }
        }
    }
});
@endif

// Payment Breakdown Chart
@if($financialStats['total_revenue'] > 0)
const paymentCtx = document.getElementById('paymentBreakdownChart').getContext('2d');
const paymentChart = new Chart(paymentCtx, {
    type: 'doughnut',
    data: {
        labels: ['Design Deposits', 'Production Deposits', 'Balance Payments'],
        datasets: [{
            data: [
                {{ $financialStats['design_deposits'] }},
                {{ $financialStats['production_deposits'] }},
                {{ $financialStats['balance_payments'] }}
            ],
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',
                'rgba(234, 179, 8, 0.8)',
                'rgba(139, 92, 246, 0.8)'
            ],
            borderColor: ['#fff', '#fff', '#fff'],
            borderWidth: 2
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
                    font: {
                        size: 12
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return label + ': RM ' + value.toFixed(2) + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});
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
