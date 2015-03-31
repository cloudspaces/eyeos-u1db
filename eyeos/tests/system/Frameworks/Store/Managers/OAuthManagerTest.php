<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 23/05/14
 * Time: 13:32
 */
//require_once("/var/www/eyeos/eyeos/system/Frameworks/Store/Managers/OAuthManager.php");

class OAuthManagerTest extends PHPUnit_Framework_TestCase
{
    private $oauthProviderMock;
    private $sut;
    private $token;

    public function setUp()
    {
        $this->oauthProviderMock = $this->getMock('OAuthProvider_');
        $this->sut = new OAuthManager($this->oauthProviderMock);
        $this->token = new stdClass();
        $this->token->token = new stdClass();
        $this->token->token->key = 'ABCD';
        $this->token->token->secret = 'EFG';
    }

    public function tearDown()
    {
        $this->oauthProviderMock = null;
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
        $cloud = "CloudName";
        $this->oauthProviderMock->expects($this->once())
            ->method('getRequestToken')
            ->with($cloud)
            ->will($this->returnValue($this->token));
        $this->sut->getRequestToken($cloud);
    }

    /**
     * method: getAccessToken
     * when: called
     * with: validCloudAndRequestTokenAndVerifier
     * should: returnToken
     */
    public function test_getAccessToken_called_validCloudAndRequestTokenAndVerifier_returnToken()
    {
        $verifier = 'verifier';
        $cloud = 'cloudName';
        $this->oauthProviderMock->expects($this->once())
            ->method('getAccessToken')
            ->with($cloud, $this->token, $verifier)
            ->will($this->returnValue(json_encode('{"key":"JKLM","secret":"JYLM"}')));
        $this->sut->getAccessToken($cloud, $this->token, $verifier);
    }


    /**
     * method: getToken
     * when: called
     * with: user
     * should: returnToken
     */
    public function test_getToken_called_user_returnToken()
    {
        $user = 'eyeID_EyeosUser_453';
        $this->oauthProviderMock->expects($this->once())
            ->method("getToken")
            ->with($user)
            ->will($this->returnValue($this->token));

        $this->sut->getToken($user);
    }

    /**
     * method: insertToken
     * when: called
     * with: token
     * should: returnCorrect
     */
    public function test_insertToken_called_token_returnCorrect()
    {
        $token = new Token();
        $token->setUserID('eyeID_EyeosUser_453');
        $token->setTkey('ABCD');
        $token->setTsecret('EFGH');
        $this->oauthProviderMock->expects($this->once())
            ->method("insertToken")
            ->with($token)
            ->will($this->returnValue(true));
        $this->sut->insertToken($token);
    }

    /**
     * method: deleteToken
     * when: called
     * with: token
     * should: returnCorrect
     */
    public function test_deleteToken_called_token_returnCorrect()
    {
        $token = new Token();
        $token->setUserID('eyeID_EyeosUser_453');
        $this->oauthProviderMock->expects($this->once())
            ->method("deleteToken")
            ->with($token)
            ->will($this->returnValue(true));
        $this->sut->deleteToken($token);
    }



    /**
     * method: getTokenUserCloud
     * when: called
     * with: user, cloud
     * should: returnToken
     */
    /*public function test_getTokenUserCloud_called_user_returnToken()
    {
        $user = 'eyeID_EyeosUser_15';
        $cloud = 'Stacksync';
        echo "Hola";
        $this->oauthProviderMock->expects($this->once())

            ->method("getToken")
            ->with($user, $cloud)
            ->will($this->returnValue("ABCDSESSS"));

        $this->sut->getTokenUserCloud($user, $cloud);
    }*/
}


?>