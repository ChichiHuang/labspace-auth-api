<?php

namespace Labspace\AuthApi\Models;

use Illuminate\Database\Eloquent\Model;

class SmsConfirmation extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'sms_confirmations';
    public $timestamps = false;

    protected $fillable = [
        'phone','token','status','msg'
    ];

}
