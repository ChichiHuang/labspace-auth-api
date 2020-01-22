<?php
namespace Labspace\AuthApi\Exceptions;

use Exception;

class UserNeedConfirmException extends Exception
{
    protected $message = '尚未被管理員審核，有任何問題請洽客服@USER_NEED_CONFIRM';
    protected $code = '403';


}
