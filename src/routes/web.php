 <?php

Route::group(['prefix' => 'lab/api','middleware' => ['cors']],function (){
	
	Route::group(['prefix' => 'auth'],function (){
		Route::get('labspace', function(){
		    return 'Hello Labspace package auth api';
		});

		Route::get('/verify',['as' => 'lab.auth-api.verify','uses'=> 'Labspace\AuthApi\Controllers\ConfirmationController@verify'] );
		Route::post('/forget-password',['uses'=> 'Labspace\AuthApi\Controllers\ForgetPasswordController@process'] );
		Route::post('/send-password-reset-link',['uses'=> 'Labspace\AuthApi\Controllers\ForgetPasswordController@sendPasswordResetLink'] );
		Route::post('/change-password-by-reset-link',['uses'=> 'Labspace\AuthApi\Controllers\ForgetPasswordController@changePasswordByResetLink'] );


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