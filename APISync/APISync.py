from BaseHTTPServer import BaseHTTPRequestHandler, HTTPServer
from settings import settings
from mongodb import mongoDb
import time
from urlparse import urlparse
import json

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
                response = {"error":400,"descripcion":"Parametros incorrectos"}
                self.sendData(response)
        else:
            response = {"error":400,"descripcion":"Recurso no encontrado"}
            self.sendData(response)


    def do_DELETE(self):
        if self.path.startswith('/comment'):
            params = self.path.split('/')
            if len(params) == 6:
                id = params[2]
                user = params[3]
                cloud = params[4]
                time_created = params[5]
                data = self.comments.deleteComment(id,user,cloud,time_created)
                self.sendData(data)

            else:
                response = {"error":400,"descripcion":"Parametros incorrectos"}
                self.sendData(response)
        else:
            response = {"error":400,"descripcion":"Recurso no encontrado"}
            self.sendData(response)

    def do_GET(self):
        if self.path.startswith('/comment'):
            params = self.path.split('/')
            if len(params) == 4:
                id = params[2]
                cloud = params[3]
                data = self.comments.getComments(id,cloud)
                self.send_response(200,"OK")
                self.end_headers()
                self.wfile.write(json.dumps(data))
            else:
                response = {"error":400,"descripcion":"Parametros incorrectos"}
                self.sendData(response)
        else:
            response = {"error":400,"descripcion":"Recurso no encontrado"}
            self.sendData(response)

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
            self.send_response(200,"OK")
         self.end_headers()
         self.wfile.write(json.dumps(response))



server = HTTPServer((settings['Server']['host'], settings['Server']['port']), RequestHandler)
print 'Test server running...'
server.serve_forever()
