@extends('layouts.app')
@section('title', 'เพิ่มสินค้าใหม่')

@section('content')
<div class="card-x">
    <div class="card-head"><h2><i class="bi bi-plus-circle me-2 text-success"></i>ข้อมูลสินค้า</h2></div>
    <div class="card-body-x">
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @csrf
            @include('products._fields')
        </form>
    </div>
</div>
@endsection
