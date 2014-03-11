__author__ = 'root'

import json
from Metadata import Metadata
import sys

class Protocol:
    def __init__(self,db=None):
        self.metadata = Metadata(db)

    def protocol(self,params):
        aux = json.loads(params)
        type = aux["type"]
        lista = aux["lista"]
        result = False

        if type == "insert":
            result = self.insert(lista)
        elif type == "select":
            result = self.select(lista[0]["file_id"])
        elif type == "update":
            result = self.update(lista)
        elif type == "delete":
            result = self.delete(lista)
        elif type == "parent":
            result = self.getParent(lista[0]['path'],lista[0]["folder"])
        elif type == "deleteFolder":
            result = self.deleteFolder(lista[0]["file_id"])

        return json.dumps(result)

    def insert(self,lista):
        self.metadata.insert(lista)
        return True

    def select(self,id):
        return self.metadata.select(id)

    def update(self,lista):
        self.metadata.update(lista)
        return True

    def delete(self,lista):
        self.metadata.delete(lista)
        return True

    def getParent(self,path,folder):
        return self.metadata.getParent(path,folder)

    def deleteFolder(self,idFolder):
        self.metadata.deleteFolder(idFolder)
        return True

if __name__ == "__main__":
    if len(sys.argv) == 2:
         protocol = Protocol()
         print (protocol.protocol(str(sys.argv[1])))
    else:
        print ('false')
