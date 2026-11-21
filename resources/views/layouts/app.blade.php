<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'หน้าหลัก') - {{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
@php
    $me = auth()->user();
    $cartCount = collect(session('cart', []))->sum();
@endphp

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <span class="brand-icon"><i class="bi bi-shop"></i></span>
        <div>
            <div class="brand-name">{{ config('app.name') }}</div>
            <small>ระบบขายสินค้า</small>
        </div>
    </div>

    <nav class="sidebar-nav">
        @if($me->isStaff())
            <div class="nav-label">ภาพรวม</div>
            <a href="{{ route('dashboard') }}" class="nav-link-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i><span>แดชบอร์ด</span>
            </a>

            <div class="nav-label">จัดการร้าน</div>
            <a href="{{ route('products.index') }}" class="nav-link-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i><span>สินค้า</span>
            </a>
            <a href="{{ route('orders.index') }}" class="nav-link-item {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i><span>คำสั่งซื้อ</span>
            </a>
            <a href="{{ route('reports.index') }}" class="nav-link-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i><span>รายงานยอดขาย</span>
            </a>
        @endif

        @if($me->isAdmin())
            <div class="nav-label">ผู้ดูแลระบบ</div>
            <a href="{{ route('users.index') }}" class="nav-link-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i><span>จัดการผู้ใช้</span>
            </a>
        @endif

        @if($me->isCustomer())
            <div class="nav-label">ร้านค้า</div>
            <a href="{{ route('shop.index') }}" class="nav-link-item {{ request()->routeIs('shop.*') ? 'active' : '' }}">
                <i class="bi bi-grid-3x3-gap"></i><span>เลือกซื้อสินค้า</span>
            </a>
            <a href="{{ route('cart.index') }}" class="nav-link-item {{ request()->routeIs('cart.*', 'checkout') ? 'active' : '' }}">
                <i class="bi bi-cart3"></i><span>ตะกร้าสินค้า</span>
                @if($cartCount > 0)<span class="badge rounded-pill">{{ $cartCount }}</span>@endif
            </a>
            <a href="{{ route('orders.index') }}" class="nav-link-item {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <i class="bi bi-bag-check"></i><span>คำสั่งซื้อของฉัน</span>
            </a>
        @endif

        <div class="nav-label">บัญชี</div>
        <a href="{{ route('profile.edit') }}" class="nav-link-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i><span>โปรไฟล์ของฉัน</span>
        </a>
    </nav>

    <div class="sidebar-user">
        <a href="{{ route('profile.edit') }}" class="d-flex align-items-center gap-2 text-decoration-none" style="min-width:0" title="แก้ไขโปรไฟล์">
            @include('partials.avatar', ['user' => $me, 'size' => 40])
            <div style="min-width:0">
                <div class="name text-truncate">{{ $me->name }}</div>
                <div class="role">{{ $me->roleLabel() }}</div>
            </div>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="ms-auto"
              data-confirm="ต้องการออกจากระบบใช่หรือไม่?" data-title="ออกจากระบบ" data-icon="question" data-ok="ออกจากระบบ">
            @csrf
            <button type="submit" class="btn-logout" title="ออกจากระบบ"><i class="bi bi-box-arrow-right"></i></button>
        </form>
    </div>
</aside>
<div class="sidebar-backdrop" id="backdrop"></div>

<main class="main">
    <header class="topbar">
        <button class="btn-burger" id="burger" type="button" aria-label="เมนู"><i class="bi bi-list"></i></button>
        <h1>@yield('title')</h1>
        <div class="ms-auto d-none d-sm-block">
            <span class="badge badge-soft role-{{ $me->role }}">{{ $me->roleLabel() }}</span>
        </div>
    </header>

    <div class="content">
        @yield('content')
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('backdrop');
    function toggleSidebar() {
        sidebar.classList.toggle('open');
        backdrop.classList.toggle('show');
    }
    document.getElementById('burger').addEventListener('click', toggleSidebar);
    backdrop.addEventListener('click', toggleSidebar);
</script>
@include('partials.alerts')
@include('partials.password-toggle')
@stack('scripts')
@include('partials.live')
</body>
</html>
