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

    'email_confirm_code_check' => true, //是否檢查email 是否認證 


];
