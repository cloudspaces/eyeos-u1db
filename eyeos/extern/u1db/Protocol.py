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
            result = self.select(lista[0]["id"])
        elif type == "update":
            result = self.update(lista)
        elif type == "delete":
            result = self.delete(lista)

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

if __name__ == "__main__":
    if len(sys.argv) == 2:
         protocol = Protocol()
         print (protocol.protocol(str(sys.argv[1])))
    else:
        print ('false')
