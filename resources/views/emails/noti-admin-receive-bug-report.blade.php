@use('App\Enums\ReportTypeEnum')
@extends('emails.master')
@section('content')
<div>
    <div>Chào {{env('ADMIN_FULL_NAME')}} !</div>
    <div>Đã nhận được báo cáo lỗi.</div>
    <br>
    <div><b>Người báo cáo</b></div>
    <div>Họ tên: {{$bugData->full_name}}</div>
    <div>Email: {{$bugData->email}}</div>
    <div>Địa chỉ IP: {{$bugData->ip_address}}</div>
    <br>
    <div><b>Nội dung báo cáo</b></div>
    <div style="padding-bottom: 15px;">Mô tả chi tiết:</div>
    <div style="padding:15px 20px;border-radius:5px;background:rgb(244, 244, 244);">{{$bugData->description}}</div>

    @if (count($bugData->bugReportImages) > 0)
    <div style="padding-top: 15px;"><b>Ảnh mô tả</b></div>
    <div>
        @foreach ($bugData->bugReportImages as $image)
        <div>
            <img
                src="{{env('APP_URL')}}/{{$image->url}}"
                alt=""
                style="width: 100%;"
            >
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
