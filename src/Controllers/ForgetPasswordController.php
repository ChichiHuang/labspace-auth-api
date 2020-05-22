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
use Labspace\AuthApi\Services\ResetPasswordService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Labspace\AuthApi\Exceptions\UserNotFoundException;
use Labspace\AuthApi\Requests\ForgetPasswordRequest;
use Labspace\AuthApi\Requests\ChangePasswordByResetLinkRequest;
use Labspace\AuthApi\Services\ErrorService;

class ForgetPasswordController extends Controller
{
    protected $resetPasswordService;
    
    public function __construct(
        ResetPasswordService $resetPasswordService
    ) {
        $this->resetPasswordService = $resetPasswordService;
    }

    //處理忘記密碼
    public function process(ForgetPasswordRequest $request)
    {
        DB::beginTransaction();
        try{
            $this->resetPasswordService->sendResetPasswordMail($request->all());    

        } catch (Exception $e){
            DB::rollBack();
            return ErrorService::response($e);

        }
        DB::commit();

        return response()->json([
            'status' => true,
            'data'=> null,
            'success_code' => 'SUCCESS'
        ]);
    }

    //寄出密碼重設連結
    public function sendPasswordResetLink(ForgetPasswordRequest $request)
    {
        DB::beginTransaction();
        try{
            $this->resetPasswordService->sendResetPasswordLinkMail($request->all());    

        } catch (Exception $e){
            DB::rollBack();
            return ErrorService::response($e);

        }
        DB::commit();

        return response()->json([
            'status' => true,
            'data'=> null,
            'success_code' => 'SUCCESS'
        ]);
    }

    //用重設密碼連結重設密碼
    public function changePasswordByResetLink(ChangePasswordByResetLinkRequest $request)
    {

        try{
            //驗證連結
            if(!$this->resetPasswordService->verifyResetPasswordLink($request->all())){
                //連結無效
                return response()->json([
                    'status' => true,
                    'data'=> null,
                    'success_code' => 'LINK_FAIL'
                ]);
            }
            
            $this->resetPasswordService->resetPassword($request->username,$request->password);
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