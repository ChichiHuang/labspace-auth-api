<?php
namespace Labspace\AuthApi\Services;

use App;
use DB;
use Exception;
use Mail;
use Labspace\AuthApi\Exceptions\UserNotFoundException;
use Log;

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
}
