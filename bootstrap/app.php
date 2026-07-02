<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
    
    $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
        if ($request->is('api/*')) {
            return true;
        }
        return $request->expectsJson();
    });

    $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, Request $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'status'  => false,
                'message' => 'عذراً، لا تمتلك الصلاحيات الكافية للوصول إلى هذا الجزء من النظام.',
                'errors'  => null
            ], 403);
        }
    });

    $exceptions->render(function (\Exception $e, Request $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(), 
                'errors'  => null
            ], 422);
        }
    });

    $exceptions->render(function (Throwable $e, Request $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'status'  => false,
                'message' => 'عذراً، حدث خطأ داخلي غير متوقع في السيرفر.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], 500);
        }
    });
        
    })->create();