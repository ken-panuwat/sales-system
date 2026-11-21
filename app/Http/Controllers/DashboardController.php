<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'products' => Product::count(),
            'low_stock' => Product::where('stock', '<=', 5)->count(),
            'orders' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'verifying' => Order::where('payment_status', 'verifying')->count(),
            // ยอดขาย = เฉพาะคำสั่งซื้อที่ชำระเงินแล้วและไม่ถูกยกเลิก
            'revenue' => Order::where('payment_status', 'paid')->where('status', '!=', 'cancelled')->sum('total'),
            'customers' => User::where('role', User::ROLE_CUSTOMER)->count(),
            'users' => User::count(),
        ];

        $recentOrders = Order::with('user')->latest()->take(6)->get();
        $lowStock = Product::where('stock', '<=', 5)->orderBy('stock')->take(6)->get();

        $charts = $this->charts();

        return view('dashboard', compact('stats', 'recentOrders', 'lowStock', 'charts'));
    }

    private function charts(): array
    {
        // 1) ยอดขาย + จำนวนคำสั่งซื้อ 14 วันล่าสุด
        $days = 14;
        $from = now()->subDays($days - 1)->startOfDay();

        $recent = Order::where('created_at', '>=', $from)
            ->where('status', '!=', 'cancelled')
            ->get(['total', 'payment_status', 'created_at']);

        $labels = [];
        $revenue = [];
        $counts = [];

        for ($i = 0; $i < $days; $i++) {
            $day = $from->copy()->addDays($i);
            $dayOrders = $recent->filter(fn ($o) => $o->created_at->isSameDay($day));

            $labels[] = $day->format('d/m');
            $revenue[] = round((float) $dayOrders->where('payment_status', 'paid')->sum('total'), 2);
            $counts[] = $dayOrders->count();
        }

        // 2) สัดส่วนสถานะคำสั่งซื้อ
        $statusCounts = Order::select('status', DB::raw('COUNT(*) as c'))->groupBy('status')->pluck('c', 'status');
        $statusColors = [
            'pending' => '#f59e0b',
            'confirmed' => '#06b6d4',
            'packing' => '#0d9488',
            'shipped' => '#10b981',
            'cancelled' => '#ef4444',
        ];

        $status = ['labels' => [], 'data' => [], 'colors' => []];
        foreach (Order::STATUSES as $key => $s) {
            if (($statusCounts[$key] ?? 0) > 0) {
                $status['labels'][] = $s['label'];
                $status['data'][] = (int) $statusCounts[$key];
                $status['colors'][] = $statusColors[$key];
            }
        }

        // 3) สินค้าขายดี 5 อันดับ
        $top = OrderItem::select('product_name', DB::raw('SUM(quantity) as qty'))
            ->whereHas('order', fn ($q) => $q->where('status', '!=', 'cancelled'))
            ->groupBy('product_name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        // 4) สัดส่วนผู้ใช้ตามบทบาท (แสดงเฉพาะผู้ดูแลระบบ)
        $roles = ['labels' => [], 'data' => []];
        $roleCounts = User::select('role', DB::raw('COUNT(*) as c'))->groupBy('role')->pluck('c', 'role');
        foreach (User::ROLES as $key => $label) {
            $roles['labels'][] = $label;
            $roles['data'][] = (int) ($roleCounts[$key] ?? 0);
        }

        return [
            'sales' => ['labels' => $labels, 'revenue' => $revenue, 'orders' => $counts],
            'status' => $status,
            'top' => [
                'labels' => $top->pluck('product_name')->all(),
                'data' => $top->pluck('qty')->map(fn ($v) => (int) $v)->all(),
            ],
            'roles' => $roles,
        ];
    }
}
