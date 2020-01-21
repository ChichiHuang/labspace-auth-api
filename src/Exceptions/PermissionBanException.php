<?php
namespace Labspace\AuthApi\Exceptions;

use Exception;

class PermissionBanException extends Exception
{
    protected $message = '無權限登入網站，有任何問題請洽客服@NO_LOGIN_PERMISSION';
    protected $code = '403';
}
