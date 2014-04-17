__author__ = 'root'

import unittest
from mongodb import mongoDb
from pymongo import MongoClient
import time

class mongodbTest(unittest.TestCase):

    def setUp(self):
        self.sut = mongoDb("localhost",27017,"test")
        self.consumerKey = "1234"
        self.consumerSecret = "ABCD"
        self.requestTokenKey = "4567"
        self.requestTokenSecret = "EFGH"
        self.accessTokenKey = "8901"
        self.accessTokenSecret = "IJKL"
        self.timestampNow = int(time.time())

    def tearDown(self):
        self.sut.client.drop_database('test')

    """
    method: insertConsumer
    when: called
    with: consumerKeyAndConsumerSecret
    should: insertCorrect
    """
    def test_insertConsumer_called_consumerKeyAndConsumerSecret_insertCorrect(self):
        self.sut.insertConsumer(self.consumerKey,self.consumerSecret)
        result = self.sut.db.collection.find({"type":"consumer","key":self.consumerKey}).count()
        self.assertEquals(1,result)

    """
    method: selectConsumer
    when: called
    with: consumerKey
    should: returnConsumerSecret
    """
    def test_selectConsumer_called_consumerKey_returnConsumerSecret(self):
        self.sut.db.collection.insert({"type":"consumer","key":self.consumerKey,"secret":self.consumerSecret})
        result = self.sut.selectConsumer(self.consumerKey)
        self.assertEquals(self.consumerSecret,result)

    """
    method: insertRequestToken
    when: called
    with: tokenKeyAndTokenSecretAndConsumerKey
    should: insertCorrect
    """
    def test_insertRequestToken_called_tokenKeyAndTokenSecretAndConsumerKey_insertCorrect(self):
        self.sut.insertRequestToken(self.requestTokenKey,self.requestTokenSecret,self.consumerKey)
        result = self.sut.db.collection.find({"type":"requestToken","consumerKey":self.consumerKey,"key":self.requestTokenKey}).count()
        self.assertEquals(1,result)


    """
    method: selectRequestToken
    when: called
    with: consumerKey
    should: returnJsonRequestToken
    """
    def test_selectRequestToken_called_consumerKey_returnJsonRequestToken(self):
        self.sut.db.collection.insert({"type":"requestToken","consumerKey":self.consumerKey,"key":self.requestTokenKey,"secret":self.requestTokenSecret})
        result = self.sut.selectRequestToken(self.consumerKey)
        self.assertEquals({"key":self.requestTokenKey,"secret":self.requestTokenSecret},result)

    """
    method: insertAccessToken
    when: called
    with: consumerKeyAndRequestTokenKeyAndAccessTokenKeyAndAccessTokenSecretAndTimeStamp
    should: insertCorrect
    """
    def test_insertAccessToken_called_consumerKeyAndRequestTokenKeyAndAccessTokenKeyAndAccessTokenSecretAndTimeStamp_insertCorrect(self):
        self.sut.insertAccessToken(self.consumerKey,self.requestTokenKey,self.accessTokenKey,self.accessTokenSecret,self.timestampNow)
        result = self.sut.db.collection.find({"type":"accessToken","consumerKey":self.consumerKey,"requestTokenKey":self.requestTokenKey,"key":self.accessTokenKey}).count()
        self.assertEquals(1,result)

    """
    method: selectAccessToken
    when: called
    with: consumerKeyAndRequestToken
    should: returnJsonAccessToken
    """
    def test_selectAccessToken_called_consumerKeyAndRequestToken_returnJsonAccessToken(self):
        self.sut.db.collection.insert({"type":"accessToken","consumerKey":self.consumerKey,"requestTokenKey":self.requestTokenKey,"key":self.accessTokenKey,"secret":self.accessTokenSecret,"timestamp":self.timestampNow})
        result = self.sut.selectAccessToken(self.consumerKey,self.requestTokenKey)
        self.assertEquals({"key":self.accessTokenKey,"secret":self.accessTokenSecret,"timestamp":self.timestampNow},result)

    """
    method: updateAccessToken
    when: called
    with: consumerKeyAndRequestTokenKeyAndAccessTokenKeyAndAccessTokenSecretAndTimeStamp
    should: updateCorrect
    """
    def test_updateAccessToken_called_consumerKeyAndRequestTokenKeyAndAccessTokenKeyAndAccessTokenSecretAndTimeStamp_updateCorrect(self):
        self.sut.db.collection.insert({"type":"accessToken","consumerKey":self.consumerKey,"requestTokenKey":self.requestTokenKey,"key":self.accessTokenKey,"secret":self.accessTokenSecret,"timestamp":self.timestampNow})
        self.sut.updateAccessToken(self.consumerKey,self.requestTokenKey,"1111",self.accessTokenSecret,self.timestampNow)
        result = self.sut.db.collection.find({"type":"accessToken","consumerKey":self.consumerKey,"requestTokenKey":self.requestTokenKey,"key":"1111"}).count()
        self.assertEquals(1,result)

    """
    method: getAcessToken
    when: called
    with: consumerKeyAndRequestTokenKey
    should: returnNewAccessToken
    """
    def test_getAcessToken_called_consumerKeyAndRequestTokenKey_returnNewAccessToken(self):
        token = self.sut.getAccessToken(self.consumerKey,self.requestTokenKey)
        result = self.sut.db.collection.find_one({"type":"accessToken","consumerKey":self.consumerKey,"requestTokenKey":self.requestTokenKey})
        self.assertEquals(result['key'],token['key'])

    """
    method: getAccessToken
    when: called
    with: consumerKeyAndRequestTokenKey
    should: returnExistsAccessToken
    """
    def test_getAccessToken_called_consumerKeyAndRequestTokenKey_returnExistsAccessToken(self):
        timestamp = int(time.time() + 86400)
        self.sut.db.collection.insert({"type":"accessToken","consumerKey":self.consumerKey,"requestTokenKey":self.requestTokenKey,"key":self.accessTokenKey,"secret":self.accessTokenSecret,"timestamp":timestamp})
        token = self.sut.getAccessToken(self.consumerKey,self.requestTokenKey)
        self.assertEquals(self.accessTokenKey,token['key'])

    """
    method: getAccessToken
    when: called
    with: consumerKeyAndRequestTokenKey
    should: returnModifyAccessToken
    """
    def test_getAccessToken_called_consumerKeyAndRequestTokenKey_returnModifyAccessToken(self):
        timestamp = int(time.time() - 86400)
        self.sut.db.collection.insert({"type":"accessToken","consumerKey":self.consumerKey,"requestTokenKey":self.requestTokenKey,"key":self.accessTokenKey,"secret":self.accessTokenSecret,"timestamp":timestamp})
        token = self.sut.getAccessToken(self.consumerKey,self.requestTokenKey)
        self.assertNotEquals(self.accessTokenKey,token['key'])

    """
    method: getResourceToken
    when: called
    with: consumerKeyAndAccessToken
    should: returnAccessToken
    """
    def test_getResourceToken_called_consumerKeyAndAccessToken_returnAccessToken(self):
        self.sut.db.collection.insert({"type":"accessToken","consumerKey":self.consumerKey,"requestTokenKey":self.requestTokenKey,"key":self.accessTokenKey,"secret":self.accessTokenSecret,"timestamp":self.timestampNow})
        token = self.sut.getResourceToken(self.consumerKey,self.accessTokenKey)
        self.assertEquals(self.accessTokenKey,token['key'])