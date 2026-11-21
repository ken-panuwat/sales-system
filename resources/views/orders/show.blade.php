@extends('layouts.app')
@section('title', 'คำสั่งซื้อ '.$order->order_no)

@section('content')
@php
    $isStaff = auth()->user()->isStaff();
    $cancelled = $order->status === 'cancelled';
    $shipped = $order->status === 'shipped';
    $notPaid = $order->payment_status !== 'paid';
    $showQr = $order->payment_method === 'promptpay' && $order->needsPayment() && config('shop.promptpay_id');
    // ลูกค้า: ตรวจสถานะอัตโนมัติจนกว่าจะจัดส่งแล้ว (ร้านอนุมัติเงิน/เปลี่ยนขั้นตอน หน้าจะรีเฟรชเอง)
    $polling = ! $cancelled && ! $shipped;

    // ขั้นตอน 4 ขั้น: วงกลมเป็นสีเขียวเมื่อขั้นตอนนั้นสำเร็จ
    $idx = array_search($order->status, \App\Models\Order::FLOW, true);
    $idx = $idx === false ? -1 : $idx;
    $steps = [
        ['label' => 'ชำระเงินสำเร็จ', 'icon' => 'bi-wallet2', 'done' => $order->payment_status === 'paid'],
        ['label' => 'ยืนยันออเดอร์', 'icon' => 'bi-patch-check', 'done' => $idx >= 1],
        ['label' => 'แพ็คสินค้า', 'icon' => 'bi-box-seam', 'done' => $idx >= 2],
        ['label' => 'จัดส่งสินค้าให้บริการขนส่ง', 'icon' => 'bi-truck', 'done' => $idx >= 3],
    ];
    $lastDone = -1;
    foreach ($steps as $i => $st) {
        if ($st['done']) { $lastDone = $i; }
    }

    // ตัวเลือกในดรอปดาวน์อัปเดตสถานะ (ฝั่งร้าน): ไปข้างหน้าเท่านั้น
    $stepOptions = collect(array_keys(\App\Models\Order::STEP_LABELS))
        ->filter(fn ($k) => array_search($k, \App\Models\Order::FLOW, true) > $idx || ($k === 'shipped' && $shipped))
        ->values();
@endphp

<div class="row g-3">
    {{-- ================= ซ้าย: สถานะจัดส่ง + รายการสินค้า ================= --}}
    <div class="col-lg-7">
        <div class="card-x mb-3">
            <div class="card-head">
                <h2><i class="bi bi-truck me-2 text-success"></i>สถานะการจัดส่ง</h2>
            </div>
            <div class="card-body-x">
                @if($cancelled)
                    <div class="alert alert-danger border-0 rounded-4 mb-0"><i class="bi bi-x-circle-fill me-2"></i>คำสั่งซื้อนี้ถูกยกเลิกแล้ว</div>
                @else
                    <div class="track">
                        @foreach($steps as $i => $st)
                            <div class="track-step {{ $st['done'] ? 'done' : '' }} {{ $i === $lastDone ? 'current' : '' }}">
                                <div class="track-dot"><i class="bi {{ $st['icon'] }}"></i></div>
                                {{ $st['label'] }}
                            </div>
                        @endforeach
                    </div>

                    @if($shipped)
                        <div class="alert alert-success border-0 rounded-4 {{ $order->tracking_no ? 'mb-3' : 'mb-0' }}">
                            <i class="bi bi-truck me-2"></i>จัดส่งสินค้าให้กับบริการขนส่งเรียบร้อยแล้ว
                        </div>
                    @endif
                    @if($order->tracking_no)
                        <div class="bank-box"><i class="bi bi-upc-scan me-1 text-success"></i> เลขพัสดุ / ข้อมูลการจัดส่ง: <strong>{{ $order->tracking_no }}</strong>@if($order->shipping_carrier) <span class="text-muted">({{ $order->shipping_carrier }})</span>@endif</div>
                    @endif
                @endif
            </div>
        </div>

        <div class="card-x">
            <div class="card-head"><h2><i class="bi bi-bag-check me-2 text-success"></i>รายการสินค้า</h2></div>
            <div class="table-responsive">
                <table class="table table-x">
                    <thead><tr><th>สินค้า</th><th class="text-end">ราคา</th><th class="text-center">จำนวน</th><th class="text-end">รวม</th></tr></thead>
                    <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td class="fw-medium">{{ $item->product_name }}</td>
                            <td class="text-end">฿{{ number_format($item->price, 2) }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">฿{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3" class="text-end text-muted">ค่าสินค้า</td>
                        <td class="text-end">฿{{ number_format($order->items_total, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end text-muted">ค่าส่ง ({{ $order->shipping_carrier ?: config('shipping.carrier') }})</td>
                        <td class="text-end">@if((float) $order->shipping_fee <= 0)<span class="text-success">ส่งฟรี</span>@else ฿{{ number_format($order->shipping_fee, 2) }}@endif</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end fw-semibold">ยอดรวมทั้งสิ้น</td>
                        <td class="text-end fs-5 fw-bold text-success">฿{{ number_format($order->total, 2) }}</td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- ================= ขวา: ชำระเงิน + จัดส่ง + จัดการ ================= --}}
    <div class="col-lg-5">
        {{-- ---------- การชำระเงิน ---------- --}}
        <div class="card-x mb-3">
            <div class="card-head">
                <h2><i class="bi bi-wallet2 me-2 text-success"></i>การชำระเงิน</h2>
                <span class="badge badge-soft badge-soft-{{ $order->paymentStatusClass() }} ms-auto">{{ $order->paymentStatusLabel() }}</span>
            </div>
            <div class="card-body-x">
                <div class="small text-muted">วิธีชำระเงิน</div>
                <div class="fw-medium mb-2"><i class="bi {{ $order->paymentMethodIcon() }} me-1 text-success"></i>{{ $order->paymentMethodLabel() }}</div>
                <div class="small text-muted">ยอดที่ต้องชำระ</div>
                <div class="fs-4 fw-bold text-success mb-2">฿{{ number_format($order->total, 2) }}</div>

                @if($order->payment_status === 'paid')
                    <div class="small text-muted">ชำระเมื่อ</div>
                    <div class="mb-2">{{ $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : '-' }}</div>
                @endif

                @if($order->payment_status === 'rejected' && $order->payment_note)
                    <div class="alert alert-danger border-0 rounded-4 py-2 small"><i class="bi bi-exclamation-circle-fill me-1"></i>ไม่ผ่านการตรวจสอบ: {{ $order->payment_note }}</div>
                @endif

                {{-- โอนเงินผ่านธนาคาร --}}
                @if($order->payment_method === 'bank_transfer' && $order->needsPayment())
                    <div class="bank-box mb-3">
                        <div class="fw-semibold mb-1"><i class="bi bi-bank me-1 text-success"></i>โอนเงินเข้าบัญชี</div>
                        <div>{{ config('shop.bank.name') }}</div>
                        <div class="fs-5 fw-bold text-success">{{ config('shop.bank.account_no') }}</div>
                        <div class="small text-muted">ชื่อบัญชี: {{ config('shop.bank.account_name') }}</div>
                    </div>
                @endif

                {{-- QR PromptPay --}}
                @if($showQr)
                    <div class="qr-box mb-3">
                        <div id="qrcode"></div>
                        <div class="small text-muted mt-2">สแกนด้วยแอปธนาคารเพื่อชำระ ฿{{ number_format($order->total, 2) }}<br>
                            PromptPay: {{ config('shop.promptpay_name') }}</div>
                    </div>
                @endif

                {{-- รอตรวจสอบ --}}
                @if($order->payment_status === 'verifying')
                    <div class="alert alert-info border-0 rounded-4 py-2 small mb-3">
                        <i class="bi bi-hourglass-split me-1"></i>
                        @if($isStaff) ลูกค้าแจ้งชำระเงินแล้ว กรุณาตรวจสอบและอนุมัติ
                        @else แจ้งชำระเงินแล้ว รอร้านค้าตรวจสอบ (หน้านี้จะอัปเดตสถานะให้อัตโนมัติ) @endif
                    </div>
                @endif

                {{-- สลิปที่แนบ --}}
                @if($order->slip_url)
                    <div class="small text-muted mb-1">สลิปที่แนบ</div>
                    <a href="{{ $order->slip_url }}" target="_blank" rel="noopener"><img src="{{ $order->slip_url }}" alt="สลิปการโอนเงิน" class="slip-img mb-3"></a>
                @endif

                {{-- ลูกค้า: แจ้งชำระเงิน / แนบสลิป --}}
                @if(! $isStaff && $order->needsPayment())
                    <form method="POST" action="{{ route('orders.payment.submit', $order) }}" enctype="multipart/form-data"
                          data-confirm="ยืนยันการแจ้งชำระเงินใช่หรือไม่?" data-title="แจ้งชำระเงิน" data-icon="question" data-ok="แจ้งชำระเงิน">
                        @csrf
                        <label class="form-label">
                            แนบสลิปการโอนเงิน
                            @if($order->payment_method === 'bank_transfer')<span class="text-danger">*</span>@else<span class="text-muted">(ไม่บังคับ)</span>@endif
                        </label>
                        <input type="file" name="slip" accept="image/*" class="form-control mb-2" @required($order->payment_method === 'bank_transfer')>
                        <button class="btn btn-primary w-100" type="submit"><i class="bi bi-send-check me-1"></i>
                            {{ $order->payment_method === 'promptpay' ? 'ฉันชำระเงินแล้ว' : 'แจ้งชำระเงิน' }}
                        </button>
                    </form>
                @endif

                {{-- เจ้าของร้าน/ผู้ดูแล: ตรวจสอบและอนุมัติ --}}
                @if($isStaff && ! $cancelled && $notPaid)
                    <form method="POST" action="{{ route('orders.payment.review', $order) }}" class="mt-3 border-top pt-3">
                        @csrf @method('PATCH')
                        @if($order->payment_status !== 'verifying')
                            <div class="alert alert-warning border-0 rounded-4 py-2 small mb-2">
                                <i class="bi bi-hourglass-split me-1"></i>รอลูกค้าชำระเงินและส่งสลิป จึงจะอนุมัติได้
                            </div>
                        @endif
                        @if($order->payment_status === 'verifying')
                            <label class="form-label small">หมายเหตุ (กรณีไม่อนุมัติ)</label>
                            <input type="text" name="note" class="form-control mb-2" maxlength="255" placeholder="เช่น ยอดเงินไม่ตรง / สลิปไม่ชัด">
                        @endif
                        <div class="d-grid gap-2">
                            <button type="submit" name="action" value="approve" class="btn btn-primary" @disabled($order->payment_status !== 'verifying')
                                    data-confirm="ยืนยันว่าตรวจสอบแล้วและได้รับเงินครบ?"
                                    data-title="ยืนยันการรับชำระเงิน" data-icon="question" data-ok="ยืนยัน">
                                <i class="bi bi-check-circle me-1"></i>อนุมัติการชำระเงิน
                            </button>
                            @if($order->payment_status === 'verifying')
                                <button type="submit" name="action" value="reject" class="btn btn-outline-danger"
                                        data-confirm="ต้องการส่งกลับให้ลูกค้าแจ้งชำระเงินใหม่ใช่หรือไม่?"
                                        data-title="ไม่อนุมัติการชำระเงิน" data-ok="ไม่อนุมัติ">
                                    <i class="bi bi-x-circle me-1"></i>ไม่อนุมัติ
                                </button>
                            @endif
                        </div>
                    </form>
                @endif
            </div>
        </div>

        {{-- ---------- ข้อมูลจัดส่ง ---------- --}}
        <div class="card-x mb-3">
            <div class="card-head"><h2><i class="bi bi-geo-alt me-2 text-success"></i>ข้อมูลจัดส่ง</h2></div>
            <div class="card-body-x">
                <div class="small text-muted">เลขที่คำสั่งซื้อ</div><div class="fw-medium mb-2">{{ $order->order_no }}</div>
                <div class="small text-muted">วันที่สั่งซื้อ</div><div class="mb-2">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                <div class="small text-muted">ผู้รับ</div><div class="mb-2">{{ $order->shipping_name }} ({{ $order->shipping_phone }})</div>
                <div class="small text-muted">ที่อยู่</div><div class="mb-2">{{ $order->shipping_address }}</div>
                @if($order->note)
                    <div class="small text-muted">หมายเหตุ</div><div>{{ $order->note }}</div>
                @endif
                @if($isStaff)
                    <hr>
                    <div class="small text-muted">บัญชีลูกค้า</div>
                    <div>{{ $order->user->name ?? '(ผู้ใช้ถูกลบแล้ว)' }}</div>
                @endif
            </div>
        </div>

        {{-- ---------- สร้างรายการส่ง / ใบปะหน้า (เจ้าของร้าน/ผู้ดูแล) ---------- --}}
        @if($isStaff && ! $cancelled)
            <div class="card-x mb-3">
                <div class="card-head"><h2><i class="bi bi-box-seam me-2 text-success"></i>รายการส่ง ({{ $order->shipping_carrier ?: config('shipping.carrier') }})</h2></div>
                <div class="card-body-x">
                    @if($order->shipment_created_at)
                        <div class="bank-box mb-3"><i class="bi bi-upc-scan me-1 text-success"></i> เลขพัสดุ: <strong>{{ $order->tracking_no }}</strong>
                            <div class="small text-muted">สร้างเมื่อ {{ $order->shipment_created_at->format('d/m/Y H:i') }}</div></div>
                        <a href="{{ route('orders.label', $order) }}" target="_blank" class="btn btn-primary w-100"><i class="bi bi-printer me-1"></i> พิมพ์ใบปะหน้ากล่องพัสดุ</a>
                    @else
                        @if($notPaid)
                            <div class="alert alert-warning border-0 rounded-4 py-2 small"><i class="bi bi-exclamation-triangle-fill me-1"></i>ต้องอนุมัติการชำระเงินก่อน จึงจะสร้างรายการส่งได้</div>
                        @endif
                        <form method="POST" action="{{ route('orders.shipment.create', $order) }}"
                              data-confirm="สร้างรายการส่งและออกเลขพัสดุสำหรับคำสั่งซื้อนี้?" data-title="สร้างรายการส่ง" data-icon="question" data-ok="สร้างรายการส่ง">
                            @csrf
                            <button class="btn btn-primary w-100" type="submit" @disabled($notPaid)><i class="bi bi-plus-circle me-1"></i> สร้างรายการส่ง</button>
                        </form>
                    @endif
                </div>
            </div>
        @endif

        {{-- ---------- อัปเดตสถานะการจัดส่ง (เจ้าของร้าน/ผู้ดูแล) ---------- --}}
        @if($isStaff && ! $cancelled)
            <div class="card-x mb-3">
                <div class="card-head"><h2><i class="bi bi-arrow-repeat me-2 text-success"></i>อัปเดตสถานะการจัดส่ง</h2></div>
                <div class="card-body-x">
                    @if($notPaid)
                        <div class="alert alert-warning border-0 rounded-4 py-2 small">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>ต้องอนุมัติการชำระเงินก่อน จึงจะอัปเดตสถานะได้
                        </div>
                    @endif
                    <form method="POST" action="{{ route('orders.status', $order) }}"
                          data-confirm="ยืนยันการอัปเดตสถานะการจัดส่ง? (อัปเดตแล้วย้อนกลับไม่ได้)"
                          data-title="ยืนยันการอัปเดตสถานะ" data-icon="question" data-ok="บันทึกสถานะ">
                        @csrf @method('PATCH')
                        <label class="form-label small" for="statusSelect">สถานะ</label>
                        <select name="status" id="statusSelect" class="form-select mb-2" @disabled($notPaid)>
                            @foreach($stepOptions as $key)
                                <option value="{{ $key }}" @selected($loop->first)>{{ \App\Models\Order::STEP_LABELS[$key] }}</option>
                            @endforeach
                        </select>
                        <div id="trackingBox" class="mb-3">
                            <label class="form-label small" for="trackingInput">เลขพัสดุ / ข้อมูลการจัดส่ง <span class="text-danger">*</span></label>
                            <input type="text" id="trackingInput" name="tracking_no" value="{{ old('tracking_no', $order->tracking_no) }}" maxlength="100" class="form-control" placeholder="เช่น Flash TH0123456789">
                        </div>
                        <button class="btn btn-primary w-100" type="submit" @disabled($notPaid)>
                            <i class="bi bi-check2-circle me-1"></i>บันทึกสถานะ
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <a href="{{ route('orders.index') }}" class="btn btn-light border w-100"><i class="bi bi-arrow-left me-1"></i> กลับไปรายการ</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ช่องเลขพัสดุ: แสดง/บังคับกรอกเฉพาะเมื่อเลือก "จัดส่งสินค้าให้บริการขนส่ง"
    (function () {
        var sel = document.getElementById('statusSelect');
        var box = document.getElementById('trackingBox');
        var input = document.getElementById('trackingInput');
        if (!sel || !box || !input) { return; }
        function sync() {
            var isShipped = sel.value === 'shipped';
            box.style.display = isShipped ? '' : 'none';
            input.required = isShipped;
        }
        sel.addEventListener('change', sync);
        sync();
    })();
</script>
@if($showQr)
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        new QRCode(document.getElementById('qrcode'), {
            text: @json(\App\Support\PromptPay::payload((string) config('shop.promptpay_id'), (float) $order->total)),
            width: 220,
            height: 220,
            correctLevel: QRCode.CorrectLevel.M
        });
    </script>
@endif
@if($polling)
    <script>
        // ตรวจสถานะการชำระเงินทุก 8 วินาที เมื่อร้านค้าอนุมัติ/ปฏิเสธ หน้าจะรีเฟรชเอง
        (function () {
            var current = @json($order->payment_status);
            var currentStatus = @json($order->status);
            setInterval(function () {
                fetch(@json(route('orders.payment.status', $order)), { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                    .then(function (r) { return r.ok ? r.json() : null; })
                    .then(function (d) {
                        if (d && (d.payment_status !== current || d.status !== currentStatus)) {
                            var a = document.activeElement;
                            var typing = a && /^(INPUT|TEXTAREA|SELECT)$/.test(a.tagName);
                            if (!typing && !(window.Swal && Swal.isVisible())) { location.reload(); }
                        }
                    })
                    .catch(function () {});
            }, 5000);
        })();
    </script>
@endif
@endpush
