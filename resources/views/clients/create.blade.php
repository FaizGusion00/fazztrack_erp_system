@extends('layouts.app')

@section('title', 'Add Client - Fazztrack')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-user-plus mr-3 text-primary-500"></i>
                    Add New Client
                </h1>
                <p class="mt-2 text-gray-600">Create a new client profile with contact information.</p>
            </div>
                        <a href="{{ route('clients.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Clients
            </a>
        </div>
    </div>

    <!-- Client Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form method="POST" action="{{ route('clients.store') }}" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <!-- Basic Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-primary-500"></i>
                    Basic Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Client Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-300 @enderror"
                               placeholder="Enter client name"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="customer_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Customer Type <span class="text-red-500">*</span>
                        </label>
                        <select id="customer_type" 
                                name="customer_type"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('customer_type') border-red-300 @enderror"
                                required>
                            <option value="">Select customer type</option>
                            <option value="Individual" {{ old('customer_type') === 'Individual' ? 'selected' : '' }}>Individual</option>
                            <option value="Agent" {{ old('customer_type') === 'Agent' ? 'selected' : '' }}>Agent</option>
                            <option value="Organisation" {{ old('customer_type') === 'Organisation' ? 'selected' : '' }}>Organisation</option>
                        </select>
                        @error('customer_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Profile Image -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-camera mr-2 text-primary-500"></i>
                    Profile Image
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                            Client Photo
                        </label>
                        <input type="file" 
                               id="image" 
                               name="image" 
                               accept="image/*"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('image') border-red-300 @enderror"
                               onchange="previewImage(this)">
                        <p class="mt-1 text-sm text-gray-500">Upload a profile photo (JPEG, PNG, JPG, GIF - Max 2MB)</p>
                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div id="image-preview" class="hidden">
                        <img id="preview" src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-address-book mr-2 text-primary-500"></i>
                    Contact Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('email') border-red-300 @enderror"
                               placeholder="client@example.com"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('phone') border-red-300 @enderror"
                               placeholder="+60123456789"
                               required>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt mr-2 text-primary-500"></i>
                    Address Information
                </h3>
                
                <div class="space-y-6">
                    <div>
                        <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-2">
                            Billing Address <span class="text-red-500">*</span>
                        </label>
                        <textarea id="billing_address" 
                                  name="billing_address" 
                                  rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('billing_address') border-red-300 @enderror"
                                  placeholder="Enter complete billing address"
                                  required>{{ old('billing_address') }}</textarea>
                        @error('billing_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-2">
                            Shipping Address
                        </label>
                        <textarea id="shipping_address" 
                                  name="shipping_address" 
                                  rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('shipping_address') border-red-300 @enderror"
                                  placeholder="Enter shipping address (optional)">{{ old('shipping_address') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Leave blank if same as billing address</p>
                        @error('shipping_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Additional Contacts -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-users mr-2 text-primary-500"></i>
                    Additional Contacts
                </h3>
                
                <div id="contacts-container">
                    <div class="contact-item bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Name</label>
                                <input type="text" 
                                       name="contacts[0][contact_name]"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="Contact person name">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                                <input type="tel" 
                                       name="contacts[0][contact_phone]"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="Contact phone number">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label>
                                <input type="email" 
                                       name="contacts[0][contact_email]"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="contact@example.com">
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="button" 
                        id="add-contact"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Add Another Contact
                </button>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('clients.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2 bg-primary-500 border border-transparent rounded-md font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Create Client
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let contactIndex = 1;
    
    document.getElementById('add-contact').addEventListener('click', function() {
        const container = document.getElementById('contacts-container');
        const newContact = document.createElement('div');
        newContact.className = 'contact-item bg-gray-50 rounded-lg p-4 mb-4';
        newContact.innerHTML = `
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-medium text-gray-700">Additional Contact</h4>
                <button type="button" class="remove-contact text-red-600 hover:text-red-700">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Name</label>
                    <input type="text" 
                           name="contacts[${contactIndex}][contact_name]"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Contact person name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                    <input type="tel" 
                           name="contacts[${contactIndex}][contact_phone]"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Contact phone number">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label>
                    <input type="email" 
                           name="contacts[${contactIndex}][contact_email]"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="contact@example.com">
                </div>
            </div>
        `;
        
        container.appendChild(newContact);
        contactIndex++;
        
        // Add remove functionality
        newContact.querySelector('.remove-contact').addEventListener('click', function() {
            newContact.remove();
        });
    });

    function previewImage(input) {
        const preview = document.getElementById('preview');
        const imagePreview = document.getElementById('image-preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                imagePreview.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = "";
            imagePreview.classList.add('hidden');
        }
    }
</script>
@endpush
@endsection 