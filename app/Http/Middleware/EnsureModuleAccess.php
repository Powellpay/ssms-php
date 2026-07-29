<?php

namespace App\Http\Middleware;

use App\Domain\Auth\Services\ModuleAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleAccess
{
    public function handle(Request $request, Closure $next, string ...$modules): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $service = app(ModuleAccessService::class);

        foreach ($modules as $module) {
            if ($service->canAccess($user, $module)) {
                return $next($request);
            }
        }

        return response()->json([
            'success' => false,
            'code' => 'MODULE_ACCESS_DENIED',
            'message' => 'You do not have access to this module.',
        ], 403);
    }
}
