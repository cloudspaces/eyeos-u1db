__author__ = 'root'

import urllib2
import httplib
import traceback
import oauth
from BaseHTTPServer import BaseHTTPRequestHandler, HTTPServer
from settings import settings
from mongodb import mongoDb
import sys, os


class MockOAuthDataStore(oauth.OAuthDataStore):

    def __init__(self):
        self.mongoDb = mongoDb(settings['MongoDb']['host'],settings['MongoDb']['port'],settings['MongoDb']['name'])
        self.consumer = oauth.OAuthConsumer()
        self.consumer.setDb(self.mongoDb)
        self.request_token = oauth.OAuthToken()
        self.request_token.setDb(self.mongoDb)
        self.access_token = oauth.OAuthToken()
        self.access_token.setDb(self.mongoDb)
        self.nonce = 'nonce'
        self.verifier = settings['VERIFIER']

    def lookup_consumer(self, key):
        if key == self.consumer.key:
            return self.consumer
        return None

    def lookup_token(self, token_type, token):
        token_attrib = getattr(self, '%s_token' % token_type)
        if token == token_attrib.key:
            ## HACK
            token_attrib.set_callback(settings['Urls']['CALLBACK_URL'])
            return token_attrib
        return None

    def lookup_nonce(self, oauth_consumer, oauth_token, nonce):
        if oauth_token and oauth_consumer.key == self.consumer.key and (oauth_token.key == self.request_token.key or oauth_token.key == self.access_token.key) and nonce == self.nonce:
            return self.nonce
        return None

    def fetch_request_token(self, oauth_consumer, oauth_callback):
        if oauth_consumer.key == self.consumer.key:
            if oauth_callback:
                # want to check here if callback is sensible
                # for mock store, we assume it is
                self.request_token.set_callback(oauth_callback)
            return self.request_token
        return None

    def fetch_access_token(self, oauth_consumer, oauth_token, oauth_verifier):
        if oauth_consumer.key == self.consumer.key and oauth_token.key == self.request_token.key and oauth_verifier == self.verifier:
            # want to check here if token is authorized
            # for mock store, we assume it is
            return self.access_token
        return None

    def authorize_request_token(self, oauth_token, user):
        if oauth_token.key == self.request_token.key:
            # authorize the request token in the store
            # for mock store, do nothing
            return self.request_token
        return None

class RequestHandler(BaseHTTPRequestHandler):
    def __init__(self, *args, **kwargs):
        self.oauth_server = oauth.OAuthServer(MockOAuthDataStore())
        self.oauth_server.add_signature_method(oauth.OAuthSignatureMethod_PLAINTEXT())
        self.oauth_server.add_signature_method(oauth.OAuthSignatureMethod_HMAC_SHA1())
        BaseHTTPRequestHandler.__init__(self, *args, **kwargs)

    def send_oauth_error(self, err=None):
        # send a 401 error
        self.send_error(401, str(err.message))
        # return the authenticate header
        header = oauth.build_authenticate_header(realm='http://192.168.3.118:8080')
        for k, v in header.iteritems():
            self.send_header(k, v)

    def do_GET(self):

        """print(self.command)
        print(self.path)"""

        postdata = None
        if self.command == 'POST' or self.command == 'PUT':
            try:
                length = int(self.headers.getheader('content-length'))
                postdata = self.rfile.read(length)
            except:
                pass

        oauth_request = oauth.OAuthRequest.from_request(self.command, self.path, headers=self.headers, query_string=postdata)

        if self.path.startswith(settings['Urls']['REQUEST_TOKEN_URL']):
            try:
                self.oauth_server.data_store.consumer.getConsumer(oauth_request.get_parameter('oauth_consumer_key'))
                self.oauth_server.data_store.request_token.getRequestToken(oauth_request.get_parameter('oauth_consumer_key'))
                token = self.oauth_server.fetch_request_token(oauth_request)
                self.send_response(200, 'OK')
                self.end_headers()
                self.wfile.write(token.to_string())
            except oauth.OAuthError, err:
                self.send_oauth_error(err)

        if self.path.startswith(settings['Urls']['AUTHORIZATION_URL']):
            try:
                self.oauth_server.data_store.consumer.getConsumer(oauth_request.get_parameter('oauth_consumer_key'))
                self.oauth_server.data_store.request_token.getRequestToken(oauth_request.get_parameter('oauth_consumer_key'))
                token = self.oauth_server.fetch_request_token(oauth_request)
                tokenClient = oauth.OAuthToken()
                tokenClient.setKey(oauth_request.get_parameter('oauth_token'))
                token = self.oauth_server.authorize_token(tokenClient, None)
                token.set_verifier(settings['VERIFIER'])
                self.send_response(200, 'OK')
                self.end_headers()
                self.wfile.write(token.get_callback_url())
            except oauth.OAuthError, err:
                self.send_oauth_error(err)

        if(self.path.startswith(settings['Urls']['ACCESS_TOKEN_URL'])):
            try:
                self.oauth_server.data_store.consumer.getConsumer(oauth_request.get_parameter('oauth_consumer_key'))
                self.oauth_server.data_store.request_token.getRequestToken(oauth_request.get_parameter('oauth_consumer_key'))
                self.oauth_server.data_store.access_token.getAccessToken(oauth_request.get_parameter('oauth_consumer_key'),oauth_request.get_parameter('oauth_token'))
                token = self.oauth_server.fetch_access_token(oauth_request)
                self.send_response(200, 'OK')
                self.end_headers()
                self.wfile.write(token.to_string())
            except oauth.OAuthError, err:
                self.send_oauth_error(err)

        if self.path.find('/sync-from/') != -1:
            try:
                url = settings['Server']['header'] + settings['Server']['host'] + ":" + str(settings['Server']['port']) + self.path
                oauth_request.http_url = url
                self.oauth_server.data_store.consumer.getConsumer(oauth_request.get_parameter('oauth_consumer_key'))
                self.oauth_server.data_store.request_token.getRequestToken(oauth_request.get_parameter('oauth_consumer_key'))
                self.oauth_server.data_store.access_token.getResourceToken(oauth_request.get_parameter('oauth_consumer_key'),oauth_request.get_parameter('oauth_token'))
                consumer, token, params = self.oauth_server.verify_request(oauth_request)

                name = self.path.split('/', 2 )
                database = settings['U1DB']['path'] + name[1]
                os.system("/usr/local/src/serverU1DB/u1db-client init-db " + database)

                handler = urllib2.HTTPHandler()
                opener = urllib2.build_opener(handler)
                url=settings['Urls']['RESOURCE_URL'] +  self.path
                request = urllib2.Request(url, data=postdata)
                request.headers = self.headers
                request.get_method = lambda: self.command

                try:
                    connection = opener.open(request)
                except urllib2.HTTPError,e:
                    connection = e

                if connection.code == 200:
                    result = connection.read()
                    self.send_response(200, 'OK')
                    self.end_headers()
                    self.wfile.write(result)
                else:
                    self.send_response(connection.code, 'KO')
                    self.end_headers()


            except oauth.OAuthError, err:
                self.send_oauth_error(err)
            except urllib2.HTTPError, err:
                self.send_oauth_error(err)
                print('HTTPError = ' + str(err.code))
            except urllib2.URLError, err:
                print('URLError = ' + str(err.reason))
                self.send_oauth_error(err)
            except httplib.HTTPException, err:
                print('HTTPException')
                self.send_oauth_error(err)
            except Exception:
                print('generic exception: ' + traceback.format_exc())
                self.send_oauth_error(traceback.format_exc())


        return

    def do_POST(self):
        return self.do_GET()

    def do_PUT(self):
        return self.do_GET()


#run(host='192.168.3.118', port=8080)


def createPid(pid):
    try:
        file = open('/var/run/serverOauth.pid', 'w')
        file.write(str(pid))
        file.close()
    except IOError as e:
        print >>sys.stderr, "Error create file pid:%d (%s)" % (e.errno, e.strerror)
        os.kill(int(pid), 9)
        sys.exit(0)

try:

    try:
        pid = os.fork()
        if pid > 0:
            createPid(str(pid))
            sys.exit(0)
    except OSError, e:
        print >>sys.stderr, "fork #1 failed: %d (%s)" % (e.errno, e.strerror)
        sys.exit(1)
    server = HTTPServer((settings['Server']['host'], settings['Server']['port']), RequestHandler)
    print 'Test server running...'
    server.serve_forever()
except KeyboardInterrupt:
    server.socket.close()
