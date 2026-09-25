<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  The required minimum role (admin, manager, staff, customer)
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Define role hierarchy: customer < staff < manager < admin
        $roleHierarchy = [
            'customer' => 1,
            'staff' => 2,
            'manager' => 3,
            'admin' => 4,
        ];

        if (!isset($roleHierarchy[$role])) {
            abort(500, "Invalid role requirement specified: {$role}");
        }

        // Get the user's role in the organization. 
        // In a single-org model, we'll look at their first/active organization.
        $userOrgRole = $user->organizations()->first()?->pivot?->role ?? 'customer';

        $requiredWeight = $roleHierarchy[$role];
        $userWeight = $roleHierarchy[$userOrgRole] ?? 0;

        if ($userWeight >= $requiredWeight) {
            return $next($request);
        }

        abort(403, 'Unauthorized. You do not have the required permissions.');
    }
}
