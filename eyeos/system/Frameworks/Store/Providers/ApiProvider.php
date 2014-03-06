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

    public function __construct(AccessorProvider $accessorProvider = NULL)
    {
        if(!$accessorProvider) $accessorProvider = new AccessorProvider();
        $this->accessorProvider = $accessorProvider;
    }

    public function getMetadata($url, $tokenId, $fileId=NULL)
    {
        $url = $url . '/stacksync/metadata';
        if($fileId) $url .= '?file_id=' . $fileId . '&list=true';
        $settings = new Settings();
        $settings->setUrl($url);
        $header = array();
        $header[0] = "X-Auth-Token: " . $tokenId;
        $header[1] = "StackSync-api: true";
        $settings->setSslVerifyPeer(false);
        $settings->setHeader(false);
        $settings->setReturnTransfer(true);
        $settings->setHttpHeader($header);

        $result = json_decode($this->accessorProvider->sendMessage($settings));
        if($result) {
            $result = $this->replaceNull($result);
        }
        return $result;
    }

    private function getValue($value) {
        return $value?'true':'false';
    }

    public function replaceNull($json) {
        if(array_key_exists("file_id",$json)) {
            if($json->file_id === NULL) {
                $json->file_id = 'null';
            } else {
                $json->file_id .= "";
            }
        }
        if(array_key_exists("parent_file_id",$json)) {
            if($json->parent_file_id === NULL) {
                $json->parent_file_id = 'null';
            } else {
                $json->parent_file_id .= '';
            }
        }

        if(array_key_exists("contents",$json)) {
            for($i = 0;$i < count($json->contents);$i++) {
                if(array_key_exists("file_id",$json->contents[$i])) {
                    if($json->contents[$i]->file_id === NULL) {
                        $json->contents[$i]->file_id = 'null';
                    } else {
                        $json->contents[$i]->file_id .= '';
                    }
                }
                if(array_key_exists("parent_file_id",$json->contents[$i])) {
                    if($json->contents[$i]->parent_file_id === NULL) {
                        $json->contents[$i]->parent_file_id = 'null';
                    } else {
                        $json->contents[$i]->parent_file_id .= '';
                    }
                }
            }
        }

        return $json;
    }
}

?>