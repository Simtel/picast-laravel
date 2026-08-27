<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

final class Authenticate extends Middleware
{
    /**
     * The guard names used by the middleware.
     *
     * @var array<int|string, string>
     */
    private array $guards = [];

    /**
     * Handle an incoming request.
     *
     * @param  string  ...$guards
     */
    public function handle($request, Closure $next, ...$guards): mixed
    {
        $this->guards = $guards;

        return parent::handle($request, $next, ...$guards);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  Request  $request
     */
    protected function redirectTo($request): ?string
    {
        if ($request->expectsJson() || $this->usesApiGuard()) {
            return null;
        }

        return route('login');
    }

    /**
     * Determine whether the request uses the API (token/sanctum) guard.
     */
    private function usesApiGuard(): bool
    {
        return in_array('api', $this->guards, true);
    }
}
