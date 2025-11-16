<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // Only SuperAdmin can access user management
        if (!$user->isSuperAdmin()) {
            abort(403, 'Access denied. Only SuperAdmin can manage users.');
        }

        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status (active/inactive)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Sorting functionality
        $sort = $request->get('sort', 'latest_added');
        switch ($sort) {
            case 'latest_added':
                $query->orderBy('created_at', 'desc');
                break;
            case 'latest_updated':
                $query->orderBy('updated_at', 'desc');
                break;
            case 'alphabetical':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $users = $query->paginate(15)->withQueryString();

        // Get user statistics
        $stats = [
            'total' => User::count(),
            'superadmin' => User::where('role', 'SuperAdmin')->count(),
            'admin' => User::where('role', 'Admin')->count(),
            'sales_manager' => User::where('role', 'Sales Manager')->count(),
            'designer' => User::where('role', 'Designer')->count(),
            'production_staff' => User::where('role', 'Production Staff')->count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];

        return view('users.index', compact('users', 'stats'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        // Only SuperAdmin can create users
        if (!$authUser->isSuperAdmin()) {
            abort(403, 'Access denied. Only SuperAdmin can create users.');
        }

        return view('users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        // Only SuperAdmin can create users
        if (!$authUser->isSuperAdmin()) {
            abort(403, 'Access denied. Only SuperAdmin can create users.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|in:SuperAdmin,Admin,Sales Manager,Designer,Production Staff',
            'phase' => 'nullable|in:PRINT,PRESS,CUT,SEW,QC',
            'is_active' => 'boolean',
        ]);

        // Set phase based on role
        $phase = null;
        if ($request->role === 'Production Staff') {
            $phase = $request->phase;
        }

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phase' => $phase,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        // Only SuperAdmin can view user details
        if (!$authUser->isSuperAdmin()) {
            abort(403, 'Access denied. Only SuperAdmin can view user details.');
        }

        // Load user relationships
        $user->load(['assignedJobs.order', 'assignedJobs.order.client']);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        // Only SuperAdmin can edit users
        if (!$authUser->isSuperAdmin()) {
            abort(403, 'Access denied. Only SuperAdmin can edit users.');
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        // Only SuperAdmin can update users
        if (!$authUser->isSuperAdmin()) {
            abort(403, 'Access denied. Only SuperAdmin can update users.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => 'required|in:SuperAdmin,Admin,Sales Manager,Designer,Production Staff',
            'phase' => 'nullable|in:PRINT,PRESS,CUT,SEW,QC',
            'is_active' => 'boolean',
        ]);

        $userData = $request->only(['name', 'username', 'email', 'role', 'is_active']);

        // Set phase based on role
        if ($request->role === 'Production Staff') {
            $userData['phase'] = $request->phase;
        } else {
            $userData['phase'] = null;
        }

        // Update password only if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        // Only SuperAdmin can delete users
        if (!$authUser->isSuperAdmin()) {
            abort(403, 'Access denied. Only SuperAdmin can delete users.');
        }

        // Prevent deleting own account
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Check if user has assigned jobs
        if ($user->assignedJobs()->count() > 0) {
            return redirect()->route('users.index')
                ->with('error', 'Cannot delete user with assigned jobs. Please reassign jobs first.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        // Only SuperAdmin can toggle user status
        if (!$authUser->isSuperAdmin()) {
            abort(403, 'Access denied. Only SuperAdmin can toggle user status.');
        }

        // Prevent deactivating own account
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->route('users.index')
            ->with('success', "User {$status} successfully.");
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, User $user)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        // Only SuperAdmin can reset passwords
        if (!$authUser->isSuperAdmin()) {
            abort(403, 'Access denied. Only SuperAdmin can reset passwords.');
        }

        $request->validate([
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User password reset successfully.');
    }

    /**
     * Get user statistics for dashboard
     */
    public function getStats()
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        // Only SuperAdmin can view user statistics
        if (!$authUser->isSuperAdmin()) {
            abort(403, 'Access denied. Only SuperAdmin can view user statistics.');
        }

        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'roles' => [
                'SuperAdmin' => User::where('role', 'SuperAdmin')->count(),
                'Admin' => User::where('role', 'Admin')->count(),
                'Sales Manager' => User::where('role', 'Sales Manager')->count(),
                'Designer' => User::where('role', 'Designer')->count(),
                'Production Staff' => User::where('role', 'Production Staff')->count(),
            ],
            'recent_users' => User::orderBy('created_at', 'desc')->take(5)->get(),
            'users_by_month' => User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Export users data
     */
    public function export(Request $request)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        // Only SuperAdmin can export user data
        if (!$authUser->isSuperAdmin()) {
            abort(403, 'Access denied. Only SuperAdmin can export user data.');
        }

        $users = User::all();

        $filename = 'users_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, ['ID', 'Name', 'Username', 'Email', 'Role', 'Phase', 'Status', 'Created At', 'Last Login']);
            
            // CSV data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->username,
                    $user->email,
                    $user->role,
                    $user->phase ?? 'N/A',
                    $user->is_active ? 'Active' : 'Inactive',
                    $user->created_at->format('Y-m-d H:i:s'),
                    $user->last_login ? $user->last_login->format('Y-m-d H:i:s') : 'Never',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 