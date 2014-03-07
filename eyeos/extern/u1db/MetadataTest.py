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
        self.db.create_index("by-fileid", "file_id")
        results = self.db.get_from_index("by-fileid", "-7755273878059615652")
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
        data = self.sut.select("null")
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
        self.db.create_index("by-fileid", "file_id")
        files = self.db.get_from_index("by-fileid",'*')

        results = []
        for file in files:
            results.append(file.content)

        results.sort()
        self.assertEquals(update,results)


    """
    method: update
    when: called
    with: array
    should: deleteCorrect
    """
    def test_delete_called_array_deleteCorrect(self):
        array = self.getArrayInsert()
        self.sut.insert(array)
        list = self.getArrayDelete()
        self.sut.delete(list)
        self.db.create_index("by-fileid", "file_id")
        files = self.db.get_from_index("by-fileid",'*')
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
        data = self.sut.getParent('/Documents/prueba/',"hola")
        data.sort()
        self.assertEquals(array[2],data[0])

    def getArrayInsert(self):
        array = [{u'status': None, u'mimetype': None, u'parent_file_id': u'null', u'checksum': None, u'filename': u'Root', u'is_root': True, u'version': None, u'file_id': u'null', u'is_folder': True, u'path': None, u'size': None, u'user': None},
                 {u'status': u'NEW', u'mimetype': u'inode/directory', u'parent_file_id': u'null', u'server_modified': u'2013-11-11 15:40:45.784', u'checksum': 0, u'client_modified': u'2013-11-11 15:40:45.784', u'filename': u'helpFolder', u'is_root': False, u'version': 1, u'file_id': -7755273878059615652, u'is_folder': True, u'path': u'/', u'size': 0, u'user': u'web'}]
        array.sort()
        return array

    def getArrayUpdate(self):
        array = [{u'status': None, u'mimetype': None, u'parent_file_id': u'null', u'checksum': None, u'filename': u'Documents', u'is_root': True, u'version': None, u'file_id': u'null', u'is_folder': True, u'path': None, u'size': None, u'user': None},
                 {u'status': u'NEW', u'mimetype': u'inode/directory', u'parent_file_id': u'null', u'server_modified': u'2013-11-11 15:40:45.784', u'checksum': 0, u'client_modified': u'2013-11-11 15:40:45.784', u'filename': u'helpFile', u'is_root': False, u'version': 1, u'file_id': -7755273878059615652, u'is_folder': False, u'path': u'/', u'size': 0, u'user': u'web'}]
        array.sort()
        return array

    def getArrayDelete(self):
        array = [{u'file_id': u'null'},
                 {u'file_id': -7755273878059615652}]
        array.sort()
        return array

    def getArrayParent(self):
        array = [{u'status': None, u'mimetype': None, u'parent_file_id': u'null', u'checksum': None, u'filename': u'Documents', u'is_root': True, u'version': None, u'file_id': u'754050', u'is_folder': True, u'path': u'/', u'size': None, u'user': None},
                 {u'status': None, u'mimetype': None, u'parent_file_id': u'754050', u'checksum': None, u'filename': u'prueba', u'is_root': True, u'version': None, u'file_id': u'123456', u'is_folder': True, u'path': u'/Documents/', u'size': None, u'user': None},
                 {u'status': None, u'mimetype': None, u'parent_file_id': u'123456', u'checksum': None, u'filename': u'hola', u'is_root': True, u'version': None, u'file_id': u'77777', u'is_folder': True, u'path': u'/Documents/prueba/', u'size': None, u'user': None},
                 {u'status': None, u'mimetype': None, u'parent_file_id': u'123456', u'checksum': None, u'filename': u'pepe', u'is_root': True, u'version': None, u'file_id': u'88888', u'is_folder': True, u'path': u'/Documents/prueba/', u'size': None, u'user': None}]
        return array




