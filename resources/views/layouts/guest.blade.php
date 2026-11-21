<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="auth-body">
<div class="auth-wrap">
    <section class="auth-hero">
        <div>
            <span class="brand-icon big"><i class="bi bi-shop"></i></span>
            <h1>{{ config('app.name') }}</h1>
            <p class="fs-5 mb-0">ระบบขายสินค้าออนไลน์ ใช้งานง่าย ครบในที่เดียว</p>
            <ul class="hero-points">
                <li><i class="bi bi-check2-circle"></i> จัดการสินค้าและสต็อกได้ทันที</li>
                <li><i class="bi bi-check2-circle"></i> ลูกค้าสั่งซื้อและติดตามคำสั่งซื้อได้เอง</li>
                <li><i class="bi bi-check2-circle"></i> แยกสิทธิ์ผู้ดูแลระบบ เจ้าของร้าน และลูกค้า</li>
            </ul>
        </div>
    </section>
    <section class="auth-panel">
        <div class="auth-card">
            @yield('content')
        </div>
    </section>
</div>
@include('partials.alerts')
@include('partials.password-toggle')
</body>
</html>
