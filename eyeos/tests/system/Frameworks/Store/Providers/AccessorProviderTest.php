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

    public function setUp()
    {
        $this->curlMock = $this->getMock('IHttpRequest');
        $this->sut = new AccessorProvider($this->curlMock);
    }

    public function tearDown()
    {

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
}

?>