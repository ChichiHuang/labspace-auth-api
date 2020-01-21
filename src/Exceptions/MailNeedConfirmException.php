<?php
namespace Labspace\AuthApi\Exceptions;

use Exception;

class MailNeedConfirmException extends Exception
{
    protected $message = '信箱尚未認證@MAIL_NEED_CONFIRM';
    protected $code = '403';


}
