@extends('layouts.app')
@section('title', 'แก้ไขสินค้า')

@section('content')
<div class="card-x">
    <div class="card-head"><h2><i class="bi bi-pencil-square me-2 text-success"></i>แก้ไข: {{ $product->name }}</h2></div>
    <div class="card-body-x">
        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('products._fields')
        </form>
    </div>
</div>
@endsection
