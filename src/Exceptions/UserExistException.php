<?php
namespace Labspace\AuthApi\Exceptions;

use Exception;

class UserExistException extends Exception
{
	//自己定義錯誤代碼&訊息
    protected $message = '帳號重複@USER_EXIST';
    protected $code = '403';
}
