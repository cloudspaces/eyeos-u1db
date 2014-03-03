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
            $oauthProvider = new OAuthProvider();
        }
        $this->oauthProvider = $oauthProvider;
    }

    public function verifyUser($settings)
    {
        return $this->oauthProvider->verifyUser($settings);
    }

    public function verifyDateExpireToken($username,$password,$dateExpire,$currentDate,$settings)
    {
        return $this->oauthProvider->verifyDateExpireToken($username,$password,$dateExpire,$currentDate,$settings);
    }
}

?>