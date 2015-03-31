<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 23/05/14
 * Time: 13:45
 */

class OAuthProviderTest extends PHPUnit_Framework_TestCase
{
    private $accessorProviderMock;
    private $sut;
    private $token;
    private $daoMock;

    public function setUp()
    {
        $this->accessorProviderMock = $this->getMock('AccessorProvider');
        $this->daoMock = $this->getMock('EyeosDAO');
        $this->sut = new OAuthProvider_($this->accessorProviderMock,$this->daoMock);
        $this->metadataIn = new stdClass();
        $this->metadataIn->token = new stdClass();
        $this->metadataIn->token->key = "ABCD";
        $this->metadataIn->token->secret = "EFG";
    }

    public function tearDown()
    {
        $this->accessorProviderMock = null;
        $this->sut = null;
    }

    /**
     * method: getRequestToken
     * when: called
     * with: validCloud
     * should: returnToken
     */
    public function test_getRequestToken_called_validCloud_returnToken()
    {
        $cloud = "cloudName";
        $metadataIn = '{"config":{"cloud":"' . $cloud . '"}}';
        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessOauthCredentials')
            ->with($metadataIn)
            ->will($this->returnValue('{"key":"ABCD","secret":"EFG"}'));
        $result = $this->sut->getRequestToken($cloud);
        $this->assertEquals($this->metadataIn->token, $result);
    }

    /**
     * method: getRequestToken
     * when: called
     * with: invalidCloud
     * should: returnInvalidToken
     */
    public function test_getRequestToken_called_invalidCloud_returnInvalidToken()
    {
        $cloud = "invalidCloudName";
        $metadataIn = '{"config":{"cloud":"' . $cloud . '"}}';
        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessOauthCredentials')
            ->with($metadataIn)
            ->will($this->returnValue(''));
        $result = $this->sut->getRequestToken($cloud);
        $this->assertEquals(null,$result);
    }

    /**
     * method: getAcessToken
     * when. called
     * with: validCloudAndrequestTokenAndVerifier
     * should: returnToken
     */
    public function test_getAccessToken_called_validCloudAndrequestTokenAndVerifier_returnToken()
    {
        $this->metadataIn->verifier = 'verifier';
        $this->metadataIn->config = new stdClass();
        $this->metadataIn->config->cloud = 'CloudName';
        $expected = '{"key":"HIJK","secret":"MNO"}';
        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessOauthCredentials')
            ->with(json_encode($this->metadataIn))
            ->will($this->returnValue($expected));
        $result = $this->sut->getAccessToken($this->metadataIn->config->cloud, $this->metadataIn->token, $this->metadataIn->verifier);
        $this->assertEquals(json_decode($expected),$result);
    }

    /**
     * method: getAcessToken
     * when. called
     * with: invalidCloudAndRequestTokenAndVerifier
     * should: returnInvalidToken
     */
    public function test_getAccessToken_called_invalidCloudAndRequestTokenAndVerifier_returnInvalidToken()
    {
        $this->metadataIn->verifier = 'verifier';
        $this->metadataIn->config = new stdClass();
        $this->metadataIn->config->cloud = 'invalidCloudName';
        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessOauthCredentials')
            ->with(json_encode($this->metadataIn))
            ->will($this->returnValue(''));
        $result = $this->sut->getAccessToken($this->metadataIn->config->cloud, $this->metadataIn->token, $this->metadataIn->verifier);
        $this->assertEquals(null,$result);
    }

    /**
     * method: getToken
     * when: called
     * with: user
     * should: returnToken
     */
    public function test_getToken_called_user_returnToken()
    {
        $userId = 'eyeID_EyeosUser_453';
        $token = $this->getToken($userId);
        $token->setUserId($userId);
        $this->daoMock->expects($this->once())
            ->method("read")
            ->with($token);

        $this->sut->getToken($userId);
    }

    /**
     * method: insertToken
     * when: called
     * with: token
     * should: returnCorrect
     */
    public function test_insertToken_called_token_returnCorrect()
    {
        $token = $this->getToken('eyeID_EyeosUser_453');
        $token->setTkey('ABCD');
        $token->setTsecret('EFGH');
        $this->daoMock->expects($this->once())
            ->method('create')
            ->with($token);

        $result = $this->sut->insertToken($token);
        $this->assertEquals(true,$result);
    }


    /**
     * method: deleteToken
     * when: called
     * with: user
     * should: returnCorrect
     */
    public function test_deleteToken_called_user_returnCorrect()
    {
        $userId = 'eyeID_EyeosUser_453';
        $token = $this->getToken($userId);
        $this->daoMock->expects($this->once())
            ->method('delete')
            ->with($token);
        $result = $this->sut->deleteToken($token);
        $this->assertEquals(true,$result);
    }

    private function getToken($userId)
    {
        $token = new Token();
        $token->setUserID($userId);
        return $token;
    }

    /*private function getTokenUser($userId, $cloud)
    {
        $token = new Token();
        $token->setUserID($userId);
        $token->setCloudspaceName($cloud);
        return $token;
    }*/


    /**
     * method: getToken
     * when: called
     * with: user
     * should: returnToken
     */
    /*public function test_getTokenUserCloud_called_user_returnToken()
    {
        $userId = 'eyeID_EyeosUser_15';
        $cloud = 'Stacksync';
        $token = $this->getTokenUser($userId, $cloud);
        $this->daoMock->expects($this->once())
            ->method("read")
            ->with($token);
        $this->sut->getTokenUserCloud($userId, $token);
    }*/
}

?>