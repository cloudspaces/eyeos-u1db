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
    private $resourceUrl;
    private $IpServer;
    private $username;
    private $timeLimit;

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
        $this->resourceUrl = "http://ast3-deim.urv.cat/v1/";
        $this->IpServer = "192.168.56.101";
        $this->username = 'eyeos';
        $this->timeLimit = 10;
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
        $metadata1 = '{"filename":"root","id":"null","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,
                      "contents":[
                            {"filename":"Client1.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false},
                            {"filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true},
                            {"filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false},
                            {"name":"cloudFolder","access_token_key":"tXn77oo5xmgq4C9koISxf0dSr46Naw","resource_url":"http://ast3-deim.urv.cat/v1/folder/1972","access_token_secret":"Ug40pvqYjNtXD6xBGZZ5rgHR3nLINr"}
                            ]}';

        $metadata2 = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/","is_shared":false}');
        $metadata3 = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false,"path":"/","is_shared":false}');
        $this->exerciseGetMetadatacalledU1dbWithoutData($metadata1,$metadata2,$metadata3,$u1dbIn,$id,$isfile,$contents,"44444755856",$this->pathCloud);

        $u1dbIn = new stdClass();
        $u1dbIn->type = 'insert';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","name":"cloudFolder","access_token_key":"tXn77oo5xmgq4C9koISxf0dSr46Naw","resource_url":"http://ast3-deim.urv.cat/v1/","access_token_secret":"Ug40pvqYjNtXD6xBGZZ5rgHR3nLINr","path":"/","is_shared":false,"id":"1972_Stacksync","parent_id":"null"}');
        array_push($u1dbIn->lista,$metadata);

        $this->filesProviderMock->expects($this->at(2))
            ->method('createFile')
            ->with($this->pathCloud . "/cloudFolder", true)
            ->will($this->returnValue(true));

        $this->accessorProviderMock->expects($this->at(3))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('true'));

        $u1dbIn = new stdClass();
        $u1dbIn->type = 'insert';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"root","id":"null","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,"path":"null","is_shared":false}');
        array_push($u1dbIn->lista,$metadata);

        $this->accessorProviderMock->expects($this->at(4))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('true'));

        $this->sut->getMetadata($this->cloud,$this->token,$id,$this->pathCloud,$this->user);
    }


    /**
     *method: getMetadata
     * when: called
     * with: tokenAndIdAndPathAndUserAndResourceUrl
     * should: calledU1dbWithoutData
     */
    public function test_getMetadata_called_tokenAndIdAndPathAndUserAndResourceUrl_calledU1dbWithoutData()
    {
        $id = '1972';
        $isfile = false;
        $contents = true;
        $u1dbIn = new stdClass();
        $u1dbIn->type = 'select';
        $u1dbIn->lista = array();
        $file = new stdClass();
        $file->id = "1972";
        $file->user_eyeos = $this->user;
        $file->cloud = $this->cloud;
        $file->path = "/cloudFolder/";
        array_push($u1dbIn->lista,$file);
        $metadata1 = '{"filename":"cloudFolder","id":"1972","status":"NEW","version":1,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,
                      "contents":[
                            {"filename":"Client1.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false},
                            {"filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true},
                            {"filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false}
                            ]}';

        $metadata2 = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/cloudFolder/","is_shared":false,"resource_url":"' . $this->resourceUrl . '","access_token_key":"1234","access_token_secret":"ABCD"}');
        $metadata3 = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false,"path":"/cloudFolder/","is_shared":false,"resource_url":"' . $this->resourceUrl . '","access_token_key":"1234","access_token_secret":"ABCD"}');

        $this->exerciseGetMetadatacalledU1dbWithoutData($metadata1,$metadata2,$metadata3,$u1dbIn,$id,$isfile,$contents,"1972",$this->pathCloud . "/cloudFolder",$this->resourceUrl);
        $this->sut->getMetadata($this->cloud,$this->token,$id,$this->pathCloud . "/cloudFolder",$this->user,$this->resourceUrl);
    }


    /**
     *method: getMetadata
     * when: called
     * with: tokenAndIdAndPathAndUser
     * should: calledU1dbSameData
     */
    public function test_getMetadata_called_tokenAndIdAndPathAndUser_calledU1dbSameData()
    {
        $path = '/';
        $metadata = '{"filename":"root","id":"null","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,
                      "contents":[
                            {"filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true},
                            {"filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false},
                            {"name":"cloudFolder","access_token_key":"tXn77oo5xmgq4C9koISxf0dSr46Naw","resource_url":"http://ast3-deim.urv.cat/v1/folder/1972","access_token_secret":"Ug40pvqYjNtXD6xBGZZ5rgHR3nLINr"}
                            ]}';

        $u1dbOut = '[{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/","is_shared":true},
                    {"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false,"path":"/","is_shared":false},
                    {"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","name":"cloudFolder","access_token_key":"tXn77oo5xmgq4C9koISxf0dSr46Naw","resource_url":"http://ast3-deim.urv.cat/v1/folder/1972","access_token_secret":"Ug40pvqYjNtXD6xBGZZ5rgHR3nLINr","path":"/","is_shared":false,"id":"1972_Stacksync","parent_id":"null"},
                    {"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"root","id":"null","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,"path":"null","is_shared":false}]';


        $this->exerciseGetMetadatacalledU1dbSameData("root","null",$metadata,$u1dbOut,"44444755856",$path,$this->pathCloud,true);

    }

    /**
     *method: getMetadata
     * when: called
     * with: tokenAndIdAndPathAndUserAndResourceUrl
     * should: calledU1dbSameData
     */
    public function test_getMetadata_called_tokenAndIdAndPathAndUserAndResourceUrl_calledU1dbSameData()
    {
        $id = "1972";
        $path =  "/cloudFolder/";

        $metadata = '{"filename":"cloudFolder","id":"1972","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,
                      "contents":[
                            {"filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true},
                            {"filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false}
                            ]}';

        $u1dbOut = '[{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"NEW","version":1,"parent_id":"1972_Stacksync","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/cloudFolder/","is_shared":true,"access_token_key":"tXn77oo5xmgq4C9koISxf0dSr46Naw","resource_url":"http://ast3-deim.urv.cat/v1","access_token_secret":"Ug40pvqYjNtXD6xBGZZ5rgHR3nLINr"},
                    {"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"1972_Stacksync","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false,"path":"/cloudFolder/","is_shared":false,"access_token_key":"tXn77oo5xmgq4C9koISxf0dSr46Naw","resource_url":"http://ast3-deim.urv.cat/v1","access_token_secret":"Ug40pvqYjNtXD6xBGZZ5rgHR3nLINr"},
                    {"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"cloudFolder","id":"1972_Stacksync","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,"path":"null","is_shared":false,"access_token_key":"tXn77oo5xmgq4C9koISxf0dSr46Naw","resource_url":"http://ast3-deim.urv.cat/v1","access_token_secret":"Ug40pvqYjNtXD6xBGZZ5rgHR3nLINr"}]';

        $this->exerciseGetMetadatacalledU1dbSameData($id,$id,$metadata,$u1dbOut,$id,$path,$this->pathCloud . "/cloudFolder",true,$this->resourceUrl);
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
                        {"filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true},
                        {"name":"cloudFolder","access_token_key":"tXn77oo5xmgq4C9koISxf0dSr46Naw","resource_url":"http://ast3-deim.urv.cat/v1/folder/1972","access_token_secret":"Ug40pvqYjNtXD6xBGZZ5rgHR3nLINr"},
                        {"name":"cloudFolder2","access_token_key":"tXn77oo5xmgq4C9koISxf0dSr46Naw","resource_url":"http://ast3-deim.urv.cat/v1/folder/1973","access_token_secret":"Ug40pvqYjNtXD6xBGZZ5rgHR3nLINr"}
                     ]}';
        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->cloud,$this->token,$isfile,$id,$contents)
            ->will($this->returnValue(json_decode($metadata)));

        $this->apiProviderMock->expects($this->at(1))
            ->method('getListUsersShare')
            ->with($this->cloud, $this->token, "885526111")
            ->will($this->returnValue(json_decode('[{"joined_at": "2015-03-27", "is_owner": true, "name": "eyeos", "email": "eyeos@test.com"}]')));

        $this->apiProviderMock->expects($this->at(2))
            ->method('getListUsersShare')
            ->with($this->cloud, $this->token, "44444755856")
            ->will($this->returnValue(json_decode('[{"joined_at": "2015-03-27", "is_owner": true, "name": "eyeos", "email": "eyeos@test.com"}, {"joined_at": "2015-03-27", "is_owner": false, "name": "aaaaa", "email": "aaaaa@test.com"}]')));

        $u1dbOut = '[{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"root","id":"null","status":"NEW","version":1,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,"path":"null","is_shared":false},
                    {"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"client","id":334254755856,"size":775412,"status":"NEW","version":1,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/","is_shared":false},
                    {"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"client1","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"NEW","version":1,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/","is_shared":false},
                    {"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false,"path":"/","is_shared":false},
                    {"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","name":"cloudFolder1","access_token_key":"tXn77oo5xmgq4C9koISxf0dSr46Naw","resource_url":"http://ast3-deim.urv.cat/v1/","access_token_secret":"Ug40pvqYjNtXD6xBGZZ5rgHR3nLINr","path":"/","is_shared":false,"id":"1973_Stacksync","parent_id":"null"},
                    {"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","name":"cloudFolder_44","access_token_key":"tXn77oo5xmgq4C9koISxf0dSr46New","resource_url":"http://ast3-deim.urv.cat/v1/","access_token_secret":"Ug40pvqYjNtXD6xBGZZ5rgHR3nLINr","path":"/","is_shared":false,"id":"1974_Stacksync","parent_id":"null"}]';
        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue($u1dbOut));

        $u1dbIn->type = 'deleteFolder';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"client","id":334254755856,"size":775412,"status":"DELETED","version":2,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/","is_shared":false}');
        array_push($u1dbIn->lista,$metadata);
        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue(true));

        $this->filesProviderMock->expects($this->at(0))
            ->method('deleteFile')
            ->with($this->pathCloud . '/client', true)
            ->will($this->returnValue(true));

        $this->filesProviderMock->expects($this->at(1))
            ->method('createFile')
            ->with($this->pathCloud . '/provider', true)
            ->will($this->returnValue(true));

        $u1dbIn->type = 'insert';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"provider","id":885526111,"size":775412,"status":"NEW","version":1,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/","is_shared":false}');
        array_push($u1dbIn->lista,$metadata);
        $this->accessorProviderMock->expects($this->at(2))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue(true));

        $this->filesProviderMock->expects($this->at(2))
            ->method('renameFile')
            ->with($this->pathCloud . '/client1', 'client2')
            ->will($this->returnValue(true));

        $u1dbIn->type = 'update';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/","is_shared":true}');
        array_push($u1dbIn->lista,json_decode('{"parent_old":"null"}'));
        array_push($u1dbIn->lista,$metadata);
        $this->accessorProviderMock->expects($this->at(3))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue(true));

        $this->filesProviderMock->expects($this->at(3))
            ->method('createFile')
            ->with($this->pathCloud . '/cloudFolder', true)
            ->will($this->returnValue(true));

        $u1dbIn->type = 'insert';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","name":"cloudFolder","access_token_key":"tXn77oo5xmgq4C9koISxf0dSr46Naw","resource_url":"http://ast3-deim.urv.cat/v1/","access_token_secret":"Ug40pvqYjNtXD6xBGZZ5rgHR3nLINr","path":"/","is_shared":false,"id":"1972_Stacksync","parent_id":"null"}');
        array_push($u1dbIn->lista, $metadata);
        $this->accessorProviderMock->expects($this->at(4))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue(true));

        $this->filesProviderMock->expects($this->at(4))
            ->method('renameFile')
            ->with($this->pathCloud . '/cloudFolder1', 'cloudFolder2')
            ->will($this->returnValue(true));

        $u1dbIn->type = 'update';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","name":"cloudFolder2","access_token_key":"tXn77oo5xmgq4C9koISxf0dSr46Naw","resource_url":"http://ast3-deim.urv.cat/v1/","access_token_secret":"Ug40pvqYjNtXD6xBGZZ5rgHR3nLINr","path":"/","is_shared":false,"id":"1973_Stacksync","parent_id":"null"}');
        array_push($u1dbIn->lista, $metadata);
        $this->accessorProviderMock->expects($this->at(5))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue(true));


        $this->filesProviderMock->expects($this->at(5))
            ->method('deleteFile')
            ->with($this->pathCloud . '/Client3.pdf', false)
            ->will($this->returnValue(true));

        $u1dbIn->type = 'deleteFolder';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false,"path":"/","is_shared":false}');
        array_push($u1dbIn->lista,$metadata);
        $this->accessorProviderMock->expects($this->at(6))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue(true));

        $this->filesProviderMock->expects($this->at(6))
            ->method('deleteFile')
            ->with($this->pathCloud . '/cloudFolder_44', true)
            ->will($this->returnValue(true));

        $u1dbIn->type = 'deleteFolder';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","name":"cloudFolder_44","access_token_key":"tXn77oo5xmgq4C9koISxf0dSr46New","resource_url":"http://ast3-deim.urv.cat/v1/","access_token_secret":"Ug40pvqYjNtXD6xBGZZ5rgHR3nLINr","path":"/","is_shared":false,"id":"1974_Stacksync","parent_id":"null"}');
        array_push($u1dbIn->lista, $metadata);
        $this->accessorProviderMock->expects($this->at(7))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue(true));

        $this->sut->getMetadata($this->cloud, $this->token, $id, $this->pathCloud, $this->user);
    }


    /**
     *method: getMetadata
     * when: called
     * with: tokenAndIdAndPathAndUserAndResourceUrl
     * should: calledU1dbSameData
     */
    public function test_getMetadata_called_tokenAndIdAndPathAndUserAndResourceUrl_calledDistinctData()
    {
        $id = '1972';
        $isfile = false;
        $contents = true;
        $u1dbIn = new stdClass();
        $u1dbIn->type = 'select';
        $u1dbIn->lista = array();
        $file = new stdClass();
        $file->id = "1972";
        $file->user_eyeos = $this->user;
        $file->cloud = $this->cloud;
        $file->path = "/cloudFolder/";
        array_push($u1dbIn->lista,$file);
        $metadata = '{"filename":"cloudFolder","id":"1972","status":"NEW","version":1,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,
                    "contents":[
                        {"filename":"Client1.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false},
                        {"filename":"client","id":334254755856,"size":775412,"status":"DELETED","version":2,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true},
                        {"filename":"provider","id":885526111,"size":775412,"status":"NEW","version":1,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true},
                        {"filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true}
                     ]}';


        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->cloud,$this->token,$isfile,$id,$contents,$this->resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $this->apiProviderMock->expects($this->at(1))
            ->method('getListUsersShare')
            ->with($this->cloud, $this->token, "1972",$this->resourceUrl)
            ->will($this->returnValue(json_decode('[{"joined_at": "2015-03-27", "is_owner": true, "name": "eyeos", "email": "eyeos@test.com"}]')));

        $this->apiProviderMock->expects($this->at(2))
            ->method('getListUsersShare')
            ->with($this->cloud, $this->token, "885526111",$this->resourceUrl)
            ->will($this->returnValue(json_decode('[{"joined_at": "2015-03-27", "is_owner": true, "name": "eyeos", "email": "eyeos@test.com"}, {"joined_at": "2015-03-27", "is_owner": false, "name": "aaaaa", "email": "aaaaa@test.com"}]')));

        $this->apiProviderMock->expects($this->at(3))
            ->method('getListUsersShare')
            ->with($this->cloud, $this->token, "44444755856",$this->resourceUrl)
            ->will($this->returnValue(json_decode('[{"joined_at": "2015-03-27", "is_owner": true, "name": "eyeos", "email": "eyeos@test.com"}, {"joined_at": "2015-03-27", "is_owner": false, "name": "aaaaa", "email": "aaaaa@test.com"}]')));


        $u1dbOut = '[{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"cloudFolder","id":"1972_Stacksync","status":"NEW","version":1,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,"path":"/","is_shared":false},
                    {"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"client","id":334254755856,"size":775412,"status":"NEW","version":1,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/cloudFolder/","is_shared":false},
                    {"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"client1","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"NEW","version":1,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/","is_shared":false},
                    {"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false,"path":"/cloudFolder/","is_shared":false},
                    {"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","name":"cloudFolder1","access_token_key":"tXn77oo5xmgq4C9koISxf0dSr46Naw","resource_url":"http://ast3-deim.urv.cat/v1","access_token_secret":"Ug40pvqYjNtXD6xBGZZ5rgHR3nLINr","path":"/","is_shared":false,"id":"1973_Stacksync","parent_id":"null"
                    }]';

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue($u1dbOut));

        $u1dbIn->type = 'deleteFolder';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"client","id":334254755856,"size":775412,"status":"DELETED","version":2,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/cloudFolder/","is_shared":false,"resource_url":"http://ast3-deim.urv.cat/v1/","access_token_key":"1234","access_token_secret":"ABCD"}');
        array_push($u1dbIn->lista,$metadata);
        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue(true));

        $this->filesProviderMock->expects($this->at(0))
            ->method('deleteFile')
            ->with($this->pathCloud . '/cloudFolder/client', true)
            ->will($this->returnValue(true));

        $this->filesProviderMock->expects($this->at(1))
            ->method('createFile')
            ->with($this->pathCloud . '/cloudFolder/provider', true)
            ->will($this->returnValue(true));


        $u1dbIn->type = 'insert';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"provider","id":885526111,"size":775412,"status":"NEW","version":1,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/cloudFolder/","is_shared":true,"resource_url":"http://ast3-deim.urv.cat/v1/","access_token_key":"1234","access_token_secret":"ABCD"}');
        array_push($u1dbIn->lista,$metadata);
        $this->accessorProviderMock->expects($this->at(2))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue(true));


        $this->filesProviderMock->expects($this->at(2))
            ->method('renameFile')
            ->with($this->pathCloud . '/cloudFolder/client1', 'client2')
            ->will($this->returnValue(true));

        $u1dbIn->type = 'update';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true,"path":"/cloudFolder/","is_shared":true,"resource_url":"http://ast3-deim.urv.cat/v1/","access_token_key":"1234","access_token_secret":"ABCD"}');
        array_push($u1dbIn->lista,json_decode('{"parent_old":"null"}'));
        array_push($u1dbIn->lista,$metadata);
        $this->accessorProviderMock->expects($this->at(3))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue(true));


        $this->filesProviderMock->expects($this->at(3))
            ->method('deleteFile')
            ->with($this->pathCloud . '/cloudFolder/Client3.pdf', false)
            ->will($this->returnValue(true));

        $u1dbIn->type = 'deleteFolder';
        $u1dbIn->lista = array();
        $metadata = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false,"path":"/cloudFolder/","is_shared":false}');
        array_push($u1dbIn->lista,$metadata);
        $this->accessorProviderMock->expects($this->at(4))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue(true));

        $this->sut->getMetadata($this->cloud, $this->token, $id, $this->pathCloud . "/cloudFolder", $this->user,$this->resourceUrl);

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
     * with: tokenAndIdAndPathAndUserAndResourceUrl
     * should: returnException
     */
    public function test_getMetadata_called_tokenAndIdAndPathAndUserAndResourceUrl_returnException()
    {
        $id = 11165632156;
        $metadata = '{"error":404}';
        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->cloud,$this->token,false,$id,true,$this->resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $this->sut->getMetadata($this->cloud,$this->token,$id,$this->pathCloud,$this->user,$this->resourceUrl);
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
     * method: getMetadata
     * when: called
     * with: tokenAndIdAndPathAndUserAndResourceUrl
     * should: returnPermissionDenied
     */
    public function test_getMetadata_called_tokenAndIdAndPathAndUserAndResourceUrl_returnPermissionDenied()
    {
        $id = 11165632156;
        $metadata = '{"error":403}';
        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->cloud,$this->token,false,$id,true,$this->resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $this->sut->getMetadata($this->cloud,$this->token,$id,$this->pathCloud,$this->user,$this->resourceUrl);
    }

    /**
     * method: getSkel
     * when: called
     * with: cloudAndTokenAndIsFileAndIdAndMetadatas
     * should: callMetadataFileApiStore
     */
    public function test_getSkel_called_cloudAndTokenAndIsFileAndIdAndMetadatas_callMetadataFileApiStore()
    {
        $id = 142555444;
        $metadatas = array();
        $cloud = "Stacksync";
        $path = '/documents/';
        $metadata = '{"filename": "Client1.pdf", "id": 142555444, "size": 775412, "mimetype": "application/pdf", "status": "DELETED", "version": 3, "parent_id": 32565632156, "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997", "is_folder": false}';
        $newmetadata = json_decode($metadata);
        $newmetadata->pathAbsolute = $this->path . $path . 'Client1.pdf';
        $newmetadata->path = $path;
        $newmetadata->pathEyeos =  $this->path . $path . 'Client1.pdf';
        $expected = array($newmetadata);
        $this->apiProviderMock->expects($this->once())
            ->method('getMetadata')
            ->with($cloud, $this->token,true,$id)
            ->will($this->returnValue(json_decode($metadata)));

        $this->sut->getSkel($cloud, $this->token, true, $id, $metadatas, $path, $newmetadata->pathAbsolute, $this->path . "/documents");
        $this->assertEquals($expected, $metadatas);
    }

    /**
     * method: getSkel
     * when: called
     * with: cloudAndTokenAndIsFileAndIdAndMetadatasAndResourceUrl
     * should: callMetadataFileApiStore
     */
    public function test_getSkel_called_cloudAndTokenAndIsFileAndIdAndMetadatasAndResourceUrl_callMetadataFileApiStore()
    {
        $id = 142555444;
        $metadatas = array();
        $cloud = "Stacksync";
        $path = '/documents/';
        $metadata = '{"filename": "Client1.pdf", "id": 142555444, "size": 775412, "mimetype": "application/pdf", "status": "DELETED", "version": 3, "parent_id": 32565632156, "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997", "is_folder": false}';
        $newmetadata = json_decode($metadata);
        $newmetadata->pathAbsolute = $this->path . $path . 'Client1.pdf';
        $newmetadata->path = $path;
        $newmetadata->pathEyeos =  $this->path . $path . 'Client1.pdf';
        $expected = array($newmetadata);
        $this->apiProviderMock->expects($this->once())
            ->method('getMetadata')
            ->with($cloud, $this->token,true,$id,null,$this->resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $this->sut->getSkel($cloud, $this->token, true, $id, $metadatas, $path, $newmetadata->pathAbsolute, $this->path . "/documents", $this->resourceUrl);
        $this->assertEquals($expected, $metadatas);
    }

    /**
     * method: getSkel
     * when: called
     * with: cloudAndtokenAndIsFileAndIdAndMetadatas
     * should: callMetadataFileApiStore
     */
    public function test_getSkel_called_cloudAndTokenAndIsFolderAndIdAndMetadatas_callMetadataFolderApiStore()
    {
        $id = -8090905582480578692;
        $metadatas = array();
        $cloud = "Stacksync";
        $path = '/';
        $metadataFile2 = '{"filename": "Client1.pdf", "id": 142555444, "size": 775412, "mimetype": "application/pdf", "status": "DELETED", "version": 3, "parent_id": 32565632156, "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997", "is_folder": false}';
        $metadataFile = '{"id": 32565632156, "parent_id": -8090905582480578692, "filename": "a", "is_folder": true, "status": "NEW", "server_modified": "2014-03-11 14:22:45.757", "client_modified": "2014-03-11 14:22:45.757", "user": "web", "version": 1, "checksum": 589445744, "size": 166, "mimetype": "text/plain", "chunks": [], "is_root": false,
                          "contents":[
                                {"filename": "Client1.pdf", "id": 142555444, "size": 775412, "mimetype": "application/pdf", "status": "DELETED", "version": 3, "parent_id": 32565632156, "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997", "is_folder":false}
                         ]}';
        $metadata='{"id": -8090905582480578692, "parent_id": null, "filename": "Cloudspaces", "is_folder": true, "status": "NEW", "server_modified": "2014-03-11 14:22:45.757", "client_modified": "2014-03-11 14:22:45.757", "user": "web", "version": 1, "checksum": 589445744, "size": 166, "mimetype": "text/plain", "chunks": [], "is_root": false,
                    "contents":[
                        {"id": 32565632156, "parent_id": -8090905582480578692, "filename": "a", "is_folder": true, "status": "NEW", "server_modified": "2014-03-11 14:22:45.757", "client_modified": "2014-03-11 14:22:45.757", "user": "web", "version": 1, "checksum": 589445744, "size": 166, "mimetype": "text/plain", "chunks": [], "is_root": false}
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
            ->with($cloud, $this->token, false, $id, true)
            ->will($this->returnValue(json_decode($metadata)));

        $this->apiProviderMock->expects($this->at(1))
            ->method('getMetadata')
            ->with($cloud, $this->token, false, 32565632156, true)
            ->will($this->returnValue(json_decode($metadataFile)));

        $this->apiProviderMock->expects($this->at(2))
            ->method('getMetadata')
            ->with($cloud, $this->token, true, 142555444, null)
            ->will($this->returnValue(json_decode($metadataFile2)));

        $this->sut->getSkel($cloud, $this->token, false, $id, $metadatas, $path, $data->pathAbsolute, $this->path);
        $this->assertEquals($expected,$metadatas);
    }

    /**
     * method: getSkel
     * when: called
     * with: cloudAndtokenAndIsFileAndIdAndMetadatasAndResourceUrl
     * should: callMetadataFileApiStore
     */
    public function test_getSkel_called_cloudAndTokenAndIsFolderAndIdAndMetadatasAndResourceUrl_callMetadataFolderApiStore()
    {
        $id = -8090905582480578692;
        $metadatas = array();
        $cloud = "Stacksync";
        $path = '/';
        $metadataFile2 = '{"filename": "Client1.pdf", "id": 142555444, "size": 775412, "mimetype": "application/pdf", "status": "DELETED", "version": 3, "parent_id": 32565632156, "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997", "is_folder": false}';
        $metadataFile = '{"id": 32565632156, "parent_id": -8090905582480578692, "filename": "a", "is_folder": true, "status": "NEW", "server_modified": "2014-03-11 14:22:45.757", "client_modified": "2014-03-11 14:22:45.757", "user": "web", "version": 1, "checksum": 589445744, "size": 166, "mimetype": "text/plain", "chunks": [], "is_root": false,
                          "contents":[
                                {"filename": "Client1.pdf", "id": 142555444, "size": 775412, "mimetype": "application/pdf", "status": "DELETED", "version": 3, "parent_id": 32565632156, "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997", "is_folder":false}
                         ]}';
        $metadata='{"id": -8090905582480578692, "parent_id": null, "filename": "Cloudspaces", "is_folder": true, "status": "NEW", "server_modified": "2014-03-11 14:22:45.757", "client_modified": "2014-03-11 14:22:45.757", "user": "web", "version": 1, "checksum": 589445744, "size": 166, "mimetype": "text/plain", "chunks": [], "is_root": false,
                    "contents":[
                        {"id": 32565632156, "parent_id": -8090905582480578692, "filename": "a", "is_folder": true, "status": "NEW", "server_modified": "2014-03-11 14:22:45.757", "client_modified": "2014-03-11 14:22:45.757", "user": "web", "version": 1, "checksum": 589445744, "size": 166, "mimetype": "text/plain", "chunks": [], "is_root": false}
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
            ->with($cloud, $this->token, false, $id, true,$this->resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $this->apiProviderMock->expects($this->at(1))
            ->method('getMetadata')
            ->with($cloud, $this->token, false, 32565632156, true,$this->resourceUrl)
            ->will($this->returnValue(json_decode($metadataFile)));

        $this->apiProviderMock->expects($this->at(2))
            ->method('getMetadata')
            ->with($cloud, $this->token, true, 142555444, null,$this->resourceUrl)
            ->will($this->returnValue(json_decode($metadataFile2)));

        $this->sut->getSkel($cloud, $this->token, false, $id, $metadatas, $path, $data->pathAbsolute, $this->path, $this->resourceUrl);
        $this->assertEquals($expected,$metadatas);
    }

    /**
     * method: getSkel
     * when: called
     * with: cloudAndTokenAndIsFileAndIdAndMetadatas
     * should: returnPermissionDenied
     */
    public function test_getSkel_called_cloudAndTokenAndIsFolderAndIdAndMetadatas_returnPermissionDenied()
    {
        $metadata='{"id": -8090905582480578692, "parent_id": null, "filename": "Cloudspaces", "is_folder": true, "status": "NEW", "server_modified": "2014-03-11 14:22:45.757", "client_modified": "2014-03-11 14:22:45.757", "user": "web", "version": 1, "checksum": 589445744, "size": 166, "mimetype": "text/plain", "chunks": [], "is_root": false,
                    "contents":[
                        {"id": 32565632156, "parent_id": -8090905582480578692, "filename": "a", "is_folder": true, "status": "NEW", "server_modified": "2014-03-11 14:22:45.757", "client_modified": "2014-03-11 14:22:45.757", "user": "web", "version": 1, "checksum": 589445744, "size": 166, "mimetype": "text/plain", "chunks": [], "is_root": false}
                    ]}';

        $metadataError = '{"error": 403}';
        $cloud = "Stacksync";
        $id = -8090905582480578692;
        $path = '/';
        $metadatas = array();
        $expected = array();
        array_push($expected,json_decode($metadataError));
        array_push($expected,json_decode('{"id": -8090905582480578692, "parent_id": null, "filename": "Cloudspaces", "is_folder": true, "status": "NEW", "server_modified": "2014-03-11 14:22:45.757", "client_modified": "2014-03-11 14:22:45.757", "user": "web", "version": 1, "checksum": 589445744, "size": 166, "mimetype": "text/plain", "chunks": [], "is_root": false, "path": "/", "pathAbsolute": null, "pathEyeos": "' . $this->path . '/Cloudspaces"}'));

        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($cloud, $this->token, false, $id, true)
            ->will($this->returnValue(json_decode($metadata)));

        $this->apiProviderMock->expects($this->at(1))
            ->method('getMetadata')
            ->with($cloud, $this->token, false, 32565632156, true)
            ->will($this->returnValue(json_decode($metadataError)));

        $this->sut->getSkel($cloud, $this->token, false, $id, $metadatas, $path, null, $this->path);
        $this->assertEquals($expected, $metadatas);
    }

    /**
     * method: getSkel
     * when: called
     * with: cloudAndTokenAndIsFileAndIdAndMetadatasAndResourceUrl
     * should: returnPermissionDenied
     */
    public function test_getSkel_called_cloudAndTokenAndIsFolderAndIdAndMetadatasAndResourceUrl_returnPermissionDenied()
    {
        $metadata='{"id": -8090905582480578692, "parent_id": null, "filename": "Cloudspaces", "is_folder": true, "status": "NEW", "server_modified": "2014-03-11 14:22:45.757", "client_modified": "2014-03-11 14:22:45.757", "user": "web", "version": 1, "checksum": 589445744, "size": 166, "mimetype": "text/plain", "chunks": [], "is_root": false,
                    "contents":[
                        {"id": 32565632156, "parent_id": -8090905582480578692, "filename": "a", "is_folder": true, "status": "NEW", "server_modified": "2014-03-11 14:22:45.757", "client_modified": "2014-03-11 14:22:45.757", "user": "web", "version": 1, "checksum": 589445744, "size": 166, "mimetype": "text/plain", "chunks": [], "is_root": false}
                    ]}';

        $metadataError = '{"error": 403}';
        $cloud = "Stacksync";
        $id = -8090905582480578692;
        $path = '/';
        $metadatas = array();
        $expected = array();
        array_push($expected,json_decode($metadataError));
        array_push($expected,json_decode('{"id": -8090905582480578692, "parent_id": null, "filename": "Cloudspaces", "is_folder": true, "status": "NEW", "server_modified": "2014-03-11 14:22:45.757", "client_modified": "2014-03-11 14:22:45.757", "user": "web", "version": 1, "checksum": 589445744, "size": 166, "mimetype": "text/plain", "chunks": [], "is_root": false, "path": "/", "pathAbsolute": null, "pathEyeos": "' . $this->path . '/Cloudspaces"}'));

        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($cloud, $this->token, false, $id, true, $this->resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $this->apiProviderMock->expects($this->at(1))
            ->method('getMetadata')
            ->with($cloud, $this->token, false, 32565632156, true,$this->resourceUrl)
            ->will($this->returnValue(json_decode($metadataError)));

        $this->sut->getSkel($cloud, $this->token, false, $id, $metadatas, $path, null, $this->path,$this->resourceUrl);
        $this->assertEquals($expected, $metadatas);
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
        $this->exerciseCreateMetadata(true, $name, $parent_id, $path, $pathabsolute, $metadataOut);
    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndUserAndIsFileAndNameAndParentIdAndPathAndPathAbsoluteAndResourceUrl
     * should: calledStackSyncNoDataInsertMetadata
     */
    public function test_createMetadata_called_tokenAndUserAndIsFileAndNameAndParentIdAndPathAndPathAbsoluteAndResourceUrl_calledStackSyncNoDataInsertMetadata()
    {
        $name = "client.pdf";
        $parent_id = "null";
        $path = '/';
        $pathabsolute = '/home/eyeos/' . $name;
        $metadataOut = '{"id":32565632156,"parent_id":"null","filename":"root","is_folder":true,"status":"NEW","server_modified":"2014-03-11 14:22:45.757","client_modified":"2014-03-11 14:22:45.757","user":"web","version":1,"checksum":589445744,"size":166,"mimetype":"text/plain","chunks":[],"is_root":false,
                          "contents":[]}';
        $this->exerciseCreateMetadata(true, $name, $parent_id, $path, $pathabsolute, $metadataOut, $this->resourceUrl);
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
     * with: tokenAndUserAndIsFileAndNameAndParentIdAndPathAndPathAbsoluteAndResourceUrl
     * should: calledStackSyncDataInsertMetadata
     */
    public function test_createMetadata_called_tokenAndUserAndIsFileAndNameAndParentIdAndPathAndPathAbsoluteAndResourceUrl_calledStackSyncDataInsertMetadata()
    {
        $name = "client.pdf";
        $parent_id = "null";
        $path = '/';
        $pathabsolute = '/home/eyeos/' . $name;
        $metadataOut = '{"id":32565632156,"parent_id":"null","filename":"root","is_folder":true,"status":"NEW","server_modified":"2014-03-11 14:22:45.757","client_modified":"2014-03-11 14:22:45.757","user":"web","version":1,"checksum":589445744,"size":166,"mimetype":"text/plain","chunks":[],"is_root":false,
                          "contents":[
                            {"filename":"prueba.pdf","id":"8888888","status":"NEW","version":1,"parent_id":32565632156,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false}
                          ]}';
        $this->exerciseCreateMetadata(true,$name,$parent_id,$path,$pathabsolute,$metadataOut,$this->resourceUrl);
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
        $metadataFile = '{"cloud": "' . $this->cloud . '", "filename":"client.pdf","id":"1111111","status":"CHANGED","version":2,"parent_id":32565632156,"user":"eyeos","size":134,"client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false}';
        $metadataUpdate = '{"cloud": "' . $this->cloud . '", "user_eyeos":"' . $this->user . '","filename":"client.pdf","id":"1111111","status":"CHANGED","version":2,"parent_id":32565632156,"user":"eyeos","size":134,"client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false,"path":"/"}';

        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->cloud, $this->token, false, $parent_id, true)
            ->will($this->returnValue(json_decode($metadataOut)));

        $this->apiProviderMock->expects($this->never())
            ->method('createMetadata');

        $this->apiProviderMock->expects($this->at(1))
            ->method('uploadMetadata')
            ->with($this->cloud, $this->token, $id, $pathabsolute)
            ->will($this->returnValue(json_decode('{"status": true}')));

        $this->apiProviderMock->expects($this->at(2))
            ->method('getMetadata')
            ->with($this->cloud, $this->token, true, $id)
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
        $aux->user_eyeos = $this->user;
        $aux->cloud = $this->cloud;
        $aux->version = 2;
        $aux->recover = false;
        array_push($params->lista,$aux);

        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue('true'));

        $this->sut->createMetadata($this->cloud, $this->token, $this->user, true, $name, $parent_id, $path, $pathabsolute);
    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndUserAndIsFileAndNameAndParentIdAndPathAndPathAbsoluteAndResourceUrl
     * should: calledStackSyncDataExistsMetadata
     */
    public function test_createMetadata_called_tokenAndUserAndIsFileAndNameAndParentIdAndPathAndPathAbsoluteAndResourceUrl_calledStackSyncDataExistsMetadata()
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
        $metadataFile = '{"cloud": "' . $this->cloud . '", "filename":"client.pdf","id":"1111111","status":"CHANGED","version":2,"parent_id":32565632156,"user":"eyeos","size":134,"client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false}';
        $metadataUpdate = '{"cloud": "' . $this->cloud . '", "user_eyeos":"' . $this->user . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1\/","access_token_key":"1234","access_token_secret":"ABCD","filename":"client.pdf","id":"1111111","status":"CHANGED","version":2,"parent_id":32565632156,"user":"eyeos","size":134,"client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false,"path":"/"}';

        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->cloud, $this->token, false, $parent_id, true,$this->resourceUrl)
            ->will($this->returnValue(json_decode($metadataOut)));

        $this->apiProviderMock->expects($this->never())
            ->method('createMetadata');

        $this->apiProviderMock->expects($this->at(1))
            ->method('uploadMetadata')
            ->with($this->cloud, $this->token, $id, $pathabsolute,$this->resourceUrl)
            ->will($this->returnValue(json_decode('{"status": true}')));

        $this->apiProviderMock->expects($this->at(2))
            ->method('getMetadata')
            ->with($this->cloud, $this->token, true, $id, null,$this->resourceUrl)
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
        $aux->user_eyeos = $this->user;
        $aux->cloud = $this->cloud;
        $aux->version = 2;
        $aux->recover = false;
        array_push($params->lista,$aux);

        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue('true'));

        $this->sut->createMetadata($this->cloud, $this->token, $this->user, true, $name, $parent_id, $path, $pathabsolute, $this->resourceUrl);
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
        $this->exerciseCreateMetadata(false, $name, $parent_id, $path, null, $metadataOut);
    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndUserAndIsFolderAndNameAndParentIdAndPathAndResourceUrl
     * should: calledStackSyncNoDataInsertMetadata
     */
    public function test_createMetadata_called_tokenAndUserAndIsFolderAndNameAndParentIdAndPathAndResourceUrl_calledStackSyncNoDataInsertMetadata()
    {
        $name = "client";
        $parent_id = "null";
        $path = '/';
        $metadataOut = '{"id":32565632156,"parent_id":"null","filename":"root","is_folder":true,"status":"NEW","server_modified":"2014-03-11 14:22:45.757","client_modified":"2014-03-11 14:22:45.757","user":"web","version":1,"checksum":589445744,"size":166,"mimetype":"text/plain","chunks":[],"is_root":false,
                          "contents":[]}';
        $this->exerciseCreateMetadata(false, $name, $parent_id, $path, null, $metadataOut, $this->resourceUrl);
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
     * with: tokenAndUserAndIsFolderAndNameAndParentIdAndPathAndResourceUrl
     * should: calledStackSyncDataInsertMetadata
     */
    public function test_createMetadata_called_tokenAndUserAndIsFolderAndNameAndParentIdAndPathAndResourceUrl_calledStackSyncDataInsertMetadata()
    {
        $name = "client";
        $parent_id = "null";
        $path = '/';
        $metadataOut = '{"id":32565632156,"parent_id":"null","filename":"root","is_folder":true,"status":"NEW","server_modified":"2014-03-11 14:22:45.757","client_modified":"2014-03-11 14:22:45.757","user":"web","version":1,"checksum":589445744,"size":166,"mimetype":"text/plain","chunks":[],"is_root":false,
                          "contents":[
                            {"filename":"prueba.pdf","id":"8888888","status":"NEW","version":1,"parent_id":32565632156,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false}
                          ]}';
        $this->exerciseCreateMetadata(false,$name,$parent_id,$path,null,$metadataOut,$this->resourceUrl);
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
     * with: tokenAndUserAndIsFolderAndNameAndParentIdAndPathAndResourceUrl
     * should: calledStackSyncDataExistsMetadata
     */
    public function test_createMetadata_called_tokenAndUserAndIsFolderAndNameAndParentIdAndPathAndResourceUrl_calledStackSyncDataExistsMetadata()
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
            ->with($this->cloud,$this->token,false,$parent_id,true,$this->resourceUrl)
            ->will($this->returnValue(json_decode($metadataOut)));

        $this->apiProviderMock->expects($this->never())
            ->method('createMetadata');

        $this->sut->createMetadata($this->cloud,$this->token,$this->user,false,$name,$parent_id,$path, null,$this->resourceUrl);
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
     * with: tokenAndUserAndIsFolderAndNameAndParentIdAndPathAndResourceUrl
     * should: returnGetMetadataPermissionDenied
     */
    public function test_createMetadata_called_tokenAndUserAndIsFolderAndNameAndParentIdAndPathAndResourceUrl_returnGetMetadataPermissionDenied()
    {
        $name = "client";
        $parent_id = "null";
        $path = '/';
        $metadata = '{"error":403}';

        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->cloud,$this->token,false,$parent_id,true,$this->resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $this->apiProviderMock->expects($this->never())
            ->method('createMetadata');

        $this->sut->createMetadata($this->cloud,$this->token,$this->user,false,$name,$parent_id,$path,null,$this->resourceUrl);
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
     * with: tokenAndUserAndIsFolderAndNameAndParentIdAndPathAndResourceUrl
     * should: returnCreateMetadataPermissionDenied
     */
    public function test_createMetadata_called_tokenAndUserAndIsFolderAndNameAndParentIdAndPathAndResourceUrl_returnCreateMetadataPermissionDenied()
    {
        $name = "client";
        $parent_id = "null";
        $path = '/';
        $metadata = '{"error":403}';
        $metadataOut = '{"id":32565632156,"parent_id":"null","filename":"root","is_folder":true,"status":"NEW","server_modified":"2014-03-11 14:22:45.757","client_modified":"2014-03-11 14:22:45.757","user":"web","version":1,"checksum":589445744,"size":166,"mimetype":"text/plain","chunks":[],"is_root":false,
                      "contents":[]}';

        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->cloud,$this->token,false,$parent_id,true,$this->resourceUrl)
            ->will($this->returnValue(json_decode($metadataOut)));

        $this->apiProviderMock->expects($this->at(1))
            ->method('createMetadata')
            ->with($this->cloud,$this->token,false,$name,$parent_id,null,$this->resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $this->sut->createMetadata($this->cloud,$this->token,$this->user,false,$name,$parent_id,$path,null,$this->resourceUrl);
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
            ->with($this->cloud, $this->token, false, $parent_id, true)
            ->will($this->returnValue(json_decode($metadataOut)));

        $this->apiProviderMock->expects($this->at(1))
            ->method('uploadMetadata')
            ->with($this->cloud, $this->token, 8888888, $pathabsolute)
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $this->sut->createMetadata($this->cloud, $this->token, $this->user, true, $name, $parent_id, $path, $pathabsolute);
    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndUserAndIsFolderAndNameAndParentIdAndPathAndResourceUrl
     * should: returnCreateMetadataPermissionDenied
     */
    public function test_createMetadata_called_tokenAndUserAndIsFileAndNameAndParentIdAndPathAndResourceUrl_returnUploadMetadataPermissionDenied()
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
            ->with($this->cloud, $this->token, false, $parent_id, true,$this->resourceUrl)
            ->will($this->returnValue(json_decode($metadataOut)));

        $this->apiProviderMock->expects($this->at(1))
            ->method('uploadMetadata')
            ->with($this->cloud, $this->token, 8888888, $pathabsolute,$this->resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $this->sut->createMetadata($this->cloud, $this->token, $this->user, true, $name, $parent_id, $path, $pathabsolute, $this->resourceUrl);
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
        $metadataVersion = json_decode('{"controlVersion":"true"}');
        $this->getDownloadMetadata($metadata, $metadataVersion,"null", $id, $this->cloud);
        $this->apiProviderMock->expects($this->at(2))
            ->method('downloadMetadata')
            ->with($this->cloud, $this->token, $id, $path)
            ->will($this->returnValue('true'));

        $params = new stdClass();
        $params->type = "insertDownloadVersion";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = $id;
        $aux->cloud = $this->cloud;
        $aux->user_eyeos = $this->user;
        $aux->version = 1;
        $aux->recover = false;
        array_push($params->lista,$aux);

        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue('true'));

        $this->sut->downloadMetadata($this->token, $id, $path, $this->user, false, $this->cloud);
    }

    /**
     * method: donwloadMetadata
     * when: called
     * with: tokenAndIdAndPathAndUser
     * should: returnDownloadCorrect
     */
    public function test_downloadMetadata_called_tokenAndIdAndPathAndUser_returnDownloadCorrect()
    {
        $path = "/home/eyeos/prueba.txt";
        $id = "8888888";
        $metadataVersion = json_decode('{"controlVersion":"false"}');

        $this->apiProviderMock->expects($this->at(0))
            ->method('getControlVersionCloud')
            ->with($this->cloud)
            ->will($this->returnValue($metadataVersion));

        $this->apiProviderMock->expects($this->at(1))
            ->method('downloadMetadata')
            ->with($this->cloud, $this->token, $id, $path)
            ->will($this->returnValue('true'));

        $result = $this->sut->downloadMetadata($this->token, $id, $path, $this->user, false, $this->cloud);
    }

    /**
     * method: donwloadMetadata
     * when: called
     * with: tokenAndIdAndPathAndUserAndResourceUrl
     * should: returnFirstDownload
     */
    public function test_downloadMetadata_called_tokenAndIdAndPathAndUserAndResourceUrl_returnFirstDownload()
    {
        $path = "/home/eyeos/prueba.txt";
        $id = "8888888";
        $metadata = '{"filename":"prueba.pdf","id":"8888888","status":"NEW","version":1,"parent_id":"32565632156","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false}';
        $metadataVersion = json_decode('{"controlVersion":"true"}');
        $this->getDownloadMetadata($metadata, $metadataVersion,"null", $id, $this->cloud, $this->resourceUrl);
        $this->apiProviderMock->expects($this->at(2))
            ->method('downloadMetadata')
            ->with($this->cloud, $this->token, $id, $path,$this->resourceUrl)
            ->will($this->returnValue('true'));

        $params = new stdClass();
        $params->type = "insertDownloadVersion";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = $id;
        $aux->cloud = $this->cloud;
        $aux->user_eyeos = $this->user;
        $aux->version = 1;
        $aux->recover = false;
        array_push($params->lista,$aux);

        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue('true'));

        $this->sut->downloadMetadata($this->token, $id, $path, $this->user, false, $this->cloud,$this->resourceUrl);
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
        $metadataVersion = json_decode('{"controlVersion":"true"}');
        $this->getDownloadMetadata($metadata, $metadataVersion,'{"id":"8888888","version":1,"recover":false}', $id, $this->cloud);
        $this->apiProviderMock->expects($this->never())
            ->method('downloadMetadata');
        $this->sut->downloadMetadata($this->token, $id, $path, $this->user, false, $this->cloud);
    }

    /**
     * method: downloadMetadata
     * when: called
     * with: tokenAndIdAndPathAndUserAndResourceUrl
     * should: returnFileLocal
     */
    public function test_downloadMetadata_called_tokenAndIdAndPathAndUserAndResourceUrl_returnFileLocal()
    {
        $path = "/home/eyeos/prueba.txt";
        $id = "8888888";
        $metadata = '{"filename":"prueba.pdf","id":"8888888","status":"NEW","version":1,"parent_id":"32565632156","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false}';
        $metadataVersion = json_decode('{"controlVersion":"true"}');
        $this->getDownloadMetadata($metadata, $metadataVersion,'{"id":"8888888","version":1,"recover":false}', $id, $this->cloud,$this->resourceUrl);
        $this->apiProviderMock->expects($this->never())
            ->method('downloadMetadata');
        $this->sut->downloadMetadata($this->token, $id, $path, $this->user, false, $this->cloud,$this->resourceUrl);
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
        $metadataVersion = json_decode('{"controlVersion":"true"}');
        $this->getDownloadMetadata($metadata, $metadataVersion,'{"id":"8888888","user_eyeos":"eyeID_EyeosUser_2","version":1,"recover":false}', $id, $this->cloud);

        $this->apiProviderMock->expects($this->at(2))
            ->method('downloadMetadata')
            ->with($this->cloud, $this->token, $id, $path)
            ->will($this->returnValue('true'));

        $params = new stdClass();
        $params->type = "updateDownloadVersion";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = $id;
        $aux->cloud = $this->cloud;
        $aux->user_eyeos = $this->user;
        $aux->version = 2;
        $aux->recover = false;
        array_push($params->lista,$aux);

        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue('true'));

        $this->sut->downloadMetadata($this->token, $id, $path, $this->user, false, $this->cloud);
    }


    /**
     * method: downloadMetadata
     * when: called
     * with: tokenAndIdAndPathAndUserAndResourceUrl
     * should: returnFileWritten
     */
    public function test_downloadMetadata_called_tokenAndIdAndPathAndUserAndResourceUrl_returnFileWritten()
    {
        $path = "/home/eyeos/prueba.txt";
        $id = "8888888";
        $metadata = '{"filename":"prueba.pdf","id":"8888888","status":"NEW","version":2,"parent_id":"32565632156","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false}';
        $metadataVersion = json_decode('{"controlVersion":"true"}');
        $this->getDownloadMetadata($metadata, $metadataVersion,'{"id":"8888888","user_eyeos":"eyeID_EyeosUser_2","version":1,"recover":false}', $id, $this->cloud, $this->resourceUrl);

        $this->apiProviderMock->expects($this->at(2))
            ->method('downloadMetadata')
            ->with($this->cloud, $this->token, $id, $path,$this->resourceUrl)
            ->will($this->returnValue('true'));

        $params = new stdClass();
        $params->type = "updateDownloadVersion";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = $id;
        $aux->cloud = $this->cloud;
        $aux->user_eyeos = $this->user;
        $aux->version = 2;
        $aux->recover = false;
        array_push($params->lista,$aux);

        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue('true'));

        $this->sut->downloadMetadata($this->token, $id, $path, $this->user, false, $this->cloud, $this->resourceUrl);
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
        $metadataVersion = json_decode('{"controlVersion":"true"}');
        $this->getDownloadMetadata($metadata, $metadataVersion,'{"id":"8888888","user_eyeos":"eyeID_EyeosUser_2","version":1,"recover":true}', $id, $this->cloud);

        $this->apiProviderMock->expects($this->never())
            ->method('downloadMetadata');

        $this->sut->downloadMetadata($this->token, $id, $path, $this->user, false, $this->cloud);
    }

    /**
     * method: downloadMetadata
     * when: called
     * with: tokenAndIdAndPathAndUserAndResourceUrl
     * should: returnFileLocalRecover
     */
    public function test_downloadMetadata_called_tokenAndIdAndPathAndUserAndResourceUrl_returnFileLocalRecover()
    {
        $path = "/home/eyeos/prueba.txt";
        $id = "8888888";
        $metadata = '{"filename":"prueba.pdf","id":"8888888","status":"NEW","version":2,"parent_id":"32565632156","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false}';
        $metadataVersion = json_decode('{"controlVersion":"true"}');
        $this->getDownloadMetadata($metadata, $metadataVersion,'{"id":"8888888","user_eyeos":"eyeID_EyeosUser_2","version":1,"recover":true}', $id, $this->cloud, $this->resourceUrl);

        $this->apiProviderMock->expects($this->never())
            ->method('downloadMetadata');

        $this->sut->downloadMetadata($this->token, $id, $path, $this->user, false, $this->cloud, $this->resourceUrl);
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
        $metadataVersion = json_decode('{"controlVersion":"true"}');
        $this->apiProviderMock->expects($this->at(0))
            ->method('getControlVersionCloud')
            ->with($this->cloud)
            ->will($this->returnValue($metadataVersion));

        $this->apiProviderMock->expects($this->at(1))
            ->method('getMetadata')
            ->with($this->cloud, $this->token, true, $id)
            ->will($this->returnValue(json_decode($metadata)));

        $params = new stdClass();
        $params->type = "getDownloadVersion";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = $id;
        $aux->user_eyeos = $this->user;
        $aux->cloud = $this->cloud;
        array_push($params->lista,$aux);

        $this->accessorProviderMock->expects($this->exactly(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue('{"id": "8888888", "cloud": "Stacksync", "user_eyeos": "eyeos", "version": 1, "recover": false}'));

        $this->apiProviderMock->expects($this->at(2))
            ->method('downloadMetadata')
            ->with($this->cloud, $this->token, $id, $path)
            ->will($this->returnValue('true'));

        $this->sut->downloadMetadata($this->token, $id, $path, $this->user, true, $this->cloud);
    }

    /**
     * method: downloadMetadata
     * when: called
     * with: tokenAndIdAndPathAndAndUserAndIsTmpAndResourceUrl
     * should: returnFileWritten
     */
    public function test_downloadMetadata_called_tokenAndIdAndPathAndUserAndIsTmpAndResourceUrl_returnFileWritten()
    {
        $path = "/home/eyeos/prueba.txt";
        $id = "8888888";
        $metadata = '{"filename":"prueba.pdf","id":"8888888","status":"NEW","version":2,"parent_id":"32565632156","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":false}';
        $metadataVersion = json_decode('{"controlVersion":"true"}');
        $this->apiProviderMock->expects($this->at(0))
            ->method('getControlVersionCloud')
            ->with($this->cloud)
            ->will($this->returnValue($metadataVersion));
        $this->apiProviderMock->expects($this->at(1))
            ->method('getMetadata')
            ->with($this->cloud, $this->token, true, $id, null, $this->resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $params = new stdClass();
        $params->type = "getDownloadVersion";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = $id;
        $aux->user_eyeos = $this->user;
        $aux->cloud = $this->cloud;
        array_push($params->lista,$aux);

        $this->accessorProviderMock->expects($this->exactly(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue('{"id": "8888888", "cloud": "Stacksync", "user_eyeos": "eyeos", "version": 1, "recover": false}'));

        $this->apiProviderMock->expects($this->at(2))
            ->method('downloadMetadata')
            ->with($this->cloud, $this->token, $id, $path,$this->resourceUrl)
            ->will($this->returnValue('true'));

        $this->sut->downloadMetadata($this->token, $id, $path, $this->user, true, $this->cloud, $this->resourceUrl);
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
        $metadataVersion = json_decode('{"controlVersion":"true"}');
        $this->apiProviderMock->expects($this->at(0))
            ->method('getControlVersionCloud')
            ->with($this->cloud)
            ->will($this->returnValue($metadataVersion));

        $this->apiProviderMock->expects($this->at(1))
            ->method('getMetadata')
            ->with($this->cloud, $this->token, true, $id)
            ->will($this->returnValue(json_decode($metadata)));

        $this->filesProviderMock->expects($this->never())
            ->method('putContents');
        $this->sut->downloadMetadata($this->token, $id, $path, $this->user, false, $this->cloud);
    }

    /**
     * method: downloadMetadata
     * when: called
     * with: tokenAndIdAndPathAndUserAndResourceUrl
     * should: returnPermissionDenied
     */
    public function test_downloadMetadata_called_tokenAndIdAndPathAndUserAndResourceUrl_returnPermissionDenied()
    {
        $path = "/home/eyeos/prueba.txt";
        $id = 8888888;
        $metadata = '{"error":403}';
        $metadataVersion = json_decode('{"controlVersion":"true"}');
        $this->apiProviderMock->expects($this->at(0))
            ->method('getControlVersionCloud')
            ->with($this->cloud)
            ->will($this->returnValue($metadataVersion));

        $this->apiProviderMock->expects($this->at(1))
            ->method('getMetadata')
            ->with($this->cloud, $this->token, true, $id,null, $this->resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $this->filesProviderMock->expects($this->never())
            ->method('putContents');
        $this->sut->downloadMetadata($this->token, $id, $path, $this->user, false, $this->cloud, $this->resourceUrl);
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
     * with: tokenAndIsFileAndIdAndUserAndResourceUrl
     * should: returnU1dbDelete
     */
    public function test_deleteMetadata_called_tokenAndIsFileAndIdAndUserAndResourceUrl_returnU1dbDelete()
    {
        $id = 8888888;
        $metadata = '{"filename":"prueba.pdf","id":"8888888","status":"DELETED","version":1,"parent_id":"32565632156","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false}';
        $this->exerciseDeleteMetadata($metadata,true,$id,"/cloudSpaces/",$this->pathCloud . "/cloudSpaces",$this->resourceUrl);
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
     * with: tokenAndIsFolderAndIdAndUserAndResourceUrl
     * should: returnU1dbDelete
     */
    public function test_deleteMetadata_called_tokenAndIsFolderAndIdAndUserAndResourceUrl_returnU1dbDelete()
    {
        $id = 1544444;
        $metadata = '{"filename":"prueba","id":"1544444","status":"DELETED","version":2,"parent_id":"32565632156","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true}';
        $this->exerciseDeleteMetadata($metadata,false,$id,"/cloudSpaces/",$this->pathCloud . "/cloudSpaces",$this->resourceUrl);
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

        $this->sut->deleteMetadata($this->cloud,$this->token,false,$id,$this->user,$this->pathCloud . "/cloudSpaces");
    }

    /**
     * method: deleteMetadata
     * when: called
     * with: tokenAndIsFolderAndIdAndUserAndResourceUrl
     * should: returnPermissionDenied
     */
    public function test_deleteMetadata_called_tokenAndIsFolderAndIdAndUserAndResourceUrl_returnPermissionDenied()
    {
        $id = 1544444;
        $metadata = '{"error":403}';
        $this->apiProviderMock->expects($this->at(0))
            ->method('deleteMetadata')
            ->with($this->cloud,$this->token,false,$id,$this->resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $this->sut->deleteMetadata($this->cloud,$this->token,false,$id,$this->user,$this->pathCloud . "/cloudSpaces", $this->resourceUrl);
    }

    /**
     * metho: renameMetadata
     * when: called
     * with: cloudAndTokenAndIsFileAndIdAndNameAndPathAndUserAndParentId
     * should: returnU1dbRename
     */
    public function test_renameMetadata_called_cloudAndTokenAndIsFileAndIdAndNameAndPathAndUserAndParentId_returnU1dbRename()
    {
        $id = 8339393;
        $name = "b.txt";
        $parent = 99999;
        $path = '/A/';
        $this->exerciseRenameMetadata(true, $id, $parent, $path, $name, $this->cloud);
    }

    /**
     * metho: renameMetadata
     * when: called
     * with: cloudAndTokenAndIsFileAndIdAndNameAndPathAndUserAndParentIdAndResourceUrl
     * should: returnU1dbRename
     */
    public function test_renameMetadata_called_cloudAndTokenAndIsFileAndIdAndNameAndPathAndUserAndParentIdAndResourceUrl_returnU1dbRename()
    {
        $id = 8339393;
        $name = "b.txt";
        $parent = 99999;
        $path = '/A/';
        $this->exerciseRenameMetadata(true, $id, $parent, $path, $name, $this->cloud,$this->resourceUrl);
    }

    /**
     * method: renameMetadata
     * when: called
     * with: cloudAndTokenAndIsFolderAndIdAndNameAndPathAndUserAndParentId
     * should: returnU1dbRename
     */
    public function test_renameMetadata_called_cloudAndTokenAndIsFolderAndIdAndNameAndPathAndUserAndParentId_returnU1dbRename()
    {
        $id = 8983444;
        $name = "F";
        $parent = 1333555;
        $path = '/D/';
        $this->exerciseRenameMetadata(false, $id, $parent, $path, $name, $this->cloud);
    }

    /**
     * method: renameMetadata
     * when: called
     * with: cloudAndTokenAndIsFolderAndIdAndNameAndPathAndUserAndParentIdAndResourceUrl
     * should: returnU1dbRename
     */
    public function test_renameMetadata_called_cloudAndTokenAndIsFolderAndIdAndNameAndPathAndUserAndParentIdAndResourceUrl_returnU1dbRename()
    {
        $id = 8983444;
        $name = "F";
        $parent = 1333555;
        $path = '/D/';
        $this->exerciseRenameMetadata(false, $id, $parent, $path, $name, $this->cloud,$this->resourceUrl);
    }

    /**
     * method: renameMetadata
     * when: called
     * with: cloudAndTokenAndIsFolderAndIdAndNameAndPathAndUserAndParentId
     * should: returnPermissionDenied
     */
    public function test_renameMetadata_called_cloudAndTokenAndIsFolderAndIdAndNameAndPathAndUserAndParentId_returnPermissionDenied()
    {
        $id = 8983444;
        $name = "F";
        $parent = 1333555;
        $path = '/D/';
        $metadata = '{"error":403}';
        $this->apiProviderMock->expects($this->at(0))
            ->method('updateMetadata')
            ->with($this->cloud, $this->token, false, $id, $name, $parent)
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $this->sut->renameMetadata($this->cloud, $this->token, false, $id, $name, $path, $this->user, $parent);
    }

    /**
     * method: renameMetadata
     * when: called
     * with: cloudAndTokenAndIsFolderAndIdAndNameAndPathAndUserAndParentIdAndResourceUrl
     * should: returnPermissionDenied
     */
    public function test_renameMetadata_called_cloudAndTokenAndIsFolderAndIdAndNameAndPathAndUserAndParentIdAndResourceUrl_returnPermissionDenied()
    {
        $id = 8983444;
        $name = "F";
        $parent = 1333555;
        $path = '/D/';
        $metadata = '{"error":403}';
        $this->apiProviderMock->expects($this->at(0))
            ->method('updateMetadata')
            ->with($this->cloud, $this->token, false, $id, $name, $parent, $this->resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $this->sut->renameMetadata($this->cloud, $this->token, false, $id, $name, $path, $this->user, $parent, $this->resourceUrl);
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
        $metadataDelete = '{"id":' . $id . ',"user_eyeos":"' . $this->user . '","cloud":"' . $this->cloud .'","path":"' . $pathOrig. '"}';
        $metadataInsert = json_decode('{"cloud":"' . $this->cloud .'","user_eyeos":"' . $this->user . '","filename":"' . $filename . '","id":"' . $id . '","size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"'. $parent . '","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false,"path":"' . $pathNew . '"}');
        $this->exerciseMoveMetadata($id,$filename,$parent,true,$pathOrig,$pathNew,$metadataMove,$metadataDelete, $metadataInsert);
    }

    /**
     * method: moveMetadata
     * when: called
     * with: tokenAndIsFileAndIdAndPathOrigAndPathDestAndUserAndParentIdAndResourceUrl
     * should: returnU1dbMove
     */
    public function test_moveMetadata_called_tokenAndIsFileAndIdAndPathOrigAndPathDestAndUserAndParentIdAndResourceUrl_returnU1dbMove()
    {
        $id = 8983444;
        $parent = 1333555;
        $pathOrig = '/';
        $pathNew = "/documents/";
        $filename = "prueba.pdf";
        $metadataMove = '{"filename":"' . $filename . '","id":"' . $id . '","size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"' . $parent . '","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false}';
        $metadataDelete = '{"id":' . $id . ',"user_eyeos":"' . $this->user . '","cloud":"' . $this->cloud .'","path":"' . $pathOrig. '"}';
        $metadataInsert = json_decode('{"cloud":"' . $this->cloud .'","user_eyeos":"' . $this->user . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1\/","access_token_key":"1234","access_token_secret":"ABCD","filename":"' . $filename . '","id":"' . $id . '","size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"'. $parent . '","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false,"path":"' . $pathNew . '"}');
        $this->exerciseMoveMetadata($id,$filename,$parent,true,$pathOrig,$pathNew,$metadataMove,$metadataDelete, $metadataInsert,null,$this->resourceUrl);
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
        $metadataDelete = '{"id":' . $id . ',"user_eyeos":"' . $this->user . '","cloud":"' . $this->cloud .'","path":"' . $pathOrig. '"}';
        $metadataInsert = json_decode('{"cloud":"' . $this->cloud .'","user_eyeos":"' . $this->user . '","filename":"' . $fileDest . '","id":"' . $id . '","size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"'. $parent . '","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false,"path":"' . $pathNew . '"}');
        $this->exerciseMoveMetadata($id,$filename,$parent,true,$pathOrig,$pathNew,$metadataMove,$metadataDelete, $metadataInsert,$fileDest);
    }

    /**
     * method: moveMetadata
     * when: called
     * with: tokenAndIsFileAndIdAndPathOrigAndPathDestAndUserAndParentIdAndNameAndResourceUrl
     * should: returnU1dbMove
     */
    public function test_moveMetadata_called_tokenAndIsFileAndIdAndPathOrigAndPathDestAndUserAndParentIdAndNameAndResourceUrl_returnU1dbMove()
    {
        $id = 8983444;
        $parent = 1333555;
        $pathOrig = '/';
        $pathNew = "/documents/";
        $filename = "prueba.pdf";
        $fileDest = "prueba 1.pdf";
        $metadataMove = '{"filename":"' . $fileDest . '","id":"' . $id . '","size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"' . $parent . '","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false}';
        $metadataDelete = '{"id":' . $id . ',"user_eyeos":"' . $this->user . '","cloud":"' . $this->cloud .'","path":"' . $pathOrig. '"}';
        $metadataInsert = json_decode('{"cloud":"' . $this->cloud .'","user_eyeos":"' . $this->user . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1\/","access_token_key":"1234","access_token_secret":"ABCD","filename":"' . $fileDest . '","id":"' . $id . '","size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":"'. $parent . '","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false,"path":"' . $pathNew . '"}');
        $this->exerciseMoveMetadata($id,$filename,$parent,true,$pathOrig,$pathNew,$metadataMove,$metadataDelete, $metadataInsert,$fileDest,$this->resourceUrl);
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
        $metadataDelete = '{"id":' . $id . ',"user_eyeos":"' . $this->user . '","cloud":"' . $this->cloud .'","path":"' . $pathOrig. '"}';
        $metadataInsert = json_decode('{"cloud":"' . $this->cloud .'","user_eyeos":"' . $this->user . '","filename":"' . $filename . '","id":"' . $id . '","size":0,"status":"CHANGED","version":2,"parent_id":"'. $parent . '","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":true,"is_root":false,"path":"' . $pathNew . '"}');
        $this->exerciseMoveMetadata($id,$filename,$parent,false,$pathOrig,$pathNew,$metadataMove,$metadataDelete,$metadataInsert);

    }

    /**
     * method: moveMetadata
     * when: called
     * with: tokenAndIsFolderAndIdAndPathOrigAndPathDestAndUserAndParentIdAndResourceUrl
     * should: returnU1dbMove
     */
    public function test_moveMetadata_called_tokenAndIsFolderAndIdAndPathOrigAndPathDestAndUserAndParentIDAndResourceUrl_returnU1dbMove()
    {
        $id = 8983444;
        $parent = 1333555;
        $pathOrig = '/';
        $pathNew = "/documents/";
        $filename = "prueba";
        $metadataMove = '{"filename":"' . $filename . '","id":"' . $id . '","size":0,"status":"CHANGED","version":2,"parent_id":"' . $parent . '","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":true,"is_root":false}';
        $metadataDelete = '{"id":' . $id . ',"user_eyeos":"' . $this->user . '","cloud":"' . $this->cloud .'","path":"' . $pathOrig. '"}';
        $metadataInsert = json_decode('{"cloud":"' . $this->cloud .'","user_eyeos":"' . $this->user . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1\/","access_token_key":"1234","access_token_secret":"ABCD","filename":"' . $filename . '","id":"' . $id . '","size":0,"status":"CHANGED","version":2,"parent_id":"'. $parent . '","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":true,"is_root":false,"path":"' . $pathNew . '"}');
        $this->exerciseMoveMetadata($id,$filename,$parent,false,$pathOrig,$pathNew,$metadataMove,$metadataDelete,$metadataInsert,null,$this->resourceUrl);
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
            ->with($this->cloud,$this->token,false,$id,null,$parent)
            ->will($this->returnValue(json_decode('{"error":403}')));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $this->filesProviderMock->expects($this->never())
            ->method('deleteFile');

        $this->sut->moveMetadata($this->cloud,$this->token,false,$id,$this->path,$this->path . "/documents",$this->user,$parent,null);
    }

    /**
     * method: moveMetadata
     * when: called
     * with: tokenAndIsFolderAndIdAndPathOrigAndPathDestAndUserAndParentIdAndResourceUrl
     * should: returnPermissionDenied
     */
    public function test_moveMetadata_called_tokenAndIsFolderAndIdAndPathOrigAndPathDestAndUserAndParentIDAndResourceUrl_returnPermissionDenied()
    {
        $id = 8983444;
        $parent = 1333555;

        $this->apiProviderMock->expects($this->at(0))
            ->method('updateMetadata')
            ->with($this->cloud,$this->token,false,$id,null,$parent,$this->resourceUrl)
            ->will($this->returnValue(json_decode('{"error":403}')));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $this->filesProviderMock->expects($this->never())
            ->method('deleteFile');

        $this->sut->moveMetadata($this->cloud,$this->token,false,$id,$this->path,$this->path . "/documents",$this->user,$parent,null,null,$this->resourceUrl);
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
        $aux->cloud = $this->cloud;
        array_push($params->lista,$aux);

        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue('true'));

        $result = $this->sut->recursiveDeleteVersion($this->cloud,$id,$user);
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
            ->with($this->cloud,$this->token,$id)
            ->will($this->returnValue(json_decode($metadata)));

        $params = new stdClass();
        $params->type = "getDownloadVersion";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = "" . $id;
        $aux->user_eyeos = $this->user;
        $aux->cloud = $this->cloud;
        array_push($params->lista,$aux);

        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue('null'));

        $result = $this->sut->listVersions($this->cloud,$this->token,8983444,$this->user);
        $this->assertEquals($expected,$result);
    }

    /**
     * method: listVersions
     * when: called
     * with: tokenAndIdAndUserAndResourceUrl
     * should: returnListCorrectAndCurrentVersion
     */
    public function test_listVersions_called_tokenAndIdAndUserAndResourceUrl_returnListCorrectAndCurrentVersion()
    {
        $id = 8983444;
        $metadata = '[{"name":"Winter2015.jpg","path":"\/documents\/clients\/Winter2015.jpg","id":32565632156,"size":775412,"mimetype":"image\/jpg","status":"RENAMED","version":3,"parent":12386548974,"user":"Adrian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"},{"name":"Winter2012.jpg","path":"\/documents\/clients\/Winter2012.jpg","id":32565632156,"size":7482,"mimetype":"image\/jpg","status":"CHANGED","version":2,"parent":12386548974,"user":"Cristian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"},{"name":"Winter2012.jpg","path":"\/documents\/clients\/Winter2012.jpg","id":32565632156,"size":775412,"mimetype":"image\/jpg","status":"NEW","version":1,"parent":12386548974,"user":"Adrian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"}]';
        $expected = '[{"name":"Winter2015.jpg","path":"\/documents\/clients\/Winter2015.jpg","id":32565632156,"size":775412,"mimetype":"image\/jpg","status":"RENAMED","version":3,"parent":12386548974,"user":"Adrian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","enabled":true},{"name":"Winter2012.jpg","path":"\/documents\/clients\/Winter2012.jpg","id":32565632156,"size":7482,"mimetype":"image\/jpg","status":"CHANGED","version":2,"parent":12386548974,"user":"Cristian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"},{"name":"Winter2012.jpg","path":"\/documents\/clients\/Winter2012.jpg","id":32565632156,"size":775412,"mimetype":"image\/jpg","status":"NEW","version":1,"parent":12386548974,"user":"Adrian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"}]';

        $this->apiProviderMock->expects($this->at(0))
            ->method('listVersions')
            ->with($this->cloud,$this->token,$id,$this->resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $params = new stdClass();
        $params->type = "getDownloadVersion";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = "" . $id;
        $aux->user_eyeos = $this->user;
        $aux->cloud = $this->cloud;
        array_push($params->lista,$aux);

        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue('null'));

        $result = $this->sut->listVersions($this->cloud,$this->token,8983444,$this->user,$this->resourceUrl);
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
            ->with($this->cloud,$this->token,$id)
            ->will($this->returnValue(json_decode($metadata)));

        $params = new stdClass();
        $params->type = "getDownloadVersion";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = "" . $id;
        $aux->user_eyeos = $this->user;
        $aux->cloud = $this->cloud;
        array_push($params->lista,$aux);

        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue('{"id":"8983444","version":2,"recover":false}'));

        $result = $this->sut->listVersions($this->cloud,$this->token,8983444,$this->user);
        $this->assertEquals($expected,$result);
    }

    /**
     * method: listVersion
     * when: called
     * with: tokenAndIdAndUserAndResourceUrl
     * should: returnListCorrectAndOlderVersion
     */
    public function test_listVersion_called_tokenAndIdAndUserAndResourceUrl_returnListCorrectAndOlderVersion()
    {
        $id = 8983444;
        $metadata = '[{"name":"Winter2015.jpg","path":"\/documents\/clients\/Winter2015.jpg","id":32565632156,"size":775412,"mimetype":"image\/jpg","status":"RENAMED","version":3,"parent":12386548974,"user":"Adrian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"},{"name":"Winter2012.jpg","path":"\/documents\/clients\/Winter2012.jpg","id":32565632156,"size":7482,"mimetype":"image\/jpg","status":"CHANGED","version":2,"parent":12386548974,"user":"Cristian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"},{"name":"Winter2012.jpg","path":"\/documents\/clients\/Winter2012.jpg","id":32565632156,"size":775412,"mimetype":"image\/jpg","status":"NEW","version":1,"parent":12386548974,"user":"Adrian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"}]';
        $expected = '[{"name":"Winter2015.jpg","path":"\/documents\/clients\/Winter2015.jpg","id":32565632156,"size":775412,"mimetype":"image\/jpg","status":"RENAMED","version":3,"parent":12386548974,"user":"Adrian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"},{"name":"Winter2012.jpg","path":"\/documents\/clients\/Winter2012.jpg","id":32565632156,"size":7482,"mimetype":"image\/jpg","status":"CHANGED","version":2,"parent":12386548974,"user":"Cristian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","enabled":true},{"name":"Winter2012.jpg","path":"\/documents\/clients\/Winter2012.jpg","id":32565632156,"size":775412,"mimetype":"image\/jpg","status":"NEW","version":1,"parent":12386548974,"user":"Adrian","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"}]';

        $this->apiProviderMock->expects($this->at(0))
            ->method('listVersions')
            ->with($this->cloud,$this->token,$id,$this->resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $params = new stdClass();
        $params->type = "getDownloadVersion";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = "" . $id;
        $aux->user_eyeos = $this->user;
        $aux->cloud = $this->cloud;
        array_push($params->lista,$aux);

        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue('{"id":"8983444","version":2,"recover":false}'));

        $result = $this->sut->listVersions($this->cloud,$this->token,8983444,$this->user,$this->resourceUrl);
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
            ->with($this->cloud,$this->token,$id)
            ->will($this->returnValue(json_decode('{"error":403}')));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $result = $this->sut->listVersions($this->cloud,$this->token,$id,$this->user);
        $this->assertEquals(array("status"=>"KO","error"=>403),$result);
    }

    /**
     * method: listVersions
     * when: called
     * with: tokenAndIdAndUserAndResourceUrl
     * should: returnPermissionDenied
     */
    public function test_listVersions_called_tokenAndIdAndUserAndResourceUrl_returnPermissionDenied()
    {
        $id = 8983444;
        $this->apiProviderMock->expects($this->at(0))
            ->method('listVersions')
            ->with($this->cloud,$this->token,$id,$this->resourceUrl)
            ->will($this->returnValue(json_decode('{"error":403}')));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $result = $this->sut->listVersions($this->cloud,$this->token,$id,$this->user,$this->resourceUrl);
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
            ->with($this->cloud,$this->token,$id)
            ->will($this->returnValue(json_decode('{"error":-1}')));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $result = $this->sut->listVersions($this->cloud,$this->token,$id,$this->user);
        $this->assertEquals(array("status"=>"KO","error"=>-1),$result);

    }

    /**
     * method: listVersions
     * when: called
     * with: tokenAndIdAndUserAndResourceUrl
     * should: returnException
     */
    public function test_listVersions_called_tokenAndIdAndUserAndResourceUrl_returnException()
    {
        $id = 8983444;
        $this->apiProviderMock->expects($this->at(0))
            ->method('listVersions')
            ->with($this->cloud,$this->token,$id,$this->resourceUrl)
            ->will($this->returnValue(json_decode('{"error":-1}')));

        $this->accessorProviderMock->expects($this->never())
            ->method('getProcessDataU1db');

        $result = $this->sut->listVersions($this->cloud,$this->token,$id,$this->user,$this->resourceUrl);
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
        $this->exerciseGetFileVersionData("null", '{"status": true}', array("status" => "OK"), $id, $version, $path, "insertDownloadVersion");

    }

    /**
     * method: getFileVersionData
     * when: called
     * with: tokenAndIdAndVersionAndPathAndUserAndResourceUrl
     * should: insertDownloadVersion
     */
    public function test_getFileVersionData_called_tokenAndIdAndVersionAndPathAndUserAndResourceUrl_insertDownloadVersion()
    {
        $id = 8983444;
        $version = 2;
        $path = "/home/eyeos/client1.pdf";
        $this->exerciseGetFileVersionData("null", '{"status": true}', array("status" => "OK"), $id, $version, $path, "insertDownloadVersion",false,$this->resourceUrl);
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
        $this->exerciseGetFileVersionData('{"id": "8983444", "version": 1, "recover": false}', '{"status": true}', array("status" => "OK"), $id, $version, $path, "updateDownloadVersion");
    }

    /**
     * method: getFileVersionData
     * when: called
     * with: tokenAndIdAndVersionAndPathAndUserAndResourceUrl
     * should: updateDownloadVersion
     */
    public function test_getFileVersionData_called_tokenAndIdAndVersionAndPathAndUserAndResourceUrl_updateDownloadVersion()
    {
        $id = 8983444;
        $version = 2;
        $path = "/home/eyeos/client1.pdf";
        $this->exerciseGetFileVersionData('{"id": "8983444", "version": 1, "recover": false}', '{"status": true}', array("status" => "OK"), $id, $version, $path, "updateDownloadVersion",false,$this->resourceUrl);
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
        $this->exerciseGetFileVersionData('{"id": "8983444", "version": 1,"recover": false}', '{"error":403}', array("status" => "KO", "error" => 403), $id, $version, $path, '', true);
    }


    /**
     * method: getFileVersionData
     * when: called
     * with: tokenAndIdAndVersionAndPathAndUserAndResourceUrl
     * should: returnPermissionDenied
     */
    public function test_getFileVersionData_called_tokenAndIdAndVersionAndPathAndUserAndResourceUrl_returnPermissionDenied()
    {
        $id = 8983444;
        $version = 2;
        $path = "/home/eyeos/client1.pdf";
        $this->exerciseGetFileVersionData('{"id": "8983444", "version": 1,"recover": false}', '{"error":403}', array("status" => "KO", "error" => 403), $id, $version, $path, '', true,$this->resourceUrl);
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
        $this->exerciseGetFileVersionData('{"id": "8983444", "version": 1, "recover": false}', '{"error":-1}', array("status" => "KO", "error" => -1), $id, $version, $path, '', true);
    }

    /**
     * method: getFileVersionData
     * when: called
     * with: tokenAndIdAndVersionAndPathAndUserAndResourceUrl
     * should: returnException
     */
    public function test_getFileVersionData_called_tokenAndIdAndVersionAndPathAndUserAndResourceUrl_returnException()
    {
        $id = 8983444;
        $version = 2;
        $path = "/home/eyeos/client1.pdf";
        $this->exerciseGetFileVersionData('{"id": "8983444", "version": 1, "recover": false}', '{"error":-1}', array("status" => "KO", "error" => -1), $id, $version, $path, '', true, $this->resourceUrl);
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
     * with: tokenAndIdAndResourceUrl
     * should: returnList
     */
    public function test_getListUsersShareAndResourceUrl_called_tokenAndId_returnList()
    {
        $id = 123;
        $metadata = '[{"joined_at":"2014-05-27","is_owner":true,"name":"tester1","email":"tester1@test.com"}]';
        $this->exerciseGetListUsersShare($metadata,$id,$metadata,$this->resourceUrl);
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
     * with: tokenAndIdAndResourceUrl
     * should: returnPermissionDenied
     */
    public function test_getListUsersShare_called_tokenAndIdAndResourceUrl_returnPermissionDenied()
    {
        $id = 123;
        $metadata = '{"error":403}';
        $metadataOut = array("status" => "KO","error" => 403);
        $this->exerciseGetListUsersShare($metadata,$id,$metadataOut,$this->resourceUrl);
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
     * method: getListUsersShare
     * when: called
     * with: tokenAndIdAndResourceUrl
     * should: returnException
     */
    public function test_getListUsersShareAndResourceUrl_called_tokenAndId_returnException()
    {
        $id = 123;
        $metadata = '{"error":-1}';
        $metadataOut = array("status" => "KO","error" => -1);
        $this->exerciseGetListUsersShare($metadata,$id,$metadataOut,$this->resourceUrl);
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
        $this->exerciseShareFolder($metadata, $id, $list, $metadataOut,false);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndListAndShared
     * should: returnCorrect
     */
    public function test_shareFolder_called_tokenAndIdAndListAndShared_returnCorrect()
    {
        $id = 123;
        $list = array("a@a.com","b@b.com");
        $metadata = '{"status":true}';
        $metadataOut = array("status" => "OK");
        $this->exerciseShareFolder($metadata, $id, $list, $metadataOut,true);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndListAndResourceUrl
     * should: returnCorrect
     */
    public function test_shareFolder_called_tokenAndIdAndListAndResourceUrl_returnCorrect()
    {
        $id = 123;
        $list = array("a@a.com","b@b.com");
        $metadata = '{"status":true}';
        $metadataOut = array("status" => "OK");
        $this->exerciseShareFolder($metadata, $id, $list, $metadataOut, false,$this->resourceUrl);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndListAndSharedAndResourceUrl
     * should: returnCorrect
     */
    public function test_shareFolder_called_tokenAndIdAndListAndSharedAndResourceUrl_returnCorrect()
    {
        $id = 123;
        $list = array("a@a.com","b@b.com");
        $metadata = '{"status":true}';
        $metadataOut = array("status" => "OK");
        $this->exerciseShareFolder($metadata, $id, $list, $metadataOut, true,$this->resourceUrl);
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
        $list = array("a@a.com", "b@b.com");
        $metadata = '{"error":403}';
        $metadataOut = array("status" => "KO", "error" => 403);
        $this->exerciseShareFolder($metadata, $id, $list, $metadataOut, false);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndListAndShared
     * should: returnPermissionDenied
     */
    public function test_shareFolder_called_tokenAndIdAndListAndShared_returnPermissionDenied()
    {
        $id = 123;
        $list = array("a@a.com", "b@b.com");
        $metadata = '{"error":403}';
        $metadataOut = array("status" => "KO", "error" => 403);
        $this->exerciseShareFolder($metadata, $id, $list, $metadataOut, true);
    }


    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndListAndResourceUrl
     * should: returnPermissionDenied
     */
    public function test_shareFolder_called_tokenAndIdAndListAndResourceUrl_returnPermissionDenied()
    {
        $id = 123;
        $list = array("a@a.com", "b@b.com");
        $metadata = '{"error":403}';
        $metadataOut = array("status" => "KO", "error" => 403);
        $this->exerciseShareFolder($metadata, $id, $list, $metadataOut, false,$this->resourceUrl);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndListAndSharedAndResourceUrl
     * should: returnPermissionDenied
     */
    public function test_shareFolder_called_tokenAndIdAndListAndSharedAndResourceUrl_returnPermissionDenied()
    {
        $id = 123;
        $list = array("a@a.com", "b@b.com");
        $metadata = '{"error":403}';
        $metadataOut = array("status" => "KO", "error" => 403);
        $this->exerciseShareFolder($metadata, $id, $list, $metadataOut, true,$this->resourceUrl);
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
        $list = array("a@a.com", "b@b.com");
        $metadata = '{"error":-1}';
        $metadataOut = array("status" => "KO", "error" => -1);
        $this->exerciseShareFolder($metadata, $id, $list, $metadataOut,false);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndListAndShared
     * should: returnException
     */
    public function test_shareFolder_called_tokenAndIdAndListAndShared_returnException()
    {
        $id = 123;
        $list = array("a@a.com", "b@b.com");
        $metadata = '{"error":-1}';
        $metadataOut = array("status" => "KO", "error" => -1);
        $this->exerciseShareFolder($metadata, $id, $list, $metadataOut,true);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndListAndResourceUrl
     * should: returnException
     */
    public function test_shareFolder_called_tokenAndIdAndListAndResourceUrl_returnException()
    {
        $id = 123;
        $list = array("a@a.com", "b@b.com");
        $metadata = '{"error":-1}';
        $metadataOut = array("status" => "KO", "error" => -1);
        $this->exerciseShareFolder($metadata, $id, $list, $metadataOut,false, $this->resourceUrl);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndListAndSharedAndResourceUrl
     * should: returnException
     */
    public function test_shareFolder_called_tokenAndIdAndListAndSharedAndResourceUrl_returnException()
    {
        $id = 123;
        $list = array("a@a.com", "b@b.com");
        $metadata = '{"error":-1}';
        $metadataOut = array("status" => "KO", "error" => -1);
        $this->exerciseShareFolder($metadata, $id, $list, $metadataOut,true, $this->resourceUrl);
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

    /**
     * method: getControlVersionCloud
     * when: called
     * with: ValidCloud
     * should: returnList
     */
    public function test_getControlVersionCloud_called_validCloud_returnList()
    {
        $cloud = "Stacksync";
        $metadata = json_decode('{"controlVersion":"true"}');
        $this->apiProviderMock->expects($this->once())
            ->method('getControlVersionCloud')
            ->with($cloud)
            ->will($this->returnValue($metadata));
        $result = $this->sut->getControlVersionCloud($cloud);
        $this->assertEquals($metadata, $result);
    }

    /**
     * method: getControlVersionCloud
     * when: called
     * with: InvalidCloud
     * should: returnException
     */
    public function test_getControlVersionCloud_called_Invalid_Cloud_returnException()
    {
        $cloud = "No_valid_cloud";
        $metadata =json_decode('{"error":-1}');
        $metadataOut = array("status" => "KO", "error" => -1);
        $this->apiProviderMock->expects($this->once())
            ->method('getControlVersionCloud')
            ->with($cloud)
            ->will($this->returnValue($metadata));
        $result = $this->sut->getControlVersionCloud($cloud);
        $this->assertEquals($metadataOut, $result);
    }

    /**
     * method: unLockedFile
     * when: called
     * with: idAndCloudAndUserAndIpServerAndTimeLimitAndDateNow
     * should: EmptyMetadataNotBlock
     */
    public function test__unLockedFile_called_idAndCloudAndIPServerAndTimeLimitAndDateNow_EmptyMetadataNotBlock()
    {
        $u1dbOut = '[]';
        $check = array("status" => "OK");
        $dt_now = new DateTime('NOW');
        $this->exerciseIsBlockedFile($u1dbOut,$check,$dt_now);
    }

    /**
     * method: unLockedFile
     * when: called
     * with: idAndCloudAndUserAndIpServerAndTimeLimitAndDateNow
     * should: MetadataCloseNotBlock
     */
    public function test__unLockedFile_called_idAndCloudAndIPServerAndTimeLimitAndDateNow_MetadataCloseNotBlock()
    {
        $u1dbOut = '[{"id":"124568","cloud":"Stacksync","username":"tester","IpServer":"192.168.56.101","datetime":"2015-05-12 10:50:00","status":"close"}]';
        $check = array("status" => "OK");
        $dt_now = new DateTime('NOW');
        $this->exerciseIsBlockedFile($u1dbOut,$check,$dt_now);
    }

    /**
     * method: unLockedFile
     * when: called
     * with: idAndCloudAndUserAndIpServerAndTimeLimitAndDateNow
     * should: MetadataTimeExpiredNotBlock
     */
    public function test__unLockedFile_called_idAndCloudAndIPServerAndTimeLimitAndDateNow_MetadataTimeExpiredNotBlock()
    {
        $u1dbOut = '[{"id":"124568","cloud":"Stacksync","username":"tester","IpServer":"192.168.56.101","datetime":"2015-05-12 10:50:00","status":"open"}]';
        $check = array("status" => "OK");
        $dt_now = DateTime::createFromFormat('Y-m-d H:i:s',"2015-05-12 11:05:00");
        $this->exerciseIsBlockedFile($u1dbOut,$check,$dt_now);
    }

    /**
     * method: unLockedFile
     * when: called
     * with: idAndCloudAndUserAndIpServerAndTimeLimitAndDateNow
     * should: MetadataSameUserNotBlock
     */
    public function test__unLockedFile_called_idAndCloudAndIPServerAndTimeLimitAndDateNow_MetadataSameUserNotBlock()
    {
        $u1dbOut = '[{"id":"124568","cloud":"Stacksync","username":"eyeos","IpServer":"192.168.56.101","datetime":"2015-05-12 10:50:00","status":"open"}]';
        $check = array("status" => "OK");
        $dt_now = DateTime::createFromFormat('Y-m-d H:i:s',"2015-05-12 10:50:00");
        $this->exerciseIsBlockedFile($u1dbOut,$check,$dt_now);
    }

    /**
     * method: unLockedFile
     * when: called
     * with: idAndCloudAndUserAndIpServerAndTimeLimitAndDateNow
     * should: MetadataOpenBlock
     */
    public function test__unLockedFile_called_idAndCloudAndIPServerAndTimeLimitAndDateNow_MetadataTimeNotExpiredBlock()
    {
        $u1dbOut = '[{"id":"124568","cloud":"Stacksync","username":"tester","IpServer":"192.168.56.101","datetime":"2015-05-12 10:50:00","status":"open"}]';
        $check = array("status" => "KO","error" => "BLOCK");
        $dt_now = DateTime::createFromFormat('Y-m-d H:i:s',"2015-05-12 10:50:00");
        $this->exerciseIsBlockedFile($u1dbOut,$check,$dt_now);
    }

    /**
     * method: lockFile
     * when: called
     * with: idAndCloudAndUserAndIpServerAndTimeLimitAndDateNow
     * should: returnCorrect
     */
    public function test__lockFile_called_idAndCloudAndIPServerAndTimeLimitAndDateNow_returnCorrect()
    {
        $u1dbOut = 'true';
        $check = array("status" => "OK");
        $this->exerciseLockFile($u1dbOut,$check,"lockFile","open");
    }

    /**
     * method: lockFile
     * when: called
     * with: idAndCloudAndUserAndIpServerAndTimeLimitAndDateNow
     * should: returnCorrect
     */
    public function test__lockFile_called_idAndCloudAndIPServerAndTimeLimitAndDateNow_returnBlock()
    {
        $u1dbOut = 'false';
        $check = array("status" => "KO","error" => "BLOCK");
        $this->exerciseLockFile($u1dbOut,$check,"lockFile","open");
    }

    /**
     * method: updateDateTime
     * when: called
     * with: idAndCloudAndUserAndIpServerAndDateNow
     * should: returnCorrect
     */
    public function test__updateDateTime_called_idAndCloudAndIPServerAndDateNow_returnCorrect()
    {
        $u1dbOut = 'true';
        $check = array("status" => "OK");
        $this->exerciseLockFile($u1dbOut,$check,"updateDateTime","open");
    }

    /**
     * method: updateDateTime
     * when: called
     * with: idAndCloudAndUserAndIpServerAndDateNow
     * should: returnCorrect
     */
    public function test__updateDateTime_called_idAndCloudAndIPServerAndDateNow_returnBlock()
    {
        $u1dbOut = 'false';
        $check = array("status" => "KO","error" => "BLOCK");
        $this->exerciseLockFile($u1dbOut,$check,"updateDateTime","open");
    }

    /**
     * method: unLockFile
     * when: called
     * with: idAndCloudAndUserAndIpServerAndDateNow
     * should: returnCorrect
     */
    public function test__unLockFile_called_idAndCloudAndIPServerAndDateNow_returnCorrect()
    {
        $u1dbOut = 'true';
        $check = array("status" => "OK");
        $this->exerciseLockFile($u1dbOut,$check,"unLockFile","close");
    }

    /**
     * method: unLockFile
     * when: called
     * with: idAndCloudAndUserAndIpServerAndDateNow
     * should: returnBlock
     */
    public function test__unLockFile_called_idAndCloudAndIPServerAndDateNow_returnBlock()
    {
        $u1dbOut = 'false';
        $check = array("status" => "KO","error" => 'BLOCK');
        $this->exerciseLockFile($u1dbOut,$check,"unLockFile","close");
    }

    /**
     * method: getMetadataFolder
     * when: called
     * with: tokenAndIdAndPathAndUser
     * should: returnMetadata
     */

    public function test_getMetadataFolder_called_tokenAndIdAndPathAndUser_returnMetadata()
    {
        $metadata = '{"filename":"root","id":"null","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,
                      "contents":[
                            {"filename":"Client1.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false},
                            {"filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true},
                            {"filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false},
                            {"name":"cloudFolder","access_token_key":"tXn77oo5xmgq4C9koISxf0dSr46Naw","resource_url":"http://ast3-deim.urv.cat/v1/folder/1972","access_token_secret":"Ug40pvqYjNtXD6xBGZZ5rgHR3nLINr"}
                            ]}';

        $this->exerciseGetMetadataFolder($metadata,json_decode($metadata));
    }

    /**
     * method: getMetadataFolder
     * when: called
     * with: tokenAndIdAndPathAndUserAndResourceUrl
     * should: returnMetadata
     */
    public function test_getMetadataFolder_called_tokenAndIdAndPathAndUserAndResourceUrl_returnMetadata()
    {
        $metadata = '{"filename":"root","id":"null","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":true,"is_folder":true,
                      "contents":[
                            {"filename":"Client1.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false},
                            {"filename":"client2","id":44444755856,"size":775412,"mimetype":"application/pdf","status":"NEW","version":1,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"is_folder":true},
                            {"filename":"Client3.pdf","id":11165632156,"size":775412,"mimetype":"application/pdf","status":"CHANGED","version":2,"parent_id":null,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":false},
                            {"name":"cloudFolder","access_token_key":"tXn77oo5xmgq4C9koISxf0dSr46Naw","resource_url":"http://ast3-deim.urv.cat/v1/folder/1972","access_token_secret":"Ug40pvqYjNtXD6xBGZZ5rgHR3nLINr"}
                            ]}';

        $this->exerciseGetMetadataFolder($metadata,json_decode($metadata),$this->resourceUrl);
    }


    /**
     * method: getMetadataFolder
     * when: called
     * with: tokenAndIdAndPathAndUser
     * should: returnException
     */
    public function test_getMetadataFolder_called_tokenAndIdAndPathAndUser_returnException()
    {
        $metadata = '{"error":404}';
        $this->exerciseGetMetadataFolder($metadata,json_decode($metadata));
    }

    /**
     * method: getMetadataFolder
     * when: called
     * with: tokenAndIdAndPathAndUserAndResourceUrl
     * should: returnException
     */
    public function test_getMetadataFolder_called_tokenAndIdAndPathAndUserAndResourceUrl_returnException()
    {
        $metadata = '{"error":404}';
        $this->exerciseGetMetadataFolder($metadata,json_decode($metadata),$this->resourceUrl);
    }


    /**
     * method: getMetadataFolder
     * when: called
     * with: tokenAndIdAndPathAndUser
     * should: returnPermissionDenied
     */
    public function test_getMetadataFolder_called_tokenAndIdAndPathAndUser_returnPermissionDenied()
    {
        $metadata = '{"error":403}';
        $this->exerciseGetMetadataFolder($metadata,json_decode($metadata));
    }

    /**
     * method: getMetadataFolder
     * when: called
     * with: tokenAndIdAndPathAndUserAndResourceUrl
     * should: returnPermissionDenied
     */
    public function test_getMetadataFolder_called_tokenAndIdAndPathAndUserAndResourceUrl_returnPermissionDenied()
    {
        $metadata = '{"error":403}';
        $this->exerciseGetMetadataFolder($metadata,json_decode($metadata),$this->resourceUrl);
    }

    /**
     * method: insertComment
     * when: called
     * with: cloudAndTokenAndIdAndUserAndTextAndResourceUrl
     * should: returnInsertCorrect
     */
    public function test_insertComment_called_cloudAndTokenAndIdAndUserAndTextAndResourceUrl_returnInsertCorrect()
    {
        $metadata = '{"id":"153","user":"eyeos","text":"prueba","cloud":"stacksync","status":"NEW","time_created":"201406201548"}';
        $this->exerciseInsertComment($metadata,array("status" => "OK"));
    }

    /**
     * method: insertComment
     * when: called
     * with: cloudAndTokenAndIdAndUserAndTextAndResourceUrl
     * should: returnException
     */
    public function test_insertComment_called_cloudAndTokenAndIdAndUserAndTextAndResourceUrl_returnException()
    {
        $metadata = '{"error":-1}';
        $this->exerciseInsertComment($metadata,array("status" => "KO","error" => -1));
    }

    /**
     * method: deleteComment
     * when: called
     * with: cloudAndTokenAndIdAndUserAndTimeCreatedAndResourceUrl
     * should: returnDeleteCorrect
     */
    public function test_deleteComment_called_cloudAndTokenAndIdAndUserAndTimeCreatedAndResourceUrl_returnDeleteCorrect()
    {
        $metadata = '{"id":"153","user":"eyeos","text":"prueba","cloud":"stacksync","status":"DELETED","time_created":"201406201548"}';
        $this->exerciseDeleteComment($metadata,array("status" => "OK"));
    }

    /**
     * method: deleteComment
     * when: called
     * with: cloudAndTokenAndIdAndUserAndTimeCreatedAndResourceUrl
     * should: returnException
     */
    public function test_deleteComment_called_cloudAndTokenAndIdAndUserAndTimeCreatedAndResourceUrl_returnException()
    {
        $metadata = '{"error":-1}';
        $this->exerciseDeleteComment($metadata,array("status" => "KO","error" => -1));
    }

    /**
     * method: getComments
     * when: called
     * with: cloudAndTokenAndIdAndResourceUrl
     * should: returnException
     */
    public function test_getComments_called_cloudAndTokenAndIdAndResourceUrl_returnListMetadata()
    {
        $metadata = '[{"id":"153","user":"eyeos","text":"prueba","cloud":"stacksync","status":"NEW","time_created":"201406201548"}]';
        $this->exerciseGetComments($metadata);
    }

    /**
     * method: getComments
     * when: called
     * with: cloudAndTokenAndIdAndResourceUrl
     * should: returnException
     */
    public function test_getComments_called_cloudAndTokenAndIdAndResourceUrl_returnException()
    {
        $metadata = '{"error":-1}';
        $this->exerciseGetComments($metadata);
    }

    /**
     * method: getControlCommentsCloud
     * when: called
     * with: ValidCloud
     * should: returnList
     */
    public function test_getControlCommentsCloud_called_validCloud_returnList()
    {
        $cloud = "Stacksync";
        $metadata = json_decode('{"comments":"true"}');
        $this->apiProviderMock->expects($this->once())
            ->method('getControlCommentsCloud')
            ->with($cloud)
            ->will($this->returnValue($metadata));
        $result = $this->sut->getControlCommentsCloud($cloud);
        $this->assertEquals($metadata, $result);
    }

    /**
     * method: getControlCommentsCloud
     * when: called
     * with: InvalidCloud
     * should: returnException
     */
    public function test_getControlCommentsCloud_called_Invalid_Cloud_returnException()
    {
        $cloud = "No_valid_cloud";
        $metadata =json_decode('{"error":-1}');
        $metadataOut = array("status" => "KO", "error" => -1);
        $this->apiProviderMock->expects($this->once())
            ->method('getControlCommentsCloud')
            ->with($cloud)
            ->will($this->returnValue($metadata));
        $result = $this->sut->getControlCommentsCloud($cloud);
        $this->assertEquals($metadataOut, $result);
    }


    private function exerciseCreateMetadata($file, $name, $parent_id, $path, $pathAbsolute, $metadataOut, $resourceUrl = null)
    {
        $type = $file?'false':'true';
        $metadata = '{"filename":"' . $name .'","id":"142555444","size":775412,"mimetype":"application/pdf","status":"NEW","version":3,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":'  . $type . '}';

        if($resourceUrl) {
            $metadataU1db = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","resource_url":"http://ast3-deim.urv.cat/v1/","access_token_key":"1234","access_token_secret":"ABCD","filename":"' . $name . '","id":"142555444","size":775412,"mimetype":"application/pdf","status":"NEW","version":3,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":' . $type . ',"path":"' . $path . '"}');
        } else {
            $metadataU1db = json_decode('{"cloud":"' . $this->cloud . '","user_eyeos":"' . $this->user . '","filename":"' . $name . '","id":"142555444","size":775412,"mimetype":"application/pdf","status":"NEW","version":3,"parent_id":"null","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_folder":' . $type . ',"path":"' . $path . '"}');
        }

        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->cloud, $this->token, false, $parent_id, true,$resourceUrl)
            ->will($this->returnValue(json_decode($metadataOut)));

        $this->apiProviderMock->expects($this->at(1))
            ->method('createMetadata')
            ->with($this->cloud,$this->token,$file,$name,$parent_id,$pathAbsolute,$resourceUrl)
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
            $aux->cloud = $this->cloud;
            $aux->user_eyeos = $this->user;
            $aux->version = 3;
            $aux->recover = false;
            array_push($params->lista,$aux);

            $this->accessorProviderMock->expects($this->at(1))
                ->method('getProcessDataU1db')
                ->with(json_encode($params))
                ->will($this->returnValue('true'));

        }

        $this->sut->createMetadata($this->cloud, $this->token, $this->user, $file,$name, $parent_id, $path, $pathAbsolute,$resourceUrl);
    }

    private function exerciseDeleteMetadata($metadata,$file,$id,$path,$pathOrig, $resourceUrl = NULL)
    {
        $this->apiProviderMock->expects($this->at(0))
            ->method('deleteMetadata')
            ->with($this->cloud,$this->token,$file,$id,$resourceUrl)
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

        $this->sut->deleteMetadata($this->cloud,$this->token,$file,$id,$this->user,$pathOrig,$resourceUrl);
    }

    private function exerciseRenameMetadata($file, $id, $parent, $path, $name, $cloud, $resourceUrl = null)
    {
        $type = $file ? 'false' : 'true';
        $metadata = '{"filename": "' . $name .'", "id": "' . $id . '", "size": 775412, "mimetype": "application/pdf", "status": "CHANGED", "version": 4, "parent_id": "'. $parent . '", "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997", "is_folder": ' . $type . '}';
        if($resourceUrl) {
            $metadataU1db = json_decode('{"cloud": "' . $cloud . '", "user_eyeos": "' . $this->user . '", "resource_url":"http:\/\/ast3-deim.urv.cat\/v1\/","access_token_key":"1234","access_token_secret":"ABCD","filename": "' . $name . '", "id": "' . $id . '", "size": 775412, "mimetype": "application/pdf", "status": "CHANGED", "version": 4, "parent_id": "' . $parent . '", "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997", "is_folder": ' . $type . ', "path": "' . $path . '"}');
        } else {
            $metadataU1db = json_decode('{"cloud": "' . $cloud . '", "user_eyeos": "' . $this->user . '", "filename": "' . $name . '", "id": "' . $id . '", "size": 775412, "mimetype": "application/pdf", "status": "CHANGED", "version": 4, "parent_id": "' . $parent . '", "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997", "is_folder": ' . $type . ', "path": "' . $path . '"}');
        }

        $this->apiProviderMock->expects($this->at(0))
            ->method('updateMetadata')
            ->with($cloud, $this->token, $file, $id, $name, $parent,$resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $u1dbIn = new stdClass();
        $u1dbIn->type = 'rename';
        $u1dbIn->lista = array();
        array_push($u1dbIn->lista, $metadataU1db);

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('true'));

        $this->sut->renameMetadata($cloud, $this->token, $file, $id, $name, $path, $this->user, $parent,$resourceUrl);
    }

    private function exerciseMoveMetadata($id,$filename,$parent,$file,$pathOrig,$pathNew,$metadataMove,$metadataDelete,$metadataInsert,$fileDest = null,$resourceUrl = null)
    {
        $this->apiProviderMock->expects($this->at(0))
            ->method('updateMetadata')
            ->with($this->cloud,$this->token,$file,$id,$fileDest?$fileDest:$filename,$parent,$resourceUrl)
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
            ->with($this->pathCloud . $pathOrig . $filename,!$file)
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
            ->with($this->pathCloud . $pathNew . $fileNew,!$file)
            ->will($this->returnValue(true));

        $this->sut->moveMetadata($this->cloud,$this->token,$file,$id,$this->pathCloud,$this->pathCloud . "/documents",$this->user,$parent,$filename,$fileDest,$resourceUrl);

    }

    private function getDownloadMetadata($metadata, $metadataVersion,$expected, $id, $cloud, $resourceUrl = null)
    {
        $this->apiProviderMock->expects($this->at(0))
            ->method('getControlVersionCloud')
            ->with($cloud)
            ->will($this->returnValue($metadataVersion));

        $this->apiProviderMock->expects($this->at(1))
            ->method('getMetadata')
            ->with($cloud, $this->token, true, $id,null, $resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $params = new stdClass();
        $params->type = "getDownloadVersion";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = $id;
        $aux->user_eyeos = $this->user;
        $aux->cloud = $cloud;
        array_push($params->lista, $aux);

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue($expected));
    }

    private function exerciseGetFileVersionData($metadataU1db, $metadataOut, $expected, $id, $version, $path, $type, $exception = false,$resourceUrl = null)
    {
        $params = new stdClass();
        $params->type = "getDownloadVersion";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = "" . $id;
        $aux->user_eyeos = $this->user;
        $aux->cloud = $this->cloud;
        array_push($params->lista, $aux);

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
            ->with($this->cloud, $this->token, $id, $version, $path,$resourceUrl)
            ->will($this->returnValue(json_decode($metadataOut)));


        if(!$exception) {
            $params = new stdClass();
            $params->type = $type;
            $params->lista = array();
            $aux = new stdClass();
            $aux->id = "" . $id;
            $aux->cloud = $this->cloud;
            $aux->user_eyeos = $this->user;
            $aux->version = $version;
            $aux->recover = true;
            array_push($params->lista,$aux);

            $this->accessorProviderMock->expects($this->at(1))
                ->method('getProcessDataU1db')
                ->with(json_encode($params))
                ->will($this->returnValue('true'));
        }

        $result = $this->sut->getFileVersionData($this->cloud, $this->token, $id, $version, $path, $this->user,$resourceUrl);
        $this->assertEquals($expected, $result);
    }

    private function exerciseGetListUsersShare($metadata, $id, $expected, $resourceUrl = null)
    {
        $this->apiProviderMock->expects($this->once())
            ->method('getListUsersShare')
            ->with($this->cloud, $this->token, $id, $resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));
        $result = $this->sut->getListUsersShare($this->cloud, $this->token, $id, $resourceUrl);
        $this->assertEquals($expected, $result);
    }

    private function exerciseShareFolder($metadata, $id, $list, $expected, $shared ,$resourceUrl = null)
    {
        $this->apiProviderMock->expects($this->once())
            ->method('shareFolder')
            ->with($this->cloud, $this->token, $id, $list,$shared,$resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $result = $this->sut->shareFolder($this->cloud, $this->token, $id, $list,$shared,$resourceUrl);
        $this->assertEquals($expected, $result);
    }

    private function exerciseGetMetadatacalledU1dbWithoutData($metadata1,$metadata2,$metadata3,$u1dbIn,$id,$isfile,$contents,$idShare,$path,$resourceUrl = NULL)
    {
        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->cloud, $this->token, $isfile, $id, $contents,$resourceUrl)
            ->will($this->returnValue(json_decode($metadata1)));

        $this->apiProviderMock->expects($this->at(1))
            ->method('getListUsersShare')
            ->with($this->cloud, $this->token, $idShare,$resourceUrl)
            ->will($this->returnValue(json_decode('{}')));

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('[]'));

        $this->filesProviderMock->expects($this->at(0))
            ->method('createFile')
            ->with($path . "/client2", true)
            ->will($this->returnValue(true));

        $this->filesProviderMock->expects($this->at(1))
            ->method('createFile')
            ->with($path . "/Client3.pdf", false)
            ->will($this->returnValue(true));

        $u1dbIn = new stdClass();
        $u1dbIn->type = 'insert';
        $u1dbIn->lista = array();
        array_push($u1dbIn->lista,$metadata2);

        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('true'));

        $u1dbIn = new stdClass();
        $u1dbIn->type = 'insert';
        $u1dbIn->lista = array();
        array_push($u1dbIn->lista,$metadata3);

        $this->accessorProviderMock->expects($this->at(2))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue('true'));

    }

    private function exerciseGetMetadatacalledU1dbSameData($id,$idU1db,$metadata,$u1dbOut,$idShare,$path,$pathCloud,$contents,$resourceUrl = NULL)
    {
        $isfile = false;
        $u1dbIn = new stdClass();
        $u1dbIn->type = 'select';
        $u1dbIn->lista = array();
        $file = new stdClass();
        $file->id = $idU1db;
        $file->user_eyeos = $this->user;
        $file->cloud = $this->cloud;
        $file->path = $path;
        array_push($u1dbIn->lista,$file);

        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->cloud,$this->token,$isfile,$id,$contents,$resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($u1dbIn))
            ->will($this->returnValue($u1dbOut));

        $this->apiProviderMock->expects($this->at(1))
            ->method('getListUsersShare')
            ->with($this->cloud, $this->token, $idShare,$resourceUrl)
            ->will($this->returnValue('{}'));

        $this->filesProviderMock->expects($this->never())
            ->method('createFile')
            ->will($this->returnValue(true));

        $this->filesProviderMock->expects($this->never())
            ->method('deleteFile')
            ->will($this->returnValue(true));

        $this->sut->getMetadata($this->cloud,$this->token,$id,$pathCloud,$this->user,$resourceUrl);
    }

    private function exerciseIsBlockedFile($u1dbOut,$check,$dt_now)
    {
        $id = "1245678";
        $params = new stdClass();
        $params->type = "getMetadataFile";
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = $id;
        $aux->cloud = $this->cloud;
        array_push($params->lista,$aux);
        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue($u1dbOut));
        $result = $this->sut->unLockedFile($id,$this->cloud,$this->username,$this->IpServer,$this->timeLimit,$dt_now);
        $this->assertEquals($check,$result);
    }

    private function exerciselockFile($u1dbOut,$check,$type,$status)
    {
        $id = "1245678";
        $dt_now = DateTime::createFromFormat('Y-m-d H:i:s',"2015-05-12 10:50:00");
        $params = new stdClass();
        $params->type = $type;
        $params->lista = array();
        $aux = new stdClass();
        $aux->id = $id;
        $aux->cloud = $this->cloud;
        $aux->username = $this->username;
        $aux->IpServer = $this->IpServer;
        $aux->datetime = $dt_now->format("Y-m-d H:i:s");
        $aux->status = $status;
        if($type == 'lockFile') {
            $aux->timeLimit = $this->timeLimit;
        }

        array_push($params->lista,$aux);
        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($params))
            ->will($this->returnValue($u1dbOut));
        if($type == 'lockFile') {
            $result = $this->sut->lockFile($id, $this->cloud, $this->username, $this->IpServer, $this->timeLimit, $dt_now);
        } else if ($type == 'updateDateTime') {
            $result = $this->sut->updateDateTime($id, $this->cloud, $this->username, $this->IpServer, $dt_now);
        } else {
            $result = $this->sut->unLockFile($id, $this->cloud, $this->username, $this->IpServer, $dt_now);
        }
        $this->assertEquals($check,$result);
    }

    private function exerciseGetMetadataFolder($metadata,$check,$resourceUrl = null)
    {
        $id = 'root';
        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->with($this->cloud, $this->token, false, $id, true,$resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $result = $this->sut->getMetadataFolder($this->cloud,$this->token,$id,$resourceUrl);
        $this->assertEquals($check, $result);
    }

    private function exerciseInsertComment($metadata,$check)
    {
        $id = "153";
        $text = "prueba";
        $resourceUrl = "http://192.168.56.101/";

        $this->apiProviderMock->expects($this->at(0))
            ->method('insertComment')
            ->with($this->cloud, $this->token,$id,$this->username,$text,$resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $result = $this->sut->insertComment($this->cloud,$this->token,$id,$this->username,$text,$resourceUrl);
        $this->assertEquals($check, $result);
    }

    private function exerciseDeleteComment($metadata,$check)
    {
        $id = "153";
        $timeCreated = "201406201548";
        $resourceUrl = "http://192.168.56.101/";

        $this->apiProviderMock->expects($this->at(0))
            ->method('deleteComment')
            ->with($this->cloud, $this->token,$id,$this->username,$timeCreated,$resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $result = $this->sut->deleteComment($this->cloud,$this->token,$id,$this->username,$timeCreated,$resourceUrl);
        $this->assertEquals($check, $result);
    }

    private function exerciseGetComments($metadata)
    {
        $id = "153";
        $resourceUrl = "http://192.168.56.101/";

        $this->apiProviderMock->expects($this->at(0))
            ->method('getComments')
            ->with($this->cloud, $this->token,$id,$resourceUrl)
            ->will($this->returnValue(json_decode($metadata)));

        $result = $this->sut->getComments($this->cloud,$this->token,$id,$resourceUrl);
        $this->assertEquals(json_decode($metadata), $result);
    }
}

?>