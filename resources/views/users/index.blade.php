@extends('layouts.app')
@section('title', 'จัดการผู้ใช้')

@section('content')
<div class="card-x">
    <div class="card-head">
        <h2><i class="bi bi-people me-2 text-success"></i>ผู้ใช้ทั้งหมด ({{ $users->total() }})</h2>
        <form method="GET" class="ms-auto d-flex gap-2 flex-wrap">
            <select name="role" class="form-select" style="width:140px" onchange="this.form.submit()">
                <option value="">ทุกบทบาท</option>
                @foreach(\App\Models\User::ROLES as $key => $label)
                    <option value="{{ $key }}" @selected(request('role') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="input-group" style="width:240px">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="ชื่อ / อีเมล">
            </div>
            <button class="btn btn-outline-primary" type="submit">ค้นหา</button>
        </form>
        <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i> เพิ่มผู้ใช้</a>
    </div>

    <div class="table-responsive">
        <table class="table table-x">
            <thead><tr><th>ชื่อ</th><th>อีเมล</th><th>เบอร์โทร</th><th>บทบาท</th><th class="text-end">จัดการ</th></tr></thead>
            <tbody>
            @forelse($users as $u)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @include('partials.avatar', ['user' => $u, 'size' => 34])
                            <span class="fw-medium">{{ $u->name }}</span>
                            @if($u->id === auth()->id())<span class="badge badge-soft badge-soft-info">คุณ</span>@endif
                        </div>
                    </td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->phone ?: '-' }}</td>
                    <td><span class="badge badge-soft role-{{ $u->role }}">{{ $u->roleLabel() }}</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i> แก้ไข</a>
                        @if($u->id !== auth()->id())
                            <form action="{{ route('users.destroy', $u) }}" method="POST" class="d-inline" data-confirm="ต้องการลบผู้ใช้ “{{ $u->name }}” ใช่หรือไม่?" data-title="ยืนยันการลบผู้ใช้" data-ok="ลบผู้ใช้">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> ลบ</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty-state"><i class="bi bi-people"></i><div>ไม่พบผู้ใช้</div></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="p-3 d-flex justify-content-center">{{ $users->links() }}</div>
    @endif
</div>
@endsection
