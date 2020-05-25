<?php
namespace Labspace\AuthApi\Exceptions;

use Exception;

class SmsVerifyFailedException extends Exception
{
    protected $message = '簡訊驗證失敗，請重新確認@SMS_VERIFY_FAILED';
    protected $code = '403';


}
