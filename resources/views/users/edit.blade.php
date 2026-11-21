@extends('layouts.app')
@section('title', 'แก้ไขผู้ใช้')

@section('content')
<div class="card-x">
    <div class="card-head"><h2><i class="bi bi-pencil-square me-2 text-success"></i>แก้ไข: {{ $user->name }}</h2></div>
    <div class="card-body-x">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf @method('PUT')
            @include('users._fields')
        </form>
    </div>
</div>
@endsection
