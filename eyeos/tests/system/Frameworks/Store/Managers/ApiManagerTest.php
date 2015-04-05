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
    private $cloud;
    private $pathCloud;

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
        $this->cloud = 'Stacksync';
        $this->pathCloud = "home://~eyeos/Cloudspaces/Stacksync";
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
        $file->cloud = $this->cloud;
        $file->path = '/';
        array_push($u1dbIn->lista,$file);
        $metadata = '{"filename":"root","id":"null","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,"contents":[{"filename":"Client1.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false},{"filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true},{"filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false}]}';
        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->cloud,$this->token,$isfile,$id,$contents)
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('[]'));

        $this->filesProviderMock->expects($this->at(0))
            ->method('createFile')
            ->with($this->pathCloud . "/client2",true)
            ->will($this->returnValue(true));

        $this->filesProviderMock->expects($this->at(1))
            ->method('createFile')
            ->with($this->pathCloud . "/Client3.pdf",false)
            ->will($this->returnValue(true));

        $u1dbIn = new stdClass();
        $u1dbIn->type = 'insert';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/"}');
        array_push($u1dbIn->lista,$metadata);

        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('true'));

        $u1dbIn = new stdClass();
        $u1dbIn->type = 'insert';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false,"path":"/"}');
        array_push($u1dbIn->lista,$metadata);

        $this->accessorProviderMock->expects($this->at(2))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('true'));

        $u1dbIn = new stdClass();
        $u1dbIn->type = 'insert';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"root","id":"null","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,"path":"null"}');
        array_push($u1dbIn->lista,$metadata);

        $this->accessorProviderMock->expects($this->at(3))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('true'));

        $this->sut->getMetadata($this->cloud,$this->token,$id,$this->pathCloud,$this->user);
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
        $file->cloud = $this->cloud;
        $file->path = '/';
        array_push($u1dbIn->lista,$file);
        $metadata = '{"filename":"root","id":"null","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,"contents":[{"filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true},{"filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false}]}';
        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->cloud,$this->token,$isfile,$id,$contents)
            ->will($this->returnValue(json_decode($metadata)));

        $u1dbOut = '[{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/"},
                    {"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false,,"path":"/"},
                    {"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"root","id":"null","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,,"path":"null"}]';
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

        $this->sut->getMetadata($this->cloud,$this->token,$id,$this->pathCloud,$this->user);
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
        $file->cloud = $this->cloud;
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
            ->with($this->cloud,$this->token,$isfile,$id,$contents)
            ->will($this->returnValue(json_decode($metadata)));

        $u1dbOut = '[{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"root","id":"null","status":"NEW","version":1,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,"path":"null"},
                    {"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"client","id":334254755856,"size":775412,"status":"NEW","version":1,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/"},
                    {"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"client1","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"NEW","version":1,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/"},
                    {"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false,"path":"/"}]';
        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue($u1dbOut));

        $u1dbIn->type = 'deleteFolder';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"client","id":334254755856,"size":775412,"status":"DELETED","version":2,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/"}');
        array_push($u1dbIn->lista,$metadata);
        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue($u1dbOut));

        $this->filesProviderMock->expects($this->at(0))
            ->method('deleteFile')
            ->with($this->pathCloud . '/client', true)
            ->will($this->returnValue(true));

        $this->filesProviderMock->expects($this->at(1))
            ->method('createFile')
            ->with($this->pathCloud . '/provider', true)
            ->will($this->returnValue(true));

        $this->filesProviderMock->expects($this->at(2))
            ->method('renameFile')
            ->with($this->pathCloud . '/client1', 'client2')
            ->will($this->returnValue(true));

        $u1dbIn->type = 'update';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/"}');
        array_push($u1dbIn->lista,json_decode('{"parent_old":"null"}'));
        array_push($u1dbIn->lista,$metadata);
        $this->accessorProviderMock->expects($this->at(3))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue($u1dbOut));

        $this->filesProviderMock->expects($this->at(3))
            ->method('deleteFile')
            ->with($this->pathCloud . '/Client3.pdf', false)
            ->will($this->returnValue(true));

        $u1dbIn->type = 'deleteFolder';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false,"path":"/"}');
        array_push($u1dbIn->lista,$metadata);
        $this->accessorProviderMock->expects($this->at(4))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue($u1dbOut));

        $this->sut->getMetadata($this->cloud,$this->token,$id,$this->pathCloud,$this->user);
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
            ->with($this->cloud,$this->token,false,$id,true)
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $this->sut->getMetadata($this->cloud,$this->token,$id,$this->pathCloud,$this->user);

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
            ->with($this->cloud,$this->token,false,$id,true)
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $this->sut->getMetadata($this->cloud,$this->token,$id,$this->pathCloud,$this->user);

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
        $newmetadata->pathAbsolute = $this->path . $path . 'Client1.pdf';
        $newmetadata->path = $path;
        $newmetadata->pathEyeos =  $this->path . $path . 'Client1.pdf';
        $expected = array($newmetadata);
        $this->apiProviderMock->expects($this->once())
            ->method('getMetadata')
            ->with($this->token,true,$id)
            ->will($this->returnValue(json_decode($metadata)));

        $this->sut->getSkel($this->token,true,$id,$metadatas,$path,$newmetadata->pathAbsolute,$this->path . "/documents");
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
        $data2->pathAbsolute = null;
        $data2->pathEyeos = $this->path . $data2->path . "Client1.pdf";
        array_push($expected,$data2);
        $data1 = json_decode($metadataFile);
        unset($data1->contents);
        $data1->path = "/Cloudspaces/";
        $data1->pathAbsolute = null;
        $data1->pathEyeos = $this->path . $data1->path . "a";
        array_push($expected,$data1);
        $data = json_decode($metadata);
        $data->path = "/";
        $data->pathAbsolute = $this->path . "/Cloudspaces";
        $data->pathEyeos = $this->path . $data->path . "Cloudspaces";
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

        $this->sut->getSkel($this->token,false,$id,$metadatas,$path,$data->pathAbsolute, $this->path);
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
        array_push($expected,json_decode('{"id":-8090905582480578692,"parent_id":null,"filename":"Cloudspaces","is_folder":true,"status":"NEW","server_modified":"2014-03-11 14:22:45.757","client_modified":"2014-03-11 14:22:45.757","user":"web","version":1,"checksum":589445744,"size":166,"mimetype":"text/plain","chunks":[],"is_root":false,"path":"/","pathAbsolute":null,"pathEyeos":"' . $this->path . '/Cloudspaces"}'));

        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->token,false,$id,true)
            ->will($this->returnValue(json_decode($metadata)));

        $this->apiProviderMock->expects($this->at(1))
            ->method('getMetadata')
            ->with($this->token,false,32565632156,true)
            ->will($this->returnValue(json_decode($metadataError)));

        $this->sut->getSkel($this->token,false,$id,$metadatas,$path,null,$this->path);
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
            ->with($this->cloud,$this->token,true,$parent_id,true)
            ->will($this->returnValue(json_decode($metadataOut)));

        $this->apiProviderMock->expects($this->never())
            ->method('createMetadata');

        $this->apiProviderMock->expects($this->at(1))
            ->method('uploadMetadata')
            ->with($this->token,$id,$pathabsolute)
            ->will($this->returnValue(json_decode('{"status":true}')));

        $this->apiProviderMock->expects($this->at(2))
            ->method('getMetadata')
            ->with($this->cloud,$this->token,true,$id)
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

        $params = new stdClass();
        $params->type = "updateDownloadVersion";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = "1111111";
        $aux->version = 2;
        $aux->recover = false;
        array_push($params->lista,$aux);

        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue('true'));



        $this->sut->createMetadata($this->cloud,$this->token,$this->user,true,$name,$parent_id,$path,$pathabsolute);
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
            ->with($this->cloud,$this->token,false,$parent_id,true)
            ->will($this->returnValue(json_decode($metadataOut)));

        $this->apiProviderMock->expects($this->never())
            ->method('createMetadata');

        $this->sut->createMetadata($this->cloud,$this->token,$this->user,false,$name,$parent_id,$path);
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
            ->with($this->cloud,$this->token,false,$parent_id,true)
            ->will($this->returnValue(json_decode($metadata)));

        $this->apiProviderMock->expects($this->never())
            ->method('createMetadata');

        $this->sut->createMetadata($this->cloud,$this->token,$this->user,false,$name,$parent_id,$path);
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
            ->with($this->cloud,$this->token,false,$parent_id,true)
            ->will($this->returnValue(json_decode($metadataOut)));

        $this->apiProviderMock->expects($this->at(1))
            ->method('createMetadata')
            ->with($this->cloud,$this->token,false,$name,$parent_id)
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $this->sut->createMetadata($this->cloud,$this->token,$this->user,false,$name,$parent_id,$path);
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
            ->with($this->cloud,$this->token,true,$parent_id,true)
            ->will($this->returnValue(json_decode($metadataOut)));

        $this->apiProviderMock->expects($this->at(1))
            ->method('uploadMetadata')
            ->with($this->token,8888888,$pathabsolute)
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $this->sut->createMetadata($this->cloud,$this->token,$this->user,true,$name,$parent_id,$path,$pathabsolute);
    }

    /**
     * method: donwloadMetadata
     * when: called
     * with: tokenAndIdAndPathAndUser
     * should: returnFirstDownload
     */
    public function test_downloadMetadata_called_tokenAndIdAndPathAndUser_returnFirstDownload()
    {
        $path = "/home/eyeos/prueba.txt";
        $id = "8888888";
        $metadata = '{"filename":"prueba.pdf","id":"8888888","status":"NEW","version":1,"parent_id":"32565632156","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false}';
        $this->getDownloadMetadata($metadata,"null",$id);
        $this->apiProviderMock->expects($this->at(1))
            ->method('downloadMetadata')
            ->with($this->token,$id,$path)
            ->will($this->returnValue('true'));

        $params = new stdClass();
        $params->type = "insertDownloadVersion";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = $id;
        $aux->user_eyeos = $this->user;
        $aux->version = 1;
        $aux->recover = false;
        array_push($params->lista,$aux);

        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue('true'));

        $this->sut->downloadMetadata($this->token,$id,$path,$this->user,false);
    }

    /**
     * method: downloadMetadata
     * when: called
     * with: tokenAndIdAndPathAndUser
     * should: returnFileLocal
     */
    public function test_downloadMetadata_called_tokenAndIdAndPathAndUser_returnFileLocal()
    {
        $path = "/home/eyeos/prueba.txt";
        $id = "8888888";
        $metadata = '{"filename":"prueba.pdf","id":"8888888","status":"NEW","version":1,"parent_id":"32565632156","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false}';
        $this->getDownloadMetadata($metadata,'{"id":"8888888","version":1,"recover":false}',$id);
        $this->apiProviderMock->expects($this->never())
            ->method('downloadMetadata');
        $this->sut->downloadMetadata($this->token,$id,$path,$this->user,false);
    }

    /**
     * method: downloadMetadata
     * when: called
     * with: tokenAndIdAndPathAndUser
     * should: returnFileWritten
     */
    public function test_downloadMetadata_called_tokenAndIdAndPathAndUser_returnFileWritten()
    {
        $path = "/home/eyeos/prueba.txt";
        $id = "8888888";
        $metadata = '{"filename":"prueba.pdf","id":"8888888","status":"NEW","version":2,"parent_id":"32565632156","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false}';
        $this->getDownloadMetadata($metadata,'{"id":"8888888","user_eyeos":"eyeID_EyeosUser_2","version":1,"recover":false}',$id);

        $this->apiProviderMock->expects($this->at(1))
            ->method('downloadMetadata')
            ->with($this->token,$id,$path)
            ->will($this->returnValue('true'));

        $params = new stdClass();
        $params->type = "updateDownloadVersion";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = $id;
        $aux->user_eyeos = $this->user;
        $aux->version = 2;
        $aux->recover = false;
        array_push($params->lista,$aux);

        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue('true'));

        $this->sut->downloadMetadata($this->token,$id,$path,$this->user,false);
    }

    /**
     * method: downloadMetadata
     * when: called
     * with: tokenAndIdAndPathAndUser
     * should: returnFileLocalRecover
     */
    public function test_downloadMetadata_called_tokenAndIdAndPathAndUser_returnFileLocalRecover()
    {
        $path = "/home/eyeos/prueba.txt";
        $id = "8888888";
        $metadata = '{"filename":"prueba.pdf","id":"8888888","status":"NEW","version":2,"parent_id":"32565632156","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false}';
        $this->getDownloadMetadata($metadata,'{"id":"8888888","user_eyeos":"eyeID_EyeosUser_2","version":1,"recover":true}',$id);

        $this->apiProviderMock->expects($this->never())
            ->method('downloadMetadata');

        $this->sut->downloadMetadata($this->token,$id,$path,$this->user,false);
    }

    /**
     * method: downloadMetadata
     * when: called
     * with: tokenAndIdAndPathAndAndUserAndIsTmp
     * should: returnFileWritten
     */
    public function test_downloadMetadata_called_tokenAndIdAndPathAndUserAndIsTmp_returnFileWritten()
    {
        $path = "/home/eyeos/prueba.txt";
        $id = "8888888";
        $metadata = '{"filename":"prueba.pdf","id":"8888888","status":"NEW","version":2,"parent_id":"32565632156","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false}';
        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->token,true,$id)
            ->will($this->returnValue(json_decode($metadata)));

        $params = new stdClass();
        $params->type = "getDownloadVersion";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = $id;
        $aux->user_eyeos = $this->user;
        array_push($params->lista,$aux);

        $this->accessorProviderMock->expects($this->exactly(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue('{"id":"8888888","version":1,"recover":false}'));

        $this->apiProviderMock->expects($this->at(1))
            ->method('downloadMetadata')
            ->with($this->token,$id,$path)
            ->will($this->returnValue('true'));

        $this->sut->downloadMetadata($this->token,$id,$path,$this->user,true);
    }

    /**
     * method: downloadMetadata
     * when: called
     * with: tokenAndIdAndPathAndUser
     * should: returnPermissionDenied
     */
    public function test_downloadMetadata_called_tokenAndIdAndPathAndUser_returnPermissionDenied()
    {
        $path = "/home/eyeos/prueba.txt";
        $id = 8888888;
        $metadata = '{"error":403}';
        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->token,true,$id)
            ->will($this->returnValue(json_decode($metadata)));

        $this->filesProviderMock->expects($this->never())
            ->method('putContents');
        $this->sut->downloadMetadata($this->token,$id,$path,$this->user,false);
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
        $metadata = '{"filename":"prueba.pdf","id":"8888888","status":"DELETED","version":1,"parent_id":"32565632156","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false}';
        $this->exerciseDeleteMetadata($metadata,true,$id,"/cloudSpaces/",$this->pathCloud . "/cloudSpaces");
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
        $metadata = '{"filename":"prueba","id":"1544444","status":"DELETED","version":2,"parent_id":"32565632156","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true}';
        $this->exerciseDeleteMetadata($metadata,false,$id,"/cloudSpaces/",$this->pathCloud . "/cloudSpaces");
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
            ->with($this->cloud,$this->token,false,$id)
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $this->sut->deleteMetadata($this->cloud,$this->token,false,$id,$this->user,"/cloudSpaces/",$this->pathCloud . "/cloudSpaces");
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

    /**
     * method: moveMetadata
     * when: called
     * with: tokenAndIsFileAndIdAndPathOrigAndPathDestAndUserAndParentId
     * should: returnU1dbMove
     */
    public function test_moveMetadata_called_tokenAndIsFileAndIdAndPathOrigAndPathDestAndUserAndParentId_returnU1dbMove()
    {
        $id = 8983444;
        $parent = 1333555;
        $pathOrig = '/';
        $pathNew = "/documents/";
        $filename = "prueba.pdf";
        $metadataMove = '{"filename":"' . $filename . '","id":"' . $id . '","size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"' . $parent . '","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false}';
        $metadataDelete = '{"id":' . $id . ',"user_eyeos":"' . $this->user . '","path":"' . $pathOrig. '"}';
        $metadataInsert = json_decode('{"user_eyeos":"' . $this->user . '","filename":"' . $filename . '","id":"' . $id . '","size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"'. $parent . '","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false,"path":"' . $pathNew . '"}');
        $this->exerciseMoveMetadata($id,$filename,$parent,true,$pathOrig,$pathNew,$metadataMove,$metadataDelete, $metadataInsert);
    }

    /**
     * method: moveMetadata
     * when: called
     * with: tokenAndIsFileAndIdAndPathOrigAndPathDestAndUserAndParentIdAndName
     * should: returnU1dbMove
     */
    public function test_moveMetadata_called_tokenAndIsFileAndIdAndPathOrigAndPathDestAndUserAndParentIdAndName_returnU1dbMove()
    {
        $id = 8983444;
        $parent = 1333555;
        $pathOrig = '/';
        $pathNew = "/documents/";
        $filename = "prueba.pdf";
        $fileDest = "prueba 1.pdf";
        $metadataMove = '{"filename":"' . $fileDest . '","id":"' . $id . '","size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"' . $parent . '","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false}';
        $metadataDelete = '{"id":' . $id . ',"user_eyeos":"' . $this->user . '","path":"' . $pathOrig. '"}';
        $metadataInsert = json_decode('{"user_eyeos":"' . $this->user . '","filename":"' . $fileDest . '","id":"' . $id . '","size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"'. $parent . '","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false,"path":"' . $pathNew . '"}');
        $this->exerciseMoveMetadata($id,$filename,$parent,true,$pathOrig,$pathNew,$metadataMove,$metadataDelete, $metadataInsert,$fileDest);
    }

    /**
     * method: moveMetadata
     * when: called
     * with: tokenAndIsFolderAndIdAndPathOrigAndPathDestAndUserAndParentId
     * should: returnU1dbMove
     */
    public function test_moveMetadata_called_tokenAndIsFolderAndIdAndPathOrigAndPathDestAndUserAndParentID_returnU1dbMove()
    {
        $id = 8983444;
        $parent = 1333555;
        $pathOrig = '/';
        $pathNew = "/documents/";
        $filename = "prueba";
        $metadataMove = '{"filename":"' . $filename . '","id":"' . $id . '","size":0,"status":"CHANGED","version":2,"parent_id":"' . $parent . '","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":true,"is_root":false}';
        $metadataDelete = '{"id":' . $id . ',"user_eyeos":"' . $this->user . '","path":"' . $pathOrig. '"}';
        $metadataInsert = json_decode('{"user_eyeos":"' . $this->user . '","filename":"' . $filename . '","id":"' . $id . '","size":0,"status":"CHANGED","version":2,"parent_id":"'. $parent . '","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":true,"is_root":false,"path":"' . $pathNew . '"}');
        $this->exerciseMoveMetadata($id,$filename,$parent,false,$pathOrig,$pathNew,$metadataMove,$metadataDelete,$metadataInsert);

    }

    /**
     * method: moveMetadata
     * when: called
     * with: tokenAndIsFolderAndIdAndPathOrigAndPathDestAndUserAndParentId
     * should: returnPermissionDenied
     */
    public function test_moveMetadata_called_tokenAndIsFolderAndIdAndPathOrigAndPathDestAndUserAndParentID_returnPermissionDenied()
    {
        $id = 8983444;
        $parent = 1333555;

        $this->apiProviderMock->expects($this->at(0))
            ->method('updateMetadata')
            ->with($this->token,false,$id,null,$parent)
            ->will($this->returnValue(json_decode('{"error":403}')));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $this->filesProviderMock->expects($this->never())
            ->method('deleteFile');

        $this->sut->moveMetadata($this->token,false,$id,$this->path,$this->path . "/documents",$this->user,$parent,null);
    }


    /**
     * method: deleteMetadataUser
     * when: called
     * with: user
     * should: calledU1dbDeleteCorrect
     */
    public function test_deleteMetadataUser_called_user_calledU1dbDeleteCorrect()
    {
        $user = 'eyeos';
        $file = array();
        $file['type'] = "deleteMetadataUser";
        $file['lista'] = array();
        array_push($file['lista'],array("user_eyeos" => $user));

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($file))
            ->will($this->returnValue('true'));

        $this->sut->deleteMetadataUser($user);
    }

    /**
     * method: deleteMetadataUser
     * when: called
     * with: userAndCloud
     * should: calledU1dbDeleteCorrect
     */
    public function test_deleteMetadataUser_called_userAndCloud_calledU1dbDeleteCorrect()
    {
        $user = 'eyeos';
        $cloud = 'Stacksync';
        $file = array();
        $file['type'] = "deleteMetadataUser";
        $file['lista'] = array();
        array_push($file['lista'],array("user_eyeos" => $user, "cloud" => $cloud));

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($file))
            ->will($this->returnValue('true'));

        $this->sut->deleteMetadataUser($user, $cloud);
    }

    /**
     * method: recursiveDeleteVersion
     * when: called
     * with: idAndUser
     * when: deleteCorrect
     */
    public function test_recursiveDeleteVersion_called_idAndUser_calledDeleteCorrect()
    {
        $id = "88888888";
        $user = "eyeos";
        $params = new stdClass();
        $params->type = "recursiveDeleteVersion";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = $id;
        $aux->user_eyeos = $user;
        array_push($params->lista,$aux);

        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue('true'));

        $result = $this->sut->recursiveDeleteVersion($id,$user);
        $this->assertEquals(array("status" =>"OK"),$result);
    }

    /**
     * method: listVersions
     * when: called
     * with: tokenAndIdAndUser
     * should: returnListCorrectAndCurrentVersion
     */
    public function test_listVersions_called_tokenAndIdAndUser_returnListCorrectAndCurrentVersion()
    {
        $id = 8983444;
        $metadata = '[{"name":"Winter2015.jpg","path":"\/documents\/clients\/Winter2015.jpg","id":32565632156,"size":775412,"mimetype":"image\/jpg","status":"RENAMED","version":3,"parent":12386548974,"user":"Adrian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"},{"name":"Winter2012.jpg","path":"\/documents\/clients\/Winter2012.jpg","id":32565632156,"size":7482,"mimetype":"image\/jpg","status":"CHANGED","version":2,"parent":12386548974,"user":"Cristian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"},{"name":"Winter2012.jpg","path":"\/documents\/clients\/Winter2012.jpg","id":32565632156,"size":775412,"mimetype":"image\/jpg","status":"NEW","version":1,"parent":12386548974,"user":"Adrian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"}]';
        $expected = '[{"name":"Winter2015.jpg","path":"\/documents\/clients\/Winter2015.jpg","id":32565632156,"size":775412,"mimetype":"image\/jpg","status":"RENAMED","version":3,"parent":12386548974,"user":"Adrian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","enabled":true},{"name":"Winter2012.jpg","path":"\/documents\/clients\/Winter2012.jpg","id":32565632156,"size":7482,"mimetype":"image\/jpg","status":"CHANGED","version":2,"parent":12386548974,"user":"Cristian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"},{"name":"Winter2012.jpg","path":"\/documents\/clients\/Winter2012.jpg","id":32565632156,"size":775412,"mimetype":"image\/jpg","status":"NEW","version":1,"parent":12386548974,"user":"Adrian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"}]';

        $this->apiProviderMock->expects($this->at(0))
            ->method('listVersions')
            ->with($this->token,$id)
            ->will($this->returnValue(json_decode($metadata)));

        $params = new stdClass();
        $params->type = "getDownloadVersion";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = "" . $id;
        $aux->user_eyeos = $this->user;
        array_push($params->lista,$aux);

        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue('null'));

        $result = $this->sut->listVersions($this->token,8983444,$this->user);
        $this->assertEquals($expected,$result);
    }

    /**
     * method: listVersion
     * when: called
     * with: tokenAndIdAndUser
     * should: returnListCorrectAndOlderVersion
     */
    public function test_listVersion_called_tokenAndIdAndUser_returnListCorrectAndOlderVersion()
    {
        $id = 8983444;
        $metadata = '[{"name":"Winter2015.jpg","path":"\/documents\/clients\/Winter2015.jpg","id":32565632156,"size":775412,"mimetype":"image\/jpg","status":"RENAMED","version":3,"parent":12386548974,"user":"Adrian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"},{"name":"Winter2012.jpg","path":"\/documents\/clients\/Winter2012.jpg","id":32565632156,"size":7482,"mimetype":"image\/jpg","status":"CHANGED","version":2,"parent":12386548974,"user":"Cristian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"},{"name":"Winter2012.jpg","path":"\/documents\/clients\/Winter2012.jpg","id":32565632156,"size":775412,"mimetype":"image\/jpg","status":"NEW","version":1,"parent":12386548974,"user":"Adrian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"}]';
        $expected = '[{"name":"Winter2015.jpg","path":"\/documents\/clients\/Winter2015.jpg","id":32565632156,"size":775412,"mimetype":"image\/jpg","status":"RENAMED","version":3,"parent":12386548974,"user":"Adrian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"},{"name":"Winter2012.jpg","path":"\/documents\/clients\/Winter2012.jpg","id":32565632156,"size":7482,"mimetype":"image\/jpg","status":"CHANGED","version":2,"parent":12386548974,"user":"Cristian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","enabled":true},{"name":"Winter2012.jpg","path":"\/documents\/clients\/Winter2012.jpg","id":32565632156,"size":775412,"mimetype":"image\/jpg","status":"NEW","version":1,"parent":12386548974,"user":"Adrian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"}]';

        $this->apiProviderMock->expects($this->at(0))
            ->method('listVersions')
            ->with($this->token,$id)
            ->will($this->returnValue(json_decode($metadata)));

        $params = new stdClass();
        $params->type = "getDownloadVersion";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = "" . $id;
        $aux->user_eyeos = $this->user;
        array_push($params->lista,$aux);

        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue('{"id":"8983444","version":2,"recover":false}'));

        $result = $this->sut->listVersions($this->token,8983444,$this->user);
        $this->assertEquals($expected,$result);
    }

    /**
     * method: listVersions
     * when: called
     * with: tokenAndIdAndUser
     * should: returnPermissionDenied
     */
    public function test_listVersions_called_tokenAndIdAndUser_returnPermissionDenied()
    {
        $id = 8983444;
        $this->apiProviderMock->expects($this->at(0))
            ->method('listVersions')
            ->with($this->token,$id)
            ->will($this->returnValue(json_decode('{"error":403}')));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $result = $this->sut->listVersions($this->token,$id,$this->user);
        $this->assertEquals(array("status"=>"KO","error"=>403),$result);
    }

    /**
     * method: listVersions
     * when: called
     * with: tokenAndIdAndUser
     * should: returnException
     */
    public function test_listVersions_called_tokenAndIdAndUser_returnException()
    {
        $id = 8983444;
        $this->apiProviderMock->expects($this->at(0))
            ->method('listVersions')
            ->with($this->token,$id)
            ->will($this->returnValue(json_decode('{"error":-1}')));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $result = $this->sut->listVersions($this->token,$id,$this->user);
        $this->assertEquals(array("status"=>"KO","error"=>-1),$result);

    }

    /**
     * method: getFileVersionData
     * when: called
     * with: tokenAndIdAndVersionAndPathAndUser
     * should: insertDownloadVersion
     */
    public function test_getFileVersionData_called_tokenAndIdAndVersionAndPathAndUser_insertDownloadVersion()
    {
        $id = 8983444;
        $version = 2;
        $path = "/home/eyeos/client1.pdf";
        $this->exerciseGetFileVersionData("null",'{"status":true}',array("status" => "OK"),$id,$version,$path,"insertDownloadVersion");

    }

    /**
     * method: getFileVersionData
     * when: called
     * with: tokenAndIdAndVersionAndPathAndUser
     * should: updateDownloadVersion
     */
    public function test_getFileVersionData_called_tokenAndIdAndVersionAndPathAndUser_updateDownloadVersion()
    {
        $id = 8983444;
        $version = 2;
        $path = "/home/eyeos/client1.pdf";
        $this->exerciseGetFileVersionData('{"id":"8983444","version":1,"recover":false}','{"status":true}',array("status" => "OK"),$id,$version,$path,"updateDownloadVersion");
    }

    /**
     * method: getFileVersionData
     * when: called
     * with: tokenAndIdAndVersionAndPathAndUser
     * should: returnPermissionDenied
     */
    public function test_getFileVersionData_called_tokenAndIdAndVersionAndPathAndUser_returnPermissionDenied()
    {
        $id = 8983444;
        $version = 2;
        $path = "/home/eyeos/client1.pdf";
        $this->exerciseGetFileVersionData('{"id":"8983444","version":1,"recover":false}','{"error":403}',array("status" => "KO","error" => 403),$id,$version,$path,'',true);
    }

    /**
     * method: getFileVersionData
     * when: called
     * with: tokenAndIdAndVersionAndPathAndUser
     * should: returnException
     */
    public function test_getFileVersionData_called_tokenAndIdAndVersionAndPathAndUser_returnException()
    {
        $id = 8983444;
        $version = 2;
        $path = "/home/eyeos/client1.pdf";
        $this->exerciseGetFileVersionData('{"id":"8983444","version":1,"recover":false}','{"error":-1}',array("status" => "KO","error" => -1),$id,$version,$path,'',true);
    }

    /**
     * method: getListUsersShare
     * when: called
     * with: tokenAndId
     * should: returnList
     */
    public function test_getListUsersShare_called_tokenAndId_returnList()
    {
        $id = 123;
        $metadata = '[{"joined_at":"2014-05-27","is_owner":true,"name":"tester1","email":"tester1@test.com"}]';
        $this->exerciseGetListUsersShare($metadata,$id,$metadata);
    }

    /**
     * method: getListUsersShare
     * when: called
     * with: tokenAndId
     * should: returnPermissionDenied
     */
    public function test_getListUsersShare_called_tokenAndId_returnPermissionDenied()
    {
        $id = 123;
        $metadata = '{"error":403}';
        $metadataOut = array("status" => "KO","error" => 403);
        $this->exerciseGetListUsersShare($metadata,$id,$metadataOut);
    }

    /**
     * method: getListUsersShare
     * when: called
     * with: tokenAndId
     * should: returnException
     */
    public function test_getListUsersShare_called_tokenAndId_returnException()
    {
        $id = 123;
        $metadata = '{"error":-1}';
        $metadataOut = array("status" => "KO","error" => -1);
        $this->exerciseGetListUsersShare($metadata,$id,$metadataOut);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndList
     * should: returnCorrect
     */
    public function test_shareFolder_called_tokenAndIdAndList_returnCorrect()
    {
        $id = 123;
        $list = array("a@a.com","b@b.com");
        $metadata = '{"status":true}';
        $metadataOut = array("status" => "OK");
        $this->exerciseShareFolder($metadata,$id,$list,$metadataOut);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndList
     * should: returnPermissionDenied
     */
    public function test_shareFolder_called_tokenAndIdAndList_returnPermissionDenied()
    {
        $id = 123;
        $list = array("a@a.com","b@b.com");
        $metadata = '{"error":403}';
        $metadataOut = array("status" => "KO", "error" => 403);
        $this->exerciseShareFolder($metadata,$id,$list,$metadataOut);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndList
     * should: returnException
     */
    public function test_shareFolder_called_tokenAndIdAndList_returnException()
    {
        $id = 123;
        $list = array("a@a.com","b@b.com");
        $metadata = '{"error":-1}';
        $metadataOut = array("status" => "KO", "error" => -1);
        $this->exerciseShareFolder($metadata,$id,$list,$metadataOut);
    }

    /**
     * method: getCloudsList
     * when: called
     * with: emptyParams
     * should: returnList
     */
    public function test_getCloudsList_called_emptyParams_returnList()
    {
        $metadata = array("Stacksync", "Nec");
        $this->apiProviderMock->expects($this->once())
            ->method('getCloudsList')
            ->will($this->returnValue($metadata));
        $result = $this->sut->getCloudsList();
        $this->assertEquals(json_encode($metadata), $result);
    }

    /**
     * method: getCloudsList
     * when: called
     * with: emptyParams
     * should: returnException
     */
    public function test_getCloudsList_called_emptyParams_returnException()
    {
        $metadata =json_decode('{"error":-1}');
        $metadataOut = array("status" => "KO", "error" => -1);
        $this->apiProviderMock->expects($this->once())
            ->method('getCloudsList')
            ->will($this->returnValue($metadata));
        $result = $this->sut->getCloudsList();
        $this->assertEquals($metadataOut, $result);
    }

    /**
     * method: getOauthUrlCloud
     * when: called
     * with: ValidCloud
     * should: returnList
     */
    public function test_getOauthUrlCloud_called_ValidCloud_returnList()
    {
        $cloud = "Stacksync";
        $metadata = json_decode("url_oauth_valid");
        $this->apiProviderMock->expects($this->once())
            ->method('getOauthUrlCloud')
            ->with($cloud)
            ->will($this->returnValue($metadata));
        $result = $this->sut->getOauthUrlCloud($cloud);
        $this->assertEquals($metadata, $result);
    }

    /**
     * method: getOauthUrlCloud
     * when: called
     * with: InvalidCloud
     * should: returnException
     */
    public function test_getOauthUrlCloud_called_InvalidCloud_returnException()
    {
        $cloud = "No_valid_cloud";
        $metadata =json_decode('{"error":-1}');
        $metadataOut = array("status" => "KO", "error" => -1);
        $this->apiProviderMock->expects($this->once())
            ->method('getOauthUrlCloud')
            ->with($cloud)
            ->will($this->returnValue($metadata));
        $result = $this->sut->getOauthUrlCloud($cloud);
        $this->assertEquals($metadataOut, $result);
    }

    private function exerciseCreateMetadata($file,$name,$parent_id,$path,$pathAbsolute,$metadataOut)
    {
        $type = $file?'false':'true';
        $metadata = '{"filename":"' . $name .'","id":"142555444","size":775412,"mimetype":"application/pdf","status":"NEW","version":3,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":'  . $type . '}';
        $metadataU1db = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"' . $name . '","id":"142555444","size":775412,"mimetype":"application/pdf","status":"NEW","version":3,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":' . $type . ',"path":"' . $path . '"}');

        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->cloud,$this->token,$file,$parent_id,true)
            ->will($this->returnValue(json_decode($metadataOut)));

        $this->apiProviderMock->expects($this->at(1))
            ->method('createMetadata')
            ->with($this->cloud,$this->token,$file,$name,$parent_id,$pathAbsolute)
            ->will($this->returnValue(json_decode($metadata)));

        $u1dbIn = new stdClass();
        $u1dbIn->type = 'insert';
        $u1dbIn->lista = array();
        array_push($u1dbIn->lista,$metadataU1db);

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('true'));

        if($file) {
            $params = new stdClass();
            $params->type = "insertDownloadVersion";
            $params->lista = array();
            $aux = new stdClass();
            $aux->id = "142555444";
            $aux->version = 3;
            $aux->recover = false;
            array_push($params->lista,$aux);

            $this->accessorProviderMock->expects($this->at(1))
                ->method('getProcessDataU1db')
                ->with(json_encode($params))
                ->will($this->returnValue('true'));

        }

        $this->sut->createMetadata($this->cloud,$this->token,$this->user,$file,$name,$parent_id,$path,$pathAbsolute);
    }

    private function exerciseDeleteMetadata($metadata,$file,$id,$path,$pathOrig)
    {
        $this->apiProviderMock->expects($this->at(0))
            ->method('deleteMetadata')
            ->with($this->cloud,$this->token,$file,$id)
            ->will($this->returnValue(json_decode($metadata)));

        $params = new stdClass();
        $params->type = "recursiveDeleteVersion";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = "" . $id;
        $aux->user_eyeos = $this->user;
        $aux->cloud = $this->cloud;
        array_push($params->lista,$aux);

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue('true'));

        $metadataU1db = new stdClass();
        $metadataU1db->id = "" . $id;
        $metadataU1db->user_eyeos = $this->user;
        $metadataU1db->cloud = $this->cloud;
        $metadataU1db->path = $path;

        $u1dbIn = new stdClass();
        $u1dbIn->type = 'deleteFolder';
        $u1dbIn->lista = array();
        array_push($u1dbIn->lista,$metadataU1db);

        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('true'));

        $this->sut->deleteMetadata($this->cloud,$this->token,$file,$id,$this->user,$pathOrig);
    }

    private function exerciseRenameMetadata($file,$id,$parent,$path,$name)
    {
        $type = $file?'false':'true';
        $metadata = '{"filename":"' . $name .'","id":"' . $id . '","size":775412,"mimetype":"application/pdf","status":"CHANGED","version":4,"parent_id":"'. $parent . '","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":' . $type . '}';
        $metadataU1db = json_decode('{"user_eyeos":"' . $this->user . '","filename":"' . $name . '","id":"' . $id . '","size":775412,"mimetype":"application/pdf","status":"CHANGED","version":4,"parent_id":"'. $parent . '","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":' . $type . ',"path":"' . $path . '"}');

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

    private function exerciseMoveMetadata($id,$filename,$parent,$file,$pathOrig,$pathNew,$metadataMove,$metadataDelete,$metadataInsert,$fileDest = null)
    {
        $this->apiProviderMock->expects($this->at(0))
            ->method('updateMetadata')
            ->with($this->token,$file,$id,$fileDest?$fileDest:$filename,$parent)
            ->will($this->returnValue(json_decode($metadataMove)));

        $u1dbIn = new stdClass();
        $u1dbIn->type = 'deleteFolder';
        $u1dbIn->lista = array();
        array_push($u1dbIn->lista,json_decode($metadataDelete));

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('true'));

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('true'));

        $this->filesProviderMock->expects($this->at(0))
            ->method('deleteFile')
            ->with($this->path . $pathOrig . $filename,!$file)
            ->will($this->returnValue(true));


        $u1dbIn = new stdClass();
        $u1dbIn->type = 'insert';
        $u1dbIn->lista = array();
        array_push($u1dbIn->lista,$metadataInsert);

        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('true'));

        $fileNew = $fileDest?$fileDest:$filename;

        $this->filesProviderMock->expects($this->at(1))
            ->method('createFile')
            ->with($this->path . $pathNew . $fileNew,!$file)
            ->will($this->returnValue(true));

        $this->sut->moveMetadata($this->token,$file,$id,$this->path,$this->path . "/documents",$this->user,$parent,$filename,$fileDest);

    }

    private function getDownloadMetadata($metadata,$expected,$id)
    {
        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->token,true,$id)
            ->will($this->returnValue(json_decode($metadata)));

        $params = new stdClass();
        $params->type = "getDownloadVersion";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = $id;
        $aux->user_eyeos = $this->user;
        array_push($params->lista,$aux);

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue($expected));
    }

    private function exerciseGetFileVersionData($metadataU1db,$metadataOut,$expected,$id,$version,$path,$type,$exception = false)
    {
        $params = new stdClass();
        $params->type = "getDownloadVersion";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = "" . $id;
        $aux->user_eyeos = $this->user;
        array_push($params->lista,$aux);

        if($exception) {
            $this->accessorProviderMock->expects($this->exactly(1))
                ->method('getProcessDataU1db')
                ->with(json_encode($params))
                ->will($this->returnValue($metadataU1db));
        } else {
            $this->accessorProviderMock->expects($this->at(0))
                ->method('getProcessDataU1db')
                ->with(json_encode($params))
                ->will($this->returnValue($metadataU1db));
        }

        $this->apiProviderMock->expects($this->at(0))
            ->method('getFileVersionData')
            ->with($this->token,$id,$version,$path)
            ->will($this->returnValue(json_decode($metadataOut)));


        if(!$exception) {
            $params = new stdClass();
            $params->type = $type;
            $params->lista = array();
            $aux = new stdClass();
            $aux->id = "" . $id;
            $aux->user_eyeos = $this->user;
            $aux->version = $version;
            $aux->recover = true;
            array_push($params->lista,$aux);

            $this->accessorProviderMock->expects($this->at(1))
                ->method('getProcessDataU1db')
                ->with(json_encode($params))
                ->will($this->returnValue('true'));
        }

        $result = $this->sut->getFileVersionData($this->token,$id,$version,$path,$this->user);
        $this->assertEquals($expected,$result);
    }

    private function exerciseGetListUsersShare($metadata,$id,$expected)
    {
        $this->apiProviderMock->expects($this->once())
            ->method('getListUsersShare')
            ->with($this->token,$id)
            ->will($this->returnValue(json_decode($metadata)));
        $result = $this->sut->getListUsersShare($this->token,$id);
        $this->assertEquals($expected,$result);
    }

    private function exerciseShareFolder($metadata,$id,$list,$expected)
    {
        $this->apiProviderMock->expects($this->once())
            ->method('shareFolder')
            ->with($this->token,$id,$list)
            ->will($this->returnValue(json_decode($metadata)));

        $result = $this->sut->shareFolder($this->token,$id,$list);
        $this->assertEquals($expected,$result);
    }
}

?>