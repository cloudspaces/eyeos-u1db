<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 27/02/14
 * Time: 12:42
 */

class OAuthManagerOld
{
    private $oauthProvider;

    public function __construct($oauthProvider=NULL)
    {
        if(!$oauthProvider) {
            $oauthProvider = new OAuthProviderOld();
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