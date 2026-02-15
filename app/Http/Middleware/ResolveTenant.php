<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $resolved = $request->route('tenant');

        if ($resolved instanceof Tenant) {
            $tenant = $resolved;
        } elseif (is_string($resolved) && $resolved !== '') {
            $tenant = Tenant::where('slug', $resolved)->first();
        } else {
            abort(404);
        }

        if (! $tenant) {
            abort(404);
        }

        $user = $request->user();

        if (! $user) {
            abort(404);
        }

        if (! $user->is_super_admin) {
            $hasAccess = $tenant->users()
                ->where('users.id', $user->id)
                ->wherePivot('status', 'active')
                ->exists();

            if (! $hasAccess) {
                abort(404);
            }
        }

        app()->instance('tenant', $tenant);
        $request->attributes->set('tenant', $tenant);

        return $next($request);
    }
}
