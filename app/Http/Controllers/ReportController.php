<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private const MONTHS = ['', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];

    public function index(Request $request)
    {
        $type = $request->input('type') === 'monthly' ? 'monthly' : 'daily';

        $date = $this->parse($request->input('date'), 'Y-m-d') ?? today();
        $month = $this->parse($request->input('month'), 'Y-m') ?? today();

        if ($type === 'daily') {
            $from = $date->copy()->startOfDay();
            $to = $date->copy()->endOfDay();
            $title = 'รายงานยอดขายรายวัน';
            $period = 'ประจำวันที่ '.$this->thaiDate($date);
        } else {
            $from = $month->copy()->startOfMonth();
            $to = $month->copy()->endOfMonth();
            $title = 'รายงานยอดขายรายเดือน';
            $period = 'ประจำเดือน '.self::MONTHS[$month->month].' '.($month->year + 543);
        }

        // ยอดขาย = คำสั่งซื้อที่ชำระเงินแล้วและไม่ถูกยกเลิก (ตามวันที่สั่งซื้อ)
        $orders = Order::with(['items', 'user'])
            ->where('payment_status', 'paid')
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->get();

        $total = (float) $orders->sum('total');
        $summary = [
            'orders' => $orders->count(),
            'items' => (int) $orders->sum(fn ($o) => $o->items->sum('quantity')),
            'total' => $total,
            'avg' => $orders->count() ? $total / $orders->count() : 0,
        ];

        // รายการหลัก: รายวัน = รายออเดอร์ / รายเดือน = สรุปรายวัน
        if ($type === 'daily') {
            $rows = $orders->map(fn ($o) => [
                $o->created_at->format('H:i'),
                $o->order_no,
                $o->user->name ?? '-',
                (int) $o->items->sum('quantity'),
                $this->money($o->total),
            ])->values()->all();
            $head = ['เวลา', 'เลขที่คำสั่งซื้อ', 'ลูกค้า', 'จำนวนชิ้น', 'ยอดรวม (บาท)'];
        } else {
            $rows = [];
            foreach ($orders->groupBy(fn ($o) => $o->created_at->format('Y-m-d')) as $day => $list) {
                $rows[] = [
                    $this->thaiDate(Carbon::parse($day)),
                    $list->count(),
                    (int) $list->sum(fn ($o) => $o->items->sum('quantity')),
                    $this->money($list->sum('total')),
                ];
            }
            $head = ['วันที่', 'จำนวนคำสั่งซื้อ', 'จำนวนชิ้น', 'ยอดรวม (บาท)'];
        }

        // สรุปตามสินค้า
        $products = $orders->flatMap->items
            ->groupBy('product_name')
            ->map(fn ($g, $name) => [
                $name,
                (int) $g->sum('quantity'),
                (float) $g->sum('subtotal'),
            ])
            ->sortByDesc(fn ($r) => $r[2])
            ->values()
            ->map(fn ($r) => [$r[0], $r[1], $this->money($r[2])])
            ->all();

        $report = [
            'title' => $title,
            'period' => $period,
            'shop' => config('app.name'),
            'printed' => 'พิมพ์เมื่อ '.$this->thaiDate(now()).' '.now()->format('H:i').' น.',
            'summary' => [
                'orders' => number_format($summary['orders']),
                'items' => number_format($summary['items']),
                'total' => $this->money($summary['total']),
                'avg' => $this->money($summary['avg']),
            ],
            'head' => $head,
            'rows' => $rows,
            'productHead' => ['สินค้า', 'จำนวนที่ขาย', 'ยอดขาย (บาท)'],
            'products' => $products,
            'file' => ($type === 'daily' ? 'sales-daily-'.$date->format('Y-m-d') : 'sales-monthly-'.$month->format('Y-m')),
        ];

        return view('reports.index', [
            'type' => $type,
            'date' => $date->format('Y-m-d'),
            'month' => $month->format('Y-m'),
            'report' => $report,
        ]);
    }

    private function parse(?string $v, string $format): ?Carbon
    {
        if (! $v) {
            return null;
        }
        try {
            $c = Carbon::createFromFormat($format, $v);

            return $c ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function thaiDate(Carbon $d): string
    {
        return $d->day.' '.self::MONTHS[$d->month].' '.($d->year + 543);
    }

    private function money($n): string
    {
        return number_format((float) $n, 2);
    }
}
