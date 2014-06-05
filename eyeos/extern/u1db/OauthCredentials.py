__author__ = 'root'

from settings import settings
import sys
import json
from oauthlib.oauth1 import SIGNATURE_PLAINTEXT
from OAuthRequest import OAuthRequest
import urllib
import types

class OauthCredentials:
    def __init__(self,requesttokenurl,accesstokenurl,resourceurl,version):
        self.requesturl = requesttokenurl
        self.accessurl = accesstokenurl
        self.resourceurl = resourceurl
        self.version = version

    def getRequestToken(self,oauth):
        token = {}
        try:
            self.createHeader(oauth)
            request_token = oauth.fetch_request_token(self.requesturl)
            if request_token:
                token=json.dumps({"key": "" + request_token['oauth_token'] + "","secret":"" + request_token['oauth_token_secret'] + ""})
        except ValueError as e:
            pass

        return token

    def getAccessToken(self,oauth):
        token = {}
        try:
            self.createHeader(oauth)
            access_token = oauth.fetch_access_token(self.accessurl)
            if access_token:
                token=json.dumps({"key": "" + access_token['oauth_token'] + "","secret":"" + access_token['oauth_token_secret'] + ""})
        except ValueError as e:
            pass
        return token

    def getMetadata(self,oauth,file,id,contents = None):
        url = self.getUrl(file,id,contents)
        self.createHeader(oauth)
        result = oauth.get(url)
        return self.createRequest(result)

    def updateMetadata(self,oauth,file,id,name = None,parent = None):
        url = self.getUrl(file,id)
        self.createHeader(oauth)
        data = {}
        if name:
            data['filename'] = name
        if parent:
            data['parent_id'] = parent
        result = oauth.put(url,data)
        return self.createRequest(result)

    def createMetadata(self,oauth,file,name,parent = None,path = None):
        dataFile = None
        url = self.getUrl(file)
        self.createHeader(oauth)

        data = {}
        data['name'] = name
        if parent and parent != 'null':
            data['parent'] = str(parent)

        if file:
            self.file = open(path,"r")
            if self.file:
                dataFile = self.file.read()
                self.file.close()
            params =  urllib.urlencode(data)
            url += "?" + params
        else:
            dataFile = json.dumps(data)

        result = oauth.post(url,dataFile)
        return self.createRequest(result)

    def uploadFile(self,oauth,id,path):
        metadata = 'false'
        self.file = open(path,"r")
        if self.file:
            self.createHeader(oauth)
            result = oauth.put(self.resourceurl + "file/" + str(id) + "/data",self.file.read())
            if(not(isinstance(result,dict) and result.has_key('error'))):
                metadata = 'true'
            elif result ['error'] == 403:
                metadata = result['error']
            self.file.close()
        return metadata

    def downloadFile(self,oauth,id,path):
        metadata = 'false'
        self.createHeader(oauth)
        result = oauth.get(self.resourceurl + "file/" + str(id) + "/data")
        #if type(result) is str or type(result) is bin:
        if isinstance(result,types.StringTypes):
            file = open(path,'w')
            if file:
                file.write(result)
                file.close()
            metadata = 'true'
        elif isinstance(result,dict) and result.has_key('error') and result['error'] == 403:
            metadata = result['error']
        return metadata

    def deleteMetadata(self,oauth,file,id):
        url = self.getUrl(file,id)
        self.createHeader(oauth)
        result= oauth.delete(url)
        return self.createRequest(result)

    def createHeader(self,oauth):
        oauth.headers['StackSync-API'] = self.version

    def getUrl(self,file,id = None,contents = None):
        url = self.resourceurl

        if file == True:
            url += "file"
        else:
            url += "folder"

        if id != None:
            url += "/" + str(id)

        if contents:
            url += "/contents"

        return url

    def replaceNull(self,data):
        for i, j in data.items():
            if j == None:
                data[i] = "null"

        if data.has_key('contents'):
            for file in data['contents']:
                self.replaceNull(file)

        return data

    def createRequest(self,result):
        if not(isinstance(result,dict)):
            result = json.loads(result)

        metadata = 'false'
        if isinstance(result,dict):
            if not result.has_key("error"):
                metadata = json.dumps(self.replaceNull(result))
            elif result['error'] == 403:
                metadata = result['error']

        return metadata


if __name__ == "__main__":
    oauthCredentials = OauthCredentials(settings['Stacksync']['urls']['REQUEST_TOKEN_URL'],settings['Stacksync']['urls']['ACCESS_TOKEN_URL'],settings['Stacksync']['urls']['RESOURCE_URL'],settings['Stacksync']['version'])
    key = settings['Stacksync']['consumer']['key']
    secret = settings['Stacksync']['consumer']['secret']
    callbackurl = settings['Stacksync']['urls']['CALLBACK_URL']
    result = None

    if len(sys.argv) == 2:
        params = json.loads(str(sys.argv[1]))
        if params.has_key('metadata') and params.has_key('token'):
            token_key = params['token']['key']
            token_secret = params['token']['secret']
            metadata = params['metadata']
            type = metadata['type']
            oauth = OAuthRequest(key, client_secret=secret,resource_owner_key=token_key,resource_owner_secret=token_secret)
            if type == "get":
                result = oauthCredentials.getMetadata(oauth,metadata['file'],metadata['id'],metadata['contents'])
            elif type == "update":
                result = oauthCredentials.updateMetadata(oauth,metadata['file'],metadata['id'],metadata['filename'],metadata['parent_id'])
            elif type == "create":
                result = oauthCredentials.createMetadata(oauth,metadata['file'],metadata['filename'],metadata['parent_id'],metadata['path'])
            elif type == 'upload':
                result = oauthCredentials.uploadFile(oauth,metadata['id'],metadata['path'])
            elif type == 'download':
                result = oauthCredentials.downloadFile(oauth,metadata['id'],metadata['path'])
            elif type == 'delete':
                result = oauthCredentials.deleteMetadata(oauth,metadata['file'],metadata['id'])
        elif params.has_key("verifier") and params.has_key('token'):
            token_key =  params['token']['key']
            token_secret = params['token']['secret']
            verifier = params['verifier']
            oauth = OAuthRequest(key, client_secret=secret,resource_owner_key=token_key,resource_owner_secret=token_secret,verifier=verifier,signature_method=SIGNATURE_PLAINTEXT)
            result = oauthCredentials.getAccessToken(oauth)
    elif len(sys.argv) == 1:
        oauth = OAuthRequest(key, client_secret=secret,callback_uri=callbackurl,signature_method=SIGNATURE_PLAINTEXT)
        result = oauthCredentials.getRequestToken(oauth)
    if result:
        if type != 'download':
            print(str(result))
        else:
            print(result)
    else:
        print('false')