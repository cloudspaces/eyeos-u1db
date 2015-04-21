<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28/05/14
 * Time: 16:05
 */

class ApiManager
{
    private $accessorProvider;
    private $apiProvider;
    private $filesProvider;

    public function __construct(AccessorProvider $accessorProvider = NULL, ApiProvider $apiProvider = NULL, FilesProvider $filesProvider = NULL)
    {
        if(!$accessorProvider) $accessorProvider = new AccessorProvider();
        $this->accessorProvider = $accessorProvider;

        if(!$apiProvider) $apiProvider = new ApiProvider();
        $this->apiProvider = $apiProvider;

        if(!$filesProvider) $filesProvider = new FilesProvider();
        $this->filesProvider = $filesProvider;
    }

    public function getMetadata($cloud, $token, $id, $path, $user)
    {
        $pathMetadata = $this->getPathU1db($path, $cloud);
        $metadata = $this->apiProvider->getMetadata($cloud, $token, false, $id, true);
        $this->addPathMetadata($metadata, $pathMetadata);
        $respuesta = json_encode($metadata);
        $files = array();
        if(!isset($metadata->error)) {
            if(array_key_exists('contents', $metadata) && count($metadata->contents) > 0) {
                $files = $metadata->contents;
                if ($id === 'root') {
                    unset($metadata->contents);
                    array_push($files, $metadata);
                }
            }
            //$this->addPathMetadata($files,$pathMetadata);
            $u1dbList = new stdClass();
            $u1dbList->id = $id == 'root'?'null':$id;
            $u1dbList->user_eyeos = $user;
            $u1dbList->cloud = $cloud;
            $u1dbList->path = $pathMetadata;

            $u1dbResult = $this->callProcessU1db('select', $u1dbList);
            if($u1dbResult === '[]') {
                foreach($files as $file) {
                    $insert = true;
                    if($file->id !== 'null') {
                        if ($file->status !== 'DELETED') {
                            $insert = $this->filesProvider->createFile($path . "/" . $file->filename, $file->is_folder);
                        } else {
                            $insert = false;
                        }
                    }
                    if($insert) {
                        $this->callProcessU1db('insert', $this->setUserEyeos($file, $user, $cloud));
                    }
                }
            } else {
                $dataU1db = json_decode($u1dbResult);
                if ($dataU1db){
                    for($i = 0; $i < count($files); $i++) {
                        $delete = $files[$i]->status === 'DELETED' ? true : false;
                        if($this->search($dataU1db, "id", $files[$i]->id) === false){
                            if(!$delete &&  $files[$i]->id !== 'null') {
                                if($this->filesProvider->createFile($path . "/" . $files[$i]->filename, $files[$i]->is_folder)) {
                                    $this->callProcessU1db('insert', $this->setUserEyeos($files[$i], $user, $cloud));
                                }
                            }
                        } else {
                            if(!$delete) {
                                $filenameDb = $this->getValue($dataU1db, "id", $files[$i]->id, "filename");
                                if ($filenameDb !== $files[$i]->filename){
                                    if($this->filesProvider->renameFile($path . "/" . $filenameDb, $files[$i]->filename)) {
                                        $lista = array();
                                        array_push($lista, json_decode('{"parent_old":"' . $files[$i]->parent_id . '"}'));
                                        array_push($lista, $this->setUserEyeos($files[$i], $user, $cloud));
                                        $this->callProcessU1db('update', $lista);
                                    }
                                }
                            } else {
                                $this->callProcessU1db('deleteFolder', $this->setUserEyeos($files[$i], $user, $cloud));
                                $this->filesProvider->deleteFile($path . "/" . $files[$i]->filename, $files[$i]->is_folder);
                            }
                        }
                    }
                    for($i = 0; $i < count($dataU1db); $i++) {
                        if($this->search($files, "id", $dataU1db[$i]->id) === false && $metadata->id !== $dataU1db[$i]->id){
                            if($this->filesProvider->deleteFile($path . "/" . $dataU1db[$i]->filename, $dataU1db[$i]->is_folder)) {
                                 $this->callProcessU1db('deleteFolder', $dataU1db[$i]);
                            }
                        }
                    }
                }
            }
        }
        return $respuesta;
    }

    public function getSkel($cloud, $token, $file, $id, &$metadatas, $path, $pathAbsolute, $pathEyeos) {
        $contents = $file == false ? true : null;
        $metadata = $this->apiProvider->getMetadata($cloud, $token, $file, $id, $contents);
        if(!isset($metadata->error)) {
            $metadata->pathAbsolute = $pathAbsolute;
            $metadata->path = $path;
            $metadata->pathEyeos = $pathEyeos . "/" . $metadata->filename;
            if($metadata->is_folder) {
                $path = $metadata->id == 'null' ? '/' : $path . $metadata->filename . '/';
                for ($i=0; $i<count($metadata->contents); $i++){
                    $this->getSkel($cloud, $token, !$metadata->contents[$i]->is_folder, $metadata->contents[$i]->id, $metadatas, $path, null, $metadata->pathEyeos);
                }
            }
            unset($metadata->contents);
        }
        array_push($metadatas, $metadata);
    }

    public function createMetadata($cloud, $token, $user, $file, $name, $parent_id, $path, $pathAbsolute=NULL)
    {
        $result['status'] = 'KO';
        $result['error'] = -1;
        $metadata = $this->apiProvider->getMetadata($cloud, $token, $file, $parent_id, true);
        if($metadata) {
            if(!isset($metadata->error)) {
                if(isset($metadata->contents)) {
                    $id = null;
                    foreach($metadata->contents as $data) {
                        if($data->filename == $name) {
                            $id = $data->id;
                            break;
                        }
                    }
                    if($id === null) {
                        $newMetadata = $this->apiProvider->createMetadata($cloud, $token, $file, $name, $parent_id, $pathAbsolute);
                        if(!isset($newMetadata->error)) {
                            $this->addPathMetadata($newMetadata, $path);
                            if($this->callProcessU1db('insert', $this->setUserEyeos($newMetadata, $user, $cloud)) == 'true') {
                                $ok = true;
                                if($file) {
                                    $lista = new stdClass();
                                    $lista->id = "" . $newMetadata->id;
                                    $lista->cloud = $cloud;
                                    $lista->user_eyeos = $user;
                                    $lista->version = $newMetadata->version;
                                    $lista->recover = false;
                                    $resultU1db = $this->callProcessU1db("insertDownloadVersion", $lista);
                                    if($resultU1db !== 'true') {
                                        $ok = false;
                                    }
                                }
                                if($ok) {
                                    $result['status'] = 'OK';
                                    unset($result['error']);
                                }
                            }
                        } else {
                            $result['error'] = $newMetadata->error;
                        }
                    } else {
                        if($file) {
                            $resp = $this->apiProvider->uploadMetadata($cloud, $token, $id, $pathAbsolute);
                            if(isset($resp->status) && $resp->status == true) {
                                $changedMetadata = $this->apiProvider->getMetadata($cloud, $token, $file, $id);
                                if(!isset($changedMetadata->error)) {
                                    $this->addPathMetadata($changedMetadata, $path);
                                    $metadataUpdate = array();
                                    $old = new stdClass();
                                    $old->parent_old = $changedMetadata->parent_id;
                                    array_push($metadataUpdate, $old);
                                    array_push($metadataUpdate, $this->setUserEyeos($changedMetadata, $user, $cloud));
                                    if($this->callProcessU1db('update', $metadataUpdate) == 'true') {
                                        $lista = new stdClass();
                                        $lista->id = "" . $changedMetadata->id;
                                        $lista->user_eyeos = $user;
                                        $lista->cloud = $cloud;
                                        $lista->version = $changedMetadata->version;
                                        $lista->recover = false;
                                        $resultU1db = $this->callProcessU1db("updateDownloadVersion", $lista);
                                        if($resultU1db === 'true') {
                                            $result['status'] = 'OK';
                                            unset($result['error']);
                                        }
                                    }
                                } else {
                                    $result['error'] = $changedMetadata->error;
                                }
                            } else {
                               if(isset($resp->error)) {
                                   $result['error'] = $resp->error;
                               }
                            }
                        }
                    }
                }
            } else {
                $result['error'] = $metadata->error;
            }
        }
        return $result;
    }

    public function downloadMetadata($token, $id, $path, $user, $isTmp=false, $cloud = NULL)
    {
        $result[ 'status' ] = 'KO';
        $result[ 'error' ] = -1;
        $metadata = $this->apiProvider->getMetadata($cloud, $token, true, $id);
        $insert = false;
        $type = '';

        if(!isset($metadata->error) && count($metadata) > 0) {
            $lista = new stdClass();
            $lista->id = "" . $id;
            $lista->user_eyeos = $user;
            $lista->cloud = $cloud;
            $metadataU1db = $this->callProcessU1db('getDownloadVersion', $lista);
            if($metadataU1db !== "null") {
                $metadataU1db = json_decode($metadataU1db);
                if($metadataU1db) {
                    if($metadata->version != $metadataU1db->version && $metadataU1db->recover === false) {
                        $insert = true;
                        $type = 'updateDownloadVersion';
                    } else {
                        $result[ 'status' ] = 'OK';
                        $result[ 'local' ] = true;
                        unset($result[ 'error' ]);
                    }
                }
            } else {
                $insert = true;
                $type = 'insertDownloadVersion';
            }

            if($insert) {
                $content = $this->apiProvider->downloadMetadata($cloud, $token, $id, $path);
                if(!isset($content->error)) {
                    if ($isTmp == false) {
                        $lista = new stdClass();
                        $lista->id = "" . $id;
                        $lista->cloud = $cloud;
                        $lista->user_eyeos = $user;
                        $lista->version = $metadata->version;
                        $lista->recover = false;
                        $resultU1db = $this->callProcessU1db($type, $lista);
                        if($resultU1db === 'true') {
                            $result[ 'status' ] = 'OK';
                            unset($result[ 'error' ]);
                        }
                    } else {
                        $result[ 'status' ] = 'OK';
                        unset($result[ 'error' ]);
                    }
                } else {
                    $result[ 'error' ] = $content->error;
                }
            }

        } else{
            $result[ 'error' ] = $metadata->error;
        }

        return $result;
    }

    public function deleteMetadata($cloud,$token,$file,$id,$user,$path)
    {
        $result['status'] = 'KO';
        $result['error'] = -1;
        $metadata = $this->apiProvider->deleteMetadata($cloud,$token,$file,$id);
        if(!isset($metadata->error)) {
            $lista = new stdClass();
            $lista->id = "" . $id;
            $lista->user_eyeos = $user;
            $lista->cloud = $cloud;
            $resultU1db = $this->callProcessU1db("recursiveDeleteVersion",$lista);
            if($resultU1db === 'true') {
                $data = new stdClass();
                $data->id = "" . $id;
                $data->user_eyeos = $user;
                $data->cloud = $cloud;
                $data->path = $this->getPathU1db($path,$cloud);

                if($this->callProcessU1db('deleteFolder',$data) === 'true') {
                    $result['status'] = 'OK';
                    unset($result['error']);
                }
            }

        } else {
            $result['error'] = $metadata->error;
        }

        return $result;
    }

    public function renameMetadata($cloud, $token, $file, $id, $name, $path, $user, $parent=NULL)
    {
        $result['status'] = 'KO';
        $result['error'] = -1;
        $metadata = $this->apiProvider->updateMetadata($cloud, $token, $file, $id, $name, $parent);
        if (!isset($metadata->error)) {
            $this->addPathMetadata($metadata, $path);
            if($this->callProcessU1db('rename', $this->setUserEyeos($metadata, $user, $cloud)) == 'true') {
                $result['status'] = 'OK';
                unset($result['error']);
            }
        } else {
            $result['error'] = $metadata->error;
        }
        return $result;
    }

    public function moveMetadata($cloud, $token, $file, $id, $pathOrig, $pathDest, $user, $parent, $filenameOld, $filenameNew = null)
    {
        $result[ 'status' ] = 'KO';
        $result[ 'error' ] = -1;
        $metadata = $this->apiProvider->updateMetadata($cloud, $token, $file, $id, $filenameNew ? $filenameNew : $filenameOld, $parent);

        if(!isset($metadata->error)) {
            $delete = new stdClass();
            $delete->id = $id;
            $delete->user_eyeos = $user;
            $delete->cloud = $cloud;
            $delete ->path = $this->getPathU1db($pathOrig, $cloud);

            if($this->callProcessU1db('deleteFolder', $delete) == 'true') {
                $delete = $this->filesProvider->deleteFile($pathOrig . '/' . $filenameOld, !$file);
                if($delete) {
                    $metadata = $this->setUserEyeos($metadata, $user, $cloud);
                    $this->addPathMetadata($metadata,$this->getPathU1db($pathDest, $cloud));
                    if($this->callProcessU1db('insert', $metadata) == 'true') {
                        $this->filesProvider->createFile($pathDest . '/' . $metadata->filename,!$file);
                        $result[ 'status' ] = 'OK';
                        unset($result[ 'error' ]);
                    }
                }
            }
        }
        return $result;
    }

    public function recursiveDeleteVersion($cloud, $id, $user) {
        $result[ 'status' ] = 'KO';
        $result[ 'error' ] = -1;
        $lista = new stdClass();
        $lista->id = "" . $id;
        $lista->user_eyeos = $user;
        $lista->cloud = $cloud;
        $resultU1db = $this->callProcessU1db("recursiveDeleteVersion", $lista);
        if($resultU1db === 'true') {
            $result[ 'status' ] = 'OK';
            unset($result[ 'error' ]);
        }
        return $result;
    }

    public function callProcessU1db($type, $lista, $credentials=NULL)
    {
        $json = new stdClass();
        $json->type = $type;
        $json->lista = array();
        if ($type == 'update') {
            $json->lista = $lista;
        } else {
            array_push($json->lista, $lista);
        }
        if ($credentials) {
            $json->credentials = $credentials;
        }
        return $this->accessorProvider->getProcessDataU1db(json_encode($json));
    }

    public function deleteMetadataUser($user, $cloud=NULL)
    {
        $file = array();
        $file['user_eyeos'] = $user;
        if ($cloud) {
            $file['cloud'] = $cloud;
        }
        return json_decode($this->callProcessU1db("deleteMetadataUser",$file));
    }

    public function listVersions($cloud,$token,$id,$user)
    {
        $result['status'] = 'KO';
        $result['error'] = -1;
        $metadata = $this->apiProvider->listVersions($cloud,$token,$id);
        if(!isset($metadata->error)) {
            $lista = new stdClass();
            $lista->id = "" . $id;
            $lista->user_eyeos = $user;
            $lista->cloud = $cloud;
            $metadataU1db = $this->callProcessU1db('getDownloadVersion',$lista);
            if($metadataU1db !== "null") {
                $metadataU1db = json_decode($metadataU1db);
                if($metadataU1db) {
                    foreach($metadata as $file) {
                        if($file->version == $metadataU1db->version) {
                            $file->enabled = true;
                            break;
                        }
                    }

                    $result = json_encode($metadata);
                }
            } else {
                if(count($metadata) > 0) {
                    $metadata[0]->enabled = true;
                    $result = json_encode($metadata);
                }
            }

        } else {
            $result['error'] = $metadata->error;
        }
        return $result;
    }

    public function getFileVersionData($cloud, $token, $id, $version, $path, $user)
    {
        $result['status'] = 'KO';
        $result['error'] = -1;
        $type = null;

        $lista = new stdClass();
        $lista->id = "" . $id;
        $lista->user_eyeos = $user;
        $lista->cloud = $cloud;
        $metadataU1db = $this->callProcessU1db('getDownloadVersion', $lista);
        if($metadataU1db !== "null") {
            $metadataU1db = json_decode($metadataU1db);
            if($metadataU1db) {
                $type = 'updateDownloadVersion';
            }
        } else {
            $type = 'insertDownloadVersion';
        }

        if($type) {
            $metadata = $this->apiProvider->getFileVersionData($cloud, $token, $id, $version, $path);
            if(!isset($metadata->error)) {
                $lista = new stdClass();
                $lista->id = "" . $id;
                $lista->cloud = $cloud;
                $lista->user_eyeos = $user;
                $lista->version = $version;
                $lista->recover = true;
                $metadataU1db = $this->callProcessU1db($type, $lista);
                if($metadataU1db == "true") {
                    $result['status'] = 'OK';
                    unset($result['error']);
                }
            } else {
                $result['error'] = $metadata->error;
            }
        }
        return $result;
    }

    public function getListUsersShare($token,$id)
    {
        $result['status'] = 'KO';
        $result['error'] = -1;
        $metadata = $this->apiProvider->getListUsersShare($token,$id);
        if (!isset($metadata->error)){
            $result = json_encode($metadata);
        } else {
            $result['error'] = $metadata->error;
        }
        return $result;
    }

    public function shareFolder($token,$id,$list)
    {
        $result['status'] = 'KO';
        $result['error'] = -1;
        $metadata = $this->apiProvider->shareFolder($token,$id,$list);
        if (!isset($metadata->error)) {
            $result['status'] = 'OK';
            unset($result['error']);
        } else {
            $result['error'] = $metadata->error;
        }
        return $result;
    }

    public function getCloudsList()
    {
        $result['status'] = 'KO';
        $result['error'] = -1;
        $metadata = $this->apiProvider->getCloudsList();
        if (!isset($metadata->error)) {
            $result = json_encode($metadata);
        } else {
            $result['error'] = $metadata->error;
        }
        return $result;
    }

    public function getOauthUrlCloud($cloud)
    {
        $result['status'] = 'KO';
        $result['error'] = -1;
        $metadata = $this->apiProvider->getOauthUrlCloud($cloud);
        if (!isset($metadata->error)) {
            $result = $metadata;
        } else {
            $result['error'] = $metadata->error;
        }
        return $result;
    }

    public function getControlVersionCloud($cloud)
    {
        $result['status'] = 'KO';
        $result['error'] = -1;
        $metadata = $this->apiProvider->getControlVersionCloud($cloud);
        if (!isset($metadata->error)) {
            $result = $metadata;
        } else {
            $result['error'] = $metadata->error;
        }
        return $result;
    }

    private function setUserEyeos($metadata, $user, $cloud = NULL)
    {
        $aux = new stdClass();
        if($cloud) {
            $aux->cloud = $cloud;
        }
        $aux->user_eyeos = $user;
        $metadata = (object)array_merge((array)$aux, (array)$metadata);
        return $metadata;
    }

    private function search($array, $key, $value)
    {
        if (is_array($array)) {
            foreach($array as $data) {
                if($data->$key == $value){
                    return true;
                    break;
                }
            }
        }
        return false;
    }

    private function getValue($array, $key, $value, $keyFind)
    {
        $name = '';
        if (is_array($array)) {
            foreach($array as $data) {
                if($data->$key == $value){
                    $name = $data->$keyFind;
                    break;
                }
            }
        }
        return $name;
    }

    private function getPathU1db($path,$cloud = NULL)
    {
        if($cloud) {
            preg_match('/home:\/\/~(.*)\/Cloudspaces\/' . $cloud . '/', $path, $match);
            $username = $match[1];
            preg_match("/home:\/\/~$username\/Cloudspaces\/$cloud(.*)/", $path, $match);
            $pathnew = $match[1] . '/';

        } else {
            preg_match('/home:\/\/~(.*)\/Stacksync/', $path, $match);
            $username = $match[1];
            preg_match("/home:\/\/~$username\/Stacksync(.*)/", $path, $match);
            $pathnew = $match[1] . '/';
        }

        return $pathnew;
    }

    private function addPathMetadata(&$metadata,$path)
    {
        if (!isset($metadata->error)) {
            $metadata->path = $metadata->id == 'null'?'null':$path;
            if(isset($metadata->contents)) {
                foreach($metadata->contents as $dato) {
                    $dato->path = $path;
                }
            }
        }
    }
}


?>