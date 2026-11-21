@php($user = $user ?? new \App\Models\User(['role' => 'customer']))
@php($isEdit = $user->exists)

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">อีเมล <span class="text-danger">*</span></label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">เบอร์โทร</label>
        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label">บทบาท <span class="text-danger">*</span></label>
        <select name="role" class="form-select" @disabled($isEdit && $user->id === auth()->id())>
            @foreach(\App\Models\User::ROLES as $key => $label)
                <option value="{{ $key }}" @selected(old('role', $user->role) === $key)>{{ $label }}</option>
            @endforeach
        </select>
        @if($isEdit && $user->id === auth()->id())
            <input type="hidden" name="role" value="{{ $user->role }}">
            <div class="form-text">ไม่สามารถเปลี่ยนบทบาทของบัญชีตัวเองได้</div>
        @endif
    </div>
    <div class="col-md-6">
        <label class="form-label">รหัสผ่าน @if(!$isEdit)<span class="text-danger">*</span>@endif</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" @required(!$isEdit)>
                               <div class="form-text">อย่างน้อย 6 ตัว ประกอบด้วยตัวพิมพ์ใหญ่ ตัวพิมพ์เล็ก ตัวเลข และอักขระพิเศษ (เช่น ! @ # $)</div>
        @if($isEdit)<div class="form-text">เว้นว่างไว้หากไม่ต้องการเปลี่ยนรหัสผ่าน</div>@endif
    </div>
    <div class="col-md-6">
        <label class="form-label">ยืนยันรหัสผ่าน</label>
        <input type="password" name="password_confirmation" class="form-control" @required(!$isEdit)>
    </div>
</div>

<div class="mt-4 d-flex gap-2 justify-content-center">
    <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg me-1"></i> บันทึก</button>
    <a href="{{ route('users.index') }}" class="btn btn-light border">ยกเลิก</a>
</div>
