#!/usr/bin/env python
# -*- coding: utf-8 -*-
__author__ = 'root'

import unittest
import u1db
import os
from Metadata import Metadata

class MetadataTest (unittest.TestCase):

    def setUp(self):
        self.db = u1db.open("test.u1db", create=True)
        self.sut = Metadata(self.db)

    def tearDown(self):
        self.db.close()
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
        self.db.create_index("by-fileid", "file_id", "user_eyeos")
        results = self.db.get_from_index("by-fileid", "-7755273878059615652","eyeos")
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
        self.db.create_index("by-user","user_eyeos")
        files = self.db.get_from_index("by-user","eyeos")

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
        self.db.create_index("by-user", "eyeos_user")
        files = self.db.get_from_index("by-user","eyeos")
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
        docs = self.db.get_all_docs()
        self.assertEquals(0,len(docs[1]))

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
        docs = self.db.get_all_docs();
        self.assertEquals(0,len(docs[1]))

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
        files = self.db.get_all_docs();
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

    """
    method: insertEvent
    when: called
    with: array
    should: insertCorrect
    """
    def test_insertEvent_called_array_insertCorrect(self):
        array = self.getArrayInsertEvent()
        self.sut.insertEvent(array)
        files = self.db.get_all_docs()
        results = []
        for file in files[1]:
            results.append(file.content)
        results.sort()
        self.assertEquals(array,results)

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
        array = [{u'type':u'event',u'user_eyeos': u'eyeos',u'calendar': u'personal', u'isallday': u'0', u'timestart': u'201419160000', u'timeend':u'201419170000', u'repetition': u'None', u'finaltype': u'1', u'finalvalue': u'0', u'subject': u'Visita Médico', u'location': u'Barcelona', u'description': u'Llevar justificante'},
                 {u'type':u'event',u'user_eyeos': u'eyeos',u'calendar': u'laboral', u'isallday': u'1', u'timestart': u'201420160000', u'timeend':u'201420170000', u'repetition': u'None', u'finaltype': u'1', u'finalvalue': u'0', u'subject': u'Excursión', u'location': u'Girona', u'description': u'Mochila'},
                 {u'type':u'event',u'user_eyeos': u'eyeos',u'calendar': u'laboral', u'isallday': u'0', u'timestart': u'201421173000', u'timeend':u'201421183000', u'repetition': u'EveryWeek', u'finaltype': u'1', u'finalvalue': u'0', u'subject': u'ClaseInglés', u'location': u'Hospitalet', u'description': u'Trimestre'}]
        array.sort()
        return array

    def getArrayDeleteEvent(self):
        array = [{u'type': u'event',u'user_eyeos': u'eyeos',u'calendar':u'personal',u'timestart':u'201419160000',u'timeend':u'201419170000',u'isallday':u'0'},
                 {u'type': u'event',u'user_eyeos': u'eyeos',u'calendar':u'laboral',u'timestart':u'201420160000',u'timeend':u'201420170000',u'isallday':u'1'},
                 {u'type': u'event',u'user_eyeos': u'eyeos',u'calendar':u'laboral',u'timestart':u'201421173000',u'timeend':u'201421183000',u'isallday':u'0'}]
        return array

    def getArrayUpdateEvent(self):
        array = [{u'type':u'event',u'user_eyeos': u'eyeos',u'calendar': u'personal', u'isallday': u'0',u'isalldayOld': u'0', u'timestartOld': u'201419160000', u'timestart': u'201419173000', u'timeendOld':u'201419170000',u'timeend':u'201419183000', u'repetition': u'None', u'finaltype': u'1', u'finalvalue': u'0', u'subject': u'Visita Museo', u'location': u'Esplugues de llobregat', u'description': u'Llevar Ticket'},
                 {u'type':u'event',u'user_eyeos': u'eyeos',u'calendar': u'laboral', u'isalldayOld': u'1',u'isallday': u'0', u'timestart': u'201420160000', u'timestartOld': u'201420160000', u'timeendOld':u'201420170000', u'timeend':u'201420170000', u'repetition': u'None', u'finaltype': u'1', u'finalvalue': u'0', u'subject': u'Excursión', u'location': u'Girona', u'description': u'Mochila'},
                 {u'type':u'event',u'user_eyeos': u'eyeos',u'calendar': u'laboral', u'isalldayOld': u'0', u'isallday': u'0',u'timestartOld': u'201421173000', u'timestart': u'201421173000',u'timeendOld':u'201421183000', u'timeend':u'201421183000',u'repetition': u'EveryMonth', u'finaltype': u'1', u'finalvalue': u'0', u'subject': u'ClaseFrancés', u'location': u'Hospitalet', u'description': u'Trimestre'}]
        array.sort()
        return array







