#!/usr/bin/env python
# -*- coding: utf-8 -*-
__author__ = 'root'

import unittest
import u1db
import os
from Metadata import Metadata

class MetadataTest (unittest.TestCase):

    def setUp(self):
        self.sut = Metadata("test.u1db",{'oauth':{'token_key':'NKKN8XVZLP5X23X','token_secret':'59ZN54UEUD3ULRU','consumer_key':'keySebas','consumer_secret':'secretSebas'}})

    def tearDown(self):
        self.sut.db.close()
        os.remove("test.u1db")

    """
    method: insert
    when: called
    with: array
    should: insertCorrect
    """
    def test_insert_called_array_insertCorrect(self):
        array = self.getArrayInsert()
        self.sut.insert(array)
        self.sut.db.create_index("by-fileid", "file_id", "user_eyeos")
        results = self.sut.db.get_from_index("by-fileid", "-7755273878059615652","eyeos")
        self.assertEquals(array[1],results[0].content)

    """
    method: select
    when: called
    with: id
    should: returnArray
    """
    def test_select_called_id_returnArray(self):
        array = self.getArrayInsert()
        self.sut.insert(array)
        data = self.sut.select("null","eyeos")
        data.sort()
        self.assertEquals(array,data)

    """
    method: update
    when: called
    with: array
    should: updateCorrect
    """
    def test_update_called_array_updateCorrect(self):
        array = self.getArrayInsert()
        update = self.getArrayUpdate()
        self.sut.insert(array)
        self.sut.update(update)
        self.sut.db.create_index("by-user","user_eyeos")
        files = self.sut.db.get_from_index("by-user","eyeos")

        results = []
        for file in files:
            results.append(file.content)

        results.sort()
        self.assertEquals(update,results)


    """
    method: delete
    when: called
    with: array
    should: deleteCorrect
    """
    def test_delete_called_array_deleteCorrect(self):
        array = self.getArrayInsert()
        self.sut.insert(array)
        list = self.getArrayDelete()
        self.sut.delete(list)
        self.sut.db.create_index("by-user", "eyeos_user")
        files = self.sut.db.get_from_index("by-user","eyeos")
        self.assertEquals(0,len(files))


    """
    method: getParent
    when: called
    with: path
    should: returnArray
      """
    def test_getParent_called_path_returnArray(self):
        array = self.getArrayParent()
        self.sut.insert(array)
        data = self.sut.getParent('/Documents/prueba/',"hola","eyeos")
        self.assertEquals(array[2],data[0])

    """
    method: deleteFolder
    when: called
    with: idFolder
    should: returnCorrect
    """
    def test_deleteFolder_called_idFolder_returnCorrect(self):
        array = self.getArrayDeleteFolder()
        self.sut.insert(array)
        self.sut.deleteFolder("754050","eyeos")
        docs = self.sut.db.get_all_docs()
        self.assertEquals(0,len(docs[1]))

    """
    method: deleteMetadataUser
    when: called
    with: user
    should: deleteCorrect
    """
    def test_deleteMetadataUser_called_user_deleteCorrect(self):
        array = self.getArrayInsert()
        self.sut.insert(array)
        self.sut.deleteMetadataUser('eyeos')
        docs = self.sut.db.get_all_docs()
        self.assertEquals(0,len(docs[1]))

    """
    method: selectMetadataUser
    when: called
    with: user
    should: return Array
    """
    def test_selectMetadataUser_called_user_returnArray(self):
        array = self.getArrayInsert()
        self.sut.insert(array)
        files = self.sut.selectMetadataUser('eyeos')
        files.sort()
        self.assertEquals(array,files)


    """
    method: deleteEvent
    when: called
    with: array
    should: deleteCorrect
    """
    def test_deleteEvent_called_array_deleteCorrect(self):
        array = self.getArrayInsertEvent()
        self.sut.insert(array)
        list = self.getArrayDeleteEvent()
        self.sut.deleteEvent(list)
        files = self.sut.db.get_all_docs()
        results = []
        for file in files[1]:
            results.append(file.content)
        results.sort()
        self.assertEquals(list,results)

    """
    method: updateEvent
    when: called
    with: array
    should: updateCorrect
    """
    def test_updateEvent_called_array_updateCorrect(self):
        array = self.getArrayInsertEvent()
        self.sut.insert(array)
        update = self.getArrayUpdateEvent()
        self.sut.updateEvent(update)
        files = self.sut.db.get_all_docs();
        results = []
        for file in files[1]:
            results.append(file.content)
        results.sort()
        self.assertEquals(update,results)

    """
    method: selectEvent
    when: called
    with: userAndIdCalendar
    should: returnArray
    """
    def test_selectEvent_called_userAndIdCalendar_returnArray(self):
        array = self.getArrayInsertEvent()
        self.sut.insert(array)
        data = self.sut.selectEvent('event','eyeos','laboral')
        self.assertEquals(2,len(data))
        """self.db = u1db.open("metadata.u1db", create=True)
        files = self.db.get_all_docs()
        for file in files[1]:
            print(file.content)"""


    """
    method: insertEvent
    when: called
    with: array
    should: insertCorrect
    """
    def test_insertEvent_called_array_insertCorrect(self):
        array = self.getArrayInsertEvent()
        self.sut.insertEvent(array)
        array2 = [{u'type':u'event',u'user_eyeos': u'eyeos',u'calendar': u'personal',u'status':u'NEW', u'isallday': u'0', u'timestart': u'201419160000', u'timeend':u'201419170000', u'repetition': u'None', u'finaltype': u'1', u'finalvalue': u'0', u'subject': u'Visita Médico', u'location': u'Barcelona', u'description': u'Llevar justificante'}]
        self.sut.insertEvent(array2)
        files = self.sut.db.get_all_docs()
        results = []
        for file in files[1]:
            results.append(file.content)
        results.sort()
        self.assertEquals(array,results)

    """
    method: insertCalendar
    when: called
    with: array
    should: insertCorrect
    """
    def test_insertCalendar_called_array_insertCorrect(self):
        array = self.getArrayInsertCalendar()
        self.sut.insertCalendar(array)
        array2 = [{u'type':u'calendar',u'user_eyeos':u'eyeos',u'name':u'school',u'status':u'NEW',u'description':u'school calendar',u'timezone':0}]
        self.sut.insertCalendar(array2)
        files = self.sut.db.get_all_docs()
        results = []
        for file in files[1]:
            results.append(file.content)
        results.sort()
        self.assertEquals(array,results)

    """
    method: deleteCalendar
    when: called
    with: array
    should: deleteCorrect
    """
    def test_deleteCalendar_called_array_deleteCorrect(self):
        array = self.getArrayInsertCalendar()
        self.sut.insertCalendar(array)
        listEvents = self.getArrayInsertCalendarEvents()
        self.sut.insertEvent(listEvents)
        arrayDelete = self.getArrayDeleteCalendar()
        self.sut.deleteCalendar(arrayDelete)
        files = self.sut.db.get_all_docs()
        results = []
        for file in files[1]:
            results.append(file.content)
        results.sort()
        self.assertEquals(self.getArrayDeleteCalendarAndEvents("DELETED"),results)

    """
    method: selectCalendar
    when: called
    with: nameCalendar
    should: returnArray
    """
    def test_selectCalendar_called_nameCalendar_returnArray(self):
        array = self.getArrayInsertCalendar()
        self.sut.insertCalendar(array)
        select = {u'type':u'calendar',u'user_eyeos':u'eyeos'}
        calendar = self.sut.selectCalendar(select)
        calendar.sort()
        self.assertEquals(array,calendar)

    """
    method: updateCalendar
    when: called
    with: array
    should: updateCorrect
    """
    def test_updateCalendar_called_array_updateCorrect(self):
        array = self.getArrayInsertCalendar()
        self.sut.insertCalendar(array)
        arrayUpdate = [{u'type':u'calendar',u'user_eyeos':u'eyeos',u'name':u'personal',u'status':u'DELETED',u'description':u'personal calendar',u'timezone':0}]
        self.sut.updateCalendar(arrayUpdate)
        calendar = self.sut.getCalendar({u'type':u'calendar',u'user_eyeos':u'eyeos',u'name':u'personal'})
        self.assertEquals(arrayUpdate[0],calendar[0].content)

    """
    method: deleteCalendarUser
    when: called
    with: user
    should: deleteCorrect
    """
    def test_deleteCalendarUser_called_user_deleteCorrect(self):
        calendars = self.getArrayInsertCalendar()
        self.sut.insertCalendar(calendars)
        events = self.getArrayInsertCalendarEvents()
        self.sut.insertEvent(events)
        self.sut.deleteCalendarUser('eyeos')
        files = self.sut.db.get_all_docs()
        self.assertEquals(0,len(files[1]))

    """
    method: selectCalendarsAndEvents
    when: called
    with: user
    should: returnArray
    """
    def test_selectCalendarsAndEvents_called_user_returnArray(self):
        calendars = self.getArrayInsertCalendar()
        self.sut.insertCalendar(calendars)
        self.sut.insertCalendar([{u'type':u'calendar',u'user_eyeos':u'eyeos',u'name':u'class',u'status':u'DELETED'}])
        events = self.getArrayInsertCalendarEvents()
        self.sut.insertEvent(events)
        self.sut.insertEvent([{u'type':u'event',u'user_eyeos': u'eyeos',u'calendar': u'class',u'status':u'DELETED', u'isallday': u'0', u'timestart': u'201419160000', u'timeend':u'201419170000', u'repetition': u'None', u'finaltype': u'1', u'finalvalue': u'0', u'subject': u'Visita Médico', u'location': u'Barcelona', u'description': u'Llevar justificante'}])
        files = self.sut.selectCalendarsAndEvents('eyeos')
        files.sort()
        self.assertEquals(self.getArrayDeleteCalendarAndEvents("NEW"),files)


    def getArrayInsert(self):
        array = [{u'user_eyeos': u'eyeos',u'status': None, u'mimetype': None, u'parent_file_id': u'null', u'checksum': None, u'filename': u'Root', u'is_root': True, u'version': None, u'file_id': u'null', u'is_folder': True, u'path': None, u'size': None, u'user': None},
                 {u'user_eyeos': u'eyeos',u'status': u'NEW', u'mimetype': u'inode/directory', u'parent_file_id': u'null', u'server_modified': u'2013-11-11 15:40:45.784', u'checksum': 0, u'client_modified': u'2013-11-11 15:40:45.784', u'filename': u'helpFolder', u'is_root': False, u'version': 1, u'file_id': -7755273878059615652, u'is_folder': True, u'path': u'/', u'size': 0, u'user': u'web'}]
        array.sort()
        return array

    def getArrayUpdate(self):
        array = [{u'user_eyeos': u'eyeos',u'status': None, u'mimetype': None, u'parent_file_id': u'null', u'checksum': None, u'filename': u'Documents', u'is_root': True, u'version': None, u'file_id': u'null', u'is_folder': True, u'path': None, u'size': None, u'user': None},
                 {u'user_eyeos': u'eyeos',u'status': u'NEW', u'mimetype': u'inode/directory', u'parent_file_id': u'null', u'server_modified': u'2013-11-11 15:40:45.784', u'checksum': 0, u'client_modified': u'2013-11-11 15:40:45.784', u'filename': u'helpFile', u'is_root': False, u'version': 1, u'file_id': -7755273878059615652, u'is_folder': False, u'path': u'/', u'size': 0, u'user': u'web'}]
        array.sort()
        return array

    def getArrayDelete(self):
        array = [{u'file_id': u'null',u'user_eyeos': u'eyeos'},
                 {u'file_id': -7755273878059615652,u'user_eyeos': u'eyeos'}]
        array.sort()
        return array

    def getArrayParent(self):
        array = [{u'user_eyeos': u'eyeos',u'status': None, u'mimetype': None, u'parent_file_id': u'null', u'checksum': None, u'filename': u'Documents', u'is_root': True, u'version': None, u'file_id': u'754050', u'is_folder': True, u'path': u'/', u'size': None, u'user': None},
                 {u'user_eyeos': u'eyeos',u'status': None, u'mimetype': None, u'parent_file_id': u'754050', u'checksum': None, u'filename': u'prueba', u'is_root': True, u'version': None, u'file_id': u'123456', u'is_folder': True, u'path': u'/Documents/', u'size': None, u'user': None},
                 {u'user_eyeos': u'eyeos',u'status': None, u'mimetype': None, u'parent_file_id': u'123456', u'checksum': None, u'filename': u'hola', u'is_root': True, u'version': None, u'file_id': u'77777', u'is_folder': True, u'path': u'/Documents/prueba/', u'size': None, u'user': None},
                 {u'user_eyeos': u'eyeos',u'status': None, u'mimetype': None, u'parent_file_id': u'123456', u'checksum': None, u'filename': u'pepe', u'is_root': True, u'version': None, u'file_id': u'88888', u'is_folder': True, u'path': u'/Documents/prueba/', u'size': None, u'user': None}]
        return array

    def getArrayDeleteFolder(self):
        array = [{u'user_eyeos': u'eyeos',u'status': None, u'mimetype': None, u'parent_file_id': u'null', u'checksum': None, u'filename': u'Documents', u'is_root': True, u'version': u'1', u'file_id': u'754050', u'is_folder': True, u'path': u'/', u'size': None, u'user': None},
                 {u'user_eyeos': u'eyeos',u'status': None, u'mimetype': None, u'parent_file_id': u'754050', u'checksum': None, u'filename': u'prueba', u'is_root': False, u'version': u'1', u'file_id': u'123456', u'is_folder': True, u'path': u'/Documents/', u'size': None, u'user': None},
                 {u'user_eyeos': u'eyeos',u'status': None, u'mimetype': None, u'parent_file_id': u'123456', u'checksum': None, u'filename': u'hola.txt', u'is_root': False, u'version': u'3', u'file_id': u'77777', u'is_folder': False, u'path': u'/Documents/prueba/', u'size': u'6', u'user': None},
                 {u'user_eyeos': u'eyeos',u'status': None, u'mimetype': None, u'parent_file_id': u'123456', u'checksum': None, u'filename': u'pepe.txt', u'is_root': False, u'version': u'2', u'file_id': u'88888', u'is_folder': False, u'path': u'/Documents/prueba/', u'size': u'15', u'user': None},
                 {u'user_eyeos': u'eyeos',u'status': None, u'mimetype': None, u'parent_file_id': u'123456', u'checksum': None, u'filename': u'folder', u'is_root': False, u'version': u'1', u'file_id': u'99999', u'is_folder': True, u'path': u'/Documents/prueba/', u'size': None, u'user': None},
                 {u'user_eyeos': u'eyeos',u'status': None, u'mimetype': None, u'parent_file_id': u'123456', u'checksum': None, u'filename': u'folder2', u'is_root': False, u'version': u'1', u'file_id': u'11111', u'is_folder': True, u'path': u'/Documents/prueba/', u'size': None, u'user': None},
                 {u'user_eyeos': u'eyeos',u'status': None, u'mimetype': None, u'parent_file_id': u'11111', u'checksum': None, u'filename': u'test.txt', u'is_root': False, u'version': u'4', u'file_id': u'22222', u'is_folder': False, u'path': u'/Documents/prueba/folder2/', u'size': u'46', u'user': None}]
        return array


    def getArrayInsertEvent(self):
        array = [{u'type':u'event',u'user_eyeos': u'eyeos',u'calendar': u'personal',u'status':u'NEW', u'isallday': u'0', u'timestart': u'201419160000', u'timeend':u'201419170000', u'repetition': u'None', u'finaltype': u'1', u'finalvalue': u'0', u'subject': u'Visita Médico', u'location': u'Barcelona', u'description': u'Llevar justificante'},
                 {u'type':u'event',u'user_eyeos': u'eyeos',u'calendar': u'laboral', u'status':u'NEW',u'isallday': u'1', u'timestart': u'201420160000', u'timeend':u'201420170000', u'repetition': u'None', u'finaltype': u'1', u'finalvalue': u'0', u'subject': u'Excursión', u'location': u'Girona', u'description': u'Mochila'},
                 {u'type':u'event',u'user_eyeos': u'eyeos',u'calendar': u'laboral',u'status':u'NEW', u'isallday': u'0', u'timestart': u'201421173000', u'timeend':u'201421183000', u'repetition': u'EveryWeek', u'finaltype': u'1', u'finalvalue': u'0', u'subject': u'ClaseInglés', u'location': u'Hospitalet', u'description': u'Trimestre'}]
        """array = [{u'status': u'NEW', u'description': u'Medico', u'finalvalue': u'0', u'finaltype': 1, u'subject': u'Prueba', u'timeend': 1395930600, u'timestart': 1395928800, u'user_eyeos': u'eyeos', u'location': u'Barcelona', u'repeattype': u'n', u'calendar': u'eyeos', u'repetition': u'None', u'type': u'event', u'isallday': 0}]"""
        array.sort()
        return array

    def getArrayDeleteEvent(self):
        array = [{u'type': u'event',u'user_eyeos': u'eyeos',u'calendar':u'personal',u'status':u'DELETED',u'timestart':u'201419160000',u'timeend':u'201419170000',u'isallday':u'0'},
                 {u'type': u'event',u'user_eyeos': u'eyeos',u'calendar':u'laboral', u'status':u'DELETED',u'timestart':u'201420160000',u'timeend':u'201420170000',u'isallday':u'1'},
                 {u'type': u'event',u'user_eyeos': u'eyeos',u'calendar':u'laboral',u'status':u'DELETED',u'timestart':u'201421173000',u'timeend':u'201421183000',u'isallday':u'0'}]
        """array = [{u'type':u'event',u'user_eyeos':u'eyeos',u'calendar':u'eyeos',u'status':u'DELETED',u'isallday':0,u'timestart':1395928800,u'timeend':1395930600,u'repetition':u'None',u'finaltype':1,u'finalvalue':u'0',u'subject':u'Prueba',u'location':u'Barcelona',u'repeattype':u'n',u'description':u'Medico'}]"""
        array.sort()
        return array

    def getArrayUpdateEvent(self):
        array = [{u'type':u'event',u'user_eyeos': u'eyeos',u'calendar': u'personal',u'status':u'CHANGED', u'isallday': u'0', u'timestart': u'201419160000', u'timeend':u'201419170000', u'repetition': u'None', u'finaltype': u'1', u'finalvalue': u'0', u'subject': u'Visita Museo', u'location': u'Esplugues de llobregat', u'description': u'Llevar Ticket'},
                 {u'type':u'event',u'user_eyeos': u'eyeos',u'calendar': u'laboral', u'status':u'CHANGED',u'isallday': u'1', u'timestart': u'201420160000', u'timeend':u'201420170000', u'repetition': u'None', u'finaltype': u'1', u'finalvalue': u'0', u'subject': u'Excursión', u'location': u'Girona', u'description': u'Mochila'},
                 {u'type':u'event',u'user_eyeos': u'eyeos',u'calendar': u'laboral', u'status':u'CHANGED',u'isallday': u'0',u'timestart': u'201421173000', u'timeend':u'201421183000',u'repetition': u'EveryMonth', u'finaltype': u'1', u'finalvalue': u'0', u'subject': u'ClaseFrancés', u'location': u'Hospitalet', u'description': u'Trimestre'}]
        array.sort()
        return array

    def getArrayInsertCalendar(self):
        array =[{u'type':u'calendar',u'user_eyeos':u'eyeos',u'name':u'personal',u'status':u'NEW',u'description':u'personal calendar',u'timezone':0},
                {u'type':u'calendar',u'user_eyeos':u'eyeos',u'name':u'school',u'status':u'NEW',u'description':u'school calendar',u'timezone':0}]
        array.sort()
        return array

    def getArrayInsertCalendarEvents(self):
        array = [{u'type':u'event',u'user_eyeos': u'eyeos',u'calendar': u'personal',u'status':u'NEW', u'isallday': u'0', u'timestart': u'201419160000', u'timeend':u'201419170000', u'repetition': u'None', u'finaltype': u'1', u'finalvalue': u'0', u'subject': u'Visita Médico', u'location': u'Barcelona', u'description': u'Llevar justificante'},
                 {u'type':u'event',u'user_eyeos': u'eyeos',u'calendar': u'personal', u'status':u'NEW',u'isallday': u'1', u'timestart': u'201420160000', u'timeend':u'201420170000', u'repetition': u'None', u'finaltype': u'1', u'finalvalue': u'0', u'subject': u'Excursión', u'location': u'Girona', u'description': u'Mochila'}]
        array.sort()
        return array

    def getArrayDeleteCalendar(self):
        array =[{u'type':u'calendar',u'user_eyeos':u'eyeos',u'name':u'personal'},
                {u'type':u'calendar',u'user_eyeos':u'eyeos',u'name':u'school'}]
        array.sort()
        return array

    def getArrayDeleteCalendarAndEvents(self,status):
        array =[{u'type':u'calendar',u'user_eyeos':u'eyeos',u'name':u'personal',u'status':u'' + status + '',u'description':u'personal calendar',u'timezone':0},
                {u'type':u'calendar',u'user_eyeos':u'eyeos',u'name':u'school',u'status':u'' + status +'',u'description':u'school calendar',u'timezone':0},
                {u'type':u'event',u'user_eyeos': u'eyeos',u'calendar': u'personal',u'status':u'' + status +'', u'isallday': u'0', u'timestart': u'201419160000', u'timeend':u'201419170000', u'repetition': u'None', u'finaltype': u'1', u'finalvalue': u'0', u'subject': u'Visita Médico', u'location': u'Barcelona', u'description': u'Llevar justificante'},
                {u'type':u'event',u'user_eyeos': u'eyeos',u'calendar': u'personal', u'status':u'' + status +'',u'isallday': u'1', u'timestart': u'201420160000', u'timeend':u'201420170000', u'repetition': u'None', u'finaltype': u'1', u'finalvalue': u'0', u'subject': u'Excursión', u'location': u'Girona', u'description': u'Mochila'}]
        array.sort()
        return array





