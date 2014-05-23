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

    public function setUp()
    {
        $this->accessorProviderMock = $this->getMock('AccessorProvider');
        $this->sut = new OAuthProvider_($this->accessorProviderMock);
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

}

?>