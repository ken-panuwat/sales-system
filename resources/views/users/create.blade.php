@extends('layouts.app')
@section('title', 'เพิ่มผู้ใช้ใหม่')

@section('content')
<div class="card-x">
    <div class="card-head"><h2><i class="bi bi-person-plus me-2 text-success"></i>ข้อมูลผู้ใช้</h2></div>
    <div class="card-body-x">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            @include('users._fields')
        </form>
    </div>
</div>
@endsection
