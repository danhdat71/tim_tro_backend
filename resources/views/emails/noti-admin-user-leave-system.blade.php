@use('App\Enums\UserTypeEnum')
@extends('emails.master')
@section('content')
<div>
    <div>Chào {{env('ADMIN_FULL_NAME')}} !</div>
    <div>Có thành viên đã rời hệ thống.</div>
    <br>
    <div><b>Thông tin thành viên</b></div>
    <div>Họ tên: {{$user->full_name}}</div>
    <div>Email: {{$user->email}}</div>
    <div>Loại tài khoản: {{UserTypeEnum::tryFrom($user->user_type)->getStringValue()}}</div>
    <br>
    @if ($user->leave_reason != "")
    <div><b>Lý do rời</b></div>
    <div
        style="padding:15px 20px;border-radius:5px;background:rgb(244, 244, 244);"
    >{{$user->leave_reason}}</div>
    @endif
</div>
@endsection
