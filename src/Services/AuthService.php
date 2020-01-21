<?php
namespace Labspace\AuthApi\Services;

use App;
use Exception;
use App\Models\User;
use Labspace\AuthApi\Exceptions\LoginFailedException;
use Labspace\AuthApi\Exceptions\PermissionBanException;
use Labspace\AuthApi\Exceptions\MailNeedConfirmException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Auth;

class AuthService {
    
    /**
     * AuthService constructor.
     *
     */
    public function __construct(
    ) {

    }


    /**
     * 登入取得token、使用者資料
     * @param string $username 帳號
     * @param string $password 密碼
     * @param string $role 身份 member
     * @return array 
     */
    public function login($username,$password,$role=null)
    {
        
        if (!Auth::once(['username' => $username, 'password' => $password])) {
            throw new LoginFailedException();
        }

        $path = config('labspace-auth-api.user_model');
        $user = new $path();

        if(!is_null($role)){
            $user = $user->where('role',$role);
        }

        

        $user = $user->where('username',$username)->first();
        
       // $this->userCheck($user);

        return $user;

    	
    }


    /**
     * 依照指定欄位值找到資料
     * @param string $field 欄位名稱
     * @param string $value 欄位值
     * @param string $role 身份 
     * @return User $user 
     */
    public function findBy($field,$value,$role=null)
    {

        $path = config('labspace-auth-api.user_model');
        $user = new $path();

        if(!is_null($role)){
            $user = $user->where('role',$role);
        }

        $user = $user->where($field,$value)->first();

        return $user;

        
    }

    /**
     * 取得使用者基本資訊
     * @param User $user
     * @return array
     */
    public function getUserInfo($user)
    {

        $user_info =  [
            'email' => $user->email,
            'name'=> $user->name,
            'avatar'=> $user->avatar,
            'id' => $user->id,
            'role' => $user->role
        ];

        return   [
            'token' => JWTAuth::fromUser($user),
            'user_info'=>  $user_info 
        ];
        
    }

    /**
     * 檢查是否可以登入
     * @param User $user
     * @return void
     */
    public function userCheck($user)
    {

        if($user->login_permission == 0){
            throw new PermissionBanException();
        }

        $email_check = config('labspace-auth-api.email_confirm_code_check');
        if($email_check){
            if($user->confirm_code){
                throw new MailNeedConfirmException();
            }
        }
        
    }
   
}
