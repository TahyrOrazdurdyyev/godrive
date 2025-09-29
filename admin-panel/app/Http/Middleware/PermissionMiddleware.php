<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use Auth;
use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission = null, $routes = null)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Получаем названия разрешений пользователя
        $user_permissions = Permission::where('role_id', $user->role_id)->pluck('name')->toArray();
        
        // Если у пользователя есть all-access или нужное разрешение - пропускаем
        if (in_array('all-access', $user_permissions) || in_array($permission, $user_permissions)) {
            return $next($request);
        }

        abort(403, 'unauthorized access');
    }
}