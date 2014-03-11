<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/03/14
 * Time: 16:25
 */
if ($_GET["user"] && $_GET["password"]) {
    $user = $_GET["user"];
    $password = $_GET["password"];
} else {
    $user = "eyeos";
    $password = "eyeos";
}

if ($_GET["Id"]) {
    $fileId = htmlspecialchars($_GET["Id"]);
} else {
    $fileId = NULL;
}

listContent($fileId,$user,$password);


function listContent($fileId,$user,$password)
{
    $token = autentication($user,$password);
    $data_response = executeCurl($token);
    if (array_key_exists('access',$data_response)) {
        if (array_key_exists('token',$data_response->access) && array_key_exists('id',$data_response->access->token)) {
            $idToken = $data_response->access->token->id;
        }
        if (array_key_exists('serviceCatalog',$data_response->access) && count($data_response->access->serviceCatalog) > 0 &&
            array_key_exists('endpoints',$data_response->access->serviceCatalog[0]) &&
            array_key_exists('publicURL',$data_response->access->serviceCatalog[0]->endpoints[0])) {
            $url = $data_response->access->serviceCatalog[0]->endpoints[0]->publicURL;
        }
    }
    if (strlen($idToken) > 0 && strlen($url) > 0) {
        $metadata = metadata($fileId,$idToken,$url);
        $list = executeCurl($metadata);
        if (array_key_exists('error',$list)) {
            echo "Error: $list->error :: $list->description";
        } else {
            if (array_key_exists('filename',$list)) {
                if ($list->is_folder) {
                    echo "Directorio: $list->filename <br><br>";
                    if (array_key_exists('contents',$list)) {
                        if (count($list->contents) > 0) {
                            foreach($list->contents as $meta) {
                                if ($meta->is_folder) {
                                    $data = '<div><img src="/eyeos/extern/images/48x48/places/folder-documents.png" align="middle"/>';
                                } else {
                                    $data = '<div><img src="/eyeos/extern/images/48x48/mimetypes/text-plain.png" align="middle"/>';
                                }
                                $data .= 'Id: ' . $meta->file_id . ' -- Filename: ' . $meta->filename . ' -- Size: ' . $meta->size . '</div>';
                                echo $data;
                            }
                        } else {
                            echo "No contiene ni ficheros ni directorios<br>";
                        }
                    }
                } else {
                    echo "Fichero: $list->filename <br><br>";
                    echo "No se pueden encontrar dependencias de un archivo";
                }
            }
        }
    }
}

function executeCurl($datos) {
    $result = curl_exec($datos);
    return json_decode($result);
}

function metadata($id,$token,$http)
{
    $header = array();
    $header[0] = "X-Auth-Token: " . $token;
    $header[1] = "StackSync-api: true";
    $http .= '/stacksync/metadata';
    if ($id != NULL) {
        $http .= '?file_id=' . $id . '&list=true';
    }
    $ch = curl_init($http);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    return $ch;
}

function autentication($user,$password)
{
    $data = array();
    $data["auth"] = array();
    $data["auth"]["passwordCredentials"] = array();
    $data["auth"]["passwordCredentials"]["username"] = $user;
    $data["auth"]["passwordCredentials"]["password"] = $password;
    $data["auth"]["tenantName"] = $user;
    $data_string = json_encode($data);
    $ch = curl_init('http://cloudspaces.urv.cat:5000/v2.0/tokens');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
    return $ch;
}

?>