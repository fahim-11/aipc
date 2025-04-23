protected $routeMiddleware = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
    'can' => \Illuminate\Auth\Middleware\Authorize::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'throttle:api' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    'role' => \App\Http\Middleware\CheckRole::class, // Add this line
];