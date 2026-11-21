@extends('layouts.app')
@section('title', 'ยืนยันคำสั่งซื้อ')

@section('content')
<form method="POST" action="{{ route('checkout.place') }}">
    @csrf
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card-x mb-3">
                <div class="card-head"><h2><i class="bi bi-truck me-2 text-success"></i>ข้อมูลจัดส่ง</h2></div>
                <div class="card-body-x">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">ชื่อผู้รับ <span class="text-danger">*</span></label>
                            <input type="text" name="shipping_name" value="{{ old('shipping_name', auth()->user()->name) }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">เบอร์โทร <span class="text-danger">*</span></label>
                            <input type="text" name="shipping_phone" value="{{ old('shipping_phone', auth()->user()->phone) }}" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">ที่อยู่จัดส่ง <span class="text-danger">*</span></label>
                            <textarea name="shipping_address" rows="3" class="form-control" required>{{ old('shipping_address') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">หมายเหตุ</label>
                            <input type="text" name="note" value="{{ old('note') }}" class="form-control" placeholder="ข้อความถึงร้านค้า (ถ้ามี)">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-x mb-3">
                <div class="card-head"><h2><i class="bi bi-truck me-2 text-success"></i>การขนส่ง</h2></div>
                <div class="card-body-x">
                    <div class="pay-box" style="cursor:default">
                        <div class="ico"><i class="bi bi-box-seam"></i></div>
                        <div>
                            <div class="title">{{ $shipping['carrier'] }}</div>
                            <div class="desc">
                                ค่าส่ง
                                @if($shipping['free']) <span class="text-success fw-semibold">ส่งฟรี</span>
                                @else <strong>฿{{ number_format($shipping['fee'], 2) }}</strong> ({{ $shipping['items'] }} ชิ้น)@endif
                                @if(! $shipping['free'] && config('shipping.free_over') > 0)
                                    · ซื้อครบ ฿{{ number_format(config('shipping.free_over'), 0) }} ส่งฟรี
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-x">
                <div class="card-head"><h2><i class="bi bi-wallet2 me-2 text-success"></i>วิธีชำระเงิน</h2></div>
                <div class="card-body-x">
                    @php($method = old('payment_method', 'bank_transfer'))

                    <label class="pay-option">
                        <input type="radio" name="payment_method" value="bank_transfer" @checked($method === 'bank_transfer')>
                        <div class="pay-box">
                            <div class="ico"><i class="bi bi-bank"></i></div>
                            <div>
                                <div class="title">โอนเงินผ่านธนาคาร</div>
                                <div class="desc">โอนเงินแล้วแนบสลิป ร้านค้าตรวจสอบและอนุมัติ</div>
                            </div>
                        </div>
                    </label>

                    <label class="pay-option mb-0">
                        <input type="radio" name="payment_method" value="promptpay" @checked($method === 'promptpay')>
                        <div class="pay-box">
                            <div class="ico"><i class="bi bi-qr-code-scan"></i></div>
                            <div>
                                <div class="title">QR PromptPay</div>
                                <div class="desc">สแกน QR Code ชำระเงิน แล้วตรวจสอบสถานะได้ในหน้าคำสั่งซื้อ</div>
                            </div>
                        </div>
                    </label>

                    <div class="form-text mt-2">หลังกดยืนยันสั่งซื้อ ระบบจะแสดงข้อมูลบัญชี/QR Code ให้ในหน้าคำสั่งซื้อ</div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card-x">
                <div class="card-head"><h2><i class="bi bi-receipt me-2 text-success"></i>สรุปรายการ</h2></div>
                <div class="card-body-x">
                    @foreach($lines as $line)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ $line['product']->name }} <span class="text-muted">× {{ $line['qty'] }}</span></span>
                            <span>฿{{ number_format($line['subtotal'], 2) }}</span>
                        </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between mb-2"><span class="text-muted">ค่าสินค้า</span><span>฿{{ number_format($total, 2) }}</span></div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted"><i class="bi bi-truck me-1"></i>ค่าส่ง ({{ $shipping['carrier'] }})</span>
                        <span>@if($shipping['free'])<span class="text-success">ส่งฟรี</span>@else ฿{{ number_format($shipping['fee'], 2) }}@endif</span>
                    </div>
                    <div class="d-flex justify-content-between fs-5 fw-bold text-success mb-3"><span>ยอดชำระทั้งสิ้น</span><span>฿{{ number_format($total + $shipping['fee'], 2) }}</span></div>
                    <button class="btn btn-primary w-100 mb-2" type="submit"><i class="bi bi-check2-circle me-1"></i> ยืนยันสั่งซื้อ</button>
                    <a href="{{ route('cart.index') }}" class="btn btn-light border w-100">กลับไปตะกร้า</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
