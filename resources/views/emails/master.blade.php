<div
    style="background-color: #f9f9f9; width: 100%;"
>
    <div>
        <div
            style="width: 100%; text-align: center;padding-top:20px;padding-bottom:10px;"
        >
            <img
                style="width: 120px;"
                src="https://trogiup.mogi.vn/wp-content/uploads/2019/04/mogi-1.png"
                alt="logo"
            >
        </div>
    </div>
    <div
        style="max-width:650px;min-width:320px;margin:auto;background: white;border-radius:10px;padding:35px;font-size:16px;font-family:Roboto,Arial;line-height:1.7;"
    >
        @yield('content')
        <div style="padding-top: 20px;">Chúc bạn có nhiều sức khỏe và vui vẻ !</div>
        <div>Trân trọng !</div>
    </div>
    <div
        style="font-size: 13px;color:#99aab5;text-align:center;padding:15px 0 20px 0;"
    >
        <div>Mail này được gởi bởi {{env('APP_NAME')}}</div>
        <div>Xin vui lòng không trả lời qua mail này.</div>
        <div>Website: {{env('APP_URL')}}.</div>
    </div>
</div>
