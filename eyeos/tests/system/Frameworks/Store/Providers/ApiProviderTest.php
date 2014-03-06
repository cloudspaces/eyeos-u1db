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
    private $sut;

    public function setUp()
    {
        $this->accessorProviderMock = $this->getMock('AccessorProvider');
        $this->sut = new ApiProvider($this->accessorProviderMock);
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

    public function test_getMetadata_called_urlAndTokenAndFileid_returnCorrectData()
    {
        $fileId = -1478707423980200270;
        $metadataIn = '{"file_id":-1478707423980200270,"parent_file_id":null,"filename":"Cloudspaces_trial","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-12-10 22:53:21.052","client_modified":"2013-12-10 22:53:21.052","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false,"contents":[{"file_id":2681230491652302322,"parent_file_id":-1478707423980200270,"filename":"Cloudspaces demo text.txt","path":"/Cloudspaces_trial/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-10 22:54:59.665","client_modified":"2013-12-10 22:54:59.664","user":"web","version":2,"checksum":3674040746,"size":299,"mimetype":"text/plain","chunks":[]},{"file_id":-2096699531480976652,"parent_file_id":-1478707423980200270,"filename":"Authentication.jpg","path":"/Cloudspaces_trial/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-10 22:55:56.393","client_modified":"2013-12-10 22:55:56.392","user":"web","version":2,"checksum":2876523746,"size":574156,"mimetype":"image/jpeg","chunks":[]}]}';
        $metadataOut = '{"file_id":-1478707423980200270,"parent_file_id":"null","filename":"Cloudspaces_trial","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-12-10 22:53:21.052","client_modified":"2013-12-10 22:53:21.052","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false,"contents":[{"file_id":2681230491652302322,"parent_file_id":-1478707423980200270,"filename":"Cloudspaces demo text.txt","path":"/Cloudspaces_trial/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-10 22:54:59.665","client_modified":"2013-12-10 22:54:59.664","user":"web","version":2,"checksum":3674040746,"size":299,"mimetype":"text/plain","chunks":[]},{"file_id":-2096699531480976652,"parent_file_id":-1478707423980200270,"filename":"Authentication.jpg","path":"/Cloudspaces_trial/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-10 22:55:56.393","client_modified":"2013-12-10 22:55:56.392","user":"web","version":2,"checksum":2876523746,"size":574156,"mimetype":"image/jpeg","chunks":[]}]}';
        $this->exerciseMetadata($metadataIn,$metadataOut,$fileId);
    }

    private function exerciseMetadata($metadataIn,$metadatOut,$fileId = NULL)
    {
        $url = "https://cloudspaces.urv.cat:8080/v1/AUTH_6d3b65697d5048d5aaffbb430c9dbe6a";
        $tokenId = '555555';

        $this->accessorProviderMock->expects($this->once())
            ->method('sendMessage')
            ->will($this->returnValue($metadataIn));

        $result = $this->sut->getMetadata($url,$tokenId,$fileId);
        $this->assertEquals(json_decode($metadatOut),$result);
    }


}

?>