@extends('layouts.app')
@section('title', 'แดชบอร์ด')

@section('content')
<div data-live="stats">
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-g1"><i class="bi bi-box-seam"></i></div>
            <div><div class="stat-label">สินค้าทั้งหมด</div><div class="stat-value">{{ number_format($stats['products']) }}</div></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-g2"><i class="bi bi-receipt"></i></div>
            <div><div class="stat-label">คำสั่งซื้อทั้งหมด</div><div class="stat-value">{{ number_format($stats['orders']) }}</div></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-g3"><i class="bi bi-hourglass-split"></i></div>
            <div><div class="stat-label">รอดำเนินการ</div><div class="stat-value">{{ number_format($stats['pending']) }}</div></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-g4"><i class="bi bi-cash-coin"></i></div>
            <div><div class="stat-label">ยอดขายที่ชำระแล้ว (บาท)</div><div class="stat-value">{{ number_format($stats['revenue'], 2) }}</div></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('orders.index', ['payment_status' => 'verifying']) }}" class="text-decoration-none">
            <div class="stat-card">
                <div class="stat-icon bg-g3"><i class="bi bi-patch-question"></i></div>
                <div><div class="stat-label">รอตรวจสอบการชำระเงิน</div><div class="stat-value">{{ number_format($stats['verifying']) }}</div></div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-g5"><i class="bi bi-exclamation-triangle"></i></div>
            <div><div class="stat-label">สินค้าใกล้หมด (≤ 5)</div><div class="stat-value">{{ number_format($stats['low_stock']) }}</div></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-g2"><i class="bi bi-person-heart"></i></div>
            <div><div class="stat-label">ลูกค้า</div><div class="stat-value">{{ number_format($stats['customers']) }}</div></div>
        </div>
    </div>
    @if(auth()->user()->isAdmin())
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-g1"><i class="bi bi-people"></i></div>
            <div><div class="stat-label">ผู้ใช้ทั้งหมด</div><div class="stat-value">{{ number_format($stats['users']) }}</div></div>
        </div>
    </div>
    @endif
</div>

</div>

{{-- ===== กราฟ ===== --}}
<div class="row g-3 mb-4">
    <div class="col-xl-8">
        <div class="card-x h-100">
            <div class="card-head"><h2><i class="bi bi-graph-up-arrow me-2 text-success"></i>ยอดขายและคำสั่งซื้อ 14 วันล่าสุด</h2></div>
            <div class="card-body-x"><div class="chart-box"><canvas id="chSales"></canvas></div></div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card-x h-100">
            <div class="card-head"><h2><i class="bi bi-pie-chart me-2 text-success"></i>สถานะคำสั่งซื้อ</h2></div>
            <div class="card-body-x">
                @if(count($charts['status']['data']))
                    <div class="chart-box"><canvas id="chStatus"></canvas></div>
                @else
                    <div class="empty-state"><i class="bi bi-pie-chart"></i><div>ยังไม่มีข้อมูล</div></div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-{{ auth()->user()->isAdmin() ? '8' : '12' }}">
        <div class="card-x h-100">
            <div class="card-head"><h2><i class="bi bi-trophy me-2 text-success"></i>สินค้าขายดี 5 อันดับ (ชิ้น)</h2></div>
            <div class="card-body-x">
                @if(count($charts['top']['data']))
                    <div class="chart-box sm"><canvas id="chTop"></canvas></div>
                @else
                    <div class="empty-state"><i class="bi bi-bar-chart"></i><div>ยังไม่มีข้อมูลการขาย</div></div>
                @endif
            </div>
        </div>
    </div>
    @if(auth()->user()->isAdmin())
    <div class="col-xl-4">
        <div class="card-x h-100">
            <div class="card-head"><h2><i class="bi bi-people me-2 text-success"></i>ผู้ใช้ตามบทบาท</h2></div>
            <div class="card-body-x"><div class="chart-box sm"><canvas id="chRoles"></canvas></div></div>
        </div>
    </div>
    @endif
</div>

<div data-live="tables">
<div class="row g-3">
    <div class="col-xl-7">
        <div class="card-x">
            <div class="card-head">
                <h2><i class="bi bi-receipt me-2 text-success"></i>คำสั่งซื้อล่าสุด</h2>
                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary ms-auto">ดูทั้งหมด</a>
            </div>
            <div class="table-responsive">
                <table class="table table-x">
                    <thead><tr><th>เลขที่</th><th>ลูกค้า</th><th class="text-end">ยอดรวม</th><th>ชำระเงิน</th><th>จัดส่ง</th></tr></thead>
                    <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td><a href="{{ route('orders.show', $order) }}" class="fw-medium text-decoration-none">{{ $order->order_no }}</a></td>
                            <td>{{ $order->user->name ?? $order->shipping_name }}</td>
                            <td class="text-end">฿{{ number_format($order->total, 2) }}</td>
                            <td><span class="badge badge-soft badge-soft-{{ $order->paymentStatusClass() }}">{{ $order->paymentStatusLabel() }}</span></td>
                            <td><span class="badge badge-soft badge-soft-{{ $order->statusClass() }}">{{ $order->statusLabel() }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty-state"><i class="bi bi-inbox"></i><div>ยังไม่มีคำสั่งซื้อ</div></div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card-x">
            <div class="card-head">
                <h2><i class="bi bi-exclamation-triangle me-2 text-warning"></i>สินค้าใกล้หมดสต็อก</h2>
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary ms-auto">จัดการสินค้า</a>
            </div>
            <div class="table-responsive">
                <table class="table table-x">
                    <thead><tr><th>สินค้า</th><th class="text-end">คงเหลือ</th></tr></thead>
                    <tbody>
                    @forelse($lowStock as $p)
                        <tr>
                            <td>{{ $p->name }}<div class="small text-muted">{{ $p->sku }}</div></td>
                            <td class="text-end"><span class="badge badge-soft {{ $p->stock == 0 ? 'badge-soft-danger' : 'badge-soft-warning' }}">{{ $p->stock }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="2"><div class="empty-state"><i class="bi bi-emoji-smile"></i><div>สต็อกสินค้าเพียงพอทุกรายการ</div></div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
<script type="application/json" id="chartData">@json($charts)</script>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    var C = JSON.parse(document.getElementById('chartData').textContent);
    var charts = {};
    Chart.defaults.font.family = "'Noto Sans Thai', sans-serif";
    Chart.defaults.color = '#4b5563';
    var grid = { color: '#eef3f0' };

    // 1) ยอดขาย (แท่ง) + จำนวนคำสั่งซื้อ (เส้น)
    charts.sales = new Chart(document.getElementById('chSales'), {
        type: 'bar',
        data: {
            labels: C.sales.labels,
            datasets: [
                { type: 'bar', label: 'ยอดขายที่ชำระแล้ว (฿)', data: C.sales.revenue, backgroundColor: 'rgba(16,185,129,.78)', borderRadius: 8, yAxisID: 'y', order: 2 },
                { type: 'line', label: 'จำนวนคำสั่งซื้อ', data: C.sales.orders, borderColor: '#f59e0b', backgroundColor: '#f59e0b', tension: .35, pointRadius: 3, yAxisID: 'y1', order: 1 }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, grid: grid },
                y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { precision: 0 } }
            }
        }
    });

    // 2) สถานะคำสั่งซื้อ
    var elStatus = document.getElementById('chStatus');
    if (elStatus) {
        charts.status = new Chart(elStatus, {
            type: 'doughnut',
            data: { labels: C.status.labels, datasets: [{ data: C.status.data, backgroundColor: C.status.colors, borderWidth: 2, borderColor: '#fff' }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom' } } }
        });
    }

    // 3) สินค้าขายดี
    var elTop = document.getElementById('chTop');
    if (elTop) {
        charts.top = new Chart(elTop, {
            type: 'bar',
            data: { labels: C.top.labels, datasets: [{ label: 'จำนวนที่ขายได้ (ชิ้น)', data: C.top.data, backgroundColor: ['#059669', '#10b981', '#34d399', '#0d9488', '#84cc16'], borderRadius: 8 }] },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, grid: grid, ticks: { precision: 0 } }, y: { grid: { display: false } } }
            }
        });
    }

    // 4) ผู้ใช้ตามบทบาท (ผู้ดูแลระบบเท่านั้น)
    var elRoles = document.getElementById('chRoles');
    if (elRoles) {
        charts.roles = new Chart(elRoles, {
            type: 'doughnut',
            data: { labels: C.roles.labels, datasets: [{ data: C.roles.data, backgroundColor: ['#064e3b', '#f59e0b', '#14b8a6'], borderWidth: 2, borderColor: '#fff' }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom' } } }
        });
    }

    // อัปเดตกราฟแบบเรียลไทม์ (เรียกจาก partials/live)
    window.onLiveDoc = function (doc) {
        var el = doc.getElementById('chartData'); if (!el) return;
        var N; try { N = JSON.parse(el.textContent); } catch (e) { return; }
        // กราฟที่เดิมไม่มีข้อมูล (ไม่ได้สร้างไว้) แต่ตอนนี้มีข้อมูล -> โหลดหน้าใหม่
        if ((N.status.data.length && !charts.status) || (N.top.data.length && !charts.top)) { location.reload(); return; }
        var s = charts.sales; if (s) { s.data.labels = N.sales.labels; s.data.datasets[0].data = N.sales.revenue; s.data.datasets[1].data = N.sales.orders; s.update('none'); }
        var t = charts.status; if (t) { t.data.labels = N.status.labels; t.data.datasets[0].data = N.status.data; t.data.datasets[0].backgroundColor = N.status.colors; t.update('none'); }
        var p = charts.top; if (p) { p.data.labels = N.top.labels; p.data.datasets[0].data = N.top.data; p.update('none'); }
        var r = charts.roles; if (r) { r.data.labels = N.roles.labels; r.data.datasets[0].data = N.roles.data; r.update('none'); }
    };
})();
</script>
@endpush
