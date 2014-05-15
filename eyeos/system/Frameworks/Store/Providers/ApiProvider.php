<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/03/14
 * Time: 10:44
 */

class ApiProvider
{
    private $accessorProvider;
    private $dao;

    public function __construct(AccessorProvider $accessorProvider = NULL,EyeosDAO $dao = NULL)
    {
        if(!$accessorProvider) $accessorProvider = new AccessorProvider();
        $this->accessorProvider = $accessorProvider;

        if(!$dao) $dao = new EyeosDAO();
        $this->dao = $dao;
    }

    public function getMetadata($url, $tokenId, $fileId=NULL)
    {
        $url = $url . '/stacksync/metadata';
        if($fileId) $url .= '?file_id=' . $fileId . '&list=true';
        return $this->executeAccessor($url,$tokenId);
    }

    public function createFile($url,$tokenId,$filename,$file,$filesize,$parent = NULL)
    {
        $url = $url . '/stacksync/files?file_name=' . urlencode($filename);
        if($parent) $url .= '&parent=' . $parent;
        return $this->executeAccessor($url,$tokenId,$file,$filesize);
    }

    public function createFolder($url,$tokenId,$foldername,$parent = NULL)
    {
        $url = $url .'/stacksync/files?folder_name=' . urlencode($foldername);
        if($parent) $url .= '&parent=' . $parent;
        return $this->executeAccessor($url,$tokenId,null,null,'POST');
    }

    public function deleteComponent($url,$tokenId,$idComponent) {
        $result = false;
        $url .= '/stacksync/files?file_id=' . $idComponent;
        $metadata = $this->executeAccessor($url,$tokenId,null,null,'DELETE');

        if(array_key_exists("status",$metadata) && $metadata->status === 'DELETED') {
            $result = true;
        }

        return $result;
    }

    public function downloadFile($url,$tokenId,$idFile)
    {
        $url .= '/stacksync/files?file_id=' . $idFile;
        return $this->executeAccessor($url,$tokenId,null,null,null,true);
    }

    public function replaceNull($json) {
        if(array_key_exists("file_id",$json)) {
            if($json->file_id === NULL || strlen($json->file_id) == 0) {
                $json->file_id = 'null';
            } else {
                $json->file_id .= "";
            }
        }
        if(array_key_exists("parent_file_id",$json)) {
            if($json->parent_file_id === NULL || strlen($json->parent_file_id) == 0) {
                $json->parent_file_id = 'null';
            } else {
                $json->parent_file_id .= '';
            }
        }

        if(array_key_exists("path",$json)) {
            if($json->path !== NULL && strlen($json->path) > 0 && $json->path[strlen($json->path) - 1] !== '/') {
                $json->path .= '/';
            }
        }

        if(array_key_exists("contents",$json)) {
            for($i = 0;$i < count($json->contents);$i++) {
                if(array_key_exists("file_id",$json->contents[$i])) {
                    if($json->contents[$i]->file_id === NULL || strlen($json->contents[$i]->file_id) == 0) {
                        $json->contents[$i]->file_id = 'null';
                    } else {
                        $json->contents[$i]->file_id .= '';
                    }
                }
                if(array_key_exists("parent_file_id",$json->contents[$i])) {
                    if($json->contents[$i]->parent_file_id === NULL || strlen($json->contents[$i]->parent_file_id) == 0) {
                        $json->contents[$i]->parent_file_id = 'null';
                    } else {
                        $json->contents[$i]->parent_file_id .= '';
                    }
                }
                if(array_key_exists("path",$json->contents[$i])) {
                    if($json->contents[$i]->path !== NULL && strlen($json->contents[$i]->path) > 0 && $json->contents[$i]->path[strlen($json->contents[$i]->path) - 1] !== '/') {
                        $json->contents[$i]->path .= '/';
                    }
                }
            }
        }

        return $json;
    }

    public function executeAccessor($url,$tokenId,$file = NULL,$filesize = NULL,$request = NULL,$isDownload = false)
    {
        $settings = new Settings();
        $settings->setUrl($url);
        $header = array();
        $header[0] = "X-Auth-Token: " . $tokenId;
        $header[1] = "StackSync-api: true";
        $settings->setSslVerifyPeer(false);
        $settings->setHeader(false);
        $settings->setReturnTransfer(true);
        $settings->setHttpHeader($header);
        if(isset($request))
        {
            $settings->setCustomRequest($request);
        }

        if($file) {
            $settings->setPut(true);
            $settings->setInFile($file);
            $settings->setInFilesize($filesize);
            $settings->setBinaryTransfer(true);
        }

        $result = $this->accessorProvider->sendMessage($settings);
        if(!$isDownload) {
            $result = json_decode($result);
            if ($result) {
                $result = $this->replaceNull($result);
            }
        }
        return $result;
    }

    public function getToken($user)
    {
        $token = new Token();
        $token->setUserId($user);
        try {
            $this->dao->read($token);
        } catch(EyeResultNotFoundException $e) {}
        return $token;
    }
}

?>