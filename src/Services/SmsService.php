<?php
namespace Labspace\AuthApi\Services;

use Exception;
use Log;

class SmsService {

	//寄簡訊(三竹)
	static public function sendSmsMessage($phone, $msg)
	{
		$username =  config('labspace-auth-api.sms.mitake.username');
		$password =config('labspace-auth-api.sms.mitake.password');
		$msg = mb_convert_encoding($msg,'big5','utf-8');
        $sms_url = config('labspace-auth-api.sms.mitake.url').'?username='.$username.'&password='.$password.'&dstaddr='.$phone.'&DestName='.env('WEBSITE_NAME').'&dlvtime=10&vldtime=660&smbody='.$msg;
      	
        $result_sms = file_get_contents($sms_url);
        $colume = explode("\r\n", $result_sms);
        $status_code = explode("=",$colume[1]);

        $result_sms = iconv("big5","UTF-8",$result_sms);
        switch ($status_code[1]) {
        	case '0':
        		return  ['status' =>true ,'detail' => $result_sms];
        		break;
        	case '1':
        		return  ['status' =>true ,'detail' => $result_sms];
        		break;
        	case '2':
        		return  ['status' =>true ,'detail' => $result_sms];
        		break;
        	case '3':
        		return  ['status' =>true ,'detail' => $result_sms];
        		break;
        	case '4':
        		return  ['status' =>true ,'detail' => $result_sms];
        		break;
        	default:
        		return  ['status' =>false ,'detail' => $result_sms];
        		break;
        }

	}


}