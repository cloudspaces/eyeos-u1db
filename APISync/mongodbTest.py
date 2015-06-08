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
    def test_getComments_called_idAndCloud_returnComment(self):
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
