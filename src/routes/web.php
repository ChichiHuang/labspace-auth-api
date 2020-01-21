 <?php

Route::group(['prefix' => 'lab/api','middleware' => ['cors']],function (){
	
	Route::group(['prefix' => 'auth'],function (){
		Route::get('labspace', function(){
		    return 'Hello Labspace package auth api';
		});
		Route::post('/login',['uses'=> 'Labspace\AuthApi\Controllers\AuthController@login'] );
		Route::post('/social-login',['uses'=> 'Labspace\AuthApi\Controllers\AuthController@socialLogin'] );
		Route::post('/logout',['uses'=> 'Labspace\AuthApi\Controllers\AuthController@logout'] );
	});
	Route::group(['prefix' => 'user','middleware' => ['jwt:admin|member']],function (){
	    Route::get('/me','Labspace\AuthApi\Controllers\AuthController@getUser');
	});
});
?>