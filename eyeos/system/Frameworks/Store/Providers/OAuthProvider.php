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

    public function getRequestToken($cloud)
    {
        $token = null;
        $aux = $this->accessorProvider->getProcessOauthCredentials($this->getRequest($cloud));

        if(strlen($aux) > 0) {
            $token = json_decode($aux);
        }

        return $token;
    }

    public function getAccessToken($cloud, $token, $verifier)
    {
        $result = null;
        $aux = $this->accessorProvider->getProcessOauthCredentials($this->getRequest($cloud, $token, $verifier));

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

    public function getTokenUserCloud($user, $cloud)
    {
        $token = new Token();
        $token->setUserId($user);
        $token->setCloudspaceName($cloud);
        try {
            $toReturn = current($this->dao->search($token));
        } catch(EyeResultNotFoundException $e) {}
        return $toReturn;
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

    private function getRequest($cloud, $token=NULL, $verifier=NULL)
    {
        $request = new stdClass();
        if ($token) {
            $request->token = new stdClass();
            $request->token->key = $token->key;
            $request->token->secret = $token->secret;
        }
        if ($verifier) {
            $request->verifier = $verifier;
        }
        $request->config = new stdClass();
        $request->config->cloud = $cloud;
        return json_encode($request);
    }
}


?>