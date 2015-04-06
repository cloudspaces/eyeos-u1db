#!/usr/bin/env python
# -*- coding: utf-8 -*-
__author__ = 'root'

import unittest
import u1db
import os
from Metadata import Metadata
import json
from settings import settings

class MetadataTest (unittest.TestCase):

    def setUp(self):
        self.sut = Metadata("test.u1db",{'oauth':{'token_key':'NKKN8XVZLP5X23X','token_secret':'59ZN54UEUD3ULRU','consumer_key':'keySebas','consumer_secret':'secretSebas'}},"test1.u1db")

    def tearDown(self):
        self.sut.db.close()
        os.remove("test.u1db")
        os.remove("test1.u1db")

    """
    method: insert
    when: called
    with: array
    should: insertCorrect
    """
    def test_insert_called_array_insertCorrect(self):
        array = self.getArrayInsert()
        self.sut.insert(array)
        self.sut.db.create_index("by-id", "id", "user_eyeos")
        results = self.sut.db.get_from_index("by-id", "32565632156","eyeID_EyeosUser_2")
        self.assertEquals(array[2],results[0].content)

    """
    method: select
    when: called
    with: id
    should: returnArray
    """
    def test_select_called_id_returnArray(self):
        settings[ 'NEW_CODE' ] = "false"
        array = self.getArrayInsert()
        self.sut.insert(array)
        data = self.sut.select({u'id': 9873615, u'user_eyeos': u'eyeID_EyeosUser_2', u'path': u'/'})
        data.sort()
        self.assertEquals(2,len(data))

    """
    method: select
    when: called
    with: idAndUserAndCloudAndPath
    should: returnArray
    """
    def test_select_called_idAndUserAndCloudAndPath_returnArray(self):
        array = self.getArrayInsert()
        self.sut.insert(array)
        data = self.sut.select({u'id': 9873615, u'user_eyeos': u'eyeID_EyeosUser_2', u'cloud': u'Stacksync', u'path': u'/'})
        data.sort()
        self.assertEquals(2,len(data))

    """
    method: update
    when: called
    with: arrayWithoutCloud
    should: updateCorrect
    """
    def test_update_called_arrayWithoutCloud_updateCorrect(self):
        settings[ 'NEW_CODE' ] = "false"
        array = self.getArrayInsert()
        update = self.getArrayUpdate()
        self.sut.insert(array)
        self.sut.update(update)
        self.sut.db.create_index("by-id", "id", "user_eyeos")
        files = self.sut.db.get_from_index("by-id", "32565632156", "eyeID_EyeosUser_2")
        results = []
        if len(files) > 0:
            for file in files:
                results.append(file.content)

        self.assertEquals(update[1],results[0])

    """
    method: update
    when: called
    with: arrayWithCloud
    should: updateCorrect
    """
    def test_update_called_arrayWithCloud_updateCorrect(self):
        array = self.getArrayInsert()
        update = self.getArrayUpdate()
        self.sut.insert(array)
        self.sut.update(update)
        self.sut.db.create_index("by-id", "id", "user_eyeos")
        files = self.sut.db.get_from_index("by-id", "32565632156", "eyeID_EyeosUser_2")
        results = []
        if len(files) > 0:
            for file in files:
                results.append(file.content)

        self.assertEquals(update[1], results[0])

    """
    method: delete
    when: called
    with: arrayWithoutCloud
    should: deleteCorrect
    """
    def test_delete_called_arrayWithoutCloud_deleteCorrect(self):
        settings[ 'NEW_CODE' ] = "false"
        array = self.getArrayInsert()
        self.sut.insert(array)
        list = self.getArrayDelete()
        self.sut.delete(list)
        self.sut.db.create_index("by-user", "user_eyeos")
        files = self.sut.db.get_from_index("by-user", "eyeID_EyeosUser_2")
        self.assertEquals(0,len(files))

    """
    method: delete
    when: called
    with: arrayWithCloud
    should: deleteCorrect
    """
    def test_delete_called_arrayWithCloud_deleteCorrect(self):
        array = self.getArrayInsert()
        self.sut.insert(array)
        list = self.getArrayDelete()
        self.sut.delete(list)
        self.sut.db.create_index("by-user", "user_eyeos", "cloud")
        files = self.sut.db.get_from_index("by-user", "eyeID_EyeosUser_2", "Stacksync")
        self.assertEquals(0, len(files))

    """
    method: getParent
    when: called
    with: path
    should: returnArray
    """
    def test_getParent_called_path_returnArray(self):
        settings[ 'NEW_CODE' ] = "false";
        array = self.getArrayParent()
        self.sut.insert(array)
        data = self.sut.getParent({u'path': u'/documents/', u'filename': u'clients', u'user_eyeos': u'eyeID_EyeosUser_2'})
        self.assertEquals(array[0],data[0])

    """
    method: getParent
    when: called
    with: cloudAndPathAndFilenameAndUser
    should: returnArray
    """
    def test_getParent_called_cloudAndPathAndFilenameAndUser_returnArray(self):
        array = self.getArrayParent()
        self.sut.insert(array)
        data = self.sut.getParent({u'cloud': u'Stacksync', u'path': u'/documents/', u'filename': u'clients', u'user_eyeos': u'eyeID_EyeosUser_2'})
        self.assertEquals(array[0], data[0])

    """
    method: deleteFolder
    when: called
    with: idFolder
    should: returnCorrect
    """
    def test_deleteFolder_called_idFolder_returnCorrect(self):
        settings[ 'NEW_CODE' ] = "false"
        array = self.getArrayDeleteFolder()
        self.sut.insert(array)
        self.sut.deleteFolder({u'id': 9873615, u'user_eyeos': u'eyeID_EyeosUser_2', u'path': u'/documents/'})
        docs = self.sut.db.get_all_docs()
        self.assertEquals(1, len(docs[1]))

    """
    method: deleteFolder
    when: called
    with: idFolderAndUserAndCloudAndPath
    should: returnCorrect
    """
    def test_deleteFolder_called_idFolderAndUserAndCloudAndPath_returnCorrect(self):
        array = self.getArrayDeleteFolder()
        self.sut.insert(array)
        self.sut.deleteFolder({u'id': 9873615, u'user_eyeos': u'eyeID_EyeosUser_2', u'cloud': u'Stacksync', u'path': u'/documents/'})
        docs = self.sut.db.get_all_docs()
        self.assertEquals(1, len(docs[1]))

    """
    method: deleteMetadataUser
    when: called
    with: user
    should: deleteCorrect
    """
    def test_deleteMetadataUser_called_user_deleteCorrect(self):
        array = self.getArrayInsert()
        self.sut.insert(array)
        arrayVersion = self.getArrayInsertVersionDeleteUser()
        for version in arrayVersion:
            self.sut.db2.create_doc_from_json(json.dumps(version))
        list = [{'user_eyeos' : 'eyeID_EyeosUser_2'}]
        self.sut.deleteMetadataUser(list)
        docs = self.sut.db.get_all_docs()
        result = len(docs[1])
        docs = self.sut.db2.get_all_docs()
        result += len(docs[1])
        self.assertEquals(0,result)

    """
    method: deleteMetadataUser
    when: called
    with: userAndCloud
    should: deleteCorrect
    """
    def test_deleteMetadataUser_called_userAndCloud_deleteCorrect(self):
        array = self.getArrayInsert()
        self.sut.insert(array)
        arrayVersion = self.getArrayInsertVersionDeleteUser()
        for version in arrayVersion:
            self.sut.db2.create_doc_from_json(json.dumps(version))
        list = [{'user_eyeos' : 'eyeID_EyeosUser_2', 'cloud' : 'Stacksync'}]
        self.sut.deleteMetadataUser(list)
        docs = self.sut.db.get_all_docs()
        result = len(docs[1])
        docs = self.sut.db2.get_all_docs()
        result += len(docs[1])
        self.assertEquals(2,result)

    """
    method: selectMetadataUser
    when: called
    with: user
    should: return Array
    """
    def test_selectMetadataUser_called_user_returnArray(self):
        array = self.getArrayInsert()
        self.sut.insert(array)
        files = self.sut.selectMetadataUser('eyeID_EyeosUser_2')
        files.sort()
        self.assertEquals(array,files)

    """
    method: renameMetadata
    when: called
    with: userAndIdAndPathAndName
    should: renameFolderCorrect
    """
    def test_renameMetadata_called_userAndIdAndPathAndName_renameFolderCorrect(self):
        array = self.getArrayInsertRename()
        self.sut.insert(array)
        expected = self.getArrayRenameFolder('/A 1/','A 1')
        self.sut.renameMetadata({u'cloud': u'Stacksync', u'user_eyeos':u'eyeID_EyeosUser_2',u'status': u'CHANGED', u'is_root': False, u'version': 2, u'filename': u'A 1', u'parent_id': u'null', u'server_modified': u'2013-03-08 10:36:41.997', u'path': u'/', u'client_modified': u'2013-03-08 10:36:41.997', u'id': u'9873615', u'user': u'eyeID_EyeosUser_2', u'is_root':False, u'is_folder':True})
        files = self.sut.db.get_all_docs()
        results = []
        for file in files[1]:
            results.append(file.content)
        results.sort()
        self.assertEquals(expected,results)

    """
    method: renameMetadata
    when: called
    with: userAndIdAndPathAndName
    should: renameFileCorrect
    """
    def test_renameMetadata_called_userAndIdAndPathAndName_renameFileCorrect(self):
        array = self.getArrayInsertRename()
        self.sut.insert(array)
        expected = self.getArrayRenameFile('B 1.txt')
        self.sut.renameMetadata({u'cloud': u'Stacksync', u'user_eyeos': u'eyeID_EyeosUser_2', u'filename': u'B 1.txt', u'path': u'/A/', u'id': u'32565632156', u'size': 775412, u'mimetype': u'application/pdf', u'status': u'CHANGED', u'version': 2, u'parent_id': u'9873615', u'user': u'eyeos', u'client_modified': u'2013-03-08 10:36:41.997', u'server_modified': u'2013-03-08 10:36:41.997', u'is_folder': False})
        files = self.sut.db.get_all_docs()
        results = []
        for file in files[1]:
            results.append(file.content)
        results.sort()
        self.assertEquals(expected,results)

    """
    method: insertDownloadVersion
    when: called
    with: metadata
    should: insertCorrect
    """
    def test_insertDownloadVersion_called_metadata_insertCorrect(self):
        expected = {u'id':u'12457988',u'user_eyeos':u'eyeID_EyeosUser_2',u'version': u'2', u'recover': False}
        self.sut.insertDownloadVersion(expected)
        results = ''
        files = self.sut.db.get_all_docs()
        if len(files[1]) > 0:
            results = files[1][0].content
        self.assertEquals(expected,results)

    """
    method: updateDownloadVersion
    when: called
    with: metadata
    should: updateCorrect
    """
    def test_updateDownloadVersion_called_metadata_updateCorrect(self):
        metadata = {u'id':u'12457988',u'user_eyeos':u'eyeID_EyeosUser_2',u'version': u'2', u'recover': False}
        expected = {u'id':u'12457988',u'user_eyeos':u'eyeID_EyeosUser_2',u'version': u'3', u'recover': True}
        self.sut.insertDownloadVersion(metadata)
        self.sut.updateDownloadVersion(expected)
        results = ''
        files = self.sut.db.get_all_docs()
        if len(files[1]) > 0:
            results = files[1][0].content
        self.assertEquals(expected,results)

    """
    method: deleteDownloadVersion
    when: called
    with: id
    should: deleteCorrect
    """
    def test_deleteDownloadVersion_called_id_deleteCorrect(self):
        metadata = {u'id':u'12457988',u'user_eyeos':u'eyeID_EyeosUser_2',u'version': u'2', u'recover': False}
        self.sut.insertDownloadVersion(metadata)
        self.sut.deleteDownloadVersion("12457988","eyeID_EyeosUser_2")
        files = self.sut.db.get_all_docs()
        self.assertEquals(0,len(files[1]))

    """
    method: getDownloadVersion
    when: called
    with: id
    should: returnMetadata
    """
    def test_getDownloadVersion_called_id_returnMetadata(self):
        metadata = {u'id':u'12457988',u'user_eyeos':u'eyeID_EyeosUser_2',u'version': u'2', u'recover': False}
        self.sut.insertDownloadVersion(metadata)
        result = self.sut.getDownloadVersion("12457988","eyeID_EyeosUser_2")
        self.assertEquals(metadata,result)

    """
    method: recursiveDeleteVersion
    when: called
    with: idAndUser
    should: deleteCorrect
    """
    def test_recursiveDeleteVersion_called_idAndUser_deleteCorrect(self):
        settings[ 'NEW_CODE' ] = "false"
        array = self.getArrayInsertVersionMetadata()
        self.sut.insert(array)
        arrayVersion = self.getArrayInsertVersion()
        for version in arrayVersion:
            self.sut.db2.create_doc_from_json(json.dumps(version))
        self.sut.recursiveDeleteVersion({u'id': 9873615, u'user_eyeos': u'eyeID_EyeosUser_2'})
        files = self.sut.db2.get_all_docs()
        self.assertEquals(0, len(files[1]))

    """
    method: recursiveDeleteVersion
    when: called
    with: idAndUserAndCloud
    should: deleteCorrect
    """
    def test_recursiveDeleteVersion_called_idAndUserAndCloud_deleteCorrect(self):
        array = self.getArrayInsertVersionMetadata()
        self.sut.insert(array)
        arrayVersion = self.getArrayInsertVersion()
        for version in arrayVersion:
            self.sut.db2.create_doc_from_json(json.dumps(version))
        self.sut.recursiveDeleteVersion({u'id': 9873615, u'user_eyeos': u'eyeID_EyeosUser_2', u'cloud': u'Stacksync'})
        files = self.sut.db2.get_all_docs()
        self.assertEquals(0, len(files[1]))


    def getArrayInsert(self):
        array = [{u'user_eyeos': u'eyeID_EyeosUser_2', u'cloud':u'Stacksync', u'status': u'NEW', u'is_root': False, u'version': 1, u'filename': u'clients', u'parent_id': u'null', u'server_modified': u'2013-03-08 10:36:41.997', u'path': u'/', u'client_modified': u'2013-03-08 10:36:41.997', u'id': u'9873615', u'user': u'eyeID_EyeosUser_2',u'is_folder':True},
                 {u'user_eyeos': u'eyeID_EyeosUser_2', u'cloud':u'Stacksync', u'filename': u'Client1.pdf', u'path': u'/clients/', u'id': u'32565632156', u'size': 775412, u'mimetype': u'application/pdf', u'status': u'NEW', u'version': 3, u'parent_id': u'9873615', u'user': u'eyeos', u'client_modified': u'2013-03-08 10:36:41.997', u'server_modified': u'2013-03-08 10:36:41.997', u'is_folder': False},
                 {u'user_eyeos': u'eyeID_EyeosUser_2', u'cloud':u'Nec', u'filename': u'Client1.pdf', u'path': u'/', u'id': u'32565632157', u'size': 775412, u'mimetype': u'application/pdf', u'status': u'NEW', u'version': 3, u'parent_id': u'null', u'user': u'eyeos', u'client_modified': u'2013-03-08 10:36:41.997', u'server_modified': u'2013-03-08 10:36:41.997', u'is_folder': False}]
        array.sort()
        return array


    def getArrayUpdate(self):
        if settings[ 'NEW_CODE' ] == "true":
            array = [{u'parent_old': u'9873615'},
                     {u'user_eyeos': u'eyeID_EyeosUser_2', u'cloud': u'Stacksync', u'filename': u'Client2.pdf', u'path': u'/clients/', u'id': u'32565632156', u'size': 775412, u'mimetype': u'application/pdf', u'status': u'CHANGED', u'version': 3, u'parent_id': u'9873615', u'user': u'eyeos', u'client_modified': u'2013-03-08 10:36:41.997', u'server_modified': u'2013-03-08 10:36:41.997', u'is_folder': False}]
        else:
            array = [{u'parent_old': u'9873615'},
                     {u'user_eyeos': u'eyeID_EyeosUser_2', u'filename': u'Client2.pdf', u'path': u'/clients/', u'id': u'32565632156', u'size': 775412, u'mimetype': u'application/pdf', u'status': u'CHANGED', u'version': 3, u'parent_id': u'9873615', u'user': u'eyeos', u'client_modified': u'2013-03-08 10:36:41.997', u'server_modified': u'2013-03-08 10:36:41.997', u'is_folder': False}]
        return array

    def getArrayDelete(self):
        if settings[ 'NEW_CODE' ] == "true":
            array = [{u'id': u'9873615', u'user_eyeos': u'eyeID_EyeosUser_2', u'cloud':u'Stacksync', u'parent_id':u'null'},
                     {u'id': u'32565632156', u'user_eyeos': u'eyeID_EyeosUser_2', u'cloud':u'Stacksync', u'parent_id':u'9873615'},
                     {u'id': u'32565632157', u'user_eyeos': u'eyeID_EyeosUser_2', u'cloud':u'Nec', u'parent_id':u'null'}]
        else:
            array = [{u'id': u'9873615', u'user_eyeos': u'eyeID_EyeosUser_2', u'parent_id':u'null'},
                     {u'id': u'32565632156', u'user_eyeos': u'eyeID_EyeosUser_2', u'parent_id':u'9873615'},
                     {u'id': u'32565632157', u'user_eyeos': u'eyeID_EyeosUser_2', u'parent_id':u'null'}]
        array.sort()
        return array

    def getArrayParent(self):
        array = [{u'user_eyeos': u'eyeID_EyeosUser_2', u'cloud': u'Stacksync', u'status': u'CHANGED', u'is_root': False, u'version': 1, u'filename':u'clients', u'parent_id': u'null', u'server_modified': u'2013-03-08 10:36:41.997', u'path': u'/documents/', u'client_modified': u'2013-03-08 10:36:41.997', u'id': u'9873615', u'user': u'eyeID_EyeosUser_2', u'is_folder': True},
                 {u'user_eyeos': u'eyeID_EyeosUser_2', u'cloud': u'Stacksync', u'filename': u'Client1.pdf', u'path': u'/documents/clients/', u'id': u'32565632156', u'size': 775412, u'mimetype': u'application/pdf', u'status': u'CHANGED', u'version': 3, u'parent_id': u'null', u'user': u'eyeos', u'client_modified': u'2013-03-08 10:36:41.997', u'server_modified': u'2013-03-08 10:36:41.997', u'is_folder': False}]
        array.sort()
        return array

    def getArrayDeleteFolder(self):
        array = [{u'user_eyeos':u'eyeID_EyeosUser_2', u'cloud':u'Stacksync', u'status': u'CHANGED', u'is_root': False, u'version': 1, u'filename':u'clients', u'parent_id': u'474411411', u'server_modified': u'2013-03-08 10:36:41.997', u'path': u'/documents/', u'client_modified': u'2013-03-08 10:36:41.997', u'id': u'9873615', u'user': u'eyeID_EyeosUser_2',u'is_folder':True},
                 {u'user_eyeos':u'eyeID_EyeosUser_2', u'cloud':u'Stacksync', u'filename':u'Client1.pdf',u'path':u'/documents/clients/',u'id':u'32565632156',u'size':775412,u'mimetype':u'application/pdf',u'status':u'CHANGED',u'version':3,u'parent_id':u'9873615',u'user':u'eyeos',u'client_modified':u'2013-03-08 10:36:41.997',u'server_modified':u'2013-03-08 10:36:41.997',u'is_folder':False},
                 {u'user_eyeos':u'eyeID_EyeosUser_2', u'cloud':u'Nec', u'status': u'CHANGED', u'is_root': False, u'version': 1, u'filename':u'datos', u'parent_id': u'474411411', u'server_modified': u'2013-03-08 10:36:41.997', u'path': u'/documents/', u'client_modified': u'2013-03-08 10:36:41.997', u'id': u'1478526', u'user': u'eyeID_EyeosUser_2',u'is_folder':True}]
        array.sort()
        return array

    def getArrayInsertRename(self):
        array = [{u'cloud': u'Stacksync', u'user_eyeos': u'eyeID_EyeosUser_2', u'status': u'NEW', u'is_root': False, u'version': 1, u'filename': u'A', u'parent_id': u'null', u'server_modified': u'2013-03-08 10:36:41.997', u'path': u'/', u'client_modified': u'2013-03-08 10:36:41.997', u'id': u'9873615', u'user': u'eyeID_EyeosUser_2', u'is_root':False, u'is_folder':True},
                 {u'cloud': u'Stacksync', u'user_eyeos': u'eyeID_EyeosUser_2', u'filename': u'B.txt', u'path': u'/A/', u'id': u'32565632156', u'size': 775412, u'mimetype': u'application/pdf', u'status': u'NEW', u'version': 1, u'parent_id': u'9873615', u'user': u'eyeos', u'client_modified': u'2013-03-08 10:36:41.997', u'server_modified': u'2013-03-08 10:36:41.997', u'is_folder': False},
                 {u'cloud': u'Stacksync', u'user_eyeos': u'eyeID_EyeosUser_2', u'filename': u'D.txt', u'path': u'/A/', u'id': u'444441714', u'size': 775412, u'mimetype': u'application/pdf', u'status': u'NEW', u'version': 1, u'parent_id': u'9873615', u'user': u'eyeos', u'client_modified': u'2013-03-08 10:36:41.997', u'server_modified': u'2013-03-08 10:36:41.997', u'is_folder': False},
                 {u'cloud': u'Stacksync', u'user_eyeos': u'eyeID_EyeosUser_2', u'filename': u'C', u'path': u'/A/', u'id': u'32565632157', u'size': 775412, u'mimetype': u'application/pdf', u'status': u'NEW', u'version': 1, u'parent_id': u'9873615', u'user': u'eyeos', u'client_modified': u'2013-03-08 10:36:41.997', u'server_modified': u'2013-03-08 10:36:41.997', u'is_root': False, u'is_folder': True},
                 {u'cloud': u'Stacksync', u'user_eyeos': u'eyeID_EyeosUser_2', u'filename': u'E.txt', u'path': u'/A/C/', u'id': u'4415512', u'size': 775412, u'mimetype': u'application/pdf', u'status': u'NEW', u'version': 1, u'parent_id': u'32565632157', u'user': u'eyeos', u'client_modified': u'2013-03-08 10:36:41.997', u'server_modified': u'2013-03-08 10:36:41.997', u'is_root': False, u'is_folder': False}]
        array.sort()
        return array

    def getArrayRenameFolder(self, path, foldername):
        array = [{u'cloud': u'Stacksync', u'user_eyeos': u'eyeID_EyeosUser_2', u'status': u'CHANGED', u'is_root': False, u'version': 2, u'filename': u'' + foldername + '', u'parent_id': u'null', u'server_modified': u'2013-03-08 10:36:41.997', u'path': u'/', u'client_modified': u'2013-03-08 10:36:41.997', u'id': u'9873615', u'user': u'eyeID_EyeosUser_2', u'is_root':False, u'is_folder':True},
                 {u'cloud': u'Stacksync', u'user_eyeos': u'eyeID_EyeosUser_2', u'filename': u'B.txt', u'path': u'' + path + '', u'id': u'32565632156', u'size': 775412, u'mimetype': u'application/pdf', u'status': u'NEW', u'version': 1,u'parent_id': u'9873615', u'user': u'eyeos', u'client_modified': u'2013-03-08 10:36:41.997', u'server_modified': u'2013-03-08 10:36:41.997', u'is_folder': False},
                 {u'cloud': u'Stacksync', u'user_eyeos': u'eyeID_EyeosUser_2', u'filename': u'D.txt', u'path': u'' + path + '', u'id': u'444441714', u'size': 775412, u'mimetype': u'application/pdf', u'status': u'NEW', u'version': 1,u'parent_id': u'9873615', u'user': u'eyeos', u'client_modified': u'2013-03-08 10:36:41.997', u'server_modified': u'2013-03-08 10:36:41.997', u'is_folder': False},
                 {u'cloud': u'Stacksync', u'user_eyeos': u'eyeID_EyeosUser_2', u'filename': u'C', u'path': u'' + path + '', u'id': u'32565632157', u'size': 775412, u'mimetype': u'application/pdf', u'status': u'NEW', u'version': 1,u'parent_id': u'9873615', u'user': u'eyeos', u'client_modified': u'2013-03-08 10:36:41.997', u'server_modified': u'2013-03-08 10:36:41.997', u'is_root': False, u'is_folder': True},
                 {u'cloud': u'Stacksync', u'user_eyeos': u'eyeID_EyeosUser_2', u'filename': u'E.txt', u'path': u'' + path + 'C/', u'id': u'4415512', u'size': 775412, u'mimetype': u'application/pdf', u'status': u'NEW', u'version': 1,u'parent_id': u'32565632157', u'user': u'eyeos', u'client_modified': u'2013-03-08 10:36:41.997', u'server_modified': u'2013-03-08 10:36:41.997', u'is_root': False, u'is_folder': False}]
        array.sort()
        return array

    def getArrayRenameFile(self, filename):
        array = [{u'cloud': u'Stacksync', u'user_eyeos': u'eyeID_EyeosUser_2', u'status': u'NEW', u'is_root': False, u'version': 1, u'filename': u'A', u'parent_id': u'null', u'server_modified': u'2013-03-08 10:36:41.997', u'path': u'/', u'client_modified': u'2013-03-08 10:36:41.997', u'id': u'9873615', u'user': u'eyeID_EyeosUser_2', u'is_root':False, u'is_folder':True},
                 {u'cloud': u'Stacksync', u'user_eyeos': u'eyeID_EyeosUser_2', u'filename': u'' + filename + '', u'path': u'/A/', u'id': u'32565632156', u'size': 775412, u'mimetype': u'application/pdf', u'status': u'CHANGED', u'version': 2, u'parent_id': u'9873615', u'user': u'eyeos', u'client_modified': u'2013-03-08 10:36:41.997', u'server_modified': u'2013-03-08 10:36:41.997', u'is_folder': False},
                 {u'cloud': u'Stacksync', u'user_eyeos': u'eyeID_EyeosUser_2', u'filename': u'D.txt', u'path': u'/A/', u'id': u'444441714', u'size': 775412, u'mimetype': u'application/pdf', u'status': u'NEW', u'version': 1, u'parent_id': u'9873615', u'user':u'eyeos', u'client_modified': u'2013-03-08 10:36:41.997', u'server_modified': u'2013-03-08 10:36:41.997', u'is_folder': False},
                 {u'cloud': u'Stacksync', u'user_eyeos': u'eyeID_EyeosUser_2', u'filename': u'C', u'path':u'/A/', u'id': u'32565632157', u'size': 775412, u'mimetype': u'application/pdf', u'status': u'NEW', u'version': 1, u'parent_id': u'9873615', u'user': u'eyeos', u'client_modified': u'2013-03-08 10:36:41.997', u'server_modified': u'2013-03-08 10:36:41.997', u'is_root': False, u'is_folder': True},
                 {u'cloud': u'Stacksync', u'user_eyeos': u'eyeID_EyeosUser_2', u'filename': u'E.txt', u'path': u'/A/C/', u'id': u'4415512', u'size': 775412, u'mimetype': u'application/pdf', u'status': u'NEW', u'version': 1, u'parent_id': u'32565632157', u'user': u'eyeos', u'client_modified': u'2013-03-08 10:36:41.997', u'server_modified': u'2013-03-08 10:36:41.997', u'is_root': False, u'is_folder': False}]
        array.sort()
        return array

    def getArrayInsertVersionMetadata(self):
        array = [{u'user_eyeos':u'eyeID_EyeosUser_2',u'cloud':u'Stacksync',u'status': u'NEW', u'is_root': False, u'version': 1, u'filename': u'clients', u'parent_id': u'null', u'server_modified': u'2013-03-08 10:36:41.997', u'path': u'/', u'client_modified': u'2013-03-08 10:36:41.997', u'id': u'9873615', u'user': u'eyeID_EyeosUser_2',u'is_folder':True},
                 {u'user_eyeos':u'eyeID_EyeosUser_2',u'cloud':u'Stacksync',u'filename':u'Client1.pdf',u'path':u'/clients/',u'id':u'32565632156',u'size':775412,u'mimetype':u'application/pdf',u'status':u'NEW',u'version':3,u'parent_id':u'9873615',u'user':u'eyeos',u'client_modified':u'2013-03-08 10:36:41.997',u'server_modified':u'2013-03-08 10:36:41.997',u'is_folder':False},
                 {u'user_eyeos':u'eyeID_EyeosUser_2',u'cloud':u'Stacksync',u'filename':u'B',u'path':u'/',u'id':u'11111',u'size':0,u'mimetype':u'application/pdf',u'status':u'NEW',u'version':2,u'parent_id':u'9873615',u'user':u'eyeos',u'client_modified':u'2013-03-08 10:36:41.997',u'server_modified':u'2013-03-08 10:36:41.997',u'is_folder':True},
                 {u'user_eyeos':u'eyeID_EyeosUser_2',u'cloud':u'Stacksync',u'filename':u'c.pdf',u'path':u'/',u'id':u'222333',u'size':775412,u'mimetype':u'application/pdf',u'status':u'NEW',u'version':3,u'parent_id':u'11111',u'user':u'eyeos',u'client_modified':u'2013-03-08 10:36:41.997',u'server_modified':u'2013-03-08 10:36:41.997',u'is_folder':False},
                 {u'user_eyeos':u'eyeID_EyeosUser_2',u'cloud':u'Stacksync',u'filename':u'Client1.pdf',u'path':u'/',u'id':u'32565632157',u'size':775412,u'mimetype':u'application/pdf',u'status':u'NEW',u'version':3,u'parent_id':u'null',u'user':u'eyeos',u'client_modified':u'2013-03-08 10:36:41.997',u'server_modified':u'2013-03-08 10:36:41.997',u'is_folder':False}]
        array.sort()
        return array

    def getArrayInsertVersion(self):
        if settings[ 'NEW_CODE' ] == "true":
            array = [{u'id': u'32565632156', u'user_eyeos': u'eyeID_EyeosUser_2', u'cloud': u'Stacksync', u'version': 2, u'recover': False},
                     {u'id': u'222333', u'user_eyeos': u'eyeID_EyeosUser_2', u'cloud': u'Stacksync', u'version': 2, u'recover': False}]
        else:
            array = [{u'id': u'32565632156', u'user_eyeos': u'eyeID_EyeosUser_2', u'version': 2, u'recover': False},
                     {u'id': u'222333', u'user_eyeos': u'eyeID_EyeosUser_2', u'version': 2, u'recover': False}]
        array.sort()
        return array

    def getArrayInsertVersionDeleteUser(self):
        array = [{u'id':u'9873615',u'user_eyeos':u'eyeID_EyeosUser_2',u'cloud':u'Stacksync',u'version':2,u'recover':False},
         {u'id':u'32565632156',u'user_eyeos':u'eyeID_EyeosUser_2',u'cloud':u'Stacksync',u'version':2,u'recover':False},
         {u'id':u'32565632157',u'user_eyeos':u'eyeID_EyeosUser_2',u'cloud':u'Nec',u'version':2,u'recover':False}]
        return array

    """
    ##################################################################################################################################################
                                                                    TEST CALENDAR
    ##################################################################################################################################################
    """

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
    method: insertCalendar
    when: called
    with: array
    should: updateCorrect
    """
    def test_insertCalendar_called_array_updateCorrect(self):
        array =[{u'type':u'calendar',u'user_eyeos':u'eyeos',u'name':u'school',u'status':u'DELETED',u'description':u'school calendar',u'timezone':0}]
        self.sut.insertCalendar(array)
        array2 = [{u'type':u'calendar',u'user_eyeos':u'eyeos',u'name':u'school',u'status':u'NEW',u'description':u'school calendar',u'timezone':0}]
        self.sut.insertCalendar(array2)
        files = self.sut.db.get_all_docs()
        results = []
        for file in files[1]:
            results.append(file.content)
        results.sort()
        self.assertEquals(array2,results)


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