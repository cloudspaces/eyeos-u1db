<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28/05/14
 * Time: 15:59
 */

class ApiManagerTest extends PHPUnit_Framework_TestCase
{
    private $accessorProviderMock;
    private $apiProviderMock;
    private $filesProviderMock;
    private $sut;
    private $calendarManagerMock;
    private $credentials;

    public function setUp()
    {
        $this->accessorProviderMock = $this->getMock('AccessorProvider');
        $this->apiProviderMock = $this->getMock('ApiProvider');
        $this->filesProviderMock = $this->getMock("FilesProvider");
        $this->calendarManagerMock = $this->getMock('ICalendarManager');
        $this->sut = new ApiManager($this->accessorProviderMock,$this->apiProviderMock,$this->filesProviderMock,$this->calendarManagerMock);
        $this->credentials = '{"credentials":{"token_key":"1234","token_secret":"ABCD","consumer_key":"keySebas","consumer_secret":"secretSebas"},"request_token":{"key":"HIJK","secret":"ABCD"},"verifier":"verifier"}';
    }

    public function tearDown()
    {
        $this->accessorProviderMock = null;
        $this->apiProviderMock = null;
        $this->filesProviderMock = null;
        $this->calendarManagerMock = null;
        $this->sut = null;
    }

    /**
     * method: getProcessDataU1db
     * when: called
     * with: paramTextJson
     * should: returnCorrect
     */
    /*public function test_getProcessDataU1db_called_paramTextJson_returnCorrect()
    {
        $json = '{"type":"select","lista":[{"file_id":5}]}';
        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessDataU1db')
            ->with($json)
            ->will($this->returnValue('[{"status": "NEW", "mimetype": "inode/directory", "parent_file_id": "null", "checksum": 0, "client_modified": "2013-11-11 15:40:45.784", "filename": "helpFile", "is_root": false, "version": 1, "file_id": 5, "server_modified": "2013-11-11 15:40:45.784", "path": "/", "user": "web", "is_folder": false, "size": 0}, {"status": "NEW", "mimetype": "inode/directory", "parent_file_id": "null", "checksum": 0, "client_modified": "2013-11-11 15:40:45.784", "filename": "helpFile", "is_root": false, "version": 1, "file_id": -7755273878059615652, "server_modified": "2013-11-11 15:40:45.784", "path": "/", "user": "web", "is_folder": false, "size": 0}]'));

        $this->sut->getProcessDataU1db($json);
    }*/
}

?>