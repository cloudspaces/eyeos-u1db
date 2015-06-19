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
         result = self.db.collection.find({"id":id,"user":user,"cloud":cloud,"time_created":time_created}).count()
         if result == 1:
             del document['_id']
             return document
         else:
             return {"error":400,"descripcion":"Error al insertar comentario"}

    def deleteComment(self,id,user,cloud,time_created):
        result = self.db.collection.find({"id":id,"user":user,"cloud":cloud,"time_created":time_created})
        if result.count() == 1:
            document = result[0]
            del document['_id']
            document['status'] = 'DELETED'
            self.db.collection.remove({"id":id,"user":user,"cloud":cloud,"time_created":time_created})
            result = self.db.collection.find({"id":id,"user":user,"cloud":cloud,"time_created":time_created})
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

    def insertEvent(self,user,calendar,cloud,isallday,timestart,timeend,repetition,finaltype,finalvalue,subject,location,description,repeattype):
        result = self.db.collection.find({"type":"event","user":user,"calendar":calendar,"cloud":cloud,"timestart":timestart,"timeend":timeend,"isallday":isallday}).count()
        if result == 0:
            document = {"type":"event","user":user,"calendar":calendar,"cloud":cloud,"isallday":isallday,"timestart":timestart,"timeend":timeend,"repetition":repetition,"finaltype":finaltype,"finalvalue":finalvalue,"subject":subject,"location":location,"description":description,"repeattype":repeattype,"status":"NEW"}
            self.db.collection.insert(document)
            result = self.db.collection.find({"type":"event","user":user,"calendar":calendar,"cloud":cloud,"timestart":timestart,"timeend":timeend,"isallday":isallday}).count()
            if result == 1:
                del document['_id']
                return document
            else:
                return {"error":400,"descripcion":"Error al insertar Evento"}
        elif result == 1:
            return self.updateEvent(user,calendar,cloud,isallday,timestart,timeend,repetition,finaltype,finalvalue,subject,location,description,repeattype)
        else:
            return {"error":400,"descripcion":"Error al insertar Evento"}


    def deleteEvent(self,user,calendar,cloud,timestart,timeend,isallday):
        result = self.db.collection.find({"type":"event","user":user,"calendar":calendar,"cloud":cloud,"timestart":timestart,"timeend":timeend,"isallday":isallday})
        if result.count() == 1:
            document = result[0]
            del document['_id']
            document['status'] = 'DELETED'
            self.db.collection.remove({"type":"event","user":user,"calendar":calendar,"cloud":cloud,"timestart":timestart,"timeend":timeend,"isallday":isallday})
            result = self.db.collection.find({"type":"event","user":user,"calendar":calendar,"cloud":cloud,"timestart":timestart,"timeend":timeend,"isallday":isallday})
            if result.count() == 0:
                return document;
            else:
               return {"error":400,"descripcion":"Error al borrar evento"}
        else:
            return {"error":400,"descripcion":"Evento no encontrado"}

    def updateEvent(self,user,calendar,cloud,isallday,timestart,timeend,repetition,finaltype,finalvalue,subject,location,description,repeattype):
        document = {"type":"event","user":user,"calendar":calendar,"cloud":cloud,"isallday":isallday,"timestart":timestart,"timeend":timeend,"repetition":repetition,"finaltype":finaltype,"finalvalue":finalvalue,"subject":subject,"location":location,"description":description,"repeattype":repeattype,"status":"CHANGED"}
        result = self.db.collection.find({"type":"event","user":user,"calendar":calendar,"cloud":cloud,"timestart":timestart,"timeend":timeend,"isallday":isallday})
        if result.count() == 1:
            self.db.collection.update({"type":"event","user":user,"calendar":calendar,"cloud":cloud,"timestart":timestart,"timeend":timeend,"isallday":isallday},document)
            result = self.db.collection.find({"type":"event","user":user,"calendar":calendar,"cloud":cloud,"timestart":timestart,"timeend":timeend,"isallday":isallday})
            if result.count() == 1:
                return document;
            else:
               return {"error":400,"descripcion":"Error al actualizar evento"}
        else:
            return {"error":400,"descripcion":"Evento no encontrado"}

    def getEvents(self,user,calendar,cloud):
        data = []
        documents = self.db.collection.find({"user":user,"calendar":calendar,"cloud":cloud})
        if documents.count() > 0:
            for document in documents:
                del document['_id']
                data.append(document)
        data.sort()
        return data

    def insertCalendar(self,user,name,cloud,description,timezone):
        result = self.db.collection.find({"type":"calendar","user":user,"name":name,"cloud":cloud}).count()
        if result == 0:
            document = {"type":"calendar","user":user,"name":name,"cloud":cloud,"description":description,"timezone":timezone,"status":"NEW"}
            self.db.collection.insert(document)
            result = self.db.collection.find({"type":"calendar","user":user,"name":name,"cloud":cloud}).count()
            if result == 1:
                del document['_id']
                return document
            else:
                return {"error":400,"descripcion":"Error al insertar Calendario"}
        elif result == 1:
            return self.updateCalendar(user,name,cloud,description,timezone)
        else:
            return {"error":400,"descripcion":"Error al insertar Calendario"}

    def deleteCalendar(self,user,name,cloud):
        result = self.db.collection.find({"type":"calendar","user":user,"name":name,"cloud":cloud})
        if result.count() == 1:
            document = result[0]
            del document['_id']
            document['status'] = 'DELETED'
            self.db.collection.remove({"type":"calendar","user":user,"name":name,"cloud":cloud})
            result = self.db.collection.find({"type":"event","user":user,"name":name,"cloud":cloud})
            if result.count() == 0:
                return document;
            else:
               return {"error":400,"descripcion":"Error al borrar calendario"}
        else:
            return {"error":400,"descripcion":"Calendario no encontrado"}

    def updateCalendar(self,user,name,cloud,description,timezone):
        document = {"type":"calendar","user":user,"name":name,"cloud":cloud,"description":description,"timezone":timezone,"status":"CHANGED"}
        result = self.db.collection.find({"type":"calendar","user":user,"name":name,"cloud":cloud})
        if result.count() == 1:
            self.db.collection.update({"type":"calendar","user":user,"name":name,"cloud":cloud},document)
            result = self.db.collection.find({"type":"calendar","user":user,"name":name,"cloud":cloud})
            if result.count() == 1:
                return document;
            else:
               return {"error":400,"descripcion":"Error al actualizar calendario"}
        else:
            return {"error":400,"descripcion":"Calendario no encontrado"}

    def getCalendars(self,user,cloud):
        data = []
        documents = self.db.collection.find({"type":"calendar","user":user,"cloud":cloud})
        if documents.count() > 0:
            for document in documents:
                del document['_id']
                data.append(document)
        data.sort()
        return data

    def getCalendarsAndEvents(self,user,cloud):
        data = []
        documents = self.db.collection.find({"user":user,"cloud":cloud})
        if documents.count() > 0:
            for document in documents:
                del document['_id']
                data.append(document)
        data.sort()
        return data

    def deleteCalendarsUser(self,user,cloud):
        self.db.collection.remove({"user":user,"cloud":cloud})
        documents = self.db.collection.find({"user":user,"cloud":cloud})
        if documents.count() == 0:
            return {"delete":True}
        else:
            return {"error":400,"descripcion":"Error al borrar calendario de Usuarios"}