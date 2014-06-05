__author__ = 'root'

import json
from Metadata_v1 import Metadata_v1
import sys

class Protocol_v1:
    def __init__(self,test = None):
        self.test = False
        if test != None:
            self.test = True

    def protocol(self,params):
        aux = json.loads(params)
        type = aux["type"]
        lista = aux["lista"]
        creds = None

        if aux.has_key('credentials'):
            creds = {'oauth':{'consumer_key':'' + str(aux['credentials']['oauth']['consumer_key']) + '','consumer_secret':'' + str(aux['credentials']['oauth']['consumer_secret']) + '','token_key':'' + str(aux['credentials']['oauth']['token_key']) + '','token_secret':'' + str(aux['credentials']['oauth']['token_secret']) + ''}}

        result = False

        self.configDb(type,creds)

        if type == "insert":
            result = self.insert(lista)
        elif type == "select":
            result = self.select(lista[0]["file_id"],lista[0]['user_eyeos'])
        elif type == "update":
            result = self.update(lista)
        elif type == "delete":
            result = self.delete(lista)
        elif type == "parent":
            result = self.getParent(lista[0]['path'],lista[0]["folder"],lista[0]['user_eyeos'])
        elif type == "deleteFolder":
            result = self.deleteFolder(lista[0]["file_id"],lista[0]['user_eyeos'])
        elif type == "deleteMetadataUser":
            result = self.deleteMetadataUser(lista[0]['user_eyeos'])
        elif type == "selectMetadataUser":
            result = self.selectMetadataUser(lista[0]['user_eyeos'])
        elif type == "deleteEvent":
            result = self.deleteEvent(lista)
        elif type == "updateEvent":
            result = self.updateEvent(lista)
        elif type == "selectEvent":
            result = self.selectEvent(lista[0]['type'],lista[0]['user_eyeos'],lista[0]['calendar'])
        elif type == "insertEvent":
            result = self.insertEvent(lista)
        elif type == "insertCalendar":
            result = self.insertCalendar(lista)
        elif type == "deleteCalendar":
            result = self.deleteCalendar(lista)
        elif type == "selectCalendar":
            result = self.selectCalendar(lista[0])
        elif type == "updateCalendar":
            result = self.updateCalendar(lista)
        elif type == "deleteCalendarUser":
            result = self.deleteCalendarUser(lista[0]['user_eyeos'])
        elif type == "selectCalendarsAndEvents":
            result = self.selectCalendarsAndEvents(lista[0]['user_eyeos'])

        return json.dumps(result)

    def insert(self,lista):
        self.metadata.insert(lista)
        return True

    def select(self,id,user):
        return self.metadata.select(id,user)

    def update(self,lista):
        self.metadata.update(lista)
        return True

    def delete(self,lista):
        self.metadata.delete(lista)
        return True

    def getParent(self,path,folder,user):
        return self.metadata.getParent(path,folder,user)

    def deleteFolder(self,idFolder,user):
        self.metadata.deleteFolder(idFolder,user)
        return True

    def deleteMetadataUser(self,user):
        self.metadata.deleteMetadataUser(user)
        return True

    def selectMetadataUser(self,user):
        return self.metadata.selectMetadataUser(user)

    def deleteEvent(self,lista):
        self.metadata.deleteEvent(lista)
        return True

    def updateEvent(self,lista):
        self.metadata.updateEvent(lista)
        return True

    def selectEvent(self,type,user,calendarid):
        return self.metadata.selectEvent(type,user,calendarid)

    def insertEvent(self,lista):
        self.metadata.insertEvent(lista)
        return True

    def insertCalendar(self,lista):
        self.metadata.insertCalendar(lista)
        return True

    def deleteCalendar(self,lista):
        self.metadata.deleteCalendar(lista)
        return True

    def selectCalendar(self,data):
        return self.metadata.selectCalendar(data)

    def updateCalendar(self,lista):
        self.metadata.updateCalendar(lista)
        return True

    def deleteCalendarUser(self,user):
        self.metadata.deleteCalendarUser(user)
        return True

    def selectCalendarsAndEvents(self,user):
        return self.metadata.selectCalendarsAndEvents(user)

    def configDb(self,type,creds = None):
        if self.test == True:
            name = "test_v1.u1db"
        else:
            name = "metadata_v1.u1db"
            if type == "deleteEvent" or type == "updateEvent" or type == "selectEvent" or type == "insertEvent" or type == "insertCalendar" or type == "deleteCalendar" or type == "selectCalendar" or type == "updateCalendar" or type == 'deleteCalendarUser' or type == 'selectCalendarsAndEvents':
                name = "calendar.u1db"

        self.metadata = Metadata_v1(name,creds)

if __name__ == "__main__":
    if len(sys.argv) == 2:
         protocol = Protocol_v1()
         print (protocol.protocol(str(sys.argv[1])))
    else:
        print ('false')
