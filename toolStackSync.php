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
    if (array_key_exists('error',$data_response)) {
        echo "Error: " . $data_response->error->code . " :: " . $data_response->error->message;
    } else {
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
                        echo "<div style=\"font-family:'Verdana';font-size:15px;\"><p><b><span style='text-decoration:underline'>$list->filename</span></b>&nbsp;(Directorio)</p>";
                        if (array_key_exists('contents',$list)) {
                            $childrens = count($list->contents);
                            if ($childrens > 0) {
                                echo "N&uacute;mero de elementos contenidos: $childrens<br><br></div>";
                                echo "<table width=\"100%\" border=\"1\">";
                                echo "<tr style=\"font-family:'Verdana';font-size:15px;font-weight:bold;background-color:lightgrey\" align='center'><td  width=\"10%\">Tipo</td><td width=\"35%\">Id</td><td width=\"35%\">Nombre</td><td width=\"20%\">Tama&ntilde;o</td></tr>";
                                foreach($list->contents as $meta) {
                                    if ($meta->is_folder) {
                                        $data = "<img src=\"/eyeos/extern/images/48x48/places/folder-documents.png\" align=\"middle\"/>";
                                    } else {
                                        $path = "/eyeos/extern/images/48x48/mimetypes/text-plain.png";
                                        if (strrpos($meta->filename,'jpg') !== false || strrpos($meta->filename,'png') !== false) {
                                            $path = "/eyeos/extern/images/48x48/mimetypes/application-x-egon.png";
                                        } elseif (strrpos($meta->filename,'pdf') !== false) {
                                            $path = "/eyeos/extern/images/48x48/mimetypes/application-pdf.png";
                                        }
                                        $data = "<img src='". $path . "' align=\"middle\"/>";
                                    }
                                    echo "<tr style=\"font-family:'Verdana';font-size:13px;\" align='center'><td>$data</td><td>$meta->file_id</td><td>$meta->filename</td><td>$meta->size</td></tr>";
                                }
                                echo "</table>";
                            } else {
                                echo "<br><br>No contiene ni ficheros ni directorios<br></div>";
                            }
                        }
                    } else {
                        echo "<div style=\"font-family:'Verdana';font-size:15px;\"><p><b><span style='text-decoration:underline'>$list->filename</span></b>&nbsp;(Fichero)</p>";
                        echo "No se pueden encontrar dependencias de un archivo</div>";
                    }
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