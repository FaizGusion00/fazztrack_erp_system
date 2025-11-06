@extends('layouts.app')

@section('title', 'Clients - Fazztrack')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-users mr-3 text-primary-500"></i>
                    Clients
                </h1>
                <p class="mt-2 text-gray-600">Manage your client relationships and contact information.</p>
            </div>
                        <a href="{{ route('clients.create') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-500 border border-transparent rounded-lg font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Add Client
            </a>
        </div>
    </div>

    <!-- Search, Filters & View Toggle -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('clients.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Clients</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search by name, email, or phone..."
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
            <div class="md:w-48">
                <label for="customer_type" class="block text-sm font-medium text-gray-700 mb-2">Customer Type</label>
                <select id="customer_type" name="customer_type" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Types</option>
                    <option value="Individual" {{ request('customer_type') == 'Individual' ? 'selected' : '' }}>Individual</option>
                    <option value="Agent" {{ request('customer_type') == 'Agent' ? 'selected' : '' }}>Agent</option>
                    <option value="Organisation" {{ request('customer_type') == 'Organisation' ? 'selected' : '' }}>Organisation</option>
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 transition-colors">
                    <i class="fas fa-search mr-1"></i>
                    Search
                </button>
                @if(request('search') || request('customer_type'))
                    <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                        <i class="fas fa-times mr-1"></i>
                        Clear
                    </a>
                @endif
            </div>
            <div class="md:ml-auto flex items-end space-x-2">
                @php $view = request('view', 'table'); @endphp
                <a href="{{ route('clients.index', array_merge(request()->except('page'), ['view' => 'table'])) }}"
                   class="px-3 py-2 rounded-md border text-sm font-medium {{ $view === 'table' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                    <i class="fas fa-table mr-1"></i> Table
                </a>
                <a href="{{ route('clients.index', array_merge(request()->except('page'), ['view' => 'cards'])) }}"
                   class="px-3 py-2 rounded-md border text-sm font-medium {{ $view === 'cards' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                    <i class="fas fa-th-large mr-1"></i> Cards
                </a>
            </div>
        </form>
    </div>
    @php($view = request('view', 'table'))
    @if($view === 'table')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($clients as $client)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($client->image)
                                        <img src="@fileUrl($client->image)" alt="{{ $client->name }}" class="w-8 h-8 rounded-full object-cover border mr-3">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 mr-3"><i class="fas fa-user text-xs"></i></div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $client->name }}</div>
                                        @if($client->address)
                                            <div class="text-xs text-gray-500 truncate max-w-xs">{{ $client->address }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $client->phone ?: '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $client->email ?: '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($client->customer_type === 'Individual') bg-blue-100 text-blue-800
                                    @elseif($client->customer_type === 'Agent') bg-green-100 text-green-800
                                    @else bg-purple-100 text-purple-800 @endif">{{ $client->customer_type }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('clients.show', $client) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 mr-2"><i class="fas fa-eye mr-1"></i>View</a>
                                <a href="{{ route('clients.edit', $client) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50"><i class="fas fa-edit mr-1"></i>Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No clients found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($clients as $client)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                @if($client->image)
                                    <img src="@fileUrl($client->image)" alt="{{ $client->name }}" class="w-12 h-12 rounded-full object-cover border-2 border-gray-200">
                                @else
                                    <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-primary-500 text-lg"></i>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $client->name }}</h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($client->customer_type === 'Individual') bg-blue-100 text-blue-800
                                        @elseif($client->customer_type === 'Agent') bg-green-100 text-green-800
                                        @else bg-purple-100 text-purple-800 @endif">{{ $client->customer_type }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex items-center"><i class="fas fa-phone w-4 mr-2"></i><span>{{ $client->phone ?: 'N/A' }}</span></div>
                            <div class="flex items-center"><i class="fas fa-envelope w-4 mr-2"></i><span>{{ $client->email ?: 'N/A' }}</span></div>
                            @if($client->address)
                                <div class="flex items-start"><i class="fas fa-map-marker-alt w-4 mr-2 mt-0.5"></i><span class="truncate">{{ $client->address }}</span></div>
                            @endif
                        </div>
                        <div class="mt-4 flex space-x-2">
                            <a href="{{ route('clients.show', $client) }}" class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-primary-300 text-sm font-medium rounded-md text-primary-700 bg-primary-50 hover:bg-primary-100 transition-colors"><i class="fas fa-eye mr-1"></i>View</a>
                            <a href="{{ route('clients.edit', $client) }}" class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors"><i class="fas fa-edit mr-1"></i>Edit</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-12 text-gray-500">No clients found.</div>
                </div>
            @endforelse
        </div>
    @endif

    <!-- Pagination -->
    @if($clients->hasPages())
        <div class="mt-8">
            {{ $clients->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Search functionality
    document.getElementById('search').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const clientCards = document.querySelectorAll('[data-client-name]');
        
        clientCards.forEach(card => {
            const clientName = card.getAttribute('data-client-name').toLowerCase();
            const clientEmail = card.getAttribute('data-client-email').toLowerCase();
            const clientPhone = card.getAttribute('data-client-phone').toLowerCase();
            
            if (clientName.includes(searchTerm) || clientEmail.includes(searchTerm) || clientPhone.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Filter by customer type
    document.getElementById('customer_type').addEventListener('change', function() {
        const selectedType = this.value;
        const clientCards = document.querySelectorAll('[data-client-type]');
        
        clientCards.forEach(card => {
            const clientType = card.getAttribute('data-client-type');
            
            if (!selectedType || clientType === selectedType) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>
@endpush
@endsection 