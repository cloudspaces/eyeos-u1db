__author__ = 'root'

from pymongo import MongoClient
import datetime

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
            result = self.db.collection.find({"type":"calendar","user":user,"name":name,"cloud":cloud})
            if result.count() == 0:
                self.db.collection.remove({"type":"event","user":user,"calendar":name})
                result = self.db.collection.find({"type":"event","user":user,"calendar":name})
                if result.count() == 0:
                    return document;
                else:
                    return {"error":400,"descripcion":"Error al borrar calendario"}
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

    def lockFile(self,id,cloud,user,ipserver,dateTime,timelimit,interop=None):
        files = self.db.collection.find({"id":id,"cloud":cloud})
        data = {"id":id,"cloud":cloud,"user":user,"ipserver":ipserver,"datetime":dateTime,"status":"open"}
        emptyData = False

        if files.count() == 0:
            if interop != None:
                files = self.db.collection.find({"id":id})
                if files.count() == 0:
                    emptyData = True
            else:
                emptyData = True

        if emptyData == True:
            self.db.collection.insert(data)
            del data['_id']
        else:
            if files[0]['status'] == 'close':
                search = {"id":id,"cloud":cloud}
                if files[0]['cloud'] != cloud and interop != None:
                    search = {"id":id}
                self.db.collection.update(search,data)
            else:
                if files[0]['user'] == user and files[0]['ipserver'] == ipserver:
                    if interop != None:
                        self.db.collection.update({"id":id},data)
                    else:
                        self.db.collection.update({"id":id,"cloud":cloud},data)
                else:
                    dt=datetime.datetime.strptime(files[0]['datetime'],'%Y-%m-%d %H:%M:%S')
                    dt_plus_timeLimit = dt + datetime.timedelta(minutes = timelimit)
                    dt_now = datetime.datetime.strptime(dateTime,'%Y-%m-%d %H:%M:%S')
                    if dt_now > dt_plus_timeLimit:
                         search = {"id":id,"cloud":cloud}
                         if interop != None:
                             search = {"id":id}
                         self.db.collection.update(search,data)
                    else:
                        return {"error":400,"descripcion":"Error al bloquear fichero"}

        files = self.db.collection.find({"id":id,"cloud":cloud})

        if files.count() == 1:
            document = files[0]
            del document['_id']
            if document == data:
                return {"lockFile":True}
            else:
                return {"error":400,"descripcion":"Error al bloquear fichero"}
        else:
            return {"error":400,"descripcion":"Error al bloquear fichero"}

    def updateDateTime(self,id,cloud,user,ipserver,dateTime):
        files = self.db.collection.find({"id":id,"cloud":cloud,"user":user,"ipserver":ipserver})
        if files.count() == 1:
            data = {"id":id,"cloud":cloud,"user":user,"ipserver":ipserver,"datetime":dateTime,"status":"open"}
            self.db.collection.update({"id":id,"cloud":cloud,"user":user,"ipserver":ipserver},data)
            files = self.db.collection.find({"id":id,"cloud":cloud,"user":user,"ipserver":ipserver})
            if files.count() == 1:
                document = files[0]
                del document['_id']
                if document == data:
                    return {"updateFile":True}
                else:
                    return {"error":400,"descripcion":"Error al actualizar fecha"}
            else:
                return {"error":400,"descripcion":"Error al actualizar fecha"}

        else:
            return {"error":400,"descripcion":"Error al actualizar fecha"}

    def unLockFile(self,id,cloud,user,ipserver,dateTime):
        files = self.db.collection.find({"id":id,"cloud":cloud,"user":user,"ipserver":ipserver})
        if files.count() == 1:
            data = {"id":id,"cloud":cloud,"user":user,"ipserver":ipserver,"datetime":dateTime,"status":"close"}
            self.db.collection.update({"id":id,"cloud":cloud,"user":user,"ipserver":ipserver},data)
            if files.count() == 1:
                document = files[0]
                del document['_id']
                if document == data:
                    return {"unLockFile":True}
                else:
                    return {"error":400,"descripcion":"Error al liberar fichero"}
            else:
                return {"error":400,"descripcion":"Error al liberar fichero"}
        else:
            return {"error":400,"descripcion":"Error al liberar fichero"}

    def getMetadataFile(self,id,cloud,interop=None):
        result = []
        files = self.db.collection.find({"id":id,"cloud":cloud})

        if files.count() == 0 and interop != None:
            files = self.db.collection.find({"id":id})

        if files.count() > 0:
            for file in files:
                del file['_id']
                result.append(file)
        return result