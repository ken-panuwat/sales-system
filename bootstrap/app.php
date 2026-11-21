<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // ไม่ให้เบราว์เซอร์เก็บหน้าเว็บไว้ (กัน 419 จากการกดปุ่มย้อนกลับแล้วส่งฟอร์มด้วย token เก่า)
        $middleware->appendToGroup('web', \App\Http\Middleware\NoCache::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 419 Page Expired (CSRF token หมดอายุ / เซสชันหมดเวลา) -> แจ้งด้วยป๊อบอัพแทนหน้า error
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            // ออกจากระบบตอนเซสชันหมดอายุแล้ว ถือว่าออกจากระบบเรียบร้อย
            if ($request->routeIs('logout')) {
                return redirect()->route('login');
            }

            $message = 'หน้าเว็บหมดอายุ (เซสชันหมดเวลา) กรุณาลองใหม่อีกครั้ง';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 419);
            }

            if (! $request->user()) {
                return redirect()->route('login')->with('error', $message);
            }

            return redirect()->back()->with('error', $message);
        });
    })->create();
