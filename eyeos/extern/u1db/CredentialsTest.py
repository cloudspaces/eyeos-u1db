__author__ = 'root'

import unittest
from Credentials import Credentials
import oauth_client
from mock import Mock

class CredentialsTest (unittest.TestCase):
    def setUp(self):
        self.credentials = Credentials()
        self.requestToken = oauth_client.OAuthToken()
        self.requestToken.setKey('ABCD')
        self.requestToken.setSecret('DBCA')
        self.verifier = 'verifier'

    def tearDown(self):
        self.credentials = None

    """
    method: getCredentials
    when: called
    with: withoutParams
    should: returnJson
    """
    def test_getCredentials_called_withoutParams_returnJson(self):

        accessToken = oauth_client.OAuthToken()
        accessToken.setKey('AAAA')
        accessToken.setSecret('dfed')
        #expected = '{"credentials":{"oauth_version":"1.0","oauth_token":"' + accessToken.key + ',"oauth_nonce":"91107164","oauth_timestamp":1398333763,"oauth_signature":"FvKD3aclZhOFshZl5Ve2Q1nfII=","oauth_consumer_key":"1234","oauth_signature_method":"HMAC-SHA1"},"request_token":{"key":"' + self.requestToken.key + '","secret":"' + self.requestToken.secret + '"},"verifier":"' + self.verifier + '"}'
        expected ='{"credentials":{"token_key":"' + accessToken.key + '","token_secret":"' + accessToken.secret + '","consumer_key":"keySebas","consumer_secret":"secretSebas"},"request_token":{"key":"' + self.requestToken.key + '","secret":"' + self.requestToken.secret + '"},"verifier":"' + self.verifier + '"}'
        self.credentials.getRequestToken = Mock()
        self.credentials.getRequestToken.return_value = self.requestToken
        self.credentials.authorizeRequestToken = Mock()
        self.credentials.authorizeRequestToken.return_value = self.verifier
        self.credentials.getAccessToken = Mock()
        self.credentials.getAccessToken.return_value = expected
        result = self.credentials.getCredentials()
        self.credentials.authorizeRequestToken.assert_called_once_with(self.requestToken)
        self.credentials.getAccessToken.assert_called_once_with(self.requestToken,self.verifier)
        self.assertEquals(expected,result)

    """
    method: getRequestToken
    when: called
    with: withoutParams
    should: returnToken
    """
    def test_getRequestToken_called_withoutParams_returnToken(self):
        self.credentials.oauthClient.fetch_request_token = Mock()
        self.credentials.oauthClient.fetch_request_token.return_value = self.requestToken
        result = self.credentials.getRequestToken()
        self.assertEquals(self.requestToken,result)

    """
    method: authorizeRequestToken
    when: called
    with: token
    should: returnVerifier
    """
    def test_authorizeRequestToken_called_Token_returnVerifier(self):
        self.credentials.oauthClient.authorize_token = Mock()
        self.credentials.oauthClient.authorize_token.return_value = "http://192.168.3.118:8080/request_token_ready?oauth_verifier=verifier"
        result = self.credentials.authorizeRequestToken(self.requestToken)
        self.assertEquals(self.verifier,result)

    """
    method: getAccessToken
    when: called
    with: TokenAndVerifier
    should: returnToken
    """
    def test_getAccessToken_called_TokenAndVerifier_returnToken(self):
        accessToken =  oauth_client.OAuthToken()
        accessToken.setKey('EFGH')
        accessToken.setSecret('IJKL')
        #expected = '{"credentials":{"oauth_version":"1.0","oauth_token":"' + accessToken.key + '","oauth_nonce":"91107164","oauth_timestamp":1398333763,"oauth_signature":"FvKD3aclZhOFshZl5Ve2Q1nfII=","oauth_consumer_key":"keySebas","oauth_signature_method":"HMAC-SHA1"},"request_token":{"key":"' + self.requestToken.key + '","secret":"' + self.requestToken.secret + '"},"verifier":"' + self.verifier + '"}'
        expected ='{"credentials":{"token_key":"' + accessToken.key + '","token_secret":"' + accessToken.secret + '","consumer_key":"keySebas","consumer_secret":"secretSebas"},"request_token":{"key":"' + self.requestToken.key + '","secret":"' + self.requestToken.secret + '"},"verifier":"' + self.verifier + '"}'
        self.credentials.oauthClient.fetch_access_token = Mock()
        self.credentials.oauthClient.fetch_access_token.return_value = accessToken
        self.credentials.formatParams = Mock()
        self.credentials.formatParams.return_value = expected
        result = self.credentials.getAccessToken(self.requestToken,self.verifier)
        self.credentials.formatParams.assert_called_once_with(self.requestToken,accessToken,self.verifier)
        self.assertEquals(expected,result)