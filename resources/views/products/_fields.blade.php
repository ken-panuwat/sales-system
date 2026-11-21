@php($product = $product ?? new \App\Models\Product(['is_active' => true, 'stock' => 0]))

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">รหัสสินค้า (SKU)</label>
        <input type="text" value="{{ $product->exists ? $product->sku : ($nextSku ?? '') }}" class="form-control bg-light" readonly disabled>
        <div class="form-text">{{ $product->exists ? 'รหัสสินค้าแก้ไขไม่ได้' : 'ระบบรันรหัสให้อัตโนมัติ' }}</div>
    </div>
    <div class="col-md-8">
        <label class="form-label">ชื่อสินค้า <span class="text-danger">*</span></label>
        <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control @error('name') is-invalid @enderror" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">หมวดหมู่</label>
        <input type="text" name="category" value="{{ old('category', $product->category) }}" class="form-control" placeholder="เช่น เครื่องดื่ม">
    </div>
    <div class="col-md-4">
        <label class="form-label">ราคา (บาท) <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">฿</span>
            <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price) }}" class="form-control @error('price') is-invalid @enderror" required>
        </div>
    </div>
    <div class="col-md-4">
        <label class="form-label">จำนวนในสต็อก <span class="text-danger">*</span></label>
        <input type="number" min="0" name="stock" value="{{ old('stock', $product->stock) }}" class="form-control @error('stock') is-invalid @enderror" required>
    </div>
    <div class="col-12">
        <label class="form-label">รายละเอียด</label>
        <textarea name="description" rows="3" class="form-control">{{ old('description', $product->description) }}</textarea>
    </div>

    {{-- ===== โปรโมชัน / ส่วนลด ===== --}}
    <div class="col-12">
        <div class="promo-box">
            <div class="fw-semibold mb-2"><i class="bi bi-megaphone me-1 text-warning"></i>โปรโมชัน / ส่วนลด
                <span class="text-muted fw-normal small">(เลือก "ไม่มีโปรโมชัน" หากไม่ต้องการลดราคา)</span></div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="discount_type">ประเภทส่วนลด</label>
                    <select name="discount_type" id="discount_type" class="form-select">
                        <option value="">ไม่มีโปรโมชัน</option>
                        <option value="percent" @selected(old('discount_type', $product->discount_type) === 'percent')>ลดเป็นเปอร์เซ็นต์ (%)</option>
                        <option value="amount" @selected(old('discount_type', $product->discount_type) === 'amount')>ลดเป็นจำนวนเงิน (บาท)</option>
                    </select>
                </div>
                <div class="col-md-4 promo-field">
                    <label class="form-label" for="discount_value">ส่วนลด <span id="discountUnit" class="text-muted"></span> <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="discount_value" id="discount_value" value="{{ old('discount_value', $product->discount_value) }}" class="form-control @error('discount_value') is-invalid @enderror">
                </div>
                <div class="col-md-4 promo-field">
                    <label class="form-label" for="promo_name">ชื่อโปรโมชัน</label>
                    <input type="text" name="promo_name" id="promo_name" value="{{ old('promo_name', $product->promo_name) }}" maxlength="100" class="form-control" placeholder="เช่น ลดล้างสต็อก">
                </div>
                <div class="col-md-6 promo-field">
                    <label class="form-label" for="promo_start">วันที่เริ่มโปรโมชัน <span class="text-muted small">(เว้นว่าง = เริ่มทันที)</span></label>
                    <input type="date" name="promo_start" id="promo_start" value="{{ old('promo_start', $product->promo_start ? $product->promo_start->format('Y-m-d') : '') }}" class="form-control">
                </div>
                <div class="col-md-6 promo-field">
                    <label class="form-label" for="promo_end">วันที่สิ้นสุดโปรโมชัน <span class="text-muted small">(เว้นว่าง = ไม่มีวันหมดอายุ)</span></label>
                    <input type="date" name="promo_end" id="promo_end" value="{{ old('promo_end', $product->promo_end ? $product->promo_end->format('Y-m-d') : '') }}" class="form-control">
                </div>
                <div class="col-12 promo-field">
                    <div class="small" id="promoPreview"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <label class="form-label">รูปสินค้า (jpg, png ไม่เกิน 2 MB)</label>
        <input type="file" name="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
        @if($product->image_url)
            <div class="mt-2 d-flex align-items-center gap-2">
                <img src="{{ $product->image_url }}" class="thumb" alt="">
                <small class="text-muted">รูปปัจจุบัน (อัปโหลดใหม่เพื่อเปลี่ยน)</small>
            </div>
        @endif
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch fs-6">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" role="switch" name="is_active" value="1" id="is_active" @checked(old('is_active', $product->is_active ? 1 : 0))>
            <label class="form-check-label" for="is_active">เปิดขายสินค้านี้</label>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2 justify-content-center">
    <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg me-1"></i> บันทึก</button>
    <a href="{{ route('products.index') }}" class="btn btn-light border">ยกเลิก</a>
</div>

@push('scripts')
<script>
(function () {
    var type = document.getElementById('discount_type');
    var val = document.getElementById('discount_value');
    var price = document.querySelector('input[name="price"]');
    var unit = document.getElementById('discountUnit');
    var prev = document.getElementById('promoPreview');
    var fields = document.querySelectorAll('.promo-field');
    function fmt(n) { return n.toLocaleString('th-TH', {minimumFractionDigits: 2, maximumFractionDigits: 2}); }
    function sync() {
        var on = type.value !== '';
        fields.forEach(function (f) { f.style.display = on ? '' : 'none'; });
        val.required = on;
        unit.textContent = type.value === 'percent' ? '(%)' : (type.value === 'amount' ? '(บาท)' : '');
        if (!on) { prev.textContent = ''; return; }
        var p = parseFloat(price.value) || 0, v = parseFloat(val.value) || 0;
        var f = type.value === 'percent' ? p * (1 - Math.min(v, 100) / 100) : p - v;
        f = Math.max(f, 0);
        prev.innerHTML = v > 0 ? 'ราคาหลังลด: <b class="text-danger">฿' + fmt(f) + '</b> (ราคาเดิม ฿' + fmt(p) + ')' : '';
    }
    [type, val, price].forEach(function (el) { el.addEventListener('input', sync); el.addEventListener('change', sync); });
    sync();
})();
</script>
@endpush
