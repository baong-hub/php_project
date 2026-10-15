<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Chỉ cho phép người dùng thuộc một trong các nhóm được truyền vào.
     * Cách dùng: ->middleware('role:admin') hoặc ->middleware('role:admin,giang_vien')
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->role, $roles, true)) {
            $message = 'Bạn không có quyền truy cập chức năng này.';

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => $message], 403);
            }

            abort(403, $message);
        }

        return $next($request);
    }
}
