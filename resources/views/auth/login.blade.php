@extends('layouts.guest')
@section('title', 'เข้าสู่ระบบ')

@section('content')
    <h2>เข้าสู่ระบบ</h2>
    <p class="text-muted mb-4">ยินดีต้อนรับกลับมา กรุณาเข้าสู่ระบบเพื่อใช้งาน</p>

    <form method="POST" action="{{ route('login.attempt') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label" for="email">อีเมล</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="name@gmail.com" required autofocus>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label" for="password">รหัสผ่าน</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" id="password" name="password" class="form-control" placeholder="password" required>
            </div>
        </div>
        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label" for="remember">จดจำการเข้าสู่ระบบ</label>
        </div>
        <button class="btn btn-primary w-100 py-2" type="submit"><i class="bi bi-box-arrow-in-right me-1"></i> เข้าสู่ระบบ</button>
    </form>

    <p class="text-center mt-4 mb-4">ยังไม่มีบัญชี? <a href="{{ route('register') }}" class="fw-semibold">สมัครสมาชิก (ลูกค้า)</a></p>

@endsection
