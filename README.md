參考網站：
https://pusher.com/tutorials/publish-laravel-packagist
http://www.alvinchen.club/2018/05/04/%E6%A8%A1%E7%B5%84%E5%8C%96-%E5%A5%97%E4%BB%B6-%E9%96%8B%E7%99%BC%E8%87%AA%E5%B7%B1%E7%9A%84package/

母專案要做的事情

STEP.0 

jwt先安裝設定好

https://jwt-auth.readthedocs.io/en/develop/

"tymon/jwt-auth": "dev-develop"

User model請先跟jwt做連結、config也要設定，請參考：

https://jwt-auth.readthedocs.io/en/develop/quick-start/

============

STEP.1

安裝套件

composer require labspace/auth-api


============


STEP.2

到config/app.php 的providers加上

Labspace\AuthApi\AuthApiServiceProvider::class,

============

STEP.3

到app/Http/Kernal

把專屬jwt登入驗證的middleware新增到routeMiddleware

'jwt' => \Labspace\AuthApi\Middleware\AuthJWT::class, //labsapce jwt


如果沒有cors設定也可以加進去

'cors' => \Labspace\AuthApi\Middleware\CORS::class, //labsapce cross-domain

============

STEP.4

php artisan vendor:publish --tag=config

 會新增專屬config檔
 labspace-auth-api.php
 裡面可以設定登入user model的位置


====================

使用說明

[登入 - POST]
username:帳號(必填)
password:密碼(必填)
role:身份  （選填 admin member 若user model 沒有區分也可以不加）

http://[server_url]/lab/api/auth/login?username=test&password=123456&role=member

{
    "status": true,
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8yMTAuNjUuMTMyLjc3XC9zc2xfcHJvamVjdFwvcHVibGljXC9sYWJcL2FwaVwvYXV0aFwvbG9naW4iLCJpYXQiOjE1NzcwNzM5MTcsImV4cCI6MTU3NzA3NzUxNywibmJmIjoxNTc3MDczOTE3LCJqdGkiOiJ5UmtYT3lRb05uVm5kNGVnIiwic3ViIjoxLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.i6sWvnU-20QYHxuPZrl4uGgSLbr-BnUXSughLQx6Ys8",
        "user_info": {
            "email": "test@gmail.com",
            "name": "王小明",
            "avatar": "",
            "id": 1,
            "role": "member"
        }
    },
    "success_code": "SUCCESS"
}



[取得基本資料 - GET]

token:使用者token

http://[server_url]/lab/api/user/me?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8yMTAuNjUuMTMyLjc3XC9zc2xfcHJvamVjdFwvcHVibGljXC9sYWJcL2FwaVwvYXV0aFwvbG9naW4iLCJpYXQiOjE1NzcwNzM3MDMsImV4cCI6MTU3NzA3NzMwMywibmJmIjoxNTc3MDczNzAzLCJqdGkiOiJJUkFsbkpPZ3hKd1MwTHhyIiwic3ViIjoxLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.LD44cHoxwDfzXYm0J3r4VTTfmhM-ByYQ-yL2i2jT-qU


{
    "status": true,
    "data": {
        "user_info": {
            "email": "test@gmail.com",
            "name": "王小明",
            "avatar": "",
            "id": 1,
            "role": "member"
        }
    },
    "success_code": "SUCCESS"
}



[登出 - POST]

token:使用者token

http://[server_url]/lab/api/auth/logout?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8yMTAuNjUuMTMyLjc3XC9zc2xfcHJvamVjdFwvcHVibGljXC9sYWJcL2FwaVwvYXV0aFwvbG9naW4iLCJpYXQiOjE1NzcwNzM3MDMsImV4cCI6MTU3NzA3NzMwMywibmJmIjoxNTc3MDczNzAzLCJqdGkiOiJJUkFsbkpPZ3hKd1MwTHhyIiwic3ViIjoxLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.LD44cHoxwDfzXYm0J3r4VTTfmhM-ByYQ-yL2i2jT-qU

{
    "status": true,
    "data": null
}




[jwt middleware用法]

route middleware

jwt => 不分身份，驗證即可
jwt:member => 只能會員
jwt:admin => 只能管理員
jwt:member|admin =>管理員、會員都可以



[社群登入]

step.1

migration新增

php artisan vendor:publish --tag=migration-social


使用說明

[登入 - POST]
social_id:社群id
provider:社群代碼 fb google
role:身份  
email:信箱

http://[server_url]/lab/api/auth/social-login?role=member&social_id=test&provider=fb&email=test@gmail.com
{
    "status": true,
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8yMTAuNjUuMTMyLjc3XC9zc2xfcHJvamVjdFwvcHVibGljXC9sYWJcL2FwaVwvYXV0aFwvbG9naW4iLCJpYXQiOjE1NzcwNzM5MTcsImV4cCI6MTU3NzA3NzUxNywibmJmIjoxNTc3MDczOTE3LCJqdGkiOiJ5UmtYT3lRb05uVm5kNGVnIiwic3ViIjoxLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.i6sWvnU-20QYHxuPZrl4uGgSLbr-BnUXSughLQx6Ys8",
        "user_info": {
            "email": "test@gmail.com",
            "name": "王小明",
            "avatar": "",
            "id": 1,
            "role": "member"
        }
    },
    "success_code": "SUCCESS"
}

若帳號不存在回傳要求註冊

{
    "status": true,
    "success_code": "PLEASE_REGISTER"
}
