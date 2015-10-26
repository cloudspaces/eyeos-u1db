__author__ = 'root'

from requests_oauthlib import OAuth1Session
from oauthlib.common import urldecode
import json

class OAuthRequest(OAuth1Session):
    logger = None
    def _fetch_token(self, url):
        self.writeLog("Function: ---> fetch_token")
        self.writeLog("URL: " + url)
        request = self.get(url)
        if(not(isinstance(request,dict) and request.has_key('error'))):
            token = dict(urldecode(request))
            self._populate_attributes(token)
            return token

        return request

    def get(self, url, **kwargs):
        kwargs.setdefault('allow_redirects', True)
        return self.createRequest(self.request('GET', url, **kwargs))

    def put(self, url, data=None, **kwargs):
        return self.createRequest(self.request('PUT', url, data=data, **kwargs))

    def post(self, url, data=None, **kwargs):
        return self.createRequest(self.request('POST', url, data=data, **kwargs))

    def delete(self, url, **kwargs):
        return self.createRequest(self.request('DELETE', url, **kwargs))

    def createRequest(self,request):

        try:
            self.writeLog('Request header: ---->' )
            self.writeLog(request.request.headers)

            if request.request.body != None:
                self.writeLog('Request body: ---->')
                self.writeLog(request.request.body)

            self.writeLog("Status Code: " + str(request.status_code))
            self.writeLog("Content: " + request.content)
        except:
            pass

        if request.status_code == 200 or request.status_code == 201 or request.status_code == 202:
            return request.content
        else:
            error = {}
            error['error'] = request.status_code
            error['description'] = request.text
            return error

    def setLogger(self,logger):
        self.logger = logger

    def writeLog(self,message):
        if message != None and self.logger != None:
            self.logger.info(message)