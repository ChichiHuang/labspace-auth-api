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
use App\Models\User;
use Labspace\AuthApi\Services\ConfirmationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Labspace\AuthApi\Exceptions\UserNotFoundException;
use Labspace\AuthApi\Services\ErrorService;

class ConfirmationController extends Controller
{
    protected $confirmationService;
    
    public function __construct(
        ConfirmationService $confirmationService
    ) {
        $this->confirmationService = $confirmationService;
    }

    //處理忘記密碼
    public function verify(Request $request)
    {

        //檢查參數
        $v = Validator::make($request->all(), [   
            'id' => 'required',
            'confirm_code' => 'required',
        ]);

        if ($v->fails()){
            return 'Invalid ';
        }

        DB::beginTransaction();
        try{   
            $this->confirmationService->verifyCofirmationCode($request->id,$request->confirm_code);
        } catch (UserNotFoundException $e){
            return 'Invalid .';
        } catch (Exception $e){
            return 'Invalid .';
        }
        DB::commit();
        return '驗證成功!';
    }

    

}