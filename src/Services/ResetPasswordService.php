<?php
namespace Labspace\AuthApi\Services;

use App;
use DB;
use Exception;
use Mail;
use Labspace\AuthApi\Exceptions\UserNotFoundException;
use Log;
use Hash;

class ResetPasswordService {
    

    /**
     * ResetPasswordService constructor.
     *
     */
    public function __construct(
    ) {
    }

    /**
     * 寄出重設密碼信
     * @return void
     */
    public function sendResetPasswordMail($data)
    {
        $path = config('labspace-auth-api.user_model');
        $user_model = new $path();
        $user = $user_model->where('username',$data['username'])->first();
        if(!$user){
            throw new UserNotFoundException();
        }
        $new_password = randomkeys(10);
        $user->update(['password' => bcrypt($new_password )] );
        if(!$user->email){
            $email = $user->username;
        } else {
            $email = $user->email;
        }
        $data = [
            'password' => $new_password,
            'name' => $user->name,
            'email' => $email
        ];
        Mail::send('labspace-auth-api::reset_password', $data, function($message) use($data){
            $message->to($data['email'])
                    ->subject('重設密碼信');
        });

    }

    /**
     * 寄出重設密碼連結信
     * @return void
     */
    public function sendResetPasswordLinkMail($data)
    {
        $path = config('labspace-auth-api.user_model');
        $user_model = new $path();

        $path = config('labspace-auth-api.reset_password_model');

        $password_reset_model = new $path();

        $user = $user_model->where('username',$data['username'])->first();
        if(!$user){
            throw new UserNotFoundException();
        }
        
        if(!$user->email){
            $email = $user->username;
        } else {
            $email = $user->email;
        }

        $token = $this->randomkeys($length =10);

        $password_reset_model->create([
            'username' => $user->username,
            'token' => bcrypt($token),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $url  = config('labspace-auth-api.reset_password_url').'?token='.$token.'&email='.$email."&role=".$user->role;
        
        $data = [
            'email' => $email,
            'url' => $url 
        ];


        Mail::send('labspace-auth-api::reset_password_link', $data, function($message) use($data){
            $message->to($data['email'])
                    ->subject('重設密碼連結');
        });

    }

     /**
     * 產生亂碼
     */
    public function randomkeys($length =6)
    {
        $key = '';
        $pattern = "1234567890ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";
        for($i=0;$i<$length;$i++){
        $key .= $pattern{rand(0,35)};
        }
        return $key;
    }


    /**
     * 驗證重設密碼連結能不能用
     * @return bool
     */
    public function verifyResetPasswordLink($data)
    {

        $path = config('labspace-auth-api.reset_password_model');
        $password_reset_model = new $path();

        $item = $password_reset_model->where('username',$data['username'])->where('status',1)->first();
        if(!$item){
            return false;
        }



        $ten_minute_before =strtotime('now') - (60*10);

        if(strtotime($item->created_at) < $ten_minute_before){
            $item->update(['status' => 0]);
            return false;
        }


        //驗證
        if (!Hash::check($data['token'], $item->token)) {

            $item->update(['status' => 0]);
            return false;
        }

        return true;

    }

   
    /**
     * 重設密碼
     * @return bool
     */
    public function resetPassword($username,$password)
    {

        $path = config('labspace-auth-api.user_model');
        $user_model = new $path();

        $user = $user_model->where('username',$username)->first();
        $user->update([
            'password' => bcrypt($password)
        ]);

        $path = config('labspace-auth-api.reset_password_model');
        $password_reset_model = new $path();
        $password_reset_model->where('username',$username)->update(['status' => 0]);

    }

}
