__author__ = 'root'

import unittest
from OauthCredentials import OauthCredentials
from mock import Mock
from requests_oauthlib import OAuth1Session
import os
import urllib
import json
from settings import settings

class OauthCredentialsTest (unittest.TestCase):
    def setUp(self):
        self.key = "eyeos"
        self.secret = "eyeosSecret"
        self.callbackurl = 'http://sebasvm.eyeos.com/index.php'
        self.requesttokenurl = "http://eyeos/request_token"
        self.accesstokenurl =  "http://eyeos/access_token"
        self.resourceurl = "http://eyeos/"
        self.version = 'v2'
        self.oauthCredentials = OauthCredentials(self.requesttokenurl,self.accesstokenurl,self.resourceurl,self.version)
        self.file = open('prueba.txt', 'w+')
        self.user = "eyeos"
        self.calendar = "personal"
        self.isallday = 0
        self.timestart = "201419160000"
        self.timeend = "201419170000"
        self.repetition = "None"
        self.finaltype = "1"
        self.finalvalue = "0"
        self.subject = "VisitaMedico"
        self.location = "Barcelona"
        self.description= "Llevar justificante"
        self.timezone = 0
        self.cloud = 'Stacksync'
        self.repeattype = "n"
        self.idFile = "2150"
        self.ipserver = "192.168.56.101"
        self.datetime = "2015-05-12 10:50:00"
        self.timelimit = 10

    def tearDown(self):
        self.oauthCredentials = None
        self.file.close()
        os.remove("prueba.txt")

    """
    method: getRequestToken
    when: called
    with: consumerAndCallback
    should: returnToken
    """
    def test_getRequestToken_called_consumerAndCallback_returnToken(self):
        expected = '{"secret": "LMN", "key": "HIJK"}'
        oauth = OAuth1Session(self.key, client_secret=self.secret,callback_uri=self.callbackurl)
        oauth.fetch_request_token = Mock()
        oauth.fetch_request_token.return_value = {"oauth_token":"HIJK","oauth_token_secret":"LMN"}
        result = self.oauthCredentials.getRequestToken(oauth)
        oauth.fetch_request_token.assert_called_once_with(self.requesttokenurl)
        self.assertEquals(expected,result)

    """
    method: getAccessToken
    when: called
    with: consumerAndRequestTokenAndVerifier
    should: returnToken
    """
    def test_getAccessToken_called_consumerAndRequestTokenAndVerifier_returnToken(self):
        expected = '{"secret": "STVM", "key": "MNOP"}'
        oauth = OAuth1Session(self.key, client_secret=self.secret,resource_owner_key="ABCD",resource_owner_secret="EFG",verifier='verifier')
        oauth.fetch_access_token = Mock()
        oauth.fetch_access_token.return_value = {"oauth_token":"MNOP","oauth_token_secret":"STVM"}
        result = self.oauthCredentials.getAccessToken(oauth)
        oauth.fetch_access_token.assert_called_once_with(self.accesstokenurl)
        self.assertEquals(expected,result)


    """
    method: getMetadata
    when: called
    with: accessTokenAndIsFileAndFileId
    should: returnJsonMetadata
    """
    def test_getMetadata_called_accessTokenAndIsFileAndId_returnJsonMetadata(self):
        fileId = 32565632156;
        metadataIn = {"filename":"Client1.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent_id":None,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":False}
        metadataOut = '{"status": "DELETED", "is_folder": false, "user": "eyeos", "server_modified": "2013-03-08 10:36:41.997", "id": 32565632156, "size": 775412, "mimetype": "application/pdf", "client_modified": "2013-03-08 10:36:41.997", "filename": "Client1.pdf", "parent_id": "null", "version": 3}'
        self.exerciseMetadata(metadataIn,metadataOut,True,fileId)

    """
    method: getMetadata
    when: called
    with: accessTokenAndIsFolderAndId
    should: returnJsonMetadata
    """
    def test_getMetadata_called_accessTokenAndIsFolderAndId_returnJsonMetadata(self):
        folderId = 9873615
        metadataIn = {"filename":"clients","id":9873615,"status":"NEW","version":1,"parent_id":None,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":False,"is_folder":True}
        metadataOut = '{"status": "NEW", "parent_id": "null", "version": 1, "user": "eyeos", "server_modified": "2013-03-08 10:36:41.997", "client_modified": "2013-03-08 10:36:41.997", "is_root": false, "id": 9873615, "is_folder": true, "filename": "clients"}'
        self.exerciseMetadata(metadataIn,metadataOut,False,folderId)

    """
    method: getMetadata
    when: called
    with: accessTokenAndIsFolderAndIdAndContents
    should: returnJsonMetadata
    """
    def test_getMetadata_called_accessTokenAndIsFolderAndIdAndContents_returnJsonMetadata(self):
        folderId = 9873615
        metadataIn = {"filename":"clients","id":9873615,"status":"NEW","version":1,"parent_id":None,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":True,"contents":[{"filename":"Client1.pdf","path":"/documents/clients/Client1.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent_id":-348534824681,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":False,"is_folder":False}]}
        metadataOut = '{"status": "NEW", "parent_id": "null", "version": 1, "user": "eyeos", "server_modified": "2013-03-08 10:36:41.997", "contents": [{"status": "DELETED", "is_folder": false, "is_root": false, "user": "eyeos", "server_modified": "2013-03-08 10:36:41.997", "path": "/documents/clients/Client1.pdf", "id": 32565632156, "size": 775412, "mimetype": "application/pdf", "client_modified": "2013-03-08 10:36:41.997", "filename": "Client1.pdf", "parent_id": -348534824681, "version": 3}], "client_modified": "2013-03-08 10:36:41.997", "id": 9873615, "is_folder": true, "filename": "clients"}'
        self.exerciseMetadata(metadataIn,metadataOut,False,folderId,"/contents")

    """
    method: getMetadata
    when: called
    with: accessTokenAndIsFolderAndId
    should: returnException
    """
    def test_getMetadata_called_accessTokenAndIsFolderAndId_returnException(self):
        folderId = -1
        metadataIn =  {"error":404, "description": "File or folder not found."}
        metadataOut = 'false'
        self.exerciseMetadata(metadataIn,metadataOut,False,folderId,"/contents")

    """
   method: getMetadata
   when: called
   with: accessTokenAndIsFolderAndId
   should: returnPermissionDenied
   """
    def test_getMetadata_called_accessTokenAndIsFolderAndId_returnPermissionDenied(self):
        folderId = 9873615
        metadataIn =  {"error":403, "description": "Forbidden. The requester does not have permission to access the specified resource"}
        metadataOut = 403
        self.exerciseMetadata(metadataIn,metadataOut,False,folderId,"/contents")

    """
    method: accessTokenAndFileAndNameAndParent
    when: called
    with: accessTokenAndIsFileAndIdAndNameAndParent
    should: returnJsonMetadataRename
    """
    def test_updateMetadata_called_accessTokenAndIsFileAndIdAndNameAndParent_returnJsonMetadataRename(self):
        fileId = 32565632156;
        parentId = 123456
        name = "prueba.pdf"
        data = {}
        data['name'] = name
        data['parent'] = parentId
        metadataIn = {"filename":"prueba.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":3,"parent_id":123456,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":False}
        metadataOut = '{"status": "CHANGED", "is_folder": false, "user": "eyeos", "server_modified": "2013-03-08 10:36:41.997", "id": 32565632156, "size": 775412, "mimetype": "application/pdf", "client_modified": "2013-03-08 10:36:41.997", "filename": "prueba.pdf", "parent_id": 123456, "version": 3}'
        self.exerciseUpdateMetadata(metadataIn,metadataOut,True,fileId,name,parentId,data)

    """
    method: updateMetadata
    when: called
    with: accessTokenAndIsFileAndIdAndParent
    should: returnJsonMetadataMove
    """
    def test_updateMetadata_called_accessTokenAndIsFileAndIdAndParent_returnJsonMetadataMove(self):
        fileId = 32565632156;
        parentId = 789456
        data = {}
        data['parent'] = parentId
        metadataIn = {"filename":"Client1.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":3,"parent_id":789456,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":False}
        metadataOut = '{"status": "CHANGED", "is_folder": false, "user": "eyeos", "server_modified": "2013-03-08 10:36:41.997", "id": 32565632156, "size": 775412, "mimetype": "application/pdf", "client_modified": "2013-03-08 10:36:41.997", "filename": "Client1.pdf", "parent_id": 789456, "version": 3}'
        self.exerciseUpdateMetadata(metadataIn,metadataOut,True,fileId,None,parentId,data)

    """
    method: updateMetadata
    when: called
    with: accessTokenAndIsFileAndId
    should: returnJsonMetadataMove
    """
    def test_updateMetadata_called_accessTokenAndIsFileAndId_returnJsonMetadataMove(self):
        fileId = 32565632156;
        data = {}
        metadataIn = {"filename":"Client1.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":3,"parent_id":295830,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":False}
        metadataOut = '{"status": "CHANGED", "is_folder": false, "user": "eyeos", "server_modified": "2013-03-08 10:36:41.997", "id": 32565632156, "size": 775412, "mimetype": "application/pdf", "client_modified": "2013-03-08 10:36:41.997", "filename": "Client1.pdf", "parent_id": 295830, "version": 3}'
        self.exerciseUpdateMetadata(metadataIn,metadataOut,True,fileId,None,None,data)

    """
    method: updateMeta
    when: called
    with: accessTokenAndIsFolderAndIdNameAndParent
    should: returnJsonMetadataRename
    """
    def test_updateMetadata_called_accessTokenAndIsFolderAndIdAndNameAndParent_returnJsonMetadataRename(self):
        folderId = 9873615
        parentId = 32565632156
        name = "images"
        data = {}
        data['name'] = name
        data['parent'] = parentId
        metadataIn = {"filename":"images","id":9873615,"status":"NEW","version":1,"parent_id":None,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":False,"is_folder":True}
        metadataOut = '{"status": "NEW", "parent_id": "null", "version": 1, "user": "eyeos", "server_modified": "2013-03-08 10:36:41.997", "client_modified": "2013-03-08 10:36:41.997", "is_root": false, "id": 9873615, "is_folder": true, "filename": "images"}'
        self.exerciseUpdateMetadata(metadataIn,metadataOut,False,folderId,name,parentId,data)

    """
    method: updateMetadata
    when: called
    with: accessTokenAndIsFolderAndIdAndParent
    should: returnJsonMetadataMove
    """
    def test_updateMetadata_called_accessTokenAndIsFolderAndIdAndParent_returnJsonMetadataMove(self):
        folderId = 9873615
        parentId = 32565632156
        data = {}
        data['parent'] = parentId
        metadataIn = {"filename":"images","id":9873615,"status":"NEW","version":1,"parent_id":None,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":False,"is_folder":True}
        metadataOut = '{"status": "NEW", "parent_id": "null", "version": 1, "user": "eyeos", "server_modified": "2013-03-08 10:36:41.997", "client_modified": "2013-03-08 10:36:41.997", "is_root": false, "id": 9873615, "is_folder": true, "filename": "images"}'
        self.exerciseUpdateMetadata(metadataIn,metadataOut,False,folderId,None,parentId,data)

    """
    method: updateMetadata
    when: called
    with: accessTokenAndIsFolderAndId
    should: returnJsonMetadataRemove
    """
    def test_updateMetadata_called_accessTokenAndIsFolderAndId_returnJsonMetadataRemove(self):
        folderId = 9873615
        data = {}
        metadataIn = {"filename":"images","path":"/documents/images","id":9873615,"status":"NEW","version":1,"parent_id":None,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":False,"is_folder":True}
        metadataOut = '{"status": "NEW", "is_folder": true, "is_root": false, "user": "eyeos", "server_modified": "2013-03-08 10:36:41.997", "path": "/documents/images", "id": 9873615, "client_modified": "2013-03-08 10:36:41.997", "filename": "images", "parent_id": "null", "version": 1}'
        self.exerciseUpdateMetadata(metadataIn,metadataOut,False,folderId,None,None,data)

    """
    method: updateMetadata
    when: called
    with: accessTokenAndIsFolderAndId
    should: returnException
    """
    def test_updateMetadata_called_accessTokenAndIsFolderAndId_returnException(self):
        folderId = 9873615
        data = {}
        metadataIn =  {"error":404, "description": "File or folder not found."}
        metadataOut = 'false'
        self.exerciseUpdateMetadata(metadataIn,metadataOut,False,folderId,None,None,data)

    """
      method: updateMetadata
      when: called
      with: accessTokenAndIsFolderAndId
      should: returnPermissionDenied
      """
    def test_updateMetadata_called_accessTokenAndIsFolderAndId_returnPermissionDenied(self):
        folderId = 9873615
        data = {}
        metadataIn =  {"error":403, "description": "Forbidden. The requester does not have permission to access the specified resource."}
        metadataOut = 403
        self.exerciseUpdateMetadata(metadataIn,metadataOut,False,folderId,None,None,data)

    """
    method: createMetadata
    when: called
    with: accessTokenAndIsFileAndNameAndParentAndPath
    should: returnJsonMetadata
    """
    def test_createMetadata_called_accessTokenAndIsFileAndNameAndParentAndPath_returnJsonMetadata(self):
        name = "Client1~prueba.pdf"
        parent = 32565632156
        metadataIn = {"filename":"Client1.pdf","id":32565632156,"parent_id":-348534824681,"user":"eyeos"}
        metadataOut = '{"parent_id": -348534824681, "user": "eyeos", "id": 32565632156, "filename": "Client1.pdf"}'
        self.exerciseCreateMetadata(metadataIn,metadataOut,True,name,parent)

    """
    method: createMetadata
    when: called
    with: accessTokenAndIsFileAndName
    should: returnJsonMetadata
    """
    def test_createMetadata_called_accessTokenAndIsFileAndName_returnJsonMetadata(self):
        name = "Client1.pdf"
        metadataIn = {"filename":"Client1.pdf","id":32565632156,"parent_id":-348534824681,"user":"eyeos"}
        metadataOut = '{"parent_id": -348534824681, "user": "eyeos", "id": 32565632156, "filename": "Client1.pdf"}'
        self.exerciseCreateMetadata(metadataIn,metadataOut,True,name,None)


    """
    method: createMetadata
    when: called
    with: accessTokenAndIsFolderAndNameAndParent
    should: returnJsonMetadata
    """
    def test_createMetadata_called_accessTokenAndIsFolderAndNameAndParent_returnJsonMetadata(self):
        name = 'clients'
        parent = -348534824681
        metadataIn = {"filename":"clients","id":9873615,"parent_id":-348534824681,"user":"eyeos"}
        metadataOut = '{"parent_id": -348534824681, "user": "eyeos", "id": 9873615, "filename": "clients"}'
        self.exerciseCreateMetadata(metadataIn,metadataOut,False,name,parent)

    """
    method: createMetadata
    when: called
    with: accessTokenAndIsFolderAndName
    should: returnJsonMetadata
    """
    def test_createMetadata_called_accessTokenAndIsFolderAndName_returnJsonMetadata(self):
        name = 'clients'
        metadataIn = {"filename":"clients","id":9873615,"parent_id":None,"user":"eyeos"}
        metadataOut = '{"parent_id": "null", "user": "eyeos", "id": 9873615, "filename": "clients"}'
        self.exerciseCreateMetadata(metadataIn,metadataOut,False,name,None)

    """
    method: createMetadata
    when: called
    with: accessTokenAndIsFolderAndName
    should: returnException
    """
    def test_createMetadata_called_accessTokenAndIsFolderAndName_returnException(self):
        name = "prueba"
        metadataIn =  {"error":400, "description": "Bad input parameter"}
        metadataOut = 'false'
        self.exerciseCreateMetadata(metadataIn,metadataOut,False,name,None)

    """
   method: createMetadata
   when: called
   with: accessTokenAndIsFolderAndName
   should: returnPermissionDenied
   """
    def test_createMetadata_called_accessTokenAndIsFolderAndName_returnPermissionDenied(self):
        name = "prueba"
        metadataIn =  {"error":403, "description": "Forbidden. The requester does not have permission to access the specified resource."}
        metadataOut = 403
        self.exerciseCreateMetadata(metadataIn,metadataOut,False,name,None)

    """
    method: uploadFile
    when: called
    with: accessTokenAndIdAndPath
    should: returnCorrect
    """
    def test_uploadFile_called_accessTokenAndAndIdPath_returnCorrect(self):
        fileId = 123456
        self.exerciseUploadFile(fileId,"true",None)

    """
    method: uploadFile
    when: called
    with: accessTokenAndIdAndPath
    should: returnException
    """
    def test_uploadFile_called_accessTokenAndIdAndPath_returnException(self):
        fileId = -1
        self.exerciseUploadFile(fileId,"false",{"error":404, "description": "File or folder not found"})

    """
   method: uploadFile
   when: called
   with: accessTokenAndIdAndPath
   should: returnPermissionDenied
   """
    def test_uploadFile_called_accessTokenAndIdAndPath_returnPermissionDenied(self):
        fileId = -1
        self.exerciseUploadFile(fileId,403, {"error":403, "description": "Forbidden. The requester does not have permission to access the specified resource."})

    """
    method: downloadFile
    when: called
    with: accessTokenAndIdAndPath
    should: returnCorrect
    """
    def test_downloadFile_called_accessTokenAndIdAndPath_returnCorrect(self):
        fileId = 123456
        data = 'true'
        self.exerciseDonwloadFile(fileId,data,data)


    """
    method: downloadFile
    when: called
    with: accessTokenAndIdAndPath
    should: returnException
    """
    def test_downloadFile_called_accessTokenAndIdAndPath_returnException(self):
        fileId = -1
        self.exerciseDonwloadFile(fileId,'false',{"error":404, "description": "File or folder not found"})

        """
    method: downloadFile
    when: called
    with: accessTokenAndId
    should: returnPermissionDenied
    """
    def test_downloadFile_called_accessTokenAndId_returnPermissionDenied(self):
        fileId = -1
        self.exerciseDonwloadFile(fileId,403,{"error":403, "description": "Forbidden. The requester does not have permission to access the specified resource."})

    """
    method: deleteMetadata
    when: called
    with: accessTokenAndIsFileAndId
    should: returnJsonMetadata
    """
    def test_deleteMetadata_called_accessTokenAndIsFileAndId_returnJsonMetadata(self):
        fileId = 32565632156
        metadataIn = {"filename":"Client1.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent_id":-348534824681,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":False}
        metadataOut = '{"status": "DELETED", "is_folder": false, "user": "eyeos", "server_modified": "2013-03-08 10:36:41.997", "id": 32565632156, "size": 775412, "mimetype": "application/pdf", "client_modified": "2013-03-08 10:36:41.997", "filename": "Client1.pdf", "parent_id": -348534824681, "version": 3}'
        self.exerciseDeleteMetadata(metadataIn,metadataOut,True,fileId)

    """
    method: deleteMetadata
    when: called
    with: accessTokenAndIsFolderAndId
    should: returnJsonMetadata
    """
    def test_deleteMetadata_called_accessTokenAndIsFolderAndId_returnJsonMetadata(self):
        folderId = 9873615
        metadataIn = {"filename":"clients","id":9873615,"status":"DELETED","version":3,"parent_id":-348534824681,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":True}
        metadataOut = '{"status": "DELETED", "parent_id": -348534824681, "version": 3, "user": "eyeos", "server_modified": "2013-03-08 10:36:41.997", "client_modified": "2013-03-08 10:36:41.997", "id": 9873615, "is_folder": true, "filename": "clients"}'
        self.exerciseDeleteMetadata(metadataIn,metadataOut,False,folderId)

    """
    method: deleteMetadata
    when: called
    with: accessTokenAndIsFolderAndId
    should: returnException
    """
    def test_deleteMetadata_called_accessTokenAndIsFolderAndId_returnException(self):
        folderId = -1
        self.exerciseDeleteMetadata({"error":404, "description": "File or folder not found"},'false',False,folderId)

    """
    method: deleteMetadata
    when: called
    with: accessTokenAndIsFolderAndId
    should: returnException
    """
    def test_deleteMetadata_called_accessTokenAndIsFolderAndId_returnException(self):
        folderId = -1
        self.exerciseDeleteMetadata({"error":403, "description": "Forbidden. The requester does not have permission to access the specified resource."},403,False,folderId)

    """
    method: getFileVersions
    when: called
    with: accessTokenAndID
    should: returnList
    """
    def test_getFileVersions_called_accessTokenAndId_returnList(self):
        id = "32565632156"
        metadataIn = {"status": "CHANGED", "mimetype": "text/plain", "versions": [{"status": "CHANGED", "mimetype": "text/plain", "checksum": 2499810342, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": None, "version": 4, "is_folder": False, "chunks": [], "id": "155", "size": 61}, {"status": "RENAMED", "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": None, "version": 3, "is_folder": False, "chunks": [], "id": "155", "size": 59}, {"status": "RENAMED", "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": None, "version": 2, "is_folder": False, "chunks": [], "id": 155, "size": 59}, {"status": "NEW", "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": None, "version": 1, "is_folder": False, "chunks": [], "id": "155", "size": 59}], "checksum": 2499810342, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": None, "version": 4, "is_folder": False, "chunks": [], "id": "155", "size": 61}
        metadataOut = '{"status": "CHANGED", "is_folder": false, "chunks": [], "id": "155", "size": 61, "mimetype": "text/plain", "versions": [{"status": "CHANGED", "is_folder": false, "chunks": [], "id": "155", "size": 61, "mimetype": "text/plain", "checksum": 2499810342, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": "null", "version": 4}, {"status": "RENAMED", "is_folder": false, "chunks": [], "id": "155", "size": 59, "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": "null", "version": 3}, {"status": "RENAMED", "is_folder": false, "chunks": [], "id": 155, "size": 59, "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": "null", "version": 2}, {"status": "NEW", "is_folder": false, "chunks": [], "id": "155", "size": 59, "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": "null", "version": 1}], "checksum": 2499810342, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": "null", "version": 4}'
        oauth = self.createOauthSession()
        oauth.get = Mock()
        oauth.get.return_value = metadataIn
        result = self.oauthCredentials.getFileVersions(oauth,id)
        oauth.get.assert_called_once_with(self.resourceurl + self.getResource(True) + "/" + id + "/versions")
        self.assertEquals(metadataOut,result)

    """
    method: getFileVersionData
    when: called
    with: accessTokenAndIdAndVersionAndPath
    should: returnCorrect
    """
    def test_getFileVersionData_called_accessTokenAndIdAndVersionAndPath_returnCorrect(self):
        id = 123456
        self.exerciseGetFileVersionData(id, "true", "true")

    """
    method: getFileVersionData
    when: called
    with: accessTokenAndIdAndVersionAndPath
    should: returnException
    """
    def test_getFileVersionData_called_accessTokenAndIdAndVersionAndPath_returnException(self):
        id = 123456
        self.exerciseGetFileVersionData(id, 403, {"error": 403, "description": "Forbidden. The requester does not have permission to access the specified resource."})

    """
    method: getListUserShare
    when: called
    with: accessTokenAndId
    should: returnList
    """
    def test_getListUsersShare_called_accessTokenAndId_returnList(self):
        id = "123"
        metadataIn = '[{"name":"tester1","email":"tester1@test.com","is_owner":true,"joined_at":"2014-05-27"}]'
        metadataOut = '[{"joined_at": "2014-05-27", "is_owner": true, "name": "tester1", "email": "tester1@test.com"}]'
        self.exerciseGetListUsersShare(id,metadataOut,metadataIn)

    """
    method: getListUsersShare
    when: called
    with: accessTokenAndId
    should: returnException
    """
    def test_getListUsersShare_called_accessTokenAndId_returnException(self):
        id = "123"
        metadataIn = {"error":403, "description": "Forbidden. The requester does not have permission to access the specified resource."}
        metadataOut = 403
        self.exerciseGetListUsersShare(id,metadataOut,metadataIn)

    """
    method: shareFolder
    when: called
    with: accessTokenAndIdAndShared
    should: returnCorrect
    """
    def test_shareFolder_called_accessTokenAndIdAndShared_returnCorrect(self):
        id = "123"
        list = ["a@a.com","b@a.com"]
        shared = False
        metadataIn = '';
        metadataOut = 'true';
        self.exerciseShareFolder(id, list, shared, metadataOut, metadataIn)

    """
    method: shareFolder
    when: called
    with: accessTokenAndIdAndUnshared
    should: returnCorrect
    """
    def test_shareFolder_called_accessTokenAndIdAndUnshared_returnCorrect(self):
        id = "123"
        list = ["a@a.com","b@a.com"]
        shared = True
        metadataIn = '';
        metadataOut = 'true';
        self.exerciseShareFolder(id, list, shared, metadataOut, metadataIn)

    """
    method: shareFolder
    when: called
    with: accessTokenAndIdAndShared
    should: returnException
    """
    def test_shareFolder_called_accessTokenAndIdAndShared_returnException(self):
        id = "123"
        list = ["a@a.com","b@a.com"]
        shared = False
        metadataIn = {"error":403,"description":"Forbidden"}
        metadataOut = 403
        self.exerciseShareFolder(id, list, shared, metadataOut, metadataIn)

    """
    method: shareFolder
    when: called
    with: accessTokenAndIdAndUnshared
    should: returnException
    """
    def test_shareFolder_called_accessTokenAndIdAndUnshared_returnException(self):
        id = "123"
        list = ["a@a.com","b@a.com"]
        shared = True
        metadataIn = {"error":403,"description":"Forbidden"}
        metadataOut = 403
        self.exerciseShareFolder(id, list, shared, metadataOut, metadataIn)


    """
    method: insertComment
    when: called
    with: accessTokenAndIdAndUserAndTextAndCloud
    should: returnJsonMetadata
    """
    def test_insertComment_called_accessTokenAndIdAndUserAndTextAndCloud_returnJsonMetadata(self):
        id = "2150"
        user = "eyeos"
        text = "prueba"
        cloud = "stacksync"
        time_created = "201406201548"
        metadataOut = '{"status": "NEW", "text": "' + text + '", "time_created": "' + time_created + '", "user": "' + user + '", "id": "' + id + '", "cloud": "' + cloud + '"}'
        metadataIn = json.loads(metadataOut)
        oauth = self.createOauthSession()
        oauth.post = Mock()
        oauth.post.return_value = metadataIn
        result = self.oauthCredentials.insertComment(oauth,id,user,text,cloud)
        data = {"id": id,"user": user,"text":text,"cloud": cloud}
        oauth.post.assert_called_once_with(self.resourceurl + 'comment',data)
        self.assertEquals(metadataOut,result)

    """
    method: insertComment
    when: called
    with: accessTokenAndIdAndUserAndTextAndCloud
    should: returnException
    """
    def test_insertComment_called_accessTokenAndIdAndUserAndTextAndCloud_returnException(self):
        id = "2150"
        user = "eyeos"
        text = "prueba"
        cloud = "stacksync"
        metadataIn =  {"error":400, "description": "Recurso no encontrado"}
        metadataOut = 'false'
        oauth = self.createOauthSession()
        oauth.post = Mock()
        oauth.post.return_value = metadataIn
        result = self.oauthCredentials.insertComment(oauth,id,user,text,cloud)
        data = {"id": id,"user": user,"text":text,"cloud": cloud}
        oauth.post.assert_called_once_with(self.resourceurl + 'comment',data)
        self.assertEquals(metadataOut,result)

    """
    method: deleteComment
    when: called
    with: accessTokenAndIdAndUserAndCloudAndTimeCreated
    should: returnJsonMetadata
    """
    def test_deleteComment_called_accessTokenAndIdAndUserAndCloudAndTimeCreated_returnJsonMetadata(self):
        id = "2150"
        user = "eyeos"
        text = "prueba"
        cloud = "stacksync"
        time_created = "201406201548"
        metadataOut = '{"status": "NEW", "text": "' + text + '", "time_created": "' + time_created + '", "user": "' + user + '", "id": "' + id + '", "cloud": "' + cloud + '"}'
        metadataIn = json.loads(metadataOut)
        oauth = self.createOauthSession()
        oauth.delete = Mock()
        oauth.delete.return_value = metadataIn
        result = self.oauthCredentials.deleteComment(oauth,id,user,cloud,time_created)
        oauth.delete.assert_called_once_with(self.resourceurl + 'comment/' + id + '/' + user + '/' + cloud + '/' + time_created)
        self.assertEquals(metadataOut,result)


    """
    method: deleteComment
    when: called
    with: accessTokenAndIdAndUserAndCloudAndTimeCreated
    should: returnException
    """
    def test_deleteComment_called_accessTokenAndIdAndUserAndCloudAndTimeCreated_returnException(self):
        id = "2150"
        user = "eyeos"
        cloud = "stacksync"
        time_created = "201406201548"
        metadataIn =  {"error":400, "description": "Recurso no encontrado"}
        metadataOut = 'false'
        oauth = self.createOauthSession()
        oauth.delete = Mock()
        oauth.delete.return_value = metadataIn
        result = self.oauthCredentials.deleteComment(oauth,id,user,cloud,time_created)
        oauth.delete.assert_called_once_with(self.resourceurl + 'comment/' + id + '/' + user + '/' + cloud + '/' + time_created)
        self.assertEquals(metadataOut,result)

    """
    method: getComments
    when: called
    with: accessTokenAndIdAndCloud
    should: returnJsonMetadata
    """
    def test_getComments_called_accessTokenAndIdAndCloud_returnJsonMetadata(self):
        id = "2150"
        cloud = "stacksync"
        metadataOut = '[{"status": "NEW", "text": "prueba", "time_created": "201406201548", "user": "eyeos", "id": "' + id + '", "cloud": "' + cloud + '"}]'
        metadataIn = metadataOut
        oauth = self.createOauthSession()
        oauth.get = Mock()
        oauth.get.return_value = metadataIn
        result = self.oauthCredentials.getComments(oauth,id,cloud)
        oauth.get.assert_called_once_with(self.resourceurl + 'comment/' + id + '/' + cloud)
        self.assertEquals(metadataOut,result)

    """
    method: getComments
    when: called
    with: accessTokenAndIdAndCloud
    should: returnException
    """
    def test_getComments_called_accessTokenAndIdAndCloud_returnException(self):
        id = "2150"
        cloud = "stacksync"
        metadataIn =  {"error":400, "description": "Recurso no encontrado"}
        metadataOut = 'false'
        oauth = self.createOauthSession()
        oauth.get = Mock()
        oauth.get.return_value = metadataIn
        result = self.oauthCredentials.getComments(oauth,id,cloud)
        oauth.get.assert_called_once_with(self.resourceurl + 'comment/' + id + '/' + cloud)
        self.assertEquals(metadataOut,result)

    """
    method: insertEvent
    when: called
    with: accessTokenAndUserAndCalendarAndCloudAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValue
          AndSubjectAndLocationAndDescriptionAndRepeattype
    should: returnEvent
    """
    def test_insertEvent_called_accessTokenAnduserAndCalendarAndCloudAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValueAndSubjectAndLocationAndDescriptionAndRepeattype_returnEvent(self):
        metadataIn = {"type":"event","user":self.user,"calendar":self.calendar,"cloud":self.cloud,"isallday":self.isallday,"timestart":self.timestart,"timeend":self.timeend,"repetition":self.repetition,"finaltype":self.finaltype,"finalvalue":self.finalvalue,"subject":self.subject,"location":self.location,"description":self.description,"repeattype":self.repeattype,"status":"NEW"}
        metadataOut = json.dumps(metadataIn)
        oauth = self.createOauthSession()
        oauth.post = Mock()
        oauth.post.return_value = metadataIn
        result = self.oauthCredentials.insertEvent(oauth,self.user,self.calendar,self.cloud,self.isallday,self.timestart,self.timeend,self.repetition,self.finaltype,self.finalvalue,self.subject,self.location,self.description,self.repeattype)
        data = {"user":self.user,"calendar":self.calendar,"cloud":self.cloud,"isallday":self.isallday,"timestart":self.timestart,"timeend":self.timeend,"repetition":self.repetition,"finaltype":self.finaltype,"finalvalue":self.finalvalue,"subject":self.subject,"location":self.location,"description":self.description,"repeattype":self.repeattype}
        oauth.post.assert_called_once_with(self.resourceurl + 'event',data)
        self.assertEquals(metadataOut,result)

    """
    method: insertEvent
    when: called
    with: accessTokenAndUserAndCalendarAndCloudAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValue
          AndSubjectAndLocationAndDescriptionAndRepeattype
    should: returnException
    """
    def test_insertEvent_called_accessTokenAnduserAndCalendarAndCloudAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValueAndSubjectAndLocationAndDescriptionAndRepeattype_returnException(self):
        metadataIn =  {"error":400, "description": "Recurso no encontrado"}
        metadataOut = 'false'
        oauth = self.createOauthSession()
        oauth.post = Mock()
        oauth.post.return_value = metadataIn
        result = self.oauthCredentials.insertEvent(oauth,self.user,self.calendar,self.cloud,self.isallday,self.timestart,self.timeend,self.repetition,self.finaltype,self.finalvalue,self.subject,self.location,self.description,self.repeattype)
        data = {"user":self.user,"calendar":self.calendar,"cloud":self.cloud,"isallday":self.isallday,"timestart":self.timestart,"timeend":self.timeend,"repetition":self.repetition,"finaltype":self.finaltype,"finalvalue":self.finalvalue,"subject":self.subject,"location":self.location,"description":self.description,"repeattype":self.repeattype}
        oauth.post.assert_called_once_with(self.resourceurl + 'event',data)
        self.assertEquals(metadataOut,result)

    """
    method: deleteEvent
    when: called
    with: accessTokenAndUserAndCalendarAndCloudAndTimeStartAndTimeEndAndIsAllDay
    should: returnEvent
    """
    def test_deleteEvent_called_accessTokenAndUserAndCalendarAndCloudAndTimeStartAndTimeEndAndIsAllDay_returnEvent(self):
        metadataIn = {"type":"event","user":self.user,"calendar":self.calendar,"cloud":self.cloud,"isallday":self.isallday,"timestart":self.timestart,"timeend":self.timeend,"repetition":self.repetition,"finaltype":self.finaltype,"finalvalue":self.finalvalue,"subject":self.subject,"location":self.location,"description":self.description,"repeattype":self.repeattype,"status":"DELETED"}
        metadataOut = json.dumps(metadataIn)
        oauth = self.createOauthSession()
        oauth.delete = Mock()
        oauth.delete.return_value = metadataIn
        result = self.oauthCredentials.deleteEvent(oauth,self.user,self.calendar,self.cloud,self.timestart,self.timeend,self.isallday)
        oauth.delete.assert_called_once_with(self.resourceurl + 'event/' + self.user + '/' + self.calendar + '/' + self.cloud + '/' + self.timestart + '/' + self.timeend + '/' + str(self.isallday))
        self.assertEquals(metadataOut,result)

    """
    method: deleteEvent
    when: called
    with: accessTokenAndUserAndCalendarAndCloudAndTimeStartAndTimeEndAndIsAllDay
    should: returnException
    """
    def test_deleteEvent_called_accessTokenAndUserAndCalendarAndCloudAndTimeStartAndTimeEndAndIsAllDay_returnException(self):
        metadataIn =  {"error":400, "description": "Recurso no encontrado"}
        metadataOut = 'false'
        oauth = self.createOauthSession()
        oauth.delete = Mock()
        oauth.delete.return_value = metadataIn
        result = self.oauthCredentials.deleteEvent(oauth,self.user,self.calendar,self.cloud,self.timestart,self.timeend,self.isallday)
        oauth.delete.assert_called_once_with(self.resourceurl + 'event/' + self.user + '/' + self.calendar + '/' + self.cloud + '/' + self.timestart + '/' + self.timeend + '/' + str(self.isallday))
        self.assertEquals(metadataOut,result)

    """
    method: updateEvent
    when: called
    with: accessTokenAndUserAndCalendarAndCloudAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValue
         AndSubjectAndLocationAndDescriptionAndRepeattype
    should: returnEvent
    """
    def test_updateEvent_called_accessTokenAnduserAndCalendarAndCloudAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValueAndSubjectAndLocationAndDescriptionAndRepeattype_returnEvent(self):
        metadataIn = {"type":"event","user":self.user,"calendar":self.calendar,"cloud":self.cloud,"isallday":self.isallday,"timestart":self.timestart,"timeend":self.timeend,"repetition":self.repetition,"finaltype":self.finaltype,"finalvalue":self.finalvalue,"subject":self.subject,"location":self.location,"description":self.description,"repeattype":self.repeattype,"status":"CHANGED"}
        metadataOut = json.dumps(metadataIn)
        oauth = self.createOauthSession()
        oauth.put = Mock()
        oauth.put.return_value = metadataIn
        result = self.oauthCredentials.updateEvent(oauth,self.user,self.calendar,self.cloud,self.isallday,self.timestart,self.timeend,self.repetition,self.finaltype,self.finalvalue,self.subject,self.location,self.description,self.repeattype)
        data = {"user":self.user,"calendar":self.calendar,"cloud":self.cloud,"isallday":self.isallday,"timestart":self.timestart,"timeend":self.timeend,"repetition":self.repetition,"finaltype":self.finaltype,"finalvalue":self.finalvalue,"subject":self.subject,"location":self.location,"description":self.description,"repeattype":self.repeattype}
        oauth.put.assert_called_once_with(self.resourceurl + 'event',data)
        self.assertEquals(metadataOut,result)

    """
    method: updateEvent
    when: called
    with: accessTokenAndUserAndCalendarAndCloudAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValue
         AndSubjectAndLocationAndDescriptionAndRepeattype
    should: returnException
    """
    def test_updateEvent_called_accessTokenAnduserAndCalendarAndCloudAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValueAndSubjectAndLocationAndDescriptionAndRepeattype_returnException(self):
        metadataIn =  {"error":400, "description": "Recurso no encontrado"}
        metadataOut = 'false'
        oauth = self.createOauthSession()
        oauth.put = Mock()
        oauth.put.return_value = metadataIn
        result = self.oauthCredentials.updateEvent(oauth,self.user,self.calendar,self.cloud,self.isallday,self.timestart,self.timeend,self.repetition,self.finaltype,self.finalvalue,self.subject,self.location,self.description,self.repeattype)
        data = {"user":self.user,"calendar":self.calendar,"cloud":self.cloud,"isallday":self.isallday,"timestart":self.timestart,"timeend":self.timeend,"repetition":self.repetition,"finaltype":self.finaltype,"finalvalue":self.finalvalue,"subject":self.subject,"location":self.location,"description":self.description,"repeattype":self.repeattype}
        oauth.put.assert_called_once_with(self.resourceurl + 'event',data)
        self.assertEquals(metadataOut,result)

    """
    method: getEvents
    when: called
    with: accessTokenAndUserAndCalendarAndCloud
    should: returnEvents
    """
    def test_getEvents_called_accessTokenAndUserAndCalendarAndCloud_returnEvents(self):
        metadataIn = json.dumps([{"type":"event","user":self.user,"calendar":self.calendar,"cloud":self.cloud,"isallday":self.isallday,"timestart":self.timestart,"timeend":self.timeend,"repetition":self.repetition,"finaltype":self.finaltype,"finalvalue":self.finalvalue,"subject":self.subject,"location":self.location,"description":self.description,"repeattype":self.repeattype,"status":"CHANGED"}])
        metadataOut = '[{"status": "CHANGED", "description": "Llevar justificante", "location": "Barcelona", "finalvalue": "0", "timeend": "201419170000", "finaltype": "1", "timestart": "201419160000", "isallday": 0, "user": "eyeos", "repeattype": "n", "calendar": "personal", "repetition": "None", "type": "event", "cloud": "Stacksync", "subject": "VisitaMedico"}]'
        oauth = self.createOauthSession()
        oauth.get = Mock()
        oauth.get.return_value = metadataIn
        result = self.oauthCredentials.getEvents(oauth,self.user,self.calendar,self.cloud)
        oauth.get.assert_called_once_with(self.resourceurl + 'event/' + self.user + '/' + self.calendar + '/' + self.cloud)
        self.assertEquals(metadataOut,result)

    """
    method: getEvents
    when: called
    with: accessTokenAndUserAndCalendarAndCloud
    should: returnException
    """
    def test_getEvents_called_accessTokenAndUserAndCalendarAndCloud_returnException(self):
        metadataIn =  {"error":400, "description": "Recurso no encontrado"}
        metadataOut = 'false'
        oauth = self.createOauthSession()
        oauth.get = Mock()
        oauth.get.return_value = metadataIn
        result = self.oauthCredentials.getEvents(oauth,self.user,self.calendar,self.cloud)
        oauth.get.assert_called_once_with(self.resourceurl + 'event/' + self.user + '/' + self.calendar + '/' + self.cloud)
        self.assertEquals(metadataOut,result)

    """
    method: insertCalendar
    when: called
    with: accessTokenAndUserAndNameAndCloudAndDescriptionAndTimeZone
    should: returnCalendar
    """
    def test_insertCalendar_called_accessTokenAndUserAndNameAndCloudAndDescriptionAndTimeZone_returnCalendar(self):
        metadataIn = {"type":"calendar","user":self.user,"name":self.calendar,"cloud":self.cloud,"description":self.description,"timezone":self.timezone,"status":"NEW"}
        metadataOut = json.dumps(metadataIn)
        oauth = self.createOauthSession()
        oauth.post = Mock()
        oauth.post.return_value = metadataIn
        result = self.oauthCredentials.insertCalendar(oauth,self.user,self.calendar,self.cloud,self.description,self.timezone)
        data = {"user":self.user,"name":self.calendar,"cloud":self.cloud,"description":self.description,"timezone":self.timezone}
        oauth.post.assert_called_once_with(self.resourceurl + 'calendar',data)
        self.assertEquals(metadataOut,result)

    """
    method: insertCalendar
    when: called
    with: accessTokenAndUserAndNameAndCloudAndDescriptionAndTimeZone
    should: returnException
    """
    def test_insertCalendar_called_accessTokenAndUserAndNameAndCloudAndDescriptionAndTimeZone_returnException(self):
        metadataIn =  {"error":400, "description": "Recurso no encontrado"}
        metadataOut = 'false'
        oauth = self.createOauthSession()
        oauth.post = Mock()
        oauth.post.return_value = metadataIn
        result = self.oauthCredentials.insertCalendar(oauth,self.user,self.calendar,self.cloud,self.description,self.timezone)
        data = {"user":self.user,"name":self.calendar,"cloud":self.cloud,"description":self.description,"timezone":self.timezone}
        oauth.post.assert_called_once_with(self.resourceurl + 'calendar',data)
        self.assertEquals(metadataOut,result)

    """
    method: deleteCalendar
    when: called
    with: accessTokenAndUserAndNameAndCloud
    should: returnCalendar
    """
    def test_deleteCalendar_called_accessTokenAndUserAndNameAndCloud_returnCalendar(self):
        metadataIn = {"type":"calendar","user":self.user,"name":self.calendar,"cloud":self.cloud,"description":self.description,"timezone":self.timezone,"status":"DELETED"}
        metadataOut = json.dumps(metadataIn)
        oauth = self.createOauthSession()
        oauth.delete = Mock()
        oauth.delete.return_value = metadataIn
        result = self.oauthCredentials.deleteCalendar(oauth,self.user,self.calendar,self.cloud)
        oauth.delete.assert_called_once_with(self.resourceurl + 'calendar/' + self.user + '/' + self.calendar + '/' + self.cloud)
        self.assertEquals(metadataOut,result)

    """
    method: deleteCalendar
    when: called
    with: accessTokenAndUserAndNameAndCloud
    should: returnException
    """
    def test_deleteCalendar_called_accessTokenAndUserAndNameAndCloud_returnException(self):
        metadataIn =  {"error":400, "description": "Recurso no encontrado"}
        metadataOut = 'false'
        oauth = self.createOauthSession()
        oauth.delete = Mock()
        oauth.delete.return_value = metadataIn
        result = self.oauthCredentials.deleteCalendar(oauth,self.user,self.calendar,self.cloud)
        oauth.delete.assert_called_once_with(self.resourceurl + 'calendar/' + self.user + '/' + self.calendar + '/' + self.cloud)
        self.assertEquals(metadataOut,result)

    """
    method: updateCalendar
    when: called
    with: accessTokenAndUserAndNameAndCloudAndDescriptionAndTimeZone
    should: returnCalendar
    """
    def test_updateCalendar_called_accessTokenAndUserAndNameAndCloudAndDescriptionAndTimeZone_returnCalendar(self):
        metadataIn = {"type":"calendar","user":self.user,"name":self.calendar,"cloud":self.cloud,"description":self.description,"timezone":self.timezone,"status":"CHANGED"}
        metadataOut = json.dumps(metadataIn)
        oauth = self.createOauthSession()
        oauth.put = Mock()
        oauth.put.return_value = metadataIn
        result = self.oauthCredentials.updateCalendar(oauth,self.user,self.calendar,self.cloud,self.description,self.timezone)
        data = {"user":self.user,"name":self.calendar,"cloud":self.cloud,"description":self.description,"timezone":self.timezone}
        oauth.put.assert_called_once_with(self.resourceurl + 'calendar',data)
        self.assertEquals(metadataOut,result)

    """
    method: updateCalendar
    when: called
    with: accessTokenAndUserAndNameAndCloudAndDescriptionAndTimeZone
    should: returnException
    """
    def test_updateCalendar_called_accessTokenAndUserAndNameAndCloudAndDescriptionAndTimeZone_returnException(self):
        metadataIn =  {"error":400, "description": "Recurso no encontrado"}
        metadataOut = 'false'
        oauth = self.createOauthSession()
        oauth.put = Mock()
        oauth.put.return_value = metadataIn
        result = self.oauthCredentials.updateCalendar(oauth,self.user,self.calendar,self.cloud,self.description,self.timezone)
        data = {"user":self.user,"name":self.calendar,"cloud":self.cloud,"description":self.description,"timezone":self.timezone}
        oauth.put.assert_called_once_with(self.resourceurl + 'calendar',data)
        self.assertEquals(metadataOut,result)


    """
    method: getCalendars
    when: called
    with: accessTokenAndUserAndCloud
    should: returnCalendars
    """
    def test_getCalendars_called_accessTokenAndUserAndCloud_returnCalendars(self):
        metadataIn = json.dumps([{"type":"calendar","user":self.user,"name":self.calendar,"cloud":self.cloud,"description":self.description,"timezone":self.timezone,"status":"NEW"}])
        metadataOut = '[{"status": "NEW", "description": "Llevar justificante", "user": "eyeos", "timezone": 0, "type": "calendar", "cloud": "Stacksync", "name": "personal"}]'
        oauth = self.createOauthSession()
        oauth.get = Mock()
        oauth.get.return_value = metadataIn
        result = self.oauthCredentials.getCalendars(oauth,self.user,self.cloud)
        oauth.get.assert_called_once_with(self.resourceurl + 'calendar/' + self.user + '/' + self.cloud)
        self.assertEquals(metadataOut,result)

    """
    method: getCalendars
    when: called
    with: accessTokenAndUserAndCloud
    should: returnException
    """
    def test_getCalendars_called_accessTokenAndUserAndCloud_returnException(self):
        metadataIn =  {"error":400, "description": "Recurso no encontrado"}
        metadataOut = 'false'
        oauth = self.createOauthSession()
        oauth.get = Mock()
        oauth.get.return_value = metadataIn
        result = self.oauthCredentials.getCalendars(oauth,self.user,self.cloud)
        oauth.get.assert_called_once_with(self.resourceurl + 'calendar/' + self.user + '/' + self.cloud)
        self.assertEquals(metadataOut,result)

    """
    method: getCalendarsAndEvents
    when: called
    with: accessTokenAndUserAndCloud
    should: returnCalendarsAndEvents
    """
    def test_getCalendarsAndEvents_called_accessTokenAndUserAndCloud_returnCalendarsAndEvents(self):
        metadataIn = []
        metadataIn.append({"type":"calendar","user":self.user,"name":self.calendar,"cloud":self.cloud,"description":self.description,"timezone":self.timezone,"status":"NEW"})
        metadataIn.append({"type":"event","user":self.user,"calendar":self.calendar,"cloud":self.cloud,"isallday":self.isallday,"timestart":self.timestart,"timeend":self.timeend,"repetition":self.repetition,"finaltype":self.finaltype,"finalvalue":self.finalvalue,"subject":self.subject,"location":self.location,"description":self.description,"repeattype":"n","status":"NEW"})
        metadataIn = json.dumps(metadataIn)
        metadataOut = '[{"status": "NEW", "description": "Llevar justificante", "user": "eyeos", "timezone": 0, "type": "calendar", "cloud": "Stacksync", "name": "personal"}, {"status": "NEW", "description": "Llevar justificante", "location": "Barcelona", "finalvalue": "0", "timeend": "201419170000", "finaltype": "1", "timestart": "201419160000", "isallday": 0, "user": "eyeos", "repeattype": "n", "calendar": "personal", "repetition": "None", "type": "event", "cloud": "Stacksync", "subject": "VisitaMedico"}]'
        oauth = self.createOauthSession()
        oauth.get = Mock()
        oauth.get.return_value = metadataIn
        result = self.oauthCredentials.getCalendarsAndEvents(oauth,self.user,self.cloud)
        oauth.get.assert_called_once_with(self.resourceurl + 'calEvents/' + self.user + '/' + self.cloud)
        self.assertEquals(metadataOut,result)

    """
    method: getCalendarsAndEvents
    when: called
    with: accessTokenAndUserAndCloud
    should: returnException
    """
    def test_getCalendarsAndEvents_called_accessTokenAndUserAndCloud_returnException(self):
        metadataIn =  {"error":400, "description": "Recurso no encontrado"}
        metadataOut = 'false'
        oauth = self.createOauthSession()
        oauth.get = Mock()
        oauth.get.return_value = metadataIn
        result = self.oauthCredentials.getCalendarsAndEvents(oauth,self.user,self.cloud)
        oauth.get.assert_called_once_with(self.resourceurl + 'calEvents/' + self.user + '/' + self.cloud)
        self.assertEquals(metadataOut,result)

    """
    method: deleteCalendarsUser
    when: called
    with: accessTokenAndUserAndCloud
    should: deleteCorrect
    """
    def test_deleteCalendarsUser_called_accessTokenAndUserAndCloud_deleteCorrect(self):
        metadataIn =  {"delete":True}
        metadataOut = json.dumps(metadataIn)
        oauth = self.createOauthSession()
        oauth.delete = Mock()
        oauth.delete.return_value = metadataIn
        result = self.oauthCredentials.deleteCalendarsUser(oauth,self.user,self.cloud)
        oauth.delete.assert_called_once_with(self.resourceurl + 'calUser/' + self.user + '/' + self.cloud)
        self.assertEquals(metadataOut,result)

    """
    method: deleteCalendarsUser
    when: called
    with: accessTokenAndUserAndCloud
    should: returnException
    """
    def test_deleteCalendarsUser_called_accessTokenAndUserAndCloud_returnException(self):
        metadataIn =  {"error":400, "description": "Recurso no encontrado"}
        metadataOut = 'false'
        oauth = self.createOauthSession()
        oauth.delete = Mock()
        oauth.delete.return_value = metadataIn
        result = self.oauthCredentials.deleteCalendarsUser(oauth,self.user,self.cloud)
        oauth.delete.assert_called_once_with(self.resourceurl + 'calUser/' + self.user + '/' + self.cloud)
        self.assertEquals(metadataOut,result)


    """
    method: lockFile
    when: called
    with: accessTokenAndIdAndCloudAndUserAndIpServerAndDateTimeAndTimeLimit
    should: returnLockCorrect
    """
    def test_lockFile_called_accessTokenAndIdAndCloudAndUserAndIpServerAndDateTimeAndTimeLimit_returnLockCorrect(self):
        data = {"id":self.idFile,"cloud":self.cloud,"user":self.user,"ipserver":self.ipserver,"datetime":self.datetime,"timelimit":self.timelimit}
        metadataIn = {"lockFile":True}
        metadataOut = json.dumps(metadataIn)
        oauth = self.createOauthSession()
        oauth.post = Mock()
        oauth.post.return_value = metadataIn
        result = self.oauthCredentials.lockFile(oauth,self.idFile,self.cloud,self.user,self.ipserver,self.datetime,self.timelimit)
        oauth.post.assert_called_once_with(self.resourceurl + 'lockFile',data)
        self.assertEquals(metadataOut,result)

    """
    method: lockFile
    when: called
    with: accessTokenAndIdAndCloudAndUserAndIpServerAndDateTimeAndTimeLimitAndInterop
    should: returnLockCorrect
    """
    def test_lockFile_called_accessTokenAndIdAndCloudAndUserAndIpServerAndDateTimeAndTimeLimitAndInterop_returnLockCorrect(self):
        data = {"id":self.idFile,"cloud":self.cloud,"user":self.user,"ipserver":self.ipserver,"datetime":self.datetime,"timelimit":self.timelimit,"interop":"true"}
        metadataIn = {"lockFile":True}
        metadataOut = json.dumps(metadataIn)
        oauth = self.createOauthSession()
        oauth.post = Mock()
        oauth.post.return_value = metadataIn
        result = self.oauthCredentials.lockFile(oauth,self.idFile,self.cloud,self.user,self.ipserver,self.datetime,self.timelimit,"true")
        oauth.post.assert_called_once_with(self.resourceurl + 'lockFile',data)
        self.assertEquals(metadataOut,result)


    """
    method: lockFile
    when: called
    with: accessTokenAndIdAndCloudAndUserAndIpServerAndDateTimeAndTimeLimit
    should: returnException
    """
    def test_lockFile_called_accessTokenAndIdAndCloudAndUserAndIpServerAndDateTimeAndTimeLimit_returnException(self):
        data = {"id":self.idFile,"cloud":self.cloud,"user":self.user,"ipserver":self.ipserver,"datetime":self.datetime,"timelimit":self.timelimit}
        metadataIn =  {"error":400, "description": "Recurso no encontrado"}
        metadataOut = 'false'
        oauth = self.createOauthSession()
        oauth.post = Mock()
        oauth.post.return_value = metadataIn
        result = self.oauthCredentials.lockFile(oauth,self.idFile,self.cloud,self.user,self.ipserver,self.datetime,self.timelimit)
        oauth.post.assert_called_once_with(self.resourceurl + 'lockFile',data)
        self.assertEquals(metadataOut,result)

    """
    method: lockFile
    when: called
    with: accessTokenAndIdAndCloudAndUserAndIpServerAndDateTimeAndTimeLimitAndInterop
    should: returnException
    """
    def test_lockFile_called_accessTokenAndIdAndCloudAndUserAndIpServerAndDateTimeAndTimeLimitAndInterop_returnException(self):
        data = {"id":self.idFile,"cloud":self.cloud,"user":self.user,"ipserver":self.ipserver,"datetime":self.datetime,"timelimit":self.timelimit,"interop":"true"}
        metadataIn =  {"error":400, "description": "Recurso no encontrado"}
        metadataOut = 'false'
        oauth = self.createOauthSession()
        oauth.post = Mock()
        oauth.post.return_value = metadataIn
        result = self.oauthCredentials.lockFile(oauth,self.idFile,self.cloud,self.user,self.ipserver,self.datetime,self.timelimit,"true")
        oauth.post.assert_called_once_with(self.resourceurl + 'lockFile',data)
        self.assertEquals(metadataOut,result)



    """
    method: updateDateTime
    when: called
    with: accessTokenAndIdAndCloudAndUserAndIpServerAndDateTime
    should: returnUpdateCorrect
    """
    def test_updateDateTime_called_accessTokenAndIdAndCloudAndUserAndIpServerAndDateTime_returnUpdateCorrect(self):
        data = {"id":self.idFile,"cloud":self.cloud,"user":self.user,"ipserver":self.ipserver,"datetime":self.datetime}
        metadataIn = {"updateFile":True}
        metadataOut = json.dumps(metadataIn)
        oauth = self.createOauthSession()
        oauth.put = Mock()
        oauth.put.return_value = metadataIn
        result = self.oauthCredentials.updateDateTime(oauth,self.idFile,self.cloud,self.user,self.ipserver,self.datetime)
        oauth.put.assert_called_once_with(self.resourceurl + 'updateTime',data)
        self.assertEquals(metadataOut,result)

    """
    method: updateDateTime
    when: called
    with: accessTokenAndIdAndCloudAndUserAndIpServerAndDateTime
    should: returnException
    """
    def test_updateDateTime_called_accessTokenAndIdAndCloudAndUserAndIpServerAndDateTime_returnException(self):
        data = {"id":self.idFile,"cloud":self.cloud,"user":self.user,"ipserver":self.ipserver,"datetime":self.datetime}
        metadataIn =  {"error":400, "description": "Recurso no encontrado"}
        metadataOut = 'false'
        oauth = self.createOauthSession()
        oauth.put = Mock()
        oauth.put.return_value = metadataIn
        result = self.oauthCredentials.updateDateTime(oauth,self.idFile,self.cloud,self.user,self.ipserver,self.datetime)
        oauth.put.assert_called_once_with(self.resourceurl + 'updateTime',data)
        self.assertEquals(metadataOut,result)

    """
    method: unLockFile
    when: called
    with: accessTokenAndIdAndCloudAndUserAndIpServerAndDateTime
    should: returnUnLockCorrect
    """
    def test_unLockFile_called_accessTokenAndIdAndCloudAndUserAndIpServerAndDateTime_returnUnLockCorrect(self):
        data = {"id":self.idFile,"cloud":self.cloud,"user":self.user,"ipserver":self.ipserver,"datetime":self.datetime}
        metadataIn = {"unLockFile":True}
        metadataOut = json.dumps(metadataIn)
        oauth = self.createOauthSession()
        oauth.put = Mock()
        oauth.put.return_value = metadataIn
        result = self.oauthCredentials.unLockFile(oauth,self.idFile,self.cloud,self.user,self.ipserver,self.datetime)
        oauth.put.assert_called_once_with(self.resourceurl + 'unLockFile',data)
        self.assertEquals(metadataOut,result)

    """
    method: unLockFile
    when: called
    with: accessTokenAndIdAndCloudAndUserAndIpServerAndDateTime
    should: returnException
    """
    def test_unLockFile_called_accessTokenAndIdAndCloudAndUserAndIpServerAndDateTime_returnException(self):
        data = {"id":self.idFile,"cloud":self.cloud,"user":self.user,"ipserver":self.ipserver,"datetime":self.datetime}
        metadataIn =  {"error":400, "description": "Recurso no encontrado"}
        metadataOut = 'false'
        oauth = self.createOauthSession()
        oauth.put = Mock()
        oauth.put.return_value = metadataIn
        result = self.oauthCredentials.unLockFile(oauth,self.idFile,self.cloud,self.user,self.ipserver,self.datetime)
        oauth.put.assert_called_once_with(self.resourceurl + 'unLockFile',data)
        self.assertEquals(metadataOut,result)


    """
    method: getMetadataFile
    when: called
    with: accessTokenAndIdAndCloud
    should: returnMetadataFile
    """
    def test_getMetadataFile_called_accessTokenAndIdAndCloud_returnMetadataFile(self):
        metadataIn = []
        metadataIn.append( {"id":self.idFile,"cloud":self.cloud,"user":self.user,"ipserver":self.ipserver,"datetime":self.datetime,"status":"open"})
        metadataIn = json.dumps(metadataIn)
        metadataOut = '[{"status": "open", "datetime": "2015-05-12 10:50:00", "user": "eyeos", "ipserver": "192.168.56.101", "id": "2150", "cloud": "Stacksync"}]'
        oauth = self.createOauthSession()
        oauth.get = Mock()
        oauth.get.return_value = metadataIn
        result = self.oauthCredentials.getMetadataFile(oauth,self.idFile,self.cloud)
        oauth.get.assert_called_once_with(self.resourceurl + 'lockFile/' + self.idFile + '/' + self.cloud)
        self.assertEquals(metadataOut,result)


    """
    method: getMetadataFile
    when: called
    with: accessTokenAndIdAndCloudAndInterop
    should: returnMetadataFile
    """
    def test_getMetadataFile_called_accessTokenAndIdAndCloudAndInterop_returnMetadataFile(self):
        metadataIn = []
        metadataIn.append( {"id":self.idFile,"cloud":self.cloud,"user":self.user,"ipserver":self.ipserver,"datetime":self.datetime,"status":"open"})
        metadataIn = json.dumps(metadataIn)
        metadataOut = '[{"status": "open", "datetime": "2015-05-12 10:50:00", "user": "eyeos", "ipserver": "192.168.56.101", "id": "2150", "cloud": "Stacksync"}]'
        oauth = self.createOauthSession()
        oauth.get = Mock()
        oauth.get.return_value = metadataIn
        result = self.oauthCredentials.getMetadataFile(oauth,self.idFile,self.cloud,"interop")
        oauth.get.assert_called_once_with(self.resourceurl + 'lockFile/' + self.idFile + '/' + self.cloud + '/interop')
        self.assertEquals(metadataOut,result)

    """
    method: getMetadataFile
    when: called
    with: accessTokenAndIdAndCloud
    should: returnException
    """
    def test_getMetadataFile_called_accessTokenAndIdAndCloud_returnException(self):
        metadataIn =  {"error":400, "description": "Recurso no encontrado"}
        metadataOut = 'false'
        oauth = self.createOauthSession()
        oauth.get = Mock()
        oauth.get.return_value = metadataIn
        result = self.oauthCredentials.getMetadataFile(oauth,self.idFile,self.cloud)
        oauth.get.assert_called_once_with(self.resourceurl + 'lockFile/' + self.idFile + '/' + self.cloud)
        self.assertEquals(metadataOut,result)

    """
    method: getMetadataFile
    when: called
    with: accessTokenAndIdAndCloudAndInterop
    should: returnException
    """
    def test_getMetadataFile_called_accessTokenAndIdAndCloud_returnException(self):
        metadataIn =  {"error":400, "description": "Recurso no encontrado"}
        metadataOut = 'false'
        oauth = self.createOauthSession()
        oauth.get = Mock()
        oauth.get.return_value = metadataIn
        result = self.oauthCredentials.getMetadataFile(oauth,self.idFile,self.cloud,"interop")
        oauth.get.assert_called_once_with(self.resourceurl + 'lockFile/' + self.idFile + '/' + self.cloud + '/interop')
        self.assertEquals(metadataOut,result)


    def createOauthSession(self):
        oauth = OAuth1Session(self.key, client_secret=self.secret,resource_owner_key="OPQR",resource_owner_secret="STVW")
        return oauth

    def exerciseMetadata(self,metadataIn,metadataOut,file,id,contents = ""):
        oauth = self.createOauthSession()
        oauth.get = Mock()
        oauth.get.return_value = metadataIn
        result = self.oauthCredentials.getMetadata(oauth,file,id,contents)
        oauth.get.assert_called_once_with(self.resourceurl + self.getResource(file) + "/" + str(id) + contents)
        self.assertEquals(metadataOut,result)

    def exerciseUpdateMetadata(self,metadataIn,metadataOut,file,id,name,parent,data):
        oauth = self.createOauthSession()
        oauth.put = Mock()
        oauth.put.return_value = metadataIn
        result = self.oauthCredentials.updateMetadata(oauth,file,id,name,parent)
        oauth.put.assert_called_once_with(self.resourceurl + self.getResource(file) + "/" + str(id),json.dumps(data))
        self.assertEquals(metadataOut,result)

    def exerciseCreateMetadata(self,metadataIn,metadataOut,file,name,parent):
        path = None
        dataFile = None
        if file == True:
            path = "prueba.txt"
            self.file.write("Esto es una prueba")
            self.file.close()
            self.file = open(path,"r")
            dataFile = self.file.read()

        data = {}
        data['name'] = name
        if parent != None:
            data['parent'] = str(parent)

        url = self.resourceurl + self.getResource(file)

        if file:
            params = urllib.urlencode(data)
            url += "?" + params
        else:
            dataFile = json.dumps(data)

        oauth = self.createOauthSession()
        oauth.post = Mock()
        oauth.post.return_value = metadataIn
        result = self.oauthCredentials.createMetadata(oauth,file,name,parent,path)
        oauth.post.assert_called_once_with(url ,dataFile)
        self.assertEquals(metadataOut,result)

    def exerciseUploadFile(self,fileId,check,returnValue):
        path = "prueba.txt"
        self.file.write("Esto es una prueba")
        self.file.close()
        self.file = open(path,"r")
        oauth = self.createOauthSession()
        oauth.put = Mock()
        oauth.put.return_value = returnValue
        result = self.oauthCredentials.uploadFile(oauth,fileId,path)
        oauth.put.assert_called_once_with(self.resourceurl + "file/" + str(fileId) + "/data",self.file.read())
        self.assertEquals(check,result)

    def exerciseDonwloadFile(self,fileId,check,returnValue):
        path = "prueba.txt"
        oauth = self.createOauthSession()
        oauth.get = Mock()
        oauth.get.return_value = returnValue
        result = self.oauthCredentials.downloadFile(oauth,fileId,path)
        oauth.get.assert_called_once_with(self.resourceurl + "file/" + str(fileId) + "/data")
        self.assertEquals(check,result)

    def exerciseDeleteMetadata(self,metadataIn,metadataOut,file,fileId):
        oauth = self.createOauthSession()
        oauth.delete = Mock()
        oauth.delete.return_value = metadataIn
        result = self.oauthCredentials.deleteMetadata(oauth,file,fileId)
        oauth.delete.assert_called_once_with(self.resourceurl + self.getResource(file) + "/" + str(fileId))
        self.assertEquals(metadataOut,result)

    def exerciseGetFileVersionData(self, id, check, returnValue):
        path = "prueba.txt"
        version = 2
        oauth = self.createOauthSession()
        oauth.get = Mock()
        oauth.get.return_value = returnValue
        result = self.oauthCredentials.getFileVersionData(oauth, id, version, path)
        oauth.get.assert_called_once_with(self.resourceurl + "file/" + str(id) + "/version/" + str(version) + "/data")
        self.assertEquals(check,result)

    def exerciseGetListUsersShare(self,id,check,returnValue):
        oauth = self.createOauthSession()
        oauth.get = Mock()
        oauth.get.return_value = returnValue
        result = self.oauthCredentials.getListUsersShare(oauth,id)
        oauth.get.assert_called_once_with(self.resourceurl + self.getResource(False) + "/" + id + "/members")
        self.assertEquals(check,result)

    def exerciseShareFolder(self, id, list, shared, check, returnValue):
        oauth = self.createOauthSession()
        oauth.post = Mock()
        oauth.post.return_value = returnValue
        result = self.oauthCredentials.shareFolder(oauth, id, list, shared)
        if shared == False:
            url = self.resourceurl + self.getResource(False) + "/" + str(id) + "/share"
        else:
            url = self.resourceurl + self.getResource(False) + "/" + str(id) + "/unshare"
        oauth.post.assert_called_once_with(url, json.dumps(list))
        self.assertEquals(check, result)

    def getResource(self,file):
        resource = "folder"
        if file:
            resource = "file"
        return resource


