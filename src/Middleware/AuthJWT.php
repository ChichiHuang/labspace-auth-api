<?php

namespace Labspace\AuthApi\Middleware;

use Auth;
use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Config;
use Exception;

class AuthJWT extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return mixed
     */
    public function handle($request, Closure $next,$roles=null)
    {
        
        try {

            // check token request
            $this->checkForToken($request);

            
            //正常就通過
            $user = $this->auth->parseToken()->authenticate();

            if($user->login_permission == 0){
                return response()->json([
                    'status'=> false,
                    'err_code'=> 'NO_LOGIN_PERMISSION',
                    'err_msg'=> '無權限登入網站，有任何問題請洽客服',
                ]);
            } else {
                //有帶身份就要檢查
                if(!is_null($roles) ){
                    $roles = explode('|', $roles);

                    foreach ($roles as $role) {
                        if(checkstr($user->role, $role)){
                            return $next($request);
                        }

                    }
                    return response()->json([
                        'status'=> false,
                        'err_code'=> 'PERMISSION_DENY',
                        'err_msg'=> '無權限使用功能',
                    ]);
                }
                
                return $next($request);
            }
            

        } catch (TokenExpiredException $exception) {

            try{
                $token = $this->auth->refresh();
                auth()->onceUsingId($this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']);
                return $this->setAuthenticationHeader($next($request), $token);
            }catch(JWTException $exception){
                #refresh 也過期  重新登入
                return response()->json([
                    'status' => false,
                    'err_code' => 'TOKEN_INVALID',
                    'err_msg'=> '請重新登入'
                ]);
            }

        } /*catch (TokenInvalideException $exception) {
            return response()->json([
                'status' => false,
                'err_code' => 'TOKEN_INVALID',
                'err_msg'=> 'token invalid'
            ],419);
          
        } catch (UnauthorizedHttpException $exception) {
            return response()->json([
                'status' => false,
                'err_code' => 'TOKEN_INVALID',
                'err_msg'=> 'token invalid'
            ],419);
          
        } catch (TokenBlacklistedException $exception) {
            return response()->json([
                'status' => false,
                'err_code' => 'TOKEN_INVALID',
                'err_msg'=> 'token invalid'
            ],419);
          
        } */catch(JWTException $exception){
 
            return response()->json([
                'status' => false,
                'err_code' => 'TOKEN_INVALID',
                'err_msg'=> 'token invalid'
            ]);
        }
    }


}
