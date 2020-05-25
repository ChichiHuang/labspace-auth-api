<?php
namespace Labspace\AuthApi\Exceptions;

use Exception;

class SmsSendFailedException extends Exception
{
    protected $message = '發送簡訊失敗，請洽客服人員@SMS_SEND_FAILED';
    protected $code = '403';


}
