__author__ = 'root'

import unittest
from mock import Mock
from Protocol import Protocol
import json
import u1db
import os

class ProtocolTest (unittest.TestCase):
    def setUp(self):
        self.sut = ''
        self.db =  u1db.open("test.u1db", create=True)

    def tearDown(self):
        self.db.close()
        os.remove("test.u1db")


    """
    method: protocol
    when: called
    with: typeInsertAndList
    should: insertCorrect
    """
    def test_protocol_called_typeInsertAndList_insertCorrect(self):
        params = '{"type":"insert","lista":[{"file_id":"null","parent_file_id":"null","filename":"root","path":null,"is_folder":true,"status":null,"user":null,"version":null,"checksum":null,"size":null,"mimetype":null,"is_root":true},{"file_id":-7755273878059615652,"parent_file_id":"null","filename":"helpFile","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-11 15:40:45.784","client_modified":"2013-11-11 15:40:45.784","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false}]}'
        aux = json.loads(params)
        protocol = Protocol(self.db)
        protocol.insert = Mock()
        protocol.insert.return_value = True
        result = protocol.protocol(params)
        protocol.insert.assert_called_once_with(aux['lista'])
        self.assertEquals('true',result)


    """
    method: protocol
    when: called
    with: typeSelectAndList
    should: returnArray
    """
    def test_protocol_called_typeSelectAndList_returnArray(self):
        params = '{"type":"select","lista":[{"file_id":"124568"}]}'
        protocol = Protocol(self.db)
        protocol.select = Mock()
        protocol.select.return_value = []
        result = protocol.protocol(params)
        protocol.select.assert_called_once_with("124568")
        self.assertEquals('[]',result)

    """
    method: protocol
    when: called
    with: typeUpdateAndList
    should: updateCorrect
    """
    def test_protocol_called_typeUpdateAndList_updateCorrect(self):
        params = '{"type":"update","lista":[{"file_id":"null","parent_file_id":"null","filename":"root","path":null,"is_folder":true,"status":null,"user":null,"version":null,"checksum":null,"size":null,"mimetype":null,"is_root":true},{"file_id":-7755273878059615652,"parent_file_id":"null","filename":"helpFile","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-11 15:40:45.784","client_modified":"2013-11-11 15:40:45.784","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false}]}'
        aux = json.loads(params)
        protocol = Protocol(self.db)
        protocol.update = Mock()
        protocol.update.return_value = True
        result = protocol.protocol(params)
        protocol.update.assert_called_once_with(aux['lista'])
        self.assertEquals('true',result)

    """
    method: protocol
    when: called
    with: typeDeleteAndList
    should: deleteCorrect
    """
    def test_protocol_called_typeDeleteAndList_deleteCorrect(self):
        params = '{"type":"delete","lista":[{"file_id":1234},{"file_id":8907}]}'
        aux = json.loads(params)
        protocol = Protocol(self.db)
        protocol.delete = Mock()
        protocol.delete.return_value = True
        result = protocol.protocol(params)
        protocol.delete.assert_called_once_with(aux['lista'])
        self.assertEquals('true',result)


    """
    method: protocol
    when: called
    with: typeGetParentAndPath
    should: returnArray
    """
    def test_protocol_called_typeSelectAndList_returnArray(self):
        params = '{"type":"parent","lista":[{"path":"/Documents/prueba/","folder":"hola"}]}'
        protocol = Protocol(self.db)
        protocol.getParent = Mock()
        protocol.getParent.return_value = []
        result = protocol.protocol(params)
        protocol.getParent.assert_called_once_with("/Documents/prueba/","hola")
        self.assertEquals('[]',result)
