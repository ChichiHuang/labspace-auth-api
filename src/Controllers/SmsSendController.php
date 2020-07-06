<?php
namespace Labspace\AuthApi\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Http\Requests;
use Exception;
use DB;
use Auth;
use Redirect;
use Labspace\AuthApi\Services\SmsConfirmationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Labspace\AuthApi\Exceptions\UserNotFoundException;
use Labspace\AuthApi\Requests\SmsSendByPhoneRequest;
use Labspace\AuthApi\Requests\SmsSendByUsernameRequest;
use Labspace\AuthApi\Requests\SmsSendVerifyRequest;
use Labspace\AuthApi\Services\ErrorService;

class SmsSendController extends Controller
{

    
    public function __construct(

    ) {

    }

    //寄出驗證碼
    public function sendCodeByPhone(SmsSendByPhoneRequest $request)
    { 
        try{
            SmsConfirmationService::sendVerifyCode($request->phone);

        } catch (Exception $e){

            return ErrorService::response($e);

        }

        return response()->json([
            'status' => true,
            'data'=> null,
            'success_code' => 'SUCCESS'
        ]);
    }

      //寄出簡訊驗證碼
    public function sendCodeByUsername(SmsSendByUsernameRequest $request)
    {
        try{
            $phone = SmsConfirmationService::findPhoneByUsername($request->username);
            SmsConfirmationService::sendVerifyCode($phone);


        } catch (Exception $e){
            return ErrorService::response($e);

        }

        return response()->json([
            'status' => true,
            'data'=> null,
            'success_code' => 'SUCCESS'
        ]);
    }
    
    //簡訊認證
    public function codeVerify(SmsSendVerifyRequest $request)
    {
        try{
            SmsConfirmationService::verifyCode($request->phone,$request->code);


        } catch (Exception $e){
            return ErrorService::response($e);

        }

        return response()->json([
            'status' => true,
            'data'=> null,
            'success_code' => 'SUCCESS'
        ]);
    }

}