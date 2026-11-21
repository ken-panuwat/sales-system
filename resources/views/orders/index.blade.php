@extends('layouts.app')
@section('title', auth()->user()->isCustomer() ? 'คำสั่งซื้อของฉัน' : 'คำสั่งซื้อ')

@section('content')
<div class="card-x">
    <div class="card-head">
        <h2><i class="bi bi-receipt me-2 text-success"></i>รายการคำสั่งซื้อ ({{ $orders->total() }})</h2>
        <form method="GET" class="ms-auto d-flex gap-2 flex-wrap">
            <select name="status" class="form-select" style="width:240px;max-width:100%;padding-top:.7rem;padding-bottom:.7rem" onchange="this.form.submit()">
                <option value="">การจัดส่งทุกสถานะ</option>
                @foreach($statusOptions as $key => $s)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $s['label'] }}</option>
                @endforeach
            </select>
            <select name="payment_status" class="form-select" style="width:240px;max-width:100%;padding-top:.7rem;padding-bottom:.7rem" onchange="this.form.submit()">
                <option value="">การชำระเงินทุกสถานะ</option>
                @foreach(\App\Models\Order::PAYMENT_STATUSES as $key => $s)
                    <option value="{{ $key }}" @selected(request('payment_status') === $key)>{{ $s['label'] }}</option>
                @endforeach
            </select>
            <div class="input-group" style="width:220px">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="เลขที่คำสั่งซื้อ">
            </div>
            <button class="btn btn-outline-primary" type="submit">ค้นหา</button>
        </form>
    </div>

    <div data-live="orders">
    <div class="table-responsive">
        <table class="table table-x">
            <thead>
            <tr>
                <th>เลขที่</th>
                @if(auth()->user()->isStaff())<th>ลูกค้า</th>@endif
                <th>วันที่สั่งซื้อ</th><th class="text-end">ยอดรวม</th><th>วิธีชำระเงิน</th><th>ชำระเงิน</th><th>จัดส่ง</th><th class="text-end">รายละเอียด</th>
            </tr>
            </thead>
            <tbody>
            @forelse($orders as $order)
                <tr>
                    <td class="fw-medium">{{ $order->order_no }}</td>
                    @if(auth()->user()->isStaff())<td>{{ $order->user->name ?? $order->shipping_name }}</td>@endif
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-end">฿{{ number_format($order->total, 2) }}</td>
                    <td class="text-nowrap"><i class="bi {{ $order->paymentMethodIcon() }} text-success me-1"></i>{{ $order->paymentMethodLabel() }}</td>
                    <td><span class="badge badge-soft badge-soft-{{ $order->paymentStatusClass() }}">{{ $order->paymentStatusLabel() }}</span></td>
                    <td><span class="badge badge-soft badge-soft-{{ $order->statusClass() }}">{{ $order->statusLabel() }}</span></td>
                    <td class="text-end"><a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> ดู</a></td>
                </tr>
            @empty
                <tr><td colspan="8"><div class="empty-state"><i class="bi bi-inbox"></i><div>ยังไม่มีคำสั่งซื้อ</div></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="p-3 d-flex justify-content-center">{{ $orders->links() }}</div>
    @endif
    </div>
</div>
@endsection
