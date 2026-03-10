<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsDriver
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, ['driver', 'admin'])) {
            abort(403, 'Доступ запрещён. Требуется роль водителя.');
        }

        return $next($request);
    }
}
