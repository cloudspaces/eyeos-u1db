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
        $this->token = new stdClass();
        $this->token->key = "ABCD";
        $this->token->secret = "EFG";
    }

    public function tearDown()
    {
        $this->accessorProviderMock = null;
        $this->sut = null;
    }

    /**
     * method: getRequestToken
     * when: called
     * with: noParams
     * should: returnToken
     */
    public function test_getRequestToken_called_noParams_returnToken()
    {
        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessOauthCredentials')
            ->will($this->returnValue('{"key":"ABCD","secret":"EFG"}'));
        $result = $this->sut->getRequestToken();
        $this->assertEquals($this->token,$result);
    }

    /**
     * method: getRequestToken
     * when: called
     * with: noParams
     * should: returnInvalidToken
     */
    public function test_getRequestToken_called_noParams_returnInvalidToken()
    {
        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessOauthCredentials')
            ->will($this->returnValue(''));
        $result = $this->sut->getRequestToken();
        $this->assertEquals(null,$result);
    }

    /**
     * method: getAcessToken
     * when. called
     * with: requestTokenAndVerifier
     * should: returnToken
     */
    public function test_getAccessToken_called_requestTokenAndVerifier_returnToken()
    {
        $this->token->verifier = 'verifier';
        $expected = '{"key":"HIJK","secret":"MNO"}';
        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessOauthCredentials')
            ->with(json_encode($this->token))
            ->will($this->returnValue($expected));
        $result = $this->sut->getAccessToken($this->token);
        $this->assertEquals(json_decode($expected),$result);
    }

    /**
     * method: getAcessToken
     * when. called
     * with: requestTokenAndVerifier
     * should: returnInvalidToken
     */
    public function test_getAccessToken_called_requestTokenAndVerifier_returnInvalidToken()
    {
        $this->token->verifier = 'verifier';

        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessOauthCredentials')
            ->with(json_encode($this->token))
            ->will($this->returnValue(''));
        $result = $this->sut->getAccessToken($this->token);
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
}

?>