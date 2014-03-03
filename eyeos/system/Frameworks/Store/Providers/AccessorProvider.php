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
            if ($option->getValue() === true) $valor = 'true';
            elseif ($option->getValue() === false) $valor = 'false';
            else $valor = $option->getValue();
            $this->curlRequest->setOption($option->getName(),$option->getValue());
        }
    }
}


?>