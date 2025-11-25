<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $role
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        switch ($role) {
            case 'superadmin':
                if (!$user->isSuperAdmin()) {
                    abort(403, 'Access denied. SuperAdmin role required.');
                }
                break;
            case 'admin':
                if (!$user->isAdmin()) {
                    abort(403, 'Access denied. Admin role required.');
                }
                break;
            case 'sales':
                if (!$user->isSalesManager()) {
                    abort(403, 'Access denied. Sales Manager role required.');
                }
                break;
            case 'designer':
                if (!$user->isDesigner()) {
                    abort(403, 'Access denied. Designer role required.');
                }
                break;
            case 'production':
                if (!$user->isProductionStaff()) {
                    abort(403, 'Access denied. Production Staff role required.');
                }
                break;
        }

        return $next($request);
    }
} 