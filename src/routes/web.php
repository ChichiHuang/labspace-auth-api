<?php

Route::group(['prefix' => 'lab/api','middleware' => ['cors']],function (){
	
	Route::group(['prefix' => 'auth'],function (){
	
		Route::get('/verify',['as' => 'lab.auth-api.verify','uses'=> 'Labspace\AuthApi\Controllers\ConfirmationController@verify'] );
		Route::post('/forget-password',['uses'=> 'Labspace\AuthApi\Controllers\ForgetPasswordController@process'] );
		Route::post('/send-password-reset-link',['uses'=> 'Labspace\AuthApi\Controllers\ForgetPasswordController@sendPasswordResetLink'] );
		Route::post('/change-password-by-reset-link',['uses'=> 'Labspace\AuthApi\Controllers\ForgetPasswordController@changePasswordByResetLink'] );
		Route::post('/change-password-by-sms-code',['uses'=> 'Labspace\AuthApi\Controllers\ForgetPasswordController@changePasswordBySmsCode'] );


		//寄簡訊
		Route::post('/sms-send-code/phone',['uses'=> 'Labspace\AuthApi\Controllers\SmsSendController@sendCodeByPhone'] );
		Route::post('/sms-send-code/username',['uses'=> 'Labspace\AuthApi\Controllers\SmsSendController@sendCodeByUsername'] );
		Route::post('/sms-send-code/verify',['uses'=> 'Labspace\AuthApi\Controllers\SmsSendController@codeVerify'] );


		Route::post('/login',['uses'=> 'Labspace\AuthApi\Controllers\AuthController@login'] );
		Route::post('/social-login',['uses'=> 'Labspace\AuthApi\Controllers\AuthController@socialLogin'] );
		Route::post('/logout',['uses'=> 'Labspace\AuthApi\Controllers\AuthController@logout'] );
	});
	Route::group(['middleware' => ['web_jwt']],function (){
		Route::get('/user/check','Labspace\AuthApi\Controllers\AuthController@check');
	});
	Route::group(['prefix' => 'user','middleware' => ['jwt']],function (){
		//Route::get('/check','Labspace\AuthApi\Controllers\AuthController@check');
	    Route::get('/me','Labspace\AuthApi\Controllers\AuthController@getUser');
	});
});
?>