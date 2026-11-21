@extends('layouts.app')
@section('title', $product->name)

@section('content')
<a href="{{ route('shop.index') }}" class="btn btn-light border btn-sm mb-3"><i class="bi bi-arrow-left"></i> กลับไปเลือกสินค้า</a>

<div data-live="shopdetail">
<div class="row g-4">
    <div class="col-md-5">
        <div class="detail-img">
            @if($product->is_on_sale)<span class="promo-ribbon">ลด {{ $product->discount_percent }}%</span>@endif
            @if($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
            @else
                <i class="bi bi-bag-heart" style="font-size:5rem;color:var(--g-500)"></i>
            @endif
        </div>
    </div>
    <div class="col-md-7">
        <div class="card-x"><div class="card-body-x">
            @if($product->category)<div class="product-cat">{{ $product->category }}</div>@endif
            <h2 class="h3 mb-1">{{ $product->name }}</h2>
            <div class="text-muted small mb-3">รหัสสินค้า {{ $product->sku }}</div>

            @if($product->is_on_sale)
                <div class="price-old">฿{{ number_format($product->price, 2) }}</div>
                <div class="product-price price-sale fs-2">฿{{ number_format($product->final_price, 2) }}</div>
                <div class="small text-danger mb-2">
                    <i class="bi bi-megaphone"></i> {{ $product->promo_name ?: 'โปรโมชันพิเศษ' }}
                    @if($product->promo_end) · ถึงวันที่ {{ $product->promo_end->format('d/m/') }}{{ $product->promo_end->year + 543 }}@endif
                </div>
            @else
                <div class="product-price fs-2">฿{{ number_format($product->price, 2) }}</div>
            @endif

            @if($product->description)<p class="mt-3" style="white-space:pre-line">{{ $product->description }}</p>@endif

            @if($product->stock > 0)
                <div class="mb-3 {{ $product->stock <= 5 ? 'text-danger' : 'text-muted' }}">คงเหลือ {{ $product->stock }} ชิ้น</div>
                <form method="POST" action="{{ route('cart.add', $product) }}" class="d-flex align-items-center gap-2">
                    @csrf
                    <div class="qty-stepper">
                        <button type="button" class="qty-btn" data-d="-1">−</button>
                        <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}">
                        <button type="button" class="qty-btn" data-d="1">+</button>
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-cart-plus me-1"></i> ใส่ตะกร้า</button>
                </form>
            @else
                <span class="badge badge-soft badge-soft-danger">สินค้าหมด</span>
            @endif
        </div></div>
    </div>
</div>

@if($related->isNotEmpty())
    <h3 class="h5 mt-4 mb-3">สินค้าที่เกี่ยวข้อง</h3>
    <div class="row g-3">
        @foreach($related as $p)
            <div class="col-6 col-md-3">
                <div class="product-card">
                    @if($p->is_on_sale)<span class="promo-ribbon">ลด {{ $p->discount_percent }}%</span>@endif
                    <div class="product-img">@if($p->image_url)<img src="{{ $p->image_url }}" alt="">@else<i class="bi bi-bag-heart"></i>@endif</div>
                    <div class="product-body">
                        <a href="{{ route('shop.show', $p) }}" class="product-name text-decoration-none text-dark stretched-link">{{ $p->name }}</a>
                        <div class="product-price {{ $p->is_on_sale ? 'price-sale' : '' }}">฿{{ number_format($p->final_price, 2) }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('click', function (e) {
    var b = e.target.closest('.qty-btn'); if (!b) return;
    var i = b.parentElement.querySelector('input');
    var v = (parseInt(i.value) || 1) + parseInt(b.dataset.d);
    var max = parseInt(i.max) || 9999;
    i.value = Math.min(Math.max(v, 1), max);
});
</script>
@endpush
