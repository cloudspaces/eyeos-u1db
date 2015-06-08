__author__ = 'root'

from pymongo import MongoClient

class mongoDb:
    def __init__(self, host,port,name):
        self.client = MongoClient(host,port)
        self.db = self.client[name]

    def __del__(self):
        self.client.close()

    def insertComment(self,id,user,text,cloud,time_created):
         document = {"id": id,"user": user,"text":text,"cloud": cloud,"time_created":time_created,"status":"NEW"}
         self.db.collection.insert(document)
         result = self.db.collection.find({"id":id,"user":user,"cloud":cloud}).count()
         if result == 1:
             del document['_id']
             return document
         else:
             return {"error":400,"descripcion":"Error al insertar comentario"}

    def deleteComment(self,id,user,cloud,time_created):
        result = self.db.collection.find({"id":id,"user":user,"cloud":cloud})
        if result.count() == 1:
            document = result[0]
            del document['_id']
            document['status'] = 'DELETED'
            self.db.collection.remove({"id":id,"user":user,"cloud":cloud,"time_created":time_created})
            result = self.db.collection.find({"id":id,"user":user,"cloud":cloud})
            if result.count() == 0:
                return document;
            else:
               return {"error":400,"descripcion":"Error al borrar comentario"}
        else:
            return {"error":400,"descripcion":"Comentario no encontrado"}

    def getComments(self,id,cloud):
        data = []
        documents = self.db.collection.find({"id":id,"cloud":cloud})
        if documents.count() > 0:
            for document in documents:
                del document['_id']
                data.append(document)
        data.sort()
        return data
