@use('App\Enums\ReportTypeEnum')
@extends('emails.master')
@section('content')
<div>
    <div>Chào {{env('ADMIN_FULL_NAME')}} !</div>
    <div>Bài đăng dưới đây có dấu hiệu vi phạm.</div>
    <br>
    <div>Người báo cáo: {{$reporterFullName}}</div>
    <div>Email: {{$reporterEmail}}</div>
    <div>SĐT: {{$reporterTel}}</div>
    <br>
    <div>ID bài đăng: {{$productId}}</div>
    <div>ID chủ bài đăng: {{$productOwnerId}}</div>
    <div>Tiêu đề bài đăng: {{$productTitle}}</div>
    <div>Ngày đăng: {{$productPostedAt}}</div>
    <div>Loại vi phạm: <b>{{ReportTypeEnum::tryFrom($reportType)->getStringValue()}}</b></div>
    <div>Mô tả chi tiết:<div>
    <div
        style="padding:15px 20px;border-radius:5px;background:rgb(235, 235, 235);"
    >{{$description}}</div>
</div>
@endsection
