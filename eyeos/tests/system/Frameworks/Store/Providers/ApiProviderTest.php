<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/03/14
 * Time: 10:20
 */

class ApiProviderTest extends PHPUnit_Framework_TestCase
{
    private $accessorProviderMock;
    private $daoMock;
    private $sut;
    private $url;
    private $token;

    public function setUp()
    {
        $this->accessorProviderMock = $this->getMock('AccessorProvider');
        $this->daoMock = $this->getMock('EyeosDAO');
        $this->sut = new ApiProvider($this->accessorProviderMock,$this->daoMock);
        $this->url = "https://cloudspaces.urv.cat:8080/v1/AUTH_6d3b65697d5048d5aaffbb430c9dbe6a";
        $this->token = '555555';
    }

    public function tearDown()
    {

    }

    /**
     *method: getMetadata
     * when: called
     * with: urlAndToken
     * should: returnCorrectData
     */
    public function test_getMetadata_called_urlAndToken_returnCorrectData()
    {
        $metadataIn = '{"file_id":null,"parent_file_id":null,"filename":"root","path":null,"is_folder":true,"status":null,"user":null,"version":null,"checksum":null,"size":null,"mimetype":null,"is_root":true,"contents":[{"file_id":-7755273878059615652,"parent_file_id":null,"filename":"helpFolder","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-11 15:40:45.784","client_modified":"2013-11-11 15:40:45.784","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":-5201053391767961053,"parent_file_id":null,"filename":"New File.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-11-26 16:00:06.308","client_modified":"2013-11-26 16:00:06.307","user":"web","version":20,"checksum":122290589,"size":8,"mimetype":"application/x-empty","chunks":[]},{"file_id":-3378160743781590173,"parent_file_id":null,"filename":"Bienvenido.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-12 13:49:46.262","client_modified":"2013-12-12 13:49:46.261","user":"web","version":6,"checksum":1705643629,"size":50,"mimetype":"application/x-empty","chunks":[]},{"file_id":-2705812544177220237,"parent_file_id":null,"filename":"images","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-10-07 17:58:02.213","client_modified":"2013-07-10 17:42:19.0","user":"ast_cotes201310071757","version":1,"checksum":0,"size":4096,"mimetype":"inode/directory","is_root":false},{"file_id":-1478707423980200270,"parent_file_id":null,"filename":"Cloudspaces_trial","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-12-10 22:53:21.052","client_modified":"2013-12-10 22:53:21.052","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":819819698545290447,"parent_file_id":null,"filename":"Documents","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-18 13:48:51.269","client_modified":"2013-11-18 13:48:51.269","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":1977451714816609267,"parent_file_id":null,"filename":"test.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-03 12:04:49.392","client_modified":"2013-12-03 12:04:49.391","user":"web","version":5,"checksum":94306754,"size":5,"mimetype":"text/plain","chunks":[]},{"file_id":3894030578176289733,"parent_file_id":null,"filename":"hola","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-18 12:11:35.656","client_modified":"2013-11-18 12:11:35.656","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":6377614534029818696,"parent_file_id":null,"filename":"testtt","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-15 13:00:00.073","client_modified":"2013-11-15 13:00:00.073","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false}]}';
        $metadataOut = '{"file_id":"null","parent_file_id":"null","filename":"root","path":null,"is_folder":true,"status":null,"user":null,"version":null,"checksum":null,"size":null,"mimetype":null,"is_root":true,"contents":[{"file_id":-7755273878059615652,"parent_file_id":"null","filename":"helpFolder","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-11 15:40:45.784","client_modified":"2013-11-11 15:40:45.784","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":-5201053391767961053,"parent_file_id":"null","filename":"New File.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-11-26 16:00:06.308","client_modified":"2013-11-26 16:00:06.307","user":"web","version":20,"checksum":122290589,"size":8,"mimetype":"application/x-empty","chunks":[]},{"file_id":-3378160743781590173,"parent_file_id":"null","filename":"Bienvenido.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-12 13:49:46.262","client_modified":"2013-12-12 13:49:46.261","user":"web","version":6,"checksum":1705643629,"size":50,"mimetype":"application/x-empty","chunks":[]},{"file_id":-2705812544177220237,"parent_file_id":"null","filename":"images","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-10-07 17:58:02.213","client_modified":"2013-07-10 17:42:19.0","user":"ast_cotes201310071757","version":1,"checksum":0,"size":4096,"mimetype":"inode/directory","is_root":false},{"file_id":-1478707423980200270,"parent_file_id":"null","filename":"Cloudspaces_trial","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-12-10 22:53:21.052","client_modified":"2013-12-10 22:53:21.052","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":819819698545290447,"parent_file_id":"null","filename":"Documents","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-18 13:48:51.269","client_modified":"2013-11-18 13:48:51.269","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":1977451714816609267,"parent_file_id":"null","filename":"test.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-03 12:04:49.392","client_modified":"2013-12-03 12:04:49.391","user":"web","version":5,"checksum":94306754,"size":5,"mimetype":"text/plain","chunks":[]},{"file_id":3894030578176289733,"parent_file_id":"null","filename":"hola","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-18 12:11:35.656","client_modified":"2013-11-18 12:11:35.656","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":6377614534029818696,"parent_file_id":"null","filename":"testtt","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-15 13:00:00.073","client_modified":"2013-11-15 13:00:00.073","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false}]}';
        $this->exerciseMetadata($metadataIn,$metadataOut);
    }

    /**
     *method: getMetadata
     * when: called
     * with: urlAndTokenAndFileid
     * should: returnCorrectData
     */
    public function test_getMetadata_called_urlAndTokenAndFileid_returnCorrectData()
    {
        $fileId = -1478707423980200270;
        $metadataIn = '{"file_id":-1478707423980200270,"parent_file_id":null,"filename":"Cloudspaces_trial","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-12-10 22:53:21.052","client_modified":"2013-12-10 22:53:21.052","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false,"contents":[{"file_id":2681230491652302322,"parent_file_id":-1478707423980200270,"filename":"Cloudspaces demo text.txt","path":"/Cloudspaces_trial/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-10 22:54:59.665","client_modified":"2013-12-10 22:54:59.664","user":"web","version":2,"checksum":3674040746,"size":299,"mimetype":"text/plain","chunks":[]},{"file_id":-2096699531480976652,"parent_file_id":-1478707423980200270,"filename":"Authentication.jpg","path":"/Cloudspaces_trial/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-10 22:55:56.393","client_modified":"2013-12-10 22:55:56.392","user":"web","version":2,"checksum":2876523746,"size":574156,"mimetype":"image/jpeg","chunks":[]}]}';
        $metadataOut = '{"file_id":-1478707423980200270,"parent_file_id":"null","filename":"Cloudspaces_trial","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-12-10 22:53:21.052","client_modified":"2013-12-10 22:53:21.052","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false,"contents":[{"file_id":2681230491652302322,"parent_file_id":-1478707423980200270,"filename":"Cloudspaces demo text.txt","path":"/Cloudspaces_trial/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-10 22:54:59.665","client_modified":"2013-12-10 22:54:59.664","user":"web","version":2,"checksum":3674040746,"size":299,"mimetype":"text/plain","chunks":[]},{"file_id":-2096699531480976652,"parent_file_id":-1478707423980200270,"filename":"Authentication.jpg","path":"/Cloudspaces_trial/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-10 22:55:56.393","client_modified":"2013-12-10 22:55:56.392","user":"web","version":2,"checksum":2876523746,"size":574156,"mimetype":"image/jpeg","chunks":[]}]}';
        $this->exerciseMetadata($metadataIn,$metadataOut,$fileId);
    }

    /**
     *method: createFile
     * when: called
     * with: urlAndTokenAndFilenameAndFileAndFilesize
     * should: returnCorrect
     */
    public function test_createFile_called_urlAndTokenAndFilenameAndFileAndFilesize_returnCorrect()
    {
        $metadata = '{"status": "CHANGED", "mimetype": "application/x-empty", "parent_file_version": null, "parent_file_id": "null", "root_id": "stacksync", "server_modified": "Fri Mar 07 11:55:32 CET 2014", "checksum": 694355124, "client_modified": "Fri Mar 07 11:55:32 CET 2014", "filename": "pruebas.txt", "version": 7, "file_id": -7705621709365758847, "is_folder": false, "chunks": ["A6960EF3C0B501B4C338DE32A6C8E9A5004FE350"], "path": "/hola/", "size": 15, "user": "web"}';
        $this->exerciseCreateFile($metadata);
    }

    /**
     *method: createFile
     * when: called
     * with: urlAndTokenAndFilenameAndFileAndFilesizeAndParent
     * should: returnCorrect
     */
    public function test_createFile_called_urlAndTokenAdnFilenameAndFileAndFilesizeAndParent_returnCorrect()
    {
        $parent = '3894030578176289733';
        $metadata = '{"status": "CHANGED", "mimetype": "application/x-empty", "parent_file_version": null, "parent_file_id": 3894030578176289733, "root_id": "stacksync", "server_modified": "Fri Mar 07 11:55:32 CET 2014", "checksum": 694355124, "client_modified": "Fri Mar 07 11:55:32 CET 2014", "filename": "pruebas.txt", "version": 7, "file_id": -7705621709365758847, "is_folder": false, "chunks": ["A6960EF3C0B501B4C338DE32A6C8E9A5004FE350"], "path": "/hola/", "size": 15, "user": "web"}';
        $this->exerciseCreateFile($metadata,$parent);
    }

    /**
     *method: createFolder
     * when: called
     * with: urlAndTokenAndFoldername
     * should: returnCorrect
     */
    public function test_createFolder_called_urlAndTokenAndFoldername_returnCorrect()
    {
        $metadata = '{"status": "NEW", "mimetype": "inode/directory", "parent_file_version": "", "parent_file_id": "null", "root_id": "stacksync", "server_modified": "Fri Mar 07 17:22:51 CET 2014", "checksum": 0, "client_modified": "Fri Mar 07 17:22:51 CET 2014", "filename": "TestFolder", "version": 1, "file_id": "-3243347967282172526", "is_folder": true, "path": "/", "size": 0, "user": "web"}';
        $this->exerciseCreateFolder($metadata);
    }

    /**
     *method: createFolder
     * when: called
     * with: urlAndTokenAndFoldernameAndParent
     * should: returnCorrect
     */
    public function test_createFolder_called_urlAndTokenAndFoldernameAndParent_returnCorrect()
    {
        $metadata = '{"status": "NEW", "mimetype": "inode/directory", "parent_file_version": "", "parent_file_id": "3894030578176289733", "root_id": "stacksync", "server_modified": "Fri Mar 07 17:22:51 CET 2014", "checksum": 0, "client_modified": "Fri Mar 07 17:22:51 CET 2014", "filename": "TestFolder", "version": 1, "file_id": -3243347967282172526, "is_folder": true, "path": "/", "size": 0, "user": "web"}';
        $parent = '3894030578176289733';
        $this->exerciseCreateFolder($metadata,$parent);
    }


    /**
     *method: deleteComponent
     * when: called
     * with: idFile
     * should: returnCorrect
     */
    public function test_deleteComponent_called_idFile_returnCorrect()
    {
        $fileId = "-5763789518128894388";
        $metadata = '{"status": "DELETED", "mimetype": "application/x-empty", "parent_file_version": "", "parent_file_id": "null", "root_id": "stacksync", "server_modified": "Mon Mar 10 14:10:12 CET 2014", "checksum": 701498804, "client_modified": "Mon Mar 10 14:10:12 CET 2014", "filename": "New File 1.txt", "version": 12, "file_id": "-5763789518128894388", "is_folder": false, "chunks": [], "path": "/", "size": 14, "user": "web"}';
        $this->exerciseDeleteComponent($fileId,$metadata);
    }

    /**
     *method: deleteComponent
     * when: called
     * with: idFolder
     * should: returnCorrect
     */
    public function test_deleteComponent_called_idFolder_returnCorrect()
    {
        $folderId = "-5763789518128894388";
        $metadata = '{"status": "DELETED", "mimetype": "application/x-empty", "parent_file_version": "", "parent_file_id": "null", "root_id": "stacksync", "server_modified": "Mon Mar 10 14:10:12 CET 2014", "checksum": 701498804, "client_modified": "Mon Mar 10 14:10:12 CET 2014", "filename": "New File 1.txt", "version": 12, "file_id": "-5763789518128894388", "is_folder": true, "chunks": [], "path": "/", "size": 14, "user": "web"}';
        $this->exerciseDeleteComponent($folderId,$metadata);
    }

    /**
     *method: downloadFile
     * when: called
     * with: idFile
     * should: returnContent
     */
    public function test_downloadFile_called_idFile_returnContent()
    {
        $fileId = "5665565566";
        $content = "Es una prueba";
        $this->accessorProviderMock->expects($this->once())
            ->method('sendMessage')
            ->will($this->returnValue($content));

        $result = $this->sut->downloadFile($this->url,$this->token,$fileId);
        $this->assertEquals($content,$result);
    }

    /**
     * method: getToken
     * when: called
     * with: user
     * should: returnToken
     */
    public function test_getToken_called_user_returnToken()
    {
        $userId = 'eyeID_EyeosUser_453';
        $token = new Token();
        $token->setUserId($userId);
        $this->daoMock->expects($this->once())
            ->method("read")
            ->with($token);

        $this->sut->getToken($userId);
    }

    /**
     * method: insertToken
     * when: called
     * with: token
     * should: returnCorrect
     */
    public function test_insertToken_called_token_returnCorrect()
    {
        $token = new Token();
        $token->setUserID('eyeID_EyeosUser_453');
        $token->setTkey('ABCD');
        $token->setTsecret('EFGH');
        $this->daoMock->expects($this->once())
            ->method('create')
            ->with($token);

        $result = $this->sut->insertToken($token);
        $this->assertEquals(true,$result);
    }

    private function exerciseMetadata($metadataIn,$metadatOut,$fileId = NULL)
    {
        $this->accessorProviderMock->expects($this->once())
            ->method('sendMessage')
            ->will($this->returnValue($metadataIn));

        $result = $this->sut->getMetadata($this->url,$this->token,$fileId);
        $this->assertEquals(json_decode($metadatOut),$result);
    }

    private function exerciseCreateFile($metadata,$parent = NULL)
    {
        $path = "resources/pruebas.txt";
        $file = fopen($path, "r");
        $filename = "pruebas.txt";

        $this->accessorProviderMock->expects($this->once())
            ->method('sendMessage')
            ->will($this->returnValue($metadata));

        $result = $this->sut->createFile($this->url,$this->token,$filename,$file,filesize($path),$parent);
        $this->assertEquals(json_decode($metadata),$result);
        fclose($file);
    }

    private function exerciseCreateFolder($metadata,$parent = NULL)
    {
        $folderName = 'TestFolder';
        $this->accessorProviderMock->expects($this->once())
            ->method('sendMessage')
            ->will($this->returnValue($metadata));

        $result = $this->sut->createFolder($this->url,$this->token,$folderName,$parent);
        $this->assertEquals(json_decode($metadata),$result);
    }

    private function exerciseDeleteComponent($idComponent,$metadata)
    {
        $this->accessorProviderMock->expects($this->once())
            ->method('sendMessage')
            ->will($this->returnValue($metadata));

        $result = $this->sut->deleteComponent($this->url,$this->token,$idComponent);
        $this->assertEquals(true,$result);
    }
}

?>