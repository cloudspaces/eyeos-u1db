from BaseHTTPServer import BaseHTTPRequestHandler, HTTPServer
from settings import settings
from mongodb import mongoDb
import time
from urlparse import urlparse

class RequestHandler(BaseHTTPRequestHandler):
    def __init__(self, *args, **kwargs):
        self.comments = mongoDb("localhost",27017,"comments")
        BaseHTTPRequestHandler.__init__(self, *args, **kwargs)

    def do_POST(self):
        #print self.path
        #print self.headers
        postdata = self.getPostData()

        if self.path.startswith('/comment'):
            if postdata.has_key('id') and postdata.has_key('user') and postdata.has_key('text') and postdata.has_key('cloud'):
                time_created = time.strftime("%Y%m%d%H%M%S")
                data = self.comments.insertComment(postdata['id'],postdata['user'],postdata['text'],postdata['cloud'],time_created)
                self.sendData(data)
            else:
                self.send_error(400, "Parametros incorrectos")
        else:
            self.send_error(400, "Recurso no encontrado")

    def do_DELETE(self):
        postdata = self.getPostData()
        if self.path.startswith('/comment'):
            if postdata.has_key('id') and postdata.has_key('user') and postdata.has_key('cloud') and postdata.has_key('time_created'):
                data = self.comments.deleteComment(postdata['id'],postdata['user'],postdata['cloud'],postdata['time_created'])
                self.sendData(data)
            else:
                self.send_error(400, "Parametros incorrectos")
        else:
            self.send_error(400, "Recurso no encontrado")

    def do_GET(self):
        if self.path.startswith('/comment'):
            params = self.path.split('/')
            if len(params) == 4:
                id = params[2]
                cloud = params[3]
                data = self.comments.getComments(id,cloud)
                self.send_response(200,data)
            else:
                self.send_error(400, "Parametros incorrectos")
        else:
            self.send_error(400, "Recurso no encontrado")

    def getPostData(self):
        data = {}
        try:
             length = int(self.headers.getheader('content-length'))
             postdata = self.rfile.read(length)
             data = dict((itm.split('=')[0],itm.split('=')[1]) for itm in postdata.split('&'))
        except:
            pass
        return data

    def sendData(self,response):
         if response.has_key('error'):
             self.send_response(response['error'],response['descripcion'])
         else:
            self.send_response(200,response)
         self.end_headers()




server = HTTPServer((settings['Server']['host'], settings['Server']['port']), RequestHandler)
print 'Test server running...'
server.serve_forever()
