<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Labspace jwt api 驗證設定
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

    'user_model' => 'App\Models\User', //user model 位置

    'reset_password_model' => 'App\Models\ResetPassword', //密碼重置 位置

    'reset_password_url' => env('http://sample.com/reset-password','http://sample.com'), //密碼重置畫面連結

    'email_confirm_code_check' => true, //是否檢查email 是否認證 

    'confirm_status_check' => false, //是否檢查該帳號被後台審核成功

    'logout_device_token_clear' => false, //登出時是否清空app裝置token

];
