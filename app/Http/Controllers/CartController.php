<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Support\Shipping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CartController extends Controller
{
    public function index()
    {
        [$lines, $total] = $this->lines();

        $shipping = $this->shippingFor($lines, $total);

        return view('cart.index', compact('lines', 'total', 'shipping'));
    }

    public function add(Request $request, Product $product)
    {
        $qty = max(1, (int) $request->input('quantity', 1));

        if (! $product->is_active || $product->stock < 1) {
            return back()->with('error', 'สินค้านี้หมดหรือไม่เปิดขาย');
        }

        $cart = session('cart', []);
        $cart[$product->id] = min(($cart[$product->id] ?? 0) + $qty, $product->stock);
        session(['cart' => $cart]);

        return back()->with('success', "เพิ่ม \"{$product->name}\" ลงตะกร้าแล้ว");
    }

    public function update(Request $request, Product $product)
    {
        $qty = (int) $request->input('quantity', 1);
        $cart = session('cart', []);

        if ($qty <= 0) {
            unset($cart[$product->id]);
        } else {
            $cart[$product->id] = min($qty, $product->stock);
        }

        session(['cart' => $cart]);

        return back()->with('success', 'อัปเดตตะกร้าแล้ว');
    }

    public function remove(Product $product)
    {
        $cart = session('cart', []);
        unset($cart[$product->id]);
        session(['cart' => $cart]);

        return back()->with('success', 'นำสินค้าออกจากตะกร้าแล้ว');
    }

    public function checkout()
    {
        [$lines, $total] = $this->lines();

        if (empty($lines)) {
            return redirect()->route('shop.index')->with('error', 'ตะกร้าสินค้าว่างอยู่');
        }

        $shipping = $this->shippingFor($lines, $total);

        return view('cart.checkout', compact('lines', 'total', 'shipping'));
    }

    public function place(Request $request)
    {
        [$lines, $cartTotal] = $this->lines();

        if (empty($lines)) {
            return redirect()->route('shop.index')->with('error', 'ตะกร้าสินค้าว่างอยู่');
        }

        $data = $request->validate([
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_phone' => ['required', 'string', 'max:20'],
            'shipping_address' => ['required', 'string'],
            'note' => ['nullable', 'string', 'max:500'],
            // รับชำระเฉพาะ โอนธนาคาร และ QR PromptPay
            'payment_method' => ['required', Rule::in(['bank_transfer', 'promptpay'])],
        ]);

        try {
            $order = DB::transaction(function () use ($lines, $data) {
                $total = 0;
                $rows = [];

                foreach ($lines as $line) {
                    // ล็อกแถวสินค้าไว้ตอนตัดสต็อก ป้องกันขายเกิน
                    $product = Product::lockForUpdate()->find($line['product']->id);

                    if (! $product || ! $product->is_active || $product->stock < $line['qty']) {
                        throw new \RuntimeException("สินค้า \"{$line['product']->name}\" มีไม่เพียงพอ");
                    }

                    $product->decrement('stock', $line['qty']);

                    // คิดราคาหลังหักโปรโมชัน ณ ตอนสั่งซื้อจริง
                    $unitPrice = $product->final_price;
                    $subtotal = $unitPrice * $line['qty'];
                    $total += $subtotal;

                    $rows[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'price' => $unitPrice,
                        'quantity' => $line['qty'],
                        'subtotal' => $subtotal,
                    ];
                }

                // ค่าส่งคิดจากยอดสินค้าจริงตอนสั่งซื้อ (ป้องกันการแก้ราคาจากฝั่งผู้ใช้)
                $shipping = $this->shippingFor($lines, $total);

                $order = Order::create($data + [
                    'user_id' => auth()->id(),
                    'order_no' => 'ORD-'.now()->format('ymd').'-'.strtoupper(Str::random(5)),
                    'total' => $total + $shipping['fee'],
                    'shipping_fee' => $shipping['fee'],
                    'shipping_carrier' => $shipping['carrier'],
                    'status' => 'pending',
                    // รอลูกค้าชำระเงิน
                    'payment_status' => 'unpaid',
                ]);

                $order->items()->createMany($rows);

                return $order;
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }

        session()->forget('cart');

        return redirect()->route('orders.show', $order)
            ->with('success', 'สั่งซื้อสำเร็จ! หมายเลขคำสั่งซื้อ '.$order->order_no
                .' กรุณาชำระเงินตามวิธีที่เลือก');
    }

    private function shippingFor(array $lines, float $total): array
    {
        $items = array_sum(array_column($lines, 'qty'));

        return Shipping::quote((int) $items, (float) $total);
    }

    /** สร้างรายการในตะกร้าจาก session พร้อมเช็คสต็อกล่าสุด */
    private function lines(): array
    {
        $cart = session('cart', []);
        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');

        $lines = [];
        $total = 0;

        foreach ($cart as $id => $qty) {
            $product = $products->get($id);

            if (! $product || ! $product->is_active) {
                continue;
            }

            $qty = min((int) $qty, $product->stock);

            if ($qty <= 0) {
                continue;
            }

            $subtotal = $product->final_price * $qty;
            $lines[] = ['product' => $product, 'qty' => $qty, 'subtotal' => $subtotal];
            $total += $subtotal;
        }

        return [$lines, $total];
    }
}
