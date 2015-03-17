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
    private $dao;

    public function __construct(AccessorProvider $accessorProvider=NULL,EyeosDAO $dao = NULL){
        if(!$accessorProvider) $accessorProvider = new AccessorProvider();
        $this->accessorProvider = $accessorProvider;
        if(!$dao)$dao = new EyeosDao();
        $this->dao = $dao;
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

    public function getToken($user)
    {
        $token = new Token();
        $token->setUserId($user);
        try {
            $this->dao->read($token);
        } catch(EyeResultNotFoundException $e) {}
        return $token;
    }

    public function insertToken($token)
    {
        try {
            $this->dao->create($token);
            return true;
        } catch (Exception $e){}
        return false;
    }

    public function deleteToken($token)
    {
        try {
            $this->dao->delete($token);
            return true;
        } catch (Exception $e) {}
        return false;
    }
}


?>