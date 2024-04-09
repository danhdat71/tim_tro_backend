@extends('emails.master')
@section('content')
<div>
    <div>Xin chào <b>{{$fullname}}</b> !</div>
    <br>
    <div><b>{{env('APP_NAME')}}</b> nhận được yêu cầu thay đổi mật khẩu từ bạn. Vui lòng không cung cấp mã bên dưới cho bất cứ bên nào để tránh gây mất bảo mật thông tin bạn nhé !</div>
    <div>Mã này có hiệu lực {{config('auth.otp_expired')}} phút kể từ lúc nhận được mail.</div>
    <div>Mã của bạn là:</div>
    <div style="font-size: 35px; font-weight: bold; letter-spacing: 10px; text-align: center;padding:35px 0;">{{$otp}}</div>
</div>
@endsection
