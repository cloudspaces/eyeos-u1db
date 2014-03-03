<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 27/02/14
 * Time: 10:56
 */

class OAuthManagerTest extends PHPUnit_Framework_TestCase
{
    private $oauthProviderMock;
    private $sut;

    public function setUp()
    {
        $this->oauthProviderMock = $this->getMock('OAuthProvider');
        $this->sut = new OAuthManager($this->oauthProviderMock);
    }

    public function tearDown()
    {

    }

    /**
     * method: verifyUser
     * when: called
     * with: userAndPassword
     * should: returnToken
     */
    public function test_verifyUser_called_userAndPassword_returnToken()
    {
        $settings = new Settings();
        $postfields = array();
        $postfields['auth'] = array();
        $postfields['auth']['passwordCredentials'] = array();
        $postfields['auth']['passwordCredentials']['username'] = 'eyeos';
        $postfields['auth']['passwordCredentials']['password'] = 'eyeos';
        $postfields['auth']['tenantName'] = 'eyeos';
        $settings->setPostFields(json_encode($postfields));

        $this->oauthProviderMock->expects($this->once())
            ->method('verifyUser')
            ->with($settings)
            ->will($this->returnValue('AB'));

        $this->sut->verifyUser($settings);
    }

    /**
     *method: veryDateExpireToken
     * when: called
     * with: usernameAndPasswordAnddateExpireAndCurrentDate
     * should: returnDateNotExpire
     */
    public function test_verifyDateExpireToken_called_usernameAndPasswordAndDateExpireAndCurrentDate_returnDateNotExpire()
    {
        $username = 'eyeos';
        $password = 'eyeos';
        $dateExpire = "2014-02-24 11:50:00";
        $currentDate = "2014-02-23 11:00:00";
        $settings = new Settings();

        $this->oauthProviderMock->expects($this->once())
            ->method('verifyDateExpireToken')
            ->with($username,$password,$dateExpire,$currentDate,$settings)
            ->will($this->returnValue(false));

        $this->sut->verifyDateExpireToken($username,$password,$dateExpire,$currentDate,$settings);

    }
}

?>