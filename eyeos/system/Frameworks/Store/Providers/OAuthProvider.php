<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 23/05/14
 * Time: 13:47
 */

class OAuthProvider_
{
    private $accessorProvider;

    public function __construct(AccessorProvider $accessorProvider=NULL){
        if(!$accessorProvider) $accessorProvider = new AccessorProvider();
        $this->accessorProvider = $accessorProvider;
    }

    public function getRequestToken()
    {
        $token = null;
        $aux = $this->accessorProvider->getProcessOauthCredentials();

        if(strlen($aux) > 0) {
            $token = json_decode($aux);
        }

        return $token;
    }

    public function getAccessToken($token)
    {
        $result = null;
        $aux = $this->accessorProvider->getProcessOauthCredentials(json_encode($token));

        if(strlen($aux) > 0) {
            $result = json_decode($aux);
        }

        return $result;
    }
}


?>