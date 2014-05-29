<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28/05/14
 * Time: 10:43
 */

class ApiProvider
{
    private $accessorProvider;

    public function __construct(AccessorProvider $accessorProvider = NULL)
    {
        if(!$accessorProvider) $accessorProvider = new AccessorProvider();
        $this->accessorProvider = $accessorProvider;
    }

    public function getMetadata($token,$file,$id,$contents = null)
    {
        $request = $this->getRequest('get',$token);
        $request->metadata->file = $file;
        $request->metadata->id = $id;
        $request->metadata->contents = $contents;
        return $this->exerciseMetadata($request);
    }

    public function updateMetadata($token,$file,$id,$name = null,$parent = null)
    {
        $request = $this->getRequest('update',$token);
        $request->metadata->file = $file;
        $request->metadata->id = $id;
        $request->metadata->name = $name;
        $request->metadata->parent = $parent;
        return $this->exerciseMetadata($request);
    }

    public function createMetadata($token,$file,$name,$parent = null)
    {
        $request = $this->getRequest('create',$token);
        $request->metadata->file = $file;
        $request->metadata->name = $name;
        $request->metadata->parent = $parent;
        return $this->exerciseMetadata($request);
    }

    public function uploadMetadata($token,$id,$path)
    {
        $request = $this->getRequest('upload',$token);
        $request->metadata->id = $id;
        $request->metadata->path = $path;
        return $this->exerciseMetadata($request);
    }

    public function downloadMetadata($token,$id)
    {
        $resp = json_decode('{"error":-1}');
        $request = $this->getRequest('download',$token);
        $request->metadata->id = $id;
        $result = $this->accessorProvider->getProcessOauthCredentials(json_encode($request));

        if($result) {
            if(!($result === 'false' || $result === '403')) {
                $resp = $result;
            }  else if($result === '403'){
                $resp = json_decode('{"error":403}');
            }
        }

        return $resp;
    }

    public function deleteMetadata($token,$file,$id)
    {
        $request = $this->getRequest('delete',$token);
        $request->metadata->file = $file;
        $request->metadata->id = $id;
        return $this->exerciseMetadata($request);
    }

    private function getRequest($type,$token)
    {
        $request = new stdClass();
        $request->token = new stdClass();
        $request->token->key = $token->key;
        $request->token->secret = $token->secret;
        $request->metadata = new stdClass();
        $request->metadata->type = $type;
        return $request;
    }

    private function exerciseMetadata($request)
    {
        $resp = json_decode('{"error":-1}');
        $result = $this->accessorProvider->getProcessOauthCredentials(json_encode($request));
        if($result) {
            if($result !== 'false' && $result !== '403') {
                $resp = json_decode($result);
            } else if($result === '403'){
                $resp = json_decode('{"error":403}');
            } else if($result === 'true') {
                $resp = json_decode('{"status":true}');
            }
        }

        return $resp;
    }


}

?>