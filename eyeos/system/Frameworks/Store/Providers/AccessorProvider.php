<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 27/02/14
 * Time: 16:26
 */

class AccessorProvider
{
    private $curlRequest;

    public function __construct(IHttpRequest $curlRequest = NULL)
    {
        if(!$curlRequest) $curlRequest = new CurlRequest();
        $this->curlRequest = $curlRequest;
    }

    public function sendMessage(Settings $settings)
    {
        $methods = get_class_methods($settings);

        $this->curlRequest->open($settings->getUrl());

        foreach($methods as $method) {
            if(strrpos($method,"get") !== false && $method !== 'getUrl') {
                $this->setCurlOption($settings->$method());
            }
        }

        $result = $this->curlRequest->execute();
        $this->curlRequest->close();

        return $result;
    }

    public function setCurlOption($option)
    {
        if ($option->getValue() !== NULL){
            $this->curlRequest->setOption($option->getName(),$option->getValue());
        }
    }

    public function getProcessDataU1db($json)
    {
        $path = "python '" . EXTERN_DIR . '/' . U1DB . "/Protocol.py' " . escapeshellarg($json);
        Logger::getLogger('sebas')->error('PathPython:' . $path);
        return exec($path);
    }

    public function getProcessCredentials($token,$verifier)
    {
        $path = "python '" . EXTERN_DIR . '/' . U1DB . "/Credentials.py'";
        if ($token && $verifier) {
            $json = '{"request_token":' . $token . ',"verifier":"' . $verifier . '"}';
            $path .= " " .escapeshellarg($json);
        }
        Logger::getLogger('sebas')->error('PathPython:' . $path);
        return exec($path);
    }

    public function getProcessOauthCredentials($json = NULL)
    {
        $path = "python '" . EXTERN_DIR . '/' . U1DB . "/OauthCredentials.py'";

        if($json) {
            $path .= " " .escapeshellarg($json);
        }

        return exec($path);
    }
}


?>