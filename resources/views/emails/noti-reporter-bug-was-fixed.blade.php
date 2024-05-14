@use('App\Enums\ReportTypeEnum')
@extends('emails.master')
@section('content')
<div>
    <div>Chào <b>{{$bugData->full_name}}</b> !</div>
    <div>Trước tiên <b>{{env('APP_NAME')}}</b> cảm ơn bạn đã báo cáo lỗi hệ thống này và đã khắc phục lỗi. <b>{{env('APP_NAME')}}</b> xin lỗi vì sự bất tiện này và sẽ cố gắng cải thiện chất lượng trải nghiệm tốt hơn.</div>
    <br>
    <div><b>Lịch sử báo cáo</b></div>
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
