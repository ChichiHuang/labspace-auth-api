<?php

namespace Labspace\AuthApi\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
//use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    /*
    |--------------------------------------------------------------------------
    | 一般登入
    |--------------------------------------------------------------------------
    */
    public function authorize()
    {
        return true;
    }

    /**
     * 驗證規則
     */
    public function rules()
    {
        return [
            'username' => 'required',
            'password' => 'required',
        ];
    }

    /**
     * 回傳訊息
     */
    public function messages()
    {
        return [

        ];
    }

    protected function failedValidation(Validator $validator) {
        $response = [
            'status' => false,
            'err_code' => 'EMPTY_REQUEST',
            'err_msg' => '參數不完整',
            'err_detail' => null
        ];
        $response['err_detail']  = $validator->errors()->toArray();
        throw new HttpResponseException(response()->json($response, 422));
    }

   
}