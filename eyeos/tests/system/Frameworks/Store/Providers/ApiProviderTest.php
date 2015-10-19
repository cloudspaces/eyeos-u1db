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
    private $urlAPISync;
    private $consumerKey;
    private $consumerSecret;

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
        $this->urlAPISync = "http://192.68.56.101/";
        $this->consumerKey = "b3af";
        $this->consumerSecret = "c168";
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
     * with: tokenAndFileAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnCorrectData
     */
    public function test_getMetadata_called_tokenAndFileAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"get","file":true,"id":"123456","contents":null}}';
        $metadataOut = '{"name":"Client1.pdf","path":"/documents/clients/Client1.pdf","id":"32565632156","size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent_id":"-348534824681","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"}';
        $this->exerciseGetMetadata($metadataIn,$metadataOut,$metadataOut,true,123456,null,$this->resourceUrl, $this->consumerKey,$this->consumerSecret);
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
     * with: tokenAndFolderAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnCorrectData
     */
    public function test_getMetadata_called_tokenAndFolderAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"get","file":false,"id":"9873615","contents":null}}';
        $metadataOut = '{"name":"clients","path":"/documents/clients","id":"9873615","status":"NEW","version":1,"parent_id":"-348534824681","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false}';
        $this->exerciseGetMetadata($metadataIn,$metadataOut,$metadataOut,false,9873615,null,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * with: tokenAndFolderAndIdAndContentsAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnCorrectData
     */
    public function test_getMetadata_called_tokenAndFolderAndIdAndContentsAndResourceUrlAndConsumerKeyAndConsumerSecret_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"get","file":false,"id":"9873615","contents":true}}';
        $metadataOut = '{"name":"clients","path":"/documents/clients","id":"9873615","status":"NEW","version":1,"parent_id":"-348534824681","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false,"contents":[{"name":"Client1.pdf","path":"/documents/clients/Client1.pdf","id":32565632156,"size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent":-348534824681,"user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false}]}';
        $this->exerciseGetMetadata($metadataIn,$metadataOut,$metadataOut,false,9873615,true,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * with: tokenAndFolderAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_getMetadata_called_tokenAndFolderAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"get","file":false,"id":"9873615","contents":null}}';
        $metadataOut = 'false';
        $this->exerciseGetMetadata($metadataIn,$metadataOut,$this->exception,false,9873615,null,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * method: getMetadata
     * when: called
     * with: tokenAndFolderAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnPermissionDenied
     */
    public function test_getMetadata_called_tokenAndFolderAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret_returnPermissionDenied()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"get","file":false,"id":"9873615","contents":null}}';
        $metadataOut = '403';
        $this->exerciseGetMetadata($metadataIn,$metadataOut,$this->permission,false,9873615,null,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * with: cloudAndTokenAndFileAndIdAndNameAndParentAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnMetadataRename
     */
    public function test_updateMetadata_called_cloudAndTokenAndFileAndIdAndNameAndParentAndResourceAndConsumerKeyAndConsumerSecret_returnMetadataRename()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":true,"id":"32565632156","filename":"Winter2012_renamed.jpg","parent_id":"12386548974"}}';
        $metadataOut = '{"name": "Winter2012_renamed.jpg", "path": "/documents/clients/", "id": "32565632156", "size": 775412, "mimetype": "application/pdf", "status": "RENAMED", "version": 2, "parent": "12386548974", "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997"}';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $metadataOut, true, 32565632156, "Winter2012_renamed.jpg", 12386548974, $this->cloud,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * with: cloudAndTokenAndFileAndIdAndParentAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnMetadataMove
     */
    public function test_updateMetadata_called_cloudAndTokenAndFileAndIdAndParentAndResourceUrlAndConsumerKeyAndConsumerSecret_returnMetadataMove()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":true,"id":"32565632156","filename":null,"parent_id":"123456"}}';
        $metadataOut = '{"name": "Winter2012_renamed.jpg", "path": "/documents/clients/", "id": "32565632156", "size": 775412, "mimetype": "application/pdf", "status": "CHANGED", "version": 2, "parent": "123456", "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997"}';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $metadataOut, true, 32565632156, null, 123456, $this->cloud,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * with: cloudAndTokenAndFileAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnMetadataMove
     */
    public function test_updateMetadata_called_cloudAndTokenAndFileAndIdAndResourceUrlAndConsumerSecret_returnMetadataMove()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":true,"id":"32565632156","filename":null,"parent_id":"null"}}';
        $metadataOut = '{"name": "Winter2012_renamed.jpg", "path": "/documents/clients/", "id": "32565632156", "size": 775412, "mimetype": "application/pdf", "status": "CHANGED", "version": 2, "parent_id": "null", "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997"}';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $metadataOut, true, 32565632156, null, null, $this->cloud,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * with: cloudAndTokenAndFolderAndIdAndNameAndParentAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnMetadataRename
     */
    public function test_updateMeta_called_cloudAndTokenAndFolderAndIdAndNameAndParentAndResourceUrlAndConsumerKeyAndConsumerSecret_returnMetadataRename()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":false,"id":"32565632156","filename":"Winter2012_renamed","parent_id":"12386548974"}}';
        $metadataOut = '{"name": "Winter2012_renamed", "path": "/documents/clients/", "id": "32565632156", "status": "CHANGED", "version": 2, "parent": "12386548974", "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997", "is_root": false}';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $metadataOut, false, 32565632156, "Winter2012_renamed", 12386548974, $this->cloud,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);

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
     * with: cloudAndTokenAndFolderAndIdAndParentAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnMetadataMove
     */
    public function test_updateMetadata_called_cloudAndTokenAndFolderAndIdAndParentAndResourceUrlAndConsumerKeyAndConsumerSecret_returnMetadataMove()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":false,"id":"32565632156","filename":null,"parent_id":"123456"}}';
        $metadataOut = '{"name": "Winter2012_renamed", "path": "/documents/clients/", "id": "32565632156", "status": "CHANGED", "version": 2, "parent": "123456", "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997", "is_root": false}';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $metadataOut, false, 32565632156, null, 123456, $this->cloud,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * with: cloudAndTokenAndFolderAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnMetadataMove
     */
    public function test_updateMetadata_called_cloudAndTokenAndFolderAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret_returnMetadataMove()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":false,"id":"32565632156","filename":null,"parent_id":"null"}}';
        $metadataOut = '{"name": "Winter2012_renamed", "path": "/documents/clients/", "id": "32565632156", "status": "CHANGED", "version": 2, "parent_id": "null", "user": "eyeos", "client_modified": "2013-03-08 10:36:41.997", "server_modified": "2013-03-08 10:36:41.997", "is_root": false}';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $metadataOut, false, 32565632156, null, null, $this->cloud,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);

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
     * with: cloudAndTokenAndFolderAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_updateMetadata_called_cloudAndTokenAndFolderAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":false,"id":"32565632156","filename":null,"parent_id":"null"}}';
        $metadataOut = 'false';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $this->exception, false, 32565632156, null, null, $this->cloud,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * method: updateMetadata
     * when: called
     * with: cloudAndTokenAndFolderAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnPermissionDenied()
     */
    public function test_updateMetadata_called_cloudAndTokenAndFolderAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret_returnPermissionDenied()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"update","file":false,"id":"32565632156","filename":null,"parent_id":"null"}}';
        $metadataOut = '403';
        $this->exerciseUpdateMetadata($metadataIn, $metadataOut, $this->permission, false, 32565632156, null, null, $this->cloud,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * with: tokenAndFileAndNameAndParentAndPathAbsoluteAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnCorrectData
     */
    public function test_createMetadata_called_tokenAndFileAndNameAndParentAndPathAbsoluteAndResourceUrlAndConsumerKeyAndConsumerSecret_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"create","file":true,"filename":"Client1.pdf","parent_id":"-348534824681","path":"\/home\/eyeos\/Client1.pdf"}}';
        $metadataOut = '{"filename":"Client1.pdf","id":"32565632156","parent_id":"-348534824681","user":"eyeos"}';
        $pathAbsolute = '/home/eyeos/Client1.pdf';
        $this->exerciseCreateMetadata($metadataIn,$metadataOut,$metadataOut,true,'Client1.pdf',-348534824681,$pathAbsolute,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * with: tokenAndFileAndNameAndPathAbsoluteAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnCorrectData
     */
    public function test_createMetadata_called_tokenAndFileAndNameAndPathAbsoluteAndResourceUrlAndConsumerKeyAndConsumerSecret_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"create","file":true,"filename":"Client1.pdf","parent_id":"null","path":"\/home\/eyeos\/Client1.pdf"}}';
        $metadataOut = '{"filename":"Client1.pdf","id":"32565632156","parent_id":"null","user":"eyeos"}';
        $pathAbsolute = '/home/eyeos/Client1.pdf';
        $this->exerciseCreateMetadata($metadataIn,$metadataOut,$metadataOut,true,'Client1.pdf',null,$pathAbsolute,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);

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
     * with: tokenAndFolderAndNameAndParentAndPathAbsoluteAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnCorrectData
     */
    public function test_createMetadata_called_tokenAndFolderAndNameAndParentAndPathAbsoluteAndResourceUrlAndConsumerKeyAndConsumerSecret_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"create","file":false,"filename":"clients","parent_id":"-348534824681","path":null}}';
        $metadataOut = '{"filename":"clients","id":"9873615","parent_id":"-348534824681","user":"eyeos","is_root":false}';
        $this->exerciseCreateMetadata($metadataIn,$metadataOut,$metadataOut,false,"clients",-348534824681,null,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);

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
     * with: tokenAndFolderAndNameAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnCorrectData
     */
    public function test_createMetadata_called_tokenAndFolderAndNameAndResourceUrlAndConsumerKeyAndConsumerSecret_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"create","file":false,"filename":"clients","parent_id":"null","path":null}}';
        $metadataOut = '{"filename":"clients","id":"9873615","parent_id":"null","user":"eyeos","is_root":false}';
        $this->exerciseCreateMetadata($metadataIn,$metadataOut,$metadataOut,false,"clients",null,null,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * with: tokenAndFolderAndNameAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_createMetadata_called_tokenAndFolderAndNameAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"create","file":false,"filename":"clients","parent_id":"null","path":null}}';
        $metadataOut = 'false';
        $this->exerciseCreateMetadata($metadataIn,$metadataOut,$this->exception,false,"clients",null,null,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * method: createMetadata
     * when: called
     * with: tokenAndFolderAndNameAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnPermissionDenied
     */
    public function test_createMetadata_called_tokenAndFolderAndNameAndResourceUrlAndConsumerKeyAndConsumerSecret_returnPermissionDenied()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"create","file":false,"filename":"clients","parent_id":"null","path":null}}';
        $metadataOut = '403';
        $this->exerciseCreateMetadata($metadataIn,$metadataOut,$this->permission,false,"clients",null,null,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * with: tokenAndIdAndPathAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnCorrect
     */
    public function test_uploadMetadata_called_tokenAndIdAndPathAndResourceUrlAndConsumerKeyAndConsumerSecret_returnCorrect()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"upload","id":"1234561","path":"\/var\/www\/eyeos\/client.pdf"}}';
        $metadataOut = '{"status":true}';
        $this->exerciseUploadMetadata($metadataIn, $metadataOut, $metadataOut, 1234561, "/var/www/eyeos/client.pdf",$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * with: tokenAndIdAndPathAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_uploadMetadata_called_tokenAndIdAndPathAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"upload","id":"1234561","path":"\/var\/www\/eyeos\/client.pdf"}}';
        $metadataOut = 'false';
        $this->exerciseUploadMetadata($metadataIn, $metadataOut, $this->exception, 1234561, "/var/www/eyeos/client.pdf",$this->resourceUrl, $this->consumerKey, $this->consumerSecret);
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
     * method: uploadMetadata
     * when: called
     * with: tokenAndIdAndPathAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnPermissionDenied
     */
    public function test_uploadMetadata_called_tokenAndIdAndPathAndResourceUrlAndConsumerKeyAndConsumerSecret_returnPermissionDenied()
    {
        $metadataIn = '{"config":{"cloud":"' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"upload","id":"1234561","path":"\/var\/www\/eyeos\/client.pdf"}}';
        $metadataOut = '403';
        $this->exerciseUploadMetadata($metadataIn, $metadataOut, $this->permission, 1234561, "/var/www/eyeos/client.pdf",$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * with: tokenAndIdAndPathAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnCorrectDonwloadFile
     */
    public function test_downloadMetadata_called_tokenAndIdAndPathAndResourceUrlAndConsumerKeyAndConsumerSecret_returnCorrectDownloadFile()
    {
        $path = "/home/eyeos/prueba1.pdf";
        $metadataIn = '{"config": {"cloud": "' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"}, "token": {"key": "ABCD", "secret": "EFGH"}, "metadata": {"type": "download", "id": "1234561", "path": "' . $path . '"}}';
        $metadataOut = 'true';
        $this->exerciseDownloadMetadata($metadataOut, $metadataIn, $metadataOut, 1234561, $path, $this->cloud, $this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * with: tokenAndIdAndPathAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_downloadMetadata_called_tokenAndIdAndPathAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $path = "/home/eyeos/prueba2.pdf";
        $metadataIn = '{"config": {"cloud": "' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"}, "token": {"key": "ABCD", "secret": "EFGH"}, "metadata": {"type": "download", "id": "1234561", "path": "' . $path . '"}}';
        $metadataOut = 'false';
        $cloud = "Stacksync";
        $this->exerciseDownloadMetadata($metadataOut, $metadataIn, json_decode($this->exception), 1234561, $path, $cloud, $this->resourceUrl, $this->consumerKey,$this->consumerSecret);
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
     * method: downloadMetadata
     * when: called
     * with: tokenAndIdAndPathAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnPermissionDenied
     */
    public function test_downloadMetadata_called_tokenAndIdAndPathAndResourceUrlAndConsumerKeyAndConsumerSecret_returnPermisssionDenied()
    {
        $path = "/home/eyeos/prueba3.pdf";
        $metadataIn = '{"config": {"cloud": "' . $this->cloud . '","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"}, "token": {"key": "ABCD", "secret": "EFGH"}, "metadata": {"type": "download", "id": "1234561", "path": "' . $path . '"}}';
        $metadataOut = '403';
        $cloud = "Stacksync";
        $this->exerciseDownloadMetadata($metadataOut, $metadataIn, json_decode($this->permission), 1234561, $path, $cloud, $this->resourceUrl, $this->consumerKey, $this->consumerSecret);
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
     * with: tokenAndFileAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnCorrectData
     */
    public function test_deleteMetadata_called_tokenAndFileAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"delete","file":true,"id":"32565632156"}}';
        $metadataOut = '{"name":"Client1.pdf","path":"/documents/clients/Client1.pdf","id":"32565632156","size":775412,"mimetype":"application/pdf","status":"DELETED","version":3,"parent_id":"-348534824681","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997"}';
        $this->exerciseDeleteMetadata($metadataIn,$metadataOut,$metadataOut,true,32565632156,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * with: tokenAndFolderAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnCorrectData
     */
    public function test_deleteMetadata_called_tokenAndFolderAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"delete","file":false,"id":"9873615"}}';
        $metadataOut = '{"name":"clients","path":"/documents/clients","id":"9873615","status":"DELETED","version":3,"parent_id":"-348534824681","user":"eyeos","client_modified":"2013-03-08 10:36:41.997","server_modified":"2013-03-08 10:36:41.997","is_root":false}';
        $this->exerciseDeleteMetadata($metadataIn,$metadataOut,$metadataOut,false,9873615,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * with: tokenAndFolderAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_deleteMetadata_called_tokenAndFolderAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"delete","file":false,"id":"9873615"}}';
        $metadataOut = 'false';
        $this->exerciseDeleteMetadata($metadataIn,$metadataOut,$this->exception,false,9873615,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * method: deleteMetadata
     * when: called
     * with: tokenAndFolderAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnPermissionDenied
     */
    public function test_deleteMetadata_called_tokenAndFolderAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret_returnPermissionDenied()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"delete","file":false,"id":"9873615"}}';
        $metadataOut = '403';
        $this->exerciseDeleteMetadata($metadataIn,$metadataOut,$this->permission,false,9873615,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * with: tokenAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnCorrectData
     */
    public function test_listVersions_called_tokenAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret_returnCorrectData()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"listVersions","id":"153"}}';
        $metadataOut = '{"status": "CHANGED", "mimetype": "text/plain", "versions": [{"status": "CHANGED", "mimetype": "text/plain", "checksum": 2499810342, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 4, "is_folder": false, "chunks": [], "id": 155, "size": 61}, {"status": "RENAMED", "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 3, "is_folder": false, "chunks": [], "id": 155, "size": 59}, {"status": "RENAMED", "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 2, "is_folder": false, "chunks": [], "id": 155, "size": 59}, {"status": "NEW", "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 1, "is_folder": false, "chunks": [], "id": 155, "size": 59}], "checksum": 2499810342, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": "null", "version": 4, "is_folder": false, "chunks": [], "id": 155, "size": 61}';
        $check = '[{"status": "CHANGED", "mimetype": "text/plain", "checksum": 2499810342, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 4, "is_folder": false, "chunks": [], "id": 155, "size": 61}, {"status": "RENAMED", "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 3, "is_folder": false, "chunks": [], "id": 155, "size": 59}, {"status": "RENAMED", "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 2, "is_folder": false, "chunks": [], "id": 155, "size": 59}, {"status": "NEW", "mimetype": "text/plain", "checksum": 1825838054, "modified_at": "2014-06-20 10:11:11.031", "filename": "welcome.txt", "parent_id": null, "version": 1, "is_folder": false, "chunks": [], "id": 155, "size": 59}]';
        $this->exerciseListVersion($metadataIn,$metadataOut,$check,153,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * with: tokenAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnPermissionDenied
     */
    public function test_listVersions_called_tokenAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret_returnPermissionDenied()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"listVersions","id":"9873615"}}';
        $metadataOut = '403';
        $this->exerciseListVersion($metadataIn,$metadataOut,$this->permission,9873615,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * method: listVersions
     * when: called
     * with: tokenAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_listVersions_called_tokenAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"listVersions","id":"9873615"}}';
        $metadataOut = 'false';
        $this->exerciseListVersion($metadataIn,$metadataOut,$this->exception,9873615,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * when: called
     * with: tokenAndIdAndVersionAndPathAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnCorrectDownloadVersion
     */
    public function test_getFileVersionData_called_tokenAndIdAndVersionAndPathAndResourceUrlAndConsumerKeyAndConsumerSecret_returnCorrectDownloadVersion()
    {
        $metadataOut = "true";
        $this->exerciseGetFileVersionData($metadataOut, '{"status":true}', $this->cloud,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * with: tokenAndIdAndVersionAndPathAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnPermissionDenied
     */
    public function test_getFileVersionData_called_tokenAndIdAndVersionAndPathAndResourceUrlAndConsumerKeyAndConsumerSecret_returnPermissionDenied()
    {
        $metadataOut = "403";
        $this->exerciseGetFileVersionData($metadataOut, $this->permission, $this->cloud, $this->resourceUrl, $this->consumerKey, $this->consumerSecret);
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
     * method: getFileVersionData
     * when: called:
     * with: tokenAndIdAndVersionAndPathAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_getFileVersionData_called_tokenAndIdAndVersionAndPathAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataOut = "false";
        $this->exerciseGetFileVersionData($metadataOut, $this->exception, $this->cloud, $this->resourceUrl, $this->consumerKey, $this->consumerSecret);
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
     * with: tokenAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnCorrectData
     */
    public function test_getListUsersShare_called_tokenAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret_returnCorrectData()
    {
        $id = 153;
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"listUsersShare","id":"153"}}';
        $metadataOut = '[{"joined_at": "2014-05-27", "is_owner": true, "name": "tester1", "email": "tester1@test.com"}]';
        $this->exerciseListUsersShare($metadataIn,$metadataOut,$metadataOut,$id,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * with: tokenAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnPermissionDenied
     */
    public function test_getListUsersShare_called_tokenAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret_returnPermissionDenied()
    {
        $id = 153;
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"listUsersShare","id":"153"}}';
        $metadataOut = '403';
        $this->exerciseListUsersShare($metadataIn,$metadataOut,$this->permission,$id,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * method: getListUsersShare
     * when: called
     * with: tokenAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_getListUsersShare_called_tokenAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $id = 153;
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"listUsersShare","id":"153"}}';
        $metadataOut = 'false';
        $this->exerciseListUsersShare($metadataIn,$metadataOut,$this->exception,$id,$this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * with: tokenAndIdAndListAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnCorrect
     */
    public function test_shareFolder_called_tokenAndIdAndListAndResourceUrlAndConsumerKeyAndConsumerSecret_returnCorrect()
    {
        $id = 153;
        $list = array("a@a.com","b@b.com");
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"shareFolder","id":"153","list":["a@a.com","b@b.com"],"shared":false}}';
        $metadataOut = 'true';
        $shared = false;
        $this->exerciseShareFolder($metadataIn, $metadataOut, '{"status":true}', $id, $list, $shared, $this->resourceUrl,$this->consumerKey,$this->consumerSecret);
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
     * with: tokenAndIdAndListAndSharedAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnCorrect
     */
    public function test_shareFolder_called_tokenAndIdAndListAndSharedAndResourceUrlAndConsumerKeyAndConsumerSecret_returnCorrect()
    {
        $id = 153;
        $list = array("a@a.com","b@b.com");
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"shareFolder","id":"153","list":["a@a.com","b@b.com"],"shared":true}}';
        $metadataOut = 'true';
        $shared = true;
        $this->exerciseShareFolder($metadataIn, $metadataOut, '{"status":true}', $id, $list, $shared, $this->resourceUrl, $this->consumerKey,$this->consumerSecret);
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
     * with: tokenAndIdAndListAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnPermissionDenied
     */
    public function test_shareFolder_called_tokenAndIdAndListAndResourceUrlAndConsumerKeyAndConsumerSecret_returnPermissionDenied()
    {
        $id = 153;
        $list = array("a@a.com","b@b.com");
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"shareFolder","id":"153","list":["a@a.com","b@b.com"],"shared":false}}';
        $metadataOut = '403';
        $shared = false;
        $this->exerciseShareFolder($metadataIn, $metadataOut, $this->permission, $id, $list, $shared, $this->resourceUrl, $this->consumerKey, $this->consumerSecret);
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
     * with: tokenAndIdAndListAndSharedAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnPermissionDenied
     */
    public function test_shareFolder_called_tokenAndIdAndListAndSharedAndResourceUrlAndConsumerKeyAndConsumerSecret_returnPermissionDenied()
    {
        $id = 153;
        $list = array("a@a.com","b@b.com");
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"shareFolder","id":"153","list":["a@a.com","b@b.com"],"shared":true}}';
        $metadataOut = '403';
        $shared = true;
        $this->exerciseShareFolder($metadataIn, $metadataOut, $this->permission, $id, $list, $shared, $this->resourceUrl, $this->consumerKey, $this->consumerSecret);
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
     * with: tokenAndIdAndListAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_shareFolder_called_tokenAndIdAndListAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $id = 153;
        $list = array("a@a.com","b@b.com");
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"shareFolder","id":"153","list":["a@a.com","b@b.com"],"shared":false}}';
        $metadataOut = 'false';
        $shared = false;
        $this->exerciseShareFolder($metadataIn, $metadataOut, $this->exception, $id, $list, $shared, $this->resourceUrl, $this->consumerKey, $this->consumerSecret);
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
     * method: shareFolder
     * when: called
     * with: tokenAndIdAndListAndSharedAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_shareFolder_called_tokenAndIdAndListAndAndSharedAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $id = 153;
        $list = array("a@a.com","b@b.com");
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/ast3-deim.urv.cat\/v1","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"shareFolder","id":"153","list":["a@a.com","b@b.com"],"shared":true}}';
        $metadataOut = 'false';
        $shared = true;
        $this->exerciseShareFolder($metadataIn, $metadataOut, $this->exception, $id, $list, $shared, $this->resourceUrl, $this->consumerKey, $this->consumerSecret);
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
        $this->exerciseInsertComment($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: insertComment
     * when: called
     * with: cloudAndTokenAndIdAndUserAndTextAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnMetadataInsert
     */
    public function test_insertComment_called_cloudAndTokenAndIdAndUserAndTextAndResourceUrlAndConsumerKeyAndConsumerSecret_returnMetadataInsert()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"insertComment","id":"153","user":"eyeos","text":"prueba"}}';
        $metadataOut = '{"id":"153","user":"eyeos","text":"prueba","cloud":"stacksync","status":"NEW","time_created":"201406201548"}';
        $this->exerciseInsertComment($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: insertComment
     * when: called
     * with: cloudAndTokenAndIdAndUserAndText
     * should: returnMetadataInsert
     */
    public function test_insertComment_called_cloudAndTokenAndIdAndUserAndText_returnMetadataInsert()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"insertComment","id":"153","user":"eyeos","text":"prueba"}}';
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
        $this->exerciseInsertComment($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: insertComment
     * when: called
     * with: cloudAndTokenAndIdAndUserAndTextAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_insertComment_called_cloudAndTokenAndIdAndUserAndTextAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"insertComment","id":"153","user":"eyeos","text":"prueba"}}';
        $metadataOut = 400;
        $this->exerciseInsertComment($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: insertComment
     * when: called
     * with: cloudAndTokenAndIdAndUserAndText
     * should: returnException
     */
    public function test_insertComment_called_cloudAndTokenAndIdAndUserAndText_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"insertComment","id":"153","user":"eyeos","text":"prueba"}}';
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
        $this->exerciseDeleteComment($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: deleteComment
     * when: called
     * with: cloudAndTokenAndIdAndUserAndTimeCreatedAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnMetadataDelete
     */
    public function test_deleteComment_called_cloudAndTokenAndIdAndUserAndTimeCreatedAndResourceUrlAndConsumerKeyAndConsumerSecret_returnMetadataDelete()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteComment","id":"153","user":"eyeos","time_created":"201406201548"}}';
        $metadataOut = '{"id":"153","user":"eyeos","text":"prueba","cloud":"stacksync","status":"DELETED","time_created":"201406201548"}';
        $this->exerciseDeleteComment($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: deleteComment
     * when: called
     * with: cloudAndTokenAndIdAndUserAndTimeCreated
     * should: returnMetadataDelete
     */
    public function test_deleteComment_called_cloudAndTokenAndIdAndUserAndTimeCreated_returnMetadataDelete()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteComment","id":"153","user":"eyeos","time_created":"201406201548"}}';
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
        $this->exerciseDeleteComment($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: deleteComment
     * when: called
     * with: cloudAndTokenAndIdAndUserAndTimeCreatedAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_deleteComment_called_cloudAndTokenAndIdAndUserAndTimeCreatedAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteComment","id":"153","user":"eyeos","time_created":"201406201548"}}';
        $metadataOut = 400;
        $this->exerciseDeleteComment($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: deleteComment
     * when: called
     * with: cloudAndTokenAndIdAndUserAndTimeCreated
     * should: returnException
     */
    public function test_deleteComment_called_cloudAndTokenAndIdAndUserAndTimeCreated_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteComment","id":"153","user":"eyeos","time_created":"201406201548"}}';
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
        $this->exerciseGetComments($metadataIn,$metadataOut,$this->urlAPISync);

    }

    /**
     * method: getComments
     * when: called
     * with: cloudAndTokenAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnListMetadata
     */
    public function test_getComments_called_cloudAndTokenAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret_returnListMetadata()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getComments","id":"153"}}';
        $metadataOut = '[{"id":"153","user":"eyeos","text":"prueba","cloud":"stacksync","status":"NEW","time_created":"201406201548"}]';
        $this->exerciseGetComments($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);

    }

    /**
     * method: getComments
     * when: called
     * with: cloudAndTokenAndId
     * should: returnListMetadata
     */
    public function test_getComments_called_cloudAndTokenAndId_returnListMetadata()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getComments","id":"153"}}';
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
        $this->exerciseGetComments($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: getComments
     * when: called
     * with: cloudAndTokenAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_getComments_called_cloudAndTokenAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getComments","id":"153"}}';
        $metadataOut = 400;
        $this->exerciseGetComments($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: getComments
     * when: called
     * with: cloudAndTokenAndIdAnd
     * should: returnException
     */
    public function test_getComments_called_cloudAndTokenAndId_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getComments","id":"153"}}';
        $metadataOut = 400;
        $this->exerciseGetComments($metadataIn,$metadataOut);
    }


    /**
     * method: getControlCommentsCloud
     * when: called
     * with: ValidCloud
     * should: returnMetadata
     */
    public function test_getControlCommentsCloud_called_Valid_Cloud_returnMetadata()
    {
        $metadataIn = '{"config":{"type":"comments","cloud":"Stacksync"}}';
        $metadataOut = '{"comments":"true"}';
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->getControlCommentsCloud("Stacksync");
        $this->assertEquals(json_decode($metadataOut),$actual);
    }

    /**
     * method: insertEvent
     * when: called
     * with: cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValue
             AndSubjectAndLocationAndDescriptionAndRepeattypeAndResourceUrl
     * should: returnEvent
     */
    public function test_insertEvent_called_cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValueAndSubjectAndLocationAndDescriptionAndRepeattypeAndResourceUrl_returnEvent()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"insertEvent","user":"eyeos","calendar":"personal","isallday":0,"timestart":"201419160000","timeend":"201419170000","repetition":"None","finaltype":"1","finalvalue":"0","subject":"Visita","location":"Barcelona","description":"Dentista","repeattype":"n"}}';
        $metadataOut = '{"status": "NEW", "repeattype":"n","description": "Llevar justificante", "finalvalue": "0", "timestart": "201419160000", "user": "eyeos", "calendar": "personal", "repetition": "None", "cloud": "Stacksync", "subject": "VisitaMedico", "timeend": "201419170000", "location": "Barcelona", "finaltype": "1", "type": "event", "isallday": 0}';
        $this->exerciseInsertEvent($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: insertEvent
     * when: called
     * with: cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValue
    AndSubjectAndLocationAndDescriptionAndRepeattypeAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnEvent
     */
    public function test_insertEvent_called_cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValueAndSubjectAndLocationAndDescriptionAndRepeattypeAndResourceUrlAndConsumerKeyAndConsumerSecret_returnEvent()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"insertEvent","user":"eyeos","calendar":"personal","isallday":0,"timestart":"201419160000","timeend":"201419170000","repetition":"None","finaltype":"1","finalvalue":"0","subject":"Visita","location":"Barcelona","description":"Dentista","repeattype":"n"}}';
        $metadataOut = '{"status": "NEW", "repeattype":"n","description": "Llevar justificante", "finalvalue": "0", "timestart": "201419160000", "user": "eyeos", "calendar": "personal", "repetition": "None", "cloud": "Stacksync", "subject": "VisitaMedico", "timeend": "201419170000", "location": "Barcelona", "finaltype": "1", "type": "event", "isallday": 0}';
        $this->exerciseInsertEvent($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: insertEvent
     * when: called
     * with: cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValue
    AndSubjectAndLocationAndDescriptionAndRepeattype
     * should: returnEvent
     */
    public function test_insertEvent_called_cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValueAndSubjectAndLocationAndDescriptionAndRepeattype_returnEvent()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"insertEvent","user":"eyeos","calendar":"personal","isallday":0,"timestart":"201419160000","timeend":"201419170000","repetition":"None","finaltype":"1","finalvalue":"0","subject":"Visita","location":"Barcelona","description":"Dentista","repeattype":"n"}}';
        $metadataOut = '{"status": "NEW", "repeattype":"n","description": "Llevar justificante", "finalvalue": "0", "timestart": "201419160000", "user": "eyeos", "calendar": "personal", "repetition": "None", "cloud": "Stacksync", "subject": "VisitaMedico", "timeend": "201419170000", "location": "Barcelona", "finaltype": "1", "type": "event", "isallday": 0}';
        $this->exerciseInsertEvent($metadataIn,$metadataOut);
    }

    /**
     * method: insertEvent
     * when: called
     * with: cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValue
    AndSubjectAndLocationAndDescriptionAndRepeattypeAndResourceUrl
     * should: returnException
     */
    public function test_insertEvent_called_cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValueAndSubjectAndLocationAndDescriptionAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"insertEvent","user":"eyeos","calendar":"personal","isallday":0,"timestart":"201419160000","timeend":"201419170000","repetition":"None","finaltype":"1","finalvalue":"0","subject":"Visita","location":"Barcelona","description":"Dentista","repeattype":"n"}}';
        $metadataOut = 400;
        $this->exerciseInsertEvent($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: insertEvent
     * when: called
     * with: cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValue
    AndSubjectAndLocationAndDescriptionAndRepeattypeAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_insertEvent_called_cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValueAndSubjectAndLocationAndDescriptionAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"insertEvent","user":"eyeos","calendar":"personal","isallday":0,"timestart":"201419160000","timeend":"201419170000","repetition":"None","finaltype":"1","finalvalue":"0","subject":"Visita","location":"Barcelona","description":"Dentista","repeattype":"n"}}';
        $metadataOut = 400;
        $this->exerciseInsertEvent($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: insertEvent
     * when: called
     * with: cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValue
    AndSubjectAndLocationAndDescriptionAndRepeattype
     * should: returnException
     */
    public function test_insertEvent_called_cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValueAndSubjectAndLocationAndDescription_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"insertEvent","user":"eyeos","calendar":"personal","isallday":0,"timestart":"201419160000","timeend":"201419170000","repetition":"None","finaltype":"1","finalvalue":"0","subject":"Visita","location":"Barcelona","description":"Dentista","repeattype":"n"}}';
        $metadataOut = 400;
        $this->exerciseInsertEvent($metadataIn,$metadataOut);
    }

    /**
     * method: deleteEvent
     * when: called
     * with: cloudAndTokenAndUserAndCalendarAndTimeStartAndTimeEndAndIsAllDayAndResourceUrl
     * should: returnEvent
     */
    public function test_deleteEvent_called_cloudAndTokenAndUserAndCalendarAndTimeStartAndTimeEndAndIsAllDayAndResourceUrl_returnEvent()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteEvent","user":"eyeos","calendar":"personal","timestart":"201419160000","timeend":"201419170000","isallday":0}}';
        $metadataOut = '{"status": "DELETED", "description": "Llevar justificante", "finalvalue": "0", "timestart": "201419160000", "user": "eyeos", "calendar": "personal", "repetition": "None", "cloud": "Stacksync", "subject": "VisitaMedico", "timeend": "201419170000", "location": "Barcelona", "finaltype": "1", "type": "event", "isallday": 0}';
        $this->exerciseDeleteEvent($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: deleteEvent
     * when: called
     * with: cloudAndTokenAndUserAndCalendarAndTimeStartAndTimeEndAndIsAllDayAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnEvent
     */
    public function test_deleteEvent_called_cloudAndTokenAndUserAndCalendarAndTimeStartAndTimeEndAndIsAllDayAndResourceUrlAndConsumerKeyAndConsumerSecret_returnEvent()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteEvent","user":"eyeos","calendar":"personal","timestart":"201419160000","timeend":"201419170000","isallday":0}}';
        $metadataOut = '{"status": "DELETED", "description": "Llevar justificante", "finalvalue": "0", "timestart": "201419160000", "user": "eyeos", "calendar": "personal", "repetition": "None", "cloud": "Stacksync", "subject": "VisitaMedico", "timeend": "201419170000", "location": "Barcelona", "finaltype": "1", "type": "event", "isallday": 0}';
        $this->exerciseDeleteEvent($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: deleteEvent
     * when: called
     * with: cloudAndTokenAndUserAndCalendarAndTimeStartAndTimeEndAndIsAllDay
     * should: returnEvent
     */
    public function test_deleteEvent_called_cloudAndTokenAndUserAndCalendarAndTimeStartAndTimeEndAndIsAllDay_returnEvent()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteEvent","user":"eyeos","calendar":"personal","timestart":"201419160000","timeend":"201419170000","isallday":0}}';
        $metadataOut = '{"status": "DELETED", "description": "Llevar justificante", "finalvalue": "0", "timestart": "201419160000", "user": "eyeos", "calendar": "personal", "repetition": "None", "cloud": "Stacksync", "subject": "VisitaMedico", "timeend": "201419170000", "location": "Barcelona", "finaltype": "1", "type": "event", "isallday": 0}';
        $this->exerciseDeleteEvent($metadataIn,$metadataOut);
    }

    /**
     * method: deleteEvent
     * when: called
     * with: cloudAndTokenAndUserAndCalendarAndTimeStartAndTimeEndAndIsAllDayAndResourceUrl
     * should: returnException
     */
    public function test_deleteEvent_called_cloudAndTokenAndUserAndCalendarAndTimeStartAndTimeEndAndIsAllDayAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteEvent","user":"eyeos","calendar":"personal","timestart":"201419160000","timeend":"201419170000","isallday":0}}';
        $metadataOut = 400;
        $this->exerciseDeleteEvent($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: deleteEvent
     * when: called
     * with: cloudAndTokenAndUserAndCalendarAndTimeStartAndTimeEndAndIsAllDayAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_deleteEvent_called_cloudAndTokenAndUserAndCalendarAndTimeStartAndTimeEndAndIsAllDayAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteEvent","user":"eyeos","calendar":"personal","timestart":"201419160000","timeend":"201419170000","isallday":0}}';
        $metadataOut = 400;
        $this->exerciseDeleteEvent($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: deleteEvent
     * when: called
     * with: cloudAndTokenAndUserAndCalendarAndTimeStartAndTimeEndAndIsAllDay
     * should: returnException
     */
    public function test_deleteEvent_called_cloudAndTokenAndUserAndCalendarAndTimeStartAndTimeEndAndIsAllDay_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteEvent","user":"eyeos","calendar":"personal","timestart":"201419160000","timeend":"201419170000","isallday":0}}';
        $metadataOut = 400;
        $this->exerciseDeleteEvent($metadataIn,$metadataOut);
    }

    /**
     * method: updateEvent
     * when: called
     * with: cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValue
    AndSubjectAndLocationAndDescriptionAndAndRepeattypeAndResourceUrl
     * should: returnEvent
     */
    public function test_updateEvent_called_cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValueAndSubjectAndLocationAndDescriptionAndRepeattypeAndResourceUrl_returnEvent()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"updateEvent","user":"eyeos","calendar":"personal","isallday":0,"timestart":"201419160000","timeend":"201419170000","repetition":"None","finaltype":"1","finalvalue":"0","subject":"Visita","location":"Barcelona","description":"Dentista","repeattype":"n"}}';
        $metadataOut = '{"status": "CHANGED", "repeattype":"n","description": "Llevar justificante", "finalvalue": "0", "timestart": "201419160000", "user": "eyeos", "calendar": "personal", "repetition": "None", "cloud": "Stacksync", "subject": "VisitaMedico", "timeend": "201419170000", "location": "Barcelona", "finaltype": "1", "type": "event", "isallday": 0}';
        $this->exerciseUpdateEvent($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: updateEvent
     * when: called
     * with: cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValue
    AndSubjectAndLocationAndDescriptionAndAndRepeattypeAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnEvent
     */
    public function test_updateEvent_called_cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValueAndSubjectAndLocationAndDescriptionAndRepeattypeAndResourceUrlAndConsumerKeyAndConsumerSecret_returnEvent()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"updateEvent","user":"eyeos","calendar":"personal","isallday":0,"timestart":"201419160000","timeend":"201419170000","repetition":"None","finaltype":"1","finalvalue":"0","subject":"Visita","location":"Barcelona","description":"Dentista","repeattype":"n"}}';
        $metadataOut = '{"status": "CHANGED", "repeattype":"n","description": "Llevar justificante", "finalvalue": "0", "timestart": "201419160000", "user": "eyeos", "calendar": "personal", "repetition": "None", "cloud": "Stacksync", "subject": "VisitaMedico", "timeend": "201419170000", "location": "Barcelona", "finaltype": "1", "type": "event", "isallday": 0}';
        $this->exerciseUpdateEvent($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: updateEvent
     * when: called
     * with: cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValue
    AndSubjectAndLocationAndDescriptionAndAndRepeattype
     * should: returnEvent
     */
    public function test_updateEvent_called_cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValueAndSubjectAndLocationAndDescriptionAndRepeattype_returnEvent()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"updateEvent","user":"eyeos","calendar":"personal","isallday":0,"timestart":"201419160000","timeend":"201419170000","repetition":"None","finaltype":"1","finalvalue":"0","subject":"Visita","location":"Barcelona","description":"Dentista","repeattype":"n"}}';
        $metadataOut = '{"status": "CHANGED", "repeattype":"n","description": "Llevar justificante", "finalvalue": "0", "timestart": "201419160000", "user": "eyeos", "calendar": "personal", "repetition": "None", "cloud": "Stacksync", "subject": "VisitaMedico", "timeend": "201419170000", "location": "Barcelona", "finaltype": "1", "type": "event", "isallday": 0}';
        $this->exerciseUpdateEvent($metadataIn,$metadataOut);
    }

    /**
     * method: updateEvent
     * when: called
     * with: cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValue
    AndSubjectAndLocationAndDescriptionAndRepeattypeAndResourceUrl
     * should: returnException
     */
    public function test_updateEvent_called_cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValueAndSubjectAndLocationAndDescriptionAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"updateEvent","user":"eyeos","calendar":"personal","isallday":0,"timestart":"201419160000","timeend":"201419170000","repetition":"None","finaltype":"1","finalvalue":"0","subject":"Visita","location":"Barcelona","description":"Dentista","repeattype":"n"}}';
        $metadataOut = 400;
        $this->exerciseUpdateEvent($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: updateEvent
     * when: called
     * with: cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValue
    AndSubjectAndLocationAndDescriptionAndRepeattypeAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_updateEvent_called_cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValueAndSubjectAndLocationAndDescriptionAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"updateEvent","user":"eyeos","calendar":"personal","isallday":0,"timestart":"201419160000","timeend":"201419170000","repetition":"None","finaltype":"1","finalvalue":"0","subject":"Visita","location":"Barcelona","description":"Dentista","repeattype":"n"}}';
        $metadataOut = 400;
        $this->exerciseUpdateEvent($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: updateEvent
     * when: called
     * with: cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValue
    AndSubjectAndLocationAndDescriptionAndRepeattype
     * should: returnException
     */
    public function test_updateEvent_called_cloudAndTokenAndUserAndCalendarAndIsAllDayAndTimeStartAndAndTimeEndAndRepetitionAndFinalTypeAndAndFinalValueAndSubjectAndLocationAndDescription_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"updateEvent","user":"eyeos","calendar":"personal","isallday":0,"timestart":"201419160000","timeend":"201419170000","repetition":"None","finaltype":"1","finalvalue":"0","subject":"Visita","location":"Barcelona","description":"Dentista","repeattype":"n"}}';
        $metadataOut = 400;
        $this->exerciseUpdateEvent($metadataIn,$metadataOut);
    }

    /**
     * method: getEvents
     * when: called
     * with: cloudAndTokenAndUserAndCalendarAndResourceUrl
     * should: returnEvents
     */
    public function test_getEvents_called_cloudAndTokenAndUserAndCalendarAndResourceUrl_returnEvents()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getEvents","user":"eyeos","calendar":"personal"}}';
        $metadataOut = '[{"status": "CHANGED", "description": "Llevar justificante", "location": "Barcelona", "finalvalue": "0", "timeend": "201419170000", "timestart": "201419160000", "isallday": 0, "user": "eyeos", "finaltype": "1", "calendar": "personal", "repetition": "None", "type": "event", "cloud": "Stacksync", "subject": "VisitaMedico"}]';
        $this->exerciseGetEvents($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: getEvents
     * when: called
     * with: cloudAndTokenAndUserAndCalendarAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnEvents
     */
    public function test_getEvents_called_cloudAndTokenAndUserAndCalendarAndResourceUrlAndConsumerKeyAndConsumerSecret_returnEvents()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getEvents","user":"eyeos","calendar":"personal"}}';
        $metadataOut = '[{"status": "CHANGED", "description": "Llevar justificante", "location": "Barcelona", "finalvalue": "0", "timeend": "201419170000", "timestart": "201419160000", "isallday": 0, "user": "eyeos", "finaltype": "1", "calendar": "personal", "repetition": "None", "type": "event", "cloud": "Stacksync", "subject": "VisitaMedico"}]';
        $this->exerciseGetEvents($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: getEvents
     * when: called
     * with: cloudAndTokenAndUserAndCalendar
     * should: returnEvents
     */
    public function test_getEvents_called_cloudAndTokenAndUserAndCalendar_returnEvents()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getEvents","user":"eyeos","calendar":"personal"}}';
        $metadataOut = '[{"status": "CHANGED", "description": "Llevar justificante", "location": "Barcelona", "finalvalue": "0", "timeend": "201419170000", "timestart": "201419160000", "isallday": 0, "user": "eyeos", "finaltype": "1", "calendar": "personal", "repetition": "None", "type": "event", "cloud": "Stacksync", "subject": "VisitaMedico"}]';
        $this->exerciseGetEvents($metadataIn,$metadataOut);
    }

    /**
     * method: getEvents
     * when: called
     * with: cloudAndTokenAndUserAndCalendarAndResourceUrl
     * should: returnException
     */
    public function test_getEvents_called_cloudAndTokenAndUserAndCalendarAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getEvents","user":"eyeos","calendar":"personal"}}';
        $metadataOut = 400;
        $this->exerciseGetEvents($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: getEvents
     * when: called
     * with: cloudAndTokenAndUserAndCalendarAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_getEvents_called_cloudAndTokenAndUserAndCalendarAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getEvents","user":"eyeos","calendar":"personal"}}';
        $metadataOut = 400;
        $this->exerciseGetEvents($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: getEvents
     * when: called
     * with: cloudAndTokenAndUserAndCalendar
     * should: returnException
     */
    public function test_getEvents_called_cloudAndTokenAndUserAndCalendar_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getEvents","user":"eyeos","calendar":"personal"}}';
        $metadataOut = 400;
        $this->exerciseGetEvents($metadataIn,$metadataOut);
    }

    /**
     * method: insertCalendar
     * when: called
     * with: cloudAndTokenAndUserAndNameAndDescriptionAndTimeZoneAndResourceUrl
     * should: returnCalendar
     */
    public function test_insertCalendar_called_cloudAndTokenAndUserAndNameAndDescriptionAndTimeZoneAndResourceUrl_returnCalendar()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"insertCalendar","user":"eyeos","name":"personal","description":"CalendarioPersonal","timezone":"0"}}';
        $metadataOut = '{"status": "NEW", "description": "CalendarioPersonal", "user": "eyeos", "timezone": 0, "type": "calendar", "cloud": "Stacksync", "name": "personal"}';
        $this->exerciseInsertCalendar($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: insertCalendar
     * when: called
     * with: cloudAndTokenAndUserAndNameAndDescriptionAndTimeZoneAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnCalendar
     */
    public function test_insertCalendar_called_cloudAndTokenAndUserAndNameAndDescriptionAndTimeZoneAndResourceUrlAndConsumerKeyAndConsumerSecret_returnCalendar()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"insertCalendar","user":"eyeos","name":"personal","description":"CalendarioPersonal","timezone":"0"}}';
        $metadataOut = '{"status": "NEW", "description": "CalendarioPersonal", "user": "eyeos", "timezone": 0, "type": "calendar", "cloud": "Stacksync", "name": "personal"}';
        $this->exerciseInsertCalendar($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: insertCalendar
     * when: called
     * with: cloudAndTokenAndUserAndNameAndDescriptionAndTimeZone
     * should: returnCalendar
     */
    public function test_insertCalendar_called_cloudAndTokenAndUserAndNameAndDescriptionAndTimeZone_returnCalendar()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"insertCalendar","user":"eyeos","name":"personal","description":"CalendarioPersonal","timezone":"0"}}';
        $metadataOut = '{"status": "NEW", "description": "CalendarioPersonal", "user": "eyeos", "timezone": 0, "type": "calendar", "cloud": "Stacksync", "name": "personal"}';
        $this->exerciseInsertCalendar($metadataIn,$metadataOut);
    }

    /**
     * method: insertCalendar
     * when: called
     * with: cloudAndTokenAndUserAndNameAndDescriptionAndTimeZoneAndResourceUrl
     * should: returnException
     */
    public function test_insertCalendar_called_cloudAndTokenAndUserAndNameAndDescriptionAndTimeZoneAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"insertCalendar","user":"eyeos","name":"personal","description":"CalendarioPersonal","timezone":"0"}}';
        $metadataOut = 400;
        $this->exerciseInsertCalendar($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: insertCalendar
     * when: called
     * with: cloudAndTokenAndUserAndNameAndDescriptionAndTimeZoneAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_insertCalendar_called_cloudAndTokenAndUserAndNameAndDescriptionAndTimeZoneAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"insertCalendar","user":"eyeos","name":"personal","description":"CalendarioPersonal","timezone":"0"}}';
        $metadataOut = 400;
        $this->exerciseInsertCalendar($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: insertCalendar
     * when: called
     * with: cloudAndTokenAndUserAndNameAndDescriptionAndTimeZone
     * should: returnException
     */
    public function test_insertCalendar_called_cloudAndTokenAndUserAndNameAndDescriptionAndTimeZone_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"insertCalendar","user":"eyeos","name":"personal","description":"CalendarioPersonal","timezone":"0"}}';
        $metadataOut = 400;
        $this->exerciseInsertCalendar($metadataIn,$metadataOut);
    }

    /**
     * method: deleteCalendar
     * when: called
     * with: cloudAndTokenAndUserAndNameAndResourceUrl
     * should: returnCalendar
     */
    public function test_deleteCalendar_called_cloudAndTokenAndUserAndNameAndResourceUrl_returnCalendar()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteCalendar","user":"eyeos","name":"personal"}}';
        $metadataOut = '{"status": "DELETED", "description": "Llevar justificante", "user": "eyeos", "timezone": 0, "type": "calendar", "cloud": "Stacksync", "name": "personal"}';
        $this->exerciseDeleteCalendar($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: deleteCalendar
     * when: called
     * with: cloudAndTokenAndUserAndNameAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnCalendar
     */
    public function test_deleteCalendar_called_cloudAndTokenAndUserAndNameAndResourceUrlAndConsumerKeyAndConsumerSecret_returnCalendar()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteCalendar","user":"eyeos","name":"personal"}}';
        $metadataOut = '{"status": "DELETED", "description": "Llevar justificante", "user": "eyeos", "timezone": 0, "type": "calendar", "cloud": "Stacksync", "name": "personal"}';
        $this->exerciseDeleteCalendar($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: deleteCalendar
     * when: called
     * with: cloudAndTokenAndUserAndName
     * should: returnCalendar
     */
    public function test_deleteCalendar_called_cloudAndTokenAndUserAndName_returnCalendar()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteCalendar","user":"eyeos","name":"personal"}}';
        $metadataOut = '{"status": "DELETED", "description": "Llevar justificante", "user": "eyeos", "timezone": 0, "type": "calendar", "cloud": "Stacksync", "name": "personal"}';
        $this->exerciseDeleteCalendar($metadataIn,$metadataOut);
    }

    /**
     * method: deleteCalendar
     * when: called
     * with: cloudAndTokenAndUserAndNameAndResourceUrl
     * should: returnException
     */
    public function test_deleteCalendar_called_cloudAndTokenAndUserAndNameAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteCalendar","user":"eyeos","name":"personal"}}';
        $metadataOut = 400;
        $this->exerciseDeleteCalendar($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: deleteCalendar
     * when: called
     * with: cloudAndTokenAndUserAndNameAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_deleteCalendar_called_cloudAndTokenAndUserAndNameAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteCalendar","user":"eyeos","name":"personal"}}';
        $metadataOut = 400;
        $this->exerciseDeleteCalendar($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: deleteCalendar
     * when: called
     * with: cloudAndTokenAndUserAndName
     * should: returnException
     */
    public function test_deleteCalendar_called_cloudAndTokenAndUserAndName_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteCalendar","user":"eyeos","name":"personal"}}';
        $metadataOut = 400;
        $this->exerciseDeleteCalendar($metadataIn,$metadataOut);
    }

    /**
     * method: updateCalendar
     * when: called
     * with: cloudAndTokenAndUserAndNameAndDescriptionAndTimeZoneAndResourceUrl
     * should: returnCalendar
     */
    public function test_updateCalendar_called_cloudAndTokenAndUserAndNameAndDescriptionAndTimeZoneAndResourceUrl_returnCalendar()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"updateCalendar","user":"eyeos","name":"personal","description":"CalendarioLaboral","timezone":"0"}}';
        $metadataOut = '{"status": "CHANGED", "description": "Llevar justificante", "user": "eyeos", "timezone": 0, "type": "calendar", "cloud": "Stacksync", "name": "personal"}';
        $this->exerciseUpdateCalendar($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: updateCalendar
     * when: called
     * with: cloudAndTokenAndUserAndNameAndDescriptionAndTimeZoneAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnCalendar
     */
    public function test_updateCalendar_called_cloudAndTokenAndUserAndNameAndDescriptionAndTimeZoneAndResourceUrlAndConsumerKeyAndConsumerSecret_returnCalendar()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"updateCalendar","user":"eyeos","name":"personal","description":"CalendarioLaboral","timezone":"0"}}';
        $metadataOut = '{"status": "CHANGED", "description": "Llevar justificante", "user": "eyeos", "timezone": 0, "type": "calendar", "cloud": "Stacksync", "name": "personal"}';
        $this->exerciseUpdateCalendar($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: updateCalendar
     * when: called
     * with: cloudAndTokenAndUserAndNameAndDescriptionAndTimeZone
     * should: returnCalendar
     */
    public function test_updateCalendar_called_cloudAndTokenAndUserAndNameAndDescriptionAndTimeZone_returnCalendar()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"updateCalendar","user":"eyeos","name":"personal","description":"CalendarioLaboral","timezone":"0"}}';
        $metadataOut = '{"status": "CHANGED", "description": "Llevar justificante", "user": "eyeos", "timezone": 0, "type": "calendar", "cloud": "Stacksync", "name": "personal"}';
        $this->exerciseUpdateCalendar($metadataIn,$metadataOut);
    }

    /**
     * method: updateCalendar
     * when: called
     * with: cloudAndTokenAndUserAndNameAndDescriptionAndTimeZoneAndResourceUrl
     * should: returnException
     */
    public function test_updateCalendar_called_cloudAndTokenAndUserAndNameAndDescriptionAndTimeZoneAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"updateCalendar","user":"eyeos","name":"personal","description":"CalendarioLaboral","timezone":"0"}}';
        $metadataOut = 400;
        $this->exerciseUpdateCalendar($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: updateCalendar
     * when: called
     * with: cloudAndTokenAndUserAndNameAndDescriptionAndTimeZoneAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_updateCalendar_called_cloudAndTokenAndUserAndNameAndDescriptionAndTimeZoneAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"updateCalendar","user":"eyeos","name":"personal","description":"CalendarioLaboral","timezone":"0"}}';
        $metadataOut = 400;
        $this->exerciseUpdateCalendar($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: updateCalendar
     * when: called
     * with: cloudAndTokenAndUserAndNameAndDescriptionAndTimeZone
     * should: returnException
     */
    public function test_updateCalendar_called_cloudAndTokenAndUserAndNameAndDescriptionAndTimeZone_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"updateCalendar","user":"eyeos","name":"personal","description":"CalendarioLaboral","timezone":"0"}}';
        $metadataOut = 400;
        $this->exerciseUpdateCalendar($metadataIn,$metadataOut);
    }

    /**
     * method: getCalendars
     * when: called
     * with: cloudAndTokenAndUserAndResourceUrl
     * should: returnCalendars
     */
    public function test_getCalendars_called_cloudAndTokenAndUserAndResourceUrl_returnCalendars()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getCalendars","user":"eyeos"}}';
        $metadataOut = '[{"status": "NEW", "description": "Llevar justificante", "user": "eyeos", "timezone": 0, "type": "calendar", "cloud": "Stacksync", "name": "personal"}]';
        $this->exerciseGetCalendars($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: getCalendars
     * when: called
     * with: cloudAndTokenAndUserAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnCalendars
     */
    public function test_getCalendars_called_cloudAndTokenAndUserAndResourceUrlAndConsumerKeyAndConsumerSecret_returnCalendars()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getCalendars","user":"eyeos"}}';
        $metadataOut = '[{"status": "NEW", "description": "Llevar justificante", "user": "eyeos", "timezone": 0, "type": "calendar", "cloud": "Stacksync", "name": "personal"}]';
        $this->exerciseGetCalendars($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: getCalendars
     * when: called
     * with: cloudAndTokenAndUser
     * should: returnCalendars
     */
    public function test_getCalendars_called_cloudAndTokenAndUser_returnCalendars()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getCalendars","user":"eyeos"}}';
        $metadataOut = '[{"status": "NEW", "description": "Llevar justificante", "user": "eyeos", "timezone": 0, "type": "calendar", "cloud": "Stacksync", "name": "personal"}]';
        $this->exerciseGetCalendars($metadataIn,$metadataOut);
    }

    /**
     * method: getCalendars
     * when: called
     * with: cloudAndTokenAndUserAndResourceUrl
     * should: returnException
     */
    public function test_getCalendars_called_cloudAndTokenAndUserAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getCalendars","user":"eyeos"}}';
        $metadataOut = 400;
        $this->exerciseGetCalendars($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: getCalendars
     * when: called
     * with: cloudAndTokenAndUserAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_getCalendars_called_cloudAndTokenAndUserAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getCalendars","user":"eyeos"}}';
        $metadataOut = 400;
        $this->exerciseGetCalendars($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: getCalendars
     * when: called
     * with: cloudAndTokenAndUser
     * should: returnException
     */
    public function test_getCalendars_called_cloudAndTokenAndUser_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getCalendars","user":"eyeos"}}';
        $metadataOut = 400;
        $this->exerciseGetCalendars($metadataIn,$metadataOut);
    }

    /**
     * method: getCalendarsAndEvents
     * when: called
     * with: cloudAndTokenAndUserAndResourceUrl
     * should: returnCalendarsAndEvents
     */
    public function test_getCalendarsAndEvents_called_cloudAndTokenAndUserAndResourceUrl_returnCalendarsAndEvents()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getCalendarsAndEvents","user":"eyeos"}}';
        $metadataOut = '[{"status": "NEW", "description": "Llevar justificante", "user": "eyeos", "timezone": 0, "type": "calendar", "cloud": "Stacksync", "name": "personal"}, {"status": "NEW", "description": "Llevar justificante", "location": "Barcelona", "finalvalue": "0", "timeend": "201419170000", "timestart": "201419160000", "isallday": 0, "user": "eyeos", "finaltype": "1", "calendar": "personal", "repetition": "None", "type": "event", "cloud": "Stacksync", "subject": "VisitaMedico"}]';
        $this->exerciseGetCalendarsAndEvents($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: getCalendarsAndEvents
     * when: called
     * with: cloudAndTokenAndUserAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnCalendarsAndEvents
     */
    public function test_getCalendarsAndEvents_called_cloudAndTokenAndUserAndResourceUrlAndConsumerKeyAndConsumerSecret_returnCalendarsAndEvents()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getCalendarsAndEvents","user":"eyeos"}}';
        $metadataOut = '[{"status": "NEW", "description": "Llevar justificante", "user": "eyeos", "timezone": 0, "type": "calendar", "cloud": "Stacksync", "name": "personal"}, {"status": "NEW", "description": "Llevar justificante", "location": "Barcelona", "finalvalue": "0", "timeend": "201419170000", "timestart": "201419160000", "isallday": 0, "user": "eyeos", "finaltype": "1", "calendar": "personal", "repetition": "None", "type": "event", "cloud": "Stacksync", "subject": "VisitaMedico"}]';
        $this->exerciseGetCalendarsAndEvents($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: getCalendarsAndEvents
     * when: called
     * with: cloudAndTokenAndUser
     * should: returnCalendarsAndEvents
     */
    public function test_getCalendarsAndEvents_called_cloudAndTokenAndUser_returnCalendarsAndEvents()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getCalendarsAndEvents","user":"eyeos"}}';
        $metadataOut = '[{"status": "NEW", "description": "Llevar justificante", "user": "eyeos", "timezone": 0, "type": "calendar", "cloud": "Stacksync", "name": "personal"}, {"status": "NEW", "description": "Llevar justificante", "location": "Barcelona", "finalvalue": "0", "timeend": "201419170000", "timestart": "201419160000", "isallday": 0, "user": "eyeos", "finaltype": "1", "calendar": "personal", "repetition": "None", "type": "event", "cloud": "Stacksync", "subject": "VisitaMedico"}]';
        $this->exerciseGetCalendarsAndEvents($metadataIn,$metadataOut);
    }

    /**
     * method: getCalendarsAndEvents
     * when: called
     * with: cloudAndTokenAndUserAndResourceUrl
     * should: returnException
     */
    public function test_getCalendarsAndEvents_called_cloudAndTokenAndUserAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getCalendarsAndEvents","user":"eyeos"}}';
        $metadataOut = 400;
        $this->exerciseGetCalendarsAndEvents($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: getCalendarsAndEvents
     * when: called
     * with: cloudAndTokenAndUserAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_getCalendarsAndEvents_called_cloudAndTokenAndUserAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getCalendarsAndEvents","user":"eyeos"}}';
        $metadataOut = 400;
        $this->exerciseGetCalendarsAndEvents($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: getCalendarsAndEvents
     * when: called
     * with: cloudAndTokenAndUser
     * should: returnException
     */
    public function test_getCalendarsAndEvents_called_cloudAndTokenAndUser_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getCalendarsAndEvents","user":"eyeos"}}';
        $metadataOut = 400;
        $this->exerciseGetCalendarsAndEvents($metadataIn,$metadataOut);
    }

    /**
     * method: deleteCalendarsUser
     * when: called
     * with: cloudAndTokenAndUserAndResourceUrl
     * should: returnDeleteCorrect
     */
    public function test_deleteCalendarsUser_called_cloudAndTokenAndUserAndResourceUrl_returnDeleteCorrect()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteCalendarsUser","user":"eyeos"}}';
        $metadataOut = '{"delete": true}';
        $this->exerciseDeleteCalendarsUser($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: deleteCalendarsUser
     * when: called
     * with: cloudAndTokenAndUserAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnDeleteCorrect
     */
    public function test_deleteCalendarsUser_called_cloudAndTokenAndUserAndResourceUrlAndConsumerKeyAndConsumerSecret_returnDeleteCorrect()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteCalendarsUser","user":"eyeos"}}';
        $metadataOut = '{"delete": true}';
        $this->exerciseDeleteCalendarsUser($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: deleteCalendarsUser
     * when: called
     * with: cloudAndTokenAndUser
     * should: returnDeleteCorrect
     */
    public function test_deleteCalendarsUser_called_cloudAndTokenAndUser_returnDeleteCorrect()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteCalendarsUser","user":"eyeos"}}';
        $metadataOut = '{"delete": true}';
        $this->exerciseDeleteCalendarsUser($metadataIn,$metadataOut);
    }

    /**
     * method: deleteCalendarsUser
     * when: called
     * with: cloudAndTokenAndUserAndResourceUrl
     * should: returnException
     */
    public function test_deleteCalendarsUser_called_cloudAndTokenAndUserAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteCalendarsUser","user":"eyeos"}}';
        $metadataOut = 400;
        $this->exerciseDeleteCalendarsUser($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: deleteCalendarsUser
     * when: called
     * with: cloudAndTokenAndUserAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_deleteCalendarsUser_called_cloudAndTokenAndUserAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteCalendarsUser","user":"eyeos"}}';
        $metadataOut = 400;
        $this->exerciseDeleteCalendarsUser($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: deleteCalendarsUser
     * when: called
     * with: cloudAndTokenAndUser
     * should: returnException
     */
    public function test_deleteCalendarsUser_called_cloudAndTokenAndUser_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"deleteCalendarsUser","user":"eyeos"}}';
        $metadataOut = 400;
        $this->exerciseDeleteCalendarsUser($metadataIn,$metadataOut);
    }

    /**
     * method: getControlCalendarCloud
     * when: called
     * with: ValidCloud
     * should: returnMetadata
     */
    public function test_getControlCalendarCloud_called_Valid_Cloud_returnMetadata()
    {
        $metadataIn = '{"config":{"type":"calendar","cloud":"Stacksync"}}';
        $metadataOut = '{"calendar":"true"}';
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->getControlCalendarCloud("Stacksync");
        $this->assertEquals(json_decode($metadataOut),$actual);
    }

    /**
     * method: lockFile
     * when: called
     * with: cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndTimeLimitAndResourceUrl
     * should: returnLockCorrect
     */
    public function test_lockFile_called_cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndTimeLimitAndResourceUrl_returnLockCorrect()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"lockFile","id":"2150","user":"eyeos","ipserver":"192.168.56.101","datetime":"2015-05-12 10:50:00","timelimit":10}}';
        $metadataOut = '{"lockFile":true}';
        $this->exerciseLockFile($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: lockFile
     * when: called
     * with: cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndTimeLimitAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnLockCorrect
     */
    public function test_lockFile_called_cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndTimeLimitAndResourceUrlAndConsumerKeyAndConsumerSecret_returnLockCorrect()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"lockFile","id":"2150","user":"eyeos","ipserver":"192.168.56.101","datetime":"2015-05-12 10:50:00","timelimit":10}}';
        $metadataOut = '{"lockFile":true}';
        $this->exerciseLockFile($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: lockFile
     * when: called
     * with: cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndTimeLimit
     * should: returnLockCorrect
     */
    public function test_lockFile_called_cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndTimeLimit_returnLockCorrect()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"lockFile","id":"2150","user":"eyeos","ipserver":"192.168.56.101","datetime":"2015-05-12 10:50:00","timelimit":10}}';
        $metadataOut = '{"lockFile":true}';
        $this->exerciseLockFile($metadataIn,$metadataOut);
    }

    /**
     * method: lockFile
     * when: called
     * with: cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndTimeLimitAndResourceUrl
     * should: returnException
     */
    public function test_lockFile_called_cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndTimeLimitAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"lockFile","id":"2150","user":"eyeos","ipserver":"192.168.56.101","datetime":"2015-05-12 10:50:00","timelimit":10}}';
        $metadataOut = 400;
        $this->exerciseLockFile($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: lockFile
     * when: called
     * with: cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndTimeLimitAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_lockFile_called_cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndTimeLimitAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"lockFile","id":"2150","user":"eyeos","ipserver":"192.168.56.101","datetime":"2015-05-12 10:50:00","timelimit":10}}';
        $metadataOut = 400;
        $this->exerciseLockFile($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: lockFile
     * when: called
     * with: cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndTimeLimit
     * should: returnException
     */
    public function test_lockFile_called_cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndTimeLimit_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"lockFile","id":"2150","user":"eyeos","ipserver":"192.168.56.101","datetime":"2015-05-12 10:50:00","timelimit":10}}';
        $metadataOut = 400;
        $this->exerciseLockFile($metadataIn,$metadataOut);
    }

    /**
     * method: updateDateTime
     * when: called
     * with: cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndResourceUrl
     * should: returnUpdateCorrect
     */
    public function test_updateDateTime_called_cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndResourceUrl_returnUpdateCorrect()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"updateDateTime","id":"2150","user":"eyeos","ipserver":"192.168.56.101","datetime":"2015-05-12 10:50:00"}}';
        $metadataOut = '{"updateFile":true}';
        $this->exerciseUpdateTime($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: updateDateTime
     * when: called
     * with: cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnUpdateCorrect
     */
    public function test_updateDateTime_called_cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndResourceUrlAndConsumerKeyAndConsumerSecret_returnUpdateCorrect()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"updateDateTime","id":"2150","user":"eyeos","ipserver":"192.168.56.101","datetime":"2015-05-12 10:50:00"}}';
        $metadataOut = '{"updateFile":true}';
        $this->exerciseUpdateTime($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: updateDateTime
     * when: called
     * with: cloudAndTokenAndIdAndUserAndIpServerAndDateTime
     * should: returnUpdateCorrect
     */
    public function test_updateDateTime_called_cloudAndTokenAndIdAndUserAndIpServerAndDateTime_returnUpdateCorrect()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"updateDateTime","id":"2150","user":"eyeos","ipserver":"192.168.56.101","datetime":"2015-05-12 10:50:00"}}';
        $metadataOut = '{"updateFile":true}';
        $this->exerciseUpdateTime($metadataIn,$metadataOut);
    }

    /**
     * method: updateDateTime
     * when: called
     * with: cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndResourceUrl
     * should: returnException
     */
    public function test_updateDateTime_called_cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"updateDateTime","id":"2150","user":"eyeos","ipserver":"192.168.56.101","datetime":"2015-05-12 10:50:00"}}';
        $metadataOut = 400;
        $this->exerciseUpdateTime($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: updateDateTime
     * when: called
     * with: cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_updateDateTime_called_cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"updateDateTime","id":"2150","user":"eyeos","ipserver":"192.168.56.101","datetime":"2015-05-12 10:50:00"}}';
        $metadataOut = 400;
        $this->exerciseUpdateTime($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: updateDateTime
     * when: called
     * with: cloudAndTokenAndIdAndUserAndIpServerAndDateTime
     * should: returnException
     */
    public function test_updateDateTime_called_cloudAndTokenAndIdAndUserAndIpServerAndDateTime_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"updateDateTime","id":"2150","user":"eyeos","ipserver":"192.168.56.101","datetime":"2015-05-12 10:50:00"}}';
        $metadataOut = 400;
        $this->exerciseUpdateTime($metadataIn,$metadataOut);
    }

    /**
     * method: unLockFile
     * when: called
     * with: cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndResourceUrl
     * should: returnUnLockCorrect
     */
    public function test_unLockFile_called_cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndResourceUrl_returnLockCorrect()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"unLockFile","id":"2150","user":"eyeos","ipserver":"192.168.56.101","datetime":"2015-05-12 10:50:00"}}';
        $metadataOut = '{"unLockFile":true}';
        $this->exerciseUnLockFile($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: unLockFile
     * when: called
     * with: cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnUnLockCorrect
     */
    public function test_unLockFile_called_cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndResourceUrlAndConsumerKeyAndConsumerSecret_returnLockCorrect()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"unLockFile","id":"2150","user":"eyeos","ipserver":"192.168.56.101","datetime":"2015-05-12 10:50:00"}}';
        $metadataOut = '{"unLockFile":true}';
        $this->exerciseUnLockFile($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: unLockFile
     * when: called
     * with: cloudAndTokenAndIdAndUserAndIpServerAndDateTime
     * should: returnUnLockCorrect
     */
    public function test_unLockFile_called_cloudAndTokenAndIdAndUserAndIpServerAndDateTime_returnLockCorrect()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"unLockFile","id":"2150","user":"eyeos","ipserver":"192.168.56.101","datetime":"2015-05-12 10:50:00"}}';
        $metadataOut = '{"unLockFile":true}';
        $this->exerciseUnLockFile($metadataIn,$metadataOut);
    }

    /**
     * method: unLockFile
     * when: called
     * with: cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndResourceUrl
     * should: returnException
     */
    public function test_unLockFile_called_cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"unLockFile","id":"2150","user":"eyeos","ipserver":"192.168.56.101","datetime":"2015-05-12 10:50:00"}}';
        $metadataOut = 400;
        $this->exerciseUnLockFile($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: unLockFile
     * when: called
     * with: cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_unLockFile_called_cloudAndTokenAndIdAndUserAndIpServerAndDateTimeAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"unLockFile","id":"2150","user":"eyeos","ipserver":"192.168.56.101","datetime":"2015-05-12 10:50:00"}}';
        $metadataOut = 400;
        $this->exerciseUnLockFile($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: unLockFile
     * when: called
     * with: cloudAndTokenAndIdAndUserAndIpServerAndDateTime
     * should: returnException
     */
    public function test_unLockFile_called_cloudAndTokenAndIdAndUserAndIpServerAndDateTime_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"unLockFile","id":"2150","user":"eyeos","ipserver":"192.168.56.101","datetime":"2015-05-12 10:50:00"}}';
        $metadataOut = 400;
        $this->exerciseUnLockFile($metadataIn,$metadataOut);
    }

    /**
     * method: getMetadataFile
     * when: called
     * with: cloudAndTokenAndIdAndResourceUrl
     * should: returnMetadataFile
     */
    public function test_getMetadataFile_called_cloudAndTokenAndIdAndResourceUrl_returnMetadataFile()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getMetadataFile","id":"2150"}}';
        $metadataOut = '[{"id":"2150","cloud":"Stacksync","user":"eyeos","ipserver":"192.168.56.101","datetime":"2015-05-12 10:50:00","status":"open"}]';
        $this->exerciseGetMetadataFile($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: getMetadataFile
     * when: called
     * with: cloudAndTokenAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnMetadataFile
     */
    public function test_getMetadataFile_called_cloudAndTokenAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret_returnMetadataFile()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getMetadataFile","id":"2150"}}';
        $metadataOut = '[{"id":"2150","cloud":"Stacksync","user":"eyeos","ipserver":"192.168.56.101","datetime":"2015-05-12 10:50:00","status":"open"}]';
        $this->exerciseGetMetadataFile($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: getMetadataFile
     * when: called
     * with: cloudAndTokenAndId
     * should: returnMetadataFile
     */
    public function test_getMetadataFile_called_cloudAndTokenAndId_returnMetadataFile()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getMetadataFile","id":"2150"}}';
        $metadataOut = '[{"id":"2150","cloud":"Stacksync","user":"eyeos","ipserver":"192.168.56.101","datetime":"2015-05-12 10:50:00","status":"open"}]';
        $this->exerciseGetMetadataFile($metadataIn,$metadataOut);
    }

    /**
     * method: getMetadataFile
     * when: called
     * with: cloudAndTokenAndIdAndResourceUrl
     * should: returnException
     */
    public function test_getMetadataFile_called_cloudAndTokenAndIdAndResourceUrl_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getMetadataFile","id":"2150"}}';
        $metadataOut = 400;
        $this->exerciseGetMetadataFile($metadataIn,$metadataOut,$this->urlAPISync);
    }

    /**
     * method: getMetadataFile
     * when: called
     * with: cloudAndTokenAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret
     * should: returnException
     */
    public function test_getMetadataFile_called_cloudAndTokenAndIdAndResourceUrlAndConsumerKeyAndConsumerSecret_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync","resource_url":"http:\/\/192.68.56.101\/","consumer_key":"b3af","consumer_secret":"c168"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getMetadataFile","id":"2150"}}';
        $metadataOut = 400;
        $this->exerciseGetMetadataFile($metadataIn,$metadataOut,$this->urlAPISync,$this->consumerKey,$this->consumerSecret);
    }

    /**
     * method: getMetadataFile
     * when: called
     * with: cloudAndTokenAndId
     * should: returnException
     */
    public function test_getMetadataFile_called_cloudAndTokenAndId_returnException()
    {
        $metadataIn = '{"config":{"cloud":"Stacksync"},"token":{"key":"ABCD","secret":"EFGH"},"metadata":{"type":"getMetadataFile","id":"2150"}}';
        $metadataOut = 400;
        $this->exerciseGetMetadataFile($metadataIn,$metadataOut);
    }

    private function exerciseGetMetadata($metadataIn,$metadataOut,$check,$file,$id,$contents = null,$url = null, $consumerKey = null, $consumerSecret = null)
    {
        $this->exerciseMockMetadata($metadataIn,$metadataOut);
        $result = $this->sut->getMetadata($this->cloud,$this->token,$file,$id,$contents,$url,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($check),$result);
    }

    private function exerciseUpdateMetadata($metadataIn, $metadataOut, $check, $file, $id, $name = null, $parent = null, $cloud = null,$resourceUrl = null, $consumerKey = null, $consumerSecret = null)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $result = $this->sut->updateMetadata($cloud, $this->token, $file, $id, $name, $parent,$resourceUrl,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($check), $result);
    }

    private function exerciseCreateMetadata($metadataIn,$metadataOut,$check,$file,$name,$parent = null,$pathAbsolute = null,$resourceUrl = null, $consumerKey = null, $consumerSecret = null)
    {
        $this->exerciseMockMetadata($metadataIn,$metadataOut);
        $result = $this->sut->createMetadata($this->cloud,$this->token,$file,$name,$parent,$pathAbsolute,$resourceUrl,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($check),$result);
    }

    private function exerciseUploadMetadata($metadataIn, $metadataOut, $check, $id, $path,$resourceUrl = null, $consumerKey = null, $consumerSecret = null)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $result = $this->sut->uploadMetadata($this->cloud, $this->token, $id, $path,$resourceUrl,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($check), $result);
    }

    private function exerciseDownloadMetadata($metadataOut, $metadataIn,$check, $id, $path, $cloud, $resourceUrl = null, $consumerKey = null, $consumerSecret = null)
    {
        $metadataIn = json_decode($metadataIn);
        $metadataIn = json_encode($metadataIn);
        $this->exerciseMockMetadata($metadataIn,$metadataOut);
        $result = $this->sut->downloadMetadata($cloud, $this->token, $id, $path, $resourceUrl, $consumerKey,$consumerSecret);
        $this->assertEquals($check, $result);
    }

    private function exerciseDeleteMetadata($metadataIn,$metadataOut,$check,$file,$id,$resourceUrl = null,$consumerKey = null,$consumerSecret = null)
    {
        $this->exerciseMockMetadata($metadataIn,$metadataOut);
        $result = $this->sut->deleteMetadata($this->cloud,$this->token,$file,$id,$resourceUrl,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($check),$result);
    }

    private function exerciseMockMetadata($metadataIn,$metadataOut)
    {
        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessOauthCredentials')
            ->with($metadataIn)
            ->will($this->returnValue($metadataOut));
    }

    private function exerciseListVersion($metadataIn,$metadataOut,$check,$id,$resourceUrl=null,$consumerKey=null,$consumerSecret=null)
    {
        $this->exerciseMockMetadata($metadataIn,$metadataOut);
        $result = $this->sut->listVersions($this->cloud,$this->token,$id,$resourceUrl,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($check),$result);
    }

    private function exerciseListUsersShare($metadataIn, $metadataOut, $check, $id, $resourceUrl = null, $consumerKey = null, $consumerSecret = null)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $result = $this->sut->getListUsersShare($this->cloud, $this->token, $id, $resourceUrl, $consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($check), $result);
    }

    private function exerciseShareFolder($metadataIn, $metadataOut, $check, $id, $list, $shared, $resourceUrl = null,$consumerKey = null, $consumerSecret = null)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $result = $this->sut->shareFolder($this->cloud, $this->token, $id, $list, $shared, $resourceUrl, $consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($check), $result);
    }

    private function exerciseGetFileVersionData($metadataOut, $check, $cloud=NULL,$resourceUrl = NULL, $consumerKey = NULL, $consumerSecret = NULL)
    {
        $metadataIn = new stdClass();
        $metadataIn->config = new stdClass();
        if ($cloud) {
            $metadataIn->config->cloud = $cloud;
        }

        if($resourceUrl) {
            $metadataIn->config->resource_url = $resourceUrl;
        }

        if($consumerKey && $consumerSecret) {
            $metadataIn->config->consumer_key = $consumerKey;
            $metadataIn->config->consumer_secret = $consumerSecret;
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
        $result = $this->sut->getFileVersionData($cloud, $this->token, "9873615", 2, "/home/eyeos/prueba3.pdf",$resourceUrl,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($check),$result);
    }

    private function exerciseInsertComment($metadataIn,$metadataOut,$resourceUrl = NULL, $consumerKey = NULL, $consumerSecret = NULL)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->insertComment($this->cloud,$this->token,"153","eyeos","prueba",$resourceUrl, $consumerKey, $consumerSecret);
        $this->assertEquals(json_decode($metadataOut),$actual);
    }

    private function exerciseDeleteComment($metadataIn,$metadataOut,$resourceUrl = NULL,$consumerKey = NULL,$consumerSecret = NULL)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->deleteComment($this->cloud,$this->token,"153","eyeos","201406201548",$resourceUrl,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($metadataOut),$actual);
    }

    private function exerciseGetComments($metadataIn,$metadataOut,$resourceUrl = NULL,$consumerKey = NULL,$consumerSecret = NULL)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->getComments($this->cloud,$this->token,"153",$resourceUrl,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($metadataOut),$actual);
    }

    private function exerciseInsertEvent($metadataIn,$metadataOut,$resourceUrl = NULL,$consumerKey = NULL,$consumerSecret = NULL)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->insertEvent($this->cloud,$this->token,"eyeos","personal",0,"201419160000","201419170000","None","1","0","Visita","Barcelona","Dentista","n",$resourceUrl,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($metadataOut),$actual);
    }

    private function exerciseDeleteEvent($metadataIn,$metadataOut,$resourceUrl = NULL,$consumerKey = NULL,$consumerSecret = NULL)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->deleteEvent($this->cloud,$this->token,"eyeos","personal","201419160000","201419170000",0,$resourceUrl,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($metadataOut),$actual);
    }

    private function exerciseUpdateEvent($metadataIn,$metadataOut,$resourceUrl = NULL,$consumerKey = NULL,$consumerSecret = NULL)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->updateEvent($this->cloud,$this->token,"eyeos","personal",0,"201419160000","201419170000","None","1","0","Visita","Barcelona","Dentista","n",$resourceUrl,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($metadataOut),$actual);
    }

    private function exerciseGetEvents($metadataIn,$metadataOut,$resourceUrl = NULL,$consumerKey = NULL,$consumerSecret = NULL)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->getEvents($this->cloud,$this->token,"eyeos","personal",$resourceUrl,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($metadataOut),$actual);
    }

    private function exerciseInsertCalendar($metadataIn,$metadataOut,$resourceUrl = NULL,$consumerKey = NULL,$consumerSecret = NULL)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->insertCalendar($this->cloud,$this->token,"eyeos","personal","CalendarioPersonal","0",$resourceUrl,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($metadataOut),$actual);
    }

    private function exerciseDeleteCalendar($metadataIn,$metadataOut,$resourceUrl = NULL,$consumerKey = NULL,$consumerSecret = NULL)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->deleteCalendar($this->cloud,$this->token,"eyeos","personal",$resourceUrl,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($metadataOut),$actual);
    }

    private function exerciseUpdateCalendar($metadataIn,$metadataOut,$resourceUrl = NULL,$consumerKey = NULL,$consumerSecret = NULL)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->updateCalendar($this->cloud,$this->token,"eyeos","personal","CalendarioLaboral","0",$resourceUrl,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($metadataOut),$actual);
    }

    private function exerciseGetCalendars($metadataIn,$metadataOut,$resourceUrl = NULL,$consumerKey = NULL,$consumerSecret = NULL)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->getCalendars($this->cloud,$this->token,"eyeos",$resourceUrl,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($metadataOut),$actual);
    }

    private function exerciseGetCalendarsAndEvents($metadataIn,$metadataOut,$resourceUrl = NULL,$consumerKey = NULL,$consumerSecret = NULL)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->getCalendarsAndEvents($this->cloud,$this->token,"eyeos",$resourceUrl,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($metadataOut),$actual);
    }

    private function exerciseDeleteCalendarsUser($metadataIn,$metadataOut,$resourceUrl = NULL,$consumerKey = NULL,$consumerSecret = NULL)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->deleteCalendarsUser($this->cloud,$this->token,"eyeos",$resourceUrl,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($metadataOut),$actual);
    }

    private function exerciseLockFile($metadataIn,$metadataOut,$resourceUrl = NULL,$consumerKey = NULL,$consumerSecret = NULL)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->lockFile($this->cloud,$this->token,"2150","eyeos","192.168.56.101","2015-05-12 10:50:00",10,$resourceUrl,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($metadataOut),$actual);
    }

    private function exerciseUpdateTime($metadataIn,$metadataOut,$resourceUrl = NULL,$consumerKey = NULL,$consumerSecret = NULL)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->updateDateTime($this->cloud,$this->token,"2150","eyeos","192.168.56.101","2015-05-12 10:50:00",$resourceUrl,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($metadataOut),$actual);
    }

    private function exerciseUnLockFile($metadataIn,$metadataOut,$resourceUrl = NULL,$consumerKey = NULL,$consumerSecret = NULL)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->unLockFile($this->cloud,$this->token,"2150","eyeos","192.168.56.101","2015-05-12 10:50:00",$resourceUrl,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($metadataOut),$actual);
    }

    private function exerciseGetMetadataFile($metadataIn,$metadataOut,$resourceUrl = NULL,$consumerKey = NULL,$consumerSecret = NULL)
    {
        $this->exerciseMockMetadata($metadataIn, $metadataOut);
        $actual = $this->sut->getMetadataFile($this->cloud,$this->token,"2150",$resourceUrl,$consumerKey,$consumerSecret);
        $this->assertEquals(json_decode($metadataOut),$actual);
    }
}
?>