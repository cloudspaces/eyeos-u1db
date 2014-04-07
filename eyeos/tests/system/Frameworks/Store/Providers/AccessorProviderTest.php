<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 27/02/14
 * Time: 15:42
 */

class AccessorProviderTest  extends PHPUnit_Framework_TestCase
{
    private $curlMock;
    private $sut;
    private $url;

    public function setUp()
    {
        $this->curlMock = $this->getMock('IHttpRequest');
        $this->sut = new AccessorProvider($this->curlMock);
        $this->url = "https://cloudspaces.urv.cat:8080/v1/AUTH_6d3b65697d5048d5aaffbb430c9dbe6a/stacksync/";
    }

    public function tearDown()
    {
        $this->sut = null;
    }

    /**
     * method: sendMessage
     * when: called
     * with: settingsOAuth
     * should: returnCorrrectData
     */
    public function test_sendMessage_called_settingsOAuth_returnCorrectData()
    {
        $settings = new Settings();
        $settings->setUrl("http://cloudspaces.urv.cat:5000/v2.0/tokens");
        $settings->setCustomRequest("POST");

        $postfields = array();
        $postfields['auth'] = array();
        $postfields['auth']['passwordCredentials'] = array();
        $postfields['auth']['passwordCredentials']['username'] = 'eyeos';
        $postfields['auth']['passwordCredentials']['password'] = 'eyeos';
        $postfields['auth']['tenantName'] = 'eyeos';

        $settings->setPostFields(json_encode($postfields));
        $settings->setReturnTransfer(true);
        $settings->setHttpHeader(array("Content-Type: application/json"));
        $settings->setHeader(false);
        $settings->setSslVerifyPeer(false);

        $this->curlMock->expects($this->at(0))
            ->method("open")
            ->with("http://cloudspaces.urv.cat:5000/v2.0/tokens");
        $this->curlMock->expects($this->at(1))
            ->method("setOption")
            ->with(CURLOPT_CUSTOMREQUEST,"POST");
        $this->curlMock->expects($this->at(2))
            ->method("setOption")
            ->with(CURLOPT_HEADER,false);
        $this->curlMock->expects($this->at(3))
            ->method("setOption")
            ->with(CURLOPT_HTTPHEADER,array("Content-Type: application/json"));
        $this->curlMock->expects($this->at(4))
            ->method("setOption")
            ->with(CURLOPT_POSTFIELDS,'{"auth":{"passwordCredentials":{"username":"eyeos","password":"eyeos"},"tenantName":"eyeos"}}');
        $this->curlMock->expects($this->at(5))
            ->method("setOption")
            ->with(CURLOPT_RETURNTRANSFER,true);
        $this->curlMock->expects($this->at(6))
            ->method("setOption")
            ->with(CURLOPT_SSL_VERIFYPEER,false);
        $this->curlMock->expects($this->at(7))
            ->method("execute");
        $this->curlMock->expects($this->at(8))
            ->method("close");

        $this->sut->sendMessage($settings);
    }

    /**
     *method: sendMessage
     * when: called
     * with: settingsOAuth
     * should: returnEyeCurlException
     * @expectedException EyeCurlException
     */
    public function test_sendMessage_called_settingsOAuth_EyeCurlException()
    {
        $settings = new Settings();

        $this->curlMock->expects($this->once())
            ->method('execute')
            ->will($this->throwException(new EyeCurlException()));

        $this->sut->sendMessage($settings);
    }


    /**
     *method: sendMessage
     * when: called
     * with: settingsMetadata
     * should: returnCorrectData
     */
    public function test_sendMessage_called_settingsMetadata_returnCorrectData()
    {
        $url = $this->url . 'metadata';
        $this->executeAccessor($url);
    }

    /**
     *method: sendMessage
     * when: called
     * with: settingsCreateFile
     * should: returnCorrectData
     */
    public function test_sendMessage_called_settingsCreateFile_returnCorrectData()
    {
        $url = $this->url . 'files?file_name=' . urlencode("prueba.txt");
        $file = -1;
        $settings = new Settings();
        $settings->setUrl($url);
        $settings->setSslVerifyPeer(false);
        $settings->setHeader(false);
        $settings->setReturnTransfer(true);
        $settings->setHttpHeader($this->getHeader());
        $settings->setPut(true);
        $settings->setInfile($file);
        $settings->setInFilesize(0);
        $settings->setBinaryTransfer(true);

        $this->curlMock->expects($this->at(0))
            ->method("open")
            ->with($url);
        $this->curlMock->expects($this->at(1))
            ->method("setOption")
            ->with(CURLOPT_HEADER,false);
        $this->curlMock->expects($this->at(2))
            ->method("setOption")
            ->with(CURLOPT_HTTPHEADER,$this->getHeader());
        $this->curlMock->expects($this->at(3))
            ->method("setOption")
            ->with(CURLOPT_RETURNTRANSFER,true);
        $this->curlMock->expects($this->at(4))
            ->method("setOption")
            ->with(CURLOPT_SSL_VERIFYPEER,false);
        $this->curlMock->expects($this->at(5))
            ->method("setOption")
            ->with(CURLOPT_PUT,true);
        $this->curlMock->expects($this->at(6))
            ->method("setOption")
            ->with(CURLOPT_INFILESIZE,0);
        $this->curlMock->expects($this->at(7))
            ->method("setOption")
            ->with(CURLOPT_INFILE,$file);
        $this->curlMock->expects($this->at(8))
            ->method("setOption")
            ->with(CURLOPT_BINARYTRANSFER,true);
        $this->curlMock->expects($this->at(9))
            ->method("execute");
        $this->curlMock->expects($this->at(10))
            ->method("close");

        $this->sut->sendMessage($settings);
    }

    /**
     * method: sendMessage
     * when: called
     * with: settingsCreateFolder
     * should: returnCorrectData
     */
    public function test_sendMessage_called_settingsCreateFolder_returnCorrectData()
    {
        $url = $this->url . 'files?folder_name=' . urlencode("Documents");
        $settings = new Settings();
        $settings->setUrl($url);
        $settings->setSslVerifyPeer(false);
        $settings->setHeader(false);
        $settings->setReturnTransfer(true);
        $settings->setHttpHeader($this->getHeader());
        $settings->setCustomRequest("POST");

        $this->curlMock->expects($this->at(0))
            ->method("open")
            ->with($url);
        $this->curlMock->expects($this->at(1))
            ->method("setOption")
            ->with(CURLOPT_CUSTOMREQUEST,"POST");
        $this->curlMock->expects($this->at(2))
            ->method("setOption")
            ->with(CURLOPT_HEADER,false);
        $this->curlMock->expects($this->at(3))
            ->method("setOption")
            ->with(CURLOPT_HTTPHEADER,$this->getHeader());
        $this->curlMock->expects($this->at(4))
            ->method("setOption")
            ->with(CURLOPT_RETURNTRANSFER,true);
        $this->curlMock->expects($this->at(5))
            ->method("setOption")
            ->with(CURLOPT_SSL_VERIFYPEER,false);
        $this->curlMock->expects($this->at(6))
            ->method("execute");
        $this->curlMock->expects($this->at(7))
            ->method("close");

        $this->sut->sendMessage($settings);
    }

    /**
     *method: sendMessage
     * when: called
     * with: settingsDelete
     * should: returnCorrectData
     */
    public function test_sendMessage_called_settingsDelete_returnCorrectData()
    {
        $url = $this->url . 'files?file_id=1771';
        $settings = new Settings();
        $settings->setUrl($url);
        $settings->setSslVerifyPeer(false);
        $settings->setHeader(false);
        $settings->setReturnTransfer(true);
        $settings->setHttpHeader($this->getHeader());
        $settings->setCustomRequest("DELETE");

        $this->curlMock->expects($this->at(0))
            ->method("open")
            ->with($url);
        $this->curlMock->expects($this->at(1))
            ->method("setOption")
            ->with(CURLOPT_CUSTOMREQUEST,"DELETE");
        $this->curlMock->expects($this->at(2))
            ->method("setOption")
            ->with(CURLOPT_HEADER,false);
        $this->curlMock->expects($this->at(3))
            ->method("setOption")
            ->with(CURLOPT_HTTPHEADER,$this->getHeader());
        $this->curlMock->expects($this->at(4))
            ->method("setOption")
            ->with(CURLOPT_RETURNTRANSFER,true);
        $this->curlMock->expects($this->at(5))
            ->method("setOption")
            ->with(CURLOPT_SSL_VERIFYPEER,false);
        $this->curlMock->expects($this->at(6))
            ->method("execute");
        $this->curlMock->expects($this->at(7))
            ->method("close");

        $this->sut->sendMessage($settings);
    }

    /**
     *method: sendMessage
     * when: called
     * with: settingsDownloadFile
     * should: returnCorrectData
     */
    public function test_sendMessage_called_settingsDownloadFile_returnCorrectData()
    {
        $url = $this->url . 'files?file_id=1771';
        $this->executeAccessor($url);
    }

    /**
     * method: sendMessage
     * when: called
     * with: settingsVersion
     * should: returnCorrectData
     */
    public function test_sendMessage_called_settingsVersion_returnCorrectData()
    {
        $url = $this->url . 'versions?file_id=1771';
        $this->executeAccessor($url);
    }

    /**
     * method: sendMessage
     * when: called
     * with: settingsRestoreFile
     * should: returnCorrectData
     */
    public function test_sendMessage_called_settingsRestoreFile_returnCorrectData()
    {
        $url = $this->url . 'files?file_id=1771&version=1';
        $this->executeAccessor($url);
    }

    private function getHeader()
    {
        $header = array();
        $header[0] = 'X-Auth-Token: 1425789544';
        $header[1] = 'StackSync-api: true';
        return $header;
    }

    private function executeAccessor($url)
    {
        $settings = new Settings();
        $settings->setUrl($url);
        $settings->setSslVerifyPeer(false);
        $settings->setHeader(false);
        $settings->setReturnTransfer(true);
        $settings->setHttpHeader($this->getHeader());

        $this->curlMock->expects($this->at(0))
            ->method("open")
            ->with($url);
        $this->curlMock->expects($this->at(1))
            ->method("setOption")
            ->with(CURLOPT_HEADER,false);
        $this->curlMock->expects($this->at(2))
            ->method("setOption")
            ->with(CURLOPT_HTTPHEADER,$this->getHeader());
        $this->curlMock->expects($this->at(3))
            ->method("setOption")
            ->with(CURLOPT_RETURNTRANSFER,true);
        $this->curlMock->expects($this->at(4))
            ->method("setOption")
            ->with(CURLOPT_SSL_VERIFYPEER,false);
        $this->curlMock->expects($this->at(5))
            ->method("setOption")
            ->with(CURLOPT_SSL_VERIFYHOST,0);
        $this->curlMock->expects($this->at(6))
            ->method("execute");
        $this->curlMock->expects($this->at(7))
            ->method("close");

        $this->sut->sendMessage($settings);
    }
}

?>