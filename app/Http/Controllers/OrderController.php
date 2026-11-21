<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\Shipping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $orders = Order::with('user')
            ->when($user->isCustomer(), fn ($q) => $q->where('user_id', $user->id))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->payment_status, fn ($q, $s) => $q->where('payment_status', $s))
            ->when($request->q, fn ($q, $term) => $q->where('order_no', 'like', "%{$term}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // ตัวเลือกในดรอปดาวน์ตัวกรอง: "ยกเลิก" แสดงเฉพาะเมื่อมีข้อมูลจริงในระบบ
        $hasCancelled = Order::where('status', 'cancelled')
            ->when($user->isCustomer(), fn ($q) => $q->where('user_id', $user->id))
            ->exists();

        $statusOptions = collect(Order::STATUSES)
            ->reject(fn ($s, $key) => $key === 'cancelled' && ! $hasCancelled);

        return view('orders.index', compact('orders', 'statusOptions'));
    }

    public function show(Request $request, Order $order)
    {
        $this->authorizeView($request, $order);

        $order->load('items', 'user');

        return view('orders.show', compact('order'));
    }

    /** ใช้ให้หน้าคำสั่งซื้อของลูกค้าตรวจสถานะการชำระเงินเป็นระยะ (polling) */
    public function paymentStatus(Request $request, Order $order)
    {
        $this->authorizeView($request, $order);

        return response()->json([
            'payment_status' => $order->payment_status,
            'payment_label' => $order->paymentStatusLabel(),
            'status' => $order->status,
            'status_label' => $order->statusLabel(),
        ]);
    }

    /** ลูกค้าแจ้งชำระเงิน (แนบสลิป) สำหรับโอนธนาคาร / PromptPay */
    public function submitPayment(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403, 'คุณไม่มีสิทธิ์ทำรายการนี้');

        if (! $order->needsPayment()) {
            return back()->with('error', 'คำสั่งซื้อนี้ไม่สามารถแจ้งชำระเงินได้');
        }

        $request->validate([
            // โอนธนาคารต้องแนบสลิป / PromptPay แนบหรือไม่ก็ได้
            'slip' => [Rule::requiredIf($order->payment_method === 'bank_transfer'), 'nullable', 'image', 'max:4096'],
        ]);

        $path = $order->payment_slip;

        if ($request->hasFile('slip')) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            $path = $request->file('slip')->store('slips', 'public');
        }

        $order->update([
            'payment_slip' => $path,
            'payment_status' => 'verifying',
            'payment_note' => null,
        ]);

        return back()->with('success', 'แจ้งชำระเงินเรียบร้อยแล้ว กรุณารอร้านค้าตรวจสอบ');
    }

    /** เจ้าของร้าน/ผู้ดูแลระบบ อนุมัติ หรือไม่อนุมัติการชำระเงิน */
    public function reviewPayment(Request $request, Order $order)
    {
        $data = $request->validate([
            'action' => ['required', Rule::in(['approve', 'reject'])],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        if ($order->status === 'cancelled') {
            return back()->with('error', 'คำสั่งซื้อที่ยกเลิกแล้วไม่สามารถทำรายการได้');
        }

        if ($order->payment_status === 'paid') {
            return back()->with('error', 'คำสั่งซื้อนี้ชำระเงินเรียบร้อยแล้ว');
        }

        // อนุมัติ/ไม่อนุมัติได้เฉพาะเมื่อลูกค้าแจ้งชำระเงิน (ส่งสลิป) มาแล้ว
        if ($order->payment_status !== 'verifying') {
            return back()->with('error', 'ลูกค้ายังไม่ได้ชำระเงินหรือส่งสลิปมา จึงยังอนุมัติการชำระเงินไม่ได้');
        }

        if ($data['action'] === 'approve') {
            $order->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'payment_note' => null,
            ]);

            return back()->with('success', 'บันทึกการชำระเงินเรียบร้อยแล้ว');
        }

        $order->update([
            'payment_status' => 'rejected',
            'payment_note' => $data['note'] ?: 'ข้อมูลการชำระเงินไม่ถูกต้อง กรุณาแจ้งชำระใหม่',
        ]);

        return back()->with('success', 'ส่งกลับให้ลูกค้าแจ้งชำระเงินใหม่แล้ว');
    }

    /**
     * อัปเดตสถานะการจัดส่ง (เจ้าของร้าน/ผู้ดูแลระบบ)
     * ขั้นตอน: ยืนยันออเดอร์ -> แพ็คสินค้า -> จัดส่งสินค้าให้บริการขนส่ง (ย้อนกลับไม่ได้)
     */
    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Order::STEP_LABELS))],
            // ต้องกรอกเลขพัสดุ / ข้อมูลการจัดส่ง เมื่อบันทึกว่าจัดส่งแล้ว
            'tracking_no' => [Rule::requiredIf($request->input('status') === 'shipped'), 'nullable', 'string', 'max:100'],
        ]);

        if ($order->status === 'cancelled') {
            return back()->with('error', 'คำสั่งซื้อที่ยกเลิกแล้วไม่สามารถเปลี่ยนสถานะได้');
        }

        // ต้องชำระเงินสำเร็จก่อนจึงจะดำเนินการขั้นต่อไปได้
        if ($order->payment_status !== 'paid') {
            return back()->with('error', 'ยังไม่ได้รับการชำระเงิน กรุณาอนุมัติการชำระเงินก่อนอัปเดตสถานะ');
        }

        $current = array_search($order->status, Order::FLOW, true);
        $current = $current === false ? -1 : $current;
        $next = array_search($data['status'], Order::FLOW, true);

        // ย้อนสถานะไม่ได้ (ยกเว้นแก้เลขพัสดุเมื่อจัดส่งแล้ว)
        $editTrackingOnly = $data['status'] === 'shipped' && $order->status === 'shipped';
        if ($next <= $current && ! $editTrackingOnly) {
            return back()->with('error', 'ไม่สามารถย้อนกลับสถานะได้');
        }

        $update = ['status' => $data['status']];

        if (filled($data['tracking_no'] ?? null)) {
            $update['tracking_no'] = $data['tracking_no'];
        }

        $order->update($update);

        return back()->with('success', 'อัปเดตสถานะการจัดส่งเรียบร้อยแล้ว');
    }

    /** สร้างรายการส่ง (ขนส่ง Flash Express) — ออกเลขพัสดุให้คำสั่งซื้อ */
    public function createShipment(Order $order)
    {
        if ($order->status === 'cancelled') {
            return back()->with('error', 'คำสั่งซื้อนี้ถูกยกเลิกแล้ว');
        }

        if ($order->payment_status !== 'paid') {
            return back()->with('error', 'ต้องอนุมัติการชำระเงินก่อนจึงจะสร้างรายการส่งได้');
        }

        if ($order->shipment_created_at) {
            return back()->with('error', 'คำสั่งซื้อนี้สร้างรายการส่งไปแล้ว');
        }

        $order->update([
            'tracking_no' => $order->tracking_no ?: Shipping::createShipment(),
            'shipping_carrier' => $order->shipping_carrier ?: Shipping::carrier(),
            'shipment_created_at' => now(),
        ]);

        return back()->with('success', 'สร้างรายการส่งเรียบร้อยแล้ว เลขพัสดุ '.$order->tracking_no);
    }

    /** หน้าพิมพ์ใบปะหน้ากล่องพัสดุ */
    public function label(Order $order)
    {
        if (! $order->shipment_created_at) {
            return redirect()->route('orders.show', $order)->with('error', 'กรุณาสร้างรายการส่งก่อนพิมพ์ใบปะหน้า');
        }

        $order->load('items');

        return view('orders.label', [
            'order' => $order,
            'sender' => config('shipping.sender'),
            'isMock' => Shipping::isMock(),
        ]);
    }

    private function authorizeView(Request $request, Order $order): void
    {
        $user = $request->user();

        if ($user->isCustomer() && $order->user_id !== $user->id) {
            abort(403, 'คุณไม่มีสิทธิ์ดูคำสั่งซื้อนี้');
        }
    }
}
