<?php
namespace Labspace\AuthApi\Services;

use Illuminate\Http\Request;
use Exception;
use Labspace\AuthApi\Services\SmsService;
use Labspace\AuthApi\Services\MemberService;
use Labspace\AuthApi\Exceptions\SmsSendFailedException;
use Labspace\AuthApi\Exceptions\SmsVerifyFailedException;
use Labspace\AuthApi\Exceptions\UserNotFoundException;
use Hash;
use Log;

class SmsConfirmationService {

	//簡訊驗證
    public function __construct()
    {
        
        
    }

	
	//發送驗證碼
	static public function sendVerifyCode($phone)
	{
		$code = rand(1000,9999);

		$path = config('labspace-auth-api.sms_confirmation_model');
        $sms_confirmation_model = new $path();

        //把之前的都設成失效
        $sms_confirmation_model->where('phone',$phone)->update([
        	'status' => 2
        ]);

        $sms_confirmation = $sms_confirmation_model->create([
        	'phone' => $phone,
        	'token' => bcrypt($code)
        ]);
        $status = 1;

        if(config('labspace-auth-api.sms.code_to_log')){
        	Log::info($code);
        }
        

        $send_result = SmsService::sendSmsMessage($phone, '['.config('labspace-auth-api.sms.from_name').'] 簡訊驗證碼：'.$code.'。請於10分鐘內完成驗證，請勿代收簡訊防詐騙');
        if(!$send_result['status']){
        	$status = 0;
        } 
        $sms_confirmation->update([
        	'status' => $status,
        	'msg' => $send_result['detail']
        ]);

        if($status == 0){
        	throw new SmsSendFailedException();
        }


	}

	//驗證驗證碼
	static public function verifyCode($phone,$code)
	{
		$path = config('labspace-auth-api.sms_confirmation_model');
        $sms_confirmation_model = new $path();

        $item = $sms_confirmation_model->where('phone',$phone)->where('status',1)->first();
        if(!$item){
            throw new SmsVerifyFailedException();
        }

        $ten_minute_before =strtotime('now') - (60*10);
        //過期
        if(strtotime($item->created_at) < $ten_minute_before){
            $item->update(['status' => 2]);
            throw new SmsVerifyFailedException();
        }

        //驗證
        if (!Hash::check($code, $item->token)) {
            $item->update(['status' => 2]);
            throw new SmsVerifyFailedException();
        }

	}

	//依照手機清空驗證碼
	static public function deleteCodeByPhone($phone)
	{
		$path = config('labspace-auth-api.sms_confirmation_model');
        $sms_confirmation_model = new $path();        
        $sms_confirmation_model->where('phone',$phone)->delete();
	}

	//依照手機清空驗證碼
	static public function deleteCodeByUsername($username)
	{

        $phone = self::findPhoneByUsername($username);
        
		$path = config('labspace-auth-api.sms_confirmation_model');
        $sms_confirmation_model = new $path();        
        $sms_confirmation_model->where('phone',$phone)->delete();
	}

	 /**
     * 依照使用者帳號取得手機
     * @return void
     */
    static public function findPhoneByUsername($username)
    {
        $path = config('labspace-auth-api.user_model');
        $user_model = new $path();
        $user = $user_model->where('username',$username)->first();
        if(!$user){
             throw new UserNotFoundException();
        }

        $path = config('labspace-auth-api.member_model');
        $member_model = new $path();

        $member = $member_model->find($user->id);
        if(!$member){
            throw new UserNotFoundException();
        }

        $phone = $member->phone;
        
        return $phone;

    }



}