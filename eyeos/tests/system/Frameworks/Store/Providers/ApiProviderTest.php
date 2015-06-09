<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28/05/14
 * Time: 10:20
 */

class ApiProviderTest extends PHPUnit_Framework_TestCase
{
    private $accessorProviderMock;
    private $sut;
    private $token;
    private $exception;
    private $permission;
    private $cloud;
    private $resourceUrl;

    public function setUp()
    {
        $this->accessorProviderMock = $this->getMock('AccessorProvider');
        $this->sut = new ApiProvider($this->accessorProviderMock);
        $this->token = new stdClass();
        $this->token->key = "ABCD";
        $this->token->secret = "EFGH";
        $this->exception = '{"error":-1}';
        $this->permission = '{"error":403}';
        $this->cloud = 'Stacksync';
        $this->resourceUrl = "http://ast3-deim.urv.cat/v1";
    }

    public function tearDown()
    {
        $this->accessorProviderMock = null;
        $this->token = null;
    }

    /**
     * method: getMetadata
     * when: called
     * with: tokenAndFileAndId
     * should: returnCorrectData
     */
    public function test_getMetadata_called_tokenAndfileAndId_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"get","file":true,"id":"123456","contents":null}}';
        $metadataOut = '{"name":"Client1.pdf","path":"/documents/clients/Client1.pdf","id":"32565632156","size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent_id":"-348534824681","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"}';
        $this->exerciseGetMetadata($metadataIn,$metadataOut,$metadataOut,true,123456);
    }

    /**
     * method: getMetadata
     * when: called
     * with: tokenAndFileAndIdAndResourceUrl
     * should: returnCorrectData
     */
    public function test_getMetadata_called_tokenAndFileAndIdAndResourceUrl_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"get","file":true,"id":"123456","contents":null}}';
        $metadataOut = '{"name":"Client1.pdf","path":"/documents/clients/Client1.pdf","id":"32565632156","size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent_id":"-348534824681","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"}';
        $this->exerciseGetMetadata($metadataIn,$metadataOut,$metadataOut,true,123456,null,$this->resourceUrl);
    }

    /**
     * method: getMetadata
     * when: called
     * with: tokenAndFolderAndId
     * should: returnCorrectData
     */
    public function test_getMetadata_called_tokenAndFolderAndId_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"get","file":false,"id":"9873615","contents":null}}';
        $metadataOut = '{"name":"clients","path":"/documents/clients","id":"9873615","status":"NEW","version":1,"parent_id":"-348534824681","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false}';
        $this->exerciseGetMetadata($metadataIn,$metadataOut,$metadataOut,false,9873615);
    }

    /**
     * method: getMetadata
     * when: called
     * with: tokenAndFolderAndIdAndResourceUrl
     * should: returnCorrectData
     */
    public function test_getMetadata_called_tokenAndFolderAndIdAndResourceUrl_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"get","file":false,"id":"9873615","contents":null}}';
        $metadataOut = '{"name":"clients","path":"/documents/clients","id":"9873615","status":"NEW","version":1,"parent_id":"-348534824681","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false}';
        $this->exerciseGetMetadata($metadataIn,$metadataOut,$metadataOut,false,9873615,null,$this->resourceUrl);
    }

    /**
     * method: getMetadata
     * when: called
     * with: tokenAndFolderAndIdAndContents
     * should: returnCorrectData
     */
    public function test_getMetadata_called_tokenAndFolderAndIdAndContents_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"get","file":false,"id":"9873615","contents":true}}';
        $metadataOut = '{"name":"clients","path":"/documents/clients","id":"9873615","status":"NEW","version":1,"parent_id":"-348534824681","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"contents":[{"name":"Client1.pdf","path":"/documents/clients/Client1.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent":-348534824681,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false}]}';
        $this->exerciseGetMetadata($metadataIn,$metadataOut,$metadataOut,false,9873615,true);

    }

    /**
     * method: getMetadata
     * when: called
     * with: tokenAndFolderAndIdAndContentsAndResourceUrl
     * should: returnCorrectData
     */
    public function test_getMetadata_called_tokenAndFolderAndIdAndContentsAndResourceUrl_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"get","file":false,"id":"9873615","contents":true}}';
        $metadataOut = '{"name":"clients","path":"/documents/clients","id":"9873615","status":"NEW","version":1,"parent_id":"-348534824681","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"contents":[{"name":"Client1.pdf","path":"/documents/clients/Client1.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent":-348534824681,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false}]}';
        $this->exerciseGetMetadata($metadataIn,$metadataOut,$metadataOut,false,9873615,true,$this->resourceUrl);
    }

    /**
     * method: getMetadata
     * when: called
     * with: tokenAndFolderAndId
     * should: returnException
     */
    public function test_getMetadata_called_tokenAndFolderAndId_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"get","file":false,"id":"9873615","contents":null}}';
        $metadataOut = 'false';
        $this->exerciseGetMetadata($metadataIn,$metadataOut,$this->exception,false,9873615);
    }

    /**
     * method: getMetadata
     * when: called
     * with: tokenAndFolderAndIdAndResourceUrl
     * should: returnException
     */
    public function test_getMetadata_called_tokenAndFolderAndIdAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"get","file":false,"id":"9873615","contents":null}}';
        $metadataOut = 'false';
        $this->exerciseGetMetadata($metadataIn,$metadataOut,$this->exception,false,9873615,null,$this->resourceUrl);
    }


    /**
     * method: getMetadata
     * when: called
     * with: tokenAndFolderAndId
     * should: returnPermissionDenied
     */
    public function test_getMetadata_called_tokenAndFolderAndId_returnPermissionDenied()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"get","file":false,"id":"9873615","contents":null}}';
        $metadataOut = '403';
        $this->exerciseGetMetadata($metadataIn,$metadataOut,$this->permission,false,9873615);
    }

    /**
     * method: getMetadata
     * when: called
     * with: tokenAndFolderAndIdAndResourceUrl
     * should: returnPermissionDenied
     */
    public function test_getMetadata_called_tokenAndFolderAndIdAndResourceUrl_returnPermissionDenied()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"get","file":false,"id":"9873615","contents":null}}';
        $metadataOut = '403';
        $this->exerciseGetMetadata($metadataIn,$metadataOut,$this->permission,false,9873615,null,$this->resourceUrl);
    }

    /**
     * method: updateMetadata
     * when: called
     * with: cloudAndTokenAndFileAndIdAndNameAndParent
     * should: returnMetadataRename
     */
    public function test_updateMetadata_called_cloudAndTokenAndFileAndIdAndNameAndParent_returnMetadataRename()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":true,"id":"32565632156","filename":"Winter2012_renamed.jpg","parent_id":"12386548974"}}';
        $metadataOut = '{"name": "Winter2012_renamed.jpg", "path": "/documents/clients/", "id": "32565632156", "size": 775412, "mimetype": "application/pdf", "status": "RENAMED", "version": 2, "parent": "12386548974", "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997"}';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $metadataOut, true, 32565632156, "Winter2012_renamed.jpg", 12386548974, $this->cloud);
    }

    /**
     * method: updateMetadata
     * when: called
     * with: cloudAndTokenAndFileAndIdAndNameAndParentAndResourceUrl
     * should: returnMetadataRename
     */
    public function test_updateMetadata_called_cloudAndTokenAndFileAndIdAndNameAndParentAndResource_returnMetadataRename()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":true,"id":"32565632156","filename":"Winter2012_renamed.jpg","parent_id":"12386548974"}}';
        $metadataOut = '{"name": "Winter2012_renamed.jpg", "path": "/documents/clients/", "id": "32565632156", "size": 775412, "mimetype": "application/pdf", "status": "RENAMED", "version": 2, "parent": "12386548974", "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997"}';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $metadataOut, true, 32565632156, "Winter2012_renamed.jpg", 12386548974, $this->cloud,$this->resourceUrl);
    }


    /**
     * method: updateMetadata
     * when: called
     * with: cloudAndTokenAndFileAndIdAndParent
     * should: returnMetadataMove
     */
    public function test_updateMetadata_called_cloudAndTokenAndFileAndIdAndParent_returnMetadataMove()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":true,"id":"32565632156","filename":null,"parent_id":"123456"}}';
        $metadataOut = '{"name": "Winter2012_renamed.jpg", "path": "/documents/clients/", "id": "32565632156", "size": 775412, "mimetype": "application/pdf", "status": "CHANGED", "version": 2, "parent": "123456", "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997"}';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $metadataOut, true, 32565632156, null, 123456, $this->cloud);
    }

    /**
     * method: updateMetadata
     * when: called
     * with: cloudAndTokenAndFileAndIdAndParentAndResourceUrl
     * should: returnMetadataMove
     */
    public function test_updateMetadata_called_cloudAndTokenAndFileAndIdAndParentAndResourceUrl_returnMetadataMove()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":true,"id":"32565632156","filename":null,"parent_id":"123456"}}';
        $metadataOut = '{"name": "Winter2012_renamed.jpg", "path": "/documents/clients/", "id": "32565632156", "size": 775412, "mimetype": "application/pdf", "status": "CHANGED", "version": 2, "parent": "123456", "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997"}';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $metadataOut, true, 32565632156, null, 123456, $this->cloud,$this->resourceUrl);
    }

    /**
     * method: updateMetadata
     * when: called
     * with: cloudAndTokenAndFileAndId
     * should: returnMetadataMove
     */
    public function test_updateMetadata_called_cloudAndTokenAndFileAndId_returnMetadataMove()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":true,"id":"32565632156","filename":null,"parent_id":"null"}}';
        $metadataOut = '{"name": "Winter2012_renamed.jpg", "path": "/documents/clients/", "id": "32565632156", "size": 775412, "mimetype": "application/pdf", "status": "CHANGED", "version": 2, "parent_id": "null", "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997"}';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $metadataOut, true, 32565632156, null, null, $this->cloud);
    }

    /**
     * method: updateMetadata
     * when: called
     * with: cloudAndTokenAndFileAndIdAndResourceUrl
     * should: returnMetadataMove
     */
    public function test_updateMetadata_called_cloudAndTokenAndFileAndIdAndResourceUrl_returnMetadataMove()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":true,"id":"32565632156","filename":null,"parent_id":"null"}}';
        $metadataOut = '{"name": "Winter2012_renamed.jpg", "path": "/documents/clients/", "id": "32565632156", "size": 775412, "mimetype": "application/pdf", "status": "CHANGED", "version": 2, "parent_id": "null", "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997"}';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $metadataOut, true, 32565632156, null, null, $this->cloud,$this->resourceUrl);
    }

    /**
     * method: updateMetadata
     * when: called
     * with: cloudAndTokenAndFolderAndIdAndNameAndParent
     * should: returnMetadataRename
     */
    public function test_updateMeta_called_cloudAndTokenAndFolderAndIdAndNameAndParent_returnMetadataRename()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":false,"id":"32565632156","filename":"Winter2012_renamed","parent_id":"12386548974"}}';
        $metadataOut = '{"name": "Winter2012_renamed", "path": "/documents/clients/", "id": "32565632156", "status": "CHANGED", "version": 2, "parent": "12386548974", "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997", "is_root": false}';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $metadataOut, false, 32565632156, "Winter2012_renamed", 12386548974, $this->cloud);
    }

    /**
     * method: updateMetadata
     * when: called
     * with: cloudAndTokenAndFolderAndIdAndNameAndParentAndResourceUrl
     * should: returnMetadataRename
     */
    public function test_updateMeta_called_cloudAndTokenAndFolderAndIdAndNameAndParentAndResourceUrl_returnMetadataRename()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":false,"id":"32565632156","filename":"Winter2012_renamed","parent_id":"12386548974"}}';
        $metadataOut = '{"name": "Winter2012_renamed", "path": "/documents/clients/", "id": "32565632156", "status": "CHANGED", "version": 2, "parent": "12386548974", "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997", "is_root": false}';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $metadataOut, false, 32565632156, "Winter2012_renamed", 12386548974, $this->cloud,$this->resourceUrl);

    }

    /**
     * method: updateMetadata
     * when: called
     * with: cloudAndTokenAndFolderAndIdAndParent
     * should: returnMetadataMove
     */
    public function test_updateMetadata_called_cloudAndTokenAndFolderAndIdAndParent_returnMetadataMove()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":false,"id":"32565632156","filename":null,"parent_id":"123456"}}';
        $metadataOut = '{"name": "Winter2012_renamed", "path": "/documents/clients/", "id": "32565632156", "status": "CHANGED", "version": 2, "parent": "123456", "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997", "is_root": false}';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $metadataOut, false, 32565632156, null, 123456, $this->cloud);
    }

    /**
     * method: updateMetadata
     * when: called
     * with: cloudAndTokenAndFolderAndIdAndParentAndResourceUrl
     * should: returnMetadataMove
     */
    public function test_updateMetadata_called_cloudAndTokenAndFolderAndIdAndParentAndResourceUrl_returnMetadataMove()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":false,"id":"32565632156","filename":null,"parent_id":"123456"}}';
        $metadataOut = '{"name": "Winter2012_renamed", "path": "/documents/clients/", "id": "32565632156", "status": "CHANGED", "version": 2, "parent": "123456", "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997", "is_root": false}';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $metadataOut, false, 32565632156, null, 123456, $this->cloud,$this->resourceUrl);
    }

    /**
     * method: updateMetadata
     * when: called
     * with: cloudAndTokenAndFolderAndId
     * should: returnMetadataMove
     */
    public function test_updateMetadata_called_cloudAndTokenAndFolderAndId_returnMetadataMove()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":false,"id":"32565632156","filename":null,"parent_id":"null"}}';
        $metadataOut = '{"name": "Winter2012_renamed", "path": "/documents/clients/", "id": "32565632156", "status": "CHANGED", "version": 2, "parent_id": "null", "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997", "is_root": false}';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $metadataOut, false, 32565632156, null, null, $this->cloud);
    }

    /**
     * method: updateMetadata
     * when: called
     * with: cloudAndTokenAndFolderAndIdAndResourceUrl
     * should: returnMetadataMove
     */
    public function test_updateMetadata_called_cloudAndTokenAndFolderAndIdAndResourceUrl_returnMetadataMove()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":false,"id":"32565632156","filename":null,"parent_id":"null"}}';
        $metadataOut = '{"name": "Winter2012_renamed", "path": "/documents/clients/", "id": "32565632156", "status": "CHANGED", "version": 2, "parent_id": "null", "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997", "is_root": false}';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $metadataOut, false, 32565632156, null, null, $this->cloud,$this->resourceUrl);

    }

    /**
     * method: updateMetadata
     * when: called
     * with: cloudAndTokenAndFolderAndId
     * should: returnException
     */
    public function test_updateMetadata_called_cloudAndTokenAndFolderAndId_returnException()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":false,"id":"32565632156","filename":null,"parent_id":"null"}}';
        $metadataOut = 'false';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $this->exception, false, 32565632156, null, null, $this->cloud);
    }

    /**
     * method: updateMetadata
     * when: called
     * with: cloudAndTokenAndFolderAndIdAndResourceUrl
     * should: returnException
     */
    public function test_updateMetadata_called_cloudAndTokenAndFolderAndIdAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":false,"id":"32565632156","filename":null,"parent_id":"null"}}';
        $metadataOut = 'false';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $this->exception, false, 32565632156, null, null, $this->cloud,$this->resourceUrl);
    }

    /**
     * method: updateMetadata
     * when: called
     * with: cloudAndTokenAndFolderAndId
     * should: returnPermissionDenied()
     */
    public function test_updateMetadata_called_cloudAndTokenAndFolderAndId_returnPermissionDenied()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":false,"id":"32565632156","filename":null,"parent_id":"null"}}';
        $metadataOut = '403';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $this->permission, false, 32565632156, null, null, $this->cloud);
    }

    /**
     * method: updateMetadata
     * when: called
     * with: cloudAndTokenAndFolderAndIdAndResourceUrl
     * should: returnPermissionDenied()
     */
    public function test_updateMetadata_called_cloudAndTokenAndFolderAndIdAndResourceUrl_returnPermissionDenied()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":false,"id":"32565632156","filename":null,"parent_id":"null"}}';
        $metadataOut = '403';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $this->permission, false, 32565632156, null, null, $this->cloud,$this->resourceUrl);
    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndFileAndNameAndParentAndPathAbsolute
     * should: returnCorrectData
     */
    public function test_createMetadata_called_tokenAndFileAndNameAndParentAndPathAbsolute_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"create","file":true,"filename":"Client1.pdf","parent_id":"-348534824681","path":"\/home\/eyeos\/Client1.pdf"}}';
        $metadataOut = '{"filename":"Client1.pdf","id":"32565632156","parent_id":"-348534824681","user":"eyeos"}';
        $pathAbsolute = '/home/eyeos/Client1.pdf';
        $this->exerciseCreateMetadata($metadataIn,$metadataOut,$metadataOut,true,'Client1.pdf',-348534824681,$pathAbsolute);
    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndFileAndNameAndParentAndPathAbsoluteAndResourceUrl
     * should: returnCorrectData
     */
    public function test_createMetadata_called_tokenAndFileAndNameAndParentAndPathAbsoluteAndResourceUrl_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"create","file":true,"filename":"Client1.pdf","parent_id":"-348534824681","path":"\/home\/eyeos\/Client1.pdf"}}';
        $metadataOut = '{"filename":"Client1.pdf","id":"32565632156","parent_id":"-348534824681","user":"eyeos"}';
        $pathAbsolute = '/home/eyeos/Client1.pdf';
        $this->exerciseCreateMetadata($metadataIn,$metadataOut,$metadataOut,true,'Client1.pdf',-348534824681,$pathAbsolute,$this->resourceUrl);
    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndFileAndNameAndPathAbsolute
     * should: returnCorrectData
     */
    public function test_createMetadata_called_tokenAndFileAndNameAndPathAbsolute_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"create","file":true,"filename":"Client1.pdf","parent_id":"null","path":"\/home\/eyeos\/Client1.pdf"}}';
        $metadataOut = '{"filename":"Client1.pdf","id":"32565632156","parent_id":"null","user":"eyeos"}';
        $pathAbsolute = '/home/eyeos/Client1.pdf';
        $this->exerciseCreateMetadata($metadataIn,$metadataOut,$metadataOut,true,'Client1.pdf',null,$pathAbsolute);
    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndFileAndNameAndPathAbsoluteAndResourceUrl
     * should: returnCorrectData
     */
    public function test_createMetadata_called_tokenAndFileAndNameAndPathAbsoluteAndResourceUrl_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"create","file":true,"filename":"Client1.pdf","parent_id":"null","path":"\/home\/eyeos\/Client1.pdf"}}';
        $metadataOut = '{"filename":"Client1.pdf","id":"32565632156","parent_id":"null","user":"eyeos"}';
        $pathAbsolute = '/home/eyeos/Client1.pdf';
        $this->exerciseCreateMetadata($metadataIn,$metadataOut,$metadataOut,true,'Client1.pdf',null,$pathAbsolute,$this->resourceUrl);

    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndFolderAndNameAndParentAndPathAbsolute
     * should: returnCorrectData
     */
    public function test_createMetadata_called_tokenAndFolderAndNameAndParentAndPathAbsolute_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"create","file":false,"filename":"clients","parent_id":"-348534824681","path":null}}';
        $metadataOut = '{"filename":"clients","id":"9873615","parent_id":"-348534824681","user":"eyeos","is_root":false}';
        $this->exerciseCreateMetadata($metadataIn,$metadataOut,$metadataOut,false,"clients",-348534824681);
    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndFolderAndNameAndParentAndPathAbsoluteAndResourceUrl
     * should: returnCorrectData
     */
    public function test_createMetadata_called_tokenAndFolderAndNameAndParentAndPathAbsoluteAndResourceUrl_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"create","file":false,"filename":"clients","parent_id":"-348534824681","path":null}}';
        $metadataOut = '{"filename":"clients","id":"9873615","parent_id":"-348534824681","user":"eyeos","is_root":false}';
        $this->exerciseCreateMetadata($metadataIn,$metadataOut,$metadataOut,false,"clients",-348534824681,null,$this->resourceUrl);

    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndFolderAndName
     * should: returnCorrectData
     */
    public function test_createMetadata_called_tokenAndFolderAndName_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"create","file":false,"filename":"clients","parent_id":"null","path":null}}';
        $metadataOut = '{"filename":"clients","id":"9873615","parent_id":"null","user":"eyeos","is_root":false}';
        $this->exerciseCreateMetadata($metadataIn,$metadataOut,$metadataOut,false,"clients");
    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndFolderAndNameAndResourceUrl
     * should: returnCorrectData
     */
    public function test_createMetadata_called_tokenAndFolderAndNameAndResourceUrl_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"create","file":false,"filename":"clients","parent_id":"null","path":null}}';
        $metadataOut = '{"filename":"clients","id":"9873615","parent_id":"null","user":"eyeos","is_root":false}';
        $this->exerciseCreateMetadata($metadataIn,$metadataOut,$metadataOut,false,"clients",null,null,$this->resourceUrl);
    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndFolderAndName
     * should: returnException
     */
    public function test_createMetadata_called_tokenAndFolderAndName_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"create","file":false,"filename":"clients","parent_id":"null","path":null}}';
        $metadataOut = 'false';
        $this->exerciseCreateMetadata($metadataIn,$metadataOut,$this->exception,false,"clients");
    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndFolderAndNameAndResourceUrl
     * should: returnException
     */
    public function test_createMetadata_called_tokenAndFolderAndNameAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"create","file":false,"filename":"clients","parent_id":"null","path":null}}';
        $metadataOut = 'false';
        $this->exerciseCreateMetadata($metadataIn,$metadataOut,$this->exception,false,"clients",null,null,$this->resourceUrl);
    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndFolderAndName
     * should: returnPermissionDenied
     */
    public function test_createMetadata_called_tokenAndFolderAndName_returnPermissionDenied()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"create","file":false,"filename":"clients","parent_id":"null","path":null}}';
        $metadataOut = '403';
        $this->exerciseCreateMetadata($metadataIn,$metadataOut,$this->permission,false,"clients");
    }

    /**
     * method: createMetadata
     * when: called
     * with: tokenAndFolderAndNameAndResourceUrl
     * should: returnPermissionDenied
     */
    public function test_createMetadata_called_tokenAndFolderAndNameAndResourceUrl_returnPermissionDenied()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"create","file":false,"filename":"clients","parent_id":"null","path":null}}';
        $metadataOut = '403';
        $this->exerciseCreateMetadata($metadataIn,$metadataOut,$this->permission,false,"clients",null,null,$this->resourceUrl);
    }

    /**
     * method: uploadMetadata
     * when: called
     * with: tokenAndIdAndPath
     * should: returnCorrect
     */
    public function test_uploadMetadata_called_tokenAndIdAndPath_returnCorrect()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"upload","id":"1234561","path":"\/var\/www\/eyeos\/client.pdf"}}';
        $metadataOut = '{"status":true}';
        $this->exerciseUploadMetadata($metadataIn, $metadataOut, $metadataOut, 1234561, "/var/www/eyeos/client.pdf");
    }

    /**
     * method: uploadMetadata
     * when: called
     * with: tokenAndIdAndPathAndResourceUrl
     * should: returnCorrect
     */
    public function test_uploadMetadata_called_tokenAndIdAndPathAndResourceUrl_returnCorrect()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"upload","id":"1234561","path":"\/var\/www\/eyeos\/client.pdf"}}';
        $metadataOut = '{"status":true}';
        $this->exerciseUploadMetadata($metadataIn, $metadataOut, $metadataOut, 1234561, "/var/www/eyeos/client.pdf",$this->resourceUrl);
    }

    /**
     * method: uploadMetadata
     * when: called
     * with: tokenAndIdAndPath
     * should: returnException
     */
    public function test_uploadMetadata_called_tokenAndIdAndPath_returnException()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"upload","id":"1234561","path":"\/var\/www\/eyeos\/client.pdf"}}';
        $metadataOut = 'false';
        $this->exerciseUploadMetadata($metadataIn, $metadataOut, $this->exception, 1234561, "/var/www/eyeos/client.pdf");
    }

    /**
     * method: uploadMetadata
     * when: called
     * with: tokenAndIdAndPathAndResourceUrl
     * should: returnException
     */
    public function test_uploadMetadata_called_tokenAndIdAndPathAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"upload","id":"1234561","path":"\/var\/www\/eyeos\/client.pdf"}}';
        $metadataOut = 'false';
        $this->exerciseUploadMetadata($metadataIn, $metadataOut, $this->exception, 1234561, "/var/www/eyeos/client.pdf",$this->resourceUrl);
    }

    /**
     * method: uploadMetadata
     * when: called
     * with: tokenAndIdAndPath
     * should: returnPermissionDenied
     */
    public function test_uploadMetadata_called_tokenAndIdAndPath_returnPermissionDenied()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"upload","id":"1234561","path":"\/var\/www\/eyeos\/client.pdf"}}';
        $metadataOut = '403';
        $this->exerciseUploadMetadata($metadataIn, $metadataOut, $this->permission, 1234561, "/var/www/eyeos/client.pdf");
    }

    /**
     * method: uploadMetadata
     * when: called
     * with: tokenAndIdAndPathAndResourceUrl
     * should: returnPermissionDenied
     */
    public function test_uploadMetadata_called_tokenAndIdAndPathAndResourceUrl_returnPermissionDenied()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"upload","id":"1234561","path":"\/var\/www\/eyeos\/client.pdf"}}';
        $metadataOut = '403';
        $this->exerciseUploadMetadata($metadataIn, $metadataOut, $this->permission, 1234561, "/var/www/eyeos/client.pdf",$this->resourceUrl);
    }

    /**
     * method: downloadMetadata
     * when: called
     * with: tokenAndIdAndPath
     * should: returnCorrectDonwloadFile
     */
    public function test_downloadMetadata_called_tokenAndIdAndPath_returnCorrectDownloadFile()
    {
        $path = "/home/eyeos/prueba1.pdf";
        $metadataIn = '{"config": {"cloud": "' . $this->cloud . '"}, "token": {"key": "ABCD", "secret": "EFGH"}, "metadata": {"type": "download", "id": "1234561", "path": "' . $path . '"}}';
        $metadataOut = 'true';
        $cloud = "Stacksync";
        $this->exerciseDownloadMetadata($metadataOut, $metadataIn, $metadataOut, 1234561, $path, $cloud);
    }

    /**
     * method: downloadMetadata
     * when: called
     * with: tokenAndIdAndPathAndResourceUrl
     * should: returnCorrectDonwloadFile
     */
    public function test_downloadMetadata_called_tokenAndIdAndPathAndResourceUrl_returnCorrectDownloadFile()
    {
        $path = "/home/eyeos/prueba1.pdf";
        $metadataIn = '{"config": {"cloud": "' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"}, "token": {"key": "ABCD", "secret": "EFGH"}, "metadata": {"type": "download", "id": "1234561", "path": "' . $path . '"}}';
        $metadataOut = 'true';
        $this->exerciseDownloadMetadata($metadataOut, $metadataIn, $metadataOut, 1234561, $path, $this->cloud, $this->resourceUrl);
    }

    /**
     * method: downloadMetadata
     * when: called
     * with: tokenAndIdAndPath
     * should: returnException
     */
    public function test_downloadMetadata_called_tokenAndIdAndPath_returnException()
    {
        $path = "/home/eyeos/prueba2.pdf";
        $metadataIn = '{"config": {"cloud": "' . $this->cloud . '"}, "token": {"key": "ABCD", "secret": "EFGH"}, "metadata": {"type": "download", "id": "1234561", "path": "' . $path . '"}}';
        $metadataOut = 'false';
        $cloud = "Stacksync";
        $this->exerciseDownloadMetadata($metadataOut, $metadataIn, json_decode($this->exception), 1234561, $path, $cloud);
    }

    /**
     * method: downloadMetadata
     * when: called
     * with: tokenAndIdAndPath
     * should: returnException
     */
    public function test_downloadMetadata_called_tokenAndIdAndPathAndResourceUrl_returnException()
    {
        $path = "/home/eyeos/prueba2.pdf";
        $metadataIn = '{"config": {"cloud": "' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"}, "token": {"key": "ABCD", "secret": "EFGH"}, "metadata": {"type": "download", "id": "1234561", "path": "' . $path . '"}}';
        $metadataOut = 'false';
        $cloud = "Stacksync";
        $this->exerciseDownloadMetadata($metadataOut, $metadataIn, json_decode($this->exception), 1234561, $path, $cloud, $this->resourceUrl);
    }

    /**
     * method: downloadMetadata
     * when: called
     * with: tokenAndIdAndPath
     * should: returnPermissionDenied
     */
    public function test_downloadMetadata_called_tokenAndIdAndPath_returnPermisssionDenied()
    {
        $path = "/home/eyeos/prueba3.pdf";
        $metadataIn = '{"config": {"cloud": "' . $this->cloud . '"}, "token": {"key": "ABCD", "secret": "EFGH"}, "metadata": {"type": "download", "id": "1234561", "path": "' . $path . '"}}';
        $metadataOut = '403';
        $cloud = "Stacksync";
        $this->exerciseDownloadMetadata($metadataOut, $metadataIn, json_decode($this->permission), 1234561, $path, $cloud);
    }

    /**
     * method: downloadMetadata
     * when: called
     * with: tokenAndIdAndPathAndResourceUrl
     * should: returnPermissionDenied
     */
    public function test_downloadMetadata_called_tokenAndIdAndPathAndResourceUrl_returnPermisssionDenied()
    {
        $path = "/home/eyeos/prueba3.pdf";
        $metadataIn = '{"config": {"cloud": "' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"}, "token": {"key": "ABCD", "secret": "EFGH"}, "metadata": {"type": "download", "id": "1234561", "path": "' . $path . '"}}';
        $metadataOut = '403';
        $cloud = "Stacksync";
        $this->exerciseDownloadMetadata($metadataOut, $metadataIn, json_decode($this->permission), 1234561, $path, $cloud, $this->resourceUrl);
    }

    /**
     * method: deleteMetadata
     * when: called
     * with: tokenAndFileAndId
     * should: returnCorrectData
     */
    public function test_deleteMetadata_called_tokenAndFileAndId_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"delete","file":true,"id":"32565632156"}}';
        $metadataOut = '{"name":"Client1.pdf","path":"/documents/clients/Client1.pdf","id":"32565632156","size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent_id":"-348534824681","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"}';
        $this->exerciseDeleteMetadata($metadataIn,$metadataOut,$metadataOut,true,32565632156);
    }

    /**
     * method: deleteMetadata
     * when: called
     * with: tokenAndFileAndIdAndResourceUrl
     * should: returnCorrectData
     */
    public function test_deleteMetadata_called_tokenAndFileAndIdAndResourceUrl_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"delete","file":true,"id":"32565632156"}}';
        $metadataOut = '{"name":"Client1.pdf","path":"/documents/clients/Client1.pdf","id":"32565632156","size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent_id":"-348534824681","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"}';
        $this->exerciseDeleteMetadata($metadataIn,$metadataOut,$metadataOut,true,32565632156,$this->resourceUrl);
    }

    /**
     * method: deleteMetadata
     * when: called
     * with: tokenAndFolderAndId
     * should: returnCorrectData
     */
    public function test_deleteMetadata_called_tokenAndFolderAndId_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"delete","file":false,"id":"9873615"}}';
        $metadataOut = '{"name":"clients","path":"/documents/clients","id":"9873615","status":"DELETED","version":3,"parent_id":"-348534824681","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false}';
        $this->exerciseDeleteMetadata($metadataIn,$metadataOut,$metadataOut,false,9873615);
    }

    /**
     * method: deleteMetadata
     * when: called
     * with: tokenAndFolderAndIdAndResourceUrl
     * should: returnCorrectData
     */
    public function test_deleteMetadata_called_tokenAndFolderAndIdAndResourceUrl_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"delete","file":false,"id":"9873615"}}';
        $metadataOut = '{"name":"clients","path":"/documents/clients","id":"9873615","status":"DELETED","version":3,"parent_id":"-348534824681","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false}';
        $this->exerciseDeleteMetadata($metadataIn,$metadataOut,$metadataOut,false,9873615,$this->resourceUrl);
    }

    /**
     * method: deleteMetadata
     * when: called
     * with: tokenAndFolderAndId
     * should: returnException
     */
    public function test_deleteMetadata_called_tokenAndFolderAndId_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"delete","file":false,"id":"9873615"}}';
        $metadataOut = 'false';
        $this->exerciseDeleteMetadata($metadataIn,$metadataOut,$this->exception,false,9873615);
    }

    /**
     * method: deleteMetadata
     * when: called
     * with: tokenAndFolderAndIdAndResourceUrl
     * should: returnException
     */
    public function test_deleteMetadata_called_tokenAndFolderAndIdAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"delete","file":false,"id":"9873615"}}';
        $metadataOut = 'false';
        $this->exerciseDeleteMetadata($metadataIn,$metadataOut,$this->exception,false,9873615,$this->resourceUrl);
    }


    /**
     * method: deleteMetadata
     * when: called
     * with: tokenAndFolderAndId
     * should: returnPermissionDenied
     */
    public function test_deleteMetadata_called_tokenAndFolderAndId_returnPermissionDenied()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"delete","file":false,"id":"9873615"}}';
        $metadataOut = '403';
        $this->exerciseDeleteMetadata($metadataIn,$metadataOut,$this->permission,false,9873615);
    }

    /**
     * method: deleteMetadata
     * when: called
     * with: tokenAndFolderAndIdAndResourceUrl
     * should: returnPermissionDenied
     */
    public function test_deleteMetadata_called_tokenAndFolderAndIdAndResourceUrl_returnPermissionDenied()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"delete","file":false,"id":"9873615"}}';
        $metadataOut = '403';
        $this->exerciseDeleteMetadata($metadataIn,$metadataOut,$this->permission,false,9873615,$this->resourceUrl);
    }

    /**
     * method: listVersions
     * when: called
     * with: tokenAndId
     * should: returnCorrectData
     */
    public function test_listVersions_called_tokenAndId_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"listVersions","id":"153"}}';
        $metadataOut = '{"status": "CHANGED", "mimetype": "text/plain", "versions": [{"status": "CHANGED", "mimetype": "text/plain", "checksum": 2499810342, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 4, "is_folder": false, "chunks": [], "id": 155, "size": 61}, {"status": "RENAMED", "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 3, "is_folder": false, "chunks": [], "id": 155, "size": 59}, {"status": "RENAMED", "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 2, "is_folder": false, "chunks": [], "id": 155, "size": 59}, {"status": "NEW", "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 1, "is_folder": false, "chunks": [], "id": 155, "size": 59}], "checksum": 2499810342, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": "null", "version": 4, "is_folder": false, "chunks": [], "id": 155, "size": 61}';
        $check = '[{"status": "CHANGED", "mimetype": "text/plain", "checksum": 2499810342, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 4, "is_folder": false, "chunks": [], "id": 155, "size": 61}, {"status": "RENAMED", "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 3, "is_folder": false, "chunks": [], "id": 155, "size": 59}, {"status": "RENAMED", "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 2, "is_folder": false, "chunks": [], "id": 155, "size": 59}, {"status": "NEW", "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 1, "is_folder": false, "chunks": [], "id": 155, "size": 59}]';
        $this->exerciseListVersion($metadataIn,$metadataOut,$check,153);

    }

    /**
     * method: listVersions
     * when: called
     * with: tokenAndIdAndResourceUrl
     * should: returnCorrectData
     */
    public function test_listVersions_called_tokenAndIdAndResourceUrl_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"listVersions","id":"153"}}';
        $metadataOut = '{"status": "CHANGED", "mimetype": "text/plain", "versions": [{"status": "CHANGED", "mimetype": "text/plain", "checksum": 2499810342, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 4, "is_folder": false, "chunks": [], "id": 155, "size": 61}, {"status": "RENAMED", "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 3, "is_folder": false, "chunks": [], "id": 155, "size": 59}, {"status": "RENAMED", "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 2, "is_folder": false, "chunks": [], "id": 155, "size": 59}, {"status": "NEW", "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 1, "is_folder": false, "chunks": [], "id": 155, "size": 59}], "checksum": 2499810342, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": "null", "version": 4, "is_folder": false, "chunks": [], "id": 155, "size": 61}';
        $check = '[{"status": "CHANGED", "mimetype": "text/plain", "checksum": 2499810342, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 4, "is_folder": false, "chunks": [], "id": 155, "size": 61}, {"status": "RENAMED", "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 3, "is_folder": false, "chunks": [], "id": 155, "size": 59}, {"status": "RENAMED", "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 2, "is_folder": false, "chunks": [], "id": 155, "size": 59}, {"status": "NEW", "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 1, "is_folder": false, "chunks": [], "id": 155, "size": 59}]';
        $this->exerciseListVersion($metadataIn,$metadataOut,$check,153,$this->resourceUrl);
    }

    /**
     * method: listVersions
     * when: called
     * with: tokenAndId
     * should: returnPermissionDenied
     */
    public function test_listVersions_called_tokenAndId_returnPermissionDenied()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"listVersions","id":"9873615"}}';
        $metadataOut = '403';
        $this->exerciseListVersion($metadataIn,$metadataOut,$this->permission,9873615);
    }

    /**
     * method: listVersions
     * when: called
     * with: tokenAndIdAndResourceUrl
     * should: returnPermissionDenied
     */
    public function test_listVersions_called_tokenAndIdAndResourceurl_returnPermissionDenied()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"listVersions","id":"9873615"}}';
        $metadataOut = '403';
        $this->exerciseListVersion($metadataIn,$metadataOut,$this->permission,9873615,$this->resourceUrl);
    }

    /**
     * method: listVersions
     * when: called
     * with: tokenAndId
     * should: returnException
     */
    public function test_listVersions_called_tokenAndId_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"listVersions","id":"9873615"}}';
        $metadataOut = 'false';
        $this->exerciseListVersion($metadataIn,$metadataOut,$this->exception,9873615);
    }

    /**
     * method: listVersions
     * when: called
     * with: tokenAndIdAndResourceUrl
     * should: returnException
     */
    public function test_listVersions_called_tokenAndIdAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"listVersions","id":"9873615"}}';
        $metadataOut = 'false';
        $this->exerciseListVersion($metadataIn,$metadataOut,$this->exception,9873615,$this->resourceUrl);
    }

    /**
     * method: getFileVersionData
     * when: called
     * with: tokenAndIdAndVersionAndPath
     * should: returnCorrectDownloadVersion
     */
    public function test_getFileVersionData_called_tokenAndIdAndVersionAndPath_returnCorrectDownloadVersion()
    {
        $metadataOut = "true";
        $this->exerciseGetFileVersionData($metadataOut, '{"status":true}', $this->cloud);
    }

    /**
     * method: getFileVersionData
     * when: called
     * with: tokenAndIdAndVersionAndPathAndResourceUrl
     * should: returnCorrectDownloadVersion
     */
    public function test_getFileVersionData_called_tokenAndIdAndVersionAndPathAndResourceUrl_returnCorrectDownloadVersion()
    {
        $metadataOut = "true";
        $this->exerciseGetFileVersionData($metadataOut, '{"status":true}', $this->cloud,$this->resourceUrl);
    }

    /**
     * method: getFileVersionData
     * when: called:
     * with: tokenAndIdAndVersionAndPath
     * should: returnPermissionDenied
     */
    public function test_getFileVersionData_called_tokenAndIdAndVersionAndPath_returnPermissionDenied()
    {
        $metadataOut = "403";
        $this->exerciseGetFileVersionData($metadataOut, $this->permission, $this->cloud);
    }

    /**
     * method: getFileVersionData
     * when: called:
     * with: tokenAndIdAndVersionAndPathAndResourceUrl
     * should: returnPermissionDenied
     */
    public function test_getFileVersionData_called_tokenAndIdAndVersionAndPathAndResourceUrl_returnPermissionDenied()
    {
        $metadataOut = "403";
        $this->exerciseGetFileVersionData($metadataOut, $this->permission, $this->cloud, $this->resourceUrl);
    }

    /**
     * method: getFileVersionData
     * when: called:
     * with: tokenAndIdAndVersionAndPath
     * should: returnException
     */
    public function test_getFileVersionData_called_tokenAndIdAndVersionAndPath_returnException()
    {
        $metadataOut = "false";
        $this->exerciseGetFileVersionData($metadataOut, $this->exception, $this->cloud);
    }

    /**
     * method: getFileVersionData
     * when: called:
     * with: tokenAndIdAndVersionAndPathAndResourceUrl
     * should: returnException
     */
    public function test_getFileVersionData_called_tokenAndIdAndVersionAndPathAndResourceUrl_returnException()
    {
        $metadataOut = "false";
        $this->exerciseGetFileVersionData($metadataOut, $this->exception, $this->cloud, $this->resourceUrl);
    }

    /**
     * method: getListUsersShare
     * when: called
     * with: tokenAndId
     * should: returnCorrectData
     */
    public function test_getListUsersShare_called_tokenAndId_returnCorrectData()
    {
        $id = 153;
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"listUsersShare","id":"153"}}';
        $metadataOut = '[{"joined_at": "2014-05-27", "is_owner": true, "name": "tester1", "email": "tester1@test.com"}]';
        $this->exerciseListUsersShare($metadataIn,$metadataOut,$metadataOut,$id);
    }

    /**
     * method: getListUsersShare
     * when: called
     * with: tokenAndIdAndResourceUrl
     * should: returnCorrectData
     */
    public function test_getListUsersShare_called_tokenAndIdAndResourceUrl_returnCorrectData()
    {
        $id = 153;
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"listUsersShare","id":"153"}}';
        $metadataOut = '[{"joined_at": "2014-05-27", "is_owner": true, "name": "tester1", "email": "tester1@test.com"}]';
        $this->exerciseListUsersShare($metadataIn,$metadataOut,$metadataOut,$id,$this->resourceUrl);
    }

    /**
     * method: getListUsersShare
     * when: called
     * with: tokenAndId
     * should: returnPermissionDenied
     */
    public function test_getListUsersShare_called_tokenAndId_returnPermissionDenied()
    {
        $id = 153;
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"listUsersShare","id":"153"}}';
        $metadataOut = '403';
        $this->exerciseListUsersShare($metadataIn,$metadataOut,$this->permission,$id);
    }

    /**
     * method: getListUsersShare
     * when: called
     * with: tokenAndIdAndResourceUrl
     * should: returnPermissionDenied
     */
    public function test_getListUsersShare_called_tokenAndIdAndResourceUrl_returnPermissionDenied()
    {
        $id = 153;
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"listUsersShare","id":"153"}}';
        $metadataOut = '403';
        $this->exerciseListUsersShare($metadataIn,$metadataOut,$this->permission,$id,$this->resourceUrl);
    }

    /**
     * method: getListUsersShare
     * when: called
     * with: tokenAndId
     * should: returnException
     */
    public function test_getListUsersShare_called_tokenAndId_returnException()
    {
        $id = 153;
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"listUsersShare","id":"153"}}';
        $metadataOut = 'false';
        $this->exerciseListUsersShare($metadataIn,$metadataOut,$this->exception,$id);
    }

    /**
     * method: getListUsersShare
     * when: called
     * with: tokenAndIdAndResourceUrl
     * should: returnException
     */
    public function test_getListUsersShare_called_tokenAndIdAndResourceUrl_returnException()
    {
        $id = 153;
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"listUsersShare","id":"153"}}';
        $metadataOut = 'false';
        $this->exerciseListUsersShare($metadataIn,$metadataOut,$this->exception,$id,$this->resourceUrl);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndList
     * should: returnCorrect
     */
    public function test_shareFolder_called_tokenAndIdAndList_returnCorrect()
    {
        $id = 153;
        $list = array("a@a.com","b@b.com");
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"shareFolder","id":"153","list":["a@a.com","b@b.com"],"shared":false}}';
        $metadataOut = 'true';
        $shared = false;
        $this->exerciseShareFolder($metadataIn, $metadataOut, '{"status":true}', $id, $list, $shared);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndListAndShared
     * should: returnCorrect
     */
    public function test_shareFolder_called_tokenAndIdAndListAndShared_returnCorrect()
    {
        $id = 153;
        $list = array("a@a.com","b@b.com");
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"shareFolder","id":"153","list":["a@a.com","b@b.com"],"shared":true}}';
        $metadataOut = 'true';
        $shared = true;
        $this->exerciseShareFolder($metadataIn, $metadataOut, '{"status":true}', $id, $list, $shared);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndListAndResourceUrl
     * should: returnCorrect
     */
    public function test_shareFolder_called_tokenAndIdAndListAndResourceUrl_returnCorrect()
    {
        $id = 153;
        $list = array("a@a.com","b@b.com");
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"shareFolder","id":"153","list":["a@a.com","b@b.com"],"shared":false}}';
        $metadataOut = 'true';
        $shared = false;
        $this->exerciseShareFolder($metadataIn, $metadataOut, '{"status":true}', $id, $list, $shared, $this->resourceUrl);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndListAndSharedAndResourceUrl
     * should: returnCorrect
     */
    public function test_shareFolder_called_tokenAndIdAndListAndSharedAndResourceUrl_returnCorrect()
    {
        $id = 153;
        $list = array("a@a.com","b@b.com");
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"shareFolder","id":"153","list":["a@a.com","b@b.com"],"shared":true}}';
        $metadataOut = 'true';
        $shared = true;
        $this->exerciseShareFolder($metadataIn, $metadataOut, '{"status":true}', $id, $list, $shared, $this->resourceUrl);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndList
     * should: returnPermissionDenied
     */
    public function test_shareFolder_called_tokenAndIdAndList_returnPermissionDenied()
    {
        $id = 153;
        $list = array("a@a.com","b@b.com");
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"shareFolder","id":"153","list":["a@a.com","b@b.com"],"shared":false}}';
        $metadataOut = '403';
        $shared = false;
        $this->exerciseShareFolder($metadataIn, $metadataOut, $this->permission, $id, $list, $shared);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndListAndShared
     * should: returnPermissionDenied
     */
    public function test_shareFolder_called_tokenAndIdAndListAndShared_returnPermissionDenied()
    {
        $id = 153;
        $list = array("a@a.com","b@b.com");
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"shareFolder","id":"153","list":["a@a.com","b@b.com"],"shared":true}}';
        $metadataOut = '403';
        $shared = true;
        $this->exerciseShareFolder($metadataIn, $metadataOut, $this->permission, $id, $list, $shared);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndListAndResourceUrl
     * should: returnPermissionDenied
     */
    public function test_shareFolder_called_tokenAndIdAndListAndResourceUrl_returnPermissionDenied()
    {
        $id = 153;
        $list = array("a@a.com","b@b.com");
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"shareFolder","id":"153","list":["a@a.com","b@b.com"],"shared":false}}';
        $metadataOut = '403';
        $shared = false;
        $this->exerciseShareFolder($metadataIn, $metadataOut, $this->permission, $id, $list, $shared, $this->resourceUrl);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndListAndSharedAndResourceUrl
     * should: returnPermissionDenied
     */
    public function test_shareFolder_called_tokenAndIdAndListAndSharedAndResourceUrl_returnPermissionDenied()
    {
        $id = 153;
        $list = array("a@a.com","b@b.com");
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"shareFolder","id":"153","list":["a@a.com","b@b.com"],"shared":true}}';
        $metadataOut = '403';
        $shared = true;
        $this->exerciseShareFolder($metadataIn, $metadataOut, $this->permission, $id, $list, $shared, $this->resourceUrl);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndList
     * should: returnException
     */
    public function test_shareFolder_called_tokenAndIdAndList_returnException()
    {
        $id = 153;
        $list = array("a@a.com","b@b.com");
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"shareFolder","id":"153","list":["a@a.com","b@b.com"],"shared":false}}';
        $metadataOut = 'false';
        $shared = false;
        $this->exerciseShareFolder($metadataIn, $metadataOut, $this->exception, $id, $list, $shared);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndListAndShared
     * should: returnException
     */
    public function test_shareFolder_called_tokenAndIdAndListAndShared_returnException()
    {
        $id = 153;
        $list = array("a@a.com","b@b.com");
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"shareFolder","id":"153","list":["a@a.com","b@b.com"],"shared":true}}';
        $metadataOut = 'false';
        $shared = true;
        $this->exerciseShareFolder($metadataIn, $metadataOut, $this->exception, $id, $list, $shared);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndListAndResourceUrl
     * should: returnException
     */
    public function test_shareFolder_called_tokenAndIdAndListAndResourceUrl_returnException()
    {
        $id = 153;
        $list = array("a@a.com","b@b.com");
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"shareFolder","id":"153","list":["a@a.com","b@b.com"],"shared":false}}';
        $metadataOut = 'false';
        $shared = false;
        $this->exerciseShareFolder($metadataIn, $metadataOut, $this->exception, $id, $list, $shared, $this->resourceUrl);
    }

    /**
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndListAndSharedAndResourceUrl
     * should: returnException
     */
    public function test_shareFolder_called_tokenAndIdAndListAndAndSharedAndResourceUrl_returnException()
    {
        $id = 153;
        $list = array("a@a.com","b@b.com");
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"shareFolder","id":"153","list":["a@a.com","b@b.com"],"shared":true}}';
        $metadataOut = 'false';
        $shared = true;
        $this->exerciseShareFolder($metadataIn, $metadataOut, $this->exception, $id, $list, $shared, $this->resourceUrl);
    }

    /**
     * method: getCloudsList
     * when: called
     * with: emptyParams
     * should: returnList
     */
    public function test_getCloudsList_called_emptyParams_returnList()
    {
        $metadataIn = '{"config":{"type":"cloudsList"}}';
        $metadataOut = '["Stacksync", "Nec"]';
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->getCloudsList();
        $this->assertEquals(json_decode($metadataOut), $actual);
    }

    /**
     * method: getOauthUrlCloud
     * when: called
     * with: ValidCloud
     * should: returnList
     */
    public function test_getOauthUrlCloud_called_ValidCloud_returnList()
    {
        $metadataIn = '{"config":{"type":"oauthUrl","cloud":"Stacksync"}}';
        $metadataOut = 'http://api.stacksync.com:8080/oauth/authorize?oauth_token=';
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->getOauthUrlCloud("Stacksync");
        $this->assertEquals(json_decode($metadataOut), $actual);
    }

    /**
     * method: getControlVersionCloud
     * when: called
     * with: ValidCloud
     * should: returnList
     */
    public function test_getControlVersionCloud_called_Valid_Cloud_returnList()
    {
        $metadataIn = '{"config":{"type":"controlVersion","cloud":"Stacksync"}}';
        $metadataOut = '{"controlVersion":"true"}';
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->getControlVersionCloud("Stacksync");
        $this->assertEquals(json_decode($metadataOut),$actual);
    }

    /**
     * method: insertComment
     * when: called
     * with: cloudAndTokenAndIdAndUserAndTextAndResourceUrl
     * should: returnMetadataInsert
     */
    public function test_insertComment_called_cloudAndTokenAndIdAndUserAndTextAndResourceUrl_returnMetadataInsert()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"insertComment","id":"153","user":"eyeos","text":"prueba"}}';
        $metadataOut = '{"id":"153","user":"eyeos","text":"prueba","cloud":"stacksync","status":"NEW","time_created":"201406201548"}';
        $this->exerciseInsertComment($metadataIn,$metadataOut);
    }

    /**
     * method: insertComment
     * when: called
     * with: cloudAndTokenAndIdAndUserAndTextAndResourceUrl
     * should: returnException
     */
    public function test_insertComment_called_cloudAndTokenAndIdAndUserAndTextAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"insertComment","id":"153","user":"eyeos","text":"prueba"}}';
        $metadataOut = 400;
        $this->exerciseInsertComment($metadataIn,$metadataOut);
    }


    /**
     * method: deleteComment
     * when: called
     * with: cloudAndTokenAndIdAndUserAndTimeCreatedAndResourceUrl
     * should: returnMetadataDelete
     */
    public function test_deleteComment_called_cloudAndTokenAndIdAndUserAndTimeCreatedAndResourceUrl_returnMetadataDelete()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteComment","id":"153","user":"eyeos","time_created":"201406201548"}}';
        $metadataOut = '{"id":"153","user":"eyeos","text":"prueba","cloud":"stacksync","status":"DELETED","time_created":"201406201548"}';
        $this->exerciseDeleteComment($metadataIn,$metadataOut);
    }

    /**
     * method: deleteComment
     * when: called
     * with: cloudAndTokenAndIdAndUserAndTimeCreatedAndResourceUrl
     * should: returnException
     */
    public function test_deleteComment_called_cloudAndTokenAndIdAndUserAndTimeCreatedAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteComment","id":"153","user":"eyeos","time_created":"201406201548"}}';
        $metadataOut = 400;
        $this->exerciseDeleteComment($metadataIn,$metadataOut);
    }

    /**
     * method: getComments
     * when: called
     * with: cloudAndTokenAndIdAndResourceUrl
     * should: returnListMetadata
     */
    public function test_getComments_called_cloudAndTokenAndIdAndResourceUrl_returnListMetadata()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getComments","id":"153"}}';
        $metadataOut = '[{"id":"153","user":"eyeos","text":"prueba","cloud":"stacksync","status":"NEW","time_created":"201406201548"}]';
        $this->exerciseGetComments($metadataIn,$metadataOut);

    }

    /**
     * method: getComments
     * when: called
     * with: cloudAndTokenAndIdAndResourceUrl
     * should: returnException
     */
    public function test_getComments_called_cloudAndTokenAndIdAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getComments","id":"153"}}';
        $metadataOut = 400;
        $this->exerciseGetComments($metadataIn,$metadataOut);
    }

    /**
     * method: getControlCommentsCloud
     * when: called
     * with: ValidCloud
     * should: returnMetadata
     */
    public function test_getControlCommentsCloud_called_Valid_Cloud_returnList()
    {
        $metadataIn = '{"config":{"type":"comments","cloud":"Stacksync"}}';
        $metadataOut = '{"comments":"true"}';
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->getControlCommentsCloud("Stacksync");
        $this->assertEquals(json_decode($metadataOut),$actual);
    }

    private function exerciseGetMetadata($metadataIn,$metadataOut,$check,$file,$id,$contents = null,$url = null)
    {
        $this->exerciseMockMetadata($metadataIn,$metadataOut);
        $result = $this->sut->getMetadata($this->cloud,$this->token,$file,$id,$contents,$url);
        $this->assertEquals(json_decode($check),$result);
    }

    private function exerciseUpdateMetadata($metadataIn, $metadataOut, $check, $file, $id, $name = null, $parent = null, $cloud = null,$resourceUrl = null)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $result = $this->sut->updateMetadata($cloud, $this->token, $file, $id, $name, $parent,$resourceUrl);
        $this->assertEquals(json_decode($check), $result);
    }

    private function exerciseCreateMetadata($metadataIn,$metadataOut,$check,$file,$name,$parent = null,$pathAbsolute = null,$resourceUrl = null)
    {
        $this->exerciseMockMetadata($metadataIn,$metadataOut);
        $result = $this->sut->createMetadata($this->cloud,$this->token,$file,$name,$parent,$pathAbsolute,$resourceUrl);
        $this->assertEquals(json_decode($check),$result);
    }

    private function exerciseUploadMetadata($metadataIn, $metadataOut, $check, $id, $path,$resourceUrl = null)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $result = $this->sut->uploadMetadata($this->cloud, $this->token, $id, $path,$resourceUrl);
        $this->assertEquals(json_decode($check), $result);
    }

    private function exerciseDownloadMetadata($metadataOut, $metadataIn,$check, $id, $path, $cloud, $resourceUrl = null)
    {
        $metadataIn = json_decode($metadataIn);
        $metadataIn = json_encode($metadataIn);
        $this->exerciseMockMetadata($metadataIn,$metadataOut);
        $result = $this->sut->downloadMetadata($cloud, $this->token, $id, $path, $resourceUrl);
        $this->assertEquals($check, $result);
    }

    private function exerciseDeleteMetadata($metadataIn,$metadataOut,$check,$file,$id, $resourceUrl = null)
    {
        $this->exerciseMockMetadata($metadataIn,$metadataOut);
        $result = $this->sut->deleteMetadata($this->cloud,$this->token,$file,$id,$resourceUrl);
        $this->assertEquals(json_decode($check),$result);
    }

    private function exerciseMockMetadata($metadataIn,$metadataOut)
    {
        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessOauthCredentials')
            ->with($metadataIn)
            ->will($this->returnValue($metadataOut));
    }

    private function exerciseListVersion($metadataIn,$metadataOut,$check,$id,$resourceUrl=null)
    {
        $this->exerciseMockMetadata($metadataIn,$metadataOut);
        $result = $this->sut->listVersions($this->cloud,$this->token,$id,$resourceUrl);
        $this->assertEquals(json_decode($check),$result);
    }

    private function exerciseListUsersShare($metadataIn, $metadataOut, $check, $id, $resourceUrl = null)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $result = $this->sut->getListUsersShare($this->cloud, $this->token, $id, $resourceUrl);
        $this->assertEquals(json_decode($check), $result);
    }

    private function exerciseShareFolder($metadataIn, $metadataOut, $check, $id, $list, $shared, $resourceUrl = null)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $result = $this->sut->shareFolder($this->cloud, $this->token, $id, $list, $shared, $resourceUrl);
        $this->assertEquals(json_decode($check), $result);
    }

    private function exerciseGetFileVersionData($metadataOut, $check, $cloud=NULL,$resourceUrl = NULL)
    {
        $metadataIn = new stdClass();
        $metadataIn->config = new stdClass();
        if ($cloud) {
            $metadataIn->config->cloud = $cloud;
        }

        if($resourceUrl) {
            $metadataIn->config->resource_url = $resourceUrl;
        }

        $metadataIn->token = new stdClass();
        $metadataIn->token->key = "ABCD";
        $metadataIn->token->secret = "EFGH";
        $metadataIn->metadata = new stdClass();
        $metadataIn->metadata->type = "getFileVersion";
        $metadataIn->metadata->id = "9873615";
        $metadataIn->metadata->version = "2";
        $metadataIn->metadata->path = "/home/eyeos/prueba3.pdf";
        $this->exerciseMockMetadata(json_encode($metadataIn), $metadataOut);
        $result = $this->sut->getFileVersionData($cloud, $this->token, "9873615", 2, "/home/eyeos/prueba3.pdf",$resourceUrl);
        $this->assertEquals(json_decode($check),$result);
    }

    private function exerciseInsertComment($metadataIn,$metadataOut)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->insertComment($this->cloud,$this->token,"153","eyeos","prueba","http://192.68.56.101/");
        $this->assertEquals(json_decode($metadataOut),$actual);
    }

    private function exerciseDeleteComment($metadataIn,$metadataOut)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->deleteComment($this->cloud,$this->token,"153","eyeos","201406201548","http://192.68.56.101/");
        $this->assertEquals(json_decode($metadataOut),$actual);
    }

    private function exerciseGetComments($metadataIn,$metadataOut)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->getComments($this->cloud,$this->token,"153","http://192.68.56.101/");
        $this->assertEquals(json_decode($metadataOut),$actual);
    }
}
?>