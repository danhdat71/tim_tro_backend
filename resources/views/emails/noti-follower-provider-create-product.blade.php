@use('Carbon\Carbon')
@extends('emails.master')
@section('content')
<div>
    <div>Chào <b>{{$finderName}}</b> !</div>
    <div>Có bài đăng mới nhất mà bạn đang quan tâm. {{env('APP_NAME')}} cố gắng tìm cho bạn những bài đăng hữu ích nhất.</div>
    <div style="padding-top: 15px;padding-bottom:15px">
        <div
            style="font-size: 17px;font-weight:bold;"
        >{{$product->title}}</div>
        <div
            style="display:flex; flex-wrap: wrap; padding-top: 4px; padding-bottom: 4px;"
        >
            @foreach ($product->productImages as $productImage)
            <div
                style="aspect-ratio:5/5; padding: 4px;"
            >
                <img
                    style="width: 100%;height:100%;object-fit:cover;"
                    src="{{env('APP_URL')}}/{{$productImage->thumb_url}}"
                />
            </div>
            @endforeach
        </div>
        <div>{{$product->detail_address}} - {{$product->acreage}}m<sup>2</sup></div>
        <div
            style="font-size: 20px; font-weight: bold; color: rgb(0, 191, 89);"
        >{{number_format($product->price)}}đ</div>
        <a
            href="{{env('FRONTEND_URL')}}"
            style="padding-top: 15px; padding-bottom: 15px; display: flex; text-decoration: none;"
        >
            <div
                style=""
            >
                <div style="width: 70px;height:70px;border-radius:9999px;overflow:hidden;">
                    <img style="width:100%;height:100%;object-fit:cover;" src="{{env('APP_URL')}}/{{$provider->avatar}}"/>
                </div>
            </div>
            <div style="width: 80%; padding-left: 15px;">
                <div style="font-weight: bold;padding-top:20px;color: rgb(47, 47, 47);">{{$provider->full_name}}</div>
            </div>
        </a>
        @php
            $carbonParsed = Carbon::parse($product->posted_at);
            $postedAtStr = $carbonParsed->format('H') . 'h' . $carbonParsed->format('i') . " Ngày " . $carbonParsed->format('d/m/Y');   
        @endphp
        <div style="font-size: 14px;">Đăng lúc: {{$postedAtStr}}</div>
        <div style="text-align:center;padding-top: 20px; padding-bottom: 20px;">
            <a
                style="display:inline-block; padding: 9px 30px;background: rgb(0, 191, 89); color: white; border-radius: 4px; text-decoration: none;"
                href="{{env('FRONTEND_URL')}}"
            >Xem chi tiết</a>
        </div>
    </div>
</div>
@endsection
