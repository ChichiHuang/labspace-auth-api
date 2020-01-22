<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <p>Hi, {{ $name }} </p><br/>
        
        <p>請點擊以下連結以開通帳號</p><br/>
        <div>
            <a href="{{ route('lab.auth-api.verify') }}?id={{ $id }}&confirm_code={{ $confirm_code }}" target="_blank">點我開通</a>

        </div><br><br>
        
        <p>系統自動通知，請勿回覆</p>
    </body>
</html>