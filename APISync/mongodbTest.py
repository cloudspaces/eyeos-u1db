__author__ = 'root'

import unittest
from mongodb import mongoDb

class mongodbTest(unittest.TestCase):
    def setUp(self):
        self.sut = mongoDb("localhost",27017,"test")
        self.idFile = "2150"
        self.user = "eyeos"
        self.text = "prueba"
        self.cloud = "stacksync"
        self.time_created = "201406201548"
        self.calendar = "personal"
        self.isallday = 0
        self.timestart = "201419160000"
        self.timeend = "201419170000"
        self.repetition = "None"
        self.finaltype = "1"
        self.finalvalue = "0"
        self.subject = "Visita Medico"
        self.location = "Barcelona"
        self.description= "Llevar justificante"
        self.timezone = 0
        self.repeattype = "n"
        self.ipserver = "192.168.56.101"
        self.timelimit = 10

    def tearDown(self):
        self.sut.client.drop_database('test')

    """
    method: insertComment
    when: called
    with: idAndUserAndTextAndCloudAndTimeCreated
    should: returnComment
    """
    def test_insertComment_called_idAndUserAndTextAndCloudAndTimeCreated_returnComment(self):
        document = {"id": self.idFile,"user": self.user,"text":self.text,"cloud": self.cloud,"time_created":self.time_created,"status":"NEW"}
        result = self.sut.insertComment(self.idFile,self.user,self.text,self.cloud,self.time_created)
        self.assertEquals(document,result)


    """
    method: deleteComment
    when: called
    with: idAndUserAndCloudAndTimeCreated
    should: returnComment
    """
    def test_deleteComment_called_idAndUserAndCloudAndTimeCreated_returnComment(self):
        document = {"id": self.idFile,"user": self.user,"text":self.text,"cloud": self.cloud,"time_created":self.time_created,"status":"NEW"}
        self.sut.insertComment(self.idFile,self.user,self.text,self.cloud,self.time_created)
        result = self.sut.deleteComment(self.idFile,self.user,self.cloud,self.time_created)
        document['status'] = 'DELETED'
        self.assertEqual(document,result)


    """
    method: getComments
    when: called
    with: idAndCloud
    should: returnComments
    """
    def test_getComments_called_idAndCloud_returnComments(self):
        data = []
        data.append({"id": self.idFile,"user": self.user,"text":self.text,"cloud": self.cloud,"time_created":self.time_created,"status":"NEW"})
        self.sut.insertComment(self.idFile,self.user,self.text,self.cloud,self.time_created)
        data.append({"id": self.idFile,"user": "test","text":"test1","cloud": self.cloud,"time_created":"201406211600","status":"NEW"})
        self.sut.insertComment(self.idFile,"test","test1",self.cloud,"201406211600")
        self.sut.insertComment("2000",self.user,self.text,self.cloud,"201406211705")
        self.sut.insertComment(self.idFile,self.user,self.text,"NEC","201406211810")
        result = self.sut.getComments(self.idFile,self.cloud)
        data.sort()
        self.assertEquals(data,result)

    """
    method: insertEvent
    when: called
    with:userAndCalendarAndCloudAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValue
         AndSubjectAndLocationAndDescriptionAndRepeattype
    should: returnEvent
    """
    def test_insertEvent_called_userAndCalendarAndCloudAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValueAndSubjectAndLocationAndDescriptionAndRepeattype_returnEvent(self):
        document = {"type":"event","user":self.user,"calendar":self.calendar,"cloud":self.cloud,"isallday":self.isallday,"timestart":self.timestart,"timeend":self.timeend,"repetition":self.repetition,"finaltype":self.finaltype,"finalvalue":self.finalvalue,"subject":self.subject,"location":self.location,"description":self.description,"repeattype":self.repeattype,"status":"NEW"}
        result = self.sut.insertEvent(self.user,self.calendar,self.cloud,self.isallday,self.timestart,self.timeend,self.repetition,self.finaltype,self.finalvalue,self.subject,self.location,self.description,self.repeattype)
        self.assertEquals(document,result)


    """
    method: insertEvent
    when: called
    with:userAndCalendarAndCloudAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValue
         AndSubjectAndLocationAndDescriptionAndRepeattype
    should: returnUpdateEvent
    """
    def test_insertEvent_called_userAndCalendarAndCloudAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValueAndSubjectAndLocationAndDescriptionAndRepeattype_returnUpdateEvent(self):
        document = {"type":"event","user":self.user,"calendar":self.calendar,"cloud":self.cloud,"isallday":self.isallday,"timestart":self.timestart,"timeend":self.timeend,"repetition":self.repetition,"finaltype":self.finaltype,"finalvalue":self.finalvalue,"subject":self.subject,"location":self.location,"description":self.description,"repeattype":self.repeattype,"status":"NEW"}
        self.sut.insertEvent(self.user,self.calendar,self.cloud,self.isallday,self.timestart,self.timeend,self.repetition,self.finaltype,self.finalvalue,self.subject,self.location,self.description,self.repeattype)
        result = self.sut.insertEvent(self.user,self.calendar,self.cloud,self.isallday,self.timestart,self.timeend,self.repetition,self.finaltype,self.finalvalue,self.subject,self.location,self.description,self.repeattype)
        document['status'] = 'CHANGED'
        self.assertEquals(document,result)

    """
    method: deleteEvent
    when: called
    with:userAndCalendarAndCloudAndTimeStartAndTimeEndAndIsAllDay
    should: returnEvent
    """
    def test_deleteEvent_called_userAndCalendarAndCloudAndTimeStartAndTimeEndAndIsAllDay_returnEvent(self):
        document = {"type":"event","user":self.user,"calendar":self.calendar,"cloud":self.cloud,"isallday":self.isallday,"timestart":self.timestart,"timeend":self.timeend,"repetition":self.repetition,"finaltype":self.finaltype,"finalvalue":self.finalvalue,"subject":self.subject,"location":self.location,"description":self.description,"repeattype":self.repeattype,"status":"NEW"}
        self.sut.insertEvent(self.user,self.calendar,self.cloud,self.isallday,self.timestart,self.timeend,self.repetition,self.finaltype,self.finalvalue,self.subject,self.location,self.description,self.repeattype)
        result = self.sut.deleteEvent(self.user,self.calendar,self.cloud,self.timestart,self.timeend,self.isallday)
        document['status'] = 'DELETED'
        self.assertEqual(document,result)

    """
    method: updateEvent
    when: called
    with:userAndCalendarAndCloudAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValue
         AndSubjectAndLocationAndDescriptionAndRepeattype
    should: returnEvent
    """
    def test_updateEvent_called_userAndCalendarAndCloudAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValueAndSubjectAndLocationAndDescriptionAndRepeattype_returnEvent(self):
        document = {"type":"event","user":self.user,"calendar":self.calendar,"cloud":self.cloud,"isallday":self.isallday,"timestart":self.timestart,"timeend":self.timeend,"repetition":self.repetition,"finaltype":self.finaltype,"finalvalue":self.finalvalue,"subject":self.subject,"location":self.location,"description":self.description,"repeattype":self.repeattype,"status":"NEW"}
        self.sut.insertEvent(self.user,self.calendar,self.cloud,self.isallday,self.timestart,self.timeend,self.repetition,self.finaltype,self.finalvalue,self.subject,self.location,self.description,self.repeattype)
        document['status'] = 'CHANGED'
        result = self.sut.updateEvent(self.user,self.calendar,self.cloud,self.isallday,self.timestart,self.timeend,self.repetition,self.finaltype,self.finalvalue,self.subject,self.location,self.description,self.repeattype)
        self.assertEquals(document,result)

    """
    method: getEvents
    when: called
    with:userAndCalendarAndCloud
    should: returnEvents
    """
    def test_getEvents_called_userAndCalendarAndCloud_returnEvents(self):
        data = []
        document = {"type":"event","user":self.user,"calendar":self.calendar,"cloud":self.cloud,"isallday":self.isallday,"timestart":self.timestart,"timeend":self.timeend,"repetition":self.repetition,"finaltype":self.finaltype,"finalvalue":self.finalvalue,"subject":self.subject,"location":self.location,"description":self.description,"repeattype":self.repeattype,"status":"NEW"}
        data.append(document)
        self.sut.insertEvent(self.user,self.calendar,self.cloud,self.isallday,self.timestart,self.timeend,self.repetition,self.finaltype,self.finalvalue,self.subject,self.location,self.description,self.repeattype)
        document = {"type":"event","user":self.user,"calendar":self.calendar,"cloud":self.cloud,"isallday":self.isallday,"timestart":"201506161200","timeend":"201506161300","repetition":self.repetition,"finaltype":self.finaltype,"finalvalue":self.finalvalue,"subject":"Clase matematicas","location":"Madrid","description":"Estudio","repeattype":self.repeattype,"status":"NEW"}
        data.append(document)
        self.sut.insertEvent(self.user,self.calendar,self.cloud,self.isallday,"201506161200","201506161300",self.repetition,self.finaltype,self.finalvalue,"Clase matematicas","Madrid","Estudio",self.repeattype)
        self.sut.insertEvent(self.user,"laboral",self.cloud,self.isallday,"201506140900","201506141300",self.repetition,self.finaltype,self.finalvalue,"Vacaciones","Zaragoza","Visita turistica",self.repeattype)
        result = self.sut.getEvents(self.user,self.calendar,self.cloud)
        data.sort()
        self.assertEquals(data,result)


    """
    method: insertCalendar
    when: called
    with:userAndNameAndCloudAndDescriptionAndTimeZone
    should: returnCalendar
    """
    def test_insertCalendar_called_userAndNameAndCloudAndDescriptionAndTimeZone_returnCalendar(self):
        document = {"type":"calendar","user":self.user,"name":self.calendar,"cloud":self.cloud,"description":self.description,"timezone":self.timezone,"status":"NEW"}
        result = self.sut.insertCalendar(self.user,self.calendar,self.cloud,self.description,self.timezone)
        self.assertEquals(document,result)

    """
    method: insertCalendar
    when: called
    with:userAndNameAndCloudAndDescriptionAndTimeZone
    should: returnUpdateCalendar
    """
    def test_insertCalendar_called_userAndNameAndCloudAndDescriptionAndTimeZone_returnCalendar(self):
        document = {"type":"calendar","user":self.user,"name":self.calendar,"cloud":self.cloud,"description":self.description,"timezone":self.timezone,"status":"NEW"}
        self.sut.insertCalendar(self.user,self.calendar,self.cloud,self.description,self.timezone)
        result = self.sut.insertCalendar(self.user,self.calendar,self.cloud,self.description,self.timezone)
        document['status'] = 'CHANGED'
        self.assertEquals(document,result)

    """
    method: deleteCalendar
    when: called
    with:userAndNameAndCloud
    should: returnCalendar
    """
    def test_deleteCalendar_called_userAndNameAndCloud_returnCalendar(self):
        document = {"type":"calendar","user":self.user,"name":self.calendar,"cloud":self.cloud,"description":self.description,"timezone":self.timezone,"status":"NEW"}
        self.sut.insertCalendar(self.user,self.calendar,self.cloud,self.description,self.timezone)
        self.sut.insertEvent(self.user,self.calendar,self.cloud,self.isallday,self.timestart,self.timeend,self.repetition,self.finaltype,self.finalvalue,self.subject,self.location,self.description,self.repeattype)
        document['status'] = 'DELETED'
        result = self.sut.deleteCalendar(self.user,self.calendar,self.cloud)
        self.assertEquals(document,result)

    """
    method: updateCalendar
    when: called
    with:userAndNameAndCloudAndDescriptionAndTimeZone
    should: returnCalendar
    """
    def test_updateCalendar_called_userAndNameAndCloudAndDescriptionAndTimeZone(self):
        document = {"type":"calendar","user":self.user,"name":self.calendar,"cloud":self.cloud,"description":self.description,"timezone":self.timezone,"status":"NEW"}
        self.sut.insertCalendar(self.user,self.calendar,self.cloud,self.description,self.timezone)
        document['status'] = 'CHANGED'
        result = self.sut.updateCalendar(self.user,self.calendar,self.cloud,self.description,self.timezone)
        self.assertEquals(document,result)

    """
    method: getCalendars
    when: called
    with:userAndCloud
    should: returnCalendars
    """
    def test_getCalendars_called_userAndCloud_returnCalendars(self):
        data = []
        document = {"type":"calendar","user":self.user,"name":self.calendar,"cloud":self.cloud,"description":self.description,"timezone":self.timezone,"status":"NEW"}
        data.append(document)
        self.sut.insertCalendar(self.user,self.calendar,self.cloud,self.description,self.timezone)
        document = {"type":"calendar","user":self.user,"name":"laboral","cloud":self.cloud,"description":self.description,"timezone":self.timezone,"status":"NEW"}
        data.append(document)
        self.sut.insertCalendar(self.user,"laboral",self.cloud,self.description,self.timezone)
        self.sut.insertCalendar("tester1",self.calendar,self.cloud,self.description,self.timezone)
        result = self.sut.getCalendars(self.user,self.cloud)
        data.sort()
        self.assertEquals(data,result)

    """
    method: getCalendarsAndEvents
    when: called
    with:userAndCloud
    should: returnCalendarsAndEvents
    """
    def test_getCalendarsAndEvents_called_userAndCloud_returnCalendars(self):
        data = []
        document = {"type":"calendar","user":self.user,"name":self.calendar,"cloud":self.cloud,"description":self.description,"timezone":self.timezone,"status":"NEW"}
        data.append(document)
        self.sut.insertCalendar(self.user,self.calendar,self.cloud,self.description,self.timezone)
        document = {"type":"event","user":self.user,"calendar":self.calendar,"cloud":self.cloud,"isallday":self.isallday,"timestart":self.timestart,"timeend":self.timeend,"repetition":self.repetition,"finaltype":self.finaltype,"finalvalue":self.finalvalue,"subject":self.subject,"location":self.location,"description":self.description,"repeattype":self.repeattype,"status":"NEW"}
        data.append(document)
        self.sut.insertEvent(self.user,self.calendar,self.cloud,self.isallday,self.timestart,self.timeend,self.repetition,self.finaltype,self.finalvalue,self.subject,self.location,self.description,self.repeattype)
        result = self.sut.getCalendarsAndEvents(self.user,self.cloud)
        data.sort()
        self.assertEquals(data,result)

    """
    method: deleteCalendarsUser
    when: called
    with:userAndCloud
    should: returnDeleteCorrect
    """
    def test_deleteCalendarsUser_called_userAndCloud_returnDeleteCorrect(self):
        self.sut.insertCalendar(self.user,self.calendar,self.cloud,self.description,self.timezone)
        self.sut.insertEvent(self.user,self.calendar,self.cloud,self.isallday,self.timestart,self.timeend,self.repetition,self.finaltype,self.finalvalue,self.subject,self.location,self.description,self.repeattype)
        result = self.sut.deleteCalendarsUser(self.user,self.cloud)
        self.assertEquals({"delete":True},result)


    """
    method: lockFile
    when: called
    with: idAndCloudAndUserAndIpServerAndDateTimeAndTimeLimit
    should: emptyData
    """
    def test_lockFile_called_idAndCloudAndUserAndIpServerAndDateTimeAndTimeLimit_emptyData(self):
        datetime = "2015-05-12 10:50:00"
        result = self.sut.lockFile(self.idFile,self.cloud,self.user,self.ipserver,datetime,self.timelimit)
        self.assertEquals({"lockFile":True},result)

    """
    method: lockFile
    when: called
    with: idAndCloudAndUserAndIpServerAndDateTimeAndTimeLimit
    should: updateData
    """
    def test_lockFile_called_idAndCloudAndUserAndIpServerAndDateTimeAndTimeLimit_updateData(self):
        datetime = "2015-05-12 10:51:00"
        data = {"id":self.idFile,"cloud":self.cloud,"user":self.user,"ipserver":self.ipserver,"datetime":datetime,"status":"close"}
        self.sut.db.collection.insert(data)
        result = self.sut.lockFile(self.idFile,self.cloud,self.user,self.ipserver,datetime,self.timelimit)
        self.assertEquals({"lockFile":True},result)


    """
    method: lockFile
    when: called
    with: idAndCloudAndUserAndIpServerAndDateTimeAndTimeLimit
    should: updateDataSameUser
    """
    def test_lockFile_called_idAndCloudAndUserAndIpServerAndDateTimeAndTimeLimit_updateDataSameUserAndServer(self):
        data = {"id":self.idFile,"cloud":self.cloud,"user":self.user,"ipserver":self.ipserver,"datetime":"2015-05-12 10:50:00","status":"open"}
        self.sut.db.collection.insert(data)
        result = self.sut.lockFile(self.idFile,self.cloud,self.user,self.ipserver,"2015-05-12 10:55:00",self.timelimit)
        self.assertEquals({"lockFile":True},result)

    """
    method: lockFile
    when: called
    with: idAndCloudAndUserAndIpServerAndDateTimeAndTimeLimit
    should: updateDataTimeExpired
    """
    def test_lockFile_called_idAndCloudAndUserAndIpServerAndDateTimeAndTimeLimit_updateDataTimeExpired(self):
        data = {"id":self.idFile,"cloud":self.cloud,"user":"tester","ipserver":"192.168.56.101","datetime":"2015-05-12 10:50:00","status":"open"}
        self.sut.db.collection.insert(data)
        result = self.sut.lockFile(self.idFile,self.cloud,self.user,self.ipserver,"2015-05-12 11:05:00",self.timelimit)
        self.assertEquals({"lockFile":True},result)

    """
    method: lockFile
    when: called
    with: idAndCloudAndUserAndIpServerAndDateTimeAndTimeLimit
    should: returnIncorrectDistinctUser
    """
    def test_lockFile_called_idAndCloudAndUserAndIpServerAndDateTimeAndTimeLimit_returnIncorrectDistinctUser(self):
        data = {"id":self.idFile,"cloud":self.cloud,"user":self.user,'ipserver':self.ipserver,"datetime":"2015-05-12 10:50:00","status":"open"}
        self.sut.db.collection.insert(data)
        result = self.sut.lockFile(self.idFile,self.cloud,"tester",self.ipserver,"2015-05-12 10:55:00",self.timelimit)
        self.assertEquals({"error":400,"descripcion":"Error al bloquear fichero"},result)

    """
    method: lockFile
    when: called
    with: idAndCloudAndUserAndIpServerAndDateTimeAndTimeLimit
    should: returnIncorrectDistinctServer
    """
    def test_lockFile_called_idAndCloudAndUserAndIpServerAndDateTimeAndTimeLimit_returnIncorrectDistinctServer(self):
        data = {"id":self.idFile,"cloud":self.cloud,"user":self.user,"ipserver":self.ipserver,"datetime":"2015-05-12 10:50:00","status":"open"}
        self.sut.db.collection.insert(data)
        result = self.sut.lockFile(self.idFile,self.cloud,self.user,"192.168.56.102","2015-05-12 10:55:00",self.timelimit)
        self.assertEquals({"error":400,"descripcion":"Error al bloquear fichero"},result)

    """
    method: updateDateTime
    when: called
    with: idAndCloudAndUserAndIpServerAndDateTime
    should: returnUpdateCorrect
    """
    def test_updateDateTime_called_idAndCloudAndUserAndIpServerAndDateTime_returnUpdateCorrect(self):
        data = {"id":self.idFile,"cloud":self.cloud,"user":self.user,"ipserver":self.ipserver,"datetime":"2015-05-12 10:50:00","status":"open"}
        self.sut.db.collection.insert(data)
        result = self.sut.updateDateTime(self.idFile,self.cloud,self.user,self.ipserver,"2015-05-12 11:50:00")
        self.assertEquals({"updateFile":True},result)

    """
    method: updateDateTime
    when: called
    with: idAndCloudAndUserAndIpServerAndDateTime
    should: returnIncorrectDistinctUser
    """
    def test_updateDateTime_called_idAndCloudAndUserAndIpServerAndDateTime_returnIncorrectDistinctUser(self):
        datetime = "2015-05-12 10:50:00"
        data = {"id":self.idFile,"cloud":self.cloud,"user":self.user,"ipserver":self.ipserver,"datetime":datetime,"status":"open"}
        self.sut.db.collection.insert(data)
        result = self.sut.updateDateTime(self.idFile,self.cloud,"tester",self.ipserver,datetime)
        self.assertEquals({"error":400,"descripcion":"Error al actualizar fecha"},result)

    """
    method: unLockFile
    when: called
    with: idAndCloudAndUserAndIpServerAndDateTime
    should: returnCorrect
    """
    def test_unLockFile_called_metadata_returnCorrect(self):
        data = {"id":self.idFile,"cloud":self.cloud,"user":self.user,"ipserver":self.ipserver,"datetime":"2015-05-12 10:50:00","status":"open"}
        self.sut.db.collection.insert(data)
        result = self.sut.unLockFile(self.idFile,self.cloud,self.user,self.ipserver,"2015-05-12 11:50:00")
        self.assertEquals({"unLockFile":True},result)


    """
    method: unLockFile
    when: called
    with: idAndCloudAndUserAndIpServerAndDateTime
    should: returnIncorrectDistinctUser
    """
    def test_unLockFile_called_metadata_returnIncorrectDistinctUser(self):
        data = {"id":self.idFile,"cloud":self.cloud,"user":self.user,"ipserver":self.ipserver,"datetime":"2015-05-12 10:50:00","status":"open"}
        self.sut.db.collection.insert(data)
        result = self.sut.unLockFile(self.idFile,self.cloud,"tester",self.ipserver,"2015-05-12 11:50:00")
        self.assertEquals({"error":400,"descripcion":"Error al liberar fichero"},result)

    """
    method: getMetadataFile
    when: called
    with: idAndCloud
    should: returnArray
    """
    def test_getMetadataFile_called_user_returnArray(self):
        data = {"id":self.idFile,"cloud":self.cloud,"user":self.user,"ipserver":self.ipserver,"datetime":"2015-05-12 10:50:00","status":"close"}
        self.sut.db.collection.insert(data)
        result = self.sut.getMetadataFile(self.idFile,self.cloud)
        self.assertEquals(1,len(result))