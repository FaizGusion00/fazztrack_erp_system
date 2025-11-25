<?php

namespace App\Http\Controllers;

use App\Models\User;

use App\Models\Job;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login request with enhanced security
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        // Rate limiting for login attempts
        $this->checkLoginAttempts($request);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            $this->incrementLoginAttempts($request);
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if user is active
        if (!$user->isActive()) {
            throw ValidationException::withMessages([
                'username' => ['Your account has been deactivated. Please contact administrator.'],
            ]);
        }

        // Clear login attempts on successful login
        $this->clearLoginAttempts($request);

        // Login with remember me functionality
        $remember = $request->boolean('remember');
        Auth::login($user, $remember);

        // Log successful login
        Log::info('User logged in successfully', [
            'user_id' => $user->id,
            'username' => $user->username,
            'role' => $user->role,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Redirect based on role
        switch ($user->role) {
            case 'SuperAdmin':
                return redirect()->route('dashboard');
            case 'Admin':
                return redirect()->route('admin.admin-dashboard');
            case 'Sales Manager':
                return redirect()->route('sales.dashboard');
            case 'Designer':
                return redirect()->route('designer.dashboard');
            case 'Production Staff':
                return redirect()->route('production.dashboard');
            default:
                return redirect()->route('dashboard');
        }
    }

    /**
     * Check login attempts and apply rate limiting
     */
    private function checkLoginAttempts(Request $request)
    {
        $key = 'login_attempts_' . $request->ip();
        $attempts = cache()->get($key, 0);

        if ($attempts >= 5) {
            $lockoutTime = cache()->get($key . '_lockout', 0);
            if (time() < $lockoutTime) {
                $remainingTime = $lockoutTime - time();
                throw ValidationException::withMessages([
                    'username' => ["Too many login attempts. Please try again in {$remainingTime} seconds."],
                ]);
            } else {
                cache()->forget($key);
                cache()->forget($key . '_lockout');
            }
        }
    }

    /**
     * Increment login attempts
     */
    private function incrementLoginAttempts(Request $request)
    {
        $key = 'login_attempts_' . $request->ip();
        $attempts = cache()->get($key, 0) + 1;
        
        cache()->put($key, $attempts, 300); // 5 minutes

        if ($attempts >= 5) {
            cache()->put($key . '_lockout', time() + 900, 900); // 15 minutes lockout
        }
    }

    /**
     * Clear login attempts
     */
    private function clearLoginAttempts(Request $request)
    {
        $key = 'login_attempts_' . $request->ip();
        cache()->forget($key);
        cache()->forget($key . '_lockout');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Show dashboard based on user role
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Cache statistics for 10 minutes to improve performance
        $cacheKey = 'dashboard_stats_superadmin';
        $stats = Cache::remember($cacheKey, 600, function () {
            return [
                'total_orders' => \App\Models\Order::count(),
                'pending_orders' => \App\Models\Order::where('status', 'Order Created')->count(),
                'in_progress_orders' => \App\Models\Order::whereIn('status', ['Job Start', 'Job Complete', 'Order Packaging'])->count(),
                'completed_orders' => \App\Models\Order::where('status', 'Order Finished')->count(),
                'total_clients' => \App\Models\Client::count(),
                'total_jobs' => Job::count(),
                'total_revenue' => \App\Models\Order::sum('total_amount'),
                'monthly_revenue' => \App\Models\Order::whereMonth('created_at', now()->month)->sum('total_amount'),
                'average_order_value' => \App\Models\Order::avg('total_amount') ?? 0,
            ];
        });

        // Cache revenue data for 15 minutes (less frequently changing)
        $revenueCacheKey = 'dashboard_revenue_data';
        $revenueData = Cache::remember($revenueCacheKey, 900, function () {
            $data = [];
            for ($i = 11; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $revenue = \App\Models\Order::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('total_amount');
                $data[] = [
                    'month' => $month->format('M Y'),
                    'revenue' => $revenue
                ];
            }
            return $data;
        });

        // Recent orders - cache for 2 minutes (more dynamic)
        $recentOrdersCacheKey = 'dashboard_recent_orders';
        $recent_orders = Cache::remember($recentOrdersCacheKey, 120, function () {
            return \App\Models\Order::with('client')
                ->latest()
                ->take(5)
                ->get();
        });

        // Top clients - cache for 10 minutes
        $topClientsCacheKey = 'dashboard_top_clients';
        $top_clients = Cache::remember($topClientsCacheKey, 600, function () {
            return \App\Models\Client::withSum('orders', 'total_amount')
                ->orderBy('orders_sum_total_amount', 'desc')
                ->take(5)
                ->get();
        });

        return view('admin.dashboard', compact('stats', 'recent_orders', 'revenueData', 'top_clients'));
    }

    public function adminDashboard()
    {
        $user = Auth::user();
        
        // Cache statistics for 5 minutes (Admin dashboard needs more real-time data)
        $cacheKey = 'dashboard_stats_admin';
        $stats = Cache::remember($cacheKey, 300, function () {
            return [
                'pending_approvals' => \App\Models\Order::where('status', 'Order Created')->count(),
                'design_reviews' => \App\Models\Order::where('status', 'Design Review')->count(),
                'approved_orders' => \App\Models\Order::where('status', 'Order Approved')->count(),
                'total_revenue' => \App\Models\Order::sum('total_amount'),
                'monthly_revenue' => \App\Models\Order::whereMonth('created_at', now()->month)->sum('total_amount'),
            ];
        });

        // Orders pending approval - cache for 1 minute (very dynamic)
        $pendingOrdersCacheKey = 'dashboard_pending_orders';
        $pending_orders = Cache::remember($pendingOrdersCacheKey, 60, function () {
            return \App\Models\Order::with('client')
                ->where('status', 'Order Created')
                ->latest()
                ->take(10)
                ->get();
        });

        // Designs pending review - cache for 1 minute (very dynamic)
        $pendingDesignsCacheKey = 'dashboard_pending_designs';
        $pending_designs = Cache::remember($pendingDesignsCacheKey, 60, function () {
            return \App\Models\Design::with(['order.client', 'designer'])
                ->where('status', 'Pending Review')
                ->latest()
                ->take(10)
                ->get();
        });

        return view('admin.admin-dashboard', compact('stats', 'pending_orders', 'pending_designs'));
    }

    /**
     * Show Sales Manager dashboard
     */
    public function salesDashboard()
    {
        $user = Auth::user();
        
        // Cache statistics for 5 minutes
        $cacheKey = 'dashboard_stats_sales';
        $stats = Cache::remember($cacheKey, 300, function () {
            return [
                'total_orders' => \App\Models\Order::count(),
                'pending_orders' => \App\Models\Order::where('status', 'Order Created')->count(),
                'approved_orders' => \App\Models\Order::where('status', 'Order Approved')->count(),
                'design_approved_orders' => \App\Models\Order::where('status', 'Design Approved')->count(),
                'total_revenue' => \App\Models\Order::sum('total_amount'),
                'monthly_revenue' => \App\Models\Order::whereMonth('created_at', now()->month)->sum('total_amount'),
                'average_order_value' => \App\Models\Order::avg('total_amount') ?? 0,
            ];
        });

        // Cache revenue data for 15 minutes
        $revenueCacheKey = 'dashboard_revenue_data_sales';
        $revenueData = Cache::remember($revenueCacheKey, 900, function () {
            $data = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $revenue = \App\Models\Order::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('total_amount');
                $data[] = [
                    'month' => $month->format('M Y'),
                    'revenue' => $revenue
                ];
            }
            return $data;
        });

        // Recent orders - cache for 2 minutes
        $recentOrdersCacheKey = 'dashboard_recent_orders_sales';
        $recent_orders = Cache::remember($recentOrdersCacheKey, 120, function () {
            return \App\Models\Order::with('client')
                ->latest()
                ->take(5)
                ->get();
        });

        // Top clients - cache for 10 minutes
        $topClientsCacheKey = 'dashboard_top_clients_sales';
        $top_clients = Cache::remember($topClientsCacheKey, 600, function () {
            return \App\Models\Client::withSum('orders', 'total_amount')
                ->orderBy('orders_sum_total_amount', 'desc')
                ->take(5)
                ->get();
        });

        return view('sales.dashboard', compact('stats', 'recent_orders', 'revenueData', 'top_clients'));
    }

    /**
     * Show Designer dashboard
     */
    public function designerDashboard()
    {
        return view('designer.dashboard');
    }

    /**
     * Show Production Staff dashboard
     */
    public function productionDashboard()
    {
        // Get all current jobs (not completed) from all phases for progress viewing
        $currentJobs = Job::where('status', '!=', 'Completed')
            ->with(['order.client', 'assignedUser'])
            ->excludeOnHoldOrders()
            ->orderBy('created_at', 'desc')
            ->get();

        // Group jobs by phase for better organization
        $jobsByPhase = $currentJobs->groupBy('phase');

        // Get recent completed jobs (last 7 days) for activity tracking
        $recentCompletedJobs = Job::where('status', 'Completed')
            ->excludeOnHoldOrders()
            ->where('end_time', '>=', now()->subDays(7))
            ->with(['order.client', 'assignedUser'])
            ->orderBy('end_time', 'desc')
            ->take(20)
            ->get();

        return view('production.dashboard', compact('currentJobs', 'jobsByPhase', 'recentCompletedJobs'));
    }
} 