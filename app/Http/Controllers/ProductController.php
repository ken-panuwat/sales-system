<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // จำนวนแถวต่อหน้า: 10 / 20 / 50 / 100 / all (ทั้งหมด)
        $perPage = in_array($request->input('per_page'), ['10', '20', '50', '100', 'all'], true)
            ? $request->input('per_page')
            : '10';

        // เรียงตามรหัสสินค้า จากก่อนไปหลัง (SKU-001, SKU-002, ... SKU-010)
        $query = Product::search($request->q)->orderByRaw('LENGTH(sku)')->orderBy('sku');
        $size = $perPage === 'all' ? max($query->count(), 1) : (int) $perPage;

        $products = $query->paginate($size)->withQueryString();

        return view('products.index', compact('products', 'perPage'));
    }

    public function create()
    {
        // แสดงรหัสที่จะได้ให้ดูเฉยๆ (ผู้ใช้แก้ไขไม่ได้)
        return view('products.create', ['nextSku' => Product::nextSku()]);
    }

    public function store(Request $request)
    {
        $data = $this->normalizePromo($request->validate($this->rules($request)));
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        // รหัสสินค้ารันอัตโนมัติจากตัวล่าสุดเสมอ (ไม่รับค่าจากฟอร์ม)
        // ถ้ามีคนเพิ่มพร้อมกันแล้วรหัสชนกัน ให้คำนวณใหม่แล้วลองอีกครั้ง
        for ($try = 0; ; $try++) {
            try {
                Product::create($data + ['sku' => Product::nextSku()]);
                break;
            } catch (UniqueConstraintViolationException $e) {
                if ($try >= 2) {
                    throw $e;
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'เพิ่มสินค้าเรียบร้อยแล้ว');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        // ไม่รับ/ไม่แก้รหัสสินค้า (sku) ตอนแก้ไข
        $data = $this->normalizePromo($request->validate($this->rules($request)));
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        } else {
            unset($data['image']);
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'อัปเดตข้อมูลสินค้าเรียบร้อยแล้ว');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'ลบสินค้าเรียบร้อยแล้ว');
    }

    private function rules(Request $request): array
    {
        $type = $request->input('discount_type');

        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],

            // โปรโมชัน (เว้นว่างทั้งหมดถ้าไม่มีโปรโมชัน)
            'promo_name' => ['nullable', 'string', 'max:100'],
            'discount_type' => ['nullable', Rule::in(['percent', 'amount'])],
            'discount_value' => array_merge(
                ['nullable', 'numeric'],
                $type ? ['required', 'min:0.01'] : [],
                $type === 'percent' ? ['max:100'] : [],
                $type === 'amount' ? ['lt:price'] : []
            ),
            'promo_start' => ['nullable', 'date'],
            'promo_end' => array_merge(
                ['nullable', 'date'],
                $request->filled('promo_start') ? ['after_or_equal:promo_start'] : []
            ),
        ];
    }

    /** ถ้าไม่ได้เลือกประเภทส่วนลด ให้ล้างข้อมูลโปรโมชันทั้งหมด */
    private function normalizePromo(array $data): array
    {
        if (empty($data['discount_type'])) {
            $data['discount_type'] = null;
            $data['discount_value'] = null;
            $data['promo_start'] = null;
            $data['promo_end'] = null;
            $data['promo_name'] = null;
        }

        return $data;
    }
}
