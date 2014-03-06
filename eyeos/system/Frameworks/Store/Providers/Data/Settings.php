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
    private $put;
    private $inFile;
    private $inFilesize;
    private $binaryTransfer;
    private $sslVerifyHost;

    public function __construct()
    {
        $this->customRequest = new OptionSetting(CURLOPT_CUSTOMREQUEST);
        $this->postFields = new OptionSetting(CURLOPT_POSTFIELDS);
        $this->returnTransfer = new OptionSetting(CURLOPT_RETURNTRANSFER);
        $this->httpHeader = new OptionSetting(CURLOPT_HTTPHEADER);
        $this->header = new OptionSetting(CURLOPT_HEADER);
        $this->sslVerifyPeer = new OptionSetting(CURLOPT_SSL_VERIFYPEER);
        $this->put = new OptionSetting(CURLOPT_PUT);
        $this->inFile =  new OptionSetting(CURLOPT_INFILE);
        $this->inFilesize = new OptionSetting(CURLOPT_INFILESIZE);
        $this->binaryTransfer = new OptionSetting(CURLOPT_BINARYTRANSFER);
        $this->sslVerifyHost = new OptionSetting(CURLOPT_SSL_VERIFYHOST);
        $this->sslVerifyHost->setValue(0);
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

    /**
     * @param \OptionSetting $put
     */
    public function setPut($put)
    {
        $this->put->setValue($put);
    }

    /**
     * @return \OptionSetting
     */
    public function getPut()
    {
        return $this->put;
    }

    /**
     * @param \OptionSetting $inFilesize
     */
    public function setInFilesize($inFilesize)
    {
        $this->inFilesize->setValue($inFilesize);
    }

    /**
     * @return \OptionSetting
     */
    public function getInFilesize()
    {
        return $this->inFilesize;
    }

    /**
     * @param \OptionSetting $inFile
     */
    public function setInFile($inFile)
    {
        $this->inFile->setValue($inFile);
    }

    /**
     * @return \OptionSetting
     */
    public function getInFile()
    {
        return $this->inFile;
    }

    /**
     * @param \OptionSetting $binaryTransfer
     */
    public function setBinaryTransfer($binaryTransfer)
    {
        $this->binaryTransfer->setValue($binaryTransfer);
    }

    /**
     * @return \OptionSetting
     */
    public function getBinaryTransfer()
    {
        return $this->binaryTransfer;
    }

    /**
     * @param mixed $sslVerifyHost
     */
    public function setSslVerifyHost($sslVerifyHost)
    {
        $this->sslVerifyHost->setValue($sslVerifyHost);
    }

    /**
     * @return mixed
     */
    public function getSslVerifyHost()
    {
        return $this->sslVerifyHost;
    }



}

?>