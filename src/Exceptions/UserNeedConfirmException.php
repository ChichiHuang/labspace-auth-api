<?php
namespace Labspace\AuthApi\Exceptions;

use Exception;

class UserNeedConfirmException extends Exception
{
    protected $message = '帳號尚未被開通審核@USER_NEED_CONFIRM';
    protected $code = '403';


}
