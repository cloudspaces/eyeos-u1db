__author__ = 'root'

import pymongo
from pymongo import MongoClient
import string
import random
import time
from settings import settings


"""client = MongoClient('localhost', 27017)
db = client.prueba
post = {"name": "Mike","edad":"15"}
id = db.collection.insert(post)

db.collection.remove({})

document ={"consumer_key":"11111","consumer_secret":"AAAAAA"}
db.collection.insert(document)

for post in db.collection.find({"consumer_key":"12345"}):
    print(post)

db.collection.remove({"consumer_key":"12345"})

document = db.collection.find_one({"consumer_key":"12345"})
print(document)

client.drop_database("prueba")

print(client.database_names())

client.disconnect()"""

class mongoDb:
    def __init__(self, host,port,name):
        self.client = MongoClient(host,port)
        self.db = self.client[name]

    def __del__(self):
        self.client.disconnect()

    def insertConsumer(self,consumerKey,consumerSecret):
        document = {"type":"consumer","key":consumerKey,"secret":consumerSecret}
        self.db.collection.insert(document)

    def selectConsumer(self,consumerKey):
        consumerSecret = None
        document = self.db.collection.find_one({"type":"consumer","key":consumerKey})
        if document != None:
            consumerSecret = document['secret']
        return consumerSecret

    def insertRequestToken(self,key,secret,consumerKey):
        document = {"type":"requestToken","consumerKey":consumerKey,"key":key,"secret":secret}
        self.db.collection.insert(document)

    def selectRequestToken(self,consumerKey):
        requestToken = None
        document = self.db.collection.find_one({"type":"requestToken","consumerKey":consumerKey})
        if document != None:
            requestToken = {"key":document['key'],"secret":document['secret']}

        return requestToken

    def insertAccessToken(self,consumerKey,requestTokenKey,key,secret,timeStamp):
        document = {"type":"accessToken","consumerKey":consumerKey,"requestTokenKey":requestTokenKey,"key":key,"secret":secret,"timestamp":timeStamp}
        self.db.collection.insert(document)

    def selectAccessToken(self,consumerKey,requestTokenKey):
        accessToken = None
        document = self.db.collection.find_one({"type":"accessToken","consumerKey":consumerKey,"requestTokenKey":requestTokenKey})
        if document != None:
            accessToken = {"key":document['key'],"secret":document['secret'],"timestamp":document['timestamp']}

        return accessToken

    def updateAccessToken(self,consumerKey,requestTokenKey,key,secret,timeStamp):
        document = self.db.collection.find_one({"type":"accessToken","consumerKey":consumerKey,"requestTokenKey":requestTokenKey})
        if document != None:
            document['key'] = key
            document['secret'] = secret
            document['timestamp'] = timeStamp

        self.db.collection.save(document)

    def getAccessToken(self,consumerKey,requestTokenKey):
        result = None
        key = self.id_generator()
        secret = self.id_generator()
        timestamp = int(time.time() + settings['token']['expires'])
        document = self.db.collection.find_one({"type":"accessToken","consumerKey":consumerKey,"requestTokenKey":requestTokenKey})
        if document == None:
            self.insertAccessToken(consumerKey,requestTokenKey,key,secret,timestamp)
            result = {"key":key,"secret":secret}
        else:
            if int(time.time()) < document['timestamp']:
                result = {"key":document['key'],"secret":document['secret']}
            else:
                self.updateAccessToken(consumerKey,requestTokenKey,key,secret,timestamp)
                result = {"key":key,"secret":secret}

        return result

    def getResourceToken(self,consumerKey,accessTokenKey):
        result = None
        document = self.db.collection.find_one({"type":"accessToken","consumerKey":consumerKey,"key":accessTokenKey})
        if document != None:
            result = {"key":document['key'],"secret":document['secret']}

        return result

    def id_generator(self,size=15,chars = string.ascii_uppercase + string.digits):
        return ''.join(random.choice(chars) for i in range(size))


