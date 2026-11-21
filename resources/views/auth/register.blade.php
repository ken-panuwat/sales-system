@extends('layouts.guest')
@section('title', 'สมัครสมาชิก')

@section('content')
    <h2>สมัครสมาชิก</h2>
    <p class="text-muted mb-4">สร้างบัญชีลูกค้าเพื่อสั่งซื้อสินค้า</p>

    <form method="POST" action="{{ route('register.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">ชื่อ-นามสกุล</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="ป้อนชื่อ-นามสกุล">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">อีเมล</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="ป้อนอีเมล" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">เบอร์โทร</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="ป้อนเบอร์โทร">
            </div>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-sm-6">
                <label class="form-label">รหัสผ่าน</label>
                <input type="password" name="password" class="form-control" placeholder="ป้อนรหัสผ่าน" required>
                <div class="form-text">อย่างน้อย 6 ตัว มีตัวพิมพ์ใหญ่ ตัวพิมพ์เล็ก ตัวเลข และอักขระพิเศษ</div>
            </div>
            <div class="col-sm-6">
                <label class="form-label">ยืนยันรหัสผ่าน</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="ยืนยันรหัสผ่าน" required>
            </div>
        </div>
        <button class="btn btn-primary w-100 py-2" type="submit"><i class="bi bi-person-plus me-1"></i> สมัครสมาชิก</button>
    </form>

    <p class="text-center mt-4">มีบัญชีแล้ว? <a href="{{ route('login') }}" class="fw-semibold">เข้าสู่ระบบ</a></p>
@endsection
