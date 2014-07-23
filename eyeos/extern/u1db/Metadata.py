__author__ = 'root'

import json
import u1db
import os
from settings import settings

class Metadata:
    db1 = None
    db2 = None
    def __init__(self, name, creds=None,name2 = None):
        if name == "test.u1db":
            db = name
        else:
            db =  os.getcwd() + "/extern/u1db/" + name
        self.db = u1db.open(db, create=True)

        db2 = None
        if name2 == "test1.u1db":
            db2 = name2
        elif name2 != None:
            db2 =  os.getcwd() + "/extern/u1db/" + name2

        if db2 != None:
            self.db2 = u1db.open(db2, create=True)

        #self.url = "http://192.168.3.118:8080/" + name
        self.url = settings['Oauth']['sync'] + settings['Oauth']['server'] + ":" + str(settings['Oauth']['port']) + "/" + name
        self.creds = creds

    def __del__(self):
        self.db.close()
        if self.db2 != None:
            self.db2.close()

    def insert(self,lista):
        for data in lista:
            self.db.create_doc_from_json(json.dumps(data))

    def select(self,id,user,path):
        results = []
        if id != "null":
            self.db.create_index("by-id-path", "id","user_eyeos","path")
            files = self.db.get_from_index("by-id-path",str(id),user,path)
            for file in files:
                results.append(file.content)

        self.db.create_index("by-parent-path", "parent_id","user_eyeos","path")
        files = self.db.get_from_index("by-parent-path",str(id),user,path + "*")
        for file in files:
            results.append(file.content)
        return results

    def update(self,lista):
        self.db.create_index("by-id-parent", "id","user_eyeos","parent_id")
        parent = ''
        for data in lista:
            if data.has_key("parent_old"):
                parent = str(data['parent_old'])
            else:
                id = str(data["id"])
                user = data['user_eyeos']
                files = self.db.get_from_index("by-id-parent",id,user,parent)
                if len(files) > 0:
                    file = files[0];
                    file.set_json(json.dumps(data))
                    self.db.put_doc(file)

    def delete(self,lista):
        self.db.create_index("by-id-parent", "id","user_eyeos","parent_id")
        for data in lista:
            id = str(data["id"])
            user = data['user_eyeos']
            parent = str(data['parent_id'])
            files = self.db.get_from_index("by-id-parent",id,user,parent)
            if len(files) > 0:
                self.db.delete_doc(files[0])

    def getParent(self,path,filename,user):
        results = []
        self.db.create_index("by-path-filename", "path","filename","user_eyeos")
        files = self.db.get_from_index("by-path-filename",path,filename,user)
        if len(files) > 0:
            results.append(files[0].content)
        return results

    def deleteFolder(self,idFolder,user,path):
        self.db.create_index("by-parent-path", "parent_id","user_eyeos","path")
        files = self.db.get_from_index("by-parent-path",str(idFolder),user,path + "*")

        if len(files) > 0:
            for file in files:
                if file.content["is_folder"] == True:
                    self.deleteFolder(file.content['id'],user,file.content['path'])
                else:
                    self.db.delete_doc(file)

        self.db.create_index("by-id-path", "id","user_eyeos","path")
        files = self.db.get_from_index("by-id-path",str(idFolder),user,path)
        if len(files) > 0:
            self.db.delete_doc(files[0])

    def deleteMetadataUser(self,user):
        self.db.create_index("by-usereyeos", "user_eyeos")
        files = self.db.get_from_index("by-usereyeos",user)
        if len(files) > 0:
            for file in files:
                id = None
                if file.content.has_key('id'):
                    id = str(file.content['id'])
                self.db.delete_doc(file)

                if id != None:
                    self.db2.create_index("by-id-user","id","user_eyeos")
                    versions = self.db2.get_from_index("by-id-user",id,user)
                    if len(versions) > 0:
                        self.db2.delete_doc(versions[0])

    def selectMetadataUser(self,user):
        result = []
        self.db.create_index("by-usereyeos", "user_eyeos")
        files = self.db.get_from_index("by-usereyeos",user)
        if len(files) > 0:
            for file in files:
                result.append(file.content)
        return result

    def renameMetadata(self,metadata):
        self.db.create_index("by-id-path", "id","user_eyeos","path")
        files = self.db.get_from_index("by-id-path",str(metadata['id']),metadata['user_eyeos'],metadata['path'])
        if len(files) > 0:
            filenameOld = files[0].content['filename']
            files[0].set_json(json.dumps(metadata))
            self.db.put_doc(files[0])
            if files[0].content['is_folder'] == True:
                pathOld = metadata['path'] + filenameOld + '/'
                pathNew = metadata['path'] + metadata['filename'] + '/'
                self.renamePath(metadata['id'],metadata['user_eyeos'],pathOld,pathNew)

    def renamePath(self,id,user,pathOld,pathNew):
        self.db.create_index("by-parent-path", "parent_id","user_eyeos","path")
        files = self.db.get_from_index("by-parent-path",str(id),user,pathOld)
        if len(files) > 0:
            for file in files:
                file.content['path'] = pathNew
                self.db.put_doc(file)
                if file.content['is_folder'] == True:
                    _pathOld = pathOld + file.content['filename'] + '/'
                    _pathNew = pathNew + file.content['filename'] + '/'
                    self.renamePath(file.content['id'],user,_pathOld,_pathNew)

    def insertDownloadVersion(self,metadata):
        self.db.create_doc_from_json(json.dumps(metadata))

    def updateDownloadVersion(self,metadata):
        self.db.create_index("by-id-user","id","user_eyeos")
        files = self.db.get_from_index("by-id-user",metadata['id'],metadata['user_eyeos'])
        if len(files) > 0:
            files[0].set_json(json.dumps(metadata))
            self.db.put_doc(files[0])

    def deleteDownloadVersion(self,id,user):
        self.db.create_index("by-id-user","id","user_eyeos")
        files = self.db.get_from_index("by-id-user",id,user)
        if len(files) > 0:
            self.db.delete_doc(files[0])

    def getDownloadVersion(self,id,user):
        result = None
        self.db.create_index("by-id-user","id","user_eyeos")
        files = self.db.get_from_index("by-id-user",id,user)
        if len(files) > 0:
            result = files[0].content
        return result

    def recursiveDeleteVersion(self,id,user):
        self.db.create_index("by-parent", "parent_id")
        files = self.db.get_from_index("by-parent",str(id))
        for file in files:
            if file.content['is_folder'] == True:
                self.recursiveDeleteVersion(file.content['id'],user)
            self.db2.create_index("by-id-user","id","user_eyeos")
            files = self.db2.get_from_index("by-id-user",str(file.content['id']),user)
            for file in files:
                self.db2.delete_doc(file)



    """
    ##################################################################################################################################################
                                                                    CALENDAR
    ##################################################################################################################################################
    """

    def deleteEvent(self,lista):
        self.updateEvent(lista)

    def updateEvent(self,lista):
        for data in lista:
            files = self.getEvents(data)
            if len(files) > 0:
                file = files[0]
                file.set_json(json.dumps(data))
                self.db.put_doc(file)
        self.sync()

    def selectEvent(self,type,user,idCalendar):
        self.sync()
        results = []
        self.db.create_index("by-event", "type","user_eyeos","calendar")
        files = self.db.get_from_index("by-event",type,user,idCalendar)
        for file in files:
            results.append(file.content)
        return results

    def getEvents(self,data):
        self.db.create_index("by-event2", "type","user_eyeos","calendar","timestart","timeend","isallday")
        timestart = str(data['timestart'])
        timeend = str(data['timeend'])
        isallday = str(data['isallday'])
        files = self.db.get_from_index("by-event2",data['type'],data['user_eyeos'],data['calendar'],timestart,timeend, isallday)
        return files

    def insertEvent(self,lista):
        #self.insert(lista)
        for data in lista:
            files = self.getEvents(data)
            if len(files) > 0:
                file = files[0]
                file.set_json(json.dumps(data))
                self.db.put_doc(file)
            else:
                self.db.create_doc_from_json(json.dumps(data))
        self.sync()

    def insertCalendar(self,lista):
        for data in lista:
            self.sync()
            calendar = self.getCalendar(data)
            if len(calendar) == 0:
                self.db.create_doc_from_json(json.dumps(data))
            elif calendar[0].content['status'] == 'DELETED':
                file = calendar[0]
                file.set_json(json.dumps(data))
                self.db.put_doc(file)



    def getCalendar(self,data):
        self.db.create_index("by-calendar", "type","user_eyeos","name")
        calendar = self.db.get_from_index("by-calendar",data['type'],data['user_eyeos'],data['name'])
        return calendar

    def deleteCalendar(self,lista):
        for data in lista:
            calendar = self.getCalendar(data)
            if len(calendar) > 0:
                file = calendar[0]
                file.content['status'] = 'DELETED'
                self.db.put_doc(file)
                self.db.create_index("by-event", "type","user_eyeos","calendar")
                events = self.db.get_from_index("by-event","event",data['user_eyeos'],data['name'])
                if len(events) > 0:
                    for event in events:
                        event.content['status'] = 'DELETED'
                        self.db.put_doc(event)
        self.sync()

    def selectCalendar(self,data):
        self.sync()
        self.db.create_index("by-calendar2", "type","user_eyeos")
        calendar = self.db.get_from_index("by-calendar2",data['type'],data['user_eyeos'])
        results = []
        if len(calendar) > 0:
            for cal in calendar:
                results.append(cal.content)
        return results

    def updateCalendar(self,lista):
        for data in lista:
            calendar = self.getCalendar(data)
            if len(calendar) > 0:
                file = calendar[0]
                file.set_json(json.dumps(data))
                self.db.put_doc(file)

    def sync(self):
        try:
            print(self.db.sync(self.url,creds=self.creds))
        except:
            pass

    def deleteCalendarUser(self,user):
        self.deleteMetadataUser(user)

    def selectCalendarsAndEvents(self,user):
        result = []
        self.db.create_index("by-userStatus", "user_eyeos","status")
        files = self.db.get_from_index("by-userStatus",user,"NEW")
        if len(files) > 0:
            for file in files:
                result.append(file.content)
        return result