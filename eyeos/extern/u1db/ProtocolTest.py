#!/usr/bin/env python
# -*- coding: utf-8 -*-
__author__ = 'root'

import unittest
from mock import Mock
from Protocol import Protocol
import json
import os

class ProtocolTest (unittest.TestCase):
    def setUp(self):
        self.protocol = Protocol(True)

    def tearDown(self):
        os.remove("test.u1db")

    """
   method: protocol
   when: called
   with: typeInsertAndList
   should: insertCorrect
   """
    def test_protocol_called_typeInsertAndList_insertCorrect(self):
        params = '{"type":"insert","lista":[{"user_eyeos":"eyeID_EyeosUser_2","status": "NEW", "is_root": false, "version": 1, "filename": "clients", "parent_id": "null", "server_modified": "2013-03-08 10:36:41.997", "path": "/documents/clients", "client_modified": "2013-03-08 10:36:41.997", "id": 9873615, "user": "eyeos","is_folder":true}]}'
        aux = json.loads(params)
        self.protocol.insert = Mock()
        self.protocol.insert.return_value = True
        result = self.protocol.protocol(params)
        self.protocol.insert.assert_called_once_with(aux['lista'])
        self.assertEquals('true',result)

    """
    method: protocol
    when: called
    with: typeSelectAndList
    should: returnArray
    """
    def test_protocol_called_typeSelectAndList_returnArray(self):
        params = '{"type":"select","lista":[{"id":"124568","user_eyeos":"eyeID_EyeosUser_2","path":"/documents/clients"}]}'
        self.protocol.select = Mock()
        self.protocol.select.return_value = []
        result = self.protocol.protocol(params)
        self.protocol.select.assert_called_once_with("124568","eyeID_EyeosUser_2","/documents/clients")
        self.assertEquals('[]',result)

    """
    method: protocol
    when: called
    with: typeUpdateAndList
    should: updateCorrect
    """
    def test_protocol_called_typeUpdateAndList_updateCorrect(self):
        params = '{"type":"update","lista":[{"parent_old":"null"},{"user_eyeos":"eyeID_EyeosUser_2","status": "NEW", "is_root": false, "version": 1, "filename": "clients", "parent_id": "null", "server_modified": "2013-03-08 10:36:41.997", "path": "/documents/clients", "client_modified": "2013-03-08 10:36:41.997", "id": 9873615, "user": "eyeos","is_folder":true}]}'
        aux = json.loads(params)
        self.protocol.update = Mock()
        self.protocol.update.return_value = True
        result = self.protocol.protocol(params)
        self.protocol.update.assert_called_once_with(aux['lista'])
        self.assertEquals('true',result)

    """
    method: protocol
    when: called
    with: typeDeleteAndList
    should: deleteCorrect
    """
    def test_protocol_called_typeDeleteAndList_deleteCorrect(self):
        params = '{"type":"delete","lista":[{"id":1234,"user_eyeos":"eyeID_EyeosUser_2","parent_id":3456},{"id":8907,"user_eyeos":"eyeID_EyeosUser_2","parent_id":3456}]}'
        aux = json.loads(params)
        self.protocol.delete = Mock()
        self.protocol.delete.return_value = True
        result = self.protocol.protocol(params)
        self.protocol.delete.assert_called_once_with(aux['lista'])
        self.assertEquals('true',result)

    """
    method: protocol
    when: called
    with: typeGetParentAndPath
    should: returnArray
    """
    def test_protocol_called_typeGetParentAndList_returnArray(self):
        params = '{"type":"parent","lista":[{"path":"/Documents/","filename":"prueba","user_eyeos":"eyeID_EyeosUser_2"}]}'
        self.protocol.getParent = Mock()
        self.protocol.getParent.return_value = []
        result = self.protocol.protocol(params)
        self.protocol.getParent.assert_called_once_with("/Documents/","prueba","eyeID_EyeosUser_2")
        self.assertEquals('[]',result)

    """
    method: protocol
    when: called
    with: typeDeleteFolderAndList
    should: deleteCorrect
    """
    def test_protocol_called_typeDeleteFolderAndList_deleteCorrect(self):
        params = '{"type":"deleteFolder","lista":[{"id":"1234","user_eyeos":"eyeID_EyeosUser_2","path":"/documents/clients"}]}'
        self.protocol.deleteFolder = Mock()
        self.protocol.deleteFolder.return_value = True
        result = self.protocol.protocol(params)
        self.protocol.deleteFolder.assert_called_once_with("1234","eyeID_EyeosUser_2","/documents/clients")
        self.assertEquals('true',result)

    """
    method: protocol
    when: called
    with: typeDeleteMetadataUserAndList
    should: deleteCorrect
    """
    def test_protocol_called_typeDeleteMetadataUserAndList_deleteCorrect(self):
        params = '{"type":"deleteMetadataUser","lista":[{"user_eyeos":"eyeID_EyeosUser_2"}]}'
        self.protocol.deleteMetadataUser = Mock()
        self.protocol.deleteMetadataUser.return_value = True
        result = self.protocol.protocol(params)
        self.protocol.deleteMetadataUser.assert_called_once_with("eyeID_EyeosUser_2")
        self.assertEquals('true',result)

    """
   method: protocol
   when: called
   with: typeSelectMetatadataUserAndList
   should: returnArray
   """
    def test_protocol_called_typeSelectMetadataUserAndList_returnArray(self):
        params = '{"type":"selectMetadataUser","lista":[{"user_eyeos":"eyeID_EyeosUser_2"}]}'
        self.protocol.selectMetadataUser = Mock()
        self.protocol.selectMetadataUser.return_value = []
        result = self.protocol.protocol(params)
        self.protocol.selectMetadataUser.assert_called_once_with("eyeID_EyeosUser_2")
        self.assertEquals('[]',result)

    """
    method: protocol
    when: called
    with: typeRenameMetadataAndUserAndList
    """
    def test_protocol_called_typeRenameMetadataAndUserAndList_renameCorrect(self):
        params = '{"type":"rename","lista":[{"user_eyeos":"eyeID_EyeosUser_2","status": "NEW", "version": 1, "filename": "prueba.txt", "parent_id": "null", "server_modified": "2013-03-08 10:36:41.997", "path": "/", "client_modified": "2013-03-08 10:36:41.997", "id": 9873615, "user": "eyeos","is_folder":false}]}'
        self.protocol.renameMetadata = Mock()
        self.protocol.renameMetadata.return_value = True
        result = self.protocol.protocol(params)
        self.protocol.renameMetadata.assert_called_once_with({"user_eyeos":"eyeID_EyeosUser_2","status": "NEW", "version": 1, "filename": "prueba.txt", "parent_id": "null", "server_modified": "2013-03-08 10:36:41.997", "path": "/", "client_modified": "2013-03-08 10:36:41.997", "id": 9873615, "user": "eyeos","is_folder":False})
        self.assertEquals('true',result)


    """
   ##################################################################################################################################################
                                                                   TEST CALENDAR
   ##################################################################################################################################################
   """

    """
    method: protocol
    when: called
    with: typeDeleteEventAndList
    should: deleteCorrect
    """
    def test_protocol_called_typeDeleteEventAndList_deleteCorrect(self):
        params = '{"type":"deleteEvent" , "lista":[{"type":"event","user_eyeos": "eyeos","calendar": "personal", "status":"DELETED" ,"isallday":"0", "timestart": "201419160000", "timeend":"201419170000", "repetition": "None", "finaltype": "1", "finalvalue": "0", "subject": "Visita Médico", "location": "Barcelona", "description": "Llevar justificante"},{"type":"event","user_eyeos": "eyeos","calendarid": "eyeID_Calendar_2b", "isallday": "1", "timestart": "201420160000", "timeend":"201420170000", "repetition": "None", "finaltype": "1", "finalvalue": "0", "subject": "Excursión", "location": "Girona", "description": "Mochila"}]}'
        aux = json.loads(params)
        self.protocol.deleteEvent = Mock()
        self.protocol.deleteEvent.return_value = True
        result = self.protocol.protocol(params)
        self.protocol.deleteEvent.assert_called_once_with(aux['lista'])
        self.assertEquals("true",result)

    """
    method: protocol
    when: called
    with: typeUpdateEventAndList
    should: updateCorrect
    """
    def test_protocol_called_typeUpdateEventAndList_updateCorrect(self):
        params = '{"type":"updateEvent" , "lista":[{"type":"event","user_eyeos": "eyeos","calendar": "personal", "status":"CHANGED", "isallday":"0", "timestart": "201419160000", "timeend":"201419170000", "repetition": "None", "finaltype": "1", "finalvalue": "0", "subject": "Visita Médico", "location": "Barcelona", "description": "Llevar justificante"},{"type":"event","user_eyeos": "eyeos","calendarid": "eyeID_Calendar_2b", "isallday": "1", "timestart": "201420160000", "timeend":"201420170000", "repetition": "None", "finaltype": "1", "finalvalue": "0", "subject": "Excursión", "location": "Girona", "description": "Mochila"}]}'
        aux = json.loads(params)
        self.protocol.updateEvent = Mock()
        self.protocol.updateEvent.return_value = True
        result = self.protocol.protocol(params)
        self.protocol.updateEvent.assert_called_once_with(aux['lista'])
        self.assertEquals("true",result)

    """
    method: protocol
    when: called
    with: typeSelectEventAndList
    should: return Array
    """
    def test_protocol_called_typeSelectEventAndList_returnArray(self):
        params = '{"type":"selectEvent","lista":[{"type":"event","user_eyeos":"eyeos","calendar":"personal"}]}'
        aux = json.loads(params)
        self.protocol.selectEvent = Mock()
        self.protocol.selectEvent.return_value = []
        result = self.protocol.protocol(params)
        self.protocol.selectEvent.assert_called_once_with("event","eyeos","personal")
        self.assertEquals("[]",result)

    """
    method: protocol
    when: called
    with: typeInsertEventAndList
    should: insertCorrect
    """
    def test_protocol_called_typeInsertEventAndList_insertCorrect(self):
        params = '{"type":"insertEvent" , "lista":[{"type":"event","user_eyeos": "eyeos","calendar": "personal", "status":"NEW", "isallday":"0", "timestart": "201419160000", "timeend":"201419170000", "repetition": "None", "finaltype": "1", "finalvalue": "0", "subject": "Visita Médico", "location": "Barcelona", "description": "Llevar justificante"},{"type":"event","user_eyeos": "eyeos","calendarid": "eyeID_Calendar_2b", "isallday": "1", "timestart": "201420160000", "timeend":"201420170000", "repetition": "None", "finaltype": "1", "finalvalue": "0", "subject": "Excursión", "location": "Girona", "description": "Mochila"}]}'
        aux = json.loads(params)
        self.protocol.insertEvent = Mock()
        self.protocol.insertEvent.return_value = True
        result = self.protocol.protocol(params)
        self.protocol.insertEvent.assert_called_once_with(aux['lista'])
        self.assertEquals("true",result)


    """
   method: protocol
   when: called
   with: typeInsertCalendarAndList
   should: insertCorrect
   """
    def test_protocol_called_typeInsertCalendarAndList_insertCorrect(self):
        params = '{"type":"insertCalendar" , "lista":[{"type":"calendar","user_eyeos": "eyeos","name": "personal", "status":"NEW","description":"personal calendar","timezone":0}]}'
        aux = json.loads(params)
        self.protocol.insertCalendar = Mock()
        self.protocol.insertCalendar.return_value = True
        result = self.protocol.protocol(params)
        self.protocol.insertCalendar.assert_called_once_with(aux['lista'])
        self.assertEquals("true",result)

    """
    method: protocol
    when: called
    with: typeDeleteCalendarAndList
    should: deleteCorrect
    """
    def test_protocol_called_typeDeleteCalendarAndList_deleteCorrect(self):
        params = '{"type":"deleteCalendar" , "lista":[{"type":"calendar","user_eyeos": "eyeos","name": "personal"}]}'
        aux = json.loads(params)
        self.protocol.deleteCalendar = Mock()
        self.protocol.deleteCalendar.return_value = True
        result = self.protocol.protocol(params)
        self.protocol.deleteCalendar.assert_called_once_with(aux['lista'])
        self.assertEquals("true",result)


    """
    method: protocol
    when: called
    with: typeSelectCalendarAndList
    should: returnArray
    """
    def test_protocol_called_typeSelectCalendarAndList_returnArray(self):
        params = '{"type":"selectCalendar" , "lista":[{"type":"calendar","user_eyeos": "eyeos"}]}'
        aux = json.loads(params)
        self.protocol.selectCalendar = Mock()
        self.protocol.selectCalendar.return_value = []
        result = self.protocol.protocol(params)
        self.protocol.selectCalendar.assert_called_once_with(aux['lista'][0])
        self.assertEquals("[]",result)

    """
   method: protocol
   when: called
   with: typeUpdateCalendarAndList
   should: updateCorrect
   """
    def test_protocol_called_typeUpdateCalendarAndList_updateCorrect(self):
        params = '{"type":"updateCalendar" , "lista":[{"type":"calendar","user_eyeos": "eyeos","name":"personal","description":"personal calendar","timezone":0,"status":"CHANGED"}]}'
        aux = json.loads(params)
        self.protocol.updateCalendar = Mock()
        self.protocol.updateCalendar.return_value = True
        result = self.protocol.protocol(params)
        self.protocol.updateCalendar.assert_called_once_with(aux['lista'])
        self.assertEquals("true",result)

    """
    method: protocol
    when: called
    with: typeDeleteCalendarUserAndList
    should: deleteCorrect
    """
    def test_protocol_called_typeDeleteCalendarUserAndList_deleteCorrect(self):
        params = '{"type":"deleteCalendarUser","lista":[{"user_eyeos":"eyeos"}]}'
        self.protocol.deleteCalendarUser = Mock()
        self.protocol.deleteCalendarUser.return_value = True
        result = self.protocol.protocol(params)
        self.protocol.deleteCalendarUser.assert_called_once_with("eyeos")
        self.assertEquals('true',result)

    """
    method: protocol
    when: called
    with: selectCalendarsAndEventsAndList
    should: returnArray
    """
    def test_protocol_called_selectCalendarsAndEventsAndList_returnArray(self):
        params = '{"type":"selectCalendarsAndEvents","lista":[{"user_eyeos":"eyeos"}]}'
        self.protocol.selectCalendarsAndEvents = Mock()
        self.protocol.selectCalendarsAndEvents.return_value = []
        result = self.protocol.protocol(params)
        self.protocol.selectCalendarsAndEvents.assert_called_once_with("eyeos")
        self.assertEquals('[]',result)