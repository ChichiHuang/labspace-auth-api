<?php
namespace Labspace\AuthApi\Services;

use App;
use DB;
use Exception;
use Mail;
use Labspace\AuthApi\Exceptions\UserNotFoundException;

class ConfirmationService {
    

    /**
     * ConfirmationService constructor.
     *
     */
    public function __construct(

    ) {

    }


    /**
     * 認證
     * @return collection
     */
    public function verifyCofirmationCode($id,$confirm_code)
    {
        $path = config('labspace-auth-api.user_model');
        $user_model = new $path();
        $user = $user_model->find($id);
        if(!$user){
            throw new UserNotFoundException();
        }
        if($user->confirm_code){
            if($confirm_code == $user->confirm_code){
                $user->update(['confirm_code' => null]);
            } else {
                throw new UserNotFoundException();
            }
        }

    }

    /**
     * 認證信處理
     */
    public function sendMail($user)
    {
        if($user->confirm_code){
            $data = [
                'confirm_code' => $user->confirm_code,
                'name' => $user->name,
                'id' => $user->id,
                'email' => $user->email
            ];
            Mail::send('labspace-auth-api::confirm_mail', $data, function($message) use($data){
                $message->to($data['email'])
                        ->subject('帳號開通連結信');
            });
        }
        

    }



  
}
