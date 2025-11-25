@extends('layouts.app')

@section('title', 'Order Report - Fazztrack')

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
                        <span class="text-sm font-medium text-gray-500">Order Report</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-shopping-cart mr-3 text-blue-600"></i>
                    Order Report
                </h1>
                <p class="text-gray-600">Detailed order analysis for the selected period</p>
            </div>
            <div class="mt-4 md:mt-0 flex items-center space-x-3">
                <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors shadow-sm no-print">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
                <a href="{{ route('reports.export', ['type' => 'orders', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
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
            <form method="GET" action="{{ route('reports.orders') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
            $totalAmount = $orders->sum(function($order) {
                return $order->design_deposit + $order->production_deposit + $order->balance_payment;
            });
            $avgAmount = $orders->count() > 0 ? $totalAmount / $orders->count() : 0;
            $completedCount = $orders->where('status', 'Completed')->count();
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-800 mb-1">Total Orders</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $orders->count() }}</p>
                    </div>
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <i class="fas fa-shopping-cart text-gray-800 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-800 mb-1">Total Revenue</p>
                        <p class="text-3xl font-bold text-gray-900">RM {{ number_format($totalAmount, 2) }}</p>
                    </div>
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <i class="fas fa-dollar-sign text-gray-800 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-800 mb-1">Average Value</p>
                        <p class="text-3xl font-bold text-gray-900">RM {{ number_format($avgAmount, 2) }}</p>
                    </div>
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <i class="fas fa-chart-line text-gray-800 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-800 mb-1">Completed</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $completedCount }}</p>
                    </div>
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <i class="fas fa-check-circle text-gray-800 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold text-primary-600">#{{ $order->order_id }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->client->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->product->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="max-w-xs truncate">{{ $order->job_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @php
                                        $statusColors = [
                                            'Completed' => 'bg-green-100 text-green-800',
                                            'Order Created' => 'bg-blue-100 text-blue-800',
                                            'Order Approved' => 'bg-indigo-100 text-indigo-800',
                                            'Design Review' => 'bg-purple-100 text-purple-800',
                                            'Design Approved' => 'bg-pink-100 text-pink-800',
                                            'Job Created' => 'bg-yellow-100 text-yellow-800',
                                            'In Production' => 'bg-orange-100 text-orange-800',
                                            'Ready for Delivery' => 'bg-teal-100 text-teal-800',
                                            'On Hold' => 'bg-red-100 text-red-800',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    RM {{ number_format($order->design_deposit + $order->production_deposit + $order->balance_payment, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $order->created_at->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                                        <p class="text-gray-500 text-lg font-medium">No orders found for this period</p>
                                        <p class="text-gray-400 text-sm mt-1">Try adjusting your date range</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($orders->count() > 0)
                    <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-right text-sm font-bold text-gray-900">TOTAL:</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                                RM {{ number_format($totalAmount, 2) }}
                            </td>
                            <td class="px-6 py-4"></td>
                        </tr>
                    </tfoot>
                    @endif
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
