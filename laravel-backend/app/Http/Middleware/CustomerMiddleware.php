<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class CustomerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if (!$user || !($user instanceof User)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Customer authentication required.'
            ], 403);
        }

        return $next($request);
    }
}

