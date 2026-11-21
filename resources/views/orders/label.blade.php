<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ใบปะหน้า {{ $order->tracking_no }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap" rel="stylesheet">
<style>
    @page { size: 100mm 150mm; margin: 0; }
    * { box-sizing: border-box; }
    body { margin: 0; background: #e5e7eb; font-family: 'Sarabun', 'Noto Sans Thai', sans-serif; color: #000; font-size: 13px; }
    .toolbar { text-align: center; padding: 10px; }
    .toolbar button { font-family: inherit; font-size: 15px; padding: 8px 22px; border: 0; border-radius: 8px; background: #059669; color: #fff; cursor: pointer; }
    .label { width: 100mm; height: 150mm; margin: 0 auto 20px; background: #fff; padding: 4mm; border: 2px solid #000; display: flex; flex-direction: column; }
    .row { border-bottom: 1.5px solid #000; padding: 2mm 0; }
    .row:last-child { border-bottom: 0; }
    .head { display: flex; justify-content: space-between; align-items: center; }
    .carrier { font-size: 20px; font-weight: 700; }
    .small { font-size: 11px; color: #333; }
    .barcode { text-align: center; }
    .barcode svg { width: 100%; height: 22mm; }
    .tracking { text-align: center; font-size: 18px; font-weight: 700; letter-spacing: 1px; }
    .to-name { font-size: 20px; font-weight: 700; }
    .to-addr { font-size: 16px; line-height: 1.35; }
    .grow { flex: 1; }
    .mock { text-align: center; font-size: 10px; color: #555; }
    @media print {
        body { background: #fff; }
        .toolbar { display: none; }
        .label { margin: 0; border: 0; }
    }
</style>
</head>
<body>
<div class="toolbar"><button onclick="window.print()">🖨️ พิมพ์ใบปะหน้า</button></div>

<div class="label">
    <div class="row head">
        <div class="carrier">{{ $order->shipping_carrier ?: config('shipping.carrier') }}</div>
        <div class="small" style="text-align:right">คำสั่งซื้อ {{ $order->order_no }}<br>{{ $order->shipment_created_at->format('d/m/Y H:i') }}</div>
    </div>

    <div class="row barcode">
        <svg id="bc"></svg>
        <div class="tracking">{{ $order->tracking_no }}</div>
    </div>

    <div class="row grow">
        <div class="small">ผู้รับ (TO)</div>
        <div class="to-name">{{ $order->shipping_name }}</div>
        <div class="to-name" style="font-size:16px">โทร {{ $order->shipping_phone }}</div>
        <div class="to-addr">{{ $order->shipping_address }}</div>
    </div>

    <div class="row">
        <div class="small">ผู้ส่ง (FROM)</div>
        <div><strong>{{ $sender['name'] }}</strong> โทร {{ $sender['phone'] }}</div>
        <div class="small">{{ $sender['address'] }}</div>
    </div>

    <div class="row">
        <div class="small">สินค้า ({{ $order->items->sum('quantity') }} ชิ้น)</div>
        <div style="font-size:12px">
            @foreach($order->items->take(4) as $it){{ $it->product_name }} ×{{ $it->quantity }}@if(! $loop->last), @endif @endforeach
            @if($order->items->count() > 4) และอื่นๆ @endif
        </div>
        @if($order->note)<div class="small">หมายเหตุ: {{ $order->note }}</div>@endif
        <div class="small">ชำระเงินแล้ว (ไม่เก็บเงินปลายทาง)</div>
    </div>
    @if($isMock)<div class="mock">* เลขพัสดุนี้เป็นเลขจำลอง ยังไม่ได้เชื่อมระบบ Flash Express จริง</div>@endif
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.6/JsBarcode.all.min.js"></script>
<script>
    try { JsBarcode('#bc', @json($order->tracking_no), { format: 'CODE128', displayValue: false, margin: 0, height: 80 }); } catch (e) {}
    if (location.search.indexOf('print=1') > -1) { window.addEventListener('load', function () { setTimeout(function () { window.print(); }, 500); }); }
</script>
</body>
</html>
