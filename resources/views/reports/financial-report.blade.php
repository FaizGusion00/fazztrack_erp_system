@extends('layouts.app')

@section('title', 'Financial Report - Fazztrack')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Financial Report</h1>
                <p class="text-gray-600">Revenue and payment analysis for the selected period</p>
            </div>
            <a href="{{ route('reports.export', ['type' => 'financial', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                <i class="fas fa-download mr-2"></i>Export Financials
            </a>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <form method="GET" action="{{ route('reports.financial') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
        <div class="mb-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                <p class="text-2xl font-bold text-gray-900">RM {{ number_format($financialStats['total_revenue'], 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <p class="text-sm font-medium text-gray-600">Design Deposits</p>
                <p class="text-2xl font-bold text-gray-900">RM {{ number_format($financialStats['design_deposits'], 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <p class="text-sm font-medium text-gray-600">Production Deposits</p>
                <p class="text-2xl font-bold text-gray-900">RM {{ number_format($financialStats['production_deposits'], 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <p class="text-sm font-medium text-gray-600">Balance Payments</p>
                <p class="text-2xl font-bold text-gray-900">RM {{ number_format($financialStats['balance_payments'], 2) }}</p>
            </div>
        </div>
        <div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <p class="text-sm font-medium text-gray-600">Average Order Value</p>
                <p class="text-2xl font-bold text-gray-900">RM {{ number_format($financialStats['average_order_value'], 2) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
            <h3 class="text-lg font-medium text-gray-900 px-6 pt-6">Revenue by Month</h3>
            <table class="min-w-full divide-y divide-gray-200 mt-2">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($financialStats['revenue_by_month'] as $row)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $row->year }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->month }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">RM {{ number_format($row->revenue, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-gray-500">No revenue data found for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection