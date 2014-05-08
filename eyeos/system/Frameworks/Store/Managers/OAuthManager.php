<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 27/02/14
 * Time: 12:42
 */

class OAuthManager
{
    private $oauthProvider;

    public function __construct($oauthProvider=NULL)
    {
        if(!$oauthProvider) {
            $oauthProvider = new OAuth_Provider();
        }
        $this->oauthProvider = $oauthProvider;
    }

    public function verifyUser($settings)
    {
        return $this->oauthProvider->verifyUser($settings);
    }

    public function verifyDateExpireToken($dateExpire,$currentDate,$settings)
    {
        return $this->oauthProvider->verifyDateExpireToken($dateExpire,$currentDate,$settings);
    }
}

?>