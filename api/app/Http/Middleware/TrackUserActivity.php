<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserActivity;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track if user is authenticated
        if (Auth::check()) {
            \Log::info('TrackUserActivity middleware triggered for user: ' . Auth::id());
            $this->recordActivity($request);
        } else {
            \Log::info('TrackUserActivity middleware - user not authenticated');
        }

        return $response;
    }

    /**
     * Record user activity
     */
    private function recordActivity(Request $request)
    {
        try {
            $route = $request->route();
            $action = $route ? $route->getAction() : [];
            $controller = $action['controller'] ?? null;

            // Parse controller and method
            $controllerName = '';
            $methodName = '';
            if ($controller) {
                $parts = explode('@', $controller);
                $controllerName = class_basename($parts[0] ?? '');
                $methodName = $parts[1] ?? '';
            }

            // Determine action type based on HTTP method
            $actionType = $this->getActionType($request->method());

            // Determine status (public or private)
            $status = $this->determineStatus($request->path(), $actionType);

            // Get description
            $description = $this->getDescription($request->path(), $actionType);

            $now = now();
            
            $data = [
                'user_id' => Auth::id(),
                'page' => $this->getPageName($request),
                'action' => $actionType,
                'name' => $methodName,
                'value' => $controllerName,
                'op1' => $description,
                'op2' => $request->path(),
                'op3' => $request->method(),
                'type' => 'record',
                'status' => $status,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            
            \Log::info('Attempting to save user activity', $data);
            
            $activity = UserActivity::create($data);
            
            \Log::info('User activity saved successfully with ID: ' . $activity->id);
        } catch (\Exception $e) {
            // Log error but don't break the request
            \Log::error('Failed to track user activity: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
        }
    }

    /**
     * Get page name from request
     */
    private function getPageName(Request $request): string
    {
        // Try to get the Inertia page name from headers
        $inertiaPage = $request->header('X-Inertia-Page');
        if ($inertiaPage) {
            return $inertiaPage;
        }

        // Fallback to route name or path
        $route = $request->route();
        if ($route && $route->getName()) {
            return $route->getName();
        }

        return $request->path();
    }

    /**
     * Get action type based on HTTP method
     */
    private function getActionType(string $method): string
    {
        return match(strtoupper($method)) {
            'GET' => 'visit',
            'POST' => 'create',
            'PATCH', 'PUT' => 'update',
            'DELETE' => 'delete',
            default => 'action',
        };
    }

    /**
     * Determine if activity should be public or private
     */
    private function determineStatus(string $path, string $action): string
    {
        // Private actions
        $privatePatterns = [
            'login',
            'logout',
            'password',
            'change-password',
            'update-profile',
            'delete',
            'destroy',
        ];

        foreach ($privatePatterns as $pattern) {
            if (str_contains(strtolower($path), $pattern)) {
                return 'private';
            }
        }

        // Update and delete actions are usually private
        if (in_array($action, ['update', 'delete'])) {
            return 'private';
        }

        return 'public';
    }

    /**
     * Get description of the activity
     */
    private function getDescription(string $path, string $action): string
    {
        $pathParts = explode('/', trim($path, '/'));
        $mainPath = $pathParts[0] ?? 'page';

        return match($action) {
            'visit' => "User is visiting the {$mainPath} page",
            'create' => "User is creating a {$mainPath} record",
            'update' => "User is updating a {$mainPath} record",
            'delete' => "User is deleting a {$mainPath} record",
            default => "User is performing an action on {$mainPath}",
        };
    }
}
