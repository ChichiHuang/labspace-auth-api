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

class WebAuthJWT extends BaseMiddleware
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
                    'status' => true,
                    'data' => null,
                    'success_code'=> 'TOKEN_INVALID'
                    
                ]);
            } else {
                
                
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
                    'status' => true,
                    'data' => null,
                    'success_code'=> 'TOKEN_INVALID'
                    
                ]);
            }

        } catch (UnauthorizedHttpException $exception) {
            return response()->json([
                'status' => true,
                'data' => null,
                'success_code'=> 'TOKEN_REQUIRED'
                
            ]);
          
          
        } catch(JWTException $exception){
 
            return response()->json([
                    'status' => true,
                    'data' => null,
                    'success_code'=> 'TOKEN_INVALID',
                    'msg' => $exception->getMessage()
                    
                ]);
        } 
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


}
