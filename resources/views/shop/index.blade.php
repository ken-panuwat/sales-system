@extends('layouts.app')
@section('title', 'เลือกซื้อสินค้า')

@section('content')
@php
    $cat = request('category');
    $promo = request()->boolean('promo');
    $qs = fn (array $o) => route('shop.index', array_filter(array_merge(['q' => request('q')], $o)));
@endphp

<form method="GET" class="mb-3">
    @if($cat)<input type="hidden" name="category" value="{{ $cat }}">@endif
    @if($promo)<input type="hidden" name="promo" value="1">@endif
    <div class="input-group" style="max-width:480px">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="ค้นหาสินค้า...">
        <button class="btn btn-primary" type="submit">ค้นหา</button>
    </div>
</form>

<div data-live="shop">
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="{{ $qs([]) }}" class="chip {{ ! $cat && ! $promo ? 'active' : '' }}"><i class="bi bi-grid"></i> ทั้งหมด <span class="cnt">{{ $totalCount }}</span></a>
    <a href="{{ $qs(['promo' => 1]) }}" class="chip {{ $promo ? 'active' : '' }}"><i class="bi bi-megaphone"></i> โปรโมชัน <span class="cnt">{{ $promoCount }}</span></a>
    @foreach($categories as $name => $count)
        <a href="{{ $qs(['category' => $name]) }}" class="chip {{ $cat === $name ? 'active' : '' }}">{{ $name }} <span class="cnt">{{ $count }}</span></a>
    @endforeach
</div>

<div class="row g-3">
    @forelse($products as $p)
        <div class="col-6 col-md-4 col-xl-3">
            <div class="product-card">
                @if($p->is_on_sale)<span class="promo-ribbon">ลด {{ $p->discount_percent }}%</span>@endif
                <div class="product-img">
                    @if($p->image_url)
                        <img src="{{ $p->image_url }}" alt="{{ $p->name }}">
                    @else
                        <i class="bi bi-bag-heart"></i>
                    @endif
                </div>
                <div class="product-body">
                    @if($p->category)<div class="product-cat">{{ $p->category }}</div>@endif
                    <a href="{{ route('shop.show', $p) }}" class="product-name text-decoration-none text-dark stretched-link">{{ $p->name }}</a>
                    @if($p->description)<div class="small text-muted mb-2">{{ \Illuminate\Support\Str::limit($p->description, 60) }}</div>@endif
                    <div class="mt-auto">
                        <small class="{{ $p->stock <= 5 ? 'text-danger' : 'text-muted' }}">เหลือ {{ $p->stock }}</small>
                        <form method="POST" action="{{ route('cart.add', $p) }}" class="actions d-flex align-items-center justify-content-between gap-2 mt-1">
                            @csrf
                            <div>
                                @if($p->is_on_sale)
                                    <div class="price-old">฿{{ number_format($p->price, 2) }}</div>
                                    <div class="product-price price-sale">฿{{ number_format($p->final_price, 2) }}</div>
                                @else
                                    <div class="product-price">฿{{ number_format($p->price, 2) }}</div>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <div class="qty-stepper">
                                    <button type="button" class="qty-btn" data-d="-1">−</button>
                                    <input type="number" name="quantity" value="1" min="1" max="{{ $p->stock }}">
                                    <button type="button" class="qty-btn" data-d="1">+</button>
                                </div>
                                <button class="btn btn-primary btn-sm" type="submit" title="ใส่ตะกร้า"><i class="bi bi-cart-plus"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="card-x"><div class="empty-state"><i class="bi bi-search"></i><div>ไม่พบสินค้า</div></div></div></div>
    @endforelse
</div>

@if($products->hasPages())
    <div class="mt-4">{{ $products->links() }}</div>
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
