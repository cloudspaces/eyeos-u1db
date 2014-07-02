__author__ = 'root'

import json
import u1db
import os
from settings import settings
import time
import sys

class Comments:
    def __init__(self, name, creds=None):
        if name == "test.u1db":
            db = name
        else:
            db =  os.getcwd() + "/extern/u1db/" + name
        self.db = u1db.open(db, create=True)
        self.url = settings['Oauth']['sync'] + settings['Oauth']['server'] + ":" + str(settings['Oauth']['port']) + "/" + name
        self.creds = creds

    def __del__(self):
        self.db.close()

    def createComment(self,id,user,text,time_created = None):
        try:
            data = {}
            data['id'] = id
            data['user'] = user
            data['text'] = text
            data['status'] =  "NEW"
            data['time_created'] = time_created
            if time_created == None:
                data['time_created'] = time.strftime("%Y%m%d%H%M%S")
            self.db.create_doc_from_json(json.dumps(data))
            self.sync()
        except:
            data = {"error":-1,"description":"Error create comment"}
        return json.dumps(data)

    def deleteComment(self,id,user,time_created):
        try:
            data = {"status":"OK"}
            self.db.create_index("by-id-user-time", "id","user","time_created")
            comments = self.db.get_from_index("by-id-user-time",id,user,time_created)
            if len(comments) > 0:
                self.db.delete_doc(comments[0])
            self.sync()
        except:
            data = {"status":"KO","error":-1}
        return json.dumps(data)

    def getComments(self,id):
        results = []
        try:
            self.sync()
            self.db.create_index("by-id", "id")
            comments = self.db.get_from_index("by-id",id)
            for data in comments:
                results.append(data.content)
        except:
            results.append({"error":-1,"description":"Error getComments"})
        return json.dumps(results)

    def sync(self):
        try:
            self.db.sync(self.url,creds=self.creds)
        except:
            pass


if __name__ == "__main__":
    if len(sys.argv) == 2:
        params = json.loads(str(sys.argv[1]))
        if params.has_key('type') and params.has_key('metadata') and params.has_key('credentials'):
            type = params['type']
            metadata = params['metadata']
            creds = {'oauth':{'consumer_key':'' + str(params['credentials']['oauth']['consumer_key']) + '','consumer_secret':'' + str(params['credentials']['oauth']['consumer_secret']) + '','token_key':'' + str(params['credentials']['oauth']['token_key']) + '','token_secret':'' + str(params['credentials']['oauth']['token_secret']) + ''}}

            comments = Comments("comment.u1db",creds)
            result = '{"error":-1,"description":"Type is not correct"}'
            try:
                if type == "create":
                    result = comments.createComment(metadata['id'],metadata['user'],metadata['text'])
                elif type == "delete":
                    result = comments.deleteComment(metadata['id'],metadata['user'],metadata['time_created'])
                elif type == "get":
                    result = comments.getComments(metadata['id'])
            except:
                pass
            print(result)
        else:
            print('{"error":-1,"description":"Bad parameters"}')

    else:
        print('{"error":-1,"description":"Mandatory receive params"}')

