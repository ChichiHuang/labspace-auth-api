<?php

namespace Labspace\AuthApi\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Exception;
use DB;
use Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Labspace\AuthApi\Services\AuthService;
use Labspace\AuthApi\Services\SocialAccountService;
use Labspace\AuthApi\Requests\LoginRequest;
use Labspace\AuthApi\Requests\SocialLoginRequest;
use Labspace\AuthApi\Exceptions\LoginFailedException;
use Labspace\AuthApi\Exceptions\PermissionBanException;
use Labspace\AuthApi\Services\ErrorService;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Log;

class AuthController extends Controller
{

    protected $authService;
    protected $socialAccountService;
    public function __construct(
        AuthService $authService,
        SocialAccountService $socialAccountService
    ) {
        $this->authService = $authService;
        $this->socialAccountService = $socialAccountService;
    }


    //使用者登入
    public function login(LoginRequest $request)
    {
        DB::beginTransaction();
        try{
            $user = $this->authService->login($request->username,$request->password,$request->role);
            if($request->has('device_token')){
                $user->update([
                    'device_token' => $request->device_token
                ]);
            }
            
        } catch (Exception $e){
            DB::rollBack();
            return ErrorService::response($e);
        }
        DB::commit();

        return $this->loginResponse($user);

        
    }

     //社群登入
    public function socialLogin(SocialLoginRequest $request)
    {
        //檢查socialAccount有沒有存在
        $provider = $request->provider;
        $provider_id = $request->social_id;

        $social_account = $this->socialAccountService->findByProviderAndId($provider,$provider_id);
        
        //帳號不存在
        if(!$social_account){
            //檢查信箱
            if($request->email && $request->email != ''){
                //信箱存不存在
                $user = $this->authService->findBy('username',$request->email,$request->role);
                if($user){
                    
                    //信箱存在  自動建立社群連結
                    $this->socialAccountService->add([
                        'provider_id' => $provider_id,
                        'provider' => $provider,
                        'user_id' => $user->id
                    ]);
                } else {
                    //告知要註冊
                    return response()->json([
                        'status'=> true,
                        'data' => null,
                        'success_code'=> 'PLEASE_REGISTER'
                    ],200);
                }
            }
        } else {
            //帳號存在，找到對應使用者
            $user = $social_account->user;
            //檢查role是不是一致
            if($request->has('role')){
                if($user->role != $request->role){
                    return response()->json([
                        'status' => false,
                        'err_code' => 'ACCOUNT_BE_USED_BY_OTHER_ROLE' ,
                        'err_msg' =>  '此帳號已被其他身份使用，請重新確認',
                        'err_detail' => null
                    ]);
                }
            }
        }

        if($request->has('device_token')){
            $user->update([
                'device_token' => $request->device_token
            ]);
        }

        
        return $this->loginResponse($user);
        
        
    }

    //登入後處理
    public function loginResponse($user)
    {
        
        try{
            //檢查使用者登入權限
            $this->authService->userCheck($user);

        } catch (Exception $e){
           
            return ErrorService::response($e);
        }
        return response()->json([
            'status' => true,
            'data' => $this->authService->getUserInfo($user),
            'success_code'=> 'SUCCESS'
        ],200);
                
    }

    //檢查token
    public function check(Request $request)
    {
    

        $user = auth()->user();
        if($request->has('role')){
                if($user->role != $request->role){
                    return response()->json([
                        'status' => true,
                        'data' => null,
                        'success_code'=> 'TOKEN_INVALID'
                        
                    ]);
                }
            }
        $user_info =  [
            'username' => $user->username,
            'email' => $user->email,
            'name'=> $user->name,
            'avatar'=> $user->avatar,
            'id' => $user->id,
            'role' => $user->role
        ];

        return response()->json([
            'status' => true,
            'data' => ['user_info' => $user_info],
            'success_code'=> 'SUCCESS'
            
        ]);
            
    }


    /**
    * 檢查字串是否存在
    *@param str 字串
    *@param needle 要檢查的
    *@return date
    **/
    private function checkstr($str, $needle){

       $tmparray = explode($needle,$str);
       if(count($tmparray)>1){
        return true;
       } else{
        return false;
       }
    }



    //取得使用者資訊
    public function getUser(Request $request)
    {

        try{
            $user = auth()->user();
            if($user->login_permission == 0){
                throw new PermissionBanException();
            }

        } catch (Exception $e){
           
            return ErrorService::response($e);
        }
        
        return response()->json([
            'status' => true,
            'data' => [
                'user_info'=> $this->authService->getUserInfo($user)
            ],
            'success_code'=> 'SUCCESS'
            
        ]);
            
    }


  
    //無效化token
    public function logout(Request $request)
    {

        try {
            $user = auth()->user();
            

            if($user){
                $logout_device_token_clear = config('labspace-auth-api.logout_device_token_clear');
                if($logout_device_token_clear){
        
                    $user->update([
                        'device_token' => null
                    ]);
                }
            }
            

            auth()->invalidate();
            
        } catch (Exception $e){

            
        }
        
        return response()->json([
            'status' => true,
            'data'=> null,
            'success_code'=> 'SUCCESS'
        ],200);
    }

    //更新token
    public function refreshToken(Request $request)
    {
        $payloadable = [
            'role' =>  auth('api')->payload()->get('machine_id'),
            'exchange_rate' =>  auth('api')->payload()->get('exchange_rate')
        ];
        return $this->respondWithToken(JWTAuth::claims($payloadable)->parseToken()->refresh());
    }

  

	 /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'status' => true,
            'data' => [
                'token' => $token,
                'expires_in' => JWTAuth::factory()->getTTL() * 60
            ]
            
        ]);
    }

}