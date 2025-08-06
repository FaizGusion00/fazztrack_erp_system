@extends('layouts.app')

@section('title', $client->name . ' - Fazztrack')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-user mr-3 text-primary-500"></i>
                    {{ $client->name }}
                </h1>
                <p class="mt-2 text-gray-600">Client details and order history</p>
            </div>
            <div class="flex items-center space-x-3">
                            <a href="{{ route('clients.edit', $client) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                <i class="fas fa-edit mr-2"></i>
                Edit Client
            </a>
            <a href="{{ route('clients.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Clients
            </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Client Information -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <!-- Client Header -->
                    <div class="flex items-center space-x-4 mb-6">
                        @if($client->image)
                            <img src="{{ asset('storage/' . $client->image) }}" alt="{{ $client->name }}" class="w-16 h-16 rounded-full object-cover border-2 border-gray-200">
                        @else
                            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-primary-500 text-2xl"></i>
                            </div>
                        @endif
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">{{ $client->name }}</h2>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($client->customer_type === 'Individual') bg-blue-100 text-blue-800
                                @elseif($client->customer_type === 'Agent') bg-green-100 text-green-800
                                @else bg-purple-100 text-purple-800 @endif">
                                {{ $client->customer_type }}
                            </span>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="space-y-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-envelope w-4 mr-3 text-gray-400"></i>
                            <span>{{ $client->email }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-phone w-4 mr-3 text-gray-400"></i>
                            <span>{{ $client->phone }}</span>
                        </div>
                        <div class="flex items-start text-sm text-gray-600">
                            <i class="fas fa-map-marker-alt w-4 mr-3 mt-0.5 text-gray-400"></i>
                            <div>
                                <p class="font-medium text-gray-700">Billing Address:</p>
                                <p class="mt-1">{{ $client->billing_address }}</p>
                            </div>
                        </div>
                        @if($client->shipping_address)
                            <div class="flex items-start text-sm text-gray-600">
                                <i class="fas fa-shipping-fast w-4 mr-3 mt-0.5 text-gray-400"></i>
                                <div>
                                    <p class="font-medium text-gray-700">Shipping Address:</p>
                                    <p class="mt-1">{{ $client->shipping_address }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Statistics -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary-600">{{ $client->orders->count() }}</div>
                                <div class="text-sm text-gray-500">Total Orders</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $client->contacts->count() }}</div>
                                <div class="text-sm text-gray-500">Contacts</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contacts -->
            @if($client->contacts->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-address-book mr-2 text-primary-500"></i>
                            Contacts ({{ $client->contacts->count() }})
                        </h3>
                        
                        <div class="space-y-4">
                            @foreach($client->contacts as $contact)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-medium text-gray-900">{{ $contact->contact_name }}</h4>
                                        <form method="POST" action="{{ route('clients.contacts.remove', ['client' => $client, 'contact' => $contact]) }}" 
                                              class="inline" onsubmit="return confirm('Are you sure you want to remove this contact?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <div class="space-y-1 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-phone w-4 mr-2"></i>
                                            <span>{{ $contact->contact_phone }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-envelope w-4 mr-2"></i>
                                            <span>{{ $contact->contact_email }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Add Contact Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-user-plus mr-2 text-primary-500"></i>
                        Add Contact
                    </h3>
                    
                    <form method="POST" action="{{ route('clients.contacts.add', $client) }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="contact_name" class="block text-sm font-medium text-gray-700 mb-1">Contact Name</label>
                                <input type="text" 
                                       id="contact_name" 
                                       name="contact_name" 
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="Contact person name"
                                       required>
                            </div>
                            <div>
                                <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                                <input type="tel" 
                                       id="contact_phone" 
                                       name="contact_phone" 
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="Contact phone number"
                                       required>
                            </div>
                            <div>
                                <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                                <input type="email" 
                                       id="contact_email" 
                                       name="contact_email" 
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="contact@example.com"
                                       required>
                            </div>
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-primary-500 border border-transparent rounded-md font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Add Contact
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Orders -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <i class="fas fa-shopping-cart mr-2 text-primary-500"></i>
                            Order History
                        </h3>
                        <a href="{{ route('orders.create') }}?client_id={{ $client->client_id }}" 
                           class="inline-flex items-center px-4 py-2 bg-primary-500 border border-transparent rounded-md font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            New Order
                        </a>
                    </div>

                    @if($client->orders->count() > 0)
                        <div class="space-y-4">
                            @foreach($client->orders as $order)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-shopping-cart text-primary-500"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $order->job_name }}</h4>
                                                <p class="text-sm text-gray-500">Order #{{ $order->order_id }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            @php
                                                $statusColors = [
                                                    'Pending' => 'bg-yellow-100 text-yellow-800',
                                                    'Approved' => 'bg-blue-100 text-blue-800',
                                                    'On Hold' => 'bg-red-100 text-red-800',
                                                    'In Progress' => 'bg-primary-100 text-primary-800',
                                                    'Completed' => 'bg-green-100 text-green-800'
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $order->status }}
                                            </span>
                                            <a href="{{ route('orders.show', $order) }}" 
                                               class="text-primary-600 hover:text-primary-700">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600">
                                        <div>
                                            <span class="font-medium">Due Date:</span>
                                            <span>{{ $order->due_date_production->format('M d, Y') }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium">Delivery:</span>
                                            <span>{{ $order->delivery_method }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium">Total:</span>
                                            <span>RM {{ number_format($order->design_deposit + $order->production_deposit + $order->balance_payment, 2) }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium">Jobs:</span>
                                            <span>{{ $order->jobs->count() }} phase{{ $order->jobs->count() !== 1 ? 's' : '' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="mx-auto h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-shopping-cart text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No orders yet</h3>
                            <p class="text-gray-500 mb-4">This client hasn't placed any orders yet.</p>
                            <a href="{{ route('orders.create') }}?client_id={{ $client->client_id }}" 
                               class="inline-flex items-center px-4 py-2 bg-primary-500 border border-transparent rounded-md font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Create First Order
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 