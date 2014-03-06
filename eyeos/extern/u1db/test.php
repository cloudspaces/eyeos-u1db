<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/03/14
 * Time: 15:20
 */

$params = array();
$params['type'] = 'insert';
$params['lista'] = array();

$file = array();
$file['file_id'] = 'null';
$file['parent_file_id'] = 'null';
$file['filename'] = 'Documents';
$file['path'] = null;
$file['is_folder'] = true;
$file['status'] = null;
$file['user'] = null;
$file['version'] = null;
$file['checksum'] = null;
$file['size'] = null;
$file['mimetype'] = null;
$file['is_root'] = true;

array_push($params['lista'],$file);

$file = array();
$file['file_id'] = -7755273878059615652;
$file['parent_file_id'] = 'null';
$file['filename'] = 'helpFile';
$file['path'] = '/';
$file['is_folder'] = false;
$file['status'] = 'NEW';
$file['user'] = 'web';
$file['version'] = 1;
$file['checksum'] = 0;
$file['size'] = 0;
$file['mimetype'] = 'inode/directory';
$file['is_root'] = false;
$file['server_modified'] = '2013-11-11 15:40:45.784';
$file['client_modified'] = '2013-11-11 15:40:45.784';

array_push($params['lista'],$file);

$json = json_encode($params);

$mystring =exec("python Protocol.py " . escapeshellarg($json));
if(!$mystring){

    echo "python exec failed:" . $mystring . "\n";
}
else{
    echo $mystring;
    echo "successfully executed!\n";

    $params = array();
    $params['type'] = 'select';
    $params['lista'] = array();
    $file = array();
    $file['file_id'] = -7755273878059615652;

    array_push($params['lista'],$file);

    $json = json_encode($params);

    $mystring =exec("python Protocol.py " . escapeshellarg($json));
    if(!$mystring){

        echo "python exec failed:" . $mystring . "\n";
    }
    else{
        echo $mystring . "\n";
    }

}

?>