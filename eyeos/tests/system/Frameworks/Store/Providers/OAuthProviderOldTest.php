<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 27/02/14
 * Time: 15:36
 */

class OAuthProviderOldTest extends PHPUnit_Framework_TestCase
{
    private $oauthToken;

    public function setUp()
    {
        $this->oauthToken = new OauthToken();
    }

    public function tearDown()
    {
        $this->oauthToken = null;
    }

    /**
     *method: verifyUser
     * when: called
     * with: userAndPassword
     * should: returnToken
     */
    public function test_verifyUser_called_userAndPassword_returnToken()
    {
        $accessorProviderMock = $this->getMock('AccessorProvider');
        $this->oauthToken->setUrl("https://cloudspaces.urv.cat:8080/v1/AUTH_6d3b65697d5c48d5aaffbb430c9dbe6a");
        $this->oauthToken->setId('MIIGBwYJKoZIhvcNAQcCoIIF+DCCBfQCAQExCTAHBgUrDgMCGjCCBOAGCSqGSIb3DQEHAaCCBNEEggTNeyJhY2Nlc3MiOiB7InRva2VuIjogeyJpc3N1ZWRfYXQiOiAiMjAxNC0wMi0yOFQxMjozMzozMy4yNzUzNzciLCAiZXhwaXJlcyI6ICIyMDE0LTAzLTAxVDEyOjMzOjMzWiIsICJpZCI6ICJwbGFjZWhvbGRlciIsICJ0ZW5hbnQiOiB7ImRlc2NyaXB0aW9uIjogIkRlc2NyaXB0aW9uIGV5ZW9zIiwgImVuYWJsZWQiOiB0cnVlLCAiaWQiOiAiNmQzYjY1Njk3ZDVjNDhkNWFhZmZiYjQzMGM5ZGJlNmEiLCAibmFtZSI6ICJleWVvcyJ9fSwgInNlcnZpY2VDYXRhbG9nIjogW3siZW5kcG9pbnRzIjogW3siYWRtaW5VUkwiOiAiaHR0cHM6Ly9jbG91ZHNwYWNlcy51cnYuY2F0OjgwODAvdjEiLCAicmVnaW9uIjogIlJlZ2lvbk9uZSIsICJpbnRlcm5hbFVSTCI6ICJodHRwczovL2Nsb3Vkc3BhY2VzLnVydi5jYXQ6ODA4MC92MS9BVVRIXzZkM2I2NTY5N2Q1YzQ4ZDVhYWZmYmI0MzBjOWRiZTZhIiwgImlkIjogIjYxODA3YTQ0ZTllMTQxZTk5OGI2NzgxNGJiMTEwM2M3IiwgInB1YmxpY1VSTCI6ICJodHRwczovL2Nsb3Vkc3BhY2VzLnVydi5jYXQ6ODA4MC92MS9BVVRIXzZkM2I2NTY5N2Q1YzQ4ZDVhYWZmYmI0MzBjOWRiZTZhIn1dLCAiZW5kcG9pbnRzX2xpbmtzIjogW10sICJ0eXBlIjogIm9iamVjdC1zdG9yZSIsICJuYW1lIjogInN3aWZ0In0sIHsiZW5kcG9pbnRzIjogW3siYWRtaW5VUkwiOiAiaHR0cDovL2Nsb3Vkc3BhY2VzLnVydi5jYXQ6MzUzNTcvdjIuMCIsICJyZWdpb24iOiAiUmVnaW9uT25lIiwgImludGVybmFsVVJMIjogImh0dHA6Ly9jbG91ZHNwYWNlcy51cnYuY2F0OjUwMDAvdjIuMCIsICJpZCI6ICIxYzc3MTExY2NiY2M0MjlhOTYwZDJiZWQwNmVhMjk0MiIsICJwdWJsaWNVUkwiOiAiaHR0cDovL2Nsb3Vkc3BhY2VzLnVydi5jYXQ6NTAwMC92Mi4wIn1dLCAiZW5kcG9pbnRzX2xpbmtzIjogW10sICJ0eXBlIjogImlkZW50aXR5IiwgIm5hbWUiOiAia2V5c3RvbmUifV0sICJ1c2VyIjogeyJ1c2VybmFtZSI6ICJleWVvcyIsICJyb2xlc19saW5rcyI6IFtdLCAiaWQiOiAiZDJlOGY3YWZjMGY3NDJhMzkwMzZiMjFkMmVjZGU0ZmEiLCAicm9sZXMiOiBbeyJuYW1lIjogIl9tZW1iZXJfIn0sIHsibmFtZSI6ICJydXNlcnMifV0sICJuYW1lIjogImV5ZW9zIn0sICJtZXRhZGF0YSI6IHsiaXNfYWRtaW4iOiAwLCAicm9sZXMiOiBbIjlmZTJmZjllZTQzODRiMTg5NGE5MDg3OGQzZTkyYmFiIiwgImEwZWVhYTYwNDVjNTRkMGQ4ZTNkY2YzNWM4YWFmMWFiIl19fX0xgf8wgfwCAQEwXDBXMQswCQYDVQQGEwJVUzEOMAwGA1UECBMFVW5zZXQxDjAMBgNVBAcTBVVuc2V0MQ4wDAYDVQQKEwVVbnNldDEYMBYGA1UEAxMPd3d3LmV4YW1wbGUuY29tAgEBMAcGBSsOAwIaMA0GCSqGSIb3DQEBAQUABIGAN6xTCTuXGPCBTkXxFwvVsotye-kb3MfMdqr+fvmdi5JMhamOI-zy97eS-a7qRdq6NhaqGaDsyk90Z3o4mReoS8ZrKWoKVBgI1c+CWdBlVDBpjnP+ISq2sZoy2p1YdRBWkrhWgECAKnoLmcIhs4o5XiQ5XZK-EuyCKJwGTwkCaXA=');
        $this->oauthToken->setExpire('2014-03-01 12:33:33');
        $settings = new Settings();
        $accessorProviderMock->expects($this->once())
            ->method('sendMessage')
            ->with($settings)
            ->will($this->returnValue('{"access": {"token": {"issued_at": "2014-02-28T12:33:33.275377", "expires": "2014-03-01T12:33:33Z", "id": "MIIGBwYJKoZIhvcNAQcCoIIF+DCCBfQCAQExCTAHBgUrDgMCGjCCBOAGCSqGSIb3DQEHAaCCBNEEggTNeyJhY2Nlc3MiOiB7InRva2VuIjogeyJpc3N1ZWRfYXQiOiAiMjAxNC0wMi0yOFQxMjozMzozMy4yNzUzNzciLCAiZXhwaXJlcyI6ICIyMDE0LTAzLTAxVDEyOjMzOjMzWiIsICJpZCI6ICJwbGFjZWhvbGRlciIsICJ0ZW5hbnQiOiB7ImRlc2NyaXB0aW9uIjogIkRlc2NyaXB0aW9uIGV5ZW9zIiwgImVuYWJsZWQiOiB0cnVlLCAiaWQiOiAiNmQzYjY1Njk3ZDVjNDhkNWFhZmZiYjQzMGM5ZGJlNmEiLCAibmFtZSI6ICJleWVvcyJ9fSwgInNlcnZpY2VDYXRhbG9nIjogW3siZW5kcG9pbnRzIjogW3siYWRtaW5VUkwiOiAiaHR0cHM6Ly9jbG91ZHNwYWNlcy51cnYuY2F0OjgwODAvdjEiLCAicmVnaW9uIjogIlJlZ2lvbk9uZSIsICJpbnRlcm5hbFVSTCI6ICJodHRwczovL2Nsb3Vkc3BhY2VzLnVydi5jYXQ6ODA4MC92MS9BVVRIXzZkM2I2NTY5N2Q1YzQ4ZDVhYWZmYmI0MzBjOWRiZTZhIiwgImlkIjogIjYxODA3YTQ0ZTllMTQxZTk5OGI2NzgxNGJiMTEwM2M3IiwgInB1YmxpY1VSTCI6ICJodHRwczovL2Nsb3Vkc3BhY2VzLnVydi5jYXQ6ODA4MC92MS9BVVRIXzZkM2I2NTY5N2Q1YzQ4ZDVhYWZmYmI0MzBjOWRiZTZhIn1dLCAiZW5kcG9pbnRzX2xpbmtzIjogW10sICJ0eXBlIjogIm9iamVjdC1zdG9yZSIsICJuYW1lIjogInN3aWZ0In0sIHsiZW5kcG9pbnRzIjogW3siYWRtaW5VUkwiOiAiaHR0cDovL2Nsb3Vkc3BhY2VzLnVydi5jYXQ6MzUzNTcvdjIuMCIsICJyZWdpb24iOiAiUmVnaW9uT25lIiwgImludGVybmFsVVJMIjogImh0dHA6Ly9jbG91ZHNwYWNlcy51cnYuY2F0OjUwMDAvdjIuMCIsICJpZCI6ICIxYzc3MTExY2NiY2M0MjlhOTYwZDJiZWQwNmVhMjk0MiIsICJwdWJsaWNVUkwiOiAiaHR0cDovL2Nsb3Vkc3BhY2VzLnVydi5jYXQ6NTAwMC92Mi4wIn1dLCAiZW5kcG9pbnRzX2xpbmtzIjogW10sICJ0eXBlIjogImlkZW50aXR5IiwgIm5hbWUiOiAia2V5c3RvbmUifV0sICJ1c2VyIjogeyJ1c2VybmFtZSI6ICJleWVvcyIsICJyb2xlc19saW5rcyI6IFtdLCAiaWQiOiAiZDJlOGY3YWZjMGY3NDJhMzkwMzZiMjFkMmVjZGU0ZmEiLCAicm9sZXMiOiBbeyJuYW1lIjogIl9tZW1iZXJfIn0sIHsibmFtZSI6ICJydXNlcnMifV0sICJuYW1lIjogImV5ZW9zIn0sICJtZXRhZGF0YSI6IHsiaXNfYWRtaW4iOiAwLCAicm9sZXMiOiBbIjlmZTJmZjllZTQzODRiMTg5NGE5MDg3OGQzZTkyYmFiIiwgImEwZWVhYTYwNDVjNTRkMGQ4ZTNkY2YzNWM4YWFmMWFiIl19fX0xgf8wgfwCAQEwXDBXMQswCQYDVQQGEwJVUzEOMAwGA1UECBMFVW5zZXQxDjAMBgNVBAcTBVVuc2V0MQ4wDAYDVQQKEwVVbnNldDEYMBYGA1UEAxMPd3d3LmV4YW1wbGUuY29tAgEBMAcGBSsOAwIaMA0GCSqGSIb3DQEBAQUABIGAN6xTCTuXGPCBTkXxFwvVsotye-kb3MfMdqr+fvmdi5JMhamOI-zy97eS-a7qRdq6NhaqGaDsyk90Z3o4mReoS8ZrKWoKVBgI1c+CWdBlVDBpjnP+ISq2sZoy2p1YdRBWkrhWgECAKnoLmcIhs4o5XiQ5XZK-EuyCKJwGTwkCaXA=", "tenant": {"description": "Description eyeos", "enabled": true, "id": "6d3b65697d5c48d5aaffbb430c9dbe6a", "name": "eyeos"}}, "serviceCatalog": [{"endpoints": [{"adminURL": "https://cloudspaces.urv.cat:8080/v1", "region": "RegionOne", "internalURL": "https://cloudspaces.urv.cat:8080/v1/AUTH_6d3b65697d5c48d5aaffbb430c9dbe6a", "id": "61807a44e9e141e998b67814bb1103c7", "publicURL": "https://cloudspaces.urv.cat:8080/v1/AUTH_6d3b65697d5c48d5aaffbb430c9dbe6a"}], "endpoints_links": [], "type": "object-store", "name": "swift"}, {"endpoints": [{"adminURL": "http://cloudspaces.urv.cat:35357/v2.0", "region": "RegionOne", "internalURL": "http://cloudspaces.urv.cat:5000/v2.0", "id": "1c77111ccbcc429a960d2bed06ea2942", "publicURL": "http://cloudspaces.urv.cat:5000/v2.0"}], "endpoints_links": [], "type": "identity", "name": "keystone"}], "user": {"username": "eyeos", "roles_links": [], "id": "d2e8f7afc0f742a39036b21d2ecde4fa", "roles": [{"name": "_member_"}, {"name": "rusers"}], "name": "eyeos"}, "metadata": {"is_admin": 0, "roles": ["9fe2ff9ee4384b1894a90878d3e92bab", "a0eeaa6045c54d0d8e3dcf35c8aaf1ab"]}}}'));

        $oauthProvider = new OAuthProviderOld($accessorProviderMock);
        $actual = $oauthProvider->verifyUser($settings);
        $this->assertEquals($this->oauthToken,$actual);
    }

    /**
     *method: veryfyDateExpireToken
     * when: called
     * with: usernameAndPasswordAndDateExpireAndCurrentDate
     * should: returnDateNotExpire
     */
    public function test_verifyDateExpireToken_called_usernameAndPasswordAndDateExpireAndCurrentDate_returnDateNotExpire()
    {
        $dateExpire = "2014-02-24 11:50:00";
        $currentDate = "2014-02-23 11:00:00";

        $settings = new Settings();
        $oauthProvider = new OAuthProviderOld();
        $actual = $oauthProvider->verifyDateExpireToken($dateExpire,$currentDate,$settings);
        $this->assertEquals(false,$actual);
    }

    /**
     *method: veryfyDateExpireToken
     * when: called
     * with: usernameAndPasswordAndDateExpireAndCurrentDate
     * should: returnToken
     */
    public function test_verifyDateExpireToken_called_usernameAndPasswordAndDateExpireAndCurrentDate_returnToken()
    {
        $dateExpire = "2014-02-24 11:50:00";
        $currentDate = "2014-02-24 11:50:01";
        $this->oauthToken->setUrl("https://cloudspaces.urv.cat:8080/v1/AUTH_6d3b65697d5c48d5aaffbb430c9dbe6a");
        $this->oauthToken->setId('MIIGBwYJKoZIhvcNAQcCoIIF+DCCBfQCAQExCTAHBgUrDgMCGjCCBOAGCSqGSIb3DQEHAaCCBNEEggTNeyJhY2Nlc3MiOiB7InRva2VuIjogeyJpc3N1ZWRfYXQiOiAiMjAxNC0wMi0yOFQxMjozMzozMy4yNzUzNzciLCAiZXhwaXJlcyI6ICIyMDE0LTAzLTAxVDEyOjMzOjMzWiIsICJpZCI6ICJwbGFjZWhvbGRlciIsICJ0ZW5hbnQiOiB7ImRlc2NyaXB0aW9uIjogIkRlc2NyaXB0aW9uIGV5ZW9zIiwgImVuYWJsZWQiOiB0cnVlLCAiaWQiOiAiNmQzYjY1Njk3ZDVjNDhkNWFhZmZiYjQzMGM5ZGJlNmEiLCAibmFtZSI6ICJleWVvcyJ9fSwgInNlcnZpY2VDYXRhbG9nIjogW3siZW5kcG9pbnRzIjogW3siYWRtaW5VUkwiOiAiaHR0cHM6Ly9jbG91ZHNwYWNlcy51cnYuY2F0OjgwODAvdjEiLCAicmVnaW9uIjogIlJlZ2lvbk9uZSIsICJpbnRlcm5hbFVSTCI6ICJodHRwczovL2Nsb3Vkc3BhY2VzLnVydi5jYXQ6ODA4MC92MS9BVVRIXzZkM2I2NTY5N2Q1YzQ4ZDVhYWZmYmI0MzBjOWRiZTZhIiwgImlkIjogIjYxODA3YTQ0ZTllMTQxZTk5OGI2NzgxNGJiMTEwM2M3IiwgInB1YmxpY1VSTCI6ICJodHRwczovL2Nsb3Vkc3BhY2VzLnVydi5jYXQ6ODA4MC92MS9BVVRIXzZkM2I2NTY5N2Q1YzQ4ZDVhYWZmYmI0MzBjOWRiZTZhIn1dLCAiZW5kcG9pbnRzX2xpbmtzIjogW10sICJ0eXBlIjogIm9iamVjdC1zdG9yZSIsICJuYW1lIjogInN3aWZ0In0sIHsiZW5kcG9pbnRzIjogW3siYWRtaW5VUkwiOiAiaHR0cDovL2Nsb3Vkc3BhY2VzLnVydi5jYXQ6MzUzNTcvdjIuMCIsICJyZWdpb24iOiAiUmVnaW9uT25lIiwgImludGVybmFsVVJMIjogImh0dHA6Ly9jbG91ZHNwYWNlcy51cnYuY2F0OjUwMDAvdjIuMCIsICJpZCI6ICIxYzc3MTExY2NiY2M0MjlhOTYwZDJiZWQwNmVhMjk0MiIsICJwdWJsaWNVUkwiOiAiaHR0cDovL2Nsb3Vkc3BhY2VzLnVydi5jYXQ6NTAwMC92Mi4wIn1dLCAiZW5kcG9pbnRzX2xpbmtzIjogW10sICJ0eXBlIjogImlkZW50aXR5IiwgIm5hbWUiOiAia2V5c3RvbmUifV0sICJ1c2VyIjogeyJ1c2VybmFtZSI6ICJleWVvcyIsICJyb2xlc19saW5rcyI6IFtdLCAiaWQiOiAiZDJlOGY3YWZjMGY3NDJhMzkwMzZiMjFkMmVjZGU0ZmEiLCAicm9sZXMiOiBbeyJuYW1lIjogIl9tZW1iZXJfIn0sIHsibmFtZSI6ICJydXNlcnMifV0sICJuYW1lIjogImV5ZW9zIn0sICJtZXRhZGF0YSI6IHsiaXNfYWRtaW4iOiAwLCAicm9sZXMiOiBbIjlmZTJmZjllZTQzODRiMTg5NGE5MDg3OGQzZTkyYmFiIiwgImEwZWVhYTYwNDVjNTRkMGQ4ZTNkY2YzNWM4YWFmMWFiIl19fX0xgf8wgfwCAQEwXDBXMQswCQYDVQQGEwJVUzEOMAwGA1UECBMFVW5zZXQxDjAMBgNVBAcTBVVuc2V0MQ4wDAYDVQQKEwVVbnNldDEYMBYGA1UEAxMPd3d3LmV4YW1wbGUuY29tAgEBMAcGBSsOAwIaMA0GCSqGSIb3DQEBAQUABIGAN6xTCTuXGPCBTkXxFwvVsotye-kb3MfMdqr+fvmdi5JMhamOI-zy97eS-a7qRdq6NhaqGaDsyk90Z3o4mReoS8ZrKWoKVBgI1c+CWdBlVDBpjnP+ISq2sZoy2p1YdRBWkrhWgECAKnoLmcIhs4o5XiQ5XZK-EuyCKJwGTwkCaXA=');
        $this->oauthToken->setExpire('2014-03-01 12:33:33');
        $settings = new Settings();
        $accessorProviderMock = $this->getMock('AccessorProvider');
        $accessorProviderMock->expects($this->once())
            ->method('sendMessage')
            ->with($settings)
            ->will($this->returnValue('{"access": {"token": {"issued_at": "2014-02-28T12:33:33.275377", "expires": "2014-03-01T12:33:33Z", "id": "MIIGBwYJKoZIhvcNAQcCoIIF+DCCBfQCAQExCTAHBgUrDgMCGjCCBOAGCSqGSIb3DQEHAaCCBNEEggTNeyJhY2Nlc3MiOiB7InRva2VuIjogeyJpc3N1ZWRfYXQiOiAiMjAxNC0wMi0yOFQxMjozMzozMy4yNzUzNzciLCAiZXhwaXJlcyI6ICIyMDE0LTAzLTAxVDEyOjMzOjMzWiIsICJpZCI6ICJwbGFjZWhvbGRlciIsICJ0ZW5hbnQiOiB7ImRlc2NyaXB0aW9uIjogIkRlc2NyaXB0aW9uIGV5ZW9zIiwgImVuYWJsZWQiOiB0cnVlLCAiaWQiOiAiNmQzYjY1Njk3ZDVjNDhkNWFhZmZiYjQzMGM5ZGJlNmEiLCAibmFtZSI6ICJleWVvcyJ9fSwgInNlcnZpY2VDYXRhbG9nIjogW3siZW5kcG9pbnRzIjogW3siYWRtaW5VUkwiOiAiaHR0cHM6Ly9jbG91ZHNwYWNlcy51cnYuY2F0OjgwODAvdjEiLCAicmVnaW9uIjogIlJlZ2lvbk9uZSIsICJpbnRlcm5hbFVSTCI6ICJodHRwczovL2Nsb3Vkc3BhY2VzLnVydi5jYXQ6ODA4MC92MS9BVVRIXzZkM2I2NTY5N2Q1YzQ4ZDVhYWZmYmI0MzBjOWRiZTZhIiwgImlkIjogIjYxODA3YTQ0ZTllMTQxZTk5OGI2NzgxNGJiMTEwM2M3IiwgInB1YmxpY1VSTCI6ICJodHRwczovL2Nsb3Vkc3BhY2VzLnVydi5jYXQ6ODA4MC92MS9BVVRIXzZkM2I2NTY5N2Q1YzQ4ZDVhYWZmYmI0MzBjOWRiZTZhIn1dLCAiZW5kcG9pbnRzX2xpbmtzIjogW10sICJ0eXBlIjogIm9iamVjdC1zdG9yZSIsICJuYW1lIjogInN3aWZ0In0sIHsiZW5kcG9pbnRzIjogW3siYWRtaW5VUkwiOiAiaHR0cDovL2Nsb3Vkc3BhY2VzLnVydi5jYXQ6MzUzNTcvdjIuMCIsICJyZWdpb24iOiAiUmVnaW9uT25lIiwgImludGVybmFsVVJMIjogImh0dHA6Ly9jbG91ZHNwYWNlcy51cnYuY2F0OjUwMDAvdjIuMCIsICJpZCI6ICIxYzc3MTExY2NiY2M0MjlhOTYwZDJiZWQwNmVhMjk0MiIsICJwdWJsaWNVUkwiOiAiaHR0cDovL2Nsb3Vkc3BhY2VzLnVydi5jYXQ6NTAwMC92Mi4wIn1dLCAiZW5kcG9pbnRzX2xpbmtzIjogW10sICJ0eXBlIjogImlkZW50aXR5IiwgIm5hbWUiOiAia2V5c3RvbmUifV0sICJ1c2VyIjogeyJ1c2VybmFtZSI6ICJleWVvcyIsICJyb2xlc19saW5rcyI6IFtdLCAiaWQiOiAiZDJlOGY3YWZjMGY3NDJhMzkwMzZiMjFkMmVjZGU0ZmEiLCAicm9sZXMiOiBbeyJuYW1lIjogIl9tZW1iZXJfIn0sIHsibmFtZSI6ICJydXNlcnMifV0sICJuYW1lIjogImV5ZW9zIn0sICJtZXRhZGF0YSI6IHsiaXNfYWRtaW4iOiAwLCAicm9sZXMiOiBbIjlmZTJmZjllZTQzODRiMTg5NGE5MDg3OGQzZTkyYmFiIiwgImEwZWVhYTYwNDVjNTRkMGQ4ZTNkY2YzNWM4YWFmMWFiIl19fX0xgf8wgfwCAQEwXDBXMQswCQYDVQQGEwJVUzEOMAwGA1UECBMFVW5zZXQxDjAMBgNVBAcTBVVuc2V0MQ4wDAYDVQQKEwVVbnNldDEYMBYGA1UEAxMPd3d3LmV4YW1wbGUuY29tAgEBMAcGBSsOAwIaMA0GCSqGSIb3DQEBAQUABIGAN6xTCTuXGPCBTkXxFwvVsotye-kb3MfMdqr+fvmdi5JMhamOI-zy97eS-a7qRdq6NhaqGaDsyk90Z3o4mReoS8ZrKWoKVBgI1c+CWdBlVDBpjnP+ISq2sZoy2p1YdRBWkrhWgECAKnoLmcIhs4o5XiQ5XZK-EuyCKJwGTwkCaXA=", "tenant": {"description": "Description eyeos", "enabled": true, "id": "6d3b65697d5c48d5aaffbb430c9dbe6a", "name": "eyeos"}}, "serviceCatalog": [{"endpoints": [{"adminURL": "https://cloudspaces.urv.cat:8080/v1", "region": "RegionOne", "internalURL": "https://cloudspaces.urv.cat:8080/v1/AUTH_6d3b65697d5c48d5aaffbb430c9dbe6a", "id": "61807a44e9e141e998b67814bb1103c7", "publicURL": "https://cloudspaces.urv.cat:8080/v1/AUTH_6d3b65697d5c48d5aaffbb430c9dbe6a"}], "endpoints_links": [], "type": "object-store", "name": "swift"}, {"endpoints": [{"adminURL": "http://cloudspaces.urv.cat:35357/v2.0", "region": "RegionOne", "internalURL": "http://cloudspaces.urv.cat:5000/v2.0", "id": "1c77111ccbcc429a960d2bed06ea2942", "publicURL": "http://cloudspaces.urv.cat:5000/v2.0"}], "endpoints_links": [], "type": "identity", "name": "keystone"}], "user": {"username": "eyeos", "roles_links": [], "id": "d2e8f7afc0f742a39036b21d2ecde4fa", "roles": [{"name": "_member_"}, {"name": "rusers"}], "name": "eyeos"}, "metadata": {"is_admin": 0, "roles": ["9fe2ff9ee4384b1894a90878d3e92bab", "a0eeaa6045c54d0d8e3dcf35c8aaf1ab"]}}}'));
        $oauthProvider = new OAuthProviderOld($accessorProviderMock);
        $actual = $oauthProvider->verifyDateExpireToken($dateExpire,$currentDate,$settings);
        $this->assertEquals($this->oauthToken,$actual);
    }


}

?>