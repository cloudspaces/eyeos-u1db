__author__ = 'root'

import unittest
from oauth import OAuthConsumer
from oauth import OAuthToken
from mock import Mock
from mongodb import mongoDb

class oauthConsumerTest(unittest.TestCase):

    def setUp(self):
        self.sut = OAuthConsumer()

    def tearDown(self):
        self.sut = None

    """
    method: getConsumer
    when: called
    with: key
    should: calledSelectConsumerMongoDb
    """
    def test_getConsumer_called_key_calledSelectConsumerMongoDb(self):
        consumerKey = "1234"
        consumerSecret = "ABCD"
        self.sut.selectConsumer = Mock()
        self.sut.selectConsumer.return_value = consumerSecret
        self.sut.getConsumer(consumerKey)
        self.sut.selectConsumer.assert_called_once_with(consumerKey)
        self.assertEquals(consumerSecret,self.sut.secret)

class oauthTokenTest(unittest.TestCase):

    def setUp(self):
        self.sut = OAuthToken()

    def tearDown(self):
        self.sut = None

    """
    method: getRequestToken
    when: called
    with: consumerKey
    should: calledSelectRequestTokenMongoDb
    """
    def test_getRequestToken_called_consumerKey_calledSelectRequestTokenMongoDb(self):
        self.sut.selectRequestToken = Mock()
        self.sut.selectRequestToken.return_value = {"key":"4561","secret":"FGRT"}
        self.sut.getRequestToken("1234")
        self.sut.selectRequestToken.assert_called_once_with("1234")
        self.assertEquals("4561",self.sut.key)

    """
    method: getAccessToken
    when: called
    with: consumerKeyAndRequestTokenKey
    should: calledGetAccessTokenMongoDb
    """
    def test_getAccessToken_called_consumerKeyAndRequestTokenKey_calledGetAccessTokenMongoDb(self):
        self.sut.getAccessTokenDb = Mock()
        self.sut.getAccessTokenDb.return_value = {"key":"ABC521","secret":"CSSDDFFFF"}
        self.sut.getAccessToken("1234","ABCCD")
        self.sut.getAccessTokenDb.assert_called_once_with("1234","ABCCD")
        self.assertEquals("ABC521",self.sut.key)

    """
    method: getResourceToken
    when: called
    with: consumerKeyAndAccessTokenKey
    should: calledGetResourceTokenMongoDb
    """
    def test_getResourceToken_called_consumerKeyAndAccessTokenKey_calledGetResourceTokenMongoDb(self):
        self.sut.getResourceTokenDb = Mock()
        self.sut.getResourceTokenDb.return_value = {"key":"ABC521","secret":"CSSDDFFFF"}
        self.sut.getResourceToken("1234","ABCCD")
        self.sut.getResourceTokenDb.assert_called_once_with("1234","ABCCD")
        self.assertEquals("ABC521",self.sut.key)


