@extends('layouts.app')

@section('title', 'User Management - Fazztrack')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">User Management</h1>
                    <p class="text-gray-600">Manage system users and their permissions</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('users.export') }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Export Users
                    </a>
                    <a href="{{ route('users.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add New User
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i class="fas fa-user-check text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active Users</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <i class="fas fa-user-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Production Staff</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['production_staff'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <i class="fas fa-user-tie text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Sales Managers</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['sales_manager'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                           placeholder="Name, email, username, role...">
                </div>
                
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select id="role" name="role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">All Roles</option>
                        <option value="SuperAdmin" {{ request('role') == 'SuperAdmin' ? 'selected' : '' }}>SuperAdmin</option>
                        <option value="Admin" {{ request('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                        <option value="Sales Manager" {{ request('role') == 'Sales Manager' ? 'selected' : '' }}>Sales Manager</option>
                        <option value="Designer" {{ request('role') == 'Designer' ? 'selected' : '' }}>Designer</option>
                        <option value="Production Staff" {{ request('role') == 'Production Staff' ? 'selected' : '' }}>Production Staff</option>
                    </select>
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">System Users</h2>
            </div>

            @if($users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Phase
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Last Login
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @php
                                                    $roleIcons = [
                                                        'SuperAdmin' => ['icon' => 'fas fa-crown', 'bg' => 'bg-red-100', 'text' => 'text-red-600'],
                                                        'Admin' => ['icon' => 'fas fa-user-shield', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
                                                        'Sales Manager' => ['icon' => 'fas fa-user-tie', 'bg' => 'bg-purple-100', 'text' => 'text-purple-600'],
                                                        'Designer' => ['icon' => 'fas fa-palette', 'bg' => 'bg-green-100', 'text' => 'text-green-600'],
                                                        'Production Staff' => ['icon' => 'fas fa-cogs', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-600'],
                                                    ];
                                                    $roleConfig = $roleIcons[$user->role] ?? ['icon' => 'fas fa-user', 'bg' => 'bg-gray-100', 'text' => 'text-gray-600'];
                                                @endphp
                                                <div class="h-10 w-10 rounded-full {{ $roleConfig['bg'] }} flex items-center justify-center">
                                                    <i class="{{ $roleConfig['icon'] }} {{ $roleConfig['text'] }}"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $roleColors = [
                                                'SuperAdmin' => 'bg-red-100 text-red-800',
                                                'Admin' => 'bg-blue-100 text-blue-800',
                                                'Sales Manager' => 'bg-purple-100 text-purple-800',
                                                'Designer' => 'bg-green-100 text-green-800',
                                                'Production Staff' => 'bg-yellow-100 text-yellow-800',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $user->phase ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $user->last_login ? $user->last_login->diffForHumans() : 'Never' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('users.show', $user) }}" 
                                               class="text-primary-600 hover:text-primary-900 transition-colors">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('users.edit', $user) }}" 
                                               class="text-blue-600 hover:text-blue-900 transition-colors">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="toggleUserStatus({{ $user->id }}, '{{ $user->is_active ? 'deactivate' : 'activate' }}')" 
                                                    class="text-yellow-600 hover:text-yellow-900 transition-colors">
                                                <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                                            </button>
                                            <button onclick="resetUserPassword({{ $user->id }})" 
                                                    class="text-purple-600 hover:text-purple-900 transition-colors">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            onclick="return confirm('Are you sure you want to delete this user?')"
                                                            class="text-red-600 hover:text-red-900 transition-colors">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $users->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <div class="mx-auto h-12 w-12 text-gray-400">
                        <i class="fas fa-users text-4xl"></i>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new user.</p>
                    <div class="mt-6">
                        <a href="{{ route('users.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Add New User
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Toggle Status Modal -->
<div id="toggleStatusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Toggle User Status</h3>
            <p id="toggleStatusMessage" class="text-sm text-gray-600 mb-4"></p>
            <form id="toggleStatusForm" method="POST">
                @csrf
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeToggleStatusModal()" 
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 transition-colors">
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div id="resetPasswordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reset User Password</h3>
            <form id="resetPasswordForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" id="new_password" name="new_password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="mb-4">
                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeResetPasswordModal()" 
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 transition-colors">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleUserStatus(userId, action) {
    const modal = document.getElementById('toggleStatusModal');
    const form = document.getElementById('toggleStatusForm');
    const message = document.getElementById('toggleStatusMessage');
    
    form.action = `/users/${userId}/toggle-status`;
    message.textContent = `Are you sure you want to ${action} this user?`;
    modal.classList.remove('hidden');
}

function closeToggleStatusModal() {
    const modal = document.getElementById('toggleStatusModal');
    modal.classList.add('hidden');
}

function resetUserPassword(userId) {
    const modal = document.getElementById('resetPasswordModal');
    const form = document.getElementById('resetPasswordForm');
    
    form.action = `/users/${userId}/reset-password`;
    modal.classList.remove('hidden');
}

function closeResetPasswordModal() {
    const modal = document.getElementById('resetPasswordModal');
    modal.classList.add('hidden');
}

// Close modals when clicking outside
document.getElementById('toggleStatusModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeToggleStatusModal();
    }
});

document.getElementById('resetPasswordModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeResetPasswordModal();
    }
});
</script>
@endsection 