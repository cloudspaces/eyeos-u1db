__author__ = 'root'

import unittest
from OauthCredentials import OauthCredentials
from mock import Mock
from requests_oauthlib import OAuth1Session

class OauthCredentialsTest (unittest.TestCase):
    def setUp(self):
        self.key = "eyeos"
        self.secret = "eyeosSecret"
        self.callbackurl = 'http://eyeos.com/callback.php'
        self.requesttokenurl = "http://eyeos/request_token"
        self.accesstokenurl =  "http://eyeos/access_token"
        self.oauthCredentials = OauthCredentials(self.requesttokenurl,self.accesstokenurl)

    def tearDown(self):
        self.oauthCredentials = None

    """
    method: getRequestToken
    when: called
    with: consumerAndCallback
    should: returnToken
    """
    def test_getRequestToken_called_consumerAndCallback_returnToken(self):
        expected = {"key":"HIJK","secret":"LMN"}
        oauth = OAuth1Session(self.key, client_secret=self.secret,callback_uri=self.callbackurl)
        oauth.fetch_request_token = Mock()
        oauth.fetch_request_token.return_value = {"oauth_token":"HIJK","oauth_token_secret":"LMN"}
        result = self.oauthCredentials.getRequestToken(oauth)
        oauth.fetch_request_token.assert_called_once_with(self.requesttokenurl)
        self.assertEquals(expected,result)

    """
    method: getAccessToken
    when: called
    with: consumerAndRequestTokenAndVerifier
    should: returnToken
    """
    def test_getAccessToken_called_consumerAndRequestTokenAndVerifier_returnToken(self):
        expected = {"key":"MNOP","secret":"STVM"}
        oauth = OAuth1Session(self.key, client_secret=self.secret,resource_owner_key="ABCD",resource_owner_secret="EFG",verifier='verifier')
        oauth.fetch_access_token = Mock()
        oauth.fetch_access_token.return_value = {"oauth_token":"MNOP","oauth_token_secret":"STVM"}
        result = self.oauthCredentials.getAccessToken(oauth)
        oauth.fetch_access_token.assert_called_once_with(self.accesstokenurl)
        self.assertEquals(expected,result)



