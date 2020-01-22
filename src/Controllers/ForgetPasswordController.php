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

    

}