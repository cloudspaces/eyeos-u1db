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
    private $token;
    private $path;
    private $user;

    public function setUp()
    {
        $this->accessorProviderMock = $this->getMock('AccessorProvider');
        $this->apiProviderMock = $this->getMock('ApiProvider');
        $this->filesProviderMock = $this->getMock("FilesProvider");
        $this->sut = new ApiManager($this->accessorProviderMock,$this->apiProviderMock,$this->filesProviderMock);
        $this->credentials = '{"credentials":{"token_key":"1234","token_secret":"ABCD","consumer_key":"keySebas","consumer_secret":"secretSebas"},"request_token":{"key":"HIJK","secret":"ABCD"},"verifier":"verifier"}';
        $this->token = new stdClass();
        $this->token->key = '1234';
        $this->token->secret = 'ABCD';
        $this->path = "home://~eyeos/Stacksync";
        $this->user = 'eyeID_EyeosUser_2';
    }

    public function tearDown()
    {
        $this->accessorProviderMock = null;
        $this->apiProviderMock = null;
        $this->filesProviderMock = null;
        $this->calendarManagerMock = null;
        $this->sut = null;
        $this->token = null;
    }

    /**
     *method: getMetadata
     * when: called
     * with: tokenAndIdAndPathAndUser
     * should: calledU1dbWithoutData
     */
    public function test_getMetadata_called_tokenAndIdAndPathAndUser_calledU1dbWithoutData()
    {
        $id = 'root';
        $isfile = false;
        $contents = true;
        $u1dbIn = new stdClass();
        $u1dbIn->type = 'select';
        $u1dbIn->lista = array();
        $file = new stdClass();
        $file->id = "null";
        $file->user_eyeos = $this->user;
        $file->path = '/';
        array_push($u1dbIn->lista,$file);
        $metadata = '{"filename":"root","id":"null","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,"contents":[{"filename":"Client1.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false},{"filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true},{"filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false}]}';
        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->token,$isfile,$id,$contents)
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('[]'));

        $this->filesProviderMock->expects($this->at(0))
            ->method('createFile')
            ->with($this->path . "/client2",true)
            ->will($this->returnValue(true));

        $this->filesProviderMock->expects($this->at(1))
            ->method('createFile')
            ->with($this->path . "/Client3.pdf",false)
            ->will($this->returnValue(true));

        $u1dbIn = new stdClass();
        $u1dbIn->type = 'insert';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"user_eyeos":"' . $this->user . '","filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/"}');
        array_push($u1dbIn->lista,$metadata);

        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('true'));

        $u1dbIn = new stdClass();
        $u1dbIn->type = 'insert';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"user_eyeos":"' . $this->user . '","filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false,"path":"/"}');
        array_push($u1dbIn->lista,$metadata);

        $this->accessorProviderMock->expects($this->at(2))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('true'));

        $u1dbIn = new stdClass();
        $u1dbIn->type = 'insert';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"user_eyeos":"' . $this->user . '","filename":"root","id":"null","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,"path":"null"}');
        array_push($u1dbIn->lista,$metadata);

        $this->accessorProviderMock->expects($this->at(3))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('true'));

        $this->sut->getMetadata($this->token,$id,$this->path,$this->user);
    }


    /**
     *method: getMetadata
     * when: called
     * with: tokenAndIdAndPathAndUser
     * should: calledU1dbSameData
     */
    public function test_getMetadata_called_tokenAndIdAndPathAndUser_calledU1dbSameData()
    {
        $id = 'root';
        $isfile = false;
        $contents = true;
        $u1dbIn = new stdClass();
        $u1dbIn->type = 'select';
        $u1dbIn->lista = array();
        $file = new stdClass();
        $file->id = "null";
        $file->user_eyeos = $this->user;
        $file->path = '/';
        array_push($u1dbIn->lista,$file);
        $metadata = '{"filename":"root","id":"null","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,"contents":[{"filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true},{"filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false}]}';
        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->token,$isfile,$id,$contents)
            ->will($this->returnValue(json_decode($metadata)));

        $u1dbOut = '[{"user_eyeos":"' . $this->user . '","filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/"},
                    {"user_eyeos":"' . $this->user . '","filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false,,"path":"/"},
                    {"user_eyeos":"' . $this->user . '","filename":"root","id":"null","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,,"path":"null"}]';
        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue($u1dbOut));

        $this->filesProviderMock->expects($this->never())
            ->method('createFile')
            ->will($this->returnValue(true));

        $this->filesProviderMock->expects($this->never())
            ->method('deleteFile')
            ->will($this->returnValue(true));

        $this->sut->getMetadata($this->token,$id,$this->path,$this->user);
    }

    /**
     *method: getMetadata
     * when: called
     * with: tokenAndIdAndPathAndUser
     * should: calledU1dbSameData
     */
    public function test_getMetadata_called_tokenAndIdAndPathAndUser_calledDistinctData()
    {
        $id = 'root';
        $isfile = false;
        $contents = true;
        $u1dbIn = new stdClass();
        $u1dbIn->type = 'select';
        $u1dbIn->lista = array();
        $file = new stdClass();
        $file->id = "null";
        $file->user_eyeos = $this->user;
        $file->path = "/";
        array_push($u1dbIn->lista,$file);
        $metadata = '{"filename":"root","id":"null","status":"NEW","version":1,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,
                    "contents":[
                        {"filename":"Client1.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false},
                        {"filename":"client","id":334254755856,"size":775412,"status":"DELETED","version":2,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true},
                        {"filename":"provider","id":885526111,"size":775412,"status":"NEW","version":1,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true},
                        {"filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true}
                     ]}';
        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->token,$isfile,$id,$contents)
            ->will($this->returnValue(json_decode($metadata)));

        $u1dbOut = '[{"user_eyeos":"' . $this->user . '","filename":"root","id":"null","status":"NEW","version":1,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,"path":"null"},
                    {"user_eyeos":"' . $this->user . '","filename":"client","id":334254755856,"size":775412,"status":"NEW","version":1,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/"},
                    {"user_eyeos":"' . $this->user . '","filename":"client1","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"NEW","version":1,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/"},
                    {"user_eyeos":"' . $this->user . '","filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false,"path":"/"}]';
        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue($u1dbOut));

        $u1dbIn->type = 'deleteFolder';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"user_eyeos":"' . $this->user . '","filename":"client","id":334254755856,"size":775412,"status":"DELETED","version":2,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/"}');
        array_push($u1dbIn->lista,$metadata);
        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue($u1dbOut));

        $this->filesProviderMock->expects($this->at(0))
            ->method('deleteFile')
            ->with($this->path . '/client', true)
            ->will($this->returnValue(true));

        $this->filesProviderMock->expects($this->at(1))
            ->method('createFile')
            ->with($this->path . '/provider', true)
            ->will($this->returnValue(true));

        $this->filesProviderMock->expects($this->at(2))
            ->method('renameFile')
            ->with($this->path . '/client1', 'client2')
            ->will($this->returnValue(true));

        $u1dbIn->type = 'update';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"user_eyeos":"' . $this->user . '","filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/"}');
        array_push($u1dbIn->lista,json_decode('{"parent_old":"null"}'));
        array_push($u1dbIn->lista,$metadata);
        $this->accessorProviderMock->expects($this->at(3))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue($u1dbOut));

        $this->filesProviderMock->expects($this->at(3))
            ->method('deleteFile')
            ->with($this->path . '/Client3.pdf', false)
            ->will($this->returnValue(true));

        $u1dbIn->type = 'deleteFolder';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"user_eyeos":"' . $this->user . '","filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false,"path":"/"}');
        array_push($u1dbIn->lista,$metadata);
        $this->accessorProviderMock->expects($this->at(4))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue($u1dbOut));

        $this->sut->getMetadata($this->token,$id,$this->path,$this->user);
    }


    /**
     * method: getMetadata
     * when: called
     * with: tokenAndIdAndPathAndUser
     * should: returnException
     */
    public function test_getMetadata_called_tokenAndIdAndPathAndUser_returnException()
    {
        $id = 11165632156;
        $metadata = '{"error":404}';
        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->token,false,$id,true)
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $this->sut->getMetadata($this->token,$id,$this->path,$this->user);

    }

    /**
     * method: getMetadata
     * when: called
     * with: tokenAndIdAndPathAndUser
     * should: returnPermissionDenied
     */
    public function test_getMetadata_called_tokenAndIdAndPathAndUser_returnPermissionDenied()
    {
        $id = 11165632156;
        $metadata = '{"error":403}';
        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->token,false,$id,true)
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $this->sut->getMetadata($this->token,$id,$this->path,$this->user);

    }

    /**
     * method: getSkel
     * when: called
     * with: tokenAndIsFileAndIdAndMetadatas
     * should: callMetadataFileApiStore
     */
    public function test_getSkel_called_tokenAndIsFileAndIdAndMetadatas_callMetadataFileApiStore()
    {
        $id = 142555444;
        $metadatas = array();
        $path = '/documents/';
        $metadata = '{"filename":"Client1.pdf","id":142555444,"size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent_id":32565632156,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false}';
        $newmetadata = json_decode($metadata);
        $newmetadata->path = $path;
        $expected = array($newmetadata);
        $this->apiProviderMock->expects($this->once())
            ->method('getMetadata')
            ->with($this->token,true,$id)
            ->will($this->returnValue(json_decode($metadata)));

        $this->sut->getSkel($this->token,true,$id,$metadatas,$path);
        $this->assertEquals($expected,$metadatas);
    }

    /**
     * method: getSkel
     * when: called
     * with:tokenAndIsFileAndIdAndMetadatas
     * should: callMetadataFileApiStore
     */
    public function test_getSkel_called_tokenAndIsFolderAndIdAndMetadatas_callMetadataFolderApiStore()
    {
        $id = -8090905582480578692;
        $metadatas = array();
        $path = '/';
        $metadataFile2 = '{"filename":"Client1.pdf","id":142555444,"size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent_id":32565632156,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false}';
        $metadataFile = '{"id":32565632156,"parent_id":-8090905582480578692,"filename":"a","is_folder":true,"status":"NEW","server_modified":"2014-03-11 14:22:45.757","client_modified":"2014-03-11 14:22:45.757","user":"web","version":1,"checksum":589445744,"size":166,"mimetype":"text/plain","chunks":[],"is_root":false,
                          "contents":[
                                {"filename":"Client1.pdf","id":142555444,"size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent_id":32565632156,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false}
                         ]}';
        $metadata='{"id":-8090905582480578692,"parent_id":null,"filename":"Cloudspaces","is_folder":true,"status":"NEW","server_modified":"2014-03-11 14:22:45.757","client_modified":"2014-03-11 14:22:45.757","user":"web","version":1,"checksum":589445744,"size":166,"mimetype":"text/plain","chunks":[],"is_root":false,
                    "contents":[
                        {"id":32565632156,"parent_id":-8090905582480578692,"filename":"a","is_folder":true,"status":"NEW","server_modified":"2014-03-11 14:22:45.757","client_modified":"2014-03-11 14:22:45.757","user":"web","version":1,"checksum":589445744,"size":166,"mimetype":"text/plain","chunks":[],"is_root":false}
                    ]}';

        $expected = array();
        $data2 = json_decode($metadataFile2);
        $data2->path = "/Cloudspaces/a/";
        array_push($expected,$data2);
        $data1 = json_decode($metadataFile);
        unset($data1->contents);
        $data1->path = "/Cloudspaces/";
        array_push($expected,$data1);
        $data = json_decode($metadata);
        $data->path = "/";
        unset($data->contents);
        array_push($expected,$data);


        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->token,false,$id,true)
            ->will($this->returnValue(json_decode($metadata)));

        $this->apiProviderMock->expects($this->at(1))
            ->method('getMetadata')
            ->with($this->token,false,32565632156,true)
            ->will($this->returnValue(json_decode($metadataFile)));

        $this->apiProviderMock->expects($this->at(2))
            ->method('getMetadata')
            ->with($this->token,true,142555444,null)
            ->will($this->returnValue(json_decode($metadataFile2)));

        $this->sut->getSkel($this->token,false,$id,$metadatas,$path);
        $this->assertEquals($expected,$metadatas);
    }

    /**
     * method: getSkel
     * when: called
     * with:tokenAndIsFileAndIdAndMetadatas
     * should: returnPermissionDenied
     */
    public function test_getSkel_called_tokenAndIsFolderAndIdAndMetadatas_returnPermissionDenied()
    {
        $metadata='{"id":-8090905582480578692,"parent_id":null,"filename":"Cloudspaces","is_folder":true,"status":"NEW","server_modified":"2014-03-11 14:22:45.757","client_modified":"2014-03-11 14:22:45.757","user":"web","version":1,"checksum":589445744,"size":166,"mimetype":"text/plain","chunks":[],"is_root":false,
                    "contents":[
                        {"id":32565632156,"parent_id":-8090905582480578692,"filename":"a","is_folder":true,"status":"NEW","server_modified":"2014-03-11 14:22:45.757","client_modified":"2014-03-11 14:22:45.757","user":"web","version":1,"checksum":589445744,"size":166,"mimetype":"text/plain","chunks":[],"is_root":false}
                    ]}';

        $metadataError = '{"error":403}';
        $id = -8090905582480578692;
        $path = '/';
        $metadatas = array();
        $expected = array();
        array_push($expected,json_decode($metadataError));
        array_push($expected,json_decode('{"id":-8090905582480578692,"parent_id":null,"filename":"Cloudspaces","is_folder":true,"status":"NEW","server_modified":"2014-03-11 14:22:45.757","client_modified":"2014-03-11 14:22:45.757","user":"web","version":1,"checksum":589445744,"size":166,"mimetype":"text/plain","chunks":[],"is_root":false,"path":"/"}'));

        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->token,false,$id,true)
            ->will($this->returnValue(json_decode($metadata)));

        $this->apiProviderMock->expects($this->at(1))
            ->method('getMetadata')
            ->with($this->token,false,32565632156,true)
            ->will($this->returnValue(json_decode($metadataError)));

        $this->sut->getSkel($this->token,false,$id,$metadatas,$path);
        $this->assertEquals($expected,$metadatas);
    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndUserAndIsFileAndNameAndParentIdAndPathAndPathAbsolute
     * should: calledStackSyncNoDataInsertMetadata
     */
    public function test_createMetadata_called_tokenAndUserAndIsFileAndNameAndParentIdAndPathAndPathAbsolute_calledStackSyncNoDataInsertMetadata()
    {
        $name = "client.pdf";
        $parent_id = "null";
        $path = '/';
        $pathabsolute = '/home/eyeos/' . $name;
        $metadataOut = '{"id":32565632156,"parent_id":"null","filename":"root","is_folder":true,"status":"NEW","server_modified":"2014-03-11 14:22:45.757","client_modified":"2014-03-11 14:22:45.757","user":"web","version":1,"checksum":589445744,"size":166,"mimetype":"text/plain","chunks":[],"is_root":false,
                          "contents":[]}';
        $this->exerciseCreateMetadata(true,$name,$parent_id,$path,$pathabsolute,$metadataOut);
    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndUserAndIsFileAndNameAndParentIdAndPathAndPathAbsolute
     * should: calledStackSyncDataInsertMetadata
     */
    public function test_createMetadata_called_tokenAndUserAndIsFileAndNameAndParentIdAndPathAndPathAbsolute_calledStackSyncDataInsertMetadata()
    {
        $name = "client.pdf";
        $parent_id = "null";
        $path = '/';
        $pathabsolute = '/home/eyeos/' . $name;
        $metadataOut = '{"id":32565632156,"parent_id":"null","filename":"root","is_folder":true,"status":"NEW","server_modified":"2014-03-11 14:22:45.757","client_modified":"2014-03-11 14:22:45.757","user":"web","version":1,"checksum":589445744,"size":166,"mimetype":"text/plain","chunks":[],"is_root":false,
                          "contents":[
                            {"filename":"prueba.pdf","id":"8888888","status":"NEW","version":1,"parent_id":32565632156,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false}
                          ]}';
        $this->exerciseCreateMetadata(true,$name,$parent_id,$path,$pathabsolute,$metadataOut);
    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndUserAndIsFileAndNameAndParentIdAndPathAndPathAbsolute
     * should: calledStackSyncDataExistsMetadata
     */
    public function test_createMetadata_called_tokenAndUserAndIsFileAndNameAndParentIdAndPathAndPathAbsolute_calledStackSyncDataExistsMetadata()
    {
        $name = "client.pdf";
        $parent_id = "null";
        $id = 1111111;
        $path = '/';
        $pathabsolute = '/home/eyeos/' . $name;
        $metadataOut = '{"id":32565632156,"parent_id":"null","filename":"root","is_folder":true,"status":"NEW","server_modified":"2014-03-11 14:22:45.757","client_modified":"2014-03-11 14:22:45.757","user":"web","version":1,"checksum":589445744,"size":166,"mimetype":"text/plain","chunks":[],"is_root":false,
                      "contents":[
                        {"filename":"prueba.pdf","id":"8888888","status":"NEW","version":1,"parent_id":32565632156,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false},
                        {"filename":"client.pdf","id":"1111111","status":"NEW","version":1,"parent_id":32565632156,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false,"size":14}
                      ]}';
        $metadataFile = '{"filename":"client.pdf","id":"1111111","status":"CHANGED","version":2,"parent_id":32565632156,"user":"eyeos","size":134,"client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false}';
        $metadataUpdate = '{"user_eyeos":"' . $this->user . '","filename":"client.pdf","id":"1111111","status":"CHANGED","version":2,"parent_id":32565632156,"user":"eyeos","size":134,"client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false,"path":"/"}';

        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->token,true,$parent_id,true)
            ->will($this->returnValue(json_decode($metadataOut)));

        $this->apiProviderMock->expects($this->never())
            ->method('createMetadata');

        $this->apiProviderMock->expects($this->at(1))
            ->method('uploadMetadata')
            ->with($this->token,$id,$pathabsolute)
            ->will($this->returnValue(json_decode('{"status":true}')));

        $this->apiProviderMock->expects($this->at(2))
            ->method('getMetadata')
            ->with($this->token,true,$id)
            ->will($this->returnValue(json_decode($metadataFile)));

        $u1dbIn = new stdClass();
        $u1dbIn->type = 'update';
        $u1dbIn->lista = array();
        $old = new stdClass();
        $old->parent_old = 32565632156;
        array_push($u1dbIn->lista,$old);
        array_push($u1dbIn->lista,json_decode($metadataUpdate));

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('true'));

        $this->sut->createMetadata($this->token,$this->user,true,$name,$parent_id,$path,$pathabsolute);
    }


    /**
     * method: createMetadata
     * when: called
     * with: tokenAndUserAndIsFolderAndNameAndParentIdAndPath
     * should: calledStackSyncNoDataInsertMetadata
     */
    public function test_createMetadata_called_tokenAndUserAndIsFolderAndNameAndParentIdAndPath_calledStackSyncNoDataInsertMetadata()
    {
        $name = "client";
        $parent_id = "null";
        $path = '/';
        $metadataOut = '{"id":32565632156,"parent_id":"null","filename":"root","is_folder":true,"status":"NEW","server_modified":"2014-03-11 14:22:45.757","client_modified":"2014-03-11 14:22:45.757","user":"web","version":1,"checksum":589445744,"size":166,"mimetype":"text/plain","chunks":[],"is_root":false,
                          "contents":[]}';
        $this->exerciseCreateMetadata(false,$name,$parent_id,$path,null,$metadataOut);
    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndUserAndIsFolderAndNameAndParentIdAndPath
     * should: calledStackSyncDataInsertMetadata
     */
    public function test_createMetadata_called_tokenAndUserAndIsFolderAndNameAndParentIdAndPath_calledStackSyncDataInsertMetadata()
    {
        $name = "client";
        $parent_id = "null";
        $path = '/';
        $metadataOut = '{"id":32565632156,"parent_id":"null","filename":"root","is_folder":true,"status":"NEW","server_modified":"2014-03-11 14:22:45.757","client_modified":"2014-03-11 14:22:45.757","user":"web","version":1,"checksum":589445744,"size":166,"mimetype":"text/plain","chunks":[],"is_root":false,
                          "contents":[
                            {"filename":"prueba.pdf","id":"8888888","status":"NEW","version":1,"parent_id":32565632156,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false}
                          ]}';
        $this->exerciseCreateMetadata(false,$name,$parent_id,$path,null,$metadataOut);
    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndUserAndIsFolderAndNameAndParentIdAndPath
     * should: calledStackSyncDataExistsMetadata
     */
    public function test_createMetadata_called_tokenAndUserAndIsFolderAndNameAndParentIdAndPath_calledStackSyncDataExistsMetadata()
    {
        $name = "client";
        $parent_id = "null";
        $path = '/';
        $metadataOut = '{"id":32565632156,"parent_id":"null","filename":"root","is_folder":true,"status":"NEW","server_modified":"2014-03-11 14:22:45.757","client_modified":"2014-03-11 14:22:45.757","user":"web","version":1,"checksum":589445744,"size":166,"mimetype":"text/plain","chunks":[],"is_root":false,
                      "contents":[
                        {"filename":"prueba.pdf","id":"8888888","status":"NEW","version":1,"parent_id":32565632156,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false},
                        {"filename":"client","id":"1111111","status":"NEW","version":1,"parent_id":32565632156,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true}
                      ]}';

        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->token,false,$parent_id,true)
            ->will($this->returnValue(json_decode($metadataOut)));

        $this->apiProviderMock->expects($this->never())
            ->method('createMetadata');

        $this->sut->createMetadata($this->token,$this->user,false,$name,$parent_id,$path);
    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndUserAndIsFolderAndNameAndParentIdAndPath
     * should: returnGetMetadataPermissionDenied
     */
    public function test_createMetadata_called_tokenAndUserAndIsFolderAndNameAndParentIdAndPath_returnGetMetadataPermissionDenied()
    {
        $name = "client";
        $parent_id = "null";
        $path = '/';
        $metadata = '{"error":403}';

        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->token,false,$parent_id,true)
            ->will($this->returnValue(json_decode($metadata)));

        $this->apiProviderMock->expects($this->never())
            ->method('createMetadata');

        $this->sut->createMetadata($this->token,$this->user,false,$name,$parent_id,$path);
    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndUserAndIsFolderAndNameAndParentIdAndPath
     * should: returnCreateMetadataPermissionDenied
     */
    public function test_createMetadata_called_tokenAndUserAndIsFolderAndNameAndParentIdAndPath_returnCreateMetadataPermissionDenied()
    {
        $name = "client";
        $parent_id = "null";
        $path = '/';
        $metadata = '{"error":403}';
        $metadataOut = '{"id":32565632156,"parent_id":"null","filename":"root","is_folder":true,"status":"NEW","server_modified":"2014-03-11 14:22:45.757","client_modified":"2014-03-11 14:22:45.757","user":"web","version":1,"checksum":589445744,"size":166,"mimetype":"text/plain","chunks":[],"is_root":false,
                      "contents":[]}';

        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->token,false,$parent_id,true)
            ->will($this->returnValue(json_decode($metadataOut)));

        $this->apiProviderMock->expects($this->at(1))
            ->method('createMetadata')
            ->with($this->token,false,$name,$parent_id)
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $this->sut->createMetadata($this->token,$this->user,false,$name,$parent_id,$path);
    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndUserAndIsFolderAndNameAndParentIdAndPath
     * should: returnCreateMetadataPermissionDenied
     */
    public function test_createMetadata_called_tokenAndUserAndIsFileAndNameAndParentIdAndPath_returnUploadMetadataPermissionDenied()
    {
        $name = "prueba.pdf";
        $parent_id = "32565632156";
        $path = '/';
        $metadata = '{"error":403}';
        $pathabsolute = '/home/eyeos/' . $name;

        $metadataOut = '{"id":32565632156,"parent_id":"null","filename":"root","is_folder":true,"status":"NEW","server_modified":"2014-03-11 14:22:45.757","client_modified":"2014-03-11 14:22:45.757","user":"web","version":1,"checksum":589445744,"size":166,"mimetype":"text/plain","chunks":[],"is_root":false,
                      "contents":[
                        {"filename":"prueba.pdf","id":"8888888","status":"NEW","version":1,"parent_id":32565632156,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false},
                        {"filename":"client","id":"1111111","status":"NEW","version":1,"parent_id":32565632156,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true}
                      ]}';

        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->token,true,$parent_id,true)
            ->will($this->returnValue(json_decode($metadataOut)));

        $this->apiProviderMock->expects($this->at(1))
            ->method('uploadMetadata')
            ->with($this->token,8888888,$pathabsolute)
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $this->sut->createMetadata($this->token,$this->user,true,$name,$parent_id,$path,$pathabsolute);
    }

    /**
     * method: donwloadMetadata
     * when: called
     * with: tokenAndIdAndPath
     * should: returnFileWritten
     */
    public function test_downloadMetadata_called_tokenAndIdAndPath_returnFileWriten()
    {
        $path = "/home/eyeos/prueba.txt";
        $id = 8888888;
        $this->apiProviderMock->expects($this->at(0))
            ->method('downloadMetadata')
            ->with($this->token,$id,$path)
            ->will($this->returnValue('true'));

        $this->sut->downloadMetadata($this->token,$id,$path);
    }

    /**
     * method: downloadMetadata
     * when: called
     * with: tokenAndIdAndPath
     * should: returnPermissionDenied
     */
    public function test_downloadMetadata_called_tokenAndIdAndPath_returnPermissionDenied()
    {
        $path = "/home/eyeos/prueba.txt";
        $id = 8888888;
        $metadata = '{"error":403}';
        $this->apiProviderMock->expects($this->at(0))
            ->method('downloadMetadata')
            ->with($this->token,$id)
            ->will($this->returnValue(json_decode($metadata)));

        $this->filesProviderMock->expects($this->never())
            ->method('putContents');
        $this->sut->downloadMetadata($this->token,$id,$path);
    }

    /**
     * method: deleteMetadata
     * when: called
     * with: tokenAndIsFileAndIdAndUser
     * should: returnU1dbDelete
     */
    public function test_deleteMetadata_called_tokenAndIsFileAndIdAndUser_returnU1dbDelete()
    {
        $id = 8888888;
        $metadata = '{"filename":"prueba.pdf","id":"8888888","status":"DELETED","version":1,"parent_id":32565632156,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false}';
        $this->exerciseDeleteMetadata($metadata,true,$id);
    }

    /**
     * method: deleteMetadata
     * when: called
     * with: tokenAndIsFolderAndIdAndUser
     * should: returnU1dbDelete
     */
    public function test_deleteMetadata_called_tokenAndIsFolderAndIdAndUser_returnU1dbDelete()
    {
        $id = 1544444;
        $metadata = '{"filename":"prueba","id":"1544444","status":"DELETED","version":2,"parent_id":32565632156,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true}';
        $this->exerciseDeleteMetadata($metadata,false,$id);
    }

    /**
     * method: deleteMetadata
     * when: called
     * with: tokenAndIsFolderAndIdAndUser
     * should: returnPermissionDenied
     */
    public function test_deleteMetadata_called_tokenAndIsFolderAndIdAndUser_returnPermissionDenied()
    {
        $id = 1544444;
        $metadata = '{"error":403}';
        $this->apiProviderMock->expects($this->at(0))
            ->method('deleteMetadata')
            ->with($this->token,false,$id)
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $this->sut->deleteMetadata($this->token,false,$id,$this->user);
    }

    /**
     * metho: renameMetadata
     * when: called
     * with: tokenAndIsFileAndIdAndNameAndPathAndUserAndParentId
     * should: returnU1dbRename
     */
    public function test_renameMetadata_called_tokenAndIsFileAndIdAndNameAndPathAndUserAndParentId_returnU1dbRename()
    {
        $id = 8339393;
        $name = "b.txt";
        $parent = 99999;
        $path = '/A/';
        $this->exerciseRenameMetadata(true,$id,$parent,$path,$name);
    }

    /**
     * method: renameMetadata
     * when: called
     * with: tokenAndIsFolderAndIdAndNameAndPathAndUserAndParentId
     * should: returnU1dbRename
     */
    public function test_renameMetadata_called_tokenAndIsFolderAndIdAndNameAndPathAndUserAndParentId_returnU1dbRename()
    {
        $id = 8983444;
        $name = "F";
        $parent = 1333555;
        $path = '/D/';
        $this->exerciseRenameMetadata(false,$id,$parent,$path,$name);
    }

    /**
     * method: renameMetadata
     * when: called
     * with: tokenAndIsFolderAndIdAndNameAndPathAndUserAndParentId
     * should: returnPermissionDenied
     */
    public function test_renameMetadata_called_tokenAndIsFolderAndIdAndNameAndPathAndUserAndParentId_returnPermissionDenied()
    {
        $id = 8983444;
        $name = "F";
        $parent = 1333555;
        $path = '/D/';
        $metadata = '{"error":403}';
        $this->apiProviderMock->expects($this->at(0))
            ->method('updateMetadata')
            ->with($this->token,false,$id,$name,$parent)
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $this->sut->renameMetadata($this->token,false,$id,$name,$path,$this->user,$parent);
    }

    private function exerciseCreateMetadata($file,$name,$parent_id,$path,$pathAbsolute,$metadataOut)
    {
        $type = $file?'false':'true';
        $metadata = '{"filename":"' . $name .'","id":142555444,"size":775412,"mimetype":"application/pdf","status":"NEW","version":3,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":'  . $type . '}';
        $metadataU1db = json_decode('{"user_eyeos":"' . $this->user . '","filename":"' . $name . '","id":142555444,"size":775412,"mimetype":"application/pdf","status":"NEW","version":3,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":' . $type . ',"path":"' . $path . '"}');

        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->token,$file,$parent_id,true)
            ->will($this->returnValue(json_decode($metadataOut)));

        $this->apiProviderMock->expects($this->at(1))
            ->method('createMetadata')
            ->with($this->token,$file,$name,$parent_id,$pathAbsolute)
            ->will($this->returnValue(json_decode($metadata)));

        $u1dbIn = new stdClass();
        $u1dbIn->type = 'insert';
        $u1dbIn->lista = array();
        array_push($u1dbIn->lista,$metadataU1db);

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('true'));

        $this->sut->createMetadata($this->token,$this->user,$file,$name,$parent_id,$path,$pathAbsolute);
    }

    private function exerciseDeleteMetadata($metadata,$file,$id)
    {
        $this->apiProviderMock->expects($this->at(0))
            ->method('deleteMetadata')
            ->with($this->token,$file,$id)
            ->will($this->returnValue(json_decode($metadata)));

        $metadataU1db = '{"id":' . $id . ',"user_eyeos":"' . $this->user . '","parent_id":' . json_decode($metadata)->parent_id . '}';
        $u1dbIn = new stdClass();
        $u1dbIn->type = 'delete';
        $u1dbIn->lista = array();
        array_push($u1dbIn->lista,json_decode($metadataU1db));

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('true'));

        $this->sut->deleteMetadata($this->token,$file,$id,$this->user);
    }

    private function exerciseRenameMetadata($file,$id,$parent,$path,$name)
    {
        $type = $file?'false':'true';
        $metadata = '{"filename":"' . $name .'","id":' . $id . ',"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":4,"parent_id":"'. $parent . '","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":' . $type . '}';
        $metadataU1db = json_decode('{"user_eyeos":"' . $this->user . '","filename":"' . $name . '","id":' . $id . ',"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":4,"parent_id":"'. $parent . '","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":' . $type . ',"path":"' . $path . '"}');

        $this->apiProviderMock->expects($this->at(0))
            ->method('updateMetadata')
            ->with($this->token,$file,$id,$name,$parent)
            ->will($this->returnValue(json_decode($metadata)));

        $u1dbIn = new stdClass();
        $u1dbIn->type = 'rename';
        $u1dbIn->lista = array();
        array_push($u1dbIn->lista,$metadataU1db);

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('true'));

        $this->sut->renameMetadata($this->token,$file,$id,$name,$path,$this->user,$parent);
    }

}

?>