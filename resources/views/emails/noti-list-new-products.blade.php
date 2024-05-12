@use('Carbon\Carbon')
@extends('emails.master')
@section('content')
<div>
    <div>Chào <b>{{$user->full_name}}</b> !</div>
    <div
        style="color:#636363;font-size: 20px;font-weight: bold;"
    >Tổng hợp tin đăng dành cho bạn.</div>
    <div
        style="font-size: 15px; color:rgb(85, 85, 85); line-height:1.3;"
    >Dưới đây là tổng hợp 10 tin đăng mới nhất có thể bạn đang quan tâm. {{env('APP_NAME')}} cố gắng tìm những tin phù hợp & hữu ích nhất dành cho bạn.</div>
    <div
        style="padding: 15px 0;"
    >
        @foreach($listNewProducts as $product)
        <a
            href="{{env('FRONTEND_URL')}}"
            style="padding:20px 0; border-bottom: 0.5px solid rgb(210, 210, 210); display:flex; text-decoration:none;"
        >
            <div
                style="padding-right: 10px;"
            >
                <div
                    style="width: 150px; height: 100px;"
                >
                    <img
                        style="width: 100%; height: 100%; object-fit:cover;"
                        src="{{env('APP_URL')}}/{{$product->thumb_url}}"
                        alt="product_image"
                    >
                </div>
            </div>
            <div
                style="width:75%;"
            >
                <div
                    style="font-weight: bold;font-size:16px; color:rgb(54, 54, 54);"
                >{{$product->title}}</div>
                <div
                    style="font-size:14px;color:rgb(101, 101, 101);"
                >{{$product->ward_name}}, {{$product->district_name}}, {{$product->province_name}}</div>
                <div
                    style="color: rgb(0, 191, 89);font-size:15px; font-weight:bold;"
                >{{number_format($product->price)}}đ</div>
                @php
                    $carbonParsed = Carbon::parse($product->posted_at);
                    $postedAtStr = $carbonParsed->format('H') . 'h' . $carbonParsed->format('i') . " Ngày " . $carbonParsed->format('d/m/Y');   
                @endphp
                <div
                    style="font-size: 14px;color:rgb(78, 78, 78);"
                >Đăng lúc: {{$postedAtStr}}</div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endsection
