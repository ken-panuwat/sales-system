@extends('layouts.app')
@section('title', 'ตะกร้าสินค้า')

@section('content')
@if(empty($lines))
    <div class="card-x">
        <div class="empty-state">
            <i class="bi bi-cart-x"></i>
            <div class="mb-3">ตะกร้าสินค้าของคุณยังว่างอยู่</div>
            <a href="{{ route('shop.index') }}" class="btn btn-primary"><i class="bi bi-grid-3x3-gap me-1"></i> เลือกซื้อสินค้า</a>
        </div>
    </div>
@else
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card-x">
            <div class="card-head"><h2><i class="bi bi-cart3 me-2 text-success"></i>สินค้าในตะกร้า</h2></div>
            <div class="table-responsive">
                <table class="table table-x">
                    <thead><tr><th>สินค้า</th><th class="text-end">ราคา</th><th style="width:150px">จำนวน</th><th class="text-end">รวม</th><th></th></tr></thead>
                    <tbody>
                    @foreach($lines as $line)
                        <tr>
                            <td class="fw-medium">{{ $line['product']->name }}<div class="small text-muted">คงเหลือ {{ $line['product']->stock }}</div></td>
                            <td class="text-end">
                                @if($line['product']->is_on_sale)
                                    <div class="price-old">฿{{ number_format($line['product']->price, 2) }}</div>
                                    <div class="text-danger fw-semibold">฿{{ number_format($line['product']->final_price, 2) }}</div>
                                @else
                                    ฿{{ number_format($line['product']->price, 2) }}
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('cart.update', $line['product']) }}" class="d-flex gap-1">
                                    @csrf @method('PATCH')
                                    <input type="number" name="quantity" value="{{ $line['qty'] }}" min="0" max="{{ $line['product']->stock }}" class="form-control form-control-sm">
                                    <button class="btn btn-sm btn-outline-primary" title="อัปเดตจำนวน"><i class="bi bi-check-lg"></i></button>
                                </form>
                            </td>
                            <td class="text-end">฿{{ number_format($line['subtotal'], 2) }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('cart.remove', $line['product']) }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="นำออก"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-x">
            <div class="card-head"><h2><i class="bi bi-receipt me-2 text-success"></i>สรุปคำสั่งซื้อ</h2></div>
            <div class="card-body-x">
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">จำนวนรายการ</span><span>{{ count($lines) }}</span></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">ค่าสินค้า</span><span>฿{{ number_format($total, 2) }}</span></div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted"><i class="bi bi-truck me-1"></i>ค่าส่ง ({{ $shipping['carrier'] }})</span>
                    <span>@if($shipping['free'])<span class="text-success">ส่งฟรี</span>@else ฿{{ number_format($shipping['fee'], 2) }}@endif</span>
                </div>
                <div class="d-flex justify-content-between fs-5 fw-bold text-success mb-3"><span>ยอดรวม</span><span>฿{{ number_format($total + $shipping['fee'], 2) }}</span></div>
                <a href="{{ route('checkout') }}" class="btn btn-primary w-100 mb-2"><i class="bi bi-credit-card me-1"></i> ไปหน้าชำระเงิน</a>
                <a href="{{ route('shop.index') }}" class="btn btn-light border w-100">เลือกซื้อสินค้าต่อ</a>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
