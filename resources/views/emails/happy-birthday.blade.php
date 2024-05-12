@extends('emails.master')
@section('content')
<div>
    <img
        src="{{env('APP_URL')}}/assets/imgs/happy-birthday-header.jpg"
        alt=""
        style="width: 100%"
    >
    <div style="padding: 20px; 0">
        <div>Chúc mừng sinh nhật bạn <b>{{$user->full_name}}</b> !</div>
        <div>Hôm nay là một ngày đặc biệt và đáng nhớ, đánh dấu một năm nữa trôi qua trong cuộc đời bạn. Chúc mừng bạn đã thêm một tuổi mới, tràn đầy niềm vui và thành công!</div>
        <div>Nhân dịp này, {{env('APP_NAME')}} muốn gửi lời chúc tốt đẹp nhất đến bạn. Mong rằng tuổi mới sẽ mang đến cho bạn nhiều điều tuyệt vời, niềm vui, sức khỏe và thành công trong mọi lĩnh vực của cuộc sống.</div><br>
        <div>Trân trọng,</div>
        <div>{{env('APP_NAME')}} team.</div>
    </div>
</div>
@endsection
