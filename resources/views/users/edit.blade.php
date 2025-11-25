@extends('layouts.app')

@section('title', 'Edit User - Fazztrack')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit User</h1>
                    <p class="text-gray-600">Update user information and permissions</p>
                </div>
                <a href="{{ route('users.show', $user) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to User
                </a>
            </div>
        </div>

        <!-- User Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <form method="POST" action="{{ route('users.update', $user) }}" class="p-8">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-user mr-3 text-primary-500"></i>
                        Basic Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-300 @enderror"
                                   placeholder="Enter full name"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                Username <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   value="{{ old('username', $user->username) }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('username') border-red-300 @enderror"
                                   placeholder="Enter username"
                                   required>
                            @error('username')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('email') border-red-300 @enderror"
                                   placeholder="Enter email address"
                                   required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <select id="role" 
                                    name="role" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('role') border-red-300 @enderror"
                                    required>
                                <option value="">Select Role</option>
                                <option value="SuperAdmin" {{ old('role', $user->role) == 'SuperAdmin' ? 'selected' : '' }}>SuperAdmin</option>
                                <option value="Admin" {{ old('role', $user->role) == 'Admin' ? 'selected' : '' }}>Admin</option>
                                <option value="Sales Manager" {{ old('role', $user->role) == 'Sales Manager' ? 'selected' : '' }}>Sales Manager</option>
                                <option value="Designer" {{ old('role', $user->role) == 'Designer' ? 'selected' : '' }}>Designer</option>
                                <option value="Production Staff" {{ old('role', $user->role) == 'Production Staff' ? 'selected' : '' }}>Production Staff</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Production Phase (for Production Staff) -->
                <div class="mb-8" id="phaseSection" style="display: {{ $user->role === 'Production Staff' ? 'block' : 'none' }};">
                    <h3 class="text-lg font-medium text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-cogs mr-3 text-primary-500"></i>
                        Production Phase Assignment
                    </h3>
                    
                    <div>
                        <label for="phase" class="block text-sm font-medium text-gray-700 mb-2">
                            Assigned Phase
                        </label>
                        <select id="phase" 
                                name="phase" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('phase') border-red-300 @enderror">
                            <option value="">Select Phase</option>
                            <option value="PRINT" {{ old('phase', $user->phase) == 'PRINT' ? 'selected' : '' }}>PRINT - T-shirt Printing</option>
                            <option value="PRESS" {{ old('phase', $user->phase) == 'PRESS' ? 'selected' : '' }}>PRESS - Heat Press Application</option>
                            <option value="CUT" {{ old('phase', $user->phase) == 'CUT' ? 'selected' : '' }}>CUT - Cutting and Trimming</option>
                            <option value="SEW" {{ old('phase', $user->phase) == 'SEW' ? 'selected' : '' }}>SEW - Sewing and Finishing</option>
                            <option value="QC" {{ old('phase', $user->phase) == 'QC' ? 'selected' : '' }}>QC - Quality Control (includes Packing)</option>
                        </select>
                        @error('phase')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">Select the production phase this staff member will be responsible for.</p>
                    </div>
                </div>

                <!-- Password Change (Optional) -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-key mr-3 text-primary-500"></i>
                        Password Change (Optional)
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                New Password
                            </label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('password') border-red-300 @enderror"
                                   placeholder="Leave blank to keep current password">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm New Password
                            </label>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="Confirm new password">
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Leave password fields blank if you don't want to change the password.</p>
                </div>

                <!-- Account Status -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-toggle-on mr-3 text-primary-500"></i>
                        Account Status
                    </h3>
                    
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1" 
                               {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Account is active (user can log in)
                        </label>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Uncheck this to temporarily disable the user account.</p>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('users.show', $user) }}" 
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('role').addEventListener('change', function() {
    const phaseSection = document.getElementById('phaseSection');
    const phaseSelect = document.getElementById('phase');
    
    if (this.value === 'Production Staff') {
        phaseSection.style.display = 'block';
        phaseSelect.required = true;
    } else {
        phaseSection.style.display = 'none';
        phaseSelect.required = false;
        phaseSelect.value = '';
    }
});

// Show phase section if role is already selected (for form validation errors)
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    if (roleSelect.value === 'Production Staff') {
        document.getElementById('phaseSection').style.display = 'block';
        document.getElementById('phase').required = true;
    }
});
</script>
@endsection 