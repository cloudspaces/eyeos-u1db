<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 30/06/14
 * Time: 16:36
 */

class U1DBCredsManager
{
    private $accessorProvider;

    public function __construct(AccessorProvider $accessorProvider = NULL)
    {
        if(!$accessorProvider) $accessorProvider = new AccessorProvider();
        $this->accessorProvider = $accessorProvider;
    }

    public function callProcessCredentials()
    {
        $credentials = NULL;
        $token = isset($_SESSION['request_token'])?$_SESSION['request_token']:null;
        $verifier = isset($_SESSION['verifier'])?$_SESSION['verifier']:null;
        $creds = json_decode($this->accessorProvider->getProcessCredentials($token,$verifier));
        if ($creds) {
            $_SESSION['request_token'] = json_encode($creds->request_token);
            $_SESSION['verifier'] = $creds->verifier;
            $json['oauth'] = $creds->credentials;
            $credentials = $json;
            //Logger::getLogger('sebas')->error('Credenciales:' . json_encode($credentials));
        }
        return $credentials;
    }
}

?>