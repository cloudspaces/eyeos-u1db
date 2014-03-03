<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 27/02/14
 * Time: 17:29
 */

interface IHttpRequest
{
    public function setOption($name,$value);
    public function open($url);
    public function execute();
    public function getInfo($name=NULL);
    public function close();
}

class CurlRequest implements IHttpRequest
{
    private $handle=NULL;

    public function __construct()
    {

    }

    public function open($url)
    {
        $this->handle = curl_init($url);
    }

    public function setOption($name,$value)
    {
        curl_setopt($this->handle,$name,$value);
    }

    public function execute()
    {
        $result = curl_exec($this->handle);

        if($result === false) {
            throw new EyeCurlException("Curl failed: " . curl_error($this->handle));
        }

        return $result;
    }

    public function getInfo($name=NULL)
    {
        return curl_getinfo($this->handle,$name);
    }

    public function close()
    {
        curl_close($this->handle);
        $this->handle = null;
    }
}

?>