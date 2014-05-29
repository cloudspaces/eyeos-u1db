__author__ = 'root'

import unittest
from OauthCredentials import OauthCredentials
from mock import Mock
from requests_oauthlib import OAuth1Session
import os

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
        expected = {"key":"HIJK","secret":"LMN"}
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
        expected = {"key":"MNOP","secret":"STVM"}
        oauth = OAuth1Session(self.key, client_secret=self.secret,resource_owner_key="ABCD",resource_owner_secret="EFG",verifier='verifier')
        oauth.fetch_access_token = Mock()
        oauth.fetch_access_token.return_value = {"oauth_token":"MNOP","oauth_token_secret":"STVM"}
        result = self.oauthCredentials.getAccessToken(oauth)
        oauth.fetch_access_token.assert_called_once_with(self.accesstokenurl)
        self.assertEquals(expected,result)


    """
    method: getMetadata
    when: called
    with: accessTokenAndFileAndFileId
    should: returnJsonMetadata
    """
    def test_getMetadata_called_accessTokenAndFileAndId_returnJsonMetadata(self):
        fileId = 32565632156;
        metadataIn = {"name":"Client1.pdf","path":"/documents/clients/Client1.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent":None,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"}
        metadataOut = '{"status": "DELETED", "parent": "null", "user": "eyeos", "server_modified": "2013-03-08 10:36:41.997", "path": "/documents/clients/Client1.pdf", "id": 32565632156, "size": 775412, "mimetype": "application/pdf", "name": "Client1.pdf", "client_modified": "2013-03-08 10:36:41.997", "version": 3}'
        self.exerciseMetadata(metadataIn,metadataOut,True,fileId)

    """
    method: getMetadata
    when: called
    with: accessTokenAndFolderAndId
    should: returnJsonMetadata
    """
    def test_getMetadata_called_accessTokenAndFolderAndId_returnJsonMetadata(self):
        folderId = 9873615
        metadataIn = {"name":"clients","path":"/documents/clients","id":9873615,"status":"NEW","version":1,"parent":None,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":False}
        metadataOut = '{"status": "NEW", "is_root": false, "version": 1, "name": "clients", "parent": "null", "server_modified": "2013-03-08 10:36:41.997", "path": "/documents/clients", "client_modified": "2013-03-08 10:36:41.997", "id": 9873615, "user": "eyeos"}'
        self.exerciseMetadata(metadataIn,metadataOut,False,folderId)

    """
    method: getMetadata
    when: called
    with: accessTokenAndFolderAndIdAndContents
    should: returnJsonMetadata
    """
    def test_getMetadata_called_accessTokenAndFolderAndIdAndContents_returnJsonMetadata(self):
        folderId = 9873615
        metadataIn = {"name":"clients","path":"/documents/clients","id":9873615,"status":"NEW","version":1,"parent":None,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","contents":[{"name":"Client1.pdf","path":"/documents/clients/Client1.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent":-348534824681,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":False}]}
        metadataOut = '{"status": "NEW", "version": 1, "name": "clients", "parent": "null", "server_modified": "2013-03-08 10:36:41.997", "path": "/documents/clients", "client_modified": "2013-03-08 10:36:41.997", "id": 9873615, "contents": [{"status": "DELETED", "parent": -348534824681, "is_root": false, "user": "eyeos", "server_modified": "2013-03-08 10:36:41.997", "path": "/documents/clients/Client1.pdf", "id": 32565632156, "size": 775412, "mimetype": "application/pdf", "name": "Client1.pdf", "client_modified": "2013-03-08 10:36:41.997", "version": 3}], "user": "eyeos"}'
        self.exerciseMetadata(metadataIn,metadataOut,False,folderId,"/contents")

    """
    method: getMetadata
    when: called
    with: accessTokenAndFolderAndId
    should: returnException
    """
    def test_getMetadata_called_accessTokenAndFolderAndId_returnException(self):
        folderId = -1
        metadataIn =  {"error":404, "description": "File or folder not found."}
        metadataOut = 'false'
        self.exerciseMetadata(metadataIn,metadataOut,False,folderId,"/contents")

    """
   method: getMetadata
   when: called
   with: accessTokenAndFolderAndId
   should: returnPermissionDenied
   """
    def test_getMetadata_called_accessTokenAndFolderAndId_returnPermissionDenied(self):
        folderId = 9873615
        metadataIn =  {"error":403, "description": "Forbidden. The requester does not have permission to access the specified resource"}
        metadataOut = 403
        self.exerciseMetadata(metadataIn,metadataOut,False,folderId,"/contents")

    """
    method: accessTokenAndFileAndNameAndParent
    when: called
    with: accessTokenAndFileAndIdAndNameAndParent
    should: returnJsonMetadataRename
    """
    def test_updateMetadata_called_accessTokenAndFileAndIdAndNameAndParent_returnJsonMetadataRename(self):
        fileId = 32565632156;
        parentId = 123456
        name = "prueba.pdf"
        data = {}
        data['name'] = name
        data['parent'] = parentId
        metadataIn = {"name":"prueba.pdf","path":"/documents/clients/prueba.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":3,"parent":123456,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"}
        metadataOut = '{"status": "CHANGED", "parent": 123456, "user": "eyeos", "server_modified": "2013-03-08 10:36:41.997", "path": "/documents/clients/prueba.pdf", "id": 32565632156, "size": 775412, "mimetype": "application/pdf", "name": "prueba.pdf", "client_modified": "2013-03-08 10:36:41.997", "version": 3}'
        self.exerciseUpdateMetadata(metadataIn,metadataOut,True,fileId,name,parentId,data)

    """
    method: updateMetadata
    when: called
    with: accessTokenAndFileAndIdAndParent
    should: returnJsonMetadataMove
    """
    def test_updateMetadata_called_accessTokenAndFileAndIdAndParent_returnJsonMetadataMove(self):
        fileId = 32565632156;
        parentId = 789456
        data = {}
        data['parent'] = parentId
        metadataIn = {"name":"Client1.pdf","path":"/documents/clients/Client1.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":3,"parent":789456,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"}
        metadataOut = '{"status": "CHANGED", "parent": 789456, "user": "eyeos", "server_modified": "2013-03-08 10:36:41.997", "path": "/documents/clients/Client1.pdf", "id": 32565632156, "size": 775412, "mimetype": "application/pdf", "name": "Client1.pdf", "client_modified": "2013-03-08 10:36:41.997", "version": 3}'
        self.exerciseUpdateMetadata(metadataIn,metadataOut,True,fileId,None,parentId,data)

    """
    method: updateMetadata
    when: called
    with: accessTokenAndFileAndId
    should: returnJsonMetadataMove
    """
    def test_updateMetadata_called_accessTokenAndFileAndId_returnJsonMetadataMove(self):
        fileId = 32565632156;
        data = {}
        metadataIn = {"name":"Client1.pdf","path":"/documents/clients/Client1.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":3,"parent":295830,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"}
        metadataOut = '{"status": "CHANGED", "parent": 295830, "user": "eyeos", "server_modified": "2013-03-08 10:36:41.997", "path": "/documents/clients/Client1.pdf", "id": 32565632156, "size": 775412, "mimetype": "application/pdf", "name": "Client1.pdf", "client_modified": "2013-03-08 10:36:41.997", "version": 3}'
        self.exerciseUpdateMetadata(metadataIn,metadataOut,True,fileId,None,None,data)

    """
    method: updateMeta
    when: called
    with: accessTokenAndFolderAndIdNameAndParent
    should: returnJsonMetadataRename
    """
    def test_updateMetadata_called_accessTokenAndFolderAndIdAndNameAndParent_returnJsonMetadataRename(self):
        folderId = 9873615
        parentId = 32565632156
        name = "images"
        data = {}
        data['name'] = name
        data['parent'] = parentId
        metadataIn = {"name":"images","path":"/documents/images","id":9873615,"status":"NEW","version":1,"parent":None,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":False}
        metadataOut = '{"status": "NEW", "is_root": false, "version": 1, "name": "images", "parent": "null", "server_modified": "2013-03-08 10:36:41.997", "path": "/documents/images", "client_modified": "2013-03-08 10:36:41.997", "id": 9873615, "user": "eyeos"}'
        self.exerciseUpdateMetadata(metadataIn,metadataOut,False,folderId,name,parentId,data)

    """
    method: updateMetadata
    when: called
    with: accessTokenAndFolderAndIdAndParent
    should: returnJsonMetadataMove
    """
    def test_updateMetadata_called_accessTokenAndFolderAndIdAndParent_returnJsonMetadataMove(self):
        folderId = 9873615
        parentId = 32565632156
        data = {}
        data['parent'] = parentId
        metadataIn = {"name":"images","path":"/documents/images","id":9873615,"status":"NEW","version":1,"parent":None,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":False}
        metadataOut = '{"status": "NEW", "is_root": false, "version": 1, "name": "images", "parent": "null", "server_modified": "2013-03-08 10:36:41.997", "path": "/documents/images", "client_modified": "2013-03-08 10:36:41.997", "id": 9873615, "user": "eyeos"}'
        self.exerciseUpdateMetadata(metadataIn,metadataOut,False,folderId,None,parentId,data)

    """
    method: updateMetadata
    when: called
    with: accessTokenAndFolderAndId
    should: returnJsonMetadataRemove
    """
    def test_updateMetadata_called_accessTokenAndFolderAndId_returnJsonMetadataRemove(self):
        folderId = 9873615
        data = {}
        metadataIn = {"name":"images","path":"/documents/images","id":9873615,"status":"NEW","version":1,"parent":None,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":False}
        metadataOut = '{"status": "NEW", "is_root": false, "version": 1, "name": "images", "parent": "null", "server_modified": "2013-03-08 10:36:41.997", "path": "/documents/images", "client_modified": "2013-03-08 10:36:41.997", "id": 9873615, "user": "eyeos"}'
        self.exerciseUpdateMetadata(metadataIn,metadataOut,False,folderId,None,None,data)

    """
    method: updateMetadata
    when: called
    with: accessTokenAndFolderAndId
    should: returnException
    """
    def test_updateMetadata_called_accessTokenAndFolderAndId_returnException(self):
        folderId = 9873615
        data = {}
        metadataIn =  {"error":404, "description": "File or folder not found."}
        metadataOut = 'false'
        self.exerciseUpdateMetadata(metadataIn,metadataOut,False,folderId,None,None,data)

    """
      method: updateMetadata
      when: called
      with: accessTokenAndFolderAndId
      should: returnPermissionDenied
      """
    def test_updateMetadata_called_accessTokenAndFolderAndId_returnPermissionDenied(self):
        folderId = 9873615
        data = {}
        metadataIn =  {"error":403, "description": "Forbidden. The requester does not have permission to access the specified resource."}
        metadataOut = 403
        self.exerciseUpdateMetadata(metadataIn,metadataOut,False,folderId,None,None,data)

    """
    method: createMetadata
    when: called
    with: accessTokenAndFileAndNameAndParent
    should: returnJsonMetadata
    """
    def test_createMetadata_called_accessTokenAndFileAndNameAndParent_returnJsonMetadata(self):
        name = "Client1.pdf"
        parent = 32565632156
        data = {}
        data['name'] = name
        data['parent'] = parent
        metadataIn = {"name":"Client1.pdf","path":"/documents/clients/Client1.pdf","id":32565632156,"parent":-348534824681,"user":"eyeos"}
        metadataOut = '{"path": "/documents/clients/Client1.pdf", "user": "eyeos", "name": "Client1.pdf", "parent": -348534824681, "id": 32565632156}'
        self.exerciseCreateMetadata(metadataIn,metadataOut,True,name,parent,data)

    """
    method: createMetadata
    when: called
    with: accessTokenAndFileAndName
    should: returnJsonMetadata
    """
    def test_createMetadata_called_accessTokenAndFileAndName_returnJsonMetadata(self):
        name = "Client1.pdf"
        data = {}
        data['name'] = name
        metadataIn = {"name":"Client1.pdf","path":"/documents/clients/Client1.pdf","id":32565632156,"parent":-348534824681,"user":"eyeos"}
        metadataOut = '{"path": "/documents/clients/Client1.pdf", "user": "eyeos", "name": "Client1.pdf", "parent": -348534824681, "id": 32565632156}'
        self.exerciseCreateMetadata(metadataIn,metadataOut,True,name,None,data)


    """
    method: createMetadata
    when: called
    with: accessTokenAndFolderAndNameAndParent
    should: returnJsonMetadata
    """
    def test_createMetadata_called_accessTokenAndFolderAndNameAndParent_returnJsonMetadata(self):
        name = 'clients'
        parent = -348534824681
        data = {}
        data['name'] = name
        data['parent'] = parent
        metadataIn = {"name":"clients","path":"/documents/clients","id":9873615,"parent":-348534824681,"user":"eyeos"}
        metadataOut = '{"path": "/documents/clients", "user": "eyeos", "name": "clients", "parent": -348534824681, "id": 9873615}'
        self.exerciseCreateMetadata(metadataIn,metadataOut,False,name,parent,data)

    """
    method: createMetadata
    when: called
    with: accessTokenAndFolderAndName
    should: returnJsonMetadata
    """
    def test_createMetadata_called_accessTokenAndFolderAndName_returnJsonMetadata(self):
        name = 'clients'
        data = {}
        data['name'] = name
        metadataIn = {"name":"clients","path":"/clients","id":9873615,"parent":None,"user":"eyeos"}
        metadataOut = '{"path": "/clients", "user": "eyeos", "name": "clients", "parent": "null", "id": 9873615}'
        self.exerciseCreateMetadata(metadataIn,metadataOut,False,name,None,data)

    """
    method: createMetadata
    when: called
    with: accessTokenAndFolderAndName
    should: returnException
    """
    def test_createMetadata_called_accessTokenAndFolderAndName_returnException(self):
        name = None
        data = {}
        metadataIn =  {"error":400, "description": "Bad input parameter"}
        metadataOut = 'false'
        self.exerciseCreateMetadata(metadataIn,metadataOut,False,name,None,data)

    """
   method: createMetadata
   when: called
   with: accessTokenAndFolderAndName
   should: returnPermissionDenied
   """
    def test_createMetadata_called_accessTokenAndFolderAndName_returnPermissionDenied(self):
        name = None
        data = {}
        metadataIn =  {"error":403, "description": "Forbidden. The requester does not have permission to access the specified resource."}
        metadataOut = 403
        self.exerciseCreateMetadata(metadataIn,metadataOut,False,name,None,data)

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
    with: accessTokenAndId
    should: returnContent
    """
    def test_downloadFile_called_accessTokenAndId_returnContent(self):
        fileId = 123456
        data = 'Esto es una prueba'
        self.exerciseDonwloadFile(fileId,data,data)


    """
    method: downloadFile
    when: called
    with: accessTokenAndId
    should: returnException
    """
    def test_downloadFile_called_accessTokenAndId_returnException(self):
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
    with: accessTokenAndFileAndId
    should: returnJsonMetadata
    """
    def test_deleteMetadata_called_accessTokenAndFileAndId_returnJsonMetadata(self):
        fileId = 32565632156
        metadataIn = {"name":"Client1.pdf","path":"/documents/clients/Client1.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent":-348534824681,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"}
        metadataOut = '{"status": "DELETED", "parent": -348534824681, "user": "eyeos", "server_modified": "2013-03-08 10:36:41.997", "path": "/documents/clients/Client1.pdf", "id": 32565632156, "size": 775412, "mimetype": "application/pdf", "name": "Client1.pdf", "client_modified": "2013-03-08 10:36:41.997", "version": 3}'
        self.exerciseDeleteMetadata(metadataIn,metadataOut,True,fileId)

    """
    method: deleteMetadata
    when: called
    with: accessTokenAndFolderAndId
    should: returnJsonMetadata
    """
    def test_deleteMetadata_called_accessTokenAndFolderAndId_returnJsonMetadata(self):
        folderId = 9873615
        metadataIn = {"name":"clients","path":"/documents/clients","id":9873615,"status":"DELETED","version":3,"parent":-348534824681,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"}
        metadataOut = '{"status": "DELETED", "version": 3, "name": "clients", "parent": -348534824681, "server_modified": "2013-03-08 10:36:41.997", "path": "/documents/clients", "client_modified": "2013-03-08 10:36:41.997", "id": 9873615, "user": "eyeos"}'
        self.exerciseDeleteMetadata(metadataIn,metadataOut,False,folderId)

    """
    method: deleteMetadata
    when: called
    with: accessTokenAndFolderAndId
    should: returnException
    """
    def test_deleteMetadata_called_accessTokenAndFolderAndId_returnException(self):
        folderId = -1
        self.exerciseDeleteMetadata({"error":404, "description": "File or folder not found"},'false',False,folderId)

    """
    method: deleteMetadata
    when: called
    with: accessTokenAndFolderAndId
    should: returnException
    """
    def test_deleteMetadata_called_accessTokenAndFolderAndId_returnException(self):
        folderId = -1
        self.exerciseDeleteMetadata({"error":403, "description": "Forbidden. The requester does not have permission to access the specified resource."},403,False,folderId)

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
        oauth.put.assert_called_once_with(self.resourceurl + self.getResource(file) + "/" + str(id),data)
        self.assertEquals(metadataOut,result)

    def exerciseCreateMetadata(self,metadataIn,metadataOut,file,name,parent,data):
        oauth = self.createOauthSession()
        oauth.post = Mock()
        oauth.post.return_value = metadataIn
        result = self.oauthCredentials.createMetadata(oauth,file,name,parent)
        oauth.post.assert_called_once_with(self.resourceurl + self.getResource(file),data)
        self.assertEquals(metadataOut,result)

    def exerciseUploadFile(self,fileId,check,returnValue):
        path = "prueba.txt"
        self.file.write("Esto es una prueba")
        self.file.close()
        self.file = open(path,"r")
        oauth = self.createOauthSession()
        oauth.post = Mock()
        oauth.post.return_value = returnValue
        result = self.oauthCredentials.uploadFile(oauth,fileId,path)
        oauth.post.assert_called_once_with(self.resourceurl + "file/" + str(fileId) + "/data",self.file.read())
        self.assertEquals(check,result)

    def exerciseDonwloadFile(self,fileId,check,returnValue):
        oauth = self.createOauthSession()
        oauth.get = Mock()
        oauth.get.return_value = returnValue
        result = self.oauthCredentials.downloadFile(oauth,fileId)
        oauth.get.assert_called_once_with(self.resourceurl + "file/" + str(fileId) + "/data")
        self.assertEquals(check,result)

    def exerciseDeleteMetadata(self,metadataIn,metadataOut,file,fileId):
        oauth = self.createOauthSession()
        oauth.delete = Mock()
        oauth.delete.return_value = metadataIn
        result = self.oauthCredentials.deleteMetadata(oauth,file,fileId)
        oauth.delete.assert_called_once_with(self.resourceurl + self.getResource(file) + "/" + str(fileId))
        self.assertEquals(metadataOut,result)

    def getResource(self,file):
        resource = "folder"
        if file:
            resource = "file"
        return resource

