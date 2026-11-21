@extends('layouts.app')
@section('title', 'จัดการสินค้า')

@section('content')
<div class="card-x">
    <div class="card-head">
        <h2><i class="bi bi-box-seam me-2 text-success"></i>รายการสินค้า ({{ $products->total() }})</h2>
        <form method="GET" class="ms-auto d-flex gap-2 flex-wrap align-items-center">
            <div class="per-page">
                <label for="per_page" class="mb-0">แสดง</label>
                <select name="per_page" style="width:110px;" id="per_page" class="form-select" onchange="this.form.submit()">
                    @foreach(['10' => '10', '20' => '20', '50' => '50', '100' => '100', 'all' => 'ทั้งหมด'] as $val => $label)
                        <option value="{{ $val }}" @selected($perPage === (string) $val)>{{ $label }}</option>
                    @endforeach
                </select>
                <span>แถว</span>
            </div>
            <div class="input-group" style="width:260px">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="ค้นหาชื่อ / รหัส / หมวดหมู่">
            </div>
            <button class="btn btn-outline-primary" type="submit">ค้นหา</button>
        </form>
        <a href="{{ route('products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> เพิ่มสินค้า</a>
    </div>

    <div data-live="products">
    <div class="table-responsive">
        <table class="table table-x">
            <thead>
            <tr>
                <th>รูป</th><th>รหัส</th><th>ชื่อสินค้า</th><th class="text-end">ราคา</th>
                <th class="text-center">สต็อก</th><th>สถานะ</th><th class="text-end">จัดการ</th>
            </tr>
            </thead>
            <tbody>
            @forelse($products as $p)
                <tr>
                    <td>
                        @if($p->image_url)
                            <img src="{{ $p->image_url }}" class="thumb" alt="{{ $p->name }}">
                        @else
                            <div class="thumb"><i class="bi bi-image"></i></div>
                        @endif
                    </td>
                    <td class="text-muted">{{ $p->sku }}</td>
                    <td>
                        <div class="fw-medium">{{ $p->name }}</div>
                        @if($p->category)<span class="small text-success">{{ $p->category }}</span>@endif
                    </td>
                    <td class="text-end">
                        @if($p->is_on_sale)
                            <div class="price-old">฿{{ number_format($p->price, 2) }}</div>
                            <div class="text-danger fw-semibold">฿{{ number_format($p->final_price, 2) }}</div>
                            <span class="badge badge-soft badge-soft-danger">โปรโมชัน -{{ $p->discount_percent }}%</span>
                        @else
                            ฿{{ number_format($p->price, 2) }}
                            @if($p->discount_type)
                                <div class="small text-muted">มีโปรโมชันที่ยังไม่ถึง/หมดช่วงเวลา</div>
                            @endif
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge badge-soft {{ $p->stock == 0 ? 'badge-soft-danger' : ($p->stock <= 5 ? 'badge-soft-warning' : 'badge-soft-success') }}">{{ $p->stock }}</span>
                    </td>
                    <td>
                        @if($p->is_active)
                            <span class="badge badge-soft badge-soft-success">เปิดขาย</span>
                        @else
                            <span class="badge badge-soft badge-soft-secondary">ปิดขาย</span>
                        @endif
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('products.edit', $p) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i> แก้ไข</a>
                        <form action="{{ route('products.destroy', $p) }}" method="POST" class="d-inline" data-confirm="ต้องการลบสินค้า “{{ $p->name }}” ใช่หรือไม่? การลบไม่สามารถกู้คืนได้" data-title="ยืนยันการลบสินค้า" data-ok="ลบสินค้า">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> ลบ</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty-state"><i class="bi bi-box"></i><div>ไม่พบสินค้า</div></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="table-foot">
        <div class="small text-muted">
            @if($products->total() > 0)
                แสดง {{ number_format($products->firstItem()) }} - {{ number_format($products->lastItem()) }} จากทั้งหมด {{ number_format($products->total()) }} รายการ
            @else
                ไม่มีรายการ
            @endif
        </div>
        @if($products->hasPages())
            <div>{{ $products->links() }}</div>
        @endif
    </div>
    </div>
</div>
@endsection
