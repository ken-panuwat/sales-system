@extends('layouts.app')
@section('title', 'รายงานยอดขาย')

@section('content')
<div class="card-x mb-3">
    <div class="card-body-x">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">ประเภทรายงาน</label>
                <select name="type" id="rtype" class="form-select" onchange="this.form.submit()">
                    <option value="daily" @selected($type === 'daily')>รายวัน</option>
                    <option value="monthly" @selected($type === 'monthly')>รายเดือน</option>
                </select>
            </div>
            <div class="col-md-3">
                @if($type === 'daily')
                    <label class="form-label">เลือกวันที่</label>
                    <input type="date" name="date" value="{{ $date }}" class="form-control" onchange="this.form.submit()">
                @else
                    <label class="form-label">เลือกเดือน</label>
                    <input type="month" name="month" value="{{ $month }}" class="form-control" onchange="this.form.submit()">
                @endif
            </div>
            <div class="col-md-6 d-flex gap-2 flex-wrap justify-content-md-end">
                <button type="button" class="btn btn-primary" id="btnDownload"><i class="bi bi-download me-1"></i> ดาวน์โหลด PDF</button>
                <button type="button" class="btn btn-outline-primary" id="btnPrint"><i class="bi bi-printer me-1"></i> พิมพ์ / เปิด PDF</button>
            </div>
        </form>
    </div>
</div>

<div data-live="report">
<div class="card-x">
    <div class="card-head">
        <h2><i class="bi bi-file-earmark-bar-graph me-2 text-success"></i>{{ $report['title'] }}</h2>
        <span class="ms-auto text-muted">{{ $report['period'] }}</span>
    </div>
    <div class="card-body-x">
        <div class="row g-3 mb-3 text-center">
            <div class="col-6 col-md-3"><div class="text-muted small">จำนวนคำสั่งซื้อ</div><div class="fs-4 fw-bold">{{ $report['summary']['orders'] }}</div></div>
            <div class="col-6 col-md-3"><div class="text-muted small">จำนวนชิ้นที่ขาย</div><div class="fs-4 fw-bold">{{ $report['summary']['items'] }}</div></div>
            <div class="col-6 col-md-3"><div class="text-muted small">ยอดขายรวม (บาท)</div><div class="fs-4 fw-bold text-success">{{ $report['summary']['total'] }}</div></div>
            <div class="col-6 col-md-3"><div class="text-muted small">เฉลี่ยต่อคำสั่งซื้อ</div><div class="fs-4 fw-bold">{{ $report['summary']['avg'] }}</div></div>
        </div>

        <div class="table-responsive">
            <table class="table table-x">
                <thead><tr>@foreach($report['head'] as $h)<th>{{ $h }}</th>@endforeach</tr></thead>
                <tbody>
                @forelse($report['rows'] as $r)
                    <tr>@foreach($r as $c)<td>{{ $c }}</td>@endforeach</tr>
                @empty
                    <tr><td colspan="{{ count($report['head']) }}"><div class="empty-state"><i class="bi bi-inbox"></i><div>ไม่มียอดขายในช่วงเวลานี้</div></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if(count($report['products']))
            <h3 class="h6 mt-4">สรุปยอดขายตามสินค้า</h3>
            <div class="table-responsive">
                <table class="table table-x">
                    <thead><tr>@foreach($report['productHead'] as $h)<th>{{ $h }}</th>@endforeach</tr></thead>
                    <tbody>
                    @foreach($report['products'] as $r)
                        <tr>@foreach($r as $c)<td>{{ $c }}</td>@endforeach</tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
<script type="application/json" id="reportData">@json($report)</script>
</div>
<div class="small text-muted mt-2">ยอดขายนับเฉพาะคำสั่งซื้อที่ชำระเงินแล้วและไม่ถูกยกเลิก (ตามวันที่สั่งซื้อ)</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script>
(function () {
    // ฟอนต์ Sarabun (Thai Sarabun): ลองไฟล์ในเครื่อง public/fonts ก่อน แล้วค่อยดึงจาก CDN
    var SRC = {
        normal: ['{{ asset('fonts/Sarabun-Regular.ttf') }}', 'https://cdn.jsdelivr.net/gh/google/fonts@main/ofl/sarabun/Sarabun-Regular.ttf'],
        bold: ['{{ asset('fonts/Sarabun-Bold.ttf') }}', 'https://cdn.jsdelivr.net/gh/google/fonts@main/ofl/sarabun/Sarabun-Bold.ttf']
    };
    var fontsReady = null;

    function fetchBuf(urls) {
        return urls.reduce(function (p, u) {
            return p.catch(function () {
                return fetch(u).then(function (r) { if (!r.ok) throw new Error(u); return r.arrayBuffer(); });
            });
        }, Promise.reject());
    }
    function b64(buf) {
        var b = new Uint8Array(buf), s = '', i, n = 0x8000;
        for (i = 0; i < b.length; i += n) { s += String.fromCharCode.apply(null, b.subarray(i, i + n)); }
        return btoa(s);
    }
    function loadFonts() {
        if (!fontsReady) {
            fontsReady = Promise.all([fetchBuf(SRC.normal), fetchBuf(SRC.bold)]).then(function (r) {
                pdfMake.vfs = pdfMake.vfs || {};
                pdfMake.vfs['Sarabun-Regular.ttf'] = b64(r[0]);
                pdfMake.vfs['Sarabun-Bold.ttf'] = b64(r[1]);
                pdfMake.fonts = { Sarabun: { normal: 'Sarabun-Regular.ttf', bold: 'Sarabun-Bold.ttf', italics: 'Sarabun-Regular.ttf', bolditalics: 'Sarabun-Bold.ttf' } };
            }).catch(function (e) { fontsReady = null; throw e; });
        }
        return fontsReady;
    }

    function table(head, rows, widths, numCols) {
        var body = [head.map(function (h, i) { return { text: h, bold: true, fillColor: '#d1fae5', alignment: numCols.indexOf(i) >= 0 ? 'right' : 'left' }; })];
        rows.forEach(function (r) { body.push(r.map(function (c, i) { return { text: String(c), alignment: numCols.indexOf(i) >= 0 ? 'right' : 'left' }; })); });
        if (!rows.length) { body.push([{ text: 'ไม่มียอดขายในช่วงเวลานี้', colSpan: head.length, alignment: 'center', color: '#6b7280' }].concat(head.slice(1).map(function () { return {}; }))); }
        return { table: { headerRows: 1, widths: widths, body: body }, layout: { hLineColor: '#d1d5db', vLineColor: '#d1d5db' }, margin: [0, 4, 0, 12] };
    }

    function build() {
        var d = JSON.parse(document.getElementById('reportData').textContent);
        var daily = d.head.length === 5;
        var content = [
            { text: d.shop, style: 'shop', alignment: 'center' },
            { text: d.title, style: 'title', alignment: 'center' },
            { text: d.period, alignment: 'center', margin: [0, 0, 0, 10] },
            { columns: [
                { text: 'จำนวนคำสั่งซื้อ: ' + d.summary.orders }, { text: 'จำนวนชิ้น: ' + d.summary.items },
                { text: 'ยอดขายรวม: ' + d.summary.total + ' บาท', bold: true }, { text: 'เฉลี่ย/ออเดอร์: ' + d.summary.avg }
            ], margin: [0, 0, 0, 10] },
            table(d.head, d.rows, daily ? [50, '*', '*', 60, 90] : ['*', 90, 70, 100], daily ? [3, 4] : [1, 2, 3])
        ];
        if (d.products.length) {
            content.push({ text: 'สรุปยอดขายตามสินค้า', bold: true, margin: [0, 4, 0, 0] });
            content.push(table(d.productHead, d.products, ['*', 90, 100], [1, 2]));
        }
        return {
            pageSize: 'A4', pageMargins: [40, 40, 40, 50],
            defaultStyle: { font: 'Sarabun', fontSize: 10 },
            styles: { shop: { fontSize: 18, bold: true, color: '#047857' }, title: { fontSize: 20, bold: true } },
            content: content,
            footer: function (p, n) { return { columns: [{ text: d.printed, fontSize: 11, color: '#6b7280', margin: [40, 0, 0, 0] }, { text: 'หน้า ' + p + ' / ' + n, alignment: 'right', fontSize: 11, color: '#6b7280', margin: [0, 0, 40, 0] }] }; }
        };
    }

    function run(mode) {
        var win = mode === 'print' ? window.open('', '_blank') : null; // เปิดหน้าต่างทันทีเพื่อไม่ให้ถูกบล็อก
        loadFonts().then(function () {
            var doc = pdfMake.createPdf(build());
            if (mode === 'print') {
                doc.getBlob(function (blob) {
                    var url = URL.createObjectURL(blob);
                    if (win) { win.location.href = url; } else { window.open(url, '_blank'); }
                });
            } else {
                doc.download(JSON.parse(document.getElementById('reportData').textContent).file + '.pdf');
            }
        }).catch(function () {
            if (win) { win.close(); }
            Pop.fire({ icon: 'error', title: 'สร้าง PDF ไม่สำเร็จ', text: 'โหลดฟอนต์ Sarabun ไม่ได้ กรุณาตรวจสอบอินเทอร์เน็ต หรือวางไฟล์ฟอนต์ไว้ที่ public/fonts' });
        });
    }
    document.getElementById('btnDownload').addEventListener('click', function () { run('download'); });
    document.getElementById('btnPrint').addEventListener('click', function () { run('print'); });
})();
</script>
@endpush
