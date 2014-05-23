__author__ = 'root'

from requests_oauthlib import OAuth1Session
from settings import settings
import sys
import json

class OauthCredentials:
    def __init__(self,requesttokenurl,accesstokenurl):
        self.requesturl = requesttokenurl
        self.accessurl = accesstokenurl

    def getRequestToken(self,oauth):
        token = {}
        request_token = oauth.fetch_request_token(self.requesturl)

        if request_token:
            token={"key": "" + request_token['oauth_token'] + "","secret":"" + request_token['oauth_token_secret'] + ""}

        return token

    def getAccessToken(self,oauth):
        token = {}
        access_token = oauth.fetch_access_token(self.accessurl)

        if access_token:
            token={"key": "" + access_token['oauth_token'] + "","secret":"" + access_token['oauth_token_secret'] + ""}

        return token


if __name__ == "__main__":
    oauthCredentials = OauthCredentials(settings['Stacksync']['urls']['REQUEST_TOKEN_URL'],settings['Stacksync']['urls']['ACCESS_TOKEN_URL'])
    key = settings['Stacksync']['consumer']['key']
    secret = settings['Stacksync']['consumer']['secret']
    callbackurl = settings['Stacksync']['urls']['CALLBACK_URL']

    if len(sys.argv) == 2:
        params = json.loads(str(sys.argv[1]))
        if params.has_key("verifier") and params.has_key('token'):
            token_key =  params['token']['key']
            token_secret = params['token']['secret']
            verifier = params['verifier']
            oauth = OAuth1Session(key, client_secret=secret,resource_owner_key=token_key,resource_owner_secret=token_secret,verifier=verifier)
            print (oauthCredentials.getAccessToken(oauth))
        else:
            print("false")

    elif len(sys.argv) == 1:
        oauth = OAuth1Session(key, client_secret=secret,callback_uri=callbackurl)
        print(oauthCredentials.getRequestToken(oauth))