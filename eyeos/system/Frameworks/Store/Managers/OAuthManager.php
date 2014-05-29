<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 23/05/14
 * Time: 13:42
 */

class OAuthManager
{
    private $oauthProvider;

    public function __construct($oauthProvider=NULL)
    {
        if(!$oauthProvider) {
            $oauthProvider = new OAuthProvider_();
        }
        $this->oauthProvider = $oauthProvider;
    }

    public function getRequestToken()
    {
        return $this->oauthProvider->getRequestToken();
    }

    public function getAccessToken($token)
    {
        return $this->oauthProvider->getAccessToken($token);
    }

    public function getToken($user)
    {
        return $this->oauthProvider->getToken($user);
    }

    public function insertToken($token)
    {
        return $this->oauthProvider->insertToken($token);
    }

    public function deleteToken($token)
    {
        return $this->oauthProvider->deleteToken($token);
    }
}


?>