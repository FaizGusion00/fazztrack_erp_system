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

    <!-- Search and Filters -->
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
        </form>
    </div>

    <!-- Clients Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($clients as $client)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="p-6">
                    <!-- Client Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-primary-500 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $client->name }}</h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($client->customer_type === 'Individual') bg-blue-100 text-blue-800
                                    @elseif($client->customer_type === 'Agent') bg-green-100 text-green-800
                                    @else bg-purple-100 text-purple-800 @endif">
                                    {{ $client->customer_type }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('clients.show', $client) }}" 
                               class="text-primary-600 hover:text-primary-700 p-1">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('clients.edit', $client) }}" 
                               class="text-gray-600 hover:text-gray-700 p-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('clients.destroy', $client) }}" 
                                  class="inline" onsubmit="return confirm('Are you sure you want to delete this client?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 p-1">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Client Details -->
                    <div class="space-y-3">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-envelope w-4 mr-2"></i>
                            <span class="truncate">{{ $client->email }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-phone w-4 mr-2"></i>
                            <span>{{ $client->phone }}</span>
                        </div>
                        <div class="flex items-start text-sm text-gray-600">
                            <i class="fas fa-map-marker-alt w-4 mr-2 mt-0.5"></i>
                            <span class="line-clamp-2">{{ $client->billing_address }}</span>
                        </div>
                    </div>

                    <!-- Contacts Count -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-address-book mr-1"></i>
                                {{ $client->contacts->count() }} contact{{ $client->contacts->count() !== 1 ? 's' : '' }}
                            </span>
                            <span class="text-sm text-gray-500">
                                {{ $client->orders->count() }} order{{ $client->orders->count() !== 1 ? 's' : '' }}
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 flex space-x-2">
                        <a href="{{ route('clients.show', $client) }}" 
                           class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-primary-300 text-sm font-medium rounded-md text-primary-700 bg-primary-50 hover:bg-primary-100 transition-colors">
                            <i class="fas fa-eye mr-1"></i>
                            View
                        </a>
                        <a href="{{ route('clients.edit', $client) }}" 
                           class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <i class="fas fa-edit mr-1"></i>
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="col-span-full">
                <div class="text-center py-12">
                    <div class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-users text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No clients yet</h3>
                    <p class="text-gray-500 mb-6">Get started by adding your first client to the system.</p>
                    <a href="{{ route('clients.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-primary-500 border border-transparent rounded-lg font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add First Client
                    </a>
                </div>
            </div>
        @endforelse
    </div>

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