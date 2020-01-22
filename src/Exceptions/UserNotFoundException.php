<?php
namespace Labspace\AuthApi\Exceptions;

use Exception;

class UserNotFoundException extends Exception
{

    protected $message = '找不到使用者@USER_NOT_FOUND';
    protected $code = '403';
}
