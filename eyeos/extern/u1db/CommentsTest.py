__author__ = 'root'

import unittest
import u1db
import os
from Comments import Comments
import json

class CommentsTest (unittest.TestCase):
    def setUp(self):
        self.sut = Comments("test.u1db",{'oauth':{'token_key':'NKKN8XVZLP5X23X','token_secret':'59ZN54UEUD3ULRU','consumer_key':'keySebas','consumer_secret':'secretSebas'}})

    def tearDown(self):
        self.sut.db.close()
        os.remove("test.u1db")

    """
    method: createComment
    when: called
    with: IdAndUserTextAndTimeCreated
    should: insertCorrect
    """
    def test_createComment_called_idAndUserAndTextAndTimeCreated_insertCorrect(self):
        comment = self.insertOneComment()
        files = self.sut.db.get_all_docs()
        result = ""
        if len(files) > 0:
            result = files[1][0].content
        self.assertEquals(comment,result)

    """
    method: deleteComment
    when: called
    with:idAndUserAndTimeCreated
    should: deleteCorrect
    """
    def test_deleteComment_called_idAndUserAndTimeCreated_deleteCorrect(self):
        comment = self.insertOneComment()
        self.sut.deleteComment(comment['id'],comment['user'],comment['time_created'])
        files = self.sut.db.get_all_docs()
        self.assertEquals(0,len(files[1]))

    """
    method: getComments
    when: called
    with: id
    should: returnArray
    """
    def test_getComments_called_id_returnArray(self):
        list = self.insertSeveralComments()
        list.sort()
        result = json.loads(self.sut.getComments(list[0]['id']))
        result.sort()
        self.assertEquals(list,result)

    def insertSeveralComments(self):
        data = []
        data.append(self.exerciseInsertComment("93509385","eyeos","Por favor, modificar la descripcion del punto 1.1","201406201548"))
        data.append(self.exerciseInsertComment("93509385","stacksync","Por favor, modificar la descripcion del punto 1.2","201406251200"))
        return data

    def insertOneComment(self):
        return self.exerciseInsertComment("93509385","eyeos","Por favor, modificar la descripcion del punto 1.1","201406201548")

    def exerciseInsertComment(self,id,user,text,time_created):
        comment = {u'id':u'' + id + '',u'user':u'' + user + '',u'time_created':u'' + time_created + '',u'status':u'NEW',u'text':u'' + text + ''}
        self.sut.createComment(id,user,text,time_created)
        return comment





