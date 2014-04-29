__author__ = 'root'

from settings import settings
import oauth_client
import httplib
import time
import sys
import json

class SimpleOAuthClient(oauth_client.OAuthClient):

    def __init__(self, server, port=httplib.HTTP_PORT, request_token_url='', access_token_url='', authorization_url=''):
        self.server = server
        self.port = port
        self.request_token_url = request_token_url
        self.access_token_url = access_token_url
        self.authorization_url = authorization_url
        self.connection = httplib.HTTPConnection("%s:%d" % (self.server, self.port))

    def fetch_request_token(self, oauth_request):
        self.connection.request(oauth_request.http_method, self.request_token_url, headers=oauth_request.to_header())
        response = self.connection.getresponse()
        return oauth_client.OAuthToken.from_string(response.read())

    def fetch_access_token(self, oauth_request):
        self.connection.request(oauth_request.http_method, self.access_token_url, headers=oauth_request.to_header())
        response = self.connection.getresponse()
        return oauth_client.OAuthToken.from_string(response.read())

    def authorize_token(self, oauth_request):
        self.connection.request(oauth_request.http_method, self.authorization_url, headers=oauth_request.to_header())
        response = self.connection.getresponse()
        return response.read()

class Credentials:
    def __init__(self):
        self.oauthClient = SimpleOAuthClient(settings['Oauth']['server'],settings['Oauth']['port'],settings['Oauth']['urls']['REQUEST_TOKEN_URL'],settings['Oauth']['urls']['ACCESS_TOKEN_URL'],settings['Oauth']['urls']['AUTHORIZATION_URL'])
        self.consumer = oauth_client.OAuthConsumer()
        self.consumer.setKey(settings['Oauth']['consumer']['key'])
        self.consumer.setSecret(settings['Oauth']['consumer']['secret'])
        self.signature_method_plaintext = oauth_client.OAuthSignatureMethod_PLAINTEXT()
        self.signature_method_hmac_sha1 = oauth_client.OAuthSignatureMethod_HMAC_SHA1()

    def getCredentials(self):
        result = ""
        requestToken = self.getRequestToken()
        if len(requestToken.key) > 0:
            verifier = self.authorizeRequestToken(requestToken)
            if verifier == settings['Oauth']['verifier']:
                accessToken = self.getAccessToken(requestToken,verifier)
                if len(accessToken) > 0:
                    result = accessToken
        return result

    def getRequestToken(self):
        oauth_request = oauth_client.OAuthRequest.from_consumer_and_token(self.consumer, callback=settings['Oauth']['urls']['CALLBACK_URL'], http_url=self.oauthClient.request_token_url)
        oauth_request.sign_request(self.signature_method_plaintext, self.consumer, None)
        token = self.oauthClient.fetch_request_token(oauth_request)
        return token

    def authorizeRequestToken(self,token):
        oauth_request = oauth_client.OAuthRequest.from_token_and_callback(token=token, oauth_consumer=self.consumer, http_url=self.oauthClient.authorization_url)
        response = self.oauthClient.authorize_token(oauth_request)

        if len(response) > 0:
            import urlparse, cgi
            query = urlparse.urlparse(response)[4]
            params = cgi.parse_qs(query, keep_blank_values=False)
            verifier = params['oauth_verifier'][0]
            return verifier
        else:
            return ""

    def getAccessToken(self,token,verifier):
        result = ''
        oauth_request = oauth_client.OAuthRequest.from_consumer_and_token(self.consumer, token=token, verifier=verifier, http_url=self.oauthClient.access_token_url)
        oauth_request.sign_request(self.signature_method_plaintext, self.consumer, token)
        access_token = self.oauthClient.fetch_access_token(oauth_request)
        if len(access_token.key) > 0:
            result = self.formatParams(token,access_token,verifier)
        return result

    def formatParams(self,request_token,access_token,verifier):
        result = ''
        """oauth_request = oauth_client.OAuthRequest.from_consumer_and_token(self.consumer, token=access_token, http_method='POST',http_url="https://192.168.3.118:8080/calendar.u1db/sync-from/0c501db709b140f0a0515d8057c3b2b1")
        oauth_request.sign_request(self.signature_method_hmac_sha1, self.consumer, access_token)
        params = str(oauth_request.parameters)
        print(params)
        if len(params) > 0:
            valor = params.replace("'", '"')
            #result = '{"credentials":' + valor + ',"request_token":{"key":"' + request_token.key + '","secret":"' + request_token.secret + '"},"verifier":"' + verifier + '"}'"""
        result = '{"credentials":{"token_key":"' + access_token.key + '","token_secret":"' + access_token.secret + '","consumer_key":"' + self.consumer.key + '","consumer_secret":"' + self.consumer.secret + '"},"request_token":{"key":"' + request_token.key + '","secret":"' + request_token.secret + '"},"verifier":"' + verifier + '"}'
        return result

if __name__ == "__main__":
    credentials = Credentials()
    if len(sys.argv) == 2:
        params = json.loads(str(sys.argv[1]))
        if params.has_key("verifier") and params.has_key('request_token'):
            verifier = params['verifier']
            token = oauth_client.OAuthToken()
            token.setKey(params['request_token']['key'])
            token.setSecret(params['request_token']['secret'])
            print (credentials.getAccessToken(token,verifier))
        else:
            print("")

    elif len(sys.argv) == 1:
        print(credentials.getCredentials())