@extends('layouts.app')
@section('title', 'โปรไฟล์ของฉัน')

@section('content')
<form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="row g-3">
        {{-- รูปโปรไฟล์ --}}
        <div class="col-lg-4">
            <div class="card-x text-center">
                <div class="card-head"><h2><i class="bi bi-person-bounding-box me-2 text-success"></i>รูปโปรไฟล์</h2></div>
                <div class="card-body-x">
                    <div id="avatarPreview" class="d-flex justify-content-center mb-3">
                        @include('partials.avatar', ['user' => $user, 'size' => 140])
                    </div>
                    <div class="fw-semibold">{{ $user->name }}</div>
                    <div class="mb-3"><span class="badge badge-soft role-{{ $user->role }}">{{ $user->roleLabel() }}</span></div>

                    <input type="file" name="avatar" id="avatarInput" accept="image/*" class="form-control mb-2">
                    <div class="form-text mb-2">jpg, png ไม่เกิน 2 MB — หากไม่ใส่รูป ระบบจะแสดงอักษรตัวแรกของชื่อ</div>

                    @if($user->avatar)
                        <div class="form-check d-inline-block text-start">
                            <input class="form-check-input" type="checkbox" name="remove_avatar" value="1" id="remove_avatar">
                            <label class="form-check-label" for="remove_avatar">ลบรูปโปรไฟล์ปัจจุบัน</label>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ข้อมูลส่วนตัว + เปลี่ยนรหัสผ่าน --}}
        <div class="col-lg-8">
            <div class="card-x mb-3">
                <div class="card-head"><h2><i class="bi bi-person-lines-fill me-2 text-success"></i>ข้อมูลส่วนตัว</h2></div>
                <div class="card-body-x">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">เบอร์โทร</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">อีเมล (ใช้เข้าสู่ระบบ)</label>
                            <input type="email" value="{{ $user->email }}" class="form-control" disabled>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-x">
                <div class="card-head"><h2><i class="bi bi-shield-lock me-2 text-success"></i>เปลี่ยนรหัสผ่าน</h2><small class="text-muted ms-auto">เว้นว่างไว้หากไม่ต้องการเปลี่ยน</small></div>
                <div class="card-body-x">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">รหัสผ่านปัจจุบัน</label>
                            <input type="password" name="current_password" class="form-control" autocomplete="current-password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">รหัสผ่านใหม่</label>
                            <input type="password" name="password" class="form-control" autocomplete="new-password">
                                                   <div class="form-text">อย่างน้อย 6 ตัว ประกอบด้วยตัวพิมพ์ใหญ่ ตัวพิมพ์เล็ก ตัวเลข และอักขระพิเศษ (เช่น ! @ # $)</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
                            <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="mt-4 d-flex gap-2 justify-content-center">
        <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg me-1"></i> บันทึกโปรไฟล์</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // แสดงตัวอย่างรูปที่เลือกก่อนบันทึก
    document.getElementById('avatarInput').addEventListener('change', function () {
        var file = this.files && this.files[0];
        var box = document.querySelector('#avatarPreview .avatar');
        if (!file || !box) { return; }
        var img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.alt = 'ตัวอย่างรูปโปรไฟล์';
        box.innerHTML = '';
        box.appendChild(img);
        var rm = document.getElementById('remove_avatar');
        if (rm) { rm.checked = false; }
    });
</script>
@endpush
