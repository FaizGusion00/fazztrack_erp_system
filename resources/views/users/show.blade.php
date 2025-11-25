@extends('layouts.app')

@section('title', 'User Details - Fazztrack')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">User Details</h1>
                    <p class="text-gray-600">View detailed information about {{ $user->name }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('users.edit', $user) }}" 
                       class="inline-flex items-center px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit User
                    </a>
                    <a href="{{ route('users.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Users
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- User Information -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <i class="fas fa-user mr-3 text-primary-500"></i>
                            User Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <!-- User Avatar -->
                        <div class="flex items-center mb-8">
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
                            <div class="w-20 h-20 rounded-full {{ $roleConfig['bg'] }} flex items-center justify-center mr-6 shadow-lg">
                                <i class="{{ $roleConfig['icon'] }} {{ $roleConfig['text'] }} text-3xl"></i>
                            </div>
                            <div class="flex-1">
                                <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $user->name }}</h2>
                                <p class="text-lg text-gray-600 font-medium">{{ $user->role }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Full Name</h4>
                                <p class="text-lg font-semibold text-gray-900">{{ $user->name }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Username</h4>
                                <p class="text-lg font-semibold text-gray-900">{{ $user->username }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Email Address</h4>
                                <p class="text-lg text-gray-900">{{ $user->email }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Role</h4>
                                @php
                                    $roleColors = [
                                        'SuperAdmin' => 'bg-red-100 text-red-800',
                                        'Admin' => 'bg-blue-100 text-blue-800',
                                        'Sales Manager' => 'bg-purple-100 text-purple-800',
                                        'Designer' => 'bg-green-100 text-green-800',
                                        'Production Staff' => 'bg-yellow-100 text-yellow-800',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $user->role }}
                                </span>
                            </div>
                            @if($user->phase)
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Assigned Phase</h4>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $user->phase }}
                                </span>
                            </div>
                            @endif
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Account Status</h4>
                                @if($user->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-ban mr-1"></i>
                                        Inactive
                                    </span>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Member Since</h4>
                                <p class="text-lg text-gray-900">{{ $user->created_at->format('F j, Y') }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Last Login</h4>
                                <p class="text-lg text-gray-900">{{ $user->last_login ? $user->last_login->format('F j, Y g:i A') : 'Never' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assigned Jobs -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <i class="fas fa-tasks mr-3 text-primary-500"></i>
                            Assigned Jobs
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($user->assignedJobs->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Job ID
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Order
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Phase
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Duration
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($user->assignedJobs->take(10) as $job)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    #{{ $job->id }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $job->order->job_name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $job->order->client->name }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $job->phase }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @php
                                                        $statusColors = [
                                                            'Pending' => 'bg-yellow-100 text-yellow-800',
                                                            'In Progress' => 'bg-blue-100 text-blue-800',
                                                            'Completed' => 'bg-green-100 text-green-800',
                                                        ];
                                                    @endphp
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$job->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                        {{ $job->status }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    @if($job->duration)
                                                        {{ $job->duration }} minutes
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($user->assignedJobs->count() > 10)
                                <div class="mt-4 text-center">
                                    <p class="text-sm text-gray-500">Showing 10 of {{ $user->assignedJobs->count() }} assigned jobs</p>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8">
                                <div class="mx-auto h-12 w-12 text-gray-400">
                                    <i class="fas fa-tasks text-4xl"></i>
                                </div>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No assigned jobs</h3>
                                <p class="mt-1 text-sm text-gray-500">This user hasn't been assigned any production jobs yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <i class="fas fa-bolt mr-3 text-primary-500"></i>
                            Quick Actions
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @if($user->id !== auth()->id())
                            <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" 
                                        class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                    <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }} mr-2"></i>
                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }} User
                                </button>
                            </form>
                        @endif
                        
                        <button onclick="resetUserPassword({{ $user->id }})" 
                                class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                            <i class="fas fa-key mr-2"></i>
                            Reset Password
                        </button>
                        
                        @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')"
                                        class="w-full flex items-center justify-center px-4 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                    <i class="fas fa-trash mr-2"></i>
                                    Delete User
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- User Statistics -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <i class="fas fa-chart-bar mr-3 text-primary-500"></i>
                            User Statistics
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Total Jobs Assigned</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $user->assignedJobs->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Completed Jobs</span>
                            <span class="text-lg font-semibold text-green-600">{{ $user->assignedJobs->where('status', 'Completed')->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Pending Jobs</span>
                            <span class="text-lg font-semibold text-yellow-600">{{ $user->assignedJobs->where('status', 'Pending')->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">In Progress Jobs</span>
                            <span class="text-lg font-semibold text-blue-600">{{ $user->assignedJobs->where('status', 'In Progress')->count() }}</span>
                        </div>
                        @if($user->assignedJobs->where('duration')->count() > 0)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Average Duration</span>
                            <span class="text-lg font-semibold text-gray-900">
                                {{ round($user->assignedJobs->where('duration')->avg('duration')) }} min
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
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

// Close modal when clicking outside
document.getElementById('resetPasswordModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeResetPasswordModal();
    }
});
</script>
@endsection 