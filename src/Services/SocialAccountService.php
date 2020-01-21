<?php
namespace Labspace\AuthApi\Services;

use App;
use App\Models\User;
use App\Models\SocialAccount;
use Exception;

class SocialAccountService {
    
    /**
     * SocialLoginService constructor.
     *
     */
    public function __construct(

    ) {

    }


    /**
     * 查詢單一社群帳號的資訊
     * @param $provider
     * @param $provider_id
     * @return collection
     */
    public function findByProviderAndId($provider,$provider_id)
    {
        return SocialAccount::where('provider',$provider)->where('provider_id',$provider_id)->first();
    }

    /**
     * 新增
     * @param $provider
     * @param $provider_id
     * @return collection
     */
    public function add($data)
    {
        if(!$this->findByProviderAndId($data['provider'],$data['provider_id'])){
            SocialAccount::create($data);
        }
    }

    
  
}
