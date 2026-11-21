<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * สั่งไม่ให้เบราว์เซอร์แคชหน้าเว็บ เพื่อให้ทุกครั้งที่เปิดหน้า (รวมถึงกดย้อนกลับ)
 * ได้ฟอร์มที่มี CSRF token ใหม่ ไม่เกิด 419 Page Expired จากหน้าเก่า
 */
class NoCache
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');

        return $response;
    }
}
