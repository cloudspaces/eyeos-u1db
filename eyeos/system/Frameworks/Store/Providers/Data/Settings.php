<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 27/02/14
 * Time: 16:21
 */

class Settings
{
    private $url;
    private $customRequest;
    private $postFields;
    private $returnTransfer;
    private $httpHeader;
    private $header;
    private $sslVerifyPeer;

    public function __construct()
    {
        $this->customRequest = new OptionSetting(CURLOPT_CUSTOMREQUEST);
        $this->postFields = new OptionSetting(CURLOPT_POSTFIELDS);
        $this->returnTransfer = new OptionSetting(CURLOPT_RETURNTRANSFER);
        $this->httpHeader = new OptionSetting(CURLOPT_HTTPHEADER);
        $this->header = new OptionSetting(CURLOPT_HEADER);
        $this->sslVerifyPeer = new OptionSetting(CURLOPT_SSL_VERIFYPEER);
    }

    /**
     * @param mixed $customRequest
     */
    public function setCustomRequest($customRequest)
    {
        $this->customRequest->setValue($customRequest);
    }

    /**
     * @return mixed
     */
    public function getCustomRequest()
    {
        return $this->customRequest;
    }

    /**
     * @param mixed $header
     */
    public function setHeader($header)
    {
        $this->header->setValue($header);
    }

    /**
     * @return mixed
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param mixed $httpHeader
     */
    public function setHttpHeader($httpHeader)
    {
        $this->httpHeader->setValue($httpHeader);
    }

    /**
     * @return mixed
     */
    public function getHttpHeader()
    {
        return $this->httpHeader;
    }

    /**
     * @param mixed $postFields
     */
    public function setPostFields($postFields)
    {
        $this->postFields->setValue($postFields);
    }

    /**
     * @return mixed
     */
    public function getPostFields()
    {
        return $this->postFields;
    }

    /**
     * @param mixed $returnTransfer
     */
    public function setReturnTransfer($returnTransfer)
    {
        $this->returnTransfer->setValue($returnTransfer);
    }

    /**
     * @return mixed
     */
    public function getReturnTransfer()
    {
        return $this->returnTransfer;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $sslVerifyPeer
     */
    public function setSslVerifyPeer($sslVerifyPeer)
    {
        $this->sslVerifyPeer->setValue($sslVerifyPeer);
    }

    /**
     * @return mixed
     */
    public function getSslVerifyPeer()
    {
        return $this->sslVerifyPeer;
    }


}

?>