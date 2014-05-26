<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 27/02/14
 * Time: 10:56
 */

class OAuthManagerOldTest extends PHPUnit_Framework_TestCase
{
    private $oauthProviderMock;
    private $sut;

    public function setUp()
    {
        $this->oauthProviderMock = $this->getMock('OAuthProviderOld');
        $this->sut = new OAuthManagerOld($this->oauthProviderMock);
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
        $dateExpire = "2014-02-24 11:50:00";
        $currentDate = "2014-02-23 11:00:00";
        $settings = new Settings();

        $this->oauthProviderMock->expects($this->once())
            ->method('verifyDateExpireToken')
            ->with($dateExpire,$currentDate,$settings)
            ->will($this->returnValue(false));

        $this->sut->verifyDateExpireToken($dateExpire,$currentDate,$settings);

    }

    /**
     *method: veryDateExpireToken
     * when: called
     * with: usernameAndPasswordAnddateExpireAndCurrentDate
     * should: returnDateExpire
     */
    public function test_verifyDateExpireToken_called_usernameAndPasswordAndDateExpireAndCurrentDate_returnDateExpire()
    {
        $dateExpire = "2014-02-24 11:50:00";
        $currentDate = "2014-02-23 11:00:00";
        $settings = new Settings();

        $token = new OauthToken();
        $token->setUrl('https://cloudspaces.urv.cat:8080/v1/AUTH_6d3b65697d5c48d5aaffbb430c9dbe6a');
        $token->setId('MIIGBwYJKoZIhvcNAQcCoIIF+DCCBfQCAQExCTAHBgUrDgMCGjCCBOAGCSqGSIb3DQEHAaCCBNEEggTNeyJhY2Nlc3MiOiB7InRva2VuIjogeyJpc3N1ZWRfYXQiOiAiMjAxNC0wMy0wNlQxMDo0ODowMS4yNTUzODYiLCAiZXhwaXJlcyI6ICIyMDE0LTAzLTA3VDEwOjQ4OjAxWiIsICJpZCI6ICJwbGFjZWhvbGRlciIsICJ0ZW5hbnQiOiB7ImRlc2NyaXB0aW9uIjogIkRlc2NyaXB0aW9uIGV5ZW9zIiwgImVuYWJsZWQiOiB0cnVlLCAiaWQiOiAiNmQzYjY1Njk3ZDVjNDhkNWFhZmZiYjQzMGM5ZGJlNmEiLCAibmFtZSI6ICJleWVvcyJ9fSwgInNlcnZpY2VDYXRhbG9nIjogW3siZW5kcG9pbnRzIjogW3siYWRtaW5VUkwiOiAiaHR0cHM6Ly9jbG91ZHNwYWNlcy51cnYuY2F0OjgwODAvdjEiLCAicmVnaW9uIjogIlJlZ2lvbk9uZSIsICJpbnRlcm5hbFVSTCI6ICJodHRwczovL2Nsb3Vkc3BhY2VzLnVydi5jYXQ6ODA4MC92MS9BVVRIXzZkM2I2NTY5N2Q1YzQ4ZDVhYWZmYmI0MzBjOWRiZTZhIiwgImlkIjogIjYxODA3YTQ0ZTllMTQxZTk5OGI2NzgxNGJiMTEwM2M3IiwgInB1YmxpY1VSTCI6ICJodHRwczovL2Nsb3Vkc3BhY2VzLnVydi5jYXQ6ODA4MC92MS9BVVRIXzZkM2I2NTY5N2Q1YzQ4ZDVhYWZmYmI0MzBjOWRiZTZhIn1dLCAiZW5kcG9pbnRzX2xpbmtzIjogW10sICJ0eXBlIjogIm9iamVjdC1zdG9yZSIsICJuYW1lIjogInN3aWZ0In0sIHsiZW5kcG9pbnRzIjogW3siYWRtaW5VUkwiOiAiaHR0cDovL2Nsb3Vkc3BhY2VzLnVydi5jYXQ6MzUzNTcvdjIuMCIsICJyZWdpb24iOiAiUmVnaW9uT25lIiwgImludGVybmFsVVJMIjogImh0dHA6Ly9jbG91ZHNwYWNlcy51cnYuY2F0OjUwMDAvdjIuMCIsICJpZCI6ICIxYzc3MTExY2NiY2M0MjlhOTYwZDJiZWQwNmVhMjk0MiIsICJwdWJsaWNVUkwiOiAiaHR0cDovL2Nsb3Vkc3BhY2VzLnVydi5jYXQ6NTAwMC92Mi4wIn1dLCAiZW5kcG9pbnRzX2xpbmtzIjogW10sICJ0eXBlIjogImlkZW50aXR5IiwgIm5hbWUiOiAia2V5c3RvbmUifV0sICJ1c2VyIjogeyJ1c2VybmFtZSI6ICJleWVvcyIsICJyb2xlc19saW5rcyI6IFtdLCAiaWQiOiAiZDJlOGY3YWZjMGY3NDJhMzkwMzZiMjFkMmVjZGU0ZmEiLCAicm9sZXMiOiBbeyJuYW1lIjogIl9tZW1iZXJfIn0sIHsibmFtZSI6ICJydXNlcnMifV0sICJuYW1lIjogImV5ZW9zIn0sICJtZXRhZGF0YSI6IHsiaXNfYWRtaW4iOiAwLCAicm9sZXMiOiBbIjlmZTJmZjllZTQzODRiMTg5NGE5MDg3OGQzZTkyYmFiIiwgImEwZWVhYTYwNDVjNTRkMGQ4ZTNkY2YzNWM4YWFmMWFiIl19fX0xgf8wgfwCAQEwXDBXMQswCQYDVQQGEwJVUzEOMAwGA1UECBMFVW5zZXQxDjAMBgNVBAcTBVVuc2V0MQ4wDAYDVQQKEwVVbnNldDEYMBYGA1UEAxMPd3d3LmV4YW1wbGUuY29tAgEBMAcGBSsOAwIaMA0GCSqGSIb3DQEBAQUABIGAY+pDBv9ib2SSh3ounXZl52hDEsm5EJwkdvKJGQj5OgLnkqI7XtdkeB5d9RPTuY7pqxbKqZmyYEGf5NLq2vF+n1G4LqsSMEAzQODU2YnTubrKcdgJvujbOUqIHsHj+2brIUmuIl4ECCvSch-eUImwKFU5g6Cja21cD-w0CULKdgg=');
        $token->setExpire('2014-03-07 10:48:01');

        $this->oauthProviderMock->expects($this->once())
            ->method('verifyDateExpireToken')
            ->with($dateExpire,$currentDate,$settings)
            ->will($this->returnValue($token));

        $result = $this->sut->verifyDateExpireToken($dateExpire,$currentDate,$settings);
        $this->assertEquals($token,$result);
    }
}

?>