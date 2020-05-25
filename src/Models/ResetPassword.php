<?php

namespace Labspace\AuthApi\Models;

use Illuminate\Database\Eloquent\Model;

class ResetPassword extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'password_resets';
    public $timestamps = false;

    protected $fillable = [
        'username','token','created_at','status'
    ];

}
