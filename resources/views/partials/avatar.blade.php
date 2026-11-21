{{-- รูปโปรไฟล์: ถ้าไม่มีรูปจะแสดงอักษรตัวแรกของชื่อ  ใช้: @include('partials.avatar', ['user' => $u, 'size' => 40]) --}}
@php($size = $size ?? 40)
<div class="avatar" style="width:{{ $size }}px;height:{{ $size }}px;font-size:{{ round($size * 0.42) }}px">
    @if($user->avatar_url)
        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
    @else
        {{ $user->initial() }}
    @endif
</div>
